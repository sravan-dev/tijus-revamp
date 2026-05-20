<?php
/**
 * Template Name: About Page
 *
 * @package tijus-theme
 */

get_header();

while ( have_posts() ) {
    the_post();

    $elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true );
    $has_elementor  = ! empty( $elementor_data ) && $elementor_data !== '[]';

    // Always call the_content() so Elementor editor can detect the content area.
    // On the frontend it outputs Elementor-rendered markup (if data exists) or nothing.
    the_content();

    if ( ! $has_elementor ) {
        // No Elementor content yet – show the static template
        get_template_part( 'template-parts/static-about' );
    }
}

get_footer();
