<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
// include_once "../../include/sfs_oo_dropmenu.php";

//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/leader_add.htm";
//建立物件
$obj= new chc_seme($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前
$obj->process();
//echo '<pre>';print_r($_POST);die();
//秀出網頁布景標頭
head("[彰]班級幹部管理");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);
//$ob=new drop($this->CONN,$IS_JHORES);
//		$this->select=$ob->select();
//echo $ob->select();
//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class chc_seme{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $stu;//學生資料
	var $class_id;//科目陣列
	var $StuTitle;
	var $kind=array('0'=>'0.班級幹部','1'=>'1.社團幹部','2'=>'2.全校性幹部');
	//var $kind0=array('班長','副班長','康樂股長','學藝股長','事務股長','衛生股長','風紀股長','輔導股長','環保股長','資訊股長');


	//建構函式
	function chc_seme($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		$YS=''; 
		if (isset($_POST['year_seme'])) $YS=$_POST['year_seme'];
		if ($YS=='' && isset($_GET['year_seme'])) $YS=$_GET['year_seme'];
		if ($YS=='') $YS=curr_year()."_".curr_seme();
		$this->year_seme=$YS;
		$aa=split("_",$this->year_seme);
		$this->year=$aa[0];
		$this->seme=$aa[1];		
		
		}
	//程序
	function process() {
		if(isset($_POST['form_act']) && $_POST['form_act']=='add1') $this->add1();
		if(isset($_POST['form_act']) && $_POST['form_act']=='add2') $this->add2();
	
		$this->all();
	}

	//擷取資料
	function all(){	}


	//顯示
	function display($tpl){
		//$ob=new drop($this->CONN);
		// $this->select=&$ob->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}





	//新增資料
	function add1(){
		//echo '<pre>';print_r($_POST);die();
		// $kind='0';
	
		$stud_id=strip_tags(trim($_POST['NO']));
		$SQL="select  stud_id ,student_sn from stud_base where stud_id='$stud_id' order by student_sn desc limit 1 ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		if(count($arr)==0) backe('！！沒有這個學生學號！！');
		$tea_sn=$_SESSION['session_tea_sn'];
		$SN=$arr[0]['student_sn'];
		$seme=strip_tags(trim($_POST['seme']));
		$title=strip_tags(trim($_POST['title']));
		$org_name=strip_tags(trim($_POST['org_name']));
		if (!is_numeric($org_name)) backe('！！班級代碼填寫錯誤！！');
		$memo=strip_tags(trim($_POST['memo']));
		$cr_time=date("Y-m-d H:i:s");
		$SQL="INSERT INTO chc_leader(student_sn,seme,kind,org_name,title,update_sn,cr_time)  
		values ('{$SN}' ,'{$seme}' ,'0' ,'{$org_name}' ,'{$title}' ,'{$tea_sn}' ,'{$cr_time}' )";
		$rs=$this->CONN->Execute($SQL) or die($SQL);

		$URL="leader_prt.php?SN=".$SN;
		Header("Location:$URL");
	}





	//新增資料2
	function add2(){
		//echo '<pre>';print_r($_POST);die();
		// $kind='0';
	
		$stud_id=strip_tags(trim($_POST['NO']));
		$SQL="select  stud_id ,student_sn from stud_base where stud_id='$stud_id' order by student_sn desc limit 1 ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		if(count($arr)==0) backe('！！沒有這個學生學號！！');
		$kind=(int)$_POST['kind'];
		if($kind!=1 and $kind!=2) backe('！！類別錯誤！！');
		$tea_sn=$_SESSION['session_tea_sn'];
		$SN=$arr[0]['student_sn'];
		$seme=strip_tags(trim($_POST['seme']));
		$title=strip_tags(trim($_POST['title']));
		$org_name=strip_tags(trim($_POST['org_name']));
		//if (!is_numeric($org_name)) backe('！！班級代碼填寫錯誤！！');
		$memo=strip_tags(trim($_POST['memo']));
		$cr_time=date("Y-m-d H:i:s");
		$SQL="INSERT INTO chc_leader(student_sn,seme,kind,org_name,title,update_sn,cr_time)  
		values ('{$SN}' ,'{$seme}' ,'{$kind}' ,'{$org_name}' ,'{$title}' ,'{$tea_sn}' ,'{$cr_time}' )";
		$rs=$this->CONN->Execute($SQL) or die($SQL);

		$URL="leader_prt.php?SN=".$SN;
		Header("Location:$URL");
	}







}


