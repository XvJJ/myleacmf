<?php

namespace app\admin\controller;

use think\Config;
use think\Db;
use think\db\Query;

/**
 * 首页
 * Class IndexController
 * @package app\admin\controller
 */
class IndexController extends CommonController
{
    public function index()
    {
        $sys_info = cache('sys_cache_server_info');
        if (!$sys_info) {
            $sys_info = $this->getServerInfo();
            cache('sys_cache_server_info', $sys_info, 10 * 60);
        }

        //会员总数
        $total['user'] = Db::name('user')->count();

        $this->assign('sys_info', $sys_info);
        $this->assign('total', $total);
        $this->assign('opcache', $this->getOpcache());

        return view();
    }

    /**
     * 获取系统信息
     * @return mixed
     * ini_get:从php.ini中获取信息
     */
    protected function getServerInfo()
    {
        // 服务器操作系统
        $sys_info['os']             = PHP_OS;
        // zlib支持
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO'; 
        // 安全模式
        $sys_info['safe_mode']      = (boolean)ini_get('safe_mode') ? 'YES' : 'NO'; //safe_mode = Off
        // 默认时区
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        // curl支持
        $sys_info['curl']           = function_exists('curl_init') ? 'YES' : 'NO';
        // 服务器环境
        $sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        // PHP版本
        $sys_info['phpv']           = phpversion();
        // IP地址
        $sys_info['ip']             = GetHostByName($_SERVER['SERVER_NAME']);
        // 文件上传限制
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown';
        //脚本最大执行时间
        $sys_info['max_ex_time']    = @ini_get("max-execution_time") ? @ini_get("max_execution_time") . 's' : 'unknown'; 
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        // 服务器域名
        $sys_info['domain']         = $_SERVER['HTTP_HOST'];
        // 最大占用内存
        $sys_info['memory_limit']   = ini_get('memory_limit');
        $dbPort                     = Config::get('database.prefix');
        $dbHost                     = Config::get('database.prefix');
        $dbHost                     = empty($dbPort) || $dbPort == 3306 ? $dbHost : $dbHost . ':' . $dbPort;

        $musql_version             = (new Query())->query('select version() as ver');
        // mysql 版本
        $sys_info['mysql_version'] = $musql_version[0]['ver'];
        // GD 版本
        if (function_exists("gd_info")) {
            $gd                 = gd_info();
            $sys_info['gdinfo'] = $gd['GD Version'];
        } else {
            $sys_info['gdinfo'] = "未知";
        }

        return $sys_info;
    }

    protected function getOpcache()
    {
        if (!function_exists('opcache_get_configuration')) {
            return [];
        }
        return opcache_get_configuration();
    }
}
