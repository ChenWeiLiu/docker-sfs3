<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();


//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/statis_chi.htm";

$isAdmin = (int)checkid($_SERVER['SCRIPT_FILENAME'],1);


//建立物件
$obj= new teacher_absent($CONN,$smarty);
//初始化
$obj->init();
//職稱陣列
$obj->post_kind=post_kind();
//管理者
$obj->isAdmin=$isAdmin;

//處理程序,有時程序內有header指令,故本程序宜於head("teacher_absent模組");之前
$obj->process();


head("年度差假統計");				//秀出網頁布景標頭
echo make_menu($school_menu_p);	//顯示SFS連結選單(欲使用請拿開註解)
$obj->display($template_file);	//顯示內容
foot();							//佈景結尾

//物件class
class teacher_absent{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $post_kind;//職稱陣列
	var $Y;//年度
/*
	var $ABS=array(
	'11'=>'事假','12'=>'家庭照顧假','21'=>'病假','22'=>'生理假','31'=>'公差',
	'41'=>'婚假','42'=>'產前假','43'=>'娩假','44'=>'流產假','45'=>'陪產假',
	'46'=>'喪假','47'=>'公假','52'=>'公差假','53'=>'公出','54'=>'路程假',
	'55'=>'慰勞假','56'=>'公傷假','61'=>'其他','23'=>'延長病假','81'=>'休假',
	'82'=>'加班補休','84'=>'值日補休','91'=>'骨髓捐贈','92'=>'器官捐贈','93'=>'災防假');
*/
	//建構函式
	function teacher_absent($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->ABS=tea_abs_kind();
	}
	//初始化
	function init() {
		$year=(int)$_GET['Y'];
		if ($year==0 || $year=='') $year=date("Y");
		$this->Y=$year;
		$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//程序
	function process() {

		$this->all();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){

		//預設只列出本人
		$ADD=" a.teacher_sn='{$_SESSION['session_tea_sn']}'  AND ";
		if ($this->isAdmin==1) $ADD='';
		
		$SQL="select a.teacher_sn,a.name,d.title_name,c.post_kind from teacher_base a,teacher_post c, teacher_title d WHERE $ADD 
		a.teach_condition=0  AND c.teacher_sn=a.teacher_sn AND c.teach_title_id=d.teach_title_id  order by  d.rank";
		//echo $SQL;
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;


		$SQL="SELECT * FROM teacher_absent WHERE check4_sn>0 and left(start_date,4)='{$this->Y}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		foreach ($arr as $A){
			$SN=$A['teacher_sn'];
			$abs[$SN][]=$A;
			}
		$this->Tea_abs=$abs;//return $arr;
	}
	//新增
	function getABS($SN){
		$tmp=array();
		foreach ($this->Tea_abs[$SN] as $ary){
			$K=$ary['abs_kind'];
			$tmp[$K]['day']=$tmp[$K]['day']+$ary['day'];
			$tmp[$K]['hour']=$tmp[$K]['hour']+$ary['hour'];
			if ($tmp[$K]['hour']>=8){
				$tmp[$K]['day']++;
				$tmp[$K]['hour']=$tmp[$K]['hour']-8;}
		}
		return $tmp;

	}


}
