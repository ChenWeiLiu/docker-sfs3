<?php
// $Id: stud_psy_tran.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

$spy_arr = array(1=>'心理測驗轉表',2=>'輔導記錄轉表');
if (isset($_POST['sel']))
$sel = $_POST['sel'];
else
$sel = $_GET['sel'];

if ($sel=='') $sel=1;

// 次選單
$submenu =  "<table  style='font-size:12px;padding:3px; margin:auto;width:300px; background-color:#dde'><tr align='center'>";
foreach ($spy_arr as $id=>$val) {
	if ($id == $sel)
	$submenu .= "<td  bgcolor='#ffff00'>";
	else
	$submenu .= "<td >";
	$submenu .= "<a href='{$_SERVER['PHP_SELF']}?sel=$id'>$val</a></td>";
}
 $submenu .= "</tr></table>";


if ($sel  ==2)
	require "stud_psy_tran2.php";
else
	require "stud_psy_tran1.php";

	

?>