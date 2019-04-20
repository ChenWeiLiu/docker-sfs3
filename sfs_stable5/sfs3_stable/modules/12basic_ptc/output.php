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
	//$domicile_data=get_domicile_data($work_year);
	
	//取得畢業資格
	$graduate_data=get_graduate_data($work_year);

	//取得12basic_ptc紀錄資料
	$final_data=get_final_data($work_year);
	
	if($_POST['act']=='EXCEL'){
		require_once "../../include/sfs_case_excel.php";
		$x=new sfs_xls();
		$x->setUTF8();
		$x->filename=$SCHOOL_BASE['sch_id'].'_'.$school_long_name.'_屏東區招生系統報名資料檔.xls';
		$x->setBorderStyle(1);
		$x->addSheet('Student');
		$x->items[0]=array('姓名','性別（男或女）','班級','身分證','畢業資格','經濟弱勢','均衡學習','服務表現','品德表現','競賽表現','體適能','適性發展','國文','英文','數學','社會','自然','志願卡序號');
	} else $main="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>
	<tr bgcolor='#ffcccc'><td>姓名</td><td>性別（男或女）</td><td>班級</td><td>身分證</td><td>畢業資格</td><td>經濟弱勢</td><td>均衡學習</td><td>服務表現</td><td>品德表現</td><td>競賽表現</td>
	<td>體適能</td><td>適性發展</td><td>國文</td><td>英文</td><td>數學</td><td>社會</td><td>自然</td><td>志願卡序號</td>";
	
	//取得指定學年已經開列的學生清單
	$sql_select="SELECT a.student_sn,b.seme_class,b.seme_class_name,b.seme_num FROM 12basic_ptc a INNER JOIN stud_seme b ON a.student_sn=b.student_sn WHERE b.seme_year_seme='$work_year_seme' ORDER BY seme_class,seme_num";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$seme_class=$recordSet->fields['seme_class'];
		$seme_class_name=$recordSet->fields['seme_class_name'];
		$seme_num=sprintf('%02d',$recordSet->fields['seme_num']);

		$birth_year=sprintf('%02d',$student_data[$student_sn]['birth_year']);
		$birth_month=sprintf('%02d',$student_data[$student_sn]['birth_month']);
		$birth_day=sprintf('%02d',$student_data[$student_sn]['birth_day']);
		$stud_name=str_replace(' ','',$student_data[$student_sn]['stud_name']);
		$stud_person_id=$student_data[$student_sn]['stud_person_id'];
		$stud_sex=$student_data[$student_sn]['stud_sex']==1?'男':'女';
		//畢修業積分
		$graduate=$graduate_score[$graduate_data[$student_sn]];
		
		//$stud_tel_2=$student_data[$student_sn]['stud_tel_2']?$student_data[$student_sn]['stud_tel_2']:$student_data[$student_sn]['stud_tel_1'];
		//$addr_zip=$student_data[$student_sn]['addr_zip'];
		//$stud_addr_2=$student_data[$student_sn]['stud_addr_2']?$student_data[$student_sn]['stud_addr_2']:$student_data[$student_sn]['stud_addr_1'];
		
		//$guardian_name=$domicile_data[$student_sn]['guardian_name'];
		//$guardian_phone=$domicile_data[$student_sn]['guardian_phone'];
		//$guardian_hand_phone=$domicile_data[$student_sn]['guardian_hand_phone'];
		
		//$stud_tel_2=$guardian_phone?$guardian_phone:$stud_tel_2;
		//$stud_addr_2=$domicile_data[$student_sn]['guardian_address']?$domicile_data[$student_sn]['guardian_address']:$stud_addr_2;
		
		//if(!strpos($guardian_hand_phone,'-')) $guardian_hand_phone=$guardian_hand_phone?substr_replace($guardian_hand_phone,'-',4,0):'';
		
		//依據模組設定進行個資遮罩
		if(!$full_personal_profile){
			$birth_day='00';
			$stud_name=substr($stud_name,0,-2).'○';
			//$stud_person_id=substr($stud_person_id,0,-4).'0000';
			//$stud_tel_2=substr($stud_tel_2,0,-3).'888';
			//$stud_addr_2=substr($stud_addr_2,0,18).'○○○○○';
			//$guardian_name=substr($guardian_name,0,-2).'○';
			//$guardian_hand_phone=$guardian_hand_phone?substr($guardian_hand_phone,0,-3).'777':'';
		}
		
		//計算12basic_ptc紀錄資料
		//$kind_id=$final_data[$student_sn]['kind_id'];	
		//$free_id=$final_data[$student_sn]['free_id'];
		
		$score_disadvantage=$final_data[$student_sn]['score_disadvantage'];
		$score_balance=$final_data[$student_sn]['score_balance_health']+$final_data[$student_sn]['score_balance_art']+$final_data[$student_sn]['score_balance_complex'];	
		$score_service=$final_data[$student_sn]['score_service'];
		$score_fault=$final_data[$student_sn]['score_fault'];
		$score_competetion=$final_data[$student_sn]['score_competetion'];	 
		$score_fitness=$final_data[$student_sn]['score_fitness'];
		$score_personality=$final_data[$student_sn]['score_my_aspiration']+$final_data[$student_sn]['score_domicile_suggestion']+$final_data[$student_sn]['score_guidance_suggestion'];
		$chinese=$final_data[$student_sn]['score_exam_c'];
		$english=$final_data[$student_sn]['score_exam_e'];
		$math=$final_data[$student_sn]['score_exam_m'];
		$social=$final_data[$student_sn]['score_exam_s'];
		$nature=$final_data[$student_sn]['score_exam_n'];
		
		$card_no=$final_data[$student_sn]['card_no'];
		
		//'姓名','性別（男或女）','班級','身分證','畢業資格','經濟弱勢','均衡學習','服務表現','品德表現','競賽表現','體適能','適性發展','國文','英文','數學','社會','自然','志願卡序號'
		
		if($_POST['act']=='EXCEL') $x->items[]=array($stud_name,$stud_sex,$seme_class,$stud_person_id,$graduate,$score_disadvantage,$score_balance,$score_service,$score_fault,$score_competetion,$score_fitness,$score_personality,$chinese,$english,$math,$social,$nature,$card_no);
		else $main.="<tr align='center'><td>$stud_name</td><td>$stud_sex</td><td>$seme_class</td><td>$stud_person_id</td><td>$graduate</td><td>$score_disadvantage</td><td>$score_balance</td><td>$score_service</td><td>$score_fault</td><td>$score_competetion</td>
		<td>$score_fitness</td><td>$score_personality</td><td>$chinese</td><td>$english</td><td>$math</td><td>$social</td><td>$nature</td><td>$card_no</td></tr>";
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
$sql="SELECT DISTINCT academic_year FROM 12basic_ptc ORDER BY academic_year";
$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
$radio_year_seme="";
while(!$rs->EOF)
{
	$academic_year=$rs->fields['academic_year'];
	$checked=($work_year==$academic_year)?'checked':'';
	$radio_year_seme.="<input type='radio' name='edit_remote' value=$academic_year $checked>$academic_year ";
	$rs->MoveNext();
}

$data="<br><br>※要輸出的格式：<input type='submit' name='act' value='HTML' onclick=\"document.myform.target='$academic_year'\"> <input type='submit' name='act' value='EXCEL' onclick=\"document.myform.target=''\">";

if($full_sealed_check) {
	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);
	if($editable_sn_array) $data="<font size=5 color='red'><br><br><center>有學生資料尚未封存！<br>模組變數設定您必須先封存所有資料才可以進行輸出。</center></font>";
}

echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><br>※要輸出的學年：$radio_year_seme	<br><br>$data</form>";

foot();
?>