<?php

include "config.php";

sfs_check();

//秀出網頁
head("弱勢身分");
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

$show_zero=$_POST['show_zero']?'checked':'';
/*
if($_POST['act']=='確定修改'){
	$sql="UPDATE 12basic_tech SET score_remote='{$_POST['edit_remote']}',score_disadvantage='{$_POST['edit_disadvantage']}',disadvantage_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$edit_sn=0;
}
*/

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

$tool_icon="<input type='checkbox' name='show_zero' value=1 $show_zero onclick=\"this.form.submit();\"><font size=2 color='green'>顯示「(0)一般生」</font>";
$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1'>";


if($stud_class)
{
	//取得指定學年已經開列的學生清單
	$listed=get_student_list($work_year);
	
	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);
	
	//取得指定學年已經開列的學生扶助弱勢分數	
	$disadvantage_array=get_student_disadvantage($work_year);
	
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td width=$pic_width>大頭照</td><td>弱勢身分</td><td>積分統計</td>";//<td>備註</td>

	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year)=$recordSet->FetchRow()) {
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		//$remote=$disadvantage_array[$student_sn]['remote'];
		$disadvantage=$disadvantage_array[$student_sn]['disadvantage'];
			if(!$_POST['show_zero'] and !$disadvantage) $disadvantage=''; else $disadvantage="($disadvantage)".$stud_free_arr[$disadvantage];
			
		$score=$disadvantage_array[$student_sn]['score'];
			$bgcolor_disadvantage=$score?$stud_sex_color:'#cccccc';
		$memo=$disadvantage_array[$student_sn]['disadvantage_memo'];
		$java_script="";
		$action='';
		
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		$java_script='';
		/*
		if($student_sn==$edit_sn){			
			//低收入戶選單
			$disadvantage_value=$disadvantage;
			$disadvantage="<select name='edit_disadvantage'>";
			foreach($stud_free_arr as $key=>$value){
				$selected=($disadvantage_value==$key)?'selected':'';
				$disadvantage.="<option value='$key'$selected>($key)$value</option>";
			}
			$disadvantage.="</select>";
			
			$stud_sex_color='#ffffaa';
			//扶助弱勢備註
			$memo="<input type='text' name='edit_memo' size=20 value='$memo'>";
			//動作按鈕
			$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的扶助弱勢積分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
		} else {
		*/
		if(array_key_exists($student_sn,$listed)){
			$editable=array_key_exists($student_sn,$editable_sn_array)?1:0;
			$stud_sex_color=$editable?$stud_sex_color:$uneditable_bgcolor;
			//$java_script=($work_year==$academic_year and $editable and $disadvantage_editable)?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'":'';
		} else { $stud_sex_color='#aaaaaa'; }
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$stud_name</td><td>$my_pic</td><td bgcolor='$bgcolor_disadvantage' align='left'>$disadvantage</td><td bgcolor='$bgcolor_disadvantage'><B>$score</B></td></tr>"; //<td>$memo $action</td>
	}		
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."</table></form>";
foot();
?>