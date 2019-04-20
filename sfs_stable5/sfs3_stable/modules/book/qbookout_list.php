<?php
                                                                                                                             
// $Id: qbookout_list.php 8723 2016-01-02 06:00:38Z qfon $

include "book_config.php";
include "header.php";
$class_name = class_base();
$bookch1_id = $_REQUEST['bookch1_id'];
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
		$tt.= sprintf(" <option value=\"%s\" >%s%s</option>",
	$row["bookch1_id"],
	$row["bookch1_id"],
	$row["bookch1_name"]);
}

$query = "SELECT count(*) from book,borrow  where book.book_id=borrow.book_id and  borrow.in_date =0 and borrow.curr_class_num <> 0  and book.bookch1_id =? order by book.book_id ";
///mysqli
$mysqliconn = get_mysqli_conn();
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s',$bookch1_id);
$stmt->execute();
$stmt->bind_result($tolnum);
$stmt->fetch();
$stmt->close();


//$query = "SELECT book.book_id, book.book_name, book.book_author, date_format(borrow.out_date,'%Y-%m-%d')as out_d ,to_days(curdate())-to_days(borrow.out_date)- $yetdate as yet,borrow.stud_id  from book,borrow  where book.book_id=borrow.book_id and  borrow.in_date =0 and borrow.curr_class_num <> 0  and book.bookch1_id ='$bookch1_id' order by book.book_id ";
$query = "SELECT book.book_id, book.book_name, book.book_author, date_format(borrow.out_date,'%Y-%m-%d')as out_d ,to_days(curdate())-to_days(borrow.out_date)- $yetdate as yet,borrow.stud_id  from book,borrow  where book.book_id=borrow.book_id and  borrow.in_date =0 and borrow.curr_class_num <> 0  and book.bookch1_id =? order by book.book_id ";
///mysqli
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s',$bookch1_id);
$stmt->execute();
$stmt->bind_result($book_id, $book_name, $book_author,$out_dx ,$yet,$stud_id );

///mysqli	

//$result = mysql_query($query,$conID) or die ($query);
//$tolnum = mysql_num_rows($result);
echo "<BR><h3><form action=\"$PHP_SELF\" method=\"post\" name=\"bookform\">";
echo "<center><select name=\"bookch1_id\" size=1  onchange=\"document.bookform.submit()\">";
echo $tt;
echo "</select>";
echo "借出 $tolnum 冊：統計時間：".date("Y-m-d")."</H3></center></form>";
echo "<table border=1 width=95% align=center>";
echo "<tr><td bgcolor=\"#8080FF\" width=20% align=center><strong>書號</strong></td>";
echo "<td bgcolor=\"#8080FF\" width=50% align=center><strong>書名</strong></td>";
echo "<td bgcolor=\"#8080FF\" width=15% align=center><strong>借閱人</strong></td>";
echo "<td bgcolor=\"#8080FF\" width=20% align=center><strong>借閱<br>日期</strong></td>";   
echo "</tr>";
//while($row = mysql_fetch_array($result)){
  while ($stmt->fetch()) {
	$query2 ="select stud_name,curr_class_num,stud_study_cond from stud_base where stud_id ='".$stud_id."'";
	$result2 = mysql_query($query2,$conID) or die ($query2);
	$row2 = mysql_fetch_array($result2);
	$cyear = $row2["curr_class_num"];
	$memo = "";
	if ($row2["stud_study_cond"]== 5){
		$memo ="(已畢業)";
	}
	$out_d = $out_dx ;
	if ($yet > $yetdate) 
		$out_d = "<font color=red>".$out_dx."</font>" ;
	echo sprintf("<tr><td>%s</td><td>%s</td><td nowrap>%s--%s %s</td><td nowrap>%s</td></tr>",$book_id,$book_name,$class_name[substr($cyear,0,3)],$row2["stud_name"],$memo,$out_d);
}
echo  "</table>";
echo "</center>";
include "footer.php"; 
?>
