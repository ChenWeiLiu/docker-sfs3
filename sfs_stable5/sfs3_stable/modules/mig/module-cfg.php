<?php
                                                                                                                             
// $Id: module-cfg.php 5310 2009-01-10 07:57:56Z hami $

// 資料表名稱定義
$MODULE_NAME ="mig";

$MODULE_TABLE_NAME[0] = "";

$MODULE_PRO_KIND_NAME = "數位相本";

// 模組版本
$MODULE_UPDATE_VER="2.0.1";

// 模組最後更新日期
$MODULE_UPDATE="2003-03-19 08:30:00";



$SFS_MODULE_SETUP[] =
	array('var'=>"P_TITLE", 'msg'=>"程式標題", 'value'=>"數位相本");

$SFS_MODULE_SETUP[] =
	array('var'=>"is_standalone", 'msg'=>"否有獨立的界面(1是,0否)", 'value'=>0);

$SFS_MODULE_SETUP[] =
	array('var'=>"convert_path", 'msg'=>"壓縮程式路徑(利用 whereis convert 指令查詢)", 'value'=>"/usr/bin/X11/");

$SFS_MODULE_SETUP[] =
	array('var'=>"indexImgWidth", 'msg'=>"引圖寬度(pix)", 'value'=>96);

$SFS_MODULE_SETUP[] =
	array('var'=>"ImgWidth", 'msg'=>"索引圖寬度(pix)", 'value'=>500);

$SFS_MODULE_SETUP[] =
	array('var'=>"maxColumns", 'msg'=>"顯示欄數", 'value'=>4);

?>
