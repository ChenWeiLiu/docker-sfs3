<?php
                                                                                                                             
// $Id: studsort.php 5310 2009-01-10 07:57:56Z hami $

include "book_config.php";
include "header.php";
$seme_year = $_REQUEST['seme_year'];
if ($seme_year == "")
	$seme_year = curr_year();
$query = "select stud_id,count(book_id) as cc from borrow  where curr_class_num <>'' ";
$query .=" and  out_date <='".date( "Y-m-d", mktime(0,0,0,8,1,$seme_year+1912) )."' and out_date >'". date( "Y-m-d", mktime(0,0,0,8,1,$seme_year+1911) )."' ";
$query .= "group by stud_id   order by cc desc limit 0,$sort_num ";

echo "<center>";
echo "<center>";
echo "<BR><form action=\"$PHP_SELF\" name= bookform method=\"post\">";
echo "<input type=text size=3  name=seme_year value=\"$seme_year\" onchange=\"document.bookform.submit()\"> <font size=4><b>學年度";
echo " 讀者借閱排行前 $sort_num 名，統計時間：".date("Y-m-d")."</font></b></form>";
echo "<table border=1 width=80% >";
echo "<tr bgcolor=#8080FF><td align=center>序號</td><td align=center>班級</td><td align=center width=20%>姓名</td><td align=center nowrap>$seme_year 學年度借閱冊數</td></tr>";
$result = mysql_query($query ,$conID) or die($query);
$i =1;
if(curr_seme()<2)
	$seme_year_temp = sprintf("%03d1",$seme_year);
else
	$seme_year_temp = sprintf("%03d2",$seme_year);

$class_name= class_base($seme_year_temp);
while ($row = mysql_fetch_array($result)){
	if ($i % 2 == 0)
		$bgcolor =" bgcolor=\"#FFFF80\" ";
	else
		$bgcolor ="";

	$query2 ="select stud_name,curr_class_num,stud_study_cond from stud_base  where stud_id='".$row["stud_id"]."'";
	$result2 = mysql_query($query2,$conID) or die ($query2);
	$row2= mysql_fetch_array($result2);
	$cyear = $row2["curr_class_num"];
	$memo = "";
	if ($row2["stud_study_cond"]== 5){
		$memo ="(已畢業)";
	}
	
	echo sprintf ("<tr %s ><td align=center >%d</td ><td align=center>%s %s</td><td align=center>%s</td><td align=center>%d</td></tr>",$bgcolor,$i,$class_name[substr($cyear,0,3)],$memo,$row2["stud_name"],$row["cc"]);
	$i++;
}
echo "</table>";
echo "</center>";
include "footer.php";
?>
