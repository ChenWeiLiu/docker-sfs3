<?php

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
$edit_sn=$_POST['edit_sn'];

if($_POST['act']=='取消') { $edit_sn=0;	$_POST['batch']=''; }

if($_POST['act']=='確定修改'){
	//避免直接輸入產生超限的值
	$_POST['edit_service']=min($_POST['edit_service'],$service_score_max);
	$_POST['edit_fault']=min($_POST['edit_fault'],$fault_score_max);
	$_POST['edit_competetion']=min($_POST['edit_competetion'],$race_score_max);
	$_POST['edit_fitness']=min($_POST['edit_fitness'],$fitness_score_max);
	$_POST['edit_fitness_assign']=min($_POST['edit_fitness_assign'],$fitness_score_max);
	
	$sql="UPDATE 12basic_ptc SET score_service='{$_POST['edit_service']}',score_fault='{$_POST['edit_fault']}',score_competetion='{$_POST['edit_competetion']}',score_fitness='{$_POST['edit_fitness']}',score_fitness_assign='{$_POST['edit_fitness_assign']}',diversification_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$edit_sn=0;
}

if($_POST['act']=='批次更新'){
	foreach($_POST['batch'] as $student_sn=>$data) {
		//決定高限
		$score_service=min($data['service'],$service_score_max);
		$score_fault=min($data['fault'],$fault_score_max);
		$score_competetion=min($data['competetion'],$race_score_max);
		$score_fitness=min($data['fitness'],$fitness_score_max);
		$score_fitness_assign=min($data['fitness_assign'],$fitness_score_max);
	
		$sql="update 12basic_ptc set score_service='$score_service',score_fault='$score_fault',score_competetion='$score_competetion',score_fitness='$score_fitness',score_fitness_assign='$score_fitness_assign',diversification_memo='{$data['memo']}'
			where academic_year=$work_year AND student_sn=$student_sn AND editable='1'";
		$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);
	}
	$edit_sn=0;
	$_POST['batch']='';
}


if($_POST['act']=='清空本年度所有開列學生的多元學習積分'){  //指定的體適能分數不清空
	$sql="UPDATE 12basic_ptc SET score_service=NULL,score_fault=NULL,score_competetion=NULL,score_fitness=NULL,diversification_memo=NULL WHERE academic_year=$work_year AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$edit_sn=0;
}

