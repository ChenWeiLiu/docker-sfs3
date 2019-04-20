<?php

// $Id: module-cfg.php 6104 2010-09-08 03:44:46Z infodaes $

// 資料表名稱定義
$MODULE_TABLE_NAME[0] = "sfs_module";
//$MODULE_TABLE_NAME[1] = "pro_check_new";

//模組中文名稱
$MODULE_PRO_KIND_NAME = "模組管理";

// 模組最後更新版本
$MODULE_UPDATE_VER="1.2";

// 模組最後更新日期
$MODULE_UPDATE="2009-06-01";

//系統重要模組
$SYS_MODULE=1;

//---------------------------------------------------
//
// 5. 這裡請定義：您這支程式需要用到的：變數或常數
//------------------------------^^^^^^^^^^
//
// (不想被 "模組設定" 程式控管者，請置放於此)
//
// 建議：請儘量用英文大寫來定義，最好要能由字面看出其代表的意義。
//---------------------------------------------------

$MODULE_DIR=$SFS_PATH."/modules/";

//目錄內程式
$school_menu_p = array(
"index.php"=>"模組管理",
"add_kind.php"=>"新增分類",
"add_module.php"=>"新增模組",
"del_module.php"=>"移除模組",
"garbage_sql.php"=>"回收桶",
"limit_adm.php"=>"權限列表",
"up_list.php"=>"模組升級訊息",
"up_list2.php"=>"模組更新狀態"
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
	
	
$SFS_MODULE_SETUP[0] =
        array('var'=>"IS_REUPGRADE",'msg'=>"是否顯示重新升級連結( 是/否 )?",'value'=>array(''=>'否','Y'=>'是'));
		
// 第2,3,4....個，依此類推： 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
