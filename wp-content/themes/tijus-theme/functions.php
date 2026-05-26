<?php
/**
 * Tijus Theme functions and definitions
 *
 * @package tijus-theme
 */

if ( ! function_exists( 'tijus_theme_setup' ) ) :
	function tijus_theme_setup() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Switch default core markup for search form, comment form, and comments
		// to output valid HTML5.
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Add support for core custom logo.
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
        
        // Elementor supports
        add_theme_support( 'elementor' );
        
        // Register menus
        register_nav_menus(
            array(
                'tijus-menu'         => esc_html__( 'Tijus Menu', 'tijus-theme' ),
                'header-right'       => esc_html__( 'Header Right Buttons', 'tijus-theme' ),
                'footer-category'    => esc_html__( 'Footer: Category', 'tijus-theme' ),
                'footer-quick-links' => esc_html__( 'Footer: Quick Links', 'tijus-theme' ),
            )
        );
	}
endif;
add_action( 'after_setup_theme', 'tijus_theme_setup' );

/**
 * Enqueue scripts and styles.
 * Note: Most theme styles are hardcoded in header.php/footer.php from the template,
 * but we enqueue the main style.css for WordPress standards and Elementor compatibility.
 */
function tijus_theme_scripts() {
	wp_enqueue_style( 'tijus-theme-style', get_stylesheet_uri(), array(), '1.0.0' );

	wp_enqueue_script( 'tijus-modernizr', get_template_directory_uri() . '/assets/js/vendor/modernizr-3.11.2.min.js', array(), '3.11.2', true );
	wp_enqueue_script( 'tijus-plugins',   get_template_directory_uri() . '/assets/js/plugins.min.js', array( 'jquery' ), '1.0.0', true );
	wp_add_inline_script( 'tijus-plugins', 'window.$ = window.jQuery;', 'before' );
	wp_enqueue_script( 'tijus-main',      get_template_directory_uri() . '/assets/js/main.js', array( 'jquery', 'tijus-plugins' ), '1.0.0', true );
	wp_localize_script( 'tijus-main', 'tijusAjax', [
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'tijus_courses_nonce' )
	] );
}
add_action( 'wp_enqueue_scripts', 'tijus_theme_scripts' );

/**
 * Filter to transfer specific classes to the anchor tag for styling the buttons
 */
function tijus_menu_link_classes( $atts, $item, $args ) {
    $title_lower = strtolower( trim( $item->title ) );
    
    // Check for explicit class OR guess by title keywords
    if ( in_array( 'sign-in', $item->classes ) || in_array( $title_lower, ['sign in', 'login', 'log in'] ) ) {
        $atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' sign-in' : 'sign-in';
    }
    if ( in_array( 'sign-up', $item->classes ) || in_array( $title_lower, ['sign up', 'register'] ) ) {
        $atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' sign-up' : 'sign-up';
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'tijus_menu_link_classes', 10, 3 );

/**
 * Filter to dynamically modify menu items based on session state
 */
function tijus_dynamic_auth_menu_objects( $sorted_menu_items, $args ) {
    if ( is_user_logged_in() ) {
        $filtered = [];
        foreach ( $sorted_menu_items as $item ) {
            $title_lower = strtolower( trim( $item->title ) );
            if ( in_array( $title_lower, ['sign in', 'login', 'log in', 'sign up', 'register', 'my account'] ) || ( is_array($item->classes) && (in_array('sign-in', $item->classes) || in_array('sign-up', $item->classes)) ) ) {
                continue; // Remove all static auth links when logged in
            }
            $filtered[] = $item;
        }
        return $filtered;
    }
    return $sorted_menu_items;
}
add_filter( 'wp_nav_menu_objects', 'tijus_dynamic_auth_menu_objects', 10, 2 );

/**
 * Safely append the 'My Account' pill button back into the navigation as a raw HTML element
 * instead of generating a fake WP_Post which throws Walker_Nav_Menu warnings.
 */
function tijus_inject_my_account_menu_item( $items, $args ) {
    if ( is_user_logged_in() ) {
        // Prevent duplication: If they have multiple menus, only inject into the 'header-right' menu if it exists.
        // If 'header-right' is deliberately empty, we can inject it at the end of 'tijus-menu'.
        if ( $args->theme_location === 'header-right' || ( $args->theme_location === 'tijus-menu' && ! has_nav_menu('header-right') ) ) {
            $items .= '<li><a class="sign-up" href="' . esc_url( home_url('/dashboard/') ) . '">My Account</a></li>';
        }
    }
    return $items;
}
add_filter( 'wp_nav_menu_items', 'tijus_inject_my_account_menu_item', 10, 2 );

// Theme Customizer settings.
require_once get_template_directory() . '/inc/theme-customizer.php';

// Theme Options admin sidebar page.
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/theme-options-page.php';
}

// Elementor home page seeder (admin tool).
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/elementor-home-seeder.php';
}

// About page seeder (admin tool).
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/about-page-seeder.php';
}

// Course seeder (admin tool).
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/course-seeder.php';
}

// Courses mega-menu: pulls categories + courses dynamically from WP database.
add_filter( 'walker_nav_menu_start_el', 'tijus_courses_mega_menu_inject', 10, 4 );
function tijus_courses_mega_menu_inject( $item_output, $item, $depth, $args ) {
	if ( $depth !== 0 ) return $item_output;
	if ( strtolower( trim( $item->title ) ) !== 'courses' ) return $item_output;

	$categories = get_terms( [
		'taxonomy'   => 'course_category',
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	] );

	if ( is_wp_error( $categories ) || empty( $categories ) ) return $item_output;

	$courses_page = esc_url( home_url( '/courses/' ) );

	$mega  = '<div class="courses-mega-menu">';
	$mega .= '<div class="container">';
	$mega .= '<div class="courses-mega-inner">';

	// Define the order of categories as shown in the image
	$cat_order = ['language-exams', 'healthcare-licensing', 'diploma-programs', 'wellness', 'tijus-media-school'];

	foreach ( $cat_order as $slug ) {
		$cat = get_term_by( 'slug', $slug, 'course_category' );
		if ( ! $cat ) continue;

		$courses = get_posts( [
			'post_type'      => 'course',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'post_date', // Maintain creation order for simple sorting
			'order'          => 'ASC',
			'tax_query'      => [ [
				'taxonomy' => 'course_category',
				'field'    => 'term_id',
				'terms'    => $cat->term_id,
			] ],
		] );

		if ( empty( $courses ) ) continue;

		$cat_url = esc_url( add_query_arg( 'course_category', $cat->slug, $courses_page ) );
		$is_media = ( $slug === 'tijus-media-school' );

		$mega .= '<div class="courses-mega-col' . ( $is_media ? ' courses-mega-special' : '' ) . '">';
		
		// Column heading (Header)
		if ( $is_media ) {
			$mega .= '<h6 class="courses-mega-heading" style="font-size: 24px; font-weight: 800; line-height: 1.2; margin-bottom: 20px; color: #212832;">Tiju\'s Media School</h6>';
		} else {
			// Transparent header for others if we want to match the "no title" look in the image for columns 1-4
			// Actually, the image doesn't show headers for the first 4 columns, just the list items.
			$mega .= '<div style="height: 60px;"></div>'; 
		}

		$mega .= '<ul style="list-style: none; padding: 0; margin: 0;">';
		foreach ( $courses as $course ) {
			$mega .= '<li style="margin-bottom: 12px;"><a href="' . esc_url( get_permalink( $course->ID ) ) . '" style="color: #52565b; font-weight: 500; font-size: 16px; transition: 0.3s; display: block;">' . esc_html( $course->post_title ) . '</a></li>';
		}
		$mega .= '</ul></div>';
	}

	$mega .= '</div></div></div>';

	return $item_output . $mega;
}

// Add has-children class to Courses <li> so arrow & hover trigger correctly.
add_filter( 'nav_menu_css_class', 'tijus_courses_mega_menu_li_class', 10, 3 );
function tijus_courses_mega_menu_li_class( $classes, $item, $args ) {
	if ( strtolower( trim( $item->title ) ) === 'courses' ) {
		$classes[] = 'menu-item-has-children';
		$classes[] = 'has-mega-menu';
	}
	return $classes;
}

