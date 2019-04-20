<?php

// $Id: module-cfg.php 8926 2016-07-19 07:53:43Z chiming $

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

//本模組須區分管理權
$MODULE_MAN = 1 ;

//管理權說明
$MODULE_MAN_DESCRIPTION = "具有管理權人員,對於評語庫有完整功能,一般使用者僅可新增評語";


//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "製作成績單";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.1";

// 模組最後更新日期
$MODULE_UPDATE="2003-09-04";

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
//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

if ($IS_JHORES=="6") {
$school_menu_p = array(
"chart_j.php"=>"國中成績單制作",
"chc_9401.php"=>"網頁式成績單",
"chc_seme.php"=>"階段成績成績檢查",
"chc_seme_rank.php"=>"階段成績進退步查詢",
"chc_check.php"=>"各班成績檢查",
"chc_top3score.php"=>"階段成績表現優異名單"
);

} else {
$school_menu_p = array(
"chart_e.php"=>"國小成績單制作",
"chc_9401.php"=>"網頁式成績單",
"chc_seme.php"=>"階段成績成績檢查",
"chc_seme_rank.php"=>"階段成績進退步查詢",
"chc_check.php"=>"各班成績檢查",
"chc_top3score.php"=>"階段成績表現優異名單"
);
}
$school_menu_p["chc_score_memo.php"]="描述文字編修";

//目錄內程式

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

$IS_MODULE_ARR = array("y"=>"是","n"=>"否");
$SFS_MODULE_SETUP[0] =array('var'=>"is_modify", 'msg'=>"允許使用者編輯評語詞庫", 'value'=>$IS_MODULE_ARR);

$LINE_WIDTH_ARR = array("0.01cm"=>"0.01cm","0.015cm"=>"0.015cm","0.02cm"=>"0.02cm","0.025cm"=>"0.025cm");
$SFS_MODULE_SETUP[1] =array('var'=>"line_width", 'msg'=>"成績單格線寬度", 'value'=>$LINE_WIDTH_ARR);

$LINE_COLOR_ARR = array("#000000"=>"黑色","#FF0000"=>"紅色","#00008B"=>"藍色","#228B22"=>"綠色");
$SFS_MODULE_SETUP[2] =array('var'=>"line_color", 'msg'=>"成績單格線顏色", 'value'=>$LINE_COLOR_ARR);

$IMG_ARR= array("1.27cm"=>"1.27cm","1.5cm"=>"1.5cm","1.8cm"=>"1.8cm","2cm"=>"2cm","2.5cm"=>"2.5cm","3cm"=>"3cm","3.5cm"=>"3.5cm","4cm"=>"4cm");
$SFS_MODULE_SETUP[3] =array('var'=>"draw_img_width", 'msg'=>"簽章寬度", 'value'=>$IMG_ARR);
$SFS_MODULE_SETUP[4] =array('var'=>"draw_img_height", 'msg'=>"簽章高度", 'value'=>$IMG_ARR);

$SIGN_ARR= array("0"=>"空白","1"=>"顯示簽章檔","2"=>"顯示文字");
$SFS_MODULE_SETUP[5] =array('var'=>"sign_1_form", 'msg'=>"校長簽章欄顯示方式", 'value'=>$SIGN_ARR);
$SFS_MODULE_SETUP[6] =array('var'=>"sign_2_form", 'msg'=>"教務主任簽章欄顯示方式", 'value'=>$SIGN_ARR);
$SFS_MODULE_SETUP[7] =array('var'=>"sign_3_form", 'msg'=>"學務主任簽章欄顯示方式", 'value'=>$SIGN_ARR);
$SFS_MODULE_SETUP[8] =array('var'=>"sign_1_name", 'msg'=>"校長姓名", 'value'=>'');
$SFS_MODULE_SETUP[9] =array('var'=>"sign_2_name", 'msg'=>"教務主任姓名", 'value'=>'');
$SFS_MODULE_SETUP[10] =array('var'=>"sign_3_name", 'msg'=>"學務主任姓名", 'value'=>'');

$SIGN_3_TITLE_ARR=array("0"=>"學務主任","1"=>"訓導主任","2"=>"教導主任");
$SFS_MODULE_SETUP[11] =array('var'=>"sign_3_title", 'msg'=>"學務主任職稱", 'value'=>$SIGN_3_TITLE_ARR);
$SFS_MODULE_SETUP[12] =array('var'=>"none_text", 'msg'=>"網頁成績單關閉學習文字敘述", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[13] =array('var'=>"text_title", 'msg'=>"學習文字敘述欄位名(空白時顯示原名稱)", 'value'=>'');

$DISABLE_SUBJECT_MEMO_TITLE_ARR=array("0"=>"不顯示","1"=>"顯示");
$SFS_MODULE_SETUP[14] =array('var'=>"disable_subject_memo_title", 'msg'=>"學習文字敘述是否顥示分科名稱", 'value'=>$DISABLE_SUBJECT_MEMO_TITLE_ARR);

// 系統選項
$SFS_TEXT_SETUP[] = array(
"g_id"=>3,
"var"=>"日常行為表現",
"s_arr"=>array(1=>"表現優異",2=>"表現良好",3=>"表現尚可",4=>"需再加油",5=>"有待改進")
);
$SFS_TEXT_SETUP[] = array(
"g_id"=>3,
"var"=>"團體活動表現",
"s_arr"=>array(1=>"表現優異",2=>"表現良好",3=>"表現尚可",4=>"需再加油",5=>"有待改進")
);
$SFS_TEXT_SETUP[] = array(
"g_id"=>3,
"var"=>"公共服務表現",
"s_arr"=>array(1=>"表現優異",2=>"表現良好",3=>"表現尚可",4=>"需再加油",5=>"有待改進")
);

$SFS_TEXT_SETUP[] = array(
"g_id"=>3,
"var"=>"校外特殊表現",
"s_arr"=>array(1=>"表現優異",2=>"表現良好",3=>"表現尚可",4=>"需再加油",5=>"有待改進")
);
$SFS_TEXT_SETUP[] = array(
"g_id"=>3,
"var"=>"努力程度",
"s_arr"=>array(1=>"表現優異",2=>"表現良好",3=>"表現尚可",4=>"需再加油",5=>"有待改進")
);


?>
