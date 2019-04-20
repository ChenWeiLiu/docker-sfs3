<?php

include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";
//目錄內程式
$teach_menu_p = array("../teacher_self/teach_list.php" => "基本資料", "../teacher_self/teach_connect.php" => "網路資料", "teach_cpass.php" => "更改密碼");
//取得模組參數的類別設定
$chp_arr = get_module_setup('chpass');
extract($chp_arr, EXTR_OVERWRITE);

?>