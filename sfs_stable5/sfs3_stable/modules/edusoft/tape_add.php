<?php 
//$Id: tape_add.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";  //軟體設定

//登入檢查
sfs_check();


if ($_POST[dopost]=="確定新增"){
	$dbquery = "insert into $subtable ";
	$dbquery .= "(tapem_id,tape_id,tape_name,tape_grade,tape_memo) ";
	$ss ="";
	$tape_grade_1 = $_POST[tape_grade_1];
	$tape_grade_2 = $_POST[tape_grade_2];
	$tape_grade_3 = $_POST[tape_grade_3];

	if($tape_grade_1!="")
		$ss= $ss.$tape_grade_1.'&nbsp';
	if($tape_grade_2!="")
		$ss= $ss.$tape_grade_2.'&nbsp';
	if($tape_grade_3!="")
		$ss= $ss.$tape_grade_3;
	$tape_name= $_POST[tape_name];	
	$tape_memo= $_POST[tape_memo];	
	$dbquery .= "values('$_POST[tapem_id]',$_POST[tape_id],'$tape_name','$ss','$tape_memo')";
	$result_tapem = mysql_query($dbquery);
//	echo $dbquery;
}
include "header.php";
?>
   <center><img src="eye.gif">
   <b>新增 <?php echo $ap_name ?></b>
   <form method="post" name="tapeform" action="tape_add.php"> 
   <table border="1" width="80%">
   <tr ><td>類別</td><td>
<?php
$dbquery = "select * from $mastertable ";
$dbquery .= "order by tapem_id ";
$result_tapem = mysql_query($dbquery) ;
echo("<select name=\"tapem_id\" onchange=\"document.tapeform.submit()\">");
while($row2 = mysql_fetch_array($result_tapem)){
	if ($_REQUEST['tapem_id'] == $row2[tapem_id])
		echo("<option value=\"$row2[tapem_id]\" selected>$row2[tapem_id] - $row2[tapem_name]</option>");
	else
		echo("<option value=\"$row2[tapem_id]\">$row2[tapem_id] - $row2[tapem_name]</option>");    
}
echo("</select> ");
if (!isset($tapem_id)) $tapem_id='A';
	$dbquery = "select max(tape_id) as maxrow from $subtable where tapem_id='$_REQUEST[tapem_id]'";
$result_tape = mysql_query($dbquery) ;
$row2 = mysql_fetch_array($result_tape);
$maxnum=$row2[maxrow]+1;
echo("編號：<input name=\"tape_id\" size=4 value=\"$maxnum\">");

?>
   </td></tr>
   <tr><td><?php echo $ap_name ?>名稱</td><td><input type="text" name="tape_name" size="60"></td></tr>
   <tr><td>適用年級</td><td><input type="checkbox" name="tape_grade_1" value="低年級">低年級&nbsp
   <input type="checkbox" name="tape_grade_2" value="中年級">中年級&nbsp
    <input type="checkbox" name="tape_grade_3" value="高年級">高年級&nbsp </td></tr>
   <tr><td>說明<br></td><td><textarea name="tape_memo"rows=6 cols="62"></textarea></td></tr>   
   <tr><td colspan=2 align=center><input type="submit" name="dopost" value="確定新增"></td></tr>   
   </table>
   </form>
   
<?php
$dbquery = "select tapem_id,tape_id,tape_name from $subtable ";
$dbquery .= " where tapem_id ='$_REQUEST[tapem_id]' ";
$dbquery .= "order by tape_id desc ";
$dbquery .="LIMIT 0, 10 ";
$result = mysql_query($dbquery) or die("<br>DJ-PIM ERROR: e to add record.<br>\n $dbquery");
echo("<table border=1>");
echo("<caption>最近編號參考</caption>");
echo("<tr><td>類別</td><td>編號</td><td><?php echo $ap_name ?>名稱</td><td colspan=2>編修</td></tr>");
while($row = mysql_fetch_array($result)){
	echo("<tr><td align=center>$row[tapem_id]</td><td>$row[tape_id]</td><td>$row[tape_name]</td>\n");
	echo("<td align=center><a href=\"tape_edit.php?tapem_id=$_REQUEST[tapem_id]&tape_id=$row[tape_id]\">修改</td>\n");  
	echo("<td align=center><a href=\"tape_delete.php?tapem_id=$_REQUEST[tapem_id]&tape_id=$row[tape_id]\">刪除</td></tr>\n");    
}

echo("</table>");
echo("<hr size=1>");

echo("</center>");
echo("</td>");
echo("</tr>");
echo("</table>");
include("footer.php");
?>
