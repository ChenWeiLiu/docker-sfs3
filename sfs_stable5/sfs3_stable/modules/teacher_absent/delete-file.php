<?php
//$Id: delete-file.php 8104 2014-09-01 05:56:02Z hami $
include "config.php";
include "../../include/sfs_class_absent.php";

//認證
sfs_check();

if ($_GET[id]) {
    $id = (int) $_GET['id'];
    $query = "select * from teacher_absent where id=$id";
    $res = $CONN->Execute($query);
    $row = $res->FetchRow();
    //非本人
    if ($_SESSION[session_tea_sn] <> $row[teacher_sn]) {
        exit();
    }
    $filePath = set_upload_path("/school/teacher_absent");
    unlink($filePath . $row['note_file']);
    $query = "UPDATE teacher_absent set note_file='' WHERE id=$id";
    $CONN->Execute($query);

}