<?php
namespace Imccc\Slim;

use \Imccc\Slim\Core\Slim;

define('SLIM_VERSION', '1.0.0');
define('SLIM_START_TIME', microtime(true));
define('SLIM_START_MEM', memory_get_usage());
define('EXT', '.php');
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('SLIM_PATH') || define('SLIM_PATH', __DIR__ . DS);

define('CORE_PATH', SLIM_PATH . 'Core' . DS);
define('EXPEND_PATH', SLIM_PATH . 'Expend' . DS);
define('MVC_PATH', SLIM_PATH . 'Mvc' . DS);

defined('APP_PATH') || define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
defined('ROOT_PATH') || define('ROOT_PATH', dirname(realpath(APP_PATH)) . DS);
defined('EXTEND_PATH') || define('EXTEND_PATH', ROOT_PATH . 'extend' . DS);
defined('VENDOR_PATH') || define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);
defined('RUNTIME_PATH') || define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS);
defined('LOG_PATH') || define('LOG_PATH', RUNTIME_PATH . 'log' . DS);
defined('CACHE_PATH') || define('CACHE_PATH', RUNTIME_PATH . 'cache' . DS);
defined('TEMP_PATH') || define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS);
defined('CONF_PATH') || define('CONF_PATH', APP_PATH . 'config' . DS); // 配置文件目录
defined('CONF_EXT') || define('CONF_EXT', EXT); // 配置文件后缀
defined('ENV_PREFIX') || define('ENV_PREFIX', 'PHP_'); // 环境变量的配置前缀

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

new Slim;