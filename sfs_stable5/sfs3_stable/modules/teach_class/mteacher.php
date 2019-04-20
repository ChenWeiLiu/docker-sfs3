<?php

// $Id: mteacher.php 7552 2013-09-19 03:38:47Z hami $

// --系統設定檔
include "teach_config.php";

//--認證 session
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


if ($act=="批次建立資料"){
	$msg=import($_FILES['userdata']['tmp_name'],$_FILES['userdata']['name'],$_FILES['userdata']['size']);
	//header("location: {$_SERVER['PHP_SELF']}?act=result&main=$msg");
}else{
	$main=&main_form();
}

//印出檔頭
head("批次建立教師資料");
if($msg) echo "<table cellspacing='1' cellpadding='10' class=main_body>
	<tr bgcolor='#E1ECFF'><td>".$msg."</td></tr></table>";
else echo $main;
foot();
	

//主要表格
function &main_form(){
	global $teach_menu_p;
	$toolbar=&make_menu($teach_menu_p);

	$main="
	$toolbar
	<table border='0' cellspacing='0' cellpadding='0' >
	<tr><td valign=top>
		<table cellspacing='1' cellpadding='10' class=main_body >
		<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
		<tr><td  nowrap valign='top' bgcolor='#E1ECFF'>
		<p>請按『瀏覽』選擇匯入檔案來源：</p>
		<input type=file name='userdata'>
		<p><input type=submit name='act' value='批次建立資料'></p>
		</td>
		<td valign='top' bgcolor='#FFFFFF'>
		<p><b><font size='4'>教師資料批次建檔說明</font></b></p>
		<ol>
		<li>本程式只能建立教師基本資料，其他資料，需至教職員資料管理程式建立。</li>
		<li>建議利用 OpenOffice.org 之 Calc 鍵入教師資料，存成 csv 檔（需勾選「篩選設定」），並保留第一列標題檔，如
		<a href=teacherdemo.csv target=new>範例檔</a></li>
		<li>出生日期以西元為準。</li>
		<li>密碼需含英文及數字，且長度至少為六個字。</li>
		</ol>
		</td>
		</tr>
		</table>
	</form>
	</td></tr></table>
	";
	return $main;
}


//匯入資料
function import($userdata,$userdata_name,$userdata_size){
	global $temp_path,$CONN;
	$temp_file= $temp_path."/tea.csv";
	if ($userdata_size>0 && $userdata_name!=""){
		copy($userdata , $temp_file);
		$fd = fopen ($temp_file,"r");
		//while ($tt = fgetcsv ($fd, 2000, ",")) {
		$contents = fread($fd, filesize($temp_file));
		$temp_arr = explode ("\n",$contents);
		foreach ($temp_arr as $tt_temp){
			$tt = explode (",",$tt_temp);
			if ($i++ == 0 or empty($tt_temp)) //第一筆為抬頭
				continue ;
			$teach_id = trim ($tt[0]);
			$login_pass = pass_operate(trim($tt[1]));
			$teach_person_id = trim ($tt[2]);
			if ($teach_person_id) {
				$teach_person_id = strtoupper($teach_person_id) ;
				$edu_key =  hash('sha256', strtoupper($teach_person_id));
			}
			$name = trim (addslashes($tt[3]));
			$sex = trim ($tt[4]);
			$birthday = trim ($tt[5]);
			$marriage = trim ($tt[6]);
			$address = trim (addslashes($tt[7]));
			$home_phone = trim ($tt[8]);
			$cell_phone = trim ($tt[9]);
			$sql_insert = "insert into teacher_base (teach_id,teach_person_id,name,sex,birthday,marriage,address,home_phone,
			cell_phone,login_pass,edu_key) values ('$teach_id','$teach_person_id','$name','$sex','$birthday','$marriage',
			'$address','$home_phone','$cell_phone','$login_pass','$edu_key')";
			$result=$CONN->Execute($sql_insert);
			$name=stripslashes($name);
			if($result){
				$msg.="$teach_id -- $name 新增成功！<br>";
				}
			else
				$msg.="$teach_id -- $name 資料新增失敗！<br>";
			$i++;
		}
	}
	else{
		$msg.="檔案格式錯誤";
	}
	unlink($temp_file);
	return $msg;
}
?>
