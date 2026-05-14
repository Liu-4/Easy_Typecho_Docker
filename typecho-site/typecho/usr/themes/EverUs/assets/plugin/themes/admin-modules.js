// 确保Layui正确加载
if (typeof layui === 'undefined') {
    console.error('Layui未加载，请检查资源路径');
} else {
    layui.use(['table', 'form', 'layer', 'jquery'], function(){
        var table = layui.table;
        var form = layui.form;
        var layer = layui.layer;
        var $ = layui.$;
        
        // 初始化友链系统
        initFriendSystem(table, form, layer, $);
        // 初始化任务系统
        initTaskSystem(table, form, layer, $);
        // 初始化作品系统
        initWorkSystem(table, form, layer, $)
    });
}

// 友链管理系统
function initFriendSystem(table, form, layer, $) {
    let friendData = [];
    let friendTableIns = null;
    let currentEditId = null;
    
    // 核心工具函数：确保存储字段存在
    function ensureFriendField() {
        let field = document.querySelector('textarea[name="friend"]');
        if (!field) {
            field = document.createElement('textarea');
            field.name = 'friend';
            field.style.display = 'none';
            document.body.appendChild(field);
        }
        return field;
    }
    
    // 加载友链数据
    function loadFriendData() {
        try {
            const field = ensureFriendField();
            if (!field.value) {
                friendData = [];
                updateFriendCount();
                return;
            }
            
            friendData = field.value.split('\n')
                .filter(line => line.trim())
                .map((line, idx) => {
                    const parts = line.split('||').map(p => p.trim());
                    return {
                        id: idx + 1,
                        title: parts[0] || '',
                        link: parts[1] || '',
                        avatar: parts[2] || '',
                        description: parts[3] || '',
                        notes: parts[4] || ''
                    };
                });
            
            updateFriendCount();
            return friendData;
        } catch (e) {
            console.error('加载友链数据失败:', e);
            layer.msg('友链数据加载失败', {icon: 2});
            return [];
        }
    }
    
    // 保存友链数据
    function saveFriendData() {
        try {
            const field = ensureFriendField();
            const dataStr = friendData.map(friend => 
                `${friend.title}||${friend.link}||${friend.avatar}||${friend.description}||${friend.notes}`
            ).join('\n');
            
            field.value = dataStr;
            return true;
        } catch (e) {
            console.error('保存友链数据失败:', e);
            layer.msg('友链数据保存失败', {icon: 2});
            return false;
        }
    }
    
    // 更新统计信息
    function updateFriendCount() {
        const total = friendData.length;
        $('#friend-total-count').text(total);
    }
    
    // 初始化表格
    function initFriendTable() {
        const data = loadFriendData();
        let tableHeight = window.innerHeight < 600 ? window.innerHeight - 300 : 400;
        
        // 销毁旧表格实例（如果存在）
        if (friendTableIns) {
            friendTableIns.reload({ data: data, height: tableHeight });
        } else {
            friendTableIns = table.render({
                elem: '#friend-table',
                height: tableHeight,
                data: data,
                page: true,
                limit: 10, // 每页显示数量
                limits: [10, 20, 50], // 每页条数选择
                toolbar: '#friend-toolbar',
                cols: [[
                    {field: 'avatar', title: '头像', width: 70, templet: '#avatar-tpl', align: 'center'},
                    {field: 'title', title: '标题', width: 120, align: 'center'},
                    {field: 'description', title: '描述', minWidth: 100, align: 'left'},
                    {field: 'link', title: '链接', width: 100, templet: '#link-tpl', align: 'center'},
                    {field: 'notes', title: '备注', minWidth: 100, align: 'left'},
                    {title: '操作', width: 180, toolbar: '#friend-action-tpl', align: 'center', fixed: 'right'}
                ]],
                done: function() {
                    updateFriendCount();
                }
            });
        }
    }
    
    // 打开友链编辑对话框
    function openFriendDialog(isEdit = false, friend = null) {
        currentEditId = isEdit ? friend.id : null;
        const formContent = document.getElementById('friend-form-tpl').innerHTML;
        let layerIndex;
        
        layerIndex = layer.open({
            type: 1,
            title: isEdit ? '编辑友链' : '新增友链',
            area: window.innerWidth < 768 ? ['92%', 'auto'] : ['500px', 'auto'],
            content: formContent,
            btn: ['确认', '取消'],
            btnAlign: 'c',
            skin: 'layui-layer-lan',
            success: function(layero) {
                form.render(null, 'friend-form');
                
                if (isEdit && friend) {
                    setTimeout(() => {
                        form.val('friend-form', {
                            id: friend.id.toString(),
                            title: friend.title,
                            link: friend.link,
                            avatar: friend.avatar,
                            description: friend.description,
                            notes: friend.notes
                        });
                    }, 50);
                }
            },
            yes: function(index) {
                const formData = form.val('friend-form');
                
                // 表单验证
                if (!formData.title.trim()) {
                    layer.tips('标题不能为空', $('input[name="title"]'), {tips: 1});
                    return false;
                }
                if (!formData.link.trim()) {
                    layer.tips('链接不能为空', $('input[name="link"]'), {tips: 1});
                    return false;
                }
                if (!formData.avatar.trim()) {
                    layer.tips('头像不能为空', $('input[name="avatar"]'), {tips: 1});
                    return false;
                }
                
                // 数据处理
                formData.id = formData.id ? parseInt(formData.id) : null;
                
                if (!isEdit) {
                    // 新增逻辑
                    const maxId = friendData.length ? Math.max(...friendData.map(f => f.id)) : 0;
                    formData.id = maxId + 1;
                    friendData.push(formData);
                } else {
                    // 编辑逻辑
                    const friendIdx = friendData.findIndex(f => f.id === currentEditId);
                    if (friendIdx !== -1) friendData[friendIdx] = formData;
                }
                
                // 保存并刷新
                if (saveFriendData()) {
                    initFriendTable();
                    layer.msg(isEdit ? '编辑成功' : '新增成功', {icon: 1, time: 1000});
                }
                
                layer.close(index);
            }
        });
    }
    
    // 表格行操作事件
    table.on('tool(friend-table)', function(obj) {
        const data = obj.data;
        const idx = friendData.findIndex(f => f.id === data.id);
        
        switch(obj.event) {
            case 'edit':
                openFriendDialog(true, data);
                break;
            case 'visit':
                if (data.link) {
                    window.open(data.link, '_blank');
                } else {
                    layer.msg('该友链没有有效的链接', {icon: 2});
                }
                break;
            case 'delete':
                layer.confirm('确定删除这条友链吗？', {
                    title: '删除确认',
                    btn: ['确认删除', '取消'],
                    btnAlign: 'c'
                }, function(confirmIndex) {
                    friendData.splice(idx, 1);
                    if (saveFriendData()) {
                        initFriendTable();
                        layer.msg('删除成功', {icon: 1, time: 800});
                    }
                    layer.close(confirmIndex);
                });
                break;
        }
    });
    
    // 工具栏事件
    table.on('toolbar(friend-table)', function(obj){
        switch(obj.event){
            case 'add':
                openFriendDialog(false);
                break;
            case 'tips':
                layer.alert('友链管理使用提示：<br>' +
                    '1. 点击"新增"按钮添加友链<br>' +
                    '2. 点击"访问"按钮可以查看友链网站<br>' +
                    '3. 点击"编辑"按钮可以修改友链信息<br>' +
                    '4. 点击"删除"按钮可以删除友链', {
                    title: '使用提示',
                    area: ['400px', 'auto'],
                    icon: 0
                });
                break;
        }
    });
    
    // 初始化系统
    initFriendTable();
}

