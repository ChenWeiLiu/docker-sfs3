<?php
//$Id: optimize.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

$res=$CONN->Execute("SHOW TABLE STATUS");
$opt_tbl=array();
$i=0;
while ($row=$res->FetchRow()) {
	if ($row['Data_free']>0) {
		$opt_tbl[$i]['name']=$row['Name'];
		$opt_tbl[$i]['data_free']=$row['Data_free'];
		$i++;
	}
}

if ($_POST['optimize']) {
	if (count($opt_tbl)>0)  { 
		while(list($i,$tbl)=each($opt_tbl)) {
			if ($CONN->Execute("repair table `".$tbl['name']."`")) $opt_tbl[$i]['repair']=1;
			if ($CONN->Execute("optimize table `".$tbl['name']."`")) $opt_tbl[$i]['optimize']=1;
		}
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","資料庫最佳化");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("rowdata",$opt_tbl);
$smarty->display("system_optimize.tpl");
?>
