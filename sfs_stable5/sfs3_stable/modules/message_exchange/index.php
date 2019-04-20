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

$main_function.= "<center><FONT SIZE='5' COLOR='#000099'>".$MODULE_PRO_KIND_NAME."模組，歡迎光臨!!</FONT><BR><BR>\n";
//$main_function.= "[<A HREF=\"admin.php\">管理介面</A>]<BR><BR>\n";


// 取出資料表中所有公告之資料
$sql = "select a.r_id as MID, b.title as title, b.m_date as board_date ";
$sql .= " from ".$user_t1." as a, ".$user_t2." as b";
$sql .= " where a.r_id = b.r_id";
$sql .= " and a.rece_id = '".$_SESSION['session_tea_sn']."'";
$sql .= " order by b.m_date desc";
//echo $sql."|<BR>\n";
$sql_result = mysql_query($sql) or die($sql."<BR>\nsql語法有誤!!");

if (mysql_num_rows($sql_result)==0){
  $main_function.= "<center>目前沒有任何訊息存在!!<br>\n";
  $main_function.= "[<A HREF=\"insert.php\">新增訊息</A>]&nbsp;&nbsp;\n";
  $main_function.= "[<A HREF=\"m_list.php\">管理傳送訊息</A>]<BR>\n";
  echo $main_function;
  exit;
}

$i=1;
$main_function.= "<table>\n";
$main_function.= "<tr bgcolor='#CCFF66'>\n";
$main_function.= "<td>序號</td>\n";
$main_function.= "<td>訊息標題</td>\n";
$main_function.= "<td>訊息日期</td>\n";
$main_function.= "</tr>\n";
while ($row = mysql_fetch_array($sql_result)){
  if($i%2){
	$main_function.= "<tr bgcolor='#FFFFCC'>\n";
  }else{
	$main_function.= "<tr bgcolor='#CCFFFF'>\n";  
  }
  $main_function.= "<td>".$i."</td>\n";
  $main_function.= "<td>";
  $main_function.= "<A HREF='browser_list.php?r_id=".$row['MID']."&s_id=".$i."'>";
  $main_function.= $row['title']."</A></td>\n";
  $main_function.= "<td>".$row['board_date']."</td>\n";
  $main_function.= "</tr>\n";
  $i++;
}
$main_function.= "</table>\n";

$main_function.= "<BR>\n[<A HREF=\"insert.php\">新增訊息</A>]&nbsp;&nbsp;\n";
$main_function.= "[<A HREF=\"m_list.php\">管理傳送訊息</A>]<BR>\n";


echo $main_function;
foot();
?>