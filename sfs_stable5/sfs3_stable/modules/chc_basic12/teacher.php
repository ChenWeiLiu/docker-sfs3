<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
// ini_set('display_errors', '1');
include "config.php";
//認證
sfs_check();

//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/teacher.htm";

//建立物件
$obj= new chc_seme($CONN,$smarty);
//初始化
$obj->init();
//判別國中6/國小0 變數
//$obj->IS_JHORES=$IS_JHORES;
//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前

$obj->process();

//秀出網頁布景標頭
head("彰化區免試入學");

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

	var $IS_JHORES;//國中小
	var $year;//學年
	var $seme;//學期
	var $YS='year_seme';//下拉式選單學期的奱數名稱
	var $year_seme;//下拉式選單班級的奱數值
	var $Sclass='class_id';//下拉式選單班級的奱數名稱
	//建構函式
	function chc_seme($CONN,$smarty){
		global $IS_JHORES;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->IS_JHORES=$IS_JHORES;

	}
	//初始化
	function init() {
		//取得任教班級代號
		$class_num = get_teach_class();
		if ($class_num == '') backe('您不是級任老師!!');
		
		$A=substr($class_num,0,1);
		$B=substr($class_num,1,2);
		$this->class_id=curr_year().'_'.curr_seme().'_'.sprintf("%02d",$A).'_'.sprintf("%02d",$B);
		//echo $this->class_id;
		}
	//程序
	function process() {
		$this->all();
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
		$this->stu=$this->get_stu();
//		print_r($this->sco);		
	}
/* 取學生陣列,取自stud_base表與stud_seme表*/
	function get_stu(){
		$CID=split("_",$this->class_id);//093_1_01_01
		$year=$CID[0];
		$seme=$CID[1];
		$grade=$CID[2];//年級
		$class=$CID[3];//班級
		$CID_1=$year.$seme;//0911
		$CID_2=sprintf("%03d",$grade.$class);//601
		$SQL="select 	a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond  from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
		$SQL="select a.stud_id,a.stud_name,a.stud_birthday ,a.stud_sex, a.stud_person_id ,
		b.seme_class,b.seme_num,a.stud_study_cond ,c.*  
		from stud_base a,stud_seme b, chc_basic12 c  where a.student_sn=c.student_sn and c.student_sn=b.student_sn 
		and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' 
		 order by b.seme_num ";
		//改用 join 方式
		$SQL="select a.stud_id,a.stud_name,a.stud_birthday ,a.stud_sex, a.stud_person_id ,b.student_sn ,
		b.seme_class,b.seme_num,a.stud_study_cond ,c.sn,  c.academic_year,  c.kind_id,  c.special,
  		c.unemployed,  c.graduation,  c.income,  c.score_nearby,  c.score_service,  c.score_reward,
  		c.score_fault, c.score_club, c.score_balance,  c.score_race,  c.score_physical,  c.score_exam,  c.update_sn
		from stud_base a,stud_seme b left join  chc_basic12 c on c.student_sn=b.student_sn  
		where a.student_sn=b.student_sn 	and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' 
		 order by b.seme_num ";
		
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All=$rs->GetArray();
		foreach ( $All as $ary ) {
			$SN=$ary['student_sn'];
			$obj_stu[$SN] = $ary;
			unset($ary);
		}
		//echo "<pre>";print_r($obj_stu);
		return $obj_stu;	
	}


	//傳回該生該科該階段成績//



##################  班名學期顯示 ##########################
function select() {
	$YS=''; 
	$YS=curr_year().'學年度第'.curr_seme().'學期 ';
	($this->IS_JHORES==6) ? $grade=array(7=>"一年",8=>"二年",9=>"三年"):$grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年");
	$SQL="select c_year,c_name,teacher_1 from  school_class where class_id='".$this->class_id."' and  enable='1'  ";
	//echo $SQL;
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return"沒有班級資料！";
	$All=$rs->GetArray();
 	$ary=$All[0];
 	$str=$YS.$grade[$ary['c_year']].$ary['c_name']."班  導師:".$ary['teacher_1'];
	return $str;
	}
	
function tol20($max,$a) {
	if ($a>$max) return $max;
	return $a;
}


}
