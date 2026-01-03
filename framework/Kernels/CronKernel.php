<?php
namespace Ocular\Framework\Kernels;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CronKernel extends AbstractKernel
{
    public function bootstrap(): void
    {
        if (!defined('DOING_CRON')) {
            define('DOING_CRON', true);
        }
        if (!defined('SHORTINIT')) {
            define('SHORTINIT', true);
        }
        require_once ABSPATH . 'wp-load.php';
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        ob_start();

        require_once ABSPATH . 'wp-cron.php';

        $content = ob_get_clean();
        return new Response(200, [], $content);
    }
}
