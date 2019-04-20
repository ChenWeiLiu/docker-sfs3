<?php
// $Id: stud_eduh_class.php 5310 2009-01-10 07:57:56Z hami $

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
TD {font-size: 10pt;}
BODY {font-size: 10pt;}

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
$class_seme = get_class_seme();	
//目前學年學期
$seme_year_seme = $_GET[seme_year_seme];
if ($seme_year_seme=='')
	$seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());


echo "<span align=center>$school_short_name $class_seme[$seme_year_seme] -- $class_base[$class_num] 學期輔導記錄表</span>";
?>
<table  cellspacing=1  bgcolor="#cccccc">
 <tr bgcolor="#DBE9DC"><td>座號</td><td>姓名</td><td>父母關係</td><td>家庭類型</td><td>家庭氣氛</td><td>父管教方式</td><td>母管教方式</td><td>居住情形</td><td>經濟狀況</td> <td>最喜愛科目</td><td>最困難科目</td><td>特殊才能</td><td>興趣</td><td>生活習慣</td><td>人際關係</td><td>外向行為</td><td>內向行為</td> <td>學習行為</td><td>不良習慣</td><td>焦慮行為</td> </tr>

<?php


$sse_relation_arr = sfs_text("父母關係");
$sse_family_kind_arr = sfs_text("家庭類型");
$sse_family_air_arr = sfs_text("家庭氣氛");
$sse_farther_arr = sfs_text("管教方式");
$sse_mother_arr = sfs_text("管教方式");
$sse_live_state_arr = sfs_text("居住情形");
$sse_rich_state_arr = sfs_text("經濟狀況");

$sse_s1_arr = sfs_text("喜愛困難科目");
$sse_s2_arr = sfs_text("喜愛困難科目");
$sse_s3_arr = sfs_text("特殊才能");
$sse_s4_arr = sfs_text("興趣");
$sse_s5_arr = sfs_text("生活習慣");
$sse_s6_arr = sfs_text("人際關係");
$sse_s7_arr = sfs_text("外向行為");
$sse_s8_arr = sfs_text("內向行為");
$sse_s9_arr = sfs_text("學習行為");
$sse_s10_arr = sfs_text("不良習慣");
$sse_s11_arr = sfs_text("焦慮行為");


$query = "select a.*,b.stud_name,b.curr_class_num from stud_seme_eduh a ,stud_base b where a.stud_id=b.stud_id and a.seme_year_seme='$seme_year_seme' and  b.curr_class_num like '$class_num%' order by b.curr_class_num ";
$recordSet = $CONN->Execute($query) or die($query);
while (!$recordSet->EOF) {

	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$stud_id = $recordSet->fields["stud_id"];
	$stud_name = $recordSet->fields["stud_name"];
	$curr_class_num = $recordSet->fields["curr_class_num"];
	$sse_relation = $sse_relation_arr[$recordSet->fields["sse_relation"]];
	$sse_family_kind = $sse_family_kind_arr[$recordSet->fields["sse_family_kind"]];
	$sse_family_air = $sse_family_air_arr[$recordSet->fields["sse_family_air"]];
	$sse_farther = $sse_farther_arr[$recordSet->fields["sse_farther"]];
	$sse_mother = $sse_mother_arr[$recordSet->fields["sse_mother"]];
	$sse_live_state = $sse_live_state_arr[$recordSet->fields["sse_live_state"]];
	$sse_rich_state = $sse_rich_state_arr[$recordSet->fields["sse_rich_state"]];	
	$sse_s1 = $recordSet->fields["sse_s1"];
	$sse_s2 = $recordSet->fields["sse_s2"];
	$sse_s3 = $recordSet->fields["sse_s3"];
	$sse_s4 = $recordSet->fields["sse_s4"];
	$sse_s5 = $recordSet->fields["sse_s5"];
	$sse_s6 = $recordSet->fields["sse_s6"];
	$sse_s7 = $recordSet->fields["sse_s7"];
	$sse_s8 = $recordSet->fields["sse_s8"];
	$sse_s9 = $recordSet->fields["sse_s9"];
	$sse_s10 = $recordSet->fields["sse_s10"];
	$sse_s11 = $recordSet->fields["sse_s11"];

	$sit_num = substr($curr_class_num,-2);
	$bgcolor = ($i++%2)?"#eeffff":"#ffffff";
	echo "<tr bgcolor='$bgcolor' onMouseOver=setBG('#AAFFCC',this) onMouseout=setBGOff('$bgcolor',this) >";
	
	echo "<td>$sit_num</td><td>$stud_name</td><td>$sse_relation</td><td>$sse_family_kind</td><td>$sse_family_air</td>
		<td>$sse_farther</td><td>$sse_mother</td><td>$sse_live_state</td><td>$sse_rich_state</td>";
	
	for($j=1;$j<=11;$j++) {
		$temp_arr = explode(",",${"sse_s".$j});
		$s_temp_arr = ${"sse_s".$j."_arr"};
		$temp_str='';
		while(list($id,$val)=each($temp_arr)){
			if ($val)
				$temp_str .= $s_temp_arr[$val].",";
		}
		echo "<td>$temp_str</td>";
	}
	echo "</tr>\n";
	$recordSet->MoveNext();
};
?>


</table>
</body>
</html>
