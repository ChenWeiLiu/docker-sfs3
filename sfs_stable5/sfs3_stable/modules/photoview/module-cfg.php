<?php

// $Id: module-cfg.php 6336 2011-02-22 09:01:01Z infodaes $

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

$MODULE_TABLE_NAME[0] = "photoviewtb";

//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "相片館";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2003-05-03 ";


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
  $chkfile=array(".jpg",".jpeg");	//只可以上傳jpg格式圖檔
  $tbname = "photoviewtb" ;	//資料表名


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



$SFS_MODULE_SETUP[0] =
	array('var'=>"pagesites", 'msg'=>"每頁顯示筆數", 'value'=>5);
$SFS_MODULE_SETUP[1] =
	array('var'=>"upfilenum", 'msg'=>"預設上傳檔案數", 'value'=>10);
$SFS_MODULE_SETUP[2] =
	array('var'=>"BIG_PIC_X", 'msg'=>"最大圖寬度", 'value'=>800);
$SFS_MODULE_SETUP[3] =
	array('var'=>"BIG_PIC_Y", 'msg'=>"最大圖高度", 'value'=>600);	
$SFS_MODULE_SETUP[4] =
	array('var'=>"show_col", 'msg'=>"顯示分欄數", 'value'=>2);		
$SFS_MODULE_SETUP[5] =
	array('var'=>"stand_alone", 'msg'=>"獨立介面顯示", 'value'=>array(0=>'否',1=>'是'));
$SFS_MODULE_SETUP[6] =
	array('var'=>"view_title", 'msg'=>"顯示標題", 'value'=>'相片展');
$SFS_MODULE_SETUP[7] =
	array('var'=>"memo_pos", 'msg'=>"說明顯示位置", 'value'=>array(0=>'右',1=>'下'));
$SFS_MODULE_SETUP[8] =
	array('var'=>"font_size", 'msg'=>"文字大小", 'value'=>'12px');
$SFS_MODULE_SETUP[9] =
	array('var'=>"font_color", 'msg'=>"文字顏色", 'value'=>'#FF0000');
$SFS_MODULE_SETUP[10] =
	array('var'=>"show_title", 'msg'=>"顯示相片名稱", 'value'=>array(0=>'否',1=>'是'));
$SFS_MODULE_SETUP[11] =
	array('var'=>"show_date", 'msg'=>"顯示建置日期", 'value'=>array(0=>'否',1=>'是'));	
$SFS_MODULE_SETUP[12] =
	array('var'=>"show_intro", 'msg'=>"顯示簡介", 'value'=>array(0=>'否',1=>'是'));	
$SFS_MODULE_SETUP[13] =
	array('var'=>"show_auth", 'msg'=>"顯示張貼者", 'value'=>array(0=>'否',1=>'是'));	
$SFS_MODULE_SETUP[14] =
	array('var'=>"show_op", 'msg'=>"顯示操作圖鈕", 'value'=>array(0=>'否',1=>'是'));		
	

// 第2,3,4....個，依此類推： 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
