<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
$ticket = 'Baskjdkj129AS';

if(
	(!isset($_GET['ticket']) && !isset($argv[1])) 
	|| (isset($_GET['ticket']) && $_GET['ticket'] == $ticket) 
	|| (isset($argv[1]) && $argv[1] != $ticket)
){
	echo 'ticket wrong,stop run';
	exit;
}
define('ROOT_PATH',str_replace('\\','/',dirname(__FILE__)));
require_once ROOT_PATH.'./TelegramBot.class.php';
$bot = new TelegramBot();
$bot->setToken('1347649736:AAE7Q4vS1XIs5QnFC2Wxy309EZR51EZA_CQ');//机器人 token
$bot->setRoomConfig([
	//'-1001369620147' => ['times'=>1],//times 同一个视频最多发送次数
	//这里的数字是群组id
	'-421042266' => ['times'=>1]
]);
$bot->sendVideo();