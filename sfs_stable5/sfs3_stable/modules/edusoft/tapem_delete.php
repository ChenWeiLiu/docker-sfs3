<?php
//$Id: tapem_delete.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

//登入檢查
sfs_check();

if ($_POST[do_delete]=='確定刪除'){//執行刪除
	$dbquery = "delete from $mastertable "; 
	$dbquery.= " where tapem_id='$_POST[tapem_id]'";
	$result = mysql_query($dbquery) or die("<br>TAPE: 刪除錯誤.<br>\n $dbquery");
	header("Location: tapem_list.php");
}
else{ //詢問是否刪除
	$dbquery = "select * from $mastertable where tapem_id='$_GET[tapem_id]' ";
	$result = mysql_query($dbquery) or die("<br>TAPE: 刪除錯誤.<br>\n $dbquery");
	$row = mysql_fetch_array($result);
	include "header.php";
	echo("<center>\n");
	echo("<b>是否刪除?</b>");
	echo("<form name=\"tapeform\" action=\"tapem_delete.php\"   method=\"post\" > \n");
	echo("<input type=\"hidden\" name=\"tapem_id\" value=\"$_GET[tapem_id]\">\n");
	echo("<table border=\"0\" width=\"400\" cellspacing=\"0\" cellpadding=\"0\">\n");
	echo("<tr>\n");
	echo("<td width=\"100%\">\n");
	echo("<hr>\n");
	echo("<p>類別代碼：$_GET[tapem_id]<br>\n");
	echo("類別名稱：$row[tapem_name]</p>\n");
	echo("<hr>\n");
	echo("<p align=\"center\"><input type=\"submit\" value=\"確定刪除\" name=\"do_delete\">&nbsp;&nbsp;&nbsp;\n");
	echo("<input type=\"button\" value=\"回上一頁\" onClick=\"history.back()\"></td>\n");
	echo("</tr></table>\n");
	echo("</form>\n");
	echo("</center>\n");
}
include("footer.php");
?>
