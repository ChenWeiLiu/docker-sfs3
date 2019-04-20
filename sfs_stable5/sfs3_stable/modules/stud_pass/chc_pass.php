<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $

//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";

sfs_check();

//學籍代碼資料
include_once "../../include/sfs_case_dataarray.php";
//引入下拉式班級選單物件(學務系統用法)
include_once "../../include/sfs_oo_dropmenu.php";

//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/chc_pass.htm";
//建立物件
$obj= new chc_seme($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前
$obj->process();

//秀出網頁布景標頭
head("學生密碼瀏覽與eduKey");

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
	var $subj;//科目陣列
	var $rule;//等第

	//建構函式
	function chc_seme($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {}
	//程序
	function process() {
		//學年度,學期
		$this->year_seme=strip_tags($_GET['year_seme']);
		//班級
		$this->class_id=strip_tags($_GET['class_id']);
		if ($_GET['act']=='Make') $this->add();
		if ($_GET['act']=='Del') $this->del();
		$this->cond=study_cond();//學籍資料代碼
		$this->all();
	}
	//顯示
	function display($tpl){
		$ob=new drop($this->CONN);
		$this->select=&$ob->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if ($this->class_id=='') return;
		$this->stu=$this->get_stu();

	}
/* 取學生陣列,取自stud_base表與stud_seme表*/
	function get_stu(){
		$CID=split("_",$this->class_id);//093_1_01_01
		$year=$CID[0];
		$seme=$CID[1];
		$grade=$CID[2];//年級
		$class=$CID[3];//班級
		$CID_1=$year.$seme;
		$CID_2=sprintf("%03d",$grade.$class);
		$SQL="select 	a.stud_id,a.student_sn, a.stud_name, a.stud_sex, a.stud_person_id , b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond, a.edu_key,a.email_pass   from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$obj_stu=array();
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$obj_stu[$ro->student_sn] = get_object_vars($ro);
		}
		return $obj_stu;	
	}


	function add(){
		$sn=(int)$_GET['sn'];
		if ($sn==0) return ;
		//1.取教師資料
		$SQL="SELECT * FROM stud_base  where student_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$perID=$arr[0]['stud_person_id'];
		if ($perID=='') return ;
		$edukey=hash('sha256',$perID);
		$SQL="update  stud_base set edu_key='{$edukey}'  where   student_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME'].'?year_seme='.$this->year_seme.'&class_id='.$this->class_id;
		header("Location:".$URL);
	//echo '<pre>';print_r($New);year_seme=105_1&class_id=105_1_02_05
	}

	function del(){
		$sn=(int)$_GET['sn'];
		if ($sn==0) return ;
		//1.取教師資料
		$SQL="update  stud_base set edu_key=''  where  student_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME'].'?year_seme='.$this->year_seme.'&class_id='.$this->class_id;
		header("Location:".$URL);
	//echo '<pre>';print_r($New);
	}




}

