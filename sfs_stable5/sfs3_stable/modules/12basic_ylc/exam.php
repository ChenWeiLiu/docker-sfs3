<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("教育會考");

print_menu($menu_p);

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

$stud_class=$_REQUEST['stud_class'];
$edit_sn=$_POST['edit_sn'];

if($_POST['act']=='確定修改'){
	$sql="UPDATE 12basic_ylc SET score_exam_c='{$_POST['edit_score_exam_c']}',score_exam_m='{$_POST['edit_score_exam_m']}',score_exam_e='{$_POST['edit_score_exam_e']}',score_exam_s='{$_POST['edit_score_exam_s']}',score_exam_n='{$_POST['edit_score_exam_n']}',exam_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$edit_sn=0;
}

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

$main.="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//取得前已開列學生資料
	$student_list_array=get_student_list($work_year);
	
	//取得會考成績
	$exam_data=get_exam_data($work_year);
//echo "<pre>";
//print_r($exam_data);
//echo "</pre>";
	if(!$_POST['edit_write'] and $work_year==$academic_year) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ff8888';\" ondblclick='document.myform.edit_write.value=1; document.myform.submit();'";
	elseif($_POST['edit_write']) $ok="<input type='submit' name='act' value='確定修改'  onclick='return confirm(\"確定要修改寫作測驗級分?\")'>";
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);	
	$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td>國文</td><td>數學</td><td>英語</td><td>社會</td><td>自然</td><td>級分統計</td><td>備註</td>";
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$seme_num=$recordSet->fields['seme_num'];
		$stud_name=$recordSet->fields['stud_name'];
		$stud_sex=$recordSet->fields['stud_sex'];
		$stud_id=$recordSet->fields['stud_id'];
		$stud_study_year=$recordSet->fields['stud_study_year'];
		
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#EEFFEE":"#FFEEEE";
		$stud_sex_color=array_key_exists($student_sn,$student_list_array)?$stud_sex_color:'#aaaaaa';
		
		
		$score_exam_c=$exam_data[$student_sn]['score_exam_c'];
		$score_exam_m=$exam_data[$student_sn]['score_exam_m'];
		$score_exam_e=$exam_data[$student_sn]['score_exam_e'];
		$score_exam_s=$exam_data[$student_sn]['score_exam_s'];
		$score_exam_n=$exam_data[$student_sn]['score_exam_n'];		
		$memo=$exam_data[$student_sn]['exam_memo'];
		//級分統計
		$score=$score_exam_c+$score_exam_m+$score_exam_e+$score_exam_s+$score_exam_n;
		
		$java_script="";
		$action='';
		if($student_sn==$edit_sn){			
			//教育會考備註
			$score_exam_c="<input type='text' name='edit_score_exam_c' size=5 value='$score_exam_c'>";
			$score_exam_m="<input type='text' name='edit_score_exam_m' size=5 value='$score_exam_m'>";
			$score_exam_e="<input type='text' name='edit_score_exam_e' size=5 value='$score_exam_e'>";
			$score_exam_s="<input type='text' name='edit_score_exam_s' size=5 value='$score_exam_s'>";
			$score_exam_n="<input type='text' name='edit_score_exam_n' size=5 value='$score_exam_n'>";
			$memo="<input type='text' name='edit_memo' size=20 value='$memo'>";
			$stud_sex_color='#ffffaa';
			$score='';
			//動作按鈕
			$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的教育會考級分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
		} else {
			if(array_key_exists($student_sn,$student_list_array)){
				if($work_year==$academic_year and $exam_editable) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'";
			} else { $stud_sex_color='#aaaaaa'; }
		}		
		if($pic_checked) $my_pic=get_pic($stud_study_year,$stud_id);
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$my_pic $stud_name</td><td>$score_exam_c</td><td>$score_exam_m</td>
		<td>$score_exam_e</td><td>$score_exam_s</td><td>$score_exam_n</td><td>$score</td><td>$memo $action</td></tr>";
		
		$recordSet->MoveNext();
	}
}
echo $main.$studentdata."<input type='hidden' name='edit_write' value=0></form></table>";
foot();
?>