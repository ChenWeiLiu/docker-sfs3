<?php
// $Id: postnews.php 8952 2016-08-29 02:23:59Z infodaes $

ob_start();
//session_start();

include "config.php";

if ($m_arr["IS_STANDALONE"]=='0'):
	//秀出網頁布景標頭
	head("新聞發佈");
 else:?>
<html lang="zh-TW">
<head>
<title>修改新聞</title>
<meta http-equiv="content-type" content="text/html; charset=Big5" >
</head>
<body>
<?php
 endif;

 //檢查使用者, 程式測試完就要打開
sfs_check();

/*  Convert image size. true color*/
//$src        來源檔案
//$dest        目的檔案
//$maxWidth    縮圖寬度
//$maxHeight    縮圖高度
//$quality    JPEG品質

function ResizeImage($src,$dest,$maxWidth,$maxHeight,$quality=95) {
	//檢查檔案是否存在
	if (file_exists($src)  && isset($dest)) {

		$destInfo  = pathInfo($dest);
		// getImageSize這個function回傳三個陣列,
		// [0] -> 圖片寬度
		// [1] -> 圖片長度
		// [2] -> 圖片格式 1:gif  2:jpg  3:png
		$srcSize   = getImageSize($src); //圖檔大小

		/*********************************************
		計算扁平率, 如果扁平率不同, 採用原圖的扁平率
		縮圖的運算原理如后：
		 1. $srcRatio 不動
		 2. 若是寬的圖片 ( $srcRatio > 1 )
			新的寬度 = 縮圖的最大寬
			新的高度 = 縮圖最大寬 / $srcRatio
		 3. 若是高的(或方形)圖片 ( $srcRatio <= 1 )
			先對調 maxWidth 及 maxHeight 例: 640 x 480 變成 480 x 640
			新的寬度 = 縮圖最大高 x $scrRatio
			新的高度 = 縮圖最大高

		利用此演算法來控制最大的寬(高)值 = 使用者自訂縮圖最大寬(高)
		*******************************************************/
		$srcRatio  = $srcSize[0]/$srcSize[1]; // 計算寬/高

		if ($srcRatio > 1) {
			$destSize[0] = $maxWidth;
			$destSize[1] = $maxWidth/$srcRatio;
		}
		else {
			// 對調 maxWidth 與 maxHeight
			$tempWidth = $maxWidth;
			$maxWidth = $maxHeight;
			$maxHeight = $tempWidth;
			// 算出新的寬與高
			$destSize[0] = $maxHeight*$srcRatio;
			$destSize[1] = $maxHeight;
		}

		//建立一個 True Color 的影像
		$destImage = imageCreateTrueColor($destSize[0],$destSize[1]);

		//根據副檔名讀取圖檔
		switch ($srcSize[2]) {
			case 2: $srcImage = imageCreateFromJpeg($src); break;
			case 3: $srcImage = imageCreateFromPng($src); break;
			default: return false; break;
		}

		//取樣縮圖
		ImageCopyResampled($destImage, $srcImage, 0, 0, 0, 0,$destSize[0],$destSize[1],$srcSize[0],$srcSize[1]);

		//輸出圖檔
		switch ($srcSize[2]) {
			case 2: imageJpeg($destImage,$dest,$quality); break;
			case 3: imagePng($destImage,$dest); break;
		}
		return true;
	}
	else {
		return false;
	}
}


function unzip($zipdir,$zipfn){
	//global $SFS_PATH,$UPLOAD_PATH;

	$is_win=ereg('win', strtolower($_SERVER['SERVER_SOFTWARE']))?true:false;

	//尋找 unzip 的所在目錄
	$whereunzip = exec("whereis -b unzip");
	$wuary = explode(" ",$whereunzip);
	$path_parts = pathinfo($wuary[1]);

	//echo "<hr>";
	//echo "	路徑： ".$path_parts["dirname"] . "<br>";
	//echo "	檔名： ".$path_parts["basename"] . "<br>";

	$ziptool=($is_win)?"UNZIP32.EXE":$path_parts['dirname'] ."/".$path_parts['basename'];

	$arg1=($is_win)?"START /min cmd /c ":"";
	$arg2=($is_win)?"-d":"-d";

	if (!file_exists($ziptool)) {
		die($ziptool."不存在！");
	}elseif(!file_exists($zipdir.$zipfn)) {
		die($zipdir.$zipfn."不存在！");
	}

	$cmd=$arg1." ".$ziptool." ".$zipdir.$zipfn." ".$arg2." ".$zipdir;

	$msg = exec($cmd,$output);
	for($i=0;$i<count($output);$i++){
		$msg .= "<br>".$output[$i];
	}
	unlink($zipdir.$zipfn);
	return $msg;
}


