<?php
	if (!session_id()) session_start();
// $Id: sfs_API.php 9189 2017-12-26 02:23:55Z brucelyc $
// 本檔為核心函式庫上層檔，
// 若非必要，請由相關核心函式庫去新增修改維護。

	// 時間相關
	require_once( "sfs_core_time.php" );
	// 系統界面/themes
	require_once( "sfs_core_html.php" );
	// 認證函式
	require_once( "sfs_core_auth.php" );
	// 模組相關
	require_once( "sfs_core_module.php" );
	// 取得學校內相關資料
	require_once( "sfs_core_schooldata.php" );
	// 系統選項文字
	require_once( "sfs_core_systext.php" );
	// 取得路徑函數
	require_once( "sfs_core_path.php" );
	// 系統選單相關
	require_once( "sfs_core_menu.php" );
	// 記錄檔相關
	require_once( "sfs_core_log.php" );
	// 訊息相關
	require_once( "sfs_core_msg.php" );
	// sql 資料庫相關
	require_once( "sfs_core_sql.php" );
	// 版本資訊 相關
	require_once( "sfs_core_version.php" );

	//https設定
	$temp_file=$UPLOAD_PATH."system/ssl_setup";
	if (is_file($temp_file)) {
		$fp=fopen($temp_file,"r");
		while(!feof($fp)) {
			$temp_str=fgets($fp,1024);
		}
		fclose($fp);
		$temp_arr=explode("HTTPS=",$temp_str);
		if(count($temp_arr)==2) {
			if(trim($temp_arr[1])<>"") {
				$HTTPS="https://".trim($temp_arr[1]);
				//如果是login頁面
				$temp_arr=explode("/",$_SERVER['REQUEST_URI']);
				if ($temp_arr[count($temp_arr)-1]=="login.php") $SFS_PATH_HTML=$HTTPS;
				//如果有登入
				if ($_SESSION[session_log_id]<>"") {
					if ($temp_arr[count($temp_arr)-1]<>"login.php?logout=yes") $SFS_PATH_HTML=$HTTPS; //除登出頁面外一律強制使用https
					if ($_SERVER['SERVER_PORT']==80 && $SFS_PATH_HTML==$HTTPS) header("Location: ".$HTTPS); //如果已登入卻自行切換到http, 強制回https
				}
			}
		}
	}

	$captcha = chk_login_img($_SESSION["Login_img"], $_POST["log_pass_chk"], 2);
	$_SESSION['CAPTCHA'] = $captcha;

	// 系統環境變數
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	$THEME_FILE = "$SFS_PATH/themes/$SFS_THEME/$SFS_THEME";
	$THEME_URL = "$SFS_PATH_HTML"."themes/$SFS_THEME";
	$SFS_BIGIN_TIME = microtime(); //系統開始時間

	//登入ip
	if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$REMOTE_ADDR=$_SERVER["HTTP_CLIENT_IP"];
	} elseif (!empty($_SERVER["REMOTE_ADDR"])) {
		$REMOTE_ADDR=$_SERVER["REMOTE_ADDR"];
	} else {
		$temp_ip=split(",",$_SERVER["HTTP_X_FORWARDED_FOR"]);
		$REMOTE_ADDR=$temp_ip[0];
	}
/*
	} elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		//有經過代理伺服器
		$temp_ip=split(",",$_SERVER["HTTP_X_FORWARDED_FOR"]);
		$REMOTE_ADDR=$temp_ip[0];
	} else {
		$REMOTE_ADDR=$_SERVER["REMOTE_ADDR"];
	}
*/

	// 定義錯誤型態
	define("FATAL", E_USER_ERROR);
	define("ERROR", E_USER_WARNING);
	define("WARNING", E_USER_NOTICE);

	// set the error reporting level for this script
	error_reporting(FATAL | ERROR | WARNING);

	// 載入界面模組
	sfs_themes();

	//時間設定及函數
	set_now_niceDate();

	// 檢查 register_globals 是否打開
	//check_phpini_register_globals();

	//設定套件目錄
	$INCLUDE_PATH=$SFS_PATH."/include/";
	$PEAR_PATH=$SFS_PATH."/include/";
	$OLE_PATH=$SFS_PATH."/OLE/";
	$SPREADSHEET_PATH=$SFS_PATH."/Spreadsheet/";

	//設定smarty物件
	require_once ("libs/Smarty.class.php");
	$smarty = new Smarty;
	$smarty->compile_check = true;
	$smarty->debugging = false;
	set_upload_path("templates_c");
	$smarty->compile_dir=$UPLOAD_PATH."templates_c";

	//定義smarty使用的tag
	$smarty->left_delimiter="{{";
	$smarty->right_delimiter="}}";

	//定時清除暫存檔
	$temp_file=$UPLOAD_PATH."system/clean_templates_c";
	$now_date=date("Y-m-d");
	if (is_file($temp_file)) {
		$fp=fopen($temp_file,"r");
		$pre_date=date("Y-m-d",strtotime(fgets($fp,10)));
		fclose($fp);
	}
	if ($pre_date!=$now_date) {
		$smarty->clear_compiled_tpl();
		$fp=fopen($temp_file,"w");
		fputs($fp,$now_date);
		fclose($fp);
	}

	//目前的程式名
	$scripts=explode("/",$_SERVER['SCRIPT_NAME']);
	$smarty->assign("CURR_SCRIPT",array_pop($scripts));
	$smarty->assign("SFS_PATH",$SFS_PATH);
	$smarty->assign('SFS_PATH_HTML',$SFS_PATH_HTML);

	//判斷register_global
	if (ini_get('register_globals')) {
		echo "<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='".$SFS_PATH_HTML."images/warn.png' align='middle' border=0>系統設定必須變更</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'>貴校的系統因<font color=red>「php.ini中的register_global設定為On」</font>，可能導致資料的危險，請立即通知系統管理員處理。<br></td></tr><tr><td align=center><br></td></tr></table>";
		exit;
	}

	//佈景相關設定
	$temp_file=$UPLOAD_PATH."system/theme";
	if (is_file($temp_file)) {
		$fp=fopen($temp_file,"r");
		while(!feof($fp)) {
			$temp_str=fgets($fp,50);
			$temp_arr=explode("=",$temp_str);
			if (!empty($temp_arr[0])) $temp[strtoupper($temp_arr[0])]=$temp_arr[1];
		}
		$temp_arr=array();
		fclose($fp);
	}
	$FOLDER="fc.gif";
	$FOLDER_OPEN="fo.gif";
	if ($temp["FOLDER"]) {
		$FOLDER="folder_".$temp["FOLDER"].".png";
		$FOLDER_OPEN="folder_".$temp["FOLDER"]."_open.png";
		$THEME_COLOR=$temp["ICON"];
	}

	//自然人憑證設定
	$temp_file=$UPLOAD_PATH.$PREFIX_PATH."system/cdc";
	if (is_file($temp_file)) {
		$fp=fopen($temp_file,"r");
		while(!feof($fp)) {
			$temp_str=fgets($fp,50);
		}
		if (trim($temp_str)=="ON") $CDCLOGIN=1;
		fclose($fp);
	}
	
	//取得檔案上傳暫存目錄
	$tmp_path=ini_get("upload_tmp_dir");
	if (empty($tmp_path)) {
		$tmp_path=$_ENV["TMP"];
	}
	if (empty($tmp_path)) {
		$tmp_path="/tmp";
	}
?>
