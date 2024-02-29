<?php
/**
 * @desc    : 配置类
 * @author  : sam
 * @email   : sam@imccc.cc
 * @date    : 2024/2/26 19:20
 * @version : 1.0.0
 * @license : MIT
 */

declare (strict_types = 1);

namespace Imccc\Slim\Core;

defined('CS') || define('CS', '.');
defined("CFG_EXT") || define("CFG_EXT", '.conf.php');
defined("CONF_PATH") || die("CONFIG_PATH is not defined");
defined("APP_CONFIG_PATH") || die("APP_CONFIG_PATH is not defined");

class Config
{
    /**
     * 加载配置文件 支持多配置。支持覆盖系统配置，优先载入app目录下的配置，不存在则载入框架默认配置
     * @access private
     * @param  string $configfile  配置参数名
     * @return mixed
     */
    private static function load($configfile)
    {
        $cf = CONF_PATH . DS . $configfile . CFG_EXT;
        $acf = APP_CONFIG_PATH . DS . $configfile . CFG_EXT;
        if (file_exists($acf)) {
            return include_once $acf;
        } elseif (file_exists($cf)) {
            return include_once $cf;
        } else {
            return [];
        }
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string $key    配置参数名（支持多级配置 {CS}号分割）
     * @param  string $def    默认值
     * @return mixed
     */
    public static function get(string $key = "", mixed $def = null): mixed
    {
        if (!$key) {
            return false;
        }
        $pm = explode(CS, $key);
        $f = $pm[0];
        $cfg = self::load($f);
        //没有{CS}分割符直接返回全部
        if (false === strpos($key, CS)) {
            return $cfg;
        } else {
            foreach ($pm as $val) {
                if ($f == $val) {
                    array_shift($pm); // 移除文件名
                } else {
                    if (isset($cfg[$val])) {
                        $cfg = $cfg[$val];
                    } else {
                        return $def;
                    }
                }
            }
            return $cfg;
        }
    }

    /**
     * 设置配置参数
     * @access public
     * @param  string $key    配置参数名（支持多级配置 {CS}号分割）
     * @param  mixed  $value  配置值
     * @return array          设置后的完整配置数组
     */
    public static function set(string $key, mixed $value): array
    {
        if (!$key) {
            return [];
        }
        $pm = explode(CS, $key);
        $f = $pm[0];
        $cfg = self::load($f);
        $current = &$cfg;

        foreach ($pm as $val) {
            if ($f == $val) {
                unset($pm[$val]); //移除文件名
            } else {
                if (!isset($current[$val]) || !is_array($current[$val])) {
                    $current[$val] = [];
                }
                $current = &$current[$val];
            }
        }

        $current = $value;

        return $cfg;
    }

    /**
     * 保存设置
     * @access public
     * @param  string $configfile  配置参数名
     * @param  mixed  $val         配置值
     * @return mixed
     */
    public function save(string $configFile, mixed $configValues): void
    {
        $configFilePath = APP_CONFIG_PATH . DS . $configFile . CFG_EXT;
        $exportedConfig = var_export($configValues, true);

        $configContent = '<?php return ' . $exportedConfig . ';';
        file_put_contents($configFilePath, $configContent);
    }
}
