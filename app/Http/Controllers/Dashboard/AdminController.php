<?php
namespace Ocular\Http\Controllers\Dashboard;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminController
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Giả lập môi trường cũ cho WP-Admin
        $this->simulateLegacyEnvironment($request);

        ob_start();
        try {
            // 2. Chặn các lệnh exit/die bằng cách sử dụng shutdown handler hoặc custom error handler
            // Nạp file admin thủ công dựa trên Path
            $adminFile = ABSPATH . 'wp-admin/' . $this->resolveAdminFile($request);

            if (file_exists($adminFile)) {
                require_once $adminFile;
            }
        } catch (\Throwable $e) {
            // Catch các lỗi phát sinh từ plugin admin
        }

        $content = ob_get_clean();

        // 3. Trả về PSR-7 Response
        return new Response(200, [], $content);
    }
}
