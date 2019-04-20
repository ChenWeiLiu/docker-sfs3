<?php

// $Id:  $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_score.php";

sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}


//秀出網頁
head("導師及輔導教師");

//模組選單
print_menu($menu_p);

//檢查是否開放
if (!$mystory){
   echo "模組變數尚未開放本功能，請洽詢學校系統管理者！";
   exit;
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$seme_year_seme=sprintf('%03d%d',$curr_year,$curr_seme);
$student_sn=$_SESSION['session_tea_sn'];
$stud_name=$_SESSION['session_tea_name'];

$menu=$_POST['menu'];

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


//抓取學生學期就讀班級
$stud_seme_arr=array();
$table=array('stud_seme_import','stud_seme');
foreach($table as $key=>$value){
	$query="select * from $value where student_sn=$student_sn";
	$res=$CONN->Execute($query);
	while(!$res->EOF){
		$stud_grade=substr($res->fields['seme_class'],0,-2);
		$year_seme=$res->fields['seme_year_seme'];
		$semester=substr($year_seme,-1);	
		$seme_key=$stud_grade.'-'.$semester;
		$stud_seme_arr[$seme_key]=$year_seme;
		//抓取本學期相關資料
		if($year_seme==$seme_year_seme) {
			$curr_stud_grade=$stud_grade;
			$curr_seme_class=$res->fields['seme_class'];
			$curr_seme_num=$res->fields['seme_num'];
			$curr_seme_key=$seme_key;			
		}
		$res->MoveNext();
	}
}

//進行排序
asort($stud_seme_arr);

//產生選單
$memu_select="※我是 $stud_name ，本學期就讀班級： $curr_seme_class ，座號： $curr_seme_num 。";

//抓取處室聯絡電話　　room_name room_tel room_fax 
$query="select * from school_room where enable='1'";
$res=$CONN->Execute($query);
while(!$res->EOF){
	$room_name=$res->fields['room_name'];
	$room_tel[$room_name]=$res->fields['room_tel'];
	$res->MoveNext();
}

$room_list="※學校相關處室聯絡電話：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width=100%>
<tr bgcolor='#c4ffd9' align='center'><td>《教務處》<br>{$room_tel['教務處']}</td><td>《學務處》<br>{$room_tel['學務處']}</td><td>《輔導處》<br>{$room_tel['輔導處']}</td></tr></table>";



//取得既有資料
$query="select * from career_contact where student_sn=$student_sn";
$res=$CONN->Execute($query);
$content_array=unserialize($res->fields['content']);

$contact_list="※導師及輔導教師：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width=100%>
		<tr bgcolor='#c4d9ff' align='center'><td>年級</td><td>學期</td><td>導師姓名</td><td>導師聯絡電話</td><td>輔導教師姓名</td><td>輔導教師聯絡電話</td></tr>";

//檢查是否為可填寫月份
$contact_months="[,$contact_months,]";
$pos=strpos($contact_months,$curr_month,1);
//內容
foreach($stud_seme_arr as $seme_key=>$year_seme) {
	if($pos) {
		$bgcolor=($career_previous or $curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
		$readonly=($career_previous or $curr_seme_key==$seme_key)?'':'readonly';
		$contact_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>
			<td><textarea name='contact[$seme_key][tutor]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$content_array[$seme_key][tutor]}</textarea></td>
			<td><textarea name='contact[$seme_key][tutor_tel]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$content_array[$seme_key][tutor_tel]}</textarea></td>
			<td><textarea name='contact[$seme_key][guidance]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$content_array[$seme_key][guidance]}</textarea></td>
			<td><textarea name='contact[$seme_key][guidance_tel]' style='border-width:1px; color:brown; background:$bgcolor; font-size:11px; width=100%; height=100%;' $readonly>{$content_array[$seme_key][guidance_tel]}</textarea></td>
			</tr>";
	} else {	
		$contact_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>
			<td>{$content_array[$seme_key][tutor]}</td>
			<td>{$content_array[$seme_key][tutor_tel]}</td>
			<td>{$content_array[$seme_key][guidance]}</td>
			<td>{$content_array[$seme_key][guidance_tel]}</td>
			</tr>";	
	}
}
$contact_list.="</table>";
	
$showdata="$room_list<br>$contact_list";

$act=$pos?"<br><center><input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; height=42'></center>":"◎學校設定可填寫月份：$m_arr[contact_months]";
$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>$showdata $act</form></font>";

echo $main;

foot();

?>
