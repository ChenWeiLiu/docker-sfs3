<?php
header('Content-type: text/html; charset=utf-8');

include_once ('config.php');
include_once ('my_functions.php');

$file_name=$_GET['set']; // 檔名


if (!isset($_SESSION['MSN_LOGIN_ID'])) {
  echo "<Script language=\"JavaScript\">window.close();</Script>";
	exit();
}

mysql_query("SET NAMES 'utf8'");

$query="select filename_r,file_download from sc_msn_file where filename='".$file_name."'";
$result=mysql_query($query);

if (mysql_num_rows($result)) {
list($filename_r,$file_download)=mysql_fetch_row($result);
//$filename_r=addslashes($filename_r);
$file_download+=1;
$query="update sc_msn_file set file_download='".$file_download."' where filename='".$file_name."'";
mysql_query($query);
}else{
 exit();
}

ini_set(‘memory_limit’, ’500M’);


$file_path = $download_path;

$file_size = filesize($file_path.$file_name);

//$filename_r=iconv("UTF-8","Big5",$filename_r);
$filename_r=urlencode($filename_r); 

//header('Pragma: public');
//header('Expires: 0');
//header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//header('Cache-Control: private', false);

header("Content-Type: application/octet-stream; charset=utf-8");


//header("Content-Length: $file_size");
header("Content-Disposition: attachment; filename=$filename_r");
//header('Content-Transfer-Encoding: binary');

readfile($file_path.$file_name);
//echo "$filename_r";
exit();

?>