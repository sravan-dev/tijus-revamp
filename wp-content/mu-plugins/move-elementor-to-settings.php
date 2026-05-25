<?php
/**
 * Plugin Name: Move Elementor to Settings
 * Description: Moves the top-level Elementor menu to the Settings submenu.
 */

function tijus_move_elementor_to_settings() {
    global $menu;
    
    // Slugs
    $elementor_slug = 'elementor';
    $templates_slug = 'edit.php?post_type=elementor_library';
    $settings_slug  = 'options-general.php';

    // Aggressively find and remove any top-level menu that looks like Elementor
    foreach ( $menu as $key => $item ) {
        if ( isset( $item[0] ) && ( stripos( $item[0], 'Elementor' ) !== false || $item[2] === $elementor_slug ) ) {
            unset( $menu[$key] );
        }
        if ( isset( $item[0] ) && ( stripos( $item[0], 'Templates' ) !== false || $item[2] === $templates_slug ) ) {
             unset( $menu[$key] );
        }
    }
    
    // Add them as submenus under Settings
    add_submenu_page(
        $settings_slug,
        'Elementor Settings',
        'Elementor',
        'manage_options',
        'admin.php?page=elementor'
    );

    add_submenu_page(
        $settings_slug,
        'Elementor Templates',
        'Templates',
        'manage_options',
        $templates_slug
    );
}

// Use a very late priority
add_action('admin_menu', 'tijus_move_elementor_to_settings', 9999);
