<?php

// $Id:  $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_score.php";

sfs_check();

//秀出網頁
head("導師及輔導教師");

//模組選單
print_menu($menu_p,$linkstr);

//儲存紀錄處理
if($_POST['go']=='儲存紀錄'){
	$content=serialize($_POST['contact']);
	//檢查是否已有舊紀錄
	$query="select sn from career_contact where student_sn=$student_sn";
	$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	$sn=$res->fields[0];
	if($sn) $query="update career_contact set content='$content' where sn=$sn";
		else $query="insert into career_contact set student_sn=$student_sn,content='$content'";
		$res=$CONN->Execute($query) or die("SQL錯誤:$query");
}

if($c_id){	
	//抓取處室聯絡電話
	$room_tel=get_room_tel();
	$room_list="※學校相關處室聯絡電話：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
	<tr bgcolor='#c4ffd9' align='center'><td>《教務處》<br>{$room_tel['教務處']}</td><td>《學務處》<br>{$room_tel['學務處']}</td><td>《輔導處》<br>{$room_tel['輔導處']}</td></tr></table>";	
}

if($student_sn){
	//抓取學生學期就讀班級
	$stud_seme_arr=get_student_seme($student_sn);
	
	//取得導師及輔導教師資料
	$query="select * from career_contact where student_sn=$student_sn";
	$res=$CONN->Execute($query);
	$content_array=unserialize($res->fields['content']);

	$contact_list="※導師及輔導教師：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr bgcolor='#c4d9ff' align='center'><td>年級</td><td>學期</td><td>導師姓名</td><td>導師聯絡電話</td><td>輔導教師姓名</td><td>輔導教師聯絡電話</td></tr>";
	//內容
	foreach($stud_seme_arr as $seme_key=>$year_seme){
		$bgcolor=($career_previous or $curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
		$readonly=($career_previous or $curr_seme_key==$seme_key)?'':'readonly';
		$contact_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>
		<td><textarea name='contact[$seme_key][tutor]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$content_array[$seme_key][tutor]}</textarea></td>
		<td><textarea name='contact[$seme_key][tutor_tel]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$content_array[$seme_key][tutor_tel]}</textarea></td>
		<td><textarea name='contact[$seme_key][guidance]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$content_array[$seme_key][guidance]}</textarea></td>
		<td><textarea name='contact[$seme_key][guidance_tel]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$content_array[$seme_key][guidance_tel]}</textarea></td>
		</tr>";
	}
	$contact_list.="</table>";
	
	$showdata="$room_list<br>$contact_list";
	$act="<br><center><input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; height=42'></center>";
}

$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'><table style='border-collapse: collapse; font-size=12px;'><tr><td valign='top'>$class_select<br>$student_select</td><td valign='top'>$showdata $act</td></tr></table></form></font>";

echo $main;

foot();

?>
