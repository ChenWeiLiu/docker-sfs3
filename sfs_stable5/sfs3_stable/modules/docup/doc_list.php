<?php

//$Id: doc_list.php 8754 2016-01-13 12:44:14Z qfon $

include "docup_config.php";
$doc_kind_id = $_POST[doc_kind_id];
if ($doc_kind_id=='')
	$doc_kind_id = strip_tags($_GET[doc_kind_id]);
$docup_p_id = $_POST[docup_p_id];
if ($docup_p_id=='')
	$docup_p_id = strip_tags($_GET[docup_p_id]);

if (!is_numeric($doc_kind_id) or !is_numeric($docup_p_id))die('索引錯誤');

//取得登入人員所在處室
$post_office = "";
if($_SESSION[session_tea_sn] !=""){
	$query = "select post_office from teacher_post where teach_id='$_SESSION[session_tea_sn]' ";
	$result = $CONN->Execute($query);
	$post_office = $result->fields[0];
}

if ($is_standalone!="1") head("文件資料庫");
$doc_kind_id=intval($doc_kind_id);
$sql_select = "select docup_p_id,docup_p_name from docup_p\n";
$sql_select .= "where doc_kind_id = '$doc_kind_id' ";
$result = $CONN->Execute($sql_select);
while (!$result->EOF) {
	if($result->fields["docup_p_id"]== $docup_p_id)
		$state_kind .= "<option value=".$result->fields["docup_p_id"]." selected >".$result->fields["docup_p_name"] ."</option> \n";
	else
		$state_kind .= "<option value=".$result->fields["docup_p_id"].">".$result->fields["docup_p_name"] ."</option> \n";
	$result->MoveNext();
}

$state_kind .= "</select>\n <input type=hidden name=doc_kind_id value=$doc_kind_id>";
$docup_p_id=intval($docup_p_id);
$sql_select="select docup_p_name from docup_p where docup_p_id=$docup_p_id";
$result = $CONN->Execute($sql_select);
$state_name = $result->fields["state_name"];	
$docup_p_name = $result->fields["docup_p_name"];
$post_office_p =room_kind();
echo "<form name=\"pform\" method=get action=\"$SCRIPT_NAME\"><table align=center class=module_body width=100%><tr><td align=center ><a href=\"docup_list.php\">文件總表</a>&nbsp;&nbsp;||&nbsp;&nbsp;<a href=\"doc_kind_list.php?doc_kind_id=$doc_kind_id\">$post_office_p[$doc_kind_id]</a>&nbsp;&nbsp;||&nbsp;&nbsp;<select name=\"docup_p_id\"  size=1 onchange=\"document.pform.submit()\"> \n";
echo $state_kind ;
echo "&nbsp;&nbsp;<a href=\"doc_add.php?docup_p_id=$docup_p_id \">新增文件</a> || <a href=\"doc_search.php?doc_kind_id=$doc_kind_id\">搜尋</a></td></tr></table>";
?>

<table border="1" class=module_body cellspacing="0" cellpadding="0" width=100% >
  <tr>
    <td  bgcolor="#008000" align="center" width=60%><font color="#FFFFFF">文件名稱</font></td>
    <td  bgcolor="#008000" align="center"><font color="#FFFFFF">檔案大小</font></td>
    <td nowrap bgcolor="#008000" align="center" colspan=2><font color="#FFFFFF">動作</font></td>
    <td  bgcolor="#008000" align="center" nowrap><font color="#FFFFFF">建置人</font></td>
    <td  bgcolor="#008000" align="center"><font color="#FFFFFF">建置時間</font></td>
    <td  bgcolor="#008000" align="center"><font color="#FFFFFF">下載</font></td>
  </tr> 
