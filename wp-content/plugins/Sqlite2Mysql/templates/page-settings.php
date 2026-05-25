<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap s2m-wrap">
    <h1 class="s2m-page-title">
        <span class="dashicons dashicons-admin-settings"></span>
        <?php esc_html_e( 'Sqlite2Mysql Settings', 'sqlite2mysql' ); ?>
    </h1>

    <div class="s2m-card">
        <form method="post">
            <?php wp_nonce_field( 's2m_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th><label for="s2m_batch_size"><?php esc_html_e( 'Default Batch Size', 'sqlite2mysql' ); ?></label></th>
                    <td>
                        <input type="number" name="s2m_batch_size" id="s2m_batch_size" class="small-text"
                               min="50" max="5000" value="<?php echo esc_attr( get_option( 's2m_batch_size', 500 ) ); ?>">
                        <p class="description"><?php esc_html_e( 'Rows per INSERT batch during migration. 50–5000.', 'sqlite2mysql' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Drop Existing Tables', 'sqlite2mysql' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="s2m_drop_existing" value="1"
                                   <?php checked( get_option( 's2m_drop_existing', 0 ), 1 ); ?>>
                            <?php esc_html_e( 'Enable by default', 'sqlite2mysql' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><label for="s2m_table_prefix"><?php esc_html_e( 'Default Table Prefix', 'sqlite2mysql' ); ?></label></th>
                    <td>
                        <input type="text" name="s2m_table_prefix" id="s2m_table_prefix" class="regular-text"
                               placeholder="e.g. imported_"
                               value="<?php echo esc_attr( get_option( 's2m_table_prefix', '' ) ); ?>">
                        <p class="description"><?php esc_html_e( 'Prepended to all migrated table names.', 'sqlite2mysql' ); ?></p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="s2m_save_settings" class="button button-primary"
                       value="<?php esc_attr_e( 'Save Settings', 'sqlite2mysql' ); ?>">
            </p>
        </form>
    </div>

    <div class="s2m-card">
        <h2><?php esc_html_e( 'WordPress MySQL Connection', 'sqlite2mysql' ); ?></h2>
        <table class="widefat striped">
            <tr><th><?php esc_html_e( 'Host', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( DB_HOST ); ?></td></tr>
            <tr><th><?php esc_html_e( 'Database', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( DB_NAME ); ?></td></tr>
            <tr><th><?php esc_html_e( 'User', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( DB_USER ); ?></td></tr>
            <tr><th><?php esc_html_e( 'WP Table Prefix', 'sqlite2mysql' ); ?></th><td><?php global $wpdb; echo esc_html( $wpdb->prefix ); ?></td></tr>
            <tr><th><?php esc_html_e( 'Charset', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( DB_CHARSET ); ?></td></tr>
        </table>
    </div>

    <div class="s2m-card">
        <h2><?php esc_html_e( 'Upload Directory', 'sqlite2mysql' ); ?></h2>
        <table class="widefat striped">
            <tr><th><?php esc_html_e( 'Path', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( S2M_UPLOAD_DIR ); ?></td></tr>
            <tr><th><?php esc_html_e( 'Exists', 'sqlite2mysql' ); ?></th><td><?php echo file_exists( S2M_UPLOAD_DIR ) ? '<span class="s2m-ok">Yes</span>' : '<span class="s2m-fail">No — activate plugin to create</span>'; ?></td></tr>
            <tr><th><?php esc_html_e( 'Writable', 'sqlite2mysql' ); ?></th><td><?php echo is_writable( S2M_UPLOAD_DIR ) ? '<span class="s2m-ok">Yes</span>' : '<span class="s2m-fail">No</span>'; ?></td></tr>
        </table>
    </div>
</div>
