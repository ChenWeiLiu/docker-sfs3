<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("班級學生缺席紀錄");
print_menu($menu_p);

$teacher_sn=$_SESSION['session_tea_sn']; //取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);
$class_id=$class_name[0];
	
//學期別
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
if($class_id)
{
	$studentdata='';
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$sql="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex FROM stud_seme a LEFT JOIN stud_base b on a.student_sn=b.student_sn WHERE a.seme_class='$class_id' AND a.seme_year_seme='$curr_year_seme' AND b.stud_study_cond=0 ORDER BY a.seme_num";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);		
	//以radio呈現
	while(list($student_sn,$seme_num,$stud_name,$stud_sex)=$rs->FetchRow()) {
		$_POST['student_sn']=$_POST['student_sn']?$_POST['student_sn']:$student_sn;
		$sex_color=($stud_sex==1)?'#0000ff':'#ff0000';
		$checked=($student_sn==$_POST['student_sn'])?'checked':'';
		$seme_num=sprintf('%02d',$seme_num);
		$studentdata.="<input type='radio' name='student_sn' value='$student_sn' onclick=\"this.form.submit();\" $checked><font color='$sex_color'>($seme_num) $stud_name</font><br>";
	}
	$class_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1'><tr><td bgcolor='#ccffff' align='center'>◎{$class_name[1]}◎</td></tr><tr><td>$studentdata</td></tr></table>";
	
	$absent_data="<table border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111'><tr align='center' bgcolor='#ccccff'><td>NO.</td><td>學期別</td><td>班級代碼</td><td>缺席日期</td><td>類別</td><td>節次</td><td>登錄者</td><td>登錄時間</td></tr>";
	
	//找出 stud_id & stud_study_year
	$rs=$CONN->Execute("select stud_id,stud_study_year from stud_base where student_sn={$_POST['student_sn']}") or user_error("讀取失敗！<br>$sql",256);
	$stud_id=$rs->fields['stud_id'];
	$stud_study_year=$rs->fields['stud_study_year'];
	$max_year=$stud_study_year+9;
	
	//抓取指定學生的缺席紀錄
	$sql="SELECT * FROM stud_absent WHERE stud_id='$stud_id' AND (`year` between $stud_study_year AND '$max_year') ORDER BY `date`";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$absent_kind=$res->fields['absent_kind'];
		$absent_date=$res->fields['date'];
		$section=$res->fields['section'];
		$class_id=substr($res->fields['class_id'],-5);
		if($section=='uf') $section='升旗'; elseif($section=='df') $section='降旗'; elseif($section=='allday') $section='全日';
		$absent_year_seme=$res->fields['year'].'學年度第'.$res->fields['semester'].'學期';
		$recno++;
		$absent_data.="<tr align='center'><td>$recno</td><td>$absent_year_seme</td><td>$class_id</td><td>$absent_date</td><td>$absent_kind</td><td>$section</td><td>{$res->fields['sign_man_name']}</td><td>{$res->fields['sign_time']}</td></tr>";
		$res->MoveNext();
	}
	$absent_data.="</table>";
	
	$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><table><tr valign='top'><td>$class_list</td><td>$absent_data</td></tr></table></form>";
	echo $main; 
} else echo "您並非班級導師！";	

foot();

?>