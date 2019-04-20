<?php

// $Id: install.php 8466 2015-07-20 16:05:21Z smallduh $
/*
// 檢查 php.ini
if (ini_get('register_globals')) {
  	echo "您好！目前 SFS 不需要打開全域變數設定，但您的 php.ini 中有打開變數全域設定，請設妥 register_globals=Off，並重新啟動 Apache！"; exit;
}
*/
$cfg_file="./include/config.php";
$SFS_PATH = dirname(__FILE__);

//學務管理首頁程式 URL (設定時，保留最後的 "/" )
//$SFS_PATH_HTML ="http://localhost/sfs3/"; 


//動態設定php 執行時間，避免 timeout
set_time_limit(180) ;

// 檢查 config.php 是否可讀且可寫入?
if(!chk_permit($cfg_file)){
	if (!is_file($cfg_file)){
		$memo = "找不到 $cfg_file 的檔案!";
		$memo2 = "建立一個 config.php 的空白檔案";
	}
	else 
		$memo = "$cfg_file 無讀寫權!";
  	$main="
	<table width='90%' align='center' cellspacing='1' cellpadding='10' bgcolor='red'><tr bgcolor='white'><td>
	<h2 align='center'>$memo </h2>
	原因：
	<p>安裝過程中的一些設定值會寫入 $cfg_file ，若是 $cfg_file 無讀寫權，那麼參數無法寫入，系統便無法正常運作，所以，請修改 $cfg_file 的屬性。</p>
	方法：
	<p>請在 include 目錄下$memo2 ， 執行 chmod 666 config.php，使 config.php 具讀寫權！</p>
	<p>當然也可以用FTP軟體直接修改權限屬性成666，也很方便！</p>
	</td></tr></table>
	";
}elseif($_POST['installsfs']=='yes_do_it_now') {
	//開設資料庫
	install_sfs_db($_POST['mysql_host'], $_POST['mysql_adm_user'], $_POST['mysql_adm_pass'],$_POST['mysql_db'],$_POST['mysql_user'],$_POST['mysql_pass']);
	// 將設定寫入 /include/config.php 中
	write_config();
	header("Location: {$_SERVER['SCRIPT_NAME']}?act=sfs_result&ud={$_POST['UPLOAD_PATH']}&uu={$_POST['UPLOAD_URL']}&sfsurl={$_POST['SFS_PATH_HTML']}");
}elseif($_GET['act']=="sfs_result"){
	$main=sfs_result($_GET['ud'],$_GET['uu'],$_GET['sfsurl']);
}else{
	require "./include/sfs_case_installform.php";
}

?>

<html>
<head>
<title>校務行政系統快速安裝</title>
<meta http-equiv='Content-Type' content='text/html; charset=Big5'>
</head>
    <style type='text/css'>
    body,td{font-size: 12px}
    .small{font-size: 12px}
    </style>
<body background='images/bg.png'>
<?php echo $main;?>
</body>
</html>


<?php


/*  函式區*/


//檢查設定能否寫入
function chk_permit($file) {
	// 若無讀取和寫入權則秀出提示訊息!
	if (!(is_readable($file) && is_writeable($file))) {
		return false;
  	}
	return true;
}




