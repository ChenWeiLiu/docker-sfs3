<?php
//$Id: module-cfg.php 5619 2009-09-01 16:09:29Z infodaes $

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

$MODULE_TABLE_NAME[0] = "cal_elps";
$MODULE_TABLE_NAME[1] = "cal_elps_set";

//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "校務行事曆";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2004-07-31";


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
"index.php"=>"□瀏覽行事",
"cal_email.php"=>"□郵件傳送",
"cal_edit.php"=>"□編修行事",
"mgr_cal.php"=>"□設定管理",
"index2.php"=>"□獨立界面",
"important.php"=>"□學校大事列表");

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

$IS_MODULE_ARR = array(""=>"否","Y"=>"是");

$SFS_MODULE_SETUP[] =array('var'=>"Tr_BGColor", 'msg'=>"欄位標題底色", 'value'=>'#FFCCCC');
$SFS_MODULE_SETUP[] =array('var'=>"SMTP_Server", 'msg'=>"寄信主機", 'value'=>'localhost');
$SFS_MODULE_SETUP[] =array('var'=>"SMTP_Port", 'msg'=>"寄信主機通訊埠號", 'value'=>'25');
$SFS_MODULE_SETUP[] =array('var'=>"Title", 'msg'=>"預設主旨", 'value'=>'來自學校SFS3學務系統的校務行事訊息....');
$SFS_MODULE_SETUP[] =array('var'=>"Content_Head", 'msg'=>"內文抬頭敬陳辭", 'value'=>'親愛的 {{teacher}} 君：');
$SFS_MODULE_SETUP[] =array('var'=>"Content_Body", 'msg'=>"預設主文",'value'=>'下面是學校本學期 {{week}} 的行事資訊,敬請您參詳!{{content}}');
$SFS_MODULE_SETUP[] =array('var'=>"Content_Foot", 'msg'=>"內文結尾敬陳辭", 'value'=>'{{sender}} 謹啟');
$SFS_MODULE_SETUP[] =array('var'=>"Note", 'msg'=>"備註事項", 'value'=>'PS.權責單位可能會有新的行事布告，最新資訊以學務系統公告為準！');
$SFS_MODULE_SETUP[] =array('var'=>"Reply", 'msg'=>"要求回條", 'value'=>$IS_MODULE_ARR);
$SFS_MODULE_SETUP[] =array('var'=>"Cc_Send", 'msg'=>"寄送副本給管理者", 'value'=>$IS_MODULE_ARR);
$SFS_MODULE_SETUP[] =array('var'=>"Show_Event", 'msg'=>"先表列單位行事資訊", 'value'=>$IS_MODULE_ARR);

?>
