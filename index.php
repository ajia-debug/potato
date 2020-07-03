<?php

use Jsyqw\PotatoBot\Potato;
use Jsyqw\PotatoBot\PotatoRequest;

require_once 'vendor/autoload.php';
$potato = new Potato('10275575:E6D8mm6DmDqzXXGP0e145p6r','9蜜');
$group = 12070345;
PotatoRequest::initialize($potato);

$path = str_replace('\\','/',dirname(__FILE__)).'/';
$videoes = glob($path.'/download/*.mp4');
$alreadySend = file_get_contents($path.'/send/'.$group.'.txt');
$alreadySendArr = explode("\r\n",$alreadySend);
$limitSend = 5;//限制每次发送的视频数量
$i=1;

$removeDate = date('Y-m-d',strtotime('-0 days'));
//删除昨天保存的已发送视频记录
$sendFileTxt = glob($path.'/send/*');
foreach($sendFileTxt as $txt){
	if(date('Y-m-d',filectime($txt)) == $removeDate){
		unlink($path.$txt);
	}
}
foreach($videoes as $vi){
	//删除两天前下载的视频
	if(date('Y-m-d',filectime($vi)) == $removeDate){
		unlink($path.$vi);
		continue;
	}
	if($i>$limitSend){
		break;
	}
	
	if(in_array(basename($vi),$alreadySendArr)){
		echo $vi.' Already Send'.PHP_EOL;
		continue;
	}

	$reqData = new \Jsyqw\PotatoBot\Requests\ReqSendVideo();
	$reqData->chat_id = $group; //群组
	$reqData->chat_type = 3;//\Jsyqw\PotatoBot\Types\ChatType::PeerChat;
	$reqData->video = fopen($vi, 'r');
	//$reqData->thumb = fopen('./img.png','r');
	//$reqData->caption = '机器人自动发的视频';
	
	$ret = PotatoRequest::sendVideo($reqData);
	
	if($ret->ok == 1){
		//登记发送成功的视频
		file_put_contents($path.'/send/'.$group.'.txt',basename($vi)."\r\n",FILE_APPEND);
		$i++;
	}
}
