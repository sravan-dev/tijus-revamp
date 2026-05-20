<?php
/**
 * Plugin Name: Auto WebP Image Formatter
 * Description: Dynamically creates and renders WebP format for local images using output buffering.
 * Version: 1.0
 * Author: Antigravity
 */

if (!defined('ABSPATH')) {
    exit;
}

class Auto_WebP_Renderer {

    public function __construct() {
        // Only run on the frontend (don't convert inside WP Admin or REST API to save resources and avoid conflicts)
        if (!is_admin() && !wp_is_json_request() && !is_feed() && !wp_doing_cron() && !wp_doing_ajax()) {
            // Priority 2 ensures it captures most of the HTML
            add_action('template_redirect', [$this, 'start_output_buffer'], 2);
        }
    }

    public function start_output_buffer() {
        // Start buffering with our rewrite callback
        ob_start([$this, 'convert_images_in_html']);
    }

    /**
     * Parse HTML, extract image URLs, and replace them with WebP URLs.
     */
    public function convert_images_in_html($html) {
        // Check if the server supports WebP generation
        if (!function_exists('imagewebp')) {
            return $html;
        }

        $wp_upload_dir = wp_get_upload_dir();
        
        // If upload dir is malformed or inaccessible, return original html
        if (empty($wp_upload_dir['baseurl']) || empty($wp_upload_dir['basedir'])) {
            return $html;
        }

        $base_url = $wp_upload_dir['baseurl'];
        $base_dir = $wp_upload_dir['basedir'];

        $base_url_escaped = preg_quote($base_url, '/');

        // Regex matches URLs from WP Uploads folder that end in jpg/jpeg/png
        $pattern = '/(' . $base_url_escaped . '\/[^\'\"\s\>]+?\.(?:jpg|jpeg|png))/i';

        $html = preg_replace_callback($pattern, function($matches) use ($base_url, $base_dir) {
            $image_url  = $matches[1];
            
            // Map the public URL to the local server file path
            $relative_path = str_replace($base_url, '', $image_url);
            
            // Decode URL components (like spaces %20) before hitting the filesystem
            $relative_path = urldecode($relative_path);
            $file_path     = $base_dir . $relative_path;

            if (file_exists($file_path)) {
                // New paths will append .webp to the end (e.g., image.jpg.webp)
                $webp_path = $file_path . '.webp';
                $webp_url  = $image_url . '.webp';

                // Generate WebP if it hasn't been generated yet for this image
                if (!file_exists($webp_path)) {
                    $this->create_webp($file_path, $webp_path);
                }

                // If the WebP successfully exists, rewrite the HTML to serve it
                if (file_exists($webp_path)) {
                    return $webp_url;
                }
            }

            // Return original URL if something goes wrong
            return $image_url;

        }, $html);

        return $html;
    }

    /**
     * Converts an image to WebP format.
     */
    private function create_webp($source, $destination) {
        $info = @getimagesize($source);
        if (!$info) return false;

        $mime = $info['mime'];
        $image = false;

        switch ($mime) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($source);
                if ($image) {
                    // Preserve transparency for PNG to WebP conversion
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
        }

        if ($image) {
            // We use quality level 80 for a good balance of size vs quality
            $success = @imagewebp($image, $destination, 80);
            imagedestroy($image);
            return $success;
        }

        return false;
    }
}

new Auto_WebP_Renderer();
