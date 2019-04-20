<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("適性發展 ");
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
	$sql="UPDATE 12basic_ylc SET score_my_aspiration='{$_POST['edit_score_my_aspiration']}',score_domicile_suggestion='{$_POST['edit_score_domicile_suggestion']}',score_guidance_suggestion='{$_POST['edit_score_guidance_suggestion']}',disadvantage_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$edit_sn=0;
}
if($_POST['act']=='設定參與免試學生皆符合適性發展'){
	$sql="update 12basic_ylc set score_my_aspiration=$my_aspiration,score_domicile_suggestion=$domicile_suggestion,score_guidance_suggestion=$guidance_suggestion where academic_year=$work_year";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
};

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

if($work_year==$academic_year) $tool_icon=" <input type='submit' value='設定參與免試學生皆符合適性發展' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'> $editable_hint";
$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//取得指定學年已經開列的學生清單
	$listed=get_student_list($work_year);
	
	//取得指定學年已經開列的學生扶助弱勢分數
	$personality_array=get_student_personality($work_year);	
//echo "<pre>";	
//print_r($personality_array);
//echo "</pre>";	
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td>我的志願</td><td>家長建議</td><td>輔導小組建議</td><td>級分統計</td><td>備註</td>";
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$seme_num=$recordSet->fields['seme_num'];
		$stud_name=$recordSet->fields['stud_name'];
		$stud_sex=$recordSet->fields['stud_sex'];
		$stud_id=$recordSet->fields['stud_id'];

		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#EEFFEE":"#FFEEEE";
		$score_my_aspiration=$personality_array[$student_sn]['score_my_aspiration'];
		$score_domicile_suggestion=$personality_array[$student_sn]['score_domicile_suggestion'];
		$score_guidance_suggestion=$personality_array[$student_sn]['score_guidance_suggestion'];
		$score=$score_my_aspiration+$score_domicile_suggestion+$score_guidance_suggestion;
		$memo=$personality_array[$student_sn]['personality_memo'];
		$java_script="";
		$action='';

		if($student_sn==$edit_sn){			
			//選單
			$score_my_aspiration="<input type='checkbox' name='edit_score_my_aspiration' value='$my_aspiration'".($score_my_aspiration?' checked':'').">符合";
			$score_domicile_suggestion="<input type='checkbox' name='edit_score_domicile_suggestion' value='$domicile_suggestion'".($score_domicile_suggestion?' checked':'').">符合";
			$score_guidance_suggestion="<input type='checkbox' name='edit_score_guidance_suggestion' value='$guidance_suggestion'".($score_guidance_suggestion?' checked':'').">符合";
			$score='';
			$stud_sex_color='#ffffaa';
			//備註
			$memo="<input type='text' name='edit_memo' size=20 value='$memo'>";
			//動作按鈕
			$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的適性發展 級分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
		} else {
			if(array_key_exists($student_sn,$listed)){
				if($work_year==$academic_year) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'";
			} else { $stud_sex_color='#aaaaaa'; }
		}		
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$score_my_aspiration</td><td>$score_domicile_suggestion</td><td>$score_guidance_suggestion</td><td><B>$score</B></td><td>$memo $action</td></tr>";
		$recordSet->MoveNext();
	}
}

echo $main.$studentdata."</table></form>";
foot();
?>