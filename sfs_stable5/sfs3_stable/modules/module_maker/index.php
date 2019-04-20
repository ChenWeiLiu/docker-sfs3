<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include_once "config.php";
sfs_check();
$sfs3_module_title=get_module_title();

//執行動作判斷
if($_POST[act]=="settbl"){
	$main=&settbl($_POST[table]);
}else{
	$main=&mainForm();
}


//秀出網頁
head($sfs3_module_title);
echo $main;
foot();

function &mainForm(){
	global $conID,$mysql_db,$school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	
	$result = mysql_listtables($mysql_db);
	$i = 0;
	while ($i < mysql_num_rows ($result)) {
		$tb_names[$i] = mysql_tablename ($result, $i);
		$tbl.="<option value='$tb_names[$i]'>$tb_names[$i]</option>";
		$i++;
	}
	$main="
	$tool_bar
	<form method='post' action='$_SERVER[PHP_SELF]'>
	請選擇一個表：
	<select name='table'>
	$tbl
	</select>
	<input type='hidden' name='act' value='settbl'>
	<input type='submit' value='確定'>
	</form>
	";
	return $main;
}


function &settbl($table){
	global $conID,$mysql_db,$CONN;

	$i = 0;
	
	$query = "select * from $table";
	$res = mysql_db_query($mysql_db,$query,$conID);	
	while ($i < mysql_num_fields($res)) {
		$name  = mysql_field_name  ($res, $i);
		$len[$name]   = mysql_field_len   ($res, $i);
		$flags[$name] = mysql_field_flags ($res, $i);
		$i++;
	}
	
	$i = 0;
	$sql="SHOW FIELDS FROM $table";
	$result = mysql_db_query($mysql_db,$sql,$conID);	

	while ($all_row = mysql_fetch_array($result)) {
		//解析資料庫型態
		$t1=explode(" ",$all_row[Type]);
		$t2=explode("(",$t1[0]);
		$type  = trim($t2[0]);
		
		if($type=="set" or $type=="enum"){
			$default=str_replace(",",";",substr($t2[1],0,-1));
			$default=str_replace("'","",$default);
		}else{
			$default="";
		}
		
		$name = $all_row[Field];
		$row.=form_row($i,$name,$type,$len[$name],$default);
		$i++;
	}
	
	$form_head=form_head();
	$form_foot=form_foot($table);
	$main="
	$form_head
	$row
	<input type='hidden' name='table' value='$table'>
	$form_foot
	";
	return $main;
}


function form_head(){
	global $school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	$main="
	$tool_bar
	<form action='generate.php' method='post'>
	<table border='0' cellpadding='3' cellspacing='0' bgcolor='#B3DDE3' class='small'>
	<tr bgcolor='#F3FDC3'>
	<td align='right'>使用</td>
	<td>欄位名稱</td>
	<td>欄位中文名稱</td>
	<td>資料型態</td>
	<td>表單種類</td>
	<td>預設</td>
	<td>大小</td>
	<td>最大值</td>
	</tr>
	";
	return $main;
}

function form_foot($table){
	global $conID,$mysql_db,$CONN;
	$option="";
	$query = "select * from $table";
	$res = mysql_db_query($mysql_db,$query,$conID);
	$i = 0;
	while ($i < mysql_num_fields($res)) {
		$type  = strtolower(mysql_field_type  ($res, $i));
		$name  = mysql_field_name  ($res, $i);
		$len   = mysql_field_len   ($res, $i);
		$flags = mysql_field_flags ($res, $i);
		$index_option.="<option value='$name'>$name</option>";
		$i++;
	}
	
	$index_col="<select name='sn_col' size=1>$index_option</select>";
	$main="
	<tr><td bgcolor='#F3FDC3' colspan='8'>更新、刪除的主要索引值是： $index_col</td></tr>
	<tr><td bgcolor='#F3FDC3' colspan='8'>
	檔名：<input type='text' name='module_file_name' value='index.php'>
	<input type='Submit' value='建立程式'>
	</td>
	</tr>
	</table>
	";
	return $main;
}

