<?php

// $Id: barcode_lend.php 6207 2010-10-05 04:46:46Z infodaes $
include "config.php";
sfs_check();

//秀出網頁
head("條碼掃瞄登錄");

echo print_menu($MENU_P,$linkstr);

$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$barcode=$_POST['barcode'];
$auth_date=$_POST['auth_date'];
$memo=$_POST['memo'];
$score=$_POST['score'];

if($barcode AND $_POST['act']=='解析處理'){
	//取得可認證的班級及細目
	$sql="select * from authentication_empower WHERE empowered_sn=$session_tea_sn and year_seme='$curr_year_seme'";
	$res=$CONN->Execute($sql) or user_error("讀取認證授權失敗！<br>$sql",256);
	while(!$res->EOF){
		$subitem_sn=$res->fields['subitem_sn'];
		$class_id=$res->fields['class_id'];
		$allowed_array[$subitem_sn][$class_id]=True;
		$res->MoveNext();
	}
	
	//取得認證細目陣列
	$sql="select a.*,b.nature,b.title as item_title,b.start_date,b.end_date from authentication_subitem a,authentication_item b WHERE a.item_sn=b.sn";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF){
		$sn=$res->fields['sn'];
		$subitem_array[$sn]['title']=$res->fields['nature'].'-'.$res->fields['item_title'].'-'.$res->fields['code'].'-'.$res->fields['title'].' ( '.$res->fields['start_date'].'~'.$res->fields['end_date'].' )';
		$res->MoveNext();
	}

	//解析掃瞄的條碼
	$barcode=explode("\r\n",$barcode);
	$excuted="<BR>※ 前次條碼讀取匯入的記錄如下～<BR>";
	$ask_items='';	
	$error_info='<BR>※ 下面的認證記錄已經存在，系統未進行記錄更新！<br>';
	$illegal_info='<BR>※ 下面的認證要求不被許可！<br>';
	foreach($barcode as $value){
		if($value) {
			$real_data=explode('-',$value);
			$student_sn=$real_data[0];
			$subitem_sn=$real_data[1];
			//合法認證檢查
			$sql="SELECT a.seme_class,a.seme_num,b.stud_name FROM stud_seme a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.student_sn=$student_sn and a.seme_year_seme='$curr_year_seme'";
			$res=$CONN->Execute($sql) or user_error("讀取學生基本資料失敗！<br>$sql",256);
			if($res->RecordCount()){
				$class_id=$res->fields['seme_class'];
				$curr_class_num=sprintf('%02d',$res->fields['seme_num']);				
				$stud_name=$res->fields['stud_name'];
				if($allowed_array[$subitem_sn][$class_id] or $my_class_id==$class_id){  //假使是被授予認證的  或者  是班級導師
					//重複性檢查
					$sql2="SELECT * FROM authentication_record WHERE sub_item_sn=$subitem_sn AND student_sn=$student_sn";
					$res2=$CONN->Execute($sql2) or user_error("讀取失敗！<br>$sql2",256);
					if($res2->RecordCount()){
						$sn=$res2->fields['sub_item_sn'];
						$error_info.="　　$class_id$curr_class_num-$stud_name--{$subitem_array[$subitem_sn]['title']}<br>";			
					} else {			
						$excuted.='　　'.$value.'<br>';
						$ask_items.="('$curr_year_seme','$subitem_sn','$student_sn','$score','$session_tea_sn','$auth_date','$memo'),";
					}
				}  else { $illegal_info.="<br>　　$value 您未獲授權進行 $class_id$curr_class_num - $stud_name --- {$subitem_array[$subitem_sn]['title']} 的認證！"; }		
			} else { $illegal_info.="<br>　　$value 經查~~本學期無此學生之就讀資料！"; }
		}
	}
	$ask_items=substr($ask_items,0,-1);
	//有需要新增的才寫入
	if($ask_items){
		$sql="INSERT INTO authentication_record(year_seme,sub_item_sn,student_sn,score,teacher_sn,date,note) VALUES $ask_items";
		$res=$CONN->Execute($sql) or user_error("寫入失敗！<br>$sql",256);
	}
}

$main="<table><form name='my_form' method='post' action='$_SERVER[PHP_SELF]'>
	<tr><td>▲認證日期：<input type='text' size=15 value='".date('Y-m-d',time())."' name='auth_date'>
	<BR>▲成　　績：<input type='text' size=5 value='' name='score'>
	<BR>▲附記說明：<input type='text' size=20 value='$memo' name='memo'>
	<BR>▲掃瞄認證條碼：<BR><textarea rows='15' name='barcode' cols=33></textarea>
<BR><input type='submit' value='解析處理' name='act'><input type='reset' value='清空重掃'></td><td valign='top'><font color='red'>$illegal_info</font><br><font color='green'>$error_info</font><br><font color='blue'>$excuted</font></td></tr></form></table>";
echo $main;
foot();
?>