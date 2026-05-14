<!-- 表单模板 -->
    <!-- 工具栏模板 -->
    <script type="text/html" id="friend-toolbar">
        <div class="layui-btn-container">
            <div class="layui-btn layui-btn-sm" lay-event="add">
                <i class="layui-icon">&#xe654;</i>新增友链
            </div>
            <div class="layui-btn layui-btn-sm layui-btn-danger" lay-event="tips">
                <i class="layui-icon">&#xe702;</i>操作提示
            </div>
        </div>
    </script>
    
    <!-- 操作列模板 -->
    <script type="text/html" id="friend-action-tpl">
        <div class="layui-btn-container">
            <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="visit">访问</a>
            <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">删除</a>
        </div>
    </script>

    <!-- 表单元模板 -->
    <script type="text/html" id="friend-form-tpl">
        <form class="layui-form" id="friend-form" lay-filter="friend-form" style="padding: 15px 20px 0 0;">
            <input type="hidden" name="id">
            <div class="layui-form-item">
                <label class="layui-form-label">网站标题</label>
                <div class="layui-input-block">
                    <input type="text" name="title" required lay-verify="required" placeholder="请输入友链网站标题" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">网站链接</label>
                <div class="layui-input-block">
                    <input type="url" name="link" required lay-verify="required|url" placeholder="https://example.com" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">头像链接</label>
                <div class="layui-input-block">
                    <input type="url" name="avatar" lay-verify="url" placeholder="https://example.com/avatar.png" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">网站描述</label>
                <div class="layui-input-block">
                    <textarea name="description" placeholder="请输入网站描述" class="layui-textarea" rows="3"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">rss订阅</label>
                <div class="layui-input-block">
                    <textarea name="notes" placeholder="请输入rss订阅链接" class="layui-textarea" rows="2"></textarea>
                </div>
            </div>
        </form>
    </script>

    <!-- 链接列模板 -->
    <script type="text/html" id="link-tpl">
        {{# if(d.link){ }}
            <a href="{{ d.link }}" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">访问链接</a>
        {{# } else { }}
            <span class="layui-badge layui-badge-gray">无链接</span>
        {{# } }}
    </script>

    <!-- 头像列模板 -->
    <script type="text/html" id="avatar-tpl">
        {{# if(d.avatar){ }}
            <img src="{{ d.avatar }}" class="avatar-img" onerror="this.src='//via.placeholder.com/40x40?text=Avatar'" style="width:40px;height:40px;border-radius:50%;">
        {{# } else { }}
            <div class="avatar-placeholder" style="width:40px;height:40px;border-radius:50%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
                <span style="color:#999;font-size:12px;">暂无</span>
            </div>
        {{# } }}
    </script>