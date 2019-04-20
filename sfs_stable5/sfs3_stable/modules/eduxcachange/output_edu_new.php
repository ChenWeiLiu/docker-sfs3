<?php
session_id($_POST['sid']);

$useragent = $_SERVER['HTTP_USER_AGENT'];
$cookie_sch_id=$_GET['cookie_sch_id'];
$serv_name=$_GET['serv_name'];
$UPLOAD_URL=$_GET['upload_url'];
$file_name=$_GET['file_name'];
$datafile =$serv_name.$UPLOAD_URL."eduxcachange/".$file_name;
if(function_exists('curl_ini')){
  echo base64_encode(curl_file_get_contents($datafile));
}else{
  echo base64_encode(file_get_contents($datafile));
}

function curl_file_get_contents($get_url){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $get_url);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
$rs = curl_exec($ch);
curl_close($ch);
return $rs;
}
?>
