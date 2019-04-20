<?php
// 將圖檔從資料庫取出後秀出
// 這段程式的使用方式為 <img src="img_show.php">
// 這樣才能應用在其他網頁上
// 程式開始
include_once('config.php');
$kind_id=intval($_GET['kind_id']);
$id=intval($_GET['id']);

	$query="select * from jshow_pic where id='".$id."'";
	$res=$CONN->Execute($query) or die($query);
	$row= $res->fetchRow();	
	$filename=$row['filename'];
	
	//讀取圖檔
	 $sFP=fopen($USR_DESTINATION.$filename,"r");							//載入檔案
   $sFilesize=filesize($USR_DESTINATION.$filename); 				//檔案大小
   $sFiletype=filetype($USR_DESTINATION.$filename);  				//檔案屬性

	 $picture=fread($sFP,$sFilesize);

	 fclose($sFP);
	 
Header("Content-type: $sFiletype");
//Header("Content-type: images/gif");
// 請將資料庫內的圖片欄位的資料取出成 $picture 變數

echo $picture;

// 程式結束
?>