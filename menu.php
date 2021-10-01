<?php
$op = $_GET['op']??'index';
class Menu{

    public $db = null;
    public function __construct()
    {
        $this->db = new PDO('sqlite:./db/Db');
    }

    public function index(){
        $stmt = $this->db->prepare('SELECT * FROM menu where type=? order by sort asc,id desc');
        $stmt->execute([$_GET['type']??1]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare('SELECT count(*) as c FROM menu');
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
        $data = [
            'title' => $_POST['title']??'',
            'link' => $_POST['link']??'',
			'sort' => $_POST['sort'] ?? 1,
			'type' => $_POST['type'] ?? 1,
        ];

        $sql = 'insert into menu('.implode(',',array_keys($data)).') values('.implode(',',array_fill(0,count($data),'?')).')';
        $stmt = $this->db->prepare($sql);
        $bool = $stmt->execute(array_values($data));
        $this->json(['code'=>$bool?0:-1,'msg'=>'添加成功']);
    }

    public function edit(){
        $sql = 'update menu set title=?,link=?,sort=? where id=?';
        $stmt = $this->db->prepare($sql);
		
        $bool = $stmt->execute([
             $_POST['title']??'',
             $_POST['link']??'',
			 $_POST['sort']??1,
            $_POST['id']
        ]);
        $this->json(['code'=>$bool?0:-1,'msg'=>'更新成功']);
    }

    public function del(){
        

        $stmt = $this->db->prepare('delete from menu where id=?');
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
$Menu = new Menu();
$Menu->$op();