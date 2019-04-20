<?php

//$Id: doc_download.php 8613 2015-11-27 06:24:31Z smallduh $

include "docup_config.php";
//送出檔案
if (!empty($_GET[profile])){
	 $file = $temp_filePath.$_GET[profile];
        $file_path = $UPLOAD_URL."school/docup/".$_GET[profile];

        header("Location: $file_path");
/*
	$file = $temp_filePath.$_GET[profile];
	$docup_store =  $_GET[profile];
	 Header ( "Content-Type: application/octet-stream"); 
	 Header ( "Content-Length: ".filesize($file)); 
	 Header ( "Content-Disposition: attachment; filename=$docup_store"); 
	 readfile($file);
  	 unlink($file); 
	 // debug 2003/05/01 added by OLS3
	 // 加上以下 exit，似已解決 head 送出後，繼續執行至 line 17 的現象。
*/
	 exit;
}

if ($_GET[docup_id]=='')
	header ("Location: index.php");
	$int_docup_id=(int)$_GET[docup_id];
$sql_select = "select a.docup_owerid,a.docup_share,a.teacher_sn ,a.docup_id,a.docup_store,b.doc_kind_id from docup a,docup_p b where a.docup_p_id = b.docup_p_id and a.docup_id='$int_docup_id'";
$result = $CONN->Execute ($sql_select) or trigger_error("  SQL 錯誤! $sql_select",E_USER_ERROR);
$docup_store = $result->fields["docup_store"];
$docup_share = $result->fields["docup_share"];
$doc_kind_id = $result->fields["doc_kind_id"];
$teach_sn = $result->fields["teacher_sn"];
$teach_id = $result->fields["docup_owerid"];
$total = $teach_id."_".$result->fields["docup_id"]."_".$result->fields["docup_store"];

session_start();
//取得登入人員所在處室
$post_office = "";
if($_SESSION[session_tea_sn] !=""){
	$query = "select post_office from teacher_post where teach_id='$teach_id' ";
	$result = $CONN->Execute($query);
	$post_office = $result->fields[0];
}


$is_download = false;

//判斷權限
//檔案所有人
if(!empty($_SESSION[session_tea_sn]) and ($_SESSION[session_tea_sn] == $teach_sn ||  checkid($_SERVER[SCRIPT_FILENAME],1) )) {
	$is_download = true;
}
//本處室人員權限
else if ($_SESSION[session_tea_sn] !="" && $post_office == $doc_kind_id){
	if (getperr($docup_share,1,1)){
		$is_download = true;
	}
}
//本校登入人員
else if ($_SESSION[session_tea_sn] !=""){ 
	if (getperr($docup_share,2,1)) {
		$is_download = true;
	}
}
else if (getperr($docup_share,3,1)){
	$is_download = true;
}

if($is_download) {
	//$source_total = $filePath.$total;
//	$temp_total = $temp_filePath.$docup_store;
//	copy($source_total,$temp_total);
//	if (copy($source_total,$temp_total)){
		header("Location: doc_download.php?profile=$total");
//	}
	
}
else {
	echo "權限錯誤!!";
	exit;
}

 ?>
