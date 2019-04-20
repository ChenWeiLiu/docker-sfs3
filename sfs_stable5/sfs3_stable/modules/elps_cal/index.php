<?php
//$Id: index.php 5310 2009-01-10 07:57:56Z hami $
include"config.php";
include_once "cal_elps_class.php";

//sfs_check();
if ($_GET[syear]=='') {
	$now_Syear=sprintf("%03d",curr_year()).curr_seme();
	header("Location:$_SERVER[PHP_SELF]?syear=$now_Syear");
	}
class cal_index extends cal_elps{
	//初始化
	function init() {
		$this->seme=$_GET[syear];	
	}
	//程序
	function process() {
		$this->init();
		$this->get_all_set();//取全部學期行事曆設定
		$this->get_use_set();//取使用中行事曆設定
		$this->get_all_event();//加入所有行事資料
		//$this->all();
	}
	//顯示
	function display(){
		$tpl=dirname(__file__)."/templates/ind.html";
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}

}
//建立物件
$obj= new cal_index();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->process();

//秀出網頁布景標頭
head("校務行事曆");

//顯示SFS連結選單(欲使用請拿開註解)
//echo make_menu($school_menu_p);
$link2="syear=$_GET[syear]";
if ($_SESSION[session_tea_sn]!='') print_menu($school_menu_p,$link2);
//myheader();
//顯示內容
$obj->display();
//佈景結尾
foot();


?>