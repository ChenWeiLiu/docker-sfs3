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

$back_name = "insert.php";// 前一支新增公告php的檔名

// 使用者輸入變數
$sender = $_SESSION['session_tea_sn'];
$receiver = $_REQUEST['receiver'];
$title = $_REQUEST['title'];
$message = $_REQUEST['message'];

//echo $receiver[0]."|<BR>\n";
//echo $receiver[1]."|<BR>\n";
//echo $receiver[2]."|<BR>\n";
$receiver_all = implode(",",$receiver);
//echo $receiver_all."|<BR>\n";
//exit;

// 確認資料是否輸入正確
if (count($receiver) == 0){
  $err_message = "收件者未輸入!!";
  header("location: ./".$back_name."?err_message=".$err_message);
  exit;
}

// 確認公告輸入之資料是否正確
if ($title == ''){
  $err_message = "訊息標題未輸入!!";
  header("location: ./".$back_name."?err_message=".$err_message);
  exit;
}

if ($message == ''){
  $err_message = "訊息內容未輸入!!";
  header("location: ./".$back_name."?err_message=".$err_message);
  exit;
}

//echo "<center>管理者您好!!<BR>\n";
// 新增一個訊息記錄
$sql = "insert into ".$user_t2;
$sql .= " set `title`='".$title."'";
$sql .= ", `content`='".$message."'";
$sql .= ", `sender`='".$sender."'";
$sql .= ", `receiver`='".$receiver_all."'";
$sql .= ", `m_date`=now()";
$sql_result = mysql_query($sql) or die($sql."<BR>\nsql語法有誤!!");

// 取出資料表中所有公告之資料
$sql = "select r_id from `".$user_t2."`";
$sql .= " where `sender`='".$sender."'";
$sql .= " and `receiver`='".$receiver_all."'";
$sql .= " order by m_date desc";
//echo $sql."|<BR>\n";
$sql_result = mysql_query($sql) or die($sql."<BR>\nsql語法有誤!!");
$row_id = mysql_fetch_array($sql_result);
$new_r_id = $row_id[0];


// 新增每個接收者之訊息內容
$sql = "insert into ".$user_t1;
$sql .= " ( `rece_id`,`send_id`, `r_id`) values";
for($i=0;$i<count($receiver);$i++){
  $sql .= " ( '".$receiver[$i]."','".$sender."','".$new_r_id."')";
  if (($i+1)==count($receiver)){
    $sql .= ";";
  }else{
    $sql .= ", ";
  }
}
//echo  $sql."<BR>\n";
$sql_result = mysql_query($sql) or die($sql."<BR>\nsql語法有誤!!");

//echo $sql."<BR>\n";
$main_function.= "新增「".count($receiver)."個」收件者之訊息完成!!<BR>\n";
$main_function.= "[<A HREF=\"index.php\">訊息總覽</A>]<BR>\n";

echo $main_function;
foot();
?>
