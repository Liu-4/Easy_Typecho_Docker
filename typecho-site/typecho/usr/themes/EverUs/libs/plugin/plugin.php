<?php
    $form->addItem(new EchoHtml('
        <!-- 选项卡容器 -->
        <div class="tab-container">
            <!-- 选项卡按钮 -->
            <div class="tab-buttons">
                <div class="tab-btn active" data-tab="friend">友链管理</div>
                <div class="tab-btn" data-tab="work">作品管理</div>
                <div class="tab-btn" data-tab="task">任务管理</div>
            </div>

            <!-- 友链管理内容 -->
            <div id="friend-tab" class="tab-content active">
                <div class="stats-box">
                    <div class="stat-item">
                        <h3>友链总数</h3>
                        <span id="friend-total-count">0</span>
                    </div>
                </div>
                <table id="friend-table" lay-filter="friend-table"></table>
            </div>

            <!-- 作品管理内容 -->
            <div id="work-tab" class="tab-content">
                <div class="stats-box">
                    <div class="stat-item">
                        <h3>作品总数</h3>
                        <span id="work-total-count">0</span>
                    </div>
                </div>
                <table id="work-table" lay-filter="work-table"></table>
            </div>

            <!-- 任务管理内容 -->
            <div id="task-tab" class="tab-content">
                <div class="stats-box">
                    <div class="stat-item">
                        <h3>任务总数</h3>
                        <span id="task-total-count">0</span>
                    </div>
                    <div class="stat-item">
                        <h3>已完成</h3>
                        <span id="task-completed-count">0</span>
                    </div>
                    <div class="stat-item">
                        <h3>未完成</h3>
                        <span id="task-uncompleted-count">0</span>
                    </div>
                </div>
                <table id="task-table" lay-filter="task-table"></table>
            </div>
        </div>

        <!-- ================= 模板区域 ================= -->
        <!-- 所有模板脚本集中放置，避免与内容混合 -->

        <!-- 工具栏模板 -->
        <script type="text/html" id="friend-toolbar">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="add"><i class="layui-icon">&#xe654;</i>新增</button>
                <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="tips"><i class="layui-icon">&#xe702;</i>提示</button>
            </div>
        </script>

        <script type="text/html" id="task-toolbar">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="add"><i class="layui-icon">&#xe654;</i>新增</button>
                <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="tips"><i class="layui-icon">&#xe702;</i>提示</button>
            </div>
        </script>

        <script type="text/html" id="work-toolbar">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" lay-event="add"><i class="layui-icon">&#xe654;</i>新增作品</button>
                <button class="layui-btn layui-btn-sm layui-btn-danger" lay-event="tips"><i class="layui-icon">&#xe702;</i>提示</button>
            </div>
        </script>

        <!-- 表单模板 -->
        <script type="text/html" id="friend-form-tpl">
            <form class="layui-form" id="friend-form" lay-filter="friend-form" style="padding: 15px 20px 0 0;">
                <input type="hidden" name="id">
                <div class="layui-form-item">
                    <label class="layui-form-label">标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" required lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">链接</label>
                    <div class="layui-input-block">
                        <input type="text" name="link" required lay-verify="required" placeholder="请输入链接" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">头像</label>
                    <div class="layui-input-block">
                        <input type="text" name="avatar" required lay-verify="required" placeholder="请输入头像链接" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <textarea name="description" placeholder="请输入描述" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">备注</label>
                    <div class="layui-input-block">
                        <textarea name="notes" placeholder="请输入备注" class="layui-textarea"></textarea>
                    </div>
                </div>
            </form>
        </script>

        <script type="text/html" id="task-form-tpl">
            <form class="layui-form" id="task-form" lay-filter="task-form" style="padding: 15px 20px 0 0;">
                <input type="hidden" name="id">
                <div class="layui-form-item">
                    <label class="layui-form-label">时间线</label>
                    <div class="layui-input-block">
                        <input type="text" name="timeline" required lay-verify="required" placeholder="请输入时间线" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" required lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <textarea name="description" placeholder="请输入描述" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">证明</label>
                    <div class="layui-input-block">
                        <input type="text" name="proof" placeholder="请输入文章链接" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">状态</label>
                    <div class="layui-input-block">
                        <select name="status">
                            <option value="0">待办</option>
                            <option value="1">完成</option>
                        </select>
                    </div>
                </div>
            </form>
        </script>

        <script type="text/html" id="work-form-tpl">
            <form class="layui-form" id="work-form" lay-filter="work-form" style="padding: 15px 20px 0 0;">
                <input type="hidden" name="id">
                <div class="layui-form-item">
                    <label class="layui-form-label">网站名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" required lay-verify="required" placeholder="请输入网站名称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">网站链接</label>
                    <div class="layui-input-block">
                        <input type="text" name="url" required lay-verify="required|url" placeholder="https://example.com" class="layui-input">
                    </div>
                </div>
            </form>
        </script>

        <!-- 行操作按钮模板 -->
        <script type="text/html" id="friend-action-tpl">
            <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="visit">访问</a>
            <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">删除</a>
        </script>

        <script type="text/html" id="task-action-tpl">
            <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
            {{# if(d.status == 0){ }}
                <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="complete">完成</a>
            {{# } else { }}
                <a class="layui-btn layui-btn-xs layui-btn-warm" lay-event="uncomplete">待办</a>
            {{# } }}
            <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">删除</a>
        </script>

        <script type="text/html" id="work-action-tpl">
            <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="visit">访问</a>
            <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="delete">删除</a>
        </script>

        <!-- 状态/链接/头像等列显示模板 -->
        <script type="text/html" id="status-tpl">
            {{# if(d.status == 1){ }}
                <span class="status-badge status-completed">已完成</span>
            {{# } else { }}
                <span class="status-badge status-pending">待办</span>
            {{# } }}
        </script>

        <script type="text/html" id="link-tpl">
            <a href="{{ d.link }}" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">访问</a>
        </script>

        <script type="text/html" id="avatar-tpl">
            <img src="{{ d.avatar }}" class="avatar-img" onerror="this.src=\'https://via.placeholder.com/40\'">
        </script>
    '));
?>