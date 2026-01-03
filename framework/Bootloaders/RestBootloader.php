<?php
namespace Ocular\Framework\Bootloaders;

class RestBootloader
{
    public function boot(): void
    {
        if (!defined('REST_REQUEST')) {
            define('REST_REQUEST', true);
        }

        add_filter('wp_die_handler', function () {
            return '_scalar_rest_die_handler';
        });
    }
}

function _scalar_rest_die_handler($message, $title = '', $args = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'code' => $args['response'] ?? 500,
        'message' => $message,
        'data' => [
            'status' => $args['response'] ?? 500,
            'title' => $title
        ]
    ]);
    die;
}
