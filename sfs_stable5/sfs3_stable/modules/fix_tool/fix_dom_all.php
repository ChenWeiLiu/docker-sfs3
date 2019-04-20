<?php
//$Id$
include "config.php";
//認證
sfs_check();
include $SFS_PATH.'/include/chi_page2.php';

//建立物件
$obj= new My_TB($CONN,$smarty);
//初始化
$obj->init();
//處理程序
$obj->process();

//秀出網頁布景標頭
head("問題工具箱--補入戶口");
//樣本檔
print_menu($school_menu_p);
//顯示內容
$obj->display();
//佈景結尾
foot();


//物件class
class My_TB{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=100;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數


	//建構函式
	function My_TB($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		$page=($_GET[page]=='') ? $_POST[page]:$_GET[page];
		$this->page=($page=='') ? 0:(int)$page;
	}
	//程序
	function process() {
		$this->init();
		if($_POST['form_act']=='add') $this->add();
		$this->all();
	}
	//顯示
	function display(){
		$tpl = "fix_dom_all.htm";
		$this->smarty->template_dir=dirname(__file__)."/templates/";
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if($_POST['form_act']=='show'){
			$SQL="SHOW CREATE TABLE stud_domicile"; 
			$rs=&$this->CONN->Execute($SQL) or die($SQL);
			$arr=&$rs->GetArray();
			$this->all=$arr[0];
			return ;
		}
		$SQL="select student_sn from stud_base ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$this->tol=$rs->RecordCount();
		
		
		$SQL="select a.student_sn,a.stud_id,a.stud_study_cond,a.stud_study_year,a.stud_name,
		a.stud_sex,a.curr_class_num,b.student_sn as NN from stud_base a 
		left join stud_domicile b on a.student_sn=b.student_sn and a.stud_id=b.stud_id 
		order by student_sn desc  limit ".($this->page*$this->size).", {$this->size} 	";
		  //a.student_sn=b.student_sn and a.stud_id=b.stud_id ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$this->all=&$rs->GetArray();
		
		$this->links=new Chi_Page($this->tol,$this->size,$this->page);
		//$this->all=$All;//return $arr;

	}
	//新增
	function add(){
		// echo '<pre>';print_r($_POST);
		if (count($_POST['StuSN'])< 1 ) return ;
		foreach ($_POST['StuSN'] as $sn=>$id){
			$SQL="INSERT INTO stud_domicile (stud_id ,student_sn) values ('{$id}' ,'{$sn}')";
			//echo $SQL.'<br>'; 
			$rs=&$this->CONN->Execute($SQL) or die($SQL);		
		}
		
		//die();
		//$Insert_ID= $this->CONN->Insert_ID();
		$URL=$_SERVER['SCRIPT_NAME'].'?page='.$this->page;
		Header("Location:$URL");
	}



}

