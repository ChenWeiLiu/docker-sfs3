<?php
//升級 sfs_text 資料表
include "../include/config.php";
include "../include/sfs_case_PLlib.php";
include "update_function.php";
if(!check_field($mysql_db,$conID,'sfs_text','t_order_id')){
	$CONN->Execute("ALTER TABLE `sfs_text` CHANGE `d_id` `d_id` VARCHAR( 20 ) NOT NULL ") or trigger_error("更新錯誤",E_USER_ERROR);
	$CONN->Execute("ALTER TABLE `sfs_text` ADD `t_order_id` INT NOT NULL AFTER `t_id`");
	$CONN->Execute("UPDATE `sfs_text` SET t_order_id = d_id");
	header("Location: ".$_SERVER['HTTP_REFERER']);
}
else {
	trigger_error("sfs_text 已升級過了，檢查一下是否還有其他原因!",E_USER_ERROR);
}
