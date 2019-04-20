<?php
// $Id: show_ss.php 8682 2015-12-25 03:00:21Z qfon $
require "config.php";

// 不需要 register_globals
/*
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
*/

$ss_id=intval($_GET['ss_id']);
$sql="select * from score_ss where ss_id='$ss_id'";
$rs=&$CONN->Execute($sql) or die($sql);
$scope_id=$rs->fields['scope_id'];
$subject_id=$rs->fields['subject_id'];
$year=$rs->fields['year'];
$semester=$rs->fields['semester'];
$class_year=$rs->fields['class_year'];
$print=$rs->fields['print'];
//echo $scope_id."--".$subject_id."--".$year."--".$semester."--".$class_year."<br>";

//有幾個階段
$rs1=&$CONN->Execute("select performance_test_times from score_setup where year='$year' and semester='$semester' and class_year='$class_year'");
$performance_test_times=$rs1->fields['performance_test_times'];
//echo $performance_test_times."<br>";

//有幾個班級
$class_id_front=sprintf("%03d_%d_%02d", $year, $semester, $class_year);
//echo $class_id_front."<br>";
$rs2=&$CONN->Execute("select c_name,class_id from school_class where class_id like '$class_id_front%'");
$i=0;
while(!$rs2->EOF){
    $c_name[$i]=$rs2->fields['c_name'];
    $class_id[$i]=$rs2->fields['class_id'];
    $i++;
    $rs2->MoveNext();
}
//echo count($c_name)."<br>";

$score_semester="score_semester_".$year."_".$semester;

$main="<table border=0 bgcolor=green cellspacing=1 cellpadding=4 border=0>";
$main.="<tr bgcolor=#E4D534 align=center>";
for($i=0;$i<=$performance_test_times;$i++){
    $stage="第".$i."階段";
    if($i==0) $stage="&nbsp;";
    $main.="<td>".$stage."</td>";
}
$main.="</tr>";

for($j=0;$j<count($c_name);$j++){
    $main.="<tr align=center><td bgcolor=#E4D534>".$c_name[$j]."班</td>";
    for($k=0;$k<$performance_test_times;$k++){
        $m=$k+1;
        if($print!=1)$m=255;
        $sql="select score_id from $score_semester where class_id='$class_id[$j]' and test_sort='$m' and ss_id='$ss_id'";
        //echo $sql;
        $rs3=&$CONN->Execute($sql);
        $score_id=$rs3->fields['score_id'];
        if($score_id!="") $realy="V";
        else $realy="X";
        $main.="<td bgcolor=#FFFFFF>$realy</td>";
    }
    $main.="</tr>";
}

$main.="</table>";
echo $main;
$button="<input type='button' value='關閉' onclick='closewindow()'>";
echo $button;
?>
<script language="JavaScript1.2">
<!-- Begin

function closewindow(){
    window.close();
}

//  End -->
</script>

