<?php 
//$Id: tape_edit.php 8146 2014-09-23 08:24:22Z smallduh $
include "config.php";  //軟體設定
//登入檢查
sfs_check();

//******存入資料//
if ($_POST[dopost]=="確定修改"){
	$tape_memo = $_POST[tape_memo];
	$tape_name = $_POST[tape_name];
	$dbquery = "update $subtable ";
	$dbquery .= " set tape_name='$tape_name',";
	$dbquery .= " tape_grade='$_POST[tape_grade]',";
	$dbquery .= " tape_memo='$tape_memo'";
	$dbquery .= " where tapem_id='$_POST[tapem_id]' and tape_id=$_POST[tape_id]";
	$result_tapem = mysql_query($dbquery)or die("存入錯誤") ;
	header ("Location: tape_list.php"); 
}
$dbquery = "select * from $subtable ";
$dbquery .= "where tapem_id='$_GET[tapem_id]' and tape_id=$_GET[tape_id] ";
$result_tapem = mysql_query($dbquery) or die("選取錯誤");
$row = mysql_fetch_array($result_tapem);
include "header.php"; 
?>

<center><img src="eye.gif">
   <b>修改 <?php echo $ap_name ?></b>
   <form method="post" name="tapeform" action="tape_edit.php"> 
   <input type="hidden" name="tapem_id" value="<?php echo("$row[tapem_id]") ?>">
   <input type="hidden" name="tape_id" value="<?php echo("$row[tape_id]") ?>">   
   <table border="1" width="80%">
   <tr ><td>類別編號</td><td><?php echo("$row[tapem_id]") ?><?php echo("$row[tape_id]") ?>     
   </td></tr>
   <tr><td>名稱</td><td><input type="text" name="tape_name" size="60" value="<?php echo("$row[tape_name]") ?>"></td></tr>
   <tr><td>適用年級</td><td><input type="text" name="tape_grade" size="30"value="<?php echo("$row[tape_grade]") ?>"></td></tr>
   <tr><td>說明<br></td><td><textarea name="tape_memo"rows=6 cols="62"><?php echo("$row[tape_memo]") ?></textarea></td></tr>   
   <tr><td colspan=2 align=center><input type="submit" name="dopost" value="確定修改">&nbsp;&nbsp;<input type="button" value="回上一頁" onClick="history.back()"></td></tr>   
   </table>
   </form>

<?php
include("footer.php");
?>
