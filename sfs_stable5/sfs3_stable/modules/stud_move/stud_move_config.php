<?php

// $Id: stud_move_config.php 9114 2017-08-04 15:18:55Z smallduh $

	//�t�γ]�w��
	include "../../include/config.php";
	//�禡�w
	include "../../include/sfs_case_PLlib.php";    
	//�ˬd��s���O
	include_once "module-upgrade.php";
	//�s�W���s�W��
	$newBtn = " �s�W��� ";
	//�ק���s�W��
	$editBtn = " �T�w�ק� ";
	//�R�����s�W��
	$delBtn = " �T�w�R�� ";
	//�T�w�s�W���s�W��
	$postMoveBtn = " �T�w���� ";
	$postInBtn = " �T�w��J ";
	$postOutBtn = " �T�w��X ";
	$postReinBtn = " �T�w�_�� ";
	$postHome = " �T�w�b�a�۾� ";
	$editModeBtn = " �ק�Ҧ� ";
	$browseModeBtn = " �s���Ҧ� ";	
	//�w�]���y
	$default_country = "���إ���";
	//�����]�w��ܵ���
	$gridRow_num = 16;
	//����橳��]�w
	$gridBgcolor="#DDDDDC";
	//�����k������C��
	$gridBoy_color = "blue";
	//�����k������C��
	$gridGirl_color = "#FF6633";
	//�w�]�Ĥ@�Ӷ}�үZ�� 
	$default_begin_class = "601";	
	//�եX���O
	$out_arr=array("7"=>"�X��","8"=>"�ծ�","11"=>"���`","12"=>"����");
	//�_�ǿ�����O
	$out_in_arr=array("7"=>"�X��","8"=>"�ծ�","12"=>"����");
	//�ɭ���
	$demote_arr=array("9"=>"�ɯ�","10"=>"����");
	//�_�����O
	$rein_arr=array("3"=>"�����_��","14"=>"��Ǵ_��");
	//�ؿ����{��
	$student_menu_p = array("stud_move.php"=>"��J","stud_move_out.php"=>"�եX","stud_move_rein.php"=>"�_��","stud_move_home.php"=>"�b�a�۾�","stud_demote.php"=>"�ɭ���","stud_move_gradu.php"=>"���~��X","stud_move_new.php"=>"�s�ͤJ��","stud_move_list2.php"=>"���ʰO���C��","stud_move_print.php"=>"���ʳ���","stud_move_cal.php"=>"���ʲέp��","../stud_reg/"=>"���y�޲z","stud_move_chiedit.php"=>"�s�ק@�~");

//big5�� utf8
function big5_to_utf8($str){
	$str = mb_convert_encoding($str, "UTF-8", "BIG5");

	$i=1;

	while ($i != 0){
		$pattern = '/&#\d+\;/';
		preg_match($pattern, $str, $matches);
		$i = sizeof($matches);
		if ($i !=0){
			$unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
			$str = preg_replace("/$matches[0]/",$unicode_char,$str);
		} //end if
	} //end wile

	return $str;

}

//base64�ѽX
function array_base64_decode($data) {
	foreach($data as $key=>$value){
		if (is_array($value)){
			$data[$key] = array_base64_decode($value);
		}else{
			$data[$key]= base64_decode($value);
		}
	} // end foreach

	return $data;
}

//�ˬd table ���, ���ǾǮզ]��Ʈw�ɯť���, ���ʥ�
function check_table_column() {
   global $CONN;

	//2017-07-14 �U������
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move` LIKE 'download_deadline'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move` ADD `download_deadline` DATE NOT NULL DEFAULT '0000-00-00'");
	}
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move_import` LIKE 'download_deadline'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move_import` ADD `download_deadline` DATE NOT NULL DEFAULT '0000-00-00'");
	}

	//2017-07-18 �U������
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move` LIKE 'download_limit'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move` ADD `download_limit` INT NOT NULL COMMENT '�U�����ƭ���'");
	}
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move_import` LIKE 'download_limit'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move_import` ADD `download_limit` INT NOT NULL COMMENT '�U�����ƭ���'");
	}
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move` LIKE 'download_times'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move` ADD `download_times` INT NOT NULL COMMENT '�U������'");
	}
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move_import` LIKE 'download_times'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move_import` ADD `download_times` INT NOT NULL COMMENT '�U������';");
	}
}

?>
