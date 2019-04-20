<?php

//$Id: module-cfg.php 5680 2009-10-06 16:10:42Z infodaes $
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
//$MODULE_TABLE_NAME[1]="xxxx";
//
// 請注意要和 module.sql 中的 table 名稱一致!!!
//---------------------------------------------------
// 資料表名稱定義
//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------

$MODULE_PRO_KIND_NAME = "物品借用申請";

//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2007-09-23";

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
$MENU_P = array("board.php"=>"公告訊息","query.php"=>"查詢借用","request.php"=>"申請紀錄","record.php"=>"借用紀錄");

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
$IS_MODULE_ARR = array("Y"=>"是",""=>"否");
$SFS_MODULE_SETUP[] =array('var'=>"User_Email", 'msg'=>"教職員須設定郵件信箱方可提出申請?", 'value'=>$IS_MODULE_ARR);
$SFS_MODULE_SETUP[] =array('var'=>"User_Removable", 'msg'=>"借用者可撤除申請?", 'value'=>$IS_MODULE_ARR);
$SFS_MODULE_SETUP[] =array('var'=>"Delay_Refused", 'msg'=>"逾期未歸拒絕再借用?", 'value'=>$IS_MODULE_ARR);
$SFS_MODULE_SETUP[] =array('var'=>"Delay_Refused_announce", 'msg'=>"拒絕再借用文字說明?", 'value'=>'下面的物品,您尚未歸還,請歸還後再辦理新的借用~~~');

$SFS_MODULE_SETUP[] =array('var'=>"Table_width", 'msg'=>"清單表格佔據畫面寬度比例(%)", 'value'=>'100');
$SFS_MODULE_SETUP[] =array('var'=>"Tr_BGColor", 'msg'=>"標題列底色", 'value'=>'#C8FFAA');
$SFS_MODULE_SETUP[] =array('var'=>"Lendable_BGColor", 'msg'=>"可借用物品底色", 'value'=>'#FFFFFF');
$SFS_MODULE_SETUP[] =array('var'=>"Requested_BGColor", 'msg'=>"已預借物品底色", 'value'=>'#CCFFCC');
$SFS_MODULE_SETUP[] =array('var'=>"NotReturned_BGColor", 'msg'=>"已借出物品底色", 'value'=>'#AAAAAA');
$SFS_MODULE_SETUP[] =array('var'=>"OverTime_BGColor", 'msg'=>"逾期未歸還物品底色", 'value'=>'#FFAAAA');
$SFS_MODULE_SETUP[] =array('var'=>"Returned_BGColor", 'msg'=>"已歸還物品底色", 'value'=>'#AAAAAA');

$SFS_MODULE_SETUP[] =array('var'=>"Read_BGColor", 'msg'=>"已經閱讀過的公告底色", 'value'=>'#CCCCCC');
$SFS_MODULE_SETUP[] =array('var'=>"Pic_Width", 'msg'=>"圖片顯示視窗寬度", 'value'=>'320');
$SFS_MODULE_SETUP[] =array('var'=>"Pic_Height", 'msg'=>"圖片顯示視窗高度", 'value'=>'240');
//$SFS_MODULE_SETUP[] =array('var'=>"Refused_Reason", 'msg'=>"外借申請狀態選項", 'value'=>'#CCCCCC');

?>
