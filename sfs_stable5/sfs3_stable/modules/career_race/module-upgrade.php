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
//更新記錄會開啟一個文字檔, 請以日期作為檔名, 以利辨別, 如: 2013-06-24.txt

$up_file_name =$upgrade_str."2013-07-20.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `career_race` ADD `word` VARCHAR( 100 ) NOT NULL AFTER `memo` , ADD `weight` FLOAT NOT NULL AFTER `word` ";

	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "競賽記錄增加核准字號及權重欄位 -- by chiming (2013-07-20)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


$up_file_name =$upgrade_str."2013-08-25.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `career_race` CHANGE `weight` `weight` FLOAT NOT NULL DEFAULT '1'";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "競賽記錄權重欄位weight預設值改為1 -- by infodaes (2013-08-25)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


$up_file_name =$upgrade_str."2014-4-27.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `career_race` CHANGE `name` `name` VARCHAR( 120 ) NOT NULL ;";
	$temp_str = '';
	if ($CONN->Execute($query))
		$temp_str .= "$query \n 更新成功 ! \n";
	else
		$temp_str .= "$query \n 更新失敗 ! \n";

	$temp_query = "擴展競賽名稱欄位長度為120 -- by infodaes (2014-01-07)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2014-04-28.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `career_race` ADD `year` TINYINT(4) NULL , ADD `nature` VARCHAR(10) NULL";

	$temp_str = '';
	if ($CONN->Execute($query))
			$temp_str .= "$query\n 更新成功 ! \n";
		else
			$temp_str .= "$query\n 更新失敗 ! \n";

	$temp_query = "競賽記錄增加核准字號及權重欄位 -- by infodaes (2014-04-28)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


$up_file_name =$upgrade_str."2014-05-08.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `career_race` ADD `weight_tech` FLOAT NOT NULL DEFAULT '1'";

	$temp_str = '';
	if ($CONN->Execute($query))
			$temp_str .= "$query\n 更新成功 ! \n";
		else
			$temp_str .= "$query\n 更新失敗 ! \n";
	
	//將權重設為與高中職相同、並自動寫入"學年度"
	$query="SELECT sn,weight,year as c_year,year(certificate_date) AS year,month(certificate_date) AS month FROM career_race";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$sn=$res->fields['sn'];
		$weight=$res->fields['weight'];
		
		if(!$res->fields['c_year']) {
			$year=$res->fields['year']-1911;
			$month=$res->fields['month'];
			//計算學年度
			if($month<8) $year--;
			
			$year="year='$year',";
		} else $year='';
		
		$query="UPDATE career_race SET $year weight_tech='$weight' WHERE sn=$sn";
		$CONN->Execute($query);
		$res->MoveNext();
	}
	
	$temp_query = "競賽記錄增加五專免試權重欄位 並依據競賽日期自動填入所屬學年度 -- by infodaes (2014-05-08)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
	
	
	
	
}



?>

