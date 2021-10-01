<?php
define('AUTH_CORE',1);
require_once 'core.php';
$db = new PDO('sqlite:../db/Db');

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
        <label class="layui-form-label">视频统一标题(选填)</label>
        <div class="layui-input-block">
            <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="视频统一标题(选填,不填读取各个上传视频的名字)" class="layui-input" value="">
        </div>
    </div>
    
	
	<div class="layui-form-item">
		<label class="layui-form-label">按钮菜单</label>
		<div class="layui-input-block">
			<?php
			
			foreach($menues as $menu){
				
				echo '<input type="checkbox" name="menu_r['.$menu['id'].']" title="'.$menu['title'].'">';
				
			}
			?>
		  
		</div>
	  </div>
	
	
    
	
	<div class="layui-form-item">
		<div class="layui-input-block">
		<div class="layui-upload">
		  <button type="button" class="layui-btn layui-btn-normal" id="testList">点击上传多个视频</button> 
		  <div class="layui-upload-list" style="max-width: 1000px;">
			<table class="layui-table">
			  <colgroup>
				<col>
				<col width="150">
				<col width="260">
				<col width="150">
			  </colgroup>
			  <thead>
				<tr><th>文件名</th>
				<th>大小</th>
				<th>上传进度</th>
				<th>操作</th>
			  </tr></thead>
			  <tbody id="demoList"></tbody>
			</table>
		  </div>
		  <button type="button" class="layui-btn" id="testListAction">开始上传视频</button>
		</div>
		</div>
	</div> 
<hr/>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">保存全部</button>
        </div>
    </div>
</form>


<script src="./static/layui/layui.js" charset="utf-8"></script>
<script src="./static/jquery-3.6.0.min.js"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use(['form', 'layedit', 'laydate','upload','element'], function(){
        var form = layui.form
            ,layer = layui.layer
            ,layedit = layui.layedit
            ,upload = layui.upload
			,element=layui.element
			,videoes=[];
			
			


		//演示多文件列表
		var uploadListIns = upload.render({
			elem: '#testList'
			,elemList: $('#demoList') //列表元素对象
			,url: '../upload.php' //此处用的是第三方的 http 请求演示，实际使用时改成您自己的上传接口即可。
			,accept: 'file'
			,multiple: true
			,number: 30
			,auto: false
			,bindAction: '#testListAction'
			,choose: function(obj){   
			  var that = this;
			  var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
			  //读取本地文件
			  obj.preview(function(index, file, result){
				var tr = $(['<tr id="upload-'+ index +'">'
				  ,'<td>'+ file.name +'</td>'
				  ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
				  ,'<td><div class="layui-progress" lay-filter="progress-demo-'+ index +'"><div class="layui-progress-bar" lay-percent=""></div></div></td>'
				  ,'<td>'
					,'<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
					,'<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
				  ,'</td>'
				,'</tr>'].join(''));
				
				//单个重传
				tr.find('.demo-reload').on('click', function(){
				  obj.upload(index, file);
				});
				
				//删除
				tr.find('.demo-delete').on('click', function(){
				  delete files[index]; //删除对应的文件
				  tr.remove();
				  uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
				});
				
				that.elemList.append(tr);
				element.render('progress'); //渲染新加的进度条组件
			  });
			}
			,done: function(res, index, upload){ //成功的回调
			  var that = this;
			  //if(res.code == 0){ //上传成功
				var tr = that.elemList.find('tr#upload-'+ index)
				,tds = tr.children();
				tds.eq(2).html('<span class="layui-bg-blue">上传成功</span>')
				tds.eq(3).html(''); //清空操作
				delete this.files[index]; //删除文件队列已经上传成功的文件
				videoes.push([res.url,res.alias]);
				return;
			  //}
			  this.error(index, upload);
			}
			,allDone: function(obj){ //多文件上传完毕后的状态回调
			  console.log(obj)
			}
			,error: function(index, upload){ //错误回调
			  var that = this;
			  var tr = that.elemList.find('tr#upload-'+ index)
			  ,tds = tr.children();
			  tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
			}
			,progress: function(n, elem, e, index){ //注意：index 参数为 layui 2.6.6 新增
			  element.progress('progress-demo-'+ index, n + '%'); //执行进度条。n 即为返回的进度百分比
			}
		});
		
        

        //创建一个编辑器
        var editIndex = layedit.build('LAY_demo_editor');

        


        //监听提交
        form.on('submit(demo1)', function(data){
			if(videoes.length == 0){
				layer.msg("你必须上传至少一个视频");
				return false;
			}
			data.field.videoes = videoes;
            $.ajax({
                type:"post",
                url:"../video.php?op=save",
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