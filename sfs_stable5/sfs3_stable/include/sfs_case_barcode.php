<?php
// $Id: sfs_case_barcode.php 5310 2009-01-10 07:57:56Z hami $

//±ø½X¨ç¦¡
function barcode($text) {
	global $SFS_PATH_HTML;
	$enc_text=urlencode($text); 	
	echo "<img src=\"".$SFS_PATH_HTML."include/sfs_barcode.php?code=$enc_text\" border=0 Alt=\"$text\">"; 
} 
?>