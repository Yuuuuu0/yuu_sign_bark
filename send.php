<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $m;

function cron_sign_bark() {
    global $m;
    $currentHourMinute = date("H:i");
    $today = date("Y-m-d");

    $query = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."users`");
    while ($fetch = $m->fetch_array($query)) {
        $name = $fetch['name'];
        $id = $fetch['id'];

        // 获取通知参数设置
        $barkEnable = option::uget('yuu_bark_enable',$id);
        $barkUrl = option::uget('yuu_bark_url', $id);
        $barkKey = option::uget('yuu_bark_key', $id);
        $barkTime = option::uget('yuu_bark_time', $id);
        if ($barkEnable == 0 || empty($barkUrl) || empty($barkTime) || empty($barkKey)) {
            continue; // 未开启通知或参数错误，跳过此用户
        }

        $lastNotificationDate = option::uget('yuu_last_notification_date', $id);
        // 判断是否进行过通知&是否到达通知时间
        if ($today == $lastNotificationDate || $currentHourMinute != $barkTime) {
            continue; // 今天已进行过通知或当前时间不匹配，跳过此用户
        }

        $notificationContent = "用户名: $name\n贴吧列表:\n";
        $query2 = $m->query("SELECT * FROM  `".DB_NAME."`.`".DB_PREFIX."tieba` WHERE `uid` = $id");
        while ($tiebaInfo = $m->fetch_array($query2)) {
            $tiebaName = $tiebaInfo['tieba'];
            $status = $tiebaInfo['status'] == 0 ? '签到成功' : '签到失败';
            $notificationContent .= "$tiebaName, $status\n";
        }

        // 发送通知
        sendBarkNotification($barkUrl, $notificationContent, $barkKey);
        // 更新最后通知日期
        option::uset('yuu_last_notification_date', $today, $id);
    }
    return '通知发送成功！';
}

function sendBarkNotification($url, $content, $deviceKey) {
    $data = json_encode([
        'title' => '贴吧签到通知',  
        'group' => '贴吧签到通知',
        'body' => $content,
        'device_key' => $deviceKey
    ]);

    $options = [
        'http' => [
            'header' => "Content-Type: application/json; charset=utf-8",
            'method' => 'POST',
            'content' => $data
        ]
    ];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}
?>
