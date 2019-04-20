<?php
//$Id: index.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

//秀出網頁布景標頭
head("學生成績查詢");

//主要內容
if(empty($sel_year))$sel_year=curr_year(); //目前學年
if(empty($sel_seme))$sel_seme=curr_seme(); //目前學期

//取得學生資料
$stud_data=stud_data($_SESSION[session_log_id]);

//取得本學期上課總日數
$c_year=substr($stud_data[curr_class_num],0,-4);
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$c_year,substr($stud_data[curr_class_num],-4,2));
$query="select days from seme_course_date where seme_year_seme='$seme_year_seme' and class_year='$c_year'";
$res=$CONN->Execute($query) or die($query);
$TOTAL_DAYS=$res->fields[0];

//取得考試樣板編號
$exam_setup=&get_all_setup("",$sel_year,$sel_seme,$c_year);
$interface_sn=$exam_setup[interface_sn];

$main=&main_form($interface_sn,$sel_year,$sel_seme,$class_id,$_SESSION[session_log_id]);

echo $main;


//佈景結尾
foot();

//觀看模板
function &main_form($interface_sn="",$sel_year="",$sel_seme="",$class_id="",$stud_id=""){
	global $CONN,$school_menu_p,$IS_JHORES;

	//轉換班級代碼
	$class=class_id_2_old($class_id);
	
	//求得學生ID	
	$student_sn=stud_id2student_sn($stud_id);

	//取得該學生日常生活表現評量值
	$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
	
	//取得學生日常生活表現分數及導師評語建議
	$nor_data=get_nor_value($student_sn,$sel_year,$sel_seme);

	//取得學生缺席情況
	$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme);
	
	if ($IS_JHORES!=0) {
		//取得學生獎懲情況
		$reward_data = get_reward_value($stud_id,$sel_year,$sel_seme);
	}

	//取得學生成績檔
	$score_data = &get_score_value($stud_id,$student_sn,$class_id,$oth_data);

	//取得詳細資料
	$html=&html2code2($class,$sel_year,$sel_seme,$oth_data,$nor_data,$abs_data,$reward_data,$score_data,$student_sn);
	
	//取得指定學生資料
	$stu=student_sn_to_id_name_num($student_sn,$sel_year,$sel_seme);

	//取得學校資料
	$s=get_school_base();
	$tool_bar=&make_menu($school_menu_p);

	$main="
	$tool_bar
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'><td bgcolor='#FFFFFF' valign='top'>
	<p align='center'>
	<font size=3>".$s[sch_cname]." ".$sel_year."學年度第".$sel_seme."學期成績單</p>
	<table align=center cellspacing=4>
	<tr>
	<td>班級：<font color='blue'>$class[5]</font></td><td width=40></td>
	<td>座號：<font color='green'>".sprintf("%02d",$stu[2])."</font></td><td width=40></td>
	<td>姓名：<font color='red'>$stu[1]</font></td>
	</tr></table></font>
	$html
	</td></tr></table>
	";

	return $main;
}
?>