function submvup($dirname) {
	//先檢查資料夾, 若有下一層的資料夾, 把下一層子目錄內的檔案全部 move 回 $dirname 下
	$handle=opendir($dirname);
	$j = 0;
	while ($file = readdir($handle)) {
		$fname[$j] = $file;
		$j++;
	}
	closedir($handle);
	if (count($fname) > 2 ){
		//一個一個檔檢查, 看是否是資料夾,
		for ($i=2;$i<$j;$i++){
			$filestype = filetype($dirname.$fname[$i]);
			if($filestype == "dir"){
				//只處理下一層子目錄內的 file 或底下還有子目錄, 不予理會
				//把下一層子目錄內的所有檔案, move 回上層, 再把子目錄名刪除
				$sub_handle=opendir($dirname.$fname[$i]);
				$sub_j = 0;
				while ($sub_file = readdir($sub_handle)) {
					$sub_fn[$sub_j] = $sub_file;
					$sub_j++;
				}
				closedir($sub_handle);
				if ( $sub_j > 2) {
					for ($sub_i=2; $sub_i<$sub_j; $sub_i++){
						if (filetype($dirname.$fname[$i]."/".$sub_fn[$sub_i])=="file"){
							copy($dirname.$fname[$i]."/".$sub_fn[$sub_i],$dirname.$sub_fn[$sub_i]);
							unlink($dirname.$fname[$i]."/".$sub_fn[$sub_i]);
						}
					}
				}
				//檢查這個子目錄, 若?面已沒有任何檔案, rmdir()
				$sub_handle=opendir($dirname.$fname[$i]);
				$sub_j = 0;
				while ($sub_file = readdir($sub_handle)) {
					$sub_fn[$sub_j] = $sub_file;
					$sub_j++;
				}
				closedir($sub_handle);
				if ($sub_j == 2) rmdir($dirname.$fname[$i]);
			}
		}
	}
}



function dealimage($dirname,$fname){
	global $m_arr; //在 config.php 內就已取出
	//一個個檢查, 只處理 "檔案" 的部份, 若是 "目錄" 不予理會
	//ob_flush();
	$filestype = filetype($dirname.$fname);
	if($filestype == "dir"){
		echo "<center>檔名: ".$fname." 是資料夾, 跳過！</center>\n\r";
	}elseif((substr($fname,0,3)=="Si-") or (substr($fname,0,3)=="Mi-")){
		echo "<center>檔名: ".$fname." 已處理過, 跳過！</center>\n\r";
	}elseif($filestype == "file"){
		// 檢查圖片格式-> GetImageSize[2] 回傳值=> 1:gif  2:jpg  3:png
		$imagesize = GetImageSize($dirname.$fname);
		//  jpg png 類型以外的所有檔案, 刪除
		if ( $imagesize[2] != 2 and $imagesize[2] != 3){
			echo  "<center>檔名: ".$fname." 格式不符，己刪除！</center>\n\r";
			unlink($dirname.$fname);
		}else{
			$pn_dest_img_s = "Si-".$fname;
			$pn_dest_img_m = "Mi-".$fname;

			$pn_mwidth = ($m_arr["MWidth"]=="") ? 640 : $m_arr["MWidth"];
			$pn_mlength = ($m_arr["MLength"]=="") ? 480 : $m_arr["MLength"];
			$pn_swidth = ($m_arr["SWidth"]=="") ? 200 : $m_arr["SWidth"];
			$pn_slength = ($m_arr["SLength"]=="") ? 150 : $m_arr["SLength"];

			//呼叫縮圖函式
			ResizeImage($dirname.$fname,$dirname.$pn_dest_img_m,$pn_mwidth,$pn_mlength,95);
			ResizeImage($dirname.$fname,$dirname.$pn_dest_img_s,$pn_swidth,$pn_slength,95);
			unlink($dirname.$fname);
			//echo "　己完成。";
		}
	}
}

