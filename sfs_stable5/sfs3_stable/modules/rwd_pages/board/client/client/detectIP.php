<?php
//source from http://note.tc.edu.tw/196.html   

$schcidr="163.17.210.129/25";

$sfs3Board = "http://163.17.39.135/modules/board/";
$agent = "http://web.dayes.tc.edu.tw/jsonBoard/#/page";


$ip=$_SERVER["REMOTE_ADDR"];

if(matchCIDR($ip,$schcidr)){
	header("Location: $sfs3Board");
}else{
	header("Location: $agent");
}
	
function matchCIDR($addr, $cidr) {
     list($ip, $mask) = explode('/', $cidr);
     return (ip2long($addr) >> (32 - $mask) == ip2long($ip) >> (32 - $mask));
}
