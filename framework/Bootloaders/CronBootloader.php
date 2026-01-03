<?php
namespace Ocular\Framework\Bootloaders;

class CronBootloader
{
    public function boot(): void
    {
        if (!defined('DOING_CRON')) {
            define('DOING_CRON', true);
        }
        if (!defined('SHORTINIT')) {
            define('SHORTINIT', true);
        }
    }
}
