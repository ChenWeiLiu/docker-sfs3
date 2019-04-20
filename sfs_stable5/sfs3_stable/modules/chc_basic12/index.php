<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

if ($_SESSION['session_who']=='學生') Header("Location:stu.php");

//指定樣本
$template_file = dirname (__file__)."/templates/chc_basic12.htm";

//建立物件
$obj= new basic_chc($CONN,$smarty);
$obj->sfsURL=$SFS_PATH_HTML;

//初始化
$obj->init();

//處理程序,有時程序內有header指令,故本程序宜於head("chc_basic12模組");之前
$obj->process();

//秀出網頁布景標頭
head("彰化區免試入學");


//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);

//佈景結尾
foot();


//物件class
class basic_chc{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $sfsURL;
	//建構函式
	function basic_chc($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {}
	//程序
	function process() {
		$this->update_TB();
		$this->all();
	}
	//顯示
	function display($tpl){
		
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		$SQL="select  academic_year ,count(*) as tol from chc_basic12 group by academic_year order by academic_year desc ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
	//產生連結頁面
	}
	//擷取資料
	function update_TB(){
		$SQL="select  score_club from chc_basic12 limit 1 ";
		$rs=$this->CONN->Execute($SQL);
		if (!$rs){
		$ADD_SQL="ALTER TABLE `chc_basic12` ADD `score_club` TINYINT(3) NULL DEFAULT NULL AFTER `score_balance` ";
		$rs=$this->CONN->Execute($ADD_SQL);
		}
	}





}


