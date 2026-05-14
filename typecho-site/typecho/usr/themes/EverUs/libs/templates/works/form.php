<!-- 表单模板 -->
<!-- Works工具栏模板 -->
<script type="text/html" id="work-toolbar">
    <div class="layui-btn-container">
        <div class="layui-btn layui-btn-sm" lay-event="add">
            <i class="layui-icon">&#xe654;</i>新增作品
        </div>
        <div class="layui-btn layui-btn-sm layui-btn-danger" lay-event="tips">
            <i class="layui-icon">&#xe702;</i>操作提示
        </div>
    </div>
</script>

<!-- Works操作列模板 -->
<script type="text/html" id="work-action-tpl">
    <div class="layui-btn-container">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="visit">访问</a>
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">删除</a>
    </div>
</script>

<!-- Works表单模板 -->
<script type="text/html" id="work-form-tpl">
    <form class="layui-form" id="work-form" lay-filter="work-form" style="padding: 15px 20px 0 0;">
        <input type="hidden" name="id">
        <div class="layui-form-item">
            <label class="layui-form-label">网站名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" required lay-verify="required" placeholder="请输入网站名称" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">网站链接</label>
            <div class="layui-input-block">
                <input type="url" name="url" required lay-verify="required|url" placeholder="https://example.com" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">网站描述</label>
            <div class="layui-input-block">
                <textarea name="description" placeholder="请输入网站描述（可选）" class="layui-textarea" rows="2"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">分类标签</label>
            <div class="layui-input-block">
                <input type="text" name="category" placeholder="如：个人博客、作品集等（可选）" autocomplete="off" class="layui-input">
            </div>
        </div>
    </form>
</script>

<!-- 链接列显示模板 -->
<script type="text/html" id="work-link-tpl">
    {{# if(d.url){ }}
        <a href="{{ d.url }}" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">访问网站</a>
    {{# } else { }}
        <span class="layui-badge layui-badge-gray">无链接</span>
    {{# } }}
</script>