if($_POST['count']){
	$counted='完成！';
	//抓本年度所有開列學生的student_sn
	$sn_array=get_student_list($work_year);
	switch ($_POST['count']) {
		case '統計服務表現積分':
			$service_score=count_student_score_service($sn_array);
			//將原有服務表現成績清空
			$sql="UPDATE 12basic_ptc SET score_service=0 WHERE academic_year='$work_year' AND editable='1'";
			$res=$CONN->Execute($sql) or user_error("清空失敗！<br>$sql",256);
			//echo "<pre>";
			//print_r($service_score);
			//echo "</pre>";
			//重新寫入  屏東的服務學習包括 1.班級幹部 2.社團社長 3.特殊服務表現   中投區社團社團部分則記錄於association內  屏東統合在service
			foreach($service_score as $student_sn=>$data) {
				$service=min($service_score_max,$data['leader']+$data['association']);
				$sql="UPDATE 12basic_ptc SET score_service='$service' WHERE academic_year='$work_year' AND student_sn=$student_sn AND editable='1'";
				$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256); 	
			}
			echo "<script language=\"Javascript\">alert(\"{$_POST['count']} $counted\")</script>";
			break;
		case '統計品德表現積分':
			$fault_score=count_student_score_fault($sn_array);
			//將原有品德表現成績清空
			$sql="UPDATE 12basic_ptc SET score_fault=0 WHERE academic_year='$work_year' AND editable='1'";
			$res=$CONN->Execute($sql) or user_error("清空失敗！<br>$sql",256);
			//重新寫入
			foreach($fault_score as $student_sn=>$bonus) {
				$sql="UPDATE 12basic_ptc SET score_fault='$bonus' WHERE academic_year='$work_year' AND student_sn=$student_sn AND editable='1'";
				$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);			
			}
			echo "<script language=\"Javascript\">alert(\"{$_POST['count']} $counted\")</script>";
			break;
		case '統計競賽表現積分':
			$competetion_score=count_student_score_competetion($sn_array);
			//將原有競賽成績清空
			$sql="UPDATE 12basic_ptc SET score_competetion=0 WHERE academic_year='$work_year' AND editable='1'";
			$res=$CONN->Execute($sql) or user_error("清空失敗！<br>$sql",256);
			//重新寫入
			foreach($competetion_score as $student_sn=>$data) {
				$sql="UPDATE 12basic_ptc SET score_competetion='{$data['score']}' WHERE academic_year='$work_year' AND student_sn=$student_sn AND editable='1'";
				$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);			
			}
			echo "<script language=\"Javascript\">alert(\"{$_POST['count']} $counted\")</script>";
			break;
		case '統計體適能積分':
			$fitness_score=count_student_score_fitness($sn_array);
			//將原有體適能成績清空
			$sql="UPDATE 12basic_ptc SET score_fitness=0 WHERE academic_year='$work_year' AND editable='1'";
			$res=$CONN->Execute($sql) or user_error("清空失敗！<br>$sql",256);
			//重新寫入
			foreach($fitness_score as $student_sn=>$score) {
				$sql="UPDATE 12basic_ptc SET score_fitness='$score' WHERE academic_year='$work_year' AND student_sn=$student_sn AND editable='1'";
				$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);			
			}
			echo "<script language=\"Javascript\">alert(\"{$_POST['count']} $counted\")</script>";
			break;
	}
}


//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

if($work_year==$academic_year) $tool_icon.=" <input type='submit' name='count' value='統計服務表現積分'>
											 <input type='submit' name='count' value='統計品德表現積分'>
											 <input type='submit' name='count' value='統計競賽表現積分'>
											 <input type='submit' name='count' value='統計體適能積分'>
											 <input type='submit' name='act' value='清空本年度所有開列學生的多元學習積分' onclick='return confirm(\"確定要\"+this.value+\"?\")'>
											 <a href='./prove_service.php' target='prove'><img src='images/service.png' alt='服務表現證明單' title='服務表現證明單' height=20></a>
											 <a href='./prove_race.php' target='prove'><img src='images/race.png' alt='競賽紀錄清單' title='競賽紀錄清單' height=20></a>
											 ";
$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='batch' value=''><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";


if($stud_class)
{
	//取得指定學年已經開列的學生清單
	$listed=get_student_list($work_year);
	
	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);
	
	//取得指定學年已經開列的學生多元學習分數	
	$diversification_array=get_student_diversification($work_year);
