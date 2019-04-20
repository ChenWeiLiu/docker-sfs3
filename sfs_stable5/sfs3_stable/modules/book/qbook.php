<?php
                                                                                                                             
// $Id: qbook.php 9155 2017-10-06 05:04:54Z smallduh $

include "book_config.php";
include "header.php";
//  $page=0; //設定初始頁

$topage = $_REQUEST['topage'];
$page = $_REQUEST['page'];
$qbook_name = strip_tags($_REQUEST['qbook_name']);
$bookch1_id = $_REQUEST['bookch1_id'];
$showpage = $_REQUEST['showpage'];

$showp =array("10"=>"10本", "15"=>"15本",  "20"=>"20本",  "25"=>"25本",  "40"=>"40本");
if ($showpage=="" )$showpage="10";
if ($qbook_name != "") $topage=0;
	$qbook_name = trim (stripslashes($qbook_name));
if ($bookch1_id =="")
	$bookch1_id = "000";
$query = "select * from bookch1 order by bookch1_id";
$result = mysql_query($query,$conID);
//分類號選項
$tt=""; 
while ($row = mysql_fetch_array ($result)){
	if ($bookch1_id == $row["bookch1_id"] and $qbook_name=="" ){
		$tt .= sprintf(" <option value=\"%s\" selected>%s%s</option>",$row["bookch1_id"],$row["bookch1_id"],$row["bookch1_name"]);
		$bookch1_name= $row["bookch1_name"];
	}
	else
		$tt.= sprintf(" <option value=\"%s\" >%s%s</option>",$row["bookch1_id"],$row["bookch1_id"],$row["bookch1_name"]);

}
//頁數選項
if ($topage !="")
	$page = $topage;
//資料庫查詢

/*
$query = "select count(*) as cc from book ";
if ($qbook_name != "")
	$query .= " where book_name like '%$qbook_name%' or book_author like  '%$qbook_name%' or book_maker like  '%$qbook_name%' ";
else
	$query .= " where bookch1_id = '$bookch1_id'"; 

$result = mysql_query($query,$conID);
$row = mysql_fetch_array($result);
$cc = $row["cc"];
*/

///mysqli
$query = "select count(*) as cc from book ";
if ($qbook_name != "")
	$query .= " where book_name like ? or book_author like ? or book_maker like ? ";
else
	$query .= " where bookch1_id = ?";

$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($query);
if ($qbook_name != "")
{
	$qbook_namex="%$qbook_name%";
	$stmt->bind_param('sss',$qbook_namex,$qbook_namex,$qbook_namex);
}
else
{
    $stmt->bind_param('s',$bookch1_id);
}
$stmt->execute();
$stmt->bind_result($cc);
$stmt->fetch();
$stmt->close();
///mysqli


if ($cc % $showpage > 0 ) 
	$tolpage = intval($cc / $showpage)+1;
else
	$tolpage = intval($cc / $showpage);

if ($qbook_name != ""){
	//$query = "SELECT a.bookch1_name, b.* from bookch1 a, book b where  a.bookch1_id = b.bookch1_id";
	//$query .=" and(b.book_name like '%$qbook_name%' or book_author like  '%$qbook_name%' or book_maker like  '%$qbook_name%')";
	$query = "SELECT a.bookch1_name,b.bookch1_id,b.book_id,b.book_name,b.book_num,b.book_author,b.book_maker,b.book_myear,b.book_bind,b.book_dollar,b.book_price,b.book_gid,b.book_content,b.book_isborrow,b.book_isbn,b.book_isout,b.book_buy_date,b.create_time,b.update_time from bookch1 a, book b where  a.bookch1_id = b.bookch1_id";
	$query .=" and(b.book_name like ? or book_author like ? or book_maker like ?)";

}
else{
	//$query = "SELECT a.bookch1_name, b.* from bookch1 a, book b where  a.bookch1_id = b.bookch1_id";
	//$query .=" and a.bookch1_id = '$bookch1_id' order by book_id ";
	$query = "SELECT a.bookch1_name,b.bookch1_id,b.book_id,b.book_name,b.book_num,b.book_author,b.book_maker,b.book_myear,b.book_bind,b.book_dollar,b.book_price,b.book_gid,b.book_content,b.book_isborrow,b.book_isbn,b.book_isout,b.book_buy_date,b.create_time,b.update_time from bookch1 a, book b where  a.bookch1_id = b.bookch1_id";
	$query .=" and a.bookch1_id = ? order by book_id ";

}
$query .= " Limit ".($page*$showpage)." ,$showpage ";

//echo $query;
//exit();

///mysqli
$stmt = "";
$stmt = $mysqliconn->prepare($query);
if ($qbook_name != "")
{
	$qbook_namex="%$qbook_name%";
	$stmt->bind_param('sss',$qbook_namex,$qbook_namex,$qbook_namex);
}
else
{
    $stmt->bind_param('s',$bookch1_id);
}
$stmt->execute();

