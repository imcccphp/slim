<?php
declare (strict_types = 1);

namespace Imccc\Slim;

use Imccc\Slim\Core\Slim;

define('SLIM_NAME', 'SLIM');
define('SLIM_VERSION', '1.0.0');
define('SLIM_START_TIME', microtime(true));
define('SLIM_START_MEM', memory_get_usage());
define('EXT', '.php');
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('SLIM_PATH') || define('SLIM_PATH', __DIR__ . DS);

defined('DEBUG') || define('DEBUG', true);

class Boot
{
    public function __construct()
    {
        $this->init();
    }

    public function init(): void
    {
        include_once "core/Slim.php";
        new Slim();
    }
}
