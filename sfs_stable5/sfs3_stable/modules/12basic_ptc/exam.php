<?php
include "config.php";

sfs_check();

//秀出網頁
head("參與免試學生");

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
	$_POST['edit_score_exam_c']=min($_POST['edit_score_exam_c'],$exam_score_well);
	$_POST['edit_score_exam_m']=min($_POST['edit_score_exam_m'],$exam_score_well);
	$_POST['edit_score_exam_e']=min($_POST['edit_score_exam_e'],$exam_score_well);
	$_POST['edit_score_exam_s']=min($_POST['edit_score_exam_s'],$exam_score_well);
	$_POST['edit_score_exam_n']=min($_POST['edit_score_exam_n'],$exam_score_well);
	
	$sql="UPDATE 12basic_ptc SET score_exam_c='{$_POST['edit_score_exam_c']}',score_exam_m='{$_POST['edit_score_exam_m']}',score_exam_e='{$_POST['edit_score_exam_e']}',score_exam_s='{$_POST['edit_score_exam_s']}',score_exam_n='{$_POST['edit_score_exam_n']}',exam_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$edit_sn=0;
}

if($_POST['act']=='批次更新'){
	foreach($_POST['batch'] as $student_sn=>$data) {
		//決定高限
		$score_exam_c=min($data['score_exam_c'],$exam_score_well);
		$score_exam_m=min($data['score_exam_m'],$exam_score_well);
		$score_exam_e=min($data['score_exam_e'],$exam_score_well);
		$score_exam_s=min($data['score_exam_s'],$exam_score_well);
		$score_exam_n=min($data['score_exam_n'],$exam_score_well);
		$sql="update 12basic_ptc set score_exam_c='$score_exam_c',score_exam_m='$score_exam_m',score_exam_e='$score_exam_e',score_exam_s='$score_exam_s',score_exam_n='$score_exam_n',exam_memo='{$data['memo']}'
		where academic_year=$work_year AND student_sn=$student_sn AND editable='1'";
		$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);
	}
	$edit_sn=0;
	$_POST['batch']='';
}

if($_POST['act']=='清除所有教育會考成績'){
	$sql="UPDATE 12basic_ptc SET score_exam_c=NULL,score_exam_m=NULL,score_exam_e=NULL,score_exam_s=NULL,score_exam_n=NULL,exam_memo=NULL WHERE academic_year=$work_year AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("清除失敗！<br>$sql",256);
	$edit_sn=0;
}


//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

if($work_year==$academic_year) $tool_icon=" <input type='submit' value='清除所有教育會考成績' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";
//假使是當年度便可以批次編修
if($work_year==$academic_year and !$_POST['batch']) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ff8888';\" ondblclick='document.myform.batch.value=\"1\"; document.myform.submit();'";
$main.="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='batch' value=''><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";

if($stud_class)
{
	//取得前已開列學生資料
	$listed=get_student_list($work_year);
	
	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);
	
	//取得會考成績
	$exam_data=get_exam_data($work_year);
//echo "<pre>";
//print_r($exam_data);
//echo "</pre>";
	//if(!$_POST['edit_write'] and $work_year==$academic_year) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ff8888';\" ondblclick='document.myform.edit_write.value=1; document.myform.submit();'";
	//elseif($_POST['edit_write']) $ok="<input type='submit' name='act' value='確定修改'  onclick='return confirm(\"確定要修改寫作測驗積分?\")'>";
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);	
	$studentdata="<tr align='center' bgcolor='#ff8888' $java_script><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td width=$pic_width>大頭照</td><td>國文</td><td>數學</td><td>英語</td><td>社會</td><td>自然</td><td>積分統計</td><td>備註</td>";
	while(!$recordSet->EOF){
		$student_sn=$recordSet->fields['student_sn'];
		$seme_num=$recordSet->fields['seme_num'];
		$stud_name=$recordSet->fields['stud_name'];
		$stud_sex=$recordSet->fields['stud_sex'];
		$stud_id=$recordSet->fields['stud_id'];
		$stud_study_year=$recordSet->fields['stud_study_year'];
		
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		$stud_sex_color=array_key_exists($student_sn,$listed)?$stud_sex_color:'#aaaaaa';
		
		
		$score_exam_c=$exam_data[$student_sn]['score_exam_c'];
		$score_exam_m=$exam_data[$student_sn]['score_exam_m'];
		$score_exam_e=$exam_data[$student_sn]['score_exam_e'];
		$score_exam_s=$exam_data[$student_sn]['score_exam_s'];
		$score_exam_n=$exam_data[$student_sn]['score_exam_n'];		
		$memo=$exam_data[$student_sn]['exam_memo'];
		//積分統計
		$score=$score_exam_c+$score_exam_m+$score_exam_e+$score_exam_s+$score_exam_n;
		
		$java_script="";
		$action='';
		
		//批次編修
		if($_POST['batch']){
			if($editable_sn_array[$student_sn] and $listed[$student_sn]){
				$score_exam_c="<input type='text' name='batch[$student_sn][score_exam_c]' value='$score_exam_c' size=5>";
				$score_exam_m="<input type='text' name='batch[$student_sn][score_exam_m]' value='$score_exam_m' size=5>";
				$score_exam_e="<input type='text' name='batch[$student_sn][score_exam_e]' value='$score_exam_e' size=5>";
				$score_exam_s="<input type='text' name='batch[$student_sn][score_exam_s]' value='$score_exam_s' size=5>";
				$score_exam_n="<input type='text' name='batch[$student_sn][score_exam_n]' value='$score_exam_n' size=5>";

				//產生備註欄
				$memo="<input type='text' size=20 name='batch[$student_sn][memo]' value='$memo'";
			}			
		} else {		
			if($student_sn==$edit_sn){			
				//教育會考備註
				$score_exam_c="<input type='text' name='edit_score_exam_c' size=5 value='$score_exam_c'>";
				$score_exam_m="<input type='text' name='edit_score_exam_m' size=5 value='$score_exam_m'>";
				$score_exam_e="<input type='text' name='edit_score_exam_e' size=5 value='$score_exam_e'>";
				$score_exam_s="<input type='text' name='edit_score_exam_s' size=5 value='$score_exam_s'>";
				$score_exam_n="<input type='text' name='edit_score_exam_n' size=5 value='$score_exam_n'>";
				$memo="<input type='text' name='edit_memo' size=20 value='$memo'>";
				$stud_sex_color='#ffffaa';
				$score='';
				//動作按鈕
				$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的教育會考積分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
			} else {
				if(array_key_exists($student_sn,$listed)){
					$editable=array_key_exists($student_sn,$editable_sn_array)?1:0;
					$stud_sex_color=$editable?$stud_sex_color:$uneditable_bgcolor;
					$java_script=($work_year==$academic_year and $editable)?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'":'';
				} else { $stud_sex_color='#aaaaaa'; }
			}		
		}
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$my_pic</td><td>$score_exam_c</td><td>$score_exam_m</td>
		<td>$score_exam_e</td><td>$score_exam_s</td><td>$score_exam_n</td><td>$score</td><td>$memo $action</td></tr>";
		
		$recordSet->MoveNext();
	}
	if($_POST['batch']) $studentdata.="<tr align='center'><td colspan=11><input type='submit' name='act' value='批次更新' onclick='return confirm(\"確定要修改本班學生所有的教育會考積分?\")'> <input type='submit' name='act' value='取消'></td></tr>";
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."<input type='hidden' name='edit_write' value=0></form></table>";
foot();
?>