<?php
//$Id: sta_view.php 6120 2010-09-11 02:38:04Z brucelyc $
include "config.php";
//認證
sfs_check();


//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/sta_view.htm";

//建立物件
$obj= new stud_sta($CONN,$smarty);

//初始化
$obj->init();

//處理程序,有時程序內有header指令,故本程序宜於head("stud_sta模組");之前
$obj->process();

//秀出網頁布景標頭
head("stud_sta模組");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);

//佈景結尾
foot();

//物件class
class stud_sta{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=25;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數

	//建構函式
	function stud_sta($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//程序
	function process() {

		if($_GET[form_act]=='enable') $this->update();
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
		$SQL="select prove_id from stud_sta ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$this->tol=$rs->RecordCount();
		$SQL="select a.*,b.stud_name  from stud_sta a ,stud_base b where a.student_sn=b.student_sn order by prove_id desc  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		//取教師姓名
		$SQL="select DISTINCT(a.set_id),b.name  from stud_sta a ,teacher_base b where a.set_id=b.teach_id ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		if ($rs ) { 
			foreach ($rs->GetArray() as $ary){$this->tea[$ary[set_id]]=$ary[name];}
		}
	//產生連結頁面
		$this->links= new Chi_Page($this->tol,$this->size,$this->page);
	}
	//更新
	function update(){
		$SQL="update  stud_sta set  prove_cancel ='0' where prove_id ='{$_GET['id']}'";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?page=".$_GET[page];
		Header("Location:$URL");
	}
	//刪除
	function del(){
		$SQL="update  stud_sta set  prove_cancel ='1' where prove_id ='{$_GET['id']}'";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?page=".$_GET[page];
		Header("Location:$URL");
	}
}
?>
