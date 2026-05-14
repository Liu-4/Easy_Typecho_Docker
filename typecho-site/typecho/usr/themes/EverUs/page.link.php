<?php 
/**
 * 手动链接
 * 
 * @package custom 
 * 
 */
$this->need('public/include.php');
?>

<div id="swup" class="content transition-fade post-content">
    
    <div class="page-main">
        <!-- 最外层内容区 -->
        <div class="page-img_content page-link">
            <div class="page-link-title">
                <h3>好友</h3>
                <p>我常访问的朋友们</p>
            </div>
            <?php
            function get_latest_post_title($rss_url) {
                // 使用Typecho的缓存机制
                $cache_key = 'rss_title_' . md5($rss_url);
                
                // 尝试从缓存获取
                if ($cached = Typecho_Widget::widget('Widget_Options')->cache($cache_key)) {
                    return $cached;
                }
                
                // 获取RSS内容
                $rss_content = @file_get_contents($rss_url);
                if (!$rss_content) return false;
                
                // 解析XML
                $xml = @simplexml_load_string($rss_content);
                if (!$xml) return false;
                
                $title = '';
                
                // 获取最新文章标题
                if (isset($xml->channel->item[0]->title)) {
                    $title = (string)$xml->channel->item[0]->title;
                } elseif (isset($xml->entry[0]->title)) {
                    $title = (string)$xml->entry[0]->title;
                }
                
                // 缓存结果（6小时）
                if ($title) {
                    Typecho_Widget::widget('Widget_Options')->setCache($cache_key, $data, 21600);
                }
                
                return $title ?: false;
            }
            function get_latest_post_data($rss_url) {
                $cache_key = 'rss_data_' . md5($rss_url);
                
                // Typecho缓存优先
                if (class_exists('Typecho_Widget')) {
                    $options = Typecho_Widget::widget('Widget_Options');
                    if ($cached = $options->cache($cache_key)) {
                        return $cached;
                    }
                }
                
                // 获取RSS内容（带超时设置）
                $context = stream_context_create(['http' => ['timeout' => 5]]);
                $rss_content = @file_get_contents($rss_url, false, $context);
                
                // 如果失败，重试一次
                if (!$rss_content) {
                    $rss_content = @file_get_contents($rss_url, false, $context);
                }
                            
                // 解析XML
                libxml_use_internal_errors(true);
                $xml = @simplexml_load_string($rss_content);
                if (!$xml) return false;
                
                $data = ['title' => '', 'date' => '', 'is_today' => false];
                
                // 获取最新文章标题和日期
                if (isset($xml->channel->item[0])) {
                    // RSS格式
                    $item = $xml->channel->item[0];
                    $data['title'] = (string)$item->title;
                    if (isset($item->pubDate)) {
                        $data['date'] = (string)$item->pubDate;
                    } elseif (isset($item->date)) {
                        $data['date'] = (string)$item->date;
                    }
                } elseif (isset($xml->entry[0])) {
                    // Atom格式
                    $entry = $xml->entry[0];
                    $data['title'] = (string)$entry->title;
                    if (isset($entry->published)) {
                        $data['date'] = (string)$entry->published;
                    } elseif (isset($entry->updated)) {
                        $data['date'] = (string)$entry->updated;
                    }
                }
                
                // 判断是否是今天更新的文章[5,6](@ref)
                if (!empty($data['date'])) {
                    $article_date = date('Y-m-d', strtotime($data['date']));
                    $today_date = date('Y-m-d');
                    $data['is_today'] = ($article_date == $today_date);
                }
                
                // 缓存结果
                if (!empty($data['title']) && class_exists('Typecho_Widget')) {
                    Typecho_Widget::widget('Widget_Options')->setCache($cache_key, $data, 3600);
                }
                
                return !empty($data['title']) ? $data : false;
            }
            // 现有友链代码
            $friend_links = [];
            $friend_links_text = $this->options->friend;
            if ($friend_links_text) {
                $friend_links_arr = explode("\r\n", $friend_links_text);
                foreach ($friend_links_arr as $line) {
                    $link = explode("||", $line);
                    if (count($link) >= 5) {
                        $friend_links[] = [
                            "title" => trim($link[0]),
                            "url" => trim($link[1]),
                            "avatar" => trim($link[2]),
                            "description" => trim($link[3]),
                            "rss_url" => trim($link[4]) 
                        ];
                    }
                }
            }
            ?>
            <?php if (!empty($friend_links)) : ?>
            <ul class="link-grid">
                <?php foreach ($friend_links as $link) : ?>
                <li class="link-item up">
                    <div class="link-content">
                        <div class="link-image">
                            <img src="<?php echo $link['avatar']; ?>" alt="<?php echo $link['title']; ?>">
                        </div>
                        <div class="link-info">
                            <h4 class="link-title line-clamp">
                                <a target="_blank" href="<?php echo $link['url']; ?>" class="hover-link">
                                    <?php echo $link['title']; ?>
                                </a>
                            </h4>
                            <p class="link-description line-clamp"><?php echo $link['description']; ?></p>
                        </div>
                    </div>
                    <div class="author-description">
                        <p class="author-bio line-clamp">
                            <?php if (!empty($link['rss_url'])) : 
                                $latest_data = get_latest_post_data($link['rss_url']);
                                // 只显示今天更新的文章
                                if ($latest_data && !empty($latest_data['title']) && $latest_data['is_today']) : ?>
                                    <a href="<?php echo $link['rss_url']; ?>" target="_blank" title="查看RSS" class="rss-link">
                                        <span class="rss-title"><?php echo $latest_data['title']; ?></span>
                                    </a>
                                    <?php if (!empty($latest_data['date'])) : ?>
                                        <span class="today-text">今天</span>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <!-- 非今天更新的文章不显示 -->
                                    <a href="<?php echo $link['rss_url']; ?>" target="_blank" title="查看RSS" class="rss-link">
                                        <span class="rss-title"><?php echo $latest_data['title']; ?></span>
                                    </a>
                                    <span class="rss">RSS</span>
                                <?php endif; ?>
                            <?php else : ?>
                                <span class="rss-missing">未配置RSS</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <div class="page-link-title">
                <h3>参与</h3>
                <p>我曾参与的作品</p>
            </div>
            <?php
            $works = [];
            $works_text = $this->options->work;
            if ($works_text) {
                // 每行格式：网站名称||网站链接
                $lines = array_filter(explode("\r\n", $works_text));
                foreach ($lines as $line) {
                    $parts = explode("||", $line);
                    if (count($parts) === 2) {
                        $works[] = [
                            'name' => trim($parts[0]),
                            'url'  => trim($parts[1])
                        ];
                    }
                }
            }
            ?>
            
            <?php if ($works) : ?>
            <ul class="work-grid">
                <?php foreach ($works as $w) : ?>
                <li class="work-item up">
                    <div class="work-content">
                        <!-- 如果没有头像，可以去掉 img 或者用占位图 -->
                        <div class="work-image">
                            <img width="100%" height="100%" src="https://s0.wp.com/mshots/v1/<?php echo $w['url']; ?>/?w=600&h=400" alt="<?php echo $w['name']; ?>">
                        </div>
                        <div class="work-info">
                            <h4 class="work-title line-clamp">
                                <a target="_blank" href="<?php echo $w['url']; ?>" class="hover-link"><?php echo $w['name']; ?></a>
                            </h4>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
        
        <div class="link-article">
            <article class="post__content">
                <?php article_ccttext($this, $this->user->hasLogin()) ?>
            </article>
            <div class="page-comments">
                <?php $this->need('public/comments.php');?>
            </div>
        </div>
    </div>
    
</div>
<?php $this->need('public/foot.php'); ?>
<?php $this->need('footer.php');?>