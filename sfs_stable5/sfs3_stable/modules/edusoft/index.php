<?php
//$Id: index.php 8668 2015-12-24 06:34:08Z qfon $

include "config.php";
 
include "header.php";  
     
//********上下頁數
if(!isset($_REQUEST[tapem_id])){ //預設值
	$query = "select min(tapem_id) from $mastertable  ";
	$result = $CONN->Execute($query);
	$_REQUEST[tapem_id] = $result->fields[0];
}

///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
if ($_REQUEST[tapem_id] <> "") {
$stmt = $mysqliconn->prepare("select count(*) as tolrow from $subtable where tapem_id=?");
$stmt->bind_param('s', $_REQUEST[tapem_id]);
}
else
{
$stmt = $mysqliconn->prepare("select count(*) as tolrow from $subtable");
}
$stmt->execute();
$stmt->bind_result($tolrow);
$stmt->fetch();
$stmt->close();
///mysqli

 /*
$dbquery="select count(*) as tolrow from $subtable where tapem_id='$_REQUEST[tapem_id]'";
$result = &$CONN->Execute($dbquery);     
$tolrow = $result->fields["tolrow"];
*/

if (!isset($_REQUEST[pos])||($_REQUEST[pos]>$tolrow))
	$pos = 0;
else
	$pos = $_REQUEST[pos];

$pos_next = $pos +  $row_num;
$pos_prev = $pos -  $row_num;   

if (isset($_REQUEST[sort]))
	$sortby = "&sort=$sort" ;  
if (isset($_REQUEST[tapem_id]))
	$tapeby = "&tapem_id=$_REQUEST[tapem_id]";     
     
if ($pos>= $row_num)
	$link_str ="<a href=\"$PHP_SELF?pos=$pos_prev".$sortby.$tapeby."\"><  上一頁</a> &nbsp;&nbsp;";
if ($tolrow>$pos_next )
	$link_str .="<a href=\"$PHP_SELF?pos=$pos_next".$sortby.$tapeby."\">下一頁  ></a>";     
    
$link_str="<table width=60%><tr><td align=center>$link_str</td></tr></table>";
      
// ****************
      
echo "<form action=\"$_SERVER[PHP_SELF]\" method=\"post\" name=\"tapeform\">";
echo " <center><img src=\"eye.gif\"><b>$school_sshort_name ".$ap_name."列表&nbsp;&nbsp;&nbsp;"; 

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
$tbgcolor = "";
$tbackground  = "";
if ($table_bgcolor)
	$tbgcolor = " bgcolor=\"$table_bgcolor\" ";
if ($table_background)
$tbackground =" background=\"$table_background\" ";
?>
  
</form></center>
<table <?php echo "$tbgcolor $tbackground " ?> border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" >
 <tr>
<td width="6%" bgcolor="#bfd8a0" align="center"><a href="<?php echo $_SERVER[PHP_SELF] ?>?sort=tapem_id,tape_id&tapem_id=<?echo $_REQUEST[tapem_id] ?>">編碼</a></td>
<td width="44%" bgcolor="#bfd8a0" align="center"><a href="<?php echo $_SERVER[PHP_SELF] ?>?sort=tape_name&tapem_id=<?echo $_REQUEST[tapem_id] ?>"><?php echo $ap_name ?>名稱</a></td>
    <td width="35%" bgcolor="#bfd8a0" align="center">說明</a></td>    
    <td width="15%" bgcolor="#bfd8a0" align="center"><a href="<?php echo $_SERVER[PHP_SELF] ?>?sort=tape_grade&tapem_id=<?echo $_REQUEST[tapem_id] ?>">適用年級</a></td>        
    </tr>

<?php

$dbquery = "select tapem_id,tape_id,tape_name,tape_grade,tape_memo from $subtable where tapem_id=? ";
if (isset($_REQUEST[sort]))
$dbquery .= "order by $_REQUEST[sort] ";      
$dbquery .="LIMIT $pos,  $row_num    ";

//if ($_REQUEST[tapem_id] <> "") {
$stmt = $mysqliconn->prepare($dbquery);
$stmt->bind_param('s', $_REQUEST[tapem_id]);
//} 
 
$stmt->execute();
$stmt->bind_result($tapem_id,$tape_id,$tape_name,$tape_grade,$tape_memo);
	
	while ($stmt->fetch()) {
	echo ($i++ %2)?"<tr bgcolor=\"$school_kind_color[3]\">":"<tr bgcolor=\"$school_kind_color[5]\">";
	echo "<td align=center>".$tapem_id.$tape_id."</td><td>".$tape_name."</td><td><font color=green size=-1>".nl2br($tape_memo)."</font></td>";
	echo "<td align=center>".$tape_grade."</td></tr>";  

		
	}

/*
$dbquery = "select tapem_id,tape_id,tape_name,tape_grade,tape_memo from $subtable where tapem_id='$_REQUEST[tapem_id]' ";
if (isset($_REQUEST[sort]))
$dbquery .= "order by $_REQUEST[sort]  ";    
  
$dbquery .="LIMIT $pos,  $row_num    ";

$result = &$CONN->Execute($dbquery)
or die("<br>DJ-PIM ERROR: e to add record.<br>\n $dbquery");   
  
while(!$result->EOF) {
	echo ($i++ %2)?"<tr bgcolor=\"$school_kind_color[3]\">":"<tr bgcolor=\"$school_kind_color[5]\">";
	echo "<td align=center>".$result->fields[tapem_id].$result->fields[tape_id]."</td><td>".$result->fields[tape_name]."</td><td><font color=green size=-1>".nl2br($result->fields[tape_memo])."</font></td>";
	echo "<td align=center>".$result->fields[tape_grade]."</td></tr>";  
  	$result->MoveNext();
}
*/


echo "</table>";
echo "<hr size=1>";
echo "<center>";
echo $link_str ;
echo "</center>";

include "footer.php";  
?> 
