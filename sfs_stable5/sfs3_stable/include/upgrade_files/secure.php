<?php
//$Id: secure.php 5984 2010-08-16 05:39:13Z brucelyc $
if(!$CONN){
        echo "go away !!";
        exit;
}
global $UPLOAD_PATH;

if (file_exists($UPLOAD_PATH."temp")) k($UPLOAD_PATH."temp");

function k($temp_dir) {
	$fp = opendir($temp_dir);
	while(gettype($file=readdir($fp)) != boolean){
		if ($file=="." || $file=="..") continue;
		$f=$temp_dir."/".$file;
		if (is_dir($f)) {
			k($f);
		} else {
			unlink($f);
		}
	}
	closedir($fp);
}
?>
