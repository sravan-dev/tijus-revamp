<?php
get_header();

$has_content = false;

if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        $content      = get_the_content();
        $elementor    = get_post_meta( get_the_ID(), '_elementor_data', true );

        if ( ! empty( trim( $content ) ) || ! empty( $elementor ) ) {
            $has_content = true;
            the_content();
        }
    }
}

if ( ! $has_content ) {
    get_template_part( 'template-parts/static-home' );
}

get_footer();
