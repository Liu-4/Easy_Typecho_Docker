<!-- Works管理内容区 -->
<?php $form->addItem(new EchoHtml('
<div class="work-management">
    <!-- Works数据表格 -->
    <table id="work-table" lay-filter="work-table"></table>
</div>
')); ?>

<!-- 引入工具栏模板 -->
<?php if (file_exists(__DIR__ . '/form.php')): ?>
    <?php include 'form.php'; ?>
<?php endif; ?>