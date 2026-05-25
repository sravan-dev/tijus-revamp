<?php
/**
 * Plugin Name: System Resource Monitor
 * Description: Displays live CPU, Memory, and Disk usage in a dashboard widget.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the dashboard widget.
 */
function tijus_add_system_monitor_widget() {
    wp_add_dashboard_widget(
        'tijus_system_resource_monitor',
        'System Resource Usage (Live)',
        'tijus_render_system_monitor_widget'
    );
}
add_action( 'wp_dashboard_setup', 'tijus_add_system_monitor_widget' );

/**
 * Render the widget content.
 */
function tijus_render_system_monitor_widget() {
    ?>
    <div id="tijus-system-monitor">
        <div class="stat-row">
            <strong>CPU Load:</strong> <span id="cpu-load">Loading...</span>
            <div class="progress-bg"><div id="cpu-bar" class="progress-fill"></div></div>
        </div>
        <div class="stat-row" style="margin-top: 15px;">
            <strong>Memory Usage:</strong> <span id="mem-usage">Loading...</span>
            <div class="progress-bg"><div id="mem-bar" class="progress-fill"></div></div>
        </div>
        <div class="stat-row" style="margin-top: 15px;">
            <strong>Disk Usage:</strong> <span id="disk-usage">Loading...</span>
            <div class="progress-bg"><div id="disk-bar" class="progress-fill"></div></div>
        </div>
        <p style="font-size: 10px; color: #999; margin-top: 15px; text-align: right;">Updates every 5 seconds</p>
    </div>

    <style>
        #tijus-system-monitor .stat-row strong { display: block; margin-bottom: 5px; }
        #tijus-system-monitor .progress-bg { background: #eee; border-radius: 10px; height: 12px; overflow: hidden; margin-top: 5px; }
        #tijus-system-monitor .progress-fill { background: #00a0e3; width: 0%; height: 100%; transition: width 0.5s ease; }
        #tijus-system-monitor .progress-fill.high { background: #f44336; }
        #tijus-system-monitor .progress-fill.med { background: #ff9800; }
    </style>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            function updateSystemStats() {
                $.ajax({
                    url: ajaxurl,
                    data: { action: 'tijus_get_system_stats' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            
                            // Update CPU
                            $('#cpu-load').text(data.cpu_load.join(', '));
                            var cpuPct = Math.min(data.cpu_pct, 100);
                            $('#cpu-bar').css('width', cpuPct + '%').removeClass('high med');
                            if (cpuPct > 80) $('#cpu-bar').addClass('high');
                            else if (cpuPct > 50) $('#cpu-bar').addClass('med');

                            // Update Memory
                            $('#mem-usage').text(data.mem_formatted);
                            $('#mem-bar').css('width', data.mem_pct + '%').removeClass('high med');
                            if (data.mem_pct > 80) $('#mem-bar').addClass('high');
                            else if (data.mem_pct > 60) $('#mem-bar').addClass('med');

                            // Update Disk
                            $('#disk-usage').text(data.disk_formatted);
                            $('#disk-bar').css('width', data.disk_pct + '%').removeClass('high med');
                            if (data.disk_pct > 90) $('#disk-bar').addClass('high');
                        }
                    }
                });
            }

            // Initial load and interval
            updateSystemStats();
            setInterval(updateSystemStats, 5000);
        });
    </script>
    <?php
}

/**
 * AJAX handler to get system stats.
 */
function tijus_get_system_stats_callback() {
    // 1. CPU Load
    $load = sys_getloadavg(); // Returns 1, 5, 15 min averages
    $cpu_count = 1;
    if (is_readable('/proc/cpuinfo')) {
        $cpu_count = substr_count(file_get_contents('/proc/cpuinfo'), 'processor');
    } else {
        // macOS fallback
        $cpu_count = (int) shell_exec('sysctl -n hw.ncpu') ?: 1;
    }
    $cpu_pct = ($load[0] / $cpu_count) * 100;

    // 2. Memory (macOS specific for local dev)
    $mem_total = 0;
    $mem_used = 0;
    
    if (PHP_OS_FAMILY === 'Darwin') {
        $vm_stat = shell_exec('vm_stat');
        preg_match('/Pages free:\s+(\d+)/', $vm_stat, $free);
        preg_match('/Pages active:\s+(\d+)/', $vm_stat, $active);
        preg_match('/Pages inactive:\s+(\d+)/', $vm_stat, $inactive);
        preg_match('/Pages speculative:\s+(\d+)/', $vm_stat, $spec);
        preg_match('/Pages wired down:\s+(\d+)/', $vm_stat, $wired);
        preg_match('/Pages compressed:\s+(\d+)/', $vm_stat, $comp);
        
        $pageSize = 4096; // macOS default
        $free_bytes = ($free[1] + $spec[1]) * $pageSize;
        $used_bytes = ($active[1] + $inactive[1] + $wired[1] + $comp[1]) * $pageSize;
        $mem_total = $free_bytes + $used_bytes;
        $mem_used = $used_bytes;
    } else {
        // Linux fallback
        $free = shell_exec('free -b');
        $lines = explode("\n", $free);
        $mem_info = preg_split('/\s+/', $lines[1]);
        $mem_total = $mem_info[1];
        $mem_used = $mem_info[2];
    }
    
    $mem_pct = $mem_total > 0 ? round(($mem_used / $mem_total) * 100, 1) : 0;
    $mem_formatted = round($mem_used / 1024 / 1024 / 1024, 1) . 'GB / ' . round($mem_total / 1024 / 1024 / 1024, 1) . 'GB (' . $mem_pct . '%)';

    // 3. Disk Usage
    $disk_total = disk_total_space('/');
    $disk_free = disk_free_space('/');
    $disk_used = $disk_total - $disk_free;
    $disk_pct = round(($disk_used / $disk_total) * 100, 1);
    $disk_formatted = round($disk_used / 1024 / 1024 / 1024, 1) . 'GB / ' . round($disk_total / 1024 / 1024 / 1024, 1) . 'GB';

    wp_send_json_success([
        'cpu_load' => $load,
        'cpu_pct' => round($cpu_pct, 1),
        'mem_pct' => $mem_pct,
        'mem_formatted' => $mem_formatted,
        'disk_pct' => $disk_pct,
        'disk_formatted' => $disk_formatted
    ]);
}
add_action( 'wp_ajax_tijus_get_system_stats', 'tijus_get_system_stats_callback' );
