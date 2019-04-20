<?php




session_id($_POST['sid']);
//session_start();

require "config.php";
require "class.php";

sfs_check();

$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$datafile = $SFS_PATH_HTML.'data/temp/studmove.zip';
//這裡要處理寫入XML檔的程序
$data = base64_decode($_POST['apidata']);
//..................
//寫入完成後,回傳剛才所接收到的內容給APPLET,以確定有接收到檔案
echo $_POST['apidata'];


?>
