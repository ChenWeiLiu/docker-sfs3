<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//�t�γ]�w��
	include_once "./module-cfg.php";
	include_once "../../include/config.php";

	//�Ҳէ�s�{��
	require_once "./module-upgrade.php";
	  

//���J���Ҳժ��M�Ψ禡�w

//���B�]�i�i��@�Ǥ@�Ҳժ��򥻳]�w
//�ŧi���䴩�Ҳ�
$remove_array=array(
		"backup"=>array(
				'name'=>'�t�γƥ�',
				'database'=>array(),
		),
		"csrc"=>array(
				'name'=>'�զw�׾�',
				'database'=>array('csrc_record','csrc_item'),
		),
		"curriculum_xml"=>array(
				'name'=>'�Ҫ�XML�洫',
				'database'=>array(),
		),
		"database_info"=>array(
				'name'=>'��Ʈw���޲z',
				'database'=>array(),
		),
		"docword"=>array(
				'name'=>'����޲z�t��',
				'database'=>array('sch_doc1','sch_doc1_unit'),
		),
		"graduate"=>array(
				'name'=>'���~�ͧ@�~',
				'database'=>array(),
		),
		"mig"=>array(
				'name'=>'�Ʀ�ۥ�',
				'database'=>array(),
		),
		"module_maker"=>array(
				'name'=>'SFS3 �Ҳղ��;�',
				'database'=>array('module_maker','module_maker_col'),
		),
		"our_blog"=>array(
				'name'=>'�ڭ̪�������',
				'database'=>array(),
		),
		"salary"=>array(
				'name'=>'�~�z�d��',
				'database'=>array('salary'),
		),
		"score_input_all"=>array(
				'name'=>'���Z��J',
				'database'=>array(),
		),
		"score_input_interface"=>array(
				'name'=>'���Z�椶���޲z',
				'database'=>array(),
		),
		"score_manage"=>array(
				'name'=>'���Z�޲z',
				'database'=>array(),
		),
		"score_query"=>array(
				'name'=>'���Z��X�d��',
				'database'=>array(),
		),
		"sfs3_blog"=>array(
				'name'=>'�ն鳡����',
				'database'=>array('blog_home','blog_content','blog_kind','blog_feelback','blog_quota'),
		),
		"stud_basic_test"=>array(
				'name'=>'�ǥͰ���W�U',
				'database'=>array('score_semester_move','dis_score_ss','dis_stage','dis_stage_fin','dis_score_fin','stud_seme_dis',),
		),
		"stud_goodbad"=>array(
				'name'=>'�ǥͼ��g�@�~',
				'database'=>array(),
		),
		"teach_report"=>array(
				'name'=>'�Юv�q�T��(²)',
				'database'=>array(),
		),
		"tnc_teach_class"=>array(
				'name'=>'�n���Юv�޲z',
				'database'=>array(),
		),
		"trans_data"=>array(
				'name'=>'�פJ�U����F�t�θ��',
				'database'=>array(),
		),
		"web_hd"=>array(
				'name'=>'�����w��',
				'database'=>array('hd_dir','hd_file','hd_quota'),
		),
		"yuanzhuming"=>array(
				'name'=>'�������U�Ǫ�',
				'database'=>array('yuanzhumin'),
		),
		"system_utf8"=>array(
				'name'=>'��ƮwUTF8��',
				'database'=>array(),
		),

);

?>

