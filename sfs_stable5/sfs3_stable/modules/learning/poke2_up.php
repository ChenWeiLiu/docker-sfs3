<?php

include "config.php";
session_start();

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
 	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


if ($act=="批次建立資料"){
	$msg="";
	echo $_FILES['userdata']['name'];
	//$msg=import($_FILES['userdata']['tmp_name'],$_FILES['userdata']['name'],$_FILES['userdata']['size']);
	$userdata_size=$_FILES['userdata']['size'];
	$userdata_name=$_FILES['userdata']['name'];
	$userdata=$_FILES['userdata']['tmp_name'];
}else{
	 echo "	<table border='0' cellspacing='0' cellpadding='0' >
	<tr><td valign=top>
		<table cellspacing='1' cellpadding='10' class=main_body >
		<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
		<tr><td  nowrap valign='top' bgcolor='#E1ECFF'>
		<p>請按『瀏覽』選擇匯入檔案來源：</p>
		<input type=file name='userdata'>
		<p><input type=submit name='act' value='批次建立資料'></p>
		<input type='hidden' name='unit' value='$unit'>	
		</td>
		<td valign='top' bgcolor='#FFFFFF'>
	
		</td>
		</tr>
		</table>
	</form>
	</td></tr></table>
	";
}

