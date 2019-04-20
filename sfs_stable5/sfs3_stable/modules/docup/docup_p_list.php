<?php

//$Id: docup_p_list.php 8143 2014-09-23 08:18:23Z smallduh $

//設定檔載入檢查
include "docup_config.php";
// --認證 session 
sfs_check();

if ($is_standalone!="1") head("文件資料庫");

$post_office_p = room_kind();

$sql_select = "select * from docup_p where  teacher_sn='$_SESSION[session_tea_sn]' \n";
$result = $CONN->Execute($sql_select);
?>

<table border="1" class=module_body cellspacing="0" cellpadding="0" width=100%>
<caption><?php echo $_SESSION[session_tea_name]; ?>--文件專案管理&nbsp;&nbsp;&nbsp;<a href="docup_p_add.php">新增文件專案</a></caption>
  <tr>    
    <td nowrap bgcolor="#008000" align="center" width=60%><font color="#FFFFFF">專案名稱</font></td>
    <td nowrap bgcolor="#008000" align="center" colspan=2 ><font color="#FFFFFF">動作</font></td>
    <td nowrap bgcolor="#008000" align="center"><font color="#FFFFFF">處室</font></td>
    <td  bgcolor="#008000" align="center"><font color="#FFFFFF">修改時間</font></td>
    <td  bgcolor="#008000" align="center"><font color="#FFFFFF">文件數</font></td>
  </tr> 

<?php
while (!$result->EOF) {
	$doc_kind_id = $result->fields["doc_kind_id"];
	$docup_p_id = $result->fields["docup_p_id"];
	$docup_p_date = $result->fields["docup_p_date"];
	$docup_p_name = $result->fields["docup_p_name"];
	$docup_p_memo = $result->fields["docup_p_memo"];
	$docup_p_owner = $result->fields["docup_p_owner"];
	$state_name = $post_office_p[$result->fields["doc_kind_id"]];
	$sql_select="select count(docup.docup_id) as cc from docup where docup_p_id=$docup_p_id ";
	$result2 = $CONN->Execute($sql_select);
	$cc = $result2->fields["cc"];
	echo ($i++ % 2 == 0)?"<TR class=nom_1>":"<TR class=nom_2>";
	echo "<TD  >$docup_p_name</TD>";
	echo "<TD  ><a href=\"doc_list.php?docup_p_id=$docup_p_id&doc_kind_id=$doc_kind_id\">瀏覽</a></TD>";
	echo "<TD  ><a href=\"docup_p_update.php?docup_p_id=$docup_p_id&doc_kind_id=$doc_kind_id\">修改</a></TD>";
	echo "<TD  align=center><a href=\"doc_kind_list.php?doc_kind_id=$doc_kind_id\">$state_name</a></TD>";        	        
	echo "<TD  noWrap  Align=center >$docup_p_date</TD>";
	echo "<TD  Align=center>$cc</TD></TR>";
	$result->MoveNext();
};
echo "</TABLE>";
echo "<br><hr size=1>\n";
if ($is_standalone!="1") foot();
?>