// Mega-menu positioning: JS pins the fixed dropdown to full header width.
add_action( 'wp_footer', 'tijus_mega_menu_position_js' );
function tijus_mega_menu_position_js() {
	?>
	<script>
	(function(){
		var li    = document.querySelector('.header-menu ul li.has-mega-menu');
		var panel = document.querySelector('.courses-mega-menu');
		if ( ! li || ! panel ) return;

		function positionPanel() {
			var header = document.querySelector('.header-main');
			if ( ! header ) return;
			var rect   = header.getBoundingClientRect();
			var liRect = li.getBoundingClientRect();
			
			panel.style.left  = '0px';
			panel.style.width = '100%';
			panel.style.top   = (liRect.bottom) + 'px';
		}

		li.addEventListener('mouseenter', function(){
			positionPanel();
			panel.classList.add('is-open');
		});
		li.addEventListener('mouseleave', function(e){
			if ( ! panel.contains(e.relatedTarget) ) {
				panel.classList.remove('is-open');
			}
		});
		panel.addEventListener('mouseleave', function(e){
			if ( ! li.contains(e.relatedTarget) ) {
				panel.classList.remove('is-open');
			}
		});
		window.addEventListener('resize', positionPanel);
		window.addEventListener('scroll', positionPanel);
	})();
	</script>
	<?php
}

/**
 * Color palettes available in Theme Options.
 */
function tijus_get_color_palettes() {
	return [
		'brand' => [
			'label'         => 'Brand',
			'primary'       => '#01A1E4',
			'primary_hover' => '#0181B6',
			'secondary'     => '#E6F5FC',
			'success'       => '#77DD77',
			'warning'       => '#F3D011',
			'danger'        => '#EE2A35',
			'dark'          => '#2E2B70',
		],
		'default' => [
			'label'         => 'Default (Blue)',
			'primary'       => '#6BB8E0',
			'primary_hover' => '#4A9AC5',
			'secondary'     => '#e0f0f8',
			'success'       => '#8DD87C',
			'warning'       => '#F2C84C',
			'danger'        => '#EF4836',
			'dark'          => '#2E3267',
		],
		'green' => [
			'label'         => 'Green',
			'primary'       => '#309255',
			'primary_hover' => '#267544',
			'secondary'     => '#e7f8ee',
			'success'       => '#198754',
			'warning'       => '#ffc107',
			'danger'        => '#dc3545',
			'dark'          => '#212832',
		],
		'purple' => [
			'label'         => 'Purple',
			'primary'       => '#7C3AED',
			'primary_hover' => '#6327C4',
			'secondary'     => '#f0e8ff',
			'success'       => '#10B981',
			'warning'       => '#F59E0B',
			'danger'        => '#EF4444',
			'dark'          => '#1E1B4B',
		],
		'orange' => [
			'label'         => 'Orange',
			'primary'       => '#EA7317',
			'primary_hover' => '#C75F0F',
			'secondary'     => '#FEF3E2',
			'success'       => '#22C55E',
			'warning'       => '#FACC15',
			'danger'        => '#E11D48',
			'dark'          => '#422006',
		],
		'red' => [
			'label'         => 'Red',
			'primary'       => '#DC2626',
			'primary_hover' => '#B91C1C',
			'secondary'     => '#FEE2E2',
			'success'       => '#16A34A',
			'warning'       => '#EAB308',
			'danger'        => '#E11D48',
			'dark'          => '#450A0A',
		],
		'teal' => [
			'label'         => 'Teal',
			'primary'       => '#0D9488',
			'primary_hover' => '#0F766E',
			'secondary'     => '#E0F7F5',
			'success'       => '#22C55E',
			'warning'       => '#F59E0B',
			'danger'        => '#EF4444',
			'dark'          => '#134E4A',
		],
		'cyan_gradient' => [
			'label'         => 'Cyan Gradient',
			'primary'       => '#1fbff1',
			'primary_hover' => '#0c8fc0',
			'secondary'     => '#e6f8fd',
			'success'       => '#22C55E',
			'warning'       => '#F59E0B',
			'danger'        => '#EF4444',
			'dark'          => '#113a48',
			'is_gradient'   => true,
		],
	];
}

/**
 * Output CSS variable overrides for the selected color palette.
 */
function tijus_output_palette_css() {
	$palette_key = get_theme_mod( 'tijus_color_palette', 'default' );
	if ( $palette_key === 'default' ) {
		return; // Default colors are already in the stylesheet.
	}

	$palettes = tijus_get_color_palettes();
	if ( ! isset( $palettes[ $palette_key ] ) ) {
		return;
	}

	$p = $palettes[ $palette_key ];

	// Convert hex to RGB components.
	$to_rgb = function( $hex ) {
		$hex = ltrim( $hex, '#' );
		return intval( substr( $hex, 0, 2 ), 16 ) . ', '
			 . intval( substr( $hex, 2, 2 ), 16 ) . ', '
			 . intval( substr( $hex, 4, 2 ), 16 );
	};

	echo '<style id="tijus-palette-overrides">
:root {
  --bs-primary: ' . $p['primary'] . ';
  --bs-primary-rgb: ' . $to_rgb( $p['primary'] ) . ';
  --bs-secondary: ' . $p['secondary'] . ';
  --bs-secondary-rgb: ' . $to_rgb( $p['secondary'] ) . ';
  --bs-success: ' . $p['success'] . ';
  --bs-success-rgb: ' . $to_rgb( $p['success'] ) . ';
  --bs-warning: ' . $p['warning'] . ';
  --bs-warning-rgb: ' . $to_rgb( $p['warning'] ) . ';
  --bs-danger: ' . $p['danger'] . ';
  --bs-danger-rgb: ' . $to_rgb( $p['danger'] ) . ';
  --bs-dark: ' . $p['dark'] . ';
  --bs-dark-rgb: ' . $to_rgb( $p['dark'] ) . ';
  --bs-link-color: ' . $p['primary'] . ';
  --bs-link-hover-color: ' . $p['primary_hover'] . ';
}
';

	if ( ! empty( $p['is_gradient'] ) ) {
		echo '
.btn-primary, .button-primary, .bg-primary, .slider-content .btn, .courses-tabs-menu .nav button.active {
  background: linear-gradient(135deg, ' . $p['primary'] . ' 0%, ' . $p['primary_hover'] . ' 100%) !important;
  border: none !important;
  color: #fff !important;
}
.btn-primary:hover, .button-primary:hover, .slider-content .btn:hover {
  background: linear-gradient(135deg, ' . $p['primary_hover'] . ' 0%, ' . $p['primary'] . ' 100%) !important;
}
';
	}

	echo '</style>' . "\n";
}
add_action( 'wp_head', 'tijus_output_palette_css', 99 );

/**
 * Helper: return social links array [{label, icon, url}, ...].
 * Falls back to legacy individual theme mods if the JSON mod isn't set yet.
 */
function tijus_get_social_links() {
	$raw = get_theme_mod( 'tijus_social_links', '' );
	if ( $raw ) {
		$links = json_decode( $raw, true );
		if ( is_array( $links ) ) {
			return $links;
		}
	}
	return [
		[ 'label' => 'Facebook',  'icon' => 'flaticon-facebook',  'url' => get_theme_mod( 'tijus_facebook_url',  '#' ) ],
		[ 'label' => 'Twitter',   'icon' => 'flaticon-twitter',   'url' => get_theme_mod( 'tijus_twitter_url',   '#' ) ],
		[ 'label' => 'Skype',     'icon' => 'flaticon-skype',     'url' => get_theme_mod( 'tijus_skype_url',     '#' ) ],
		[ 'label' => 'Instagram', 'icon' => 'flaticon-instagram', 'url' => get_theme_mod( 'tijus_instagram_url', '#' ) ],
	];
}

/**
 * Helper: return the theme logo URL (custom upload, or default theme logo).
 */
function tijus_get_logo_url() {
	$custom = get_theme_mod( 'tijus_logo', '' );
	if ( $custom && function_exists('tijus_replace_hardcoded_urls') ) {
		$custom = tijus_replace_hardcoded_urls( $custom );
	}
	return $custom ? esc_url( $custom ) : esc_url( get_template_directory_uri() . '/assets/images/logo.png' );
}

/**
 * Register Courses Custom Post Type and Taxonomy.
 */
