<?php
require "../../include/config.php";
// 檢查 php 版本


if (!phpMinV('5.2.*')) {
	die('本程式僅支援 PHP 5.2 以上版本, 目前版本:'. PHP_VERSION);
}


$menu_p = array('list.php' => '列表', 'sign.php'=>'投稿', 'manager.php'=>'管理','parameter.php'=>'設定');
$fileExt = array('jpg','png','jpeg','gif');
$imageArr = array('0'=>'靠左','1'=>'靠右');


//管理變數
$query = "SELECT * FROM artical_paramter";
$res = $CONN->Execute($query);
if ($res->fields['paramter'])
$PARAMSTER = unserialize($res->fields['paramter']);
else {
	// 每頁筆數
	$PARAMSTER['items_per_page'] = 10;
	$PARAMSTER['title'] = $school_sshort_name.'兒童';
	$PARAMSTER['image_width'] = 450;
}
$res = array();
/* 上傳檔案目的目錄 */
$path_str = "school/artical/";
$uploadPath = set_upload_path($path_str);
$photo_path_str = "school/artical/photo/";
$photoUploadPath = set_upload_path($photo_path_str);

function phpMinV($v)
{
    $phpV = PHP_VERSION;

    if ($phpV[0] >= $v[0]) {
        if (empty($v[2]) || $v[2] == '*') {
            return true;
        } elseif ($phpV[2] >= $v[2]) {
            if (empty($v[4]) || $v[4] == '*' || $phpV[4] >= $v[4]) {
                return true;
            }
        }
    }

    return false;
}