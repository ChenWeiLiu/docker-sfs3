<?php
//$Id: module-upgrade.php 7398 2013-08-02 04:07:41Z infodaes $

if(!$CONN){
        echo "go away !!";
        exit;
}
// reward_reason和reward_base 欄位屬性為text

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2003-06-24.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `reward` CHANGE `reward_base` `reward_base` TEXT DEFAULT NULL";
	if ($CONN->Execute($query))
		$temp_str = "$query 更新成功 !\n";
	else
		$temp_str = "$query 更新失敗 !\n";
		
	$query = "ALTER TABLE `reward` CHANGE `reward_reason` `reward_reason` TEXT DEFAULT NULL";
	if ($CONN->Execute($query))
		$temp_str .= "$query 更新成功 !\n";
	else
		$temp_str .= "$query 更新失敗 !\n";

	$temp_query = "更改 reward_reason和reward_base 欄位屬性為text -- by hami (2003-06-08)\n$temp_query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_str);
		fclose ($fp);
}

$up_file_name =$upgrade_str."2003-11-26.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `reward` CHANGE `move_year_seme` `reward_year_seme` VARCHAR(6) DEFAULT NULL";
	if ($CONN->Execute($query))
		$temp_str = "$query 更新成功 !\n";
	else
		$temp_str = "$query 更新失敗 !\n";
		
	$query = "ALTER TABLE `reward` CHANGE `move_date` `reward_date` DATE DEFAULT '0000-00-00'";
	if ($CONN->Execute($query))
		$temp_str .= "$query 更新成功 !\n";
	else
		$temp_str .= "$query 更新失敗 !\n";

	$query = "ALTER TABLE `reward` CHANGE `move_c_date` `reward_c_date` DATE DEFAULT '0000-00-00'";
	if ($CONN->Execute($query))
		$temp_str .= "$query 更新成功 !\n";
	else
		$temp_str .= "$query 更新失敗 !\n";

	$query = "ALTER TABLE `reward` ADD `dep_id` BIGINT(20) DEFAULT '0'";
	if ($CONN->Execute($query))
		$temp_str .= "$query 更新成功 !\n";
	else
		$temp_str .= "$query 更新失敗 !\n";

	$query = "ALTER TABLE `reward` ADD `student_sn` INT(10) DEFAULT '0'";
	if ($CONN->Execute($query))
		$temp_str .= "$query 更新成功 !\n";
	else
		$temp_str .= "$query 更新失敗 !\n";
	$sql="update reward set dep_id=reward_id";
	$rs=$CONN->Execute($sql);
	$sql="select distinct stud_id from reward order by stud_id";
	$rs=$CONN->Execute($sql);
	if ($rs){
		while (!$rs->EOF) {
			$stud_id=$rs->fields["stud_id"];
			if (intval($stud_id)>0)  $all_id.="'".$stud_id."',";
			$rs->MoveNext();
		}
	}
	$all_id=substr($all_id,0,-1);
	if ($all_id) {
		$sql="select stud_id,student_sn from stud_base where stud_id in ($all_id)";
		$rs=$CONN->Execute($sql);
		while (!$rs->EOF) {
			$student_sn=$rs->fields["student_sn"];
			$stud_id=$rs->fields["stud_id"];
			$sql_s="update reward set student_sn='$student_sn' where stud_id='$stud_id'";
			$rs_s=$CONN->Execute($sql_s);
			$rs->MoveNext();
		}
	}
	$temp_query = "新增 dep_id 、 student_sn 欄位\n";
	$temp_query .= "更改 move_year_seme 為 reward_year_seme 、 move_date 為 reward_date 及 move_c_date 為 reward_c_date -- by brucelyc (2003-11-26)\n$temp_query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_str);
		fclose ($fp);
}

$up_file_name =$upgrade_str."2004-03-17.txt";
if (!is_file($up_file_name)){
	$CONN->Execute("update reward set dep_id=reward_id");
	$temp_query = "更新 dep_id (等於0為團體獎懲, 等於reward_id為個人獎懲)-- by hami (2004-03-17)\n$temp_query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_str);
		fclose ($fp);
}

$up_file_name =$upgrade_str."2008-03-18.txt";
if (!is_file($up_file_name)){
	if ($CONN->Execute("select * from reward where 1=0")) {
		$query = "ALTER TABLE `reward` CHANGE `dep_id` `dep_id` BIGINT(20) DEFAULT '0'";
		if ($CONN->Execute($query))
			$temp_str = "$query 更新成功 !\n";
		else
			$temp_str = "$query 更新失敗 !\n";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_str);
		fclose ($fp);
	}
}

$up_file_name =$upgrade_str."2009-04-24.txt";
if (!is_file($up_file_name)){
	$query="select distinct stud_id from stud_seme_rew where student_sn=0";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$stud_id=$res->fields['stud_id'];
		if (intval($stud_id)>0) $temp_arr[]=$stud_id;
		$res->MoveNext();
	}
	if (count($temp_arr)>0) {
		$temp_str="'".implode("','",$temp_arr)."'";
		$query="select student_sn,stud_id from stud_base where stud_id in ($temp_str)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$query="update stud_seme_rew set student_sn='".$res->fields['student_sn']."' where stud_id='".$res->fields['stud_id']."' and student_sn=0";
			$CONN->Execute($query);
			$res->MoveNext();
		}
		$str="已修正";
	} else {
		$str="未修正";
	}
	$temp_query = "修正 stud_seme_rew 表中 student_sn 為 0 的問題 -- by brucelyc (2009-04-24)\n$str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_str);
	fclose ($fp);
	$temp_arr=array();
}
$up_file_name =$upgrade_str."2012-04-06.txt";
if (!is_file($up_file_name)){
		$query = "update `reward` set reward_div='2' WHERE reward_div='1' AND reward_kind<0 ";
		if ($CONN->Execute($query))
		
		$query = "update `reward` set reward_div='1' WHERE reward_div='2' AND reward_kind>0 ";
		if ($CONN->Execute($query))
			$temp_str = "$query 修正獎懲錯誤資料 成功 !\n";
		else
			$temp_str = "$query 更新失敗 !\n";
		
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_str);
		fclose ($fp);
}

$up_file_name =$upgrade_str."2013-08-02.txt";
if (!is_file($up_file_name)){
		$temp_str = "$query 增加積分採計欄位更新失敗 !\n";
		$query = "ALTER TABLE `reward` ADD `reward_bonus` FLOAT NOT NULL DEFAULT '1'";
		if ($CONN->Execute($query))
		{
			$query = "ALTER TABLE `reward_exchange` ADD `reward_bonus` FLOAT NOT NULL DEFAULT '1'";
			if ($CONN->Execute($query))	$temp_str = "$query 增加積分採計欄位reward_bonus資料 成功 !\n";
		}
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_str);
		fclose ($fp);
}
?>