function tijus_register_courses_cpt() {
	$labels = [
		'name'               => 'Courses',
		'singular_name'      => 'Course',
		'menu_name'          => 'Courses',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Course',
		'edit_item'          => 'Edit Course',
		'new_item'           => 'New Course',
		'view_item'          => 'View Course',
		'search_items'       => 'Search Courses',
		'not_found'          => 'No courses found',
		'not_found_in_trash' => 'No courses found in Trash',
	];

	$args = [
		'labels'              => $labels,
		'public'              => true,
		'has_archive'         => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-welcome-learn-more',
		'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ],
		'rewrite'             => [ 'slug' => 'course', 'with_front' => false ],
	];

	register_post_type( 'course', $args );

	// Rename Excerpt to Summary globally
	add_action( 'add_meta_boxes', function() {
		global $wp_meta_boxes;
		foreach ( $wp_meta_boxes as $post_type => $contexts ) {
			foreach ( $contexts as $context => $priorities ) {
				foreach ( $priorities as $priority => $boxes ) {
					if ( isset( $boxes['postexcerpt'] ) ) {
						$wp_meta_boxes[$post_type][$context][$priority]['postexcerpt']['title'] = 'Summary';
					}
				}
			}
		}
	}, 100 );

	// Register Course Category Taxonomy
	register_taxonomy( 'course_category', [ 'course' ], [
		'hierarchical'      => true,
		'labels'            => [
			'name'              => 'Course Categories',
			'singular_name'     => 'Course Category',
			'search_items'      => 'Search Categories',
			'all_items'         => 'All Categories',
			'parent_item'       => 'Parent Category',
			'parent_item_colon' => 'Parent Category:',
			'edit_item'         => 'Edit Category',
			'update_item'       => 'Update Category',
			'add_new_item'      => 'Add New Category',
			'new_item_name'     => 'New Category Name',
			'menu_name'         => 'Categories',
		],
		'show_ui'           => true,
		'show_admin_column' => true,
		'rewrite'           => [ 'slug' => 'course-category' ],
	] );
}
add_action( 'init', 'tijus_register_courses_cpt' );

/**
 * Register Certifications Custom Post Type.
 */
function tijus_register_certifications_cpt() {
	$labels = [
		'name'               => 'Certifications',
		'singular_name'      => 'Certification',
		'menu_name'          => 'Certifications',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Certification',
		'edit_item'          => 'Edit Certification',
		'new_item'           => 'New Certification',
		'view_item'          => 'View Certification',
		'search_items'       => 'Search Certifications',
		'not_found'          => 'No certifications found',
		'not_found_in_trash' => 'No certifications found in Trash',
	];

	$args = [
		'labels'              => $labels,
		'public'              => true,
		'has_archive'         => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 21,
		'menu_icon'           => 'dashicons-awards',
		'supports'            => [ 'title', 'thumbnail' ],
		'rewrite'             => [ 'slug' => 'certification', 'with_front' => false ],
	];

	register_post_type( 'certification', $args );
}
add_action( 'init', 'tijus_register_certifications_cpt' );

/**
 * Register Meta Boxes for Courses.
 */
