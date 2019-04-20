<?php

//$Id:$
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
$MODULE_TABLE_NAME[0] = "salary";
//$MODULE_TABLE_NAME[1]="xxxx";
//
// 請注意要和 module.sql 中的 table 名稱一致!!!
//---------------------------------------------------
// 資料表名稱定義
//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------

$MODULE_PRO_KIND_NAME = "薪津查詢";

//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2007-09-15";

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
//本模組須區分管理權
$MODULE_MAN = 1 ;

//目錄內程式
//$MENU_P = array(
//"list.php"=>"列表");

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
$IS_MODULE_ARR = array("Y"=>"是",""=>"否");

$SFS_MODULE_SETUP[] =array('var'=>"No", 'msg'=>"No欄位顯示標題", 'value'=>'紀錄編號');
$SFS_MODULE_SETUP[] =array('var'=>"InType", 'msg'=>"InType欄位顯示標題", 'value'=>'入帳類別');
$SFS_MODULE_SETUP[] =array('var'=>"AnnounceDate", 'msg'=>"AnnounceDate欄位顯示標題", 'value'=>'發布日期');
$SFS_MODULE_SETUP[] =array('var'=>"ID", 'msg'=>"ID欄位顯示標題", 'value'=>'身分證字號');
$SFS_MODULE_SETUP[] =array('var'=>"Name", 'msg'=>"Name欄位顯示標題", 'value'=>'姓名');
$SFS_MODULE_SETUP[] =array('var'=>"DutyType", 'msg'=>"DutyType欄位顯示標題", 'value'=>'職等或學歷');
$SFS_MODULE_SETUP[] =array('var'=>"JobType", 'msg'=>"JobType欄位顯示標題", 'value'=>'職別');
$SFS_MODULE_SETUP[] =array('var'=>"JobTitle", 'msg'=>"JobTitle欄位顯示標題", 'value'=>'職稱');
$SFS_MODULE_SETUP[] =array('var'=>"MaxPoint", 'msg'=>"MaxPoint欄位顯示標題", 'value'=>'最高本薪');
$SFS_MODULE_SETUP[] =array('var'=>"MaxExtPoint", 'msg'=>"MaxExtPoint欄位顯示標題", 'value'=>'最高年功薪');
$SFS_MODULE_SETUP[] =array('var'=>"Point", 'msg'=>"Point欄位顯示標題", 'value'=>'薪額');
$SFS_MODULE_SETUP[] =array('var'=>"Thirty", 'msg'=>"Thirty欄位顯示標題", 'value'=>'服務滿30年');
$SFS_MODULE_SETUP[] =array('var'=>"ClassTMFactor", 'msg'=>"ClassTMFactor欄位顯示標題", 'value'=>'導師費係數');
$SFS_MODULE_SETUP[] =array('var'=>"Insurance1Factor", 'msg'=>"Insurance1Factor欄位顯示標題", 'value'=>'公保係數');
$SFS_MODULE_SETUP[] =array('var'=>"Insurance2Factor", 'msg'=>"Insurance2Factor欄位顯示標題", 'value'=>'勞保係數');
$SFS_MODULE_SETUP[] =array('var'=>"Insurance3Factor", 'msg'=>"Insurance3Factor欄位顯示標題", 'value'=>'健保係數');
$SFS_MODULE_SETUP[] =array('var'=>"InsureAmount", 'msg'=>"InsureAmount欄位顯示標題", 'value'=>'投保額');
$SFS_MODULE_SETUP[] =array('var'=>"InsuranceLevel", 'msg'=>"InsuranceLevel欄位顯示標題", 'value'=>'投保級數');
$SFS_MODULE_SETUP[] =array('var'=>"Family", 'msg'=>"Family欄位顯示標題", 'value'=>'眷保係數');
$SFS_MODULE_SETUP[] =array('var'=>"Memo", 'msg'=>"Memo欄位顯示標題", 'value'=>'備註');
$SFS_MODULE_SETUP[] =array('var'=>"BankName1", 'msg'=>"BankName1欄位顯示標題", 'value'=>'薪津入帳金融機構');
$SFS_MODULE_SETUP[] =array('var'=>"AccountID1", 'msg'=>"AccountID1欄位顯示標題", 'value'=>'薪津入帳帳戶號碼');
$SFS_MODULE_SETUP[] =array('var'=>"BankName2", 'msg'=>"BankName2欄位顯示標題", 'value'=>'優存入帳金融機構');
$SFS_MODULE_SETUP[] =array('var'=>"AccountID2", 'msg'=>"AccountID2欄位顯示標題", 'value'=>'優存入帳帳戶號碼');
$SFS_MODULE_SETUP[] =array('var'=>"BankName3", 'msg'=>"BankName3欄位顯示標題", 'value'=>'優存入帳金融機構');
$SFS_MODULE_SETUP[] =array('var'=>"AccountID3", 'msg'=>"AccountID3欄位顯示標題", 'value'=>'優存入帳帳戶號碼');

