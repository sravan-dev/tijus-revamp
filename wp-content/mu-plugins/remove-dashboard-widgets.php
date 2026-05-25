<?php
/**
 * Plugin Name: Remove Dashboard Widgets
 * Description: Removes default WordPress dashboard widgets for a cleaner experience.
 */

function tijus_remove_dashboard_widgets() {
    // Remove "At a Glance"
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    // Remove "Activity"
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    // Remove "Quick Draft"
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    // Remove "WordPress Events and News"
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    // Remove "Welcome" panel
    remove_action('welcome_panel', 'wp_welcome_panel');
    // Remove "Site Health Status"
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

    // Remove Elementor "Overview"
    remove_meta_box('e-dashboard-overview', 'dashboard', 'normal');
    
    // Remove Elementor "Accessibility" (Ally) - often registered in 'column3' or 'side'
    remove_meta_box('e-dashboard-ally', 'dashboard', 'normal');
    remove_meta_box('e-dashboard-ally', 'dashboard', 'side');
    remove_meta_box('e-dashboard-ally', 'dashboard', 'column3');
    remove_meta_box('e-dashboard-ally', 'dashboard', 'column4');
    
    // Remove AI Status and Capabilities
    remove_meta_box('wpai_status', 'dashboard', 'normal');
    remove_meta_box('wpai_status', 'dashboard', 'side');
    remove_meta_box('wpai_status', 'dashboard', 'column3');
    
    remove_meta_box('wpai_capabilities', 'dashboard', 'normal');
    remove_meta_box('wpai_capabilities', 'dashboard', 'side');
    remove_meta_box('wpai_capabilities', 'dashboard', 'column3');
}

add_action('wp_dashboard_setup', 'tijus_remove_dashboard_widgets', 999);
