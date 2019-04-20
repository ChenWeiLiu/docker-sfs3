<?php

// $Id: module-cfg.php 8638 2015-12-15 15:36:01Z qfon $

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

$MODULE_TABLE_NAME[0] = "";

//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "學生資料自建";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0.0";

// 模組最後更新日期
$MODULE_UPDATE="2013/01/14";


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


// 待填


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
// $ret_array =& get_module_setup("stud_eduh")
//
//
// 詳情請參考 include/sfs_core_module.php 中的說明。
//
// 這區的 "變數名稱 $SFS_MODULE_SETUP" 請勿改變!!!
//---------------------------------------------------

//$SFS_MODULE_SETUP[] =	array('var'=>'ha_checkary', 'msg'=>'啟用健保卡驗證功能', 'value'=>array('1'=>'是','0'=>'否'));
$SFS_MODULE_SETUP[] =   array('var'=>'ha_checkary', 'msg'=>'健保卡驗證功能', 'value'=>array('1'=>'校外使用需要','2'=>'校內外使用均需要','0'=>'不啟用'));
$SFS_MODULE_SETUP[] =	array('var'=>'base_edit', 'msg'=>'開放基本資料讓學生自行編修', 'value'=>array('0'=>'無法顯示','-1'=>'僅可瀏覽','1'=>'允許修改'));
$SFS_MODULE_SETUP[] =	array('var'=>'dom_edit', 'msg'=>'開放戶籍資料讓學生自行編修', 'value'=>array('0'=>'否','1'=>'是'));
$SFS_MODULE_SETUP[] =	array('var'=>'club_enable', 'msg'=>'啟用社團活動學生模組', 'value'=>array('0'=>'否','1'=>'是'));
$SFS_MODULE_SETUP[] =	array('var'=>'service_enable', 'msg'=>'啟用服務學習學生模組', 'value'=>array('0'=>'否','1'=>'是'));
$SFS_MODULE_SETUP[] =	array('var'=>"feedback_deadline", 'msg'=>"填寫服務學習自我省思期限?（幾日內必須填寫完畢）", 'value'=>"60");
$SFS_MODULE_SETUP[] =	array('var'=>'career_contact', 'msg'=>'啟用生涯輔導-導師及輔導教師填寫功能', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'contact_months', 'msg'=>'導師及輔導教師填寫月份', 'value'=>'09,10,02,03');
$SFS_MODULE_SETUP[] =	array('var'=>'mystory', 'msg'=>'啟用生涯輔導-我的成長故事填寫功能', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'mystory_months', 'msg'=>'我的成長故事填寫月份', 'value'=>'09');
$SFS_MODULE_SETUP[] =	array('var'=>'psy_test', 'msg'=>'啟用檢視生涯輔導-各項心理測驗功能', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'psy_test_months', 'msg'=>'各項心理測驗填寫月份', 'value'=>'');
$SFS_MODULE_SETUP[] =	array('var'=>'study_spe', 'msg'=>'啟用生涯輔導-學習成果及特殊表現填寫功能', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'cadre_result', 'msg'=>'學生可填寫幹部資料', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'race_result', 'msg'=>'學生可填寫競賽成果資料', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'study_spe_months', 'msg'=>'學習成果及特殊表現填寫月份', 'value'=>'09,03');
	$SFS_MODULE_SETUP[] =	array('var'=>'explore_exclude', 'msg'=>'生涯試探活動紀錄不受月份限制', 'value'=>array('0'=>'否','1'=>'是'));
$SFS_MODULE_SETUP[] =	array('var'=>'career_view', 'msg'=>'啟用生涯輔導-生涯統整面面觀填寫功能', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'view_months', 'msg'=>'生涯統整面面觀填寫月份', 'value'=>'03');
$SFS_MODULE_SETUP[] =	array('var'=>'career_evaluate', 'msg'=>'啟用生涯輔導-生涯發展規劃書填寫功能', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'evaluate_months', 'msg'=>'生涯發展規劃書填寫月份', 'value'=>'03,04,05');
$SFS_MODULE_SETUP[] =	array('var'=>'career_guidance', 'msg'=>'啟用生涯輔導-諮詢紀錄填寫功能', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'guidance_months', 'msg'=>'諮詢紀錄填寫填寫月份', 'value'=>'09,03,04,05');
$SFS_MODULE_SETUP[] =	array('var'=>'gmap_location', 'msg'=>'Google地圖訂位中心經緯度座標', 'value'=>'24.345415,120.587642');
$SFS_MODULE_SETUP[] =	array('var'=>'gmap_zoom', 'msg'=>'Google地圖顯示遠近大小', 'value'=>10);
$SFS_MODULE_SETUP[] =	array('var'=>'career_previous', 'msg'=>'啟用生涯輔導紀錄填寫過往學期資料功能', 'value'=>array('0'=>'否','1'=>'是'));
$SFS_MODULE_SETUP[] =	array('var'=>'stud_eduh_editable', 'msg'=>'啟用學生填寫輔導紀錄表功能', 'value'=>array('0'=>'否','1'=>'是'));
	$SFS_MODULE_SETUP[] =	array('var'=>'eduh_months', 'msg'=>'輔導紀錄表填寫月份', 'value'=>'09,10,03,04');
$SFS_MODULE_SETUP[] =	array('var'=>'password_changed', 'msg'=>'允許學生自行修改密碼', 'value'=>array('0'=>'否','1'=>'是'));	
$SFS_MODULE_SETUP[] =	array('var'=>'stage_score', 'msg'=>'允許學生檢視階段成績', 'value'=>array('0'=>'否','1'=>'是'));
$SFS_MODULE_SETUP[] =	array('var'=>'stage_teacher', 'msg'=>'階段成績顯示繕打成績教師', 'value'=>array('0'=>'否','1'=>'是'));
$SFS_MODULE_SETUP[] =	array('var'=>'stud_view_self_absent', 'msg'=>'允許學生查詢缺曠課', 'value'=>array('0'=>'否','1'=>'是'));



// 第2,3,4....個，依此類推： 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
