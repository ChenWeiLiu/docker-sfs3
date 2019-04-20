<?php

// $Id: config.php 7035 2012-12-06 01:15:12Z smallduh $

//系統設定檔
include_once "../../include/config.php";

//函式庫
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_studclass.php";
require_once "./module-cfg.php";
include_once "my_fun.php";
include "module-upgrade.php";


// 學生自建資料
$m_arr = get_sfs_module_set("stud_eduh_self");
extract($m_arr, EXTR_OVERWRITE);

//取得模組設定
$m_arr = get_sfs_module_set("12basic_career");
extract($m_arr, EXTR_OVERWRITE);

//過濾POST值
foreach($_POST as $k=>$v) {
	if (!is_array($v)) {
		//為了要解決單引號取代後處生的問題
		$v=str_replace("'", "@$@", $v);
		//過濾--
		$_POST[$k]=str_replace(array("\\@$@","@$@","--"),array("","",""),$v);
	}
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();
$curr_year_seme=sprintf('%03d%d',$curr_year,$curr_seme);
$class_array=class_base();


$c_id=$_REQUEST['class_id'];
$student_sn=$_REQUEST['student_sn'];
$seme_year_seme=$curr_year_seme;

$min=$IS_JHORES?7:4;
$max=$IS_JHORES?9:6;

$linkstr = "class_id=$c_id&student_sn=$student_sn";


//抓取輔導班級產生選單
$class_select="<select name='class_id' onchange='this.form.target=\"$class_id\"; this.form.submit()'><option value='' selected>-請選擇班級-</option>";
$query="select * from `score_eduh_teacher2` where year_seme='$curr_year_seme' and teacher_sn=".$_SESSION['session_tea_sn'];
$res=$CONN->Execute($query) or die("SQL錯誤：<br>$query");
while(!$res->EOF){
	$data=explode('_',$res->fields['class_id']);
	$class_id=sprintf('%d%02d',$data[2],$data[3]);
	$selected=($c_id==$class_id)?'selected':'';
	$class_select.="<option value='$class_id' $selected>{$class_array[$class_id]}</option>";
	$res->MoveNext();
}
$class_select.="</select>";

if($c_id){
	//產生學生名單
	$query="select a.student_sn,a.seme_num,b.stud_name,b.stud_sex from `stud_seme` a inner join stud_base b on a.student_sn=b.student_sn where seme_year_seme='$curr_year_seme' and seme_class='{$c_id}' order by a.seme_num";
	$res=$CONN->Execute($query) or die("SQL錯誤：<br>$query");
	$size=$res->RecordCount()+1;
	while(!$res->EOF){
		$checked=($student_sn==$res->fields['student_sn'])?'checked':'';
		$color=($res->fields['stud_sex']==1)?'#0000ff':'#ff0000';
		$color=($student_sn==$res->fields['student_sn'])?'#00ff00':$color;
		$student_select.="<input type='radio' name='student_sn' onclick='this.form.submit()' value='{$res->fields['student_sn']}' $checked><font color='$color'>({$res->fields['seme_num']}) {$res->fields['stud_name']}</font><br>";
		$res->MoveNext();
	}
	$student_select.="</select>";
}

$level_array=array(1=>'國際',2=>'全國、臺灣區',3=>'區域性（跨縣市）',4=>'省、直轄市',5=>'縣市區（鄉鎮）',6=>'校內');
$squad_array=array(1=>'個人賽',2=>'團體賽');

?>
