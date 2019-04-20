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
?>
