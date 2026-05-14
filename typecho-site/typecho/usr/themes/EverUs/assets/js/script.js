/* ========== 全局立即执行函数 ========== */
(function ($) {
    /* --------- Scroll Box --------- */
    // 点击事件：滚动到顶部并给 body 添加类名
    $(".huojian__toggle").click(function () {
        $('html,body').animate({ scrollTop: 0 }, 500, function () {
            // 动画完成后给 body 添加类名
            $('body').removeClass('nav-fixed');
        });
    });

    // 滚动事件：根据滚动位置显示或隐藏 .huojian__toggle 按钮，并给 body 添加或移除类名
    $(window).on("scroll", function () {
        var fromTop = $(window).scrollTop();
        if (fromTop > 50) {  // 判断滚动后高度超过50px,就显示
            $('.huojian__toggle').removeClass('hidden');
            $('body').addClass('nav-fixed'); // 添加类名
        } else {
            $('.huojian__toggle').addClass('hidden');
            $('body').removeClass('nav-fixed'); // 移除类名
        }
    });
    
    /* --------- nav --------- */
    $(function () {
        // 开关
        $('.daohang').on('click', function (e) {
            $('body').toggleClass('nav-open');
        });
    
        // 页面刚渲染就关闭
        $('body').removeClass('nav-open');
    
        // 导航内部所有链接点击后关闭
        $(document).on('click', '.site-nav a', function () {
            $('body').removeClass('nav-open');
        });
    });
    
    /* --------- Music --------- */
    // 默认状态
    $('.site-footer').slideDown(300);
    $('.footer-music').slideUp(300);
    $('body').removeClass('music-on');
    
    $('.music__toggle').on('click', function () {
        $('.site-footer').slideToggle(400);
        $('.footer-music').slideToggle(400, function () {
            $('body').toggleClass('music-on', $(this).is(':visible'));
        });
    });

    
})(jQuery);

/*=============  全局懒加载 ============= */

