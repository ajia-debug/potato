<?php

use Jsyqw\PotatoBot\Potato;
use Jsyqw\PotatoBot\PotatoRequest;

require_once 'vendor/autoload.php';
$potato = new Potato('10275702:VdjeBGir5tZDYWLef930906b','D字一族');
PotatoRequest::initialize($potato);
$reqData = new \Jsyqw\PotatoBot\Requests\ReqSendMessage();
$reqData->chat_type = \Jsyqw\PotatoBot\Types\ChatType::PeerChat;
$reqData->chat_id = 12103450;
$reqData->text = date('Y-m-d H:i:s');
$ret = PotatoRequest::sendTextMessage($reqData);
print_r($ret);

$reqData = new \Jsyqw\PotatoBot\Requests\ReqSendVideo();
$reqData->chat_id = 12103450; //群组
$reqData->chat_type = \Jsyqw\PotatoBot\Types\ChatType::PeerChat;
$reqData->video = '0020022c9c416dc2cb5e63f9768e3a23';
//$reqData->video = fopen('./mov_bbb.mp4', 'r');
//$reqData->thumb = fopen('./img.png','r');
$reqData->caption = '机器人自动发的视频';
$ret = PotatoRequest::sendVideo($reqData);
print_r($ret);