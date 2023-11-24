<?php
function callback_init() {
    cron::set('sign_bark', 'plugins/yuu_sign_bark/send.php', '0', '0', '0');
}

function callback_remove() {
    cron::del('sign_bark');
}
?>