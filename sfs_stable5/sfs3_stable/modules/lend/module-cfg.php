<?php

//$Id: module-cfg.php 6207 2010-10-05 04:46:46Z infodaes $

$MODULE_TABLE_NAME[1]="equ_board";
$MODULE_TABLE_NAME[2]="equ_equipments";
$MODULE_TABLE_NAME[3]="equ_record";
$MODULE_TABLE_NAME[4]="equ_request";
//

$MODULE_PRO_KIND_NAME = "物品借用管理";


// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2007-10-8";


//目錄內程式
$MENU_P = array("item.php"=>"經管物品維護","issue.php"=>"配發登錄","allow.php"=>"申請核示","barcode_lend.php"=>"撥交登錄","maintain.php"=>"借用紀錄維護","barcode_refund.php"=>"歸還登錄","message.php"=>"訊息公告","mail.php"=>"郵件通知","report.php"=>"統計分析","barcode_crash.php"=>"物品報廢","consign.php"=>"管理移交");

//---------------------------------------------------
$IS_MODULE_ARR = array("Y"=>"是",""=>"否");
$import_ARR = array("I"=>"新增模式(INSERT)","R"=>"替換模式(REPLACE)");

$SFS_MODULE_SETUP[] =array('var'=>"Import_Type", 'msg'=>"匯入模式?", 'value'=>$import_ARR);
$SFS_MODULE_SETUP[] =array('var'=>"User_Removable", 'msg'=>"管理者可撤除申請?", 'value'=>$IS_MODULE_ARR);
$SFS_MODULE_SETUP[] =array('var'=>"Barcode_Font", 'msg'=>"條碼字型名稱?", 'value'=>'IDAutomationHC39M');
$SFS_MODULE_SETUP[] =array('var'=>"Footer", 'msg'=>"借用單底部註記?", 'value'=>'借用人：　　　　　　　　管理人：　　　　 　　　　組長：　　　 　　　　主任：　　　　　　　　　');

$SFS_MODULE_SETUP[] =array('var'=>"Table_width", 'msg'=>"清單表格佔據畫面寬度比例(%)", 'value'=>'100');
$SFS_MODULE_SETUP[] =array('var'=>"Tr_BGColor", 'msg'=>"標題列底色", 'value'=>'#C8FFAA');
$SFS_MODULE_SETUP[] =array('var'=>"Lendable_BGColor", 'msg'=>"可借用物品底色", 'value'=>'#FFFFFF');
$SFS_MODULE_SETUP[] =array('var'=>"Requested_BGColor", 'msg'=>"已預借物品底色", 'value'=>'#CCFFCC');
$SFS_MODULE_SETUP[] =array('var'=>"NotReturned_BGColor", 'msg'=>"已借出物品底色", 'value'=>'#AAAAAA');
$SFS_MODULE_SETUP[] =array('var'=>"OverTime_BGColor", 'msg'=>"逾期未歸還物品底色", 'value'=>'#FFAAAA');
$SFS_MODULE_SETUP[] =array('var'=>"Returned_BGColor", 'msg'=>"已歸還物品底色", 'value'=>'#AAAAAA');
$SFS_MODULE_SETUP[] =array('var'=>"Crashed_BGColor", 'msg'=>"已報廢物品底色", 'value'=>'#333333');

$SFS_MODULE_SETUP[] =array('var'=>"SMTP_Server", 'msg'=>"催還郵件寄信主機", 'value'=>'');
$SFS_MODULE_SETUP[] =array('var'=>"SMTP_Port", 'msg'=>"催還郵件寄信主機", 'value'=>'25');
$SFS_MODULE_SETUP[] =array('var'=>"Title", 'msg'=>"催還郵件預設主旨", 'value'=>'來自學校SFS3學務系統的物品借用訊息....');
$SFS_MODULE_SETUP[] =array('var'=>"Content_Head", 'msg'=>"催還郵件內文抬頭敬陳辭", 'value'=>'親愛的 {{borrower}} 君');
$SFS_MODULE_SETUP[] =array('var'=>"Content_Body", 'msg'=>"催還郵件預設主文", 'value'=>'　　下面的物品借用資訊,請您參詳!\r\n若借用物品已過借用期限,敬請惠予儘速辦理歸還手續為荷!\r\n{{content}}');
$SFS_MODULE_SETUP[] =array('var'=>"Content_Foot", 'msg'=>"催還郵件內文結尾敬陳辭", 'value'=>'{{manager}} 謹啟');
$SFS_MODULE_SETUP[] =array('var'=>"Reply", 'msg'=>"催還郵件要求回條", 'value'=>$IS_MODULE_ARR);
$SFS_MODULE_SETUP[] =array('var'=>"Cc_Send", 'msg'=>"催還郵件寄送副本給管理者", 'value'=>$IS_MODULE_ARR);

$SFS_MODULE_SETUP[] =array('var'=>"Cur_BGColor", 'msg'=>"目前顯示的公告底色", 'value'=>'#FFFFFF');
$SFS_MODULE_SETUP[] =array('var'=>"Pre_BGColor", 'msg'=>"預計上架的公告底色", 'value'=>'#FFCCCC');
$SFS_MODULE_SETUP[] =array('var'=>"Aft_BGColor", 'msg'=>"過期的公告底色", 'value'=>'#CCCCCC');
$SFS_MODULE_SETUP[] =array('var'=>"Over_Days", 'msg'=>"逾期未歸公告日數", 'value'=>'30');
$SFS_MODULE_SETUP[] =array('var'=>"Over_Title", 'msg'=>"逾期未歸公告主旨", 'value'=>'[私人訊息]您有借用物品逾期未歸還, 請儘速向物品管理者辦理歸還手續!!');
$SFS_MODULE_SETUP[] =array('var'=>"Over_Content", 'msg'=>"逾期未歸公告內文", 'value'=>'相關資訊請至學校的SFS學務系統查詢!!');

$SFS_MODULE_SETUP[] =array('var'=>"Cols", 'msg'=>"移交物品類別顯示欄數", 'value'=>'5');

$SFS_MODULE_SETUP[] =array('var'=>"Label_Cols", 'msg'=>"物品條碼欄數", 'value'=>'3');
$SFS_MODULE_SETUP[] =array('var'=>"Pic_Width", 'msg'=>"圖片顯示視窗寬度", 'value'=>'320');
$SFS_MODULE_SETUP[] =array('var'=>"Pic_Height", 'msg'=>"圖片顯示視窗高度", 'value'=>'240');

//$SFS_MODULE_SETUP[] =array('var'=>"Refused_Reason", 'msg'=>"拒絕外借原因選項", 'value'=>'維修中,有逾期未歸,另有他用,期末盤整');
//$SFS_MODULE_SETUP[] =array('var'=>"Refused_Reason", 'msg'=>"外借申請狀態選項", 'value'=>'待核,封包整理中,憑單領取');

$SFS_MODULE_SETUP[] =array('var'=>"remove_sfs3head", 'msg'=>"顯示時移除sfs3標頭標尾?",'value'=>$IS_MODULE_ARR);



?>
