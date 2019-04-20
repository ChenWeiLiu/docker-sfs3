<?php

// $Id: school_room.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "school_base_config.php";

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
	update_all_room($room_name,$room_tel,$room_fax);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif ($act=="新增確定") {
	add_room($room_name,$room_tel,$room_fax);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif ($act=="delete") {
	del_room($room_id);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif ($act=="修改模式") {
	$main=&room_setup_form("edit");
}elseif ($act=="新增處室") {
	$main=&room_setup_form("add");
}else{
	$main=&room_setup_form();
}

//秀出網頁
head("處室設定");
echo $main;
foot();

//觀看各處室表單
function &room_setup_form($mode=""){
	global $CONN,$school_menu_p;
	
	$view_button="<input type=submit name='act' value='瀏覽模式'>";
	$add_button="<input type=submit name='act' value='新增處室'>";
	$modify_button="<input type=submit name='act' value='修改模式'>";
	$modify_submit_button="<input type='submit' name='act' value='修改確定'>";


	if ($mode=="edit"){
		$b0="$view_button $add_button $modify_submit_button";
		$b1="$modify_submit_button";
	}elseif($mode=="add"){
		$b0="$view_button $modify_button";
		$function_name="<td align='center'>動作</td>";
		$add_form="<tr class='title_mbody'>
		<td></td>
		<td><input type='text' size='20' maxlength='30' name='room_name'></td>
		<td align='center' ><input type='text' size='20' maxlength='15' name='room_tel'></td>
		<td align='center' ><input type='text' size='15'  name='room_fax'></td>
		<td><input type='submit' name='act' value='新增確定'></td>
		</tr>";
	}else{
		$b0="$view_button $add_button $modify_button";
		$function_name="<td align='center'>動作</td>";
	}
	
	$button0="<tr  class='title_sbody2'><td colspan='5'>$b0</td></tr>";
	$button1=(!empty($b1))?"<tr  class='title_sbody2'><td colspan='5'>$b1</td></tr>":$button0;

	//讀取資料
	$sql_select = "select room_id,room_name,room_tel,room_fax from school_room where enable='1'";
	$result = $CONN->Execute ($sql_select) or die($sql_select) ;
	while (!$result->EOF) {
		$room_id = $result->fields["room_id"];
		$room_name = $result->fields["room_name"];
		$room_tel = $result->fields["room_tel"];
		$room_fax = $result->fields["room_fax"];
		$ti = ($i++%2)+1;


		$room=($mode=="edit")?
		"<td><input type='text' size='30' maxlength='30' name='room_name[$room_id]' value='$room_name'></td>
		<td align='center'><input type='text' size='15' maxlength='20' name='room_tel[$room_id]' value='$room_tel'></td>
		<td align='center'><input type='text' size='15' maxlength='20' name='room_fax[$room_id]' value='$room_fax'></td>
		":"
		<td>$room_name</td>
		<td align='center'>$room_tel</td>
		<td align='center'>$room_fax</td>
		<td align='center'>
		<a href='{$_SERVER['PHP_SELF']}?act=delete&room_id=$room_id' onClick=\"return confirm('確定刪除 $room_name 記錄？');\">刪除</a>
		</td>
		";

		$room_data.="
		<tr class=nom_$ti><td align='center'>$room_id</td>
		$room
		</tr>";

		$result->MoveNext();
	}


	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='4' class='main_body'>
	<form name ='myform' action='{$_SERVER['PHP_SELF']}' method='post'>
	$button0
	<tr class='title_mbody'>
	<td  nowrap>編號</td>
	<td  nowrap>處室名稱</td>
	<td >電話</td>
	<td >傳真</td>
	$function_name
	</tr>
	$add_form
	$room_data
	$button1
	</table>
	</form>
	";

	return $main;
}


//刪除一個課室
function real_del_room($room_id){
	global $CONN;
	$query = "delete from school_room where room_id ='$room_id'";
	$CONN->Execute($query);
	return ;
}

//隱藏一個課室
function del_room($room_id){
	global $CONN;
	$sql_update = "update school_room set enable='0' where room_id='$room_id'";
	$CONN->Execute ($sql_update);
	return ;
}

//新增一個課室
function add_room($room_name,$room_tel,$room_fax){
	global $CONN;
	$sql_insert = "insert into school_room(room_name,room_tel,room_fax) values ('$room_name','$room_tel','$room_fax')";
	$CONN->Execute($sql_insert);
	return ;
}

//修改一個課室
function update_room($room_id,$room_name,$room_tel,$room_fax){
	global $CONN;
	$sql_update = "update school_room set room_name='$room_name',room_tel='$room_tel',room_fax='$room_fax' where room_id=$room_id";
	$CONN->Execute($sql_update);
	return ;
}

//修改所有課室
function update_all_room($room_name,$room_tel,$room_fax){
	global $CONN;
	while(list($room_id,$name)=each($room_name)){
		update_room($room_id,$name,$room_tel[$room_id],$room_fax[$room_id]);
	}
	return ;
}
?>


