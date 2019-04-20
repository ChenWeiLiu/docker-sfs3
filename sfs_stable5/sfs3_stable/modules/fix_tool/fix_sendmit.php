<?php
//$Id: fix_sendmit.php 5310 2009-01-10 07:57:56Z hami $
require_once("config.php");

//使用者認證
sfs_check();
head("開鎖器");
print_menu($school_menu_p);
$obj=new sendmit();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->IS_JHORES=&$IS_JHORES;
$obj->process();
/*
  sendmit='0'上鎖
  sendmit='1'開鎖
*/
class sendmit{
   var $CONN;//ADO
   var $smarty;
   var $IS_JHORES;//國中小判斷參數
   var $year;//年
   var $seme;//學期
   var $Y_name='year_seme';//下拉式選單學期的奱數名稱
   var $S_name='class_id';//下拉式選單班級的奱數名稱
   var $YS_ary;//學期陣列
   var $YC_ary;//年班陣列
   var $year_seme;//下拉式選單學期的奱數值95_1
   var $Sel_class;//下拉式選單班級的奱數值095_1_04_02
   var $open=array(
"1s"=>"第1階段段考","2s"=>"第2階段段考","3s"=>"第3階段段考","a_s"=>"全部段考",
"1n"=>"第1階段平時","2n"=>"第2階段平時","3n"=>"第3階段平時","a_n"=>"全部平時",
"255"=>"不分階段","all"=>"本班全部","school"=>"本年級全部","subj"=>"本科全部");
   var $open_readme=array(
"1s"=>"僅打開或關上本班本科目第1階段段考的成績","2s"=>"僅打開或關上本班本科目第2階段段考的成績","3s"=>"僅打開或關上本班本科目第3階段段考的成績","a_s"=>"僅打開或關上本班本科目1-3次的段考的成績",
"1n"=>"僅打開或關上本班本科目第1階段平時","2n"=>"僅打開或關上本班本科目第2階段平時","3n"=>"僅打開或關上本班本科目第3階段平時","a_n"=>"僅打開或關上本班本科目1-3次全部平時的成績",
"255"=>"僅打開或關上本班本科目不分階段","all"=>"打開或關上本班全部(含段考平時或不分階段)的成績","school"=>"打開或關上本年級全部(含段考平時或不分階段)的成績","subj"=>"凡資料庫有本科目的成績都打開或關上");
	

function process() {
//if ($_POST){echo "<pre>";print_r($_POST);die();}
	$this->init();
	if ($_POST[form_act]=='updata') $this->sendmit_open();
	$this->YS_ary=$this->sel_year();//學期陣列
	$this->YC_ary=$this->grade();//年級班級陣列
	if ($this->Sel_class!='') $this->sub=$this->get_subj($this->Sel_class);
	$this->display();
}

function init() {
	($_GET[$this->Y_name]=='') ? $this->year_seme=$_POST[$this->Y_name]:$this->year_seme=$_GET[$this->Y_name];
	if ($this->year_seme=='') $this->year_seme=curr_year()."_".curr_seme();
	($_GET[$this->S_name]=='') ? $this->Sel_class=$_POST[$this->S_name]:$this->Sel_class=$_GET[$this->S_name];
	
   $tmp=split("_",$this->year_seme);
   $this->year=$tmp[0];
   $this->seme=$tmp[1]; 

}
function display(){
	if ($this->tpl=='') $this->tpl=dirname(__file__)."/templates/fix_sendmit.htm";
		$this->smarty->assign("this",$this);
		$this->smarty->display($this->tpl);
}

function sendmit_open() {
	//echo "<pre>";print_r($_POST);die();
	if ($_POST[year_seme]=='') return;
	if ($_POST[class_id]=='') {return;}else {$class_id=$_POST[class_id];}
	if ($_POST[sendmit]=='') {return;}else {$sendmit=$_POST[sendmit];}
	
	// 095_1_02_01
	if ($_POST[subj]=='') {return;}else {$subj=$_POST[subj];}
	if ($_POST[open][$subj]=='') {return;} else{$key=$_POST[open][$subj];}
	
	$tmp=split('_',$class_id);
	$tmp1=$tmp[0]."_".$tmp[1]."_".$tmp[2];//全年級
	
	$TB="score_semester_".$_POST[year_seme];
	$SQL1="update $TB set sendmit='$sendmit' where  ss_id='{$subj}' ";	
	$SQL["1s"]=" and test_sort='1' and  test_kind='定期評量' and class_id='$class_id' ";
	$SQL["2s"]=" and test_sort='2' and  test_kind='定期評量' and class_id='$class_id' ";
	$SQL["3s"]=" and test_sort='3' and  test_kind='定期評量' and class_id='$class_id' ";
	$SQL["a_s"]=" and test_kind='定期評量' and class_id='$class_id' ";	
	$SQL["1n"]=" and test_sort='1' and  test_kind='平時成績' and class_id='$class_id' ";
	$SQL["2n"]=" and test_sort='2' and  test_kind='平時成績' and class_id='$class_id' ";
	$SQL["3n"]=" and test_sort='3' and  test_kind='平時成績' and class_id='$class_id' ";
	$SQL["a_n"]=" and  test_kind='平時成績' and class_id='$class_id' ";
	$SQL["255"]=" and  test_kind='全學期' and class_id='$class_id' ";
	$SQL["all"]=" and class_id='$class_id' ";
	$SQL["school"]=" and class_id like '$tmp1%' ";
	$SQL["subj"]=" ";
	
	$SQL=$SQL1.$SQL[$key];
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$URL=$_SERVER[PHP_SELF]."?".$this->Y_name."=".$_POST[year_seme]."&".$this->S_name."=".$class_id;
		Header("Location:$URL");
}




function sel_year() {
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
	$ro = $rs->FetchNextObject(false);
	// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
	$tmp_y=$ro->year."_".$ro->seme;
	$tmp[$tmp_y]=$ro->year."學年度第".$ro->seme."學期";
	}
	return $tmp;
	}

