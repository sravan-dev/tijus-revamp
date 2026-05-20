<?php
/**
 * Plugin Name: Tijus Career Manager
 * Description: Manages Career listings and job application workflow.
 * Author: Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Career Custom Post Type
 */
function tijus_register_career_cpt() {
    $args = [
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => [ 'slug' => 'career' ],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-businessperson',
        'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
        'labels'             => [
            'name'          => 'Careers',
            'singular_name' => 'Career',
            'menu_name'     => 'Careers',
            'all_items'     => 'All Careers',
            'add_new'       => 'Add New Career',
            'add_new_item'  => 'Add New Career',
            'edit_item'     => 'Edit Career',
            'new_item'      => 'New Career',
            'view_item'     => 'View Career',
        ]
    ];
    register_post_type( 'career', $args );
}
add_action( 'init', 'tijus_register_career_cpt' );

/**
 * Register Application Custom Post Type (Hidden from main menu)
 */
function tijus_register_application_cpt() {
    $args = [
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => false, // Added separately via submenu
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'supports'           => [ 'title' ],
        'labels'             => [
            'name'          => 'Applications',
            'singular_name' => 'Application',
            'all_items'     => 'All Applications',
        ]
    ];
    register_post_type( 'tijus_application', $args );
}
add_action( 'init', 'tijus_register_application_cpt' );

/**
 * Add Applications Submenu under Careers
 */
function tijus_add_applications_submenu() {
    add_submenu_page(
        'edit.php?post_type=career',
        'Applications',
        'Applications',
        'manage_options',
        'edit.php?post_type=tijus_application'
    );
}
add_action( 'admin_menu', 'tijus_add_applications_submenu' );

/**
 * Setup Application Columns
 */
function tijus_application_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['app_name'] = 'Applicant Name';
    $new_columns['app_email'] = 'Email';
    $new_columns['app_phone'] = 'Phone';
    $new_columns['app_job'] = 'Applied Job';
    $new_columns['app_resume'] = 'Resume';
    $new_columns['date'] = 'Date Applied';
    return $new_columns;
}
add_filter('manage_tijus_application_posts_columns', 'tijus_application_columns');

function tijus_application_custom_column($column, $post_id) {
    switch ($column) {
        case 'app_name':
            echo esc_html(get_post_meta($post_id, 'app_name', true));
            break;
        case 'app_email':
            $email = get_post_meta($post_id, 'app_email', true);
            echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            break;
        case 'app_phone':
            echo esc_html(get_post_meta($post_id, 'app_phone', true));
            break;
        case 'app_job':
            $job_id = get_post_meta($post_id, 'app_job_id', true);
            if ($job_id) {
                echo '<a href="'.esc_url(get_edit_post_link($job_id)).'">' . esc_html(get_the_title($job_id)) . '</a>';
            } else {
                echo '-';
            }
            break;
        case 'app_resume':
            $resume_url = get_post_meta($post_id, 'app_resume_url', true);
            if ($resume_url) {
                echo '<a href="' . esc_url($resume_url) . '" target="_blank" class="button button-small">View Resume</a>';
            } else {
                echo 'No Resume';
            }
            break;
    }
}
add_action('manage_tijus_application_posts_custom_column', 'tijus_application_custom_column', 10, 2);

/**
 * Application Meta Box
 */
