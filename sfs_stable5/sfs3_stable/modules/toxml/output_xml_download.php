<?php
// $Id: output_xml.php 8928 2016-07-20 18:11:45Z smallduh $

require "config.php";

$filename=$_GET['set'];

ini_set('memory_limit', '100M');
sfs_check();
$tmp_path = $ini_val ? $ini_val : sys_get_temp_dir();


$filename_r=$SCHOOL_BASE['sch_id']."_全校學生學籍XML_".date("Ymd").".xml";

header("Content-Type: application/octet-stream");

header("Content-Disposition: attachment; filename=$filename_r");

readfile($tmp_path.$filename);

unlink($tmp_path.$filename);
?>