<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include "config.php";

sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
	foreach ( $_POST as $keyinit => $valueinit) {
		$$keyinit = $valueinit;
	}
	foreach ( $_GET as $keyinit => $valueinit) {
		$$keyinit = $valueinit;
	}
}


//執行動作判斷
if($act=="add"){
	$interface_sn=add_col($C);
	header("location: scorecard_col.php?interface_sn=$interface_sn");
}elseif($act=="update"){
	update_col($C,$interface_sn);
	header("location: scorecard_col.php?interface_sn=$interface_sn");
}elseif($act=="view"){
	$main=view($interface_sn);
}else{
	$main=main_form($interface_sn);
}


//秀出網頁
head("成績單版面設定");
echo $main;
foot();


//主要介面
function main_form($interface_sn=""){
	global $input_kind,$PHP_SELF,$school_menu_p;

	if(!empty($interface_sn)){
		$C=&get_sc($interface_sn);
	}
	
	$tool_bar=&make_menu($school_menu_p);

	$get_sc_list=&get_sc_list();
	$submit=(!empty($interface_sn))?"儲存修改":"新增";
	$submit_act=(!empty($interface_sn))?"update":"add";

	$main="
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td valign='top'>
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
		<tr class='small' bgcolor='#EBEBEB'>
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
		<td>請替此成績單樣板命名：<input type='text' name='C[title]' size=14 maxlength=255 value='$C[title]'>
		<p>請輸入相關說明或文字：</p>
		<textarea cols='60' rows='2' name='C[text]'>$C[text]</textarea><br>

		<input type='hidden' name='act' value='$submit_act'>
		<input type='hidden' name='interface_sn' value='$interface_sn'>
		<p><input type='submit' value='$submit'></p>
		</td></tr>
		</form>
		</table>
	</td><td valign='top'>$get_sc_list</td></tr>
	</table>
	";
	return $main;
}

//新增一個設定
function add_col($C){
	global $CONN;
	$sql_insert = "insert into score_input_interface (title,text) values ('$C[title]','$C[text]')";
	if($CONN->Execute($sql_insert))	return mysql_insert_id();
	die($sql_insert);
	return  false;
}

//更新一個設定
function update_col($C,$interface_sn){
	global $CONN;
	$sql_update = "update score_input_interface set title='$C[title]',text='$C[text]' where interface_sn=$interface_sn";
	if($CONN->Execute($sql_update))	return $interface_sn;
	die($sql_update);
	return  false;
}


?>
