<?php
//$Id$
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
//您可以自己加入引入檔

##################回上頁函式1#####################
function backe($value= "BACK"){
	echo  "<head><meta http-equiv='Content-Type' content='text/html; charset=big5'></head><br><br><br><br><CENTER><form><input type=button value='".$value."' onclick=\"history.back()\" style='font-size:16pt;color:red;'></form><BR></CENTER>";
	exit;
}

include "my_fun.php";
