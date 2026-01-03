<?php
namespace Ocular\Framework\Kernels;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminKernel extends AbstractKernel
{
    public function bootstrap(): void
    {
        if (!defined('WP_ADMIN')) {
            define('WP_ADMIN', true);
        }
        require_once ABSPATH . 'wp-load.php';
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Legacy Sandbox logic
        ob_start();

        // Simulating the path to admin files
        $path = ltrim($request->getUri()->getPath(), '/');
        if (empty($path) || $path === 'wp-admin' || $path === 'wp-admin/') {
            $adminFile = 'index.php';
        } else {
            $adminFile = str_replace('wp-admin/', '', $path);
        }

        $adminPath = ABSPATH . 'wp-admin/' . $adminFile;

        if (file_exists($adminPath)) {
            require_once $adminPath;
        } else {
            return new Response(404, [], 'Admin file not found');
        }

        $content = ob_get_clean();
        return new Response(200, [], $content);
    }
}
