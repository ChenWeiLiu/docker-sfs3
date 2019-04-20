<?php

// $Id: list_class_sum.php 5671 2009-09-25 06:18:03Z infodaes $

/* 取得基本設定檔 */
include "config.php";
include "../../include/sfs_case_dataarray.php";

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$year_seme=$_GET['year_seme'];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//截取教師姓名參照
$teacher_name=teacher_array();

//截取課表名稱
$sql= "select a.ss_id,a.link_ss,b.subject_name from score_ss a left join score_subject b on a.subject_id=b.subject_id where a.enable=1 and a.year=$sel_year and a.semester=$sel_seme";
$res=$CONN->Execute($sql) or trigger_error("錯誤訊息： $sql", E_USER_ERROR);
$subject_array=array();
while(!$res->EOF) {
	$ss_id=$res->fields['ss_id'];
	$subject_array[$ss_id]=$res->fields['subject_name']?$res->fields['subject_name']:$res->fields['link_ss'];
	$res->MoveNext();
}

$sql_select = "select max(sector),max(day) from score_course where year=$sel_year and semester=$sel_seme";
$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
$ms= $recordSet->fields[0];
$md= $recordSet->fields[1];

$sql_select = "select distinct class_id from score_course where year=$sel_year and semester=$sel_seme order by class_id ";
$rs=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
$class_data=array();
while(!$rs->EOF){
	$class_id=$rs->fields['class_id'];
	//轉換班級代碼
	$class_arr=class_id_2_old($class_id);	
	$class_no=$class_arr[2];
	$class_data[$class_id]['class_name']=$class_arr[5];
	
	//抓取班級導師
	$sql="select teacher_1,teacher_2 from school_class where year='$sel_year' and semester='$sel_seme' and class_id='$class_id' and enable='1'";
	$r=$CONN->Execute($sql) or trigger_error("錯誤訊息： $sql",E_USER_ERROR);
	$class_data[$class_id]['tutor']=$r->fields[0].' '.$r->fields[1];
	$class_data[$class_id]['tutor'].=($r->fields[1]?'、'.$r->fields[1]:'');

	//抓取課表資料
	$sql_course="select course_id,teacher_sn,cooperate_sn,day,sector,ss_id,room,c_kind from score_course where class_id='$class_id' order by day,sector";
	$recordSet=$CONN->Execute($sql_course) or trigger_error("錯誤訊息： $sql_course",E_USER_ERROR);
	while(list($course_id,$teacher_sn,$cooperate_sn,$day,$sector,$ss_id,$room,$c_kind)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$class_data[$class_id]['course'][$k]['ss_id']=$subject_array[$ss_id];
		$class_data[$class_id]['course'][$k]['teacher']=$teacher_sn;
		$class_data[$class_id]['course'][$k]['co_teacher']=$cooperate_sn;
		$class_data[$class_id]['course'][$k]['room']=$room;
		$class_data[$class_id]['course'][$k]['c_kind']=$c_kind;
	}
	$rs->MoveNext();
}

//產生抬頭
$csv_data="學年,學期,班級代號,班級名稱,導師,";
for($w=1;$w<=$md;$w++) {
	for($s=1;$s<=$ms;$s++){
		$k=$w."_".$s;
		$csv_data.="{$k}_科目,{$k}_教師,{$k}_教室,{$k}_兼課,";
	}
}
$csv_data=substr($csv_data,0,-1)."\r\n";

foreach($class_data as $class_id=>$data){	
	$row_data="$sel_year,$sel_seme,$class_id,{$class_data[$class_id]['class_name']},{$class_data[$class_id]['tutor']},";
	for($w=1;$w<=$md;$w++) {
		for($s=1;$s<=$ms;$s++) {
			$k=$w."_".$s;
			$teacher_sn=$class_data[$class_id]['course'][$k]['teacher'];
			$t=$teacher_name[$teacher_sn];
			
			$cooperate_sn=$class_data[$class_id]['course'][$k]['co_teacher'];
			if($cooperate_sn) $t.='、'.$teacher_name[$cooperate_sn];
			
			$row_data.="{$class_data[$class_id]['course'][$k]['ss_id']},$t,{$class_data[$class_id]['course'][$k]['room']},{$class_data[$class_id]['course'][$k]['c_kind']},";
		}
	}
	$row_data=substr($row_data,0,-1)."\r\n";
	$csv_data.=$row_data;
}

$filename=$sel_year."學年度第".$sel_seme."班級功課表套印CSV.csv";
header("Content-disposition: attachment;filename=$filename");
header("Content-type: text/x-csv ; Charset=Big5");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

header("Expires: 0");

echo $csv_data;
?>
