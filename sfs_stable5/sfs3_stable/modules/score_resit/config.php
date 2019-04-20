<?php
// $Id: board_config.php 7779 2013-11-20 16:09:00Z smallduh $
/* 學務系統設定檔 */
require_once "../../include/config.php";

/* 學務系統函式庫 */

include "module-upgrade.php";

include_once "../../include/sfs_case_score.php";
include_once "./module-cfg.php";

include_once "my_functions.php";

//國小或國中, 用於顯示最近就學年度
$CY_step=($IS_JHORES==6)?2:5;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();
$curr_year_seme=$curr_year.$curr_seme;


?>
