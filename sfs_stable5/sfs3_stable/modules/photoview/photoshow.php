<?php
// $Id: photoshow.php 5310 2009-01-10 07:57:56Z hami $

  require "config.php";
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","相片展管理");

$smarty->assign("now_date",date("Y-m-d"));

$smarty->assign("dir",$dirstr);
$smarty->assign("PHP_SELF",basename($PHP_SELF));
$smarty->assign("id",$_GET['id']);
$smarty->assign("web_url", "http://".$_SERVER["HTTP_HOST"]  . dirname ($_SERVER["PHP_SELF"]) );


$smarty->display("view_imagerotator.htm");

  
?>
