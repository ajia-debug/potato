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
<body>

<div style="width: 90%;margin: 0 auto">
    <a class="layui-btn" href="addMenu.php?type=<?php echo $_GET['type']??1;?>">添加</a>
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
    layui.use(['table'], function () {
        var table = layui.table;
	

        table.render({
            elem: '#dataTable'
            , url: '../menu.php?type=<?php echo $_GET['type']??1;?>'
            , width: "100%"
            , height: 600
			, toolbar:"#toolBar"
            , cols: [[
                
                 {field: 'id', width: 80, title: 'ID', sort: true, fixed: 'left'}
                , {field: 'title', width: 200, title: '按钮名字'}
                , {field: 'link', width: 400, title: '按钮链接'}
				, {field: 'sort', width: 200, title: '排序'}
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
                        url:"../menu.php?op=del&type=<?php echo $_GET['type']??1;?>",
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
                window.location.href='editMenu.php?id='+obj.data.id+'&type=<?php echo $_GET['type']??1;?>';
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