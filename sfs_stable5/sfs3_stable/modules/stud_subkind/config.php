<?php
//$Id: config.php 8973 2016-09-12 08:14:48Z infodaes $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
include "module-upgrade.php";

//您可以自己加入引入檔
 

//目標身份t_id
$type_id=$_REQUEST[type_id]?$_REQUEST[type_id]:1;
//橫向選單標籤
$linkstr="type_id=$type_id";
 
?>