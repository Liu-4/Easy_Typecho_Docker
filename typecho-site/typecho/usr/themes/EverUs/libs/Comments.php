<?php
// 防止直接访问
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 评论加@
 * @param $coid
 */
function getPermalinkFromCoid($coid) {
    $db = Typecho_Db::get();
    $row = $db->fetchRow($db->select('author')->from('table.comments')->where('coid = ? AND status = ?', $coid, 'approved'));
    if (empty($row)) return '';
    return '<a class="comments-at" href="#comment-'.$coid.'"> @ '.$row['author'].'</a>';
}

function get_user_last_login($mail,$num = true){
    $db  = Typecho_Db::get();
    $row = $db->fetchRow($db
        ->select('created')
        ->from('table.comments')
        ->where('mail = ?', $mail)
        ->order('created', Typecho_Db::SORT_DESC)
    );
    $from = $row['created'];
    $now = time();
    $between = $now - $from;
    if($num){
        if ($between > 0 && $between < 604800) {
            return ('#2bde3f');
        }elseif ($between >= 604800 && $between < 2592000) {
            return ('#ffd86a');
        }elseif ($between >= 2592000){
            return ('#ff6a6a');
        }
    }else{
        if ($between > 0 && $between < 86400) {
            return '一天之内';
        }elseif ($between >= 86400 && $between < 604800) {
            return '一周之内';
        }elseif ($between >= 604800 && $between < 2592000) {
            return '一月之内';
        }elseif ($between >= 2592000 && $between < 7776000) {
            return '三月之内';
        }elseif ($between >= 7776000){
            return '很久没有';
        }
    }
}

function cat_check_XSS($text)
{
    $isXss = false;
    $list = array(
        '/onabort/is',
        '/onblur/is',
        '/onchange/is',
        '/onclick/is',
        '/ondblclick/is',
        '/onerror/is',
        '/onfocus/is',
        '/onkeydown/is',
        '/onkeypress/is',
        '/onkeyup/is',
        '/onload/is',
        '/onmousedown/is',
        '/onmousemove/is',
        '/onmouseout/is',
        '/onmouseover/is',
        '/onmouseup/is',
        '/onreset/is',
        '/onresize/is',
        '/onselect/is',
        '/onsubmit/is',
        '/onunload/is',
        '/eval/is',
        '/ascript:/is',
        '/style=/is',
        '/width=/is',
        '/width:/is',
        '/height=/is',
        '/height:/is',
        '/src=/is',  
    );
    if (strip_tags($text)) {
        for ($i = 0; $i < count($list); $i++) {
            if (preg_match($list[$i], $text) > 0) {
                $isXss = true;
                break;
            }
        }
    } else {
        $isXss = true;
    };
    return $isXss;
}

function cat_comment_changetext_pre($text)
{
    if (cat_check_XSS($text)) {
        echo "该回复疑似异常，已被系统拦截！";
    } else {
        $text = preg_replace('/{linkname name="([\s\S]*?)" link="([\s\S]*?)"}/', '<a style="color: var(--theme);margin: 0 0.25rem;" href="${2}" target="_blank">${1}</a>', $text);
        if(Helper::options()->cat_comment_IMGcode){
            $text = preg_replace('/{' . Helper::options()->cat_comment_IMGcode . '}([\s\S]*?){\/' . Helper::options()->cat_comment_IMGcode . '}/', '<img src="' . get_Lazyload() . '" class="isfancy_ungallery comment_img_little lazyload" data-src="${1}" alt="" title="">', $text);
        }else{
            $text = preg_replace('/{IMG}([\s\S]*?){\/IMG}/', '<img src="' . get_Lazyload() . '" class="isfancy_ungallery comment_img_little lazyload" data-src="${1}" alt="" title="">', $text);
        }
        $text = preg_replace('/{netmusic}([\s\S]*?){\/netmusic}/', '<meting-js server="netease" type="song" id="${1}"></meting-js>', $text);
        if(Helper::options()->cat_bili_choose == 'on'){
            $text = preg_replace('/{bilibili p="([\s\S]*?)" q="([\s\S]*?)"}([\s\S]*?){\/bilibili}/', '<cat_article_bili><iframe src="' . Helper::options()->themeUrl . '/api/bilibili/?bv=${3}&p=${1}&q=${2}&format=dash&otype=dplayer" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></cat_article_bili>', $text);
            $text = preg_replace('/{bilibili}([\s\S]*?){\/bilibili}/', '<cat_article_bili><iframe src="' . Helper::options()->themeUrl . '/api/bilibili/?bv=${1}&p=1&q=80&format=dash&otype=dplayer" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></cat_article_bili>', $text);
        }elseif(Helper::options()->cat_bili_choose == 'office'){
            $text = preg_replace('/{bilibili p="([\s\S]*?)" q="([\s\S]*?)"}([\s\S]*?){\/bilibili}/', '<cat_article_bili><iframe src="https://api.injahow.cn/bparse/?bv=${3}&p=${1}&q=${2}&format=dash&otype=dplayer" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></cat_article_bili>', $text);
            $text = preg_replace('/{bilibili}([\s\S]*?){\/bilibili}/', '<cat_article_bili><iframe src="https://api.injahow.cn/bparse/?bv=${1}&p=1&q=80&format=dash&otype=dplayer" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></cat_article_bili>', $text);
        }elseif(Helper::options()->cat_bili_choose == 'html5'){
            $text = preg_replace('/{bilibili p="([\s\S]*?)" q="([\s\S]*?)"}([\s\S]*?){\/bilibili}/', '<cat_article_bili><iframe src="//player.bilibili.com/player.html?&bvid=${3}&&page=${1}" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></cat_article_bili>', $text);
            $text = preg_replace('/{bilibili([\s\S]*?)}([\s\S]*?){\/bilibili}/', '<cat_article_bili><iframe src="https://www.bilibili.com/blackboard/html5mobileplayer.html?bvid=${2}&amp;page=${1}&amp;as_wide=1&amp;danmaku=0&amp;hasMuteButton=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></cat_article_bili>', $text);
        }else{
            $text = preg_replace('/{bilibili p="([\s\S]*?)" q="([\s\S]*?)"}([\s\S]*?){\/bilibili}/', '<cat_article_bili><iframe src="//player.bilibili.com/player.html?&bvid=${3}&&page=${1}" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></cat_article_bili>', $text);
            $text = preg_replace('/{bilibili([\s\S]*?)}([\s\S]*?){\/bilibili}/', '<cat_article_bili><iframe src="//player.bilibili.com/player.html?&bvid=${2}&&page=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></cat_article_bili>', $text);
        }
        return $text;
    }
}

