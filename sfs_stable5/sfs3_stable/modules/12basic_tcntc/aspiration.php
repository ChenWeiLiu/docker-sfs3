<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("志願序");
print_menu($menu_p);

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

echo "<center><font size=5 color='blue'><BR><BR><BR>志願填報與其積分計算不於SFS3處理！</font></center>";
foot();
?>