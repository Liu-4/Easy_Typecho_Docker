<?php
class EchoHtml extends Typecho_Widget_Helper_Layout {
	public function __construct($html) {
		$this->html($html);
		$this->start();
		$this->end();
	}
	public function start() {
	}
	public function end() {
	}
}
/**
 * 主题后台配置入口
 * @param object $form Typecho表单对象
 */
function themeConfig($form)
{
    $form->addItem(new EchoHtml('<div class="layui-tab" lay-filter="demo">'));
    
    // 选项卡导航
    require_once __DIR__ . '/templates/partials/tab-nav.php';
    // 其他配置内容
    $form->addItem(new EchoHtml('<div class="layui-tab-content">'));
    
        $form->addItem(new EchoHtml('<li class="layui-tab-item layui-show">'));
        require_once __DIR__ . '/templates/partials/backup.php';
        $form->addItem(new EchoHtml('
            <ul class="layui-timeline">
  <li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">EverUs 1.3.4版本</div>
      <ul>
        <li>添加Docker搭配订阅功能</li>
        <li>修改默认字体为小米的 MiSans 字体</li>
      </ul>
    </div>
  </li>
  <li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">EverUs 1.3.3版本</div>
      <ul>
        <li>添加rss订阅代替作者描述</li>
        <li>添加Docker搭配订阅功能</li>
        <li>美化后台写文工具</li>
      </ul>
    </div>
  </li>
  <li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">EverUs 1.3.2版本</div>
      <ul>
        <li>更新主题后台设置</li>
        <li>添加音乐自定义设置</li>
        <li>添加备份数据功能</li>
      </ul>
    </div>
  </li>
  <li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">EverUs 1.3.1版本</div>
      <ul>
        <li>添加动态 只支持音乐代码 请使用文章代码来实现音乐</li>
        <li>首页头部图片支持后台主题设置里填写</li>
        <li>去掉主题里的文章缩略图 添加自定义缩略图链接</li>
        <li>后台设置里添加作品将会显示在友链下</li>
      </ul>
      <p>因为添加动态功能的缘故 使用之前<em> 在数据库里添加下面代码 </em> 否则评论会出现问题 下面两行的意思是在typecho_comments里添加weather（天气）temperature（温度）字段 这个用于动态发表的时候填写天气和温度的数据存进数据库 目前不支持自动获取</p>
      <pre class="layui-code layui-box layui-code-view layui-code-notepad">
            ALTER TABLE `typecho_comments`
            ADD `weather` varchar(50) DEFAULT NULL,
            ADD `temperature` varchar(20) DEFAULT NULL;
        </pre>
    </div>
  </li>
  <li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">EverUs 1.3版本</div>
      <ul>
        <li>添加懒加载和音乐 （文件里修改）</li>
        <li>美化后台友链和任务</li>
        <li>添加文章音乐 emoji 视频功能 （不含表格）</li>
        <li>标题 描述 全部改成中文</li>
        <li>修改了分类样式</li>
      </ul>
      <p>文章<em> 缩略图 </em>都存在了文件里 导致文件<em> 有点大 </em></p>
    </div>
  </li>
  <li class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis"></i>
    <div class="layui-timeline-content layui-text">
      <div class="layui-timeline-title">EverUs 1.2版本正式发布</div>
    </div>
  </li>
</ul>
        '));
        $form->addItem(new EchoHtml('</li>'));
        
        $form->addItem(new EchoHtml('<li class="layui-tab-item">'));
            $AssetsURL = new Typecho_Widget_Helper_Form_Element_Text(
                'AssetsURL',
                NULL,
                NULL,
                _t('静态资源CDN地址（非必填）'),
                _t('填写静态资源CDN地址前缀，例如：https://cdn.example.com/assets<br>'.
                   '留空则使用本地资源')
            );
            $AssetsURL->input->setAttribute('class', 'layui-input');
            $AssetsURL->input->setAttribute('style', 'width: 100%;');
            $AssetsURL->input->setAttribute('placeholder', 'https://cdn.example.com/assets');
            $form->addInput($AssetsURL);
        
            $CustomAvatarSource = new Typecho_Widget_Helper_Form_Element_Text(
                'CustomAvatarSource',
                NULL,
                NULL,
                _t('自定义头像源（非必填）'),
                _t('填写自定义Gravatar镜像地址，例如：https://gravatar.example.com/avatar/<br>'.
                   '注意：地址必须以斜杠(/)结尾')
            );
            $CustomAvatarSource->input->setAttribute('class', 'layui-input');
            $CustomAvatarSource->input->setAttribute('style', 'width: 100%;');
            $CustomAvatarSource->input->setAttribute('placeholder', 'https://gravatar.example.com/avatar/');
            $form->addInput($CustomAvatarSource);

            $CustomFont = new Typecho_Widget_Helper_Form_Element_Text(
                'CustomFont',
                NULL,
                NULL,
                _t('自定义字体（非必填）'),
                _t('填写WOFF格式字体URL，例如：https://cdn.example.com/font.woff<br>'.
                   '留空则使用系统默认字体')
            );
            $CustomFont->input->setAttribute('class', 'layui-input');
            $CustomFont->input->setAttribute('style', 'width: 100%;');
            $CustomFont->input->setAttribute('placeholder', 'https://cdn.example.com/font.woff');
            $form->addInput($CustomFont);
            
            // -------------------------- 0. 首页图头设置 --------------------------
            $Header_img = new Typecho_Widget_Helper_Form_Element_Text(
                'Header_img',
                NULL,
                NULL,
                _t('首页头部图片'),
                _t('填写首页要显示的图片链接')
            );
            $Header_img->input->setAttribute('class', 'layui-input');
            $Header_img->input->setAttribute('style', 'width: 100%;');
            $form->addInput($Header_img);
            
            // -------------------------- 1. 走心评论设置 --------------------------
            $zxComment = new Typecho_Widget_Helper_Form_Element_Text(
                'zx',
                null,
                null,
                _t('走心评论（非必填）'),
                _t('多个评论ID（coid）用英文逗号分割，例：111,222,333')
            );
            $zxComment->input->setAttribute('class', 'layui-input');
            $zxComment->input->setAttribute('style', 'width: 100%;');
            $form->addInput($zxComment);
            
            // -------------------------- 2. 文章视频设置 --------------------------
            $CustomPlayer = new Typecho_Widget_Helper_Form_Element_Text(
                'CustomPlayer',
                NULL,
                NULL,
                '自定义视频播放器（非必填）',
                '介绍：用于修改主题自带的默认播放器 <br />
                     例如：https://v.ini0.com/player/?url= <br />
                     注意：主题自带的播放器只能解析M3U8的视频格式'
            );
            $CustomPlayer->input->setAttribute('class', 'layui-input');
            $CustomPlayer->input->setAttribute('style', 'width: 100%;');
            $form->addInput($CustomPlayer);
        
        $form->addItem(new EchoHtml('</li>'));
        
        $form->addItem(new EchoHtml('<li class="layui-tab-item">'));
            // -------------------------- 3. 链接设置 --------------------------
        $form->addItem(new EchoHtml('<fieldset class="layui-elem-field">
  <legend>注意</legend>
  <div class="layui-field-box">
    <p>建议使用<span style="color:red;">订阅链接</span>而非<span style="color:red;">手动链接</span>页面。并且根据需求配置下面其中一种信息。</p>
    <p>订阅链接需要填写下面订阅信息配置，并且安装 Docker </p>
    <p>手动链接需要填写下面表格信息，弊端就是加载缓慢，链接越多越慢。</p>
  </div>
</fieldset>'));
            $form->addItem(new EchoHtml('<blockquote class="layui-elem-quote">选择订阅模板配置下面信息</blockquote>'));
            // 自带友链管理
            $links = new Typecho_Widget_Helper_Form_Element_Text(
                'links',
                NULL,
                NULL,
                '部署FreshRSS的域名（必填）',
                '介绍：https://你部署FreshRSS的域名/<br>
                注意：结尾的/很重要'
            );
            $links->input->setAttribute('class', 'layui-input');
            $links->input->setAttribute('style', 'width: 100%;');
            $form->addInput($links);
            
            $User_name = new Typecho_Widget_Helper_Form_Element_Text(
                'User_name',
                NULL,
                NULL,
                'FreshRSS的用户名（必填）',
                '介绍：注册时填写的用户名'
            );
            $User_name->input->setAttribute('class', 'layui-input');
            $User_name->input->setAttribute('style', 'width: 100%;');
            $form->addInput($User_name);
            
            $api_password = new Typecho_Widget_Helper_Form_Element_Text(
                'api_password',
                NULL,
                NULL,
                'api密码（必填）',
                '介绍：设置的api密码，不是登入密码'
            );
            $api_password->input->setAttribute('class', 'layui-input');
            $api_password->input->setAttribute('style', 'width: 100%;');
            $form->addInput($api_password);
            
            $form->addItem(new EchoHtml('<blockquote class="layui-elem-quote">选择手动模板填写下面数据</blockquote>'));
            // 手动友链管理
            require_once __DIR__ . '/templates/friends/main.php';
        $form->addItem(new EchoHtml('</li>'));
        
        $form->addItem(new EchoHtml('<li class="layui-tab-item">'));
            // 作品管理内容
            require_once __DIR__ . '/templates/works/main.php';
        $form->addItem(new EchoHtml('</li>'));
        
        $form->addItem(new EchoHtml('<li class="layui-tab-item">'));
            // 任务管理内容
            require_once __DIR__ . '/templates/tasks/main.php';
        $form->addItem(new EchoHtml('</li>'));
        
        $form->addItem(new EchoHtml('<li class="layui-tab-item">'));
    
            // -------------------------- 2. 评论邮件通知设置 --------------------------
            $commentMailSwitch = new Typecho_Widget_Helper_Form_Element_Select(
                'CommentMail',
                ['off' => '关闭（默认）', 'on' => '开启'],
                'off',
                _t('是否开启评论邮件通知'),
                _t('介绍：开启后，新评论会通过邮箱通知博主<br>注意：需完整填写下方邮箱配置（推荐QQ邮箱）')
            );
            $commentMailSwitch->input->setAttribute('class', 'layui-select');
            $commentMailSwitch->input->setAttribute('lay-verify', 'required');
            $form->addInput($commentMailSwitch->multiMode());
        
            // 邮箱服务器地址
            $commentMailHost = new Typecho_Widget_Helper_Form_Element_Text(
                'CommentMailHost',
                null,
                'smtp.qq.com',
                _t('邮箱服务器地址'),
                _t('例：QQ邮箱填 smtp.qq.com，163邮箱填 smtp.163.com')
            );
            $commentMailHost->input->setAttribute('class', 'layui-input');
            $commentMailHost->input->setAttribute('style', 'width: 100%;');
            $form->addInput($commentMailHost->multiMode());
        
            // 加密方式
            $commentSMTPSecure = new Typecho_Widget_Helper_Form_Element_Select(
                'CommentSMTPSecure',
                ['ssl' => 'SSL（默认）', 'tsl' => 'TLS'],
                'ssl',
                _t('加密方式'),
                _t('QQ邮箱用SSL，部分邮箱用TLS，具体参考邮箱服务商文档')
            );
            $commentSMTPSecure->input->setAttribute('class', 'layui-select');
            $commentSMTPSecure->input->setAttribute('lay-verify', 'required');
            $form->addInput($commentSMTPSecure->multiMode());
        
            // 服务器端口
            $commentMailPort = new Typecho_Widget_Helper_Form_Element_Text(
                'CommentMailPort',
                null,
                '465',
                _t('邮箱服务器端口号'),
                _t('例：SSL对应465，TLS对应587（参考邮箱服务商文档）')
            );
            $commentMailPort->input->setAttribute('class', 'layui-input');
            $commentMailPort->input->setAttribute('style', 'width: 100%;');
            $form->addInput($commentMailPort->multiMode());
    
            // 发件人昵称
            $commentMailFromName = new Typecho_Widget_Helper_Form_Element_Text(
                'CommentMailFromName',
                null,
                null,
                _t('发件人昵称'),
                _t('收到邮件时显示的“发件人”名称，例：我的博客')
            );
            $commentMailFromName->input->setAttribute('class', 'layui-input');
            $commentMailFromName->input->setAttribute('style', 'width: 100%;');
            $form->addInput($commentMailFromName->multiMode());
        
            // 发件人邮箱
            $commentMailAccount = new Typecho_Widget_Helper_Form_Element_Text(
                'CommentMailAccount',
                null,
                null,
                _t('发件人邮箱'),
                _t('例：2323333339@qq.com（需与服务器地址匹配）')
            );
            $commentMailAccount->input->setAttribute('class', 'layui-input');
            $commentMailAccount->input->setAttribute('style', 'width: 100%;');
            $form->addInput($commentMailAccount->multiMode());
        
            // 邮箱授权码
            $commentMailPassword = new Typecho_Widget_Helper_Form_Element_Text(
                'CommentMailPassword',
                null,
                null,
                _t('邮箱授权码'),
                _t('介绍：非邮箱登录密码，需在邮箱后台开启SMTP后获取<br>QQ邮箱获取：设置 → 账户 → 开启IMAP/SMTP → 生成授权码')
            );
            $commentMailPassword->input->setAttribute('class', 'layui-input');
            $commentMailPassword->input->setAttribute('style', 'width: 100%;');
            $form->addInput($commentMailPassword->multiMode());
        
        $form->addItem(new EchoHtml('</li>'));
        
        // 音乐
        $form->addItem(new EchoHtml('<li class="layui-tab-item">'));
            $musicPlatform = new Typecho_Widget_Helper_Form_Element_Select(
                'musicPlatform', // 字段名，用于在数据库和模板中识别
                array(           // 选项数组，'选项值' => '显示文本'
                    'netease' => '网易云音乐',
                    'tencent' => 'QQ音乐',
                    'kugou' => '酷狗音乐',
                    'xiami' => '虾米音乐',
                    'baidu' => '百度音乐'
                ),
                'netease',      // 默认选中的平台（例如网易云音乐）
                _t('音乐平台'),
                _t('选择在博客中播放音乐时优先使用的平台。')
            );
            $musicPlatform->input->setAttribute('class', 'layui-select');
            $musicPlatform->input->setAttribute('lay-verify', 'required');
            $form->addInput($musicPlatform);
            
            $musicid = new Typecho_Widget_Helper_Form_Element_Text(
                'musicid',
                null,
                null,
                _t('音乐id'),
                _t('播放列表 ID ')
            );
            $musicid->input->setAttribute('class', 'layui-input');
            $musicid->input->setAttribute('style', 'width: 100%;');
            $form->addInput($musicid);
            
        $form->addItem(new EchoHtml('</li>'));
    
        // 隐藏的文本域：存储任务数据（格式：时间线||标题||说点||证明）
        $compass = new Typecho_Widget_Helper_Form_Element_Textarea(
            'compass',
            null,
            null,
            _t('任务表数据（隐藏）'),
            _t('自动存储任务数据，无需手动编辑')
        );
        $compass->setAttribute('style', 'display:none;');
        $form->addInput($compass);
        
        // 隐藏的文本域：存储友链数据（格式：标题||链接||头像||描述||说点）
        $friend = new Typecho_Widget_Helper_Form_Element_Textarea(
            'friend',
            null,
            null,
            _t('友链表数据（隐藏）'),
            _t('自动存储友链数据，无需手动编辑')
        );
        $friend->setAttribute('style', 'display:none;');
        $form->addInput($friend);
        
        /* 作品数据隐藏字段 */
        $work = new Typecho_Widget_Helper_Form_Element_Textarea(
            'work',          // 字段名
            null,
            null,
            _t('作品表数据（隐藏）'),
            _t('自动存储作品数据，无需手动编辑')
        );
        $work->setAttribute('style', 'display:none;');
        $form->addInput($work);
    
    $form->addItem(new EchoHtml('</div>'));
    
    $themeUrl = Helper::options()->themeUrl;
    ?>
    <!-- 加载Layui资源 -->
    <link href="//unpkg.com/layui@2.11.6/dist/css/layui.css" rel="stylesheet">
    <script src="//unpkg.com/layui@2.11.6/dist/layui.js"></script>
    <link href="<?php echo $themeUrl; ?>/assets/plugin/themes/admin-modules.css" rel="stylesheet">
    <script src="<?php echo $themeUrl; ?>/assets/plugin/themes/admin-modules.js"></script>
    <?php
    
    $form->addItem(new EchoHtml('</div>'));
}
