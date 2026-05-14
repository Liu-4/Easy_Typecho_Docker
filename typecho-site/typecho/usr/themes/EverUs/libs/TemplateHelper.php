<?php
/**
 * 模板工具函数：前端页面调用的辅助方法
 */

/**
 * 1. 获取资源路径（本地/自定义URL）
 * @param string $assets 资源相对路径（如：assets/js/main.js）
 * @param bool $type 是否直接输出（true=输出，false=返回字符串）
 * @return string|null 资源URL（type=false时返回）
 */
function _getAssets($assets, $type = true)
{
    $options = Helper::options();
    // 优先使用自定义资源URL，否则用主题默认路径
    $assetsURL = $options->AssetsURL 
        ? $options->AssetsURL . '/' . $assets 
        : $options->themeUrl . '/' . $assets;

    if ($type) {
        echo $assetsURL;
    } else {
        return $assetsURL;
    }
}

/**
 * 2. 通过邮箱生成头像地址（QQ头像优先，其次Gravatar）
 * @param string $mail 用户邮箱
 */
function _getAvatarByMail($mail)
{
  $gravatarsUrl = Helper::options()->CustomAvatarSource ? Helper::options()->CustomAvatarSource : 'https://gravatar.helingqi.com/avatar/';
  $mailLower = strtolower($mail);
  $md5MailLower = md5($mailLower);
  $qqMail = str_replace('@qq.com', '', $mailLower);
  if (strstr($mailLower, "qq.com") && is_numeric($qqMail) && strlen($qqMail) < 11 && strlen($qqMail) > 4) {
    echo 'https://thirdqq.qlogo.cn/g?b=qq&nk=' . $qqMail . '&s=100';
  } else {
    echo $gravatarsUrl . $md5MailLower . '?d=mm';
  }
};


/**
 * 3. 获取文章缩略图（自定义优先，其次提取内容图片，最后用默认图）
 * @param object $item Typecho文章对象（$archive或$post）
 * @return array 缩略图URL数组（至少1张，最多补充到3张）
 */
function _getThumbnails($item)
{
    $thumbnails = [];
    // 正则规则：匹配HTML图片、Markdown图片（行内/脚注）
    $patterns = [
        '/\<img.*?src\=\"(.*?)\"[^>]*>/i',          // HTML图片
        '/\!\[.*?\]\((http(s)?:\/\/.*?(jpg|jpeg|gif|png|webp))/i', // Markdown行内图片
        '/\[.*?\]:\s*(http(s)?:\/\/.*?(jpg|jpeg|gif|png|webp))/i'  // Markdown脚注图片
    ];

    // 1. 优先使用自定义缩略图（文章字段thumb）
    if ($item->fields->thumb) {
        $thumbnails = explode("\r\n", $item->fields->thumb);
    }

    // 2. 提取文章内容中的图片补充
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $item->content, $matches)) {
            $thumbnails = array_merge($thumbnails, $matches[1]);
        }
    }

    // 3. 去重+补充默认图（确保至少1张，最多3张）
    $thumbnails = array_unique($thumbnails);
    while (count($thumbnails) < 3) {
        $defaultThumb = _getAssets('assets/img/1.jpg', false);
        $thumbnails[] = $defaultThumb;
    }

    return $thumbnails;
}

/**
 * 4. 格式化文章发布时间（友好显示：如“1小时前”“3天前”）
 * @param int $time 时间戳（文章发布时间）
 * @return string 格式化后的时间文本
 */
function formatTime($time)
{
    $time = intval($time);
    $currentTime = time();
    $timeDiff = $currentTime - $time;

    // 未来时间（异常情况）：直接显示日期
    if ($timeDiff < 0) {
        return date('Y-m-d', $time);
    }

    $yearDiff = date('Y', $currentTime) - date('Y', $time);

    // 时间差判断：按秒→分→时→天→月→年
    switch (true) {
        case $timeDiff == 0:
            return '刚刚';
        case $timeDiff < 60:
            return $timeDiff . '秒前';
        case $timeDiff < 3600:
            return floor($timeDiff / 60) . '分钟前';
        case $timeDiff < 86400:
            return floor($timeDiff / 3600) . '小时前';
        case $timeDiff < 2592000: // 30天内
            $yesterday = strtotime(date('Ymd', strtotime("-1 day")));
            $beforeYesterday = strtotime(date('Ymd', strtotime("-2 days")));
            if ($time > $yesterday) return '昨天';
            if ($time > $beforeYesterday) return '前天';
            return floor($timeDiff / 86400) . '天前';
        case $timeDiff < 31536000: // 1年内
            $monthDiff = $yearDiff == 0 
                ? date('m', $currentTime) - date('m', $time) - 1 
                : 11 - date('m', $time) + date('m', $currentTime);
            return $monthDiff == 0 
                ? floor($timeDiff / 86400) . '天前' 
                : $monthDiff . '个月前';
        default: // 1年以上
            return $yearDiff . '年前';
    }
}