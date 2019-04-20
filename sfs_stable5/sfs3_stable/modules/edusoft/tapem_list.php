<?php 
//$Id: tapem_list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";  //軟體設定
//登入檢查
sfs_check();

include "header.php";
?>
<center>
<b><?php echo $ap_name ?>類別</b><br>
<table border="1" width="300">
  <tr>
    <td width="20%" bgcolor="#bfd8a0" align="center">編碼</td>
    <td width="50%" bgcolor="#bfd8a0" align="center">類別名稱</td>
    <td width="30%" bgcolor="#bfd8a0" align="center" colspan=2>編修動作</td>
  </tr>

<?php
$dbquery = "select * from $mastertable order by tapem_id";
$result = &$CONN->Execute($dbquery);

while(!$result->EOF){
	echo ($i++ %2)?"<tr bgcolor=\"$school_kind_color[3]\">":"<tr bgcolor=\"$school_kind_color[5]\">";
	echo "<td align=center>".$result->fields[tapem_id]."</td><td>".$result->fields[tapem_name]."</td>";
	echo "<td><a href=\"tapem_edit.php?edit_tapem=1&tapem_id=".$result->fields[tapem_id]."\">修改</a></td>";
	echo "<td><a href=\"tapem_delete.php?delete_tapem=1&tapem_id=".$result->fields[tapem_id]."\">刪除</a></td></tr>";    
	$result->MoveNext();
}

?>
</table>
<hr size=1>
<a href="tapem_add.php">新增類別</a>
</center>

<?php
include("footer.php");
?>
