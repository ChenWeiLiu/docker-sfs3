<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();


//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/index.htm";

//建立物件
$obj= new basic_chc($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("12basic_chc模組");之前
$obj->process();


//秀出網頁布景標頭
head("補考成績管理");

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
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數

	//建構函式
	function basic_chc($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//程序
	function process() {
		//if ($_GET['act']=='update') $this->updateDate();
		$this->all();
	}



	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){

	}








}