//每一欄資料的選項
function form_row($i,$name,$type,$len,$default){
	//檢查該型態是否是要使用 textarea
	$use_textarea=array("tinytext","text","mediumtext","longtext","tinyblob","blob","mediumblob","longblob");
	
	//取得相同表格的欄位名稱以及預設值
	$db_data=get_module_maker_col_data($_POST[table],$name);

	//根據資料型態來預設表單選項種類
	$textarea_selected=(in_array($type,$use_textarea))?"selected":"";
	$select_selected=($type=='set')?"selected":"";
	$radio_selected=($type=='enum')?"selected":"";
	
	//非 enum 以及 set 的欄位不予列出長寬
	if(in_array($type,$use_textarea)){
		// 設定 textarea 表單的長寬
		$lencol="<input type='text' size='3' name='size[$name]' value='40' class='small'> 寬";
	    $maxlencol="<input type='text' size='3' name='maxlen[$name]' value='5' class='small'> 高";
	}elseif($type=='set'){
		//如果是 set 型態資料，設定為可複選
		$lencol="<input type='hidden' name='is_multiple[$name]' value=1>";
	}elseif($type!='enum' and  $type!='set') {
		
		$llen=($len>50)?50:$len;
	
	    $lencol="<input type='text' size='3' name='size[$name]' value='$llen' class='small'>";
	    $maxlencol="<input type='text' size='3' value='$len' name='maxlen[$name]' class='small'>";
	}
	
	//處理輸入欄位以及預設值
	
	//若資料庫有值，先使用資料庫的值作為預設值
	$default_txt=(!empty($db_data[default_txt]))?$db_data[default_txt]:$default;
	
	$input_col="";
	
	//文字區域
	if(in_array($type,$use_textarea)){
		$input_col="<textarea name='default[$name]' class='small' cols='16' rows='2'>$default_txt</textarea>";	
	}elseif($type=='enum'){
		$input_col="<input type='hidden' name='default[$name]' value='$default_txt'>";
		$df=explode(";",$default_txt);
		foreach($df as $k => $v){
			$input_col.="<input type='radio' name='use_default[$name]' value='$v'>$v";
		}
	}elseif($type=='set'){
		$input_col="<input type='hidden' name='default[$name]' value='$default_txt'>";
		$df=explode(";",$default_txt);
		foreach($df as $k => $v){
			$input_col.="<input type='checkbox' name='use_default[$name][]' value='$v'>$v";
		}
	}else{
		$input_col="<input type='text' size='20' name='default[$name]' value='$default_txt' class='small'>
		<input type='checkbox' name='isfun[$name]' value='1'>函數";
	}
	
	//各種表單選項
	$op_text="<option value='text'>text 文字輸入";
	$op_textarea="<option value='textarea' $textarea_selected>textarea 文字方塊";
	$op_select="<option value='select' $select_selected>selectbox 下拉選單";
	$op_radio="<option value='radio' $radio_selected>radio 單選鈕";
	$op_checkbox="<option value='checkbox'>checkbox 複選鈕";
	$op_password="<option value='password'>password 密碼欄位";
	$op_file="<option value='file'>file 檔案欄位";
	//$op_image="<option value='image'>image 影像欄位";
	//$op_button="<option value='button'>button 一般按鈕";
	
	
	$op_hidden="<option value='hidden'>hidden 隱藏欄位";
	$op_display="<option value='display'>顯示值+隱藏欄位";
	
	
	//根據資料型態預設各種表單選項
	if(in_array($type,$use_textarea)){
		$select_option="$op_textarea";
	}elseif($type=='set'){
		$select_option="
		$op_checkbox
		";
	}elseif($type=='enum'){
		$select_option="
		$op_radio
		$op_checkbox
		";
	}else{
		$select_option="
		$op_text
		$op_password
		$op_file
		";
	}
	
	$main="
	<tr bgcolor='#FFFFFF'>
	<td valign='top' align='center'>
	<input type='Checkbox' name='use[$name]' value='1' checked>
	</td>
	<td valign='top' align='right'>	   
	$name
	<input type='hidden' name='field_name[$name]' value='$name'>
	<input type='hidden' name='field_type[$name]' value='$type'>
	</td>
	<td valign='top'>
	<input type='text' size='10' name='Cname[$name]' value='$db_data[cname]'  class='small'>
	</td>
	<td valign='top'>
	$type
	</td>
	<td valign='top'>
	<select name='input_type[$name]' class='small'>
	$select_option
	$op_select
	$op_hidden
	$op_display
	</select>
	</td>
	<td valign='top'>
	$input_col
	</td>
	<td valign='top'>
	$lencol
	</td>
	<td valign='top'>
	$maxlencol
	</td>
	</tr>";
	return $main;
}


//取得某一筆資料
function get_module_maker_col_data($table,$name){
	global $CONN;
	$sql_select="select cname,default_txt from module_maker_col where table_name='$table' and ename='$name'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($cname,$default)=$recordSet->FetchRow()){
		$theData[cname]=$cname;
		$theData[default_txt]=$default;
	}
	return $theData;
}
?>
