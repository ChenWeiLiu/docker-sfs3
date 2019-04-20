<?php

// $Id: module-cfg.php 8860 2016-03-30 01:03:20Z brucelyc $

//---------------------------------------------------
//
// 1.這裡定義：模組資料表名稱 (供 "模組權限設定" 程式使用)
//   這區的 "變數名稱" 請勿改變!!!
//-----------------------------------------------
//
// 若有一個以上，請接續此  陣列來定義
//
// 也可以用以下這種設法：
//
// $MODULE_TABLE_NAME=array(0=>"lunchtb", 1=>"xxxx");
//
// $MODULE_TABLE_NAME[0] = "lunchtb";
// $MODULE_TABLE_NAME[1]="xxxx";
//
// 請注意要和 module.sql 中的 table 名稱一致!!!
//---------------------------------------------------

// 資料表名稱定義

$MODULE_TABLE_NAME[0] = "";

//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "級務管理";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.1";

// 模組最後更新日期
$MODULE_UPDATE="2003-4-8 13:45:00";

//重要模組，免被勿刪
$SYS_MODULE=1;
//---------------------------------------------------
//
// 4. 這裡請定義：您這支程式需要用到的：變數或常數
//---------------------------------^^^^^^^^^^
//
// (不想被 "模組參數管理" 控管者，請置放於此)
//
// 建議：請儘量用英文大寫來定義，最好要能由字面看出其代表的意義。
//
// 這區的 "變數名稱" 可以自由改變!!!
//
//---------------------------------------------------

//星期
$week_array=array("日", "一", "二", "三", "四", "五", "六");
$monthNames = array("1"=>"一月", "二月", "三月", "四月", "五月", "六月","七月", "八月", "九月", "十月", "十一月", "十二月");

$button["Excel"]="MS Office Excel 檔";
$button["Word"]="MS Office Word 檔";
$button["sxw"]="OpenOffice.org Writer 檔";
while(list($k,$v)=each($button)){
                $import_option.="<option value='$k'>$v</option>\n";
}
$import_option = "<select name='print_key' size='1'>$import_option</select>";

$today=date("Y-m-d");
//列出橫向的連結選單模組

$menu_p = array(
"name_form.php"=>"班級名條",
"address_book_th.php"=>"教師手冊名單",
"address_book.php"=>"班級通訊錄",
"address_book2.php"=>"通訊錄2",
"stud_birth.php"=>"月份統計",
"stud_star_list.php"=>"星座統計",
"stud_kind2.php"=>"特殊身份別",
"select_behalf2.php"=>"班代表圈選表",
"parent_manage.php"=>"家長帳號管理",
"link_parent.php"=>"聯絡簿管理",
"absent_list.php"=>"勤惰");
if($is_rewrad) $menu_p["reward_list.php"]="獎懲";
$menu_p["leader_list.php"]="幹部";
$menu_p["service_class_list.php"]="服務學習";
$menu_p["club_select.php"]="學生社團選填";
if ($is_absent=='y') $menu_p["absent_class.php"]="缺曠課紀錄";
if ($course_input) $menu_p["course_setup3.php"]="設定功課表";
$menu_p["score_query.php"]="階段成績列表";
if ($is_sms) {
	$menu_p["sms_guardian.php"]="發送簡訊";
	$menu_p["sms_record.php"]="簡訊發送記錄";
}
if($is_pwd) $menu_p["stud_pwd.php"]="學生登入密碼";

//---------------------------------------------------
//
// 5. 這裡定義：預設值要由 "模組參數管理" 程式來控管者，
//    若不想，可不必設定。
//
// 格式： var 代表變數名稱
//       msg 代表顯示訊息
//       value 代表變數設定值
//
// 若您決定將這些變數交由 "模組參數管理" 來控管，那麼您的模組程式
// 就要對這些變數有感知，也就是說：若這些變數值在模組參數管理中改變，
// 您的模組就要針對這些變數有不同的動作反映。
//
// 例如：某留言板模組，提供每頁顯示筆數的控制，如下：
// $SFS_MODULE_SETUP[1] =
// array('var'=>"PAGENUM", 'msg'=>"每頁顯示筆數", 'value'=>10);
//
// 上述的意思是說：您定義了一個變數 PAGENUM，這個變數的預設值為 10
// PAGENUM 的中文名稱為 "每頁顯示筆數"，這個變數在安裝模組時會寫入
// pro_module 這個 table 中
//
// 我們有提供一個函式 get_module_setup
// 供您取用目前這個變數的最新狀況值，
//
// 詳情請參考 include/sfs_core_module.php 中的說明。
//
// 這區的 "變數名稱 $SFS_MODULE_SETUP" 請勿改變!!!
//---------------------------------------------------

// =
//        array('var'=>"IS_STANDALONE", 'msg'=>"是否有獨立的界面(1是,0否)", 'value'=>0);


// 第2,3,4....個，依此類推：

$SFS_MODULE_SETUP[0] = array('var'=>"is_absent", 'msg'=>"是否開放讓級任老師登錄學生出缺席記錄", 'value'=>array("n"=>"否","y"=>"是"));
$SFS_MODULE_SETUP[1] = array('var'=>"course_input", 'msg'=>"是否可以修改課表", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[2] = array('var'=>"influenza", 'msg'=>"是否啟用流感登錄", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[3] = array('var'=>"is_sms", 'msg'=>"是否啟用簡訊功能", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[4] = array('var'=>"is_rewrad", 'msg'=>"是否啟用列式獎懲功能", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[5] = array('var'=>"is_pwd", 'msg'=>"是否啟用更改學生登入密碼功能", 'value'=>array("0"=>"否","1"=>"是"));

// $SFS_MODULE_SETUP[2] =
//        array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
