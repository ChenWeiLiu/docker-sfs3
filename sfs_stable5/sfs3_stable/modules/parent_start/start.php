<?php
// $Id: start.php 8811 2016-02-05 17:04:03Z qfon $
// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 叫用 SFS3 的版頭
head("啟動家長帳號");

// 認證
//sfs_check();

//mysqli
$mysqliconn = get_mysqli_conn("");
 

//
// 您的程式碼由此開始

//全域變數轉換區*****************************************************
$submit=($_GET['submit'])?$_GET['submit']:$_POST['submit'];
$parent_id=($_GET['parent_id'])?$_GET['parent_id']:$_POST['parent_id'];
$start_code=($_GET['start_code'])?$_GET['start_code']:$_POST['start_code'];
$email=($_GET['email'])?$_GET['email']:$_POST['email'];


//********************************************************************

//橫向選單標籤
//echo print_menu($MENU_P);

if($submit=="確定"){
	//檢查送出的資料是否正確
	$parent_id=trim($parent_id);
	$start_code-trim($start_code);
	$email=trim($email);
	if(!checkemail($email)) { echo "您所輸入的email位置不正確，請回<span class='button'><a href='javascript:history.back(-1);'>上一頁</a></span>重新輸入";  exit;}
	/*
	$sql="select count(*) from parent_auth where parent_id='$parent_id' and start_code='$start_code' and enable='1'";
	$rs=$CONN->Execute($sql) or die($sql);
	$CK=$rs->fields[0];
     */
//mysqli	
$sql="select count(*) from parent_auth where parent_id=? and start_code=? and enable='1'";
$stmt = "";
$stmt = $mysqliconn->prepare($sql);
$stmt->bind_param('ss',$parent_id,$start_code);
$stmt->execute();
$stmt->bind_result($CK);
$stmt->fetch();
$stmt->close();
//mysqli	
	
	if($CK=="1"){
		//寫入資料表，並啟動該帳號和寄出mail
		$new_pass=creat_code($level="2",$many_char="8");
		/*
		$upd_sql="update parent_auth set enable='2' , login_id='$parent_id' , parent_pass='$new_pass' , email='$email' where parent_id='$parent_id' and start_code='$start_code' ";
		$CONN->Execute($upd_sql) or die($upd_sql);
		*/

//mysqli	
$upd_sql="update parent_auth set enable='2' , login_id=? , parent_pass=? , email=? where parent_id=? and start_code=? ";
$stmt = "";
$stmt = $mysqliconn->prepare($upd_sql);
$stmt->bind_param('sssss',check_mysqli_param($parent_id),check_mysqli_param($new_pass),check_mysqli_param($email),check_mysqli_param($parent_id),check_mysqli_param($start_code));
$stmt->execute();
$stmt->close();
///mysqli		
		
		
		//開始寄信
		$user_parent=$email;
		$mail_subject="來自".$school_short_name."學務系統的信件";
		$mail_message="貴家長：\n您的".$school_short_name."學務系統帳號已經啟動\n
帳號：$parent_id\n密碼：$new_pass\n
請立即至『<a href='$SFS_PATH_HTML'>親職通聯</a>』修改您的帳號密碼，並不定時加以修正，以防帳號被他人取得！\n
".$school_short_name."學務系統敬上";
		
		mail($user_parent, $mail_subject, $mail_message);
		
		//顯示訊息給使用者觀看
		echo "您的帳號已經啟用且寄出，請用您剛才輸入的EMAIL位址收信，並立即至<span class='button'><a href='../parent'>親職通聯</a></span>更改您的帳號密碼";
	}
	else{		
		echo "您所輸入的資料有誤，請回<span class='button'><a href='javascript:history.back(-1);'>上一頁</a></span>重新輸入";
	}
}
else{
	$form="<table cellspacing='1' cellpadding='6' border='0' bgcolor='#FFCAF8'><form name='start_form' method='post' action='{$_SERVER['PHP_SELF']}'>
				<tr bgcolor='#F2A3FD'><td>監護人身分證字號</td><td><input type='text' name='parent_id' size=10 maxlength=10></td></tr>
				<tr bgcolor='#F2A3FD'><td>啟動碼</td><td><input type='text' name='start_code' size=10 maxlength=10></td></tr>
				<tr bgcolor='#F2A3FD'><td>E-mail</td><td><input type='text' name='email' size=20 maxlength=60></td></tr>
				<tr bgcolor='#F2A3FD'><td colspan=2 align='center'><input type='submit' name='submit' value='確定'></td></tr>
			</table></form>";
	echo $form;
}
// SFS3 的版尾
foot();


function checkemail($email){
   if (eregi("themail.com",$email)) return 0;
   if (!$email) return 0;
   $a=split('@',$email);
   if (stripslashes($a[0])!=$a[0]) return 0;
   if (ereg_replace("[[:alnum:]._-]+","",$a[0])!="") return 0;
   if (!$a[1]) return 0;
   //傳回hostname的IP位址
   $b=@gethostbyname($a[1]);    
   if ($b!=$a[1]) return 1;
   //搜尋DNS型態MX的記錄，如果找到記錄則傳回true
   if (checkdnsrr($a[1])) return 1;
   return 0;
}

function check_mysqli_param($param){
	if (!isset($param))$param="";
	return $param;
}

?>

