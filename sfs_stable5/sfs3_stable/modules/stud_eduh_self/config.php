<?php

// $Id: config.php 8645 2015-12-17 02:22:47Z qfon $

//系統設定檔
include_once "../../include/config.php";

//函式庫
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_studclass.php";
require_once "./module-cfg.php";
require_once "./module-upgrade.php";


//取得模組設定
$m_arr = get_sfs_module_set("stud_eduh_self");
extract($m_arr, EXTR_OVERWRITE);


//判斷登入者是否為學生
if ($_SESSION['session_who']!="學生") {
	echo "抱歉，本模組只限學生操作！";
	exit;
}


if($base_edit) $menu_p["stud_list_self.php"]="基本資料";
if($dom_edit) $menu_p["stud_dom1_self.php"]="戶口資料";
if($dom_edit) $menu_p["stud_bs_self.php"]="兄弟姊妹";
if($stud_eduh_editable) $menu_p["stud_eduh_self.php"]="輔導資料";
if($club_enable) $menu_p["stud_club.php"]="社團活動";
if($service_enable) $menu_p["service_feedback.php"]="服務學習";
if($career_contact) $menu_p["career_contact.php"]="聯絡電話";
if($mystory) $menu_p["mystory.php"]="我的成長故事";
if($psy_test) $menu_p["psy_test.php"]="各項心理測驗";
if($study_spe) $menu_p["study_spe.php"]="學習成果及特殊表現";
if($career_view) $menu_p["career_view.php"]="生涯統整面面觀";
if($career_evaluate) $menu_p["career_evaluate.php"]="生涯發展規劃書";
if($career_guidance) $menu_p["career_guidance.php"]="生涯輔導諮詢建議";
if($stage_score) $menu_p["stage_score.php"]="領域階段成績";
if($stud_view_self_absent) $menu_p["report1.php"]="缺曠課查詢";
$menu_p["stud_cpass.php"]="更改登入密碼";

$curr_month=','.date('m').',';


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
$gridBgcolor="#DDDDDC";
//左選單男生顯示顏色
$gridBoy_color = "blue";
//左選單女生顯示顏色
$gridGirl_color = "#FF6633";

//設定上傳檔案路徑
$img_path = "photo/student";
$upload_str = set_upload_path("$img_path");

//過濾POST值
foreach($_POST as $k=>$v) {
	if (!is_array($v)) {
		//為了要解決單引號取代後處生的問題
		$v=str_replace("'", "@$@", $v);
		//過濾--
		$_POST[$k]=str_replace(array("\\@$@","@$@","--"),array("","",""),$v);
	}
}

function ha_check(){
		if (!$_SESSION['stud_hacard_serial']){
			header("Location: readha.php");
		}else{
			return true;
		}
}



?>
