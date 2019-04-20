<?php
// $Id: $

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


$MODULE_PRO_KIND_NAME = "班級學期領域成績冊";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0";

// 模組最後更新日期
$MODULE_UPDATE="2012/1/13";

//重要模組，免被勿刪
$SYS_MODULE=0;
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
$SFS_MODULE_SETUP[]=array('var'=>"behavior_script", 'msg'=>"友善列印時顯示日常生活表現", 'value'=>array(1=>'是',0=>'否'));
$SFS_MODULE_SETUP[]=array('var'=>"style", 'msg'=>"報表樣式", 'value'=>array(1=>'成績與日常生活表現合列',0=>'成績與日常生活表現分列'));
$SFS_MODULE_SETUP[]=array('var'=>"title", 'msg'=>"報表名稱", 'value'=>'學期成績冊');
$SFS_MODULE_SETUP[]=array('var'=>"title_font_name", 'msg'=>"報表抬頭字體名稱", 'value'=>'標楷體');
$SFS_MODULE_SETUP[]=array('var'=>"title_font_size", 'msg'=>"報表抬頭字體大小", 'value'=>'22px');
$SFS_MODULE_SETUP[]=array('var'=>"text_size", 'msg'=>"內文字體大小", 'value'=>'12px');
$SFS_MODULE_SETUP[]=array('var'=>"percision", 'msg'=>"成績顯示的精度", 'value'=>array(1=>'整數',2=>'小數1位',3=>'小數2位'));
$SFS_MODULE_SETUP[]=array('var'=>"class_width", 'msg'=>"班級欄顯示寬度", 'value'=>60);
$SFS_MODULE_SETUP[]=array('var'=>"num_width", 'msg'=>"座號欄顯示寬度", 'value'=>25);
$SFS_MODULE_SETUP[]=array('var'=>"id_width", 'msg'=>"學號欄顯示寬度", 'value'=>40);
$SFS_MODULE_SETUP[]=array('var'=>"name_width", 'msg'=>"姓名欄顯示寬度", 'value'=>60);
$SFS_MODULE_SETUP[]=array('var'=>"area_width", 'msg'=>"成績欄顯示寬度", 'value'=>30);
$SFS_MODULE_SETUP[]=array('var'=>"avg_width", 'msg'=>"平均成績欄顯示寬度", 'value'=>40);
$SFS_MODULE_SETUP[]=array('var'=>"header_bgcolor", 'msg'=>"欄位名顯示底色", 'value'=>'#ccffcc');
$SFS_MODULE_SETUP[]=array('var'=>"area_avg_bgcolor", 'msg'=>"領域平均成績欄顯示底色", 'value'=>'#ffffcc');
$SFS_MODULE_SETUP[]=array('var'=>"print_sign_row", 'msg'=>"簽章列", 'value'=>array('Y'=>'是','N'=>'否'));
$SFS_MODULE_SETUP[]=array('var'=>"sign_row", 'msg'=>"簽章列", 'value'=>'導師：　　　　　　　　　　　　　教務主任：　　　　　　　　　　　　　校長：');

?>