function tijus_add_course_meta_boxes() {
	add_meta_box(
		'tijus_course_details',
		'Course Details',
		'tijus_render_course_meta_box',
		'course',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tijus_add_course_meta_boxes' );

function tijus_render_course_meta_box( $post ) {
	wp_nonce_field( 'tijus_save_course_data', 'tijus_course_meta_nonce' );

	$secondary_author = get_post_meta( $post->ID, '_course_secondary_author', true );
	$progress         = get_post_meta( $post->ID, '_course_progress', true );
	$duration         = get_post_meta( $post->ID, '_course_duration', true );
	$lectures         = get_post_meta( $post->ID, '_course_lectures', true );
	$regular_price    = get_post_meta( $post->ID, '_course_regular_price', true );
	$sale_price       = get_post_meta( $post->ID, '_course_sale_price', true );
	$rating           = get_post_meta( $post->ID, '_course_rating', true );
	$level            = get_post_meta( $post->ID, '_course_level', true );
	$language         = get_post_meta( $post->ID, '_course_language', true );
	$certificate      = get_post_meta( $post->ID, '_course_certificate', true );
	$video_type       = get_post_meta( $post->ID, '_course_video_type', true );
	$video_url        = get_post_meta( $post->ID, '_course_video_url', true );

	if ( empty( $video_type ) ) $video_type = 'youtube';

	$course_image = get_post_meta( $post->ID, '_course_thumbnail_url', true );

	wp_enqueue_media();
	?>
	<table class="form-table">
		<tr>
			<th><label>Course Image</label></th>
			<td>
				<div id="course_image_preview" style="margin-bottom: 10px;">
					<?php if ( $course_image ) : ?>
						<img src="<?php echo esc_url( $course_image ); ?>" style="max-width: 300px; height: auto; border: 1px solid #ccc; border-radius: 4px;" />
					<?php endif; ?>
				</div>
				<input type="hidden" name="course_image_url" id="course_image_url" value="<?php echo esc_attr( $course_image ); ?>" />
				<button type="button" class="button" id="upload_course_image_btn">Select/Upload Image</button>
				<button type="button" class="button" id="remove_course_image_btn" <?php echo $course_image ? '' : 'style="display:none;"'; ?>>Remove Image</button>
				<p class="description">This image is displayed as the course banner on the course page.</p>
			</td>
		</tr>
		<tr>
			<th><label>Preview Video</label></th>
			<td>
				<label style="margin-right: 20px;">
					<input type="radio" name="course_video_type" value="youtube" <?php checked( $video_type, 'youtube' ); ?> /> YouTube URL
				</label>
				<label>
					<input type="radio" name="course_video_type" value="local" <?php checked( $video_type, 'local' ); ?> /> Local File (Media Library)
				</label>
			</td>
		</tr>
		<tr id="course_video_youtube_row" style="<?php echo $video_type === 'local' ? 'display:none;' : ''; ?>">
			<th><label for="course_video_url_yt">YouTube URL</label></th>
			<td>
				<input type="url" id="course_video_url_yt" name="course_video_url" value="<?php echo $video_type === 'youtube' ? esc_attr( $video_url ) : ''; ?>" class="regular-text" placeholder="https://www.youtube.com/watch?v=..." />
			</td>
		</tr>
		<tr id="course_video_local_row" style="<?php echo $video_type === 'youtube' ? 'display:none;' : ''; ?>">
			<th><label>Local Video File</label></th>
			<td>
				<input type="text" id="course_video_url_local" name="course_video_url_local" value="<?php echo $video_type === 'local' ? esc_attr( $video_url ) : ''; ?>" class="regular-text" placeholder="Select a video file" readonly />
				<button type="button" class="button" id="upload_course_video_btn">Select Video</button>
				<button type="button" class="button" id="remove_course_video_btn" <?php echo ( $video_type === 'local' && $video_url ) ? '' : 'style="display:none;"'; ?>>Remove</button>
				<?php if ( $video_type === 'local' && $video_url ) : ?>
					<p class="description" style="margin-top:5px;"><a href="<?php echo esc_url( $video_url ); ?>" target="_blank">View current file</a></p>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="course_secondary_author">Secondary Author</label></th>
			<td><input type="text" id="course_secondary_author" name="course_secondary_author" value="<?php echo esc_attr( $secondary_author ); ?>" class="regular-text" placeholder="e.g. Ohula Malsh" /></td>
		</tr>
		<tr>
			<th><label for="course_progress">Progress % (for My Courses style)</label></th>
			<td><input type="number" id="course_progress" name="course_progress" value="<?php echo esc_attr( $progress ); ?>" class="small-text" placeholder="38" /> % Complete</td>
		</tr>
		<tr>
			<th><label for="course_duration">Course Duration</label></th>
			<td><input type="text" id="course_duration" name="course_duration" value="<?php echo esc_attr( $duration ); ?>" class="regular-text" placeholder="e.g. 08 hr 15 mins" /></td>
		</tr>
		<tr>
			<th><label for="course_lectures">Total Lectures</label></th>
			<td><input type="text" id="course_lectures" name="course_lectures" value="<?php echo esc_attr( $lectures ); ?>" class="regular-text" placeholder="e.g. 29 Lectures" /></td>
		</tr>
		<tr>
			<th><label for="course_regular_price">Regular Price ($)</label></th>
			<td><input type="text" id="course_regular_price" name="course_regular_price" value="<?php echo esc_attr( $regular_price ); ?>" class="small-text" placeholder="e.g. 440.00" /></td>
		</tr>
		<tr>
			<th><label for="course_sale_price">Sale Price ($) / 'Free'</label></th>
			<td><input type="text" id="course_sale_price" name="course_sale_price" value="<?php echo esc_attr( $sale_price ); ?>" class="small-text" placeholder="e.g. 385.00 or Free" /></td>
		</tr>
		<tr>
			<th><label for="course_rating">Rating (out of 5)</label></th>
			<td><input type="number" step="0.1" max="5" min="0" id="course_rating" name="course_rating" value="<?php echo esc_attr( $rating ); ?>" class="small-text" placeholder="4.9" /></td>
		</tr>
		<tr>
			<th><label for="course_level">Level</label></th>
			<td><input type="text" id="course_level" name="course_level" value="<?php echo esc_attr( $level ); ?>" class="regular-text" placeholder="e.g. Beginner, Secondary" /></td>
		</tr>
		<tr>
			<th><label for="course_language">Language</label></th>
			<td><input type="text" id="course_language" name="course_language" value="<?php echo esc_attr( $language ); ?>" class="regular-text" placeholder="e.g. English" /></td>
		</tr>
		<tr>
			<th><label for="course_certificate">Certificate</label></th>
			<td><input type="text" id="course_certificate" name="course_certificate" value="<?php echo esc_attr( $certificate ); ?>" class="small-text" placeholder="e.g. Yes" /></td>
		</tr>
        <tr>
            <th><label>Course FAQs</label></th>
            <td>
                <?php $faqs = get_post_meta($post->ID, '_course_faqs', true); ?>
                <div id="tijus_faq_container">
                    <?php 
                    if (is_array($faqs) && !empty($faqs)) {
                        foreach ($faqs as $index => $faq) {
                            ?>
                            <div class="faq-row" style="margin-bottom:15px; padding:15px; background:#f9f9f9; border:1px solid #ddd;">
                                <input type="text" name="course_faqs[<?php echo $index; ?>][question]" value="<?php echo esc_attr($faq['question'] ?? ''); ?>" placeholder="Question" style="width:100%; margin-bottom:10px;" />
                                <textarea name="course_faqs[<?php echo $index; ?>][answer]" placeholder="Answer..." style="width:100%; height:60px; margin-bottom:10px;"><?php echo esc_textarea($faq['answer'] ?? ''); ?></textarea>
                                <button type="button" class="button remove-faq-btn" style="color:#b32d2e;">Remove FAQ</button>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <button type="button" id="add_faq_btn" class="button button-primary">Add FAQ</button>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var container = document.getElementById('tijus_faq_container');
                        var addBtn = document.getElementById('add_faq_btn');
                        var index = <?php echo is_array($faqs) ? count($faqs) : 0; ?>;
                        
                        addBtn.addEventListener('click', function() {
                            var row = document.createElement('div');
                            row.className = 'faq-row';
                            row.style.cssText = 'margin-bottom:15px; padding:15px; background:#f9f9f9; border:1px solid #ddd;';
                            row.innerHTML = 
                                '<input type="text" name="course_faqs[' + index + '][question]" placeholder="Question" style="width:100%; margin-bottom:10px;" />' +
                                '<textarea name="course_faqs[' + index + '][answer]" placeholder="Answer..." style="width:100%; height:60px; margin-bottom:10px;"></textarea>' +
                                '<button type="button" class="button remove-faq-btn" style="color:#b32d2e;">Remove FAQ</button>';
                            container.appendChild(row);
                            index++;
                        });

                        container.addEventListener('click', function(e) {
                            if (e.target && e.target.classList.contains('remove-faq-btn')) {
                                e.target.closest('.faq-row').remove();
                            }
                        });
                    });
                </script>
            </td>
        </tr>
        <tr>
            <th><label>Testimonial Videos</label></th>
            <td>
                <?php $testimonials = get_post_meta($post->ID, '_course_testimonials', true); ?>
                <div id="tijus_testimonial_container">
                    <?php
                    if (is_array($testimonials) && !empty($testimonials)) {
                        foreach ($testimonials as $ti => $t) {
                            $t_type = $t['type'] ?? 'youtube';
                            $t_url  = $t['url'] ?? '';
                            $t_title = $t['title'] ?? '';
                            ?>
                            <div class="testimonial-row" style="margin-bottom:15px; padding:15px; background:#f0f7ff; border:1px solid #c8ddf0; border-radius:4px;">
                                <input type="text" name="course_testimonials[<?php echo $ti; ?>][title]" value="<?php echo esc_attr($t_title); ?>" placeholder="Title (optional)" style="width:100%; margin-bottom:8px;" />
                                <div style="margin-bottom:8px;">
                                    <label style="margin-right:15px;"><input type="radio" name="course_testimonials[<?php echo $ti; ?>][type]" value="youtube" <?php checked($t_type, 'youtube'); ?> class="testimonial-type-radio" /> YouTube</label>
                                    <label><input type="radio" name="course_testimonials[<?php echo $ti; ?>][type]" value="local" <?php checked($t_type, 'local'); ?> class="testimonial-type-radio" /> Local File</label>
                                </div>
                                <div class="testimonial-yt-field" style="<?php echo $t_type === 'local' ? 'display:none;' : ''; ?>">
                                    <input type="url" name="course_testimonials[<?php echo $ti; ?>][url_yt]" value="<?php echo $t_type === 'youtube' ? esc_attr($t_url) : ''; ?>" placeholder="https://www.youtube.com/watch?v=..." style="width:100%;" />
                                </div>
                                <div class="testimonial-local-field" style="<?php echo $t_type === 'youtube' ? 'display:none;' : ''; ?>">
                                    <input type="text" name="course_testimonials[<?php echo $ti; ?>][url_local]" value="<?php echo $t_type === 'local' ? esc_attr($t_url) : ''; ?>" placeholder="Select a video file" style="width:80%;" readonly />
                                    <button type="button" class="button testimonial-upload-btn">Select</button>
                                </div>
                                <button type="button" class="button remove-testimonial-btn" style="color:#b32d2e; margin-top:8px;">Remove</button>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <button type="button" id="add_testimonial_btn" class="button button-primary">Add Testimonial Video</button>
            </td>
        </tr>
	</table>
	<script>
		jQuery(document).ready(function($) {
			// Course image uploader
			var courseImageUploader;
			$('#upload_course_image_btn').on('click', function(e) {
				e.preventDefault();
				if (courseImageUploader) { courseImageUploader.open(); return; }
				courseImageUploader = wp.media({
					title: 'Select Course Image',
					button: { text: 'Use this image' },
					library: { type: 'image' },
					multiple: false
				});
				courseImageUploader.on('select', function() {
					var attachment = courseImageUploader.state().get('selection').first().toJSON();
					$('#course_image_url').val(attachment.url);
					$('#course_image_preview').html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto; border: 1px solid #ccc; border-radius: 4px;" />');
					$('#remove_course_image_btn').show();
				});
				courseImageUploader.open();
			});
			$('#remove_course_image_btn').on('click', function(e) {
				e.preventDefault();
				$('#course_image_url').val('');
				$('#course_image_preview').empty();
				$(this).hide();
			});

			// Video type toggle
			$('input[name="course_video_type"]').on('change', function() {
				if ($(this).val() === 'youtube') {
					$('#course_video_youtube_row').show();
					$('#course_video_local_row').hide();
				} else {
					$('#course_video_youtube_row').hide();
					$('#course_video_local_row').show();
				}
			});

			// Local video media uploader
			var videoUploader;
			$('#upload_course_video_btn').on('click', function(e) {
				e.preventDefault();
				if (videoUploader) { videoUploader.open(); return; }
				videoUploader = wp.media({
					title: 'Select Video File',
					button: { text: 'Use this video' },
					library: { type: 'video' },
					multiple: false
				});
				videoUploader.on('select', function() {
					var attachment = videoUploader.state().get('selection').first().toJSON();
					$('#course_video_url_local').val(attachment.url);
					$('#remove_course_video_btn').show();
				});
				videoUploader.open();
			});
			$('#remove_course_video_btn').on('click', function(e) {
				e.preventDefault();
				$('#course_video_url_local').val('');
				$(this).hide();
			});

			// Testimonial Videos
			var tIndex = <?php echo is_array($testimonials) ? count($testimonials) : 0; ?>;
			$('#add_testimonial_btn').on('click', function() {
				var html = '<div class="testimonial-row" style="margin-bottom:15px; padding:15px; background:#f0f7ff; border:1px solid #c8ddf0; border-radius:4px;">' +
					'<input type="text" name="course_testimonials[' + tIndex + '][title]" placeholder="Title (optional)" style="width:100%; margin-bottom:8px;" />' +
					'<div style="margin-bottom:8px;">' +
					'<label style="margin-right:15px;"><input type="radio" name="course_testimonials[' + tIndex + '][type]" value="youtube" checked class="testimonial-type-radio" /> YouTube</label>' +
					'<label><input type="radio" name="course_testimonials[' + tIndex + '][type]" value="local" class="testimonial-type-radio" /> Local File</label>' +
					'</div>' +
					'<div class="testimonial-yt-field"><input type="url" name="course_testimonials[' + tIndex + '][url_yt]" placeholder="https://www.youtube.com/watch?v=..." style="width:100%;" /></div>' +
					'<div class="testimonial-local-field" style="display:none;"><input type="text" name="course_testimonials[' + tIndex + '][url_local]" placeholder="Select a video file" style="width:80%;" readonly /> <button type="button" class="button testimonial-upload-btn">Select</button></div>' +
					'<button type="button" class="button remove-testimonial-btn" style="color:#b32d2e; margin-top:8px;">Remove</button>' +
					'</div>';
				$('#tijus_testimonial_container').append(html);
				tIndex++;
			});

			$('#tijus_testimonial_container').on('click', '.remove-testimonial-btn', function() {
				$(this).closest('.testimonial-row').remove();
			});

			$('#tijus_testimonial_container').on('change', '.testimonial-type-radio', function() {
				var row = $(this).closest('.testimonial-row');
				if ($(this).val() === 'youtube') {
					row.find('.testimonial-yt-field').show();
					row.find('.testimonial-local-field').hide();
				} else {
					row.find('.testimonial-yt-field').hide();
					row.find('.testimonial-local-field').show();
				}
			});

			$('#tijus_testimonial_container').on('click', '.testimonial-upload-btn', function(e) {
				e.preventDefault();
				var inputField = $(this).prev('input');
				var uploader = wp.media({
					title: 'Select Testimonial Video',
					button: { text: 'Use this video' },
					library: { type: 'video' },
					multiple: false
				});
				uploader.on('select', function() {
					var attachment = uploader.state().get('selection').first().toJSON();
					inputField.val(attachment.url);
				});
				uploader.open();
			});
		});
	</script>
	<?php
}

function tijus_save_course_meta_data( $post_id ) {
	if ( ! isset( $_POST['tijus_course_meta_nonce'] ) || ! wp_verify_nonce( $_POST['tijus_course_meta_nonce'], 'tijus_save_course_data' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = [
		'course_secondary_author' => '_course_secondary_author',
		'course_progress'         => '_course_progress',
		'course_duration'         => '_course_duration',
		'course_lectures'         => '_course_lectures',
		'course_regular_price'    => '_course_regular_price',
		'course_sale_price'       => '_course_sale_price',
		'course_rating'           => '_course_rating',
		'course_level'            => '_course_level',
		'course_language'         => '_course_language',
		'course_certificate'      => '_course_certificate',
	];

	foreach ( $fields as $field => $meta_key ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}

	// Save course image
	if ( isset( $_POST['course_image_url'] ) ) {
		$img_url = esc_url_raw( $_POST['course_image_url'] );
		if ( ! empty( $img_url ) ) {
			update_post_meta( $post_id, '_course_thumbnail_url', $img_url );
		} else {
			delete_post_meta( $post_id, '_course_thumbnail_url' );
		}
	}

	// Save video type and URL
	if ( isset( $_POST['course_video_type'] ) ) {
		$video_type = sanitize_text_field( $_POST['course_video_type'] );
		update_post_meta( $post_id, '_course_video_type', $video_type );

		if ( $video_type === 'youtube' && isset( $_POST['course_video_url'] ) ) {
			update_post_meta( $post_id, '_course_video_url', esc_url_raw( $_POST['course_video_url'] ) );
		} elseif ( $video_type === 'local' && isset( $_POST['course_video_url_local'] ) ) {
			update_post_meta( $post_id, '_course_video_url', esc_url_raw( $_POST['course_video_url_local'] ) );
		}
	}

    // Handle FAQs array safely
    if (isset($_POST['course_faqs']) && is_array($_POST['course_faqs'])) {
        $sanitized_faqs = [];
        foreach ($_POST['course_faqs'] as $faq) {
            if (!empty(trim($faq['question'])) && !empty(trim($faq['answer']))) {
                $sanitized_faqs[] = [
                    'question' => sanitize_text_field(wp_unslash($faq['question'])),
                    'answer'   => sanitize_textarea_field(wp_unslash($faq['answer']))
                ];
            }
        }
        update_post_meta($post_id, '_course_faqs', $sanitized_faqs);
    } else {
        delete_post_meta($post_id, '_course_faqs');
    }

    // Save testimonial videos
    if (isset($_POST['course_testimonials']) && is_array($_POST['course_testimonials'])) {
        $sanitized = [];
        foreach ($_POST['course_testimonials'] as $t) {
            $type  = sanitize_text_field($t['type'] ?? 'youtube');
            $url   = ($type === 'youtube') ? esc_url_raw($t['url_yt'] ?? '') : esc_url_raw($t['url_local'] ?? '');
            $title = sanitize_text_field(wp_unslash($t['title'] ?? ''));
            if (!empty($url)) {
                $sanitized[] = ['type' => $type, 'url' => $url, 'title' => $title];
            }
        }
        update_post_meta($post_id, '_course_testimonials', $sanitized);
    } else {
        delete_post_meta($post_id, '_course_testimonials');
    }
}
add_action( 'save_post_course', 'tijus_save_course_meta_data' );

/**
 * Creates dummy courses automatically.
 */
function tijus_create_dummy_courses_once() {
	if ( ! get_option( 'tijus_dummy_courses_created_v1' ) ) {
		$dummy_courses = [
			[ 'title' => 'Data Science and Machine Learning with Python - Hands On!', 'secondary' => 'Ohula Malsh', 'progress' => '38', 'duration' => '08 hr 15 mins', 'lectures' => '29 Lectures', 'price' => '440.00', 'sale' => '385.00', 'rating' => '4.9', 'image' => 'courses-01.jpg' ],
			[ 'title' => 'Create Amazing Color Schemes for Your UX Design Projects',  'secondary' => 'Ohula Malsh', 'progress' => '80', 'duration' => '05 hr 10 mins', 'lectures' => '15 Lectures', 'price' => '',       'sale' => '420.00', 'rating' => '4.9', 'image' => 'courses-02.jpg' ],
			[ 'title' => 'Culture & Leadership: Strategies for a Successful Business', 'secondary' => 'Ohula Malsh', 'progress' => '15', 'duration' => '12 hr 30 mins', 'lectures' => '40 Lectures', 'price' => '340.00', 'sale' => '295.00', 'rating' => '4.9', 'image' => 'courses-03.jpg' ],
			[ 'title' => 'Finance Series: Learn to Budget and Calculate your Net Worth', 'secondary' => 'Ohula Malsh', 'progress' => '45', 'duration' => '03 hr 45 mins', 'lectures' => '12 Lectures', 'price' => '',     'sale' => 'Free',   'rating' => '4.9', 'image' => 'courses-04.jpg' ],
			[ 'title' => 'Build Brand Into Marketing: Tackling the New Marketing Landscape', 'secondary' => 'Ohula Malsh', 'progress' => '38', 'duration' => '09 hr 20 mins', 'lectures' => '35 Lectures', 'price' => '',   'sale' => '136.00', 'rating' => '4.9', 'image' => 'courses-05.jpg' ],
			[ 'title' => 'Graphic Design: Illustrating Badges and Icons with Geometric Shapes', 'secondary' => 'Ohula Malsh', 'progress' => '0', 'duration' => '06 hr 50 mins', 'lectures' => '22 Lectures', 'price' => '', 'sale' => '237.00', 'rating' => '4.8', 'image' => 'courses-06.jpg' ]
		];

		foreach ( $dummy_courses as $item ) {
			$post_id = wp_insert_post( [
				'post_title'   => $item['title'],
				'post_content' => 'This is a sample description for the course: ' . $item['title'],
				'post_status'  => 'publish',
				'post_type'    => 'course',
			] );

			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_course_secondary_author', $item['secondary'] );
				update_post_meta( $post_id, '_course_progress', $item['progress'] );
				update_post_meta( $post_id, '_course_duration', $item['duration'] );
				update_post_meta( $post_id, '_course_lectures', $item['lectures'] );
				update_post_meta( $post_id, '_course_regular_price', $item['price'] );
				update_post_meta( $post_id, '_course_sale_price', $item['sale'] );
				update_post_meta( $post_id, '_course_rating', $item['rating'] );

				update_post_meta( $post_id, '_course_thumbnail_url', '/wp-content/themes/tijus-theme/assets/images/courses/' . $item['image'] );
			}
		}
		
		update_option( 'tijus_dummy_courses_created_v1', 1 );
	}
}
add_action( 'init', 'tijus_create_dummy_courses_once' );

/**
 * Flush rewrite rules once to fix 404 on single course pages.
 */
function tijus_flush_rules_once_fix() {
	if ( ! get_option( 'tijus_flush_rules_v1' ) ) {
		// Set permalink structure to something pretty (removes index.php dependency if possible)
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		
		flush_rewrite_rules( false );
		update_option( 'tijus_flush_rules_v1', 1 );
	}
}
add_action( 'init', 'tijus_flush_rules_once_fix', 99 );

/**
 * AJAX handler for courses filtering.
 */
function tijus_ajax_filter_courses() {
	check_ajax_referer( 'tijus_courses_nonce', 'nonce' );

	$paged         = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
	$current_cat   = isset( $_POST['course_category'] ) ? sanitize_text_field( wp_unslash( $_POST['course_category'] ) ) : '';
	$course_search = isset( $_POST['course_search'] ) ? sanitize_text_field( wp_unslash( $_POST['course_search'] ) ) : '';

	$args = [
		'post_type'      => 'course',
		'posts_per_page' => 12,
		'paged'          => $paged,
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

	if ( ! empty( $course_search ) ) {
		$args['s'] = $course_search;
	}

	$courses_query = new WP_Query( $args );

	ob_start();
	if ( $courses_query->have_posts() ) :
		while ( $courses_query->have_posts() ) : $courses_query->the_post();
			get_template_part( 'template-parts/content', 'course' );
		endwhile;
		wp_reset_postdata();
	else :
		echo '<div class="col-12"><p>No courses found.</p></div>';
	endif;
	$courses_html = ob_get_clean();

	ob_start();
	$total_pages = $courses_query->max_num_pages;
	if ( $total_pages > 1 ) {
		$current_page = max( 1, $paged );
		echo '<div class="col-12 mt-4 text-center pagination-area">';
		echo paginate_links( array(
			// Provide a clean format so JS can extract 'paged' easily from generated links
			'base'      => add_query_arg( 'paged', '%#%' ),
			'format'    => '',
			'current'   => $current_page,
			'total'     => $total_pages,
			'prev_text' => '<i class="icofont-rounded-left"></i>',
			'next_text' => '<i class="icofont-rounded-right"></i>',
		) );
		echo '</div>';
	}
	$pagination_html = ob_get_clean();

	wp_send_json_success( [
		'courses'    => $courses_html,
		'pagination' => $pagination_html,
	] );
}
add_action( 'wp_ajax_tijus_filter_courses', 'tijus_ajax_filter_courses' );
add_action( 'wp_ajax_nopriv_tijus_filter_courses', 'tijus_ajax_filter_courses' );

/**
 * Shortcode to display the dynamic Home Page courses tabbed section.
 */
function tijus_home_course_tabs_shortcode() {
    ob_start();
	$categories = get_terms( [
		'taxonomy'   => 'course_category',
		'hide_empty' => false,
	] );
	
	if ( empty($categories) || is_wp_error($categories) ) {
		$categories = [
			(object)['slug' => 'ui-ux-design', 'name' => 'UI/UX Design'],
			(object)['slug' => 'development', 'name' => 'Development'],
			(object)['slug' => 'data-science', 'name' => 'Data Science'],
			(object)['slug' => 'business', 'name' => 'Business'],
			(object)['slug' => 'financial', 'name' => 'Financial'],
			(object)['slug' => 'marketing', 'name' => 'Marketing'],
			(object)['slug' => 'design', 'name' => 'Design'],
		];
	}
	?>
	<!-- All Courses Tabs Menu Start -->
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
				<div class="courses-wrapper">
					<div class="row">
						<?php
						$has_real_terms = ! empty( get_terms( [ 'taxonomy' => 'course_category', 'hide_empty' => false ] ) );
						
						$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
						
						$args = [
							'post_type'      => 'course',
							'posts_per_page' => 6,
							'paged'          => $paged,
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
							
							$total_pages = $cat_query->max_num_pages;
							if ( $total_pages > 1 ) {
								echo '<div class="col-12 mt-4 text-center pagination-area">';
								echo paginate_links( [
									'base'      => add_query_arg( 'paged', '%#%' ),
									'format'    => '',
									'current'   => max( 1, $paged ),
									'total'     => $total_pages,
									'prev_text' => '<i class="icofont-rounded-left"></i>',
									'next_text' => '<i class="icofont-rounded-right"></i>',
								] );
								echo '</div>';
							}
							
							wp_reset_postdata();
						else :
							echo '<div class="col-12"><p>No courses found in ' . esc_html( $category->name ) . '.</p></div>';
						endif;
						?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<!-- All Courses tab content End -->
	<?php
	return ob_get_clean();
}
add_shortcode( 'tijus_home_course_tabs', 'tijus_home_course_tabs_shortcode' );

/**
 * Run-once fix to replace Elementor static HTML with shortcode for courses.
 */
function tijus_fix_elementor_courses_once() {
    if ( ! get_option( 'tijus_elementor_courses_dynamic_fix_5' ) ) {
        $front_page_id = (int) get_option( 'page_on_front' );
        if ( $front_page_id ) {
            $elementor_data = get_post_meta( $front_page_id, '_elementor_data', true );
            
            if ( $elementor_data && is_string( $elementor_data ) ) {
                $data = json_decode( $elementor_data, true );
                if ( is_array( $data ) ) {
                    $modified = false;
                    foreach ( $data as &$section ) {
                        if ( isset($section['elements']) && is_array($section['elements']) ) {
                            foreach ( $section['elements'] as &$column ) {
                                if ( isset($column['elements']) && is_array($column['elements']) ) {
                                    foreach ( $column['elements'] as &$widget ) {
                                        if ( $widget['widgetType'] === 'html' && isset($widget['settings']['html']) ) {
                                            $html = $widget['settings']['html'];
                                            // Ensure we replace everything inside the tabbed box with the shortcode
                                            $pattern = '/\<\!\-\- All Courses Tabs Menu Start \-\-\>.*?\<\!\-\- All Courses tab content End \-\-\>/s';
                                            
                                            if ( preg_match($pattern, $html) ) {
                                                $widget['settings']['html'] = preg_replace( $pattern, '[tijus_home_course_tabs]', $html );
                                                $modified = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ( $modified ) {
                        $new_json = wp_json_encode( $data );
                        update_post_meta( $front_page_id, '_elementor_data', wp_slash( $new_json ) );
                        if ( class_exists( '\Elementor\Plugin' ) ) {
                            \Elementor\Plugin::$instance->files_manager->clear_cache();
                        }
                    }
                }
            }
        }
        update_option( 'tijus_elementor_courses_dynamic_fix_5', 1 );
    }
}
add_action( 'init', 'tijus_fix_elementor_courses_once' );

/**
 * Run-once fix to update the "Other Course" button href to the actual courses page.
 */
function tijus_fix_elementor_courses_link_once() {
    if ( ! get_option( 'tijus_elementor_courses_link_fix_1' ) ) {
        $front_page_id = (int) get_option( 'page_on_front' );
        if ( $front_page_id ) {
            $elementor_data = get_post_meta( $front_page_id, '_elementor_data', true );
            
            if ( $elementor_data && is_string( $elementor_data ) ) {
                if ( strpos($elementor_data, 'href="courses.html"') !== false || strpos($elementor_data, 'href=\"courses.html\"') !== false ) {
                    // Update all statically linked instances of courses.html to point to /courses/
                    // Note: elementor data is json, so double quotes are escaped as \" 
                    $new_data = str_replace( 'href=\"courses.html\"', 'href=\"/courses/\"', $elementor_data );
                    $new_data = str_replace( 'href="courses.html"', 'href="/courses/"', $new_data );
                    
                    update_post_meta( $front_page_id, '_elementor_data', wp_slash( $new_data ) );
                    
                    if ( class_exists( '\Elementor\Plugin' ) ) {
                        \Elementor\Plugin::$instance->files_manager->clear_cache();
                    }
                }
            }
        }
        update_option( 'tijus_elementor_courses_link_fix_1', 1 );
    }
}
add_action( 'init', 'tijus_fix_elementor_courses_link_once' );

/**
 * Display a single course comment (review)
 */
function tijus_course_comment_callback( $comment, $args, $depth ) {
	$rating = get_comment_meta( $comment->comment_ID, 'course_rating', true );
	if ( ! $rating ) $rating = 5; // Default fallback to 5

	?>
	<li <?php comment_class( 'comment' ); ?> id="li-comment-<?php comment_ID(); ?>">
		<div class="comment-avatar">
			<?php echo get_avatar( $comment, 60 ); ?>
		</div>
		<div class="comment-body w-100">
			<div class="d-flex justify-content-between align-items-start">
				<div>
					<div class="comment-author"><?php echo get_comment_author(); ?></div>
					<div class="comment-date"><?php printf( '%1$s at %2$s', get_comment_date(), get_comment_time() ); ?></div>
				</div>
				<div class="comment-rating mt-1">
					<?php for ( $i = 1; $i <= 5; $i++ ) {
						echo '<i class="icofont-star ' . ( $i <= $rating ? '' : 'empty' ) . '"></i> ';
					} ?>
				</div>
			</div>
			<div class="comment-content">
				<?php comment_text(); ?>
			</div>
		</div>
	</li>
	<?php
}

/**
 * Save rating meta and update overall course rating.
 */
function tijus_save_course_comment_rating( $comment_id, $comment_approved, $commentdata ) {
	if ( isset( $_POST['course_rating'] ) && $commentdata['comment_post_ID'] ) {
		$rating = absint( $_POST['course_rating'] );
		if ( $rating > 0 && $rating <= 5 ) {
			add_comment_meta( $comment_id, 'course_rating', $rating );

			// Recalculate average rating for the course
			$post_id = $commentdata['comment_post_ID'];
			$comments = get_comments( [
				'post_id' => $post_id,
				'status'  => 'approve'
			] );

			if ( ! empty( $comments ) ) {
				$total = 0;
				$count = 0;
				foreach ( $comments as $c ) {
					$r = get_comment_meta( $c->comment_ID, 'course_rating', true );
					if ( $r ) {
						$total += intval( $r );
						$count++;
					}
				}
				if ( $count > 0 ) {
					$avg = round( $total / $count, 1 );
					update_post_meta( $post_id, '_course_rating', $avg );
				}
			}
		}
	}
}
add_action( 'comment_post', 'tijus_save_course_comment_rating', 10, 3 );

/**
 * Run-once fix to open comments for all existing courses.
 */
function tijus_open_existing_course_comments_once() {
    if ( ! get_option( 'tijus_course_comments_opened_1' ) ) {
        global $wpdb;
        $wpdb->query( "UPDATE {$wpdb->posts} SET comment_status = 'open' WHERE post_type = 'course'" );
        update_option( 'tijus_course_comments_opened_1', 1 );
    }
}
add_action( 'init', 'tijus_open_existing_course_comments_once' );

/**
 * Auto-detect and fix hardcoded database URLs on the fly for staging and local sync natively.
 * Compares the raw DB site URL with current auto-detected WP_SITEURL.
 */
add_action( 'template_redirect', 'tijus_dynamic_url_replacement', -9999 );
function tijus_dynamic_url_replacement() {
    if ( ! is_admin() ) {
        ob_start( 'tijus_replace_hardcoded_urls' );
    }
}

function tijus_replace_hardcoded_urls( $html ) {
    global $wpdb;
    static $original_url = null;
    $current_url = home_url();

    if ( $original_url === null ) {
        $original_url = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
    }

    if ( $original_url && $original_url !== $current_url ) {
        $html = str_replace( $original_url, $current_url, $html );
        $html = str_replace( str_replace('/', '\/', $original_url), str_replace('/', '\/', $current_url), $html );
    }
    return $html;
}

/**
 * Flush Elementor CSS cache automatically if environment URL changes.
 */
add_action( 'init', 'tijus_auto_flush_elementor_css' );
function tijus_auto_flush_elementor_css() {
    global $wpdb;
    
    // Avoid running this on every single page load if possible, but safe since it only checks transient
    $original_url = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'" );
    if ( $original_url && $original_url !== home_url() ) {
		if ( ! get_transient( 'tijus_elementor_cache_cleared_' . md5( home_url() ) ) ) {
			if ( class_exists( '\Elementor\Plugin' ) ) {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			}
			set_transient( 'tijus_elementor_cache_cleared_' . md5( home_url() ), true, DAY_IN_SECONDS );
		}
    }
}

/**
 * Register widget area.
 */
function tijus_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'tijus-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'tijus-theme' ),
			'before_widget' => '<div id="%1$s" class="sidebar-widget widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'tijus_widgets_init' );

/**
 * Enable user registration.
 */
add_action( 'init', function() {
    if ( ! get_option( 'users_can_register' ) ) {
        update_option( 'users_can_register', 1 );
    }
} );

/**
 * Register Feedback (Testimonials) Custom Post Type.
 */
function tijus_register_feedback_cpt() {
    $labels = [
        'name'               => 'Feedbacks',
        'singular_name'      => 'Feedback',
        'menu_name'          => 'Feedbacks',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Feedback',
        'edit_item'          => 'Edit Feedback',
        'new_item'           => 'New Feedback',
        'view_item'          => 'View Feedback',
        'search_items'       => 'Search Feedbacks',
        'not_found'          => 'No feedbacks found',
        'not_found_in_trash' => 'No feedbacks found in Trash',
    ];

    $args = [
        'labels'              => $labels,
        'public'              => false, // We only display these via loops, not separate single pages
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 21,
        'menu_icon'           => 'dashicons-testimonial',
        'supports'            => [ 'title', 'editor', 'thumbnail' ], // Title = Name, Editor = Feedback content, Thumbnail = Author Photo
    ];

    register_post_type( 'tijus_feedback', $args );
}
add_action( 'init', 'tijus_register_feedback_cpt' );

/**
 * Register Meta Boxes for Feedbacks.
 */
function tijus_add_feedback_meta_boxes() {
    add_meta_box(
        'tijus_feedback_details',
        'Feedback Details',
        'tijus_render_feedback_meta_box',
        'tijus_feedback',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tijus_add_feedback_meta_boxes' );

function tijus_render_feedback_meta_box( $post ) {
    wp_nonce_field( 'tijus_save_feedback_data', 'tijus_feedback_meta_nonce' );

    $designation = get_post_meta( $post->ID, '_feedback_designation', true );
    $rating      = get_post_meta( $post->ID, '_feedback_rating', true );
    if ( ! $rating ) $rating = 5; // Default 5 stars
    ?>
    <table class="form-table">
        <tr>
            <th><label for="feedback_designation">Designation / Role / Location</label></th>
            <td><input type="text" id="feedback_designation" name="feedback_designation" value="<?php echo esc_attr( $designation ); ?>" class="regular-text" placeholder="e.g. Product Designer, USA" /></td>
        </tr>
        <tr>
            <th><label for="feedback_rating">Star Rating (1-5)</label></th>
            <td>
                <select name="feedback_rating" id="feedback_rating">
                    <option value="5" <?php selected($rating, 5); ?>>5 Stars</option>
                    <option value="4" <?php selected($rating, 4); ?>>4 Stars</option>
                    <option value="3" <?php selected($rating, 3); ?>>3 Stars</option>
                    <option value="2" <?php selected($rating, 2); ?>>2 Stars</option>
                    <option value="1" <?php selected($rating, 1); ?>>1 Star</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function tijus_save_feedback_meta_data( $post_id ) {
    if ( ! isset( $_POST['tijus_feedback_meta_nonce'] ) || ! wp_verify_nonce( $_POST['tijus_feedback_meta_nonce'], 'tijus_save_feedback_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['feedback_designation'] ) ) {
        update_post_meta( $post_id, '_feedback_designation', sanitize_text_field( wp_unslash( $_POST['feedback_designation'] ) ) );
    }
    if ( isset( $_POST['feedback_rating'] ) ) {
        update_post_meta( $post_id, '_feedback_rating', absint( $_POST['feedback_rating'] ) );
    }
}
add_action( 'save_post_tijus_feedback', 'tijus_save_feedback_meta_data' );

/**
 * Creates dummy feedbacks automatically.
 */
function tijus_seed_dummy_feedback() {
    if ( ! get_option( 'tijus_dummy_feedbacks_created_v1' ) ) {
        $dummy_data = [
            [
                'name' => 'Sara Alexander',
                'desc' => "Lorem Ipsum has been the industry's standard dummy text since the 1500s, when an unknown printer took a galley of type and scrambled it to make type specimen book has survived not five centuries but also the leap into electronic.",
                'role' => 'Product Designer, USA',
                'img'  => 'author-06.jpg',
                'rate' => 5
            ],
            [
                'name' => 'Melissa Roberts',
                'desc' => "Lorem Ipsum has been the industry's standard dummy text since the 1500s, when an unknown printer took a galley of type and scrambled it to make type specimen book has survived not five centuries but also the leap into electronic.",
                'role' => 'Product Designer, USA',
                'img'  => 'author-07.jpg',
                'rate' => 4
            ],
            [
                'name' => 'Sara Alexander', // Intentionally duplicated dummy logic from original html
                'desc' => "Lorem Ipsum has been the industry's standard dummy text since the 1500s, when an unknown printer took a galley of type and scrambled it to make type specimen book has survived not five centuries but also the leap into electronic.",
                'role' => 'Product Designer, USA',
                'img'  => 'author-03.jpg',
                'rate' => 5
            ]
        ];

        foreach ( $dummy_data as $item ) {
            $post_id = wp_insert_post( [
                'post_title'   => $item['name'],
                'post_content' => $item['desc'],
                'post_status'  => 'publish',
                'post_type'    => 'tijus_feedback',
            ] );

            if ( ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, '_feedback_designation', $item['role'] );
                update_post_meta( $post_id, '_feedback_rating', $item['rate'] );
                update_post_meta( $post_id, '_feedback_dummy_img', $item['img'] ); // Store specific dummy img path
            }
        }
        
        update_option( 'tijus_dummy_feedbacks_created_v1', 1 );
    }
}
add_action( 'init', 'tijus_seed_dummy_feedback' );

/**
 * Creates Dashboard Page Automatically.
 */
function tijus_seed_dashboard_page() {
    if ( ! get_option( 'tijus_dashboard_page_created_v1' ) ) {
        $page = get_page_by_path( 'dashboard' );
        if ( ! $page ) {
            $page_id = wp_insert_post( [
                'post_title'   => 'Dashboard',
                'post_name'    => 'dashboard',
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ] );
            if ( ! is_wp_error( $page_id ) ) {
                update_post_meta( $page_id, '_wp_page_template', 'template-dashboard.php' );
            }
        }
        update_option( 'tijus_dashboard_page_created_v1', 1 );
        flush_rewrite_rules(); // Ensure rewrite rules are updated
    }
}
add_action( 'init', 'tijus_seed_dashboard_page' );
/**
 * Display dynamic blog carousel
 */
function tijus_latest_blogs_shortcode() {
    ob_start();
    ?>
    <!-- Blog Wrapper Start (Dynamic Carousel) -->
    <div class="container">
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
    </div>
    <!-- Blog Wrapper End -->
    <?php
    return ob_get_clean();
}
add_shortcode( 'tijus_latest_blogs', 'tijus_latest_blogs_shortcode' );

/**
 * Hide the WordPress admin bar for logged in users (except administrators).
 */
function tijus_remove_admin_bar() {
    if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
        show_admin_bar( false );
    }
}
add_action( 'after_setup_theme', 'tijus_remove_admin_bar' );

/**
 * AJAX handler for updating profile name from student dashboard.
 */
function tijus_update_profile_ajax_handler() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( 'You must be logged in to update your profile.' );
    }

    $current_user_id = get_current_user_id();
    $full_name = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';

    if ( empty( $full_name ) ) {
        wp_send_json_error( 'Full name cannot be empty.' );
    }

    $userdata = array(
        'ID'           => $current_user_id,
        'display_name' => $full_name,
    );

    $name_parts = explode( ' ', $full_name, 2 );
    if ( ! empty( $name_parts[0] ) ) {
        $userdata['first_name'] = $name_parts[0];
    }
    if ( ! empty( $name_parts[1] ) ) {
        $userdata['last_name'] = $name_parts[1];
    }

    $user_id = wp_update_user( $userdata );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( 'Failed to update profile: ' . $user_id->get_error_message() );
    }

    wp_send_json_success( 'Profile updated successfully!' );
}
add_action( 'wp_ajax_tijus_update_profile', 'tijus_update_profile_ajax_handler' );

/**
 * Shortcode for dynamic Certifications Section.
 */
function tijus_certifications_shortcode() {
    ob_start();
    ?>
    <!-- Brand Logo Wrapper Start -->
    <div class="brand-logo-wrapper">
        <img class="shape-1" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/shape/shape-19.png" alt="Shape">
        <img class="shape-2 animation-round" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/shape/shape-20.png" alt="Shape">

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
                        'posts_per_page' => -1,
                        'post_status'    => 'publish',
                    );
                    $cert_query = new WP_Query($cert_args);

                    if ($cert_query->have_posts()) :
                        while ($cert_query->have_posts()) : $cert_query->the_post();
                            if (has_post_thumbnail()) :
                                ?>
                                <div class="single-brand swiper-slide">
                                    <?php the_post_thumbnail('full', array('alt' => get_the_title())); ?>
                                </div>
                                <?php
                            endif;
                        endwhile;
                        wp_reset_postdata();
                    else :
                        for ($i = 1; $i <= 6; $i++) {
                            echo '<div class="single-brand swiper-slide">';
                            echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/brand/brand-0' . $i . '.png" alt="Brand">';
                            echo '</div>';
                        }
                    endif;
                    ?>
                </div>
            </div>
        </div>
        <!-- Brand Logo End -->
    </div>
    <!-- Brand Logo Wrapper End -->
    <?php
    return ob_get_clean();
}
add_shortcode( 'tijus_certifications', 'tijus_certifications_shortcode' );

/**
 * Run-once fix to replace Elementor static HTML with shortcode for certifications.
 */
function tijus_fix_elementor_certifications_once() {
    if ( ! get_option( 'tijus_elementor_certifications_dynamic_fix_1' ) ) {
        
        $pages_to_fix = [];
        $front_page_id = (int) get_option( 'page_on_front' );
        if ( $front_page_id ) {
            $pages_to_fix[] = $front_page_id;
        }
        
        // Also fix the about page if we can find it
        $about_page = get_page_by_path( 'about' );
        if ( $about_page ) {
            $pages_to_fix[] = $about_page->ID;
        }

        foreach ( $pages_to_fix as $page_id ) {
            $elementor_data = get_post_meta( $page_id, '_elementor_data', true );
            
            if ( $elementor_data && is_string( $elementor_data ) ) {
                $data = json_decode( $elementor_data, true );
                if ( is_array( $data ) ) {
                    $modified = false;
                    foreach ( $data as &$section ) {
                        if ( isset($section['elements']) && is_array($section['elements']) ) {
                            foreach ( $section['elements'] as &$column ) {
                                if ( isset($column['elements']) && is_array($column['elements']) ) {
                                    foreach ( $column['elements'] as &$widget ) {
                                        if ( $widget['widgetType'] === 'html' && isset($widget['settings']['html']) ) {
                                            $html = $widget['settings']['html'];
                                            
                                            // Ensure we replace everything inside the Brand Logo wrapper with the shortcode
                                            $pattern = '/\<\!\-\- Brand Logo Wrapper Start \-\-\>.*?\<\!\-\- Brand Logo Wrapper End \-\-\>/s';
                                            
                                            if ( preg_match($pattern, $html) ) {
                                                $widget['settings']['html'] = preg_replace( $pattern, '[tijus_certifications]', $html );
                                                $modified = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ( $modified ) {
                        $new_json = wp_json_encode( $data );
                        update_post_meta( $page_id, '_elementor_data', wp_slash( $new_json ) );
                    }
                }
            }
        }
        
        if ( class_exists( '\Elementor\Plugin' ) ) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }
        
        update_option( 'tijus_elementor_certifications_dynamic_fix_1', 1 );
    }
}
add_action( 'init', 'tijus_fix_elementor_certifications_once' );
