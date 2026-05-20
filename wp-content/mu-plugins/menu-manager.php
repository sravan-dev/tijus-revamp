<?php
/**
 * Plugin Name: Menu Manager
 * Description: Reorders the admin sidebar menu and allows enabling/disabling specific menu items.
 * Author: Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Settings Page Registration
add_action( 'admin_menu', 'tijus_menu_manager_settings_page' );
function tijus_menu_manager_settings_page() {
    add_options_page(
        'Menu Manager',
        'Menu Manager',
        'manage_options',
        'tijus-menu-manager',
        'tijus_menu_manager_render_page'
    );
}

function tijus_menu_manager_render_page() {
    global $menu;
    
    // Handle form submission
    if ( isset( $_POST['tijus_menu_manager_submit'] ) && check_admin_referer( 'tijus_menu_manager_nonce', 'tijus_menu_manager_nonce_field' ) ) {
        $hidden_menus = isset( $_POST['hidden_menus'] ) ? array_map( 'sanitize_text_field', $_POST['hidden_menus'] ) : [];
        $enable_reorder = isset( $_POST['enable_reorder'] ) ? 1 : 0;
        
        update_option( 'tijus_hidden_menus', $hidden_menus );
        update_option( 'tijus_enable_menu_reorder', $enable_reorder );
        
        echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
    }

    $hidden_menus = get_option( 'tijus_hidden_menus', [] );
    $enable_reorder = get_option( 'tijus_enable_menu_reorder', 1 ); // Enabled by default
    ?>
    <div class="wrap">
        <h1>Menu Manager</h1>
        <form method="post" action="">
            <?php wp_nonce_field( 'tijus_menu_manager_nonce', 'tijus_menu_manager_nonce_field' ); ?>
            
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Menu Reordering</th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_reorder" value="1" <?php checked( $enable_reorder, 1 ); ?>>
                                Enable custom top-level reordering (Dashboard, Courses, Course Reviews, Customers)
                            </label>
                            <p class="description">If enabled, these 4 items will be prioritized at the top of the sidebar.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h2>Disable Menu Items</h2>
            <p>Select the menu options you want to <strong>hide</strong> from the sidebar.</p>
            
            <table class="wp-list-table widefat striped" style="max-width: 600px; margin-top: 15px;">
                <thead>
                    <tr>
                        <th style="padding-left: 20px;">Menu Item</th>
                        <th>Disable (Hide)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Collect all main menu items
                    foreach ( $menu as $item ) {
                        // Skip separators or empty items
                        if ( empty( $item[0] ) || strpos( $item[2], 'separator' ) !== false ) {
                            continue;
                        }
                        
                        // Extract title, ignoring notification spans like "<span class='update-plugins...'>"
                        $menu_title_parts = explode( '<span', $item[0] );
                        $menu_title = trim( wp_strip_all_tags( $menu_title_parts[0] ) );

                        $menu_slug = $item[2];
                        
                        // Don't allow hiding the Menu Manager itself to prevent lockouts
                        if ( $menu_slug === 'tijus-menu-manager' ) continue;

                        $checked = in_array( $menu_slug, $hidden_menus ) ? 'checked' : '';
                        ?>
                        <tr>
                            <td style="padding-left: 20px;"><strong><?php echo esc_html( $menu_title ); ?></strong></td>
                            <td>
                                <label>
                                    <input type="checkbox" name="hidden_menus[]" value="<?php echo esc_attr( $menu_slug ); ?>" <?php echo $checked; ?>>
                                    Disable
                                </label>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            
            <?php submit_button( 'Save Changes', 'primary', 'tijus_menu_manager_submit' ); ?>
        </form>
    </div>
    <?php
}

// 2. Hide Disabled Menus
add_action( 'admin_menu', 'tijus_remove_hidden_menus', 999 );
function tijus_remove_hidden_menus() {
    $hidden_menus = get_option( 'tijus_hidden_menus', [] );
    foreach ( $hidden_menus as $slug ) {
        // Prevent hiding Settings completely so user isn't locked out of Menu Manager
        if ( $slug === 'options-general.php' ) continue; 
        
        remove_menu_page( $slug );
    }
}

// 3. Custom Reordering
function tijus_custom_menu_order( $menu_ord ) {
    if ( ! $menu_ord ) return true;
    
    // Only apply if enabled
    $enable_reorder = get_option( 'tijus_enable_menu_reorder', 1 );
    if ( ! $enable_reorder ) return $menu_ord;

    $custom_order = array(
        'index.php',
        'edit.php?post_type=course',
        'edit-comments.php?post_type=course',
        'tijus-customers',
    );

    // Explicitly append all other menus so WP doesn't lose their relative sorting
    if ( is_array( $menu_ord ) ) {
        foreach ( $menu_ord as $item ) {
            if ( ! in_array( $item, $custom_order ) ) {
                $custom_order[] = $item;
            }
        }
    }

    return $custom_order;
}

add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'tijus_custom_menu_order', 999 );
