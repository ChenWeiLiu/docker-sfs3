<?php

// $Id: file.php 8829 2016-02-26 02:06:44Z hsiao $
include "config.php";
$_GET['FSN'] = intval($_GET['FSN']);
$str = "SELECT main_data, type, size, filename FROM file_db WHERE FSN={$_GET['FSN']}";
$recordSet = $CONN->Execute($str);
if ($recordSet) {
    $image = $recordSet->FetchRow();
    header("Content-Type: $image[type]");
    header('Content-disposition: attachment; filename=' . $image[filename]);
    header("Content-Length: $image[size]");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo stripslashes(Base64_Decode($image[main_data]));
}
?> 
