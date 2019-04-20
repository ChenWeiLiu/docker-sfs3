<?php
// $Id: delete.php 5310 2009-01-10 07:57:56Z hami $
/*引入學務系統設定檔*/
include "../../include/config.php";
//引入函數
include "./my_fun.php";
//使用者認證
sfs_check();


$score_semester=$_GET['score_semester'];
$score_semester=$_GET['score_semester'];
$class_id=$_GET['class_id'];
$test_sort=$_GET['test_sort'];
$ss_id=$_GET['ss_id'];
$year_seme=$_GET['year_seme'];
$year_name=$_GET['year_name'];
$me=$_GET['me'];
$stage=$_GET['stage'];

$sql_del="delete from $score_semester where class_id='$class_id' and ss_id='$ss_id' and test_sort='$test_sort' ";

$CONN->Execute($sql_del);
header("Location:index.php?class_id=$_GET[temp_class]&year_seme=$year_seme&year_name=$year_name&me=$me&stage=$stage&is_open=1");
?>
