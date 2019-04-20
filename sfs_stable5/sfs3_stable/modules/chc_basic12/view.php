<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
// ini_set('display_errors', '1');
include "config.php";
//認證
sfs_check();
chk_login('學務處,訓導處,教務處,教導處,輔導室,輔導處,輔導組,校長室');

//引入換頁物件(學務系統用法)
// include_once "../../include/sfs_oo_dropmenu.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/view.htm";

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
	//身分類別
	var $Okind=array('0'=>'一般生','1'=>'原住民','2'=>'派外人員子女','3'=>'蒙藏生','4'=>'回國僑生','5'=>'港澳生','6'=>'退伍軍人','7'=>'境外優秀科學技術人才子女');
	var $Okind2=array('0'=>'0.一般生','1'=>'1.原住民','2'=>'2.派外人員子女','3'=>'3.蒙藏生','4'=>'4.回國僑生','5'=>'5.港澳生','6'=>'6.退伍軍人','7'=>'7.境外優秀科學技術人才子女');
	//障礙類別
	var $xOspecial=array('0'=>'一般生','1'=>'智能障礙','2'=>'視覺障礙','3'=>'聽覺障礙','4'=>'語言障礙','5'=>'肢體障礙','6'=>'身體病弱','7'=>'境情緒行為障礙',	'8'=>'學習障礙','9'=>'多重障礙','A'=>'自閉症','B'=>'其他障礙'	);
	var $xOspecial2=array('0'=>'0.一般生','1'=>'1.智能障礙','2'=>'2.視覺障礙','3'=>'3.聽覺障礙','4'=>'4.語言障礙','5'=>'5.肢體障礙','6'=>'6.身體病弱','7'=>'7.境情緒行為障礙','8'=>'8.學習障礙','9'=>'9.多重障礙','A'=>'A.自閉症','B'=>'B.其他障礙'	);
	//障礙類別 -- fix
	var $Ospecial=array('0'=>'一般生','1'=>'智能障礙','2'=>'視覺障礙','3'=>'聽覺障礙',
	'4'=>'語言障礙','5'=>'肢體障礙','6'=>'腦性麻痺','7'=>'身體病弱','8'=>'情緒行為障礙',
	'9'=>'學習障礙','A'=>'多重障礙','B'=>'自閉症','C'=>'發展遲緩','D'=>'其他障礙');
	var $Ospecial2=array('0'=>'0.一般生','1'=>'1.智能障礙','2'=>'2.視覺障礙','3'=>'3.聽覺障礙',
	'4'=>'4.語言障礙','5'=>'5.肢體障礙','6'=>'6.腦性麻痺','7'=>'7.身體病弱','8'=>'8.情緒行為障礙',
	'9'=>'9.學習障礙','A'=>'A.多重障礙','B'=>'B.自閉症','C'=>'C.發展遲緩','D'=>'D.其他障礙');
	
	var $YESNO=array('0'=>'否','1'=>'是');
	var $YESNO2=array('0'=>'0.否','1'=>'1.是');
	var $Ograde=array('0'=>'肄業','1'=>'畢業');
	var $Ograde2=array('0'=>'0.肄業','1'=>'1.畢業');
	var $Oincome=array('0'=>'無','1'=>'中低收入戶','2'=>'低收入戶');
	var $Oincome2=array('0'=>'0.無','1'=>'1.中低收入','2'=>'2.低收入');
	//建構函式
	function chc_seme($CONN,$smarty){
		global $IS_JHORES;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->IS_JHORES=$IS_JHORES;
		$YS=''; 
		if (isset($_POST['year_seme'])) $YS=$_POST['year_seme'];
		if ($YS=='' && isset($_GET['year_seme'])) $YS=$_GET['year_seme'];
		if ($YS=='') $YS=curr_year()."_".curr_seme();
		$this->year_seme=$YS;
		$aa=split("_",$this->year_seme);
		$this->year=$aa[0];
		$this->seme=$aa[1];
	}
	//初始化
	function init() {	}
	//程序
	function process() {
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
		$SQL="select 	a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond  from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
		$SQL="select a.stud_id,a.stud_name,a.stud_birthday ,a.stud_sex, a.stud_person_id ,b.student_sn ,
		b.seme_class,b.seme_num,a.stud_study_cond ,c.sn,  c.academic_year,  c.kind_id,  c.special,
  		c.unemployed,  c.graduation,  c.income,  c.score_nearby,  c.score_service,  c.score_reward,
  		c.score_fault,  c.score_balance,  c.score_club,  c.score_race,  c.score_physical,  c.score_exam,  c.update_sn
		from stud_base a,stud_seme b left join  chc_basic12 c on c.student_sn=b.student_sn  
		where a.student_sn=b.student_sn 	and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' 
		 order by b.seme_num ";
		//echo $SQL;
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		//$obj_stu=array();
		$All=$rs->GetArray();
		foreach ( $All as $ary ) {
			$SN=$ary['student_sn'];
			$obj_stu[$SN] = $ary;
			unset($ary);
		}
		//echo "<pre>";print_r($obj_stu);
		return $obj_stu;	
	}

	//更新資料
	function update(){

 
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
	$str="<select name='".$this->YS."' onChange=\"location.href='".$_SERVER[SCRIPT_NAME]."?".$this->YS."='+this.options[this.selectedIndex].value;\">\n";
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
	($this->IS_JHORES==6) ? $grade=array(7=>"一年",8=>"二年",9=>"三年"):$grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年");
	$gradeA=($this->IS_JHORES==6) ? 9:6;
	//只選高年級6/9
	$SQL="select class_id,c_year,c_name,teacher_1 from  school_class 
	where year='".$this->year."' and semester='".$this->seme."' and enable='1' 
	  order by class_id desc ";
	
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
