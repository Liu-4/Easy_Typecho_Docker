<?php 
/**
 * 订阅链接
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
// 获取JSON数据
$jsonData = file_get_contents('./output.json');
$articles = json_decode($jsonData, true);

// 对文章按时间排序（最新的排在前面）
usort($articles, function ($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

// 按订阅源分组，获取每个订阅源的最新文章
$subscriptions = [];
foreach ($articles as $article) {
    $siteUrl = $article['site_url'];
    
    // 如果该订阅源还未添加，或者当前文章更新，则更新该订阅源信息
    if (!isset($subscriptions[$siteUrl]) || 
        strtotime($article['time']) > strtotime($subscriptions[$siteUrl]['time'])) {
        $subscriptions[$siteUrl] = [
            'site_name' => $article['site_name'],
            'site_url' => $article['site_url'],
            'latest_title' => $article['title'],
            'latest_link' => $article['link'],
            'time' => $article['time'],
            'icon' => $article['icon'],
            'description' => $article['description']
        ];
    }
}

// 按最新文章时间对订阅源排序
usort($subscriptions, function ($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});
?>
<ul class="link-grid">
    <?php foreach ($subscriptions as $subscription) : ?>
    <li class="link-item up">
        <div class="link-content">
            <div class="link-image">
                <img src="<?php echo htmlspecialchars($subscription['icon']); ?>" alt="<?php echo htmlspecialchars($subscription['site_name']); ?>">
            </div>
            <div class="link-info">
                <h4 class="link-title line-clamp">
                    <a target="_blank" href="<?php echo htmlspecialchars($subscription['site_url']); ?>" class="hover-link">
                        <?php echo htmlspecialchars($subscription['site_name']); ?>
                    </a>
                </h4>
                <p class="link-description line-clamp">
                    <?php echo htmlspecialchars($subscription['description']); ?>
                </p>
            </div>
        </div>
        <div class="author-description">
            <p class="author-bio line-clamp">
                <a href="<?php echo htmlspecialchars($subscription['latest_link']); ?>" target="_blank" title="查看最新文章" class="rss-link">
                    <span class="rss-title"><?php echo htmlspecialchars($subscription['latest_title']); ?></span>
                </a>
                <?php 
                $articleTime = strtotime($subscription['time']);
                $todayStart = strtotime('today');
                $todayEnd = strtotime('tomorrow') - 1;
                
                if ($articleTime >= $todayStart && $articleTime <= $todayEnd) {
                    echo '<span class="today-text">今天</span>';
                } else {
                    echo '<span class="rss">RSS</span>';
                }
                ?>
            </p>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
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