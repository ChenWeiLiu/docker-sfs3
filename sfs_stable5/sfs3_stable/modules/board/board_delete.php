<?php

// $Id: board_delete.php 8709 2015-12-30 15:17:02Z qfon $

// --系統設定檔
include	"board_config.php";
// session 認證
//session_start();
//session_register("session_log_id");

$bk_id = $_REQUEST['bk_id'];
$b_id = $_REQUEST['b_id'];

if(!board_checkid($bk_id)){

	$go_back=1; //回到自已的認證畫面
	if ($is_standalone)
		include "header.php";
	else
		head("校務佈告欄");

	include $SFS_PATH."/rlogin.php";
	if ($is_standalone)
		include "footer.php";
	else
		foot();
	exit;
}


//檢查修改權
//$query = "select b_id from board_p where b_id ='$b_id' and b_own_id='$session_log_id'";
$b_id=intval($b_id);
$query = "select b_id from board_p where b_id ='$b_id' and teacher_sn ='$_SESSION[session_tea_sn]'";
$result = $CONN->Execute($query) or die($query);
if ($result->EOF && !checkid($_SERVER[SCRIPT_FILENAME],1)){
	echo "沒有權限修改本公告";
	exit;
}

//-----------------------------------

if ($_POST['key']=="確定刪除"){
	$b_store = $bk_id."_".$b_id."_".$b_upload;
	if(file_exists ($USR_DESTINATION.$b_store))
		unlink($USR_DESTINATION.$b_store);
	elseif(is_dir($USR_DESTINATION.$b_id)){
		$fArr = board_getFileArray($b_id);
		foreach($fArr as $val){
			unlink($USR_DESTINATION.$b_id.'/'.$val['new_filename']);
		}
		rmdir($USR_DESTINATION.$b_id);
	}
    $b_id=intval($b_id);
	$query= "delete from board_p where b_id = '$b_id'";
	$CONN->Execute($query);
	$query= "delete from board_files where b_id = '$b_id'";
	$CONN->Execute($query);
	
	Header ("Location: board_view.php?bk_id=$bk_id");
}
//是否有獨立的界面
if ($is_standalone)
	include "header.php";
else
	head("校務佈告欄");
$b_id=intval($b_id);
$query = "select b_sub,b_upload from board_p where b_id='$b_id'";
$result = $CONN->Execute($query);
$row= $result->fetchRow();

?>

<table align="center" border="0" cellPadding="3" cellSpacing="0" width="411">
<tr bgColor="#dae085">
	<td align="middle" height="30" width="60%"><b>確定刪除 編號：<?php echo "$b_id ：". $row["b_sub"];
?>？</b><br>
	<form action="<?php echo $PHP_SELF ?>" method="post">
	<input type=hidden name=b_id value="<?php echo $b_id ?>">
	<input type=hidden name=bk_id value="<?php echo $bk_id ?>">
	<input type=hidden name=b_upload value="<?php echo $row["b_upload"] ?>">
	<input type=submit name="key" value="確定刪除">
	<INPUT TYPE="button" VALUE="回上一頁" onClick="history.back()">
	</form>
	</td>
</tr>
</table>
<?php
if($is_standalone)
	include	"footer.php";
else
	foot();
?>
