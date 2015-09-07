<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 支持的HTTP方法
define('SUPPORTED_METHOD', 'GET,POST,PUT,DELETE,EVENT,EVENT_GET,REMOVE');

// 定义应用目录
define('ENTRY_PATH', dirname(__FILE__));
define('APPLICATION_PATH',ENTRY_PATH.'/Application/');
define('APP_PATH',ENTRY_PATH.'/Application/');
define('CTS', time());

header("Access-Control-Allow-Origin: *");

/**
 * CORS非简单跨域请求第一次讯问是否支持跨域
 * 输出支持的method，header
 */
if($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    header("Access-Control-Allow-Methods: ".SUPPORTED_METHOD);
    header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, X-Requested-With, Token, Client-Language, API-Version");
    header("Access-Control-Max-Age", 600);
    exit;
}

ini_set('session.cookie_lifetime', '1800');

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.5.9','<')) {
    header("HTTP/1.1 500");
    $msg = array(
        'error'=> true,
        'msg'  => 'require php5.5.9+ and MySQL5.6.5+'
    );
    echo json_encode($msg);exit;
}

require './vendor/autoload.php';

// Yaml
$ones = array();
try{
    $ones = \Symfony\Component\Yaml\Yaml::parse(file_get_contents('./config.yaml'));
} catch(Exception $e) {}


// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', $ones['DEBUG']);

//修正POST不能正确获取数据问题
if(in_array($_SERVER["REQUEST_METHOD"], array("POST", "PUT"))
    && !$_POST
    && isset($_SERVER["CONTENT_TYPE"])
    && strpos($_SERVER['CONTENT_TYPE'], "application/json") !== false) {
    try{
        $_POST = json_decode(file_get_contents("php://input"), true);
        $_REQUEST = array_merge((array)$_GET, (array)$_POST);
    } catch(Exception $e) {}
}

define('RUNTIME_PATH','./Runtime/');

define('CURRENT_TIMESTAMP', time());

// 引入ThinkPHP入口文件
require './vendor/topthink/thinkphp/ThinkPHP/ThinkPHP.php';