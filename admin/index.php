<?php
define('AUTH_CORE',1);
require_once 'core.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>资源管理系统</title>
  <link rel="stylesheet" href="./static/layui/css/layui.css">
</head>
<body>
<div class="layui-layout layui-layout-admin">
  <div class="layui-header">
    <div class="layui-logo layui-hide-xs layui-bg-black">资源管理</div>
    
  </div>
  
  <div class="layui-side layui-bg-black">
    <div class="layui-side-scroll">
      <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
      <ul class="layui-nav layui-nav-tree" lay-filter="test">
        <li class="layui-nav-item"><a href="menuList.php" target="adminshow">菜单管理</a></li>
        <li class="layui-nav-item"><a href="videoList.php" target="adminshow">视频管理</a></li>
		<li class="layui-nav-item"><a href="menuList.php?type=2" target="adminshow">公共底部菜单</a></li>
      </ul>
    </div>
  </div>
  
  <div class="layui-body" >
    <!-- 内容主体区域 -->
    <iframe src="menuList.php" width="100%" border="0" cellspacing="0" height="100%" name="adminshow"></iframe>
  </div>
  
  <div class="layui-footer">
    <!-- 底部固定区域 -->
    
  </div>
</div>
<script src="./static/layui/layui.js"></script>
<script>
//JS 
layui.use(['element', 'layer', 'util'], function(){
  
  
});
</script>
</body>
</html>