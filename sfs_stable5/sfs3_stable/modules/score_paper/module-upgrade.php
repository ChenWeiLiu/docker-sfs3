<?php
// $Id: module-upgrade.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
//啟動 session
session_start();
//
// 檢查更新否
// 更新記錄檔路徑

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

// 加入教師上傳成績單資料表
$up_file_name =$upgrade_str."score_paper_upload_06_27.txt";
if (!is_file($up_file_name) ){
        $query = "CREATE TABLE IF NOT EXISTS score_paper_upload (
  spu_sn int(5) NOT NULL auto_increment,
  curr_seme varchar(5) NOT NULL default '',
  class_num char(3) NOT NULL default '',
  file_name varchar(255) NOT NULL default '',
  log_id varchar(20) NOT NULL default '',
  time datetime default NULL,
  printed tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (spu_sn),
  UNIQUE KEY class_num (class_num,curr_seme)
) ";

        $res = $CONN->Execute($query);
        $fp = fopen ($up_file_name, "w");
        $temp_query = "加入教師上傳成績單資料表 score_paper_upload	-- by hami (2006-6-27)";
        fwrite($fp,$temp_query);
        fclose($fd);
}

?>
