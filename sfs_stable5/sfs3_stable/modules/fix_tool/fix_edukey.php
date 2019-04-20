<?php

include "config.php";
//認證
sfs_check();

//指定樣本
$template_file = dirname (__file__)."/templates/fix_edukey.htm";

//建立物件
$obj= new fix_teacherID($CONN,$smarty);


//初始化
$obj->init();

//處理程序,有時程序內有header指令,故本程序宜於head("chc_basic12模組");之前
$obj->process();

//秀出網頁布景標頭
head("edukey產生");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);
//~ echo "<pre>";
//~ print_r($obj);
//顯示內容
$obj->display($template_file);

//佈景結尾
foot();


//物件class
class fix_teacherID{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $sfsURL;
	//建構函式
	function fix_teacherID($CONN,$smarty){
		$this->CONN=$CONN;
		$this->smarty=$smarty;
	}
	//初始化
	function init() {}
	//程序
	function process() {
		if ($_GET['act']=='Make') $this->add();
		if ($_GET['act']=='Del') $this->del();
		$this->check1();
	}
	//顯示
	function display($tpl){
		
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function check1(){
		//1.取教師資料
		$SQL="SELECT teach_person_id as perID,name as cname,teacher_sn as SN,sex ,edu_key ,ldap_password  FROM teacher_base  where teach_condition ='0' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;
	//echo '<pre>';print_r($New);
	}

	function add(){
		$sn=(int)$_GET['sn'];
		if ($sn==0) return ;
		//1.取教師資料
		$SQL="SELECT teach_person_id,name as cname,teacher_sn 
		 FROM teacher_base  where  teacher_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$perID=$arr[0]['teach_person_id'];
		if ($perID=='') return ;
		$edukey=hash('sha256',$perID);
		$SQL="update  teacher_base set edu_key='{$edukey}'  where  teacher_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		header("Location:".$_SERVER['SCRIPT_NAME']);
	//echo '<pre>';print_r($New);
	}

	function del(){
		$sn=(int)$_GET['sn'];
		if ($sn==0) return ;
		//1.取教師資料
		$SQL="update  teacher_base set edu_key=''  where  teacher_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		header("Location:".$_SERVER['SCRIPT_NAME']);
	//echo '<pre>';print_r($New);
	}



}


