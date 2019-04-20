<?php

// $Id: barcode_crash.php 7708 2013-10-23 12:19:00Z smallduh $

include "config.php";
sfs_check();

$barcode=$_POST['barcode'];
$crash_date=$_POST['crash_date'];
$crashed_reason=$_POST['crashed_reason'];


if($_POST['act']=='物品報廢CSV清冊'){
	$sql="SELECT * FROM equ_equipments WHERE manager_sn=$session_tea_sn AND NOT ISNULL(crash_date) ORDER BY crash_date,nature";
	$res=$CONN->Execute($sql) or user_error("選取物品紀錄失敗！<br>$sql",256);
	$CSV='"物品編號","國際條碼號","物品名稱","財產編號","分類","位置","製造商","型號","機能","外借","借期","購買日期","購買金額","經銷商","保固期限","風險評估","使用年限","報廢日期","報廢原因"'."\r\n";
	while(!$res->EOF) {
		$CSV.='"'.$res->fields['serial'].'",';
		$CSV.='"'.$res->fields['barcode'].'",';
		$CSV.='"'.$res->fields['item'].'",';
		$CSV.='"'.$res->fields['asset_no'].'",';
		$CSV.='"'.$res->fields['nature'].'",';
		$CSV.='"'.$res->fields['position'].'",';
		$CSV.='"'.$res->fields['maker'].'",';
		$CSV.='"'.$res->fields['model'].'",';
		$CSV.='"'.$res->fields['healthy'].'",';
		$CSV.='"'.$res->fields['opened'].'",';
		$CSV.='"'.$res->fields['days_limit'].'",';
		$CSV.='"'.$res->fields['sign_date'].'",';
		$CSV.='"'.$res->fields['cost'].'",';
		$CSV.='"'.$res->fields['saler'].'",';
		$CSV.='"'.$res->fields['warranty'].'",';
		$CSV.='"'.$res->fields['importance'].'",';
		$CSV.='"'.$res->fields['usage_years'].'",';
		$CSV.='"'.$res->fields['crash_date'].'",';
		$CSV.='"'.$res->fields['crashed_reason'].'"';
		$CSV.="\r\n";
		$res->MoveNext();
	}
	$filename=$teacher_array[$session_tea_sn]['title']."-".$teacher_array[$session_tea_sn]['name']."經管物品報廢清冊.CSV";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $CSV;
	exit;
}

if($_POST['act']=='細目'){
	$sql="SELECT *,DATE_ADD(sign_date,INTERVAL usage_years YEAR)as suggest_date FROM equ_equipments WHERE manager_sn=$session_tea_sn AND ISNULL(crash_date) AND DATE_ADD(sign_date,INTERVAL usage_years YEAR)<'$crash_date' ORDER BY nature,sign_date";
	$res=$CONN->Execute($sql) or user_error("選取物品紀錄失敗！<br>$sql",256);
	$CSV='"物品編號","國際條碼號","物品名稱","財產編號","分類","位置","製造商","型號","機能","外借","借期","購買日期","購買金額","經銷商","保固期限","風險評估","使用年限","推估可報廢日期","報廢日期","報廢原因"'."\r\n";
	while(!$res->EOF) {
		$CSV.='"'.$res->fields['serial'].'",';
		$CSV.='"'.$res->fields['barcode'].'",';
		$CSV.='"'.$res->fields['item'].'",';
		$CSV.='"'.$res->fields['asset_no'].'",';
		$CSV.='"'.$res->fields['nature'].'",';
		$CSV.='"'.$res->fields['position'].'",';
		$CSV.='"'.$res->fields['maker'].'",';
		$CSV.='"'.$res->fields['model'].'",';
		$CSV.='"'.$res->fields['healthy'].'",';
		$CSV.='"'.$res->fields['opened'].'",';
		$CSV.='"'.$res->fields['days_limit'].'",';
		$CSV.='"'.$res->fields['sign_date'].'",';
		$CSV.='"'.$res->fields['cost'].'",';
		$CSV.='"'.$res->fields['saler'].'",';
		$CSV.='"'.$res->fields['warranty'].'",';
		$CSV.='"'.$res->fields['importance'].'",';
		$CSV.='"'.$res->fields['usage_years'].'",';
		$CSV.='"'.$res->fields['suggest_date'].'",';
		$CSV.='"'.$res->fields['crash_date'].'",';
		$CSV.='"'.$res->fields['crashed_reason'].'"';
		$CSV.="\r\n";
		$res->MoveNext();
	}
	$filename=$teacher_array[$session_tea_sn]['title']."-".$teacher_array[$session_tea_sn]['name']."經管物品建議報廢細目.CSV";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $CSV;
	exit;
}

if($_POST['act']=='建議清單'){
	$sql="SELECT serial FROM equ_equipments WHERE manager_sn=$session_tea_sn AND ISNULL(crash_date) AND DATE_ADD(sign_date,INTERVAL usage_years YEAR)<'$crash_date' ORDER BY nature,sign_date";
	$res=$CONN->Execute($sql) or user_error("選取物品紀錄失敗！<br>$sql",256);
	$suggestion='';
	while(!$res->EOF) {
		$suggestion.=$res->fields['serial']."\r\n";
		$res->MoveNext();
	}
}

//秀出網頁
if(!$remove_sfs3head) head("物品報廢條碼登錄");
if($_GET['menu']<>'off') echo print_menu($MENU_P,$linkstr);
if($barcode AND $_POST['act']=='解析處理'){
	$barcode=explode("\r\n",$barcode);
	$executed="<BR>※ 前次條碼解析之結果如下～<BR><BR>";
	$ask_items='';
	foreach($barcode as $value){
		if($value) { $ask_items.="$value,"; }
	}  
	$ask_items=SUBSTR($ask_items,0,-1);
	$sql="UPDATE equ_equipments SET opened='N',crash_date='$crash_date',crashed_reason='$crashed_reason',crash_teacher_sn=$session_tea_sn";
	$sql.=" WHERE manager_sn=$session_tea_sn AND serial IN ('$ask_items') AND ISNULL(crash_date)";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$executed.='◎ '.date('Y/m/d h:i:s')." 已將下列編號物品報廢[ $ask_items ]";
}
if(!$crash_date) $crash_date=date('Y-m-d',time());
$main="<table><form name='my_form' method='post' action='$_SERVER[PHP_SELF]'>
	<tr><td>▲報廢日期：<input type='text' size=12 value='$crash_date' name='crash_date'><input type='submit' value='建議清單' name='act'><input type='submit' value='細目' name='act'>
	<BR>▲報廢依據：<input type='text' size=29 value='$crashed_reason' name='crashed_reason'>
	<BR>▲請掃描報廢物品編號條碼：<BR><textarea rows='20' name='barcode' cols=42>$suggestion</textarea>
	<BR><input type='submit' value='解析處理' name='act'><input type='reset' value='清空重掃'><input type='submit' value='物品報廢CSV清冊' name='act'></td>
	<td valign='top'>$executed</td></tr></form></table><br>";
echo $main;
if(!$remove_sfs3head) foot();
?>