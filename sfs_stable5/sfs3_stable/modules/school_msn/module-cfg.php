<?php
//$Id$

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

$MODULE_TABLE_NAME[0] = "sc_msn_data";
$MODULE_TABLE_NAME[1] = "sc_msn_online";
$MODULE_TABLE_NAME[2] = "sc_msn_file";
$MODULE_TABLE_NAME[3] = "sc_msn_folder";
$MODULE_TABLE_NAME[4] = "sc_msn_board_pic";


//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "校園MSN";

//$MODULE_MAN=True;
//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2014-01-14";


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
$MODULE_MENU=array("index.php"=>"使用說明","msn_folder.php"=>"檔案夾設定","msn_file.php"=>"檔案清理","msn_users.php"=>"使用者狀態及功能設定");


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

$SFS_MODULE_SETUP[] = array('var'=>"SOURCE", 'msg'=>"首頁公告訊息來源", 'value'=>array("board"=>"board模組","jboard"=>"jboard模組"));
$SFS_MODULE_SETUP[] = array('var'=>"LAST_DAYS", 'msg'=>"首頁公告顯示幾日內的訊息", 'value'=>15);
$SFS_MODULE_SETUP[] = array('var'=>"PRESERVE_DAYS", 'msg'=>"私人訊息保留天數", 'value'=>15);
$SFS_MODULE_SETUP[] = array('var'=>"CLEAN_MODE", 'msg'=>"私人過期訊息清理模式", 'value'=>array(0=>"全部刪除",1=>"保留尚未讀取的"));
$SFS_MODULE_SETUP[] = array('var'=>"POSITION", 'msg'=>"彈出視窗預設位置", 'value'=>array(0=>"右上角",1=>"左上角",2=>"正中間",3=>"右下角",4=>"左下角"));
$SFS_MODULE_SETUP[] =	array('var'=>"insite_ip", 'msg'=>"設定內部IP範圍,留空時使用系統預設值,例163.17.43 或 163.17.43.1-163.17.43.128 ", 'value'=>'');

$SFS_MODULE_SETUP[] = array('var'=>"SMPTHost", 'msg'=>"SMPT伺服器網址", 'value'=>'');
$SFS_MODULE_SETUP[] = array('var'=>"SMPTAuth", 'msg'=>"SMPT伺服器認證", 'value'=>array(0=>"不需要",1=>"需要"));
$SFS_MODULE_SETUP[] = array('var'=>"SMPTPort", 'msg'=>"SMPT伺服器Port", 'value'=>'25');
$SFS_MODULE_SETUP[] = array('var'=>"SMPTusername", 'msg'=>"SMPT的使用者帳號", 'value'=>'username@smpt_url.com');
$SFS_MODULE_SETUP[] = array('var'=>"SMPTpassword", 'msg'=>"SMPT的使用者帳號", 'value'=>'yourpassword');

$SFS_MODULE_SETUP[] = array('var'=>"portfolio", 'msg'=>"教師網頁連結列表", 'value'=>array(0=>"不啟用",1=>"啟用"));

$SFS_MODULE_SETUP[] = array('var'=>"IS_UTF8", 'msg'=>"sfs3的編碼方式(通常為Big5)", 'value'=>array(0=>"Big5",1=>"UTF8"));

?>