//echo "<pre>";	
//print_r($diversification_array);	
//echo "</pre>";	
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//假使是當年度便可以批次編修
	if($work_year==$academic_year and !$_POST['batch']) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ff8888';\" ondblclick='document.myform.batch.value=\"1\"; document.myform.submit();'";
	$studentdata="<tr align='center' bgcolor='#ff8888' $java_script><td width=80 rowspan=2>學號</td><td width=50 rowspan=2>座號</td><td width=120 rowspan=2>姓名</td><td width=$pic_width rowspan=2>大頭照</td><td rowspan=2>服務表現<br>($service_score_max)</td><td rowspan=2>品德表現<br>($fault_score_max)</td><td rowspan=2>競賽表現<br>($race_score_max)</td><td colspan=2>體適能($fitness_score_max)</td><td rowspan=2>積分統計<br>($diversification_score_max)</td><td rowspan=2>備　　註</td></tr>
					<tr align='center' bgcolor='#ff8888'><td>計算</td><td>指定</td></tr>";

	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year)=$recordSet->FetchRow()) {
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		$service=$diversification_array[$student_sn]['score_service'];
			$bgcolor_service=$service?$stud_sex_color:'#cccccc';
		$fault=$diversification_array[$student_sn]['score_fault'];
			$bgcolor_fault=$fault?$stud_sex_color:'#cccccc';
		$competetion=$diversification_array[$student_sn]['score_competetion'];
			$bgcolor_competetion=$competetion?$stud_sex_color:'#cccccc';
		$fitness=$diversification_array[$student_sn]['score_fitness'];
			$bgcolor_fitness=$fitness?$stud_sex_color:'#cccccc';
		$fitness_assign=$diversification_array[$student_sn]['score_fitness_assign'];
			$bgcolor_fitness_assign=$fitness_assign?$stud_sex_color:'#cccccc';
		$score=$diversification_array[$student_sn]['score'];
			$bgcolor_score=$score?$stud_sex_color:'#cccccc';
		$memo=$diversification_array[$student_sn]['diversification_memo'];
		$java_script="";
		$action='';
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		//批次編修
		if($_POST['batch']){
			if($editable_sn_array[$student_sn] and $listed[$student_sn]){
				$service="<input type='text' name='batch[$student_sn][service]' size=5 value='$service'>";
				$fault="<input type='text' name='batch[$student_sn][fault]' size=5 value='$fault'>";
				$competetion="<input type='text' name='batch[$student_sn][competetion]' size=5 value='$competetion'>";
				$fitness="<input type='text' name='batch[$student_sn][fitness]' size=5 value='$fitness'>";
				$fitness_assign="<input type='text' name='batch[$student_sn][fitness_assign]' size=5 value='$fitness_assign'>";
				
				//產生備註欄
				$memo="<input type='text' size=20 name='batch[$student_sn][memo]' value='$memo'";
			}			
		} else {
			if($student_sn==$edit_sn){			
				//多元學習備註
				$service="<input type='text' name='edit_service' size=5 value='$service'>";
				$fault="<input type='text' name='edit_fault' size=5 value='$fault'>";
				$competetion="<input type='text' name='edit_competetion' size=5 value='$competetion'>";
				$fitness="<input type='text' name='edit_fitness' size=5 value='$fitness'>";
				$fitness_assign="<input type='text' name='edit_fitness_assign' size=5 value='$fitness_assign'>";
				$memo="<input type='text' name='edit_memo' size=20 value='$memo'>";
				$stud_sex_color='#ffffaa';
				//動作按鈕
				$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的多元學習積分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
			} else {
				if(array_key_exists($student_sn,$listed)){
					$editable=array_key_exists($student_sn,$editable_sn_array)?1:0;
					$stud_sex_color=$editable?$stud_sex_color:$uneditable_bgcolor;
					$java_script=($work_year==$academic_year and $editable and $diversification_editable)?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'":'';
				} else { $stud_sex_color='#aaaaaa'; }
			}
		}
		$stud_sex_color=$listed[$student_sn]?$stud_sex_color:'#cccccc';		
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$my_pic</td><td bgcolor='$bgcolor_service'>$service</td><td bgcolor='$bgcolor_fault'>$fault</td><td bgcolor='$bgcolor_competetion'>$competetion</td><td bgcolor='$bgcolor_fitness'>$fitness</td><td bgcolor='$bgcolor_fitness_assign'>$fitness_assign</td><td bgcolor='$bgcolor_score'><B>$score</B></td><td>$memo $action</td></tr>";
	}
	if($_POST['batch']) $studentdata.="<tr align='center'><td colspan=10><input type='submit' name='act' value='批次更新' onclick='return confirm(\"確定要修改本班學生所有的多元學習表現積分?\")'> <input type='submit' name='act' value='取消'></td></tr>";
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."</table></form>";
foot();
?>