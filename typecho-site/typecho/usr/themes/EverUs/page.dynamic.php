<?php 
/**
 * 动态页面
 * 
 * @package custom 
 */
$this->need('public/include.php');
?>

<div id="swup" class="content transition-fade page">
    <!-- 评论容器 -->
    <div id="dyn-comment-container" class="comment">
        <?php
        // 1. 初始化全局状态与工具函数
        $GLOBALS['isLogin'] = $this->user->hasLogin() ? true : false;

        /**
         * 评论嵌套渲染核心函数
         * 区分博主主评论、普通评论两类模板，支持子评论嵌套
         */
        function threadedComments($comments, $options) {
            $db = Typecho_Db::get();
            
            // 评论功能开关配置
            $enableComment = true;
            if (isset($options->class->fields->enable_comment)) {
                $enableComment = $options->class->fields->enable_comment !== '0';
            }

            // 获取评论扩展信息
            $extInfo = [];
            try {
                $extInfo = $db->fetchRow(
                    $db->select('weather', 'temperature')
                        ->from('table.comments')
                        ->where('coid = ?', $comments->coid)
                );
            } catch (Exception $e) {
                echo "<div class='comment-error'>数据库查询错误: {$e->getMessage()}</div>";
            }
            $weather = $extInfo['weather'] ?? '';
            $temperature = $extInfo['temperature'] ?? '';

            // 身份标识判断
            $isAdmin = $comments->authorId === $comments->ownerId;
            $isTopLevel = $comments->levels === 0;
            $isAdminTopComment = $isAdmin && $isTopLevel;
        ?>

        <!-- 博主主评论模板 -->
        <?php if ($isAdminTopComment) : ?>
        <li id="li-<?php $comments->theId(); ?>" class="dyn-comment-parent dyn-admin-top-comment up">
            <div class="dyn-comment-wrapper dyn-admin-wrapper" replyout_id="<?php $comments->theId(); ?>">
                <div class="dyn-comment-content dyn-admin-content" id="comment-<?php $comments->theId(); ?>">
                    <!-- 头部时间信息 -->
                    <div class="dyn-admin-header">
                        <span class="dyn-id"></span>
                        <p><?php $comments->date('Y'); ?></p>
                        <div class="dyn-admin-time">
                            <time title="<?php $comments->date('Y年n月j日'); ?>" 
                                  class="dyn-comment-time" 
                                  datetime="<?php $comments->date('Y-m-d'); ?>">
                                <?php $comments->date('n.j'); ?>
                            </time>
                            <p><?php $comments->date('F'); ?> / <?php $comments->date('G:i:s'); ?></p>
                        </div>
                    </div>

                    <!-- 评论内容区 -->
                    <div class="dyn-comment-text dyn-admin-text post__content">
                        <?php getPermalinkFromCoid($comments->coid); ?>
                        <?php if ($comments->status === "waiting") : ?>
                            <em class="dyn-comment-pending">（评论审核中...）</em>
                        <?php else: ?>
                            <?php
                                // 获取评论内容
                                $content = $comments->content;
                                
                                // 处理音乐标签
                                $content = preg_replace(
                                    '/{meting-js server="([\s\S]*?)" type="song" id="([\s\S]*?)"}{\/meting-js}/is',
                                    '<div class="meting-wrapper"><meting-js style="height: 8rem;display: block;width: 100%;" server="${1}" type="song" id="${2}" data-autoplay="false" class="meting-lazy"></meting-js><loaed>加载中</loaed></div>',
                                    $content
                                );
                                
                                // 输出处理后的内容
                                echo $content;
                                ?>
                        <?php endif; ?>
                    </div>

                    <!-- 底部操作区 -->
                    <div class="dyn-comment-footer dyn-admin-footer">
                        <?php echo getPermalinkFromCoid($comments->parent); ?>
                        <?php echo $weather ? "<span><i class='Nug Nug-shouye45'></i> " . diary_he_weather($weather) . "</span>" : ''; ?>
                        <?php echo $temperature ? "<span><i class='Nug Nug-shouye33'></i> {$temperature} ℃</span>" : ''; ?>
                        
                        <!-- 评论按钮 -->
                        <?php if ($enableComment) : ?>
                        <span class="item comment-reply dyn-comment-reply-btn dyn-admin-reply-btn comment-reply-link"
                              data-coid="<?php $comments->coid(); ?>"
                              data-author="博主">
                            <i class='Nug Nug-shouye7'></i> 回复
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 博主主评论的子评论列表 -->
            <?php if ($comments->children) : ?>
            <div class="list dyn-comment-children">
                <?php foreach ($comments->children as $child) : ?>
                <div class="item" id="comment-<?php echo $child['coid']; ?>">
                    <img width="22" height="22" 
                         class="dyn-child-avatar" 
                         src="<?php echo _getAvatarByMail($child['mail']); ?>" 
                         alt="<?php echo $child['author']; ?>的头像" />
                    <?php echo $child['text']; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- 博主评论的回复表单 (默认隐藏) -->
            <?php if ($enableComment) : ?>
            <div class="vcomment">
                <form id="comment-form" no-pjax class="dyn-comment-post-form comment-form comment-form--root Comments_publisher reply dynamic-reply" style="display:none;"
                  action="<?php _e($options->commentUrl); ?>" 
                  method="post">
                    <input type="hidden" name="parent" value="<?php $comments->coid(); ?>">
                        
                    <div class="dyn-post-head">
                                    <div class="dyn-form-list">
                                        <input autocomplete="off" placeholder="昵称（必填）" type="text" name="author" 
                                            value="<?php $options->class->remember('author'); ?>" required>
                                    </div>
                                    <div class="dyn-form-list">
                                        <input autocomplete="off" placeholder="邮箱（必填）" type="email" name="mail" 
                                               value="<?php $options->class->remember('mail'); ?>" required>
                                    </div>
                                    <div class="dyn-form-list">
                                        <input autocomplete="off" placeholder="网址（选填）" type="url" name="url" 
                                               value="<?php $options->class->remember('url'); ?>">
                                    </div>
                                        <input type="hidden" name="_" value="<?php 
                                            Typecho_Widget::widget('Widget_Security')->to($security);
                                            echo $security->getToken($comments->request->getRequestUrl()); 
                                        ?>">
                                </div>
                    <!-- 评论内容输入区 -->
                    <div class="dyn-post-body comment-editor">
                                    <div class="comment-editor__input">
                                        <textarea name="text" class="comment-editor__textarea comment-emoji" 
                                          onkeydown="if((event.ctrlKey||event.metaKey)&&event.keyCode==13){this.form.submit();return false}" 
                                          placeholder="注意文明发言哦！" required
                                          emoji="😀😃😄😁😆😅🤣😂🙂🙃😉😊😇🥰😍🤩😘😗😚😙😋😛😜🤪🤝🤑🤗🤭🤫🤔🤐🤨😐😑😶😏😒🙄😬🤥😌😔😪🤤😴😷🤒🤕🤢🤮🤧🥵🥶🥴😵🤯🤠🥳😎🤓🧐😕😟🙁☹️😮😯😲😳🥺😦😧😨😰😥😢😭😱😖😣😞😓😩😫🥱😤😡😠🤬"></textarea>
                                        
                                    </div>
                                    
                                </div>
                    <!-- 评论提交区 -->
                    <div class="dyn-post-foot">
                        <div class="dyn-foot-right">
                            <div class="dyn-post-submit" id="Captcha_ok" title="发送">
                                <button type="submit" id="comment_put">发送</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </li>

        <!-- 普通评论模板 -->
        <?php else : ?>
        <li id="li-comment-<?php $comments->theId(); ?>" 
            class="<?php echo $comments->levels > 0 ? 'dyn-comment-child' : 'dyn-comment-parent'; ?>">
            <div class="dyn-comment-wrapper" replyout_id="<?php $comments->theId(); ?>">
                <div class="dyn-comment-content" id="comment-<?php $comments->theId(); ?>">
                    <!-- 头部用户信息 -->
                    <div class="dyn-comment-header">
                        <img width="48" height="48" class="dyn-comment-avatar" 
                             src="<?php _getAvatarByMail($comments->mail); ?>" 
                             alt="<?php $comments->author(); ?>的头像" />
                        <div class="dyn-comment-author" title="<?php $comments->date('Y年n月j日 H:i'); ?>">
                            <?php $comments->author(); ?>
                        </div>
                        <?php echo getPermalinkFromCoid($comments->parent); ?>
                        <time title="<?php $comments->date('Y年n月j日 H:i'); ?>" 
                              class="dyn-comment-time" 
                              datetime="<?php $comments->date('Y-m-d'); ?>">
                            <?php $comments->dateWord(); ?>
                        </time>
                        
                    </div>

                    <!-- 评论内容区 -->
                    <div class="dyn-comment-text">
                        <?php getPermalinkFromCoid($comments->coid); ?>
                        <?php if ($comments->status === "waiting") : ?>
                            <em class="dyn-comment-pending">（评论审核中...）</em>
                        <?php else: ?>
                            <?php $comments->content(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 普通评论的子评论列表 -->
            <?php if ($comments->children) : ?>
            <div class="dyn-comment-children">
                <?php foreach ($comments->children as $child) : ?>
                <div class="item" id="comment-<?php echo $child['coid']; ?>">
                    <span class="name"><?php echo $child['author']; ?>：</span>
                    <?php echo $child['text']; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </li>
        <?php endif; ?>
        <?php } ?>

        <!-- 评论主体渲染区域 -->
        <?php $this->comments()->to($comments); ?>
        <div class="dynamic">
            <input type="hidden" class="comment-url" value="<?php $this->commentUrl(); ?>">

            <!-- 登录用户的评论表单 -->
            <?php if ($this->user->hasLogin()) : ?>
            <div class="respond" id="comments">
                <form id="comment-form"
                      action="<?php $this->commentUrl(); ?>"
                      method="post"
                      role="form"
                      class="comment-form comment-form--root">
                    <div class="comment-form__editor comment-editor">
                        <div class="comment-editor__input dyn-post-body">
                            <textarea name="text" style="min-height: 10rem;"  class="comment-editor__textarea comment-emoji" 
                                      onkeydown="if((event.ctrlKey||event.metaKey)&&event.keyCode==13){this.form.submit();return false}" 
                                      placeholder="注意文明发言哦！" required
                                      emoji="😀😃😄😁😆😅🤣😂🙂🙃😉😊😇🥰😍🤩😘😗😚😙😋😛😜🤪🤝🤑🤗🤭🤫🤔🤐🤨😐😑😶😏😒🙄😬🤥😌😔😪🤤😴😷🤒🤕🤢🤮🤧🥵🥶🥴😵🤯🤠🥳😎🤓🧐😕😟🙁☹️😮😯😲😳🥺😦😧😨😰😥😢😭😱😖😣😞😓😩😫🥱😤😡😠🤬"></textarea>
                        
                        </div>
                        
                        <div class="comment-userinfo__row">
                            <input type="hidden" name="author" value="<?php $this->user->screenName(); ?>" />
                            <input type="hidden" name="mail" value="<?php $this->user->mail(); ?>" />
                            <input type="hidden" name="url" value="<?php $this->options->siteUrl(); ?>" />
                            <input type="hidden" name="_" value="<?php 
                                Typecho_Widget::widget('Widget_Security')->to($security);
                                echo $security->getToken($this->request->getRequestUrl()); 
                            ?>">
                        </div>
                        
                    <div class="dyn-post-hidden-fields">
                                        <?php 
                                        // 自动获取天气信息（如果配置了API）
                                        if($this->options->diary_weather == 'on' && $this->options->diary_weather_key && $this->options->map_key3) :
                                            $wea_key = $this->options->diary_weather_key;
                                            $wea_loc = $this->options->map_key3;
                                            
                                            // 调用天气API
                                            $curl = curl_init('https://devapi.qweather.com/v7/weather/now?location=' . $wea_loc . '&key=' . $wea_key);
                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
                                            curl_setopt($curl, CURLOPT_ENCODING, "gzip");
                                            $content = curl_exec($curl);
                                            $wea_data = json_decode($content);
                                            curl_close($curl);
                                            
                                            $wea_icon = $wea_data->now->icon ?? '';
                                            $wea_text = $wea_data->now->text ?? '';
                                            $wea_temp = $wea_data->now->temp ?? '';
                                        ?>
                                            <div class="dyn-form-list">
                                                <select name="weather" disabled="disabled">
                                                    <option value="<?php echo $wea_icon ?>"><?php echo $wea_text ?>（自动）</option>
                                                </select>
                                            </div>
                                            <div class="dyn-form-list">
                                                <input type="number" disabled="disabled" value="<?php echo $wea_temp ?>" name="temperature" />
                                            </div>
                                        <?php else: ?>
                                        <!-- 手动选择天气 -->
                                            <div class="dyn-form-list">
                                                <select name="weather">
                                                    <option value="">天气选择</option>
                                                    <option value="晴">晴</option>
                                                    <option value="多云">多云</option>
                                                    <option value="阴">阴</option>
                                                    <option value="阵雨"> 阵雨</option>
                                                    <option value="小雨">小雨</option>
                                                    <option value="中雨">中雨</option>
                                                    <option value="雷电">雷电</option>
                                                    <option value="暴雨">暴雨</option>
                                                    <option value="雾">雾</option>
                                                    <option value="大风">大风</option>
                                                    <option value="雪">雪</option>
                                                    <option value="冰雹">冰雹</option>
                                                    <option value="台风">台风</option>
                                                    <option value="酷热">酷热</option>
                                                    <option value="流星雨">流星雨</option>
                                                </select>
                                            </div>
                                            <div class="dyn-form-list">
                                                <input type="number" value="" autocomplete="off" name="temperature" placeholder="气温（纯数字）" />
                                            </div>
                                        <?php endif;?>


                        <div class="comment-form__footer comment-footer dyn-post-submit">
                            <button type="submit" id="submitComment" class="comment-footer__submit">
                                <span>提交</span>
                            </button>
                        </div>
                                    </div>
                    </div>
                </form>
            </div>
            <?php else : ?>
            <!-- 未登录用户提示 -->
            
            <?php endif; ?>
        </div>
            <!-- 评论列表渲染 -->
            <?php $comments->listComments([
                'commentUrl' => $this->commentUrl,
                'class' => $this
            ]); ?>

            <!-- 评论分页 -->
            <?php $comments->pageNav(
                '<svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" class="pagination-icon"><path d="M822.272 146.944l-396.8 396.8c-19.456 19.456-51.2 19.456-70.656 0-18.944-19.456-18.944-51.2 0-70.656l396.8-396.8c19.456-19.456 51.2-19.456 70.656 0 18.944 19.456 18.944 45.056 0 70.656z" fill="" p-id="9417"></path><path d="M745.472 940.544l-396.8-396.8c-19.456-19.456-19.456-51.2 0-70.656 19.456-19.456 51.2-19.456 70.656 0l403.456 390.144c19.456 25.6 19.456 51.2 0 76.8-26.112 19.968-51.712 19.968-77.312 0.512zM181.248 877.056c0-3.584 0-7.68 0.512-11.264h-0.512V151.552h0.512c-0.512-3.584-0.512-7.168-0.512-11.264 0-43.008 21.504-78.336 48.128-78.336s48.128 34.816 48.128 78.336c0 3.584 0 7.68-0.512 11.264h0.512V865.792h-0.512c0.512 3.584 0.512 7.168 0.512 11.264 0 43.008-21.504 78.336-48.128 78.336s-48.128-35.328-48.128-78.336z"></path></svg>',
                '<svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" class="pagination-icon"><path d="M822.272 146.944l-396.8 396.8c-19.456 19.456-51.2 19.456-70.656 0-18.944-19.456-18.944-51.2 0-70.656l396.8-396.8c19.456-19.456 51.2-19.456 70.656 0 18.944 19.456 18.944 45.056 0 70.656z" fill="" p-id="9417"></path><path d="M745.472 940.544l-396.8-396.8c-19.456-19.456-19.456-51.2 0-70.656 19.456-19.456 51.2-19.456 70.656 0l403.456 390.144c19.456 25.6 19.456 51.2 0 76.8-26.112 19.968-51.712 19.968-77.312 0.512zM181.248 877.056c0-3.584 0-7.68 0.512-11.264h-0.512V151.552h0.512c-0.512-3.584-0.512-7.168-0.512-11.264 0-43.008 21.504-78.336 48.128-78.336s48.128 34.816 48.128 78.336c0 3.584 0 7.68-0.512 11.264h0.512V865.792h-0.512c0.512 3.584 0.512 7.168 0.512 11.264 0 43.008-21.504 78.336-48.128 78.336s-48.128-35.328-48.128-78.336z"></path></svg>',
                1,
                '...',
                array(
                    'wrapTag' => 'ul',
                    'wrapClass' => 'j-pagination',
                    'itemTag' => 'li',
                    'textTag' => 'a',
                    'currentClass' => 'active',
                    'prevClass' => 'prev',
                    'nextClass' => 'next'
                )
            ); ?>
        
        
    </div>
</div>

<!-- JavaScript 功能 -->
<script>
    // URL参数修改函数
    function changeURLArg(url, arg, arg_val) {
        let pattern = arg + '=([^&]*)';
        let replaceText = arg + '=' + arg_val;
        if (url.match(pattern)) {
            let tmp = new RegExp('(' + arg + '=)([^&]*)', 'gi');
            return url.replace(tmp, replaceText);
        } else {
            return url + (url.match('[\?]') ? '&' : '?') + replaceText;
        }
    }
</script>

<?php $this->need('public/foot.php'); ?>
<?php $this->need('footer.php'); ?>
    