<?php
/*
* $Id: list.php 7752 2013-11-08 10:31:02Z hami $
* 家览ㄏノ
*/

// 更╰参砞﹚郎
require_once "config.php";

// ōだ粄靡
sfs_check();

// 更ン祘Α
require_once "simu_class.php";

// ミン
$obj = new simu_class();

// 磅︽祘矪瞶
$obj->process();


?>
