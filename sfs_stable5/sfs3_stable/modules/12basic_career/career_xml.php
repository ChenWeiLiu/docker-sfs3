<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("生涯輔導資料XML交換");

//模組選單
print_menu($menu_p,$linkstr);

if(checkid($_SERVER['SCRIPT_FILENAME'],1)) {
 echo "<center><font size=5 color='#ff0000'><br><br>本功能開發中，敬請能耐心等候！<br><br></font></center>";
} else echo "<center><font size=5 color='#ff0000'><br><br>您不具有模組管理權，系統禁止您使用！<br><br></font></center>";

foot();

?>
