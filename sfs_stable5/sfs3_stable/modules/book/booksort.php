<?php
                                                                                                                             
// $Id: booksort.php 5310 2009-01-10 07:57:56Z hami $

include "book_config.php";
include "header.php";
echo "<center>";
echo "<BR><font size=4><b>熱門書籍排行榜前 $sort_num 名  統計時間：".date("Y-n-j")."</b></font><BR>";
echo "<table border=1 width=80%>";
echo "<tr bgcolor=#8080FF><td width=80 align=center>序號</td><td nowrap align=center>書號</td><td nowrap align=center>書名</td><td width=100 align=center>一年內<br>借閱次數</td></tr>";
$prior_day  = date("Y/n/j",mktime(0,0,0,date("m")  ,date("d"),date("Y")-1));
$query = "SELECT count(*) as cc, book_id FROM borrow  where  out_date > '$prior_day' group by book_id order by cc desc limit 0,$sort_num ";
$result = mysql_query($query ,$conID) or die($query);
$i =1;
while ($row = mysql_fetch_array($result)){
	if ($i % 2 == 0)
		$bgcolor =" bgcolor=\"#FFFF80\" ";
	else
		$bgcolor ="";
	$query2 ="select book_name from book  where book_id='".$row["book_id"]."'";
	$result2 = mysql_query($query2,$conID) or die ($query2);
	$row2= mysql_fetch_array($result2);
	echo sprintf ("<tr %s ><td align=center >%d</td ><td align=center>%s</td><td>%s</td><td align=center>%d</td></tr>",$bgcolor,$i,$row["book_id"],$row2["book_name"],$row["cc"]);
	$i++;
}
echo "</table>";
echo "</center>";
include "footer.php";
?>
