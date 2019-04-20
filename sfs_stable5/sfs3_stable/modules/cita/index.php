<?php
// $Id: index.php 6507 2011-09-05 13:36:15Z infodaes $
include 'config.php';
if($_SESSION[session_tea_sn]) header("Location: citaList.php"); else header("Location: list.php");
?>


