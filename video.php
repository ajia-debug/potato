<?php
$op = $_GET['op']??'index';
class Video{

    public $db = null;
    public function __construct()
    {
        $this->db = new PDO('sqlite:./db/Db');
    }

    public function index(){
        $stmt = $this->db->prepare('SELECT * FROM video order by add_time desc');
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($data as &$d){
			$stmt = $this->db->prepare('SELECT menu.title,menu.link FROM menu left join menu_r on menu.id=menu_r.menu_id where menu_r.video_id=?');
			$stmt->execute([$d['id']]);
			$d['menu'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

        $stmt = $this->db->prepare('SELECT count(*) as c FROM video');
        $stmt->execute();
        $c = $stmt->fetch(PDO::FETCH_ASSOC);
        $json = [
            'code' => 0,
            'count' => $c['c'],
            'data' => $data
        ];
        $this->json($json);
    }
    public function save(){
		$videoes = $_POST['videoes'];
		$title = $_POST['title']??'';
		foreach($videoes as $videoObj){
			$data = [
				'title' => $title,
				'button' => $_POST['button']??'',
				'path' => $videoObj[0],
				'add_time' => date('Y-m-d H:i:s'),
				'send_times' => 0
			];
			if(empty($title)){
				$split = explode('.',basename($videoObj[1]));
				$data['title'] = $split[0];
			}
			

			$sql = 'insert into video('.implode(',',array_keys($data)).') values('.implode(',',array_fill(0,count($data),'?')).')';
			$stmt = $this->db->prepare($sql);
			$bool = $stmt->execute(array_values($data));
			$videoId = $this->db->lastInsertId();
			if(!empty($_POST['menu_r'])){
				foreach($_POST['menu_r'] as $menu_id => $m){
					if($m == 'on'){
						$sql = 'insert into menu_r(video_id,menu_id) values(?,?)';
						$stmt = $this->db->prepare($sql);
						$bool = $stmt->execute([
							$videoId,
							$menu_id
						]);
					}
				}
			}
		}
		
        
        $this->json(['code'=>$bool?0:-1,'msg'=>'添加成功']);
    }

    public function edit(){
        $data = [
            'title' => $_POST['title']??'',
            'button' => $_POST['button']??'',
            'path' => $_POST['videoUrl']??'',
        ];

        $sql = 'update video set title=?,button=?,path=? where id=?';
        $stmt = $this->db->prepare($sql);
        $bool = $stmt->execute([
             $_POST['title']??'',
             $_POST['button']??'',
             $_POST['videoUrl']??'',
            $_POST['id']
        ]);
		
		//删除原来菜单
		$sql = 'delete from menu_r where video_id=?';
        $stmt = $this->db->prepare($sql);
        $bool = $stmt->execute([
            $_POST['id']
        ]);
		//菜单关联
		foreach($_POST['menu_r'] as $menu_id => $valiable){
			if($valiable == 'on'){
				$sql = 'insert into menu_r(video_id,menu_id) values(?,?)';
				$stmt = $this->db->prepare($sql);
				$bool = $stmt->execute([
					$_POST['id'],
					$menu_id
				]);
			}
		}
		
        $this->json(['code'=>$bool?0:-1,'msg'=>'更新成功']);
    }

    public function del(){
        $stmt = $this->db->prepare('SELECT * FROM video where id=?');
        $stmt->execute([$_POST['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        @unlink(dirname(__FILE__).'/'.$data['path']);

        $stmt = $this->db->prepare('delete from video where id=?');
        $stmt->execute([$_POST['id']]);



        $json = [
            'code' => 0,
            'msg' => 'ok'
        ];
        $this->json($json);
    }

    public function json($data){
        echo json_encode($data,JSON_UNESCAPED_UNICODE);exit;
    }
    public function __call($name,$args){
        print_r($name);
        exit;
    }
}
$video = new Video();
$video->$op();