$SFS_MODULE_SETUP[] =array('var'=>"Mg1", 'msg'=>"Mg1欄位顯示標題", 'value'=>'薪俸');
$SFS_MODULE_SETUP[] =array('var'=>"Mg2", 'msg'=>"Mg2欄位顯示標題", 'value'=>'研究費');
$SFS_MODULE_SETUP[] =array('var'=>"Mg3", 'msg'=>"Mg3欄位顯示標題", 'value'=>'職務加給');
$SFS_MODULE_SETUP[] =array('var'=>"Mg4", 'msg'=>"Mg4欄位顯示標題", 'value'=>'專業加給');
$SFS_MODULE_SETUP[] =array('var'=>"Mg5", 'msg'=>"Mg5欄位顯示標題", 'value'=>'特教津貼');
$SFS_MODULE_SETUP[] =array('var'=>"Mg6", 'msg'=>"Mg6欄位顯示標題", 'value'=>'導師費');
$SFS_MODULE_SETUP[] =array('var'=>"Mg7", 'msg'=>"Mg7欄位顯示標題", 'value'=>'業務入帳');
$SFS_MODULE_SETUP[] =array('var'=>"Mg8", 'msg'=>"Mg8欄位顯示標題", 'value'=>'退補扣');
$SFS_MODULE_SETUP[] =array('var'=>"Mg9", 'msg'=>"Mg9欄位顯示標題", 'value'=>'');

$SFS_MODULE_SETUP[] =array('var'=>"Mh1", 'msg'=>"Mh1欄位顯示標題", 'value'=>'退撫自付');
$SFS_MODULE_SETUP[] =array('var'=>"Mh2", 'msg'=>"Mh2欄位顯示標題", 'value'=>'公保自付');
$SFS_MODULE_SETUP[] =array('var'=>"Mh3", 'msg'=>"Mh3欄位顯示標題", 'value'=>'勞保自付');
$SFS_MODULE_SETUP[] =array('var'=>"Mh4", 'msg'=>"Mh4欄位顯示標題", 'value'=>'健保自付');
$SFS_MODULE_SETUP[] =array('var'=>"Mh5", 'msg'=>"Mh5欄位顯示標題", 'value'=>'所得扣繳');
$SFS_MODULE_SETUP[] =array('var'=>"Mh6", 'msg'=>"Mh6欄位顯示標題", 'value'=>'');
$SFS_MODULE_SETUP[] =array('var'=>"Mh7", 'msg'=>"Mh7欄位顯示標題", 'value'=>'');
$SFS_MODULE_SETUP[] =array('var'=>"Mh8", 'msg'=>"Mh8欄位顯示標題", 'value'=>'');
$SFS_MODULE_SETUP[] =array('var'=>"Mh9", 'msg'=>"Mh9欄位顯示標題", 'value'=>'');

$SFS_MODULE_SETUP[] =array('var'=>"Mi1", 'msg'=>"Mi1欄位顯示標題", 'value'=>'優惠存款');
$SFS_MODULE_SETUP[] =array('var'=>"Mi2", 'msg'=>"Mi2欄位顯示標題", 'value'=>'餐費');
$SFS_MODULE_SETUP[] =array('var'=>"Mi3", 'msg'=>"Mi3欄位顯示標題", 'value'=>'雜支');
$SFS_MODULE_SETUP[] =array('var'=>"Mi4", 'msg'=>"Mi4欄位顯示標題", 'value'=>'雜支');
$SFS_MODULE_SETUP[] =array('var'=>"Mi5", 'msg'=>"Mi5欄位顯示標題", 'value'=>'雜支');
$SFS_MODULE_SETUP[] =array('var'=>"Mi6", 'msg'=>"Mi6欄位顯示標題", 'value'=>'雜支');
$SFS_MODULE_SETUP[] =array('var'=>"Mi7", 'msg'=>"Mi7欄位顯示標題", 'value'=>'雜支');
$SFS_MODULE_SETUP[] =array('var'=>"Mi8", 'msg'=>"Mi8欄位顯示標題", 'value'=>'雜支');
$SFS_MODULE_SETUP[] =array('var'=>"Mi9", 'msg'=>"Mi9欄位顯示標題", 'value'=>'雜支');

$SFS_MODULE_SETUP[] =array('var'=>"BasisData1", 'msg'=>"左側基本資料顯示項目", 'value'=>'ID,Name,DutyType,JobType,JobTitle,MaxPoint,MaxExtPoint,Point,Thirty,ClassTMFactor');
$SFS_MODULE_SETUP[] =array('var'=>"BasisData2", 'msg'=>"右側基本資料顯示項目", 'value'=>'Insurance1Factor,Insurance2Factor,Insurance3Factor,InsureAmount,InsuranceLevel,Family,BankName1,AccountID1,BankName2,AccountID2');
$SFS_MODULE_SETUP[] =array('var'=>"Mg", 'msg'=>"應給顯示項目", 'value'=>'1,2,3,4,5,6,7,8');
$SFS_MODULE_SETUP[] =array('var'=>"Mh", 'msg'=>"代收顯示項目", 'value'=>'1,2,3,4,5');
$SFS_MODULE_SETUP[] =array('var'=>"Mi", 'msg'=>"代付顯示項目", 'value'=>'1,2,3,4,5');

$SFS_MODULE_SETUP[] =array('var'=>"Title", 'msg'=>"顯示抬頭", 'value'=>'薪津印領細目清單');
$SFS_MODULE_SETUP[] =array('var'=>"BasisData_caption", 'msg'=>"基本資料標題", 'value'=>'個人基本資料');
$SFS_MODULE_SETUP[] =array('var'=>"Mg_caption", 'msg'=>"應給項目標題", 'value'=>'應給項目');
$SFS_MODULE_SETUP[] =array('var'=>"Mh_caption", 'msg'=>"代收項目標題", 'value'=>'代收項目');
$SFS_MODULE_SETUP[] =array('var'=>"Mi_caption", 'msg'=>"代付項目標題", 'value'=>'代付項目');

$SFS_MODULE_SETUP[] =array('var'=>"Table_width", 'msg'=>"清單表格佔據畫面寬度比例(%)", 'value'=>'80');
$SFS_MODULE_SETUP[] =array('var'=>"Tr_BGColor", 'msg'=>"標題列底色", 'value'=>'#FFC8AA');

?>
