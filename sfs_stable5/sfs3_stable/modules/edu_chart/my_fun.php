<?php
//$Id: my_fun.php 5310 2009-01-10 07:57:56Z hami $

function sock_put($fp,$method,$host,$url,$referer,$ASP_NET_SessionID)
{
	fputs($fp, "$method $url HTTP/1.1\r\nHost: $host\r\nUser-Agent: Mozilla/4.0 (compatibal; MSIE 6.0; windows NT 5.1)\r\nAccept: text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png\r\nAccpet-Language: zh-tw,en-us\r\nAccept-Encoding: gzip,deflate\r\nAccept-Charset: Big5,utf-8\r\nKeep-Alive: 300\r\nConnection: keep-alive\r\nReferer: http://".$referer."\r\nCookie: ASP.NET_SessionId=".$ASP_NET_SessionID."\r\n");
}

function sock_put_str($fp,$str)
{
	fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\nContent-length: ".strlen($str)."\r\n\r\n$str");
}
?>
