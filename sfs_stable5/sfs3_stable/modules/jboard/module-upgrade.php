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



$up_file_name = $upgrade_str . "2013-12-16.txt";
if (!is_file($up_file_name)) {
    //增加
    $query = "ALTER TABLE `jboard_kind` ADD board_is_sort tinyint NOT NULL";
    $CONN->Execute($query);
    $fp = fopen($up_file_name, "w");
    $temp_query = "文章分類設定，加入「是否允許自訂排序」欄位	-- by smallduh (2013-12-16)";
    fwrite($fp, $temp_query);
    $query = "ALTER TABLE `jboard_kind` CHANGE `bk_order` `bk_order` INT(5) NOT NULL ";
    $CONN->Execute($query);
    $temp_query = "修改 bk_order 分類排序欄位	-- by smallduh (2013-12-16)";
    fwrite($fp, $temp_query);    
    fclose($fp);
}


$up_file_name = $upgrade_str . "2013-12-17.txt";
if (!is_file($up_file_name)) {
	//增加
	$query = "ALTER TABLE `jboard_kind` ADD position tinyint(1) NOT NULL";
	$CONN->Execute($query);
	$fp = fopen($up_file_name, "w");
	$temp_query = "分類列表加入「層級」欄位	-- by smallduh (2013-12-17)";
	fwrite($fp, $temp_query);
	fclose($fp);
}

$up_file_name = $upgrade_str . "2014-04-16.txt";
if (!is_file($up_file_name)) {
	//增加
	$query = "ALTER TABLE `jboard_kind` ADD board_is_coop_edit tinyint(1) NOT NULL";
	$CONN->Execute($query);
	$fp = fopen($up_file_name, "w");
	$temp_query = "增加欄位選項「是否允許共編文件」	-- by smallduh (2014-04-16)";
	fwrite($fp, $temp_query);
	fclose($fp);
}

//修正發表者的處室 2014-04-22
$up_file_name = $upgrade_str . "2014-04-22.txt";
if (!is_file($up_file_name)) {
	//取出所有文章
	$sql="select * from jboard_p";
	$res=$CONN->Execute($sql);
	while ($row=$res->fetchRow()) {
	  $teacher_sn=$row['teacher_sn'];
	  $b_id=$row['b_id'];
		//取得發表人的處室
		$query = "select  a.post_office , b.title_name ,b.room_id,c.name from teacher_post a ,teacher_title b ,teacher_base c  where a.teacher_sn = c.teacher_sn and  a.teach_title_id =b.teach_title_id  and a.teacher_sn='$teacher_sn' ";
		$result = $CONN->Execute($query) or die ($query);
		$row_room = $result->fetchRow();
		$b_unit=$row_room['room_id'];		//發文者所在處室
		//update寫入
	 	$sql_update = "update jboard_p set b_unit='$b_unit' where b_id='$b_id' ";
		$CONN->Execute($sql_update) or die ($sql_update);
	}
	
	$fp = fopen($up_file_name, "w");
	$temp_query = "修正發表者的處室資料 -- by smallduh (2014-04-22)";
	fwrite($fp, $temp_query);
	fclose($fp);
}

//修正職稱, 以代碼取代 2014-04-23
$up_file_name = $upgrade_str . "2014-04-23.txt";
if (!is_file($up_file_name)) {
	//取出所有文章
	$sql="select * from jboard_p";
	$res=$CONN->Execute($sql);
	while ($row=$res->fetchRow()) {
	  $teacher_sn=$row['teacher_sn'];
	  $b_id=$row['b_id'];
	  $b_title=$row['b_title'];
		//取得本職稱的id
		$query = "select teach_title_id from teacher_title where title_name='".$b_title."'";
		$result = $CONN->Execute($query) or die ($query);
		if ($result->RecordCount()>0) {
			$row_title = $result->fetchRow();
			$b_title=$row_title['teach_title_id'];		//發文者職稱
	  } else {
	    $b_title="";
	  }
		//update寫入
	 	$sql_update = "update jboard_p set b_title='$b_title' where b_id='$b_id' ";
		$CONN->Execute($sql_update) or die ($sql_update);
	}
	
	$fp = fopen($up_file_name, "w");
	$temp_query = "將發表者的職稱以代碼取代 -- by smallduh (2014-04-23)";
	fwrite($fp, $temp_query);
	fclose($fp);
}


