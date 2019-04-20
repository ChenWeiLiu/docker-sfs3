<?php

// $Id: module-upgrade.php 7779 2013-11-20 16:09:00Z smallduh $
if (!$CONN) {
    echo "go away !!";
    exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/" . get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name = $upgrade_str . "2013-11-20.txt";
//改變上傳檔案記錄儲存方式
if (!is_file($up_file_name)) {
$SQL="
create table board_files (
id int(5) not null auto_increment,
b_id int(5) not null,
org_filename text not null,
new_filename text not null,
primary key (id)
) ENGINE=MyISAM;";
    $rs = $CONN->Execute($SQL);
    if ($rs) {
        $temp_query = "改變上傳檔案記錄儲存方式，增加 table: board_files  成功-- by smallduh (2013-11-20)\n $SQL";
    } else {
        $temp_query = "改變上傳檔案記錄儲存方式，增加 table: board_files 失敗 !!,請手動更新下列語法\n $SQL";
    }

    $fp = fopen($up_file_name, "w");
    fwrite($fp, $temp_query . $str);
    fclose($fp);
    unset($temp_query);
    unset($str);
}



$up_file_name = $upgrade_str . "2007-03-21.txt";
//改變上傳檔案為text型態以便可以多檔附件
if (!is_file($up_file_name)) {
    $SQL = "ALTER TABLE `board_p` CHANGE `b_upload` `b_upload` TEXT NOT NULL ";
    $rs = $CONN->Execute($SQL);
    if ($rs) {
        $temp_query = "改變上傳檔案為text型態 board_p 成功-- by infodaes (2007-03-21)\n $SQL";
    } else {
        $temp_query = "改變上傳檔案為text型態 board_p 失敗 !!,請手動更新下列語法\n $SQL";
    }

    $fp = fopen($up_file_name, "w");
    fwrite($fp, $temp_query . $str);
    fclose($fp);
    unset($temp_query);
    unset($str);
}

$up_file_name = $upgrade_str . "2007-03-20.txt";

// 加入 簽收欄位

if (!is_file($up_file_name)) {
    //增加 簽收欄位
    $query = "ALTER TABLE `board_p` ADD `b_is_sign` CHAR( 1 ) NOT NULL DEFAULT '0'";
    $CONN->Execute($query);
    $query = "ALTER TABLE `board_p` ADD `b_signs` TEXT NOT NULL";
    $CONN->Execute($query);
    $query = "ALTER TABLE `board_p` CHANGE `b_title` `b_title` VARCHAR( 60 ) NOT NULL DEFAULT ''";
    $CONN->Execute($query);
    //增加排序欄位
    $query = "ALTER TABLE `board_kind` ADD bk_order tinyint NOT NULL";
    $CONN->Execute($query);
    $fp = fopen($up_file_name, "w");
    $temp_query = "佈告欄加入簽收欄位	-- by hami (2006-6-27)";
    fwrite($fp, $temp_query);
    fclose($fp);
}

//  更改  b_con 的欄位
$up_file_name = $upgrade_str . "2007-03-23.txt";

if (!is_file($up_file_name)) {

    $query = "ALTER TABLE `board_p` CHANGE `b_con` `b_con` MEDIUMTEXT NOT NULL ";
    $CONN->Execute($query);
    $fp = fopen($up_file_name, "w");
    $temp_query = " 更改  b_con 的欄位	-- by hami (2007-3-23)";
    fwrite($fp, $temp_query);
    fclose($fp);
}


$up_file_name = $upgrade_str . "2007-10-15.txt";
//調整 公告標題
if (!is_file($up_file_name)) {
    //
    $query = "ALTER TABLE `board_p` CHANGE `b_sub`  `b_sub` CHAR( 100 ) NOT NULL DEFAULT ''";
    $CONN->Execute($query);
    $fp = fopen($up_file_name, "w");
    $temp_query = " 更改  b_sub 的欄位	-- by hami (2007-10-15)";
    fwrite($fp, $temp_query);
    fclose($fp);
}

//加入是否置於跑馬燈欄位
$up_file_name = $upgrade_str . "2013-04-03.txt";
if (!is_file($up_file_name)) {
    //增加 跑馬燈欄位
    $query = "ALTER TABLE `board_p` ADD `b_is_marquee` CHAR( 1 ) DEFAULT NULL";
    $CONN->Execute($query);
    $fp = fopen($up_file_name, "w");
    $temp_query = "佈告欄加入是否顯示在跑馬燈欄位	-- by shengche (2013-04-03)";
    fwrite($fp, $temp_query);
    fclose($fp);
}