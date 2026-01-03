<?php
namespace Ocular\Framework\Kernels;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RestKernel extends AbstractKernel
{
    public function bootstrap(): void
    {
        if (!defined('REST_REQUEST')) {
            define('REST_REQUEST', true);
        }
        require_once ABSPATH . 'wp-load.php';
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        ob_start();

        // WP REST API handling
        // We might need to manually trigger the REST server if it's not automatically triggered by wp-load/settings
        global $wp_rest_server;
        if (empty($wp_rest_server)) {
            do_action('rest_api_init');
        }

        // This is a simplified version, real implementation would use WP_REST_Request
        require_once ABSPATH . 'wp-settings.php';

        $content = ob_get_clean();
        return new Response(200, ['Content-Type' => 'application/json'], $content);
    }
}
