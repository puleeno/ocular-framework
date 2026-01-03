<?php
namespace Ocular\Http\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

class GlobalController
{

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        global $wp, $wp_query, $wp_the_query;

        // 1. Đồng bộ Request URI vào nhân WP
        $wp->parse_request($request->getUri()->getPath());

        // 2. Kích hoạt Query chính thức của WordPress dựa trên Rewrite Rules
        // Đây là nơi Ocular "hỏi" WP: "URL này thuộc về dữ liệu nào?"
        $wp->query_posts();

        // 3. Sử dụng Output Buffer để bắt lấy giao diện từ Theme
        ob_start();

        // Tìm kiếm template phù hợp (single.php, archive.php, v.v.)
        $template = \locate_template(\get_query_template(\get_post_type()));

        if ($template) {
            include $template;
        } else {
            // Fallback nếu không thấy template trong theme
            include ABSPATH . 'wp-includes/template-loader.php';
        }

        $html = ob_get_clean();

        return new Response(200, ['Content-Type' => 'text/html'], $html);
    }
}
