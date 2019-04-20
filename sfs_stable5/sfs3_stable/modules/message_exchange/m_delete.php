<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include "config.php";

sfs_check();
//$use_school=$_REQUEST['use_school'];


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
$main_function = "";

$r_id = $_REQUEST['r_id'];
$s_id = $_REQUEST['s_id'];

$err_message = $_REQUEST['err_message'];
if ($err_message <> ''){
  $main_function.= "<FONT SIZE='7' COLOR='#FF0000'>".$err_message."</FONT>";
}

$main_function.= "<center><FONT SIZE='5' COLOR='#66CCFF'>個人訊息閱覽!!</FONT><BR><BR>\n";

// 取出資料表中某一則之資料
$sql = "select * from ".$user_t2;
$sql .= " where `r_id` = '".$r_id."' ";
$sql_result = mysql_query($sql) or die($sql."sql語法有誤!!");

// 公告細項內容
$row = mysql_fetch_array($sql_result);

$main_function.= "<table>\n";
$main_function.= "<tr>\n";
$main_function.= "<td bgcolor='#FFFFCC'>\n";
$main_function.= "序號";
$main_function.= "</td>\n";
$main_function.= "<td bgcolor='#CCFFFF'>\n";
$main_function.= $s_id;
$main_function.= "</td>\n";
$main_function.= "</tr>\n";

$main_function.= "<tr>\n";
$main_function.= "<td bgcolor='#FFFFCC'>\n";
$main_function.= "訊息標題";
$main_function.= "</td>\n";
$main_function.= "<td bgcolor='#CCFFFF'>\n";
$main_function.= $row['title'];
$main_function.= "</td>\n";
$main_function.= "</tr>\n";

$main_function.= "<tr>\n";
$main_function.= "<td bgcolor='#FFFFCC'>\n";
$main_function.= "訊息內容";
$main_function.= "</td>\n";
$main_function.= "<td bgcolor='#CCFFFF'>\n";
$main_function.= nl2br($row['content']);
$main_function.= "</td>\n";
$main_function.= "</tr>\n";

$main_function.= "<tr>\n";
$main_function.= "<td bgcolor='#FFFFCC'>\n";
$main_function.= "訊息日期";
$main_function.= "</td>\n";
$main_function.= "<td bgcolor='#CCFFFF'>\n";
$main_function.= $row['m_date'];
$main_function.= "</td>\n";
$main_function.= "</tr>\n";

$main_function.= "</table>\n";

$main_function.= "<form action='m_delete_check.php' method='post'>\n";
$main_function.= "<INPUT TYPE='hidden' NAME='r_id' value='".$r_id."'>";
$main_function.= "<INPUT TYPE='submit' value='確認刪除'><br>\n";

$main_function.= "[ <A HREF='index.php'>回訊息總覽</A> ]&nbsp;";
$main_function.= "[ <A HREF='m_list.php'>回管理傳送訊息</A> ]\n";

echo $main_function;
foot();
?>