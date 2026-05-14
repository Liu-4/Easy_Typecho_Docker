<!-- 选项卡导航 -->
<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $form->addItem(new EchoHtml('
<!-- 后台管理选项卡导航 -->
<ul class="layui-tab-title">
    <li class="layui-this" data-tab="history">
        <i class="layui-icon">&#xe656;</i>更新历史
    </li>
    <li class="layui-this" data-tab="site">
        <i class="layui-icon">&#xe628;</i>站点设置
    </li>
    <li class="layui-this" data-tab="friend">
        <i class="layui-icon">&#xe658;</i>友链管理
    </li>
    <li class="layui-this" data-tab="work">
        <i class="layui-icon">&#xe60a;</i>作品管理
    </li>
    <li class="layui-this" data-tab="task">
        <i class="layui-icon">&#xe623;</i>任务管理
    </li>
    <li class="layui-this" data-tab="email">
        <i class="layui-icon">&#xe609;</i>邮箱配置
    </li>
    <li class="layui-this" data-tab="music">
        <i class="layui-icon">&#xe652;</i>音乐配置
    </li>
</ul>
'));