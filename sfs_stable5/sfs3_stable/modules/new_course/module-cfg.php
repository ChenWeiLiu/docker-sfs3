<?php

// $Id: module-cfg.php 6662 2012-01-09 08:42:43Z infodaes $

// 資料表名稱定義
$MODULE_TABLE_NAME[0] = "";
//$MODULE_TABLE_NAME[1] = "pro_check_new";

//模組中文名稱
$MODULE_PRO_KIND_NAME = "學校課表查詢系統";

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0";

// 模組最後更新日期
$MODULE_UPDATE="2003-01-01";


// 需要使用管理者權限
$MODULE_MAN=true;

//---------------------------------------------------
//
// 5. 這裡請定義：您這支程式需要用到的：變數或常數
//------------------------------^^^^^^^^^^
//
// (不想被 "模組設定" 程式控管者，請置放於此)
//
// 建議：請儘量用英文大寫來定義，最好要能由字面看出其代表的意義。
//---------------------------------------------------


//目錄內程式
$school_menu_p = array(
"index.php"=>"班級課表查詢",
"teacher_class.php"=>"教師課表查詢",
"room_class.php"=>"專科教室課表查詢" ,
"blank_class.php"=>"空堂查詢"
);


//---------------------------------------------------
//
// 6. 這裡定義：預設值要由 "模組設定" 程式來控管者，
//    若不想，可不必設定。
//
// 格式： var 代表變數名稱
//       msg 代表顯示訊息
//       value 代表變數設定值
//---------------------------------------------------

$SFS_MODULE_SETUP[0] =	array('var'=>"IS_STANDALONE", 'msg'=>"是否有獨立的界面", 'value'=>array("0"=>"否","1"=>"是"));

//$SFS_MODULE_SETUP[0] =
//	array('var'=>"IS_STANDALONE", 'msg'=>"是否有獨立的界面(1是,0否)", 'value'=>0);


// 第2,3,4....個，依此類推： 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
