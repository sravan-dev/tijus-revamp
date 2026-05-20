<?php
/**
 * Elementor Home Page Seeder
 *
 * Converts static-home.php into individual Elementor-editable sections on the front page.
 * Each homepage section becomes a separate Elementor section so widgets can be inserted between them.
 * Access via: WP Admin → Tools → Seed Home Page
 *
 * @package tijus-theme
 */

add_action( 'admin_menu', 'tijus_seeder_admin_page' );

function tijus_seeder_admin_page() {
	add_management_page(
		'Seed Home Page for Elementor',
		'Seed Home Page',
		'manage_options',
		'tijus-seed-home',
		'tijus_seeder_render_page'
	);
}

function tijus_seeder_render_page() {
	$result = '';

	if ( isset( $_POST['tijus_run_seeder'] ) && check_admin_referer( 'tijus_seed_home_nonce' ) ) {
		$result = tijus_seed_front_page_elementor_data();
	}

	?>
	<div class="wrap">
		<h1>Seed Home Page for Elementor</h1>
		<?php if ( $result ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo wp_kses_post( $result ); ?></p></div>
		<?php endif; ?>
		<p>Converts the static home template into <strong>8 separate Elementor sections</strong> — one per homepage section — so you can drag new widgets between them.</p>
		<p><strong>Note:</strong> This overwrites any existing Elementor data on the front page. Run once, then edit freely in Elementor.</p>
		<form method="post">
			<?php wp_nonce_field( 'tijus_seed_home_nonce' ); ?>
			<p><input type="submit" name="tijus_run_seeder" class="button button-primary button-large" value="Seed Elementor Content on Front Page"></p>
		</form>
	</div>
	<?php
}

function tijus_seed_front_page_elementor_data() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return 'Elementor is not active. Please install and activate the Elementor plugin first.';
	}

	$front_page_id = (int) get_option( 'page_on_front' );

	if ( ! $front_page_id ) {
		return 'No static front page is set. Go to <a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '">Settings &rarr; Reading</a> and assign a static front page, then re-run this tool.';
	}

	// Read the raw static-home.php file.
	$file_path = get_template_directory() . '/template-parts/static-home.php';
	$raw       = file_get_contents( $file_path );

	if ( ! $raw ) {
		return 'Could not read static-home.php.';
	}

	// Replace the only PHP call in this file with the actual theme URL.
	$theme_uri = get_template_directory_uri();
	$html      = preg_replace(
		'/\<\?php\s+echo\s+get_template_directory_uri\(\);\s*\?\>/',
		esc_url( $theme_uri ),
		$raw
	);

	// Split HTML into lines (1-based index matching original file).
	$lines = explode( "\n", $html );

	/**
	 * Top-level section definitions.
	 * Line numbers are 1-based and match static-home.php.
	 * Note: "How It Work" and "Testimonial" have their opening comment mislabeled
	 * as "End" in the source file — we use the line ranges directly to avoid that issue.
	 */
	$sections = [
		'Slider'         => [ 1,    76   ],
		'All Courses'    => [ 78,   1942 ],
		'Call to Action' => [ 1944, 1977 ],
		'How It Work'    => [ 1979, 2051 ],
		'Download App'   => [ 2053, 2089 ],
		'Testimonial'    => [ 2091, 2178 ],
		'Brand Logo'     => [ 2180, 2248 ],
		'Blog'           => [ 2250, 2375 ],
	];

	$elementor_data = [];
	$i              = 1;

	foreach ( $sections as $name => $range ) {
		list( $start_line, $end_line ) = $range;

		// array_slice uses 0-based offset; convert from 1-based line numbers.
		$section_lines = array_slice( $lines, $start_line - 1, $end_line - $start_line + 1 );
		$section_html  = implode( "\n", $section_lines );

		$n = str_pad( $i, 2, '0', STR_PAD_LEFT );

		$elementor_data[] = [
			'id'       => 'tijuss' . $n,
			'elType'   => 'section',
			'settings' => [
				'layout'  => 'full_width',
				'padding' => [
					'unit'     => 'px',
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => false,
				],
			],
			'elements' => [
				[
					'id'       => 'tijusc' . $n,
					'elType'   => 'column',
					'settings' => [
						'_column_size' => 100,
						'_inline_size' => null,
					],
					'elements' => [
						[
							'id'         => 'tijusw' . $n,
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => $section_html,
							],
							'elements'   => [],
						],
					],
					'isInner'  => false,
				],
			],
			'isInner'  => false,
		];

		$i++;
	}

	$data_json = wp_json_encode( $elementor_data );

	if ( ! $data_json ) {
		return 'JSON encoding failed. The HTML content may contain invalid characters.';
	}

	// wp_slash() is required — WordPress strips backslashes from post meta on save,
	// which corrupts Elementor's JSON and causes the editor to show empty content.
	update_post_meta( $front_page_id, '_elementor_data', wp_slash( $data_json ) );
	update_post_meta( $front_page_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $front_page_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $front_page_id, '_elementor_version', '3.23.4' );

	// Set page template to Elementor Full Width.
	update_post_meta( $front_page_id, '_wp_page_template', 'elementor_header_footer' );

	// Clear Elementor CSS/file cache.
	\Elementor\Plugin::$instance->files_manager->clear_cache();

	$edit_url = get_edit_post_link( $front_page_id );
	$view_url = get_permalink( $front_page_id );

	return 'Front page seeded with 8 separate sections! <a href="' . esc_url( $edit_url ) . '">Edit in WordPress</a> &nbsp;|&nbsp; <a href="' . esc_url( $view_url ) . '" target="_blank">View front page</a>';
}
