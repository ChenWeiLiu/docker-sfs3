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
// 您打算把此一模組放在那一個系統區塊中呢?
//
// 目前僅有二區供您選擇
//
// "校務行政" 模組區塊代碼：28
// "工具箱"  模組區塊代碼：161
//---------------------------------------------------

// 您這個模組的名稱，就是您這個模組放置在 SFS 中的目錄名稱

$MODULE_NAME = "jarticle";

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

$MODULE_TABLE_NAME[0] = "jboard_kind";
$MODULE_TABLE_NAME[1] = "jboard_p";
$MODULE_TABLE_NAME[2] = "jboard_check";
$MODULE_TABLE_NAME[3] = "jboard_files";
$MODULE_TABLE_NAME[4] = "jboard_images";

//
// 3.這裡定義：模組中文名稱，請精簡命名 (供 "模組安裝管理" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "joomla!文章編輯";


//---------------------------------------------------
//
// 4. 這裡定義：模組版本相關資訊 (供 "相關系統程式" 取用)
//
//---------------------------------------------------

// 模組版本
$MODULE_VER="1.0.0";

// 模組程式作者
$MODULE_AUTHOR="hami";

// 模組版權種類
$MODULE_LICENSE="";

// 模組外顯名稱(供 "模組設定" 程式使用)
$MODULE_DISPLAY_NAME="joomla!文章編輯";

// 模組隸屬群組
$MODULE_GROUP_NAME="校務行政";

// 模組開始日期
$MODULE_CREATE_DATE="2013-11-27";

// 模組最後更新日期
$MODULE_UPDATE="2013-11-27 11:00:00";

// 模組更新者
$MODULE_UPDATE_MAN="smallduh";


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
	array('var'=>"api_key", 'msg'=>"joomla元件與本模組連線的密碼", 'value'=>"publicKey");
$SFS_MODULE_SETUP[] =
	array('var'=>"display_limit", 'msg'=>"公告日期限定", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[] =
	array('var'=>"page_count", 'msg'=>"每頁顯示筆數", 'value'=>15);
$SFS_MODULE_SETUP[] =
	array('var'=>"is_standalone", 'msg'=>"是否有獨立的界面", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[] =
        array('var'=>"no_footer", 'msg'=>"獨立界面是否去底部說明", 'value'=>array("0"=>"否","1"=>"是"));
$SFS_MODULE_SETUP[] =
	array('var'=>"insite_ip", 'msg'=>"設定內部IP範圍,留空時使用系統預設值,例163.17.40 或 163.17.40.1-163.17.40.128 ", 'value'=>'');
$SFS_MODULE_SETUP[] =
	array('var'=>"title_img", 'msg'=>"標題圖連結", 'value'=>"images/title.gif");
$SFS_MODULE_SETUP[] =
	array('var'=>"bg_img", 'msg'=>"背景圖連結", 'value'=>"images/backg.gif");
$SFS_MODULE_SETUP[] =
	array('var'=>"table_bg_color", 'msg'=>"表格底色", 'value'=>"#D0FFB9");
$SFS_MODULE_SETUP[] =
	array('var'=>"table_width", 'msg'=>"表格佔用版面比大小", 'value'=>"95%");
$SFS_MODULE_SETUP[] =
	array('var'=>"table_border_width", 'msg'=>"表格框線大小", 'value'=>"1");
$SFS_MODULE_SETUP[] =
	array('var'=>"table_border_color", 'msg'=>"表格框線顏色", 'value'=>"#BBDD89");
$SFS_MODULE_SETUP[] =
	array('var'=>"header_height", 'msg'=>"欄位抬頭列高度(pt)", 'value'=>"25");
$SFS_MODULE_SETUP[] =
	array('var'=>"header_bg_color", 'msg'=>"欄位抬頭底色", 'value'=>"#CCCCFF");
$SFS_MODULE_SETUP[] =
	array('var'=>"header_text_size", 'msg'=>"欄位抬頭文字大小(pt)", 'value'=>"12");
$SFS_MODULE_SETUP[] =
	array('var'=>"header_text_color", 'msg'=>"欄位抬頭文字顏色", 'value'=>"#000099");
$SFS_MODULE_SETUP[] =
	array('var'=>"record_height", 'msg'=>"紀錄列顯示高度(pt)", 'value'=>"25");
$SFS_MODULE_SETUP[] =
	array('var'=>"record_bg_color", 'msg'=>"滑鼠移過紀錄列底色", 'value'=>"#AAFFCC");
$SFS_MODULE_SETUP[] =
	array('var'=>"offset_color", 'msg'=>"奇偶紀錄紀錄列底色差距", 'value'=>"20");
$SFS_MODULE_SETUP[] =
	array('var'=>"record_text_color", 'msg'=>"紀錄列文字顏色", 'value'=>"#000000");
$SFS_MODULE_SETUP[] =
	array('var'=>"font_size", 'msg'=>"紀錄列文字大小(pt)", 'value'=>"12");
$SFS_MODULE_SETUP[] =
	array('var'=>"enable_title", 'msg'=>"顯示職稱欄", 'value'=>array("1"=>"是","0"=>"否"));
$SFS_MODULE_SETUP[] =
	array('var'=>"enable_days", 'msg'=>"顯示公告天數欄", 'value'=>array("1"=>"是","0"=>"否"));
$SFS_MODULE_SETUP[] =
	array('var'=>"enable_point", 'msg'=>"顯示點閱數欄", 'value'=>array("1"=>"是","0"=>"否"));
$SFS_MODULE_SETUP[] =
	array('var'=>"enable_is_sign", 'msg'=>"啟用回簽功能", 'value'=>array("1"=>"是","0"=>"否"));
$SFS_MODULE_SETUP[] =
	array('var'=>"enable_is_html", 'msg'=>"HTML編輯器", 'value'=>array(""=>"不啟用","Basic"=>"基本","Default"=>"完整"));
$SFS_MODULE_SETUP[] =
        array('var'=>"top_item", 'msg'=>"分類選單之引導選項", 'value'=>"近期發佈");
$SFS_MODULE_SETUP[] =
        array('var'=>"bg_color", 'msg'=>"頁面底色", 'value'=>"#CCFFCC");
$SFS_MODULE_SETUP[] =
        array('var'=>"login_force", 'msg'=>"強制須登入才能檢視文章內容", 'value'=>array("0"=>"否","1"=>"是"));

$SFS_MODULE_SETUP[] =
        array('var'=>"marquee_backcolor", 'msg'=>"(公告跑馬燈)--背景色", 'value'=>"yellow");
$SFS_MODULE_SETUP[] =
       array('var'=>"marquee_fontcolor", 'msg'=>"(公告跑馬燈)--字體顏色", 'value'=>"blue");
$SFS_MODULE_SETUP[] =
       array('var'=>"marquee_height", 'msg'=>"(公告跑馬燈)--高度", 'value'=>"25");
$SFS_MODULE_SETUP[] =
        array('var'=>"marquee_behavior", 'msg'=>"(公告跑馬燈)--內容移動方式", 'value'=>"scroll");
$SFS_MODULE_SETUP[] =
        array('var'=>"marquee_direction", 'msg'=>"(公告跑馬燈)--內容移動方向", 'value'=>"left");
$SFS_MODULE_SETUP[] =
        array('var'=>"marquee_scrollamount", 'msg'=>"(公告跑馬燈)--每次內容移動距離", 'value'=>"5");
$SFS_MODULE_SETUP[] =
        array('var'=>"max_marquee_days", 'msg'=>"(公告跑馬燈)--展示最長期限(日)", 'value'=>"30");

?>
