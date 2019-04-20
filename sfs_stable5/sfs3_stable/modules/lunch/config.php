<?php
require_once "./module-cfg.php";

// 引入 SFS 設定檔，它會幫您載入 SFS 的核心函式庫
include_once "../../include/config.php";
include_once "./module-upgrade.php";

// 引入林朝敏師的函式庫 (這支午餐食譜需要用到)
include_once "../../include/sfs_case_PLlib.php" ;




// 每日的中文名稱
$WEEK_DATE = array('一','二','三','四','五','六','日');

// 輸入欄寬
$INPUT_SIZE = 22;

// 文字編輯區行列的大小
$TEXTAREA_COLS_SIZE = 20;

$TEXTAREA_ROWS_SIZE = 5;

//取得模組參數設定
$m_arr = &get_sfs_module_set("lunch");
extract($m_arr, EXTR_OVERWRITE);

$DESIGN = explode(",",$DESIGN_NAME);
$font_size=$font_size?$font_size:'9pt';
$column_bgcolor_w='#FFDDDD';
$column_bgcolor_m='#DDDDFF';

// 食譜列出天數
$WEEK_DAYS = $WEEK_DAYS?$WEEK_DAYS:5;

?>