<?php
/**
 * Plugin Name: Tijus Lead Manager
 * Description: Captures course enrollments, registers users, and manages them in a dedicated Customers tab.
 * Author: Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One-time setup
 */
function tijus_seed_site_content_once() {
    // Unconditionally force the announcement text to overwrite DB cache
    set_theme_mod('tijus_announcement_text', "Enroll now to get 50% off on all courses");

    if ( get_option( 'tijus_has_seeded_blogs_v2' ) !== 'yes' ) {
        // 1. Update Contact and Locations
        set_theme_mod('tijus_phone', '+91 95392 59910');
        set_theme_mod('tijus_email', 'info@tijusacademy.com');
        set_theme_mod('tijus_footer_address_title', "Tiju's Academy");
        set_theme_mod('tijus_footer_address_city', "East of Budha Jn., Mavelikara, Kerala");

        // 2. Create 5 Blog Posts
        $blogs = [
            [
                'title'   => 'How Discourse Marker Anchoring Can Change the Way You Score',
                'content' => 'You have read the OET passage and learned the tips from Tiju\'s Academy. Here is how discourse marker anchoring can skyrocket your reading scores... <br><br> Join Tiju\'s Academy for the best OET Training in Kerala.',
                'category'=> 'OET'
            ],
            [
                'title'   => 'SATA Fear is Real: How to Stay Calm When the Multi-Select Questions Hit',
                'content' => 'The National Council Licensure Examination (NCLEX) is the final hurdle between a nurse and their International dream. Let’s explore how the multi-select questions work and how Tiju\'s academy helps you solve them easily.',
                'category'=> 'NCLEX-RN'
            ],
            [
                'title'   => 'From Doubt to Distinction: How Abhishek Ananthan Scored IELTS Band 8',
                'content' => 'It seems that for some individuals, a dream isn\'t complete without going abroad. Abhishek Ananthan, a Civil Engineer, scored Band 8 with the rigorous coaching from Tiju\'s Academy experts... ',
                'category'=> 'IELTS'
            ],
            [
                'title'   => 'PTE Academic vs PTE Core Writing Module: What You Need to Know',
                'content' => 'If you are planning to take the PTE exam, you have probably come across two options. Let’s break down the differences between the PTE Academic and Core Writing modules for maximum score retention.',
                'category'=> 'PTE'
            ],
            [
                'title'   => 'Master the Metrics: Why Vital Statistics are Your Secret Weapon for Passing the NCLEX',
                'content' => 'At Tiju\'s Academy NCLEX RN coaching center, we recognize how overwhelming the exam metrics can appear. Let us explain why vital statistics hold the key to passing your NCLEX RN on the very first try.',
                'category'=> 'NCLEX-RN'
            ]
        ];

        foreach ($blogs as $blog) {
            $cat_id = 1; // Default
            $term = term_exists( $blog['category'], 'category' );
            if ( $term !== 0 && $term !== null ) {
                $cat_id = intval( is_array($term) ? $term['term_id'] : $term );
            } else {
                $new_term = wp_insert_term( $blog['category'], 'category' );
                if ( ! is_wp_error( $new_term ) ) {
                    $cat_id = intval( $new_term['term_id'] );
                }
            }
            
            $post_data = array(
                'post_title'    => wp_strip_all_tags( $blog['title'] ),
                'post_content'  => $blog['content'],
                'post_status'   => 'publish',
                'post_category' => array( $cat_id )
            );
            wp_insert_post( $post_data );
        }

        update_option( 'tijus_has_seeded_blogs_v2', 'yes' );
    }
}
add_action( 'init', 'tijus_seed_site_content_once' );

/**
 * Seed sample FAQs to all available courses.
 */
