<?php

// $Id: doc_out.php 8746 2016-01-08 15:41:01Z qfon $

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

///mysqli	
$mysqliconn = get_mysqli_conn();

if ($key =="登錄公文"){
	//$query = "insert into sch_doc1 (doc1_id,doc1_year_limit,doc1_kind,doc1_date,doc1_date_sign,doc1_unit,doc1_word,doc1_main,doc1_unit_num1,teach_id,doc1_k_id) values ('$doc1_id','$doc1_year_limit','$doc1_kind','$doc1_date','$doc1_date_sign','$doc1_unit','$doc1_word','$doc1_main','$doc1_unit_num1','$session_log_id','1')";
	//mysql_query($query)or die($query);

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
//預設發文日期
if ($doc1_date == "")
	$doc1_date = date("Y-m-j") ;
//預設發文時間
$doc1_date_sign = mysql_date();
//預設發文單位
if ($doc1_unit == "")
	$doc1_unit = $default_out_unit;

$year = date("Y")-1911;

$query = "select max(doc1_id)+1 mm from sch_doc1 where doc1_id like '$year%'";
$result = mysql_query ($query) or die ($query);
$row = mysql_fetch_row($result);
$mm = $row[0];

if ($mm =="" )
	$mm = $year."00001";

//預設發文文號
if ($doc1_word == ""){
	$temp_mm = intval (substr($mm,strlen($year)));
	$doc1_word = $year.$default_out_word."第".$temp_mm."號";
}


	
prog(2); //主menu (在 docword_config.php 中設定)

?>

<form action="<?php echo $PHP_SELF ?>" method = post >
<table border=1 cellpadding=0 cellspacing=0 align=center  >
<tr><td>
<table border=0 cellpadding=3 cellspacing=1  align=center >
<tr><td bgcolor=CCCCCC colspan=2 align=center>發文登錄作業</td></tr>

<tr>
	<td align="right" valign="middle">收發文號</td>
	<td><input type="text" size="10" maxlength="10" name="doc1_id" value="<?php echo $mm ?>"></td>
</tr>

<tr>
	<td align="right" valign="middle">發文日期</td>
	<td><input type="text" size="10" maxlength="10" name="doc1_date" value="<?php echo $doc1_date ?>">
	&nbsp;&nbsp;收文時間 <input type="text" size="16" maxlength="19" name="doc1_date_sign" value="<?php echo $doc1_date_sign ?>"></td>
</tr>

<tr>
	<td align="right" valign="middle">受文者</td>
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
	<td align="right" valign="middle">發文字號</td>
	<td><input type="text" size="60" maxlength="60" name="doc1_word" value="<?php echo $doc1_word ?>"></td>
</tr>


<tr>
	<td align="right" valign="top">發文摘要</td>
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
</table>
<?php
include "footer.php";
?>
