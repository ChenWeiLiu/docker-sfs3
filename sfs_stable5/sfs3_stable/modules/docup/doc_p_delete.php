<?php

//$Id: doc_p_delete.php 5310 2009-01-10 07:57:56Z hami $

//設定檔載入檢查
include "docup_config.php";
// --認證 session 
sfs_check();
//------------------------
if ($key == "確定刪除"){
	$sql_delete = "delete from  docup_p where docup_p_id = '$docup_p_id'";
	$result = $CONN->Execute($sql_delete);
	$sql_delete = "delete from  docup where docup_p_id = '$docup_p_id'";
	$result = $CONN->Execute($sql_delete);
	$alias = $filePath."/".$session_tea_sn."_".$docup_p_id."_*";
	if (file_exists($alias))
		unlink($alias)or die($alias);
	if ($doc_kind_id != "")	
		header ("Location: doc_kind_list.php?doc_kind_id=$doc_kind_id");
	else
		header ("Location: doc_p_list.php");
}
if ($is_standalone!="1") head("文件資料庫");
	echo "<b>$docup_p_name"."將被刪除</b>\n";
	echo "<form active=\"$SCRIPT_NAME\" method=post>\n";
	echo "<input type=hidden name=docup_p_id value=$docup_p_id>\n";
	echo "<input type=submit name=key value=\"確定刪除\">\n";
	echo "</form></center>";
if ($is_standalone!="1") foot();
?>