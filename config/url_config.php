<?php

class URLConfig {
    private static $base_url;

    public static function init() {
        // Get the server name from the request
        $server_name = $_SERVER['SERVER_NAME'];
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        
        // Check if we're in local environment
        if ($server_name === 'localhost' || $server_name === '127.0.0.1') {
            self::$base_url = $protocol . $server_name . ':' . $_SERVER['SERVER_PORT'] . '/git/CUCEA-PA';
        } else {
            // Production environment
            self::$base_url = 'https://pa.cucea.udg.mx';
        }
    }

    public static function getBaseURL() {
        if (!isset(self::$base_url)) {
            self::init();
        }
        return self::$base_url;
    }

    public static function getFullURL($path) {
        return self::getBaseURL() . $path;
    }
}