/***************
newsmig 資料表欄位

news_sno	->	流水號
title		->	新聞標題
posterid	->	貼新聞者的身份 id
news		->	新聞內容
postdate	->	時間
newslink	->	相關連結

****************/

//若status是空值, 就是新增
$pn_act = $_REQUEST["act"];
if ($pn_act=="") $pn_act = "add";
$pnStatus = "";

//$todayis = date("Y-m-d H:s",time()); 在module-cfg.php內己定義
//$timestamp = time();在module-cfg.php內己定義

$isDataOK = false;  //使用者所填的資料是否OK

//取得使用者資料
$teacher_id = $_SESSION["session_log_id"];
userdata($teacher_id);


//先進行資料檢查，若沒有空白欄位，再繼續以下動作
if ($_POST["btnOK"] or $_POST["btnOnce"] or $_POST["btnAll"]){
	$pn_title = $_POST["txtTitle"];
	$pn_news = $_POST["txtNews"];
	$pn_newslink = $_POST["txtNewsLink"];
	//$pn_act = $_POST["hdnAct"];
	$pn_rdsno = $_POST["hdnRdsno"];

	if ($pn_title=="" and $_POST["btnOK"]){
		$pnMsg = "沒填「標題」！請補填後，再點一次「送出」";
	}elseif ($pn_news=="" and $_POST["btnOK"]){
		$pnMsg = "沒填「新聞內容」！請補填後，再點一次「送出」";
	}else{
		$isDataOK = true;
	}
}else{
	$pn_rdsno = $_REQUEST["rdsno"];
}

//修改及刪除模式下, 須先檢查張貼者名稱是否正確, 不正確不可讓他動作
if (($pn_act=="mod" or $pn_act=="del") and $pn_rdsno!=""){
	$sqlis = "SELECT posterid \n\r";
	$sqlis.= "FROM newsmig\n\r";
	$sqlis.= "WHERE news_sno='$pn_rdsno\n\r'";
	$rsis = $CONN -> Execute($sqlis);
	if ($rsis) {
		list($pn_posterid) = $rsis -> FetchRow();
		if ($pn_posterid!=$teacher_id) {
			$pnMsg = "No.".$pn_rdsno." 本則新聞不是您所發佈, 請勿修改。目前為新增新聞模式";
			$pn_act="add";
			$pn_rdsno = "";
		}
	}else{
		$pnMsg = "<br>系統錯誤：".$CONN -> ErrorMsg()."</br>";
	}
}

/****
  `news_sno` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(60) default NULL,
  `posterid` varchar(10) default NULL,
  `news` text,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newslink` varchar(70) default NULL
******/


