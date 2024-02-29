<?php
/**
 * @desc    : 框架功能类
 * @author  : sam
 * @email   : sam@imccc.cc
 * @date    : 2024/2/26 19:27
 * @version : 1.0.0
 * @license : MIT
 */

declare (strict_types = 1);

namespace Imccc\Slim\Core;

use Imccc\Slim\Core\Config;
use Imccc\Slim\Core\HandlerException;

class Slim
{
    protected $_cfg;
    protected $_router;

    public function __construct()
    {
        $this->init();
    }

    /**
     * 初始化
     */
    private function init(): void
    {
        session_start();
        $this->autoloader();
        $this->registerException();
        $this->_cfg = Config::get("system");
        $this->setTimeZone();
        $this->report();
        $this->dispatch();
    }

    /**
     * 注册加载类
     */
    private function autoloader(): void
    {
        require_once '../../../autoloader.php';
        // Loader::register();
    }

    /**
     * 设置时区
     */
    private function setTimeZone(): void
    {
        date_default_timezone_set($this->_cfg['sys']['timezone'] ?? 'Asia/Shanghai');
        ini_set('date.timezone', $this->_cfg['sys']['timezone'] ?? 'Asia/Shanghai');
    }

    /**
     * 设置错误报告
     */
    private function report(): void
    {
        if ($this->_cfg["debug"]["report"]) {
            error_reporting(E_ALL); //报告所有错误
        } else {
            error_reporting(0); //关闭错误报告
        }
    }

    /**
     * 注册异常处理
     */
    private function registerException(): void
    {
        set_error_handler([HandlerException::class, 'handleException']);
    }

    /**
     * 路由分发
     */
    public function dispatch(): void
    {
        // $a = $b / 0;
        $this->_router = Config::get("router");
        $r = new Router();
        $a = $r->parse($this->_router);
        print_r($a);die;
        // $dispatcher = new Dispatcher($this->_router);
        // print_r($dispatcher);die;
        // $dispatcher->dispatch();
    }

    /**
     * 销毁
     */
    public function __destruct()
    {
        echo "Use Time: " . (microtime(true) - SLIM_START_TIME) . "s\n";
        // 可以添加一些清理操作，如果有需要的话
    }
}
