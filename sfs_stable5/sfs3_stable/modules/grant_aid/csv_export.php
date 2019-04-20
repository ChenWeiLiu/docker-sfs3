<?php
// $Id: csv_export.php 7707 2013-10-23 12:13:23Z smallduh $

include "config.php";
sfs_check();

//學校ID
$school_id=$SCHOOL_BASE["sch_id"];

//今天的日期
$today=(date("Y")-1911).date("年m月d日");

//學期別
$work_year_seme= ($_POST[work_year_seme])?$_POST[work_year_seme]:$_GET[work_year_seme];
if($work_year_seme=='')        $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$work_year=substr($work_year_seme,0,3)+0;

//縣市
$area=substr($school_long_name,0,6)."政府";

// 取出班級陣列
$class_base = class_base($work_year_seme);
$class_teacher=get_class_teacher();

//取得學年學期陣列
$year_seme_arr = get_class_seme();

//取得學生基本資料
$sql_select="select a.student_sn,left(a.class_num,length(a.class_num)-2) as class_id,b.stud_id,b.stud_name,b.stud_person_id,a.dollar from grant_aid a,stud_base b where a.year_seme='$work_year_seme' and a.type='$type' and a.student_sn=b.student_sn order by class_num";
$res=$CONN->Execute($sql_select) or user_error("身分別紀錄讀取失敗！<br>$sql_select",256);
$student_arr=array();
while(!$res->EOF) {
	$student_sn=$res->fields['student_sn'];
	
	$student_arr[$student_sn]['class_id']=$res->fields['class_id'];
	$student_arr[$student_sn]['stud_id']=$res->fields['stud_id'];
	$student_arr[$student_sn]['stud_name']=$res->fields['stud_name'];
	$student_arr[$student_sn]['stud_person_id']=$res->fields['stud_person_id'];
	$student_arr[$student_sn]['dollar']=$res->fields['dollar'];
	$res->MoveNext();
}

//加入類別屬性資料
$sql_select="select student_sn,clan,area from stud_subkind where type_id='".$target_id[$type]."'";
$res=$CONN->Execute($sql_select) or user_error("身分屬性讀取失敗！<br>$sql_select",256);
while(!$res->EOF) {
	$student_sn=$res->fields['student_sn'];
	if(array_key_exists($student_sn,$student_arr)){	
		$student_arr[$student_sn]['clan']=$res->fields['clan'];
		$student_arr[$student_sn]['area']=$res->fields['area'];
	}
	$res->MoveNext();
}

//echo '<PRE>';
//print_r($student_arr);
//echo '</PRE>';

$student_arr_len=count($student_arr);

foreach($student_arr as $key=>$value){
	$class_id=$class_base[$value['class_id']];
	$stud_name=$value['stud_name'];
	$stud_id=$value['stud_id'];
	$stud_person_id=$value['stud_person_id'];
	$clan=$value['clan'];
	$area=$value['area'];
	$dollar=$value['dollar'];

	$num_count=$num_count+1;
	$total=$total+$dollar;
	$row_data=str_replace("\n","","$num_count,$class_id,$stud_id,$stud_name,$stud_birthday,$stud_person_id,$clan,$area,$dollar");
	$row_data=str_replace("\r","","$num_count,$class_id,$stud_id,$stud_name,$stud_birthday,$stud_person_id,$clan,$area,$dollar");
	$data.="$row_data\n";
}

################################    輸出 CSV    ##################################
$filename = $school_id.$school_short_name.$work_year."學年度[$type]($today).csv";
$Str="編號,班級,學號,姓名,出生年月日,身分證字號,$clan_title,$area_title,金額(元)\n";
$Str.=$data;

header("Content-disposition: attachment; filename=$filename");
header("Content-type: text/x-csv");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

header("Expires: 0");
echo $Str;

?>