//先處理資料的異動, 取得 rdsno後, 再上傳圖片
if ($_POST["btnOK"] and $isDataOK){
	if ($pn_act=="add"){
		$sql ="INSERT INTO newsmig \n\r";
		$sql.="  (title,posterid,news,postdate,newslink) \n\r";
		$sql.="VALUES \n\r";
		$sql.="  ('$pn_title','$teacher_id','$pn_news','$todayis','$pn_newslink') \n\r";
		$rs= $CONN -> Execute($sql);
		if ($rs){
			$pnMsg = "<br><font color='blue' size='+2'>新聞新增成功!</font><br>";
		}else{
			$pnMsg = "<br><font color='blue' size='+2'>新聞新增失敗！因為系統發生：".$CONN->ErrorMsg()."</font><br>";
		}
		//寫入後, 立刻把 $pn_rdsno找出
		$sql = "SELECT news_sno FROM newsmig WHERE posterid='$teacher_id' AND postdate = '$todayis' ";
		$rs = $CONN -> Execute($sql);
		if ($rs) list($pn_rdsno) = $rs -> FetchRow();
	}elseif($pn_act=="mod"){
		$sql ="UPDATE newsmig \n\r";
		$sql.="SET	\n\r";
		$sql.="		title='$pn_title', \n\r";
		$sql.="		posterid='$teacher_id', \n\r";
		$sql.="		news='$pn_news', \n\r";
		$sql.="		postdate='$todayis', \n\r";
		$sql.="		newslink='$pn_newslink' \n\r";
		$sql.="WHERE news_sno='$pn_rdsno' \n\r";
		$rs= $CONN -> Execute($sql);
		if ($rs){
			$pnMsg = "<br><font color='blue' size='+2'>新聞修改成功!</font><br>";
			$pnStatus ="ModOk";
		}else{
			$pnMsg = "<br><font color='blue' size='+2'>新聞修改失敗！因為系統發生：".$CONN->ErrorMsg()."</font><br>";
		}
	}elseif($pn_act=="del"){
		$sql = "DELETE FROM newsmig WHERE news_sno='$pn_rdsno'";
		$rs = $CONN -> Execute($sql);
		if ($rs){
			//要把圖片也一併刪除, 移除 $savepath/$rdsno/ 資料夾及其內容
			$pn_dir = $savepath.$pn_rdsno."/";
			clearstatcache( );
			$fexist = file_exists($pn_dir);
			// 如果查得到本目錄, 再做刪除的動作
			if ($fexist){
				clearstatcache();
				$handle=opendir($pn_dir);
				$j = 0;
				while ($file = readdir($handle)) {
					$filesname[$j] = $file;
					$j++;
				}
				closedir($handle);
				for ($i=0;$i<=count($filesname);$i++){
					if (($filesname[$i]!=".") or ($filesname[$i]!="..")){
						unlink($pn_dir.$filesname[$i]);
					}
				}
				rmdir(rtrim($pn_dir,"/"));
			}

			//show 成功的 message
			$pnMsg = "<br><font color='blue' size='+2'>新聞刪除成功!</font><br>";
			$pnStatus = "DelOk";
		}else{
			$pnMsg = "<br><font color='blue' size='+2'>新聞刪除失敗！因為系統發生：".$CONN->ErrorMsg()."</font><br>";
		}
	}

	//echo "<br>".$sql."<br>";
}


//上傳圖檔目錄規劃如后： $UPLOAD_PATH/school/newsmig/$newsno/
//所以要先把新聞 insert into 至 table 取得 news_sno 後, 再上傳圖片
//只要依此規則, 到該目錄下檢查是否有檔案便知有沒有上傳圖片
clearstatcache();
$pn_dir = $savepath.$pn_rdsno."/";
$direxist=file_exists($pn_dir);
if (!$direxist){
	mkdir("$pn_dir",0777);
}

//上傳圖片方法一之處理, 注意, apache 會 time out

for ( $i=0;$i<12;$i++){
	$j = $i +1;
	// 判斷是否有上傳圖片,而且不得是修改模式
	if ($_FILES["fleImgName"]["name"][$i] != "" and $pn_act=="add"){
		$pn_src_img[$i]=$_FILES["fleImgName"]["name"][$i];
	}else{
		$pn_src_img[$i] = "none";
	}

	//把圖片上傳, 最後把 temp 檔刪除
	if ($pn_src_img[$i] != "none"){
		if (copy($_FILES["fleImgName"]["tmp_name"][$i],$pn_dir.$pn_src_img[$i])){
			$pnMsg .="<br>圖片 ".$j."：".$_FILES["fleImgName"]["name"][$i]." 上傳成功！<br>\n\r";
			//$pnMsg .="檔案 ".$j." 暫存：".$_FILES["fleImgName"]["tmp_name"][$i]."<br>\n\r";
			//$pnMsg .="檔案 ".$j." 種類：".$_FILES["fleImgName"]["type"][$i]."<br><br>";
			unlink($_FILES["fleImgName"]["tmp_name"][$i]);

		}else{
			$pnMsg .= "<br>圖片上傳失敗！<br>";
		}
	}
}

