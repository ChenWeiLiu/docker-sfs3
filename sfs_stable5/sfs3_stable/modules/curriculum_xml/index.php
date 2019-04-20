<?php
// $Id: index.php  $
//header("Location: class_kind_setup.php");

require "config.php";

sfs_check();
head('停止維護公告');
print_menu($menu_p);

echo "<h1>由於教育部已另訂新的EXCEL格式，本模組已停止維護。<br>請改用 <a href='../curriculum_k12ea'><img src='../curriculum_k12ea/images/new_icon.png' height='36px'>國教署人力資源網課表匯出</a> 模組</h1>";

foot();

?>
