<?php
/**
 * Single Course Template
 *
 * @package tijus-theme
 */

get_header();

while ( have_posts() ) :
	the_post();

	$author_name      = get_the_author();
	$author_avatar    = get_avatar_url( get_the_author_meta( 'ID' ), [ 'size' => 100 ] );
	if ( ! $author_avatar ) {
		$author_avatar = get_template_directory_uri() . '/assets/images/author/author-01.jpg';
	}

	$secondary_author = get_post_meta( get_the_ID(), '_course_secondary_author', true );
	$progress         = get_post_meta( get_the_ID(), '_course_progress', true );
	$duration         = get_post_meta( get_the_ID(), '_course_duration', true );
	if ( ! $duration ) $duration = '08 hr 15 mins';
	
	$lectures         = get_post_meta( get_the_ID(), '_course_lectures', true );
	if ( ! $lectures ) $lectures = '29';
	
	$regular_price    = get_post_meta( get_the_ID(), '_course_regular_price', true );
	$sale_price       = get_post_meta( get_the_ID(), '_course_sale_price', true );
	$rating           = get_post_meta( get_the_ID(), '_course_rating', true );
	$level            = get_post_meta( get_the_ID(), '_course_level', true );
	if ( ! $level ) $level = 'Beginner';
	
	$language         = get_post_meta( get_the_ID(), '_course_language', true );
	if ( ! $language ) $language = 'English';
	
	$certificate      = get_post_meta( get_the_ID(), '_course_certificate', true );
	if ( ! $certificate ) $certificate = 'Yes';

	if ( get_comments_number() == 0 ) {
		$rating = '0.0';
	} else if ( ! $rating ) {
		$rating = '0.0';
	} else {
	    $rating = number_format( (float)$rating, 1 );
	}
	$rating_percent = ( floatval( $rating ) / 5 ) * 100;
	if ( $rating_percent > 100 ) $rating_percent = 100;

	$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $thumbnail_url ) {
		$thumbnail_url = get_post_meta( get_the_ID(), '_course_thumbnail_url', true );
	}
	if ( ! $thumbnail_url ) {
		$thumbnail_url = get_template_directory_uri() . '/assets/images/courses/courses-details.jpg';
	}
	
	$price_display = $sale_price ? $sale_price : $regular_price;
	if ( is_numeric( $price_display ) ) {
		$price_display = '$' . $price_display;
	} else if ( empty( $price_display ) ) {
		$price_display = 'Free';
	}
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
                        <li><a href="<?php echo esc_url( get_post_type_archive_link( 'course' ) ); ?>">Courses</a></li>
                        <li class="active"><?php the_title(); ?></li>
                    </ul>
                    <h2 class="title">Courses <span> Details</span></h2>
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
        <div class="section section-padding mt-n10">
            <div class="container">
                <div class="row gx-10">
                    <div class="col-lg-8">

                        <!-- Courses Details Start -->
                        <div class="courses-details">

                            <div class="courses-details-images">
                                <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php the_title_attribute(); ?>">
                                <span class="tags">Course</span>

                                <?php
                                $video_type = get_post_meta( get_the_ID(), '_course_video_type', true );
                                $video_url  = get_post_meta( get_the_ID(), '_course_video_url', true );
                                if ( $video_url ) :
                                ?>
                                <div class="courses-play">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/courses/circle-shape.png" alt="Play">
                                    <?php if ( $video_type === 'local' ) : ?>
                                        <a class="play video-popup-local" href="<?php echo esc_url( $video_url ); ?>" data-type="local"><i class="flaticon-play"></i></a>
                                    <?php else : ?>
                                        <a class="play video-popup" href="<?php echo esc_url( $video_url ); ?>"><i class="flaticon-play"></i></a>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <h2 class="title"><?php the_title(); ?></h2>

                            <div class="courses-details-admin">
                                <div class="admin-author">
                                    <div class="author-thumb">
                                        <img src="<?php echo esc_url( $author_avatar ); ?>" alt="Author">
                                    </div>
                                    <div class="author-content">
                                        <a class="name" href="#"><?php echo esc_html( $author_name ); ?></a>
                                        <?php
                                        $enrolled_users = get_post_meta( get_the_ID(), '_enrolled_users', true );
                                        $enroll_count = is_array( $enrolled_users ) ? count( $enrolled_users ) : 0;
                                        ?>
                                        <span class="Enroll"><?php echo esc_html( $enroll_count ); ?> Enrolled Students</span>
                                    </div>
                                </div>
                                <div class="admin-rating" style="display: flex; align-items: center;">
                                    <span class="rating-count"><?php echo esc_html( $rating ); ?></span>
                                    <span class="rating-star">
											<span class="rating-bar" style="width: <?php echo esc_attr( $rating_percent ); ?>%;"></span>
                                    </span>
                                    <span class="rating-text" style="margin-right: 15px;">(<?php echo esc_html( get_comments_number() ); ?> Rating)</span>
                                    <a href="javascript:void(0)" class="tijus-love-btn" data-target-id="<?php echo get_the_ID(); ?>" style="color: #999; font-size: 24px; line-height: 1;">
                                        <i class="icofont-heart-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Courses Details Tab Start -->
                            <div class="courses-details-tab">

                                <!-- Details Tab Menu Start -->
                                <div class="details-tab-menu">
                                    <ul class="nav justify-content-center">
                                        <li><button class="active" data-bs-toggle="tab" data-bs-target="#description">Description</button></li>
                                        <li><button data-bs-toggle="tab" data-bs-target="#testimonials">Testimonials</button></li>
                                        <li><button data-bs-toggle="tab" data-bs-target="#reviews">Reviews</button></li>
                                        <li><button data-bs-toggle="tab" data-bs-target="#faqs">FAQ</button></li>
                                    </ul>
                                </div>
                                <!-- Details Tab Menu End -->

                                <!-- Details Tab Content Start -->
                                <div class="details-tab-content">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="description">

                                            <!-- Tab Description Start -->
                                            <div class="tab-description">
                                                <div class="description-wrapper">
                                                    <?php the_content(); ?>
                                                </div>
                                            </div>
                                            <!-- Tab Description End -->

                                        </div>
                                        <div class="tab-pane fade" id="testimonials">

                                            <!-- Tab Testimonials Start -->
                                            <div class="tab-testimonials" style="margin-top: 30px;">
                                                <h3 class="tab-title">Student Testimonials:</h3>

                                                <?php
                                                $testimonials = get_post_meta( get_the_ID(), '_course_testimonials', true );
                                                if ( is_array( $testimonials ) && ! empty( $testimonials ) ) :
                                                ?>
                                                <style>
                                                    .testimonial-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 20px; }
                                                    .testimonial-card { background: #f8f9fa; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.2s; }
                                                    .testimonial-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.12); }
                                                    .testimonial-video-wrap { position: relative; padding-top: 56.25%; background: #000; cursor: pointer; }
                                                    .testimonial-video-wrap img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
                                                    .testimonial-play-icon { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 50px; height: 50px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
                                                    .testimonial-play-icon::after { content: ''; display: block; width: 0; height: 0; border-style: solid; border-width: 8px 0 8px 16px; border-color: transparent transparent transparent #333; margin-left: 3px; }
                                                    .testimonial-card-title { padding: 12px 15px; font-size: 14px; font-weight: 600; color: #333; text-align: center; }
                                                    .testimonial-video-wrap video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
                                                    @media (max-width: 992px) { .testimonial-grid { grid-template-columns: repeat(2, 1fr); } }
                                                    @media (max-width: 576px) { .testimonial-grid { grid-template-columns: 1fr; } }
                                                </style>
                                                <div class="testimonial-grid">
                                                    <?php foreach ( $testimonials as $ti => $t ) :
                                                        $t_type  = $t['type'] ?? 'youtube';
                                                        $t_url   = $t['url'] ?? '';
                                                        $t_title = $t['title'] ?? '';

                                                        // Get YouTube thumbnail
                                                        $yt_thumb = '';
                                                        if ( $t_type === 'youtube' && $t_url ) {
                                                            preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $t_url, $m);
                                                            if ( ! empty( $m[1] ) ) {
                                                                $yt_thumb = 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
                                                            }
                                                        }
                                                    ?>
                                                    <div class="testimonial-card">
                                                        <div class="testimonial-video-wrap">
                                                            <?php if ( $t_type === 'youtube' ) : ?>
                                                                <a href="<?php echo esc_url( $t_url ); ?>" class="video-popup" style="display:block;position:absolute;top:0;left:0;width:100%;height:100%;z-index:2;">
                                                                    <?php if ( $yt_thumb ) : ?>
                                                                        <img src="<?php echo esc_url( $yt_thumb ); ?>" alt="<?php echo esc_attr( $t_title ); ?>">
                                                                    <?php endif; ?>
                                                                    <span class="testimonial-play-icon"></span>
                                                                </a>
                                                            <?php else : ?>
                                                                <a href="<?php echo esc_url( $t_url ); ?>" class="video-popup-local" style="display:block;position:absolute;top:0;left:0;width:100%;height:100%;z-index:2;">
                                                                    <video muted preload="metadata" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;">
                                                                        <source src="<?php echo esc_url( $t_url ); ?>#t=0.5" type="video/mp4">
                                                                    </video>
                                                                    <span class="testimonial-play-icon"></span>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ( $t_title ) : ?>
                                                            <div class="testimonial-card-title"><?php echo esc_html( $t_title ); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php else : ?>
                                                    <p style="color:#888; margin-top:15px;">No testimonial videos added yet.</p>
                                                <?php endif; ?>

                                            </div>
                                            <!-- Tab Testimonials End -->

                                        </div>
                                        <div class="tab-pane fade" id="reviews">

                                            <!-- Tab Reviews Start -->
                                            <div class="tab-reviews">
                                                <?php
                                                if ( comments_open() || get_comments_number() ) {
                                                    comments_template();
                                                }
                                                ?>
                                            </div>
                                            <!-- Tab Reviews End -->

                                        </div>
                                        <div class="tab-pane fade" id="faqs">

                                            <!-- Tab FAQs Start -->
                                            <div class="tab-faqs" style="margin-top: 30px;">
                                                <?php
                                                $faqs = get_post_meta( get_the_ID(), '_course_faqs', true );
                                                if ( is_array( $faqs ) && ! empty( $faqs ) ) {
                                                    echo '<div class="accordion" id="courseFaqAccordion">';
                                                    foreach ( $faqs as $i => $faq ) {
                                                        $collapse_id = 'collapseFaq' . $i;
                                                        $heading_id  = 'headingFaq' . $i;
                                                        $is_expanded = ( $i === 0 ) ? 'true' : 'false';
                                                        $collapse_class = ( $i === 0 ) ? 'accordion-collapse collapse show' : 'accordion-collapse collapse';
                                                        $btn_class   = ( $i === 0 ) ? 'accordion-button' : 'accordion-button collapsed';
                                                        ?>
                                                        <div class="accordion-item" style="border: 1px solid #eee; margin-bottom: 10px; border-radius: 5px; overflow: hidden;">
                                                            <h2 class="accordion-header" id="<?php echo $heading_id; ?>" style="margin:0;">
                                                                <button class="<?php echo $btn_class; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapse_id; ?>" aria-expanded="<?php echo $is_expanded; ?>" aria-controls="<?php echo $collapse_id; ?>" style="background-color: #f9f9f9; padding: 15px 20px; font-weight: 600; box-shadow: none;">
                                                                    <?php echo esc_html( $faq['question'] ); ?>
                                                                </button>
                                                            </h2>
                                                            <div id="<?php echo $collapse_id; ?>" class="<?php echo $collapse_class; ?>" aria-labelledby="<?php echo $heading_id; ?>" data-bs-parent="#courseFaqAccordion">
                                                                <div class="accordion-body" style="padding: 20px; color: #555;">
                                                                    <?php echo wpautop( esc_html( $faq['answer'] ) ); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    echo '</div>';
                                                } else {
                                                    echo '<p>No FAQs available for this course yet.</p>';
                                                }
                                                ?>
                                            </div>
                                            <!-- Tab FAQs End -->

                                        </div>
                                    </div>
                                </div>
                                <!-- Details Tab Content End -->

                            </div>
                            <!-- Courses Details Tab End -->

                        </div>
                        <!-- Courses Details End -->

                    </div>
                    <div class="col-lg-4">
                        <!-- Courses Details Sidebar Start -->
                        <div class="sidebar">

                            <!-- Sidebar Widget Information Start -->
                            <div class="sidebar-widget widget-information">
                                <div class="info-price">
                                    <span class="price"><?php echo esc_html( $price_display ); ?></span>
                                </div>
                                <div class="info-list">
                                    <ul>
                                        <li><i class="icofont-man-in-glasses"></i> <strong>Instructor</strong> <span><?php echo esc_html( $author_name ); ?></span></li>
                                        <li><i class="icofont-clock-time"></i> <strong>Duration</strong> <span><?php echo esc_html( $duration ); ?></span></li>
                                        <li><i class="icofont-ui-video-play"></i> <strong>Lectures</strong> <span><?php echo esc_html( $lectures ); ?></span></li>
                                        <li><i class="icofont-bars"></i> <strong>Level</strong> <span><?php echo esc_html( $level ); ?></span></li>
                                        <li><i class="icofont-book-alt"></i> <strong>Language</strong> <span><?php echo esc_html( $language ); ?></span></li>
                                        <li><i class="icofont-certificate-alt-1"></i> <strong>Certificate</strong> <span><?php echo esc_html( $certificate ); ?></span></li>
                                    </ul>
                                </div>
                                <div class="info-btn">
                                    <a href="#" class="btn btn-primary btn-hover-dark" data-bs-toggle="modal" data-bs-target="#enrollmentModal">Enroll Now</a>
                                </div>
                            </div>
                            <!-- Sidebar Widget Information End -->

                            <!-- Sidebar Widget Share Start -->
                            <div class="sidebar-widget">
                                <h4 class="widget-title">Share Course:</h4>

                                <?php
                                $share_url = urlencode( get_permalink() );
                                $share_title = urlencode( get_the_title() );
                                ?>
                                <ul class="social">
                                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener"><i class="flaticon-facebook"></i></a></li>
                                    <li><a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener"><i class="flaticon-linkedin"></i></a></li>
                                    <li><a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" target="_blank" rel="noopener"><i class="flaticon-twitter"></i></a></li>
                                    <li><a href="https://web.skype.com/share?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" target="_blank" rel="noopener"><i class="flaticon-skype"></i></a></li>
                                    <li><a href="mailto:?subject=<?php echo $share_title; ?>&body=Check%20out%20this%20course:%20<?php echo $share_url; ?>"><i class="icofont-envelope"></i></a></li>
                                </ul>
                            </div>
                            <!-- Sidebar Widget Share End -->

                        </div>
                        <!-- Courses Details Sidebar End -->
                    </div>
                </div>
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
endwhile;

get_footer();
?>
