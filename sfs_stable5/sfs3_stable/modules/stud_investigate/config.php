<?php

//�w�]���ޤJ�ɡA���i�����C
include_once "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";
require_once "./module-cfg.php";

//�ǮեN�X
$school_id=$SCHOOL_BASE['sch_id'];

$room_kind=room_kind();
$title_kind=title_kind();
$class_name_arr = class_base() ;

//���o�Юv�ҳB�B��
$my_sn=$_SESSION['session_tea_sn'];
$my_name=$_SESSION['session_tea_name'];
$sql="select post_office,teach_title_id from teacher_post where teacher_sn=$my_sn;";
$rs=$CONN->Execute($sql) or die("�L�k���o�z���Ҧb�B��!<br>$sql");
$my_room=$room_kind[($rs->fields['post_office'])];
$my_title=$title_kind[($rs->fields['teach_title_id'])];

$page_break ="<P style='page-break-after:always'>&nbsp;</P>";
?>
