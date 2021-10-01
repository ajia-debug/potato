<?php
$return = [
    'error' => 0,
    'message' => '',
    'url' => '',
];
// 允许上传的图片后缀
$allowedExts = array('mp4','mov','wmv','avi','flv','mpeg','ts');
$temp = explode(".", $_FILES["file"]["name"]);
$extension = strtolower(end($temp));     // 获取文件后缀名
if (($_FILES["file"]["size"] < 1024*1024*50)   // 小于 20 mb
    && in_array($extension, $allowedExts))
{
    if ($_FILES["file"]["error"] > 0)
    {
        $return['message'] = $_FILES["file"]["error"];
        $return['error'] = -1;
    }
    else
    {
        // 判断当前目录下的 upload 目录是否存在该文件
        // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
        if(!is_dir('./upload')){
            mkdir('upload',0777);
        }
        $path = "upload/" . md5($temp[0].mt_rand(1000,9999)).'.'.$extension;
        move_uploaded_file($_FILES["file"]["tmp_name"], $path);
        $return['url'] =  $path;
		$return['alias'] =  $_FILES["file"]["name"];
    }
}
else
{
    $return['message'] = "非法的文件格式";
    $return['error'] = -1;
}
echo json_encode($return,JSON_UNESCAPED_UNICODE);exit;
print_r($_FILES);exit;

$db = new PDO('sqlite:./db/Db');
$stmt = $db->prepare('SELECT * FROM video');
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($data);