<?php

// $Id: sfs_core_log.php 6864 2012-08-27 05:32:39Z hsiao $

function sfs_log($log_table,$update_kind,$chang_id='') {
	global $CONN,$REMOTE_ADDR;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if ($_SESSION['session_log_id'] !='' and  $log_table!='') {
		$sql_insert = "insert into sfs_log (log_user,log_table,log_ip,update_kind,chang_id) values ('{$_SESSION['session_log_id']}','$log_table','$REMOTE_ADDR','$update_kind','$chang_id')";
		//echo $sql_insert;
		$CONN->Execute($sql_insert) or trigger_error("log存入失敗： $sql_insert", E_USER_ERROR);
	}
	return;
}


//比較簡易，並可儲存大量中文說明的log紀錄
function add_log($log,$mark) {
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if ($_SESSION['session_log_id'] !='') {
		$log=addslashes($log);
		$sql_insert = "insert into sfs3_log (log,mark,id,time) values ('$log','$mark','{$_SESSION['session_log_id']}',now())";
		$CONN->Execute($sql_insert) or trigger_error("log存入失敗： $sql_insert", E_USER_ERROR);
	}
	$n=mysql_insert_id();
	return $n;
}

//觀看sfs3_log的紀錄資料
function &view_log($mark="",$log_sn="",$show_back=true) {
	global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if ($_SESSION['session_log_id'] !='') {
		$log=addslashes($log);
		$where=(empty($log_sn))?"mark='$mark'":"log_sn='$log_sn'";

		$sql_select = "select log,id,time from sfs3_log where $where";
		 $recordSet=$CONN->Execute($sql_select) or trigger_error("log存入失敗： $sql_select", E_USER_ERROR);
		while(list($log,$id,$time) = $recordSet->FetchRow()){
			$main.="<table  cellspacing='1' cellpadding='8' align='center' bgcolor='#8080FF'>
			<tr bgcolor='#D9CEF9'><td class='small' >
			於 $time 由 $id 存入的紀錄如下：
			</td></tr>
			<tr bgcolor='#FFFFFF'><td class='small' style='line-height: 160%;'>$log</td></tr></table>";
		}
		if($show_back){
			$main.="<div align='right'><a href='javascript:history.back(-1);'>回上一頁</a></div>";
		}
	}
	return $main;;
}

//寫入個資存取記錄
function pipa_log($comment) {
	global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if ($_SESSION['session_tea_sn'] !='') {
		$remote_ip=getip();
		$teacher_name=addslashes($_SESSION['session_tea_name']);
		$sql="INSERT INTO pipa SET teacher_sn={$_SESSION['session_tea_sn']},teacher_name='$teacher_name',ip='$remote_ip',script_name='{$_SERVER['SCRIPT_NAME']}',comment='$comment';";
		$rs=$CONN->Execute($sql) or trigger_error("存入pipa_log失敗： $sql", E_USER_ERROR);		
	}
	return $rs;
}

?>
