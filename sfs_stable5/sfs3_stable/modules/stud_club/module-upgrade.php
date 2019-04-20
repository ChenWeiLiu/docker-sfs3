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
//修改資料表，是否一個學生同時可參加多個社團
$up_file_name =$upgrade_str."2013-09-10.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_club_setup` ADD `multi_join` tinyint(1) not NULL default '0'" ; //是否一個學生同時可參加多個社團
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位, 是否允許一個學生同時選修多個社團-- by smallduh (2013-09-10)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//修改資料表，增加忽略性別編班選項
$up_file_name =$upgrade_str."2013-02-08.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_club_base` ADD `ignore_sex` tinyint(1) not NULL default '0'" ; //讓導師看到學生分數
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位, 編班時可忽略性別編班-- by smallduh (2013-02-08)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}



//修改資料表，將預計開班人數, 欄位改為可單獨設定男生女生人數, 並增加通過標準分數與編班記錄
$up_file_name =$upgrade_str."2013-01-04.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_club_base` ADD `stud_boy_num` int(3) not NULL" ; //開班男生數
	$query[1] = "ALTER TABLE `stud_club_base` ADD `stud_girl_num` int(3) not NULL" ; //開班女生數
	$query[2] = "ALTER TABLE `stud_club_base` ADD `pass_score` int(3) not NULL default '60'" ; //通過分數
  $query[3] = "ALTER TABLE `stud_club_setup` ADD `arrange_record` text NULL" ; //編班記錄
  $query[4] = "ALTER TABLE `stud_club_setup` ADD `teacher_double` tinyint(1) not NULL default '0'" ; //是否允許一個老師指導多個社團

	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	
	$query="select club_sn,club_student_num from stud_club_base";
	$res=mysql_query($query);
	if (mysql_num_rows($res)) {
	  while ($row=mysql_fetch_array($res)) {
	    
	    $stud_boy_num=round($row['club_student_num']/2);
	    $stud_girl_num=$row['club_student_num']-$stud_boy_num;
	    
	    $query="update stud_club_base set stud_boy_num='$stud_boy_num',stud_girl_num='$stud_girl_num' where club_sn='".$row['club_sn']."'";
	    mysql_query($query);
	    
	  } // end while
	 
	} // end if
	
	$temp_query = "調整欄位, 開班人數的設定改為分別設定男生和女生人數 \n新增欄位, 設定學生分數必須達此標準才能獲得社團認證\n新增記欄位,可記錄編班記錄\n-- by smallduh (2013-01-04)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


//修改資料表
$up_file_name =$upgrade_str."2012-11-28.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_club_setup` ADD `show_score` tinyint(1) not NULL" ; //讓導師看到學生分數
	$query[1] = "ALTER TABLE `stud_club_setup` ADD `show_feedback` tinyint(1) not NULL" ; //讓導師看到學生自我省思
	$query[2] = "ALTER TABLE `stud_club_setup` ADD `show_teacher_feedback` tinyint(1) not NULL" ; //讓社團老師看到學生自我省思
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位, 可調整導師或社團指導老師能否看到學生成績或自我省思-- by smallduh (2012-11-28)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

 
//修改資料表
$up_file_name =$upgrade_str."2012-11-23.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `association` ADD `stud_post` varchar(20) NULL" ; //學生擔任職務
	$query[1] = "ALTER TABLE `association` ADD `stud_feedback` text NULL" ; //學生自我省思
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位, 因應學生自我省思與擔任職務-- by smallduh (2012-11-23)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


//修改資料表 , 上課地點設定
$up_file_name =$upgrade_str."2012-10-12.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_club_base` ADD `club_location` VARCHAR( 20 ) NOT NULL AFTER `club_memo`" ;
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位 club_location  -- by smallduh (2012-10-12)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

?>