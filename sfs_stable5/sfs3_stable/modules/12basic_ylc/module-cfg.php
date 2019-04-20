<?php
//$Id: $

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

$MODULE_TABLE_NAME[0] = "12basic_ylc";
$MODULE_TABLE_NAME[1] = "12basic_kind_ylc";

//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "12年國教雲林區免試入學";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
$MODULE_UPDATE_VER="1.2.1";

// 模組最後更新日期
$MODULE_UPDATE="2017-11-20";


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

//目錄內程式
//$MENU_P = array("readme.php"=>"使用說明","student_list.php"=>"參與免試學生","kind_mirror.php"=>"身分對應設定","student_kind.php"=>"報名資料","aspiration.php"=>"志願序","disadvantage.php"=>"扶助弱勢","nearby.php"=>"就近入學","morality.php"=>"品德服務","diversification.php"=>"多元學習","exam.php"=>"教育會考",'transcript.php'=>'成績證明單',"output.php"=>"招生報名電子檔輸出");
//$MENU_P = array("readme.php"=>"使用說明","student_list.php"=>"參與免試學生","kind_mirror.php"=>"身分對應設定","student_kind.php"=>"報名資料","disadvantage.php"=>"扶助弱勢","nearby.php"=>"就近入學","morality.php"=>"品德服務","diversification.php"=>"多元學習",'sealed.php'=>"資料封存",'transcript.php'=>'成績證明單','transcript_chk.php'=>'積分審查表',"output.php"=>"招生報名電子檔輸出","output_tmd.php"=>"試探系統電子檔輸出");

$MENU_P = array("readme.php"=>"使用說明","student_list.php"=>"參與免試學生","kind_mirror.php"=>"身分對應設定","student_kind.php"=>"報名資料","disadvantage.php"=>"扶助弱勢","nearby.php"=>"就近入學","morality.php"=>"品德服務","diversification.php"=>"多元學習",'sealed.php'=>"資料封存",'transcript.php'=>'成績證明單',"output.php"=>"招生報名電子檔輸出","output_tmd.php"=>"試探系統電子檔輸出");


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

//認定未知、目前採線上修改  $SFS_MODULE_SETUP[]=array('var'=>"remote_school", 'msg'=>"符合偏遠小校", 'value'=>array(0=>"不符",2=>"9班以下",1=>"13班以下"));
$school_nature_array = array("5"=>"符合就近入學學校","0"=>"不符就近入學學校");
$SFS_MODULE_SETUP[0]=array('var'=>"school_nature", 'msg'=>"本校符合就近入學", 'value'=>$school_nature_array);
$school_remote_array = array("0"=>"不符合偏遠小校","2"=>"符合偏遠小校(7班以下)","1"=>"符合偏遠小校(8-12班)");
$SFS_MODULE_SETUP[1]=array('var'=>"school_remote", 'msg'=>"本校符合偏遠小校", 'value'=>$school_remote_array);
$SFS_MODULE_SETUP[2]=array('var'=>"moral_editable", 'msg'=>"允許直接修改品德服務級分", 'value'=>array(1=>"是",0=>"否"));
$SFS_MODULE_SETUP[3]=array('var'=>"diversification_editable", 'msg'=>"允許直接修改多元學習級分", 'value'=>array(1=>"是",0=>"否"));
$SFS_MODULE_SETUP[4]=array('var'=>"exam_editable", 'msg'=>"允許直接修改教育會考級分", 'value'=>array(1=>"是",0=>"否"));
$SFS_MODULE_SETUP[5] =array('var'=>"pic_checked", 'msg'=>"顯示學生大頭照", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[6] =array('var'=>"pic_width", 'msg'=>"大頭照顯示的寬度", 'value'=>'60');
//$SFS_MODULE_SETUP[]=array('var'=>"aspiration_separateor", 'msg'=>"志願列表方式", 'value'=>array("<br>"=>"分項條列"," "=>"以空白分隔"));
$SFS_MODULE_SETUP[]=array('var'=>"kind_evaluate", 'msg'=>"身分類別權值<br>一般生,原住民,原住民(語言認證),身心障礙,其他,境外優秀科技人才子女,政府派赴國外工作人員子女,僑生,蒙藏生", 'value'=>'0,10,20,30,40,50,60,70,80');
$SFS_MODULE_SETUP[]=array('var'=>"native_id", 'msg'=>"原住民代號", 'value'=>9);
$SFS_MODULE_SETUP[]=array('var'=>"native_language_sort", 'msg'=>"族語認證屬性記載順位", 'value'=>3);
$SFS_MODULE_SETUP[]=array('var'=>"native_language_text", 'msg'=>"通過族語認證標記文字", 'value'=>'是');
$SFS_MODULE_SETUP[]=array('var'=>"full_personal_profile", 'msg'=>"報名檔輸出完整個資", 'value'=>array(1=>"是",0=>"否"));
$SFS_MODULE_SETUP[]=array('var'=>"uneditable_bgcolor", 'msg'=>"已封存記錄列顏色", 'value'=>'#ffffcc');
$SFS_MODULE_SETUP[]=array('var'=>"full_sealed_check", 'msg'=>"全數封存才允許輸出", 'value'=>array(0=>"否",1=>"是"));

?>
