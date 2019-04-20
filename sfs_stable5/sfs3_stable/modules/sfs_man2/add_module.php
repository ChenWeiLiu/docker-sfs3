<?php

// $Id: add_module.php 5310 2009-01-10 07:57:56Z hami $

//設定檔載入檢查
require "config.php";
// 認證檢查
sfs_check();

//執行動作判斷
if($_POST[act]=="安裝"){
	add_prob();
	header("location: $_SERVER[PHP_SELF]");
}else{
	$main=&main_form();
}


//秀出網頁
head("學務程式設定");
echo $main;
foot();

//主要表格
function &main_form(){
	global $CONN,$SFS_PATH,$school_menu_p,$MODULE_DIR;
	$tool_bar=&make_menu($school_menu_p);
	$select_dir=$_REQUEST['dir'];
	$realdir=$SFS_PATH."/modules/";

	//真實路徑下的模組
	$real_dir=real_dir_array($realdir);

	$sql_select="select dirname from sfs_module where kind='模組'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($dirname)=$recordSet->FetchRow()){
		$db_dir[]=$dirname;
	}


	if(empty($db_dir))$db_dir=array();

	//將資料庫中的目錄和實際路徑來比對一下
	$diff_dir=array_diff($real_dir,$db_dir);

	sort($diff_dir);

	foreach($diff_dir as $d){
		$color=($select_dir==$d)?"#FFFB8A":"white";

		$is_stand_module=is_stand_module($MODULE_DIR,$d);
		$MODULE_PRO_KIND_NAME ='';
		if ($is_stand_module) 
			include_once $MODULE_DIR.$d."/module-cfg.php";
	
		
		$stand_txt=($is_stand_module)?"<font color='#358E1F' class='small'>。</font>":"<font color='red' class='small'>。</font>";


		$diff_dir_txt.="<div style='background:$color'>
		$stand_txt
		<a href='$_SERVER[PHP_SELF]?mode=add&dir=$d'>$d"." [".$MODULE_PRO_KIND_NAME."]</a></div>";
	}

	if(empty($diff_dir_txt)){
		$diff_dir_txt="
		<tr bgcolor='#FFFFFF'>
		<td colspan='4'>所有模組均已安裝，無新模組可供安裝。</td>
		</tr>";
	}
	
	if($_REQUEST[mode]=="add" and !empty($select_dir)){
		$addForm=&addForm($select_dir,$_REQUEST[id_kind]);
	}

	$main="
	$tool_bar
	<table cellspacing='0' cellpadding='0' >
		<tr><td valign='top'>
			<table width='100%'  cellspacing='1' cellpadding='4' bgcolor='#9C4569'>
			<tr bgcolor='#800000'>
			<td><font color='#FFFFFF'>尚未設定的模組</font></td></tr>
			<tr bgcolor='#FFFFFF'><td style='font-size:16px;font-family:Arial;line-height:150%'>$diff_dir_txt</td></tr>
			</table>

		</td><td width='10'></td><td valign='top'>
		$addForm
		</td></tr>
		</table>
	";

	return $main;
}

//新增對話匡
function &addForm($dir,$id_kind){
	global $MODULE_DIR;
	//取得舊模組說明檔
	$log=get_auth_txt($dir);
	
	//找出分類選單
	$get_of_group=get_of_group($_REQUEST[of_group],"of_group",$_POST[of_group],$id_kind,"1");

	//權限設定選單
	$power_set=power_set($id_kind,"id_kind","id_sn","is_admin");

	$is_stand_module=is_stand_module($MODULE_DIR,$dir);
	$stand_txt=($is_stand_module)?"<font color='#358E1F'>標準模組</font>":"<font color='red'>非標準模組</font>";

	if($is_stand_module){
		include $MODULE_DIR.$dir."/module-cfg.php";
		$hidden="
		<input type='hidden' name='ver' value='$MODULE_UPDATE_VER'>
		<input type='hidden' name='author' value='$MODULE_UPDATE_MAN'>
		<input type='hidden' name='creat_date' value='$MODULE_UPDATE'>";
		$log2="
		『標準模組所含資訊』<br>
		最後更新版本：
		$MODULE_UPDATE_VER<br>
		最後更新日期：
		$MODULE_UPDATE<br>
		";
	}

	$showname=(empty($_REQUEST[showname]))?$MODULE_PRO_KIND_NAME:$_REQUEST[showname];

	$islive_checked=($_REQUEST[islive]=="1" and  $_POST[islive]!='')?"checked":"";
	$isopen_checked=($_REQUEST[isopen]=="1" and  $_POST[isopen]!='')?"checked":"";


	$main="
	<table cellspacing='1' cellpadding='4' bgcolor='#C0C0C0'><tr bgcolor='#FFFFFF'><td>
		<form action='$_SERVER[PHP_SELF]' method='post'>
		<input type='hidden' name='dir' value='$dir'>
		<input type='hidden' name='dirname' value='$dir'>
		<input type='hidden' name='mode' value='$_REQUEST[mode]'>
		$hidden
		<table cellspacing='0' cellpadding='4' bgcolor='#FFFFFF' class='small'>
		<tr><td bgcolor='#F1F2E6'>$stand_txt</td><td>
		$dir $author</td></tr>
		<tr><td bgcolor='#F1F2E6'>中文名稱</td><td>
		<input type='text' name='showname' value='$showname'>
		</td></tr>
		<tr><td bgcolor='#F1F2E6'>安裝目錄</td><td>
		$get_of_group</td></tr>
		<tr><td bgcolor='#F1F2E6'>是否開放進入</td><td>
		<input type='checkbox' name='isopen' value='1' $isopen_checked>
		允許一般網友進入瀏覽</td></tr>
		<tr><td bgcolor='#F1F2E6'>安裝後立即啟用</td><td>
		<input type='checkbox' name='islive' value='1' $islive_checked>
		立即啟用</td></tr>
		<tr bgcolor='#FFDFDF'><td>模組授權</td>

		<td>$power_set<input type='submit' name='act' value='安裝'></td></tr>
		<tr bgcolor='#DFEFD8'><td colspan='2'>
		$log
		</td></tr>
		<tr bgcolor='#FFFB8A'><td colspan='2'>
		$log2
		</td></tr>
		</table></form>
	</td></tr></table>
	";
	return $main;
}

