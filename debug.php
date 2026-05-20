<?php
require 'wp-load.php';
$pages = get_pages();
foreach($pages as $p) {
    if (strpos($p->post_content, 'Jason Williams') !== false) {
        echo "Found in Page ID: " . $p->ID . "\n";
    }
}
$posts = get_posts(array('post_type'=>'post', 'numberposts'=>-1));
foreach($posts as $p) {
    if (strpos($p->post_content, 'Jason Williams') !== false) {
        echo "Found in Post ID: " . $p->ID . "\n";
    }
}
$elementor_data = get_post_meta( get_option('page_on_front'), '_elementor_data', true );
if (strpos($elementor_data, 'Jason Williams') !== false) {
    echo "Found in Elementor Data for ID: " . get_option('page_on_front') . "\n";
}
echo "Done\n";
