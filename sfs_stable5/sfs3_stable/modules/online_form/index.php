<?php

// $Id: index.php 7846 2014-01-09 05:33:48Z infodaes $

include_once "config.php";

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if(empty($act))$act="";

if($act=="sign"){
	if(empty($ofsn)){
		header("location: {$_SERVER['PHP_SELF']}");
	}elseif(check_signd_form($_SESSION['session_tea_sn'],$ofsn)){
		$main="<center><font color='red'>此項目您已經填報過，目前為修改模式，若沒有要更改可以按上一頁退出。</font></center><br>".view_form($ofsn,"modify");
	}else{
		$main=&view_form($ofsn);
	}
}elseif($act=="sign_in"){
	
	add_school_sign_data($ofsn,$_SESSION['session_tea_sn'],$col);
	header("location: {$_SERVER[PHP_SELF]}");

}elseif($act=="update_in"){
	add_school_sign_data($ofsn,$_SESSION['session_tea_sn'],$col,$schfi_sn);
	header("location: {$_SERVER[PHP_SELF]}");
}else{
	$tool_bar=&make_menu($school_menu_p);
	$main=$tool_bar.school_sign_form();
}

//秀出網頁
head("線上填報");
echo $main;
foot();


//新增一筆學校填報相關資料
function add_school_sign_data($ofsn,$teacher_sn,$col,$schfi_sn=0){
	global $CONN;
	if(empty($teacher_sn))return false;
	if(!empty($schfi_sn)){
		$str="update form_fill_in set fill_time=now() where schfi_sn=$schfi_sn";
	}else{
		$str="insert into form_fill_in (ofsn,teacher_sn,fill_time) values($ofsn,$teacher_sn,now())";
	}
	
	$CONN->Execute($str) or user_error("填報失敗！<br>$str", 256);
	
	$mysql_insert_id=(!empty($schfi_sn))?$schfi_sn:mysql_insert_id();
	$mode=(!empty($schfi_sn))?"update":"add";
	$result=add_school_ans_data($mysql_insert_id,$teacher_sn,$ofsn,$col,$mode);

	return true;
}


//新增學校填報回答值資料
function add_school_ans_data($schfi_sn,$teacher_sn,$ofsn,$col,$mode="add"){
	global $CONN;
	$query = "select col_sn,col_dataType from form_col where ofsn=$ofsn";
	$res = $CONN->Execute($query) or die($CONN->ErrorMsg());
	// 檔案處理
	while(!$res->EOF){
		if ($res->fields['col_dataType']=='file'){
			$col_sn = $res->fields['col_sn'];
			$col_name = "col_$col_sn";
			//設定上傳檔案路徑
			$file_path = set_upload_path(get_store_path()."/".$ofsn."/".$col_sn);
			if($_FILES[$col_name]['tmp_name']){
				
				if (!check_is_php_file($_FILES[$col_name]['name'])){
					// 刪除舊檔
					$query = "select value from form_value where schfi_sn=$schfi_sn and ofsn=$ofsn and col_sn=$col_sn";
					$res2 = $CONN->Execute($query);
					unlink($file_path."/".$res2->fields[0]);
					$temp_arr = explode(".",$_FILES[$col_name]['name']);
					$file_name = $teacher_sn.".".end($temp_arr);
					copy($_FILES[$col_name]['tmp_name'], $file_path.$file_name);
					if($mode=="add"){
						$str="insert into form_value (schfi_sn,teacher_sn,ofsn,col_sn,value) values($schfi_sn,$teacher_sn,$ofsn,$col_sn,'$file_name')";
					}elseif($mode=="update"){
						$str="update form_value set value='$file_name' where schfi_sn=$schfi_sn and teacher_sn=$teacher_sn and ofsn=$ofsn and col_sn=$col_sn";
		}
					$CONN->Execute($str) or user_error("填報失敗！<br>$str", 256);
				}
			}
			
		}
		$res->MoveNext();
	}
	//print_r($col);
	while (list($col_sn,$value) = each($col)){
		$sql="SELECT value_sn FROM form_value WHERE schfi_sn=$schfi_sn and teacher_sn=$teacher_sn and ofsn=$ofsn and col_sn=$col_sn";
		$detect=$CONN->Execute($sql);
		if(! $detect->fields[0])
			$str="insert into form_value (schfi_sn,teacher_sn,ofsn,col_sn,value) values($schfi_sn,$teacher_sn,$ofsn,$col_sn,'$value')";
		else
			$str="update form_value set value='$value' where schfi_sn=$schfi_sn and teacher_sn=$teacher_sn and ofsn=$ofsn and col_sn=$col_sn";
		
		$CONN->Execute($str) or user_error("填報失敗！<br>$str", 256);
	}
	return true;
}



?>
