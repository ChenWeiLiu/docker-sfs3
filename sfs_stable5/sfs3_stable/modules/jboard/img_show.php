<?php

// 將圖檔從資料庫取出後秀出
// 這段程式的使用方式為 <img src="img_show.php">
// 這樣才能應用在其他網頁上
// 程式開始
include_once('board_config.php');
$name = $_GET['name'];
$b_id = intval($_GET['b_id']);

///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
if ($name <> "") {
    $stmt = $mysqliconn->prepare("select filetype,content from jboard_images where b_id='$b_id' and filename=?");
    $stmt->bind_param('s', $name);
}
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($filetype, $picture);
$stmt->fetch();
$stmt->close();
///mysqli

/*
  $query="select filetype,content from jboard_images where b_id='".$b_id."' and filename='".$name."'";
  $res=$CONN->Execute($query);
  $filetype=$res->fields['filetype'];
  $picture=$res->fields['content'];
 */

Header("Content-type: $filetype");
//Header("Content-type: images/gif");
// 請將資料庫內的圖片欄位的資料取出成 $picture 變數
$picture = stripslashes(base64_decode($picture));
echo $picture;
// 程式結束
?>