//上傳圖片方法二: 上傳 zip 檔
if ($_FILES["fleZip"]["name"][$i] != "" and $pn_act=="add"){
	$pn_src_zip = $_FILES["fleZip"]["name"];
	if(copy($_FILES["fleZip"]["tmp_name"],$pn_dir.$pn_src_zip)){
		$pnMsg .="<br>壓縮檔 ".$j."：".$_FILES["fleZip"]["name"]." 上傳成功！<br>\n\r";
		unlink($_FILES["fleZip"]["tmp_name"]);
		//開始解壓縮 -> 會在 pn_dir 下解開所有檔案, 回傳解壓縮時所產生的訊息
		$pnMsg .= unzip($pn_dir,$pn_src_zip);

		//解壓縮完, 檢查是否內含資料夾, 若有 -> 把底下的檔案移上來
		submvup($pn_dir);

		// 改成轉向另一支專門處理圖片的程式(或是函式)
	}
}else{
	$pn_src_zip = "none";
}


//把資料帶出來。
//放在這裡是因為，若做修改一定要先儲存再 select ,否則會找到舊資料, 重要是變數會被改掉
if ($pn_rdsno!=""){
	$sqlsh = "SELECT title,posterid,news,newslink \n\r";
	$sqlsh.= "FROM newsmig \n\r";
	$sqlsh.= "WHERE news_sno='$pn_rdsno\n\r'";
	$rssh = $CONN -> Execute($sqlsh);
	if ($rssh) {
		list($pn_title,$pn_posterid,$pn_news,$pn_newslink) = $rssh -> FetchRow();
	}else{
		$pnMsg .= "<br>找不到本筆新聞<br>";
	}
}

?>
<center>
<h3>
	<?
		echo $user_name."  你好　　";
		if($pn_act == "add"){
			echo "新增新聞";
		}elseif($pn_act == "mod"){
			echo "修改新聞";
		}elseif($pn_act == "del"){
			echo "刪除新聞";
		}
		if ( $pnStatus != "" ) {
			echo " 已完成 \n\r";
		}else{
			echo " 中 \n\r";
		}
	?>
</h3>
<form  name="frmPostNews" action="postnews.php" method="POST" enctype="multipart/form-data">

