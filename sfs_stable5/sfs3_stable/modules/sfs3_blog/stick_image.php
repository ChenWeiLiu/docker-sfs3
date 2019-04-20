<?php
// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

$bc_sn=($_POST['bc_sn'])?$_POST['bc_sn']:$_GET['bc_sn'];
$bh_sn=($_POST['bh_sn'])?$_POST['bh_sn']:$_GET['bh_sn'];
$image_name=($_POST['image_name'])?$_POST['image_name']:$_GET['image_name'];

if($_POST['s1']=="上傳圖檔" && $bc_sn) {
	content_upload_file($bc_sn);
}
if($_POST['s2']=="刪除" && $bc_sn && $image_name){
	unlink ($UPLOAD_PATH."blog/content/".$bc_sn."/".$image_name);

}

if(!$bc_sn) echo"<table bgcolor='#FFF08B' align='center'><tr><td><font color='#FF0000'>請先選擇文章！</font></td></tr></table><button onclick=\"window.close()\">關閉</button>";
else{
	//檔案滿了沒
	$quota_message=blog_quota($bh_sn);



	//尋找圖檔相關訊息
	$file_list.="<tr bgcolor='#FaFaFa'><td>檔名</td><td>大小</td><td align='center'>貼上</td><td align='center'>刪除</td></tr>";
	$image_path = $UPLOAD_PATH."blog/content/".$bc_sn;
	$handle=opendir($image_path);
	while ($file = readdir($handle)){
		if ($file != "." and $file != ".." ) {
			$url_str6=$UPLOAD_URL."blog/content/".$bc_sn."/".$file;
			$file_list.="<tr bgcolor='#FAFAFA'>
			<td onclick=\"window.open('$url_str6','$file','width=340,height=320,resizeable=yes,scrollbars=yes')\" style=\"cursor:help\">$file</td>
			<td>".round(filesize ($UPLOAD_PATH."blog/content/".$bc_sn."/".$file)/1000,1)."k</td>
			<td><button onclick=\"call('$url_str6')\">貼上一區</button><button onclick=\"call2('$url_str6')\">貼上二區</button></td>
			<td>
				<form action='{$_SERVER['PHP_SELF']}' method='POST'>
					<input type='hidden' name='bc_sn' value='$bc_sn'>
					<input type='hidden' name='image_name' value='$file'>
					<input type='submit' name='s2' value='刪除'>
				</form>
			</td>
			</tr>";

		}

	}
	echo "
	<table cellspacing='6' align='center' bgcolor='#F5E2FD' width='100%'>
	<tr><td>$quota_message[1]</td></tr>
	<tr><td>
	本篇文章用圖列表<br>
	<table>$file_list</table>
	</td></tr></table>
	";

	if($quota_message[0]=="1"){
		echo "
		<table cellspacing='6' align='center' bgcolor='#F3CEF8' width='100%'><tr><td>
		新增本篇文章用圖<br>
		<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method='post'>
			<input type='hidden' name='bc_sn' value='$bc_sn'>
			<input type='file' name='userdata' >
			<input type='submit' name='s1' value='上傳圖檔'><br>
		</form>
		</td></tr></table>
		<button onclick=\"window.close()\">關閉</button>
		";
	}else{
		echo "
		<table cellspacing='6' align='center' bgcolor='#F3CEF8' width='100%'><tr><td>
			您可上傳的空間已滿，請刪除不必要的圖檔或洽管理員為您加大空間！
		</td></tr></table>
		<button onclick=\"window.close()\">關閉</button>
		";
	}
}


//封面檔案上傳函式
    function content_upload_file($bc_sn){
		global $CONN,$UPLOAD_PATH;
        //判斷上傳檔案是否存在
        if (!$_FILES['userdata']['tmp_name']) blog_error("沒有傳入檔案代碼！請檢查！",256);
        if (!$_FILES['userdata']['name']) user_error("沒有傳入檔案代碼！請檢查！",256);
        if (!$_FILES['userdata']['size']) user_error("沒有傳入檔案代碼！請檢查！",256);
		if (!$bc_sn) blog_error("沒有傳入檔案代碼！請檢查！",256);
		$d_arr=explode(".",$_FILES['userdata']['name']);
		$new_name= $d_arr[0].".jpg";
        //複製檔案到指定位置
		if(!is_dir($UPLOAD_PATH."blog")) mkdir ($UPLOAD_PATH."blog", 0700);
		if(!is_dir($UPLOAD_PATH."blog/content")) mkdir ($UPLOAD_PATH."blog/content", 0700);
		if(!is_dir($UPLOAD_PATH."blog/content/".$bc_sn)) mkdir ($UPLOAD_PATH."blog/content/".$bc_sn, 0700);
        copy($_FILES['userdata']['tmp_name'], $UPLOAD_PATH."blog/content/".$bc_sn."/".$new_name);
		//呼叫縮圖函式
    	ImageCopyResizedTrue2($UPLOAD_PATH."blog/content/".$bc_sn."/".$new_name,$UPLOAD_PATH."blog/content/".$bc_sn."/".$new_name,320,320);
        //移除暫存檔
        unlink ($_FILES['userdata']['tmp_name']);

	}


/*  Convert image size. true color*/
    //$src        來源檔案
    //$dest        目的檔案
    //$maxWidth    縮圖寬度
    //$maxHeight    縮圖高度
    //$quality    JPEG品質
    function ImageCopyResizedTrue2($src,$dest,$maxWidth,$maxHeight,$quality=100) {

        //檢查檔案是否存在
        if (file_exists($src)  && isset($dest)) {

            $destInfo  = pathInfo($dest);
            $srcSize   = getImageSize($src); //圖檔大小
            $srcRatio  = $srcSize[0]/$srcSize[1]; // 計算寬/高
            $destRatio = $maxWidth/$maxHeight;
            if ($destRatio > $srcRatio) {
                $destSize[1] = $maxHeight;
                $destSize[0] = $maxHeight*$srcRatio;
            }
            else {
                $destSize[0] = $maxWidth;
                $destSize[1] = $maxWidth/$srcRatio;
            }


            //GIF 檔不支援輸出，因此將GIF轉成JPEG
            if ($destInfo['extension'] != "jpg") {
				echo "您所上傳的圖片副檔名不是jpg，系統已自動轉成jpg圖";
				$dest = substr_replace($dest, 'jpg', -3);
			}

            //建立一個 True Color 的影像
            $destImage = imageCreateTrueColor($destSize[0],$destSize[1]);

            //根據副檔名讀取圖檔
            switch ($srcSize[2]) {
                case 1: $srcImage = imageCreateFromGif($src); break;
                case 2: $srcImage = imageCreateFromJpeg($src); break;
                case 3: $srcImage = imageCreateFromPng($src); break;
                default: return false; break;
            }

            //取樣縮圖
            ImageCopyResampled($destImage, $srcImage, 0, 0, 0, 0,$destSize[0],$destSize[1],
                                $srcSize[0],$srcSize[1]);

            //輸出圖檔
            switch ($srcSize[2]) {
                case 1: case 2: imageJpeg($destImage,$dest,$quality); break;
                case 3: imagePng($destImage,$dest); break;
            }
            return true;
        }
        else {
            return false;
        }
    }
?>
<script language="JavaScript1.2">

	function call(url_str6){
		window.opener.fromsub(url_str6);
	}
	function call2(url_str6){
		window.opener.fromsub2(url_str6);
	}

</script>
