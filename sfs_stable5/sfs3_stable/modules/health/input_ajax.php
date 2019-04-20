<?php

require "config.php";
sfs_check();

$smarty->assign("sight_kind",hSightManage() );
$studentSn = (int)$_GET['studentSn'];
$yearSeme = strip_tags($_GET['yearSeme']);
$year = (int)substr($yearSeme, 0 ,-1);
$semester = (int)substr($yearSeme, -1);

$query = "SELECT * FROM health_sight WHERE student_sn='$studentSn'
AND year=$year AND semester=$semester";
$res = $CONN->Execute($query) or die($query);
$arr = array();
foreach($res as $row)
$arr[$row['side']] = $row;

$smarty->assign('data', $arr);
$smarty->assign('sutdent_sn',$studentSn);

$tpl = $dirPath.'/templates/ajax/signt_form.tpl';
header("Content-Type: text/html; charset=BIG5");

$smarty->display($tpl);