<?php
/**
 * Template part for displaying a single course card.
 */

$author_name      = get_the_author();
$author_avatar    = get_avatar_url( get_the_author_meta( 'ID' ), [ 'size' => 100 ] );
if ( ! $author_avatar ) {
    $author_avatar = get_template_directory_uri() . '/assets/images/author/author-01.jpg';
}

$secondary_author = get_post_meta( get_the_ID(), '_course_secondary_author', true );
$progress         = get_post_meta( get_the_ID(), '_course_progress', true );
$rating           = get_post_meta( get_the_ID(), '_course_rating', true );

if ( ! $progress ) $progress = '0';

if ( get_comments_number() == 0 ) {
    $rating = '0.0';
} else if ( ! $rating ) {
    $rating = '0.0';
}

$rating_percent = ( floatval( $rating ) / 5 ) * 100;
if ( $rating_percent > 100 ) $rating_percent = 100;

$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
if ( ! $thumbnail_url ) {
    $thumbnail_url = get_post_meta( get_the_ID(), '_course_thumbnail_url', true );
}
?>
<div class="col-lg-4 col-md-6">
    <!-- Single Courses Start -->
    <div class="single-courses">
        <div class="courses-images">
            <a href="<?php the_permalink(); ?>">
                <img src="<?php echo esc_url( $thumbnail_url ?: get_template_directory_uri() . '/assets/images/courses/courses-01.jpg' ); ?>" alt="<?php the_title_attribute(); ?>">
            </a>

            <div class="courses-option dropdown">
                <button class="option-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="dropdown-menu">
                    <?php if ( isset( $args['dashboard_action'] ) && $args['dashboard_action'] === 'remove_favorite' ) : ?>
                        <li><a href="#" class="tijus-remove-favorite-btn" data-target-id="<?php echo get_the_ID(); ?>" style="color: #ffffff;"><i class="icofont-trash" style="color:#ffccd5;"></i> Remove from Favorites</a></li>
                    <?php elseif ( isset( $args['dashboard_action'] ) && $args['dashboard_action'] === 'remove_collection' ) : ?>
                        <li><a href="#" class="tijus-remove-collection-btn" data-target-id="<?php echo get_the_ID(); ?>" style="color: #ffffff;"><i class="icofont-trash" style="color:#ffccd5;"></i> Remove from Collection</a></li>
                    <?php else : ?>
                        <li><a href="#" class="tijus-share-btn" data-url="<?php the_permalink(); ?>" data-title="<?php the_title_attribute(); ?>"><i class="icofont-share-alt"></i> Share</a></li>
                        <li><a href="#" class="tijus-collection-btn" data-target-id="<?php echo get_the_ID(); ?>"><i class="icofont-plus"></i> Create Collection</a></li>
                        <li><a href="#" class="tijus-dropdown-love-btn"><i class="icofont-star"></i> Favorite</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="courses-content">
            <h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

            <div class="courses-rating" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="rating-meta">
                    <span class="rating-star">
                            <span class="rating-bar" style="width: <?php echo esc_attr( $rating_percent ); ?>%;"></span>
                    </span>
                    <span style="font-size: 14px; color: #555; margin-left: 8px;">(<?php echo esc_html( get_comments_number() ); ?> Rating)</span>
                </div>
                <div class="favorite-action">
                    <a href="javascript:void(0)" class="tijus-love-btn" data-target-id="<?php echo get_the_ID(); ?>" style="color: #999; font-size: 24px;">
                        <i class="icofont-heart-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Single Courses End -->
</div>
