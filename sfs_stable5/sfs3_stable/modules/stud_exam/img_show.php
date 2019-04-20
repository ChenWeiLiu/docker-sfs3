<?php
// 將圖檔從資料庫取出後秀出
// 這段程式的使用方式為 <img src="img_show.php">
// 這樣才能應用在其他網頁上
// 程式開始
include_once('config.php');

$sn=intval($_GET['sn']);
$query="select filetype,content from resit_images where sn='".$sn."'";
$res=$CONN->Execute($query);
$filetype=$res->fields['filetype'];
$picture=$res->fields['content'];
Header("Content-type: $filetype");
//Header("Content-type: images/gif");
// 請將資料庫內的圖片欄位的資料取出成 $picture 變數
$picture=stripslashes(base64_decode($picture));
echo $picture;
// 程式結束
?>