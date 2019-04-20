<?php
// $Id: mstudent.php 6141 2010-09-14 03:17:12Z brucelyc $

// --系統設定檔
include "create_data_config.php";

//--認證 session
sfs_check();

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

$act=($_GET[act])?$_GET[act]:$_POST[act];

if ($act=="批次建立資料"){
	$msg=import($_POST[class_id],$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?act=result&main=$msg");
}elseif($act=="result"){
	$main="<table cellspacing='1' cellpadding='10' class=main_body>
	<tr bgcolor='#E1ECFF'><td>$_GET[main]</td></tr></table>";
}else{
	$main=main_form($sel_year,$sel_seme);
}

//印出檔頭
head("大量建立學生資料：簡易版");
echo $main;
foot();


//主要表格
function main_form($sel_year,$sel_seme){
	global $menu_p;
	
	$toolbar=&make_menu($menu_p);
	
	//年級與班級選單
	$class_select=&classSelect($sel_year,$sel_seme,"","class_id","",false);
	
		
	$main="
	$toolbar
	<table border='0' cellspacing='0' cellpadding='0' >
	<tr><td valign=top>
		<table cellspacing='1' cellpadding='10' class=main_body >
		<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
		<tr><td  nowrap valign='top' bgcolor='#E1ECFF'>
		<p>請按『瀏覽』選擇匯入檔案來源：</p>
		將名單匯入哪一個班級？
		$class_select<br>
		<input type=file name='userdata'>
		<p><input type=submit name='act' value='批次建立資料'></p></td>
		<td valign='top' bgcolor='#FFFFFF'>
		<p><b><font size='4'>學生簡易資料批次建檔說明</font></b></p>
		<ol>
		<li>此匯入介面<font color='#FF0000'>不支援萬豐版格式！</font>，必須自行製作匯入檔。</li>
		<li>匯入檔可利用試算表工具或文字編輯器來製作，存成 csv 檔，並保留第一列標題檔，如
		<a href=newstudemo.csv target=new>範例檔</a></li>
		<li>出生日期以西元為準。</li>
		<li>性別用「1」來代表男生，「2」來代表女生。</li>
		
		</ol>
		</td></tr>
		</form>
		</table>
	</td></tr></table>
	";
	return $main;
}


//匯入資料
function import($class_id,$sel_year,$sel_seme){
	global $temp_path,$CONN;

	$userdata=$_FILES['userdata']['tmp_name'];
	$userdata_name=$_FILES['userdata']['name'];
	$userdata_size=$_FILES['userdata']['size'];

	$temp_file= $temp_path."stud.csv";
	
	//從class_id 中取得相關資料
	//091_1_01_02=>[0]=91、[1]=1、[2]=102，[3]=1，[4]=2，[5]=一年二班
	$c=class_id_2_old($class_id);
	
	
	//取出該班的名稱
	$query = "select c_name from school_class where class_id='$class_id'";
	$recordSet= $CONN->Execute($query) or user_error($query,256);
	list($c_name)=$recordSet->FetchRow();
	
	//die($temp_file);
	if ($userdata_size > 0 && $userdata_name!=""){
		//die($temp_file);
		$msg=(copy($userdata , $temp_file))?"檔案匯入完畢。<br>":"檔案匯入失敗。<br>";
		$fd = fopen ($temp_file,"r");
		$msg.=(empty($fd))?"讀取檔案失敗。<br>":"讀取檔案完畢。<br>";
		while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
			if ($i++ == 0) //第一筆為抬頭
			continue ;
			$stud_id = trim ($tt[0]);					//學號
			$stud_num=chop ($tt[1]);				//座號
			$stud_name = trim ($tt[2]);					//姓名
			$stud_person_id = trim ($tt[3]);			//身分證號
			$stud_birthday = trim ($tt[4]);				//生日
			$stud_sex = trim ($tt[5]);					//性別
			
			if(empty($stud_id) or empty($stud_name) or empty($stud_birthday)){
				continue ;
			}
					
			//轉換成怪怪的學生編號
			$curr_class_num= $c[2]*100+$stud_num;
			
			//學生入學年，利用學號前三碼來判斷
			$stud_study_year=$tt[6];
			
			//新增學生資料製資料庫
			$add1=add_2_stud_base($stud_id,$stud_name,$stud_sex,$stud_birthday,$stud_person_id,$stud_study_year,$curr_class_num);
			
			//把學年和學期兜在一起
			//學年學期
			$seme_year_seme = sprintf("%04d", $sel_year.$sel_seme);
			
			//新增學生資料到學期紀錄中
			$add2=add_2_stud_seme($seme_year_seme,$stud_id,$c[2],$stud_num,$c_name);
			
			//新增學生資料到戶籍紀錄中
			$add3=add_2_stud_domicile($stud_id);
			
			$msg.=($add1 and $add2 and $add3)?"$stud_id -- $stud_name 新增OK！<br>":"<font color=red>$stud_id -- $stud_name 新增過程有問題，請自行查明！</font><br>";
		}
	}else{
		$msg.="檔案格式錯誤！";
	}
	unlink($temp_file);
	return $msg;
}

//新增到stud_base
function add_2_stud_base($stud_id,$stud_name,$stud_sex,$stud_birthday,$stud_person_id,$stud_study_year,$curr_class_num){
	global $CONN;
	$stud_kind =',0,';
	$sql_insert = "replace into stud_base
	(stud_id,stud_name,stud_sex,stud_birthday,stud_person_id,stud_study_year,curr_class_num,stud_study_cond,stud_kind,enroll_school)
	values
	('$stud_id','$stud_name','$stud_sex','$stud_birthday','$stud_person_id','$stud_study_year','$curr_class_num','0','$stud_kind','$school_long_name')";
	if($CONN->Execute($sql_insert)){return true;}else{user_error($sql_insert,256);}
	return false;
}


//新增到stud_seme
function add_2_stud_seme($seme_year_seme,$stud_id,$seme_class,$seme_num,$c_name){
	global $CONN;
	//取得 student_sn
	$query = "select student_sn from stud_base where stud_id='$stud_id'";
	$resss = $CONN->Execute($query);
	$student_sn= $resss->fields[0];

	$sql_insert = "replace into stud_seme
	(seme_year_seme,stud_id,seme_class,seme_class_name,seme_num,student_sn)
	values
	('$seme_year_seme','$stud_id','$seme_class','$c_name','$seme_num','$student_sn')";

	if($CONN->Execute($sql_insert)){return true;}
	return false;
}
function add_2_stud_domicile($stud_id){
	global $CONN;
	$query = "replace into  stud_domicile (stud_id) values('$stud_id')";
	if($CONN->Execute($query)){return true;}
	return false;
}	
?>
