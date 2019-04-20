<?php
header('Content-type: text/html;charset=big5');
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "../../include/config.php";

sfs_check();

    //如果已選擇了文章
    if ($_GET['type_id']!='') {

        $sql="select * from contest_typebank where id='{$_GET['type_id']}'";
        $res=$CONN->Execute($sql);
        list($id,$kind,$article,$content)=$CONN->Execute($sql)->fetchrow();

        echo $content;

    }

?>