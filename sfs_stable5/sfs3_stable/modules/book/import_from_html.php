<?php
// $Id: import_from_html.php 5310 2009-01-10 07:57:56Z hami $

include "../../include/config.php";

$ISBN=$_GET[ISBN];

//從國家圖書館取得機讀資料網頁
$host="lib.ncl.edu.tw";
$url="http://lib.ncl.edu.tw/cgi-bin/isbnpost";

//第一次連結取得 $RID值
$RID=0;
$fp=fsockopen($host, 80, $errno, $errstr, 10);
if(!$fp) {
	echo "連線失敗，請檢查網路是否不通或等網路品質較佳時再試試！";
	exit;
} else {
	$str="OPT=BOOK.B&TYPE=S&PGNO=1&SEL.CL=&FNM=S&TOPICS1=BN&SEARCHSTR1=$ISBN&BOL1=AND&TOPICS2=TI&SEARCHSTR2=&BOL2=AND&TOPICS3=TI&SEARCHSTR3=&PAGELINE=10&Submit=%B6%7D%A9l%ACd%B8%DF";
	fputs($fp, "POST $url HTTP/1.1\r\nHost: $host\r\nUser-Agent: Mozilla/4.0 (compatibal; MSIE 6.0; windows NT 5.1)\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-length: ".strlen($str)."\r\n\r\n$str");
	fwrite($fp,$post);
	while(!feof($fp)){
		$d=array();
		$message .= fgets($fp,1024);
		$d=explode('RID=',$message);
		if (count($d)>1 && count($dd)<1) {
			$dd=array();
			$dd=explode('&TYPE',$d[1]);
			$RID=$dd[0];
			break;
		}
	}
}
fclose($fp);

//第二次連結 取得機讀資料網頁
if (!empty($RID)) {
	$fp=fsockopen($host, 80, $errno, $errstr, 10);
	$str="OPT=BOOK.F&VNM=&TOT=1&THIS=1&RID=$RID&NN=1&MARC=MARC+%AE%E6%A6%A1";
	fputs($fp, "POST $url HTTP/1.1\r\nHost: $host\r\nUser-Agent: Mozilla/4.0 (compatibal; MSIE 6.0; windows NT 5.1)\r\nReferer: http://lib.ncl.edu.tw/cgi-bin/isbnget?OPT=BOOK.F&VNM=&TOT=1&THIS=1&RID=$RID&TYPE=F\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-length: ".strlen($str)."\r\n\r\n$str");
	fwrite($fp,$post);
	while(!feof($fp)){
		$d=array();
		$message = fgets($fp,1024);
		if (strstr($message,"form")) break;
		$d=explode('<TR><TD>',$message);
		if (count($d)>1) {
			$dd=array();
			$dd=explode('</TD><TD>',$d[1]);
			if ($old_num==$dd[0]) {
				$i++;
			} else {
				$i=0;
			}
			$old_num=substr($dd[0],0,3);
			$data_arr[$old_num][$i][0]=$dd[1];
			$data_arr[$old_num][$i][1]=substr($dd[2],0,-6);
		}
	}
	$data_arr["010"]["0"]=explode_str($data_arr["010"]["0"]["1"],"abdz");
	$data_arr["100"]["0"]["a"]=substr($data_arr["100"]["0"]["1"],2,8);
	$data_arr["200"]["0"]=explode_str($data_arr["200"]["0"]["1"],"abcdefghipvzr");
	$data_arr["210"]["0"]=explode_str($data_arr["210"]["0"]["1"],"abcdefgh");
	$data_arr["681"]["0"]=explode_str($data_arr["681"]["0"]["1"],"abvy");
}
fclose($fp);

$smarty->assign("data_arr",$data_arr);
$smarty->display("book_import_from_html.tpl");

//拆解機讀碼
function explode_str($str="",$arr="") {
	$temp_arr=array();
	$pre_v=substr($arr,0,1);
	for($i=0;$i<strlen($arr);$i++) {
		$v=substr($arr,$i,1);
		$e_str="\$".$v;
		$d=array();
		$d=explode($e_str,$str);
		if ($d[1]!="") {
			$temp_arr[$pre_v]=$d[0];
			$str=$d[1];
			$pre_v=$v;
		}
		if ($i == strlen($arr)-1) $temp_arr[$pre_v]=$d[0];
	}
	return $temp_arr;
}
?>