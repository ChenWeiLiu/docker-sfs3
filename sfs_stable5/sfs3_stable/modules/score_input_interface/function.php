<?php

// $Id: function.php 5310 2009-01-10 07:57:56Z hami $

//取得欄位名稱
function get_col_name($col_sn=""){
	global $CONN,$input_kind;
	$sql_select = "select col_text from score_input_col where col_sn='$col_sn'";
	$recordSet=$CONN->Execute($sql_select);
	list($col_text)=$recordSet->FetchRow();

	return $col_text;
}



//取得類型選單
function input_kind_select($kind=""){
	global $input_kind;
	for($i=0;$i<sizeof($input_kind);$i++){
		$selected=($kind==$input_kind[$i])?"selected":"";
		$select.="<option value='$input_kind[$i]' $selected>$input_kind[$i]</option>";
	}
	$input_kind_select="<select name='C[col_type]'>$select</select>";
	return $input_kind_select;
}




//把目前有的欄位語法全部列出
function get_col_list($interface_sn=""){
	global $CONN,$input_kind;

	if(empty($interface_sn))return get_sc_list();

	for($i=0;$i<sizeof($input_kind);$i++){
		$select.="<option value='$input_kind[$i]'>$input_kind[$i]</option>";
	}
	$input_kind_select="<select name='C[col_type]'>$select</select>";

	$sql_select = "select *  from score_input_col where interface_sn=$interface_sn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);


	while($C=$recordSet->FetchRow()){
		$need_chk=($C[col_check])?"要檢查":"不需要";

		$level_name=(!empty($C[col_level]))?get_col_name($C[col_level]):"";
		
		$mark="";
		for($i=2;$i<=$r;$i++){
			$mark.="....";
		}
		$form=&get_col_html($C[col_sn],$C[col_type],$C[col_value]);

		$data.="
		<tr class='small' bgcolor='#FFFFFF'>
		<td>$mark $C[col_text]</td>
		<td>$form</td>
		<td>$C[col_fn]</td>
		<td>$need_chk</td>
		<td><a href='index.php?act=modify&col_sn=$C[col_sn]'>修改</a></td>
		</tr>";
	}

	return $data;
}




?>
