<?php
/*
Plugin Name: Tijus Popup Manager
Description: Create and manage promotional popups with page targeting and trigger time options.
Author: Gemini CLI
Version: 1.0
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
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-megaphone',
        'supports'            => array( 'title', 'editor', 'thumbnail' ),
        'has_archive'         => false,
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
        'Popup Display Settings',
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

    if ( $delay === '' ) $delay = 0;
    ?>
    <table class="form-table">
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

    update_post_meta( $post_id, '_popup_active', $is_active );
    update_post_meta( $post_id, '_popup_show_always', $show_always );
    update_post_meta( $post_id, '_popup_display_on', $display_on );
    update_post_meta( $post_id, '_popup_specific_page_ids', $specific_ids );
    update_post_meta( $post_id, '_popup_delay', $delay );
}
add_action( 'save_post_tijus_popup', 'tijus_save_popup_meta' );

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

    $content = apply_filters( 'the_content', $popup->post_content );
    $thumb   = get_the_post_thumbnail_url( $popup->ID, 'full' );

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
            max-width: 650px;
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

        .tijus-popup-image {
            width: 100%;
            height: auto;
            border-radius: 0;
            display: block;
            margin: 0;
        }
        .tijus-popup-body {
            padding: 40px;
            font-family: "Montserrat", sans-serif;
            color: #333;
            line-height: 1.7;
        }
        .tijus-popup-body > *:first-child { margin-top: 0; }
        .tijus-popup-body > *:last-child { margin-bottom: 0; }
    </style>

    <div class="tijus-popup-overlay" id="tijusPopup">
        <div class="tijus-popup-content">
            <span class="tijus-popup-close" id="tijusPopupClose">&times;</span>
            <div class="tijus-popup-inner">
                <?php if ( $thumb ) : ?>
                    <div class="tijus-popup-image-wrap">
                        <img src="<?php echo esc_url( $thumb ); ?>" class="tijus-popup-image" />
                    </div>
                <?php endif; ?>
                <div class="tijus-popup-body">
                    <?php echo $content; ?>
                </div>
            </div>
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

            close.onclick = function() {
                popup.style.display = 'none';
                if (!showAlways) {
                    sessionStorage.setItem('tijus_popup_shown_<?php echo $popup->ID; ?>', 'true');
                }
            };

            popup.onclick = function(e) {
                if (e.target === popup) {
                    popup.style.display = 'none';
                    if (!showAlways) {
                        sessionStorage.setItem('tijus_popup_shown_<?php echo $popup->ID; ?>', 'true');
                    }
                }
            };
        })();
    </script>
    <?php
}
add_action( 'wp_footer', 'tijus_inject_popup' );
