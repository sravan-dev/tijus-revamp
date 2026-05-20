<?php
/**
 * The template for displaying all single pages
 *
 * @package tijus-theme
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <div <?php post_class(); ?>>
                <?php the_content(); ?>
            </div>
            <?php
        endwhile; // End of the loop.
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
