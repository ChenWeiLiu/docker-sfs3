<?php
/* 取得學務系統設定檔 */
include "../../include/config.php";

//成績處理相關函式庫
include "../../include/sfs_case_subjectscore.php";
include "../../include/sfs_case_studclass.php";

//取得相關函式庫
include "function.php";

//目錄內程式
$school_menu_p = array(
"index.php"=>"新建成績單版面",
"scorecard_col.php"=>"成績單所需欄位設定",
"scorecard_setup.php"=>"成績單版面設定"
);

?>
