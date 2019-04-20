<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";
?>
<link type="text/css" rel="stylesheet" href="../contest_teacher/include/my.css">

<?php

sfs_check();


//目前選定學期
  $c_curr_seme=($_POST['c_curr_seme']!="")?$_POST['c_curr_seme']:sprintf('%03d%1d',$curr_year,$curr_seme);
//目前選定學期
//$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//目前日期時間, 用於比對消息有效期限
$Now=date("Y-m-d H:i:s");

if (!$MANAGER) {
 echo "<font color=red>抱歉! 你沒有管理權限, 系統禁止你繼續操作本功能!!!</font>";
 exit();
}

$TEST=get_test_setup($_POST['option1']);


  list_user_print($_POST['option1'],$_POST['act']);



?>