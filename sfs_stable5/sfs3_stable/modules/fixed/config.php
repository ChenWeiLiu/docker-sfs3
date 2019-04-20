<?php

// $Id: config.php 7968 2014-03-28 07:36:51Z smallduh $

require_once "./module-cfg.php";

include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";



//取得模組參數設定
$m_arr = &get_module_setup("fixed");
extract($m_arr, EXTR_OVERWRITE);

$PHP_SELF = $_SERVER["PHP_SELF"] ;

$unitstr = get_Unit_Name() ;

function get_Unit_Name() {
  global  $CONN ;     
  //取得各單位代號、中文名 fixed_kind 中取得
  $sqlstr = "SELECT bk_id ,board_name  FROM fixed_kind " ;
  $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
  $i = 0 ;
  if ($result) 
    
    while ($nb=$result->FetchRow()) {     
        
      $bk_id = $nb["bk_id"] ;
      $board_name = $nb["board_name"];	
      $Unit[$bk_id] = $board_name ;

    } 
    return $Unit ;
}    
  
function get_Unit_Email_list() {
  global  $CONN ;     
  //取得各單位代號、中文名 fixed_kind 中取得
  $sqlstr = "SELECT bk_id ,Email_list  FROM fixed_kind " ;
  $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
  $i = 0 ;
  if ($result) 
    
    while ($nb=$result->FetchRow()) {     
        
      $bk_id = $nb["bk_id"] ;
      $Email_list = $nb["Email_list"];	
      $Unit[$bk_id] = $Email_list ;

    } 
    return $Unit ;
}    

function board_checkid($chk){
	global $CONN ;
	$chkary= explode ("/",$chk);
	$pp	= $chkary[count($chkary)-2];
	$post_office = -1;
	$teach_title_id = -1;
	$teach_id = -1 ;
	$dbquery = " select a.teacher_sn,a.login_pass,a.name,b.post_office,b.teach_title_id ";
	$dbquery .="from teacher_base a ,teacher_post b  "; 
	$dbquery .="where a.teacher_sn = b.teacher_sn and a.teacher_sn='$_SESSION[session_tea_sn]'"; 
	$result= $CONN->Execute($dbquery) or user_error("讀取失敗！<br>$dbquery",256) ; 
	
	if ($result->RecordCount() > 0){
		$row = $nb=$result->FetchRow();
		$post_office = $row["post_office"];
		$teach_title_id	= $row["teach_title_id"];
		$teacher_sn =	$row["teacher_sn"];
	
		$dbquery = "select pro_kind_id from fixed_check where pro_kind_id ='$chk' and (post_office='$post_office' or post_office='99' or teach_title_id='$teach_title_id' or teacher_sn='$teacher_sn')";

		$result=$CONN->Execute($dbquery) or user_error("讀取失敗！<br>$dbquery",256) ; 
		if ($result->RecordCount() > 0)	{
			return true;
		}
		else
			return false;
	}
	else
		return false;
}

//由 teacher_id 取得 E-mail 設定
function get_teacher_email_by_id($teach_id){
	$MYEMAIL="";
	$query="select b.email,b.email2,b.email3 from teacher_base a,teacher_connect b where a.teacher_sn=b.teacher_sn and a.teach_id='$teach_id'";
	$result=mysql_query($query);
	list($email,$email2,$email3)=mysql_fetch_row($result);
	$MYEMAIL=($email=="")?$email2:$email;
	if ($MYEMAIL=="") $MYEMAIL=$email3;
  
  return $MYEMAIL;
  
}

?>
