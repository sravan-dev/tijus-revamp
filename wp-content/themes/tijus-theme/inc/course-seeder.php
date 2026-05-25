<?php
/**
 * Tijus Course Seeder - Admin Tool
 *
 * Appears under Tools -> Seed Courses.
 * Deletes all existing course posts, creates real ones with appropriate images.
 *
 * @package tijus-theme
 */

add_action( 'admin_menu', 'tijus_course_seeder_menu' );

function tijus_course_seeder_menu() {
    add_management_page(
        'Seed Courses',
        'Seed Courses',
        'manage_options',
        'tijus-seed-courses',
        'tijus_course_seeder_render'
    );
}

add_action( 'admin_init', 'tijus_course_seeder_handle' );

function tijus_course_seeder_handle() {
    if (
        ! isset( $_POST['tijus_seed_courses_nonce'] ) ||
        ! wp_verify_nonce( $_POST['tijus_seed_courses_nonce'], 'tijus_seed_courses' ) ||
        ! current_user_can( 'manage_options' )
    ) {
        return;
    }

    // Delete all existing course posts
    $existing = get_posts( [
        'post_type'      => 'course',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ] );
    foreach ( $existing as $id ) {
        wp_delete_post( $id, true );
    }

    // Delete all existing course_category terms
    $existing_terms = get_terms( [ 'taxonomy' => 'course_category', 'hide_empty' => false ] );
    if ( ! is_wp_error( $existing_terms ) ) {
        foreach ( $existing_terms as $term ) {
            wp_delete_term( $term->term_id, 'course_category' );
        }
    }

    // Create categories
    $cats = [
        'language-exams'       => 'Language Exams',
        'healthcare-licensing' => 'Healthcare Licensing',
        'diploma-programs'     => 'Diploma Programs',
        'wellness'             => 'Wellness',
        'tijus-media-school'   => 'Tiju\'s Media School',
    ];
    $cat_ids = [];
    foreach ( $cats as $slug => $name ) {
        $term = wp_insert_term( $name, 'course_category', [ 'slug' => $slug ] );
        $cat_ids[ $slug ] = is_wp_error( $term ) ? $term->error_data['term_exists'] : $term['term_id'];
    }

    // Courses: [ title, category_slug, theme_image_number (1-6) ]
    $courses = [
        // Column 1
        [ 'OET',                                     'language-exams',       1 ],
        [ 'CBT',                                     'language-exams',       2 ],
        [ 'IELTS',                                   'language-exams',       3 ],
        [ 'PTE',                                     'language-exams',       4 ],
        [ 'German',                                  'language-exams',       5 ],
        
        // Column 2
        [ 'Prometic',                                'healthcare-licensing', 6 ],
        [ 'DHA',                                     'healthcare-licensing', 1 ],
        [ 'MOH',                                     'healthcare-licensing', 2 ],
        [ 'HAAD',                                    'healthcare-licensing', 3 ],
        [ 'NCLEX-Rn',                                'healthcare-licensing', 4 ],
        
        // Column 3
        [ 'Diploma in Airline & Airport Management', 'diploma-programs',     5 ],
        [ 'Diploma in Logistics',                    'diploma-programs',     6 ],
        [ 'Diploma in Data Analytics',               'diploma-programs',     1 ],
        [ 'Digital Marketing',                       'diploma-programs',     2 ],
        
        // Column 4
        [ 'Yoga',                                    'wellness',             3 ],
        [ 'Zumba',                                   'wellness',             4 ],

        // Column 5
        [ 'Diploma in script writing and direction',          'tijus-media-school', 1 ],
        [ 'Diploma in cinematography and still photography', 'tijus-media-school', 2 ],
        [ 'Diploma in editing and color grading',            'tijus-media-school', 3 ],
        [ 'Diploma in vfx and motion graphics',              'tijus-media-school', 4 ],
    ];

    $base = get_template_directory_uri() . '/assets/images/courses/';

    foreach ( $courses as $course ) {
        list( $title, $cat_slug, $img_num ) = $course;

        $post_id = wp_insert_post( [
            'post_title'   => $title,
            'post_status'  => 'publish',
            'post_type'    => 'course',
            'post_content' => '',
        ] );

        if ( is_wp_error( $post_id ) ) continue;

        wp_set_object_terms( $post_id, [ (int) $cat_ids[ $cat_slug ] ], 'course_category' );
        update_post_meta( $post_id, '_course_thumbnail_url', $base . sprintf( 'courses-%02d.jpg', $img_num ) );
    }

    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Courses seeded successfully!</strong> 16 courses created with keyword-matched images.</p></div>';
    } );
}

function tijus_course_seeder_render() {
    ?>
    <div class="wrap">
        <h1>Seed Courses</h1>
        <p>Deletes all existing courses and creates 16 with keyword-matched images from loremflickr.com.</p>
        <table class="widefat" style="max-width:600px;margin-bottom:20px;">
            <thead><tr><th>Course</th><th>Category</th></tr></thead>
            <tbody>
                <tr><td>OET, CBT, IELTS, PTE, German</td><td>Language Exams</td></tr>
                <tr><td>Prometic, DHA, MOH, HAAD, NCLEX-Rn</td><td>Healthcare Licensing</td></tr>
                <tr><td>Diploma in Airline &amp; Airport Management, Diploma in Logistics, Diploma in Data Analytics, Digital Marketing</td><td>Diploma Programs</td></tr>
                <tr><td>Yoga, Zumba</td><td>Wellness</td></tr>
            </tbody>
        </table>
        <form method="post">
            <?php wp_nonce_field( 'tijus_seed_courses', 'tijus_seed_courses_nonce' ); ?>
            <p><input type="submit" class="button button-primary button-large" value="Run Course Seeder (Deletes Old Courses)"></p>
        </form>
    </div>
    <?php
}
