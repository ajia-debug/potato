<?php
if(!defined('AUTH_CORE')){
	header('HTTP/1.1 403 Forbidden');
	exit;
}
//管理员账号密码
$auth_user = 'admin';
$auth_pwd = '123456';

defined('ROOT_PATH') || define('ROOT_PATH',str_replace('\\','/',dirname(__FILE__)).'/');


$user = $_SERVER['PHP_AUTH_USER'] ?? '';
$pwd = $_SERVER['PHP_AUTH_PW'] ?? '';
if ($user != $auth_user || $pwd != $auth_pwd) {
	header('WWW-Authenticate: Basic realm="My Realm"');
	header('HTTP/1.0 401 Unauthorized');
	echo '你必须验证管理员权限才能访问';
	exit;
}