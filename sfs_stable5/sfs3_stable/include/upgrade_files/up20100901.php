<?php

//$Id: up20100901.php 9240 2018-05-04 03:25:04Z igogo $

if(!$CONN){
        echo "go away !!";
        exit;
}

$d_arr=array("..","templates_c");
write_scan();
find_php();
$d_arr=array();

function write_scan() {
	global $UPLOAD_PATH;

	$fp = fopen($UPLOAD_PATH."/found_php", "a");
	fwrite($fp,date("Y-m-d H:i:s")." --- 掃瞄 data 目錄中不應存在的 php 檔\n");
	fclose ($fp);
}

function find_php($dir) {
	$arr=read_dir($dir);
	if (count($arr)==0) return;
	if (!empty($dir)) $dir.="/";
	foreach($arr as $d) find_php($dir.$d);
}

function read_dir($path="") {
	global $UPLOAD_PATH, $d_arr;

	$temp_arr=array();
	$U=$UPLOAD_PATH;
	if (substr($U,-1,1)=="/") $U=substr($U,0,-1);
	$full_path=$U.(($path=="")?"":"/").$path;
	$handle=opendir($full_path);
	$i=0;
	while ($file = readdir($handle)) {
		if ($path!="" || !in_array($file,$d_arr)) {
			if ($file=="." || $file=="..") continue;
			if (is_dir($full_path."/".$file)) {
				$temp_arr[$i]=$file;
				$i++;
			} else {
				if (substr(strtoupper($file),-4,4)==".PHP") {
					$fp = fopen($U."/found_php", "a");
					fwrite($fp,date("Y-m-d H:i:s")." --- 刪除 ".$full_path."/".$file . "\n");
					fclose ($fp);
					unlink($full_path."/".$file);
				}
			}
		}
	}
	closedir($handle);
	return $temp_arr;
}
?>
