<?php
$this->need('public/include.php');
?>

<div id="swup" class="content transition-fade category">
    
    <!-- 最外层内容区 -->
    <div class="page-main">
        <div class="category_left">
            <h3 class="up"><?php $this->archiveTitle(); ?>
                <em class="num"><?php echo $this->getTotal(); ?>篇</em>
            </h3>
            <p class="up"><?php echo $this->getDescription(); ?></p>
        </div>
        <ul class="category-list">
            <?php if ($this->have()): ?>
            <?php while($this->next()): ?>
            <li class="category-item">
                <img src="<?php echo _getThumbnails($this)[0] ?>" class="category-image" alt="Category Image">
                <hgroup class="category-info">
                    <div class="category-title">
                        <h3 class="category-heading line-clamp"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>	</h3>
                        <a href="<?php $this->permalink() ?>" class="category-link">展现我的故事</a>
                    </div>
                </hgroup>
            </li>
            <?php endwhile; ?>
            
            <?php else: ?>暂无文章<?php endif; ?>
        </ul>
    </div>
    <!-- 分页 -->
    <?php $this->pageNav('<div class="pagination-prve"><i class="Nug Nug-youbian"></i></div>', '<div class="pagination-next"><i class="Nug Nug-youbian"></i></div>', '0', '…'); ?>
    
</div>    
<?php $this->need('public/foot.php'); ?>
<?php $this->need('footer.php');?>