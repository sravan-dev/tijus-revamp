<?php
/**
 * Tijus Theme Options – Admin Sidebar Page
 *
 * @package tijus-theme
 */

add_action( 'admin_menu', 'tijus_options_add_menu_page' );

function tijus_options_add_menu_page() {
	add_menu_page(
		'Theme Options',
		'Theme Options',
		'manage_options',
		'tijus-theme-options',
		'tijus_options_render_page',
		'dashicons-admin-customizer',
		61
	);
}

// Enqueue WP media uploader on this page only.
add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( $hook !== 'toplevel_page_tijus-theme-options' ) return;
	wp_enqueue_media();
} );

add_action( 'admin_init', 'tijus_options_save' );

function tijus_options_save() {
	if (
		! isset( $_POST['tijus_options_nonce'] ) ||
		! wp_verify_nonce( $_POST['tijus_options_nonce'], 'tijus_save_options' ) ||
		! current_user_can( 'manage_options' )
	) {
		return;
	}

	$fields = [
		'tijus_logo'                 => 'esc_url_raw',
		'tijus_announcement_text'    => 'wp_kses_post',
		'tijus_announcement_link'    => 'esc_url_raw',
		'tijus_phone'                => 'sanitize_text_field',
		'tijus_email'                => 'sanitize_email',
		'tijus_footer_address_title' => 'sanitize_text_field',
		'tijus_footer_address_city'  => 'sanitize_text_field',
		'tijus_copyright_text'       => 'wp_kses_post',
		'tijus_logo_width'           => 'absint',
		'tijus_logo_height'          => 'absint',
		'tijus_footer_logo_width'    => 'absint',
		'tijus_footer_logo_height'   => 'absint',
		'tijus_color_palette'        => 'sanitize_key',
	];

	foreach ( $fields as $key => $sanitize_cb ) {
		if ( isset( $_POST[ $key ] ) ) {
			set_theme_mod( $key, call_user_func( $sanitize_cb, wp_unslash( $_POST[ $key ] ) ) );
		}
	}

	// Social links – stored as a JSON array.
	if ( isset( $_POST['tijus_social_icon'] ) && is_array( $_POST['tijus_social_icon'] ) ) {
		$social_links = [];
		$icons  = array_map( 'sanitize_html_class', array_map( 'wp_unslash', $_POST['tijus_social_icon'] ) );
		$urls   = isset( $_POST['tijus_social_url'] ) && is_array( $_POST['tijus_social_url'] )
			? array_map( 'esc_url_raw', array_map( 'wp_unslash', $_POST['tijus_social_url'] ) )
			: [];
		$labels = isset( $_POST['tijus_social_label'] ) && is_array( $_POST['tijus_social_label'] )
			? array_map( 'sanitize_text_field', array_map( 'wp_unslash', $_POST['tijus_social_label'] ) )
			: [];
		foreach ( $icons as $i => $icon ) {
			if ( ! empty( $icon ) ) {
				$social_links[] = [
					'label' => $labels[ $i ] ?? '',
					'icon'  => $icon,
					'url'   => $urls[ $i ] ?? '#',
				];
			}
		}
		set_theme_mod( 'tijus_social_links', wp_json_encode( $social_links ) );
	}

	wp_redirect( add_query_arg( [ 'page' => 'tijus-theme-options', 'saved' => '1' ], admin_url( 'admin.php' ) ) );
	exit;
}

