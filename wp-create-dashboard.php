<?php
require_once('wp-load.php');
$page_id = wp_insert_post([
    'post_title' => 'Dashboard',
    'post_name' => 'dashboard',
    'post_status' => 'publish',
    'post_type' => 'page',
]);
if(!is_wp_error($page_id)){
    update_post_meta($page_id, '_wp_page_template', 'template-dashboard.php');
    echo "Dashboard Page Created: " . $page_id;
} else {
    echo "Error";
}
