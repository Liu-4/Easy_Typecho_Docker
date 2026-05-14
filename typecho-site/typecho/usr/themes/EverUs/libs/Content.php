<?php
Typecho_Plugin::factory('admin/write-post.php')->bottom = array('Editor', 'edit');
Typecho_Plugin::factory('admin/write-page.php')->bottom = array('Editor', 'edit');
class Editor
{
    public static function edit()
    {
        echo "<link rel='stylesheet' href='" . Helper::options()->themeUrl . '/assets/plugin/edit/option.css' . "'>";
        echo "<script src='" . Helper::options()->themeUrl . '/assets/plugin/edit/editor.js' . "'></script>";
        echo "<link href='//unpkg.com/layui@2.11.6/dist/css/layui.css' rel='stylesheet'>";
        echo "<script src='//unpkg.com/layui@2.11.6/dist/layui.js'></script>";
    }
}

function get_Lazyload($type = true)
{
    return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
}

function article_ccttext($post, $login) {
    $content = $post->content;

    $content = preg_replace_callback('/<img\s+([^>]*?)src="([^"]+)"([^>]*?)alt="([^"]+)"([^>]*?)>/i', function($matches) {
        // 重组属性，保留所有原始属性
        return '<a href="'.$matches[2].'" data-fancybox="gallery">'.
               '<img '.$matches[1].'src="'.$matches[2].'"'.$matches[3].'alt="'.$matches[4].'"'.$matches[5].'>'.
               '</a>';
    }, $content);

    $content = preg_replace('/<p\s*>\s*<a\s+href="([^"]+)"\s+data-fancybox="gallery"\s*>[^<]*<img\s+[^>]*?src="([^"]+)"[^>]*?alt="([^"]+)"[^>]*?>\s*<\/a>\s*<\/p>/is', 
        '<div class="gallery-item up">
            <div class="work-card">
                <a href="$1" data-fancybox="gallery" data-fancybox="post" class="links">
                    <div class="lazy-item">
                        <img src="' . get_Lazyload() . '" alt="$3" data-src="$2">
                        <loaed>图片加载中</loaed>
                    </div>
                </a>
                <div class="mil-descr">
                    <a class="zoom">
                        <button class="Nug Nug-zhankai2 zoom" type="button"></button>
                    </a>
                </div>
            </div>
        </div>', 
        $content);
        
        // 只匹配不含 class 属性的 <a> 标签
        $content = preg_replace(
            '/<a\s+(?![^>]*class=)([^>]*href="([^"]+)")([^>]*)/is',
            '<a $1$3 class="text-link"',
            $content);
        
        // 音乐
        $content = preg_replace(
        '/{meting-js server="([\s\S]*?)" type="song" id="([\s\S]*?)"}{\/meting-js}/is',
        '<meting-js server="${1}" type="song" id="${2}" data-autoplay="false" class="meting-lazy"></meting-js>',
        $content);
        
        $content = preg_replace(
            '#<p\s*>(\s*<meting-js\b[^>]*>.*?</meting-js>\s*)</p>#is',
            '<div class="meting-wrapper">
                $1
                <loaed>加载中</loaed>
            </div>',
            $content
        );
        
        // 视频
        $content = preg_replace_callback(
            '/{(video|bvideo)\s+([^}]*)}{\/\1}/is',          // 1 捕获标签名，2 捕获属性串
            function ($m) {
                $tag = $m[1];          // video 或 bvideo
                $attr = [];            // 把属性串解析成键值数组
                preg_match_all('/(\w+)="([^"]*)"/', $m[2], $kv, PREG_SET_ORDER);
                foreach ($kv as $v) $attr[$v[1]] = $v[2];

                if ($tag === 'bvideo') {
                    $bv   = $attr['bv']   ?? '';
                    $avid = $attr['avid'] ?? '';
                
                    // 至少有一个
                    if (!$bv && !$avid) return $m[0];  
                
                    // 构造查询串
                    $query = $bv ? 'bvid=' . $bv : 'aid=' . $avid;
                
                    return '<iframe data-src="//player.bilibili.com/player.html?' . $query .
                           '" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe>';
                }

                if ($tag === 'video') {
                    $src = $attr['src'] ?? '';
                return '<video data-src="' .
                       htmlspecialchars($src, ENT_QUOTES) . '" controls="" autoplay="" name="media"><source src="' .
                       htmlspecialchars($src, ENT_QUOTES) . '" type="video/mp4"></video>';
            }
        
                return $m[0]; // 没匹配到就原样返回
            },
            $content
        );
        
        /* 视频短码外再包一层 <media-embed> */
        $content = preg_replace(
            '#<p\s*>(\s*(?:<iframe\b[^>]*\bdata-src="[^"]*player\.bilibili\.com[^"]*"[^>]*></iframe>|<video\b[^>]*>.*?</video>)\s*)</p>#is',
            '<media-embed>
                $1
                <loaed>加载中</loaed>
            </media-embed>',
            $content
        );
        
       /* ① 先把 {video} 转成真正的 <video> */
        $content = preg_replace(
            '/{video\s+src="([^"]*)"}\s*{\/video}/i',
            '<video data-src="$1" controls style="max-width:100%"></video>',
            $content
        );
        
        /* ② 给裸 <video> 再包一层 <media-embed>，方便 JS 找 */
        $content = preg_replace(
            '#<p\s*>(\s*<video\b[^>]*\bdata-src="[^"]*"[^>]*>.*?</video>\s*)</p>#is',
            "<media-embed>\n$1\n<loaed>加载中</loaed>\n</media-embed>",
            $content
        );


    // 输出处理后的内容
    echo $content;
}

function get_Abstract($item, $num)
{
    $abstract = "";
    if ($item->password) {
        $abstract = "此文章已加密";
    } else {
        if ($item->fields->post_abstract) {
            $abstract = $item->fields->post_abstract;
        } else {
            $abstract = strip_tags($item->excerpt);

            /* 1. 先把音乐短码换成友好文字 */
            $abstract = preg_replace_callback(
                '/{meting-js\s+server="(netease|tencent|kugou)"[^}]+}{\/meting-js}/i',
                function ($m) {
                    $map = [
                        'netease' => '[网易云音乐]',
                        'tencent' => '[QQ音乐]',
                        'kugou'   => '[酷狗音乐]',
                    ];
                    return $map[strtolower($m[1])] ?? '[音乐]';
                },
                $abstract
            );

            /* 2. 把视频短码换成友好文字 */
            $abstract = preg_replace('/{bvideo[^}]+}{\/bvideo}/i', '[B站视频]', $abstract);
            $abstract = preg_replace('/{video[^}]+}{\/video}/i',   '[本地视频]', $abstract);

            /* 3. 去掉剩余所有 {xxx} 短码 */
            $abstract = preg_replace('/\{[^{}]+\}/', '', $abstract);

            /* 4. 去掉你原来的占位符 */
            $abstract = preg_replace('/\-o\-/', '', $abstract);
        }
    }

    if ($abstract === '') $abstract = "此文章暂无简介";
    return mb_substr(trim($abstract), 0, $num);
}