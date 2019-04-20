<?php
// $Id: config.php 5310 2010-08-19 22:57:56Z wkb $
include_once "../../include/config.php";
include_once "../../pnadodb/adodb-lib.inc.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_calendar.php";

require_once "./module-cfg.php";

//使用到的資料表
$user_t1 = $MODULE_TABLE_NAME[0];
$user_t2 = $MODULE_TABLE_NAME[1];
$user_t3 = "teacher_base";
$user_t4 = "teacher_post";
$user_t5 = "teacher_title";
$user_t6 = "teacher_connect";

//取得模組設定
$m_arr = &get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
/*
function receiver_name($u_id){
  global $user_t3;
  // 找出帳號對應之使用者姓名
  $sql_1 = "select name from ".$user_t3;
  $sql_1 .= " where `teacher_sn` = '".$u_id."' ";
  $sql_result_1 = mysql_query($sql_1) or die($sql."sql語法有誤!!");
  $row_1 = mysql_fetch_array($sql_result_1);
  return $row_1['name'];
}*/

class user_info{
  var $uname;
  var $utitle;
  var $umail;
  function receiver_name($u_id){
      global $user_t3;
      global $user_t4;
      global $user_t5;
	  global $user_t6;
	  // 找出帳號對應之使用者姓名
	  $sql1 = "select a.name, c.title_name ";
	  $sql1 .= " from ".$user_t3." as a, ".$user_t4." as b, ".$user_t5." as c";
	  $sql1 .= " where a.teacher_sn = b.teacher_sn";
	  $sql1 .= " and b.teach_title_id = c.teach_title_id";
	  $sql1 .= " and a.teach_condition = 0";
      $sql1 .= " and a.teacher_sn = '".$u_id."';";
	  //echo $sql."<BR>\n";
	  $sql_result_1 = mysql_query($sql1) or die($sql1."sql語法有誤!!");
	  $row_1 = mysql_fetch_array($sql_result_1);
	  $this->uname = $row_1[0];
	  $this->utitle = $row_1[1];

	  $sql3 = "select count(email) ";
	  $sql3 .= " from `".$user_t6."`";
	  $sql3 .= " where teacher_sn = '".$u_id."';";
	  $sql_result_3 = mysql_query($sql3) or die($sql3."sql語法有誤!!");
	  $row_3 = mysql_fetch_array($sql_result_3);
      //echo $sql3."||".$row_3[0]."||<BR>\n";
	  if($row_3[0]>0){
		  $sql2 = "select a.name, b.email ";
		  $sql2 .= " from ".$user_t3." as a, ".$user_t6." as b";
		  $sql2 .= " where a.teacher_sn = b.teacher_sn";
		  $sql2 .= " and a.teach_condition = 0";
		  $sql2 .= " and a.teacher_sn = '".$u_id."';";
		  $sql_result_2 = mysql_query($sql2) or die($sql2."sql語法有誤!!");
		  $row_2 = mysql_fetch_array($sql_result_2);
		  //echo $sql2."|||<BR>\n";
		  $this->umail = $row_2[1];	  
	  }else{
	      $this->umail = "";
	  }


  }
}

function receiver_list($r_all,$r_id){
  global $user_t1;
  $receiver_text = "";
  // 找出此接收者是否有閱讀此訊息
  $a1 = new user_info;
  $a = explode(",",$r_all);
  for($i=0;$i<count($a);$i++){
	$sql_1 = "select m_id, r_check from ".$user_t1;
    $sql_1 .= " where `rece_id` = '".$a[$i]."' ";
	$sql_1 .= " and `r_id` = '".$r_id."' ";
    $sql_result_1 = mysql_query($sql_1) or die($sql_1."sql語法有誤!!");
    $row_1 = mysql_fetch_array($sql_result_1);
	//echo $row_1['r_check']."|<BR>\n";
	$a1 -> receiver_name($a[$i]);//找出teacher_sn的對應資料
	if ($row_1['r_check']>0){
	  $receiver_text.= "[<U><B><A HREF='m_view.php?m_id=".$row_1['m_id']."'>".$a1->uname."</A></B></U>] ";
	}else{
	  $receiver_text.= "[".$a1->uname."] ";
	}
	if(($i+1)%10 == 0 and ($i+1) <> count($a)){
	  $receiver_text.= "<BR>\n";
	}
  }

  return $receiver_text;
}
?>