<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class S2M_Admin {

    public function __construct() {
        add_action( 'admin_menu',            array( $this, 'register_menus' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function register_menus() {
        // Top-level menu
        add_menu_page(
            __( 'Sqlite2Mysql', 'sqlite2mysql' ),
            __( 'Sqlite2Mysql', 'sqlite2mysql' ),
            'manage_options',
            'sqlite2mysql',
            array( $this, 'page_dashboard' ),
            'dashicons-database-import',
            80
        );

        // Submenu: Dashboard
        add_submenu_page(
            'sqlite2mysql',
            __( 'Dashboard', 'sqlite2mysql' ),
            __( 'Dashboard', 'sqlite2mysql' ),
            'manage_options',
            'sqlite2mysql',
            array( $this, 'page_dashboard' )
        );

        // Submenu: New Migration
        add_submenu_page(
            'sqlite2mysql',
            __( 'New Migration', 'sqlite2mysql' ),
            __( 'New Migration', 'sqlite2mysql' ),
            'manage_options',
            'sqlite2mysql-migrate',
            array( $this, 'page_migrate' )
        );

        // Submenu: History
        add_submenu_page(
            'sqlite2mysql',
            __( 'Migration History', 'sqlite2mysql' ),
            __( 'History', 'sqlite2mysql' ),
            'manage_options',
            'sqlite2mysql-history',
            array( $this, 'page_history' )
        );

        // Submenu: Settings
        add_submenu_page(
            'sqlite2mysql',
            __( 'Settings', 'sqlite2mysql' ),
            __( 'Settings', 'sqlite2mysql' ),
            'manage_options',
            'sqlite2mysql-settings',
            array( $this, 'page_settings' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'sqlite2mysql' ) === false ) {
            return;
        }
        wp_enqueue_style(
            'sqlite2mysql-admin',
            S2M_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            S2M_VERSION
        );
        wp_enqueue_script(
            'sqlite2mysql-admin',
            S2M_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            S2M_VERSION,
            true
        );
        wp_localize_script( 'sqlite2mysql-admin', 'S2M', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 's2m_nonce' ),
            'strings'  => array(
                'uploading'  => __( 'Uploading file...', 'sqlite2mysql' ),
                'analyzing'  => __( 'Analyzing SQLite database...', 'sqlite2mysql' ),
                'migrating'  => __( 'Migrating data...', 'sqlite2mysql' ),
                'done'       => __( 'Migration complete!', 'sqlite2mysql' ),
                'error'      => __( 'An error occurred.', 'sqlite2mysql' ),
                'confirm'    => __( 'This will write data to your MySQL database. Continue?', 'sqlite2mysql' ),
            ),
        ) );
    }

    public function page_dashboard() {
        $log = get_option( 's2m_migration_log', array() );
        include S2M_PLUGIN_DIR . 'templates/page-dashboard.php';
    }

    public function page_migrate() {
        include S2M_PLUGIN_DIR . 'templates/page-migrate.php';
    }

    public function page_history() {
        $log = get_option( 's2m_migration_log', array() );
        include S2M_PLUGIN_DIR . 'templates/page-history.php';
    }

    public function page_settings() {
        if ( isset( $_POST['s2m_save_settings'] ) && check_admin_referer( 's2m_settings' ) ) {
            update_option( 's2m_batch_size',     absint( $_POST['s2m_batch_size'] ?? 500 ) );
            update_option( 's2m_drop_existing',  isset( $_POST['s2m_drop_existing'] ) ? 1 : 0 );
            update_option( 's2m_table_prefix',   sanitize_text_field( $_POST['s2m_table_prefix'] ?? '' ) );
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved.', 'sqlite2mysql' ) . '</p></div>';
        }
        include S2M_PLUGIN_DIR . 'templates/page-settings.php';
    }
}
