<?php

// $Id: module-cfg.php 9085 2017-06-12 07:53:27Z infodaes $

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


$MODULE_PRO_KIND_NAME = "學期初設定";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="";

// 模組最後更新日期
$MODULE_UPDATE="";

// 是否為系統模組? 若設為 1 則該模組不可刪除
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

//目錄內程式
$dts=($IS_JHORES==6)?"設定導師":"設定級任老師";
$school_menu_p = array(
"setup_schoolday.php"=>"開學日設定");
$school_menu_p["class_year_setup.php"]="班級設定";
$school_menu_p["seme_date.php"]="上課日設定";
$school_menu_p["score_setup.php"]="成績設定";
$school_menu_p["ss_setup.php"]="課程設定";
//$school_menu_p["section_setup.php"]="節數設定";
//$school_menu_p["auto_course_setup.php"]="自動排課";
$school_menu_p["classroom_setup.php"]="專科教室設定";
$school_menu_p["course_setup3.php"]="課表設定";
$school_menu_p["chc_teacher.v2.php"]=$dts;
$school_menu_p["section_time.php"]="各節時間設定";

//起始年級
$school_kind_start=1;

//結束年級
$school_kind_end=9;

//年級個數
$school_kind_name_n=($school_kind_end-$school_kind_start)+1;


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
//	array('var'=>"IS_STANDALONE", 'msg'=>"是否有獨立的界面(1是,0否)", 'value'=>0);


// 第2,3,4....個，依此類推： 

$SFS_MODULE_SETUP[1] = array('var'=>"debug", 'msg'=>"課表設定時顯示課程代碼", 'value'=>array(1=>"是",0=>"否"));
$SFS_MODULE_SETUP[2] = array('var'=>"FIN_SCORE_RATE_MODE", 'msg'=>"計算學期各領域總平均加權模式", 'value'=>array(0=>"學習領域算數平均",1=>"學分式加權平均"));
$SFS_MODULE_SETUP[3] = array('var'=>"IS_CLASS_SUBJECT", 'msg'=>"允許設定班級課程?", 'value'=>array(0=>"否",1=>"是"));
$SFS_MODULE_SETUP[4] = array('var'=>"show_nor_items", 'msg'=>"顯示平時成績項目資料?",'value'=>array(0=>"否",1=>"是"));
$SFS_MODULE_SETUP[5] = array('var'=>"local_language", 'msg'=>"本土語言預設語言別?",'value'=>'閩語(閩南語)');
?>
