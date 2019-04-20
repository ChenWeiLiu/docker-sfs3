<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//系統設定檔
	include_once "./module-cfg.php";
	include_once "../../include/config.php";

	//模組更新程式
	require_once "./module-upgrade.php";
	  

//載入本模組的專用函式庫

//此處也可進行一些一模組的基本設定
//宣告不支援模組
$remove_array=array(
		"backup"=>array(
				'name'=>'系統備份',
				'database'=>array(),
		),
		"csrc"=>array(
				'name'=>'校安匯整',
				'database'=>array('csrc_record','csrc_item'),
		),
		"curriculum_xml"=>array(
				'name'=>'課表XML交換',
				'database'=>array(),
		),
		"database_info"=>array(
				'name'=>'資料庫欄位管理',
				'database'=>array(),
		),
		"docword"=>array(
				'name'=>'公文管理系統',
				'database'=>array('sch_doc1','sch_doc1_unit'),
		),
		"graduate"=>array(
				'name'=>'畢業生作業',
				'database'=>array(),
		),
		"mig"=>array(
				'name'=>'數位相本',
				'database'=>array(),
		),
		"module_maker"=>array(
				'name'=>'SFS3 模組產生器',
				'database'=>array('module_maker','module_maker_col'),
		),
		"our_blog"=>array(
				'name'=>'我們的部落格',
				'database'=>array(),
		),
		"salary"=>array(
				'name'=>'薪津查詢',
				'database'=>array('salary'),
		),
		"score_input_all"=>array(
				'name'=>'成績輸入',
				'database'=>array(),
		),
		"score_input_interface"=>array(
				'name'=>'成績單介面管理',
				'database'=>array(),
		),
		"score_manage"=>array(
				'name'=>'成績管理',
				'database'=>array(),
		),
		"score_query"=>array(
				'name'=>'成績綜合查詢',
				'database'=>array(),
		),
		"sfs3_blog"=>array(
				'name'=>'校園部落格',
				'database'=>array('blog_home','blog_content','blog_kind','blog_feelback','blog_quota'),
		),
		"stud_basic_test"=>array(
				'name'=>'學生基測名冊',
				'database'=>array('score_semester_move','dis_score_ss','dis_stage','dis_stage_fin','dis_score_fin','stud_seme_dis',),
		),
		"stud_goodbad"=>array(
				'name'=>'學生獎懲作業',
				'database'=>array(),
		),
		"teach_report"=>array(
				'name'=>'教師通訊錄(簡)',
				'database'=>array(),
		),
		"tnc_teach_class"=>array(
				'name'=>'南縣教師管理',
				'database'=>array(),
		),
		"trans_data"=>array(
				'name'=>'匯入廳版行政系統資料',
				'database'=>array(),
		),
		"web_hd"=>array(
				'name'=>'網路硬碟',
				'database'=>array('hd_dir','hd_file','hd_quota'),
		),
		"yuanzhuming"=>array(
				'name'=>'原住民獎助學金',
				'database'=>array('yuanzhumin'),
		),
		"system_utf8"=>array(
				'name'=>'資料庫UTF8化',
				'database'=>array(),
		),

);

?>

