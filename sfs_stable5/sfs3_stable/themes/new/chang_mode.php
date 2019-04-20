<?php
$cmkey=$_REQUEST["cmk"];
$value=$_REQUEST["v"];

$addtime=60*60*24*365;

switch ($cmkey) {
    case "close_left_menu":
		setcookie("close_left_menu");
        setcookie("close_left_menu", $value,time()+$addtime,"/");
	break;
	case "close_fast_link":
		setcookie("close_fast_link");
        setcookie("close_fast_link", $value,time()+$addtime,"/");
	break;
	
}

header("location: $_SERVER[HTTP_REFERER]");
?>