function tijus_seed_course_faqs_once() {
    if ( get_option( 'tijus_has_seeded_faqs_v1' ) !== 'yes' ) {
        $courses = get_posts( [
            'post_type'   => 'course',
            'numberposts' => -1,
            'post_status' => 'publish'
        ] );

        $sample_faqs = [
            [
                'question' => 'Do I get access to the recorded videos after the live session?',
                'answer'   => 'Yes, absolutely! All session recordings are uploaded to your student dashboard within 24 hours and are accessible for lifetime.'
            ],
            [
                'question' => 'Is there any prerequisite for enrolling in this course?',
                'answer'   => 'This course is designed for beginners. However, having a basic understanding of computer operations will certainly help you grasp concepts faster.'
            ],
            [
                'question' => 'Do you provide a completion certificate?',
                'answer'   => 'Yes. Once you complete all modules and pass the final evaluation, you will receive a verifiable digital certificate instantly.'
            ]
        ];

        foreach ( $courses as $course ) {
            update_post_meta( $course->ID, '_course_faqs', $sample_faqs );
        }

        update_option( 'tijus_has_seeded_faqs_v1', 'yes' );
    }
}
add_action( 'init', 'tijus_seed_course_faqs_once' );

/**
 * Register the Customers admin menu.
 */
function tijus_add_customers_admin_menu() {
    add_menu_page(
        'Customers',
        'Customers',
        'manage_options',
        'tijus-customers',
        'tijus_render_customers_page',
        'dashicons-groups',
        26
    );

    // Provide explicit submenus to ensure they display properly
    add_submenu_page(
        'tijus-customers',
        'All Customers',
        'All Customers',
        'manage_options',
        'tijus-customers',
        'tijus_render_customers_page'
    );

    add_submenu_page(
        'tijus-customers',
        'Contacts',
        'Contacts',
        'manage_options',
        'edit.php?post_type=tijus_contact'
    );
}
add_action( 'admin_menu', 'tijus_add_customers_admin_menu' );

/**
 * Render the Customers page.
 */
function tijus_render_customers_page() {
    // Get all users with subscriber role
    $args = [
        'role'    => 'subscriber',
        'orderby' => 'registered',
        'order'   => 'DESC'
    ];
    $user_query = new WP_User_Query( $args );
    $users = $user_query->get_results();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Customers</h1>
        <p>List of users registered via the Course Enrollment form.</p>
        
        <table class="wp-list-table widefat fixed striped table-view-list users mt-3">
            <thead>
                <tr>
                    <th class="manage-column column-name">Name</th>
                    <th class="manage-column column-email">Email</th>
                    <th class="manage-column">Phone</th>
                    <th class="manage-column">WhatsApp</th>
                    <th class="manage-column">Course Mode</th>
                    <th class="manage-column">Enrolled Course(s)</th>
                    <th class="manage-column">Location</th>
                    <th class="manage-column">Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $users ) ) : ?>
                    <?php foreach ( $users as $user ) : 
                        $phone = get_user_meta($user->ID, 'tijus_phone', true);
                        $whatsapp = get_user_meta($user->ID, 'tijus_whatsapp', true);
                        $mode = get_user_meta($user->ID, 'tijus_course_mode', true);
                        $country = get_user_meta($user->ID, 'tijus_country', true);
                        $city = get_user_meta($user->ID, 'tijus_city', true);
                        $enrolled = get_user_meta($user->ID, '_enrolled_courses', true);
                        
                        $courses_list = '-';
                        if ( is_array($enrolled) && ! empty($enrolled) ) {
                            $titles = array_map(function($cid) {
                                return get_the_title($cid);
                            }, $enrolled);
                            $courses_list = implode('<br>', $titles);
                        }
                        
                        $location = array_filter([$city, $country]);
                        $location_str = empty($location) ? '-' : implode(', ', $location);
                    ?>
                    <tr>
                        <td class="name column-name">
                            <strong><?php echo esc_html( $user->first_name . ' ' . $user->last_name ); ?></strong>
                        </td>
                        <td class="email column-email">
                            <a href="mailto:<?php echo esc_attr( $user->user_email ); ?>"><?php echo esc_html( $user->user_email ); ?></a>
                        </td>
                        <td><?php echo esc_html( $phone ?: '-' ); ?></td>
                        <td><?php echo esc_html( $whatsapp ?: '-' ); ?></td>
                        <td><?php echo esc_html( $mode ?: '-' ); ?></td>
                        <td><?php echo wp_kses_post( $courses_list ); ?></td>
                        <td><?php echo esc_html( $location_str ); ?></td>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) ) ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr class="no-items">
                        <td class="colspanchange" colspan="8">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Handle Enrollment Form AJAX Submission.
 */
