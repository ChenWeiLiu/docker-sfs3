<?php

// $Id: config.php 8705 2015-12-29 03:03:33Z qfon $

require_once "./module-cfg.php";
/* 學務系統設定檔 */
require_once "../../include/config.php";
/* 學務系統函式庫 */
require_once "../../include/sfs_case_PLlib.php";
/* 取得全域變數值 */
require_once "../../include/sfs_core_globals.php";

/* 上傳檔案目的目錄 */
$path_str = "unit/";
set_upload_path($path_str);
$USR_DESTINATION = $UPLOAD_PATH.$path_str;
$download_path=$UPLOAD_URL."unit/";
/*線上測驗下載路徑 */
$downtest_path = $UPLOAD_URL."test/";
$TES_DESTINATION = $UPLOAD_PATH."test/";
//領域名稱陣列
$modules_s = array("a"=>"國語","b"=>"數學","c"=>"自然","d"=>"社會","e"=>"藝文","f"=>"生活","g"=>"健體","h"=>"綜合","i"=>"鄉土","j"=>"英語","k"=>"主題");
//$modules_s = array("k"=>"資訊","l"=>"兩性","m"=>"人權","n"=>"生涯","o"=>"環境");  //六大議題


//領域名稱陣列
$modules = array("a"=>"國語文","b"=>"數學","c"=>"自然與生活科技","d"=>"社會","e"=>"藝術與人文","f"=>"生活","g"=>"健康與體育","h"=>"綜合活動","i"=>"鄉土語言","j"=>"英語","k"=>"主題學習");

//領域名稱陣列
$entry_s = array("a"=>"單元公告","b"=>"教材內容","c"=>"教學檔案","d"=>"討論互動","e"=>"參考網站");
// $entry_s = array("a"=>"單元公告","b"=>"教材內容","c"=>"教學檔案","d"=>"討論互動","e"=>"參考網站","f"=>"線上測驗");
$note_s = array(
"a"=>"　　歡迎進入本學習資源網，本網站是以各領域、各單元為規畫，提供大家一個整理數位教學資料的園地，您可以在課前將各項教材或檔案貼上、上傳或做好連結，同時與別人分享，上課時就可使用
隨手可得的各項資源，也可讓同學們自修或參考。<br>
	　　本站限校內師生家長進入，各項資料不對外開放，所有內容的智慧財產權屬原作者，除上課教學外，不得做其它用途。<br>
	　　請大家一起來充實本園地吧！",
"b"=>"教材內容是指上課時可能會用到的任何內容，比如一段文字說明、一張圖片、一段影音，如果是一個連結，希望是直接連到我們所要的頁面，而不是網站的目錄。
	<br>我們希望能有系統的整理歸類，讓大家使用時更方便。",
"c"=>"教學檔案是指較大、較複雜的內容，如整個教案、投影片、互動軟體等，也希望是直接可以開啟或下載使用的。",
"d"=>"討論互動區是提供師生互動的管道，老師可以出作業題目，學生線上回答，或把習作內容全部輸入，也是一個不錯的主意。",
"e"=>"參考網站就是另外提供參考的網站，只要提供網址連結就好了吧！");


//檢查是否為內部 ip
function check_ip() {
	global $man_ip,$REMOTE_ADDR ;
	$is_intranet = false;
	for($mi=0 ; $mi< count($man_ip) ;$mi++){
		$ee = explode ('.',$man_ip[$mi]);
		if ((count($ee) == 4 && $man_ip[$mi] == $REMOTE_ADDR) || count($ee) < 4 && $man_ip[$mi] == substr($REMOTE_ADDR,0,strlen($man_ip[$mi]))){
			$is_intranet = true;
			break;
		}		
	}
	return $is_intranet;
}

function redir_str( $surl ,$str="",$sec) {
  //等 $sec 秒後，轉向到$sul 網頁，必須放在 header部份
  print("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\">");
  print("<meta http-equiv=\"refresh\" content=\"" . $sec . "; url=" . $surl ."\">  \n" ) ; 
  print("</head><body>");
  print($str);
  print("</body></html>");
  //備註，php 中函數 Header("Location:cal2.php") ;會馬上轉址，無法出現訊息及等待
}

//檢查使用權
function board_checkid($chk){
	global $conID,$session_log_id ,$app_name,$session_tea_sn;
if($_SESSION['session_who'] =="學生"){	
	$dbquery = "select pro_kind_id from board_check where pro_kind_id ='$chk' and teach_id='$_SESSION[session_log_id]'";
		$result= mysql_query($dbquery,$conID)or die("<br>資料庫連結錯誤<br>\n $dbquery");
		if (mysql_num_rows ($result)>0)	{
			return true;
		}
		else
			return false;

	return true;	
	
}else{
	$chkary= explode ("/",$chk);
	$pp	= $chkary[count($chkary)-2];
	$post_office = -1;
	$teach_title_id = -1;
	$teach_id = -1 ;
	$dbquery = " select a.teacher_sn,a.login_pass,a.name,b.post_office,b.teach_title_id ";
	$dbquery .="from teacher_base a ,teacher_post b  "; 
	$dbquery .="where a.teacher_sn = b.teacher_sn and a.teacher_sn='$_SESSION[session_tea_sn]'"; 
	$result= mysql_query($dbquery,$conID)or ("<br>資料連結錯誤<br>\n $dbquery");
	
	if (mysql_num_rows($result) > 0){
		$row = mysql_fetch_array($result);
		$post_office = $row["post_office"];
		$teach_title_id	= $row["teach_title_id"];
		$teacher_sn =	$row["teacher_sn"];
	
		$dbquery = "select pro_kind_id from board_check where pro_kind_id ='$chk' and (post_office='$post_office' or post_office='99' or teach_title_id='$teach_title_id' or teacher_sn='$teacher_sn' )";

		$result= mysql_query($dbquery,$conID)or die("<br>資料庫連結錯誤<br>\n $dbquery");
		if (mysql_num_rows ($result)>0)	{
			return true;
		}
		else
			return false;
	}
	else
		return false;
}
}
//檢查目前學年
function stud_ye($stud_id){
	global $CONN;
    $stud_id=substr($stud_id,0,7);
	$rs_sn=$CONN->Execute("select curr_class_num from stud_base where stud_id='$stud_id'") or trigger_error("SQL語法錯誤 ", E_USER_ERROR);
	$stud_year= substr($rs_sn->fields["curr_class_num"],0,1);

	if($stud_year=='')
		$stud_year=1;
	return $stud_year;
}
?>
