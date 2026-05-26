        <!-- Slider Start -->
        <div class="section slider-section">

            <!-- Slider Shape Start -->
            <div class="slider-shape">
                <img class="shape-1 animation-round" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-8.png" alt="Shape">
            </div>
            <!-- Slider Shape End -->

            <div class="container">

                <!-- Slider Content Start -->
                <div class="slider-content">
                    <h4 class="sub-title">Start your favourite course</h4>
                    <h2 class="main-title">Now learning from anywhere, and build your <span>bright career.</span></h2>
                    <p>It has survived not only five centuries but also the leap into electronic typesetting.</p>
                    <a class="btn btn-primary btn-hover-dark" href="<?php echo home_url('/courses/'); ?>">Start A Course</a>
                </div>
                <!-- Slider Content End -->

            </div>

            <!-- Slider Courses Box Start -->
            <div class="slider-courses-box">

                <img class="shape-1 animation-left" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-5.png" alt="Shape">

                <div class="box-content">
                    <div class="box-wrapper">
                        <i class="flaticon-open-book"></i>
                        <span class="count">1,235</span>
                        <p>courses</p>
                    </div>
                </div>

                <img class="shape-2" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-6.png" alt="Shape">

            </div>
            <!-- Slider Courses Box End -->

            <!-- Slider Rating Box Start -->
            <div class="slider-rating-box">

                <div class="box-rating">
                    <div class="box-wrapper">
                        <span class="count">4.8 <i class="flaticon-star"></i></span>
                        <p>Rating (86K)</p>
                    </div>
                </div>

                <img class="shape animation-up" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-7.png" alt="Shape">

            </div>
            <!-- Slider Rating Box End -->

            <!-- Slider Images Start -->
            <div class="slider-images">
                <div class="images">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/slider/slider-1.png" alt="Slider">
                </div>
            </div>
            <!-- Slider Images End -->

            <!-- Slider Video Start -->
            <div class="slider-video">
                <img class="shape-1" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-9.png" alt="Shape">

                <div class="video-play">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-10.png" alt="Shape">
                    <a href="https://www.youtube.com/watch?v=BRvyWfuxGuU" class="play video-popup"><i class="flaticon-play"></i></a>
                </div>
            </div>
            <!-- Slider Video End -->

        </div>
        <!-- Slider End -->

        <!-- All Courses Start -->
        <div class="section section-padding-02">
            <div class="container">

                <!-- All Courses Top Start -->
                <div class="courses-top">

                    <!-- Section Title Start -->
                    <div class="section-title shape-01">
                        <h2 class="main-title">All <span>Courses</span> of Edule</h2>
                    </div>
                    <!-- Section Title End -->

                    <!-- Courses Search Start -->
                    <div class="courses-search">
                        <form action="#">
                            <input type="text" placeholder="Search your course">
                            <button><i class="flaticon-magnifying-glass"></i></button>
                        </form>
                    </div>
                    <!-- Courses Search End -->

                </div>
                <!-- All Courses Top End -->

                <!-- All Courses Tabs Menu Start -->
                <?php
                // Order categories to match the mega menu
                $cat_slugs_order = ['language-exams', 'healthcare-licensing', 'diploma-programs', 'wellness', 'tijus-media-school'];
                $categories = [];
                foreach ( $cat_slugs_order as $slug ) {
                    $term = get_term_by( 'slug', $slug, 'course_category' );
                    if ( $term && ! is_wp_error( $term ) ) {
                        $categories[] = $term;
                    }
                }
                // Fallback to dummy names
                if ( empty( $categories ) ) {
                    $categories = [
                        (object)['slug' => 'language-exams', 'name' => 'Language Exams'],
                        (object)['slug' => 'healthcare-licensing', 'name' => 'Healthcare Licensing'],
                        (object)['slug' => 'diploma-programs', 'name' => 'Diploma Programs'],
                        (object)['slug' => 'wellness', 'name' => 'Wellness'],
                        (object)['slug' => 'tijus-media-school', 'name' => "Tiju's Media School"],
                    ];
                }
                ?>
                <div class="courses-tabs-menu courses-active">
                    <div class="swiper-container">
                        <ul class="swiper-wrapper nav">
                            <?php foreach ( $categories as $index => $category ) : ?>
                                <li class="swiper-slide">
                                    <button class="<?php echo ( $index === 0 ) ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#tabs-<?php echo esc_attr( $category->slug ); ?>">
                                        <?php echo esc_html( $category->name ); ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Add Pagination -->
                    <div class="swiper-button-next"><i class="icofont-rounded-right"></i></div>
                    <div class="swiper-button-prev"><i class="icofont-rounded-left"></i></div>
                </div>
                <!-- All Courses Tabs Menu End -->

                <!-- All Courses tab content Start -->
                <div class="tab-content courses-tab-content">
                    <?php foreach ( $categories as $index => $category ) : ?>
                        <div class="tab-pane fade <?php echo ( $index === 0 ) ? 'show active' : ''; ?>" id="tabs-<?php echo esc_attr( $category->slug ); ?>">
                            
                            <!-- All Courses Wrapper Start -->
                            <div class="courses-wrapper">
                                <div class="row">
                                    <?php
                                    // Allow dummy categories to display all courses as demo, otherwise strictly filter by term if terms exist
                                    $has_real_terms = ! empty( get_terms( [ 'taxonomy' => 'course_category', 'hide_empty' => false ] ) );
                                    
                                    $args = [
                                        'post_type'      => 'course',
                                        'posts_per_page' => 6,
                                        'orderby'        => 'menu_order',
                                        'order'          => 'ASC',
                                    ];
                                    
                                    if ( $has_real_terms ) {
                                        $args['tax_query'] = [
                                            [
                                                'taxonomy' => 'course_category',
                                                'field'    => 'slug',
                                                'terms'    => $category->slug,
                                            ]
                                        ];
                                    }

                                    $cat_query = new WP_Query( $args );
                                    
                                    if ( $cat_query->have_posts() ) :
                                        while ( $cat_query->have_posts() ) : $cat_query->the_post();
                                            get_template_part( 'template-parts/content', 'course' );
                                        endwhile;
                                        wp_reset_postdata();
                                    else :
                                        echo '<div class="col-12"><p>No courses found in ' . esc_html( $category->name ) . '.</p></div>';
                                    endif;
                                    ?>
                                </div>
                            </div>
                            <!-- All Courses Wrapper End -->
                            
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- All Courses tab content End -->

                <!-- All Courses BUtton Start -->
                <div class="courses-btn text-center">
                    <a href="courses.html" class="btn btn-secondary btn-hover-primary">Other Course</a>
                </div>
                <!-- All Courses BUtton End -->

            </div>
        </div>
        <!-- All Courses End -->

        <!-- Call to Action Start -->
        <div class="section section-padding-02">
            <div class="container">

                <!-- Call to Action Wrapper Start -->
                <div class="call-to-action-wrapper">

                    <img class="cat-shape-01 animation-round" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-12.png" alt="Shape">
                    <img class="cat-shape-02" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-13.svg" alt="Shape">
                    <img class="cat-shape-03 animation-round" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-12.png" alt="Shape">

                    <div class="row align-items-center">
                        <div class="col-md-6">

                            <!-- Section Title Start -->
                            <div class="section-title shape-02">
                                <h5 class="sub-title">Become A Instructor</h5>
                                <h2 class="main-title">You can join with Edule as <span>a instructor?</span></h2>
                            </div>
                            <!-- Section Title End -->

                        </div>
                        <div class="col-md-6">
                            <div class="call-to-action-btn">
                                <a class="btn btn-primary btn-hover-dark" href="contact.html">Drop Information</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Call to Action Wrapper End -->

            </div>
        </div>
        <!-- Call to Action End -->

        <!-- How It Work End -->
        <div class="section section-padding mt-n1">
            <div class="container">

                <!-- Section Title Start -->
                <div class="section-title shape-03 text-center">
                    <h5 class="sub-title">Over 1,235+ Course</h5>
                    <h2 class="main-title">How It <span> Work?</span></h2>
                </div>
                <!-- Section Title End -->

                <!-- How it Work Wrapper Start -->
                <div class="how-it-work-wrapper">

                    <!-- Single Work Start -->
                    <div class="single-work">
                        <img class="shape-1" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-15.png" alt="Shape">

                        <div class="work-icon">
                            <i class="flaticon-transparency"></i>
                        </div>
                        <div class="work-content">
                            <h3 class="title">Find Your Course</h3>
                            <p>It has survived not only centurie also leap into electronic.</p>
                        </div>
                    </div>
                    <!-- Single Work End -->

                    <!-- Single Work Start -->
                    <div class="work-arrow">
                        <img class="arrow" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-17.png" alt="Shape">
                    </div>
                    <!-- Single Work End -->

                    <!-- Single Work Start -->
                    <div class="single-work">
                        <img class="shape-2" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-15.png" alt="Shape">

                        <div class="work-icon">
                            <i class="flaticon-forms"></i>
                        </div>
                        <div class="work-content">
                            <h3 class="title">Book A Seat</h3>
                            <p>It has survived not only centurie also leap into electronic.</p>
                        </div>
                    </div>
                    <!-- Single Work End -->

                    <!-- Single Work Start -->
                    <div class="work-arrow">
                        <img class="arrow" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-17.png" alt="Shape">
                    </div>
                    <!-- Single Work End -->

                    <!-- Single Work Start -->
                    <div class="single-work">
                        <img class="shape-3" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-16.png" alt="Shape">

                        <div class="work-icon">
                            <i class="flaticon-badge"></i>
                        </div>
                        <div class="work-content">
                            <h3 class="title">Get Certificate</h3>
                            <p>It has survived not only centurie also leap into electronic.</p>
                        </div>
                    </div>
                    <!-- Single Work End -->

                </div>

            </div>
        </div>
        <!-- How It Work End -->

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

        <!-- Testimonial End -->
        <div class="section section-padding-02 mt-n1">
            <div class="container">

                <!-- Section Title Start -->
                <div class="section-title shape-03 text-center">
                    <h5 class="sub-title">Student Testimonial</h5>
                    <h2 class="main-title">Feedback From <span> Student</span></h2>
                </div>
                <!-- Section Title End -->

                <!-- Testimonial Wrapper End -->
                <div class="testimonial-wrapper testimonial-active">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php
                            $feedback_query = new WP_Query( [
                                'post_type'      => 'tijus_feedback',
                                'posts_per_page' => 10,
                                'order'          => 'DESC'
                            ] );

                            if ( $feedback_query->have_posts() ) :
                                while ( $feedback_query->have_posts() ) : $feedback_query->the_post();
                                    
                                    $designation = get_post_meta( get_the_ID(), '_feedback_designation', true );
                                    $rating      = get_post_meta( get_the_ID(), '_feedback_rating', true ) ?: 5;
                                    $rating_pct  = ( absint( $rating ) / 5 ) * 100;
                                    
                                    // Handle image precedence: 1. standard wp thumbnail, 2. seeded dummy image
                                    $img_url     = get_template_directory_uri() . '/assets/images/author/author-06.jpg'; // fallback
                                    if ( has_post_thumbnail() ) {
                                        $img_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                                    } elseif ( $dummy_img = get_post_meta( get_the_ID(), '_feedback_dummy_img', true ) ) {
                                        $img_url = get_template_directory_uri() . '/assets/images/author/' . $dummy_img;
                                    }
                                    ?>
                                    <!-- Single Testimonial Start -->
                                    <div class="single-testimonial swiper-slide">
                                        <div class="testimonial-author">
                                            <div class="author-thumb">
                                                <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                                                <i class="icofont-quote-left"></i>
                                            </div>

                                            <span class="rating-star">
                                                <span class="rating-bar" style="width: <?php echo esc_attr( $rating_pct ); ?>%;"></span>
                                            </span>
                                        </div>
                                        <div class="testimonial-content">
                                            <?php the_content(); ?>
                                            <h4 class="name"><?php the_title(); ?></h4>
                                            <span class="designation"><?php echo esc_html( $designation ); ?></span>
                                        </div>
                                    </div>
                                    <!-- Single Testimonial End -->
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                            else :
                                echo '<p style="text-align:center;width:100%;">No feedback available yet.</p>';
                            endif;
                            ?>
                        </div>

                        <!-- Add Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <!-- Testimonial Wrapper End -->

            </div>
        </div>
        <!-- Testimonial End -->

        <!-- Brand Logo Start -->
        <div class="section section-padding-02">
            <div class="container">

                <!-- Brand Logo Wrapper Start -->
                <div class="brand-logo-wrapper">

                    <img class="shape-1" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-19.png" alt="Shape">

                    <img class="shape-2 animation-round" src="<?php echo get_template_directory_uri(); ?>/assets/images/shape/shape-20.png" alt="Shape">

                    <!-- Section Title Start -->
                    <div class="section-title shape-03">
                        <h2 class="main-title">Our <span> Certifications</span></h2>
                    </div>
                    <!-- Section Title End -->

                    <!-- Brand Logo Start -->
                    <div class="brand-logo brand-active">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">

                                <?php
                                $cert_args = array(
                                    'post_type'      => 'certification',
                                    'posts_per_page' => -1, // Get all certifications
                                    'post_status'    => 'publish',
                                );
                                $cert_query = new WP_Query($cert_args);

                                if ($cert_query->have_posts()) :
                                    while ($cert_query->have_posts()) : $cert_query->the_post();
                                        if (has_post_thumbnail()) :
                                            ?>
                                            <!-- Single Brand Start -->
                                            <div class="single-brand swiper-slide">
                                                <?php the_post_thumbnail('full', array('alt' => get_the_title())); ?>
                                            </div>
                                            <!-- Single Brand End -->
                                            <?php
                                        endif;
                                    endwhile;
                                    wp_reset_postdata();
                                else :
                                    // Fallback to dummy data if no certifications are added yet
                                    for ($i = 1; $i <= 6; $i++) {
                                        echo '<!-- Single Brand Start -->';
                                        echo '<div class="single-brand swiper-slide">';
                                        echo '<img src="' . get_template_directory_uri() . '/assets/images/brand/brand-0' . $i . '.png" alt="Brand">';
                                        echo '</div>';
                                        echo '<!-- Single Brand End -->';
                                    }
                                endif;
                                ?>

                            </div>
                        </div>
                    </div>
                    <!-- Brand Logo End -->

                </div>
                <!-- Brand Logo Wrapper End -->

            </div>
        </div>
        <!-- Brand Logo End -->

        <!-- Blog Start -->
        <div class="section section-padding mt-n1">
            <div class="container">

                <!-- Section Title Start -->
                <div class="section-title shape-03 text-center">
                    <h5 class="sub-title">Latest News</h5>
                    <h2 class="main-title">Educational Tips & <span> Tricks</span></h2>
                </div>
                <!-- Section Title End -->

                <!-- Blog Wrapper Start (Dynamic Carousel) -->
                <div class="blog-wrapper blog-active swiper-container pb-5">
                    <div class="swiper-wrapper">
                        <?php
                        $args = array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 6);
                        $blog_query = new WP_Query($args);
                        if ($blog_query->have_posts()) :
                            while ($blog_query->have_posts()) : $blog_query->the_post();
                                ?>
                                <div class="swiper-slide">
                                    <div class="single-blog">
                                        <div class="blog-image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php if ( has_post_thumbnail() ) : ?>
                                                    <?php the_post_thumbnail( 'full', array( 'alt' => get_the_title() ) ); ?>
                                                <?php else : ?>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/blog/blog-01.jpg" alt="<?php the_title_attribute(); ?>">
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="blog-content">
                                            <div class="blog-author">
                                                <div class="author">
                                                    <div class="author-thumb">
                                                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                                                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 60 ); ?>
                                                        </a>
                                                    </div>
                                                    <div class="author-name">
                                                        <a class="name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
                                                    </div>
                                                </div>
                                                <div class="tag">
                                                    <?php
                                                    $categories = get_the_category();
                                                    if ( ! empty( $categories ) ) {
                                                        echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                            <div class="blog-meta">
                                                <span> <i class="icofont-calendar"></i> <?php echo get_the_date( 'd F, Y' ); ?></span>
                                                <span> <i class="icofont-heart"></i> <?php echo get_comments_number(); ?> </span>
                                            </div>
                                            <a href="<?php the_permalink(); ?>" class="btn btn-secondary btn-hover-primary">Read More</a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<div class="swiper-slide"><p>No posts found.</p></div>';
                        endif;
                        ?>
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
                <!-- Blog Wrapper End -->

            </div>
        </div>
        <!-- Blog End -->

