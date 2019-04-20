<?php

// $Id: add_book.php 5394 2009-02-12 06:40:08Z brucelyc $

// --系統設定檔
include "book_config.php";
// --認證 session
session_start();
if(!checkid(substr($_SERVER['PHP_SELF'],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}
include "header.php";

if ($_POST['key']=="批次建立資料"){
	$rst=-1;
	 if ($_FILES['userdata']['size']>0 && is_uploaded_file($_FILES['userdata']['tmp_name']) && !$_FILES['userdata']['error']){
		move_uploaded_file($_FILES['userdata']['tmp_name'],$tmp_path."/book_data.txt");
		$fd=file($tmp_path."/book_data.txt");
		$i=1;
		while ($fd[$i]!=""){
			$fd[$i]=ereg_replace("'","",$fd[$i]);
			$fd[$i]=ereg_replace("\"","",$fd[$i]);
			$tt=split(chr(9),$fd[$i]);
			// 書號不可為空字串
			if ($tt[0] != '' && $tt[1] != ''){ 
				$sql_insert = "insert into book (bookch1_id,book_id,book_name,book_num,book_author,book_maker,book_myear,book_bind,book_dollar,book_price,book_gid,book_content,book_isborrow,book_isbn,book_buy_date) values ('$tt[0]','$tt[1] ','$tt[2] ','$tt[3]','$tt[4] ','$tt[5] ','$tt[6]','$tt[7]','$tt[8]','$tt[9]','$tt[10]','$tt[11] ','$tt[12]','$tt[13]','$tt[14]')";
				$result=@mysql_query("$sql_insert");  
				if ($result)
					print "$tt[1] -- $tt[2] 新增成功!<br>";
				else
					print "資料新增失敗!$sql_insert<br>";
			}
			$i++;
		}
		//更新統計資料
		$query = "select count(bookch1_id) as cc ,bookch1_id from book group by bookch1_id" ;
		$result = mysql_query($query);
		while ($row = mysql_fetch_row ($result)) {
			$query2 = "update bookch1 set tolnum= $row[0] where bookch1_id = '$row[1]' ";
			mysql_query ($query2);
		}
	}
	else {
		echo "請選擇一個以txt為附加檔名的純文字檔案!";
		exit;
	}
}
?>

<h3>圖書資料批次建檔</h3>  
<form action ="<?php echo $_SERVER['SCRIPT_NAME'] ?>" enctype="multipart/form-data" method=post>  

檔案：<input type=file name=userdata ><br><p>  
<input type=submit name=key value="批次建立資料">  
</form>  

圖書資料文字檔參考如下：<br>
建檔方式：以 excel 建立資料。存成 文字檔(Tab字元分隔) 的檔案型態，並保留第一列抬頭說明。
範例檔：<a href="Book3.txt">Book3.txt</a>

<?php
	include "footer.php";
?>
