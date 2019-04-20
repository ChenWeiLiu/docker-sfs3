<?php
//$Id: $
$img=new pass_img();
if (intval($_REQUEST['num'])>0) {
	echo $img->getNum();
	$a = $img->chk();
	exit;
}
if ($_SESSION['imgArr'] && $_REQUEST['x'] && $_REQUEST['y']) $img->draw();
else $img->show();

class pass_img {

	var $width = 260;
	var $height = 200;
	var $dir = "./images/";
	var $backgroundImg = "grass.png";
	var $animals = array("kitten_01"=>"1", "kitten_02"=>"1", "rabbit"=>"1", "guinea_pig"=>"2", "chick"=>"4", "hamster"=>"6");
	var $img;
	var $imgArr = array();
	var $grid = 5;
	var $gMax = 0;
	var $gArr = array();
	var $overLap = 0.5;

	function __construct() {
		$this->gMax = intval($this->width/$this->grid) * intval($this->height/$this->grid);
		$img = imagecreatefrompng($this->dir.$this->backgroundImg);
		$this->img = imagecreatetruecolor($this->width, $this->height);
		imagecopy($this->img, $img, 0, 0, 0, 0, $this->width, $this->height);
		session_start();
		//unset($_SESSION["imgArr"]);
	}

	function getNum() {
		$nums = 0;
		if (count($_SESSION["onClick"])>0) foreach($_SESSION["onClick"] as $v) if ($v==1) $nums++;
		return $nums;
	}

	function chk() {
		$chk = 0;
		if ($this->getNum() != 2) return "Wrong!";
		foreach($_SESSION["onClick"] as $k=>$v) if ($v==1 && substr($_SESSION["imgArr"][$k][0], 0,1) == "k") $chk++;
		if ($chk == 2) {
			$_SESSION["kittenCheck"] = "OK";
			return "Right!";
		} else {
			$_SESSION["kittenCheck"] = "OH";
			return "Wrong!";
		}
	}

	function imagecopymerge_alpha($dstImg, $srcImg, $paddingX, $paddingY, $opacity) {
		//獲取原圖gd圖像標識
		$srcWidth = imagesx($srcImg);
		$srcHeight = imagesy($srcImg);

		//創建新圖
		$cutImg = imagecreatetruecolor($srcWidth, $srcHeight);
		//先截取欲疊合部份底圖
		imagecopy($cutImg, $dstImg, 0, 0, $paddingX, $paddingY, $srcWidth, $srcHeight);
		//將新圖畫到部份底圖上
		imagecopy($cutImg, $srcImg, 0, 0, 0, 0, $srcWidth, $srcHeight);
		//將畫好的疊合圖合併回原底圖, 並依alpha值處理透明度
		imagecopymerge($dstImg, $cutImg, $paddingX, $paddingY, 0, 0, $srcWidth, $srcHeight, 100-$opacity);
		return $dstImg;

		//分配顏色 + alpha，將顏色填充到新圖上
		//$alpha = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
		//imagefill($newImg, 0, 0, $alpha);

		//將原圖拷貝到新圖上，並設置在保存 PNG 圖像時保存完整的 alpha 通道信息
		//imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0, $srcWidth, $srcHeight, $srcWidth, $srcHeight);
		//imagesavealpha($newImg, true);
		//imagepng($newImg);
	}

	function show() {
		foreach($this->animals as $m => $mmax) {
			$img2 = imagecreatefrompng($this->dir.$m.".png");
			$posArr = $this->putPic(imagesx($img2), imagesy($img2), $mmax);
			foreach($posArr as $v) {
				//先畫小貓以外的動物
				if (substr($m, 0, 1)<>"k") $img = $this->imagecopymerge_alpha($this->img, $img2, $v[0], $v[1], 25);
				$this->imgArr[] = array($m, $v[0], $v[1], imagesx($img2), imagesy($img2));
			}
		}
		//再補畫小貓
		$kArr = array();
		foreach($this->imgArr as $k=>$i) {
			$img2 = imagecreatefrompng($this->dir.$i[0].".png");
			if (substr($i[0], 0, 1)=="k") $img = $this->imagecopymerge_alpha($this->img, $img2, $i[1], $i[2], 25);
			//取出小貓資料
			$kArr[] = $i;
			//在結果陣列中清除小貓資料
			unset($this->imgArr[$k]);
		}
		//將小貓資料補回陣列最後(為了解決圖片的疊合與選取問題)
		foreach($kArr as $i) $this->imgArr[] = $i;
		krsort($this->imgArr);
//exit;
		$_SESSION['imgArr'] = $this->imgArr;
		unset($_SESSION['onClick']);
		header('Content-Type: image/png');
		ImagePNG($img);
		ImageDestroy($img);
	}

