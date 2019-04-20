<?php

// $Id: doc_in.php 8746 2016-01-08 15:41:01Z qfon $

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
$add_kind = array("0"=>"自動取得最後一個文號", "1"=>"參考上筆累加","2"=>"全部手動輸入");

///mysqli	
$mysqliconn = get_mysqli_conn();

if ($key =="登錄公文"){
	//$query = "insert into sch_doc1 (doc1_id,doc1_year_limit,doc1_kind,doc1_date,doc1_date_sign,doc1_unit,doc1_word,doc1_main,doc1_unit_num1,teach_id,doc1_k_id) values ('$doc1_id','$doc1_year_limit','$doc1_kind','$doc1_date','$doc1_date_sign','$doc1_unit','$doc1_word','$doc1_main','$doc1_unit_num1','$session_log_id','0')";
	//mysql_query($query);
///mysqli
$query = "insert into sch_doc1 (doc1_id,doc1_year_limit,doc1_kind,doc1_date,doc1_date_sign,doc1_unit,doc1_word,doc1_main,doc1_unit_num1,teach_id,doc1_k_id) values (?,?,?,?,?,?,?,?,?,?,'1')";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('ssssssssss',$doc1_id,$doc1_year_limit,$doc1_kind,$doc1_date,$doc1_date_sign,$doc1_unit,$doc1_word,$doc1_main,$doc1_unit_num1,$session_log_id);
$stmt->execute();
$stmt->close();

///mysqli	
	
	
	
}

include "header.php";
//預設來文日期
if ($doc1_date == "")
	$doc1_date = date("Y-m-j") ;
//預設收文時間
$doc1_date_sign = mysql_date();
//預設來文單位
if ($doc1_unit == "")
	$doc1_unit = $default_unit;

$year = date("Y")-1911;
//預設來文文號
if ($doc1_word == "")	
	$doc1_word = $year.$default_word;
$query = "select max(doc1_id)+1 mm from sch_doc1 where doc1_id like '$year%'";
$result = mysql_query ($query) or die ($query);
$row = mysql_fetch_row($result);
$mm = $row[0];

//年度第一件公文
if ($mm =="" ) 
	$mm =  sprintf ("%d%0$max_doc"."s",$year,1);

if ($add_kind_id == "1" && $doc1_id!="" ) //參考上筆
	$mm = $doc1_id + 1;
else if ($add_kind_id == "2") // 手動記錄
	$mm = "";
prog(1); //主menu (在 docword_config.php 中設定)

?>
<body  onload="setfocus()">
<script language="JavaScript"><!--
function setfocus() {
      document.myform.doc1_word.focus();
      return;
 }
 function sel_unit() {
	document.myform.doc1_unit_num1.value = document.myform.doc1_unit_sel.value;	
      return;
 }
 function sel_year() {
	document.myform.doc1_year_limit.value = document.myform.doc1_year_sel.value;
	
      return;
 }
// --></script>
<form action="<?php echo $PHP_SELF ?>" method = post name=myform >
<table border=1 cellpadding=0 cellspacing=0 align=center  >
<tr><td>
<table border=0 cellpadding=3 cellspacing=1  align=center >
<tr><td bgcolor=CCCCCC colspan=2 align=center>一般公文登錄作業</td></tr>
<tr><td>編號方式</td>
<td>
	<table cellspacing=3 ><tr>
	<?php
	while(list($tkey,$tvalue)= each ($add_kind)){
	if ($tkey == $add_kind_id)
		echo  "<td bgcolor=yellow><a href=\"$PHP_SELF?add_kind_id=$tkey\">$tvalue</td>";
	else
		echo  "<td ><a href=\"$PHP_SELF?add_kind_id=$tkey\">$tvalue</td>";
	}
	echo "<input type=\"hidden\" name=\"add_kind_id\" value=\"$add_kind_id\">";
	?>
	</tr></table>
</td></tr>
<tr>
	<td align="right" valign="middle">收發文號</td>
	<td><input type="text" size="10" maxlength="10" name="doc1_id" value="<?php echo $mm ?>">	
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
	<td><input type="text" name="doc1_main" size="60" ></td>
</tr>
<tr>
	<td align="right" valign="middle" rowspan=2>承辦單位</td>
	<td>
	代號: <input type="text" name="doc1_unit_sel" size="3" onchange="sel_unit()">
	&nbsp;&nbsp;保存年限: <input type="text" name="doc1_year_sel" size="3" onchange="sel_year()">
	</td>
</tr>
	
<tr>
	
	<td>
<select name=doc1_unit_num1 style="BACKGROUND-COLOR: #CCCCCC">
<?php
//承辦處室
$doc_unit_p = doc_unit();

while(list($tkey,$tvalue)= each ($doc_unit_p)){
	if ($tkey == $doc1_unit_num1)
		echo  sprintf ("<option value=\"%d\" selected>%d-%s</option>\n",$tkey,$tkey,$tvalue);
	else
		echo  sprintf ("<option value=\"%d\">%d-%s</option>\n",$tkey,$tkey,$tvalue);
}

?>
	</select>
&nbsp;&nbsp;保存年限 <select name=doc1_year_limit style="BACKGROUND-COLOR: #CCCCCC">
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
</select>(年)&nbsp;&nbsp;&nbsp; 
<a href="javascript:var aa=window.open('file_save.htm', 'external', 'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0')">檔案保存年限基準表</a>
</td>
</tr>
<tr>
	<td  colspan=2>
	<input type="submit" name="key" value="登錄公文"></td>
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
$query = "select * from sch_doc1 where doc1_k_id=0 and  doc1_date_sign > '$today' ";
$result = mysql_query($query);
//echo $query;
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
