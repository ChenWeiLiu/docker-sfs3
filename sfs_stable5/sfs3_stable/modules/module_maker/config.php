<?php
// $Id: config.php 5972 2010-07-07 03:58:49Z hami $

include_once "../../include/config.php";
require_once "./module-cfg.php";
//
head();
?>
<h1 style="color:red">模組停用</h1>
<h2>基於安全理由,本模組已停用,如有模組開發需要,請自行修改 sfs3/modules/module_maker/config.php 開啟本功能</h2>
<?php
foot();
exit;

//---------------------------------------------------
// 這裡請引入您自己的函式庫
//---------------------------------------------------
include_once "../../include/sfs_oo_zip.php";
include_once "../../include/sfs_case_sql.php";
include_once "function.php";

?>