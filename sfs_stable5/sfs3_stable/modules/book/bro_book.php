<?php
                                                                                                                             
// $Id: bro_book.php 8753 2016-01-13 12:40:19Z qfon $

// --系統設定檔  
include "book_config.php";
include "../../include/sfs_case_dataarray.php";

$study_cond=study_cond();
$book_id = $_REQUEST['book_id'];
$stud_id = $_REQUEST['stud_id'];

if ($book_id == "33" || $book_id == "333333")
	header("Location: bro_book.php");

if (!$un_limit_ip) {
	//檢查是否為內部 IP 
	if (!check_home_ip($man_ip))
		header("Location: err.php");
}
// --認證 session   
//session_start();
//session_register("session_log_id");
if(!checkid(substr($PHP_SELF,1))){
	include "header.php";
	include "$rlogin";
	include "footer.php";
	exit;
}

include "header.php";

//mysqli		
$mysqliconn = get_mysqli_conn();

/*
//刪除
if ($sel == "del")
{
	$query ="delete from borrow where b_num='$b_num'";
	mysql_query($query);
	//設定為可借
	$query = "update book
		set book_isout=0
      		where book_id='$dbook_id'";
      	$result = mysql_query($query)or die ($query);  
}	

*/
$reader_flag = 0;
//謮者登入
if ($stud_id !=""){
	$stud_id=substr($stud_id,0,7);
	$query = "select stud_name,curr_class_num,stud_study_cond,stud_study_year from stud_base where stud_id = '$stud_id' and stud_study_cond=0";
	$result = mysql_query($query)or die ($query);
	if ( mysql_num_rows($result) >0){
		$row= mysql_fetch_array($result);
		$stud_name = $row["stud_name"];
		if($row["stud_study_cond"]<>0) $stud_color="#FF0000"; else $stud_color="#000000";
		$stud_study_cond = $study_cond[($row["stud_study_cond"])];
		$stud_name ="<font color='$stud_color'>".$row["stud_name"]."($stud_study_cond)</font>";
		$curr_class_num =$row["curr_class_num"];
		$stud_study_year=$row["stud_study_year"];
		//檢查抓取學生大頭照
		$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$stud_id; 
		if (file_exists($img)) $img_link="<img src='".$UPLOAD_URL."photo/student/".$stud_study_year."/".$stud_id."' width=$pic_width border=1><br>"; else $img_link='';
		
		$reader_flag = 1 ;
	}
}
//借書處理
if ($book_id != ""){
	//檢查是否超出借閱本數
	$stud_id=substr($stud_id,0,7);
	$amount_limit_s=$amount_limit_s?$amount_limit_s:7;
	$query = "SELECT count(*) AS counter FROM borrow WHERE stud_id='$stud_id' and ISNULL(in_date)";
	$result = mysql_query($query)or die ($query);
	$row= mysql_fetch_array($result);
	if($row["counter"]>=$amount_limit_s) echo "<script language=\"Javascript\"> alert (\"本學生未歸還借書數：{$row['counter']}，已經達到模組變數設定的限制數： $amount_limit_s 本了。\\n\\n 請將欲借出的圖書收回！\")</script>";
	else {
		
		$query = "select book_id,bookch1_id,book_name,book_author from book where book_id='$book_id' and book_isout=0 and book_isborrow=0";
		$result = mysql_query($query)or die ($query); 
		
//mysqli		
$query = "select book_id,bookch1_id,book_name,book_author from book where book_id=? and book_isout=0 and book_isborrow=0";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s',$book_id);
$stmt->execute();
$stmt->bind_result($book_id,$bookch1_id,$book_name,$book_author);
$stmt->fetch();
$stmt->close();
//mysqli	
		
		//$temp_bb = "<font color=red><b>找不到這本書或已被借出</b></font>";
		$temp_bb = "<script language=\"Javascript\"> alert (\"找不到這本書或已被借出了！\")</script>";
		//if ( mysql_num_rows($result) >0){
		if(!empty($book_name)){
			/*
			$row= mysql_fetch_array($result);
			$bookch1_id = $row["bookch1_id"];
			$book_id = $row["book_id"];	
			$book_name = $row["book_name"];	
			$book_author = $row["book_author"];
			*/
			$temp_bb ="<table><tr><td>$book_id</td><td>$book_name</td><td>$book_authod</td></tr></table>" ;
			//借書登記
			/*
			$query = "insert into borrow(stud_id, bookch1_id, book_id, out_date,curr_class_num) values ('$stud_id', '$bookch1_id', '$book_id', '".$now."','$curr_class_num')";
			$result = mysql_query($query)or die ($query);
			*/
//mysqli			
$query = "insert into borrow(stud_id, bookch1_id, book_id, out_date,curr_class_num) values (?, ?, ?, '".$now."',?)";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('ssss',check_mysqli_param($stud_id),check_mysqli_param($bookch1_id),check_mysqli_param($book_id),check_mysqli_param($curr_class_num));
$stmt->execute();
$stmt->close();
//mysqli	
		
			//設定已借出
			/*
			$query = "update book set book_isout=1 where book_id='$book_id'";
			$result = mysql_query($query)or die ($query);
			*/
	
//mysqli			
$query = "update book set book_isout=1 where book_id=?";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s',$book_id);
$stmt->execute();
$stmt->close();
//mysqli	
		
			
			$reader_flag = 1 ;
		}
	}
}
if ($reader_flag == 0){
?>
<body onload="setfocus()">
<script language="JavaScript"><!--
function setfocus() {
      document.bookform.stud_id.focus();
      return;
 }
// --></script>
<table><tr><td><h3>學生借書作業</h3></td><td><h4><a href="ret_book.php">學生還書作業</a></h4></td></tr></table>
<form action ="<?php echo $PHP_SELF ?>" method="post" name="bookform">
輸入學號：<input type=text name="stud_id" size="12" onchange="document.bookform.submit()" >
</form>
<?php
}
else if ($reader_flag == 1)
{
?>
<body onload="setfocus()">
<script language="JavaScript"><!--
function setfocus() {
      document.bookform.book_id.focus();
      return;
 }
// --></script>

<form action ="<?php echo $PHP_SELF ?>" method="post" name="bookform">
<table width=100%><tr><td><h3>學生借書作業</h3></td><td><h4><a href="ret_book.php">學生還書作業</a></h4></td></tr>

<tr><td><?php echo $img_link ?></td><td align='left'><?php echo "◎學號：$stud_id<br><br>◎姓名：$stud_name "; ?></td></td><td align='center'>》 輸入書號 《<br><input type=text name="book_id" size="14" onchange="document.bookform.submit()" ><?php echo $temp_bb; ?><br><b>(下一位按33)</b>
<input type=hidden name="stud_id" value="<?php echo $stud_id ?>"></td></tr>
</table>
</form>
<?php
}
?>

