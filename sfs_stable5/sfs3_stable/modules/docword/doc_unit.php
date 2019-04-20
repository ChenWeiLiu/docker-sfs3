<?php

// $Id: doc_unit.php 8746 2016-01-08 15:41:01Z qfon $

//載入設定檔
include "docword_config.php";
// session 認證
//session_start();
//session_register("session_log_id");


if(!checkid($PHP_SELF)){
	$go_back=1; //回到自已的認證畫面
	include "header.php";
	include $SFS_PATH."/rlogin.php";
	include "footer.php";
	exit;
}
else
	$ischecked = true;
//-----------------------------------
include "header.php";

///mysqli	
$mysqliconn = get_mysqli_conn();

if ($key =="新增") {
	//$sql_insert = "insert into sch_doc1_unit (doc1_unit_name) values ('$doc1_unit_name')";	
	$sql_insert = "insert into sch_doc1_unit (doc1_unit_name) values (?)";	
///mysqli	
$stmt = "";
$stmt = $mysqliconn->prepare($sql_insert);
$stmt->bind_param('s',$doc1_unit_name);
$stmt->execute();
$stmt->close();

///mysqli	
	//if (mysql_query ($sql_insert)) {
   if ($mysqliconn->affected_rows==1){
		echo "<h1><center>新增一個單位: $doc1_unit_name</center></h1></body></html>";
		
		redir( "$PHP_SELF" , 1);
		exit;
	}
}

else if ($key =="修改") {
	//$sql_update = "update sch_doc1_unit set doc1_unit_num1='$doc1_unit_num1',doc1_unit_name='$doc1_unit_name' where doc1_unit_num1='$doc1_unit_num1_old'";
	$sql_update = "update sch_doc1_unit set doc1_unit_num1=?,doc1_unit_name=? where doc1_unit_num1=?";
///mysqli	
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('sss',$doc1_unit_num1,$doc1_unit_name,$doc1_unit_num1_old);
$stmt->execute();
$stmt->close();

///mysqli		
	
	//if (mysql_query ($sql_update)) {
	if ($mysqliconn->affected_rows==1){
		echo "<h1><center>$doc1_unit_num1 : $doc1_unit_name 修改成功!!</center></h1></body></html>";
		
		redir( "$PHP_SELF" , 1);
		exit;
	}
}

else if ($key =="刪除") {
	//$sql_update = "delete from sch_doc1_unit where doc1_unit_num1='$doc1_unit_num1_old'";
	$sql_update = "delete from sch_doc1_unit where doc1_unit_num1=?";
///mysqli	
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('s',$doc1_unit_num1_old);
$stmt->execute();
$stmt->close();

///mysqli	
	if ($mysqliconn->affected_rows==1){
	//if (mysql_query ($sql_update)) {
		echo "<h1><center>$doc1_unit_num1 : $doc1_unit_name 已刪除!!</center></h1></body></html>";
		
		redir( "$PHP_SELF" , 1);
		exit;
	}
}
prog(3); //主menu (在 docword_config.php 中設定)

?>
<center>
<form action="<?php echo $PHP_SELF ?>" method="post"> 
<table cellSpacing="0" cellPadding="0"  align="center" bgColor="#000000" border="0">
  <tbody>
    <tr>
      <td>
        <table cellSpacing="1" cellPadding="3" width="100%" border="0">
          <tbody>
<?php
	$doc1_unit_name ="";
	if ($sel == "edit") {

		//$sql_select = "select doc1_unit_num1,doc1_unit_name from sch_doc1_unit where doc1_unit_num1='$doc1_unit_num1'";
		$sql_select = "select doc1_unit_num1,doc1_unit_name from sch_doc1_unit where doc1_unit_num1=? ";
///mysqli	
$stmt = "";
$stmt = $mysqliconn->prepare($sql_select);
$stmt->bind_param('s',$doc1_unit_num1);
$stmt->execute();
$stmt->bind_result($doc1_unit_num1,$doc1_unit_name);
$stmt->fetch();
$stmt->close();

///mysqli
		//$result = mysql_query ($sql_select);
		//while ($row = mysql_fetch_array($result)) {

	?>
	<tr>
	<td align="right" valign="middle" bgColor="#ffffff">編號</td>
	<td bgColor="#ffffff"><input type="text" size="20" maxlength="20" name="doc1_unit_num1" value="<?php echo $doc1_unit_num1 ?>"></td>
	<input type="hidden" name="doc1_unit_num1_old" value="<?php echo $doc1_unit_num1 ?>">
	</tr>	
<?php	
	}
?>

<tr>
	<td align="right" valign="middle" bgColor="#ffffff">單位名稱</td>
	<td bgColor="#ffffff"><input type="text" size="20" maxlength="20" name="doc1_unit_name" value="<?php echo $doc1_unit_name ?>"></td>
</tr>
<tr>
	<td align="center" valign="middle" colspan=2 bgColor="#ffffff" >
<?php
	if ($sel == "edit") 
		echo "<input type=submit name=key value = \"修改\" >&nbsp;&nbsp;<input type=submit name=key value = \"刪除\" onClick=\"return confirm('$doc1_unit_name \\n確定刪除這筆記錄?')\">";
	else
		echo "<input type=submit name=key value = \"新增\" >";
	
?>
	</td>
</tr>
<tr>
	<td colspan=2>
	<! -- 已有單位列表 --- !>
	<table border="1" bgColor="#cccccc" cellSpacing="1" cellPadding="3" width="100%">
	<tr>
	<td align="center">編號</td><td align="center">單位名稱</td><td align="center">編修</td>
	</tr>
	
<?php 

$sql_select = "select doc1_unit_num1,doc1_unit_name from sch_doc1_unit order by doc1_unit_num1";
$result = mysql_query ($sql_select);
while ($row = mysql_fetch_array($result)) {
	$doc1_unit_num1 = $row["doc1_unit_num1"];
	$doc1_unit_name = $row["doc1_unit_name"];
	echo "<tr><td align=\"center\">$doc1_unit_num1</td><td align=\"center\">$doc1_unit_name</td><td align=\"center\"><a href=\"$PHP_SELF?sel=edit&doc1_unit_num1=$doc1_unit_num1\">修改</a></td></tr>";
}
?>
	</td>
	</tr>
	</table>
</td>
</tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
</form>	

</center>
<?php
include "footer.php";
?>
