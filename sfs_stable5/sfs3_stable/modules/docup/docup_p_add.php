<?php

//$Id: docup_p_add.php 8142 2014-09-23 08:17:57Z smallduh $

//設定檔載入檢查
include "docup_config.php";
// --認證 session 
sfs_check();
//------------------------
if ($_POST[key] == "新增"){
	$sql_insert = "insert into docup_p (doc_kind_id,docup_p_id,docup_p_date,docup_p_name,docup_p_memo,docup_p_owner,teacher_sn) values ('$_POST[doc_kind_id]','$_POST[docup_p_id]','$now','$_POST[docup_p_name]','$_POST[docup_p_memo]','".$_SESSION[session_tea_name]."','$_SESSION[session_tea_sn]')";
	$result =$CONN->Execute($sql_insert)or trigger_error("語法錯誤",E_USER_ERROR);
	if ($_POST[doc_kind_id] != "")	
		header ("Location: doc_kind_list.php?doc_kind_id=$_POST[doc_kind_id]");
	else
		header ("Location: doc_p_list.php");
}

if ($is_standalone!="1") head("文件資料庫");
$post_office_p = room_kind();
$state = "<select name=doc_kind_id >";
while (list($tid, $tname) = each($post_office_p)){
	if ($tid==$_GET[doc_kind_id])
		$state .= "<option value=\"$tid\" selected >$tname</option>";
	else
		$state .= "<option value=\"$tid\">$tname</option>";
}
$state .= "</select>";
?>

<form method="post" action="<?php echo $_SERVER[PHP_SELF] ?>">
<input type=hidden name="doc_kind_id" value="<?php echo $_GET[doc_kind_id] ?>">
<table  class=module_body align=center>
<caption><b>新增文件專案</b></caption>  
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
	<td align=center colspan=2 ><input type="submit" name="key" value="新增">
	</td>
</tr>
<tr>
<td colspan=2 align=center>
    <hr size=1>
<?php
	if ($doc_kind_id != "")
       		echo"<a href=\"doc_kind_list.php?doc_kind_id=$_GET[doc_kind_id]\">回專案列表</a>";
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
