<?php

	// $Id: teach_bir.php 5310 2009-01-10 07:57:56Z hami $
	//原版作者：chi 2004/03/08
	//載入設定檔
	include "config.php";
  include "../../include/sfs_case_PLlib.php";

	// --認證 session
	sfs_check();
	//執行動作判斷
	//秀出網頁
	head("教職員本月壽星");
	$tool_bar=&make_menu($school_menu_p);
	echo $tool_bar;
	($_GET[mon]=='' )?$m=date("n"):$m=$_GET[mon];
	if ($m>12) $m-=12 ;
	if ($m<=0) $m= 12 ;
	echo "[<A HREF='$PHP_SELF?mon=".($m-1)."'>上個月</A>|";
	echo "<A HREF='$PHP_SELF?mon=".date("n")."'>本月</A>|";
	echo "<A HREF='$PHP_SELF?mon=".($m+1)."'>下個月</A>]<BR><BR>";
  echo "$m 月壽星<br>" ; 
	$SQL="select name , birthday from teacher_base where MONTH(birthday)=$m  and teach_condition=0 order by DAYOFMONTH(birthday) "; 
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	for($i=0; $i<$rs->RecordCount(); $i++) {
		echo $arr[$i][name]." (".Getday($arr[$i][birthday])."日)<BR>";
	}

	foot();
?>