<?php
//$sql_sel = "select * from docup where docup_p_id = '$docup_p_id' order by docup_id desc";
$docup_p_id=intval($docup_p_id);
$sql_sel = "select * from docup where docup_p_id = '$docup_p_id' order by docup_name ";
$result = $CONN->Execute($sql_sel);
//if ($result->RecordCount()>0)
//	mysql_data_seek($result,0);
while (!$result->EOF) {
	$docup_p_id = $result->fields["docup_p_id"];
	$docup_id = $result->fields["docup_id"];
	$docup_name = $result->fields["docup_name"];
	$docup_date = substr($result->fields["docup_date"],0,10);
	$docup_owner = $result->fields["docup_owner"];
	$docup_store = $result->fields["docup_store"];
	$docup_share = $result->fields["docup_share"];
	$docup_url  = $result->fields["url"];
	$teacher_sn = $result->fields["teacher_sn"];
	//echo $teacher_sn ;
	$docup_file_size = round(($result->fields["docup_file_size"] /1024),2) ." K";
	$download_str ="--";
	$dfile=explode(".",$docup_store);
	$filetype=strtolower(array_pop($dfile));
	$graph="images/".$filetype.".png";
	if ($docup_url) {
		$icon = "images/link.png";
		$ialt = "連結";
		$docup_file_size = "&nbsp;";
	}  else {
		if (file_exists($graph)) {
			$icon = $graph;
			$ialt = $filetype."檔";
		} else {
			$icon = "images/file.png";
			$ialt = "檔案";
		}
	}              
	echo ($i++ % 2 == 0)?"<TR class=nom_1>":"<TR class=nom_2>";
	echo "<TD><img src='$icon' alt='".$ialt."圖示'> $docup_name</TD>";
	echo "<TD align=right>$docup_file_size</TD>";
	//檔案所有人
	if ($_SESSION[session_tea_sn] == $teacher_sn ||  checkid($_SERVER[SCRIPT_FILENAME],1) ){
		echo "<TD align=center><a href=\"doc_update.php?docup_id=$docup_id&doc_kind_id=$doc_kind_id\">修改</a></TD>";
		echo "<TD align=center><a href=\"doc_delete.php?key=delete&docup_id=$docup_id&doc_kind_id=$doc_kind_id\">刪除</a></TD>";
		if ($docup_url) 
		           $download_str =  "<a href=\"$docup_url\" target='_blank'>連結</a>";
		else 	
		    $download_str =  "<a href=\"doc_download.php?docup_id=$docup_id\">下載</a>";
	}
	//本處室人員權限
	else if ($_SESSION[session_tea_sn] !="" && $post_office == $doc_kind_id){
		if (getperr($docup_share,1,1)) //下載權
		        if ($docup_url) 
		           $download_str =  "<a href=\"$docup_url\" target='_blank'>連結</a>";
		        else 		
			   $download_str =  "<a href=\"doc_download.php?docup_id=$docup_id\">下載</a>";		
		if (getperr($docup_share,1,2)) //修改權
			echo "<TD   align=center><a href=\"doc_update.php?docup_id=$docup_id&doc_kind_id=$doc_kind_id\">修改</a></TD>";
		else
			echo "<TD   align=center>-</TD>";
		if (getperr($docup_share,1,3)) //刪除權
			echo "<TD   align=center><a href=\"doc_delete.php?key=delete&docup_id=$docup_id&doc_kind_id=$doc_kind_id\">刪除</a></TD>";
		else
			echo "<TD   align=center>-</TD>";			
	}
	//本校登入人員
	else if ($_SESSION[session_tea_sn] !=""){ 
		if (getperr($docup_share,2,1)) //下載權
		        if ($docup_url) 
		           $download_str =  "<a href=\"$docup_url\" target='_blank'>連結</a>";
		        else 
			   $download_str =  "<a href=\"doc_download.php?docup_id=$docup_id\">下載</a>";
		if (getperr($docup_share,2,2)) //修改權
			echo "<TD   align=center><a href=\"doc_update.php?docup_id=$docup_id&doc_kind_id=$doc_kind_id\">修改</a></TD>";
		else
			echo "<TD   align=center>-</TD>";
		if (getperr($docup_share,2,3)) //刪除權
			echo "<TD   align=center><a href=\"doc_delete.php?key=delete&docup_id=$docup_id&doc_kind_id=$doc_kind_id\">刪除</a></TD>";
		else
			echo "<TD   align=center>-</TD>";
	}
	//網路來賓
	else {	
		echo "<TD   align=center colspan=2>--</TD>";			
		if (getperr($docup_share,3,1)) { //下載權 
		        if ($docup_url) 
		           $download_str =  "<a href=\"$docup_url\" target='_blank'>連結</a>";
		        else 
			   $download_str =  "<a href=\"doc_download.php?docup_id=$docup_id\">下載</a>";
		} 	
	}
	echo "<TD  align=center>$docup_owner</TD>";
	echo "<TD  noWrap align=center >$docup_date</TD>";
	echo "<TD  align=center>\n";
	echo $download_str;
	echo "</TD></TR>\n";
	$result->MoveNext();
}
echo "</TABLE></form>";
if ($is_standalone!="1") foot();
?>
