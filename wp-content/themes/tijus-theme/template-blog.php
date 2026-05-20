<?php
/**
 * Template Name: Blog Page
 * 
 * Defines the custom blog grid layout
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
                <li class="active">Blog</li>
            </ul>
            <h2 class="title">Our <span>Blog</span></h2>
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

<!-- Blog Start -->
<div class="section section-padding mt-n10">
    <div class="container">

        <!-- Blog Wrapper Start -->
        <div class="blog-wrapper">
            <div class="row">
				<?php 
				$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
				$args = array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'paged' => $paged,
				);
				
				$blog_query = new WP_Query($args);
				
				if ( $blog_query->have_posts() ) : ?>
					<?php
					while ( $blog_query->have_posts() ) :
						$blog_query->the_post();
						?>
						<div class="col-lg-4 col-md-6 mb-4">
							<!-- Single Blog Start -->
							<div class="single-blog" style="height: 100%;">
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
							<!-- Single Blog End -->
						</div>
						<?php
					endwhile;
					?>
				<?php else : ?>
					<div class="col-12">
						<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'tijus-theme' ); ?></p>
					</div>
				<?php endif; ?>
            </div>
        </div>
        <!-- Blog Wrapper End -->

        <!-- Page Pagination -->
        <div class="page-pagination">
			<?php
			$pagination_links = paginate_links( array(
				'total'     => $blog_query->max_num_pages,
				'current'   => $paged,
				'prev_text' => '<i class="icofont-rounded-left"></i>',
				'next_text' => '<i class="icofont-rounded-right"></i>',
				'type'      => 'list',
			) );

			if ( $pagination_links ) {
				// Re-format WordPress UL class to Theme UL class
				$pagination_links = str_replace( "<ul class='page-numbers'>", '<ul class="pagination justify-content-center">', $pagination_links );
				
				// Translate WordPress 'span.current' to Theme 'a.active' to fix misalignment
				$pagination_links = preg_replace( '/<span aria-current="page" class="page-numbers current">(.*?)<\/span>/', '<a class="page-numbers active" href="javascript:void(0);">$1</a>', $pagination_links );

				echo $pagination_links;
			}
			wp_reset_postdata();
			?>
        </div>
        <!-- Page Pagination End -->

    </div>
</div>
<!-- Blog End -->

<?php
get_footer();
