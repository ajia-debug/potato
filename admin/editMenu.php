<?php

define('AUTH_CORE',1);
require_once 'core.php';

$db = new PDO('sqlite:../db/Db');
$stmt = $db->prepare('SELECT * FROM menu where id=?');
$stmt->execute([$_GET['id']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="./static/layui/css/layui.css" media="all">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>
<body>
<div class="layui-item" style="padding-top:50px"></div>
<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">按钮标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input" value="<?php echo $data['title'];?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">按钮链接</label>
        <div class="layui-input-block">
			<input type="text" name="link" value="<?php echo $data['link'];?>" lay-verify="url" autocomplete="off" placeholder="请输入链接" class="layui-input">
        </div>
    </div>
	<div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block">
            <input type="text" name="sort" lay-verify="number" autocomplete="off" placeholder="1为最前" value="<?php echo $data['sort'];?>" class="layui-input">
        </div>
    </div>
    

    <div class="layui-form-item">
        <div class="layui-input-block">
			<input type="hidden" name="id"  value="<?php echo $data['id'];?>"/>
			<input type="hidden" name="type"  value="<?php echo $_GET['type']??1;?>"/>
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
        </div>
    </div>
</form>


<script src="./static/layui/layui.js" charset="utf-8"></script>
<script src="./static/jquery-3.6.0.min.js"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use(['form', 'layedit', 'laydate','upload'], function(){
        var form = layui.form
            ,layer = layui.layer;
        //自定义验证规则
        form.verify({
            title: function(value){
                if(value.length < 1){
                    return '视频标题必须填写';
                }
            }
            
        });


        //监听提交
        form.on('submit(demo1)', function(data){
            $.ajax({
                type:"post",
                url:"../menu.php?op=edit",
                data:data.field,
                dataType:'json',
                success:function (response) {
                    console.log(response);
                    if(response.code === 0){
                        layer.alert(response.msg,function(){
                            window.location.href = 'menuList.php?type=<?php echo $_GET['type']??1;?>'
                        });
                    }else{
                        layer.msg('添加失败')
                    }
                }

            })
            return false;
        });

    });
</script>
</body>
</html>