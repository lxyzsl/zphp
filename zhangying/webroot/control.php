<?php
/**
 * 控制台入口
 */
//开启session
session_start();
//设置最大处理时间
ini_set('max_execution_time',86400);
//设置页面内容是html 编码格式是utf-8
header("Content-type:text/html;charset=utf-8");
//定义路径分隔符
define('DS',DIRECTORY_SEPARATOR);
//定义根目录名
define('ROOT_PATH',dirname(__DIR__));
//包含框架入口文件
require dirname(ROOT_PATH).DS.'ZPHP'.DS.'ZPHP.php';
//执行
\ZPHP\ZPHP::run(ROOT_PATH);
