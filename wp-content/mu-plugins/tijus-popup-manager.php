<?php
/*
Plugin Name: Tijus Popup Manager
Description: Create and manage promotional popups with page targeting and trigger time options.
Author: Gemini CLI
Version: 1.3
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the 'Popup' Custom Post Type.
 */
function tijus_register_popup_cpt() {
    $labels = array(
        'name'               => 'Popups',
        'singular_name'      => 'Popup',
        'menu_name'          => 'Popups',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Popup',
        'edit_item'          => 'Edit Popup',
        'new_item'           => 'New Popup',
        'view_item'          => 'View Popup',
        'search_items'       => 'Search Popups',
        'not_found'          => 'No popups found',
        'not_found_in_trash' => 'No popups found in Trash',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 9,
        'menu_icon'           => 'dashicons-megaphone',
        'supports'            => array( 'title', 'editor' ),
        'has_archive'         => false,
        'capability_type'     => 'post',
        'hierarchical'        => false,
    );

    register_post_type( 'tijus_popup', $args );
}
add_action( 'init', 'tijus_register_popup_cpt' );

/**
 * Add Meta Boxes for Popup Settings.
 */
function tijus_add_popup_meta_boxes() {
    add_meta_box(
        'tijus_popup_settings',
        'Popup Content & Display Settings',
        'tijus_render_popup_settings_meta_box',
        'tijus_popup',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tijus_add_popup_meta_boxes' );

function tijus_render_popup_settings_meta_box( $post ) {
    wp_nonce_field( 'tijus_save_popup_settings', 'tijus_popup_nonce' );

    $is_active    = get_post_meta( $post->ID, '_popup_active', true );
    $show_always  = get_post_meta( $post->ID, '_popup_show_always', true );
    $display_on   = get_post_meta( $post->ID, '_popup_display_on', true );
    $specific_ids = get_post_meta( $post->ID, '_popup_specific_page_ids', true );
    $delay        = get_post_meta( $post->ID, '_popup_delay', true );
    $popup_img    = get_post_meta( $post->ID, '_popup_image_url', true );
    $cta_enabled  = get_post_meta( $post->ID, '_popup_cta_enabled', true );
    $cta_text     = get_post_meta( $post->ID, '_popup_cta_text', true );
    $cta_url      = get_post_meta( $post->ID, '_popup_cta_url', true );

    if ( $delay === '' ) $delay = 0;
    if ( $cta_text === '' ) $cta_text = 'Learn More';

    // Ensure media uploader scripts are loaded
    wp_enqueue_media();
    ?>
    <table class="form-table">
        <tr>
            <th><label>Popup Image</label></th>
            <td>
                <div id="popup_image_preview" style="margin-bottom: 10px;">
                    <?php if ( $popup_img ) : ?>
                        <img src="<?php echo esc_url( $popup_img ); ?>" style="max-width: 300px; height: auto; border: 1px solid #ccc; border-radius: 4px;" />
                    <?php endif; ?>
                </div>
                <input type="hidden" name="popup_image_url" id="popup_image_url" value="<?php echo esc_attr( $popup_img ); ?>" />
                <button type="button" class="button" id="upload_popup_image_btn">Select/Upload Image</button>
                <button type="button" class="button" id="remove_popup_image_btn" <?php echo $popup_img ? '' : 'style="display:none;"'; ?>>Remove Image</button>
                <p class="description">This image will appear on the left column of the popup.</p>
            </td>
        </tr>
        <tr>
            <th><label>CTA Button</label></th>
            <td>
                <label>
                    <input type="checkbox" name="popup_cta_enabled" id="popup_cta_enabled" value="1" <?php checked( $cta_enabled, '1' ); ?> />
                    Enable CTA button in popup footer
                </label>
            </td>
        </tr>
        <tr class="cta-settings-row" style="<?php echo ( $cta_enabled === '1' ) ? '' : 'display:none;'; ?>">
            <th><label for="popup_cta_text">CTA Button Text</label></th>
            <td>
                <input type="text" name="popup_cta_text" id="popup_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" class="regular-text" placeholder="e.g. Enroll Now" />
                <p class="description">Text for the call-to-action button in the popup footer.</p>
            </td>
        </tr>
        <tr class="cta-settings-row" style="<?php echo ( $cta_enabled === '1' ) ? '' : 'display:none;'; ?>">
            <th><label for="popup_cta_url">CTA Button URL</label></th>
            <td>
                <input type="url" name="popup_cta_url" id="popup_cta_url" value="<?php echo esc_attr( $cta_url ); ?>" class="regular-text" placeholder="https://example.com/page" />
                <p class="description">Link for the CTA button. Leave empty to scroll to the contact form in the popup.</p>
            </td>
        </tr>
        <tr>
            <th><label>Active Status</label></th>
            <td>
                <label style="margin-right: 20px;">
                    <input type="checkbox" name="popup_active" value="1" <?php checked( $is_active, '1' ); ?> />
                    Enable this popup
                </label>
                <label>
                    <input type="checkbox" name="popup_show_always" value="1" <?php checked( $show_always, '1' ); ?> />
                    Show every time on reload
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="popup_display_on">Display On</label></th>
            <td>
                <select name="popup_display_on" id="popup_display_on" style="width: 250px;">
                    <option value="all" <?php selected( $display_on, 'all' ); ?>>All Pages</option>
                    <option value="home" <?php selected( $display_on, 'home' ); ?>>Home Page Only</option>
                    <option value="specific" <?php selected( $display_on, 'specific' ); ?>>Specific Pages/Posts</option>
                </select>
            </td>
        </tr>
        <tr id="specific_pages_row" style="<?php echo ( $display_on === 'specific' ) ? '' : 'display:none;'; ?>">
            <th><label for="popup_specific_page_ids">Page/Post IDs</label></th>
            <td>
                <input type="text" name="popup_specific_page_ids" id="popup_specific_page_ids" value="<?php echo esc_attr( $specific_ids ); ?>" class="regular-text" placeholder="e.g. 12, 45, 89" />
                <p class="description">Comma separated list of Page or Post IDs.</p>
            </td>
        </tr>
        <tr>
            <th><label for="popup_delay">Trigger Delay (Seconds)</label></th>
            <td>
                <input type="number" name="popup_delay" id="popup_delay" value="<?php echo esc_attr( $delay ); ?>" min="0" class="small-text" />
                <p class="description">How many seconds to wait before showing the popup.</p>
            </td>
        </tr>
    </table>
    <script>
        document.getElementById('popup_display_on').addEventListener('change', function() {
            document.getElementById('specific_pages_row').style.display = (this.value === 'specific') ? '' : 'none';
        });

        document.getElementById('popup_cta_enabled').addEventListener('change', function() {
            var rows = document.querySelectorAll('.cta-settings-row');
            for (var i = 0; i < rows.length; i++) {
                rows[i].style.display = this.checked ? '' : 'none';
            }
        });

        // Media Uploader Script
        jQuery(document).ready(function($){
            var mediaUploader;
            $('#upload_popup_image_btn').click(function(e) {
                e.preventDefault();
                if (mediaUploader) { mediaUploader.open(); return; }
                mediaUploader = wp.media({
                    title: 'Select Popup Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#popup_image_url').val(attachment.url);
                    $('#popup_image_preview').html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto; border: 1px solid #ccc; border-radius: 4px;" />');
                    $('#remove_popup_image_btn').show();
                });
                mediaUploader.open();
            });
            $('#remove_popup_image_btn').click(function(e) {
                e.preventDefault();
                $('#popup_image_url').val('');
                $('#popup_image_preview').empty();
                $(this).hide();
            });
        });
    </script>
    <?php
}

/**
 * Save Popup Meta Data.
 */
function tijus_save_popup_meta( $post_id ) {
    if ( ! isset( $_POST['tijus_popup_nonce'] ) || ! wp_verify_nonce( $_POST['tijus_popup_nonce'], 'tijus_save_popup_settings' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    $is_active    = isset( $_POST['popup_active'] ) ? '1' : '0';
    $show_always  = isset( $_POST['popup_show_always'] ) ? '1' : '0';
    $display_on   = sanitize_text_field( $_POST['popup_display_on'] );
    $specific_ids = sanitize_text_field( $_POST['popup_specific_page_ids'] );
    $delay        = absint( $_POST['popup_delay'] );
    $image_url    = sanitize_text_field( $_POST['popup_image_url'] );
    $cta_enabled  = isset( $_POST['popup_cta_enabled'] ) ? '1' : '0';
    $cta_text     = sanitize_text_field( $_POST['popup_cta_text'] );
    $cta_url      = esc_url_raw( $_POST['popup_cta_url'] );

    update_post_meta( $post_id, '_popup_active', $is_active );
    update_post_meta( $post_id, '_popup_show_always', $show_always );
    update_post_meta( $post_id, '_popup_display_on', $display_on );
    update_post_meta( $post_id, '_popup_specific_page_ids', $specific_ids );
    update_post_meta( $post_id, '_popup_delay', $delay );
    update_post_meta( $post_id, '_popup_image_url', $image_url );
    update_post_meta( $post_id, '_popup_cta_enabled', $cta_enabled );
    update_post_meta( $post_id, '_popup_cta_text', $cta_text );
    update_post_meta( $post_id, '_popup_cta_url', $cta_url );
}
add_action( 'save_post_tijus_popup', 'tijus_save_popup_meta' );

/**
 * Handle Popup Contact Form AJAX Submission.
 */
function tijus_ajax_popup_contact() {
    if ( ! isset( $_POST['popup_contact_nonce'] ) || ! wp_verify_nonce( $_POST['popup_contact_nonce'], 'tijus_popup_contact_form' ) ) {
        wp_send_json_error( 'Invalid security token.' );
    }

    $name    = sanitize_text_field( wp_unslash( $_POST['popup_name'] ?? '' ) );
    $email   = sanitize_email( wp_unslash( $_POST['popup_email'] ?? '' ) );
    $phone   = sanitize_text_field( wp_unslash( $_POST['popup_phone'] ?? '' ) );
    $message = sanitize_textarea_field( wp_unslash( $_POST['popup_message'] ?? '' ) );

    if ( empty( $name ) || empty( $email ) || empty( $phone ) ) {
        wp_send_json_error( 'Please fill in all required fields.' );
    }

    $post_id = wp_insert_post([
        'post_type'   => 'tijus_contact',
        'post_title'  => 'Popup Enquiry - ' . $name,
        'post_status' => 'publish',
        'post_author' => 1,
    ]);

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( 'Failed to submit. Please try again.' );
    }

    update_post_meta( $post_id, 'contact_name', $name );
    update_post_meta( $post_id, 'contact_email', $email );
    update_post_meta( $post_id, 'contact_phone', $phone );
    update_post_meta( $post_id, 'contact_message', $message );

    wp_send_json_success( 'Thank you! We will get back to you shortly.' );
}
add_action( 'wp_ajax_tijus_popup_contact', 'tijus_ajax_popup_contact' );
add_action( 'wp_ajax_nopriv_tijus_popup_contact', 'tijus_ajax_popup_contact' );

/**
 * Frontend: Inject Popup.
 */
function tijus_inject_popup() {
    if ( is_admin() ) return;

    $args = array(
        'post_type'      => 'tijus_popup',
        'posts_per_page' => 1,
        'meta_query'     => array(
            array(
                'key'   => '_popup_active',
                'value' => '1',
            ),
        ),
    );

    $popups = get_posts( $args );

    if ( empty( $popups ) ) return;

    $popup = $popups[0];
    $show_always  = get_post_meta( $popup->ID, '_popup_show_always', true );
    $display_on   = get_post_meta( $popup->ID, '_popup_display_on', true );
    $specific_ids = get_post_meta( $popup->ID, '_popup_specific_page_ids', true );
    $delay        = get_post_meta( $popup->ID, '_popup_delay', true );
    $popup_img    = get_post_meta( $popup->ID, '_popup_image_url', true );
    $cta_enabled  = get_post_meta( $popup->ID, '_popup_cta_enabled', true );
    $cta_text     = get_post_meta( $popup->ID, '_popup_cta_text', true );
    $cta_url      = get_post_meta( $popup->ID, '_popup_cta_url', true );

    if ( empty( $cta_text ) ) $cta_text = 'Learn More';

    $should_show = false;

    if ( $display_on === 'all' ) {
        $should_show = true;
    } elseif ( $display_on === 'home' && is_front_page() ) {
        $should_show = true;
    } elseif ( $display_on === 'specific' ) {
        $ids = array_map( 'trim', explode( ',', $specific_ids ) );
        if ( in_array( get_the_ID(), $ids ) ) {
            $should_show = true;
        }
    }

    if ( ! $should_show ) return;

    // Set up global $post so content filters use popup data, not the current page
    global $post;
    $original_post = $post;
    $post = $popup;
    setup_postdata( $post );
    $content = apply_filters( 'the_content', $popup->post_content );
    $post = $original_post;
    wp_reset_postdata();

    $nonce = wp_create_nonce( 'tijus_popup_contact_form' );

    ?>
    <style>
        .tijus-popup-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.75);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999999;
            backdrop-filter: blur(8px);
            padding: 20px;
            box-sizing: border-box;
        }
        .tijus-popup-content {
            background: #fff;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            border-radius: 24px;
            position: relative;
            box-shadow: 0 30px 60px -12px rgba(0,0,0,0.3);
            animation: tijusPopupIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        @keyframes tijusPopupIn {
            from { opacity: 0; transform: scale(0.95) translateY(30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .tijus-popup-close {
            position: absolute;
            top: 15px; right: 20px;
            font-size: 32px;
            cursor: pointer;
            color: #000;
            transition: all 0.2s;
            line-height: 1;
            z-index: 10;
            background: #fff;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .tijus-popup-close:hover { transform: rotate(90deg); background: #f0f0f0; }

        .tijus-popup-inner {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #FFC988 #f0f0f0;
            display: flex;
            flex-direction: column;
        }
        .tijus-popup-inner::-webkit-scrollbar { width: 6px; }
        .tijus-popup-inner::-webkit-scrollbar-track { background: #f0f0f0; }
        .tijus-popup-inner::-webkit-scrollbar-thumb { background: #FFC988; border-radius: 10px; }

        .tijus-popup-columns {
            display: flex;
            flex-direction: row;
            min-height: 0;
        }
        .tijus-popup-left {
            flex: 1;
            padding: 40px;
            font-family: "Montserrat", sans-serif;
            color: #333;
            line-height: 1.7;
            display: flex;
            flex-direction: column;
        }
        .tijus-popup-left h2.popup-title {
            margin-top: 0;
            font-weight: 800;
            font-size: 28px;
            color: #000;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        .tijus-popup-image {
            width: 100%;
            height: auto;
            border-radius: 12px;
            margin-top: 15px;
            display: block;
        }
        .tijus-popup-right {
            flex: 1;
            padding: 40px;
            background: #f8f9fa;
            font-family: "Montserrat", sans-serif;
            display: flex;
            flex-direction: column;
        }
        .tijus-popup-right h3 {
            margin-top: 0;
            font-weight: 700;
            font-size: 20px;
            color: #000;
            margin-bottom: 20px;
        }
        .tijus-popup-form-group {
            margin-bottom: 14px;
        }
        .tijus-popup-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }
        .tijus-popup-form-group input,
        .tijus-popup-form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            font-family: "Montserrat", sans-serif;
            transition: border-color 0.2s;
            box-sizing: border-box;
            background: #fff;
        }
        .tijus-popup-form-group input:focus,
        .tijus-popup-form-group textarea:focus {
            outline: none;
            border-color: #FFC988;
            box-shadow: 0 0 0 3px rgba(255,201,136,0.2);
        }
        .tijus-popup-form-group textarea {
            resize: vertical;
            min-height: 70px;
        }
        .tijus-popup-form-submit {
            width: 100%;
            padding: 12px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: "Montserrat", sans-serif;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 5px;
        }
        .tijus-popup-form-submit:hover { background: #333; }
        .tijus-popup-form-submit:disabled { background: #999; cursor: not-allowed; }
        .tijus-popup-form-msg {
            margin-top: 10px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            display: none;
        }
        .tijus-popup-form-msg.success { background: #d4edda; color: #155724; display: block; }
        .tijus-popup-form-msg.error { background: #f8d7da; color: #721c24; display: block; }

        /* Footer CTA */
        .tijus-popup-footer {
            padding: 16px 40px;
            text-align: center;
            border-top: 1px solid #eee;
            background: #fff;
        }
        .tijus-popup-cta {
            display: inline-block;
            padding: 12px 40px;
            background: #FFC988;
            color: #000;
            text-decoration: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            font-family: "Montserrat", sans-serif;
            transition: all 0.2s;
        }
        .tijus-popup-cta:hover { background: #ffb85c; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255,201,136,0.4); }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .tijus-popup-content { max-width: 95%; }
            .tijus-popup-columns { flex-direction: column; }
            .tijus-popup-left, .tijus-popup-right { padding: 25px; }
            .tijus-popup-left h2.popup-title { font-size: 22px; }
            .tijus-popup-footer { padding: 14px 25px; }
        }
    </style>

    <div class="tijus-popup-overlay" id="tijusPopup">
        <div class="tijus-popup-content">
            <span class="tijus-popup-close" id="tijusPopupClose">&times;</span>
            <div class="tijus-popup-inner">
                <div class="tijus-popup-columns">
                    <div class="tijus-popup-left">
                        <h2 class="popup-title"><?php echo esc_html( $popup->post_title ); ?></h2>
                        <div class="popup-text-content">
                            <?php echo $content; ?>
                        </div>
                        <?php if ( $popup_img ) : ?>
                            <img src="<?php echo esc_url( $popup_img ); ?>" class="tijus-popup-image" />
                        <?php endif; ?>
                    </div>
                    <div class="tijus-popup-right">
                        <h3>Get In Touch</h3>
                        <form id="tijusPopupContactForm" autocomplete="off">
                            <input type="hidden" name="action" value="tijus_popup_contact" />
                            <input type="hidden" name="popup_contact_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
                            <div class="tijus-popup-form-group">
                                <label for="popup_name">Name *</label>
                                <input type="text" id="popup_name" name="popup_name" required />
                            </div>
                            <div class="tijus-popup-form-group">
                                <label for="popup_email">Email *</label>
                                <input type="email" id="popup_email" name="popup_email" required />
                            </div>
                            <div class="tijus-popup-form-group">
                                <label for="popup_phone">Phone *</label>
                                <input type="tel" id="popup_phone" name="popup_phone" required />
                            </div>
                            <div class="tijus-popup-form-group">
                                <label for="popup_message">Message</label>
                                <textarea id="popup_message" name="popup_message" rows="3"></textarea>
                            </div>
                            <button type="submit" class="tijus-popup-form-submit">Send Enquiry</button>
                            <div class="tijus-popup-form-msg" id="tijusPopupFormMsg"></div>
                        </form>
                    </div>
                </div>
            </div>
            <?php if ( $cta_enabled === '1' ) : ?>
            <div class="tijus-popup-footer">
                <?php if ( ! empty( $cta_url ) ) : ?>
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="tijus-popup-cta" target="_blank"><?php echo esc_html( $cta_text ); ?></a>
                <?php else : ?>
                    <a href="#" class="tijus-popup-cta" id="tijusPopupCtaBtn"><?php echo esc_html( $cta_text ); ?></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        (function() {
            var delay = <?php echo intval( $delay ) * 1000; ?>;
            var showAlways = <?php echo ($show_always === '1') ? 'true' : 'false'; ?>;
            var popup = document.getElementById('tijusPopup');
            var close = document.getElementById('tijusPopupClose');

            setTimeout(function() {
                if ( showAlways || ! sessionStorage.getItem('tijus_popup_shown_<?php echo $popup->ID; ?>') ) {
                    popup.style.display = 'flex';
                }
            }, delay);

            function closePopup() {
                popup.style.display = 'none';
                if (!showAlways) {
                    sessionStorage.setItem('tijus_popup_shown_<?php echo $popup->ID; ?>', 'true');
                }
            }

            close.onclick = closePopup;

            popup.onclick = function(e) {
                if (e.target === popup) closePopup();
            };

            // CTA button without URL scrolls to the contact form
            var ctaBtn = document.getElementById('tijusPopupCtaBtn');
            if (ctaBtn) {
                ctaBtn.onclick = function(e) {
                    e.preventDefault();
                    var formEl = document.getElementById('tijusPopupContactForm');
                    if (formEl) {
                        formEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        var nameInput = document.getElementById('popup_name');
                        if (nameInput) nameInput.focus();
                    }
                };
            }

            // Contact Form AJAX
            var form = document.getElementById('tijusPopupContactForm');
            var msgDiv = document.getElementById('tijusPopupFormMsg');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var btn = form.querySelector('.tijus-popup-form-submit');
                btn.disabled = true;
                btn.textContent = 'Sending...';
                msgDiv.className = 'tijus-popup-form-msg';
                msgDiv.style.display = 'none';

                var formData = new FormData(form);

                fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    btn.disabled = false;
                    btn.textContent = 'Send Enquiry';
                    if (res.success) {
                        msgDiv.className = 'tijus-popup-form-msg success';
                        msgDiv.textContent = res.data;
                        msgDiv.style.display = 'block';
                        form.reset();
                    } else {
                        msgDiv.className = 'tijus-popup-form-msg error';
                        msgDiv.textContent = res.data || 'Something went wrong.';
                        msgDiv.style.display = 'block';
                    }
                })
                .catch(function() {
                    btn.disabled = false;
                    btn.textContent = 'Send Enquiry';
                    msgDiv.className = 'tijus-popup-form-msg error';
                    msgDiv.textContent = 'Network error. Please try again.';
                    msgDiv.style.display = 'block';
                });
            });
        })();
    </script>
    <?php
}
add_action( 'wp_footer', 'tijus_inject_popup' );
