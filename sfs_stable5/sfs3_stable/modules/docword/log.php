<?php

// $Id: log.php 6805 2012-06-22 08:00:32Z smallduh $

//載入設定檔
include "docword_config.php";
// session 認證

//session_register("session_log_id");
//session_register("session_tea_name");
if ($sel == "out") {//登出
	$session_log_id="";
	$session_tea_name="";
}
else if ($sel == "in") { //登入
	if(!checkid($PHP_SELF)){
		$go_back=1; //回到自已的認證畫面
		include "header.php";
		include $SFS_PATH."/rlogin.php";
		include "footer.php";
		exit;
	}
}
header ("Location: index.php");
?>
