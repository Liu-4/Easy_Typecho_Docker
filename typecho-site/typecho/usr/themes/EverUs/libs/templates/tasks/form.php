<!-- 表单模板 -->
<script type="text/html" id="task-toolbar">
    <div class="layui-btn-container">
        <div class="layui-btn layui-btn-sm" lay-event="add">
            <i class="layui-icon">&#xe654;</i>新增任务
        </div>
        <div class="layui-btn layui-btn-sm layui-btn-danger" lay-event="tips">
            <i class="layui-icon">&#xe702;</i>操作提示
        </div>
    </div>
</script>

<!-- Task操作列模板 -->
<script type="text/html" id="task-action-tpl">
    <div class="layui-btn-container">
        <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
        {{# if(d.status == 0){ }}
            <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="complete">完成</a>
        {{# } else { }}
            <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="uncomplete">待办</a>
        {{# } }}
        <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">删除</a>
    </div>
</script>

<!-- Task表单模板 -->
<script type="text/html" id="task-form-tpl">
    <form class="layui-form" id="task-form" lay-filter="task-form" style="padding: 15px 20px 0 0;">
        <input type="hidden" name="id">
        <div class="layui-form-item">
            <label class="layui-form-label">时间线</label>
            <div class="layui-input-block">
                <input type="text" name="timeline" required lay-verify="required" placeholder="请输入时间线（如：2024-2025）" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">任务标题</label>
            <div class="layui-input-block">
                <input type="text" name="title" required lay-verify="required" placeholder="请输入任务标题" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">任务描述</label>
            <div class="layui-input-block">
                <textarea name="description" placeholder="请输入任务详细描述" class="layui-textarea" rows="3"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">证明链接</label>
            <div class="layui-input-block">
                <input type="url" name="proof" placeholder="https://example.com/proof" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">任务状态</label>
            <div class="layui-input-block">
                <select name="status">
                    <option value="0">待办</option>
                    <option value="1">已完成</option>
                </select>
            </div>
        </div>
    </form>
</script>

<!-- 状态列显示模板 -->
<script type="text/html" id="status-tpl">
    {{# if(d.status == 1){ }}
        <span class="layui-badge layui-bg-green">已完成</span>
    {{# } else { }}
        <span class="layui-badge layui-bg-orange">待办</span>
    {{# } }}
</script>

<!-- 证明链接列模板 -->
<script type="text/html" id="proof-tpl">
    {{# if(d.proof){ }}
        <a href="{{ d.proof }}" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">查看证明</a>
    {{# } else { }}
        <span class="layui-badge layui-badge-gray">无证明</span>
    {{# } }}
</script>