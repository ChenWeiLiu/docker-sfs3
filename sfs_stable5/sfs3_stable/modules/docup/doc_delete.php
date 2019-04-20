<?php

//$Id: doc_delete.php 5310 2009-01-10 07:57:56Z hami $

//設定檔載入檢查
include "docup_config.php";
// --認證 session 
sfs_check();

if (! checkid($_SERVER[SCRIPT_FILENAME],1)) { //非管理者
	//------------------------
	//取得登入人員所在處室
	$post_office = "";
	if($_SESSION[session_tea_sn] !=""){
		$query = "select post_office from teacher_post where teach_id='$_SESSION[session_tea_sn]' ";
		$res = $CONN->Execute($query);
		$post_office = $res->fields[0];
	}
	//------------------------
	//檢查修改權
	$log_flag = false;
	$query = "select a.teacher_sn,a.docup_share,b.doc_kind_id from docup a, docup_p b where a.docup_p_id = b.docup_p_id and a.docup_id='$_POST[docup_id]'";
	$result = $CONN->Execute($query);
	$teacher_sn = $res->fields[teacher_sn];
	$docup_share = $res->fields[docup_share];
	$doc_kind_id = $res->fields[doc_kind_id];

	//檔案所有人
	if ($_SERVER[session_tea_sn] == $teacher_sn)
		$log_flag = true;
	//所在處室人員
	else if ($_SERVER[session_tea_sn] !="" && $post_office == $doc_kind_id)
		if (getperr($docup_share,1,3)) //刪除權
			$log_flag = true;
	//本校人員
	else if ($_SERVER[session_tea_sn] !="")
		if (getperr($docup_share,2,3)) //刪除權
			$log_flag = true;
		
	if ($log_flag == false){
		echo "沒有權限刪除本文件";
		exit;
	}
}

if ($_POST[key] == "確定刪除"){
	$sql_delete = "delete from  docup where docup_id = '$_POST[docup_id]'";
	$CONN->Execute($sql_delete);
	$query = "select count(docup_id) as cc from docup where docup_p_id='$_POST[docup_p_id]'";
	$result = $CONN->Execute($query)or die ($query);
	$query = "update docup_p set docup_p_count = ".$result->fields[0]." where docup_p_id='$_POST[docup_p_id]'";
	$CONN->Execute($query)or die ($query);
	$alias = $filePath."/".$_SESSION[session_log_id]."_".$_POST[docup_id]."_".$_POST[docup_store];  
	if (file_exists($alias))
		unlink($alias);
	header ("Location: doc_list.php?docup_p_id=$_POST[docup_p_id]&doc_kind_id=$_POST[doc_kind_id]");
}

if ($is_standalone!="1") head("文件資料庫");
	$query = "select * from docup where docup_id ='$_GET[docup_id]'";
	$result = $CONN->Execute($query);
	$docup_name = $result->fields["docup_name"];
	$docup_p_id = $result->fields["docup_p_id"];
	$docup_store = $result->fields["docup_store"];
	echo "<center><b>$docup_name"."將被刪除</b>\n";
	echo "<form active=\"$PHP_SELF\" method=post>\n";
	echo "<input type=hidden name=docup_id value=$_GET[docup_id]>\n";
	echo "<input type=hidden name=docup_p_id value=$docup_p_id>\n";
	echo "<input type=hidden name=docup_store value=\"$docup_store\">\n";
	echo "<input type=hidden name=doc_kind_id value=\"$_GET[doc_kind_id]\">\n";
	echo "<input type=submit name=key value=\"確定刪除\">\n";
	echo "&nbsp;&nbsp;<input type=button value=\"回上一頁\" onclick=\"javascript:history.back()\">\n";
	echo "</form></center>";
if ($is_standalone!="1") foot();
?>
