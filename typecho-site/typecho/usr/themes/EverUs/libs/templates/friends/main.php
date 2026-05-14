<!-- 主界面 -->
<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php $form->addItem(new EchoHtml('
<!-- 友链管理内容区 -->
<div class="friend-management">
    <!-- 友链数据表格 -->
    <table id="friend-table" lay-filter="friend-table"></table>
</div>
')); ?>

<!-- 引入工具栏模板 -->
<?php if (file_exists(__DIR__ . '/form.php')): ?>
    <?php include 'form.php'; ?>
<?php endif; ?>