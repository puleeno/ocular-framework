<?php
namespace Ocular\Framework\Kernels;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FrontendKernel extends AbstractKernel
{
    public function bootstrap(): void
    {
        // Frontend specific boot logic
        if (!defined('SHORTINIT')) {
            // Standard boot for frontend
        }
        require_once ABSPATH . 'wp-load.php';
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        ob_start();

        // Resolve and include WP frontend
        require_once ABSPATH . 'wp-blog-header.php';

        $content = ob_get_clean();
        return new Response(200, [], $content);
    }
}
