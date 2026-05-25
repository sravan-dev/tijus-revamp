<?php
/**
 * Plugin Name: Move Elementor to Settings
 * Description: Moves the top-level Elementor menu to the Settings submenu.
 */

function tijus_move_elementor_to_settings() {
    // We use a high priority to ensure this runs after Elementor has registered its menus
    
    // Slugs
    $elementor_slug = 'elementor';
    $templates_slug = 'edit.php?post_type=elementor_library';
    $settings_slug  = 'options-general.php';

    // 1. Move Elementor main menu
    // We remove the top level page
    remove_menu_page($elementor_slug);
    
    // Add it as a submenu under Settings
    add_submenu_page(
        $settings_slug,
        'Elementor',
        'Elementor',
        'manage_options',
        $elementor_slug
    );

    // 2. Move Templates menu
    remove_menu_page($templates_slug);
    add_submenu_page(
        $settings_slug,
        'Templates',
        'Templates',
        'manage_options',
        $templates_slug
    );
}

// Priority 999 to run after plugin registrations
add_action('admin_menu', 'tijus_move_elementor_to_settings', 999);
