<?php

// $Id: board_delete.php 7779 2013-11-20 16:09:00Z smallduh $

// --系統設定檔
include	"board_config.php";
// session 認證
//session_start();
//session_register("session_log_id");

$bk_id = $_REQUEST['bk_id'];
$b_id = $_REQUEST['b_id'];

if(!board_checkid($bk_id) && !checkid($_SERVER[SCRIPT_FILENAME],1)){

	$go_back=1; //回到自已的認證畫面
	if ($is_standalone)
		include "header.php";
	else
		head("joomla!文章編輯");

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
$query = "select b_id from jboard_p where b_id ='$b_id' and teacher_sn ='$_SESSION[session_tea_sn]'";
$result = $CONN->Execute($query) or die($query);
if ($result->EOF && !checkid($_SERVER[SCRIPT_FILENAME],1)){
	echo "沒有權限修改本公告";
	exit;
}

//-----------------------------------

if ($_POST['key']=="確定刪除"){
	$b_id=intval($b_id);
	$query= "delete from jboard_p where b_id = '$b_id'";
	$CONN->Execute($query);
	$query= "delete from jboard_images where b_id = '$b_id'";
	$CONN->Execute($query);
	
	//把所有附檔刪除
	$query="select * from jboard_files where b_id='$b_id'";
	$res=$CONN->Execute($query);
	if ($res->RecordCount()>0) {
	  while ($row=$res->FetchRow()) {
	    $sFile=$row['new_filename'];
	    unlink($Download_Path.$sFile);
	  }
	}
	$query= "delete from jboard_files where b_id = '$b_id'";
	$CONN->Execute($query);
	
	Header ("Location: board_view.php?bk_id=$bk_id");
}
//是否有獨立的界面
if ($is_standalone)
	include "header.php";
else
	head("joomla!文章編輯");
$b_id=intval($b_id);
$query = "select b_sub from jboard_p where b_id='$b_id'";
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
