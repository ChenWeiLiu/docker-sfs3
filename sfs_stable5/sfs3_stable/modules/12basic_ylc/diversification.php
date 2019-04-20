<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

sfs_check();

//秀出網頁
head("多元學習");
print_menu($menu_p);

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

$stud_class=$_REQUEST['stud_class'];
$selected_stud=$_POST['selected_stud'];
$edit_sn=$_POST['edit_sn'];

if($_POST['act']=='統計本年度所有開列學生多元學習的級分'){
	//抓本年度所有開列學生的student_sn
	$sn_array=get_student_list($work_year);

	//均衡學習-健體health,藝文art,綜合complex
	$score_balance=count_student_score_balance($sn_array);	
	//競賽成績
	$score_competetion=count_student_score_competetion();	
	//體適能
	$score_fitness=count_student_score_fitness($sn_array);
/*	
echo '<pre>';
print_r($score_fitness);
echo '</pre>';
*/	
	//更新級分
	foreach($sn_array as $key=>$student_sn){
		$score_competetion[$student_sn]=min($score_competetion[$student_sn],$race_score_max);
		$score_fitness[$student_sn]=min($score_fitness[$student_sn],$fitness_score_max);
		
		$sql="UPDATE 12basic_ylc set score_balance_health='{$score_balance[$student_sn]['health']}',score_balance_art='{$score_balance[$student_sn]['art']}',score_balance_complex='{$score_balance[$student_sn]['complex']}'
				,score_competetion='{$score_competetion[$student_sn]}',score_fitness='{$score_fitness[$student_sn]}'
			WHERE academic_year=$work_year AND student_sn=$student_sn AND editable='1'";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	}
};


if($_POST['act']=='確定修改'){
	$_POST['edit_health']=min($_POST['edit_health'],$balance_score);
	$_POST['edit_art']=min($_POST['edit_art'],$balance_score);
	$_POST['edit_complex']=min($_POST['edit_complex'],$balance_score);
	$_POST['edit_competetion']=min($_POST['edit_competetion'],$race_score_max);
	$_POST['edit_fitness']=min($_POST['edit_fitness'],$fitness_score_max);	
	$sql="UPDATE 12basic_ylc SET score_balance_health='{$_POST['edit_health']}',score_balance_art='{$_POST['edit_art']}',score_balance_complex='{$_POST['edit_complex']}',score_competetion='{$_POST['edit_competetion']}',score_fitness='{$_POST['edit_fitness']}',diversification_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn";
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

if($work_year==$academic_year) $tool_icon=" <input type='submit' value='統計本年度所有開列學生多元學習的級分' name='act' onclick='return confirm(\"確定要重新統計本年度所有開列學生多元學習的級分?\")'>";
if($diversification_editable) $tool_icon.=$editable_hint;

$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon <table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//取得指定學年已經開列的學生清單
	$listed=get_student_list($work_year);
	
	//取得指定學年已經開列的學生多元學習分數	
	$diversification_array=get_student_diversification($work_year);
/*
echo '<pre>';
print_r($diversification_array);
echo '</pre>';
*/
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80 rowspan=2>學號</td><td width=50 rowspan=2>座號</td><td width=120 rowspan=2>姓名</td><td colspan=3>均衡學習</td><td rowspan=2>競賽成績</td><td rowspan=2>體適能</td><td rowspan=2>級分統計</td><td rowspan=2>備註</td>";
	$studentdata.="<tr align='center' bgcolor='#ff8888'><td width=50>健體</td><td width=50>藝文</td><td width=50>綜合</td>";
	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year)=$recordSet->FetchRow()) {
		if($pic_checked) $my_pic=get_pic($stud_study_year,$stud_id);
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#EEFFEE":"#FFEEEE";

		$score_balance_health=$diversification_array[$student_sn]['score_balance_health'];
		$score_balance_art=$diversification_array[$student_sn]['score_balance_art'];
		$score_balance_complex=$diversification_array[$student_sn]['score_balance_complex'];
	
		$score_competetion=$diversification_array[$student_sn]['score_competetion'];
		$score_fitness=$diversification_array[$student_sn]['score_fitness'];

		$score=$diversification_array[$student_sn]['score'];
		$memo=$diversification_array['diversification_memo'];
	
		$java_script="";
		$action='';
		if($student_sn==$edit_sn){			
			$score_balance_health="<input type='text' name='edit_health' size=5 value='$score_balance_health'>";
			$score_balance_art="<input type='text' name='edit_art' size=5 value='$score_balance_art'>";
			$score_balance_complex="<input type='text' name='edit_complex' size=5 value='$score_balance_complex'>";

			$score_competetion="<input type='text' name='edit_competetion' size=5 value='$score_competetion'>";
			$score_fitness="<input type='text' name='edit_fitness' size=5 value='$score_fitness'>";
			
			//多元學習備註
			$memo="<input type='text' name='edit_memo' size=20 value='$memo'>";
			//動作按鈕
			$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的多元學習級分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
		} else {
			if(array_key_exists($student_sn,$listed)){
				if($work_year==$academic_year and $diversification_editable)  $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'";
			} else { $stud_sex_color='#aaaaaa'; }
		}		
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$my_pic $stud_name</td><td>$score_balance_health</td><td>$score_balance_art</td><td>$score_balance_complex</td><td>$score_competetion</td><td>$score_fitness</td><td><B>$score</B></td><td>$memo<br>$action</td></tr>";
	}
}

echo $main.$studentdata."</form></table>";
foot();
?>