<?php
$stud_id=substr($stud_id,0,7);
$query = "SELECT book.bookch1_id, book.book_id, book.book_name, book.book_num, borrow.stud_id,borrow.b_num,book.book_author,borrow.out_date, borrow.in_date FROM book , borrow where  book.book_id = borrow.book_id  and borrow.stud_id= '$stud_id' order by borrow.out_date desc ,borrow.in_date LIMIT 0, 10 ";
$result = mysql_query($query)or die ($query);
echo "<center><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111' id='AutoNumber1'>";
echo "<tr bgcolor=#8080FF align='center'><td>NO.</td><td>總號</td><td>書號</td><td>書名</td><td>借閱日期</td><td>歸還日期</td></tr>";
while ($row = mysql_fetch_array($result)){
	$i++;
	$bookch1_id = $row["bookch1_id"];
	$book_id = $row["book_id"];
	$book_name = $row["book_name"];
	$book_num = $row["book_num"];
	$out_date = $row["out_date"];
	$in_date = $row["in_date"];
	$b_num = $row["b_num"];
	if ($in_date == 0){
		echo "<tr bgcolor=yellow  align='center'>";
		$in_date = "尚未歸還";
	}
	else{
		echo "<tr align='center'>";
	}
	echo "<td>$i</td>";
	echo "<td>$bookch1_id</td>";
	echo "<td>$book_id</td>";
	echo "<td align='left'>$book_name</td>";
	echo "<td>$out_date</td>";
	echo "<td>$in_date</td>";
	echo "</tr>";
}
echo "</table>";
echo "</center>";
include "footer.php";
?>
