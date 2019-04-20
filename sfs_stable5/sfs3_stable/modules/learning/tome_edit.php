<?php

// $Id: tome_edit.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "config.php";
$unit_m=$m;
// 認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if ($act == "修改確定") {
	update_all_room($room_name,$room_tel,$room_fax,$room_t);
	header("location:index.php?m=$m ");
}elseif ($act=="回上頁") {
	header("location: index.php?m=$m");
}elseif ($act=="新增確定") {
	add_room($room_name,$room_tel,$room_fax,$room_t);
	header("location: index.php?m=$m");
}elseif ($act=="delete") {
	del_room($room_id);
	header("location: {index.php?m=$m");
}elseif ($act=="修改") {
	$main=&room_setup_form("edit");
}elseif ($act=="新增") {
	$main=&room_setup_form("add");
}else{
	$main=&room_setup_form();
}

//秀出網頁
include "header_u.php";
echo $main;


//觀看各處室表單
function &room_setup_form($mode=""){
	global $CONN,$unit_m;
	
	$view_button="<input type=submit name='act' value='回上頁'><input type=submit name='act' value='瀏覽模式'>";
	$add_button="<input type=submit name='act' value='新增'>";
	$modify_button="<input type=submit name='act' value='修改'>";
	$modify_submit_button="<input type='submit' name='act' value='修改確定'>";


	if ($mode=="edit"){
		$b0="$view_button $add_button $modify_submit_button";
		$b1="$modify_submit_button";
	}elseif($mode=="add"){
		$b0="$view_button $modify_button";
		$function_name="<td align='center'>動作</td>";
		$add_form="<tr class='title_mbody'>
		<td><input type='text' size='10' maxlength='10' name='room_t'></td>
		<td><input type='text' size='20' maxlength='30' name='room_name'></td>
		<td align='center' ><input type='text' size='20' maxlength='15' name='room_tel'></td>
		<td align='center' ><input type='text' size='15'  name='room_fax'></td>

		<td><input type='submit' name='act' value='新增確定'></td>
		
		</tr>";
	}else{
		$b0="$view_button $add_button $modify_button";
		
	}
	
	$button0="<tr  class='title_sbody2'><td colspan='5'>$b0</td></tr>";
	$button1=(!empty($b1))?"<tr  class='title_sbody2'><td colspan='5'>$b1</td></tr>":$button0;

	//讀取資料
	$sql_select = "select * from unit_tome where unit_m='$unit_m' order by seme,unit_t";
	$result = $CONN->Execute ($sql_select) or die($sql_select) ;
	while (!$result->EOF) {
		$room_id = $result->fields["ut_id"];
		$room_t = $result->fields["unit_t"];
		$room_name = $result->fields["unit_tome"];
		$room_tel = $result->fields["tome_ver"];
		$room_fax = $result->fields["seme"];
		$ti = ($i++%2)+1;


		$room=($mode=="edit")?
		"<td><input type='text' size='30' maxlength='30' name='room_t[$room_id]' value='$room_t'></td>
		<td><input type='text' size='30' maxlength='30' name='room_name[$room_id]' value='$room_name'></td>
		<td align='center'><input type='text' size='15' maxlength='20' name='room_tel[$room_id]' value='$room_tel'></td>
		<td align='center'><input type='text' size='15' maxlength='20' name='room_fax[$room_id]' value='$room_fax'></td>
		":"
		<td>$room_t</td>
		<td>$room_name</td>
		<td align='center'>$room_tel</td>
		<td align='center'>$room_fax</td>
		
		";

		$room_data.="
		<tr class=nom_$ti>
		$room
		</tr>";

		$result->MoveNext();
	}


	//相關功能表
	

	$main="
	
	<table border='1' cellPadding='3' cellSpacing='0' class='main_body'>
	<form name ='myform' action='{$_SERVER['PHP_SELF']}' method='post'>
	$button0
	<tr class='title_mbody'>
	<td  nowrap>代號</td>
	<td  nowrap>學期(冊別)</td>
	<td >版本(若要隱藏，請將版本清空即可)</td>
	<td >學期排序</td>
	
	</tr>
	$add_form
	$room_data
	$button1
	</table>
	<input type='hidden' name='m' value= $unit_m >
	</form>
	";

	return $main;
}


//刪除一個課室
function real_del_room($room_id){
	global $CONN;
	$query = "delete from unit_tome where ut_id ='$ut_id'";
	$CONN->Execute($query);
	return ;
}

//隱藏一個課室
function del_room($room_id){
	global $CONN;
	$sql_update = "update unit_tome set enable='0' where ut_id ='$ut_id'";
	$CONN->Execute ($sql_update);
	return ;
}

//新增一個課室
function add_room($room_name,$room_tel,$room_fax,$room_t){
	global $CONN,$unit_m;
	$sql_insert = "insert into unit_tome (unit_t,unit_tome,tome_ver,seme,unit_m) values ('$room_t','$room_name','$room_tel','$room_fax','$unit_m')";
	
	$CONN->Execute($sql_insert);
	return ;
}

//修改一個課室
function update_room($room_id,$room_name,$room_tel,$room_fax,$room_t){
	global $CONN,$unit_m;
	$sql_update = "update unit_tome set unit_t='$room_t',unit_tome='$room_name',tome_ver='$room_tel',seme='$room_fax' where ut_id=$room_id";
	
	 $CONN->Execute($sql_update);
	return ;
}

//修改所有課室
function update_all_room($room_name,$room_tel,$room_fax,$room_t){
	global $CONN,$unit_m;
	while(list($room_id,$name)=each($room_name)){
		update_room($room_id,$name,$room_tel[$room_id],$room_fax[$room_id],$room_t[$room_id]);
	}
	return ;
}
?>


