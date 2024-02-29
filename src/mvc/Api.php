<?php
namespace imccc\Slim\Mvc;

use imccc\Slim\Core\Config;

class Api
{
    protected $_cfg;
    public function __construct()
    {
        $this->_cfg = Config::get('api');
    }
}
