<?php
define('AUTH_CORE',1);
require_once 'core.php';
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
<style>
.layui-table-cell{
	height:auto;
	line-height:40px;
	word-break:break-all;
	text-overflow:inherit;
	white-space:normal;
}
</style>
<body>

<div style="width: 90%;margin: 0 auto">
    <a class="layui-btn" href="addVideo.php">添加</a>
    <div class="layui-row">
        <table class="layui-table" id="dataTable" lay-filter="dataTable"></table>
    </div>
</div>
<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>


<script src="./static/layui/layui.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use('table', function () {
        var table = layui.table;

        table.render({
            elem: '#dataTable'
            , url: '../video.php'
            , width: "100%"
            , height: 600
            , cols: [[
                
                  {field: 'title', width: 350, title: '标题'}
                , {field: 'menu', width:600, title: '按钮配置',templet:function(d){
					
					if(d.menu == null){
						return;
					}
					var str = '';
					for(var i in d.menu){
						str+='<span class="layui-btn layui-btn-xs">'+d.menu[i].title+'</span>';
					}
					return str;
				}}
                , {title: '操作', width:200,fixed: 'right', toolbar: '#barDemo'}
            ]]
            , page: true
        });

        //监听表格复选框选择
        table.on('checkbox(dataTable)', function (obj) {
            console.log(obj)
        });
        //监听工具条
        table.on('tool(dataTable)', function (obj) {
            var data = obj.data;
            console.log(obj);
            if (obj.event === 'detail') {
                layer.msg('ID：' + data.id + ' 的查看操作');
            } else if (obj.event === 'del') {
                layer.confirm('真的删除行么', function (index) {
                    $.ajax({
                        type:"post",
                        url:"../video.php?op=del",
                        data:{"id":obj.data.id},
                        dataType:'json',
                        success:function (response) {
                            console.log(response);
                            if(response.code === 0){
                                obj.del();
                            }else{
                                layer.msg('删除失败')
                            }
                        }

                    })
                    layer.close(index);
                });
            } else if (obj.event === 'edit') {
                window.location.href='edit.php?id='+obj.data.id;
            }
        });


        var $ = layui.$, active = {
            getCheckData: function () { //获取选中数据
                var checkStatus = table.checkStatus('idTest')
                    , data = checkStatus.data;
                layer.alert(JSON.stringify(data));
            }
            , getCheckLength: function () { //获取选中数目
                var checkStatus = table.checkStatus('idTest')
                    , data = checkStatus.data;
                layer.msg('选中了：' + data.length + ' 个');
            }
            , isAll: function () { //验证是否全选
                var checkStatus = table.checkStatus('idTest');
                layer.msg(checkStatus.isAll ? '全选' : '未全选')
            }
        };

        $('.demoTable .layui-btn').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });
    });
</script>

</body>
</html>