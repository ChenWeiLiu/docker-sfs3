<?php
if(!$CONN){
        echo "go away !!";
        exit;
}

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//以上保留--------------------------------------------------------

//修改資料表，增加承辦單位
$up_file_name =$upgrade_str."2013-03-13.txt";

if (!is_file($up_file_name)){
	
	$query = "ALTER TABLE stud_service add sponsor varchar(64) " ; //主辦單位
	$temp_str = '';
		if ($CONN->Execute($query))
			$temp_str .= "$query\n 更新成功 ! \n";
		else
			$temp_str .= "$query\n 更新失敗 ! \n";
	

	$temp_query = "修改資料表 stud_service (記錄主辦單位)-- by smallduh (2013-03-13)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
	
	//將原有資料的的主辦單位皆寫承辦單位名稱
	$sql="select sn,department from stud_service";
	$res=mysql_query($sql);
	while ($row=mysql_fetch_array($res,1)) {
	  $department=$row['department'];
	  $sql_select = "select room_name from school_room where room_id='$department'";
    $result=$CONN->Execute($sql_select);
    $room_name=$result->fields['room_name'];	
	  $sql_update="update stud_service set sponsor='$room_name' where sn='".$row['sn']."'";
	  mysql_query($sql_update);
	}	
	
}

//修改資料表，增加填寫自我省思功能
$up_file_name =$upgrade_str."2012-11-30.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_service_detail` add `feedback` text " ; //自我省思
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修改資料表 stud_service_detail (學生填寫自我省思功能)-- by smallduh (2012-11-30)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


//修改資料表，增加認證功能
$up_file_name =$upgrade_str."2012-11-14.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_service` add `confirm` tinyint(1) UNSIGNED NOT NULL default '0' " ; //是否已核可
	$query[1] = "ALTER TABLE `stud_service` add `confirm_sn` int(10) NULL" ; //是誰核可
	$query[2] = "ALTER TABLE `stud_service` add `input_sn` int(10) NOT NULL"; //申請人是誰
	$query[3] = "ALTER TABLE `stud_service` add `input_time` datetime NOT NULL"; //申請登錄日期時間
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修改資料表 stud_service -- by smallduh (2012-11-14)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//修改資料表，將 minutes 欄位由 tinyint 改為 int
$up_file_name =$upgrade_str."2012-09-27.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_service_detail` CHANGE `minutes` `minutes` INT(3) UNSIGNED NOT NULL " ;
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修改資料表 stud_service -- by smallduh (2012-09-27)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}
?>