<?php
//$Id$
include "config.php";
//認證
sfs_check();


//建立物件
$obj= new My_TB($CONN,$smarty);
//初始化
$obj->init();
//處理程序
$obj->process();

//秀出網頁布景標頭
head("問題工具箱--補入戶口");
//樣本檔

//顯示內容
$obj->display();
//佈景結尾
foot();


//物件class
class My_TB{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數


	//建構函式
	function My_TB($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {}
	//程序
	function process() {
		if($_GET['act']=='add') $this->add();
		$this->all();
	}
	//顯示
	function display(){
		$tpl = "fix_dom.htm";
		$this->smarty->template_dir=dirname(__file__)."/templates/";
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if ($_POST[stud_id]=='') return ;
		$stud_id=(int)$_POST['stud_id'];
		if ($stud_id==0 || $stud_id=='') return ;
		$SQL="select * from stud_base where stud_id='$stud_id' ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$arr=&$rs->GetArray();
		foreach ($arr as $ary){
			$SN=$ary['student_sn'];
			$ID=$ary['stud_id'];
			$ary['check_dom']=$this->sTol($SN,$ID);
			$All[]=$ary;		
		}
		
		$this->all=$All;//return $arr;

	}
	//新增
	function add(){
		if ($_GET['sn']=='') return ;
		$sn=(int) $_GET['sn'];
		if ($sn==0 ||$sn=='' ) return ;
		$SQL="select student_sn,stud_id from stud_base where student_sn='{$sn}' ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$tol=$rs->RecordCount();
		if ($tol==0 || $tol >1 ) return ; 
		$arr=&$rs->GetArray();
		$ary=$arr[0];
		
		
		$SQL="INSERT INTO stud_domicile (stud_id ,student_sn) values (
		'{$ary['stud_id']}' ,'{$ary['student_sn']}'  )";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		//$Insert_ID= $this->CONN->Insert_ID();
		$URL=$_SERVER[PHP_SELF];
		Header("Location:$URL");
	}
	//更新
	function sTol($sn,$id){
		$SQL="select * from stud_domicile where stud_id='{$id}' and student_sn='{$sn}' ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		return $rs->RecordCount();
	}


}

