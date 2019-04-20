<?php

// $Id: doc_edit.php 8746 2016-01-08 15:41:01Z qfon $

//載入設定檔
include "docword_config.php";
// session 認證
//session_start();
//session_register("session_log_id");


if(!checkid($PHP_SELF)){
	$go_back=1; //回到自已的認證畫面
	include "header.php";
	include $SFS_PATH."/rlogin.php";
	include "footer.php";
	exit;
}
else
	$ischecked = true;
//-----------------------------------
include "header.php";
//沒有設定時回到收文表列
if ($doc1_id == "") {
	header ("Location: doc1_list.php");
	exit;
}

///mysqli	
$mysqliconn = get_mysqli_conn();

if ($key =="修改"){
	//$query = "update sch_doc1 set doc1_year_limit='$doc1_year_limit',doc1_kind='$doc1_kind',doc1_date='$doc1_date',doc1_date_sign='$doc1_date_sign',doc1_unit='$doc1_unit',doc1_word='$doc1_word',doc1_main='$doc1_main',doc1_unit_num1='$doc1_unit_num1',doc1_infile_date='$doc1_infile_date',doc_stat='$doc_stat',doc1_end_date='$doc1_end_date',teach_id='$session_log_id' where doc1_id='$doc1_id'";	
	//mysql_query($query)or die ($query);
///mysqli
$query = "update sch_doc1 set doc1_year_limit=?,doc1_kind=?,doc1_date=?,doc1_date_sign=?,doc1_unit=?,doc1_word=?,doc1_main=?,doc1_unit_num1=?,doc1_infile_date=?,doc_stat=?,doc1_end_date=?,teach_id=? where doc1_id=?";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('sssssssssssss',$doc1_year_limit,$doc1_kind,$doc1_date,$doc1_date_sign,$doc1_unit,$doc1_word,$doc1_main,$doc1_unit_num1,$doc1_infile_date,$doc_stat,$doc1_end_date,$session_log_id,$doc1_id);
$stmt->execute();
$stmt->close();

///mysqli		
	
	
	echo "<h1><center>編號 : $doc1_id 修改成功!!</center></h1></body></html>";		
	redir( "$PHP_SELF?doc1_id=$doc1_id" , 1);
	exit;			
}
if ($key == "刪除"){
	//$query = "delete from sch_doc1 where doc1_id = '$doc1_id' ";
	//mysql_query($query)or die ($query);
///mysqli
$query = "delete from sch_doc1 where doc1_id =?  ";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s',$doc1_id);
$stmt->execute();
$stmt->close();

///mysqli		
	
	
	echo "<h1><center>編號 : $doc1_id 已刪除!!</center></h1></body></html>";		
	redir( "doc1_list.php" , 1);
	exit;			
}

 ///mysqli	
$sql_select = "select doc1_id,doc1_year_limit,doc1_kind,doc1_date,doc1_date_sign,doc1_unit,doc1_word,doc1_main,doc1_unit_num1,doc1_unit_num2,teach_id,doc1_k_id,doc_stat,doc1_end_date,doc1_infile_date,do_teacher from sch_doc1 where doc1_id=? ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_select);
$stmt->bind_param('s',$doc1_id);
$stmt->execute();
$stmt->bind_result($doc1_id,$doc1_year_limit,$doc1_kind,$doc1_date,$doc1_date_sign,$doc1_unit,$doc1_word,$doc1_main,$doc1_unit_num1,$doc1_unit_num2,$teach_id,$doc1_k_id,$doc_stat,$doc1_end_date,$doc1_infile_date,$do_teacher );
$stmt->fetch();
$stmt->close();
///mysqli

/*
$sql_select = "select * from sch_doc1 where doc1_id='$doc1_id'";
$result = mysql_query ($sql_select,$conID);

while ($row = mysql_fetch_array($result)) {

	$doc1_id = $row["doc1_id"];
	$doc1_year_limit = $row["doc1_year_limit"];
	$doc1_kind = $row["doc1_kind"];
	$doc1_date = $row["doc1_date"];
	$doc1_date_sign = $row["doc1_date_sign"];
	$doc1_unit = $row["doc1_unit"];
	$doc1_word = $row["doc1_word"];
	$doc1_main = $row["doc1_main"];
	$doc1_unit_num1 = $row["doc1_unit_num1"];
	$doc1_unit_num2 = $row["doc1_unit_num2"];
	$teach_id = $row["teach_id"];
	$doc_stat = $row["doc_stat"];
	$doc1_infile_date = $row["doc1_infile_date"]; 
	$doc1_end_date = $row["doc1_end_date"];

};
*/


prog(1); //上方menu (在 docword_config.php 中設定)
?>
<form action="<?php echo $PHP_SELF ?>" method = post >
<table border=1 cellpadding=0 cellspacing=0 align=center  >
<tr><td>
<table border=0 cellpadding=3 cellspacing=1  align=center bgcolor=yellow>
<tr><td bgcolor=CCCCCC colspan=2 align=center>公文修改作業</td></tr>

