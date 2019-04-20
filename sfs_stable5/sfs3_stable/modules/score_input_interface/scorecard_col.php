<?php

// $Id: scorecard_col.php 5310 2009-01-10 07:57:56Z hami $

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
	add_col($C,$interface_sn);
	header("location: {$_SERVER['PHP_SELF']}?interface_sn=$interface_sn");
}elseif($act=="modify"){
	$main=&main_form($interface_sn,$col_sn);
}elseif($act=="update"){
	update_col($C,$col_sn,$interface_sn);
	header("location: {$_SERVER['PHP_SELF']}?interface_sn=$interface_sn");
}elseif($act=="del"){
	del_col($col_sn);
	header("location: {$_SERVER['PHP_SELF']}?interface_sn=$interface_sn");
}else{
	$main=&main_form($interface_sn,$col_sn);
}


//秀出網頁
head("成績表單輸入介面欄位設定");
echo $main;
foot();


//主要介面
function &main_form($interface_sn="",$col_sn=""){
	global $input_kind,$school_menu_p;

	$tool_bar=&make_menu($school_menu_p);

	if(empty($interface_sn)){
		//所有現存樣板
		$get_sc_list=&get_sc_list("text");
		$main="
		$tool_bar
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4><tr bgcolor='#FFFFFF'><td valign='top'>
		請選擇一個成績單樣板：<p>
		$get_sc_list
		</td></tr></table>";
		return $main;
	}

	for($i=0;$i<sizeof($input_kind);$i++){
		$select.="<option value='$input_kind[$i]'>$input_kind[$i]</option>";
	}
	$input_kind_select="<select name='C[col_type]'>$select</select>";
	$tool_bar=&make_menu($school_menu_p);

	$tmp=&get_col_setup($interface_sn,"",$col_sn);
	$main="
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr class='small' bgcolor='#E1ECFF'><td>中文標題</td><td>輸入型態</td><td>預設值</td><td>取值函式</td><td>科目相關</td><td>呼叫選填</td><td>檢查空值</td><td>功能</td></tr>
	<tr class='small' bgcolor='#EBEBEB'>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<td><input type='text' name='C[col_text]' size=14 maxlength=255></td>
	<td>$input_kind_select</td>
	<td><input type='text' name='C[col_value]' size=10 maxlength=255></td>
	<td><input type='text' name='C[col_fn]' size=10 maxlength=255></td>
	<td><select name='C[col_ss]'>
		<option value='n' selected>否</option>
		<option value='y'>是</option>
		</select></td>
	<td><select name='C[col_comment]'>
		<option value='n' selected>否</option>
		<option value='y'>是</option>
		</select></td>
	<td>
	<select name='C[col_check]'>
	<option value=0>不需要</option>
	<option value=1>要檢查</option>
	</select>
	</td>
	<td align='center'>
	<input type='hidden' name='act' value='add'>
	<input type='hidden' name='interface_sn' value='$interface_sn'>
	<input type='submit' value='新增'></td></tr>
	</form>
	".$tmp."
	</table>
	<form action='scorecard_setup.php' method='post'>
	<input type='hidden' name='interface_sn' value='$interface_sn'>
   	<input type='submit' value='將以上欄位匯出成填寫表單'>
	 </form>
	";
	return $main;
}


//所有現存欄位設定
function &get_col_setup($interface_sn="",$col_sn="",$modify_col_sn="",$enable=""){
	global $CONN,$input_kind;

	if(!empty($col_sn)){
		$where=($enable)?"and col_sn=$col_sn and enable='1'":"and col_sn=$col_sn";
	}else{
		$where=($enable)?"and enable='1'":"";
	}

	$sql_select = "select * from score_input_col where interface_sn=$interface_sn  $where";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	

	while($C=$recordSet->FetchRow()){
		$need_chk=($C[col_check])?"要檢查":"不需要";
		$ss_y=($C[col_ss]=="y")?"selected":"";
		$ss_n=($C[col_ss]=="n")?"selected":"";
		$about_ss=($C[col_ss]=="y")?"是":"否";
		$comment_y=($C[col_comment]=="y")?"selected":"";
		$comment_n=($C[col_comment]=="n")?"selected":"";
		$need_comment=($C[col_comment]=="y")?"是":"否";

		//取得欄位種類
		$input_kind_select=input_kind_select($C[col_type]);

		$mark="";
		for($i=2;$i<=$r;$i++){
			$mark.="....";
		}

		$data.=($modify_col_sn==$C[col_sn])?"
		<tr class='small' bgcolor='#FFFFFF'>
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
		<td><input type='text' name='C[col_text]' size=14 maxlength=255 value='$C[col_text]'></td>
		<td>$input_kind_select</td>
		<td><input type='text' name='C[col_value]' size=10 maxlength=255 value='$C[col_value]'></td>
		<td><input type='text' name='C[col_fn]' size=10 maxlength=255 value='$C[col_fn]'></td>
		
		<td>
		<select name='C[col_ss]'>
		<option value='n' $ss_n>否</option>
		<option value='y' $ss_y>是</option>
		</select>
		</td>
		<td>
		<select name='C[col_comment]'>
		<option value='n' $comment_n>否</option>
		<option value='y' $comment_y>是</option>
		</select>
		</td>

		<td>
		<select name='C[col_check]'>
		<option value=0>不需要</option>
		<option value=1>要檢查</option>
		</select>
		</td>
		<td>
		<input type='hidden' name='col_sn' value='$C[col_sn]'>
		<input type='hidden' name='interface_sn' value='$interface_sn'>
		<input type='hidden' name='act' value='update'>
		<input type='submit' value='儲存'></td></tr>
		</form>
		":"
		<tr class='small' bgcolor='#FFFFFF'>
		<td>$mark $C[col_text]</td>
		<td>$C[col_type]</td>
		<td>$C[col_value]</td>
		<td>$C[col_fn]</td>
		<td>$about_ss</td>
		<td>$need_comment</td>
		<td>$need_chk</td>
		<td><a href='{$_SERVER['PHP_SELF']}?act=modify&col_sn=$C[col_sn]&interface_sn=$interface_sn'>修</a> | <a href='{$_SERVER['PHP_SELF']}?act=del&col_sn=$C[col_sn]&interface_sn=$interface_sn'>刪</a></td></td>
		</tr>";
	}

	return $data;
}


//新增一個欄位設定
function add_col($C,$interface_sn){
	global $CONN;
	$sql_insert = "insert into score_input_col (interface_sn,col_text,col_value,col_type,col_fn,col_ss,col_comment,col_check,col_date,enable) values ($interface_sn,'$C[col_text]','$C[col_value]','$C[col_type]','$C[col_fn]','$C[col_ss]','$C[col_comment]','$C[col_check]',now(),'1')";
	if($CONN->Execute($sql_insert))	return mysql_insert_id();
	return  false;
}

//更新一個欄位設定
function update_col($C,$col_sn,$interface_sn){
	global $CONN;
	$sql_update = "update score_input_col set interface_sn=$interface_sn,col_text='$C[col_text]',col_value='$C[col_value]',col_type='$C[col_type]',col_fn='$C[col_fn]',col_ss='$C[col_ss]',col_comment='$C[col_comment]',col_check='$C[col_check]',col_date=now() where col_sn = '$col_sn'";
	if($CONN->Execute($sql_update))	return;
	return  false;
}

//刪除一個欄位設定
function del_col($col_sn){
	global $CONN;
	$sql_delete = "delete from score_input_col  where col_sn = '$col_sn'";
	if($CONN->Execute($sql_delete))	return;
	return  false;
}


?>
