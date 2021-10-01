<?php
// #母狗 #淫妻 #妓女 #群交 #滥交 #骚逼 #肉便器 #精液 #阿姨 #儿子 #干妈 #乱伦 #绿帽 #3P #无套 #内射 #换妻 #妈妈 #外婆 #奶奶 #孙子 #夫妻 #单男 #潮吹 #中出 #套套 #射精
$search = array(
	'骚逼',
	'麻豆',
	'91',
	'丝袜',
	'换妻',
	'射精',
	'国产自拍',
	'韩国女主播'
);
$startTime = date('Y-m-d',strtotime('-2 days'));
$endTime = date('Y-m-d',strtotime('-1 days'));

$path = str_replace('\\','/',dirname(__FILE__)).'/';

foreach($search as $word){
	$keywords = urlencode($word.' until:'.$endTime.' since:'.$startTime);

	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.twitter.com/1.1/search/tweets.json?q=".$keywords."&result_type=mixed&count=100",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_SSL_VERIFYPEER =>false,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
		"authorization: Bearer AAAAAAAAAAAAAAAAAAAAAGV7FgEAAAAA4b7rzA4NQWLtepeGptx3N2Gfx5w%3Dq54iC6aXP9CMb9j99hfhTTSkI4o1JpOXFqeNVMF8KAX2eTuQSr",
	  ),
	));
	$response = curl_exec($curl);
	curl_close($curl);
	$videoUrls = array();
	//解析json
	$response = json_decode($response,true);
	if(!empty($response['statuses'])){
		//遍历数据获取视频
		foreach($response['statuses'] as $tweets){
			//检查视频 $tweets['text'] 推文内容
			//检查是否有视频资源内容
			if(isset($tweets['extended_entities']['media'])){
				//获取视频资源
				foreach($tweets['extended_entities']['media'] as $media){
					// duration_millis 视频秒数 过滤低于120秒视频
					if(isset($media['video_info']['variants']) && $media['video_info']['duration_millis'] < 120000){
						continue;
					}
					if(isset($media['video_info']['variants'])){
						//遍历视频码率资源 优先固定 832000 的 mp4
						$url = '';
						$stardUrl = '';
						foreach($media['video_info']['variants'] as $video){
							if($video['content_type'] == 'video/mp4'){
								$url = $video['url'];
								if($video['bitrate'] == '832000'){
									$stardUrl = $video['url'];
									break;
								}
							}
						}
						if(!empty($stardUrl)){
							$videoUrls[] = $stardUrl;
						}elseif(!empty($url)){
							$videoUrls[] = $url;
						}
					}
				}
			}
		}
	}
	//视频资源去重
	$videoUrls = array_unique($videoUrls);
	//echo count($videoUrls).PHP_EOL;
	//continue;
	
	//检查视频是否已经被下载过
	foreach($videoUrls as $v){
		$fileName = parse_url($v,PHP_URL_PATH);
		$fileName = explode('/',$fileName);
		$file = array_pop($fileName);
		$filePath = $path.'/download/'.md5($word);
		if(!is_dir($filePath)){
			mkdir($filePath,0777,true);
		}
		//检查视频是否存在
		if(!file_exists($filePath.'/'.$file)){
			$flag = file_put_contents($filePath.$file,file_get_contents($v));
			if($flag !== false){
				//记录下载成功到视频
				echo 'Download:'.$v.PHP_EOL;
			}
		}
	}
}