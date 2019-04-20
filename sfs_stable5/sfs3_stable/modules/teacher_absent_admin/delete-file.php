<?php
//$Id: list.php 7448 2013-08-29 14:16:21Z hami $
include "config.php";
include "../../include/sfs_class_absent.php";

//ป{รา
sfs_check();

if ($_GET[id]) {
    $id = (int) $_GET['id'];
    $query = "select * from teacher_absent where id=$id";
    $res = $CONN->Execute($query);
    $row = $res->FetchRow();

    $filePath = set_upload_path("/school/teacher_absent");
    unlink($filePath . $row['note_file']);
    $query = "UPDATE teacher_absent set note_file='' WHERE id=$id";
    $CONN->Execute($query);

}