<?php
                                                                                                                             
// $Id: board_edit_c.php 8870 2016-04-13 14:38:42Z brucelyc $

// --系統設定檔
include "config.php"; 
// session 認證
session_start();

sfs_check();

if ($key == "確定修改"){
	$b_post_time = mysql_date();

	$b_edit_id=$_SESSION[session_log_id];
	$b_edit_con=$b_edit_con+1;
	if($o_kind=='' and $b_kind!=''){		
			$bk_id=$o_bk_id;		
	}
	$sql_update = "update unit_c set b_open_date='$b_open_date',  b_sub='$b_sub',b_con='$b_con', b_url='$b_url' ,b_edit_time='$b_post_time',b_is_intranet='$b_is_intranet',update_ip='{$_SERVER['REMOTE_ADDR']}',b_edit_id='$b_edit_id',b_edit_con='$b_edit_con' ,b_kind='$b_kind' ,bk_id='$bk_id'";
	$b_store = $b_id."_".$_FILES[b_upload][name];
	$b_old_store = $b_id."_".$b_old_upload;
	if($del_img==1){
		$sql_update .= ", b_upload=''";
		if(file_exists($USR_DESTINATION.$b_old_store))
			unlink($USR_DESTINATION.$b_old_store);

		
	}elseif (is_file($_FILES[b_upload][tmp_name])){
		$sql_update .= ", b_upload='".$_FILES[b_upload][name]."' ";
		if(file_exists($USR_DESTINATION.$b_old_store))
			unlink($USR_DESTINATION.$b_old_store);
		//檢查是否上傳 php 程式檔
		if  (check_is_php_file($_FILES[b_upload][name]))
			$error_flag = true;
		else	
			copy($_FILES[b_upload][tmp_name] , ($USR_DESTINATION.$b_store));
	}
    $b_id=intval($b_id);
	$sql_update .= " where b_id='$b_id' " ;
	mysql_query($sql_update) or die ($sql_update);
	
	if (!$error_flag){
		Header ("Location: etoe.php?unit=$unit&entry=$entry&m_id=$b_id");


	}
}
$b_id=intval($b_id);
$query = "select * from unit_c where b_id ='$b_id' ";
$result	= mysql_query($query);

$row = mysql_fetch_array($result);
$b_id = $row["b_id"];
$bk_id = $row["bk_id"];
$b_open_date = $row["b_open_date"];
$b_days = $row["b_days"];
$b_unit = $row["b_unit"];
$b_title = $row["b_title"];
$b_name = $row["b_name"];
$b_sub = $row["b_sub"];
$b_con = $row["b_con"];
$b_hints = $row["b_hints"];
$b_upload = $row["b_upload"];
$b_url = $row["b_url"];
$b_kind = $row["b_kind"];
$b_own_id = $row["b_own_id"];
$b_is_intranet = $row["b_is_intranet"];
$b_edit_con = $row["b_edit_con"];
$o_kind = $b_kind;

$l_entry="<select size=1 name=b_kind>";
$l_entry.="<option value=''>細目</option>";
foreach($entry_s as $key=>$value){
	$sel='';
	 if($key==$b_kind) $sel="selected";
	$l_entry.="<option $sel  value=$key>$value</option>";
}

$l_entry.="</select>";

$pk_id="t".$b_own_id;
if($b_title=='學生')
	$pk_id="s".$b_own_id;
if($b_title=='家長')
	$pk_id="f".$b_own_id;

if($row["teacher_sn"] ==$_SESSION[session_tea_sn] || checkid($_SERVER[SCRIPT_FILENAME],1)){   //自己可修改

include "header_u.php";?>

<script language="JavaScript">
function checkok(){
	var OK=true
	if(document.eform.b_sub.value == ""){
		OK=false;
	}
	if (OK == false){
		alert("標題不可空白")
	}
	return OK
}

//-->
</script>
<form enctype="multipart/form-data" name=eform method="post" action="<?php echo $PHP_SELF ?>" 
onSubmit="return checkok()" >
<center>
<?php  echo "<b>修改　$s_title </b>"; ?>

<?php
//顯示錯誤訊息
if ($error_flag)
	echo "<h3><font color=red>錯誤 !! 不可上傳 php 程式檔!!</font></h3>";
?>

<table	border="1" bgcolor="#CCFFFF" bordercolor="#9999FF">  
<tr>
	<td align="right" valign="top">發表者 </td>
	<td><?php echo " $b_name "; ?>　　　　 (空格及標點請用全形字)<br><font color=blue>，。、！？；：「」… ─『』</font>　　（可使用範例複製及貼上）

</tr>

<tr>
	<td align="right" valign="top">排序</td>
	<td><input type="text" size="12" maxlength="12" name="b_open_date" value="<?php echo $b_open_date ?>">(請以日期格式輸入)</td>
</tr>

<tr>
	<td align="right" valign="top">標題</td>
	<td><input type="text" size="70" maxlength="70" name="b_sub" value="<?php echo $b_sub  ?>"></td>
</tr>

<tr>
	<td align="right" valign="top">內容</td>
	<td><textarea name="b_con" cols=70 rows=10 wrap=virtual><?php echo $b_con ?></textarea></td>
</tr>
<tr>
<tr>
	<td align="right" valign="top">網址：</td>
	<td><input type="text" name="b_url" size=50 value="<?php echo $b_url ?>"></td>
</tr>

<tr>
	<td align="right" valign="top">附件(圖片)</td>
	
	<td><input type="file" size="50" maxlength="50" name="b_upload" value="<?php echo $b_upload ?>">　<font size=2><input type=checkbox name="del_img" value="1"> 刪除附件</font></td>
</tr>
<tr>
	<td align="right" valign="top">項目類別</td>	
	<td><?=$l_entry ?>(可調整分項，如為細目則需為空白)</td>
</tr>
<?php
if (checkid($_SERVER[SCRIPT_FILENAME],1)){
?>

<tr>
	<td align="right" valign="top">單元序號</td>	
	<td><input  type="text" size="5"  name="bk_id" value="<?php echo $bk_id ?>">(如輸入領域代號則為全領域共用，如為細目則輸入該主題的序號)</td>
</tr>

<?php
}else{
?>
<input type="hidden" name="bk_id" value="<?php echo $bk_id ?>">
<?php
}
?>

<tr>
	<td colspan=2 align=center><input type="submit" name="key" value="確定修改">&nbsp;&nbsp;&nbsp;
	<INPUT TYPE="button" VALUE="回上一頁" onClick="history.back()"></td>
</tr>
</table>
<input type="hidden" name="o_kind" value="<?php echo $o_kind ?>">
<input type="hidden" name="o_bk_id" value="<?php echo $o_bk_id ?>">
<input type="hidden" name="pk_id" value="<?php echo $pk_id ?>">

<input type="hidden" name="unit" value="<?php echo $unit ?>">
<input type="hidden" name="entry" value="<?php echo $entry ?>">

<input type="hidden" name="board_name" value="<?php echo $board_name ?>">
<input type="hidden" name="b_old_upload" value="<?php echo $b_upload ?>">
<input type="hidden" name="b_id" value="<?php echo $b_id ?>">
<input type="hidden" name="sel" value="<?php echo $self ?>">
<input type="hidden" name="b_edit_con" value="<?php echo $b_edit_con ?>">
</form>
</center>

<?php
}
?>
