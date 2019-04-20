<?php

// $Id: sfs_core_msg.php 5310 2009-01-10 07:57:56Z hami $

//錯誤訊息格式
function &error_tbl($error_title,$error_main){
	
	$main=
	"<table width='90%' align='center' cellspacing='1' cellpadding='10' bgcolor='red'>
	<tr bgcolor='white'><td style='line-height: 1.5; '>
	<div align='center' style='font-weight: bold; background-color: #fff158; padding: 5px; font-size: 20pt'>$error_title</div>
	$error_main
	<div align='right'><a href='javascript:history.back(-1);'>回上一頁</a></div></td></tr></table>
	";
	return $main;
}

//說明格式
function &help($help_text,$help_title="相關說明"){
	global $SFS_PATH_HTML;
	$txt=explode("||",$help_text);
	$all_text="";
	for($i=0;$i<sizeof($txt);$i++){
		$all_text.="<li class='small'>".$txt[$i]."</li>";
	}
	$main="
	<table>
	<tr bgcolor='#FBFBC4'><td><img src='".$SFS_PATH_HTML."images/filefind.png' width=16 height=16 hspace=3 border=0>$help_title</td></tr>
	<tr><td style='line-height: 150%;'>
	<ol>
	$all_text
	</ol>
	</td></tr>
	</table>
	";
	return $main;
}


// 檢查 register_global
function check_phpini_register_globals() {
	global $SFS_NEED_REGISTER_GLOBALS;	
	if ($SFS_NEED_REGISTER_GLOBALS) {
	  if (!ini_get('register_globals')) {
	  	trigger_error("您的 php.ini 中未打開變數全域設定，請設妥 register_globals=On，並重新啟動 Apache！", E_USER_ERROR);

	  } 
	}
	else {
 	  if (ini_get('register_globals')) {
	  	trigger_error("您好! 目前 SFS 不需要打開全域變數設定，但您的 php.ini 中有打開變數全域設定，請設妥 register_globals=Off，並重新啟動 Apache！", E_USER_ERROR);

	  } 
	}
}


?>