<?php
include "config.php";
//登入檢查
sfs_check();

if ($_POST[tapem_edit]=='確定修改'){
	$tapem_name = stripslashes($_POST[tapem_name]);
	$dbquery = "update $mastertable "; 
	$dbquery.= "set tapem_id='$_POST[tapem_id]', tapem_name='$tapem_name' "; 
	$dbquery.= " where tapem_id='$_POST[old_id]'";
	
	$result = mysql_query($dbquery) or die("<br>修改錯誤：查看代號是否重複<br>\n $dbquery");
	header ("Location: tapem_list.php");   
}
else{
	$dbquery = "select * from $mastertable where tapem_id='$_REQUEST[tapem_id]' ";    
	$result = mysql_query($dbquery) or die("<br>DJ-PIM ERROR: 選取錯誤.<br>\n $dbquery");    
	$row = mysql_fetch_array($result);
	include ("header.php"); 
	echo("<p align=\"center\"><img src=\"eye.gif\"><b>修改 $ap_name 類別</b></p>");
	echo("<div align=\"center\">");
	echo("<form name=\"tapeform\" action=\"$_SERVER[PHP_SELF]\"   method=\"post\" > ");
	echo("<input type=\"hidden\" name=\"old_id\" value=\"$_REQUEST[tapem_id]\"> ");
	echo("<table border=\"0\" width=\"400\" cellspacing=\"0\" cellpadding=\"0\">");
	echo("<tr>");
	echo("<td width=\"100%\">");
	echo("<hr>");
	echo("<p>類別代碼：");
	echo("<input type=\"text\" name=\"tapem_id\" value=\"$_REQUEST[tapem_id]\" size=\"4\" maxlength=\"2\"><br>");
	echo("類別名稱：<!--webbot bot=\"Validation\" B-Value-Required=\"TRUE\"");
	echo("I-Maximum-Length=\"30\" --><input type=\"text\" name=\"tapem_name\" value=\"$row[tapem_name]\" size=\"30\"  maxlength=\"30\"></p>");
	echo("<p align=\"center\"><input type=\"submit\" value=\"確定修改\" name=\"tapem_edit\">&nbsp;&nbsp;");
	echo("<input type=\"button\" value=\"回上一頁\" onClick=\"history.back()\"></td>\n");
	echo("</tr></table>");
	echo("</form><hr width=400>");
}
?>

</div>
<?php
	include("footer.php")
?>
