<?php
/**
 * Template Name: Courses Page
 */

get_header();
?>

<!-- Page Banner Start -->
<div class="section page-banner">

    <img class="shape-1 animation-round" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-8.png" alt="Shape">

    <img class="shape-2" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-23.png" alt="Shape">

    <div class="container">
        <!-- Page Banner Start -->
        <div class="page-banner-content">
            <ul class="breadcrumb">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                <li class="active"><?php the_title(); ?></li>
            </ul>
            <h2 class="title">My <span>Courses</span></h2>
        </div>
        <!-- Page Banner End -->
    </div>

    <!-- Shape Icon Box Start -->
    <div class="shape-icon-box">

        <img class="icon-shape-1 animation-left" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-5.png" alt="Shape">

        <div class="box-content">
            <div class="box-wrapper">
                <i class="flaticon-badge"></i>
            </div>
        </div>

        <img class="icon-shape-2" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-6.png" alt="Shape">

    </div>
    <!-- Shape Icon Box End -->

    <img class="shape-3" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-24.png" alt="Shape">

    <img class="shape-author" src="<?php echo get_template_directory_uri(); ?>/assets/images/author/author-11.jpg" alt="Shape">

</div>
<!-- Page Banner End -->

<!-- Courses Start -->
<div class="section section-padding">
    <div class="container">

        <!-- Courses Category Wrapper Start  -->
        <div class="courses-category-wrapper m1-style-wrapper">
            <ul class="category-menu" id="tijus-courses-category-menu">
                <?php
                $current_cat = isset( $_GET['course_category'] ) ? sanitize_text_field( $_GET['course_category'] ) : '';
                ?>
                <li><a class="<?php echo empty( $current_cat ) ? 'active' : ''; ?>" href="<?php echo esc_url( get_permalink() ); ?>">All Courses</a></li>
                <?php
                // Get categories in the same order as the mega menu
                $cat_slugs = ['language-exams', 'healthcare-licensing', 'diploma-programs', 'wellness', 'tijus-media-school'];
                
                foreach ( $cat_slugs as $slug ) {
                    $category = get_term_by( 'slug', $slug, 'course_category' );
                    if ( $category && ! is_wp_error( $category ) ) {
                        $is_active = ( $current_cat === $category->slug ) ? 'active' : '';
                        $cat_url = add_query_arg( 'course_category', $category->slug, get_permalink() );
                        echo '<li><a class="' . esc_attr( $is_active ) . '" href="' . esc_url( $cat_url ) . '">' . esc_html( $category->name ) . '</a></li>';
                    }
                }
                ?>
            </ul>

            <form id="tijus-courses-filter-form" action="<?php echo esc_url( get_permalink() ); ?>" method="get">
                <div class="courses-search search-2 m1-search-style">
                    <input type="text" name="course_search" placeholder="" value="<?php echo isset($_GET['course_search']) ? esc_attr($_GET['course_search']) : ''; ?>">
                    <button type="submit"><i class="icofont-search"></i></button>
                    <!-- Preserve category if currently filtering -->
                    <?php if ( isset($_GET['course_category']) ) : ?>
                        <input type="hidden" name="course_category" value="<?php echo esc_attr($_GET['course_category']); ?>" />
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <!-- Courses Category Wrapper End  -->

        <!-- Courses Wrapper Start  -->
        <div class="courses-wrapper-02">
            <div class="row" id="tijus-courses-grid" style="position: relative;">

            <?php
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $args = [
                'post_type'      => 'course',
                'posts_per_page' => 12,
                'paged'          => $paged,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ];

            if ( ! empty( $current_cat ) ) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => 'course_category',
                        'field'    => 'slug',
                        'terms'    => $current_cat,
                    ],
                ];
            }

            if ( isset( $_GET['course_search'] ) && ! empty( $_GET['course_search'] ) ) {
                $args['s'] = sanitize_text_field( $_GET['course_search'] );
            }

            $courses_query = new WP_Query( $args );

            if ( $courses_query->have_posts() ) :
                while ( $courses_query->have_posts() ) : $courses_query->the_post();
                    get_template_part( 'template-parts/content', 'course' );
                endwhile;
                wp_reset_postdata();
            else :
                echo '<div class="col-12"><p>No courses found.</p></div>';
            endif;
            ?>

            </div>
            
            <div id="tijus-courses-pagination-wrapper">
            <?php
            $total_pages = $courses_query->max_num_pages;
            if ( $total_pages > 1 ) {
                $current_page = max( 1, get_query_var( 'paged' ) );
                $pagination_links = paginate_links( array(
                    'base'      => get_pagenum_link( 1 ) . '%_%',
                    'format'    => 'page/%#%',
                    'current'   => $current_page,
                    'total'     => $total_pages,
                    'prev_text' => '<i class="icofont-rounded-left"></i>',
                    'next_text' => '<i class="icofont-rounded-right"></i>',
                    'type'      => 'list',
                ) );
                if ( $pagination_links ) {
                    $pagination_links = str_replace( "<ul class='page-numbers'>", '<ul class="pagination justify-content-center">', $pagination_links );
                    $pagination_links = preg_replace( '/<span aria-current="page" class="page-numbers current">(.*?)<\/span>/', '<a class="page-numbers active" href="javascript:void(0);">$1</a>', $pagination_links );
                    echo '<div class="page-pagination mt-4">' . $pagination_links . '</div>';
                }
            }
            ?>
            </div>
        </div>
        <!-- Courses Wrapper End  -->

    </div>
</div>
<!-- Courses End -->

<!-- Download App Start -->
<div class="section section-padding download-section">

    <div class="app-shape-1"></div>
    <div class="app-shape-2"></div>
    <div class="app-shape-3"></div>
    <div class="app-shape-4"></div>

    <div class="container">

        <!-- Download App Wrapper Start -->
        <div class="download-app-wrapper mt-n6">

            <!-- Section Title Start -->
            <div class="section-title section-title-white">
                <h5 class="sub-title">Ready to start?</h5>
                <h2 class="main-title">Download our mobile app. for easy to start your course.</h2>
            </div>
            <!-- Section Title End -->

            <img class="shape-1 animation-right" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-14.png" alt="Shape">

            <!-- Download App Button End -->
            <div class="download-app-btn">
                <ul class="app-btn">
                    <li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/google-play.png" alt="Google Play"></a></li>
                    <li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/app-store.png" alt="App Store"></a></li>
                </ul>
            </div>
            <!-- Download App Button End -->

        </div>
        <!-- Download App Wrapper End -->

    </div>
</div>
<!-- Download App End -->

<?php
get_footer();
?>
