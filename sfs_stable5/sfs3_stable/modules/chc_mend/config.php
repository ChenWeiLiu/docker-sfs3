<?php
//$Id$
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
include_once "chi_fun.php";
//您可以自己加入引入檔
function gVar($N){
	if (isset($_POST[$N])) return strip_tags(trim($_POST[$N]));
	if (isset($_GET[$N])) return strip_tags(trim($_GET[$N]));	
}
	