<?php
// 這段程式碼用途：處理圖片所以要加個 if , 新增時, 也是先 insert 入資料庫, 取得rdsno後,再處理
// 圖片。
if (($_POST["btnOK"] and $pn_act=="add") or $_POST["btnAll"]){
	echo "<center><hr> \n\r";

	//再把上傳目錄內的所有影像檔進行改造
	set_time_limit(0);

	//在postnews.php內 要注意的是, 幾個使用者輸入的資料仍須一直帶進帶出(用session)
	//if(!session_is_registered("newsmig_status")) {
    if(!isset($_SESSION["newsmig_status"])) {
		clearstatcache();
		$handle=opendir($pn_dir);
		$j = 0;
		while ($file = readdir($handle)) {
			$filesname[$j] = $file;
			$j++;
		}
		//若有3個檔+'.'及'..' -> 結束時 $filesname[0-4] , 但 $j = 5
		closedir($handle);


		for ($i=0;$i<$j;$i++){
			$sename = "imgfiles_".$i;
			//session_register($sename);
			$_SESSION[$sename]=$filesname[$i];
		}
		//session_register("newsmig_num");
		$_SESSION["newsmig_num"] = $j - 2; //圖片數目

		//session_register("newsmig_status"); //圖片處理狀態, 全部圖片處理完畢, 給予 "end"
		$_SESSION["newsmig_status"] = "start";

	}
	echo "<h3>上傳目錄內共有　".$_SESSION["newsmig_num"]."　個檔案</h3>\n\r";

	$MN = $_SESSION["newsmig_num"]+2;

	if($_POST["btnAll"]){
		for ($i=0;$i < $MN;$i++){
			$fn = "imgfiles_".$i;
			dealimage($pn_dir,$_SESSION[$fn]);
		}
		//所有檔案己處理完
		//session_unregister("newsmig_status");
		$newsmig_status="end";
		for ($i=0; $i<$MN; $i++){
			$sename = "imgfiles_".$i;
			//session_unregister($sename);
		}
		echo "<br>恭喜您！圖片己全部轉換成功。新聞新增完成，請點下方的「回新聞總覽」檢視。<hr>";
	}

	if ($_SESSION["newsmig_status"]=="start"){
		//for ($i=0;$i<$_SESSION["newsmig_no1"];$i++) echo "+";
		echo "<table width='620' align='center' cellspacing='2' bgcolor='#FBFF00'>\n\r";
		//echo "<tr><td colspan='2' align='center'><h3>請選擇你要執行的模式</h3></td></tr>\n\r";
		echo "<tr><td colspan='2'>&nbsp;</td></tr>\n\r";
		echo "<tr>\n\r";
		echo "	<td align='right' valign='top'>";
		echo "		<input type='submit' name='btnAll' value='一次解決(限20張內)'></td>\n\r";
		echo "	<td><ol><li>處理過程中，畫面會暫停很久，請耐心等候！</li>\n\r";
		echo "		<li>請勿傳超過20張以上，若圖片傳不上去，請資訊人員修正 php.ini 檔案上傳大小的限制。</li>";
		echo "		</ol>";
		echo "	</td> \n\r";
		echo "</tr> \n\r";
		echo "<tr><td colspan='2'>&nbsp;</td></tr>\n\r";
		echo "</table> \n\r";
	}

	$handle=opendir($pn_dir);
	//echo "SavePath is : ".$pn_dir."\n\r";
	//echo "<br>Directory handle: $handle\n\r";

	clearstatcache( );
	$fexist = file_exists($pn_dir);
	// 如果查得到本目錄
	if ($fexist){
		$handle=opendir($pn_dir);
		$j = 0;
		while ($file = readdir($handle)) {
			$pn_files[$j] = $file;
			$j++;
		}
		closedir($handle);
		//clearstatcache;
		echo "<br>";
		echo "<table align='center' width='620'> \n\r";
		echo "<tr><td colspan='2' align='center'>目前資料夾內的檔案：</td></tr>";
		echo "<tr><td colspan='2'>&nbsp;</td></tr>";

		for ($i=0;$i<$j;$i++){
			clearstatcache();
			$pn_filetype = filetype($pn_dir.$pn_files[$i]);
			//$k = $i - 1;
			$k = $i;
			echo "	<tr>";
			if ($pn_filetype == "dir"){
				echo "<td>第 $k 個檔是 : ".$pn_files[$i]."</td><td>Type :".$pn_filetype."--> 不予處理！</td>\n\r";
			}elseif (substr($pn_files[$i],1,2)=='i-'){
				echo "<td>第 $k 個檔是 : ".$pn_files[$i]."</td><td>Type :".$pn_filetype."--> OK! </td>\n\r";
			}else {
				echo "<td>第 $k 個檔是 : ".$pn_files[$i]."</td><td>Type :".$pn_filetype."--> 待處理...</td> \n\r";
			}
			echo "	</tr>\n\r";
		}
		echo "</table>\n\r";
	}
	echo "</center>\n\r";
	echo "<hr>";
}
?>

