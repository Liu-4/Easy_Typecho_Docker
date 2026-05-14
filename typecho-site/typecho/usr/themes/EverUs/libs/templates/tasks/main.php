<!-- 主界面 -->
<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $form->addItem(new EchoHtml('
<div class="task-management">
    <table id="task-table" lay-filter="task-table"></table>
</div>
')); ?>
<!-- 引入工具栏模板 -->
<?php if (file_exists(__DIR__ . '/form.php')): ?>
    <?php include 'form.php'; ?>
<?php endif; ?>