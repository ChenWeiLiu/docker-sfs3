<?php
//$Id: chc_view.php 7979 2014-04-15 14:19:13Z chiming $

require "config.php";
sfs_check();

//$stu=stu();
//echo "<pre>";print_r($stu);
//echo "hi";die();

//引入函數

$template_file = dirname (__file__)."/chc_view.htm";

$obj= new chc_view($CONN,$smarty);
$obj->IS_JHORES=&$IS_JHORES;
$obj->process();

//1.秀出網頁布景標頭
head("舊班新班對照檢視");

//顯示SFS連結選單(欲使用請拿開註解)
//echo make_menu($school_menu_p);

//myheader();
//2.顯示內容
$obj->display($template_file);

//3.佈景結尾
foot();



class chc_view{ //建立類別
  var $CONN;    //adodb物件
  var $Smarty;  //smarty物件
//  var $seme;    //學期    
  var $rs;      //所有學生
  var $stu;      //本學期所有學生陣列
	//建構函式
	function chc_view($CONN,$smarty){
		global $IS_JHORES;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->IS_JHORES=$IS_JHORES;

	}
  //初始化
  function init() {
		if ($this->IS_JHORES==6){
			$this->grade=array(7=>" 一年級",8=>" 二年級",9=>" 三年級");
			$this->sgrade=array(7=>" 一年",8=>" 二年",9=>" 三年");
			}
		else{
			$this->grade=array(1=>" 一年級",2=>" 二年級",3=>" 三年級",4=>" 四年級",5=>"五年級",6=>"六年級");
			$this->sgrade=array(1=>" 一年",2=>" 二年",3=>" 三年",4=>" 四年",5=>"五年",6=>"六年");
		}
	}

  //啟用程序
  function process(){
		$this->init();
		if (isset($_GET['year']) && $_GET['year']!='')  $this->all();
  }

	//顯示
	function display($tpl){
		//$ob=new drop($this->CONN);
		//$this->select=$this->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		$Y=(int)$_GET['year'];
		if ($Y=='' ||$Y=='0' ) return;
		$SQL="select a.*,b.stud_name,b.stud_id,b.stud_sex, b.stud_study_cond   
		from stud_compile a,stud_base b  where 
		a.student_sn=b.student_sn and a.old_class like '{$Y}%' order by a.old_class ";
		
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return;
		//if ($rs->RecordCount()==0) backe("01.系統暫無您的資料！");
		$all=$rs->GetArray();
		foreach ($all as $ary){
			$cla=substr($ary['old_class'],0,3);
			$No=substr($ary['old_class'],3,2);
			$A[$cla][$No]=$ary;	
			}
			ksort($A);//依班級排
			$this->Old=$A;
		unset($A);
		
		foreach ($all as $ary){
			$cla=$ary['new_class'];
			$No=$ary['site_num'];
			$A[$cla][$No]=$ary;			
			}
			ksort($A);//依班級排
		$this->New=$A;



	}



}
//  end class
