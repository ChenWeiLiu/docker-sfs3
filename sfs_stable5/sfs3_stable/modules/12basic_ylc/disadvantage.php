<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("扶助弱勢");
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

if($_POST['act']=='統計本年度不符合偏遠小校加分條件學生的級分'){
	//抓本年度所有開列學生的student_sn
	$sn_array=get_student_list($work_year);
	
	//不列入本年度偏遠小校加分的起始日期
	$remote_score_no_date=($academic_year+1911)."-08-01";
	
	//更新級分
	foreach($sn_array as $key=>$student_sn){
		if($school_remote==0) continue;
		$sql="SELECT move_date FROM stud_move WHERE student_sn=$student_sn AND move_kind=2 AND move_date>='{$remote_score_no_date}'";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		if($res->recordcount()>0) {
			$sql="UPDATE 12basic_ylc SET score_remote=0,disadvantage_memo='偏遠小校不加分' WHERE academic_year=$work_year AND student_sn=$student_sn";
			$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		}
	}
}

if($_POST['act']=='確定修改'){
	$sql="UPDATE 12basic_ylc SET score_remote='{$_POST['edit_remote']}',score_disadvantage='{$_POST['edit_disadvantage']}',disadvantage_memo='{$_POST['edit_memo']}' WHERE academic_year=$work_year AND student_sn=$edit_sn";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$edit_sn=0;
}
/*
if($_POST['act']=='設定參與免試學生皆符合就讀偏遠'){
	$sql="update 12basic_ylc set score_remote=1 where academic_year=$work_year";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
};
*/

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

if($work_year==$academic_year) {
	if($school_remote==1 || $school_remote==2) $tool_icon.=" <input type='submit' value='統計本年度不符合偏遠小校加分條件學生的級分' name='act' onclick='return confirm(\"確定要重新統計統計本年度不符合偏遠小校加分條件學生的級分?\")'>";
	$tool_icon.=$editable_hint;
	if($school_remote==1 || $school_remote==2) $tool_icon.="<br><font size=3 color='#DC143C'>★依【雲林區十二年國教免試入學辦法】規定，畢業生若於畢（結）業學年度開始後轉入者（例如：甲生於".($academic_year+1)."年6月畢業，於{$academic_year}年8月1日以後轉入），不列入本項比序加分。請承辦人務必點選上方【統計本年度不符合偏遠小校加分條件學生的級分】按鈕進行系統檢查，謝謝您！</font>";
}

$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>";


if($stud_class)
{
	//取得指定學年已經開列的學生清單
	$listed=get_student_list($work_year);
	
	//取得指定學年已經開列的學生扶助弱勢分數	
	$disadvantage_array=get_student_disadvantage($work_year);	
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_study_year FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td>就讀偏遠地區</td><td>低收入戶/中低收入戶</td><td>級分統計</td><td>備註</td>";
	$studentdata="<tr align='center' bgcolor='#ff8888'><td width=80>學號</td><td width=50>座號</td><td width=120>姓名</td><td>經濟弱勢</td><td>偏遠小校</td><td>級分統計</td><td>備註</td>";

	while(list($student_sn,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_study_year)=$recordSet->FetchRow()) {
		if($pic_checked) $my_pic=get_pic($stud_study_year,$stud_id);
		$seme_num=sprintf('%01d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#EEFFEE":"#FFEEEE";
		//$remote=$disadvantage_array[$student_sn]['remote'];
		$disadvantage=$disadvantage_array[$student_sn]['disadvantage'];
		$remote=$disadvantage_array[$student_sn]['remote'];
		$score=$disadvantage_array[$student_sn]['score'];
		$memo=$disadvantage_array[$student_sn]['disadvantage_memo'];
		$java_script="";
		$action='';

		if($student_sn==$edit_sn){			
			//經濟弱勢選單
			$disadvantage_value=$disadvantage;
			$disadvantage='';
			foreach($disadvantage_level as $key=>$value){
				$checked=($disadvantage_value==$key)?'checked':'';
				$disadvantage.="<input type='radio' name='edit_disadvantage' value=$key $checked>$value ";
			}
			//偏遠小校選單
			$remote_value=$remote;
			$remote='';
			foreach($remote_level as $key=>$value){
				$checked=($remote_value==$key)?'checked':'';
				$remote.="<input type='radio' name='edit_remote' value=$key $checked>$value<br>";
			}
			
			$stud_sex_color='#ffffaa';
			//扶助弱勢備註
			$memo.="<input type='text' name='edit_memo' size=20 value='$memo'>";
			//動作按鈕
			$action="<input type='submit' name='act' value='確定修改' onclick='return confirm(\"確定要修改 $stud_name 的扶助弱勢級分?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
		} else {
			if(array_key_exists($student_sn,$listed)){
				if($work_year==$academic_year) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'";
			} else { $stud_sex_color='#aaaaaa'; }
		}		
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_num</td><td>$my_pic $stud_name</td><td>$disadvantage</td><td>$remote</td><td><B>$score</B></td><td>$memo $action</td></tr>";
	}
}

echo $main.$studentdata."</table></form>";

//警告訊息
//if($school_remote==1 || $school_remote==2) {
//	$msg="依【雲林區十二年國教免試入學辦法】規定，畢業生若於畢（結）業學年度開始後轉入者（例如：甲生於103年6月畢業，於102年8月1日以後轉入），不列入本項比序加分。請承辦人務必將不符合規定學生之【偏遠小校】分數欄改為【不符偏遠小校加分條件】，謝謝您！";
//	$alert=AlertBox($msg);
//	echo $alert;
//}

foot();

?>
