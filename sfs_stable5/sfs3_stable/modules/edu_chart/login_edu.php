<?php
//$Id: login_edu.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//ป{รา
sfs_check();

$smarty->assign("school_name",$SCHOOL_BASE[sch_cname_s]);
$smarty->display("edu_chart_login_edu.tpl");
?>
