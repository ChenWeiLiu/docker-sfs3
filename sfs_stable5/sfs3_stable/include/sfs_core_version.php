<?php

// $Id: sfs_core_version.php 5310 2009-01-10 07:57:56Z hami $

// 版本相關

	$SFS_VERSION = "3.0";
	$SFS_DATE = "2002-10-1";

// SFS patch 狀態
//	$SFS_PATCH_LEVEL 定義在 sfs-release.php 中

	if (file_exists("$SFS_PATH/sfs-release.php")) 
			 include_once "$SFS_PATH/sfs-release.php";

// 暫時放 TEMPLATE 目錄

	$SFS_TEMPLATE = $SFS_PATH . "/templates/new";
?>
