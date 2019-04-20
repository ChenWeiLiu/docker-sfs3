<?php
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
	
	$class_base = class_base();
	$class_seme = get_class_seme();	
	//目前學年學期
	$seme_year_seme = $_GET[seme_year_seme];
	if ($seme_year_seme=='')
		$seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());


echo "<span align=center>$school_short_name $class_seme[$seme_year_seme]  $class_base[$class_num]  輔導訪談記錄表</span>";
?>
<table  cellspacing=1  bgcolor="#cccccc">
  <tr bgcolor="#DBE9DC"><td>座號</td><td>姓名</td><td>記錄日期</td><td>連絡對象</td><td>連絡事項</td><td>內容要點</td><td>建檔者</td></tr>

<?php


$query = "select a.*,b.stud_name,b.curr_class_num from stud_seme_talk a,stud_base b where a.stud_id=b.stud_id and a.seme_year_seme = '$seme_year_seme' and b.curr_class_num like '$class_num%' and b.stud_study_cond=0 order by b.curr_class_num,a.sst_date";
$recordSet = $CONN->Execute($query) or die($query);
while (!$recordSet->EOF) {

	$sst_id = $recordSet->fields["sst_id"];
	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$stud_id = $recordSet->fields["stud_id"];
	$stud_name = $recordSet->fields["stud_name"];
	$sit_num = substr($recordSet->fields["curr_class_num"],-2);
	$sst_date = $recordSet->fields["sst_date"];
	$sst_name = $recordSet->fields["sst_name"];
	$sst_main = $recordSet->fields["sst_main"];
	$sst_memo = $recordSet->fields["sst_memo"];
	
	$teach_name = get_teacher_name($recordSet->fields["teach_id"]);
	$update_time = $recordSet->fields["update_time"];

	$bgcolor = ($i++%2)?"#eeffff":"#ffffff";
	echo "<tr bgcolor='$bgcolor' onMouseOver=setBG('#AAFFCC',this) onMouseout=setBGOff('$bgcolor',this) >";
	
	echo "<td>$sit_num</td><td>$stud_name<td>$sst_date</td><td>$sst_name</td><td>$sst_main</td>
		<td>$sst_memo</td><td>$teach_name</td>";
	
	echo "</tr>\n";
	$recordSet->MoveNext();
};
?>


</table>
</body>
</html>
