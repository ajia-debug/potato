<?php
class TelegramBot{
	private $bot = '';
	private $botApi = '';
	
	private $roomConfig = [];// 'chat_id' => ['times'=>1,'tags'=>['md5(tag)']]  房间号 => 发送部数,分类标签md5文件夹
	
	private $button = [
		'inline_keyboard'=>[
							[
								[
									'text'=>'厕拍',
									'url' => 'http://google.com'
								],
								[
									'text'=>'偷拍',
									'url' => 'http://google.com'
								],
								[
									'text'=>'偷窥',
									'url' => 'http://google.com'
								],
								[
									'text'=>'摄像头',
									'url' => 'http://google.com'
								]
							],
							[
								[
									'text'=>'街射',
									'url' => 'http://google.com'
								],
							]
						],
	];
	
	
	
	public function setToken($token){
		$this->token = $token;
		$this->botApi = 'https://api.telegram.org/bot'.$this->token;
	}
	
	public function setRoomConfig($roomConfig){
		
		$this->roomConfig = $roomConfig;
	}
	
	//发布每天上传的新视频
	public function sendVideo(){
		$filePath = str_replace('\\','/',dirname(__FILE__)).'/';
        $db = new PDO('sqlite:'.ROOT_PATH.'./db/Db');
        $stmt = $db->prepare('SELECT * FROM video order by add_time desc');
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//print_r($data);exit;
		//公共底部菜单
		$stmt = $db->prepare('SELECT title,link FROM menu where type=2 order by sort asc,id desc');
        $stmt->execute();
        $publicMenu = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
        //过滤出需要发送的视频
        $sendTimes = 0;
		foreach($this->roomConfig as $chatId => $config){
            foreach($data as $d){
		            if($sendTimes>=$config['times']){
		                break;
                    }
		            //过滤今天发送过的
                    if(date('Y-m-d') == date('Y-m-d',strtotime($d['send_time']))){
                        echo '过滤发送过的 ====> '.$d['title'].PHP_EOL;
                        continue;
                    }
					//获取按钮菜单设置
					$stmt = $db->prepare('SELECT title,link FROM menu left join menu_r 
					on menu.id=menu_r.menu_id 
					where menu_r.video_id=? 
					order by menu.sort asc,id desc');
					$stmt->execute([$d['id']]);
					$menuDbConfig = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$menuSendInner = [];
					$newLineMenu = [];//新行菜单容器
					$charNum = 0;
					foreach($menuDbConfig as $menuDb){
						//每行最多显示15个占位，一个独立菜单占位=对应字数个数+2
						//echo ($charNum+mb_strlen($menuDb['title'],'UTF-8') + 2).'=';
						if($charNum+mb_strlen($menuDb['title'],'UTF-8') + 2 > 15){
							$newLineMenu[] = $menuSendInner;
							$menuSendInner = [];
							$charNum = 0;
						}
						$charNum += mb_strlen($menuDb['title'],'UTF-8') + 2;
						$menuSendInner[] = [
							'text' => $menuDb['title'],
							'url' => $menuDb['link']??''
						];
					}
					//加上最后一次的菜单
					$newLineMenu[] = $menuSendInner;
					//补充上公共底部菜单，一行一个公共
					foreach($publicMenu as $pMenu){
						$newLineMenu[][] = [
							'text' => $pMenu['title'],
							'url' => $pMenu['link']??''
						];
					}
					$buttonConfig['inline_keyboard'] = $newLineMenu;
					
                    $params = [
                        'chat_id'=>$chatId,
                        'caption' => $d['title'],
                        'parse_mode' => 'Markdown',
                        'reply_markup' => json_encode($buttonConfig,JSON_UNESCAPED_UNICODE),
                        'video'=> new CURLFILE($filePath.$d['path']) //
                    ];
					print_r($params);
                    $response = $this->callSendVideoApi($params);
                    //记录已经发送过的视频
                    if($response['ok'] === true){
                        $stmt = $db->prepare('update video set send_times=send_times+1,send_time=? where id=?');
                        $stmt->execute([date('Y-m-d H:i:s'),$d['id']]);
                        $sendTimes++;
                        echo 'success ====> '.$d['title'].PHP_EOL;
                    }else{
                        echo 'Faild:'.$response['description'].$d['title'].PHP_EOL;
                    }
					exit;

            }
		}
	}
	
	private function callSendVideoApi($params){
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
				CURLOPT_URL => $this->botApi."/sendVideo",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $params,
				CURLOPT_SSL_VERIFYPEER => false
			)
		);
		$response = curl_exec($curl);
		//print_r($response);
		//print_r(curl_getinfo($curl));
		if($response === false){
			$err = curl_error($curl);
			echo '网络错误:'.print_r($err,true);
			exit;
		}

		curl_close($curl);
		
		return json_decode($response,true);
	}
}