//新增儲存模組設定資訊
function add_prob(){
	global $CONN,$MODULE_DIR,$UPLOAD_PATH;
	//取得該分類下最後一個排序數字
	$sort=get_sort($_POST[of_group]);
	if (!$_POST['isopen']) $_POST['isopen'] =0 ;
	$sql_insert = "insert into sfs_module (showname,dirname,sort,isopen,islive,of_group,ver,author,creat_date,kind) values ('$_POST[showname]','$_POST[dirname]','$sort','$_POST[isopen]','$_POST[islive]','$_POST[of_group]','$_POST[ver]','$_POST[author]','$_POST[creat_date]','模組')";
	$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
	$msn=mysql_insert_id();

	//儲存權限部分
	if(!empty($_POST[id_kind])){
		if (!$_POST['is_admin'])  $_POST['is_admin'] = 0 ;
		$str="INSERT INTO pro_check_new (pro_kind_id,id_kind,id_sn,is_admin) VALUES ('$msn','$_POST[id_kind]','$_POST[id_sn]','$_POST[is_admin]')";
		$CONN->Execute($str) or user_error($str, 256);
	}

	//判斷是否為標準模組
	$is_stand_module=is_stand_module($MODULE_DIR,$_POST['dirname']);

	if($is_stand_module){
		// 新增模組選項設定
		get_sfs_module_set($_POST['dirname']);

		// 新增 sfs_text 記錄($SFS_TEXT_SETUP也是在module-cfg中設定)
		if(isset($SFS_TEXT_SETUP) and is_array($SFS_TEXT_SETUP)){
			for ($i=1; $i<=count($SFS_TEXT_SETUP); $i++) {
				$arr=$SFS_TEXT_SETUP[$i-1];
				$pm_g_id = trim($arr['g_id']);
				$pm_item = trim($arr['var']);
				$pm_arr = $arr['s_arr'];
				join_sfs_text($pm_g_id,$pm_item,$pm_arr) or trigger_error("$pm_item, 無法加入選項清單 !", E_USER_ERROR);
			}
		}
	}

	// 若 sql 檔存在，才新增資料表
	$MODULE_SQL_FILE=$MODULE_DIR.$_POST['dirname']."/module.sql";
	if (file_exists($MODULE_SQL_FILE)) install_module_tb ($MODULE_SQL_FILE);

	//重新產生路徑表
	unlink($UPLOAD_PATH."Module_Path.txt");
	Creat_Module_Path();
	
	//重設使用者狀態
	reset_user_state();
	return $msn;
}

// 自動安裝模組資料表的函式
function install_module_tb($MODULE_SQL_FILE="") {
    global $SFS_PATH, $mysql_db;

	//檢查是否安裝過了
	if(check_installed($_POST['module_store_path'])){
		user_error("$module_name 模組已經安裝，您可以先移除再安裝！", 256);
	}

	$sql_query = fread(fopen($MODULE_SQL_FILE, 'r'), filesize($MODULE_SQL_FILE));

	run_sql($sql_query, $mysql_db);

}

//檢查是否安裝過了
function check_installed($module_name) {
	global $CONN;

	if ($module_path) {
		$sql="SELECT msn FROM sfs_module WHERE diename='$module_name'";
		$res=$CONN->Execute($sql) or user_error("錯誤訊息： 查詢模組資料時有問題!", 256);

		list($id)=$res->FetchRow();
		if ($id)			return true;
	}
	return false;
}
?>
