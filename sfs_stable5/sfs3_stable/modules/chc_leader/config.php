<?php
//$Id$
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
//您可以自己加入引入檔

	//擷取資料
function getLeaderKind($K){
	global $UPLOAD_PATH;
	$file=$UPLOAD_PATH.'school/chc_leader/var.txt';
	$kind['A']=array('班長','副班長','康樂股長','學藝股長','事務股長','衛生股長','風紀股長','輔導股長','環保股長','資訊股長');
	$kind['B']=array('國樂社','弦樂團','管樂團','直笛隊','籃球隊');
	$kind['C']=array('社長','副社長','隊長','副隊長');
	if ( $K!='A' && $K!='B' && $K!='C' ) return ;
	if (!file_exists($file)) :
		return $kind[$K]; 
	else:
		$str=file_get_contents ($file);
		$data=unserialize($str);
		return $data[$K];
	endif;
		
}

##################回上頁函式1#####################
function backe($value= "BACK"){
	echo  "<head><meta http-equiv='Content-Type' content='text/html; charset=big5'></head><br><br><br><br><CENTER><form><input type=button value='".$value."' onclick=\"history.back()\" style='font-size:16pt;color:red;'></form><BR></CENTER>";
	exit;
}