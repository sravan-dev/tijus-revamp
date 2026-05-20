<?php
/**
 * Tijus About Page Seeder – Admin Tool
 *
 * Appears under Tools → Seed About Page.
 * Creates (or updates) the About page and assigns the About Page template.
 *
 * @package tijus-theme
 */

add_action( 'admin_menu', 'tijus_about_seeder_menu' );

function tijus_about_seeder_menu() {
    add_management_page(
        'Seed About Page',
        'Seed About Page',
        'manage_options',
        'tijus-seed-about',
        'tijus_about_seeder_render'
    );
}

add_action( 'admin_init', 'tijus_about_seeder_handle' );

function tijus_about_seeder_handle() {
    if (
        ! isset( $_POST['tijus_seed_about_nonce'] ) ||
        ! wp_verify_nonce( $_POST['tijus_seed_about_nonce'], 'tijus_seed_about' ) ||
        ! current_user_can( 'manage_options' )
    ) {
        return;
    }

    // Find or create the About page
    $existing = get_page_by_path( 'about' );
    $page_id  = $existing ? $existing->ID : 0;

    $page_data = [
        'post_title'   => 'About',
        'post_name'    => 'about',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ];

    if ( $page_id ) {
        $page_data['ID'] = $page_id;
        wp_update_post( $page_data );
    } else {
        $page_id = wp_insert_post( $page_data );
    }

    if ( ! $page_id || is_wp_error( $page_id ) ) {
        wp_redirect( add_query_arg( [ 'page' => 'tijus-seed-about', 'result' => 'error' ], admin_url( 'tools.php' ) ) );
        exit;
    }

    // Assign the About Page template and clear any Elementor data
    update_post_meta( $page_id, '_wp_page_template', 'page-about.php' );
    delete_post_meta( $page_id, '_elementor_data' );
    delete_post_meta( $page_id, '_elementor_edit_mode' );

    wp_redirect( add_query_arg( [ 'page' => 'tijus-seed-about', 'result' => 'ok', 'pid' => $page_id ], admin_url( 'tools.php' ) ) );
    exit;
}

function tijus_about_seeder_render() {
    $result   = isset( $_GET['result'] ) ? sanitize_key( $_GET['result'] ) : '';
    $page_id  = isset( $_GET['pid'] )    ? intval( $_GET['pid'] )          : 0;
    $existing = get_page_by_path( 'about' );
    ?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-admin-page" style="font-size:26px;width:26px;height:26px;color:#2271b1;margin-right:6px;vertical-align:middle;"></span>
            Seed About Page
        </h1>
        <p style="color:#666;">Creates (or updates) the <strong>About</strong> page and assigns the <em>About Page</em> template so the static content is displayed with the theme's design.</p>

        <?php if ( $result === 'ok' ) : ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <strong>About page created/updated successfully.</strong>
                    <a href="<?php echo esc_url( get_permalink( $page_id ) ); ?>" target="_blank">View Page &rarr;</a>
                    &nbsp;|&nbsp;
                    <a href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>">Edit in WordPress &rarr;</a>
                </p>
            </div>
        <?php elseif ( $result === 'error' ) : ?>
            <div class="notice notice-error"><p><strong>Error creating the page. Please try again.</strong></p></div>
        <?php endif; ?>

        <?php if ( $existing ) : ?>
            <div class="notice notice-info" style="margin-top:10px;">
                <p>An "About" page already exists (ID <?php echo intval( $existing->ID ); ?>, status: <strong><?php echo esc_html( $existing->post_status ); ?></strong>). Running the seeder will update its template assignment.</p>
            </div>
        <?php endif; ?>

        <form method="post" action="" style="margin-top:20px;">
            <?php wp_nonce_field( 'tijus_seed_about', 'tijus_seed_about_nonce' ); ?>
            <?php submit_button( $existing ? 'Update About Page Template' : 'Create About Page', 'primary large', 'submit', false ); ?>
        </form>
    </div>
    <?php
}