function tijus_options_render_page() {
	$saved = isset( $_GET['saved'] ) && $_GET['saved'] === '1';
	$logo  = get_theme_mod( 'tijus_logo', '' );
	?>
	<style>
		.tijus-opts-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 20px;
			margin-top: 20px;
			align-items: start;
		}
		.tijus-opts-grid .postbox { margin: 0; }
		.tijus-opts-grid .postbox .inside { padding: 0 16px 16px; }
		.tijus-opts-grid .form-table { margin: 0; }
		.tijus-opts-grid .form-table th {
			width: 140px;
			padding: 12px 10px 12px 0;
			font-weight: 600;
			white-space: nowrap;
			vertical-align: middle;
		}
		.tijus-opts-grid .form-table td {
			padding: 10px 0;
			vertical-align: middle;
		}
		.tijus-opts-grid .form-table input[type="text"],
		.tijus-opts-grid .form-table input[type="url"],
		.tijus-opts-grid .form-table input[type="email"],
		.tijus-opts-grid .form-table textarea {
			width: 100%;
			max-width: 100%;
			box-sizing: border-box;
		}
		.tijus-logo-preview {
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 12px;
			background: #f6f7f7;
			border: 1px solid #dcdcde;
			border-radius: 4px;
			margin-bottom: 14px;
		}
		.tijus-logo-preview img {
			max-height: 52px;
			max-width: 160px;
			object-fit: contain;
			background: #fff;
			border: 1px solid #ddd;
			padding: 4px;
			border-radius: 3px;
		}
		.tijus-logo-preview .tijus-logo-placeholder {
			color: #999;
			font-style: italic;
			font-size: 13px;
		}
		.tijus-logo-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
		#tijus_logo { flex: 1; min-width: 0; }

		/* Color Palette Picker */
		.tijus-palette-grid {
			display: flex;
			gap: 12px;
			flex-wrap: wrap;
			padding: 4px 0;
		}
		.tijus-palette-card {
			position: relative;
			cursor: pointer;
			border: 2px solid #dcdcde;
			border-radius: 8px;
			padding: 12px 14px 10px;
			background: #fff;
			text-align: center;
			transition: border-color .15s, box-shadow .15s;
			min-width: 110px;
		}
		.tijus-palette-card:hover {
			border-color: #2271b1;
		}
		.tijus-palette-card.active {
			border-color: #2271b1;
			box-shadow: 0 0 0 1px #2271b1;
		}
		.tijus-palette-card.active::after {
			content: "\f147";
			font-family: dashicons;
			position: absolute;
			top: -8px;
			right: -8px;
			background: #2271b1;
			color: #fff;
			border-radius: 50%;
			width: 20px;
			height: 20px;
			font-size: 16px;
			line-height: 20px;
			text-align: center;
		}
		.tijus-palette-card input[type="radio"] {
			display: none;
		}
		.tijus-palette-dots {
			display: flex;
			gap: 4px;
			justify-content: center;
			margin-bottom: 6px;
		}
		.tijus-palette-dot {
			width: 22px;
			height: 22px;
			border-radius: 50%;
			border: 1px solid rgba(0,0,0,.1);
		}
		.tijus-palette-label {
			font-size: 11px;
			font-weight: 600;
			color: #555;
		}
	</style>

	<div class="wrap">
		<h1 style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
			<span class="dashicons dashicons-admin-customizer" style="font-size:26px;width:26px;height:26px;color:#2271b1;margin-top:2px;"></span>
			Theme Options
		</h1>
		<p style="color:#666;margin-top:0;">Changes apply immediately to header and footer across the site.</p>

		<?php if ( $saved ) : ?>
			<div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully.</strong></p></div>
		<?php endif; ?>

		<form method="post" action="">
			<?php wp_nonce_field( 'tijus_save_options', 'tijus_options_nonce' ); ?>

			<?php /* ── Color Palette ────────────────── */ ?>
			<?php
			$palettes        = tijus_get_color_palettes();
			$active_palette  = get_theme_mod( 'tijus_color_palette', 'default' );
			?>
			<div class="postbox" style="margin-top:20px;">
				<div class="postbox-header"><h2 class="hndle">Color Palette</h2></div>
				<div class="inside" style="padding:12px 16px 16px;">
					<div class="tijus-palette-grid">
						<?php foreach ( $palettes as $key => $pal ) : ?>
						<label class="tijus-palette-card<?php echo $key === $active_palette ? ' active' : ''; ?>">
							<input type="radio" name="tijus_color_palette" value="<?php echo esc_attr( $key ); ?>"
								<?php checked( $active_palette, $key ); ?>>
							<div class="tijus-palette-dots">
								<span class="tijus-palette-dot" style="background:<?php echo esc_attr( $pal['primary'] ); ?>;"></span>
								<span class="tijus-palette-dot" style="background:<?php echo esc_attr( $pal['success'] ); ?>;"></span>
								<span class="tijus-palette-dot" style="background:<?php echo esc_attr( $pal['warning'] ); ?>;"></span>
								<span class="tijus-palette-dot" style="background:<?php echo esc_attr( $pal['danger'] ); ?>;"></span>
								<span class="tijus-palette-dot" style="background:<?php echo esc_attr( $pal['dark'] ); ?>;"></span>
							</div>
							<span class="tijus-palette-label"><?php echo esc_html( $pal['label'] ); ?></span>
						</label>
						<?php endforeach; ?>
					</div>
					<p class="description" style="margin-top:10px;">Select a color palette. Changes apply site-wide after saving.</p>
				</div>
			</div>

			<div class="tijus-opts-grid">

				<?php /* ── Logo ─────────────────────────────── */ ?>
				<div class="postbox">
					<div class="postbox-header"><h2 class="hndle">Logo</h2></div>
					<div class="inside">
						<div class="tijus-logo-preview">
							<?php if ( $logo ) : ?>
								<img id="tijus-logo-preview-img" src="<?php echo esc_url( $logo ); ?>" alt="Current logo">
								<span style="font-size:12px;color:#555;">Current logo</span>
							<?php else : ?>
								<span class="tijus-logo-placeholder" id="tijus-logo-preview-img" style="display:block;">No logo set — default theme logo in use.</span>
							<?php endif; ?>
						</div>
						<table class="form-table">
							<tr>
								<th style="vertical-align:top;padding-top:14px;"><label>Logo Image</label></th>
								<td>
									<div class="tijus-logo-actions">
										<input type="url" id="tijus_logo" name="tijus_logo"
											value="<?php echo esc_attr( $logo ); ?>"
											placeholder="https://..." style="flex:1;min-width:180px;">
										<button type="button" id="tijus-logo-browse" class="button button-secondary">
											<span class="dashicons dashicons-format-image" style="vertical-align:middle;margin-right:3px;font-size:16px;height:16px;width:16px;"></span>
											Browse
										</button>
										<?php if ( $logo ) : ?>
										<button type="button" id="tijus-logo-remove" class="button" style="color:#b32d2e;">Remove</button>
										<?php endif; ?>
									</div>
									<p class="description" style="margin-top:6px;">Select from Media Library or paste a URL directly.</p>
								</td>
							</tr>
							<tr>
								<th><label>Dimensions</label></th>
								<td>
									<div style="display:flex;gap:8px;align-items:center;">
										<input type="text" id="tijus_logo_width" name="tijus_logo_width"
											value="<?php echo esc_attr( get_theme_mod( 'tijus_logo_width', '' ) ); ?>"
											placeholder="Auto" style="width:80px;"> <span>×</span>
										<input type="text" id="tijus_logo_height" name="tijus_logo_height"
											value="<?php echo esc_attr( get_theme_mod( 'tijus_logo_height', '' ) ); ?>"
											placeholder="Auto" style="width:80px;"> <span>px</span>
									</div>
									<p class="description" style="margin-top:6px;">Header logo width and height in pixels. Leave blank for auto.</p>
								</td>
							</tr>
							<tr>
								<th><label>Footer Logo Size</label></th>
								<td>
									<div style="display:flex;gap:8px;align-items:center;">
										<input type="text" id="tijus_footer_logo_width" name="tijus_footer_logo_width"
											value="<?php echo esc_attr( get_theme_mod( 'tijus_footer_logo_width', '' ) ); ?>"
											placeholder="Auto" style="width:80px;"> <span>×</span>
										<input type="text" id="tijus_footer_logo_height" name="tijus_footer_logo_height"
											value="<?php echo esc_attr( get_theme_mod( 'tijus_footer_logo_height', '' ) ); ?>"
											placeholder="Auto" style="width:80px;"> <span>px</span>
									</div>
									<p class="description" style="margin-top:6px;">Footer logo width and height in pixels. Leave blank for auto.</p>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<?php /* ── Header Top Bar ──────────────────── */ ?>
				<div class="postbox">
					<div class="postbox-header"><h2 class="hndle">Header Top Bar</h2></div>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="tijus_announcement_text">Announcement</label></th>
								<td><input type="text" id="tijus_announcement_text" name="tijus_announcement_text"
									value="<?php echo esc_attr( get_theme_mod( 'tijus_announcement_text', "All course 28% off for Liberian people's." ) ); ?>"></td>
							</tr>
							<tr>
								<th><label for="tijus_announcement_link">Link URL</label></th>
								<td><input type="url" id="tijus_announcement_link" name="tijus_announcement_link"
									value="<?php echo esc_attr( get_theme_mod( 'tijus_announcement_link', '#' ) ); ?>"
									placeholder="https://..."></td>
							</tr>
							<tr>
								<th><label for="tijus_phone">Phone</label></th>
								<td><input type="text" id="tijus_phone" name="tijus_phone"
									value="<?php echo esc_attr( get_theme_mod( 'tijus_phone', '(970) 262-1413' ) ); ?>"></td>
							</tr>
							<tr>
								<th><label for="tijus_email">Email</label></th>
								<td><input type="email" id="tijus_email" name="tijus_email"
									value="<?php echo esc_attr( get_theme_mod( 'tijus_email', 'address@gmail.com' ) ); ?>"></td>
							</tr>
						</table>
					</div>
				</div>

				<?php /* ── Social Links ─────────────────────── */ ?>
				<div class="postbox">
					<div class="postbox-header"><h2 class="hndle">Social Media Links</h2></div>
					<div class="inside">
						<?php
						$_sl_raw = get_theme_mod( 'tijus_social_links', '' );
						if ( $_sl_raw ) {
							$_sl_list = json_decode( $_sl_raw, true ) ?: [];
						} else {
							$_sl_list = [
								[ 'label' => 'Facebook',  'icon' => 'flaticon-facebook',  'url' => get_theme_mod( 'tijus_facebook_url',  '#' ) ],
								[ 'label' => 'Twitter',   'icon' => 'flaticon-twitter',   'url' => get_theme_mod( 'tijus_twitter_url',   '#' ) ],
								[ 'label' => 'Skype',     'icon' => 'flaticon-skype',     'url' => get_theme_mod( 'tijus_skype_url',     '#' ) ],
								[ 'label' => 'Instagram', 'icon' => 'flaticon-instagram', 'url' => get_theme_mod( 'tijus_instagram_url', '#' ) ],
							];
						}
						?>
						<div id="tijus-social-rows">
							<?php foreach ( $_sl_list as $_sl ) : ?>
							<div class="tijus-social-row" style="display:flex;gap:6px;align-items:center;margin-bottom:8px;background:#f9f9f9;padding:8px;border:1px solid #e5e5e5;border-radius:3px;">
								<input type="text" name="tijus_social_label[]" placeholder="Label" value="<?php echo esc_attr( $_sl['label'] ?? '' ); ?>" style="width:90px;flex-shrink:0;">
								<input type="text" name="tijus_social_icon[]" placeholder="e.g. flaticon-facebook" value="<?php echo esc_attr( $_sl['icon'] ?? '' ); ?>" style="flex:1;min-width:0;">
								<input type="url"  name="tijus_social_url[]"  placeholder="https://..." value="<?php echo esc_attr( $_sl['url'] ?? '#' ); ?>" style="flex:1;min-width:0;">
								<button type="button" class="button tijus-remove-social" style="color:#b32d2e;flex-shrink:0;">Remove</button>
							</div>
							<?php endforeach; ?>
						</div>
						<button type="button" id="tijus-add-social" class="button button-secondary" style="margin-top:4px;">
							<span class="dashicons dashicons-plus-alt2" style="vertical-align:middle;margin-right:3px;font-size:16px;height:16px;width:16px;"></span>
							Add Social Link
						</button>
						<p class="description" style="margin-top:8px;">Icon class: flaticon name used in the theme (e.g. <code>flaticon-facebook</code>, <code>flaticon-instagram</code>).</p>
					</div>
				</div>

				<?php /* ── Footer ───────────────────────────── */ ?>
				<div class="postbox">
					<div class="postbox-header"><h2 class="hndle">Footer</h2></div>
					<div class="inside">
						<table class="form-table">
							<tr>
								<th><label for="tijus_footer_address_title">Address Line 1</label></th>
								<td><input type="text" id="tijus_footer_address_title" name="tijus_footer_address_title"
									value="<?php echo esc_attr( get_theme_mod( 'tijus_footer_address_title', 'Caribbean Ct' ) ); ?>"></td>
							</tr>
							<tr>
								<th><label for="tijus_footer_address_city">Address Line 2</label></th>
								<td><input type="text" id="tijus_footer_address_city" name="tijus_footer_address_city"
									value="<?php echo esc_attr( get_theme_mod( 'tijus_footer_address_city', 'Haymarket, Virginia (VA).' ) ); ?>"></td>
							</tr>
							<tr>
								<th style="vertical-align:top;padding-top:14px;"><label for="tijus_copyright_text">Copyright</label></th>
								<td>
									<textarea id="tijus_copyright_text" name="tijus_copyright_text"
										rows="3"><?php echo esc_textarea( get_theme_mod( 'tijus_copyright_text', '2021 <span>Edule</span> Made with &#10084; by <a href="#">Codecarnival</a>' ) ); ?></textarea>
									<p class="description">HTML allowed. The &copy; symbol is added automatically before this text.</p>
								</td>
							</tr>
						</table>
					</div>
				</div>

			</div><!-- .tijus-opts-grid -->

			<div style="margin-top:20px;padding-top:16px;border-top:1px solid #dcdcde;">
				<?php submit_button( 'Save Changes', 'primary large', 'submit', false ); ?>
			</div>

		</form>
	</div>

	<script>
	jQuery(function($){
		var mediaUploader;

		$('#tijus-logo-browse').on('click', function(e){
			e.preventDefault();
			if ( mediaUploader ) {
				mediaUploader.open();
				return;
			}
			mediaUploader = wp.media({
				title: 'Select Logo Image',
				button: { text: 'Use this image' },
				multiple: false,
				library: { type: 'image' }
			});
			mediaUploader.on('select', function(){
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				$('#tijus_logo').val( attachment.url );
				// Update preview
				var $preview = $('#tijus-logo-preview-img');
				if ( $preview.is('img') ) {
					$preview.attr('src', attachment.url);
				} else {
					$preview.replaceWith('<img id="tijus-logo-preview-img" src="' + attachment.url + '" alt="Logo" style="max-height:52px;max-width:160px;object-fit:contain;background:#fff;border:1px solid #ddd;padding:4px;border-radius:3px;">');
				}
				// Show remove button if not present
				if ( ! $('#tijus-logo-remove').length ) {
					$('#tijus-logo-browse').after('<button type="button" id="tijus-logo-remove" class="button" style="color:#b32d2e;">Remove</button>');
					bindRemove();
				}
			});
			mediaUploader.open();
		});

		function bindRemove(){
			$('#tijus-logo-remove').on('click', function(){
				$('#tijus_logo').val('');
				var $preview = $('#tijus-logo-preview-img');
				$preview.replaceWith('<span class="tijus-logo-placeholder" id="tijus-logo-preview-img" style="display:block;color:#999;font-style:italic;font-size:13px;">No logo set — default theme logo in use.</span>');
				$(this).remove();
			});
		}
		bindRemove();

		// Social links – add / remove rows
		var socialRowTpl = '<div class="tijus-social-row" style="display:flex;gap:6px;align-items:center;margin-bottom:8px;background:#f9f9f9;padding:8px;border:1px solid #e5e5e5;border-radius:3px;">' +
			'<input type="text" name="tijus_social_label[]" placeholder="Label" style="width:90px;flex-shrink:0;">' +
			'<input type="text" name="tijus_social_icon[]" placeholder="e.g. flaticon-facebook" style="flex:1;min-width:0;">' +
			'<input type="url" name="tijus_social_url[]" placeholder="https://..." style="flex:1;min-width:0;">' +
			'<button type="button" class="button tijus-remove-social" style="color:#b32d2e;flex-shrink:0;">Remove</button>' +
			'</div>';

		$('#tijus-add-social').on('click', function(){
			$('#tijus-social-rows').append(socialRowTpl);
		});

		$('#tijus-social-rows').on('click', '.tijus-remove-social', function(){
			$(this).closest('.tijus-social-row').remove();
		});

		// Color palette picker
		$('.tijus-palette-card').on('click', function(){
			$('.tijus-palette-card').removeClass('active');
			$(this).addClass('active');
		});
	});
	</script>
	<?php
}