$stmt->bind_result($bookch1_name,$bookch1_id,$book_id,$book_name,$book_num,$book_author,$book_maker,$book_myear,$book_bind,$book_dollar,$book_price,$book_gid,$book_content,$book_isborrow,$book_isbn,$book_isout,$book_buy_date,$create_time,$update_time);

///mysqli
//$result = mysql_query($query,$conID);


$stopi=0;
echo "<form action=\"$PHP_SELF\" method=\"post\" name=\"bookform\">";
echo "<center><BR><table border=0 width=95% cellpadding=0 cellspacing=0>";
echo "<tr><td width=30% align=right valign=middle nowrap><font COLOR=GREEN size=2 >分類 <select name=\"bookch1_id\" size=1  onchange=\"document.bookform.topage.value=0;document.bookform.submit()\">";
echo $tt;
echo "</select>";
echo "第";
echo " <select name=\"topage\" size=1  onchange=\"document.bookform.submit()\">";
for ($i= 0 ; $i < $tolpage ;$i++)
	if ($page == $i)
		echo sprintf(" <option value=\"%d\" selected>%2d</option>",$i,$i+1);
	else
		echo sprintf(" <option value=\"%d\" >%2d</option>",$i,$i+1);

echo "</select>頁 ";
echo "顯示</font><select name=showpage  onchange=\"document.bookform.submit()\">";
reset($showp);
while(list($tkey,$tvalue)= each ($showp)){
	if ($tkey == $showpage)
		echo  sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
	else
		echo sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
}
echo "</select>   </td>";
echo " <td width=35% align=right valign=middle><font COLOR=GREEN size=2>關鍵字查詢：";
echo " <input type=text size=10  name=\"qbook_name\" onchange=\"document.bookform.submit()\"></font></td>";
echo "<td width=25% align=right valign=bottom>";
echo  sprintf("<font COLOR=GREEN size=2>第 %d 頁/共 %d 頁</font>",$page+1,$tolpage);
echo "</td><tr></table></center>";
echo "<hr width=95% size=4 color=#008000></form>";
echo "<table border=1 width=100%><tr bgcolor=#8080FF><td >編號</td><td >書目號</td><td >書名</td><td >作者</td><td >出版商</td><td >備註</td></tr>";
if ($qbook_name == "")
	echo "<caption><b><font color=red>$bookch1_name </font></b>書目查詢";
else
	echo "<caption><b><font color=red>$qbook_name </font></b>關鍵字查詢";
$ci = 0;
if ($page > 0)
	echo sprintf("&nbsp;&nbsp;&nbsp;<A HREF=%s?bookch1_id=%s&page=%d&qbook_name=$qbook_name&showpage=$showpage>上一頁</A>　",$PHP_SELF,$bookch1_id,$page-1);
if ($tolpage-($page+1)>0)
	echo sprintf("&nbsp;&nbsp;&nbsp;<A HREF=%s?bookch1_id=%s&page=%d&qbook_name=$qbook_name&showpage=$showpage>下一頁</A>",$PHP_SELF,$bookch1_id,$page+1);
echo "</caption>";
//while ($row = mysql_fetch_array($result) ){
while ($stmt->fetch()) {
	if ($ci % 2 == 1 )
		$bgcolor =" bgcolor=#FFFF80 ";
	else
		$bgcolor = "";
	if ($book_isout == 1)
		$is_out = "<font color=red>本書已借出</font>";
	else
		$is_out ="<font>　</font>";

	if ($qbook_name !=""){
		$replace_text = "<font color=red><B>$qbook_name</B></font>";
		echo sprintf("<tr %s><td>%3d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",$bgcolor,$page*$showpage+$stopi+1,$book_id,str_replace($qbook_name,$replace_text,$book_name),str_replace($qbook_name,$replace_text,$book_author),str_replace($qbook_name,$replace_text,$book_maker),$is_out);
	}
	else{
		echo sprintf("<tr %s><td>%3d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",$bgcolor,$page*$showpage+$stopi+1,$book_id,$book_name,$book_author,$book_maker,$is_out);
	}
	$ci++;
	$stopi++;
}
echo "</table>";
echo "<table  align=center width=100%><tr><td align=center>";
if ($page > 0)
	echo sprintf("<A HREF=%s?bookch1_id=%s&page=%d&qbook_name=$qbook_name&showpase=$showpage>上一頁</A>　",$PHP_SELF,$bookch1_id,$page-1);
if ($tolpage-($page+1)>0)
	echo sprintf("　<A HREF=%s?bookch1_id=%s&page=%d&qbook_name=$qbook_name&showpase=$showpage>下一頁</A>",$PHP_SELF,$bookch1_id,$page+1);
echo "</td></tr></table>";

include "footer.php";
?>
