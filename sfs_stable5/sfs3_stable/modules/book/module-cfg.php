<?php
                                                                                                                             
// $Id: module-cfg.php 7794 2013-12-03 03:39:50Z infodaes $

//---------------------------------------------------
//
// 1.這裡定義：系統變數 (供 "模組安裝管理" 程式使用)
//------------------------------------------
//
// "模組安裝管理" 程式會寫入貴校的 SFS/pro_kind 表中
//
// 建議：請儘量用英文大寫來定義，最好能由字面看出其代表的意義。
//---------------------------------------------------
// 您打算把此一模組放在那一個系統區塊中呢?
//
// 目前僅有二區供您選擇
//
// "校務行政" 模組區塊代碼：28
// "工具箱"  模組區塊代碼：161
//---------------------------------------------------


// 您這個模組的名稱，就是您這個模組放置在 SFS 中的目錄名稱

$MODULE_NAME = "book";


// 模組置放主要目錄：
// 可選擇的有 school 及 module

$MODULE_MAIN_DIR="school";

//本模組須區分管理權
$MODULE_MAN = 1 ;

//管理權說明
$MODULE_MAN_DESCRIPTION = "具有管理權人員,可授權學生操作圖書系統";


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

$MODULE_TABLE_NAME[0] = "book";
$MODULE_TABLE_NAME[1] = "borrow";
$MODULE_TABLE_NAME[2] = "bookch1";

//---------------------------------------------------
//
// 3.這裡定義：模組中文名稱，請精簡命名 (供 "模組安裝管理" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "圖書管理系統";


//---------------------------------------------------
//
// 4. 這裡定義：模組版本相關資訊 (供 "相關系統程式" 取用)
//
//---------------------------------------------------

// 模組版本
$MODULE_VER="2.0.1";

// 模組程式作者
$MODULE_AUTHOR="hami";

// 模組版權種類
$MODULE_LICENSE="";

// 模組外顯名稱(供 "模組設定" 程式使用)
$MODULE_DISPLAY_NAME="圖書管理";

// 模組隸屬群組
$MODULE_GROUP_NAME="校務行政";

// 模組開始日期
$MODULE_CREATE_DATE="2002-12-15";

// 模組最後更新日期
$MODULE_UPDATE="2003-03-19 08:30:00";

// 模組更新者
$MODULE_UPDATE_MAN="hami";


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
	array('var'=>"lib_name", 'msg'=>"圖書室條碼顯示部份", 'value'=>"外埔圖書室");
$SFS_MODULE_SETUP[] =
	array('var'=>"barcore_cols", 'msg'=>"圖書室條碼顯示行數", 'value'=>4);
$SFS_MODULE_SETUP[] =
	array('var'=>"barcare_type", 'msg'=>"圖書室條碼圖型格式(Png or Gif)", 'value'=>'Png');
$SFS_MODULE_SETUP[] =
	array('var'=>"man_name", 'msg'=>"系統管理員姓名", 'value'=>"系統管理員");
$SFS_MODULE_SETUP[] =
	array('var'=>"man_mail", 'msg'=>"管理員 email", 'value'=>"");
$SFS_MODULE_SETUP[] =
	array('var'=>"data_mail", 'msg'=>"資料管理員", 'value'=>"資料管理員");
$SFS_MODULE_SETUP[] =
	array('var'=>"man_ip1", 'msg'=>"借還書限定IP 1", 'value'=>"163.17.169");
$SFS_MODULE_SETUP[] =
	array('var'=>"man_ip2", 'msg'=>"借還書限定IP 2", 'value'=>"163.17.169.10");
$SFS_MODULE_SETUP[] =
	array('var'=>"man_ip3", 'msg'=>"借還書限定IP 3", 'value'=>"163.17.169.11");
$SFS_MODULE_SETUP[] =
	array('var'=>"yetdate", 'msg'=>"學生借閱日數", 'value'=>14);
$SFS_MODULE_SETUP[] =
	array('var'=>"tea_yetdate", 'msg'=>"教師借閱日數", 'value'=>28);
$SFS_MODULE_SETUP[] =
	array('var'=>"sort_num", 'msg'=>"排行榜顯示名次", 'value'=>40);

$SFS_MODULE_SETUP[] =
	array('var'=>"un_limit_ip", 'msg'=>"取消限制IP 功能", 'value'=>array(0=>"否",1=>"是"));

$SFS_MODULE_SETUP[] =
	array('var'=>"amount_limit_s", 'msg'=>"學生借閱本數限制", 'value'=>7);
$SFS_MODULE_SETUP[] =
	array('var'=>"pic_width", 'msg'=>"學生大頭照顯示寬度", 'value'=>64);	
	
// 第2,3,4....個，依此類推： 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
