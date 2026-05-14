<?php if (!$this->hidden && $this->fields->video) : ?>
<div class="article-video">
    <div class="play">
        <div class="box">
            <iframe 
                allowfullscreen="true" 
                webkitallowfullscreen="true" mozallowfullscreen="true"
                data-player="<?php $this->options->CustomPlayer ? $this->options->CustomPlayer() : Helper::options()->themeUrl('public/player.php?url='); ?>" 
                data-src=""
                width="100%" 
                height="500"
                frameborder="0"
            ></iframe>
        </div>
    </div>
    <div class="episodes">
        <?php if ($this->fields->video) : ?>
            <?php $video_arr = explode("\r\n", $this->fields->video); ?>
            <div class="box">
                <?php foreach ($video_arr as $index => $item) : ?>
                    <?php if (!empty(trim($item))) : ?>
                        <?php $parts = explode("$", $item); ?>
                        <?php if (count($parts) >= 2) : ?>
                            <div class="item" data-src="<?php echo htmlspecialchars($parts[1]); ?>">
                                <?php echo htmlspecialchars($parts[0]); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="empty-message">暂无视频资源</div>
        <?php endif; ?>
    </div>
</div>
<?php endif ?>

    <?php
        preg_match_all('/<img.*?src=[\"\'](.*?)[\"\'].*?>/i', $this->content, $matches);
        $images = $matches[1];
        $imageCount = count($images);
        $imageCount = (int)$imageCount;
    ?>
    
    <!-- 1. 图片数量 = 1：单张图片布局 -->
    <?php if ($imageCount == 1): ?>
        <header class="post-images post-images-single">
            <?php foreach ($images as $image): ?>
            <li class="gallery-item work-card">
                <a href="<?php echo $image; ?>" data-fancybox="header" class="">
                    <div class="lazy-item">
                        <img src="<?php echo get_Lazyload(); ?>" data-src="<?php echo $image; ?>" alt="<?php $this->title() ?>">
                        <loaed>图片加载中</loaed>
                    </div>
                </a>
                <div class="mil-descr">
                    <a class="zoom">
                        <button class="Nug Nug-zhankai2 zoom" type="button"></button>
                    </a>
                </div>
            </li>
            <?php endforeach; ?>
        </header>
    
    <!-- 2. 图片数量 = 2：网格布局（非轮播） -->
    <?php elseif ($imageCount == 2): ?>
        <header class="post-images post-images-grid">
            <div class="post-images-grid-container">
                <?php foreach ($images as $image): ?>
                <li class="gallery-item work-card">
                    <a href="<?php echo $image; ?>" data-fancybox="header" class="">
                        <div class="lazy-item">
                            <img src="<?php echo get_Lazyload(); ?>" data-src="<?php echo $image; ?>" alt="<?php $this->title() ?>">
                            <loaed>图片加载中</loaed>
                        </div>
                    </a>
                    <div class="mil-descr">
                        <a class="zoom">
                            <button class="Nug Nug-zhankai2 zoom" type="button"></button>
                        </a>
                    </div>
                </li>
                <?php endforeach; ?>
            </div>
        </header>
    
    <!-- 3. 图片数量 ≥3：轮播布局（含控制按钮） -->
    <?php elseif ($imageCount >= 3): ?>
        <header class="post-images post-images-carousel">
            <div class="article-carousel">
                <?php foreach ($images as $image): ?>
                <li class="article-slide gallery-item work-card">
                    <a href="<?php echo $image; ?>" data-fancybox="header" class="">
                        <div class="lazy-item">
                            <img src="<?php echo get_Lazyload(); ?>" data-src="<?php echo $image; ?>" alt="<?php $this->title() ?>">
                            <loaed>图片加载中</loaed>
                        </div>
                    </a>
                    <div class="mil-descr">
                        <a class="zoom">
                            <button class="Nug Nug-zhankai2 zoom" type="button"></button>
                        </a>
                    </div>
                </li>
                <?php endforeach; ?>
            </div>
            <!-- 轮播控制按钮（仅≥3张时显示） -->
            <div class="carousel-controls">
                <button class="carousel-btn slick-custom-prev" aria-label="上一张"><i class="Nug Nug-fanhui"></i></button>
                <button class="carousel-btn slick-custom-next" aria-label="下一张"><i class="Nug Nug-youjiantou1"></i></button>
            </div>
        </header>
    
    <!-- 4. 无图片：空状态（可选，避免页面留空） -->
    <?php else: ?>
    <?php endif; ?>
