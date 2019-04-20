<?php

// $Id: index.php 7696 2013-10-23 08:04:10Z smallduh $

include "config.php";

sfs_check();


if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


//執行動作判斷
if($act=="下載資料備份"){
	download_sql($tbl_name);
	exit;
}else{
	$main=pre_form();
}


//秀出網頁
head("學務系統資料備份");
echo $main;
foot();


/*
函式區
*/

//基本設定表單
function pre_form(){
	global $school_menu_p,$mysql_db,$CONN;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	//取得所有表單名稱
	$result = mysql_list_tables($mysql_db);

	if (!$result) {
		user_error("無法取得資料表資料。",256);
	}
	$n=3;
	$i=$n+1;
	while ($row=mysql_fetch_row($result)) {
		$tr1=(($i%$n)==1)?"<tr bgcolor='#F8F8F8'>":"";
		$tr2=(($i%$n)==0)?"</tr>":"";
		$option.="$tr1<td><input type='checkbox' name='tbl_name[]' value='$row[0]' checked>
		<font size=2 face='arial'>$row[0]</font></td>$tr2";
		$i++;
	}

	mysql_free_result($result);

	$main="
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td>
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=1>

	<form method='post' action='$_SERVER[PHP_SELF]' name='db_dump'>
	$option
    	</table>
	</td><td valign='top'>
	<input type='submit'  name='act' value='下載資料備份' /></td></tr></table>
	</form>
	";
	return $main;
}


//下載資料備份
function download_sql($tbl_name){
	global $CONN,$mysql_db,$SFS_PATH,$UPLOAD_PATH;
	$today=date("YmdHi");
	for($i=0;$i<sizeof($tbl_name);$i++){
		$data.=table_data($tbl_name[$i]);
	}
	$filename="backup_".$mysql_db."_".$today;
	/*
	//備份到主機上
	if(!opendir($UPLOAD_PATH."backup")) mkdir ($UPLOAD_PATH."backup", "0700");

	$full_filename=$UPLOAD_PATH."backup/".$filename;
	$fp=fopen($full_filename,"w");
	fwrite($fp,$data);
	fclose($fp);
	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$filename);
	echo $data;
	exit;
	*/
	header("Content-Disposition: attachment; filename=".$filename);
	header("Content-type: text/plain");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	
	header("Expires: 0");
	echo $data;
	exit;
	
}




?>
