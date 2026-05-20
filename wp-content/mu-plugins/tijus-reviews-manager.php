<?php
/**
 * Plugin Name: Tijus Reviews Manager
 * Description: Adds a custom sidebar menu option in WP Admin to manage Course Reviews and ensures guest reviews are globally supported for courses.
 * Author: Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add a custom top-level menu for managing Course Reviews.
 */
function tijus_add_reviews_admin_menu() {
    add_menu_page(
        'Course Reviews',
        'Course Reviews',
        'moderate_comments',
        'edit-comments.php?post_type=course',
        '',
        'dashicons-star-filled',
        25
    );
}
add_action( 'admin_menu', 'tijus_add_reviews_admin_menu' );

/**
 * Ensure guest reviews are allowed specifically for courses.
 * This overrides the global "Users must be registered and logged in to comment" 
 * setting ONLY for courses, allowing guests to use the interactive star rating form.
 */
function tijus_force_guest_course_reviews( $val ) {
	// If viewing a single course
	if ( is_singular('course') ) {
		return '0';
	}
	
	// If processing a comment submission for a course
	if ( isset( $_POST['comment_post_ID'] ) ) {
		$post_type = get_post_type( absint( $_POST['comment_post_ID'] ) );
		if ( $post_type === 'course' ) {
			return '0';
		}
	}
	
    return $val;
}
add_filter( 'pre_option_comment_registration', 'tijus_force_guest_course_reviews' );
