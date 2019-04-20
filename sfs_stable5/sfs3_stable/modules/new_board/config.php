<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

/* 取得學務系統設定檔 */
include_once "../../include/config.php";
include_once "../../include/sfs_case_signpost.php";
include_once "../../include/sfs_case_file2db.php";
include_once "../../sfs_case_studclass.php";
include_once "./module-cfg.php";
$teacher_sn=$_SESSION['session_tea_sn'];
$POSTUPDIR=$UPLOAD_PATH."new_board";
?>