// Task管理系统
function initTaskSystem(table, form, layer, $) {
    let taskData = [];
    let taskTableIns = null;
    let currentEditId = null;
    
    // 核心工具函数：确保存储字段存在
    function ensureTaskField() {
        let field = document.querySelector('textarea[name="compass"]');
        if (!field) {
            field = document.createElement('textarea');
            field.name = 'compass';
            field.style.display = 'none';
            document.body.appendChild(field);
        }
        return field;
    }
    
    // 加载Task数据
    function loadTaskData() {
        try {
            const field = ensureTaskField();
            if (!field.value) {
                taskData = [];
                updateTaskCount();
                return;
            }
            
            taskData = field.value.split('\n')
                .filter(line => line.trim())
                .map((line, idx) => {
                    const parts = line.split('||').map(p => p.trim());
                    return {
                        id: idx + 1,
                        timeline: parts[0] || '',
                        title: parts[1] || '',
                        description: parts[2] || '',
                        proof: parts[3] || '',
                        status: parts[4] ? parseInt(parts[4]) : 0
                    };
                });
            
            updateTaskCount();
        } catch (e) {
            console.error('加载任务数据失败:', e);
            layer.msg('任务数据加载失败', {icon: 2});
        }
    }
    
    // 保存Task数据
    function saveTaskData() {
        try {
            const field = ensureTaskField();
            const dataStr = taskData.map(task => 
                `${task.timeline}||${task.title}||${task.description}||${task.proof}||${task.status}`
            ).join('\n');
            
            field.value = dataStr;
            return true;
        } catch (e) {
            console.error('保存任务数据失败:', e);
            layer.msg('任务数据保存失败', {icon: 2});
            return false;
        }
    }
    
    // 更新统计信息
    function updateTaskCount() {
        const total = taskData.length;
        const completed = taskData.filter(task => task.status === 1).length;
        const uncompleted = total - completed;
        
        $('#task-total-count').text(total);
        $('#task-completed-count').text(completed);
        $('#task-uncompleted-count').text(uncompleted);
    }
    
    // 初始化表格
    function initTaskTable() {
        loadTaskData();
        let tableHeight = window.innerHeight < 600 ? window.innerHeight - 300 : 400;
        
        if (taskTableIns) {
            taskTableIns.reload({ data: taskData, height: tableHeight });
        } else {
            taskTableIns = table.render({
                elem: '#task-table',
                height: tableHeight,
                data: taskData,
                page: true,
                toolbar: '#task-toolbar',
                cols: [[
                    {field: 'timeline', title: '时间线', width: 120, align: 'center'},
                    {field: 'title', title: '标题', width: 150, align: 'center'},
                    {field: 'description', title: '描述', minWidth: 200, align: 'left'},
                    {field: 'proof', title: '证明', width: 120, templet: '#proof-tpl', align: 'center'},
                    {field: 'status', title: '状态', width: 100, templet: '#status-tpl', align: 'center'},
                    {title: '操作', width: 200, toolbar: '#task-action-tpl', align: 'center', fixed: 'right'}
                ]],
                done: function() {
                    updateTaskCount();
                }
            });
        }
    }
    
    // 打开Task编辑对话框
    function openTaskDialog(isEdit = false, task = null) {
        currentEditId = isEdit ? task.id : null;
        const formContent = document.getElementById('task-form-tpl').innerHTML;
        
        layer.open({
            type: 1,
            title: isEdit ? '编辑任务' : '新增任务',
            area: window.innerWidth < 768 ? ['92%', 'auto'] : ['500px', 'auto'],
            content: formContent,
            btn: ['确认', '取消'],
            btnAlign: 'c',
            skin: 'layui-layer-lan',
            success: function(layero, index) {
                form.render();
                
                if (isEdit && task) {
                    setTimeout(() => {
                        form.val('task-form', {
                            id: task.id.toString(),
                            timeline: task.timeline,
                            title: task.title,
                            description: task.description,
                            proof: task.proof,
                            status: task.status.toString()
                        });
                    }, 50);
                }
            },
            yes: function(index, layero) {
                const formData = form.val('task-form');
                
                // 表单验证
                if (!formData.timeline.trim()) {
                    layer.msg('时间线不能为空');
                    return false;
                }
                if (!formData.title.trim()) {
                    layer.msg('任务标题不能为空');
                    return false;
                }
                
                // 数据处理
                formData.id = formData.id ? parseInt(formData.id) : null;
                formData.status = parseInt(formData.status);
                
                if (!isEdit) {
                    // 新增逻辑
                    const maxId = taskData.length ? Math.max(...taskData.map(t => t.id)) : 0;
                    formData.id = maxId + 1;
                    taskData.push(formData);
                } else {
                    // 编辑逻辑
                    const taskIdx = taskData.findIndex(t => t.id === currentEditId);
                    if (taskIdx !== -1) taskData[taskIdx] = formData;
                }
                
                // 保存并刷新
                if (saveTaskData()) {
                    initTaskTable();
                    layer.msg(isEdit ? '编辑成功' : '新增成功', {icon: 1, time: 1000});
                }
                
                layer.close(index);
            }
        });
    }
    
    // 表格行操作事件
    table.on('tool(task-table)', function(obj) {
        const data = obj.data;
        const idx = taskData.findIndex(t => t.id === data.id);
        
        switch(obj.event) {
            case 'edit':
                openTaskDialog(true, data);
                break;
                
            case 'complete':
                taskData[idx].status = 1;
                if (saveTaskData()) {
                    initTaskTable();
                    layer.msg('任务标记为已完成', {icon: 1, time: 800});
                }
                break;
                
            case 'uncomplete':
                taskData[idx].status = 0;
                if (saveTaskData()) {
                    initTaskTable();
                    layer.msg('任务标记为待办', {icon: 0, time: 800});
                }
                break;
                
            case 'delete':
                layer.confirm('确定删除这个任务吗？', {
                    title: '删除确认',
                    btn: ['确认删除', '取消'],
                    btnAlign: 'c'
                }, function(confirmIndex) {
                    taskData.splice(idx, 1);
                    if (saveTaskData()) {
                        initTaskTable();
                        layer.msg('删除成功', {icon: 1, time: 800});
                    }
                    layer.close(confirmIndex);
                });
                break;
        }
    });
    
    // 工具栏事件
    table.on('toolbar(task-table)', function(obj){
        switch(obj.event){
            case 'add':
                openTaskDialog(false);
                break;
                
            case 'tips':
                layer.alert('任务管理使用提示：<br>' +
                    '1. 点击"新增"按钮添加任务<br>' +
                    '2. 点击"完成"按钮可以标记任务为已完成<br>' +
                    '3. 点击"待办"按钮可以将已完成任务标记为待办<br>' +
                    '4. 点击"编辑"按钮可以修改任务信息<br>' +
                    '5. 点击"删除"按钮可以删除任务', {
                    title: '使用提示',
                    area: ['400px', 'auto'],
                    icon: 0
                });
                break;
        }
    });
    
    // 初始化系统
    initTaskTable();
}

