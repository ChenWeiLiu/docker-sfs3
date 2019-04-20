<?php
//$Id: config.php 6260 2010-11-18 08:30:29Z infodaes $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
//您可以自己加入引入檔
//取得模組參數的類別設定
$m_arr = &get_module_setup("fix_tool");
extract($m_arr,EXTR_OVERWRITE);

?>
