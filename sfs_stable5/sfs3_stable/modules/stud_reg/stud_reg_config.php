<?php

//$Id: stud_reg_config.php 8558 2015-10-13 01:45:06Z hsiao $
//系統設定檔
include_once "../../include/config.php";
//函式庫
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_studclass.php";

//升級檢查 
require "module-upgrade.php";

//新增按鈕名稱
$sameBtn = "同 戶籍地址";
//新增按鈕名稱
$newBtn = " 新增資料 ";
//修改按鈕名稱
$editBtn = " 確定修改 ";
//刪除按鈕名稱
$delBtn = " 確定刪除 ";
//確定新增按鈕名稱
$postBtn = " 確定新增 ";
$editModeBtn = " 修改模式 ";
$browseModeBtn = " 瀏覽模式 ";
//預設國籍
$default_country = "中華民國";
//左選單設定顯示筆數
$gridRow_num = 16;
//左選單底色設定
$gridBgcolor = "#DDDDDC";
//左選單男生顯示顏色
$gridBoy_color = "blue";
//左選單女生顯示顏色
$gridGirl_color = "#FF6633";
//預設第一個開啟年級 
$default_begin_class = 6;
//照片寬度
$img_width = 120;

//目錄內程式

$menu_p = array("stud_list.php" => "基本資料", "chi_edit.php" => "整班編修", "chi_photo.php" => "相片", "stud_photo.php" => "相片2", "stud_dom1.php" => "戶口資料", "stud_ext_data.php" => "補充資料", "stud_bs.php" => "兄弟姐妹", "stud_kinfolk.php" => "其他親屬", "../stud_move/" => "學生異動","basisdata_check.php" => "全校基本資料檢查", "stud_drop.php" => "學籍資料刪除", "../stud_query/check_error2.php" => "學籍資料檢查^", "show_ext_data.php" => "補充資料管理", "session_upload.php" => "本學年度在籍生資料上傳臺中市就學管控系統");

//設定上傳檔案路徑
$img_path = "photo/student";
$upload_str = set_upload_path("$img_path");

$modify_flag = true;
//檢查是否為管理權限人員
$sys_arr = get_sfs_module_set();
if ($sys_arr['edit_kind']) {
    if (!checkid($_SERVER['SCRIPT_FILENAME'], 1))
        $modify_flag = false;
}
//目錄內程式
if ($modify_flag) {
    $menu_p = array("stud_list.php" => "基本資料", "chi_edit.php" => "整班編修", "chi_photo.php" => "相片", "stud_photo.php" => "相片2", "stud_dom1.php" => "戶口資料", "stud_ext_data.php" => "補充資料", "stud_bs.php" => "兄弟姐妹", "stud_kinfolk.php" => "其他親屬", "session_upload.php" => "本學年度在籍生資料上傳臺中市就學管控系統", "../stud_move/" => "學生異動","basisdata_check.php" => "全校基本資料檢查", "stud_drop.php" => "學籍資料刪除", "../stud_query/check_error2.php" => "學籍資料檢查^", "show_ext_data.php" => "補充資料管理",);
} else {
    $menu_p = array("stud_list.php" => "基本資料", "chi_edit.php" => "整班編修", "stud_dom1.php" => "戶口資料", "stud_ext_data.php" => "補充資料", "stud_bs.php" => "兄弟姐妹", "stud_kinfolk.php" => "其他親屬", "session_upload.php" => "本學年度在籍生資料上傳臺中市就學管控系統","basisdata_check.php" => "全校基本資料檢查");
}
?>
 