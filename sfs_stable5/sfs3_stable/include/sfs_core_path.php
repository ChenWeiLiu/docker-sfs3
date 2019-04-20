<?php

// $Id: sfs_core_path.php 5498 2009-06-17 08:56:47Z brucelyc $

  // 檢查是否屬於不顯示的目錄名稱
  // 可以顯示者傳回 true
  // 不顯示者傳回 false
function is_display_path($chk){
  if (!$chk) user_error("沒有傳入參數！請檢查。",256);
	$is_path= true;
	$pp = non_display_path();
	for($i=0; $i< count($pp); $i++){
		if ($pp[$i]==$chk){
			$is_path= false;
			break;
		}
	}
	return $is_path;
}

//系統管理---不顯示目錄名(重要目錄或說明、圖檔)
// 傳回 array
function non_display_path(){
	global $SFS_SCHOOL_LOGIN_PATH;
	$non_display=SFS_TEXT("non_display");
	if(!empty($non_display)){
		if(!empty($SFS_SCHOOL_LOGIN_PATH))
			$non_display[]=$SFS_SCHOOL_LOGIN_PATH;
		return $non_display;
	}else{
		return array(".","..","images","db","themes","pnadodb","include","upgrade","data","CVS","phpMyAdmin");
	}
}

//取得路徑函數
function get_path($chk){
	if (!$chk) user_error("沒有傳入參數！請檢查。",256);
	global $SFS_PATH;
	$chk = str_replace("\\\\","/",$chk); 
	$pp = substr($chk,strlen($SFS_PATH)+1);
	return updir($pp);
}


//取得上層路徑函數
function updir( $path ){
	//if (!$path) user_error("沒有傳入參數！請檢查。",256);
	$last = strrchr( $path, "/" );
	$n1   = strlen( $last );
	$n2   = strlen( $path );
	return substr( $path, 0, $n2-$n1 );
}


//取得所在路徑名稱函數
function get_store_path($path=""){
	global $SFS_PATH, $SFS_PATH_HTML;
	if ($path =="" || $path==$SFS_PATH)
		$path = $_SERVER['SCRIPT_FILENAME'];
	$ap_path = str_replace("\\\\","/",$path);
	$n1   = strlen( $SFS_PATH );
	$n2   = strlen( $ap_path );
	$SFS_PATH_List = substr($ap_path, $n1, $n2-$n1 );
	$store_path = updir($SFS_PATH_List);
	if (substr($store_path,0,1)=='/')
                $store_path = substr($store_path,1);
	return $store_path;
}

//取得程式路徑名稱函數
function get_sfs_path($curr_msn=""){
	global $CONN,$SFS_PATH_HTML,$UPLOAD_PATH;
	
	//@include_once($UPLOAD_PATH."Module_Path.php");
	$file = $UPLOAD_PATH."Module_Path.txt";
	$fp = @fopen($file,'r');
	$contents = fread($fp, filesize($file));
	$MPath = unserialize($contents);
	if(empty($curr_msn) and $SCRIPT_NAME!="/index.php"){
		$SCRIPT_NAME=$_SERVER[SCRIPT_NAME];
		$SN=explode("/",$SCRIPT_NAME);
		$m=getDBdata("",$SN[count($SN)-2]);
		$curr_msn=$m[msn];
	}
	$path="<a href='$SFS_PATH_HTML' accesskey='H'><img src='".$SFS_PATH_HTML."images/gohome.png' alt='' width='16' height='16' hspace='3' border='0' align='absmiddle'>學務管理系統首頁</a> / $MPath[$curr_msn]";
	
	return $path;
}



//模組路徑階層連結
function get_module_path($curr_msn=0,$home_name="首頁",$needlink=0){
	global $CONN,$SFS_PATH_HTML;

	if(empty($curr_msn)){
		$m_name=($needlink)?"<a href='$SFS_PATH_HTML'>$home_name</a>":$home_name;
		return $m_name;
	}

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select="select of_group,showname,kind from sfs_module where  msn='$curr_msn' order by sort";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	list($of_group,$showname,$kind)=$recordSet->FetchRow();
	$pre_path=get_module_path($of_group,$home_name,$needlink);
	if ($kind=="分類")
		$p.=($needlink)?$pre_path." / <a href='$SFS_PATH_HTML"."index.php?_Msn=$curr_msn'>$showname</a>":$pre_path." / $showname";
	else
		$p.=($needlink)?$pre_path." / <a href='index.php'>$showname</a>":$pre_path." / $showname";
	return $p;
}

//取得某一筆資料
function getDBdata($msn="",$dirname=""){
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if(!empty($msn)){
		$where="msn='$msn'";
	}elseif(!empty($dirname)){
		$where="dirname='$dirname'";
	}else{
		return array();
	}

	// init $theData
	$theData=array();

	$sql_select="select * from sfs_module where $where";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$theData=$recordSet->FetchRow();
	return $theData;
}




//設定上傳目錄路徑
function set_upload_path($path_str) {
	global $UPLOAD_PATH,$UPLOAD_URL;
	if ($path_str !="") {
		$arr = explode ("/",$path_str);
		//建立目錄
		$temp = $UPLOAD_PATH;
		for($i=0;$i<count($arr);$i++){
			if ($arr[$i]<>''){
				$temp .= $arr[$i];
				if (!is_dir($temp))
					mkdir($temp, 0755); 
				$temp .= "/";		
			}
		}
	}
	return $temp;
}



//檢查不允許上傳檔案
function check_is_php_file ($filename) {
	if (!$filename) user_error("沒有傳入參數！請檢查。",256);
	$res = SFS_TEXT("php_file");
	if (count($res)==0)
		$res = array("1"=>"php","2"=>"php3","3"=>"ini","4"=>"inc");
	$temp_arr = array_values ($res);
	$subname = substr( strrchr($filename, "." ), 1 );
	if (in_array ($subname, $temp_arr))
		return true;
	else
		return false;
}


// 檢查系統是否設定為可上傳檔案
function check_phpini_upload() {
	if (!ini_get(file_uploads)) 
		trigger_error("您的 php.ini 中未打開上傳檔案設定，請設妥 file_uploads=On，並重新啟動 Apache！", E_USER_ERROR);

}


//檢查上傳檔名並回傳暫存檔名
function check_upload_file($f=array(),$ext=array()) {
	global $_ENV;

	//取得檔案上傳暫存目錄
	$tmp_path=ini_get("upload_tmp_dir");
	if (empty($tmp_path)) {
		$tmp_path=$_ENV["TMP"];
	}
	if (empty($tmp_path)) {
		$tmp_path="/tmp";
	}

	if (count($f)>0 && count($ext)>0) {
		$file_name=strtoupper($f['name']);
		$s_str="/";
		if (substr(strtoupper($_ENV['OS']),0,3)=="WIN") {
			$ff_arr=explode("\\",$f['tmp_name']);
			$ff_str=$ff_arr[0];
			for($i=1;$i<(count($ff_arr)-1);$i++) $ff_str.="\\".$ff_arr[$i];
			if (strtoupper($ff_str)==strtoupper($tmp_path)) $tmp_path=$ff_str;
			$s_str="\\";
		}
		if (in_array(substr($file_name,-3,3),$ext)) return $tmp_path.$s_str;
	}
}
?>
