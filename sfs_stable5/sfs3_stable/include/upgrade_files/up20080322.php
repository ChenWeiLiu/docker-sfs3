<?php

//$Id: up20080322.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

$query = "select * from stud_addr_zip where 1=0";
if ($CONN->Execute($query)) {
	$CONN->Execute("update stud_addr_zip set town='北屯區' where country='台中市' and town='北屯'");
	$CONN->Execute("update stud_addr_zip set town='西屯區' where country='台中市' and town='西屯'");
	$CONN->Execute("update stud_addr_zip set town='南屯區' where country='台中市' and town='南屯'");
}
?>
