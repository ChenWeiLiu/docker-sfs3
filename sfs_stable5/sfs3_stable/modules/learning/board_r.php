<?php

// $Id: board_r.php 5310 2009-01-10 07:57:56Z hami $

// --系統設定檔
include	"config.php"; 
// session 認證
session_start();


if($_SESSION[session_log_id]=='' ){
	
	$go_back=1; //回到自已的認證畫面  
		include "header.php";
	include $SFS_PATH."/rlogin.php";  
	exit;
}
//-----------------------------------





$b_name=$_SESSION[session_tea_name]; //張貼人姓名
$b_title = $_SESSION[session_who]; //職稱

$b_name= addslashes($b_name);  //檢查特殊字


if ($key == "確定"){
	$b_days=1;
	$b_post_time = mysql_date();
	$b_open_date = mysql_date();

	$b_upload_name = $_FILES[b_upload][name];
	$sql_insert = "insert into unit_c(bk_id,b_open_date,b_days,b_unit,b_title,b_name,b_sub,b_con,b_upload,b_url,b_own_id,b_post_time,b_is_intranet,teacher_sn,update_ip,act)values ('$bk_id','$b_open_date','$b_days','$board_name ','$b_title ','$b_name ','$b_sub','$b_con ','$b_upload_name ','$b_url','$_SESSION[session_log_id]','$b_post_time','$b_is_intranet','$_SESSION[session_tea_sn]','{$_SERVER['REMOTE_ADDR']}','$a_b_id')";

	mysql_query($sql_insert) or die ($sql_insert); 
	$query = "select max(b_id) as mm from unit_c ";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$mm = $row["mm"] ;
	$b_upload_name = $mm."_".$_FILES[b_upload][name];
	if($_FILES[b_upload][name] !="" ) {
		//檢查是否上傳 php 程式檔
		if  (check_is_php_file($_FILES[b_upload][name]))
			$error_flag = true;
		else
			copy($_FILES[b_upload][tmp_name] , ($USR_DESTINATION.$b_upload_name));
	}
	if (!$error_flag)
		if($c_is_in=="")
			Header ("Location: etoe.php?unit=$unit&entry=$entry&m_id=$bk_id");
		else 
			Header ("Location: etoe.php?unit=$unit&entry=$entry&m_id=$bk_id");

}
$u_id = $_POST['u_id'] ;
if( $u_id=="")   
 	exit();
//避免直接輸入參數

include "header_u.php";

$b_open_date = date("Y-m-j");
?>

<script language="JavaScript">
function checkok()
{
	var OK=true
	if(document.eform.b_sub.value == "")
	{
		OK=false;
	}
	if (OK == false)
	{
		alert("標題不可空白")
	}
	return OK
}

//-->
</script>



<form enctype="multipart/form-data" name=eform method="post" action="<?php echo $PHP_SELF ?>" 
onSubmit="return checkok()">
<center>
<?php  echo "<b> 新增 $s_title </b>"; ?>
<?php
//顯示錯誤訊息
if ($error_flag)
	echo "<h3><font color=red>錯誤 !! 不可上傳 php 程式檔!!</font></h3>";
?>
<table	border="1" bgcolor="#CCFFFF" bordercolor="#9999FF">   
<tr>
	<td align="right" valign="top"></td>
	<td><?php echo " $b_name "; ?>　　　　 (空格及標點請用全形字)<br><font color=blue>，。、！？；：「」… ─『』</font>　　（可使用範例複製及貼上）

</td>
</tr>
<tr>
	<td align="right" valign="top">標題</td>
	<td><input type="text" size="70" maxlength="70" name="b_sub" value="<?php echo $c_sub  ?>"></td>
</tr>
<tr>
	<td align="right" valign="top">內容</td>
	<td><textarea name="b_con" cols=70 rows=10 wrap=virtual><?php echo $b_con ?></textarea></td>
</tr>
<tr>
	<td align="right" valign="top">相關網址：</td>
	<td><input type="text" name="b_url" size=50 value="<?php echo $b_url ?>"></td>
</tr>

<?php
//if ($board_is_upload){
?>
<tr>
	<td align="right" valign="top">附件(圖片)</td>
	
	<td><input type="file" size="50" maxlength="50" name="b_upload" value="<?php echo $b_upload ?>"></td>
</tr>
<?php
//}
?>
<input type="hidden" name="bk_id" value="<?php echo $u_id ?>">
<input type="hidden" name="unit" value="<?php echo $unit ?>">
<input type="hidden" name="entry" value="<?php echo $entry ?>">
<input type="hidden" name="board_name" value="<?php echo $board_name ?>">
<input type="hidden" name="a_b_id" value="<?php echo $a_b_id ?>">
<input type="hidden" name="ins" value="<?php echo $ins ?>">
<input type="hidden" name="c_is_in" value="<?php echo $c_is_in ?>">
<tr>
	<td  align=center colspan=2 ><input type="submit" name="key" value="確定">
	<INPUT TYPE="button" VALUE="回上一頁" onClick="history.back()"></td>
</td>
</tr>
</table>
</form>
</center>


