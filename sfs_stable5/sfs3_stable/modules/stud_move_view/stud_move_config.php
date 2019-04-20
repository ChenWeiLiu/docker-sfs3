<?php
// $Id: stud_move_config.php 5310 2009-01-10 07:57:56Z hami $
	//系統設定檔
	include "../../include/config.php";
	//函式庫
	include "../../include/sfs_case_PLlib.php";    
	
$menu_p = array("stud_move_view.php"=>"查看學生異動","explode_stu.php"=>"匯出萬豐版資料");

//取得縣市鄉鎮轉 zip 陣列
function get_addr_zip_arr() {
	global $CONN;
	$query = "select zip,country,town from stud_addr_zip order by zip";
	$res= $CONN->Execute($query) or trigger_error("語法錯誤!",E_USER_ERROR);
	while(!$res->EOF){
		$addr =   $res->fields[1].$res->fields[2];
		$zip_arr[$addr]=$res->fields[0] ;
		//$zip_arr[$res->fields[0]] = $res->fields[1].$res->fields[2];
		$res->MoveNext();
	}
	return $zip_arr;
}	

function change_addr($addr,$mode=0) {
	//縣市
	$temp_str = split_str($addr,"縣",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//鄉鎮	
	$temp_str = split_str($addr,"鄉",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"鎮",1);

	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"區",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//村里
	$temp_str = split_str($addr,"村",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"里",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//鄰
	$temp_str = split_str($addr,"鄰",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//路
	$temp_str = split_str($addr,"路",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"街",1);
	
	$res[] = $temp_str[0];
	$addr=$temp_str[1];

      	//段
	$temp_str = split_str($addr,"段",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//巷
	$temp_str = split_str($addr,"巷",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//弄
	$temp_str = split_str($addr,"弄",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//號
	$temp_str = split_str($addr,"號",$mode);
	$temp_arr = explode("-",$temp_str);
	if (sizeof($temp_arr)>1){
		$res[]=$temp_arr[0];
		$res[]=$temp_arr[1];
	}else {
		$res[]=$temp_str[0];
		$res[]="";
	}
	$addr=$temp_str[1];
	
	//樓
	$temp_str = split_str($addr,"樓",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//樓之
	if ($addr != "") {
		if ($mode)
			$temp_str = $addr;
		else
			$temp_str = substr(chop($addr),2);
	} else
		$temp_str ="";
		
	$res[]=$temp_str ;
      	return $res;
}

function split_str($addr,$str,$last=0) {
      	$temp = explode ($str, $addr);
	if (count($temp)<2 ){
		$t[0]="";
		$t[1]=$addr;
	}else{
		$t[0]=(!empty($last))?$temp[0].$str:$temp[0];
		$t[1]=$temp[1];
	}
	return $t;
}
?>