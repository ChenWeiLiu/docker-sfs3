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
$up_file_name =$upgrade_str."2013-12-24.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `ldap` ADD `enable1` tinyint(1) not NULL default '0'" ; //學生登入是否啟用
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位 enable1 , 學生登入是否啟用 ldap -- by smallduh (2013-12-24)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2013-10-09.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `ldap` ADD `teacher_ou` varchar(20) not NULL" ; //教師的 ou
	$query[1] = "ALTER TABLE `ldap` ADD `stud_ou` varchar(20) not NULL" ; //學生的 ou
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位, 教師的 ou 及學生的 ou -- by smallduh (2013-10-09)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//增加 base uid 設定
$up_file_name =$upgrade_str."2013-09-30.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `ldap` ADD `base_uid` VARCHAR( 10 ) NOT NULL AFTER `base_dn` ;";
	if ($CONN->Execute($query)) {
		$str="成功\ ";
		$CONN->Execute("UPDATE `ldap` SET `base_uid`='uid'");
	}
	else
		$str="失敗";
	$temp_query = "ldap 欄位加入 base_uid 欄位 ".$str." -- by hami (2013-09-30)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

