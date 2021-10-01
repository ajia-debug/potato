<?php
define('AUTH_CORE',1);
require_once 'core.php';
$db = new PDO('sqlite:../db/Db');
$stmt = $db->prepare('SELECT * FROM video where id=?');
$stmt->execute([$_GET['id']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
//查询菜单关联
$stmt = $db->prepare('SELECT menu_id FROM menu_r where video_id=?');
$stmt->execute([$_GET['id']]);
$menu_r = $stmt->fetchALL(PDO::FETCH_ASSOC);
$myMenu = [];
foreach($menu_r as $m){
	$myMenu[] = $m['menu_id'];
}
$menu_r = $menu_r ? array_values($menu_r) : [];
//全部菜单
$stmt = $db->prepare('SELECT * FROM menu where type=1 order by sort asc,id desc');
$stmt->execute();
$menues = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<div class="layui-item" style="padding-top:20px"></div>
<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">视频标题</label>
        <div class="layui-input-block">
            <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input" value="<?php echo $data['title'];?>">
        </div>
    </div>
    
	
	<div class="layui-form-item">
		<label class="layui-form-label">按钮菜单</label>
		<div class="layui-input-block">
			<?php
			
			foreach($menues as $menu){
				if(in_array($menu['id'],$myMenu)){
					echo '<input type="checkbox" name="menu_r['.$menu['id'].']" title="'.$menu['title'].'" checked="">';
				}else{
					echo '<input type="checkbox" name="menu_r['.$menu['id'].']" title="'.$menu['title'].'">';
				}
			}
			?>
		  
		</div>
	  </div>
	
	
    <div class="layui-form-item">
        <label class="layui-form-label">视频</label>
        <div class="layui-input-block">
            <input type="hidden" lay-verify="videoUrl" name="videoUrl" id="videoUrl" value="<?php echo $data['path'];?>"/>
            <input type="hidden" name="id"  value="<?php echo $data['id'];?>"/>
            <button type="button" class="layui-btn" id="test5"><i class="layui-icon"></i>上传视频</button>
            <br/>
            <video id="previewVideo" src="<?php echo '/'.$data['path'];?>" width="500" height="300" controls></video>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
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
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload;


        upload.render({
            elem: '#test5'
            ,url: '../upload.php' //改成您自己的上传接口
            ,accept: 'video' //视频
            // ,auto: false
            //,multiple: true
            // ,bindAction: '#test9'
            ,before: function(obj){
                //预读本地文件示例，不支持ie8
                console.log(obj);
                obj.preview(function(index, file, result){
                    $('#previewVideo').attr('src', result); //图片链接（base64）
                    $('#previewVideo').get(0).play();
                });
            }
            ,done: function(res){
                if(res.error != 0) {
                    layer.alert(res.message);
                }else{
                    $('#videoUrl').val(res.url);
                    layer.alert('上传成功');
                }
                console.log(res)
            }
        });

        //创建一个编辑器
        var editIndex = layedit.build('LAY_demo_editor');

        //自定义验证规则
        form.verify({
            title: function(value){
                if(value.length < 1){
                    return '视频标题必须填写';
                }
            }
            ,videoUrl: function(value){
                if(value.length < 1){
                    return '视频不能为空';
                }
            }
        });


        //监听提交
        form.on('submit(demo1)', function(data){
            $.ajax({
                type:"post",
                url:"../video.php?op=edit",
                data:data.field,
                dataType:'json',
                success:function (response) {
                    console.log(response);
                    if(response.code === 0){
                        layer.alert(response.msg,function(){
                            window.location.href = 'videoList.php'
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