	//以Session資料直接畫圖
	function draw() {
		$onClick = "";
		foreach($_SESSION['imgArr'] as $k=>$imgData) {
			if ($_REQUEST['x'] >= $imgData[1] && $_REQUEST['x'] <= ($imgData[1]+$imgData[3]) && $_REQUEST['y'] >= $imgData[2] && $_REQUEST['y'] <= ($imgData[2]+$imgData[4])) $onClick = $k;
		}
		if ($onClick!=="") {
			if ($_SESSION['onClick'][$onClick]==1) $_SESSION['onClick'][$onClick] = "";
			else $_SESSION['onClick'][$onClick] = 1;
		}
		foreach($_SESSION['imgArr'] as $k=>$imgData) {
			if ($_SESSION['onClick'][$k]==1) $img2 = imagecreatefrompng($this->dir.$imgData[0]."_highlight.png");
			else $img2 = imagecreatefrompng($this->dir.$imgData[0].".png");
			$img = $this->imagecopymerge_alpha($this->img, $img2, $imgData[1], $imgData[2], 25);
		}
		header('Content-Type: image/png');
		ImagePNG($img);
		ImageDestroy($img);
	}

	function putPic($picW, $picH, $num) {
		$tempArr = array();
		$effW = $this->width - $picW;
		$effH = $this->height - $picH;
		$gwMax = floor(($effW+1) / $this->grid); //計算最大格寬時先加1以避免剛好整除卻少算一格的情況
		$ghMax = floor(($effH+1) / $this->grid); //計算最大格高同上

		//陣列中標記無效點
		foreach($this->imgArr as $d) {
			$tempImg = imagecreatefrompng($this->dir.$d[0].".png");
			$xStart = intval(($d[1] - $picW * $this->overLap) / $this->grid);
			if ($xStart < 0) $xStart = 0;
			$xEnd = intval(($d[1] + imagesx($tempImg) - $picW * $this->overLap) / $this->grid);
			if ($xEnd > $gwMax) $xEnd = $gwMax;
			$yStart = intval(($d[2] - $picH * $this->overLap) / $this->grid);
			if ($yStart < 0) $yStart = 0;
			$yEnd = intval(($d[2] + imagesy($tempImg) - $picH * $this->overLap) / $this->grid);
			if ($yEnd > $ghMax) $yEnd = $ghMax;
			for($x = $xStart; $x <= $xEnd; $x++) {
				for($y = $yStart; $y <= $yEnd; $y++) {
					$tempArr[$x + $y * ($gwMax + 1)]++; //寬從0算起, 所以要加1
				}
			}
		}


		//隨機取點
		$rArr = array();
		for($i=1; $i<=$num; $i++) {
/*
echo "picW=$picW, picH=$picH ... $effW, $effH($gwMax, $ghMax) ... ".$this->width.", ".$this->height." ... ".intval($effW/$this->grid).", ".intval($effH/$this->grid)."<br><pre>";
print_r($this->imgArr);
print_r($rArr);
echo "</pre>";
$this->trace($tempArr, $gwMax, $ghMax);
*/
			//取有效點
			$arr = array();
			$arr = $this->getEffArr($tempArr, ($gwMax + 1) * ($ghMax + 1));
			$tempArr2 = $arr['arr'];
			$aMax = $arr['max'];
			if ($aMax <= 0) break;
			$rr = mt_rand(0,$aMax);
			if ($tempArr2[$rr] > 0) $rr=$tempArr2[$rr];
			$x = ($rr % ($gwMax + 1)) * $this->grid + intval(mt_rand(0, $this->grid));
			$y = intval($rr / ($gwMax + 1)) * $this->grid + intval(mt_rand(0, $this->grid));
			$uStart = intval(($x - $picW * $this->overLap) / $this->grid);
			if ($uStart < 0) $uStart = 0;
			$uEnd = intval(($x + $picW - $picW * $this->overLap) / $this->grid);
			if ($uEnd > $gwMax) $uEnd = $gwMax;
			$vStart = intval(($y - $picH * $this->overLap) / $this->grid);
			if ($vStart < 0) $vStart = 0;
			$vEnd = intval(($y + $picH - $picH * $this->overLap) / $this->grid);
			if ($vEnd > $ghMax) $vEnd = $ghMax;
//echo "<br><br>realU=".($rr % ($gwMax + 1)).", realV=".intval($rr / ($gwMax + 1))." uStart=$uStart, uEnd=$uEnd, vStart=$vStart, vEnd=$vEnd gwMax=$gwMax, ghMax=$ghMax<br>";
			for($u=$uStart; $u<=$uEnd; $u++) {
				for($v=$vStart; $v<=$vEnd; $v++) {
					$tempArr[$u + $v * ($gwMax + 1)]++; //寬從0算起, 所以要加1
//echo "rr=$rr, x=$x, y=$y, $u, $v, ".($u + $v * $gwMax)."<br>";
				}
			}
//$this->trace($tempArr, $gwMax, $ghMax);
			$rArr[] = array($x, $y);
		}
		return $rArr;
	}

	//取得有效點格
	function getEffArr($arr, $max) {
		$tempArr = array();
		krsort($arr);
		foreach($arr as $k=>$v) {
			if ($tempArr[$max]>0) $tempArr[$k] = $tempArr[$max];
			else $tempArr[$k] = $max;
			$max--;
		}
		return array('arr'=>$tempArr, 'max'=>$max);
	}

	//除錯用函式
	function trace($arr, $w, $h) {
		echo "0";
		for($i=0;$i<=9;$i++) for($j=0;$j<=9;$j++) echo $j;
		echo "<br>";
		for($j=0;$j<=$h;$j++) {
			echo ($j % 10);
			for($i=0;$i<=$w;$i++) {
				echo intval($arr[$i+$j*($w+1)]);
			}
			echo "<br>";
		}
		echo "<br><br>";
	}
}
?>
