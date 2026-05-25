<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap s2m-wrap">
    <h1 class="s2m-page-title">
        <span class="dashicons dashicons-upload"></span>
        <?php esc_html_e( 'New Migration', 'sqlite2mysql' ); ?>
    </h1>

    <!-- Step 1: Upload -->
    <div class="s2m-card" id="s2m-step-1">
        <h2><span class="s2m-step-num">1</span> <?php esc_html_e( 'Select SQLite Database', 'sqlite2mysql' ); ?></h2>
        
        <div class="s2m-detection-area" style="margin-bottom: 25px; padding: 20px; background: #f0f6fb; border-radius: 8px; border-left: 4px solid #00a0e3;">
            <p style="margin-top:0;"><strong><?php esc_html_e( 'Auto-detect active database:', 'sqlite2mysql' ); ?></strong></p>
            <button class="button" id="s2m-btn-detect">
                <span class="dashicons dashicons-search" style="vertical-align: middle; margin-top: -3px;"></span>
                <?php esc_html_e( 'Detect Current Site Database', 'sqlite2mysql' ); ?>
            </button>
            <p class="description"><?php esc_html_e( 'Automatically find and use the SQLite file currently powering this WordPress site.', 'sqlite2mysql' ); ?></p>
        </div>

        <p class="description"><strong><?php esc_html_e( 'Or manually upload a file:', 'sqlite2mysql' ); ?></strong></p>
        <p class="description"><?php esc_html_e( 'Supported formats: .sqlite, .sqlite3, .db, .sdb. Max upload size: ', 'sqlite2mysql' ); ?><?php echo esc_html( size_format( wp_max_upload_size() ) ); ?></p>

        <div class="s2m-upload-area" id="s2m-drop-zone">
            <span class="dashicons dashicons-database-import s2m-drop-icon"></span>
            <p><?php esc_html_e( 'Drag & drop your SQLite file here, or', 'sqlite2mysql' ); ?></p>
            <label class="button button-primary" for="s2m-file-input">
                <?php esc_html_e( 'Browse File', 'sqlite2mysql' ); ?>
            </label>
            <input type="file" id="s2m-file-input" accept=".sqlite,.sqlite3,.db,.sdb" style="display:none;">
        </div>

        <div id="s2m-upload-status" class="s2m-status-box" style="display:none;"></div>

        <button class="button button-primary" id="s2m-btn-upload" disabled>
            <span class="dashicons dashicons-cloud-upload"></span>
            <?php esc_html_e( 'Upload & Analyze', 'sqlite2mysql' ); ?>
        </button>
    </div>

    <!-- Step 2: Table selection -->
    <div class="s2m-card" id="s2m-step-2" style="display:none;">
        <h2><span class="s2m-step-num">2</span> <?php esc_html_e( 'Select Tables to Migrate', 'sqlite2mysql' ); ?></h2>

        <div class="s2m-table-toolbar">
            <label><input type="checkbox" id="s2m-check-all"> <?php esc_html_e( 'Select All', 'sqlite2mysql' ); ?></label>
        </div>

        <table class="widefat striped" id="s2m-table-list">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th><?php esc_html_e( 'Table Name', 'sqlite2mysql' ); ?></th>
                    <th><?php esc_html_e( 'Columns', 'sqlite2mysql' ); ?></th>
                    <th><?php esc_html_e( 'Rows', 'sqlite2mysql' ); ?></th>
                </tr>
            </thead>
            <tbody id="s2m-table-body">
                <!-- filled by JS -->
            </tbody>
        </table>
    </div>

    <!-- Step 3: Options & Run -->
    <div class="s2m-card" id="s2m-step-3" style="display:none;">
        <h2><span class="s2m-step-num">3</span> <?php esc_html_e( 'Migration Options', 'sqlite2mysql' ); ?></h2>

        <table class="form-table">
            <tr>
                <th><label for="s2m-prefix"><?php esc_html_e( 'Table Prefix', 'sqlite2mysql' ); ?></label></th>
                <td>
                    <input type="text" id="s2m-prefix" class="regular-text" placeholder="e.g. imported_"
                           value="<?php echo esc_attr( get_option( 's2m_table_prefix', '' ) ); ?>">
                    <p class="description"><?php esc_html_e( 'Prepend to all migrated table names. Leave empty to keep original names.', 'sqlite2mysql' ); ?></p>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Drop Existing Tables', 'sqlite2mysql' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" id="s2m-drop-existing"
                               <?php checked( get_option( 's2m_drop_existing', 0 ), 1 ); ?>>
                        <?php esc_html_e( 'Drop and recreate tables if they already exist in MySQL', 'sqlite2mysql' ); ?>
                    </label>
                    <p class="description" style="color:#b32d2e;"><?php esc_html_e( 'Warning: existing data in those tables will be permanently deleted.', 'sqlite2mysql' ); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="s2m-batch-size"><?php esc_html_e( 'Batch Size', 'sqlite2mysql' ); ?></label></th>
                <td>
                    <input type="number" id="s2m-batch-size" class="small-text" min="50" max="5000"
                           value="<?php echo esc_attr( get_option( 's2m_batch_size', 500 ) ); ?>">
                    <p class="description"><?php esc_html_e( 'Rows per INSERT batch (50–5000). Lower = safer on shared hosting.', 'sqlite2mysql' ); ?></p>
                </td>
            </tr>
        </table>

        <div class="s2m-migrate-actions">
            <button class="button button-primary button-hero" id="s2m-btn-migrate">
                <span class="dashicons dashicons-controls-play"></span>
                <?php esc_html_e( 'Run Migration', 'sqlite2mysql' ); ?>
            </button>
        </div>
    </div>

    <!-- Progress -->
    <div class="s2m-card" id="s2m-progress-card" style="display:none;">
        <h2><?php esc_html_e( 'Migration Progress', 'sqlite2mysql' ); ?></h2>
        <div class="s2m-progress-wrap">
            <div class="s2m-progress-bar"><div class="s2m-progress-fill" id="s2m-progress-fill"></div></div>
            <div class="s2m-progress-label" id="s2m-progress-label"><?php esc_html_e( 'Starting...', 'sqlite2mysql' ); ?></div>
        </div>
    </div>

    <!-- Result -->
    <div class="s2m-card" id="s2m-result-card" style="display:none;">
        <h2><?php esc_html_e( 'Migration Result', 'sqlite2mysql' ); ?></h2>
        <div id="s2m-result-content"></div>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=sqlite2mysql-migrate' ) ); ?>"
           class="button button-secondary"><?php esc_html_e( 'Migrate Another File', 'sqlite2mysql' ); ?></a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=sqlite2mysql-history' ) ); ?>"
           class="button button-secondary"><?php esc_html_e( 'View History', 'sqlite2mysql' ); ?></a>
    </div>
</div>
