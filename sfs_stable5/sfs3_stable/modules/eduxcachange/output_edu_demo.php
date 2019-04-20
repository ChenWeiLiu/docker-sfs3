<?php

session_id($_POST['sid']);
//session_start();

require "config.php";
require "class.php";

//sfs_check();
//いみ際や刊
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}
//$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];

$datafile = $SFS_PATH_HTML.'modules/eduxcachange/demo.zip';

//upload test file
//$datafile = $SFS_PATH_HTML . 'data/eduxcachange/test.zip';

//$xmls = iconv("Big5","UTF-8",$data);
echo base64_encode(curl_file_get_contents($datafile));
//echo iconv("Big5","UTF-8",$data);
//echo $aeskey;

function curl_file_get_contents($get_url){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $get_url);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
$rs = curl_exec($ch);
curl_close($ch);
return $rs;
}
?>
