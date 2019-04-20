<?php

// $Id: sfs_oo_db.php 5310 2009-01-10 07:57:56Z hami $
// 取代 DBClass.php

// 村仔於90.09.30訂正版
class DBClass {
	var $db ;
	var $server;
	var $stduser;
	var $stdpass;

function Recordset($SQL) {
	if (($this->stdpass != "") && ($this->stduser != "") && ($this->server != "")) {
	$link = @mysql_connect($this->server, $this->stduser, $this->stdpass) or  $this->DBDie("無法連接資料庫,請檢查連線資訊!<BR>"); 
	}
//	mysql_select_db($this->db);
	$rs = mysql_db_query($this->db, $SQL) or $this->DBDie($SQL."<BR>無法連接資料庫,請檢查資料庫名稱是否設定正確!<BR>");
//	$rs = mysql_query($SQL ,$link) or $this->DBDie();
	return $rs;// 
	if (($this->stdpass != "") && ($this->stduser != "") && ($this->server != "")) { 
	$this->DBClose($link); 
	}
}

function Execute($SQL) {
	if (($this->stdpass != "") && ($this->stduser != "") && ($this->server != "")) { 
	$link = @mysql_connect($this->server, $this->stduser, $this->stdpass)  or  $this->DBDie("無法連接資料庫,請檢查連線資訊!<BR>");
	}
	$rs = mysql_db_query($this->db, $SQL) or $this->DBDie("資料庫名稱或指令敘述錯誤!<BR> $SQL ");
	return mysql_insert_id();
	if (($this->stdpass != "") && ($this->stduser != "") && ($this->server != "")) {
		$this->DBClose($link); 
	}
}

function RecordCount($rs) {
	if (!$rs) {return $this->DBDie($SQL."沒有連結指標!無法計數!<BR> ");exit;}
	return mysql_num_rows($rs) ;
	}

function GetRows($rs) {
	if (!$rs) {return $this->DBDie("沒有連結指標!無法傳回資料!<BR>");exit;}
	$arr = array();
	$counter = 0;
	while ($row = mysql_fetch_array($rs)) {
	$arr[$counter] = $row;
	$counter++;
	}
	return $arr;
}

function GetString($rs, $col, $row) {
	$return_str = "";
	while($thisRow = mysql_fetch_row($rs)) {
	$return_str .= join($col, $thisRow).$row;
	}
	return $return_str;
}

function GetFieldCount($rs) {
if (!$rs) {return $this->DBDie("沒有連結指標!無法計數!<BR>");exit;}
	return mysql_num_fields($rs);
}

function DBClose($link) {
	mysql_close($link);
}

function DBDie($error) {
	echo $error;exit;
	}
}
?>
