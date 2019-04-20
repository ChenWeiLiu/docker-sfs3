<?php
                                                                                                                             
// $Id: book_config.php 8753 2016-01-13 12:40:19Z qfon $

	/** 學務管理設定 **/
	require_once "../../include/config.php";
	require_once "../../include/sfs_case_PLlib.php";

	include "./module-upgrade.php";
	//取得模組設定
	$m_arr = get_sfs_module_set("book");
	extract($m_arr, EXTR_OVERWRITE);	

	$pic_width=$pic_width?$pic_width:64;
	
	 //借還書程式限定IP範圍
	 $man_ip = array($man_ip1,$man_ip2,$man_ip3);

	/* 上傳檔案目的目錄 */
	$path_str = "school/book";
	set_upload_path($path_str);
	$upload_path = $UPLOAD_PATH.$path_str;

	/*下載路徑 */
	$download_url = $UPLOAD_URL.$path_str;

//圖書條碼函式
function  barcode($text) { 
	$enc_text  =  urlencode($text); 	
	echo  "<img  src=\"barcode.php?code=$enc_text\" border=0 Alt=\"$text\">"; 
} 
//取得圖書室介紹檔

function get_booksay_option(){
global $CONN;
//建立表格
$CONN->Execute("
CREATE TABLE if not EXISTS `book_say` (
`bs_id` INT NOT NULL AUTO_INCREMENT,
`bs_title` VARCHAR( 30 ) NOT NULL ,
`bs_con` TEXT NOT NULL ,
`us_id` VARCHAR( 20 ) NOT NULL ,
`create_time` TIMESTAMP NOT NULL ,
PRIMARY KEY ( `bs_id` )
)");

$query = "select count(*) from book_say ";
$res = $CONN->Execute($query) or trigger_error("查詢錯誤 $query",E_USER_ERROR);
//加入預設值
if($res->fields[0]==0){
	$con=addslashes(read_file("b_begin.htm"));
	$CONN->Execute("insert into book_say(bs_title,bs_con)values('緣起','$con')");
	$con= addslashes(read_file("b_order.htm"));
	$str = addslashes ('圖書借閱規則');
	$CONN->Execute("insert into book_say(bs_title,bs_con)values('$str','$con')");
	$con=addslashes(read_file("b_teacher.htm"));
	$CONN->Execute("insert into book_say(bs_title,bs_con)values('圖書管理老師','$con')");
	$con=addslashes(read_file("b_student.htm"));
	$CONN->Execute("insert into book_say(bs_title,bs_con)values('圖書管理小義工','$con')");
}

$query = "select bs_id,bs_title from book_say order by bs_id";
$res = $CONN->Execute($query) or trigger_error("查詢錯誤 $query",E_USER_ERROR);
while(!$res->EOF){
	$bs_id = $res->fields[bs_id];
	$bs_title = $res->fields[bs_title];
	$say_file .="<OPTION VALUE=\"booksay.php?sel=$bs_id\">$bs_title</OPTION>";
	$res->MoveNext();
}
return $say_file;
}

function check_mysqli_param($param){
	if (!isset($param))$param="";
	return $param;
}

?>
