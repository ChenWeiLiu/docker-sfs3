<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include "config.php";

sfs_check();
$use_school=$_REQUEST['use_school'];

//秀出網頁
head("訊息傳遞");
?>
<style type="text/css">
<!--
.calendarTr {font-size:12px; font-weight: bolder; color: #006600}
.calendarHeader {font-size:12px; font-weight: bolder; color: #cc0000}
.calendarToday {font-size:12px; background-color: #ffcc66}
.calendarTheday {font-size:12px; background-color: #ccffcc}
.calendar {font-size:11px;font-family: Arial, Helvetica, sans-serif;}
.dateStyle {font-size:15px;font-family: Arial; color: #cc0066; font-weight: bolder}
-->
</style>
<?php
$main_function = "<center>";

$back_name = "delete.php";// 前一支php的檔名

// 使用者輸入變數
$user = $_SESSION['session_tea_sn'];
$r_id = $_POST['r_id'];

// 確認資料是否輸入正確
if ($user == ''){
  $err_message = "使用者帳號未輸入!!";
  header("location: ./".$back_name."?err_message=".$err_message."&r_id=".$r_id."&s_id=".$r_id);
  exit;
}

if ($r_id == ''){
  $err_message = "訊息編號有錯誤!!";
  header("location: ./".$back_name."?err_message=".$err_message."&r_id=".$r_id."&s_id=".$r_id);
  exit;
}

// 修改資料表中的資料
$sql = "DELETE FROM `".$user_t2."`";
$sql.= " WHERE `r_id` = '".$r_id."'";
$sql_result = mysql_query($sql) or die("delete error!!<BR>\n".$sql);

// 修改資料表中的資料
$sql = "DELETE FROM `".$user_t1."`";
$sql.= " WHERE `r_id` = '".$r_id."'";
$sql_result = mysql_query($sql) or die("delete error!!<BR>\n".$sql);
//echo $sql;
$main_function.= "刪除第".$r_id."則發佈之訊息!!<BR>\n";
$main_function.= "[ <A HREF=\"index.php\">回訊息總覽</A>]&nbsp;";
$main_function.= "[ <A HREF='m_list.php'>回管理傳送訊息</A> ]\n";
echo $main_function;
foot();
?>