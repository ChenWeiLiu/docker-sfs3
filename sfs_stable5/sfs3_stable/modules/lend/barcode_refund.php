<?php

// $Id: barcode_refund.php 6207 2010-10-05 04:46:46Z infodaes $

include "config.php";
sfs_check();
//秀出網頁
if(!$remove_sfs3head) head("歸還登錄");
$teacher_sn=$_REQUEST['teacher_sn'];
if($_GET['menu']<>'off') echo print_menu($MENU_P,$linkstr);

$barcode=$_POST['barcode'];
$return_date=$_POST['return_date'];
$memo=$_POST['memo'];

if($barcode AND $_POST['act']=='解析處理'){
	$barcode=explode("\r\n",$barcode);
	$excuted="<BR>※ 前次條碼解析之結果如下～<BR><BR>";
	//檢查借用者是否在職
	if($barcode[0])
	
	$ask_items='';
	foreach($barcode as $value){
		if($value){
			//先檢查是否示為合宜紀錄　(借用者代號與物品是否尚未歸還?)
			$sql="SELECT a.sn,a.equ_serial,a.manager_sn,b.item,b.manager_sn AS new_manager_sn FROM equ_record a,equ_equipments b";
			$sql.=" WHERE a.equ_serial='$value' AND ISNULL(a.refund_date) AND a.equ_serial=b.serial";
			$res=$CONN->Execute($sql);
			$excuted.="　▲ ($value) ===>";
			if($res->recordcount()) {
				if($res->fields['manager_sn']==$session_tea_sn or $res->fields['new_manager_sn']==$session_tea_sn) {
					$ask_items.=$res->fields['sn'].',';
					$excuted.="<font color='blue'>".$res->fields['item']."===> 成功!</font><BR>";
				} else { $excuted.=$res->fields['item']."===> 您並非本物品管理者, 失敗!!<BR>"; }
			} else { $excuted.="[無此物品]或者[已經歸還了]~~ 失敗!!<BR>"; }
		}
	}
	if($ask_items) {
		$ask_items=SUBSTR($ask_items,0,-1);
		$sql="UPDATE equ_record SET refund_date='$return_date',receiver_sn=$session_tea_sn WHERE sn IN ($ask_items)";
		//echo $sql."<BR><BR><BR>";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$excuted",256);
	}
}

$main="<table><form name='my_form' method='post' action='$_SERVER[PHP_SELF]'>
	<tr><td>▲歸還日期：<input type='text' size=10 value='".date('Y-m-d',time())."' name='return_date'>
	<BR>▲附記說明：<input type='text' size=10 value='$memo' name='memo'>
	<BR>▲請掃描物品條碼：<BR><textarea rows='20' name='barcode' cols=30></textarea>
<BR><input type='submit' value='解析處理' name='act'><input type='reset' value='清空重掃'></td><td valign='top'>$excuted</td></tr></form></table>";
echo $main;
if(!$remove_sfs3head) foot();

?>