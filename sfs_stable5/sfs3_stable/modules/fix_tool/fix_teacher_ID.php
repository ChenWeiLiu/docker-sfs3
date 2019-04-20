<?php

include "config.php";
//認證
sfs_check();

//指定樣本
$template_file = dirname (__file__)."/templates/fix_teacher_id.htm";

//建立物件
$obj= new fix_teacherID($CONN,$smarty);
$obj->sfsURL=$SFS_PATH_HTML;

//初始化
$obj->init();

//處理程序,有時程序內有header指令,故本程序宜於head("chc_basic12模組");之前
$obj->process();

//秀出網頁布景標頭
head("全校身份證字號重複檢查");

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
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {}
	//程序
	function process() {
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
		$SQL="SELECT teach_person_id as perID,name as cname,teacher_sn as SN,
		'T' as kind FROM teacher_base";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr1=$rs->GetArray();
		//2.取學生資料
		$SQL="SELECT stud_person_id as perID,stud_name as cname,student_sn as SN,
		'S' as kind FROM stud_base ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr2=$rs->GetArray();
		// 3.合併$arr1+$arr2;
		$arr = array_merge($arr1,$arr2);
		//4.跑迴圈拿字號當KEY成為新陣列
		foreach ($arr as $ary){
			$K=$ary['perID'];//拿字號當Key
			$Pid[$K][]=$ary;
			}
		//5.計算新陣列內容大於1的,並列出顯示
		foreach ($Pid as $k=>$AR){
			if (count($AR)>1) $New[$k]=$AR;
			}
	
	$this->check=$New;
	$this->tol=count($New);
	//echo '<pre>';print_r($New);
	}


}


