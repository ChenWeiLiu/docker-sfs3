<?php
//$Id: run_test.php 6811 2012-06-22 08:18:14Z smallduh $
include "config.php";
//認證
sfs_check();

//引入換頁物件
include_once "../../include/chi_page2.php";

//建立物件
$obj= new run_test($smarty);
//初始化
$obj->init($mysql_host,$mysql_user,$mysql_pass);
//處理程序
$obj->process();

//秀出網頁布景標頭
head("測試模組");
echo make_menu($school_menu_p);
//樣本檔
$template_file = dirname (__file__)."/template/run_test.htm";
//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class run_test{
	// var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=20;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	
	var $DB;
	var $TB;
	var $field;
	
	

	//建構函式
	function run_test($smarty){
		//$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init($mysql_host,$mysql_user,$mysql_pass) {
		$this->link = mysql_connect($mysql_host,$mysql_user,$mysql_pass);
		$this->page=($_GET[page]=='') ? 0:$_GET[page];
		}
	//程序
	function process() {
		if ($_GET[TB]!='' && $_GET[DB]!='' && $_GET[DB]!='mysql'){
			$this->DB=$_GET[DB];
			$this->TB=$_GET[TB];
			$this->get_info();
			if($_POST[act]=='new_ord') $this->New_ord();
			$this->all();
		}
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//組合順序
	function New_ord(){
//		echo "<PRE>";print_r($_POST);
		//處理搜尋

		foreach ($_POST[serch_Key] as $key => $val ){
			if(in_array($key,$this->field) && $val!='' ) { $Serch[]= $key." like '%".$val."%' ";}
			}

		if ($Serch!=''){
			$this->Serch=" where ".join(" and ",$Serch);
			//session_register("Serch_SQL");
			$_SESSION[Serch_SQL]=$this->Serch;
		}

		//處理排序
		$ary=explode(",",$_POST[ord_key]);
		$str='';
		foreach ($ary as $val ){
			if(in_array($val,$this->field)) {$str[]=$val." ".$_POST[Ord_Key][$val];}
			}
		if($str!='') {
			$this->Add_SQL=" order by ".join(",",$str);
			//session_register("Add_SQL");
			$_SESSION[Add_SQL]=$this->Add_SQL;
			}
		else {
			unset($_SESSION[Add_SQL]);unset($_SESSION[Serch_SQL]);}
	}
	//擷取資料
	function all(){
		$this->Add_SQL=$_SESSION[Add_SQL];
		$this->Serch=$_SESSION[Serch_SQL];
		$SQL="select `{$this->field[0]}` from `{$this->TB}` {$this->Serch}";
		$SQL1="select `{$this->field[0]}` from `{$this->TB}` ";
		$rs = mysql_db_query ($this->DB,$SQL);
		if(!$rs) {
			unset($_SESSION[Serch_SQL]);
			$rs = mysql_db_query ($this->DB,$SQL1);
			}
		$this->tol=mysql_num_rows($rs);
		// order by {$this->field[0]} desc 
		
		$SQL="select * from `{$this->TB}` {$this->Add_SQL} limit ".($this->page*$this->size).", {$this->size}  ";
		$SQL1="select * from `{$this->TB}`  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs = mysql_db_query ($this->DB,$SQL);
		if(!$rs) {
			unset($_SESSION[Add_SQL]);
			$rs = mysql_db_query ($this->DB,$SQL1);
			}
		while ($row = mysql_fetch_array ($rs)) {$arr[]=$row;}
		$this->all=$arr;//return $arr;
		//產生連結頁面
		$URL=$_SERVER[PHP_SELF]."?DB=".$this->DB."&TB=".$this->TB;
		$this->links= new Chi_Page($this->tol,$this->size,$this->page,$URL);
	}

	function get_info(){
		$SQL=" SHOW DATABASES  ";
		$data = mysql_query( $SQL,$this->link ) or die("無法連接資料庫") ; //執行指令取出資料
		while ($row = mysql_fetch_array ($data)) {
			if($row[0]=='mysql') continue;
			$db[]=$row[0];
		}

		if(!in_array($this->DB,$db)) die("無該資料庫");

		$SQL="SHOW TABLES FROM  `{$this->DB}` ";
		$data = mysql_query( $SQL,$this->link ) or die($SQL); //執行指令取出資料
		while ($row = mysql_fetch_array ($data)) {$tb[]=$row[0];}
		if(!in_array($this->TB,$tb)) die("無該資料表");
		
		$SQL="SHOW FIELDS FROM `{$this->TB}`  ";
		$data = mysql_db_query ($this->DB,$SQL);
		while ($row = mysql_fetch_array ($data)) {$Field[]=$row[0];}
		$this->field=$Field;
		$this->count_field=count($Field);
	}

}
?>