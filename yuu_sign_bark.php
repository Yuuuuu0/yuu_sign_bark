<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/*
Plugin Name: 签到Bark通知
Version: 1.0
Plugin URL: https://github.com/Yuuuuu0/yuu_sign_bark
Description: 贴吧签到后自动Bark提醒
Author: Yuuuuu0
Author URL: https://github.com/Yuuuuu0
*/

function yuu_sign_bark_setting() {
	global $m;
	?>
	<tr><td>开启签到Bark通知</td>
	<td>
		<input type="radio" name="yuu_bark_enable" value="1" <?php if (option::uget('yuu_bark_enable') == 1) { echo 'checked'; } ?> > 是&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="yuu_bark_enable" value="0" <?php if (option::uget('yuu_bark_enable') != 1) { echo 'checked'; } ?> > 否
	</td>
	</tr>
	<tr><td>Bark地址</td>
	<td>
		<input type="text" class="form-control" name="yuu_bark_url" value="<?php echo option::uget('yuu_bark_url'); ?>" >
	</td>
	</tr>
	<tr><td>推送时间</td>
	<td>
	    <input type="time" name="yuu_bark_time" value="<?php echo option::uget('yuu_bark_time'); ?>">
	</td>
	<?php
}
function yuu_sign_bark_set() {
	global $PostArray;
	if (!empty($PostArray)) {
		$PostArray[] = 'yuu_bark_enable';
		$PostArray[] = 'yuu_bark_url';
		$PostArray[] = 'yuu_bark_time';
	}
}

addAction('set_save1','yuu_sign_bark_set');
addAction('set_2','yuu_sign_bark_setting');
?>
