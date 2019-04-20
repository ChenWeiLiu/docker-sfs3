<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

if ($chk_menu_arr) {
	header("Location: chk_account.php");
} else {
	if ($IS_JHORES==0) header("Location: chart_e.php");
	else header("Location: chart_j.php");
}
?>
