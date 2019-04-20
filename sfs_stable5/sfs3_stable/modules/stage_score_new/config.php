<?php

include_once "../../include/config.php";
require_once "./module-cfg.php";

//取得模組參數的類別設定
$m_arr = get_sfs_module_set();
extract($m_arr,EXTR_OVERWRITE);

$default_subject=",$default_subject,";

?>
