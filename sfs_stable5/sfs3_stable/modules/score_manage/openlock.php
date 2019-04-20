<?php
// $Id: openlock.php 5310 2009-01-10 07:57:56Z hami $
/*引入學務系統設定檔*/
include "../../include/config.php";
//引入函數
include "./my_fun.php";
//使用者認證
sfs_check();
//是否每一次月考要配合一次平時成績
$rs_yorn=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='score_input' AND pm_item='yorn'");
$yorn=$rs_yorn->fields['pm_value'];

$score_semester=$_GET['score_semester'];
$score_semester=$_GET['score_semester'];
$class_id=$_GET['class_id'];
$test_sort=$_GET['test_sort'];
$ss_id=$_GET['ss_id'];
$year_seme=$_GET['year_seme'];
$year_name=$_GET['year_name'];
$me=$_GET['me'];
$stage=$_GET['stage'];
//echo $score_semester.$score_semester.$class_id.$test_sort.$ss_id;
//$sql_del="UPDATE $score_semester SET sendmit='0' where class_id='$class_id' and test_sort='$test_sort' and ss_id='$ss_id'";
$CONN->Execute($sql_del);

if($yorn=="n"){
	if($stage=="254") $sql_upd="UPDATE $score_semester SET sendmit='1' where class_id='$class_id'  and ss_id='$ss_id' and test_kind='平時成績'";
	elseif($stage=="255") $sql_upd="UPDATE $score_semester SET sendmit='1' where class_id='$class_id'  and ss_id='$ss_id' and test_kind='全學期'";
	else $sql_upd="UPDATE $score_semester SET sendmit='1' where class_id='$class_id' and test_sort='$stage' and ss_id='$ss_id' and test_kind='定期評量'";
}
else{
	$sql_upd="UPDATE $score_semester SET sendmit='1' where class_id='$class_id' and test_sort='$stage' and ss_id='$ss_id'";
}

$CONN->Execute($sql_upd);
header("Location:index.php?class_id=$_GET[temp_class]&year_seme=$year_seme&year_name=$year_name&me=$me&stage=$stage&is_open=1");
?>