// Works管理系统
function initWorkSystem(table, form, layer, $) {
    let workData = [];
    let workTableIns = null;
    let currentEditId = null;
    
    // 核心工具函数：确保存储字段存在
    function ensureWorkField() {
        let field = document.querySelector('textarea[name="work"]');
        if (!field) {
            field = document.createElement('textarea');
            field.name = 'work';
            field.style.display = 'none';
            document.body.appendChild(field);
        }
        return field;
    }
    
    // 加载Works数据
    function loadWorkData() {
        try {
            const field = ensureWorkField();
            if (!field.value) {
                workData = [];
                updateWorkCount();
                return;
            }
            
            workData = field.value.split('\n')
                .filter(line => line.trim())
                .map((line, idx) => {
                    const parts = line.split('||').map(p => p.trim());
                    return {
                        id: idx + 1,
                        name: parts[0] || '',
                        url: parts[1] || '',
                        description: parts[2] || '',
                        category: parts[3] || ''
                    };
                });
            
            updateWorkCount();
        } catch (e) {
            console.error('加载作品数据失败:', e);
            layer.msg('作品数据加载失败', {icon: 2});
        }
    }
    
    // 保存Works数据
    function saveWorkData() {
        try {
            const field = ensureWorkField();
            const dataStr = workData.map(work => 
                `${work.name}||${work.url}||${work.description}||${work.category}`
            ).join('\n');
            
            field.value = dataStr;
            return true;
        } catch (e) {
            console.error('保存作品数据失败:', e);
            layer.msg('作品数据保存失败', {icon: 2});
            return false;
        }
    }
    
    // 更新统计信息
    function updateWorkCount() {
        const total = workData.length;
        $('#work-total-count').text(total);
    }
    
    // 初始化表格
    function initWorkTable() {
        loadWorkData();
        let tableHeight = window.innerHeight < 600 ? window.innerHeight - 300 : 400;
        
        if (workTableIns) {
            workTableIns.reload({ data: workData, height: tableHeight });
        } else {
            workTableIns = table.render({
                elem: '#work-table',
                height: tableHeight,
                data: workData,
                page: true,
                toolbar: '#work-toolbar',
                cols: [[
                    {field: 'name', title: '网站名称', width: 150, align: 'center'},
                    {field: 'url', title: '网站链接', width: 200, templet: '#work-link-tpl', align: 'center'},
                    {field: 'description', title: '描述', minWidth: 150, align: 'left'},
                    {field: 'category', title: '分类', width: 120, align: 'center'},
                    {title: '操作', width: 150, toolbar: '#work-action-tpl', align: 'center', fixed: 'right'}
                ]],
                done: function() {
                    updateWorkCount();
                }
            });
        }
    }
    
    // 打开Works编辑对话框
    function openWorkDialog(isEdit = false, work = null) {
        currentEditId = isEdit ? work.id : null;
        const formContent = document.getElementById('work-form-tpl').innerHTML;
        
        layer.open({
            type: 1,
            title: isEdit ? '编辑作品' : '新增作品',
            area: window.innerWidth < 768 ? ['92%', 'auto'] : ['500px', 'auto'],
            content: formContent,
            btn: ['确认', '取消'],
            btnAlign: 'c',
            skin: 'layui-layer-lan',
            success: function(layero, index) {
                form.render();
                
                if (isEdit && work) {
                    setTimeout(() => {
                        form.val('work-form', {
                            id: work.id.toString(),
                            name: work.name,
                            url: work.url,
                            description: work.description,
                            category: work.category
                        });
                    }, 50);
                }
            },
            yes: function(index, layero) {
                const formData = form.val('work-form');
                
                // 表单验证
                if (!formData.name.trim()) {
                    layer.msg('网站名称不能为空');
                    return false;
                }
                if (!formData.url.trim()) {
                    layer.msg('网站链接不能为空');
                    return false;
                }
                
                // URL格式验证
                const urlPattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
                if (!urlPattern.test(formData.url)) {
                    layer.msg('请输入有效的网站链接地址');
                    return false;
                }
                
                // 数据处理
                formData.id = formData.id ? parseInt(formData.id) : null;
                
                if (!isEdit) {
                    // 新增逻辑
                    const maxId = workData.length ? Math.max(...workData.map(w => w.id)) : 0;
                    formData.id = maxId + 1;
                    workData.push(formData);
                } else {
                    // 编辑逻辑
                    const workIdx = workData.findIndex(w => w.id === currentEditId);
                    if (workIdx !== -1) workData[workIdx] = formData;
                }
                
                // 保存并刷新
                if (saveWorkData()) {
                    initWorkTable();
                    layer.msg(isEdit ? '编辑成功' : '新增成功', {icon: 1, time: 1000});
                }
                
                layer.close(index);
            }
        });
    }
    
    // 表格行操作事件
    table.on('tool(work-table)', function(obj) {
        const data = obj.data;
        const idx = workData.findIndex(w => w.id === data.id);
        
        switch(obj.event) {
            case 'edit':
                openWorkDialog(true, data);
                break;
                
            case 'visit':
                if (data.url) {
                    // 确保URL有协议前缀
                    let visitUrl = data.url;
                    if (!visitUrl.startsWith('http://') && !visitUrl.startsWith('https://')) {
                        visitUrl = 'https://' + visitUrl;
                    }
                    window.open(visitUrl, '_blank');
                } else {
                    layer.msg('该作品没有有效的链接', {icon: 2});
                }
                break;
                
            case 'delete':
                layer.confirm('确定删除这个作品吗？', {
                    title: '删除确认',
                    btn: ['确认删除', '取消'],
                    btnAlign: 'c'
                }, function(confirmIndex) {
                    workData.splice(idx, 1);
                    if (saveWorkData()) {
                        initWorkTable();
                        layer.msg('删除成功', {icon: 1, time: 800});
                    }
                    layer.close(confirmIndex);
                });
                break;
        }
    });
    
    // 工具栏事件
    table.on('toolbar(work-table)', function(obj){
        switch(obj.event){
            case 'add':
                openWorkDialog(false);
                break;
                
            case 'tips':
                layer.alert('作品管理使用提示：<br>' +
                    '1. 点击"新增"按钮添加作品<br>' +
                    '2. 点击"访问"按钮可以查看作品网站<br>' +
                    '3. 点击"编辑"按钮可以修改作品信息<br>' +
                    '4. 支持分类标签，方便组织管理<br>' +
                    '5. 点击"删除"按钮可以删除作品', {
                    title: '使用提示',
                    area: ['400px', 'auto'],
                    icon: 0
                });
                break;
        }
    });
    
    // 初始化系统
    initWorkTable();
}