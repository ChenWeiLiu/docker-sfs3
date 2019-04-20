<?php

//$Id:$

if (!$CONN) {
    echo "go away !!";
    exit;
}
global $UPLOAD_PATH;
//刪除新生匯入資料暫存檔
$tempFile = $UPLOAD_PATH . 'temp/newstud/newstud.csv';

if (is_file($tempFile))
    unlink($tempFile);

?>