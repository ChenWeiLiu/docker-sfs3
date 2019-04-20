<?php
//$Id: module-upgrade.php 6737 2012-04-06 12:25:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
// reward_reason和reward_base 欄位屬性為text

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//以上保留--------------------------------------------------------

//新增資料表，

$up_file_name =$upgrade_str."2012-10-30.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "CREATE TABLE IF NOT EXISTS `score_eduh_teacher2` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year_seme` varchar(4) NOT NULL,
  `teacher_sn` int(10) unsigned NOT NULL,
  `class_id` varchar(11) NOT NULL,
  `update_sn` int(10) unsigned NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;
	";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增資料表 score_eduh_teacher2  -- by smallduh (2012-10-30)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}



$up_file_name =$upgrade_str."2014-08-25.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `stud_seme_talk` ADD `interview_method` varchar(10) AFTER `interview`;";
	if ($CONN->Execute($query))
		$str="新增訪談方式欄位成功\";
	else
		$str="新增訪談方式欄位失敗( 有可能已經自級務管理中升級完成了! )";
	$temp_query = "於 stud_seme_talk 資料表新增訪談方式 -- by infodaes 2014-08-25 \n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

?>