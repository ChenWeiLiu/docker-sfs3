<?php

// $Id: $

$MODULE_PRO_KIND_NAME = "亞太電信簡訊代發";


// 需要使用管理者權限
$MODULE_MAN=true;

// 資料表名稱定義

$MODULE_TABLE_NAME[0] = "sms_apol_task";
$MODULE_TABLE_NAME[1] = "sms_apol_record";



//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0";

// 模組最後更新日期
$MODULE_UPDATE="2012-7-1";

//重要模組，免被勿刪
$SYS_MODULE=0;


$yes_or_no = array("1"=>"是","0"=>"否");
 $SFS_MODULE_SETUP[0] =	array('var'=>"MDN", 'msg'=>"企業代表號", 'value'=>'');
 $SFS_MODULE_SETUP[1] =	array('var'=>"UID", 'msg'=>"帳號", 'value'=>'');
 $SFS_MODULE_SETUP[2] =	array('var'=>"UPASS", 'msg'=>"密碼", 'value'=>'');
 $SFS_MODULE_SETUP[3] =	array('var'=>"sign_name", 'msg'=>"簡訊內容強制增加發送者姓名", 'value'=>$yes_or_no);
 $SFS_MODULE_SETUP[4] =	array('var'=>"room_select", 'msg'=>"處室選取方式", 'value'=>array('0'=>'選項式','1'=>'下拉式'));
 $SFS_MODULE_SETUP[5] =	array('var'=>"class_select", 'msg'=>"班級選取方式", 'value'=>array('0'=>'選項式','1'=>'下拉式'));


?>
