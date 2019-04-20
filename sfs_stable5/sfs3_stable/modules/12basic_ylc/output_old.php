<?php

include "config.php";

sfs_check();

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];


if($_POST['act']){
	//取得指定學年已經開列的學生清單
	$student_list_array=get_student_list($work_year);

	//取得學生基本資料
	$student_data=get_student_data($work_year);

	//取得監護人資料
	$domicile_data=get_domicile_data($work_year);

	//取得12basic_ylc紀錄資料
	$final_data=get_final_data($work_year);
	
	if($_POST['act']=='EXCEL'){
		require_once "../../include/sfs_case_excel.php";
		$x=new sfs_xls();
		$x->setUTF8();
		$x->filename=$SCHOOL_BASE['sch_id'].'_'.$school_long_name.'_Student資料檔.xls';
		$x->setBorderStyle(1);
		$x->addSheet('Student');
		$x->items[0]=array('1.班級','2.座號','3.學生姓名','4.身分證號','5.性別','6.出生年','7.出生月','8.出生日','9.畢業學年度','10.學生身分','11.低收失業','12.監護人','13.電話','14.郵遞區號','15.通訊處','16.手機',
					'17.經濟弱勢','18.偏遠小校','19.就近入學','20.獎勵記錄','21.出缺席記錄','22.無記過記錄','23.均衡學習','24.競賽成績','25.體適能','26.國文分數','27.數學分數','28.英語分數','29.社會分數','30.自然分數');
	} else $main="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
	<tr bgcolor='#ffcccc'><td>1.班級</td><td>2.座號</td><td>3.學生姓名</td><td>4.身分證號</td><td>5.性別</td><td>6.出生年</td><td>7.出生月</td><td>8.出生日</td><td>9.畢業學年度</td><td>10.學生身分</td>
	<td>11.低收失業</td><td>12.監護人</td><td>13.電話</td><td>14.郵遞區號</td><td>15.通訊處</td><td>16.手機</td><td>17.經濟弱勢</td><td>18.偏遠小校</td><td>19.就近入學</td><td>20.獎勵記錄</td><td>21.出缺席記錄</td><td>22.無記過記錄</td>
	<td>23.均衡學習</td><td>24.競賽成績</td><td>25.體適能</td><td>26.國文分數</td><td>27.數學分數</td><td>28.英語分數</td><td>29.社會分數</td><td>30.自然分數</td>";
	
	//取得指定學年已經開列的學生清單
	$sql_select="SELECT a.student_sn,b.seme_class,b.seme_class_name,b.seme_num FROM 12basic_ylc a INNER JOIN stud_seme b ON a.student_sn=b.student_sn WHERE b.seme_year_seme='$work_year_seme' ORDER BY seme_class,seme_num";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$seme_class=substr($recordSet->fields['seme_class'],-2);
		$seme_class_name=$recordSet->fields['seme_class_name'];
		$seme_num=sprintf('%02d',$recordSet->fields['seme_num']);

		$birth_year=sprintf('%02d',$student_data[$student_sn]['birth_year']);
		$birth_month=sprintf('%02d',$student_data[$student_sn]['birth_month']);
		$birth_day=sprintf('%02d',$student_data[$student_sn]['birth_day']);
		$stud_name=str_replace(' ','',$student_data[$student_sn]['stud_name']);
		$stud_person_id=$student_data[$student_sn]['stud_person_id'];
		$stud_sex=$student_data[$student_sn]['stud_sex'];
		$stud_tel_2=$student_data[$student_sn]['stud_tel_2']?$student_data[$student_sn]['stud_tel_2']:$student_data[$student_sn]['stud_tel_1'];
		$stud_tel_3=$student_data[$student_sn]['stud_tel_3'];
		$addr_zip=$student_data[$student_sn]['addr_zip'];
		$stud_addr_2=$student_data[$student_sn]['stud_addr_2']?$student_data[$student_sn]['stud_addr_2']:$student_data[$student_sn]['stud_addr_1'];
		
		$guardian_name=$domicile_data[$student_sn]['guardian_name'];
		$guardian_phone=$domicile_data[$student_sn]['guardian_phone'];
		$guardian_hand_phone=$domicile_data[$student_sn]['guardian_hand_phone'];
		
		//$stud_tel_2=$guardian_phone?$guardian_phone:$stud_tel_2;
		//$stud_addr_2=$domicile_data[$student_sn]['guardian_address']?$domicile_data[$student_sn]['guardian_address']:$stud_addr_2;
		
		if(!strpos($guardian_hand_phone,'-')) $guardian_hand_phone=$guardian_hand_phone?substr_replace($guardian_hand_phone,'-',4,0):'';
		
		//個資遮罩
		if(!$full_personal_profile){
			$birth_day='00';
			$stud_name=substr($stud_name,0,-2).'○';
			//$stud_person_id=substr($stud_person_id,0,-4).'0000';
			$stud_tel_2=substr($stud_tel_2,0,-3).'888';
			$stud_addr_2=substr($stud_addr_2,0,18).'○○○○○';
			$guardian_name=substr($guardian_name,0,-2).'○';
			$guardian_hand_phone=$guardian_hand_phone?substr($guardian_hand_phone,0,-3).'777':'';
		}
		
		//計算12basic_ylc紀錄資料
		$kind_id=$final_data[$student_sn]['kind_id'];	
		$free_id=$final_data[$student_sn]['free_id'];
		//'17.經濟弱勢','18.偏遠小校','19.就近入學','20.獎勵記錄','21.出缺席記錄','22.無記過記錄','23.均衡學習','24.競賽成績','25.體適能','26.國文分數','27.數學分數','28.英語分數','29.社會分數','30.自然分數');		
		$score_disadvantage=$final_data[$student_sn]['score_disadvantage'];
		$score_remote=$final_data[$student_sn]['score_remote'];
		
		$score_reward=$final_data[$student_sn]['score_reward'];	 
		$score_absence=$final_data[$student_sn]['score_absence'];
		$score_fault=$final_data[$student_sn]['score_fault'];
		
		$score_balance=$final_data[$student_sn]['score_balance_health']+$final_data[$student_sn]['score_balance_art']+$final_data[$student_sn]['score_balance_complex'];	

		$score_competetion=$final_data[$student_sn]['score_competetion'];
		$score_fitness=$final_data[$student_sn]['score_fitness'];		
		
		$chinese=round($final_data[$student_sn]['score_exam_c']);
		$math=round($final_data[$student_sn]['score_exam_m']);
		$english=round($final_data[$student_sn]['score_exam_e']);
		$social=round($final_data[$student_sn]['score_exam_s']);
		$nature=round($final_data[$student_sn]['score_exam_n']); 		
		$write=$final_data[$student_sn]['score_exam_w'];
		
		if($_POST['act']=='EXCEL') $x->items[]=array($seme_class,$seme_num,$stud_name,$stud_person_id,$stud_sex,$birth_year,$birth_month,$birth_day,$work_year,$kind_id,$free_id,$guardian_name,$stud_tel_2,$addr_zip,$stud_addr_2,$stud_tel_3,$score_disadvantage,$score_remote,$school_nature,
			$score_reward,$score_absence,$score_fault,$score_balance,$score_competetion,$score_fitness,$chinese,$math,$english,$social,$nature);
		else $main.="<tr align='center'><td>$seme_class</td><td>$seme_num</td><td>$stud_name</td><td>$stud_person_id</td><td>$stud_sex</td><td>$birth_year</td><td>$birth_month</td><td>$birth_day</td><td>$work_year</td><td>$kind_id</td>
		<td>$free_id</td><td>$guardian_name</td><td>$stud_tel_2</td><td>$addr_zip</td><td>$stud_addr_2</td><td>$stud_tel_3</td><td>$score_disadvantage</td><td>$score_remote</td><td>$school_nature</td><td>$score_reward</td><td>$score_absence</td><td>$score_fault</td><td>$score_balance</td>
		<td>$score_competetion</td><td>$score_fitness</td><td>$chinese</td><td>$math</td><td>$english</td><td>$social</td><td>$nature</td></tr>";
		
		$recordSet->MoveNext();
	}
	
	if($_POST['act']=='EXCEL') {
		$x->writeSheet();
		$x->process();
	} else echo $main."</table>";
	exit;
}

//秀出網頁
head("資料匯出");
echo print_menu($MENU_P,$linkstr);
//取得有紀錄年度的學期的下拉選單
$sql="SELECT DISTINCT academic_year FROM 12basic_ylc ORDER BY academic_year";
$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
$radio_year_seme="";
while(!$rs->EOF)
{
	$academic_year=$rs->fields['academic_year'];
	$checked=($work_year==$academic_year)?'checked':'';
	$radio_year_seme.="<input type='radio' name='edit_remote' value=$academic_year $checked>$academic_year ";
	$rs->MoveNext();
}

echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><br>※要輸出的學年：$radio_year_seme <br><br>※要輸出的格式：<input type='submit' name='act' value='HTML' onclick=\"document.myform.target='$academic_year'\"><input type='submit' name='act' value='EXCEL' onclick=\"document.myform.target=''\"></form>";

foot();
?>