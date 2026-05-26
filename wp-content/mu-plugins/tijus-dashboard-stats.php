<?php
/*
Plugin Name: Tijus Dashboard Stats
Description: Displays custom stat cards for Posts, Courses, Customers, and Careers on the admin dashboard.
Author: Gemini CLI
Version: 1.1
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add the Dashboard Widget.
 */
function tijus_add_dashboard_stats_widget() {
    wp_add_dashboard_widget(
        'tijus_dashboard_stats',
        'Website Overview',
        'tijus_render_dashboard_stats_widget'
    );

    // Move the widget to the top
    global $wp_meta_boxes;
    $dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
    $tijus_widget = array( 'tijus_dashboard_stats' => $dashboard['tijus_dashboard_stats'] );
    unset( $dashboard['tijus_dashboard_stats'] );
    $wp_meta_boxes['dashboard']['normal']['core'] = array_merge( $tijus_widget, $dashboard );
}
add_action( 'wp_dashboard_setup', 'tijus_add_dashboard_stats_widget' );

/**
 * Render the Dashboard Widget.
 */
function tijus_render_dashboard_stats_widget() {
    // Get counts
    $post_count = wp_count_posts( 'post' )->publish;
    $course_count = wp_count_posts( 'course' )->publish;
    $career_count = wp_count_posts( 'career' )->publish;
    
    $user_counts = count_users();
    $customer_count = isset( $user_counts['avail_roles']['subscriber'] ) ? $user_counts['avail_roles']['subscriber'] : 0;

    $stats = array(
        array(
            'count' => $post_count,
            'label' => 'Posts',
            'color' => '#17a2b8',
            'icon'  => 'dashicons-pin',
            'link'  => admin_url( 'edit.php' )
        ),
        array(
            'count' => $course_count,
            'label' => 'Courses',
            'color' => '#28a745',
            'icon'  => 'dashicons-education',
            'link'  => admin_url( 'edit.php?post_type=course' )
        ),
        array(
            'count' => $customer_count,
            'label' => 'Customers',
            'color' => '#ffc107',
            'icon'  => 'dashicons-groups',
            'link'  => admin_url( 'admin.php?page=tijus-customers' ),
            'text_color' => '#fff'
        ),
        array(
            'count' => $career_count,
            'label' => 'Careers',
            'color' => '#dc3545',
            'icon'  => 'dashicons-businessperson',
            'link'  => admin_url( 'edit.php?post_type=career' )
        ),
    );
    ?>
    <style>
        #tijus_dashboard_stats { border: none; background: transparent; box-shadow: none; margin-top: 20px; }
        #tijus_dashboard_stats .postbox-header { display: none; }
        #tijus_dashboard_stats .inside { margin: 0; padding: 0; }
        .tijus-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            padding: 0 0 20px 0;
        }
        @media (max-width: 1200px) {
            .tijus-stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .tijus-stats-grid { grid-template-columns: 1fr; }
        }
        .tijus-stat-card {
            border-radius: 5px;
            color: #fff !important;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            min-height: 130px;
        }
        .tijus-stat-card .inner { padding: 15px 20px; z-index: 2; }
        .tijus-stat-card h3 {
            font-size: 42px;
            font-weight: 700;
            margin: 0 0 5px 0;
            white-space: nowrap;
            padding: 0;
            color: #fff !important;
            line-height: 1.2;
        }
        .tijus-stat-card p {
            font-size: 18px;
            margin: 0;
            font-weight: 500;
            color: rgba(255,255,255,0.8);
        }
        .tijus-stat-card .icon {
            position: absolute;
            top: 5px;
            right: 15px;
            z-index: 1;
            transition: all .3s linear;
        }
        .tijus-stat-card .icon .dashicons {
            font-size: 80px;
            width: 80px;
            height: 80px;
            opacity: 0.2;
            color: #000;
        }
        /* Specific Icon Rotations to match screenshot */
        .tijus-stat-card:nth-child(1) .icon { transform: rotate(30deg); top: 15px; }
        
        .tijus-stat-card .small-box-footer {
            background-color: rgba(0,0,0,.1);
            color: rgba(255,255,255,.8);
            display: block;
            padding: 4px 0;
            position: relative;
            text-align: center;
            text-decoration: none;
            z-index: 10;
            margin-top: auto;
            font-size: 14px;
        }
        .tijus-stat-card .small-box-footer:hover {
            background-color: rgba(0,0,0,.15);
            color: #fff;
        }
    </style>

    <div class="tijus-stats-grid">
        <?php foreach ( $stats as $stat ) : 
            $text_class = isset($stat['text_color']) ? 'text-dark' : '';
            $color_style = 'background-color: ' . $stat['color'] . ' !important;';
            if (isset($stat['text_color'])) $color_style .= ' color: ' . $stat['text_color'] . ' !important;';
        ?>
            <div class="tijus-stat-card <?php echo $text_class; ?>" style="<?php echo $color_style; ?>">
                <div class="inner">
                    <h3><?php echo number_format_i18n( $stat['count'] ); ?></h3>
                    <p><?php echo esc_html( $stat['label'] ); ?></p>
                </div>
                <div class="icon">
                    <span class="dashicons <?php echo esc_attr( $stat['icon'] ); ?>"></span>
                </div>
                <a href="<?php echo esc_url( $stat['link'] ); ?>" class="small-box-footer">
                    More info <span class="dashicons dashicons-arrow-right-alt2" style="font-size: 14px; vertical-align: middle;"></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Force Dashboard Widget to full width and hide title.
 */
function tijus_dashboard_widget_full_width() {
    ?>
    <style>
        #tijus_dashboard_stats { width: 100% !important; margin-right: 0 !important; }
        #dashboard-widgets .postbox-container { width: 100% !important; }
        #dashboard-widgets #postbox-container-2, 
        #dashboard-widgets #postbox-container-3, 
        #dashboard-widgets #postbox-container-4 { width: 100% !important; }
        
        /* Layout Fix for WP Dashboard */
        #wpbody-content #dashboard-widgets.columns-1 .postbox-container,
        #wpbody-content #dashboard-widgets.columns-2 .postbox-container,
        #wpbody-content #dashboard-widgets.columns-3 .postbox-container,
        #wpbody-content #dashboard-widgets.columns-4 .postbox-container { width: 100% !important; }

        /* User requested heading styles */
        #dashboard-widgets h3, #dashboard-widgets h4, #dashboard_quick_press .drafts h2 {
            margin: 0 12px 8px;
            padding: 0;
            font-size: 46px;
            font-weight: 400;
            color: #1d2327;
        }
    </style>
    <?php
}
add_action( 'admin_head-index.php', 'tijus_dashboard_widget_full_width' );
