<?php
/**
 * Template Name: Careers Page
 */

get_header();
$theme_uri = get_template_directory_uri();
?>

<!-- Page Banner Start -->
<div class="section page-banner">

    <img class="shape-1 animation-round" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-8.png" alt="Shape">

    <img class="shape-2" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-23.png" alt="Shape">

    <div class="container">
        <!-- Page Banner Start -->
        <div class="page-banner-content">
            <ul class="breadcrumb">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                <li class="active"><?php the_title(); ?></li>
            </ul>
            <h2 class="title">Join Our <span>Team</span></h2>
        </div>
        <!-- Page Banner End -->
    </div>

    <!-- Shape Icon Box Start -->
    <div class="shape-icon-box">

        <img class="icon-shape-1 animation-left" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-5.png" alt="Shape">

        <div class="box-content">
            <div class="box-wrapper">
                <i class="flaticon-badge"></i>
            </div>
        </div>

        <img class="icon-shape-2" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-6.png" alt="Shape">

    </div>
    <!-- Shape Icon Box End -->

    <img class="shape-3" src="<?php echo $theme_uri; ?>/assets/images/shape/shape-24.png" alt="Shape">

</div>
<!-- Page Banner End -->

<!-- Careers Start -->
<div class="section section-padding">
    <div class="container">

        <!-- Careers Wrapper Start  -->
        <div class="courses-wrapper-02">
            <div class="row">

            <?php
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $args = [
                'post_type'      => 'career',
                'posts_per_page' => 12,
                'paged'          => $paged,
                'post_status'    => 'publish'
            ];

            $careers_query = new WP_Query( $args );

            if ( $careers_query->have_posts() ) :
                while ( $careers_query->have_posts() ) : $careers_query->the_post();
                    ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                        <div class="single-courses" style="background:#fff; padding:30px; border-radius:10px; box-shadow:0 0 20px rgba(0,0,0,0.05); height:100%; display:flex; flex-direction:column; transition: all 0.3s ease;">
                            <div class="courses-images" style="margin-bottom:20px; border-radius:10px; overflow:hidden;">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if(has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium_large', ['style' => 'width:100%; height:200px; object-fit:cover;']); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url($theme_uri . '/assets/images/blog/blog-01.jpg'); ?>" style="width:100%; height:200px; object-fit:cover;" alt="<?php the_title_attribute(); ?>">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="courses-content" style="flex-grow:1;">
                                <div class="courses-author">
                                    <h4 class="title"><a href="<?php the_permalink(); ?>" style="color:#212832; font-weight:700; font-size:22px; text-decoration:none;"><?php the_title(); ?></a></h4>
                                </div>
                                <div style="color:#52565b; font-size:15px; line-height:1.6; margin-bottom:20px; margin-top:10px;">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </div>
                            </div>
                            <div class="courses-price-review" style="margin-top:auto; border-top:1px solid #eee; padding-top:20px;">
                                <div class="courses-price" style="width: 100%;">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-hover-dark" style="width: 100%; text-align: center; border-radius: 5px;">View Details & Apply</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<div class="col-12"><div style="background: rgba(48, 146, 85, 0.1); border-left: 4px solid #309255; padding: 20px; border-radius: 4px; color: #309255; font-size: 16px;">We currently do not have any open positions. Please check back later!</div></div>';
            endif;
            ?>

            </div>
            
            <div id="tijus-courses-pagination-wrapper">
            <?php
            // Pagination
            $total_pages = $careers_query->max_num_pages;
            if ( $total_pages > 1 ) {
                $current_page = max( 1, get_query_var( 'paged' ) );
                echo '<div class="col-12 mt-4 text-center pagination-area">';
                echo paginate_links( array(
                    'base'      => get_pagenum_link( 1 ) . '%_%',
                    'format'    => 'page/%#%',
                    'current'   => $current_page,
                    'total'     => $total_pages,
                    'prev_text' => '<i class="icofont-rounded-left"></i>',
                    'next_text' => '<i class="icofont-rounded-right"></i>',
                ) );
                echo '</div>';
            }
            ?>
            </div>
        </div>
        <!-- Careers Wrapper End  -->
    </div>
</div>
<!-- Careers End -->

<?php
get_footer();