<tr>
	<td align="right" valign="middle">收發文號</td>
	<td>
	<?php
	echo $doc1_id;
	echo "<input type=hidden name=doc1_id value=\"$doc1_id\">";
	?>
	 </td>
</tr>

<tr>
	<td align="right" valign="middle">來文日期</td>
	<td><input type="text" size="10" maxlength="10" name="doc1_date" value="<?php echo $doc1_date ?>">
	&nbsp;&nbsp;收文時間 <input type="text" size="16" maxlength="19" name="doc1_date_sign" value="<?php echo $doc1_date_sign ?>"></td>
</tr>

<tr>
	<td align="right" valign="middle">來文單位</td>
	<td><input type="text" size="40" maxlength="60" name="doc1_unit" value="<?php echo $doc1_unit ?>"></td>
</tr>


<tr>
	<td align="right" valign="middle">公文類別</td>
	<td><select  name="doc1_kind" >
<?php
//公文類別(在 docword_config.php 中設定)
$doc_kind_p = doc_kind();
while(list($tkey,$tvalue)= each ($doc_kind_p)){
	if ($tkey == $doc1_kind)
		echo  sprintf ("<option value=\"%d\" selected>%s</option>\n",$tkey,$tvalue);
	else
		echo  sprintf ("<option value=\"%d\">%s</option>\n",$tkey,$tvalue);
}
?>
	</select></td>
</tr>



<tr>
	<td align="right" valign="middle">來文字號</td>
	<td><input type="text" size="60" maxlength="60" name="doc1_word" value="<?php echo $doc1_word ?>"></td>
</tr>


<tr>
	<td align="right" valign="top">來文摘要</td>
	<td><input type="text" name="doc1_main" size="60" value="<?php echo $doc1_main ?>"></td>
</tr>


<tr>
	<td align="right" valign="middle">承辦單位</td>
	<td><select name=doc1_unit_num1>
<?php
//承辦處室(在 docword_config.php 中設定)
$doc_unit_p = doc_unit();
while(list($tkey,$tvalue)= each ($doc_unit_p)){
	if ($tkey == $doc1_unit_num1)
		echo  sprintf ("<option value=\"%d\" selected>%s</option>\n",$tkey,$tvalue);
	else
		echo  sprintf ("<option value=\"%d\">%s</option>\n",$tkey,$tvalue);
}

?>
	</select>
&nbsp;&nbsp;保存年限 <select name=doc1_year_limit>
<?php
//保存年限(在 docword_config.php 中設定)
$doc_life_p = doc_life();
while(list($tkey,$tvalue)= each ($doc_life_p)){
	if ($tkey == $doc1_year_limit)
		echo  sprintf ("<option value=\"%d\" selected>%s</option>\n",$tkey,$tvalue);
	else
		echo  sprintf ("<option value=\"%d\">%s</option>\n",$tkey,$tvalue);
}
?>	
</select>(年)
</td>
</tr>
<tr>
	<td align="right" valign="middle">公文狀態</td>
<td> 
<select name="doc_stat">
<?php
//(公文狀態 $doc_stat_array 在 docword_config.php 中設定)
while(list($tkey,$tvalue)= each ($doc_stat_array)){
	if ($tkey == $doc_stat)
		echo  sprintf ("<option value=\"%d\" selected>%s</option>\n",$tkey,$tvalue);
	else
		echo  sprintf ("<option value=\"%d\">%s</option>\n",$tkey,$tvalue);
}
?>
</select>&nbsp;&nbsp;歸檔日期:
<input type="text" name="doc1_infile_date" size="10" value="<?php echo $doc1_infile_date ?>">
&nbsp;&nbsp;銷毀日期:
<input type="text" name="doc1_end_date" size="10" value="<?php echo $doc1_end_date ?>">

</td>
</tr>
<tr>
	<td  colspan=2 align=center>
	<input type="submit" name="key" value="修改">&nbsp;&nbsp;
	<input type="submit" name="key" value="刪除" onClick="return confirm('文號:<?php echo $doc1_id ?>\n確定刪除這筆記錄?')">
	</td>
</tr>

</table>
</form>
</td>
</tr>
<tr>
<td>
<?php
//顯示今日登錄件數

$today  = date("Y-m-d");
$query = "select * from sch_doc1 where doc1_k_id=0 and doc1_date_sign > '$today' ";
$result = mysql_query($query);
echo "<center><b>今日來文</b></center>";
echo "<table width=100% ><tr bgcolor=#C0C0C0><td>文號</td><td>來文單位</td><td>摘要</td><td>承辦處室</td></tr>";
while($row = mysql_fetch_array($result)) {
	$unit_temp = $doc_unit_p[$row[doc1_unit_num1]]; //取得處室名稱
	if ($i++ % 2 == 0)
		echo "<tr bgcolor=#AAEEAA>";
	else
		echo "<tr>";
	echo "<td><a href=\"doc_edit.php?doc1_id=$row[doc1_id]\">$row[doc1_id]</a></td><td>$row[doc1_unit]</td><td>$row[doc1_main]</td><td>$unit_temp</td></tr>";
}
echo "</table>";
echo "</td></tr></table>";
include "footer.php";
?>
