<?php

//$Id: docup_list.php 8754 2016-01-13 12:44:14Z qfon $

include "docup_config.php";
if ($is_standalone!="1") head("文件資料庫");   
?>
<table border="1" class=module_body cellspacing="0" cellpadding="0" width=100%>
 <caption><?php echo $school_short_name ?> 文件總表&nbsp;&nbsp;&nbsp;<a href="<?php echo "docup_p_add.php" ?>">文件專案管理</a>| <a href="<?php echo "doc_search.php" ?>">搜尋</a></caption>
  <tr>
    <td nowrap bgcolor="#008000" align="center"><font color="#FFFFFF">處室</font></td>
    <td nowrap bgcolor="#008000" align="center"><font color="#FFFFFF">專案數</font></td>
    <td  bgcolor="#008000" align="center" nowrap><font color="#FFFFFF">文件數</font></td>    
  </tr> 
<?php
$post_office_p = room_kind();
while (list($tid, $tname) = each($post_office_p)){
	$tid=intval($tid);
	$sql_select = "select count(docup_p_id) as cc ,sum(docup_p_count) as ccc from docup_p where doc_kind_id ='$tid'";
	$result = $CONN->Execute($sql_select) or die ($sql_select);
	$cc =$result->fields[0];
	$ccc =$result->fields[1];
	if ($cc>0) {
		echo ($i++ % 2 == 0)?"<TR class=nom_1>":"<TR class=nom_2>";	
        	echo "<TD align=center><a href=\"doc_kind_list.php?doc_kind_id=$tid\">$tname</a></TD>";
        	echo "<TD align=center>$cc</TD>";
        	echo "<TD align=center>$ccc</TD></TR>";
        }
};
echo "</TABLE>";
if ($is_standalone!="1") foot();
?>