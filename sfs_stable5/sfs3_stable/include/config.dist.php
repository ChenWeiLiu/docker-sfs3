<?php
// set to the user defined error handler
session_start();
$old_error_handler = set_error_handler("error_die");

/**********************************
 系統設定
***********************************/
//程式根目錄 PATH
$SFS_PATH = "/var/www/sfs3";

//學務管理首頁程式 URL (設定時，保留最後的 "/" )
$SFS_PATH_HTML ="http://localhost/sfs3/"; 

//學校首頁 URL
$HOME_URL ="http://localhost/";

//學校IP 範圍
/*半個c class 設定 (起始IP 與 結束IP 以 - 隔開)
例 array("163.17.169.1-163.17.169.128");

多組IP 範圍設定
例 array("163.17.169","163.17.168.1-163.17.169.128","163.17.40.1");
*/
$HOME_IP = array("127.0.0"); // 一個 c class

/**********************************
  MYSQL 連接設定
***********************************/
// mysql 主機
$mysql_host ="localhost";

// mysql 使用者
$mysql_user ="sfs3";

// mysql 密碼
$mysql_pass ="pass";

// 資料庫名稱
$mysql_db   ="sfs3";


/**********************************
  上載檔案設定
***********************************/
//上載檔案放置位置，上載目錄權限需設為 777
$UPLOAD_PATH = "/home/cik/SFS3-STABLE/sfs3/data/";

//別名 (alias)  apache 在設定檔 httpd.conf 中加入  WIN32 在IIS管理員中設定
$UPLOAD_URL = "/sfs3/data/";


/**********************************
  程式界面
***********************************/
//程式模版 webmin or treemenu or new
$SFS_THEME = "new";

//模組顯示欄數
$nocols = 4 ;

//是否顯示 SFS 的版本資訊
$SHOW_SFS_VER = 1;

//目前的 SFS 不需要打開 php.ini 的全域變數設定
//請設定 php.ini 中的 register_globals=Off
$SFS_NEED_REGISTER_GLOBALS = 0;

//是否隱藏快速連結選單(fast_link)
$SFS_HIDDEN_FAST_LINK=1;

//是否為中心端集中式的SFS版本
$SFS_IS_CENTER_VER=0;

//是否隱藏模組標題
$SFS_IS_HIDDEN_TITLE=0;
	
/**********************************
  學籍資料設定
***********************************/
//上學期開始月份
$SFS_SEME1 = 8 ; //八月

//下學期開始月份
$SFS_SEME2 = 2 ; //二月



// 年段
$class_year = array("1"=>"一年","2"=>"二年","3"=>"三年","4"=>"四年","5"=>"五年","6"=>"六年","a"=>"幼稚園","b"=>"特教班","c"=>"資源班");

// 班名
$class_name = array("01"=>"甲","02"=>"乙","03"=>"丙","04"=>"丁","05"=>"戊","06"=>"己","07"=>"庚","08"=>"辛"); 

// 國中 設 6 ,國小設 0
$IS_JHORES=0;

/**********************************
其他設定
***********************************/

//教師代號之 流水號開頭預設字串 例: tea (tea0001 ,tea0002 ...)
$DEFAULT_TEA_ID_TITLE = "tea-";

//教師代號之 流水號預設起始值 例: 00001 (tea00001 ,tea00002 ...)
$DEFAULT_TEA_ID_NUMS = "0001"; // " " 引號不可拿掉 

//教師登錄預設密碼
$DEFAULT_LOG_PASS = "demo";

//學生登錄預設密碼
$DEFAULT_STUD_LOG_PASS = "1111";

//家長登錄預設密碼
$DEFAULT_FAM_LOG_PASS = "3333";

//上課日數設定
$weekN = array('一','二','三','四','五');

//----一些名稱選項
$school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
$school_kind_color=array(
"#FFE1E1",
"#EBFFE1","#DEFFCD","#D0FFB9","#C3FFA5","#B6FF91","#A8FF7D",
"#FFF7CD","#FFF3B9","#FFF0A5",
"#E1E6FF","#CDD5FF","#B9C5FF");
$class_name_kind=array("","一、二、三","甲、乙、丙","忠、孝、仁","其他");
$class_name_kind_1=array("","一","二","三","四","五","六","七","八","九","十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十","二十一","二十二","二十三","二十四","二十五","二十六","二十七","二十八","二十九","三十","三十一","三十二","三十三","三十四","三十五","三十??","三十?C","三十八","三十九","四十","四十一","四十二","四十三","四十四","四十五","四十六","四十七","四十八","四十九","五十");
$class_name_kind_2=array("","甲","乙","丙","丁","戊","己","庚","辛","壬","癸");
$class_name_kind_3=array("","忠","孝","仁","愛","信","義","和","平");


//------------底下設定勿改
require "$SFS_PATH/include/sfs_API.php"; //系統核心函式庫
//重新認證檔案url
$rlogin = $SFS_PATH."/rlogin.php";

$conID = @mysql_connect ("$mysql_host","$mysql_user","$mysql_pass") or trigger_error("資料庫無法連上，或許網路斷線，也或許您的資料庫設定有誤，請檢查資料庫設定並重新啟動資料庫。", E_USER_ERROR);
@mysql_select_db($mysql_db,$conID); 


//ADODB 物件
require_once("$SFS_PATH/pnadodb/adodb.inc.php"); # load code common to ADODB
require_once("$SFS_PATH/include/sfs_case_ado.php"); # 函式庫

$DB_TYPE = 'mysql';
$CONN = &ADONewConnection($DB_TYPE);  # create a connection
$CONN->Connect($mysql_host,$mysql_user,$mysql_pass,$mysql_db);# connect to postgresSQL, agora db

//取得 Mysql 環境變數
if ($DB_TYPE == 'mysql')
	$DATA_VAR = get_mysql_var();

//看看路徑表存不存在
if(!file_exists($UPLOAD_PATH."Module_Path.txt")){
	Creat_Module_Path();
}

/* 
取得學校基本資料
*/
$SCHOOL_BASE = get_school_base($mysql_db);
$school_long_name = $SCHOOL_BASE["sch_cname"];  /* 學校(全銜)名稱 */
$school_short_name = $SCHOOL_BASE["sch_cname_s"]; /* 學校名稱 */
$school_sshort_name = $SCHOOL_BASE["sch_cname_ss"]; /* 學校簡稱 */ 
$path = $SFS_PATH; // 相容於 sfs1.1 設定 
$path_html = $SFS_PATH_HTML; // 相容於 sfs1.1 設定 


//成績單欄位格式設定
$input_kind=array("","text","password","select","textarea","checkbox","radio");

//錯誤訊息格式並死當
function error_die ($errno, $errstr, $errfile, $errline) {
	global $HAVE_SHOW_HEADER;
	switch ($errno) {
		case FATAL:
		case ERROR:
		case WARNING:
		case 256:
		//default:
		$msg=&error_tbl("執行錯誤","$errstr<p>程式目前執行位置：$errfile 的第 $errline 行</p>");
		if(!$HAVE_SHOW_HEADER)head();
		echo($msg);
		if(!$HAVE_SHOW_HEADER)foot();
		die();
		break;
	 }
}
?>
