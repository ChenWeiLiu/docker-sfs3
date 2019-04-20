<?php
//$Id: chi_fun.php 8061 2014-06-10 14:21:11Z chiming $
########-      get_subj  取得該班所有SS_ID科目名稱函式 --------#################
##   $type=all全部,seme有學期成績,stage須段考,no_test不須段考
##    計分need_exam  完整print  加權
###########################################################################
function get_subj2($class_id,$type='') {
global $CONN ;
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

	if ($class=="all"){
		$SQL="select * from score_ss where class_id='' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."' $add_sql order by enable , scope_id  , sort,sub_sort ";
		}else{
		$SQL="select * from score_ss where class_id='$CID_1' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."'   $add_sql order by enable , scope_id ,sort,sub_sort ";
		}
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$All_ss=$rs->GetArray();
	$subj_name=initArray("subject_id,subject_name","select * from score_subject ");
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

##################  基本工具 initArray轉化資料為索引與帶值函式 #######################
## 選取資料的欄A為索引,欄B為值,欄A須是唯一
## 使用時 傳入 $F1為字串==>subject_id,subject_name
## 使用時 傳入 $SQL為資料庫語法
##################  基本工具 initArray轉化資料為索引與帶值函式 #######################

function initArray($F1,$SQL){
	global $CONN ;
	$col=split(",",$F1);
	$key_field=$col[0];
	$value_field=$col[1];

	$rs = $CONN->Execute($SQL) or die($SQL);
	$sch_all = array();
	if (!$rs) {
		Return $CONN->ErrorMsg(); 
	} else {
		while (!$rs->EOF) {
		$sch_all[$rs->fields[$key_field]]=$rs->fields[$value_field]; 
		$rs->MoveNext(); // 移至下一筆記錄
		}
	}
	Return $sch_all;
}
##################取資料函式###########################
function get_data($SQL) {
	global $CONN ;
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr = $rs->GetArray();
	return $arr ;
}
##################  學期下拉式選單函式  ##########################
function sel_year($name,$select_t='') {
	global $CONN ;
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
	$ro = $rs->FetchNextObject(false);
	// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
	$year_seme=$ro->year."_".$ro->seme;
	$obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
	}
	$str="<select name='$name' onChange=\"location.href='".$_SERVER[PHP_SELF]."?".$name."='+this.options[this.selectedIndex].value;\">\n";
		//$str.="<option value=''>-未選擇-</option>\n";
	foreach($obj_stu as $key=>$val) {
		($key==$select_t) ? $bb=' selected':$bb='';
		$str.= "<option value='$key' $bb>$val</option>\n";
		}
	$str.="</select>";
	return $str;
	}


##################陣列列示函式2##########################
function sel_grade($name,$select_t='',$url='') {
	//名稱,起始值,結束值,選擇值
	global $IS_JHORES;
($IS_JHORES==6) ? $all_grade=array(7=>"一年級",8=>"二年級",9=>"三年級"):$all_grade=array(1=>"一年級",2=>"二年級",3=>"三年級",4=>"四年級",5=>"五年級",6=>"六年級");

$str="<select name='$name' onChange=\"location.href='".$url."'+this.options[this.selectedIndex].value;\">\n";
$str.= "<option value=''>-未選擇-</option>\n";
foreach($all_grade as $key=>$val) {
 ($key==$select_t) ? $bb=' selected':$bb='';
	$str.= "<option value='$key' $bb>$val</option>\n";
	}

$str.="</select>";
return $str;
 }
###########################################################
##  傳入年級,學年度,學期 預設值為all表示將傳出所有年級與班級
##  傳出以  class_id  為索引的陣列  
function get_class_info1($grade='all',$year_seme='') {
	global $CONN ;
if ($year_seme=='') {
	$curr_year=curr_year(); $curr_seme=curr_seme();}
else {
	$CID=split("_",$year_seme);//093_1
	$curr_year=$CID[0]; $curr_seme=$CID[1];}
	($grade=='all') ? $ADD_SQL='':$ADD_SQL=" and c_year='$grade'  ";
	$SQL="select class_id,c_name,teacher_1 from  school_class where year='$curr_year' and semester='$curr_seme' and enable=1  $ADD_SQL order by class_id  ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return"尚未設定班級資料！";
	$obj_stu=$rs->GetArray();
	($grade=='all') ? $Cg='NO':$Cg=sprintf("%02d",$grade);
	$all_y=sprintf("%03d",$curr_year)."_".$curr_seme."_".$Cg."_all";
	$all=array("0"=>array("class_id"=>$all_y,"c_name"=>"全年級","teacher_1"=>""));
	$obj=array_merge($obj_stu,$all);
	return $obj;
}

function get_subj3($name='scope') {
	global $CONN ;
	$SQL1="select subject_id,subject_name from score_subject WHERE subject_kind='scope' and  enable=1 order by subject_id ";
	$SQL2="select subject_id,subject_name from score_subject WHERE subject_kind='subject' and  enable=1 order by subject_id ";
	($name=='scope')? $SQL=$SQL1:$SQL=$SQL2;
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return "尚未設定任何科目資料！";
	$obj=$rs->GetArray();
	$obj2=array();
	if ($name=='subject') $obj2[0]="0：僅領域名";
	for($i=0;$i<count($obj);$i++){
		$key=$obj[$i][subject_id];//索引
		$obj2[$key]=$obj[$i][subject_id]."：".$obj[$i][subject_name];//全部陣列,暫不用
	}
	return $obj2;
}
#####################   CSS  ###########################
function myheader(){
?>
<style type="text/css">
body{background-color:#f9f9f9;font-size:12pt}
.f12{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;}
.ipmei{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;}
.ipme2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;color:red;font-family:標楷體 新細明體;}
.ip2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:11pt;color:red;font-family:新細明體 標楷體;}
.ip3{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;color:blue;font-family:新細明體 標楷體;}
.bu1{border-style: groove;border-width:1px: groove;background-color:#CCCCFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bub{background-color:#FFCCCC;font-size:14pt;}
.bur2{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.f8{font-size:9pt;color:blue;}
.f9{font-size:9 pt;}
.tth{ text-align: center; white-space: nowrap; background-color:#9EBCDD;}
.ttd{  white-space: nowrap; background-color:#FFFFFF;font-size:10pt }
A:link  {text-decoration:none;color:blue; }
A:visited {text-decoration:none;color:blue; }
A:hover {background-color:FF8000;color: #000000;  }
</style><?php
}
function backe($st="未填妥!按下後回上頁重填!") {
echo "<BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}