//修改附檔存放方式, 不存入資料庫, 而是取出以檔案方式處理 2014-08-08
$up_file_name = $upgrade_str . "2014-08-08.txt";
if (!is_file($up_file_name)) {
	//取出所有文章
	$path_str = "school/jboard/files/";
  set_upload_path($path_str);
  $download_file_path = $UPLOAD_PATH.$path_str;

	$sql="select * from jboard_files";
	$res=$CONN->Execute($sql);
	while ($row=$res->fetchRow()) {
   $filename=$row['new_filename'];
	 $fp = fopen($download_file_path.$filename, "w");
   //內容 decode 回來 
   $content=stripslashes(base64_decode($row['content']));
   fwrite($fp,$content);
   fclose($fp);
	}
	$sql="update jboard_files set content=''";
	$res=$CONN->Execute($sql);
	$fp = fopen($up_file_name, "w");
	$temp_query = "將附檔改為以檔案方式處理 -- by smallduh (2014-08-08)\n檔案存放位置".$download_file_path;
	fwrite($fp, $temp_query);
	fclose($fp);
}

//加長分類區名稱及代碼欄位長度
$up_file_name = $upgrade_str . "2014-10-13.txt";
if (!is_file($up_file_name)) {
	//$sql="ALTER TABLE `jboard_kind` CHANGE `bk_id` `bk_id` VARCHAR(36) NOT NULL DEFAULT '0'";
	$SQL[0]="ALTER TABLE `jboard_kind` CHANGE `bk_id` `bk_id` VARCHAR( 36 ) NOT NULL DEFAULT '0';";
	$SQL[1]="ALTER TABLE `jboard_kind` CHANGE `board_name` `board_name` VARCHAR( 72 ) NOT NULL DEFAULT '';";
  $SQL[2]="ALTER TABLE `jboard_p` CHANGE `bk_id` `bk_id` VARCHAR( 36 ) NOT NULL DEFAULT '0';";
  $SQL[3]="ALTER TABLE `jboard_check` CHANGE `pro_kind_id` `pro_kind_id` VARCHAR( 36 ) NOT NULL DEFAULT '0';";
  $temp_query="";
 
  foreach ($SQL as $sql) {
		$res=$CONN->Execute($sql) or die($sql);
		$temp_query=$temp_query.$sql."\n";
  }
	$fp = fopen($up_file_name, "w");
	$temp_query=$temp_query."加長分類區代碼bk_id及名稱bk_name及欄位長度 -- by smallduh (2014-10-13)\n";
	fwrite($fp, $temp_query);
	fclose($fp);
}


//增加分類區同步顯示設定
$up_file_name = $upgrade_str . "2015-11-20.txt";
if (!is_file($up_file_name)) {
	$SQL[0]="ALTER TABLE `jboard_kind` add `synchronize` VARCHAR(36) NOT NULL DEFAULT '';";
	$SQL[1]="ALTER TABLE `jboard_kind` add `synchronize_days` int(2) NOT NULL DEFAULT '30';";

  $temp_query="";
 
  foreach ($SQL as $sql) {
		$res=$CONN->Execute($sql) or die($sql);
		$temp_query=$temp_query.$sql."\n";
  }
	$fp = fopen($up_file_name, "w");
	$temp_query=$temp_query."增加分類區同步顯示於另一板區的設定 -- by smallduh (2015-11-20)\n";
	fwrite($fp, $temp_query);
	fclose($fp);
}

//增加置頂設定
$up_file_name = $upgrade_str . "2015-11-24.txt";
if (!is_file($up_file_name)) {
	$SQL[0]="ALTER TABLE `jboard_p` add `top_days` tinyint(2) NOT NULL DEFAULT '0';";
  $temp_query="";
  foreach ($SQL as $sql) {
		$res=$CONN->Execute($sql);
		$temp_query=$temp_query.$sql."\n";
  }
	$fp = fopen($up_file_name, "w");
	$temp_query=$temp_query."增加文章置頂設定 -- by smallduh (2015-11-24)\n";
	fwrite($fp, $temp_query);
	fclose($fp);
}
