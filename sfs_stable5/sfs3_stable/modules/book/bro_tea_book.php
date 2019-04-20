<?php
                                                                                                                             
// $Id: bro_tea_book.php 8753 2016-01-13 12:40:19Z qfon $

// --系統設定檔  
include "book_config.php";

$teach_id = $_REQUEST['teach_id'];
$book_id = $_REQUEST['book_id'];

//快速換至下一位借書者
if ($book_id == "33" || $book_id == "333333")
	header("Location: bro_tea_book.php");

if (!$un_limit_ip) {
        //檢查管理IP
        $is_man = 0;
        for($mi=0 ; $mi< count($man_ip) ;$mi++){
                if (check_home_ip($man_ip[$mi])){
                 $is_man = 1;
                 break;
                }
        }

        if (!$is_man)
                header("Location: err.php");
}


// --認證 session
//session_start();
//session_register("session_log_id");
if(!checkid(substr($_SERVER['PHP_SELF'],1))){
	include "header.php";
	include "$rlogin";
	include "footer.php";
	exit;
}

include "header.php";
/*
//刪除
if ($sel == "del"){
	$query ="delete from borrow where b_num='$b_num'";
	mysql_query($query);
	//設定為可借
	$query = "update book set book_isout=0 where book_id='$dbook_id'";
	$result = mysql_query($query)or die ($query);
}	
*/

$reader_flag = 0;
//謮者登入

$mysqliconn = get_mysqli_conn();

if ($teach_id !=""){
///mysqli
$query = "select teach_id,name from teacher_base  where teach_id = ? and teach_condition=0 ";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s',$teach_id);
$stmt->execute();
$stmt->bind_result($teach_id,$name);
$stmt->fetch();
$stmt->close();

///mysqli	
	
	//$query = "select teach_id,name from teacher_base  where teach_id = '$teach_id' and teach_condition=0 ";
	//$result = mysql_query($query)or die ($query); 
	//if ( mysql_num_rows($result) >0){
	//	$row= mysql_fetch_array($result);
	//	$name = $row["name"];
		$reader_flag = 1 ;
	//}
	
}
//借書處理
if ($book_id != ""){
$query = "select book_id,bookch1_id,book_name,book_author from book where book_id=? and book_isout=0 and book_isborrow=0";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s',$book_id);
$stmt->execute();
$stmt->bind_result($book_id,$bookch1_id,$book_name,$book_author);
$stmt->fetch();
$stmt->close();
	
	//$query = "select book_id,bookch1_id,book_name,book_author from book where book_id='$book_id' and book_isout=0 and book_isborrow=0";
	//$result = mysql_query($query)or die ($query); 
	$temp_bb = "<font color=red><b>找不到這本書或已被借出</b></font>";
	//if ( mysql_num_rows($result) >0){
		//$row= mysql_fetch_array($result);
		//$bookch1_id = $row["bookch1_id"];
		//$book_id = $row["book_id"];
		//$book_name = $row["book_name"];
		//$book_author = $row["book_author"];
		$temp_bb ="<table><tr><td>$book_id</td><td>$book_name</td><td>$book_author</td></tr></table>";

		//借書登記
		/*
		$query = "insert into borrow (stud_id, bookch1_id, book_id, out_date) values ('$teach_id', '$bookch1_id', '$book_id', '".$now."')";
		$result = mysql_query($query)or die ($query);
		*/
//mysqli			
$query = "insert into borrow (stud_id, bookch1_id, book_id, out_date) values (?, ?, ?, '".$now."')";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('sss',$teach_id,$bookch1_id,$book_id);
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
	//}
}
if ($reader_flag == 0){
?>
<body onload="setfocus()">
<script language="JavaScript"><!--
function setfocus() {
      document.bookform.teach_id.focus();
      return;
 }
// --></script>
<table><tr><td><h3>教師借書作業</h3></td><td><h4><a href="ret_tea_book.php">教師還書作業</a></h4></td></tr></table>
<form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="bookform">
輸入教師代號：<input type=text name="teach_id" size="12" onchange="document.bookform.submit()" >
</form>
<?php
}
else if ($reader_flag == 1){
?>
<body onload="setfocus()">
<script language="JavaScript"><!--
function setfocus() {
      document.bookform.book_id.focus();
      return;
 }
// --></script>

<table><tr><td><h3>教師借書作業</h3></td><td><h4><a href="ret_tea_book.php">教師還書作業</a></h4></td></tr></table>
<?php echo "教師姓名：$teach_id -- $name"; ?>
<form action ="<?php echo $PHP_SELF ?>" method="post" name="bookform">
輸入書號：<input type=text name="book_id" size="12" onchange="document.bookform.submit()" >&nbsp;&nbsp;<?php echo $temp_bb; ?><b>(下一位按33)</b>
<input type=hidden name="teach_id" value="<?php echo $teach_id ?>">

</form>
<?php
}
?>


<?php
//$query = "SELECT book.bookch1_id, book.book_id, book.book_name, book.book_num, borrow.stud_id,borrow.b_num,book.book_author,borrow.out_date, borrow.in_date FROM book , borrow where  book.book_id = borrow.book_id  and borrow.stud_id= '$teach_id' order by borrow.out_date desc ,borrow.in_date LIMIT 0, 10 ";
//$result = mysql_query($query)or die ($query); 

$query = "SELECT book.bookch1_id, book.book_id, book.book_name, book.book_num, borrow.stud_id,borrow.b_num,book.book_author,borrow.out_date, borrow.in_date FROM book , borrow where  book.book_id = borrow.book_id  and borrow.stud_id= ? order by borrow.out_date desc ,borrow.in_date LIMIT 0, 10 ";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s',$teach_id);
$stmt->execute();
$stmt->bind_result($bookch1_id, $book_id, $book_name, $book_num, $stud_id,$b_num,$book_author,$out_date, $in_date);

echo "<center><table border=1>";
echo "<tr bgcolor=#8080FF><td>總號</td><td>書號</td><td>書名</td><td>借閱日期</td><td>歸還日期</td></tr>";
//while ($row = mysql_fetch_array($result)){
  while ($stmt->fetch()) {
	/*
	$bookch1_id = $row["bookch1_id"];
	$book_id = $row["book_id"];
	$book_name = $row["book_name"];
	$book_num = $row["book_num"];
	$out_date = $row["out_date"];
	$in_date = $row["in_date"];
	$b_num = $row["b_num"];
	*/
	if ($in_date == 0){
		echo "<tr bgcolor=yellow >";
		$in_date = "尚未歸還";
	}
	else{
		echo "<tr>";
	}
	echo "<td>$bookch1_id</td>";
	echo "<td>$book_id</td>";
	echo "<td>$book_name</td>";
	echo "<td>$out_date</td>";
	echo "<td>$in_date</td>";
	echo "</tr>";
}
echo "</table>";
echo "</center>";
include "footer.php";
?>
