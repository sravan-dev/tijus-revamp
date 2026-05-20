<?php
/**
 * The sidebar containing the main widget area
 *
 * @package tijus-theme
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	// Fallback to static Edule design if no widgets are configured.
	?>
	<!-- Blog Sidebar Start -->
	<div class="sidebar">

		<!-- Sidebar Widget Search Start -->
		<div class="sidebar-widget widget-search">
			<form action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="text" name="s" placeholder="Search here" value="<?php echo get_search_query(); ?>">
				<button type="submit"><i class="icofont-search-1"></i></button>
			</form>
		</div>
		<!-- Sidebar Widget Search End -->

		<!-- Sidebar Widget Category Start -->
		<div class="sidebar-widget">
			<h4 class="widget-title">Post Category</h4>

			<div class="widget-category">
				<ul class="category-list">
					<?php
					$categories = get_categories( array( 'hide_empty' => 1 ) );
					foreach ( $categories as $category ) {
						// Keeping the count inside the anchor tag so the CSS pill wraps the entire content on a single line
						echo '<li><a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . ' <span class="category-count">(' . intval( $category->count ) . ')</span></a></li>';
					}
					?>
				</ul>
			</div>
		</div>
		<!-- Sidebar Widget Category End -->

		<!-- Sidebar Widget Post Start -->
		<div class="sidebar-widget">
			<h4 class="widget-title">Recent Post</h4>

			<div class="widget-post">
				<ul class="post-items">
					<?php
					$recent_posts = wp_get_recent_posts( array( 'numberposts' => 4, 'post_status' => 'publish' ) );
					foreach ( $recent_posts as $post ) {
						echo '<li><div class="single-post">';
						$thumbnail = get_the_post_thumbnail_url( $post['ID'], 'thumbnail' );
						if ( $thumbnail ) {
							echo '<div class="post-thumb"><a href="' . get_permalink( $post['ID'] ) . '"><img src="' . esc_url( $thumbnail ) . '" alt="Post"></a></div>';
						}
						echo '<div class="post-content">';
						echo '<h5 class="title"><a href="' . get_permalink( $post['ID'] ) . '">' . esc_html( $post['post_title'] ) . '</a></h5>';
						echo '<span class="date"><i class="icofont-calendar"></i> ' . get_the_date( 'd F, Y', $post['ID'] ) . '</span>';
						echo '</div></div></li>';
					}
					wp_reset_query();
					?>
				</ul>
			</div>
		</div>
		<!-- Sidebar Widget Post End -->

		<!-- Sidebar Widget Tags Start -->
		<div class="sidebar-widget">
			<h4 class="widget-title">Popular Tags</h4>

			<div class="widget-tags">
				<ul class="tags-list">
					<?php
					$tags = get_tags( array( 'number' => 8 ) );
					if ( $tags ) {
						foreach ( $tags as $tag ) {
							echo '<li><a href="' . get_tag_link( $tag->term_id ) . '">' . esc_html( $tag->name ) . '</a></li>';
						}
					} else {
						echo '<li><a href="#">No tags expected</a></li>';
					}
					?>
				</ul>
			</div>
		</div>
		<!-- Sidebar Widget Tags End -->

	</div>
	<!-- Blog Sidebar End -->
	<?php
	return;
}
?>

<div class="sidebar">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div>
