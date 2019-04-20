<?php

// $Id: $

$MODULE_PRO_KIND_NAME = "自訂通訊錄";


// 需要使用管理者權限
$MODULE_MAN=true;

// 資料表名稱定義

$MODULE_TABLE_NAME[0] = "address_book";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0";

// 模組最後更新日期
$MODULE_UPDATE="2010-10-10";

//重要模組，免被勿刪
$SYS_MODULE=0;

$menu_p = array("manage.php"=>"樣式管理", "html_output.php"=>"通訊錄輸出") ;

//模組變數
$SFS_MODULE_SETUP[0]=
	array('var'=>"student_forbid", 'msg'=>"非模組管理員禁列的學生項目(請以[]包覆)", 'value'=>"[身份證字號][出生年月日]");
$SFS_MODULE_SETUP[1]=
	array('var'=>"teacher_forbid", 'msg'=>"非模組管理員禁列的教職員項目(請以[]包覆)", 'value'=>"[身份證字號]");


?>
