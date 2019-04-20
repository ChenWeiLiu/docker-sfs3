<?php
// $Id: index.php 5310 2009-01-10 07:57:56Z hami $
//取得設定檔
include_once "config.php";
sfs_check();
head("失效模組");
$message="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='../../images/warn.png' align='middle' border=0>模組失效訊息</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'>本模組已不維護，請改用「成績繳交管理查詢」(score_manage_new)模組。<br></td></tr><tr><td align=center><br></td></tr></table>";
echo $message;
foot();
?>

