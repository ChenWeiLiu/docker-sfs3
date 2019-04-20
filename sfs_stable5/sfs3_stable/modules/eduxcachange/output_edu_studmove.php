<?php




session_id($_POST['sid']);
session_start();

require "config.php";
require "class.php";

sfs_check();

$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$datafile = $SFS_PATH_HTML.'modules/toxml/studmove_ori.zip';
echo base64_encode(file_get_contents($datafile));


?>
