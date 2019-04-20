<?php
                                                                                                                             
// $Id: classsort.php 5310 2009-01-10 07:57:56Z hami $

include "book_config.php";
include "header.php";
$seme_year = $_REQUEST['seme_year'];
if ($seme_year == "")
	$seme_year = curr_year();
$temp_year = sprintf("%03d%d",$seme_year,1);
$class_name = class_base($temp_year); 
$query = "select curr_class_num,count(book_id) as cc ,floor(curr_class_num/100) as ff  from borrow  where curr_class_num <>'' ";
$query .=" and  out_date <='".date( "Y-m-d", mktime(0,0,0,8,1,$seme_year+1912) )."' and out_date >'". date( "Y-m-d", mktime(0,0,0,8,1,$seme_year+1911) )."' ";
$query .= "group by ff order by cc desc,ff";
echo "<center>";
echo "<BR><form action=\"$PHP_SELF\" name= bookform method=\"post\">";
echo "<input type=text size=3  name=seme_year value=\"$seme_year\" onchange=\"document.bookform.submit()\"> <font size=4><b>學年度";
echo " 班級借閱排行榜，統計時間：".date("Y-m-d")."</font></b></form>";
echo "<table border=1  width=80%>";
echo "<tr bgcolor=#8080FF><td >序號</td><td width=80 align=center>班級</td><td nowrap align=center>$seme_year 學年度借閱冊數</td></tr>";
$i = 1;
$result = mysql_query($query ,$conID) or die($query);
$tol_all = 0;
while ($row = mysql_fetch_array($result)){
	if ($i % 2 == 0 )
		$bgcolor =" bgcolor=#FFFF80 ";
	else
		$bgcolor = "";
	$temp_class = substr($row["curr_class_num"],0,3);
	echo sprintf ("<tr %s><td align=right >%d</td><td align=center>%s</td><td align=center>%d</td></tr>",$bgcolor,$i,$class_name[$temp_class],$row["cc"]);
	$i++;
	$tol_all += $row["cc"];
}
echo "<tr bgcolor=#8080FF><td align=right colspan=2>$seme_year 學年度 合計</td><td align=center>$tol_all 冊</td></tr>";
echo "</table>";
echo "</center>";
include "footer.php";
?>