(function() {
    // 多功能懒加载库
    class UnifiedLazyLoad {
        constructor(options = {}) {
            // 默认配置
            this.config = {
                // 图片懒加载配置
                imgSelector: 'img[data-src]',
                bgSelector: '[data-bg-src]',
                imgRootMargin: '200px 0px',
                imgThreshold: 0.01,
                imgLoadingClass: 'lazy-loading',
                imgLoadedClass: 'lazy-loaded',
                imgErrorClass: 'lazy-error',
                imgDataAttr: 'src',
                bgDataAttr: 'bg-src',
                defaultImage: '',
                useNativeLazyLoad: false,
                
                // 多媒体嵌入配置
                mediaSelector: 'media-embed, meting-js.meting-lazy',
                mediaRootMargin: '300px 0px',
                mediaThreshold: 0.01,
                
                // 通用配置
                throttleTime: 200
            };
            
            // 合并用户配置
            Object.assign(this.config, options);
            
            // 存储观察器实例
            this.imgObserver = null;
            this.mediaObserver = null;
            
            // 初始化
            this.init();
        }
        
        // 初始化方法
        init() {
            // 初始化图片懒加载
            this.initImageLazyLoad();
            
            // 初始化多媒体懒加载
            this.initMediaLazyLoad();
        }
        
        // 图片懒加载初始化
        initImageLazyLoad() {
            // 检查是否支持原生懒加载
            if (this.config.useNativeLazyLoad && 'loading' in HTMLImageElement.prototype) {
                this.initNativeImageLazyLoad();
            } 
            // 检查是否支持 IntersectionObserver
            else if ('IntersectionObserver' in window) {
                this.initIntersectionImageObserver();
            } 
            // 回退到传统滚动事件监听
            else {
                this.initScrollImageListener();
            }
        }
        
        // 多媒体懒加载初始化
        initMediaLazyLoad() {
            if ('IntersectionObserver' in window) {
                this.initIntersectionMediaObserver();
            } else {
                this.initScrollMediaListener();
            }
        }
        
        /* 图片懒加载相关方法 */
        initNativeImageLazyLoad() {
            const images = document.querySelectorAll(this.config.imgSelector);
            images.forEach(img => {
                img.loading = 'lazy';
                this.loadImage(img);
            });
            
            const bgElements = document.querySelectorAll(this.config.bgSelector);
            bgElements.forEach(el => {
                this.loadBackgroundImage(el);
            });
        }
        
        initIntersectionImageObserver() {
            this.imgObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        
                        if (target.tagName.toLowerCase() === 'img') {
                            this.loadImage(target);
                        } else if (target.hasAttribute(`data-${this.config.bgDataAttr}`)) {
                            this.loadBackgroundImage(target);
                        }
                        
                        observer.unobserve(target);
                    }
                });
            }, {
                rootMargin: this.config.imgRootMargin,
                threshold: this.config.imgThreshold
            });
            
            this.observeImageElements();
        }
        
        initScrollImageListener() {
            this.checkImageVisibility();
            
            let timer = null;
            const handler = () => {
                if (timer) clearTimeout(timer);
                timer = setTimeout(() => {
                    this.checkImageVisibility();
                    timer = null;
                }, this.config.throttleTime);
            };
            
            window.addEventListener('scroll', handler);
            window.addEventListener('resize', handler);
        }
        
        observeImageElements() {
            document.querySelectorAll(this.config.imgSelector).forEach(img => {
                this.imgObserver.observe(img);
            });
            
            document.querySelectorAll(this.config.bgSelector).forEach(el => {
                this.imgObserver.observe(el);
            });
        }
        
        checkImageVisibility() {
            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;
            
            document.querySelectorAll(this.config.imgSelector).forEach(img => {
                if (this.isElementInViewport(img, scrollY, windowHeight, this.config.imgRootMargin) && 
                    !img.classList.contains(this.config.imgLoadedClass)) {
                    this.loadImage(img);
                }
            });
            
            document.querySelectorAll(this.config.bgSelector).forEach(el => {
                if (this.isElementInViewport(el, scrollY, windowHeight, this.config.imgRootMargin) && 
                    !el.classList.contains(this.config.imgLoadedClass)) {
                    this.loadBackgroundImage(el);
                }
            });
        }
        
        /* 多媒体懒加载相关方法 */
        initIntersectionMediaObserver() {
            this.mediaObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        this.loadMediaContent(target);
                        observer.unobserve(target);
                    }
                });
            }, {
                rootMargin: this.config.mediaRootMargin,
                threshold: this.config.mediaThreshold
            });
            
            this.observeMediaElements();
        }
        
        initScrollMediaListener() {
            this.checkMediaVisibility();
            
            let timer = null;
            const handler = () => {
                if (timer) clearTimeout(timer);
                timer = setTimeout(() => {
                    this.checkMediaVisibility();
                    timer = null;
                }, this.config.throttleTime);
            };
            
            window.addEventListener('scroll', handler);
            window.addEventListener('resize', handler);
        }
        
        observeMediaElements() {
            document.querySelectorAll(this.config.mediaSelector).forEach(el => {
                this.mediaObserver.observe(el);
            });
        }
        
        checkMediaVisibility() {
            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;
            
            document.querySelectorAll(this.config.mediaSelector).forEach(el => {
                if (this.isElementInViewport(el, scrollY, windowHeight, this.config.mediaRootMargin) && 
                    !el.classList.contains('loaded')) {
                    this.loadMediaContent(el);
                }
            });
        }
        
        /* 通用方法 */
        isElementInViewport(el, scrollY, windowHeight, margin = '0px') {
            const rect = el.getBoundingClientRect();
            const marginValue = parseInt(margin, 10) || 0;
            
            return (
                rect.bottom >= -marginValue &&
                rect.top <= (windowHeight + marginValue)
            );
        }
        
        // 加载图片
        loadImage(img) {
            img.classList.add(this.config.imgLoadingClass);
            
            if (this.config.defaultImage && !img.src) {
                img.src = this.config.defaultImage;
            }
            
            const newImg = new Image();
            newImg.src = img.getAttribute(`data-${this.config.imgDataAttr}`);
            
            newImg.onload = () => {
                img.classList.remove(this.config.imgLoadingClass);
                img.classList.add(this.config.imgLoadedClass);
                img.src = newImg.src;
                this.triggerEvent(img, 'lazyloaded');
            };
            
            newImg.onerror = () => {
                img.classList.remove(this.config.imgLoadingClass);
                img.classList.add(this.config.imgErrorClass);
                this.triggerEvent(img, 'lazyerror');
            };
        }
        
        // 加载背景图
        loadBackgroundImage(el) {
            el.classList.add(this.config.imgLoadingClass);
            const bgUrl = el.getAttribute(`data-${this.config.bgDataAttr}`);
            
            const img = new Image();
            img.src = bgUrl;
            
            img.onload = () => {
                el.classList.remove(this.config.imgLoadingClass);
                el.classList.add(this.config.imgLoadedClass);
                el.style.backgroundImage = `url(${bgUrl})`;
                this.triggerEvent(el, 'lazyloaded');
            };
            
            img.onerror = () => {
                el.classList.remove(this.config.imgLoadingClass);
                el.classList.add(this.config.imgErrorClass);
                this.triggerEvent(el, 'lazyerror');
            };
        }
        
        // 加载多媒体内容
        loadMediaContent(el) {
    /* 1. B 站 iframe 逻辑 */
    const iframe = el.querySelector('iframe[data-src]');
    if (iframe) {
        iframe.src = iframe.dataset.src;
        el.classList.add('loaded');
        this.triggerEvent(el, 'medialoaded');
        return;
    }

    /* 2. 本地视频 <video data-src="..."> */
    const video = el.querySelector('video[data-src]');
    if (video) {
        video.src = video.dataset.src; // 把地址还回去
        video.load();                  // 手动触发加载
        el.classList.add('loaded');
        this.triggerEvent(el, 'medialoaded');
        return;
    }

    /* 3. 音乐 meting-js*/
    if (el.tagName.toLowerCase() === 'meting-js' && !el.classList.contains('loaded')) {
        ['server','type','id','autoplay','list','mutex','preload','theme']
            .forEach(k => { if (el.dataset[k]) el.setAttribute(k, el.dataset[k]); });
        el.classList.add('loaded');
        this.triggerEvent(el, 'medialoaded');
        if (window.APlayer && window.Meting) new Meting(el);
    }
}
        
        // 触发自定义事件
        triggerEvent(element, eventName) {
            const event = new Event(eventName, { bubbles: true, cancelable: true });
            element.dispatchEvent(event);
        }
        
        // 销毁实例
        destroy() {
            if (this.imgObserver) {
                this.imgObserver.disconnect();
                this.imgObserver = null;
            }
            
            if (this.mediaObserver) {
                this.mediaObserver.disconnect();
                this.mediaObserver = null;
            }
            
            window.removeEventListener('scroll', this.checkImageVisibility);
            window.removeEventListener('resize', this.checkImageVisibility);
            window.removeEventListener('scroll', this.checkMediaVisibility);
            window.removeEventListener('resize', this.checkMediaVisibility);
        }
    }
    
    // 全局暴露 API
    window.UnifiedLazyLoad = UnifiedLazyLoad;
    
    // 自动初始化
    document.addEventListener('DOMContentLoaded', () => {
        new UnifiedLazyLoad();
    });
})();

