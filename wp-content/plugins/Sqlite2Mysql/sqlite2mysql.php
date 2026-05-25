<?php
/**
 * Plugin Name: Sqlite2Mysql
 * Plugin URI:  https://github.com/yourname/sqlite2mysql
 * Description: Migrate SQLite databases to MySQL directly from your WordPress dashboard.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://yourwebsite.com
 * License:     GPL-2.0+
 * Text Domain: sqlite2mysql
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'S2M_VERSION',    '1.0.0' );
define( 'S2M_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'S2M_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'S2M_UPLOAD_DIR', WP_CONTENT_DIR . '/sqlite2mysql-uploads/' );

require_once S2M_PLUGIN_DIR . 'includes/class-s2m-admin.php';
require_once S2M_PLUGIN_DIR . 'includes/class-s2m-migrator.php';
require_once S2M_PLUGIN_DIR . 'includes/class-s2m-ajax.php';

function s2m_init() {
    new S2M_Admin();
    new S2M_Ajax();
}
add_action( 'plugins_loaded', 's2m_init' );

register_activation_hook( __FILE__, 's2m_activate' );
function s2m_activate() {
    if ( ! file_exists( S2M_UPLOAD_DIR ) ) {
        wp_mkdir_p( S2M_UPLOAD_DIR );
        // Protect upload dir
        file_put_contents( S2M_UPLOAD_DIR . '.htaccess', 'deny from all' );
        file_put_contents( S2M_UPLOAD_DIR . 'index.php', '<?php // silence' );
    }
}

register_deactivation_hook( __FILE__, 's2m_deactivate' );
function s2m_deactivate() {
    // Nothing needed on deactivate
}
