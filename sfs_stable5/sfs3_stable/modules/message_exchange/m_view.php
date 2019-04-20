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

$m_id = $_REQUEST['m_id'];

$main_function.= "<center><FONT SIZE='5' COLOR='#66CCFF'>傳送訊息被接收閱覽時間!!</FONT><BR><BR>\n";

// 取出資料表中某一則之資料
$sql = "select * from ".$user_t1;
$sql .= " where `m_id` = '".$m_id."' ";
$sql .= " and `r_check` = '1' ";
$sql_result = mysql_query($sql) or die($sql."sql語法有誤!!");

// 公告細項內容
$row = mysql_fetch_array($sql_result);

$main_function.= "<table>\n";
$main_function.= "<tr>\n";
$main_function.= "<td bgcolor='#FFFFCC'>\n";
$main_function.= "訊息接收者";
$main_function.= "</td>\n";
$main_function.= "<td bgcolor='#CCFFFF'>\n";
$user_name = new user_info;//找出user id的對應姓名
$user_name -> receiver_name($row['rece_id']);
$main_function.= $user_name->uname;
$main_function.= "</td>\n";
$main_function.= "</tr>\n";

$main_function.= "<tr>\n";
$main_function.= "<td bgcolor='#FFFFCC'>\n";
$main_function.= "訊息閱讀日期";
$main_function.= "</td>\n";
$main_function.= "<td bgcolor='#CCFFFF'>\n";
$main_function.= $row['r_date'];
$main_function.= "</td>\n";
$main_function.= "</tr>\n";

$main_function.= "</table>\n";
$main_function.= "[ <A HREF='index.php'>回訊息總覽</A> ]&nbsp;";
$main_function.= "[ <A HREF='m_list.php'>回管理傳送訊息</A> ]\n";

echo $main_function;
foot();
?>