<?php
//$Id: module-cfg.php 5628 2009-09-07 00:24:46Z brucelyc $

//---------------------------------------------------
//
// 1.這裡定義：模組資料表名稱 (供 "模組權限設定" 程式使用)
//   這區的 "變數名稱" 請勿改變!!!
//-----------------------------------------------
//
// 若有一個以上，請接續此 $MODULE_TABLE_NAME 陣列來定義
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

$MODULE_TABLE_NAME[0] = "BMI";
$MODULE_TABLE_NAME[1] = "GHD";
$MODULE_TABLE_NAME[2] = "health_WH";
$MODULE_TABLE_NAME[3] = "health_sight";
$MODULE_TABLE_NAME[4] = "health_sight_ntu";
$MODULE_TABLE_NAME[5] = "health_disease";
$MODULE_TABLE_NAME[6] = "health_diseaseserious";
$MODULE_TABLE_NAME[7] = "health_bodymind";
$MODULE_TABLE_NAME[8] = "health_inherit";
$MODULE_TABLE_NAME[9] = "health_checks_item";
$MODULE_TABLE_NAME[10] = "health_checks_record";
$MODULE_TABLE_NAME[11] = "health_worm";
$MODULE_TABLE_NAME[12] = "health_uri";
$MODULE_TABLE_NAME[13] = "health_teeth";
$MODULE_TABLE_NAME[14] = "health_insurance";
$MODULE_TABLE_NAME[15] = "health_insurance_record";
$MODULE_TABLE_NAME[16] = "health_hospital";
$MODULE_TABLE_NAME[17] = "health_hospital_record";
$MODULE_TABLE_NAME[18] = "health_exam_item";
$MODULE_TABLE_NAME[19] = "health_exam_record";
$MODULE_TABLE_NAME[20] = "health_mapping";
$MODULE_TABLE_NAME[21] = "health_inject_item";
$MODULE_TABLE_NAME[22] = "health_inject_record";
$MODULE_TABLE_NAME[23] = "health_other";
$MODULE_TABLE_NAME[24] = "health_accident_place";
$MODULE_TABLE_NAME[25] = "health_accident_reason";
$MODULE_TABLE_NAME[26] = "health_accident_part";
$MODULE_TABLE_NAME[27] = "health_accident_status";
$MODULE_TABLE_NAME[28] = "health_accident_attend";
$MODULE_TABLE_NAME[29] = "health_accident_record";
$MODULE_TABLE_NAME[30] = "health_accident_part_record";
$MODULE_TABLE_NAME[31] = "health_accident_status_record";
$MODULE_TABLE_NAME[32] = "health_accident_attend_record";

//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "學生健康資訊";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2006-12-24";


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

//目錄內程式
$school_menu_p = array(
"base.php"=>"學生資料",
"input.php"=>"資料登錄",
"sight.php"=>"視力",
"wh.php"=>"身高體重",
"teesem.php"=>"口腔",
"inflection.php"=>"傳染病",
"inject.php"=>"預防接種",
"accident.php"=>"傷病",
"check.php"=>"健康檢查",
"analyze.php"=>"統計分析",
"other.php"=>"其他",
"setup.php"=>"系統選項設定",
"CSV_OUT.php"=>"資料表DUMP"
);

$study_str="'0','5','15'";

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
// 使用法：
//
// $ret_array =& get_module_setup("module_makeer")
//
//
// 詳情請參考 include/sfs_core_module.php 中的說明。
//
// 這區的 "變數名稱 $SFS_MODULE_SETUP" 請勿改變!!!
//---------------------------------------------------


//$SFS_MODULE_SETUP[0] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>1);

// 第2,3,4....個，依此類推： 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