function tijus_ajax_enroll_user() {
    // Check nonce
    if ( ! isset( $_POST['enroll_nonce'] ) || ! wp_verify_nonce( $_POST['enroll_nonce'], 'tijus_enroll_action' ) ) {
        wp_send_json_error( 'Invalid security token.' );
    }

    $first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
    $last_name  = sanitize_text_field( $_POST['last_name'] ?? '' );
    $email      = sanitize_email( $_POST['email'] ?? '' );
    $password   = $_POST['password'] ?? '';
    $phone      = sanitize_text_field( $_POST['phone'] ?? '' );
    $whatsapp   = sanitize_text_field( $_POST['whatsapp'] ?? '' );
    $mode       = sanitize_text_field( $_POST['course_mode'] ?? '' );
    $course_id  = absint( $_POST['course_id'] ?? 0 );
    $country    = sanitize_text_field( $_POST['country'] ?? '' );
    $city       = sanitize_text_field( $_POST['city'] ?? '' );

    if ( empty( $email ) || empty( $first_name ) || empty( $phone ) || empty( $course_id ) ) {
        wp_send_json_error( 'Please fill in all required fields.' );
    }

    if ( empty( $password ) ) {
        $password = wp_generate_password( 12, false );
    }

    if ( username_exists( $email ) || email_exists( $email ) ) {
        wp_send_json_error( 'An account with this email address already exists. Please log in first, or use a different email.' );
    }

    // Create the user
    $user_id = wp_create_user( $email, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( $user_id->get_error_message() );
    }

    // Set role
    $user = new WP_User( $user_id );
    $user->set_role( 'subscriber' );

    // Save standard meta
    wp_update_user([
        'ID'         => $user_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
    ]);

    // Save custom meta
    update_user_meta( $user_id, 'tijus_phone', $phone );
    update_user_meta( $user_id, 'tijus_whatsapp', $whatsapp );
    update_user_meta( $user_id, 'tijus_course_mode', $mode );
    update_user_meta( $user_id, 'tijus_country', $country );
    update_user_meta( $user_id, 'tijus_city', $city );

    // Map course to user
    $enrolled_courses = get_user_meta( $user_id, '_enrolled_courses', true );
    if ( ! is_array( $enrolled_courses ) ) {
        $enrolled_courses = [];
    }
    if ( ! in_array( $course_id, $enrolled_courses ) ) {
        $enrolled_courses[] = $course_id;
        update_user_meta( $user_id, '_enrolled_courses', $enrolled_courses );
    }

    // Map user to course
    $course_users = get_post_meta( $course_id, '_enrolled_users', true );
    if ( ! is_array( $course_users ) ) {
        $course_users = [];
    }
    if ( ! in_array( $user_id, $course_users ) ) {
        $course_users[] = $user_id;
        update_post_meta( $course_id, '_enrolled_users', $course_users );
    }
    // Generate confirmation token & send email instead of auto-login
    $confirm_token = wp_generate_password( 32, false );
    update_user_meta( $user_id, 'tijus_confirm_token', $confirm_token );
    update_user_meta( $user_id, 'tijus_confirm_token_time', time() );
    update_user_meta( $user_id, 'tijus_is_confirmed', 0 );

    $confirm_url = add_query_arg( [
        'tijus_confirm_user' => $user_id,
        'tijus_token'        => $confirm_token
    ], get_permalink( $course_id ) );

    $subject = 'Confirm Your Enrollment - Tijus Academy';
    $message = "Hello $first_name,\n\nThank you for enrolling in our course. Please click the link below to confirm your account and complete your enrollment. This link is valid for 1 minute only:\n\n$confirm_url\n\nThanks,\nTijus Academy";
    $headers = array('Content-Type: text/plain; charset=UTF-8');

    wp_mail( $email, $subject, $message, $headers );

    wp_send_json_success( 'A confirmation link has been sent to your email. Please check your inbox and click it to confirm your account (Valid for 1 minute).' );
}
add_action( 'wp_ajax_tijus_enroll_user', 'tijus_ajax_enroll_user' );
add_action( 'wp_ajax_nopriv_tijus_enroll_user', 'tijus_ajax_enroll_user' );

/**
 * Handle account confirmation link
 */
