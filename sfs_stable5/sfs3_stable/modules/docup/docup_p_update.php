<?php

//$Id: docup_p_update.php 8144 2014-09-23 08:19:00Z smallduh $

//設定檔載入檢查
include "docup_config.php";
// --認證 session 
sfs_check();

$docup_p_id = $_POST[docup_p_id];
if (empty($docup_p_id))
	$docup_p_id = $_GET[docup_p_id];

//檢查修改權
$query = "select docup_p_owner from docup_p where docup_p_id='$docup_p_id' and teacher_sn='$_SESSION[session_tea_sn]'";
$result = $CONN->Execute($query) or trigger_error("語法錯誤 !",E_USER_ERROR);
if ($result->RecordCount() == 0 && !  checkid($_SERVER[SCRIPT_FILENAME],1)){
	echo "沒有權限修改本專案";
	exit;
}

if ($_POST[key] == "修改"){
	$sql_update = "update docup_p set doc_kind_id='$_POST[doc_kind_id]',docup_p_id='$_POST[docup_p_id]',docup_p_date='$now',docup_p_name='$_POST[docup_p_name]',docup_p_memo='$_POST[docup_p_memo]',docup_p_owner='$_SESSION[session_tea_name]' where docup_p_id = '$_POST[docup_p_id]'";
	$CONN->Execute($sql_update) or trigger_error("語法錯誤 !",E_USER_ERROR);

	if ($_POST[doc_kind_id] != "")	
		header ("Location: doc_kind_list.php?doc_kind_id=$_POST[doc_kind_id]");
	else
		header ("Location: doc_p_list.php");
}

else if ($_POST[key] == "刪除"){
	head();
	echo "<center><b>$_POST[docup_p_name]"."將被刪除</b>\n";
	echo "<form active=\"$_SERVER[PHP_SELF]\" method=post>\n";
	echo "<input type=hidden name=docup_p_id value=$_POST[docup_p_id]>\n";
	echo "<input type=submit name=key value=\"確定刪除\">\n";
	echo "</form></center>";
	foot();
	exit;
}
else if ($_POST[key] == "確定刪除"){
	$sql_delete = "delete from  docup_p where docup_p_id = '$_POST[docup_p_id]'";
	$CONN->Execute($sql_delete) or trigger_error("語法錯誤 !",E_USER_ERROR);
	
	$query = "select * from  docup where docup_p_id = '$_POST[docup_p_id]'";	
	$result = $CONN->Execute ($query);
	while (!$result->EOF){
		$alias = $filePath."/".$result->fields[teacher_sn]."_".$result->fields[docup_id]."_".$result->fields[docup_store];
		if (file_exists($alias))
			unlink($alias)or die($alias);
		$result->MoveNext();
	}
	$sql_delete = "delete from  docup where docup_p_id = '$_POST[docup_p_id]'";
	$CONN->Execute($sql_delete) or trigger_error("語法錯誤!",E_USER_ERROR);
	if ($_POST[doc_kind_id] != "")	
		header ("Location: doc_kind_list.php?doc_kind_id=$_POST[doc_kind_id]");
	else
		header ("Location: docup_p_list.php");		
}

//------------------------
if ($is_standalone!="1") head("文件資料庫");

$sql_select = "select doc_kind_id,docup_p_id,docup_p_date,docup_p_name,docup_p_memo,docup_p_owner from docup_p where docup_p_id ='$_GET[docup_p_id]' ";

$result = $CONN->Execute($sql_select);
$doc_kind_id = $result->fields["doc_kind_id"];
$docup_p_id = $result->fields["docup_p_id"];
$docup_p_date = $result->fields["docup_p_date"];
$docup_p_name = $result->fields["docup_p_name"];
$docup_p_memo = $result->fields["docup_p_memo"];
$docup_p_owner = $result->fields["docup_p_owner"];
	
$post_office_p = room_kind();
$state = "<select name=doc_kind_id >";
while (list($tid, $tname) = each($post_office_p)){
	if ($tid == $doc_kind_id)
		$state .= "<option value=\"$tid\" selected>$tname</option>";
	else
		$state .= "<option value=\"$tid\">$tname</option>";
}
$state .= "</select>";

?>

<form method="post" action="<?php echo $_SERVER[PHP_SELF] ?>">
<input type=hidden name="docup_p_id" value="<?php echo $docup_p_id ?>">
<input type=hidden name="doc_kind_id" value="<?php echo $doc_kind_id ?>">
<table  class=module_body align=center>
<caption><b>修改文件專案</b></caption>
<tr> 
	<td align="right" valign="top">處室別</td>
	<td><?php echo $state ?></td>
</tr>
<tr> 
	<td align="right" valign="top">專案名稱</td>
	<td><input type="text" size="40" maxlength="60" name="docup_p_name" value="<?php echo $docup_p_name ?>"></td>
</tr>


<tr>
	<td align="right" valign="top">說明</td>
	<td><textarea name="docup_p_memo" cols=40 rows=5 wrap=virtual><?php echo $docup_p_memo ?></textarea></td>
</tr>

<tr>
	<td colspan=2 align=center ><input type="submit" name="key" value="修改">&nbsp;&nbsp;<input type="submit" name="key" value="刪除">
	</td>
</tr>
<tr>
      <td colspan=2 align=center>
       <hr size=1>
<?php
	if ($doc_kind_id != "")
       		echo"<a href=\"doc_kind_list.php?doc_kind_id=$doc_kind_id\">回專案列表</a>";
       else
       		echo"<a href=\"docup_p_list.php\">回專案列表</a>";
?>
      </td>
</tr>
</table>
</form>
<?php
if ($is_standalone!="1") foot();
?>
