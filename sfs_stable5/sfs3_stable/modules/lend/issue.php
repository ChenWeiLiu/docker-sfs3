<?php

// $Id: issue.php 6207 2010-10-05 04:46:46Z infodaes $
include "config.php";
sfs_check();

//秀出網頁
if(!$remove_sfs3head) head("配發作業");

if($_GET['menu']<>'off') echo print_menu($MENU_P,$linkstr);

$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$barcode=$_POST['barcode'];
$lend_date=$_POST['lend_date'];
$ask_date=$_POST['lend_date'];
$allowed_date=$_POST['lend_date'];
$refund_limit=$_POST['refund_limit'];
$memo=$_POST['memo'];

if($barcode and $_POST['act']=='解析處理'){
	$barcode=explode("\r\n",$barcode);
	$excuted="<BR>※ 前次配發借用物品結果如下～<BR><BR>";
	//檢查是否在職
	$teach_id=array_shift($barcode);
	if($teach_id and array_key_exists($teach_id,$teach_id_array)){
		$ask_items='';
		$teacher_sn=$teach_id_array[$teach_id];
		$teach_name=$teacher_array[$teacher_sn]['title'].'-'.$teacher_array[$teacher_sn]['name'];
		$excuted.="◎借用者：$teach_id $teach_name<BR><BR>";
		$excuted.='◎物品編號清單：<BR>';
		$sql="INSERT INTO equ_record(year_seme,teacher_sn,ask_date,allowed_date,lend_date,equ_serial,refund_limit,memo,manager_sn) VALUES ";
		foreach($barcode as $value){
			if($value) {
				//檢查管理物品是否存在
				$check_sql="SELECT item,days_limit FROM equ_equipments WHERE manager_sn=$session_tea_sn AND serial='$value'";
				$res=$CONN->Execute($check_sql) or user_error("檢查管理物品是否存在失敗！<br>$check_sql",256);
				if($res->recordcount()){
					$ask_items.="'$value',";
					$excuted.="　[$value]".$res->fields['item']."~~成功!!<BR>";
					$refund_date="CURDATE()+".$res->fields['days_limit'];
					$sql.="('$curr_year_seme',$teacher_sn,'$ask_date','$allowed_date','$lend_date','$value',".($refund_limit?"'".$refund_limit."'":$refund_date).",'$memo',$session_tea_sn),";
				} else { $excuted.="　<font color='red'>$value ->[無此物品]或[非經管物品]~~失敗</font><BR>"; }
			}
		}
		
		if($ask_items){   //假使有成功紀錄　　再執行sql
			$sql=substr($sql,0,-1);
			$res=$CONN->Execute($sql) or user_error("寫入借用紀錄失敗！<br>$sql",256);

			//刪除request裡面的紀錄
			$ask_items=SUBSTR($ask_items,0,-1);
			$sql="DELETE FROM equ_request WHERE equ_serial IN ($ask_items)";
			$res=$CONN->Execute($sql) or user_error("已經創建借用紀錄,唯刪除申請紀錄失敗！<br>$sql",256);
		} 
		//else { $excuted.="<BR><BR><font color='red'>◎未輸入借用物品編號</font>"; }
	} else { $excuted.="<font color='red'>◎找不到借用者資料~~該員可能[未在職]或者[更改學務系統登入帳號了]</font>"; }
}

$main="<table><form name='my_form' method='post' action='$_SERVER[PHP_SELF]'>
	<tr>
	<td>▲物品編號<font size=2 color='red'> (第一列為借用者代號)</font>：<BR><textarea rows='15' name='barcode' cols=33></textarea>
	<BR>▲配發日期：<input type='text' size=10 value='".date('Y-m-d',time())."' name='lend_date'>
	<BR>▲歸還期限：<input type='text' size=10 value='$refund_limit' name='refund_limit'><BR><font size=2>(此處留空則以各物品原設定標準借期置入)</font>
	<BR>▲附記說明：<input type='text' size=20 value='$memo' name='memo'>
	<BR><BR><input type='submit' value='解析處理' name='act'> <input type='reset' value='清空重掃'></td>
	<td valign='top'>$excuted</td>
	</tr>
	</form></table>";
echo $main;
if(!$remove_sfs3head) foot();

?>