<?php
//$Id: module-cfg.php 6414 2011-04-21 08:23:58Z infodaes $

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

$MODULE_TABLE_NAME[] = "career_consultation";
$MODULE_TABLE_NAME[] = "career_contact";
$MODULE_TABLE_NAME[] = "career_course";
$MODULE_TABLE_NAME[] = "career_exam";
$MODULE_TABLE_NAME[] = "career_explore";
$MODULE_TABLE_NAME[] = "career_guidance";
$MODULE_TABLE_NAME[] = "career_mystory";
$MODULE_TABLE_NAME[] = "career_opinion";
$MODULE_TABLE_NAME[] = "career_parent";
$MODULE_TABLE_NAME[] = "career_race";
$MODULE_TABLE_NAME[] = "career_self_ponder";
$MODULE_TABLE_NAME[] = "career_test";
$MODULE_TABLE_NAME[] = "career_view";

//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "12年國教生涯輔導紀錄";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2013-01-07";


// 需要使用管理者權限
$MODULE_MAN=true;

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
$menu_p["career_pwd.php"]="學生登入密碼";
$menu_p["career_contact.php"]="導師及輔導教師";
$menu_p["mystory.php"]="我的成長故事";
$menu_p["psy_test.php"]="各項心理測驗";
if(checkid($_SERVER['SCRIPT_FILENAME'],1)) $menu_p["psy_test_import.php"]="心理測驗資料匯入";
$menu_p["study_spe.php"]="學習成果及特殊表現";
$menu_p["career_view.php"]="生涯統整面面觀";
$menu_p["career_evaluate.php"]="生涯發展規劃書";
$menu_p["career_guidance.php"]="生涯輔導諮詢建議";
$menu_p["career_sign.php"]="審閱簽記";
$menu_p["career_statistics.php"]="統計分析";
$menu_p["career_report.php"]="報表(測試中)";


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

//$SFS_MODULE_SETUP[]=array('var'=>'career_ghostwrite', 'msg'=>'啟用教職員登載學生資料功能', 'value'=>array(''=>'否','1'=>'是'));

$SFS_MODULE_SETUP[]=array('var'=>'career_previous', 'msg'=>'啟用補登過往學期資料功能', 'value'=>array(''=>'否','1'=>'是'));
$SFS_MODULE_SETUP[]=array('var'=>'guidance_title', 'msg'=>'性向測驗預設名稱', 'value'=>'');
$SFS_MODULE_SETUP[]=array('var'=>'interest_title', 'msg'=>'興趣測驗預設名稱', 'value'=>'');
$SFS_MODULE_SETUP[]=array('var'=>'other_title', 'msg'=>'其他測驗預設名稱', 'value'=>'');
$SFS_MODULE_SETUP[]=array('var'=>'sort_rank', 'msg'=>'統計排序名次列示數', 'value'=>10);

?>
