<?php
// $Id: stud_spe_class.php 5310 2009-01-10 07:57:56Z hami $

include "stud_reg_config.php";

sfs_check();

//取得任教班級代號
$class_num = get_teach_class();
if ($class_num == '') {
        head("權限錯誤");
        echo "<h3>本項作業為級任導師權限</h3>";
        foot();
        exit;
}


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
	//目前學年學期
	$class_base = class_base();
	$class_seme = get_class_seme();	
	$seme_year_seme = $_GET[seme_year_seme];
	if ($seme_year_seme=='')
		$seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());



	
echo "<span align=center>$school_short_name $class_seme[$seme_year_seme]  $class_base[$class_num]  特殊優良表現記錄表</span>";
?>

<table  cellspacing=1  bgcolor="#cccccc">
  <tr bgcolor="#DBE9DC"><td>座號</td><td>姓名</td><td>記錄日期</td><td>優良表現事由</td><td>建檔者</td></tr>

<?php


$query = "select a.*,b.stud_name,b.curr_class_num from stud_seme_spe a,stud_base b where a.stud_id=b.stud_id and b.curr_class_num like '$class_num%' and a.seme_year_seme='$seme_year_seme' order by b.curr_class_num";
$recordSet = $CONN->Execute($query) or die($query);
while (!$recordSet->EOF) {

	$ss_id = $recordSet->fields["ss_id"];
	$stud_name = $recordSet->fields[stud_name];
	$sit_num = substr($recordSet->fields[curr_class_num],-2);
	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$stud_id = $recordSet->fields["stud_id"];
	$sp_date = $recordSet->fields["sp_date"];
	$sp_memo = $recordSet->fields["sp_memo"];
	$teach_name = get_teacher_name($recordSet->fields["teach_id"]);
	$update_time = $recordSet->fields["update_time"];


	$bgcolor = ($i++%2)?"#eeffff":"#ffffff";
	echo "<tr bgcolor='$bgcolor' onMouseOver=setBG('#AAFFCC',this) onMouseout=setBGOff('$bgcolor',this) >";
	
	echo "<td>$sit_num</td><td>$stud_name</td><td>$sp_date</td><td>$sp_memo</td>
		<td>$teach_name</td>";
	
	echo "</tr>\n";
	$recordSet->MoveNext();
};
?>


</table>
</body>
</html>