<table align="center" bgcolor="#B7FF00" width="620" border="0">
  <tr>
  	<td colspan="2" align="center" bgcolor="#1F03D4" height="36">
		<font color="#FBFF00">基本資料區</font>
	</td>
  </tr>
  <tr>
    <td height="40" width="110" align="right">日期：</td>
    <td><?php echo substr($todayis,0,10); ?>
		<input type="hidden" name="hdnDate" value="<?php echo $todayis; ?>">
	</td>
  </tr>
  <tr>
    <td height="50" width="110" align="right">標題：</td>
    <td>
		<input type="text" name="txtTitle" size="24" align="left" value="<?php echo $pn_title; ?>">　(必填)
	</td>
  </tr>
  <tr>
    <td height="40" width="110" align="right">新聞內容：<br>(必填)</td>
    <td><textarea name="txtNews" cols="42" rows="5" wrap="physical" ><?php echo $pn_news; ?></textarea></td>
  </tr>
  <tr>
    <td height="40" width="110" align="right">相關連結：</td>
    <td><input type="text" name="txtNewsLink" size="54" maxlength="70" align="left" value="<?php echo $pn_newslink; ?>"></td>
  </tr>
  <tr>
  	<td colspan="2" align="center" bgcolor="#1F03D4" height="36">
		<font color="#FBFF00">上傳圖片方法一：　一張張上傳，限十二張以內</font>
	</td>
  </tr>

  <?php
	$isDisable = "";
	if ($pn_act=="mod") $isDisable = "disabled";

  	//顯示12張圖片上傳畫面的迴圈
	for($i=0;$i<12;$i++){
		$j = $i+1;
		echo "  <tr>\n\r";
		echo "	<td width='110' align='right' height='28'>圖片".$j."</td>\n\r";
		echo "	<td><input ".$isDisable." size='46' type='file' name='fleImgName[]'></td>\n\r";
		echo "  </tr>\n\r";
	}
  ?>

  <tr>
  	<td colspan="2" align="center" bgcolor="#1F03D4" height="36">
		<font color="#FBFF00">上傳圖片方法二：　把所有圖片壓成一個zip檔，再上傳(建議30張以內)</font>
	</td>
  </tr>
  <tr>
  	<td align="right">Zip檔：</td>
  	<td><input <?php if ($pn_act=="mod") echo "disabled"; ?> size="46" type="file" name="fleZip"></td>
  </tr>
</table>
<br>

<input type="submit" name="btnOK" value="確定" >　
<input type="reset" name="btnCancel" value="清除重填">　
<input type="hidden" name="act" value="<?php echo $pn_act; ?>">
<input type="hidden" name="hdnRdsno" value="<?php echo $pn_rdsno; ?>">

</form>
<table cellspacing="0" border="1">
<tr bgcolor="#E1E0C9"><td><a href="newslist.php"><font size="+2">回新聞總覽</font></a></td></tr>
</table>
<br>
<table width="620" bgcolor="#CFFBFF">
	<tr>
	<td>
	<ul>
		<li>新聞標題及新聞內容，不得空白。</li>
		<br>
		<li>使用步驟：。</li>
		<br>
		<ol>
		<li>寫入新聞並上傳圖片</li><br>
			<dl>方法一：一張張傳，限十二張以內。</dl>
			<br>
			<dl>方法二：要先把所有圖片壓縮成zip格式，再上傳，沒有張數限制。但是php有上傳檔案大小的限制，詳洽資訊組長。</dl>
			<br>
			<dl><font color="#FF1D11">使用zip格式上傳圖片者請注意，壓縮檔內不得內含資料夾。</font></dl>
			<br>
			<dl>新聞圖片只接受<font color="#FF1D11"> png </font>及<font color="#FF1D11"> jpeg </font>的格式。</dl>
			<br>
		<li>壓縮圖片</li><br>
			<dl><b><font color="#FF1D11">上傳圖片後，系統會列出所有的圖片，請依指示再進行圖片的壓縮工作(一大張 --> 縮成 "中圖M-" 及 "小圖S-" 各一張)。</font></b></dl>
			<br>
			<dl>縮圖的Size可在模組的變數管理設定。</dl>
			<br>
		<li>以上兩個步驟，系統會自動處理。首先把圖片及文字輸入完，按一次「確定」，待系統找出所有圖片，
			自然會有進一步提示，只要再依指示便可處理完畢。</li>
		</ol>
		<br>
		<li><b><font color="#FF1D11">修改模式只能修改文字，無法重傳照片，若要修改照片，請刪除本則新聞重來。</font></b></li>
	</ul>
	</td>
	</tr>
</table>
<br>
<table bgcolor="#FF0004" width="620">
	<tr>
		<td align="left" width="15%" height="40"><font color="#51FF00">訊息：</font></td>
		<td align="left" width="85%"><font color="#51FF00"><?php echo $pnMsg; ?></font></td>
	</tr>
</table>
</center>

<?php
if ($m_arr["IS_STANDALONE"]=='0'):
	//SFS3佈景結尾
	foot();
else:?>
</body></html>
<?php
endif;
?>
