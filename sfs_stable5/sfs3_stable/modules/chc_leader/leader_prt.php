<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();


//建立物件
$obj= new chc_seme($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前
$obj->process();


//物件class
class chc_seme{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $stu;//學生資料
	var $class_id;//科目陣列
	var $StuTitle;
	var $Grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年",7=>"一年",8=>"二年",9=>"三年");
	//建構函式
	function chc_seme($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->kind0=getLeaderKind('A');
	}
	//初始化,取得學生SN陣列
	function init() {
		$this->allSN='';
		//傳入學生SN
		if (isset($_GET['SN']) && empty($_GET['class_id'])){
			$SN=(int)$_GET['SN'];//取數字
			$this->allSN=array($SN);
		}
		//傳入班級碼
		if (isset($_GET['class_id']) && empty($_GET['SN'])){
			$cla=strip_tags($_GET['class_id']);//取數字
			$this->allSN=$this->getSN($cla);
		}
		if ($this->allSN=='') backe('!!!未傳值!!!');
		
	}
	//程序
	function process() {
		// 新增 if(isset($_POST['form_act']) && $_POST['form_act']=='add') $this->add();
		// 更新 if(isset($_POST['form_act']) && $_POST['form_act']=='update') $this->update();
		// 刪除 if(isset($_GET['form_act']) && $_GET['form_act']=='del') $this->del();
		$this->all();
		$this->display();
	}

	//顯示
	function display(){
		//程式使用的Smarty樣本檔
		$tpl = dirname (__file__)."/templates/leader_prt.htm";
		// 秀出網頁布景標頭
		// head("[彰]班級幹部管理");
		// 顯示SFS連結選單(欲使用請拿開註解)
		// echo make_menu($school_menu_p);
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
		// 佈景結尾
		// foot();
	}

	//傳入class_id,傳出學生sn陣列(一個班)
	function getSN($class_id){
		$st_sn=array();
		$class_ids=split("_",$class_id);
		$seme=$class_ids[0].$class_ids[1];
		$the_class=($class_ids[2]+0).$class_ids[3];
		$SQL="select student_sn from stud_seme where  seme_year_seme ='$seme' and seme_class='$the_class' order by seme_num ";
		//echo $SQL;
		$rs = $this->CONN->Execute($SQL);
		if ($rs->RecordCount()===0) return '';
		$all=$rs->GetArray();
		foreach ($all as $ary){
			$st_sn[]=$ary['student_sn'];
			}
		return $st_sn;
	}
	//擷取資料
	function all(){
		//if ($this->SN=='') return;
		$this->sch=get_school_base();

	}


	//擷取資料
	function OneStu($sn){

		if ($sn=='') return;
		//print_r($this->sch);
		$SQL="select stud_id,stud_name,stud_sex,stud_birthday ,stud_person_id  from stud_base where student_sn='{$sn}' ";
		// echo $SQL; 
		// $this->StuTitle='';
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()===0) backe('!!!沒有該學生!!!');
		$tmp=$rs->GetArray();
		$Stu['base']=$tmp[0];


		//$SQL="select a.*,b.seme_year_seme,b.seme_class,b.seme_num from chc_leader a,stud_seme b	
		//where a.student_sn='{$sn}' and a.student_sn=b.student_sn  and a.seme=b.seme_year_seme
		//order by a.seme asc ,a.kind asc ";
		
		$SQL="select a.*,b.seme_year_seme,b.seme_class,b.seme_num from chc_leader a
		left join stud_seme b	on (a.student_sn=b.student_sn and a.seme=b.seme_year_seme)
		where a.student_sn='{$sn}' 	order by a.seme asc ,a.kind asc ";

		// echo $SQL; 
		// $this->StuTitle='';
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()===0) {$Stu['data']='';}else {	$Stu['data']=$rs->GetArray();} 
		return $Stu;
	}

	function OName($cla){
		$G=substr($cla,0,1);
		$G2=substr($cla,-2);
		return $this->Grade[$G].$G2.'班';
	}
	function Birth($dd){
		$da=explode('-',$dd);
		$str=($da[0]-1911).'-'.$da[1].'-'.$da[2];
	return $str;
}




}
