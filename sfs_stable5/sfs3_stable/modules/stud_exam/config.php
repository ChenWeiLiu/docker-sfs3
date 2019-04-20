<?php
// $Id: board_config.php 7779 2013-11-20 16:09:00Z smallduh $
/* 學務系統設定檔 */
require_once "../../include/config.php";

/* 學務系統函式庫 */

include "module-upgrade.php";

include_once "../../include/sfs_case_score.php";
include_once "./module-cfg.php";

//判斷登入者是否為學生
if ($_SESSION['session_who']!="學生") {
	echo "抱歉，本模組只限學生操作！";
	exit;
}

//經由線上補上管理模組 score_resit 載入函式庫
include_once "../score_resit/my_functions.php";

//國小或國中, 用於顯示最近就學年度
$CY_step=($IS_JHORES==6)?2:5;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();
$curr_year_seme=$curr_year.$curr_seme;


?>
