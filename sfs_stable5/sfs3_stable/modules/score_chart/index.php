<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

include "../../include/config.php";
 
if ($IS_JHORES==0) header("Location: chart_e.php");
else header("Location: chart_j.php");
?>
