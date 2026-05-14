<?php
/**
 * 主题主入口：加载功能模块 + 核心初始化
 */

// 加载功能模块（按依赖顺序加载）
require_once __DIR__ . '/libs/TemplateHelper.php';
require_once __DIR__ . '/libs/ThemeConfig.php';

// 引入第三方类库（原文件依赖）
require_once __DIR__ . '/libs/CommentPush.php';
require_once __DIR__ . '/libs/Comments.php';
require_once __DIR__ . '/libs/Content.php';

// 引入FreshRSS文件
if (!empty(Helper::options()->links)) {
    require_once __DIR__ . '/rss.php';
}
/**
 * 主题初始化函数
 * 作用：设置评论规则、分类页条数等全局配置
 */
function themeInit(Widget_Archive $archive)
{
    // 解决加密文章PJAX刷新问题
    if ($archive->hidden) {
        header('HTTP/1.1 200 OK');
    }

    // 评论相关配置
    Helper::options()->commentsMaxNestingLevels = 999;    // 评论最大嵌套层级
    Helper::options()->commentsAntiSpam = false;          // 关闭反垃圾保护
    Helper::options()->commentsOrder = 'DESC';            // 最新评论在前
    Helper::options()->commentsCheckReferer = false;      // 关闭来源URL校验
    Helper::options()->commentsMarkdown = '1';            // 强制开启Markdown评论
    Helper::options()->commentsHTMLTagAllowed .= '<img class src alt><div class>'; // 允许额外HTML标签

    // 分类页配置：统一每页显示9篇文章
    if ($archive->is('category')) {
        $archive->parameter->pageSize = 9;
    }
}
function themeFields($layout)
{
    if($_SERVER['SCRIPT_NAME']=="/admin/write-post.php"){
        
        // 自定义缩略图
        $thumb = new Typecho_Widget_Helper_Form_Element_Text(
            'thumb',
            NULL,
            NULL,
            _t('文章缩略图'),
            _t('此处请填写缩略图链接，没有的话默认使用主题自带的一张图。')
        );
        $thumb->input->setAttribute('class', 'layui-input');
        $thumb->input->setAttribute('style', 'width: 100%;');
        $layout->addItem($thumb);
        // 文章模板
        $postTemplate = new Typecho_Widget_Helper_Form_Element_Select(
            'postTemplate', // 字段名
            array(
                'default' => _t('默认模板'),
                'video' => _t('视频模板')
            ),
            'default', // 默认值
            _t('文章模板选择'), // 标签文字
            _t('为此文章选择一个特定的显示模板。') // 描述文字
        );
        $postTemplate->input->setAttribute('class', 'layui-select');
        $postTemplate->input->setAttribute('lay-verify', 'required');
        $layout->addItem($postTemplate);
        $video = new Typecho_Widget_Helper_Form_Element_Textarea(
            'video',
            NULL,
            NULL,
            'M3U8或MP4地址（非必填）',
            '填写后，文章会插入一个视频模板 <br>
                 格式：视频名称$视频地址。如果有多个，换行写即可 <br>
                 例如：<br>
                    第01集$https://iqiyi.cdn9-okzy.com/20201104/17638_8f3022ce/index.m3u8 <br>
                    第02集$https://iqiyi.cdn9-okzy.com/20201104/17639_5dcb8a3b/index.m3u8 
                '
          );
        $video->input->setAttribute('class', 'layui-textarea');
        $video->input->setAttribute('style', 'width: 100%;');
        $layout->addItem($video);

    }
}