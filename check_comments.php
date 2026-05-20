<?php
require_once 'wp-load.php';
global $wpdb;
$status = $wpdb->get_var("SELECT comment_status FROM {$wpdb->posts} WHERE post_type = 'course' LIMIT 1");
echo "Comment status for courses: " . $status . "\n";
