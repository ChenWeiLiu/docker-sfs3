<?php
// $Id: stud_spe_detail.php 5310 2009-01-10 07:57:56Z hami $

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
	$query = "select stud_name,curr_class_num from stud_base where stud_id='$_GET[stud_id]'";
	$res = $CONN->Execute($query);
	$stud_name = $res->fields[stud_name];
	$curr_class_num = $class_base[substr($res->fields[curr_class_num],0,-2)];
	
	
echo "<span align=center>$_GET[stud_id] -- $curr_class_num -- $stud_name 特殊優良表現記錄表</span>";
?>
<table  cellspacing=1  bgcolor="#cccccc">
  <tr bgcolor="#DBE9DC"><td>學年學期</td><td>記錄日期</td><td>優良表現事由</td><td>建檔者</td></tr>

<?php


$query = "select * from stud_seme_spe where stud_id='$_GET[stud_id]' order by seme_year_seme";
$recordSet = $CONN->Execute($query) or die($query);
while (!$recordSet->EOF) {

	$ss_id = $recordSet->fields["ss_id"];
	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$stud_id = $recordSet->fields["stud_id"];
	$sp_date = $recordSet->fields["sp_date"];
	$sp_memo = $recordSet->fields["sp_memo"];
	$teach_name = get_teacher_name($recordSet->fields["teach_id"]);
	$update_time = $recordSet->fields["update_time"];


	$bgcolor = ($i++%2)?"#eeffff":"#ffffff";
	echo "<tr bgcolor='$bgcolor' onMouseOver=setBG('#AAFFCC',this) onMouseout=setBGOff('$bgcolor',this) >";
	
	echo "<td>$seme_year_seme</td><td>$sp_date</td><td>$sp_memo</td>
		<td>$teach_name</td>";
	
	echo "</tr>\n";
	$recordSet->MoveNext();
};
?>


</table>
</body>
</html>
