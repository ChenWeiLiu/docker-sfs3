<?php
                                                                                                                             
// $Id: book_edit.php 8753 2016-01-13 12:40:19Z qfon $

// --系統設定檔
include "book_config.php";
$store_path = get_store_path();

if (!$un_limit_ip) {

	//檢查是否為內部 IP 
	if (!check_home_ip($man_ip))
		header("Location: err.php");
}

 // --認證 session 
//session_start();
//session_register("session_log_id"); 

$book_id =$_REQUEST['book_id'];
$bookch1_id = $_REQUEST['bookch1_id'];
$qbook_name = $_REQUEST['qbook_name'];
$showpage = $_REQUEST['showpage'];

//mysqli
$mysqliconn = get_mysqli_conn();

if(!checkid(substr($_SERVER['PHP_SELF'],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include $path."/rlogin.php";
	include "footer.php"; 
	exit;
}
if ($_REQUEST['key'] == "回書目修改區"){
	$ss = sprintf("%s%s?book_id=%s&bookch1_id=%s&page=%d&qbook_name=%s&showpage=%s",$path_html,"$store_path/book_input.php",$book_id,$bookch1_id,$page,$qbook_name,$showpage);
	header("Location: ".$ss);   
	exit;
}
if ($_POST['key'] == "確定刪除"){
	/*
	$query = "delete from book where book_id = '$book_id'";
	mysql_query($query,$conID) or die($query);
	*/
///mysqli
$query = "delete from book where book_id = ?";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s', $book_id);
$stmt->execute();
$stmt->close();
///mysqli	
	
	/*
	$query = "update  bookch1 set tolnum = tolnum -1 where bookch1_id = '$bookch1_id'";
	mysql_query($query,$conID) or die($query);
	*/
///mysqli
$query = "update  bookch1 set tolnum = tolnum -1 where bookch1_id = ?";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s', $bookch1_id);
$stmt->execute();
$stmt->close();
///mysqli	
	
	$ss = sprintf("%s%s?book_id=%s&bookch1_id=%s&page=%d&qbook_name=%s&showpage=%s",$path_html,"$store_path/book_input.php",$book_id,$bookch1_id,$page,$qbook_name,$showpage );
	header("Location: ".$ss);
}
if ($_POST['key'] == "確定修改"){
	/*
	$sql_update = "update book set  bookch1_id='$_POST[bookch1_id]',book_name='$_POST[book_name] ',book_author='$_POST[book_author]',book_maker='$_POST[book_maker] ',book_myear='$_POST[book_myear]',book_bind='$_POST[book_bind]',book_price='$_POST[book_price]',book_content='$_POST[book_content] ',book_isborrow='$_POST[book_isborrow]',book_isbn='$_POST[book_isbn]' where book_id='$_POST[book_id]' ";
	mysql_query($sql_update,$conID) or die ($sql_update);
    */
///mysqli
$sql_update = "update book set  bookch1_id=?,book_name=?,book_author=?,book_maker=?,book_myear=?,book_bind=?,book_price=?,book_content=?,book_isborrow=?,book_isbn=? where book_id=? ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('sssssssssss', $_POST[bookch1_id],$_POST[book_name],$_POST[book_author],$_POST[book_maker],$_POST[book_myear],$_POST[book_bind],$_POST[book_price],$_POST[book_content],$_POST[book_isborrow],$_POST[book_isbn],$_POST[book_id]);
$stmt->execute();
$stmt->close();
///mysqli	
	
}

 ///mysqli
$sql_select = "select bookch1_id,book_id,book_name,book_author,book_maker,book_myear,book_bind,book_price,book_content,book_isborrow,book_isbn,book_isout,book_buy_date from book where book_id =?"; 
$stmt = "";
$stmt = $mysqliconn->prepare($sql_select);
$stmt->bind_param('s',$book_id);
$stmt->execute();
$stmt->bind_result($bookch1_id,$book_id,$book_name,$book_author,$book_maker,$book_myear,$book_bind,$book_price,$book_content,$book_isborrow,$book_isbn,$book_isout,$book_buy_date);
$stmt->fetch();
$stmt->close();

///mysqli

/*
$sql_select = "select bookch1_id,book_id,book_name,book_author,book_maker,book_myear,book_bind,book_price,book_content,book_isborrow,book_isbn,book_isout,book_buy_date from book where book_id ='$book_id'";
$result = mysql_query ($sql_select,$conID);
$row = mysql_fetch_array($result);
$bookch1_id = $row["bookch1_id"];
$book_id = $row["book_id"];
$book_name = $row["book_name"];	
$book_author = $row["book_author"];
$book_maker = $row["book_maker"];
$book_myear = $row["book_myear"];
$book_bind = $row["book_bind"];	
$book_price = $row["book_price"];	
$book_content = $row["book_content"];
$book_isborrow = $row["book_isborrow"];
$book_isbn = $row["book_isbn"];
$book_isout = $row["book_isout"];
*/


include "header.php";

if ($_POST['key'] =="刪除"){
	echo "<form method=\"post\" action=\"$_SERVER[PHP_SELF]\">";
	echo "<table width=100%>";  
	echo "<tr><td align=center>確定刪除<B><font color=red>$book_id $book_name</font></b>圖書</td></tr>\n";
	echo "<input type=hidden name=bookch1_id value=\"$bookch1_id\">\n";
	echo "<input type=hidden name=book_id value=\"$book_id\">\n";
	echo "<input type=hidden name=page value=\"$page\">\n";
	echo "<input type=hidden name=qbook_name value=\"$qbook_name\">\n";
	echo "<input type=hidden name=showpage value=\"$showpage\">\n";
	echo "<tr><td align=center><input type=submit name=key value=\"確定刪除\"></td></tr>\n";  
	echo "</form></table>\n";
	include "footer.php";
	exit;
}
?>
<center>
<form name="bookform" method="post" action ="<?php echo $_SERVER['PHP_SELF'] ?>">
<table>

<tr>
	<td align="right" valign="top">中國圖書分類號</td>
	<td>
	<select name=bookch1_id>
<?php
$query = "select * from bookch1 order by bookch1_id ";
$result = mysql_query($query ,$conID);
while ($row = mysql_fetch_array($result)){
	if ($row["bookch1_id"]==$bookch1_id)
		echo sprintf("<option value=\"%s\" selected>%s %s",$row["bookch1_id"],$row["bookch1_id"],$row["bookch1_name"]);
	else
		echo sprintf("<option value=\"%s\">%s %s",$row["bookch1_id"],$row["bookch1_id"],$row["bookch1_name"]);
}
?>
	</select>
	<input type="text" size="8" maxlength="8" name="book_no" value="<?php echo $book_sid ?>">
	</td>
</tr>


<tr>
	<td align="right" valign="top">圖書編號</td>
	<td><input type="text" size="8" maxlength="8" name="book_id" value="<?php echo $book_id ?>"></td>
</tr>


<tr>
	<td align="right" valign="top">書名</td>
	<td><input type="text" size="40" maxlength="40" name="book_name" value="<?php echo $book_name ?>"></td>
</tr>


<tr>
	<td align="right" valign="top">作者</td>
	<td><input type="text" size="20" maxlength="20" name="book_author" value="<?php echo $book_author ?>"></td>
</tr>


<tr>
	<td align="right" valign="top">出版商</td>
	<td><input type="text" size="20" maxlength="20" name="book_maker" value="<?php echo $book_maker ?>"></td>
</tr>


<tr>	
	<td align="right" valign="top">出版日期</td>
	<td><input type="text" size="10" maxlength="10" name="book_myear" value="<?php echo $book_myear ?>"> (格式：2000-8-1)</td>
</tr>


<tr>
	<td align="right" valign="top">裝訂</td>
	<td>
<?php 
if ($book_bind == "精裝"){
	echo "<input type=radio name=book_bind checked value=\"精裝\">精裝";
	echo "  &nbsp;<input type=radio name=book_bind value=\"平裝\">平裝";
}
else{
	echo "<input type=radio name=book_bind value=\"精裝\">精裝";
	echo "  &nbsp;<input type=radio name=book_bind checked value=\"平裝\">平裝";	
}
?>
 </td>
</tr>

<tr>
	<td align="right" valign="top">定價</td>
	<td><input type="text" size="11" maxlength="11" name="book_price" value="<?php echo $book_price ?>">元</td>
</tr>


<tr>
	<td align="right" valign="top">ISBN</td>
	<td><input type="text" size="13" maxlength="13" name="book_isbn" value="<?php echo $book_isbn ?>"></td>
</tr>

<tr>
	<td align="right" valign="top">內容簡介</td>
	<td><input type="text" size="40" maxlength="40" name="book_content" value="<?php echo $book_content ?>"></td>
</tr>


<tr>
	<td align="right" valign="top">是否外借</td>
	<td>
<?php 
if ($book_isborrow == "0"){
	echo "<input type=radio name=book_isborrow checked value=\"0\">是";
	echo "  &nbsp;<input type=radio name=book_isborrow value=\"1\">否";
}
else{
	echo "<input type=radio name=book_isborrow value=\"0\">是";
	echo "  &nbsp;<input type=radio name=book_isborrow checked value=\"1\">否";
}
?>
	</td>
</tr>

<tr>
	<td colspan=2 align=center><hr size=1>
	<input type=submit name=key value="回書目修改區"> &nbsp;
	<input type=button value="圖書資料匯入" OnClick="openwindow('import_from_html.php')"> &nbsp;
	<input type=submit name=key value="確定修改"> &nbsp;
<?php
	if($_SESSION['session_who']=="教師") echo "<input type=submit name=key value='刪除' onclick=\"return confirm('確定要刪除？');\">";
?>
	</td>
</tr>
</table>
<input type="hidden" name="book_id" value="<?php echo $book_id ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="qbook_name" value="<?php echo $qbook_name ?>">
<input type="hidden" name="showpage" value="<?php echo $showpage ?>">

</form>
</center>
<script language="JavaScript">
<!--
function openwindow(url_str){
	urls=url_str+"?ISBN="+document.bookform.book_isbn.value;
	win=window.open (urls,"new","toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=no,copyhistory=no,width=450,height=320");
	win.creator=self;
}
// -->
</script>
<?php
	include "footer.php";
?>
