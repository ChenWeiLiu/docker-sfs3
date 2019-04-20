<?php

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

if($_POST['act']=='取消') { $edit_sn=0;	$_POST['batch']=''; }

if($_POST['act']=='確定修改'){
	$sql="UPDATE 12basic_ptc SET score_my_aspiration='{$_POST['edit_score_my_aspiration']}',score_domicile_suggestion='{$_POST['edit_score_domicile_suggestion']}',score_guidance_suggestion='{$_POST['edit_score_guidance_suggestion']}',personality_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$edit_sn=0;
}

if($_POST['act']=='批次更新'){
	foreach($_POST['batch'] as $student_sn=>$data) {
		//決定高限
		$score_my_aspiration=min($data['score_my_aspiration'],$my_aspiration);
		$score_domicile_suggestion=min($data['score_domicile_suggestion'],$domicile_suggestion);
		$score_guidance_suggestion=min($data['score_guidance_suggestion'],$guidance_suggestion);
		
		$sql="update 12basic_ptc set score_my_aspiration='$score_my_aspiration',score_domicile_suggestion='$score_domicile_suggestion',score_guidance_suggestion='$score_guidance_suggestion',personality_memo='{$data['memo']}'
			where academic_year=$work_year AND student_sn=$student_sn AND editable='1'";
		$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);
	}
	$edit_sn=0;
	$_POST['batch']='';
}

if($_POST['act']=='依據生涯輔導紀錄填報資料判定'){
	//先全部清空
	$sql="update 12basic_ptc set score_my_aspiration=0,score_domicile_suggestion=0,score_guidance_suggestion=0 where academic_year=$work_year AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	//再依據填報資料判定
	$sn_array=get_student_list($work_year);
	$personality=get_student_direction($sn_array);
	foreach($personality as $student_sn=>$value) {
		$self=$value[self];
        $parent=$value[parent];
        $teacher=$value[teacher];
		
		$score_domicile_suggestion=($self==$parent)?$domicile_suggestion:0;
		$score_guidance_suggestion=($self==$teacher)?$guidance_suggestion:0;

		$sql="update 12basic_ptc set score_my_aspiration=$my_aspiration,score_domicile_suggestion=$score_domicile_suggestion,score_guidance_suggestion=$score_guidance_suggestion where student_sn=$student_sn and academic_year=$work_year AND editable='1'";
		$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);
	}
};


if($_POST['act']=='設定參與免試學生皆符合適性發展'){
	$sql="update 12basic_ptc set score_my_aspiration=$my_aspiration,score_domicile_suggestion=$domicile_suggestion,score_guidance_suggestion=$guidance_suggestion where academic_year=$work_year AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
};

if($_POST['act']=='全部清空'){
	$sql="update 12basic_ptc set score_my_aspiration=0,score_domicile_suggestion=0,score_guidance_suggestion=0 where academic_year=$work_year AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
};

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

if($work_year==$academic_year) $tool_icon=" <input type='submit' value='依據生涯輔導紀錄填報資料判定' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'>
											<input type='submit' value='設定參與免試學生皆符合適性發展' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'>
											<input type='submit' value='全部清空' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";
$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='batch' value=''><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//取得指定學年已經開列的學生清單
	$listed=get_student_list($work_year);
	
	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);
	
	//取得指定學年已經開列的學生扶助弱勢分數
	$personality_array=get_student_personality($work_year);	
//echo "<pre>";	
//print_r($personality_array);
//echo "</pre>";	
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//假使是當年度便可以批次編修
	if($work_year==$academic_year and !$_POST['batch']) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ff8888';\" ondblclick='document.myform.batch.value=\"1\"; document.myform.submit();'";
	$studentdata="<tr align='center' bgcolor='#ff8888' $java_script><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td width=$pic_width>大頭照</td><td>我的志願</td><td>家長建議</td><td>輔導小組建議</td><td>積分統計</td><td>備註</td>";
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$seme_num=$recordSet->fields['seme_num'];
		$stud_name=$recordSet->fields['stud_name'];
		$stud_sex=$recordSet->fields['stud_sex'];
		$stud_id=$recordSet->fields['stud_id'];
		$stud_study_year=$recordSet->fields['stud_study_year'];

		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		$score_my_aspiration=$personality_array[$student_sn]['score_my_aspiration'];
			$bgcolor_my_aspiration=$score_my_aspiration?$stud_sex_color:'#cccccc';
		$score_domicile_suggestion=$personality_array[$student_sn]['score_domicile_suggestion'];
			$bgcolor_domicile_suggestion=$score_domicile_suggestion?$stud_sex_color:'#cccccc';
		$score_guidance_suggestion=$personality_array[$student_sn]['score_guidance_suggestion'];
			$bgcolor_guidance_suggestion=$score_guidance_suggestion?$stud_sex_color:'#cccccc';
		$score=$score_my_aspiration+$score_domicile_suggestion+$score_guidance_suggestion;
			$bgcolor_score=$score?$stud_sex_color:'#cccccc';
		$memo=$personality_array[$student_sn]['personality_memo'];
		$java_script="";
		$action='';
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';

		//批次編修
		if($_POST['batch']){
			if($editable_sn_array[$student_sn] and $listed[$student_sn]){
				
				$score_my_aspiration="<input type='checkbox' name='batch[$student_sn][score_my_aspiration]' value='$my_aspiration'".($score_my_aspiration?' checked':'').">符合";
				$score_domicile_suggestion="<input type='checkbox' name='batch[$student_sn][score_domicile_suggestion]' value='$domicile_suggestion'".($score_domicile_suggestion?' checked':'').">符合";
				$score_guidance_suggestion="<input type='checkbox' name='batch[$student_sn][score_guidance_suggestion]' value='$guidance_suggestion'".($score_guidance_suggestion?' checked':'').">符合";

				//產生備註欄
				$memo="<input type='text' size=20 name='batch[$student_sn][memo]' value='$memo'";
			}			
		} else {
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
				$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的適性發展 積分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
			} else {
				if(array_key_exists($student_sn,$listed)){
					$editable=array_key_exists($student_sn,$editable_sn_array)?1:0;
					$stud_sex_color=$editable?$stud_sex_color:$uneditable_bgcolor;
					$java_script=($work_year==$academic_year and $editable and $personality_editable)?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'":'';
				} else { $stud_sex_color='#aaaaaa'; }
			}
		}
		$stud_sex_color=$listed[$student_sn]?$stud_sex_color:'#cccccc';		
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$my_pic</td><td bgcolor='$bgcolor_my_aspiration'>$score_my_aspiration</td><td bgcolor='$bgcolor_domicile_suggestion'>$score_domicile_suggestion</td><td bgcolor='$bgcolor_guidance_suggestion'>$score_guidance_suggestion</td><td bgcolor='$bgcolor_score'><B>$score</B></td><td>$memo $action</td></tr>";
		$recordSet->MoveNext();
	}
	if($_POST['batch']) $studentdata.="<tr align='center'><td colspan=10><input type='submit' name='act' value='批次更新' onclick='return confirm(\"確定要修改本班學生所有的適性發展積分?\")'> <input type='submit' name='act' value='取消'></td></tr>";
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."</table></form>";
foot();
?>