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

$stud_class=$_REQUEST['stud_class'];
$selected_stud=$_POST['selected_stud'];

if($_POST['go']=='確定輸出'){
	$sn_list=implode(',',$_POST['selected_stud']);
	$stud_data=array();

	//抓取基本資料
	$sql_select="SELECT student_sn,stud_name,stud_person_id,curr_class_num FROM stud_base WHERE student_sn IN ($sn_list) ORDER BY curr_class_num";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$stud_data[$student_sn]['stud_person_id']=$recordSet->fields['stud_person_id'];
		//依據模組設定進行個資遮罩
		if(!$full_personal_profile){
			$stud_data[$student_sn]['stud_person_id']=substr($stud_data[$student_sn]['stud_person_id'],0,-4).'0000';
		}
		$stud_data[$student_sn]['stud_name']=$recordSet->fields['stud_name'];
		$stud_data[$student_sn]['class_id']=substr($recordSet->fields['curr_class_num'],0,3);
		
		$recordSet->MoveNext();
	}
	
	//抓取競賽紀錄
	$competetion_score=count_student_score_competetion($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($competetion_score);
	//echo '</pre>';
	
	$service_score=count_student_score_service($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($service_score);
	//echo '</pre>';
	
	$fault_score=count_student_score_fault($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($fault_score);
	//echo '</pre>';
	
	$fitness_score=count_student_score_fitness($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($fitness_score);
	//echo '</pre>';
	
	//計算多元學習上限
    foreach($_POST['selected_stud'] as $student_sn)
        $diversification_score[$student_sn]=min($diversification_score_max,$competetion_score[$student_sn]['score']+$service_score[$student_sn]['bonus']+$fault_score[$student_sn]['bonus']+$fitness_score[$student_sn]['bonus']);

	
	$particular_score=get_student_particular($work_year);
	//echo '<pre>';
	//print_r($particular_score);
	//echo '</pre>';
	
	$disadvantage_score=get_student_disadvantage($work_year);
	//echo '<pre>';
	//print_r($disadvantage_score);
	//echo '</pre>';
	
	//取得指定學年已經開列的學生多元學習的分數
	$balance_score_t=get_student_balance($work_year);
	//echo '<pre>';
	//print_r($balance_score_t);
	//echo '</pre>';
	
	$balance_area_score=get_student_score_balance($_POST['selected_stud']);
	//echo '<pre>';
	//print_r($balance_area_score);
	//echo '</pre>';

	//取得指定學年已經開列的適性輔導分數
	$personality_score=get_student_personality($work_year);	
	//echo '<pre>';
	//print_r($personality_score);
	//echo '</pre>';
	
	//取得指定學年已經開列的教育會考分數
	$exam_score=get_exam_data($work_year);
	//echo '<pre>';
	//print_r($exam_score);
	//echo '</pre>';
	
	//取得指定學年已經開列的其他項目
	$others_score=get_student_others($work_year);
	//echo '<pre>';
	//print_r($others_score);
	//echo '</pre>';

	$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
	$ny=$work_year+1;
	$smarty->assign("report_title","{$ny}學年度五專入學專用免試入學超額比序項目積分證明單");
	
	$smarty->assign("school_long_name",$school_long_name); 
	$smarty->assign("sch_id",$SCHOOL_BASE[sch_id]); 
	
	$smarty->assign("stud_data",$stud_data); 
	$smarty->assign("competetion_score",$competetion_score);
	$smarty->assign("level_array",$level_array);
	$smarty->assign("squad_array",$squad_array);

	$smarty->assign("service_score",$service_score);
	$smarty->assign("fault_score",$fault_score);
	$smarty->assign("fitness_score",$fitness_score);
	$smarty->assign("diversification_score",$diversification_score);
	
	$smarty->assign("particular_score",$particular_score);
	$smarty->assign("disadvantage_score",$disadvantage_score);
	$smarty->assign("balance_score_t",$balance_score_t);
	$smarty->assign("balance_area_score",$balance_area_score);
	$smarty->assign("personality_score",$personality_score);

	$smarty->assign("exam_level_description",$exam_level_description);
	$smarty->assign("exam_level",$exam_level);
	$smarty->assign("exam_score",$exam_score);

	$smarty->assign("others_array",$others_array);
	$smarty->assign("others_score",$others_score);
	
	$smarty->assign("data_color",$data_color);	
	$smarty->assign("header_bgcolor",$header_bgcolor);	

	$smarty->display("prove.tpl");
	exit;
}

//秀出網頁
head("資料證明單");

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);
//print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='selected_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

//取出班級名稱陣列
$class_base=class_base($work_year_seme);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

//取得指定學年已經開列的學生清單
$listed=get_student_list($work_year);

if($stud_class and $work_year_seme==$curr_year_seme){
	$tool_icon.="<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'> <input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'> 
		<input type='submit' name='go' value='確定輸出' onclick=\"this.form.target='$stud_class';\">";
}
$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]' target='$stud_class'><input type='hidden' name='student_sn' value=''>$recent_semester $class_list $tool_icon <table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>";

//檢查是否有可修改紀錄的參與免試學生
$editable_sn_array=get_editable_sn($work_year);

if($full_sealed_check and $editable_sn_array) {	
	if($editable_sn_array) $studentdata="<font size=5 color='red'><br><br><center>有學生資料尚未封存！<br>模組變數設定您必須先封存所有資料才可以進行輸出。</center></font>";
} elseif($stud_class){
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//以checkbox呈現
	$col=7; //設定每一列顯示幾人
	
	$studentdata="";
	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year)=$recordSet->FetchRow()) {
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		if($recordSet->currentrow() % $col==1) $studentdata.="<tr align='center'>";
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		if(array_key_exists($student_sn,$listed)) {
				$checkable=($curr_year_seme==$work_year_seme)?"<input type='checkbox' name='selected_stud[]' value='$student_sn'>":"";
				$studentdata.="<td bgcolor='$stud_sex_color'>$my_pic $checkable($seme_num)$stud_name</td>";				
		} else {
			$studentdata.="<td bgcolor='#cccccc'>$my_pic ($seme_num)$stud_name</td>";
		}
		if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
	}
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."</form></table>";
foot();
?>