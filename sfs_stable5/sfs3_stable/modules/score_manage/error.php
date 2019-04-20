<?php
// $Id: error.php 5310 2009-01-10 07:57:56Z hami $
/*引入學務系統設定檔*/
include "../../include/config.php";
//引入函數
include "./my_fun.php";
//使用者認證
sfs_check();

//程式檔頭
head("成績管理");

//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc><tr><td>";
echo "您未被授權管理這個模組，如有疑問請洽系統管理人員";
//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();




















?>
