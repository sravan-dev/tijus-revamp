<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap s2m-wrap">
    <h1 class="s2m-page-title">
        <span class="dashicons dashicons-list-view"></span>
        <?php esc_html_e( 'Migration History', 'sqlite2mysql' ); ?>
    </h1>

    <?php if ( empty( $log ) ) : ?>
        <div class="s2m-card">
            <p><?php esc_html_e( 'No migrations recorded yet.', 'sqlite2mysql' ); ?>
               <a href="<?php echo esc_url( admin_url( 'admin.php?page=sqlite2mysql-migrate' ) ); ?>"><?php esc_html_e( 'Start one now →', 'sqlite2mysql' ); ?></a>
            </p>
        </div>
    <?php else : ?>
        <div class="s2m-card">
            <div class="tablenav top">
                <button class="button button-secondary" id="s2m-clear-log">
                    <span class="dashicons dashicons-trash"></span>
                    <?php esc_html_e( 'Clear History', 'sqlite2mysql' ); ?>
                </button>
            </div>
            <table class="widefat striped s2m-history-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php esc_html_e( 'Date/Time', 'sqlite2mysql' ); ?></th>
                        <th><?php esc_html_e( 'File', 'sqlite2mysql' ); ?></th>
                        <th><?php esc_html_e( 'Tables Created', 'sqlite2mysql' ); ?></th>
                        <th><?php esc_html_e( 'Rows Inserted', 'sqlite2mysql' ); ?></th>
                        <th><?php esc_html_e( 'Skipped', 'sqlite2mysql' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'sqlite2mysql' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( array_reverse( $log, true ) as $i => $entry ) :
                        $has_errors = ! empty( $entry['errors'] );
                        $has_warnings = ! empty( $entry['warnings'] );
                    ?>
                    <tr>
                        <td><?php echo esc_html( count( $log ) - $i ); ?></td>
                        <td><?php echo esc_html( $entry['time'] ); ?></td>
                        <td><?php echo esc_html( $entry['file'] ); ?></td>
                        <td><?php echo esc_html( $entry['stats']['tables_created'] ?? 0 ); ?></td>
                        <td><?php echo esc_html( number_format( $entry['stats']['rows_inserted'] ?? 0 ) ); ?></td>
                        <td><?php echo esc_html( $entry['stats']['tables_skipped'] ?? 0 ); ?></td>
                        <td>
                            <?php if ( $has_errors ) : ?>
                                <span class="s2m-badge s2m-badge-error"><?php esc_html_e( 'Errors', 'sqlite2mysql' ); ?></span>
                            <?php elseif ( $has_warnings ) : ?>
                                <span class="s2m-badge s2m-badge-warning"><?php esc_html_e( 'Warnings', 'sqlite2mysql' ); ?></span>
                            <?php else : ?>
                                <span class="s2m-badge s2m-badge-ok"><?php esc_html_e( 'Success', 'sqlite2mysql' ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ( $has_errors || $has_warnings ) : ?>
                    <tr class="s2m-history-details">
                        <td colspan="7">
                            <?php foreach ( $entry['errors'] as $err ) : ?>
                                <div class="s2m-notice-error">&#10007; <?php echo esc_html( $err ); ?></div>
                            <?php endforeach; ?>
                            <?php foreach ( $entry['warnings'] as $warn ) : ?>
                                <div class="s2m-notice-warning">&#9888; <?php echo esc_html( $warn ); ?></div>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
