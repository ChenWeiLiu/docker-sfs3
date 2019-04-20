<?php

// $Id: config.php 8418 2015-05-12 02:10:21Z smallduh $
include "../../include/config.php";
include "../../include/sfs_case_dataarray.php";
include "module-cfg.php";
include "module-upgrade.php";
include "my_fun.php";

//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
//設定上傳檔案路徑
$img_path = "score_experiment";
$upload_str = set_upload_path("$img_path");

?>
