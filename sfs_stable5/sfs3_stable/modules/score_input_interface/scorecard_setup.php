<?php

// $Id: scorecard_setup.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include "config.php";

sfs_check();


if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


//執行動作判斷
if($act=="add"){
	add_col($C);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="update"){
	update_col($C,$interface_sn);
	header("location: {$_SERVER['PHP_SELF']}?interface_sn=$interface_sn");
}elseif($act=="view"){
	$main=view($interface_sn);
}elseif($act=="回復預設值"){
	$main=clear_html($interface_sn);
	header("location: {$_SERVER['PHP_SELF']}?interface_sn=$interface_sn");
}else{
	$main=&main_form($interface_sn);
}


//秀出網頁
head("成績單版面設定");
echo $main;
foot();


//主要介面
function &main_form($interface_sn=""){
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
		exit;
	}

	//取得該樣板資料
	$C=&get_sc($interface_sn);

	//所有現存樣板
	$get_sc_list=&get_sc_list();

	$submit=(!empty($interface_sn))?"儲存修改":"新增";
	$submit_act=(!empty($interface_sn))?"update":"add";

	
	if(empty($C[html])){
		$data=&make_templeat($interface_sn);
		$ssdata=&make_ss_templeat($interface_sn);
		$ssdata_title=&make_ss_templeat($interface_sn,"title");

		$textarea_html="<table cellspacing=0 cellpadding=0>\n<tr><td>\n<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4 width='100%'>\n$data\n</table>\n</td></tr><tr><td>\n<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>\n<tr bgcolor='#C4D9FF'>\n<td>科目</td>\n$ssdata_title\n<!--此處會自動加入下方『和科目相關欄位』的設定-->\n</table>\n</td></tr></table>";
		$ss_textarea_html=$ssdata;

	}else{
		$textarea_html=$C[html];
		$ss_textarea_html=$C[sshtml];
	}

	$show_html=&html2code($interface_sn,$textarea_html,$ss_textarea_html,"",true);


	$checked_y=($C[all_ss]=='y')?"checked":"";
	$checked_n=($C[all_ss]=='n' or empty($C[all_ss]))?"checked":"";

	$main="
	<script language=\"JavaScript\">
	function OpenWindow()
	{
	alert(\"教師填寫成績單時，按下此按鈕會出現評論的填寫輔助工具。\");
	}
	</script>
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td valign='top'>
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
		<tr class='small' bgcolor='#FFFFFF'>
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
		<td><font size=3 color='#D06030'>".$C[title]."：</font><p><font color='#865992'>".nl2br($C[text])."</font>
		</td></tr><tr class='small' bgcolor='#EBEBEB'><td>
		<p>請輸入版面標籤語法：</p>
		<textarea cols='60' rows='10' name='C[html]' class='small' style='width:100%'>".htmlspecialchars($textarea_html)."</textarea><br>
		<p>和科目相關的欄位標籤語法：（只要&lt;tr>&lt;/tr>這一段而已）</p>
		<textarea cols='60' rows='10' name='C[sshtml]' class='small' style='width:100%'>".htmlspecialchars($ss_textarea_html)."</textarea><br>
		</td></tr>
		<tr class='small' ><td valign='top'>
		成績單自動列出科目模式：<input type='radio' name='C[all_ss]' value='y' $checked_y> 列出所有分科<input type='radio' name='C[all_ss]' value='n' $checked_n>僅列出領域
		<input type='hidden' name='act' value='$submit_act'>
		<input type='hidden' name='interface_sn' value='$interface_sn'>
		<p><input type='submit' value='$submit'><input type='submit' name='act' value='回復預設值'></p>
		</td></tr>
		</form>
		</table>
	</td><td valign='top'>$get_sc_list</td></tr>
	</table><br>
	教師填寫表單預覽：<br>
	$show_html
	";
	return $main;
}

//新增一個設定
function add_col($C){
	global $CONN;
	$sql_insert = "insert into score_input_interface (title,text,html,sshtml,all_ss) values ('$C[title]','$C[text]','$C[html]','$C[sshtml]','$C[all_ss]')";
	if($CONN->Execute($sql_insert))	return mysql_insert_id();
	die($sql_insert);
	return  false;
}

//更新一個設定
function update_col($C,$interface_sn){
	global $CONN;
	$sql_update = "update score_input_interface set html='$C[html]',sshtml='$C[sshtml]',all_ss='$C[all_ss]' where interface_sn=$interface_sn";
	if($CONN->Execute($sql_update))	return;
	die($sql_update);
	return  false;
}

//清除HTML設定
function clear_html($interface_sn){
	global $CONN;
	$sql_update = "update score_input_interface set html='',sshtml='' where interface_sn=$interface_sn";
	if($CONN->Execute($sql_update))	return;
	die($sql_update);
	return  false;
}

//取得和科目無關的欄位樣板碼
function &make_templeat($interface_sn){
	global $CONN,$input_kind;

	$sql_select = "select *  from score_input_col where interface_sn=$interface_sn and col_ss='n'";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);

	while($C=$recordSet->FetchRow()){
		$template="{".$C[col_sn]."_輸入欄}";
		$data.="<tr bgcolor='white'>\n<td>$C[col_text]</td><td>$template</td>\n</tr>\n";
	}
	return $data;
}

//取得和科目有關的欄位樣板碼
function &make_ss_templeat($interface_sn,$get_what=""){
	global $CONN,$input_kind;

	$sql_select = "select *  from score_input_col where interface_sn=$interface_sn and col_ss='y'";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);

	while($C=$recordSet->FetchRow()){
		$template=($get_what=="title")?$C[col_text]:"{".$C[col_sn]."_輸入欄}";
		$data.="<td>$template</td>\n";
	}
	$main=($get_what=="title")?$data:"<tr bgcolor='white'>\n<td>{科目名稱}</td>\n$data</tr>\n";
	return $main;
}


?>