function tijus_add_application_meta_boxes() {
    add_meta_box( 'tijus_app_details', 'Application Details', 'tijus_render_app_meta_box', 'tijus_application', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'tijus_add_application_meta_boxes' );

function tijus_render_app_meta_box( $post ) {
    $name    = get_post_meta( $post->ID, 'app_name', true );
    $email   = get_post_meta( $post->ID, 'app_email', true );
    $phone   = get_post_meta( $post->ID, 'app_phone', true );
    $message = get_post_meta( $post->ID, 'app_message', true );
    $resume  = get_post_meta( $post->ID, 'app_resume_url', true );
    $job_id  = get_post_meta( $post->ID, 'app_job_id', true );

    ?>
    <table class="form-table">
        <tr>
            <th>Job Applied For</th>
            <td><?php echo $job_id ? esc_html(get_the_title($job_id)) : 'Unknown'; ?></td>
        </tr>
        <tr>
            <th>Applicant Name</th>
            <td><input type="text" value="<?php echo esc_attr( $name ); ?>" class="regular-text" readonly /></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><input type="email" value="<?php echo esc_attr( $email ); ?>" class="regular-text" readonly /></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><input type="text" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" readonly /></td>
        </tr>
        <tr>
            <th>Cover Letter / Message</th>
            <td><textarea rows="5" class="large-text" readonly><?php echo esc_textarea( $message ); ?></textarea></td>
        </tr>
        <tr>
            <th>Resume</th>
            <td>
                <?php if ($resume): ?>
                    <a href="<?php echo esc_url($resume); ?>" target="_blank" class="button button-primary">Download Resume</a>
                <?php else: ?>
                    <p>No resume uploaded.</p>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Handle Application Frontend Submission
 */
function tijus_ajax_submit_application() {
    if ( ! isset( $_POST['app_nonce'] ) || ! wp_verify_nonce( $_POST['app_nonce'], 'tijus_career_application' ) ) {
        wp_send_json_error( 'Invalid security token.' );
    }

    $name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
    $email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    $phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
    $message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
    $job_id  = absint( $_POST['job_id'] ?? 0 );

    if ( empty( $name ) || empty( $email ) || empty( $phone ) || empty( $job_id ) ) {
        wp_send_json_error( 'Please fill in all required fields.' );
    }

    // Handle File Upload
    $resume_url = '';
    if ( ! empty( $_FILES['resume']['name'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        
        $uploadedfile     = $_FILES['resume'];
        $upload_overrides = array( 'test_form' => false, 'mimes' => array('pdf' => 'application/pdf', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') );
        
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            $resume_url = $movefile['url'];
        } else {
            wp_send_json_error( 'Error uploading resume: ' . $movefile['error'] );
        }
    } else {
        wp_send_json_error( 'Please upload a resume.' );
    }

    // Insert App Post
    $job_title = get_the_title( $job_id );
    $post_id = wp_insert_post([
        'post_type'    => 'tijus_application',
        'post_title'   => $name . ' - ' . $job_title,
        'post_status'  => 'publish',
    ]);

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( 'Failed to submit application. Please try again later.' );
    }

    // Update Meta
    update_post_meta( $post_id, 'app_name', $name );
    update_post_meta( $post_id, 'app_email', $email );
    update_post_meta( $post_id, 'app_phone', $phone );
    update_post_meta( $post_id, 'app_message', $message );
    update_post_meta( $post_id, 'app_job_id', $job_id );
    update_post_meta( $post_id, 'app_resume_url', $resume_url );

    wp_send_json_success( 'Your application has been submitted successfully!' );
}
add_action( 'wp_ajax_tijus_submit_application', 'tijus_ajax_submit_application' );
add_action( 'wp_ajax_nopriv_tijus_submit_application', 'tijus_ajax_submit_application' );

/**
 * Seed sample careers
 */
function tijus_seed_sample_careers_once() {
    if ( get_option( 'tijus_has_seeded_careers_v1' ) !== 'yes' ) {
        $careers = [
            [
                'title'   => 'Senior IELTS Trainer',
                'excerpt' => 'Join our expert faculty to train aspiring students in IELTS modules.',
                'content' => '<h3>About the Role</h3><p>We are looking for an experienced IELTS Trainer to join our academy. You will be responsible for creating training modules, conducting mock tests, and analyzing student performance...</p><h4>Requirements:</h4><ul><li>Minimum 3 years of teaching IELTS</li><li>Band 8.0 or above in IELTS previously</li><li>Excellent communication skills</li></ul>'
            ],
            [
                'title'   => 'Student Counselor',
                'excerpt' => 'Help guide students through their enrollment process and visa applications.',
                'content' => '<h3>About the Role</h3><p>We are seeking a highly motivated Student Counselor to join our admission team. The ideal candidate will be a friendly face for all new students, helping them navigate their course options...</p><h4>Requirements:</h4><ul><li>Degree in Psychology or related field</li><li>Strong interpersonal skills</li><li>Experience in educational counseling</li></ul>'
            ],
            [
                'title'   => 'Marketing Executive',
                'excerpt' => 'Drive our digital campaigns and enhance our brand visibility across Kerala.',
                'content' => '<h3>About the Role</h3><p>As a Marketing Executive, you will handle our social media channels, create engaging content, and organize local promotional events...</p><h4>Requirements:</h4><ul><li>Experience in digital marketing</li><li>Creative and proactive approach</li><li>Proficiency in Malayalam and English</li></ul>'
            ]
        ];

        foreach ($careers as $career) {
            $post_data = array(
                'post_title'    => wp_strip_all_tags( $career['title'] ),
                'post_excerpt'  => $career['excerpt'],
                'post_content'  => $career['content'],
                'post_type'     => 'career',
                'post_status'   => 'publish',
            );
            wp_insert_post( $post_data );
        }

        update_option( 'tijus_has_seeded_careers_v1', 'yes' );
    }
}
add_action( 'init', 'tijus_seed_sample_careers_once' );

/**
 * Flush rewrite rules to recognize the new CPT
 */
function tijus_flush_careers_rewrite_once() {
    if ( get_option( 'tijus_flushed_rewrite_careers_v1' ) !== 'yes' ) {
        flush_rewrite_rules();
        update_option( 'tijus_flushed_rewrite_careers_v1', 'yes' );
    }
}
add_action( 'init', 'tijus_flush_careers_rewrite_once', 99 );
