<?php
//取得設定檔
include_once "config.php";

sfs_check();
//秀出 SFS3 標題
head();
print_menu($school_menu_p);

?>
<br><p align="center">
<a href='./prove_race.php' target='prove'><img src='images/violet_new_icon.png' title='競賽紀錄清單'></a> ↖ 點此圖示開新視窗列示本年度參與免試學生競賽記錄
</p>