function tijus_check_account_confirmation() {
    if ( isset( $_GET['tijus_confirm_user'] ) && isset( $_GET['tijus_token'] ) ) {
        $user_id = absint( $_GET['tijus_confirm_user'] );
        $token   = sanitize_text_field( $_GET['tijus_token'] );

        $saved_token = get_user_meta( $user_id, 'tijus_confirm_token', true );
        $token_time  = (int) get_user_meta( $user_id, 'tijus_confirm_token_time', true );

        if ( $saved_token && $saved_token === $token ) {
            // Check expiration (1 minute = 60 seconds)
            if ( time() - $token_time > 60 ) {
                global $wp_query, $tijus_error_title, $tijus_error_message;
                $wp_query->set_404();
                status_header( 404 );
                $tijus_error_title = "Link Expired";
                $tijus_error_message = "This confirmation link has expired. It was only valid for 1 minute.";
                include get_query_template( '404' );
                exit;
            }

            // Confirm the account
            update_user_meta( $user_id, 'tijus_is_confirmed', 1 );
            delete_user_meta( $user_id, 'tijus_confirm_token' );
            delete_user_meta( $user_id, 'tijus_confirm_token_time' );

            // Auto log them in
            $user = get_user_by( 'id', $user_id );
            if ( $user ) {
                wp_set_current_user( $user_id );
                wp_set_auth_cookie( $user_id );
                do_action( 'wp_login', $user->user_login, $user );
            }

            // Redirect to dashboard with a success message
            // Optionally, add a querystring so dashboard can show "account confirmed"
            wp_redirect( home_url( '/dashboard/?confirmed=1' ) );
            exit;
        } else {
            global $wp_query, $tijus_error_title, $tijus_error_message;
            $wp_query->set_404();
            status_header( 404 );
            $tijus_error_title = "Invalid Link";
            $tijus_error_message = "Invalid or expired confirmation link. Please contact support if you need help.";
            include get_query_template( '404' );
            exit;
        }
    }
}
add_action( 'template_redirect', 'tijus_check_account_confirmation' );

/**
 * Handle dynamic AJAX check for existing emails.
 */
function tijus_check_email_exists_ajax() {
    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( empty( $email ) ) {
        wp_send_json_error();
    }
    
    if ( email_exists( $email ) ) {
        wp_send_json_success( ['exists' => true] );
    } else {
        wp_send_json_success( ['exists' => false] );
    }
}
add_action( 'wp_ajax_tijus_check_email_exists', 'tijus_check_email_exists_ajax' );
add_action( 'wp_ajax_nopriv_tijus_check_email_exists', 'tijus_check_email_exists_ajax' );

/**
 * Handle dynamic AJAX profile update.
 */
function tijus_ajax_update_profile() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'Unauthorized' );
    }

    $user_id = get_current_user_id();
    $full_name = isset($_POST['full_name']) ? sanitize_text_field($_POST['full_name']) : '';

    if ( empty($full_name) ) {
        wp_send_json_error( 'Name cannot be empty.' );
    }

    $user_data = wp_update_user( array(
        'ID'           => $user_id,
        'display_name' => $full_name,
        'first_name'   => $full_name // Assuming simple single field for now based on UI
    ) );

    if ( is_wp_error( $user_data ) ) {
        wp_send_json_error( 'Failed to update profile.' );
    }

    wp_send_json_success( 'Profile updated successfully!' );
}
add_action( 'wp_ajax_tijus_update_profile', 'tijus_ajax_update_profile' );

/**
 * Handle dynamic AJAX fetch for frontend Favourites mapping.
 */
