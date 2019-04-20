<?php
include "stud_reg_config.php";

sfs_check();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">

<style type="text/css">
<!--
TD {font-size: 11pt;}
BODY {font-size: 11pt;}

-->
</style>

<script language="JavaScript">
<!--
function setBG(TheColor,thetable) {
thetable.bgColor=TheColor
}
function setBGOff(TheColor,thetable) {
thetable.bgColor=TheColor
}
//-->
</script>

</head>
<body>
<?php
	$class_base = class_base();
  // 2012/10/05 by smallduh	
	//$query = "select stud_name,curr_class_num from stud_base where stud_id='$_GET[stud_id]'";
	$query = "select stud_id,stud_name,curr_class_num,stud_study_year from stud_base where student_sn='$_GET[student_sn]'";
	$res = $CONN->Execute($query);
	$stud_id=$res->fields[stud_id];
	$stud_name = $res->fields[stud_name];
	$stud_study_year = $res->fields[stud_study_year];
	$curr_class_num = $class_base[substr($res->fields[curr_class_num],0,-2)];
	
// 2012/10/05 by smallduh	
//由 student_sn 取得 stud_id 及 stud_study_year(入學年), 
//由於 stud_seme_eduh 及 stud_seme_talk 這兩個 table 沒有 student_sn欄位, 但有 seme_year_seme 欄位, 
//且 stud_id 在國中, 每10年會重覆
//故利用入學年判斷, 取入學後 9年內的資料	
 $min_year_seme=sprintf("%03d",$stud_study_year)."1";
 $max_year_seme=sprintf("%03d",$stud_study_year+8)."2"; 
	
//2012/10/05 by smallduh	
//echo "<span align=center>$_GET[stud_id] -- $curr_class_num -- $stud_name 輔導訪談記錄表</span>";
echo "<span align=center>$stud_id -- $curr_class_num -- $stud_name 輔導訪談記錄表</span>";
?>
<table  cellspacing=1  bgcolor="#cccccc">
  <tr bgcolor="#DBE9DC"><td>學年學期</td><td>記錄日期</td><td>連絡對象</td><td>連絡事項</td><td>內容要點</td><td>訪談者</td><td>建檔者</td></tr>

<?php

//by smallduh 2012/10/05
//$query = "select * from stud_seme_talk where stud_id='$_GET[stud_id]' order by seme_year_seme";
$query = "select * from stud_seme_talk where stud_id='$stud_id' and seme_year_seme>='$min_year_seme' and seme_year_seme<='$max_year_seme' order by seme_year_seme";
$recordSet = $CONN->Execute($query) or die($query);

while (!$recordSet->EOF) {

	$sst_id = $recordSet->fields["sst_id"];
	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$stud_id = $recordSet->fields["stud_id"];
	$sst_date = $recordSet->fields["sst_date"];
	$sst_name = $recordSet->fields["sst_name"];
	$sst_main = $recordSet->fields["sst_main"];
	$sst_memo = $recordSet->fields["sst_memo"];
	$sst_interview = $recordSet->fields["interview"];

	$teach_name = get_teacher_name($recordSet->fields["teach_id"]);
	$update_time = $recordSet->fields["update_time"];

	$bgcolor = ($i++%2)?"#eeffff":"#ffffff";
	echo "<tr bgcolor='$bgcolor' onMouseOver=setBG('#AAFFCC',this) onMouseout=setBGOff('$bgcolor',this) >";
	
	echo "<td>$seme_year_seme</td><td>$sst_date</td><td>$sst_name</td><td>$sst_main</td>
		<td>$sst_memo</td><td>$sst_interview</td><td>$teach_name</td>";
	
	echo "</tr>\n";
	$recordSet->MoveNext();
};
?>


</table>
</body>
</html>
