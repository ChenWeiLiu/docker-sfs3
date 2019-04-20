<?php
include "lib.php";

$sfs3config=new SFS3Server();
$configFile="../../../../include/config.php";
$SFS_PATH_HTML=$sfs3config->getConfig($configFile,'$SFS_PATH_HTML');

$downloadUrl=$SFS_PATH_HTML."data/school/rwd_pages/board.client.zip";
$UPLOAD_PATH=$sfs3config->getConfig($configFile,'$UPLOAD_PATH');
$output_dir=$UPLOAD_PATH."school/rwd_pages/";

$template_file=$output_dir."template.board.client.zip";
$dst=$output_dir."board.client.zip";

  if(file_exists($dst)){
		unlink($dst);
	}

 if (!copy($template_file,$dst)) {
    $ERROR=1;
    $msg=sprintf("%s zip複製失敗",$output_dir);
  }


$zip=new ZipArchive;
$config_file= $output_dir."config.php";
if ($zip->open($dst) === TRUE) {
    $zip->addFile($config_file, 'config.php');
    $zip->close();
} else {
  $ERROR=1;
	$downloadUrl="";
}

print $downloadUrl;
/*
$downloadUrl = "http://163.17.210.234/sfs3/data/school/mobile/board.client.zip";
*/
