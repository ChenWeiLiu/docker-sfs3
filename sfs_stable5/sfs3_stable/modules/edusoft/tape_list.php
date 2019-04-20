<?php
//$Id: tape_list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

//登入檢查
sfs_check();

include "header.php";
//********上下頁數
if(!isset($_REQUEST[tapem_id])){ //預設值
	$query = "select min(tapem_id) from $mastertable ";
	$result = mysql_query($query);
	$row= mysql_fetch_row($result);
	$tapem_id = $row[0];
}
else
	$tapem_id = $_REQUEST[tapem_id];

$dbquery="select count(*)as tolrow from $subtable where tapem_id='$tapem_id'";
$result = mysql_query($dbquery) or die("<br>DJ-PIM ERROR: e to add record.<br>\n $dbquery");   
$row = mysql_fetch_array($result);
$tolrow = $row["tolrow"];
if (!isset($_REQUEST[pos])||($_REQUEST[pos]>$tolrow))
	$pos = 0;
else
	$pos = $_REQUEST[pos];

$pos_next = $pos + 20;
$pos_prev = $pos - 20;
if (isset($_REQUEST[sort]))
	$sortby = "&sort=$sort" ;
if (isset($_REQUEST[tapem_id]))
	$tapeby = "&tapem_id=$tapem_id";
if ($pos>=20)
	$link ="<a href=\"$_SERVER[PHP_SELF]?pos=$pos_prev".$sortby.$tapeby."\"><  上一頁</a> &nbsp;&nbsp;";
if ($tolrow>$pos_next )
	$link .="<a href=\"$_SERVER[PHP_SELF]?pos=$pos_next".$sortby.$tapeby."\">下一頁  ></a>";
$link="<table width=60%><tr><td width=30% align=center><a href=\"tape_add.php?tapem_id=$tapem_id\">新增$ap_name</a></td><td align=center>$link</td></tr></table>";
echo("<center>");
echo("<form action=\"tape_list.php\" method=\"post\" name=\"tapeform\">");  
echo(" <img src=\"eye.gif\"><b>$school_sshort_name".$ap_name."列表&nbsp;&nbsp;"); 
$dbquery = "select * from $mastertable ";
$dbquery .= "order by tapem_id ";
$result = $CONN->Execute($dbquery) ;
while(!$result->EOF){
	$tapem_arr[$result->fields[tapem_id]] = $result->fields[tapem_id]." - ".$result->fields[tapem_name];
	$result->MoveNext();
}
$sel = new drop_select();
$sel->s_name = "tapem_id";
$sel->id = $_REQUEST[tapem_id];
$sel->arr = $tapem_arr;
$sel->is_submit=true;
$sel->has_empty = false;
$sel->do_select();
echo("</b>");
echo(" 數量：$tolrow 片&nbsp;&nbsp;  $link_str");

?>
<table border="1" width="100%">
    <tr>
    <td width="10%" bgcolor="#bfd8a0" align="center"><a href="tape_list.php?sort=tapem_id,tape_id&tapem_id=<?echo $tapem_id ?>">編碼</a></td>
    <td width="30%" bgcolor="#bfd8a0" align="center"><a href="tape_list.php?sort=tape_name&tapem_id=<?echo ($tapem_id) ?>"><?php echo $ap_name ?>名稱</a></td>
    <td width="30%" bgcolor="#bfd8a0" align="center">說明</td>    
    <td width="10%" bgcolor="#bfd8a0" align="center"><a href="tape_list.php?sort=tape_grade&tapem_id=<?echo ($tapem_id) ?>">適用年級</a></td>    
    <td width="20%" bgcolor="#bfd8a0" align="center" colspan=2>編修動作</td>
  </tr>

<?PHP
$dbquery = "select tapem_id,tape_id,tape_name,tape_grade,tape_memo from $subtable ";
if (isset($tapem_id))
	$dbquery .= " where tapem_id ='$tapem_id' ";
if (isset($_REQUEST[sort]))
	$dbquery .= "order by $_REQUEST[sort]  ";
$dbquery .="LIMIT $pos, 20 ";
$result = mysql_query($dbquery)or die("<br>DJ-PIM ERROR: e to add record.<br>\n $dbquery");
while($row = mysql_fetch_array($result)) {
	echo("<tr><td align=center>$row[tapem_id]$row[tape_id]</td><td>$row[tape_name]</td>");
	echo("<td ><font color=green size=-1>".nl2br($row[tape_memo])."</font></td>");    
	echo("<td align=center>$row[tape_grade]</td>");  
	echo("<td><a href=\"tape_edit.php?edit_tapem&tapem_id=$row[tapem_id]&tape_id=$row[tape_id]\">修改</a></td>");
	echo("<td><a href=\"tape_delete.php?delete_tapem&tapem_id=$row[tapem_id]&tape_id=$row[tape_id]\">刪除</a></td></tr>");    
}
echo("</table>");
echo("<hr size=1>");
echo($link);
echo("</center>");
echo("</td>");
echo("</tr>");
echo("</table>");
include("footer.php")
?>
