<?php

//---------------------------------------------------
//
// 1.這裡定義：系統變數 (供 "模組安裝管理" 程式使用)
//------------------------------------------
//
// "模組安裝管理" 程式會寫入貴校的 SFS/pro_kind 表中
//
// 建議：請儘量用英文大寫來定義，最好能由字面看出其代表的意義。
//---------------------------------------------------
//

// 您這個模組的名稱，就是您這個模組放置在 SFS 中的目錄名稱

$MODULE_NAME = "learning";


//本模組須區分管理權
$MODULE_MAN = 1 ;
//管理權說明
$MODULE_MAN_DESCRIPTION = "具有管理權人員,可刪修其他人員佈告";



//---------------------------------------------------
//
// 2.這裡定義：模組資料表名稱 (供 "模組安裝管理" 程式使用)
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

$MODULE_TABLE_NAME[0] = "unit_c";
$MODULE_TABLE_NAME[1] = "unit_tome";
$MODULE_TABLE_NAME[2] = "unit_u";
$MODULE_TABLE_NAME[3] = "test_badge";
$MODULE_TABLE_NAME[4] = "test_data";
$MODULE_TABLE_NAME[5] = "test_online";
$MODULE_TABLE_NAME[6] = "test_paper";
$MODULE_TABLE_NAME[7] = "test_score";
$MODULE_TABLE_NAME[8] = "test_setup";
$MODULE_TABLE_NAME[9] = "poke_base";
//
// 3.這裡定義：模組中文名稱，請精簡命名 (供 "模組安裝管理" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "教學資源";


//---------------------------------------------------
//
// 4. 這裡定義：模組版本相關資訊 (供 "相關系統程式" 取用)
//
//---------------------------------------------------

// 模組版本
$MODULE_VER="1.0.0";
// 模組程式作者
$MODULE_AUTHOR="log7";

// 模組版權種類
$MODULE_LICENSE="";

// 模組外顯名稱(供 "模組設定" 程式使用)
$MODULE_DISPLAY_NAME="教學資源";

// 模組隸屬群組
$MODULE_GROUP_NAME="校務行政";

// 模組開始日期
$MODULE_CREATE_DATE="2006-05-10";

// 模組最後更新日期
$MODULE_UPDATE="2005-05-10 08:30:00";

// 模組更新者
$MODULE_UPDATE_MAN="log7";


//---------------------------------------------------
//
// 5. 這裡請定義：您這支程式需要用到的：變數或常數
//------------------------------^^^^^^^^^^
//
// (不想被 "模組設定" 程式控管者，請置放於此)
//
// 建議：請儘量用英文大寫來定義，最好要能由字面看出其代表的意義。
//---------------------------------------------------



//---------------------------------------------------
//
// 6. 這裡定義：預設值要由 "模組設定" 程式來控管者，
//    若不想，可不必設定。
//
// 格式： var 代表變數名稱
//       msg 代表顯示訊息
//       value 代表變數設定值
//---------------------------------------------------

$SFS_MODULE_SETUP[] =
	array('var'=>"page_count", 'msg'=>"每頁顯示筆數", 'value'=>15);
$SFS_MODULE_SETUP[] =
	array('var'=>"is_standalone", 'msg'=>"是否有獨立的界面(1 是,0 否", 'value'=>0);
// 第2,3,4....個，依此類推： 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