document.addEventListener('DOMContentLoaded', function () {
    // ==================== SWUP 初始化部分 ====================
    const swup = new Swup({
        containers: ['#swup'],
        animateHistoryBrowsing: true, // 这个最重要！开启历史记录动画
        animationSelector: '.transition-fade', // CSS类名是否正确
        cache: true // 启用缓存提高速度
    });

    // ==================== 非SWUP初始化部分（页面首次加载执行） ====================
    
    /* --------- gsap 动画 --------- */
    function animateParagraphs() {
        gsap.registerPlugin(ScrollTrigger);

        $('.post__content > *, .up').each((i, el) => {
            gsap.fromTo(el, {
                opacity: 0,
                y: 30,
                pointerEvents: 'none'
            }, {
                opacity: 1,
                y: 0,
                duration: 0.3,
                delay: i * 0.01,          // 0.01 s 步长，足够短
                pointerEvents: 'all',
                scrollTrigger: {
                    trigger: el,          // 以段落本身触发
                    start: 'top 110%',
                    once: false,          // 允许重复
                    toggleActions: 'restart none none reverse'
                }
            });
        });
    }
    
    // 页面加载时执行一次
    animateParagraphs();
    
    /* --------- 导航激活状态设置 --------- */
    function setActiveLink() {
        const currentUrl = window.location.href;
        const links = document.querySelectorAll('.site-nav__dropdown-item > a');

        // 先移除所有 mm-active
        links.forEach(link => {
            link.classList.remove('mm-active');
            link.parentElement.classList.remove('mm-active');
        });

        // 再添加匹配的类
        links.forEach(link => {
            if (link.href === currentUrl) {
                link.classList.add('mm-active');
                link.parentElement.classList.add('mm-active');
            }
        });
    }
    
    // 页面加载时执行一次
    setActiveLink();

    /* --------- 评论表情功能 --------- */
    function bindEmojiButton() {
        // 绑定点击事件
        $('.emoji-btn').off('click').on('click', function() {
            $('.comment-emoji').toggleClass('show');
        });
    }

    // 页面加载时执行一次
    bindEmojiButton();

    /* --------- 评论提交功能 --------- */
    function bindCommentForm() {
        $('.comment-form').off('submit').on('submit', function(event) {
            event.preventDefault();
            var commentdata = $(this).serializeArray();
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: commentdata,
                beforeSend: function() {
                    // 在提交前的逻辑
                },
                error: function(request) {
                    // 错误处理逻辑
                },
                success: function(data) {
                    $('#submitComment').addClass('submit').text('发表评论');
                    var error = /<title>Error<\/title>/;
                    if (error.test(data)) {
                        var text = data.match(/<div(.*?)>(.*?)<\/div>/is);
                        var str = '发生了未知错误'; if (text != null) str = text[2];
                        var text = $("#textarea").val();
                        var author = $("#author").val();
                        var mail = $("#mail").val();
                        var newUrl = str.replace(".html", ".html/comment?text=" + text + "&author=" + author + "&mail=" + mail + "&url=");
                    } else {
                        $('#comment-parent').remove();
                        $("#textarea").val('');
                        $(".comment-respond textarea").attr('placeholder', '评论成功！');
                        $('#cancelReply').text('').css('display', 'none');

                        $('.comment').html($('.comment', data).html());
                        $('.Comments-lists').html($('.Comments-lists', data).html());

                        var biggestNum = 0;
                        $('li[id^="li-comment-"]').each(function() {
                            var currentNum = parseInt($(this).attr('id').replace('li-comment-', ''), 0);
                            if (currentNum > biggestNum) {
                                biggestNum = currentNum;
                            }
                        });
                        $("html, body").animate({
                            scrollTop: $('#li-comment-' + biggestNum).offset().top - 60 + "px"
                        }, {
                            duration: 600,
                            easing: "linear"
                        });
                    }
                    initCommentReply();
                    UnifiedLazyLoad();
                }
            });
        });
    }

    // 页面加载时执行一次
    bindCommentForm();

    /* --------- 评论回复功能 --------- */
    // 定义 createReply 函数
    window.createReply = function(coid, author) {
        $('.comment-form').addClass('Comments_publisher');
        console.log('#coid-' + coid);
        $('#comment-parent').remove();
        $('#comment-form').append('<input type="hidden" name="parent" id="comment-parent" value="' + coid + '">');
        $('#cancelReply').html("<span>取消回复：" + author + "</span>").css('display', 'inline-flex');
        $("html, body").animate({
            scrollTop: $('#comment-form').offset().top - 120 + "px"
        }, {
            duration: 250,
            easing: "linear"
        });
        $('.comment-respond textarea').attr('placeholder', '正在回复：' + author);
        $('#textarea').focus();
    };

    // 定义 cancelReply 函数
    window.cancelReply = function() {
        $('#comment-parent').remove();
        $('#cancelReply').text('').css('display', 'none');
        $('.comment-respond textarea').attr('placeholder', '来都来了，说点什么呗~');
    };

    // 绑定点击事件
    function bindReplyEvents() {
        $('.comment-reply-link').off('click').on('click', function(event) {
            event.preventDefault();
            const coid = $(this).data('coid');
            const author = $(this).data('author');
            window.createReply(coid, author);
        });

        $('#cancelReply').off('click').on('click', function(event) {
            event.preventDefault();
            window.cancelReply();
        });
    }

    // 页面加载时执行一次
    bindReplyEvents();
    
    /* --------- 评论回复事件处理函数 --------- */
    function initCommentReply() {
        /* 点击评论按钮显示隐藏评论区域 */
        $('.comment-reply').unbind('click').bind('click', function (e) {
            e.stopPropagation();
            // 查找并切换回复框显示状态
            $(this).parents('li').find('.dynamic-reply').toggle();
        });
    }
    initCommentReply();
    
    /* --------- 走心轮播 --------- */
    function initCarousel() {
        $('.commentator-slick').not('.slick-initialized').each(function () {
            $(this).slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 3000,
                pauseOnHover: true,
                pauseOnFocus: true,
                arrows: false,
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 1, slidesToScroll: 1 } },
                    { breakpoint: 600, settings: { arrows: false, dots: true } }
                ]
            });
        });
        /* 自定义上一页 / 下一页按钮 */
        $('.slick-custom-prev').on('click', function () {
            $('.commentator-slick').slick('slickPrev');
        });
        $('.slick-custom-next').on('click', function () {
            $('.commentator-slick').slick('slickNext');
        });
    }

    // 页面加载时执行一次
    initCarousel();
    
    /* --------- 缩略图轮播 --------- */
    // 先销毁已存在的轮播实例（关键步骤）
    function destroyCarousel() {
        if ($('.article-carousel').hasClass('slick-initialized')) {
            $('.article-carousel').slick('unslick'); // 彻底销毁实例
        }
    }
    
    function initArticle() {
        // 先销毁可能存在的旧实例
        destroyCarousel();
        $('.article-carousel').not('.slick-initialized').each(function () {
            $(this).slick({
                dots: false,
                infinite: false,
                speed: 500,
                fade: false, // 关闭淡入淡出效果（淡入淡出通常用于单张显示）
                cssEase: 'linear',
                autoplay: true,
                slidesToShow: 2, // 明确设置显示2张
                slidesToScroll: 1, // 每次滚动1张
                autoplaySpeed: 4000,
                pauseOnHover: true,
                pauseOnFocus: true,
                arrows: false,
                // 新增响应式设置，确保在不同屏幕尺寸下都能正确显示
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2 // 小屏幕也保持显示2张（可根据需要调整）
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1 // 超小屏幕显示1张（可选）
                        }
                    }
                ]
            });
        });
            
            // 自定义上一页 / 下一页按钮
        $('.slick-custom-prev').on('click', function () {
            $('.article-carousel').slick('slickPrev');
        });
            
        $('.slick-custom-next').on('click', function () {
            $('.article-carousel').slick('slickNext');
        });
    }

    initArticle();

    /* --------- Fancybox 初始化 --------- */
    function initFancybox() {
        // 初始化 Fancybox
        Fancybox.bind("[data-fancybox='gallery'],[data-fancybox='header']", {
            hideScrollbar: false,
            idle: false,
            Carousel: {
                transition: "slide",
            },
        });

        // 绑定点击事件到 .zoom 按钮
        document.querySelectorAll('.zoom').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const parentCard = button.closest('.work-card');
                const image = parentCard?.querySelector('a[data-fancybox="gallery"],a[data-fancybox="header"]');
                if (image) image.click();
            });
        });
    }

    // 页面加载时执行一次
    initFancybox();
    
    /* --------- 需要初始化懒加载 --------- */
    let lazyLoadInstance = null;
    
    function initLazyLoad() {
        // 先销毁旧实例（避免重复监听，内存泄漏）
        if (lazyLoadInstance) {
            lazyLoadInstance.destroy();
        }
        // 新建懒加载实例（可自定义配置，覆盖默认值）
        lazyLoadInstance = new UnifiedLazyLoad({
            // 示例：自定义配置（根据需求调整）
            imgRootMargin: '200px 0px', // 提前300px加载图片
            defaultImage: '', // 占位图
            useNativeLazyLoad: false, // 禁用原生懒加载（优先用IntersectionObserver）
            imgLoadedClass: 'lazy-loaded' // 自定义加载完成类名
        });
    }
    
    /* --------- 加载状态管理工具 --------- */
    function loader(parent, loadingText = '加载中', errorText = '加载失败，点击重试') {
	    const loader = document.createElement('loaed');
    	loader.textContent = loadingText;
    	parent.appendChild(loader);
    	return {
    		complete: () => {
    			loader.classList.add('loaded');
    			setTimeout(() => {
    				loader.remove();
    			}, 1000);
    		},
    		error: (retryCallback) => {
    			loader.textContent = errorText;
    			loader.classList.add('error');
    			loader.style.cursor = 'pointer';
    			loader.addEventListener('click', () => {
    				loader.textContent = loadingText;
    				loader.classList.remove('error');
    				if (typeof retryCallback === 'function') {
    					retryCallback();
    				}
    			}, { once: true });
    		}
    	};
    }
    
    /* --------- 文章视频切换功能 --------- */
    
    function initVideoPlayer() {
        const articleVideo = document.querySelector('.article-video');
        if (articleVideo) {
            const playerIframe = articleVideo.querySelector('.play iframe');
            const playerBaseUrl = playerIframe.getAttribute('data-player');
            const episodeItems = articleVideo.querySelectorAll('.episodes .item');
            
            if (episodeItems.length > 0) {
                // 为每个剧集项添加点击事件
                episodeItems.forEach(item => {
                    item.addEventListener('click', function() {
                        // 移除其他项的active类
                        episodeItems.forEach(el => el.classList.remove('active'));
                        // 为当前项添加active类
                        this.classList.add('active');
                        
                        // 获取视频地址并更新iframe
                        const videoUrl = this.getAttribute('data-src');
                        const fullVideoUrl = playerBaseUrl + encodeURIComponent(videoUrl);
                        playerIframe.setAttribute('src', fullVideoUrl);
                    });
                });
                
                // 自动触发第一个剧集的点击事件
                episodeItems[0].click();
            }
        }
    }
    initVideoPlayer();
    
    // ==================== SWUP 页面切换后重新初始化的部分 ====================
    // 每次内容替换（前进 / 后退 / 刷新）后重播动画
    swup.hooks.on('content:replace', async () => {
      // 等待DOM完全更新（比setTimeout更可靠）
      await new Promise(resolve => requestAnimationFrame(resolve));
      
      // 执行所有需要重新初始化的功能
      animateParagraphs();      // GSAP动画
      setActiveLink();          // 导航激活状态
      bindEmojiButton();        // 表情按钮
      bindCommentForm();        // 评论表单
      bindReplyEvents();        // 回复功能
      initCommentReply();       // 动态回复
      initCarousel();           // 轮播
      initArticle();            // 轮播
      initFancybox();           // Fancybox
      Prism.highlightAll();     // 代码高亮
      initLazyLoad();           // 懒加载
      initVideoPlayer();        // 文章视频
      // 如果你使用了GSAP的ScrollTrigger，添加这个确保动画正确触发
      if (window.ScrollTrigger) {
        ScrollTrigger.refresh();
      }
    });

    window.addEventListener('beforeunload', () => {
        if (lazyLoadInstance) {
            lazyLoadInstance.destroy();
            lazyLoadInstance = null;
        }
    });
    
});