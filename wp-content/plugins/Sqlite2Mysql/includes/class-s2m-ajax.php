<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class S2M_Ajax {

    public function __construct() {
        add_action( 'wp_ajax_s2m_upload',   array( $this, 'handle_upload' ) );
        add_action( 'wp_ajax_s2m_detect',   array( $this, 'handle_detect_current' ) );
        add_action( 'wp_ajax_s2m_analyze',  array( $this, 'handle_analyze' ) );
        add_action( 'wp_ajax_s2m_migrate',  array( $this, 'handle_migrate' ) );
        add_action( 'wp_ajax_s2m_clear_log', array( $this, 'handle_clear_log' ) );
    }

    private function verify() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ), 403 );
        }
        check_ajax_referer( 's2m_nonce', 'nonce' );
    }

    // ── Upload SQLite file ──────────────────────────────────────
    public function handle_upload() {
        $this->verify();

        if ( empty( $_FILES['sqlite_file'] ) ) {
            wp_send_json_error( array( 'message' => 'No file received.' ) );
        }

        $file = $_FILES['sqlite_file'];

        if ( $file['error'] !== UPLOAD_ERR_OK ) {
            wp_send_json_error( array( 'message' => 'Upload error code: ' . $file['error'] ) );
        }

        // Validate SQLite magic bytes
        $handle = fopen( $file['tmp_name'], 'rb' );
        $magic  = fread( $handle, 16 );
        fclose( $handle );

        if ( substr( $magic, 0, 15 ) !== 'SQLite format 3' ) {
            wp_send_json_error( array( 'message' => 'File is not a valid SQLite database.' ) );
        }

        // Sanitize filename
        $filename = sanitize_file_name( $file['name'] );
        $ext      = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
        if ( ! in_array( $ext, array( 'sqlite', 'sqlite3', 'db', 'sdb' ), true ) ) {
            wp_send_json_error( array( 'message' => 'Invalid file extension. Allowed: .sqlite .sqlite3 .db .sdb' ) );
        }

        $dest = S2M_UPLOAD_DIR . uniqid( 's2m_', true ) . '.' . $ext;

        if ( ! move_uploaded_file( $file['tmp_name'], $dest ) ) {
            wp_send_json_error( array( 'message' => 'Failed to save uploaded file.' ) );
        }

        // Store path in transient (30 min TTL per user)
        set_transient( 's2m_file_' . get_current_user_id(), $dest, 30 * MINUTE_IN_SECONDS );

        wp_send_json_success( array(
            'message'  => 'File uploaded: ' . esc_html( $filename ),
            'filename' => esc_html( $filename ),
            'size'     => size_format( $file['size'] ),
        ) );
    }

    // ── Detect current SQLite database ──────────────────────────
    public function handle_detect_current() {
        $this->verify();

        $possible_paths = array(
            ABSPATH . 'wp-content/database/.ht.sqlite',
            ABSPATH . 'wp-content/db.sqlite',
        );

        $detected = '';
        foreach ( $possible_paths as $p ) {
            if ( file_exists( $p ) && is_readable( $p ) ) {
                $detected = $p;
                break;
            }
        }

        if ( ! $detected ) {
            wp_send_json_error( array( 'message' => 'Could not auto-detect active SQLite database file.' ) );
        }

        $ext  = pathinfo( $detected, PATHINFO_EXTENSION ) ?: 'sqlite';
        $dest = S2M_UPLOAD_DIR . 'detected_' . uniqid() . '.' . $ext;

        if ( ! copy( $detected, $dest ) ) {
            wp_send_json_error( array( 'message' => 'Found database but failed to copy for migration.' ) );
        }

        set_transient( 's2m_file_' . get_current_user_id(), $dest, 30 * MINUTE_IN_SECONDS );

        wp_send_json_success( array(
            'message'  => 'Successfully detected: ' . basename( $detected ),
            'filename' => basename( $detected ),
            'size'     => size_format( filesize( $dest ) ),
        ) );
    }

    // ── Analyze SQLite tables ───────────────────────────────────
    public function handle_analyze() {
        $this->verify();

        $path = get_transient( 's2m_file_' . get_current_user_id() );
        if ( ! $path || ! file_exists( $path ) ) {
            wp_send_json_error( array( 'message' => 'No uploaded file found. Please upload again.' ) );
        }

        try {
            $migrator = new S2M_Migrator( $path );
            $migrator->connect();
            $tables = $migrator->analyze();
            wp_send_json_success( array( 'tables' => $tables ) );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'message' => $e->getMessage() ) );
        }
    }

    // ── Run migration ───────────────────────────────────────────
    public function handle_migrate() {
        $this->verify();

        $path = get_transient( 's2m_file_' . get_current_user_id() );
        if ( ! $path || ! file_exists( $path ) ) {
            wp_send_json_error( array( 'message' => 'No uploaded file found. Please upload again.' ) );
        }

        $selected_tables = isset( $_POST['tables'] ) ? array_map( 'sanitize_text_field', (array) $_POST['tables'] ) : array();
        $drop_existing   = ! empty( $_POST['drop_existing'] );
        $prefix          = sanitize_text_field( $_POST['table_prefix'] ?? get_option( 's2m_table_prefix', '' ) );
        $batch_size      = absint( $_POST['batch_size'] ?? get_option( 's2m_batch_size', 500 ) );
        $batch_size      = max( 50, min( $batch_size, 5000 ) );

        // Increase execution time for large DBs
        if ( ! ini_get( 'safe_mode' ) ) {
            set_time_limit( 300 );
        }

        try {
            $migrator = new S2M_Migrator( $path );
            $migrator->connect();
            $result = $migrator->migrate( $selected_tables, $drop_existing, $prefix, $batch_size );

            // Log to options
            $log   = get_option( 's2m_migration_log', array() );
            $log[] = array(
                'time'    => current_time( 'mysql' ),
                'file'    => basename( $path ),
                'stats'   => $result['stats'],
                'errors'  => $result['errors'],
                'warnings'=> $result['warnings'],
            );
            // Keep last 50 entries
            if ( count( $log ) > 50 ) {
                $log = array_slice( $log, -50 );
            }
            update_option( 's2m_migration_log', $log );

            // Clean up uploaded file
            @unlink( $path );
            delete_transient( 's2m_file_' . get_current_user_id() );

            wp_send_json_success( $result );

        } catch ( Exception $e ) {
            wp_send_json_error( array( 'message' => $e->getMessage() ) );
        }
    }

    // ── Clear history log ───────────────────────────────────────
    public function handle_clear_log() {
        $this->verify();
        delete_option( 's2m_migration_log' );
        wp_send_json_success( array( 'message' => 'Log cleared.' ) );
    }
}
