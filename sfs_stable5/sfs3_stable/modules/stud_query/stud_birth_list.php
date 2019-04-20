<?php

// $Id:  $

/* 學務系統設定檔 */
include "stud_query_config.php";


// --認證 session 
sfs_check();

//head("學生生日月份名單列表");
//print_menu($menu_p);
$this_month=$_GET['month'];

$class_name_arr = class_base(); //班級
echo "<html><head><title>{$this_month}月份生日名單列表</title></head><body>";
$showdata="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111'><tr align='center' bgcolor='#ffcccc'><td>NO.</td><td>班級</td><td>座號</td><td>學號</td><td>姓名</td><td>出生年月日</td></tr>";
$recno=1;
//取得名單列表
$query = "SELECT curr_class_num,stud_id,stud_name,stud_birthday FROM stud_base WHERE stud_study_cond in (0,15) and month(stud_birthday)=$this_month ORDER BY curr_class_num";
$rs=$CONN->Execute($query) or die($query);
while(!$rs->EOF){
	$class_id=substr($rs->fields['curr_class_num'],0,-2);
	$class_name=$class_name_arr[$class_id];
	$class_no=substr($rs->fields['curr_class_num'],-2);
	$stud_id=$rs->fields['stud_id'];
	$stud_name=$rs->fields['stud_name'];
	$stud_birthday=$rs->fields['stud_birthday'];
	$showdata.="<tr align='center'><td>$recno</td><td>$class_name</td><td>$class_no</td><td>$stud_id</td><td>$stud_name</td><td>$stud_birthday</td></tr>";
	$recno++;
	$rs->MoveNext();
}	
$showdata.="</table>";
echo $showdata."</body></html>";
//foot();

?>