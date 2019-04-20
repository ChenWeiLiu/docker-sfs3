<?php
//$Id: mgr_cal.php 7866 2014-01-23 09:32:31Z hami $

include "config.php";
//認證
sfs_check();


 
//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/cal_elps_set.htm";

//建立物件
$obj= new cal_elps_set($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("cal_elps_set模組");之前
$obj->process();

//秀出網頁布景標頭
head("校務行事曆");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class cal_elps_set{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $wk_mode=array("0"=>"自動計算","1"=>"開學日設定");
	//建構函式
	function cal_elps_set($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//程序
	function process() {
		if($_POST[form_act]=='add') $this->add();
		if($_POST[form_act]=='update') $this->update();
		if($_GET[form_act]=='del') $this->del();
		$this->all();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		$SQL="select syear from cal_elps_set ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->tol=$rs->RecordCount();
		$SQL="select * from cal_elps_set  order by syear desc  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
	//產生連結頁面
		$this->links= new Chi_Page($this->tol,$this->size,$this->page);
	}
	//新增
	function add(){
		$SQL="INSERT INTO cal_elps_set(syear,sday,weeks,unit,week_mode) values ('{$_POST['syear']}' ,'{$_POST['sday']}','{$_POST['weeks']}' ,'{$_POST['unit']}' ,'{$_POST['week_mode']}')";
		$res=$this->CONN->Execute($SQL) or die($SQL);
		//$Insert_ID= $this->CONN->Insert_ID();
		if($_POST['copy_prior']){
			$syear=$_POST['syear'];
			$prior_year=substr($syear,0,-1)-1;
			$prior_seme=substr($syear,-1);
			$prior=sprintf('%03d%d',$prior_year,$prior_seme);
			$now=date("Y-m-d");
			
			//更新cal_elps_set資料   
			$sql2="SELECT weeks,unit,week_mode FROM cal_elps_set WHERE syear='$prior'";
			$rs=$this->CONN->Execute($sql2) or die('SQL執行錯誤:'.$sql2);
			$sql_update="UPDATE cal_elps_set SET weeks='{$rs->fields['weeks']}',unit='{$rs->fields['unit']}',week_mode='{$rs->fields['week_mode']}' WHERE syear='$syear'";
			$rs_update=$this->CONN->Execute($sql_update) or die($sql_update);
			//新增cal_elps資料 
			$sql2="SELECT * FROM cal_elps WHERE syear='$prior'";	
			$rs=$this->CONN->Execute($sql2) or die('SQL執行錯誤:'.$sql2);
			while(!$rs->EOF) {
				$sql_copy="INSERT INTO cal_elps SET syear='$syear',week='{$rs->fields['week']}',unit='{$rs->fields['unit']}',user='{$rs->fields['user']}',day='$now',event='{$rs->fields['event']}',important='{$rs->fields['important']}'";
				$rs_copy=$this->CONN->Execute($sql_copy) or die('SQL執行錯誤:'.$sql_copy);
				$rs->MoveNext();
			}
		}
		$URL=$_SERVER[PHP_SELF]."?page=".$_POST[page];
		Header("Location:$URL");
	}
	//更新
	function update(){
		$SQL="update  cal_elps_set set   syear ='{$_POST['syear']}', sday ='{$_POST['sday']}', weeks ='{$_POST['weeks']}', unit ='{$_POST['unit']}', week_mode ='{$_POST['week_mode']}' where syear ='{$_POST['syear']}'";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?page=".$_POST[page];
		Header("Location:$URL");
	}
	//刪除
	function del(){
		$SQL="Delete from  cal_elps_set  where  syear='{$_GET['syear']}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$SQL="Delete from  cal_elps  where  syear='{$_GET['syear']}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?page=".$_GET[page];
		Header("Location:$URL");
	}
}

?>