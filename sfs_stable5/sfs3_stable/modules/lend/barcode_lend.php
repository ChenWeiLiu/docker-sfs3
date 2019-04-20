<?php

// $Id: barcode_lend.php 6731 2012-03-28 01:50:11Z infodaes $
include "config.php";
sfs_check();

//秀出網頁
if(!$remove_sfs3head) head("撥交登錄");

$teacher_sn=$_REQUEST['teacher_sn'];
if($_GET['menu']<>'off') echo print_menu($MENU_P,$linkstr);

$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$barcode=$_POST['barcode'];
$lend_date=$_POST['lend_date'];
$refund_limit=$_POST['refund_limit'];
$memo=$_POST['memo'];

if($barcode AND $_POST['act']=='解析處理'){
	$barcode=explode("\r\n",$barcode);
	$excuted="<BR>※ 前次條碼解析之結果如下～<BR><BR>";
	$ask_items='';
	foreach($barcode as $value){
		if($value) { $ask_items.="'$value',"; }
	}  
	$ask_items=SUBSTR($ask_items,0,-1);
	
	$sql="SELECT a.*,b.item,DATE_ADD(curdate(),INTERVAL b.days_limit DAY) AS refund_limit FROM equ_request a,equ_equipments b WHERE (a.equ_serial=b.serial) AND (a.equ_serial IN ($ask_items))";
	$res=$CONN->Execute($sql) or user_error("條碼解析失敗！<br>$sql",256);
	
	//準備移植的sql
	$ask_items='';  //重新準備,作為後面刪除用的
	$sql="INSERT INTO equ_record(year_seme,teacher_sn,ask_date,allowed_date,lend_date,equ_serial,refund_limit,memo,manager_sn) VALUES ";
	while(!$res->EOF) {
		$excuted.='NO.'.($res->CurrentRow( )+1).'　'.$teacher_array[$res->fields['teacher_sn']]['title']."-".$teacher_array[$res->fields['teacher_sn']]['name'].' '.$res->fields['equ_serial'].' '.$res->fields['item'].' ======> ';
		if($res->fields['manager_sn']==$session_tea_sn)
		{
			$sql.="('$curr_year_seme',".$res->fields['teacher_sn'].",'".$res->fields['ask_date']."','".$res->fields['allowed_date']."','$lend_date','".$res->fields['equ_serial']."','".($refund_limit?$refund_limit:$res->fields['refund_limit'])."','".($memo?$memo:$res->fields['memo'])."',$session_tea_sn),";
			$excuted.='成功<BR>';
			$ask_items.=$res->fields['sn'].',';
		} else {
			$excuted.='非管理者經管物品,撥交失敗!!<BR>';			
		}
		$res->MoveNext();
	}
	if($ask_items){   //假使有成功紀錄　　再執行sql
		$sql=substr($sql,0,-1);
		$res=$CONN->Execute($sql) or user_error("寫入申請紀錄失敗！<br>$sql",256);

		//刪除request裡面的紀錄
		$ask_items=SUBSTR($ask_items,0,-1);
		$sql="DELETE FROM equ_request WHERE sn IN ($ask_items)";
		$res=$CONN->Execute($sql) or user_error("已經創建借用紀錄,唯刪除申請紀錄失敗！<br>$sql",256);
	} else { $excuted="<BR><BR><font color='red'>※ 您所掃描的物品~~[非您所經手管理]或者[已經撥交借用] ※</font>"; }

}

$main="<table><form name='my_form' method='post' action='$_SERVER[PHP_SELF]'>
	<tr><td>▲借用日期：<input type='text' size=15 value='".date('Y-m-d',time())."' name='lend_date'>
	<BR>▲歸還期限：<input type='text' size=15 value='$refund_limit' name='refund_limit'><BR><font size=2>(此處留空則以各物品原設定標準借期置入)</font>
	<BR>▲附記說明：<input type='text' size=20 value='$memo' name='memo'>
	<BR>▲請掃描物品條碼：<BR><textarea rows='15' name='barcode' cols=33></textarea>
<BR><input type='submit' value='解析處理' name='act'><input type='reset' value='清空重掃'></td><td valign='top'>$excuted</td></tr></form></table>";
echo $main;
if(!$remove_sfs3head) foot();

?>