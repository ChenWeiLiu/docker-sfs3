<?php
                                                                                                                             
// $Id: board_delete_c.php 8763 2016-01-13 13:02:47Z qfon $

// --系統設定檔
include	"config.php"; 

// session 認證
session_start();

if ($key=="確定刪除"){
	$b_post_time = mysql_date();

	$b_edit_id=$_SESSION[session_log_id];

	$b_store = $b_id."_".$b_upload;
	if(file_exists ($USR_DESTINATION.$b_store))
		unlink($USR_DESTINATION.$b_store);
	// $query= "delete from board_c where b_id = '$b_id'";
	$b_id=intval($b_id);
	$query= "UPDATE unit_c SET b_days = '0' ,b_edit_time='$b_post_time' ,update_ip='{$_SERVER['REMOTE_ADDR']}',b_edit_id='$b_edit_id'  where b_id = '$b_id'";  //刪除

	mysql_query($query);
		Header ("Location: etoe.php?unit=$unit&entry=$entry");
}

	include "header_u.php";
$b_id=intval($b_id);
$query = "select b_sub,b_con,b_upload,teacher_sn from unit_c where b_id='$b_id'";
$result = mysql_query($query);
$row= mysql_fetch_array($result);

if($row["teacher_sn"] ==$_SESSION[session_tea_sn] || checkid($_SERVER[SCRIPT_FILENAME],1)){   //自己可刪除

?>

<table align="center" border="0" cellPadding="3" cellSpacing="0" width="411">
<tr bgColor="#dae085">
	<td align="middle" height="30" width="60%"><b>確定刪除 編號：<?php echo "$b_id ：". $row["b_sub"]  . "：" . $row["b_con"]; 
?>？</b><br>
	<form action="<?php echo $PHP_SELF ?>" method="post">
	<input type=hidden name=b_id value="<?php echo $b_id ?>">
	<input type=hidden name=bk_id value="<?php echo $bk_id ?>">
	<input type=hidden name=sel value="<?php echo $self ?>">
	<input type="hidden" name="unit" value="<?php echo $unit ?>">
	<input type="hidden" name="entry" value="<?php echo $entry ?>">

	<input type=hidden name=b_upload value="<?php echo $row["b_upload"] ?>">
	<input type=submit name="key" value="確定刪除">  
	<INPUT TYPE="button" VALUE="回上一頁" onClick="history.back()">
	</form>
	</td>
</tr>
</table>
<?php
}
?>
