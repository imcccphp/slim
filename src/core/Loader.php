<?php
/**
 * @desc    : Composer加载类
 * @author  : sam
 * @email   : sam@imccc.cc
 * @date    : 2024/2/26 19:27
 * @version : 1.0.0
 * @license : MIT
 */

namespace Imccc\Slim\Core;

class Loader
{

    public static function register()
    {
        if (PHP_VERSION_ID < 50600) {
            if (!headers_sent()) {
                header('HTTP/1.1 500 Internal Server Error');
            }
            $err = 'Composer 2.3.0 dropped support for autoloading on PHP <5.6 and you are running ' . PHP_VERSION . ', please upgrade PHP or use Composer 2.2 LTS via "composer self-update --2.2". Aborting.' . PHP_EOL;
            if (!ini_get('display_errors')) {
                if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
                    fwrite(STDERR, $err);
                } elseif (!headers_sent()) {
                    echo $err;
                }
            }
            trigger_error(
                $err,
                E_USER_ERROR
            );
        }
        require_once VENDOR_PATH . 'composer/autoload_real.php';
        return \ComposerAutoloaderInit9e85d9877ce5830b7fe7e34f81fbe62e::getLoader();
    }
}
