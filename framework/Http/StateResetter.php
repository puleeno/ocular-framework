<?php
namespace Ocular\Framework\Http;

class StateResetter
{
    protected array $globalsToReset = [
        'wp',
        'wp_query',
        'wp_the_query',
        'wp_rewrite',
        'wp_did_header',
        'wp_filter',
        'wp_actions',
        'wp_current_filter',
        'wp_plugin_paths',
        'posts',
        'post',
        'request',
        'query_string',
        'more',
        'single',
        'authordata',
    ];

    public function reset(): void
    {
        // 1. Reset WordPress Globals
        foreach ($this->globalsToReset as $global) {
            unset($GLOBALS[$global]);
        }

        // 2. Clear WP DB cache if exists
        global $wpdb;
        if (isset($wpdb)) {
            $wpdb->queries = [];
            // Optional: reconsider if we need to close connection
        }

        // 3. Reset Object Cache if using non-persistent cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        // 4. Reset $_GET, $_POST, etc. if BridgeMiddleware was used
        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
        $_FILES = [];
        $_COOKIE = [];

        // 5. Clean output buffers if any
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
}