function cat_comment_changetext($text)
{
    $text = cat_comment_changetext_pre($text);
    $text = substr_replace(substr_replace($text,"",-4,4),"",0,3);
    return $text;
}

function diary_he_weather($weather){
    if(is_numeric($weather)){
        switch ($weather){
            case "100": return "晴"; break;
            case "101": return "多云"; break;
            case "102": return "少云"; break;
            case "103": return "晴间多云"; break;
            case "104": return "阴"; break;
            case "150": return "晴"; break;
            case "151": return "多云"; break;
            case "152": return "少云"; break;
            case "153": return "晴间多云"; break;
            case "300": return "阵雨"; break;
            case "301": return "强阵雨"; break;
            case "302": return "雷阵雨"; break;
            case "303": return "强雷阵雨"; break;
            case "304": return "雷阵雨伴有冰雹"; break;
            case "305": return "小雨"; break;
            case "306": return "中雨"; break;
            case "307": return "大雨"; break;
            case "308": return "极端降雨"; break;
            case "309": return "毛毛雨/细雨"; break;
            case "310": return "暴雨"; break;
            case "311": return "大暴雨"; break;
            case "312": return "特大暴雨"; break;
            case "313": return "冻雨"; break;
            case "314": return "小到中雨"; break;
            case "315": return "中到大雨"; break;
            case "316": return "大到暴雨"; break;
            case "317": return "暴雨到大暴雨"; break;
            case "318": return "大暴雨到特大暴雨"; break;
            case "350": return "阵雨"; break;
            case "351": return "强阵雨"; break;
            case "399": return "雨"; break;
            case "400": return "小雪"; break;
            case "401": return "中雪"; break;
            case "402": return "大雪"; break;
            case "403": return "暴雪"; break;
            case "404": return "雨夹雪"; break;
            case "405": return "雨雪天气"; break;
            case "406": return "阵雨夹雪"; break;
            case "407": return "阵雪"; break;
            case "408": return "小到中雪"; break;
            case "409": return "中到大雪"; break;
            case "410": return "大到暴雪"; break;
            case "456": return "阵雨夹雪"; break;
            case "457": return "阵雪"; break;
            case "499": return "雪"; break;
            case "500": return "薄雾"; break;
            case "501": return "雾"; break;
            case "502": return "霾"; break;
            case "503": return "扬沙"; break;
            case "504": return "浮尘"; break;
            case "507": return "沙尘暴"; break;
            case "508": return "强沙尘暴"; break;
            case "509": return "浓雾"; break;
            case "510": return "强浓雾"; break;
            case "511": return "中度霾"; break;
            case "512": return "重度霾"; break;
            case "513": return "严重霾"; break;
            case "514": return "大雾"; break;
            case "515": return "特强浓雾"; break;
            case "800": return "新月"; break;
            case "801": return "蛾眉月"; break;
            case "802": return "上弦月"; break;
            case "803": return "盈凸月"; break;
            case "804": return "满月"; break;
            case "805": return "亏凸月"; break;
            case "806": return "下弦月"; break;
            case "807": return "残月"; break;
            case "900": return "热"; break;
            case "901": return "冷"; break;
            case "999": return "未知";
        }
    }else{
        return $weather;
    }
}
// 更换钩子为 finishComment
Typecho_Plugin::factory('Widget_Feedback')->finishComment = 'themeHandleComment';

function themeHandleComment($comment, $post) {
    // 从 POST 请求中获取自定义字段的值
    $weather = isset($_POST['weather']) ? trim($_POST['weather']) : '';
    $temperature = isset($_POST['temperature']) ? trim($_POST['temperature']) : '';
    
    // 获取评论ID（此时 coid 已确定存在）
    $commentId = $comment->coid;

    // 获取数据库对象
    $db = Typecho_Db::get();

    // 更新评论记录
    $db->query($db->update('table.comments')->rows(array(
        'weather' => $weather,
        'temperature' => $temperature
    ))->where('coid = ?', $commentId));

    return $comment;
}
?>