<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
// ini_set('display_errors', '1');
include "config.php";
//認證
sfs_check();

chk_login('學務處,訓導處');

//引入換頁物件(學務系統用法)
// include_once "../../include/sfs_oo_dropmenu.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/stud_listB.htm";

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
		$YS=''; 
		/* -- 限制其他學期輸入
		if (isset($_POST['year_seme'])) $YS=$_POST['year_seme'];
		if ($YS=='' && isset($_GET['year_seme'])) $YS=$_GET['year_seme'];
		if ($YS=='') $YS=curr_year()."_".curr_seme();
		*/
		$YS=curr_year()."_".curr_seme();
		$this->year_seme=$YS;
		$aa=split("_",$this->year_seme);
		$this->year=$aa[0];
		$this->seme=$aa[1];
	}
	//初始化
	function init() {	}
	//程序
	function process() {
		if(isset($_POST['form_act']) && $_POST['form_act']=='update') $this->update();

		$this->all();
		//echo $this->year;
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
		if ($_GET['class_id']=='') return;
		$this->class_id=$_GET['class_id'];
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
		$SQL="select a.stud_id,a.stud_name,a.stud_birthday ,a.stud_sex, a.stud_person_id ,
		b.seme_class,b.seme_num,a.stud_study_cond ,c.*  
		from stud_base a,stud_seme b, chc_basic12 c  where a.student_sn=c.student_sn and c.student_sn=b.student_sn 
		and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' 
		 order by b.seme_num ";
		
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$obj_stu=array();
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$obj_stu[$ro->student_sn] = get_object_vars($ro);
		}
		return $obj_stu;	
	}

	//更新資料
	function update(){
		$session_tea_sn=$_SESSION['session_tea_sn'];
		
		$fields=array('sn','student_sn','class_id','score_service','score_reward', 'score_fault','score_club','score_race','score_physical');


		foreach ($fields as $FF){
			//if ($_POST[$FF]=='') continue ;
			$tmp=filter_var($_POST[$FF], FILTER_SANITIZE_STRING);
			$$FF=strip_tags(trim($tmp));
		}
		$academic_year=$this->year;
		$class_id=strip_tags($_POST['class_id']);
		$SQL="update chc_basic12 set 	`score_service`='{$score_service}' ,
		`score_reward`='{$score_reward}' ,`score_fault`='{$score_fault}' , `score_club`='{$score_club}'  ,
		`score_race`='{$score_race}' ,`score_physical`='{$score_physical}', update_sn = '{$session_tea_sn}'  
		where sn='{$sn}' and  `academic_year`='{$academic_year}' and `student_sn`='{$student_sn}' ";

		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME']."?year_seme=".$this->year_seme."&class_id=".$class_id;
		Header("Location:$URL");
 
	}
	//傳回該生該科該階段成績//



##################  學期下拉式選單函式  ##########################
function select() {
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
		$ro = $rs->FetchNextObject(false);
		// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
		$year_seme=$ro->year."_".$ro->seme;
		$obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
	}
	$str="<select name='".$this->YS."' onChange=\"location.href='".$_SERVER[SCRIPT_NAME]."?".$this->YS."='+this.options[this.selectedIndex].value;\" disabled>\n";
		//$str.="<option value=''>-未選擇-</option>\n";
	foreach($obj_stu as $key=>$val) {
		($key==$this->year_seme) ? $bb=' selected':$bb='';
		$str.= "<option value='$key' $bb>$val</option>\n";
		}
	$str.="</select>";
	$str.=$this->grade();
	return $str;
}
##################陣列列示函式2##########################
function grade() {
	//名稱,起始值,結束值,選擇值
	$url="?".$this->YS."=". $this->year_seme."&".$this->Sclass."=";
	//($this->IS_JHORES==6) ? $grade=array(7=>"一年",8=>"二年",9=>"三年"):$grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年");
	($this->IS_JHORES==6) ? $grade=array(9=>"三年"):$grade=array(6=>"六年");
	$gradeA=($this->IS_JHORES==6) ? 9:6;
	//只選高年級6/9
	$SQL="select class_id,c_year,c_name,teacher_1 from  school_class 
	where year='".$this->year."' and semester='".$this->seme."' and enable='1' 
	and c_year='{$gradeA}'  order by class_id  ";
	//echo $SQL;
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return"尚未設定班級資料！";
	$All=$rs->GetArray();
	$str="<select name='".$this->Sclass."' onChange=\"location.href='".$url."'+this.options[this.selectedIndex].value;\">\n";
	$str.= "<option value=''>-未選擇-</option>\n";
	foreach($All as $ary) {
		($ary[class_id]==$_GET[$this->Sclass]) ? $bb=' selected':$bb='';
		$str.= "<option value='".$ary[class_id]."' $bb>".$grade[$ary[c_year]].$ary[c_name]."班 (".$ary[teacher_1].")</option>\n";
		}
	$str.="</select>";
	return $str;
	}
	
function tol20($max,$a) {
	if ($a>$max) return $max;
	return $a;
}

}
