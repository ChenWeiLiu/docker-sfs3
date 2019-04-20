<?php
// $Id: headnews.php 5310 2009-01-10 07:57:56Z hami $
ob_start();
session_start();
include("config.php");
?>
<html>
<head>
<meta content="text/html; charset=big5" http-equiv="content-type">
</head>

<?php

if ($m_arr["hn_bgimg"]=='1'){
	//是否顯示背景
	echo "<body background='images/backg.gif'topmargin='2'>";
}else{
	echo "<body topmargin='2'>";
}

$sqlLast = "SELECT news_sno,title,posterid,news,postdate \n\r";
$sqlLast .= "FROM newsmig ORDER BY postdate DESC \n\r";
$rsLast = $CONN -> Execute($sqlLast);
$rownums = $rsLast -> RecordCount();

//以亂數隨機取前五則之其中一則當作HeadNews, 不過若不足五則, 要酌減
if ($rownums < 5) {
	$k = $rownums - 1;
}else{
	$k = 4;
}
// k=0 第一筆, k=1 第二筆, 如果 k = -1 資料庫內沒有資料 -> 不必show headnews
if ($k >= 0) {
	if($k==0){
		$i = 0;
	}else{
		$i = rand(0,$k);
	}
	$j = 0;
	while (!$rsLast->EOF and $i!=$j) {
		$j += 1; 
		$rsLast->MoveNext();
	}

	$asLast = $rsLast -> FetchRow();


	if ($asLast){
		list($hn_newsno,$hn_title,$hn_poster,$hn_news,$hn_postdate)=$asLast;
		//把本則新聞的小圖找出來
		userdata($hn_poster);
			//先把 檔名(含路徑) 處理出來
		clearstatcache();
		$hn_dir = $savepath.$hn_newsno."/";
		$hn_dir_url = $htmlsavepath.$hn_newsno."/";
		$direxist=file_exists($hn_dir);
		if (!$direxist){
			$hn_dir = "";
			$hn_dir_url = "";
			$imagename = "";
		}else{
			$handle=opendir($hn_dir);
			$j = 0;
			while ($file = readdir($handle)) {
				if (substr($file,0,3) == 'Si-') {
					$fname[$j] = $file;
					$j++;
				}
			}
			if ($j == 0) $fname = array();
			$lastnum = $j - 1;
			// 這?要 random 找出一支 S- 開頭的小圖
			$whichone = rand(0,$lastnum);
			$hn_image=$hn_dir_url.$fname[$whichone];
			//echo "<br>".$imagename;
		}
		//再把datetime 改成date
		$hn_postdate = substr($hn_postdate,0,4)."-".substr($hn_postdate,5,2)."-".substr($hn_postdate,8,2);
		
		echo "<table width='96%' align='center'>";
		echo "<tr> \n\r";
		echo "<td width='40%'>";
		echo "<img src='images/newsmig_title.gif'></td>";
		echo "<td width='40%'><a href='postnews.php' target='blank'>";
		echo "<img src='images/schnews_add.gif' border='0' width='100' height='30'></a>";
		echo "</td> \n\r";
		echo "<td width='20%'>";
		echo "</td> \n\r";
		echo "</tr>";
		$hn_news=substr($hn_news,0,128);
		$hn_news=substr_replace($hn_news,"．．．．",-8);
		echo "<tr bgcolor='#FFF7D1')> \n\r";
		echo "<td colspan='2'> \n\r";
			
		//echo "<a href='shownews.php?rdnum=$newsno'>";
		echo "	<a href='shownews.php?rdsno=$hn_newsno' target='blank'>".$hn_postdate."--".$hn_title."</a> \n\r";
		echo "--".$user_name."報導 ";
		echo "</td> \n\r";
		echo "<td bgcolor='#61FFFA' align='right'> \n\r";
		echo "	<a href='newslist.php' target='blank'>觀看所有新聞</a> \n\r";
		echo "</td> \n\r";
		echo "</tr> \n\r";
		echo "</table> \n\r";
		echo "<table bgcolor='#FFF7D1' width='96%' align='center'><tr>";
		echo "<td valign='top'><p style='line-height:120%; margin-left:0.2cm'>";
		echo nl2br($hn_news);
		echo "</p></td> \n\r";
		echo "<td valign='top' width='120' align='center'>";
		if ($hn_image != ""){
			echo "<img src='".$hn_image."' width='110' height='80'><br>\n\r";
		}else{
			echo "&nbsp;";
		}
		echo "</td></tr> \n\r";
		echo "</table>\n\r";
	}else{
		echo "&nbsp;";
	}
}
?>
</body>
</html>
