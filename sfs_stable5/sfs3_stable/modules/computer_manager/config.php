<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//系統設定檔
	include_once "./module-cfg.php";
	include_once "../../include/config.php";
	
	require_once "./module-upgrade.php";


//取得模組變數
$m_arr = &get_module_setup("computer_manager");
extract($m_arr,EXTR_OVERWRITE);
?>