function tijus_ajax_get_favorite_courses() {
    $course_ids_raw = isset($_POST['course_ids']) ? stripslashes($_POST['course_ids']) : '[]';
    $course_ids = json_decode($course_ids_raw, true);

    if ( ! is_array($course_ids) || empty($course_ids) ) {
        wp_send_json_error( 'No favorites found.' );
    }

    $args = array(
        'post_type'      => 'course',
        'post__in'       => $course_ids,
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    );
    
    $q = new WP_Query($args);
    
    if ( ! $q->have_posts() ) {
        wp_send_json_error( 'Courses no longer exist.' );
    }

    ob_start();
    while ($q->have_posts()) {
        $q->the_post();
        get_template_part('template-parts/content', 'course', array('dashboard_action' => 'remove_favorite'));
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( $html );
}
add_action( 'wp_ajax_tijus_get_favorite_courses', 'tijus_ajax_get_favorite_courses' );
add_action( 'wp_ajax_nopriv_tijus_get_favorite_courses', 'tijus_ajax_get_favorite_courses' );

/**
 * Handle dynamic AJAX fetch for frontend Collections mapping.
 */
function tijus_ajax_get_collection_courses() {
    $course_ids_raw = isset($_POST['course_ids']) ? stripslashes($_POST['course_ids']) : '[]';
    $course_ids = json_decode($course_ids_raw, true);

    if ( ! is_array($course_ids) || empty($course_ids) ) {
        wp_send_json_error( 'No collections found.' );
    }

    $args = array(
        'post_type'      => 'course',
        'post__in'       => $course_ids,
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    );
    
    $q = new WP_Query($args);
    
    if ( ! $q->have_posts() ) {
        wp_send_json_error( 'Courses no longer exist.' );
    }

    ob_start();
    while ($q->have_posts()) {
        $q->the_post();
        get_template_part('template-parts/content', 'course', array('dashboard_action' => 'remove_collection'));
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( $html );
}
add_action( 'wp_ajax_tijus_get_collection_courses', 'tijus_ajax_get_collection_courses' );
add_action( 'wp_ajax_nopriv_tijus_get_collection_courses', 'tijus_ajax_get_collection_courses' );

/**
 * Handle Live Autocomplete Dropdown Search
 */
function tijus_live_search_ajax() {
    $search = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
    if ( strlen($search) < 2 ) {
        wp_send_json_error();
    }
    
    $args = array(
        'post_type'      => 'course',
        's'              => $search,
        'posts_per_page' => 5,
        'post_status'    => 'publish'
    );
    
    $q = new WP_Query($args);
    if ( ! $q->have_posts() ) {
        wp_send_json_success('<li><span style="padding:15px; display:block; color:#777;">No courses found.</span></li>');
    }
    
    $html = '';
    while ($q->have_posts()) {
        $q->the_post();
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
        if (!$thumb) $thumb = get_template_directory_uri() . '/assets/images/courses/courses-01.jpg';
        
        $html .= '<li style="border-bottom:1px solid #eee; transition: background 0.2s;">';
        $html .= '<a href="'.get_permalink().'" style="display:flex; align-items:center; padding:10px 15px; color:#212832; text-decoration:none;">';
        $html .= '<img src="'.esc_url($thumb).'" style="width:50px; height:50px; object-fit:cover; border-radius:5px; margin-right:15px;" alt="course">';
        $html .= '<span style="font-weight:600; font-size:14px; line-height:1.4;">'.get_the_title().'</span>';
        $html .= '</a>';
        $html .= '</li>';
    }
    wp_reset_postdata();
    
    wp_send_json_success($html);
}
add_action( 'wp_ajax_tijus_live_search', 'tijus_live_search_ajax' );
add_action( 'wp_ajax_nopriv_tijus_live_search', 'tijus_live_search_ajax' );

/**
 * Intercept auto-enrollment payloads natively post-login.
 */
function tijus_auto_enroll_intercept() {
    if ( is_user_logged_in() && isset( $_GET['auto_enroll_course'] ) ) {
        
        $user_id = get_current_user_id();
        $course_id = absint( $_GET['auto_enroll_course'] );
        
        if ( get_post_type( $course_id ) === 'course' ) {
            // Map course to user
            $enrolled_courses = get_user_meta( $user_id, '_enrolled_courses', true );
            if ( ! is_array( $enrolled_courses ) ) {
                $enrolled_courses = [];
            }
            if ( ! in_array( $course_id, $enrolled_courses ) ) {
                $enrolled_courses[] = $course_id;
                update_user_meta( $user_id, '_enrolled_courses', $enrolled_courses );
            }

            // Map user to course
            $course_users = get_post_meta( $course_id, '_enrolled_users', true );
            if ( ! is_array( $course_users ) ) {
                $course_users = [];
            }
            if ( ! in_array( $user_id, $course_users ) ) {
                $course_users[] = $user_id;
                update_post_meta( $course_id, '_enrolled_users', $course_users );
            }
        }
        
        // Strip the GET parameter securely by doing a clean redirect.
        wp_redirect( remove_query_arg( 'auto_enroll_course' ) );
        exit;
    }
}
add_action( 'template_redirect', 'tijus_auto_enroll_intercept' );
add_action( 'admin_init', 'tijus_auto_enroll_intercept' ); // Catch it if they land in WP Admin Dashboard

/**
 * Register Contacts Custom Post Type
 */
function tijus_register_contact_cpt() {
    $args = [
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => false, // manually added via add_submenu_page
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => ['title'], // Subject becomes Title
        'labels'             => [
            'name'          => 'Contacts',
            'singular_name' => 'Contact',
            'menu_name'     => 'Contacts',
            'all_items'     => 'All Contacts',
        ]
    ];
    register_post_type( 'tijus_contact', $args );
}
add_action( 'init', 'tijus_register_contact_cpt' );

/**
 * Configure columns for Contacts CPT
 */
function tijus_contact_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['contact_name'] = 'Name';
    $new_columns['contact_email'] = 'Email';
    $new_columns['title'] = 'Subject';
    $new_columns['contact_message'] = 'Message';
    $new_columns['date'] = 'Date';
    return $new_columns;
}
add_filter('manage_tijus_contact_posts_columns', 'tijus_contact_columns');

function tijus_contact_custom_column($column, $post_id) {
    switch ($column) {
        case 'contact_name':
            echo esc_html(get_post_meta($post_id, 'contact_name', true));
            break;
        case 'contact_email':
            $email = get_post_meta($post_id, 'contact_email', true);
            echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            break;
        case 'contact_message':
            $message = get_post_meta($post_id, 'contact_message', true);
            echo esc_html(wp_trim_words($message, 15, '...'));
            break;
    }
}
add_action('manage_tijus_contact_posts_custom_column', 'tijus_contact_custom_column', 10, 2);

/**
 * Register Meta Boxes for Contacts.
 */
function tijus_add_contact_meta_boxes() {
    add_meta_box(
        'tijus_contact_details',
        'Contact Details',
        'tijus_render_contact_meta_box',
        'tijus_contact',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tijus_add_contact_meta_boxes' );

function tijus_render_contact_meta_box( $post ) {
    wp_nonce_field( 'tijus_save_contact_data', 'tijus_contact_meta_nonce' );

    $name    = get_post_meta( $post->ID, 'contact_name', true );
    $email   = get_post_meta( $post->ID, 'contact_email', true );
    $message = get_post_meta( $post->ID, 'contact_message', true );

    ?>
    <table class="form-table">
        <tr>
            <th><label for="contact_name">Name</label></th>
            <td><input type="text" id="contact_name" name="contact_name" value="<?php echo esc_attr( $name ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="contact_email">Email</label></th>
            <td><input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr( $email ); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="contact_message">Message</label></th>
            <td><textarea id="contact_message" name="contact_message" rows="5" class="large-text"><?php echo esc_textarea( $message ); ?></textarea></td>
        </tr>
    </table>
    <?php
}

function tijus_save_contact_meta_data( $post_id ) {
    if ( ! isset( $_POST['tijus_contact_meta_nonce'] ) || ! wp_verify_nonce( $_POST['tijus_contact_meta_nonce'], 'tijus_save_contact_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['contact_name'] ) ) {
        update_post_meta( $post_id, 'contact_name', sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) );
    }
    if ( isset( $_POST['contact_email'] ) ) {
        update_post_meta( $post_id, 'contact_email', sanitize_email( wp_unslash( $_POST['contact_email'] ) ) );
    }
    if ( isset( $_POST['contact_message'] ) ) {
        update_post_meta( $post_id, 'contact_message', sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) );
    }
}
add_action( 'save_post_tijus_contact', 'tijus_save_contact_meta_data' );

/**
 * Handle Contact Form Submissions
 */
function tijus_ajax_submit_contact() {
    if ( ! isset( $_POST['contact_nonce'] ) || ! wp_verify_nonce( $_POST['contact_nonce'], 'tijus_contact_form' ) ) {
        wp_send_json_error( 'Invalid security token.' );
    }

    $captcha_answer = isset( $_POST['captcha_answer'] ) ? strtoupper(sanitize_text_field( $_POST['captcha_answer'] )) : '';
    $captcha_hash   = isset( $_POST['captcha_hash'] ) ? sanitize_text_field( $_POST['captcha_hash'] ) : '';

    if ( md5($captcha_answer . 'tijus_captcha_salt') !== $captcha_hash || empty($captcha_answer) ) {
        wp_send_json_error( 'Incorrect CAPTCHA code. Please try again.' );
    }

    // SQL Injection Prevention: all fields are heavily sanitized via sanitize_text_field
    // before being passed to wp_insert_post and update_post_meta, which internally
    // parameterize the database queries using $wpdb->prepare() ensuring 100% SQLi protection.
    $name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
    $email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    $subject = sanitize_text_field( wp_unslash( $_POST['subject'] ?? '' ) );
    $message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

    if ( empty( $name ) || empty( $email ) || empty( $subject ) || empty( $message ) ) {
        wp_send_json_error( 'Please fill in all required fields.' );
    }

    $post_id = wp_insert_post([
        'post_type'    => 'tijus_contact',
        'post_title'   => $subject, // Save Subject as Title
        'post_status'  => 'publish',
        'post_author'  => 1
    ]);

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( 'Failed to submit contact query. Please try again later.' );
    }

    update_post_meta( $post_id, 'contact_name', $name );
    update_post_meta( $post_id, 'contact_email', $email );
    update_post_meta( $post_id, 'contact_message', $message );

    wp_send_json_success( 'Your message has been sent successfully. We will get back to you shortly!' );
}
add_action( 'wp_ajax_tijus_submit_contact', 'tijus_ajax_submit_contact' );
add_action( 'wp_ajax_nopriv_tijus_submit_contact', 'tijus_ajax_submit_contact' );

/**
 * Handle Custom Login Form Submission
 */
function tijus_ajax_login_handler() {
    if ( ! isset( $_POST['tijus_login_nonce'] ) || ! wp_verify_nonce( $_POST['tijus_login_nonce'], 'tijus_login_nonce_action' ) ) {
        wp_send_json_error( 'Invalid security token. Please refresh the page and try again.' );
    }

    $email = sanitize_email( $_POST['log_email'] ?? '' );
    $password = $_POST['log_password'] ?? '';

    if ( empty( $email ) || empty( $password ) ) {
        wp_send_json_error( 'Please fill in both email and password.' );
    }

    $user = get_user_by( 'email', $email ) ?: get_user_by( 'login', $email );

    if ( ! $user ) {
        wp_send_json_error( 'No account found with that email address.' );
    }

    $creds = array(
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => true
    );

    $user_signon = wp_signon( $creds, false );

    if ( is_wp_error( $user_signon ) ) {
        wp_send_json_error( 'Incorrect password. Please try again.' );
    }

    wp_send_json_success( 'Successfully logged in. Redirecting...' );
}
add_action( 'wp_ajax_tijus_ajax_login', 'tijus_ajax_login_handler' );
add_action( 'wp_ajax_nopriv_tijus_ajax_login', 'tijus_ajax_login_handler' );

/**
 * Handle Custom Register Form Submission
 */
function tijus_ajax_register_handler() {
    if ( ! isset( $_POST['tijus_register_nonce'] ) || ! wp_verify_nonce( $_POST['tijus_register_nonce'], 'tijus_register_nonce_action' ) ) {
        wp_send_json_error( 'Invalid security token. Please refresh the page and try again.' );
    }

    $name = sanitize_text_field( $_POST['reg_name'] ?? '' );
    $email = sanitize_email( $_POST['reg_email'] ?? '' );
    $password = $_POST['reg_password'] ?? '';
    
    if ( empty( $name ) || empty( $email ) || empty( $password ) ) {
        wp_send_json_error( 'Please fill in all required fields.' );
    }

    if ( email_exists( $email ) || username_exists( $email ) ) {
        wp_send_json_error( 'This email is already registered.' );
    }

    $user_id = wp_create_user( $email, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( $user_id->get_error_message() );
    }

    $user = new WP_User( $user_id );
    $user->set_role( 'subscriber' );

    $name_parts = explode( ' ', trim( $name ), 2 );
    $first_name = $name_parts[0];
    $last_name = $name_parts[1] ?? '';

    wp_update_user([
        'ID'         => $user_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
    ]);

    // Auto login immediately
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );
    do_action( 'wp_login', $email, $user );

    wp_send_json_success( 'Registration successful. Redirecting...' );
}
add_action( 'wp_ajax_nopriv_tijus_ajax_register', 'tijus_ajax_register_handler' );
