<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

sfs_check();

//秀出網頁
head("均衡學習");
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

if($_POST['act']=='統計本年度所有開列學生均衡學習的級分'){
	//抓本年度所有開列學生的student_sn
	$sn_array=get_student_list($work_year);

	//均衡學習
	$score_balance_array=count_student_score_balance($sn_array);	

	//更新級分
	foreach($sn_array as $key=>$student_sn){
		$sql="update 12basic_ylc set score_balance_health='{$score_balance_array[$student_sn]['health']}',score_balance_art='{$score_balance_array[$student_sn]['art']}',score_balance_complex='{$score_balance_array[$student_sn]['complex']}'	where academic_year=$work_year AND student_sn=$student_sn";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	}
};

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

if($work_year==$academic_year) $tool_icon.=" <input type='submit' value='統計本年度所有開列學生均衡學習的級分' name='act' onclick='return confirm(\"確定要重新統計本年度所有開列學生均衡學習的級分?\")'>";

$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon <table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//取得指定學年已經開列的學生清單
	$listed=get_student_list($work_year);
	
	//取得指定學年已經開列的學生多元學習的分數
	$diversification_array=get_student_balance($work_year);
	
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td width=50>健體</td><td width=50>藝文</td><td width=50>綜合</td><td>級分統計</td><td>備註</td>";
	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id)=$recordSet->FetchRow()) {
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#EEFFEE":"#FFEEEE";
		$score_balance_health=$diversification_array[$student_sn]['score_balance_health'];
		$score_balance_art=$diversification_array[$student_sn]['score_balance_art'];
		$score_balance_complex=$diversification_array[$student_sn]['score_balance_complex'];
		//$score_association=$diversification_array[$student_sn]['score_association'];		
		//$score_service=$diversification_array[$student_sn]['score_service'];
		//$score_fault=$diversification_array[$student_sn]['score_fault'];
		//$score_reward=$diversification_array[$student_sn]['score_reward'];
		$score=$diversification_array[$student_sn]['score'];
		$memo=$diversification_array['balance_memo'];
		
		$stud_sex_color=array_key_exists($student_sn,$listed)?$stud_sex_color:'#aaaaaa';

		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$score_balance_health</td><td>$score_balance_art</td><td>$score_balance_complex</td><td><B>$score</B></td><td>$memo<br>$action</td></tr>";
	}
}

echo $main.$studentdata."</form></table>";
foot();
?>