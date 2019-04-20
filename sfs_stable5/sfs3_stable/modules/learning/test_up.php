<?php

include "config.php";
session_start();

//限教師進入
if($_SESSION['session_who']=='教師'){


// 不需要 register_globals
if (!ini_get('register_globals')) {
 	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if ($unit ==""){
		exit();
}
// 領域名稱
	$m = substr ($unit, 0, 1); 
	$t = substr ($unit, 1, 2); 
	$u = trim (substr ($unit, 3, 4)); 
	//取得各領域冊別
	$sqlstr = "select * from unit_tome where  unit_m='$m' and unit_t='$t' " ;
	$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$row= mysql_fetch_array($result);
	$c_tome = $row["unit_tome"];
	$tome_ver = $row["tome_ver"];
	//取得單元名稱
	$sqlstr = "select * from unit_u where  unit_m='$m'  and unit_t='$t' and u_s='$u' and tome_ver='$tome_ver' ";
	$result = mysql_query($sqlstr);
	$row= mysql_fetch_array($result);
	$c_unit = $row["unit_name"];
	$u_id = $row["u_id"];

	echo "<a href=test_edit.php?unit=$unit >回上一頁</a>　<a href=test.csv >上傳檔案格式範例</a>";	
	echo "如要增加題目，請先下載格式範例檔.csv，依格式建好題庫後再上傳就可以了！附檔請以修改的方式再上傳。";
if ($act=="批次建立資料"){
	$msg="";
	$b_edit_time = mysql_date();

	//$msg=import($_FILES['userdata']['tmp_name'],$_FILES['userdata']['name'],$_FILES['userdata']['size']);
	$userdata_size=$_FILES['userdata']['size'];
	$userdata_name=$_FILES['userdata']['name'];
	$userdata=$_FILES['userdata']['tmp_name'];
	$temp_file= $USR_DESTINATION."test.csv";	
	if ($userdata_size>0 && $userdata_name!=""){
		
		copy($userdata , $temp_file);
		$fd = fopen ($temp_file,"r");
		while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
			if ($i++ == 0) //第一筆為抬頭
				continue ;		
			 
			  $breed=trim($tt[0]);
			  $answer=trim(addslashes($tt[1]));
			  $ques=trim(addslashes($tt[2]));
			  $ch1=trim(addslashes($tt[3]));
			  $ch2=trim(addslashes($tt[4]));
			  $ch3=trim(addslashes($tt[5]));
			  $ch4=trim(addslashes($tt[6]));
			  $ch5=trim(addslashes($tt[7]));
			  $ch6=trim(addslashes($tt[8]));
		
		
			$strSQL = "insert into test_data set  u_id='$u_id',breed='$breed',answer='$answer',ques='$ques',ch1='$ch1',ch2='$ch2',ch3='$ch3',ch4='$ch4',ch5='$ch5',ch6='$ch6',unit_m='$m',unit_t='$t',u_s='$u',up_date='$b_edit_time',teacher_sn='$_SESSION[session_tea_sn]'";
			
			$result=$CONN->Execute($strSQL) or user_error("讀取失敗！<br>$sqlstr",256);;
	
			
			$name=stripslashes($ques);
			if($result){
				$msg.=" -- $name 新增成功！<br>";
				}
			else
				$msg.=" -- $name 資料新增失敗！<br>";
			$i++;
		        
		}
	}
	else{
		$msg.="檔案格式錯誤";
	}
	unlink($temp_file);

	echo "<table cellspacing='1' cellpadding='10' class=main_body>
	<tr bgcolor='#E1ECFF'><td>".$msg."</td></tr></table>";
}else{
	 echo "	<table border='0' cellspacing='0' cellpadding='0' >
	<tr><td valign=top>
		<table cellspacing='1' cellpadding='10' class=main_body >
		<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
		<tr><td  nowrap valign='top' bgcolor='#E1ECFF'>
		<p>請按『瀏覽』選擇匯入檔案來源：</p>
		<input type=file name='userdata'>
		<p><input type=submit name='act' value='批次建立資料'></p>
		<input type='hidden' name='unit' value='$unit'>	
		</td>
		<td valign='top' bgcolor='#FFFFFF'>
	
		</td>
		</tr>
		</table>
	</form>
	</td></tr></table>
	";
}
}
