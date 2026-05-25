<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap s2m-wrap">
    <h1 class="s2m-page-title">
        <span class="dashicons dashicons-database-import"></span>
        <?php esc_html_e( 'Sqlite2Mysql — Dashboard', 'sqlite2mysql' ); ?>
    </h1>

    <div class="s2m-cards">
        <!-- Quick stats -->
        <?php
        $last = ! empty( $log ) ? end( $log ) : null;
        $total_migrations = count( $log );
        $total_rows = array_sum( array_column( array_column( $log, 'stats' ), 'rows_inserted' ) );
        ?>
        <div class="s2m-card s2m-stat-card">
            <div class="s2m-stat-number"><?php echo esc_html( $total_migrations ); ?></div>
            <div class="s2m-stat-label"><?php esc_html_e( 'Total Migrations', 'sqlite2mysql' ); ?></div>
        </div>
        <div class="s2m-card s2m-stat-card">
            <div class="s2m-stat-number"><?php echo esc_html( number_format( $total_rows ) ); ?></div>
            <div class="s2m-stat-label"><?php esc_html_e( 'Total Rows Migrated', 'sqlite2mysql' ); ?></div>
        </div>
        <div class="s2m-card s2m-stat-card">
            <div class="s2m-stat-number"><?php echo $last ? esc_html( human_time_diff( strtotime( $last['time'] ), current_time( 'timestamp' ) ) . ' ago' ) : '—'; ?></div>
            <div class="s2m-stat-label"><?php esc_html_e( 'Last Migration', 'sqlite2mysql' ); ?></div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="s2m-card s2m-actions-card">
        <h2><?php esc_html_e( 'Quick Actions', 'sqlite2mysql' ); ?></h2>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=sqlite2mysql-migrate' ) ); ?>" class="button button-primary button-hero">
            <span class="dashicons dashicons-upload"></span>
            <?php esc_html_e( 'Start New Migration', 'sqlite2mysql' ); ?>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=sqlite2mysql-history' ) ); ?>" class="button button-secondary button-hero">
            <span class="dashicons dashicons-list-view"></span>
            <?php esc_html_e( 'View History', 'sqlite2mysql' ); ?>
        </a>
    </div>

    <!-- Last migration result -->
    <?php if ( $last ) : ?>
    <div class="s2m-card">
        <h2><?php esc_html_e( 'Last Migration Summary', 'sqlite2mysql' ); ?></h2>
        <table class="widefat striped">
            <tr><th><?php esc_html_e( 'Time', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( $last['time'] ); ?></td></tr>
            <tr><th><?php esc_html_e( 'File', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( $last['file'] ); ?></td></tr>
            <tr><th><?php esc_html_e( 'Tables Created', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( $last['stats']['tables_created'] ); ?></td></tr>
            <tr><th><?php esc_html_e( 'Rows Inserted', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( number_format( $last['stats']['rows_inserted'] ) ); ?></td></tr>
            <tr><th><?php esc_html_e( 'Tables Skipped', 'sqlite2mysql' ); ?></th><td><?php echo esc_html( $last['stats']['tables_skipped'] ); ?></td></tr>
        </table>
        <?php if ( ! empty( $last['errors'] ) ) : ?>
            <div class="notice notice-error inline"><p><strong><?php esc_html_e( 'Errors:', 'sqlite2mysql' ); ?></strong></p>
                <ul><?php foreach ( $last['errors'] as $e ) : ?><li><?php echo esc_html( $e ); ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Requirements check -->
    <div class="s2m-card">
        <h2><?php esc_html_e( 'Server Requirements', 'sqlite2mysql' ); ?></h2>
        <table class="widefat striped">
            <?php
            $checks = array(
                'PDO extension'        => class_exists( 'PDO' ),
                'PDO SQLite driver'    => class_exists( 'PDO' ) && in_array( 'sqlite', PDO::getAvailableDrivers(), true ),
                'Upload directory'     => file_exists( S2M_UPLOAD_DIR ) && is_writable( S2M_UPLOAD_DIR ),
                'PHP >= 7.4'           => version_compare( PHP_VERSION, '7.4', '>=' ),
            );
            foreach ( $checks as $label => $ok ) :
            ?>
            <tr>
                <th><?php echo esc_html( $label ); ?></th>
                <td>
                    <?php if ( $ok ) : ?>
                        <span class="s2m-ok">&#10003; <?php esc_html_e( 'OK', 'sqlite2mysql' ); ?></span>
                    <?php else : ?>
                        <span class="s2m-fail">&#10007; <?php esc_html_e( 'Not available', 'sqlite2mysql' ); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
