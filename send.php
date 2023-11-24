<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $m;

function cron_sign_bark() {
    global $m;
    $today=date("Y-m-d");
    $lastday=option::get('yuu_sign_bark');
    if ((time()-1396281600)%86400<21600)
        return '未到发送Bark通知时间';
    if ($today!=$lastday)
        option::set('yuu_sign_bark',$today);
    else return '今日任务已经执行完毕';
    $query = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users`");
    while ($fetch = $m->fetch_array($query)) {
        $name = $fetch['name'];
        $id = $fetch['id'];
        if(option::uget('yuu_bark_enable',$id) == 0){
            return "通知未开启";
        }
        $barkUrl = option::uget('yuu_bark_url', $id);
        if (empty($barkUrl)) {
            continue; // 如果未设置 barkUrl，则跳过此用户
        }

        $notificationContent = "用户名: $name\n贴吧列表:\n";
        $query2 = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `uid` = $id");
        while ($tiebaInfo = $m->fetch_array($query2)) {
            $tiebaName = $tiebaInfo['tieba'];
            $status = $tiebaInfo['status'] == 0 ? '签到成功' : '签到失败';
            $notificationContent .= "$tiebaName, $status\n";
        }

        sendBarkNotification($barkUrl, $notificationContent);
    }
    return '通知发送成功！';
}

function sendBarkNotification($url, $content) {
    $notificationUrl = $url . "?title=签到通知&body=" . urlencode($content);
    file_get_contents($notificationUrl); // 发送请求
}
?>