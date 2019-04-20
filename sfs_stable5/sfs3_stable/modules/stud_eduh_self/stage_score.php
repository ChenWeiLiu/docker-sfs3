<?php

// $Id:  $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_subjectscore.php";

sfs_check();

// 健保卡查核
switch ($ha_checkary){
	case 2:
		ha_check();
		break;
	case 1:
		if (!check_home_ip()){
				ha_check();
		}
		break;
}

//秀出網頁
head("學習領域階段成績");

//模組選單
print_menu($menu_p);

//檢查是否開放
if (!$stage_score){
   echo "模組變數尚未開放本功能，請洽詢學校系統管理者！";
   exit;
}

$student_sn=$_SESSION['session_tea_sn'];
//$stud_name=$_SESSION['session_tea_name'];


//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();
//目前選定學期
$seme_year_seme=sprintf('%03d%d',$curr_year,$curr_seme);

$_POST['year_seme']=$_POST['year_seme']?$_POST['year_seme']:$seme_year_seme;

$work_year=intval(substr($_POST['year_seme'],0,3));
$work_seme=substr($_POST['year_seme'],-1);

//抓取學生學期
$seme_select="◎就讀學期：<select name='year_seme' onchange=\"this.form.submit();\">";
$query="select * from stud_seme where student_sn=$student_sn order by seme_year_seme desc";
$res=$CONN->Execute($query) or die('SQL錯誤<br>'.$query);
while(!$res->EOF){
	$seme_class=$res->fields['seme_class'];
	$year_seme=$res->fields['seme_year_seme'];
	$seme_class_name=$res->fields['seme_class_name'];
	$seme_num=$res->fields['seme_num'];
	$seme_name=substr($year_seme,0,3).'學年第'.substr($year_seme,-1).'學期';
	$selected=($year_seme==$_POST['year_seme'])?'selected':'';
	$seme_select.="<option value='$year_seme' $selected>$seme_name ( $seme_class 班 $seme_num 號 )";

	$res->MoveNext();
}
$seme_select.="</select>";

//抓教師資料
$teacher_arr=array();

if($stage_teacher){
	$query="select teacher_sn,name from teacher_base";
	$res=$CONN->Execute($query) or die('SQL錯誤<br>'.$query);
	while(!$res->EOF){
		$teacher_arr[$res->fields[0]]=$res->fields[1];
		$res->MoveNext();
	}
}


//目標資料表
$target_semester='score_semester_'.$work_year.'_'.$work_seme;

//取得科目名稱陣列
$subject_arr=get_subject_name_arr();

//抓取資料
$score_arr=array();
$ss_id_array=array();
$query="select a.*,b.scope_id,b.subject_id,b.link_ss,b.print from $target_semester a inner join score_ss b on a.ss_id=b.ss_id where a.student_sn=$student_sn and a.sendmit=1 order by print desc,ss_id,test_sort";
$res=$CONN->Execute($query) or die('SQL錯誤<br>'.$query);
while(!$res->EOF){
	$test_kind=$res->fields['test_kind'];
	$test_sort=$res->fields['test_sort'];
	$ss_id=$res->fields['ss_id'];
	$scope_id=$res->fields['scope_id'];
	$subject_id=$res->fields['subject_id'];
	$subject_name=$subject_arr[$subject_id]['subject_name'];

	$scope_name=$subject_arr[$scope_id]['subject_name'];
	$subject_name=$subject_name?$subject_name:$scope_name;

	$score_arr[$test_sort][$ss_id][$test_kind]['teacher_sn']=$res->fields['teacher_sn'];
	$score_arr[$test_sort][$ss_id][$test_kind]['print']=$res->fields['print'];
	$score_arr[$test_sort][$ss_id][$test_kind]['score']=$res->fields['score'];
	$score_arr[$test_sort][$ss_id][$test_kind]['update_time']=$res->fields['update_time'];

	//抓取科目陣列
	$ss_id_array[$ss_id]['subject_name']=$subject_name;
	$ss_id_array[$ss_id]['link_ss']=$res->fields['link_ss'];

	$res->MoveNext();
}

//階段別
$stage_arr=array(1=>'第一階段',2=>'第二階段',3=>'第三階段',4=>'第四階段',5=>'第五階段',255=>'不分階段');
$color_array=array(1=>'#111111',2=>'#222222',3=>'#333333',4=>'#444444',5=>'#555555',6=>'#ffcccc',7=>'#ccffcc',8=>'#ccccff',9=>'#ffcccc',10=>'#ffdddd');
$stage_key=array_keys($score_arr);

$data="<tr align='center' bgcolor='#ddffdd'><td rowspan=2>學習領域</td><td rowspan=2>科目名稱</td>";
foreach($stage_key as $key){
	if($key==255) $data.="<td rowspan=2>{$stage_arr[$key]}</td>";
	else {
		$data.="<td colspan=2>{$stage_arr[$key]}</td>";
		$sub_title.="<td>定期評量</td><td>平時成績</td>";
	}
}
$data.="</tr>";
$data.="<tr align='center' bgcolor='#ddffdd'>$sub_title</tr>";
/*
echo '<pre>';
print_r($score_arr);
echo '</pre>';
*/
foreach($ss_id_array as $ss_id=>$ss_data){
	$data.="<tr align='center'><td bgcolor='#ffffcc'>{$ss_data['link_ss']}</td><td bgcolor='#ffffcc'>{$ss_data['subject_name']}</td>";
	foreach($stage_key as $key){
		if($key==255){
			$score_all=$score_arr[$key][$ss_id]['全學期']['score'];
			$bgcolor=($score_all<60)?'#ffdddd':'#ffffff';
			$teacher_sn=$score_arr[$key][$ss_id]['全學期']['teacher_sn'];
			$teacher_name=$stage_teacher?"<br><font size=1 color='brown'>".$teacher_arr[$teacher_sn]."</font>":'';
			$data.="<td bgcolor='$bgcolor'>$score_all$teacher_name</td>";
		} else {
			$score_1=$score_arr[$key][$ss_id]['定期評量']['score'];
				$teacher_sn=$score_arr[$key][$ss_id]['定期評量']['teacher_sn'];
				$teacher_name1=$stage_teacher?"<br><font size=1 color='brown'>".$teacher_arr[$teacher_sn]."</font>":'';
				$bgcolor_1=($score_1<60)?'#ffdddd':'#ffffff';
			$score_2=$score_arr[$key][$ss_id]['平時成績']['score'];
				$teacher_sn=$score_arr[$key][$ss_id]['平時成績']['teacher_sn'];
				$teacher_name2=$stage_teacher?"<br><font size=1 color='brown'>".$teacher_arr[$teacher_sn]."</font>":'';
				$bgcolor_2=($score_2<60)?'#ffdddd':'#ffffff';
			$data.="<td bgcolor='$bgcolor_1'>$score_1$teacher_name1</td><td bgcolor='$bgcolor_2'>$score_2$teacher_name2</td>";
		}
	}
	$data.="</tr>";
}


$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>$seme_select<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>$data</table></form>";

echo $main;

foot();

?>