function grade() {
    //名稱,起始值,結束值,選擇值
    ($this->IS_JHORES==6) ? $grade=array(7=>"一年",8=>"二年",9=>"三年"):$grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年");
    $SQL="select class_id,c_year,c_name,teacher_1 from  school_class where year='".$this->year."' and semester='".$this->seme."' and enable=1  order by class_id  ";
    $rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
    if ($rs->RecordCount()==0) return"尚未設定班級資料！";
    $All=$rs->GetArray();

    foreach($All as $ary) {
    	$tmp[$ary[class_id]]=$grade[$ary[c_year]].$ary[c_name]."班 (".$ary[teacher_1].")";
		}
    return $tmp;
} 


function get_subj($class_id,$type='') {
	if ($_GET[mods]=='') return;
	switch ($type) {
		case 'all':
		$add_sql=" ";break;
		case 'seme':
		$add_sql=" and need_exam='1' ";break;//有成績的
		case 'stage':
		$add_sql=" and need_exam='1'  and print='1' ";break;//有段考,完整
		case 'no_test':
		$add_sql=" and need_exam='1'  and print!='1' ";break; //不用段考的
		default:
		$add_sql=" ";break;
	} 
//	$add_sql.=" and enable='1'  ";
	$CID=split("_",$class_id);//093_1_01_01
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$CID[2];
	$class=$CID[3];
	$CID_1=$year."_".$seme."_".$grade."_".$class;
	$SQL1="select * from score_ss where class_id='' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."' $add_sql order by enable , scope_id  , sort,sub_sort ";
	
	$SQL2="select * from score_ss where class_id='$CID_1' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."'   $add_sql order by enable , scope_id ,sort,sub_sort ";
	
	//echo $SQL;
	($_GET[mods]=='year') ? $SQL=$SQL1:$SQL=$SQL2;
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return;

	$All_ss=$rs->GetArray();
	//print_r($All_ss);
	/*取科目名稱*/
	$SQL="select * from score_subject ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$All_subj=$rs->GetArray();
   foreach($All_subj as $ary) {
    	$subj_name[$ary[subject_id]]=$ary[subject_name];
		}
	
	$obj_SS=array();
	for($i=0;$i<count($All_ss);$i++){
		$key=$All_ss[$i][ss_id];//索引
		$obj_SS[$key]=$All_ss[$i];//全部陣列,暫不用
		//$obj_SS[$key][rate]=$All_ss[$i][rate];//加權
		$obj_SS[$key][scope]=$subj_name[$All_ss[$i][scope_id]];//領域名稱
		$obj_SS[$key][subject]=$subj_name[$All_ss[$i][subject_id]];//科目名稱
		//($obj_SS[$key][sb]=='') ? $obj_SS[$key][sb]=$obj_SS[$key][sc]:"";
	}
	//die("無法查詢，語法:".$SQL);
	return $obj_SS;
}



}

foot();
// echo "<pre>";
// print_r($obj->sub);

?>