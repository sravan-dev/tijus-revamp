<?php
$root = $_SERVER['DOCUMENT_ROOT'];
$path = '/' . ltrim( parse_url( urldecode( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ), '/' );

if ( file_exists( $root . $path ) ) {
    // Enforce trailing slashes on directories to prevent relative path issues.
    if ( is_dir( $root . $path ) && substr( $path, -1 ) !== '/' ) {
        header( "Location: $path/" );
        exit;
    }
    
    // Let the PHP built-in server handle existing files and directories.
    return false;
}

// Route non-existent paths to index.php (This is what enables Permalinks/Slugs to work).
$_SERVER['SCRIPT_NAME'] = '/index.php';
chdir( $root );
require 'index.php';