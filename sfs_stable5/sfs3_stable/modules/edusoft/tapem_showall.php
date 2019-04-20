<?php
//$Id: tapem_showall.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//登入檢查
sfs_check();
include "header.php";
?>
<center>
<b><?php echo $ap_name ?> 總表</b><br>
<table border="1" width="300">
  <tr>
    <td width="20%" bgcolor="#8080FF" align="center">編碼</td>
    <td width="50%" bgcolor="#8080FF" align="center"><?php echo $ap_name ?>名稱</td>
  </tr>

<?php
$dbquery = "select * from $subtable order by tapem_id,tape_id";
$result = mysql_query($dbquery) or die("<br>DJ-PIM ERROR: e to add record.<br>\n $dbquery");
while($row = mysql_fetch_array($result)){
	echo("<tr><td align=center>$row[tapem_id]$row[tape_id]</td><td>$row[tape_name]</td>");
}
?>
</table>
</center>
<?php
if (!isset($dbname)){
	include("footer.php");
}
?>
