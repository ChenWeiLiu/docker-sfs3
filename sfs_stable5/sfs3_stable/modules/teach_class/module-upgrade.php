<?php

// $Id: module-upgrade.php 9173 2017-11-28 17:12:29Z smallduh $

if(!$CONN){
        echo "go away !!";
        exit;
}

// �ˬd��s�_
// ��s�O���ɸ��|
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2003-06-09.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `teacher_post` DROP PRIMARY KEY ;";

	if ($CONN->Execute($query)) {
		$query2 =" ALTER TABLE `teacher_post` ADD UNIQUE (`teacher_sn`);";
		$CONN->Execute($query2);
		$temp_query = "��steacher_post  primary key �� -- by hami (2003-06-09)\n$query2";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}
$up_file_name =$upgrade_str."2005-04-04.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `teacher_connect` DROP PRIMARY KEY;";

	if ($CONN->Execute($query)) {
		$query2 =" ALTER TABLE `teacher_connect` ADD PRIMARY KEY ( `teacher_sn` ) ";
		$CONN->Execute($query2);
		$temp_query = "��steacher_pconnect  primary key �� -- by hami (2005-04-04)\n$query2";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}
$up_file_name =$upgrade_str."2007-03-26.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `teacher_connect` CHANGE `email` `email` VARCHAR(120);ALTER TABLE `teacher_connect` CHANGE `selfweb` `selfweb` VARCHAR(200);ALTER TABLE `teacher_connect` CHANGE `classweb` `classweb` VARCHAR(200); FLUSH TABLE `teacher_connect`; ";

	if ($CONN->Execute($query)) {
		$temp_query = "��semail  selfweb classweb���j�p -- by chi(2007-03-26)\n$query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}

$up_file_name =$upgrade_str."2011-11-22.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `teacher_base` ADD `master_subjects` VARCHAR( 200 ) NULL AFTER `teach_is_cripple`;";
	if ($CONN->Execute($query)) {
		$temp_query = "�W�[�Ǭ�����бM�������� master_subjects -- by Infodaes(2011-11-22)\n$query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}

$up_file_name =$upgrade_str."2011-11-22-2.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `teacher_base` ADD `certdate` DATE NULL AFTER `teach_check_word`, ADD `certgroup` VARCHAR( 40 ) NULL AFTER `certdate`,ADD `certarea` VARCHAR( 40 ) NULL AFTER `certgroup`;";
	if ($CONN->Execute($query)) {
		$temp_query = "�W�[�Юv�ҵn�O����B�Юv���O�B�n�O�Ǭ������ master_subjects -- by Infodaes(2011-11-22)\n$query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}

?>