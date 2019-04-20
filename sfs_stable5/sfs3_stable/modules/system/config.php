<?php
//$Id: config.php 9136 2017-09-01 08:07:32Z smallduh $
include_once "../../include/config.php";
require_once "./module-cfg.php";
$path_str="/system";
set_upload_path($path_str);
$temp_path=$UPLOAD_PATH.$path_str;

//檢查 table 欄位, 有些學校因資料庫升級失敗, 欄位缺失
function check_table_bad_login() {
    global $CONN;

    $sql="CREATE TABLE IF NOT EXISTS `bad_login` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `log_id` varchar(20) NOT NULL,
  `log_ip` varchar(15) NOT NULL,
  `err_kind` varchar(100) NOT NULL,
  `log_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`) );";

    $CONN->Execute($sql);
}


?>
