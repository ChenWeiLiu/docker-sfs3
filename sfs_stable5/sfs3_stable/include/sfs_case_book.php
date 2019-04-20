<?php
// $Id: sfs_case_book.php 5351 2009-01-20 00:39:21Z brucelyc $

function book_check($go_back=1) {
	global $CONN,$SFS_TEMPLATE,$SFS_PATH_HTML,$THEME_FILE,$SFS_THEME,$smarty;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	if(!empty($_REQUEST[_Msn])){
		$curr_msn=$_REQUEST[_Msn];
	}elseif(empty($_REQUEST[_Msn]) and $_SERVER[SCRIPT_NAME]!="/index.php"){
		$SCRIPT_NAME=$_SERVER[SCRIPT_NAME];
		$SN=explode("/",$SCRIPT_NAME);
		$m=getDBdata("",$SN[count($SN)-2]);
		$curr_msn=$m[msn];
	}
	
	if(!checkid($_SERVER[SCRIPT_NAME])){
		$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE."/book");
		$smarty->assign("BOOK_URL",$SFS_PATH_HTML."templates/$SFS_THEME/book");
		$smarty->display("$SFS_TEMPLATE/book/book_login.tpl");
		exit;	
	}
}
?>
