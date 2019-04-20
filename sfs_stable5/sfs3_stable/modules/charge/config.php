<?php
//$Id: config.php 6064 2010-08-31 12:26:33Z infodaes $
//預設的引入檔，不可移除。
include_once "../../include/config.php";
require_once "./module-cfg.php";
require_once "./module-upgrade.php";

//您可以自己加入引入檔

$detail_types=array(""=>"學校","1"=>"合作社","2"=>"代辦");

//取得模組參數的類別設定
$m_arr = &get_module_setup("charge");
extract($m_arr,EXTR_OVERWRITE);

//預設值
if(! $m_arr['detail_types']) $m_arr['detail_types']='學校公庫,合作社';
if(! $m_arr['detail_lists']) $m_arr['detail_lists']='10,10';

$detail_types=explode(',',$m_arr['detail_types']);
$detail_lists=explode(',',$m_arr['detail_lists']);

?>
