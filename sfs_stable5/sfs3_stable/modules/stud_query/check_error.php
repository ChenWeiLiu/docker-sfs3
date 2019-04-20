<?php
                                                                                                                             
// $Id: check_error.php 5599 2009-08-21 05:30:50Z infodaes $


//載入設定檔
include "stud_query_config.php";

//認證檢查
sfs_check();
head('學籍成績檢查');
print_menu($menu_p);
echo "<font size=4 color='red'><BR>學生的學籍與成績檢查，已經獨立至stud_check模組，<BR><BR>若有需要，請系統管理員安裝該模組！</font>";
foot();
?>