//自動安裝資料庫的函式，包括建立資料庫、資料表、設定權限...等三個動作
function install_sfs_db($host, $adm_user, $adm_pass,$db,$user,$passwd){
	$link = @mysql_connect($host, $adm_user, $adm_pass)
	or die("無法連接資料庫 $db ,
	<p>請確定以下資料是否正確：</p>
	<p>資料庫位置：$host</p>
	<p>管理者帳號：$adm_user</p>
	<p>管理者密碼：$adm_pass</p>
	請回上頁修改！");

	if(mysql_select_db($db)){
		die("$db 已存在。您可以把 $db 資料庫移除，或是將「資料庫名稱」改成不同於 $db 的名稱，例如：「".$db.date("_md")."」，即可繼續安裝。");
	}else{

		$sql="CREATE DATABASE $db";

		// 相容於 MySQL 4.1.X
		if (mysql_query($sql, $link)) {

			$str="grant all privileges on $db.* to $user@$host identified by '$passwd'";

			require "./include/sfs_case_sql.php";

			$sql_file="./db/sfs3.sql";

			$sql_query = fread(fopen($sql_file, 'r'), filesize($sql_file));

			run_sql($sql_query, $db);

			//mysql_db_query($db,$str,$link) or user_error("資料庫 $db 無法建立！請回上頁修改！",256);
			mysql_query($str,$link) or user_error("資料庫 $db 無法建立！請回上頁修改！",256);
			
			$str="grant create  on $db.* to sfs3addman@$host identified by '$passwd'";

			//mysql_db_query ($db,$str,$link) or user_error("無法建立用來匯入資料表的使用者權限！<br>$str",256);
			mysql_query ($str,$link) or user_error("無法建立用來匯入資料表的使用者權限！<br>$str",256);

			// 更新權限，以免 MySQL 4.1.X 版無法讓新增使用者連線
			mysql_query ("FLUSH PRIVILEGES", $link) or user_error("無法更新使用者權限！請手動重新啟動 MySQL",256);

		} else {
			trigger_error("資料庫 $db 無法建立！請回上頁修改！", E_USER_ERROR);
		}
	}

	/* Closing connection */
	mysql_close($link);
}



//安裝結果
function sfs_result($ud,$uu,$sfsurl){
	$msg="
	<div style='color:white ;font-size: 30px' align='center'>恭喜您！系統應該已經安裝完畢。</div><p></p>

	<table width='90%' align='center' cellspacing='0' cellpadding='10' bgcolor='red'>
	<tr bgcolor='#E2FFB6'><td colspan=3>恭喜您！系統應該已經安裝完畢。接著，您必須作一些設定才能讓學務系統運作無誤：</td></tr>
	<tr bgcolor='white'><td width='50%'>
	<ol>
	<li>修改include/config.php的權限成唯讀，確保您系統不會被竄改，方法如下：
	在sfs3的根目錄下，鍵入以下指令：
	<p style='color:blue'>chmod <font color='red'>644</font> include/config.php</p></li>
	<li>移除 install.php，方法如下： 
	在sfs3的根目錄下，鍵入以下指令：
	<p style='color:blue'>rm -f install.php</p></li>

	<li>建立上傳目錄「".$ud."」，方法如下：
	<p style='color:blue'>mkdir $ud</p>
	<li>修改上傳目錄「".$ud."」權限為777，方法如下：
	<p style='color:blue'>chmod <font color='red'>777</font> $ud</p>
	</li>
	<li>在apache 在設定檔 httpd.conf 中加入底下資料，（WIN32 在IIS管理員中設定）。注意目錄結尾要有 /<p>
	<font color='darkRed'>
	Alias $uu '$ud'<br>
	&lt;Directory '$ud'&gt;<br>
	Options None<br>
	AllowOverride None<br>
	Order allow,deny<br>
	Allow from all<br>
	&lt;/Directory&gt;
	</font>
	<p>
	<li>之後，請重新啟 apache，方法如下：<br>執行 service httpd restart<br>
	或執行 /etc/rc.d/init.d/httpd restart
	</li>
	</ol>
	</td><td width=10 background='images/line.png'></td><td valign='top' width='50%'>
	<ul>
	<li>您可以先看看<a href='$sfsurl' target='_blank'>新架好的校務行政系統</a>！（會開在新視窗），看完後，請回到這一頁來繼續做設定。</li>
	<p>
	<li>最後，進行相關設定：
	<p>
	<ol>
	<li>請進行<a href='".$sfsurl."modules/school_setup/'  target='_blank'>學校基本設定</a>！這只要設定一次即可。
	<p>
	<li>接著，進行<a href='".$sfsurl."modules/every_year_setup/class_year_setup.php'  target='_blank'>學期初設定</a>！這是每一學期開學前就要設定好的喔！<font color='red'>換言之，每一學期都要設定喔！</font>
	<p>
	<li>然後，進行<a href='".$sfsurl."modules/teach_class/teach_list.php'  target='_blank'>教師設定</a>！這樣教師才能使用行政系統！
	<p>
	<li>最後，進行<a href='".$sfsurl."modules/create_data/mstudent2.php'  target='_blank'>匯入學生或教師資料</a>！這樣校務行政系統的資料已經差不多啦！
	</ol>
	<p>
	<li>若需要輸入帳號密碼，請輸入帳號：「<font color='red'>1001</font>」，密碼：「<font color='red'>demo</font>」</li>
	<p>
	<li>建議您，把本頁存起來，日後倘若還要修改才知道要改些甚麼。</li>
	</ul>
	</td></tr></table>
	";
	return $msg;
}


// 將設定寫入 include/config.php 中

function write_config() {
  global $cfg_file;

$cfg1=<<<HERE
<?php
// set to the user defined error handler
session_start();
\$old_error_handler = set_error_handler("error_die");

/**********************************
 系統設定
***********************************/
//程式根目錄 PATH
\$SFS_PATH = "$_POST[SFS_PATH]";

//學務管理首頁程式 URL (設定時，保留最後的 "/" )
\$SFS_PATH_HTML ="$_POST[SFS_PATH_HTML]"; 

//學校首頁 URL
\$HOME_URL ="$_POST[HOME_URL]";

//學校IP 範圍
/*半個c class 設定 (起始IP 與 結束IP 以 - 隔開)
例 array("163.17.169.1-163.17.169.128");

多組IP 範圍設定
例 array("163.17.169","163.17.168.1-163.17.169.128","163.17.40.1");
*/
\$HOME_IP = array("$_POST[HOME_IP]"); // 一個 c class

/**********************************
  MYSQL 連接設定
***********************************/
// mysql 主機
\$mysql_host ="$_POST[mysql_host]";

// mysql 使用者
\$mysql_user ="$_POST[mysql_user]";

// mysql 密碼
\$mysql_pass ="$_POST[mysql_pass]";

// 資料庫名稱
\$mysql_db   ="$_POST[mysql_db]";


/**********************************
  上載檔案設定
***********************************/
//上載檔案放置位置，上載目錄權限需設為 777
\$UPLOAD_PATH = "$_POST[UPLOAD_PATH]";

//別名 (alias)  apache 在設定檔 httpd.conf 中加入  WIN32 在IIS管理員中設定
\$UPLOAD_URL = "$_POST[UPLOAD_URL]";


/**********************************
  程式界面
***********************************/
//程式模版 webmin or treemenu or new
\$SFS_THEME = "new";

//模組顯示欄數
\$nocols = 4 ;

//是否顯示 SFS 的版本資訊
\$SHOW_SFS_VER = 1;

//目前的 SFS 不需要打開 php.ini 的全域變數設定
//請設定 php.ini 中的 register_globals=Off
\$SFS_NEED_REGISTER_GLOBALS = 0;

//是否隱藏快速連結選單(fast_link)
\$SFS_HIDDEN_FAST_LINK=1;

//是否為中心端集中式的SFS版本
\$SFS_IS_CENTER_VER=0;

//是否隱藏模組標題
\$SFS_IS_HIDDEN_TITLE=0;
	
/**********************************
  學籍資料設定
***********************************/
//上學期開始月份
\$SFS_SEME1 = $_POST[SFS_SEME1] ; //八月

//下學期開始月份
\$SFS_SEME2 = $_POST[SFS_SEME2] ; //二月



HERE;


 if ($_POST[SFS_JHORES] == 1) {

$cfg2=<<<HERE2A

// 年段
\$class_year = array("1"=>"一年","2"=>"二年","3"=>"三年","4"=>"四年","5"=>"五年","6"=>"六年","a"=>"幼稚園","b"=>"特教班","c"=>"資源班");

// 班名
\$class_name = array("01"=>"甲","02"=>"乙","03"=>"丙","04"=>"丁","05"=>"戊","06"=>"己","07"=>"庚","08"=>"辛"); 

\$IS_JHORES=0;

HERE2A;

 } elseif ($_POST[SFS_JHORES] == 2) {

$cfg2=<<<HERE2B

// 年段
\$class_year = array("7"=>"一年","8"=>"二年","9"=>"三年","a"=>"補校","b"=>"特教班","c"=>"資源班");

// 班名
\$class_name = array("01"=>"1","02"=>"2","03"=>"3","04"=>"4","05"=>"5","06"=>"6","07"=>"7","08"=>"8","09"=>"9"); 

\$IS_JHORES=6;

HERE2B;

 } elseif ($_POST[SFS_JHORES] == 3) {

$cfg22=<<<HERE2C

// 年段
\$class_year = array("10"=>"一年","11"=>"二年","12"=>"三年","a"=>"補校","b"=>"特教班","c"=>"資源班");

// 班名
\$class_name = array("01"=>"1","02"=>"2","03"=>"3","04"=>"4","05"=>"5","06"=>"6","07"=>"7","08"=>"8","09"=>"9"); 

\$IS_JHORES=9;

HERE2C;

 }


$cfg3=<<<HERE3

/**********************************
其他設定
***********************************/

//教師代號之 流水號開頭預設字串 例: tea (tea0001 ,tea0002 ...)
\$DEFAULT_TEA_ID_TITLE = "tea-";

//教師代號之 流水號預設起始值 例: 00001 (tea00001 ,tea00002 ...)
\$DEFAULT_TEA_ID_NUMS = "0001"; // " " 引號不可拿掉 

//教師登錄預設密碼
\$DEFAULT_LOG_PASS = "demo";

//學生登錄預設密碼
\$DEFAULT_STUD_LOG_PASS = "1111";

//家長登錄預設密碼
\$DEFAULT_FAM_LOG_PASS = "3333";

//上課日數設定
\$weekN = array('一','二','三','四','五');

//----一些名稱選項
\$school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
\$school_kind_color=array(
"#FFE1E1",
"#EBFFE1","#DEFFCD","#D0FFB9","#C3FFA5","#B6FF91","#A8FF7D",
"#FFF7CD","#FFF3B9","#FFF0A5",
"#E1E6FF","#CDD5FF","#B9C5FF");
\$class_name_kind=array("","一、二、三","甲、乙、丙","忠、孝、仁","其他");
\$class_name_kind_1=array("","一","二","三","四","五","六","七","八","九","十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十","二十一","二十二","二十三","二十四","二十五","二十六","二十七","二十八","二十九","三十","三十一","三十二","三十三","三十四","三十五","三十??","三十?C","三十八","三十九","四十","四十一","四十二","四十三","四十四","四十五","四十六","四十七","四十八","四十九","五十");
\$class_name_kind_2=array("","甲","乙","丙","丁","戊","己","庚","辛","壬","癸");
\$class_name_kind_3=array("","忠","孝","仁","愛","信","義","和","平");


//------------底下設定勿改
require "\$SFS_PATH/include/sfs_API.php"; //系統核心函式庫
//重新認證檔案url
\$rlogin = \$SFS_PATH."/rlogin.php";

\$conID = @mysql_connect ("\$mysql_host","\$mysql_user","\$mysql_pass") or trigger_error("資料庫無法連上，或許網路斷線，也或許您的資料庫設定有誤，請檢查資料庫設定並重新啟動資料庫。", E_USER_ERROR);
@mysql_select_db(\$mysql_db,\$conID); 


//ADODB 物件
require_once("\$SFS_PATH/pnadodb/adodb.inc.php"); # load code common to ADODB
require_once("\$SFS_PATH/include/sfs_case_ado.php"); # 函式庫

\$DB_TYPE = 'mysql';
\$CONN = &ADONewConnection(\$DB_TYPE);  # create a connection
\$CONN->Connect(\$mysql_host,\$mysql_user,\$mysql_pass,\$mysql_db);# connect to postgresSQL, agora db

//取得 Mysql 環境變數
if (\$DB_TYPE == 'mysql')
	\$DATA_VAR = get_mysql_var();

//看看路徑表存不存在
if(!file_exists(\$UPLOAD_PATH."Module_Path.txt")){
	Creat_Module_Path();
}

/* 
取得學校基本資料
*/
\$SCHOOL_BASE = get_school_base(\$mysql_db);
\$school_long_name = \$SCHOOL_BASE["sch_cname"];  /* 學校(全銜)名稱 */
\$school_short_name = \$SCHOOL_BASE["sch_cname_s"]; /* 學校名稱 */
\$school_sshort_name = \$SCHOOL_BASE["sch_cname_ss"]; /* 學校簡稱 */ 
\$path = \$SFS_PATH; // 相容於 sfs1.1 設定 
\$path_html = \$SFS_PATH_HTML; // 相容於 sfs1.1 設定 


//成績單欄位格式設定
\$input_kind=array("","text","password","select","textarea","checkbox","radio");

//錯誤訊息格式並死當
function error_die (\$errno, \$errstr, \$errfile, \$errline) {
	global \$HAVE_SHOW_HEADER;
	switch (\$errno) {
		case FATAL:
		case ERROR:
		case WARNING:
		case 256:
		//default:
		\$msg=&error_tbl("執行錯誤","\$errstr<p>程式目前執行位置：\$errfile 的第 \$errline 行</p>");
		if(!\$HAVE_SHOW_HEADER)head();
		echo(\$msg);
		if(!\$HAVE_SHOW_HEADER)foot();
		die();
		break;
	 }
}
?>
HERE3;


# write into config.php

 $hfile=fopen($cfg_file, "w");
 if (!$hfile) { echo "開啟 $cfg_file 錯誤，請檢查 $cfg_file 是否有寫入權?"; exit; }

 fputs($hfile, $cfg1);
 fputs($hfile, $cfg2);
 fputs($hfile, $cfg22);
 fputs($hfile, $cfg3);

 fclose($hfile);

}


?>
