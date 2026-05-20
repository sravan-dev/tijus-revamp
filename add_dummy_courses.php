<?php
require_once __DIR__ . '/wp-load.php';

$dummy_courses = [
    [
        'title' => 'Data Science and Machine Learning with Python - Hands On!',
        'secondary_author' => 'Ohula Malsh',
        'progress' => '38',
        'duration' => '08 hr 15 mins',
        'lectures' => '29 Lectures',
        'regular_price' => '440.00',
        'sale_price' => '385.00',
        'rating' => '4.9',
    ],
    [
        'title' => 'Create Amazing Color Schemes for Your UX Design Projects',
        'secondary_author' => 'Ohula Malsh',
        'progress' => '80',
        'duration' => '05 hr 10 mins',
        'lectures' => '15 Lectures',
        'regular_price' => '',
        'sale_price' => '420.00',
        'rating' => '4.9',
    ],
    [
        'title' => 'Culture & Leadership: Strategies for a Successful Business',
        'secondary_author' => 'Ohula Malsh',
        'progress' => '15',
        'duration' => '12 hr 30 mins',
        'lectures' => '40 Lectures',
        'regular_price' => '340.00',
        'sale_price' => '295.00',
        'rating' => '4.9',
    ],
    [
        'title' => 'Finance Series: Learn to Budget and Calculate your Net Worth',
        'secondary_author' => 'Ohula Malsh',
        'progress' => '45',
        'duration' => '03 hr 45 mins',
        'lectures' => '12 Lectures',
        'regular_price' => '',
        'sale_price' => 'Free',
        'rating' => '4.9',
    ],
    [
        'title' => 'Build Brand Into Marketing: Tackling the New Marketing Landscape',
        'secondary_author' => 'Ohula Malsh',
        'progress' => '38',
        'duration' => '09 hr 20 mins',
        'lectures' => '35 Lectures',
        'regular_price' => '',
        'sale_price' => '136.00',
        'rating' => '4.9',
    ],
    [
        'title' => 'Graphic Design: Illustrating Badges and Icons with Geometric Shapes',
        'secondary_author' => 'Ohula Malsh',
        'progress' => '0',
        'duration' => '06 hr 50 mins',
        'lectures' => '22 Lectures',
        'regular_price' => '',
        'sale_price' => '237.00',
        'rating' => '4.8',
    ],
];

foreach ($dummy_courses as $item) {
    $post_data = [
        'post_title' => $item['title'],
        'post_content' => 'This is a sample description for the course: ' . $item['title'] . '. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        'post_status' => 'publish',
        'post_type' => 'course',
        'post_author' => 1,
    ];
    
    $post_id = wp_insert_post($post_data);
    
    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_course_secondary_author', $item['secondary_author']);
        update_post_meta($post_id, '_course_progress', $item['progress']);
        update_post_meta($post_id, '_course_duration', $item['duration']);
        update_post_meta($post_id, '_course_lectures', $item['lectures']);
        update_post_meta($post_id, '_course_regular_price', $item['regular_price']);
        update_post_meta($post_id, '_course_sale_price', $item['sale_price']);
        update_post_meta($post_id, '_course_rating', $item['rating']);
        echo "Created course: {$item['title']}\n";
    }
}

echo "All dummy courses added successfully.\n";
