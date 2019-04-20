<?php
// $Id: ticker.php 5310 2009-01-10 07:57:56Z hami $

include "../../include/config.php";

$smarty->assign(bc,($_REQUEST["bc"]=="")?"white":$_REQUEST["bc"]);
$smarty->assign(fw,($_REQUEST["fw"]=="")?"normal":$_REQUEST["fw"]);
$smarty->assign(fs,($_REQUEST["fs"]=="")?"10":$_REQUEST["fs"]);
$smarty->assign(fc,($_REQUEST["fc"]=="")?"#3D753C":$_REQUEST["fc"]);
$smarty->assign(lh,($_REQUEST["lh"]=="")?"10":$_REQUEST["lh"]);
$smarty->assign(td,($_REQUEST["td"]=="")?"none":$_REQUEST["td"]);
$smarty->assign(hc,($_REQUEST["hc"]=="")?"#CC3300":$_REQUEST["hc"]);

$today=date("Y-m-d");
$query="select * from board_p where (b_open_date + INTERVAL b_days DAY) >= '$today' order by b_id desc";
$res=$CONN->Execute($query);
$smarty->assign(data_arr,$res->GetRows());
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("school_short_name",$school_short_name);
$smarty->display("board_ticker.tpl");
?>