<?php
namespace Imccc\Slim\Core;

use Imccc\Slim\Core\Config;
use Imccc\Slim\Core\Loader;
use Imccc\Slim\Core\Router;

class Slim
{
    public $_cfg;
    public function __construct()
    {
        $this->run();
    }

    //运行
    private function run()
    {
        $this->register();
        $this->init();
        $this->router();
    }

    //初始化
    private function init()
    {
        $this->_cfg = Config::get("system");
        date_default_timezone_set($this->_cfg['sys']["timezone"]);
        ini_set('date.timezone', $this->_cfg['sys']["timezone"]);
        session_start();
        if ($this->_cfg["debug"]["report"]) {
            error_reporting(E_ALL); //报告所有错误
        } else {
            error_reporting(0); //关闭错误报告
        }

    }

    /**
     * 注册加载类
     */
    private function register()
    {
        Loader::register();
    }

    /**
     * 路由类
     */
    public function router()
    {
        new Router;
    }
}
