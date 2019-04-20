<?php
include "config.php";

sfs_check();

//秀出網頁
head("畢業資格");

print_menu($menu_p);

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

$stud_class=$_REQUEST['stud_class'];

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

$graduate_arr=array(''=>"全部為修業",'1'=>"全部為畢業",'2'=>"依據畢業生升學資料模組的設定");
if($stud_class and $work_year_seme==$curr_year_seme){
	$tool_icon="<font size=2 color='green'>*畢業資格的判定(模組變數graduate_source的設定)：{$graduate_arr[$graduate_source]}*</font>";
}
$main.="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>$recent_semester $class_list $tool_icon<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width=60%>";

if($stud_class)
{
	//取得前已開列學生資料
	$student_list_array=get_student_list($work_year);
	
	//取得畢業資格
	$graduate_data=get_graduate_data($work_year);
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td width=$pic_width>大頭照</td><td>畢業資格</td><td>積分</td>";
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$seme_num=$recordSet->fields['seme_num'];
		$stud_name=$recordSet->fields['stud_name'];
		$stud_sex=$recordSet->fields['stud_sex'];
		$stud_id=$recordSet->fields['stud_id'];
		$stud_study_year=$recordSet->fields['stud_study_year'];
		
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		
		if($graduate_source<2) $graduate_data[$student_sn]=$graduate_source;
		$graduate_kind=$graduate_data[$student_sn]==1?"<img src='./images/on.png'>":"<img src='./images/off.png'>";
		$my_score=$graduate_score[$graduate_data[$student_sn]];
		
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		$stud_sex_color=array_key_exists($student_sn,$student_list_array)?$stud_sex_color:'#aaaaaa';
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color'><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$my_pic</td><td>$graduate_kind</td><td>$my_score</td></tr>";
		
		$recordSet->MoveNext();
	}	
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."<input type='hidden' name='edit_write' value=0></form></table>";
foot();
?>