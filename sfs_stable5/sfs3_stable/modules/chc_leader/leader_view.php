<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/leader_view.html";

//建立物件
$obj= new chc_leader($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("chc_leader模組");之前
$obj->process();

//秀出網頁布景標頭
head("[彰]班級幹部管理");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class chc_leader{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=20;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $KKary=array('A'=>'班級幹部','B'=>'社團幹部','C'=>'全校性幹部');
	var $K2ary=array('A'=>'0','B'=>'1','C'=>'2');
	var $Grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年",7=>"一年",8=>"二年",9=>"三年");
	//建構函式
	function chc_leader($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		if ($_GET['KK']!='') $KK=strip_tags($_GET['KK']);
		if ($_POST['KK']!='') $KK=strip_tags($_POST['KK']);
		$this->KK=($KK=='') ? 'A':$KK;
		$this->page=($_GET['page']=='') ? 0:$_GET['page'];}
	//程序
	function process() {
		if(isset($_GET['form_act']) && $_GET['form_act']=='del') $this->del();
		$this->all();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//刪除
	function del(){
		$id=(int)$_GET['id'];
		$SQL="Delete from  chc_leader  where  id='{$id}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME']."?KK=".$this->KK."&page=".$this->page;
		Header("Location:$URL");
	}


	//擷取資料
	function all(){
		$K=$this->KK;
		$KIND=$this->K2ary[$K]; 
		
		$SQL="select id from chc_leader where kind='{$KIND}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->tol=$rs->RecordCount();
		$SQL="select a.*,b.stud_name,b.stud_id,b.stud_sex from chc_leader  a,stud_base b  
		where  kind='{$KIND}' and a.student_sn=b.student_sn order by a.seme desc, 	a.org_name  desc,a.title  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		//echo$SQL; 
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
	//產生連結頁面
		$url=$_SERVER['SCRIPT_NAME']."?KK=".$this->KK;
		$this->links= new Chi_Page($this->tol,$this->size,$this->page,$url);
	}

	function OName($cla){
		$G=substr($cla,0,1);
		$G2=substr($cla,-2);
		return $this->Grade[$G].$G2.'班';
	}
	
}

