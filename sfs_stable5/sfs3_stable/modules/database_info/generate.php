<?php

// $Id: generate.php 5310 2009-01-10 07:57:56Z hami $

include "database_info_config.php";
sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
if (!$is_view_source)
	ob_start();
if ($styles =="sfs3") {
print "<?php \n";
print "// \$Id\$ \n";
print "\n";
print "// 載入設定檔\n";
print "include \""."config.php\";\n";
print "// 認證檢查\n";
print "// sfs_check();\n";
print "// 印出頁頭\n";
print "head();\n";
print "// 欄位資訊\n";
print "$" . "field_data = get_field_info(\"$table\")" . chr(59) ."\n";

print "// 模組選單\n";
print "//print_menu($"."menu_p);\n";

print "?> \n";
}
?>

<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 CLASS="tableBg" WIDTH="100%" ALIGN="CENTER"> 
<TR>
<TD >
<form name="myform" action="<?php echo "<"."?php echo $"."_SERVER['"."PHP_SELF'] ?>" ?>" method="post">
  <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body" >
<?php

$sql_insert = "insert into $table (";
$sql_update = "update $table set ";
$sql_select = "select * from $table";

$x=0;
while ($x<count($field_name))		// Go through array
{	

	// Beginn php-code for output
	$mysql_query_s 	= "$" . "recordSet = $"."CONN->Execute($" . sql_select . ")" . chr(59) . "\n";
	$mysql_query_u 	= "// Update: \n" . "$" . "recordSet = $"."CONN->Execute($" . sql_update  . ")" . chr(59) . "\n";
	$mysql_query_i 	= "// Insert: \n" . "$" . "recordSet = $"."CONN->Execute($" . sql_insert . ")" . chr(59) . "\n";
	$while_result	= "while (!$"."recordSet->EOF) {\n";

	if ($use[$x]==1)				// Only if used
	{	
		$template = "styles/" . $styles . ".tpl";	// Name of the template
		include ($template);		// Include	

		// Insert SQL-Statements 
		$sql_insert_fields .= $field_name[$x] . ",";
		if ($field_type[$x] != "int")
			$sql_insert_values .="'". "$"."_POST[" . $field_name[$x] . "]',";
		else
			$sql_insert_values .= "$" ."_POST[". $field_name[$x] . "],";

		// Update SQL-Statements 
		if ($field_type[$x] != "int")
			$sql_update .= $field_name[$x] . "='"."$"."_POST[". $field_name[$x] . "]',";
		else
			$sql_update .= $field_name[$x] . "="."$"."_POST[". $field_name[$x] . "],";

		// php/mysql - while
		$while_row .= "\t$" . $field_name[$x] . " = " . "$" . "recordSet->fields[\"" . $field_name[$x] . "\"]" . chr(59) . "\n";

	}
	$x++;
}

// Quick hack to delete last comma
$sql_insert_fields 	= substr($sql_insert_fields,0,strlen($sql_insert_fields)-1);
$sql_insert_values 	= substr($sql_insert_values,0,strlen($sql_insert_values)-1);
$sql_update 		= substr($sql_update,0,strlen($sql_update)-1);


// rest
$sql_insert			= $sql_insert . $sql_insert_fields . ") values (" . $sql_insert_values . ")";
$sql_select			= ereg_replace ("\*",$sql_insert_fields,$sql_select);

// if "where" is set
if ($where)
{	$sql_update .= stripslashes($where);
	$sql_select .= stripslashes($where);
}

?>
</table>
</FORM>
</TD>
</TR>
</TABLE>
<?php
if ($styles =="sfs3") {
print "<?php \n";
print "//印出頁尾\n";
print "foot();\n";
print "?> \n";
}
?>
<pre>

<?php 
// Output
echo "This can be used at the top of the form. It will fill the form:\n\n";
echo "$" . "sql_select = " . "\"$sql_select\"" . chr(59) . "\n";
echo "$" . "sql_insert = " . "\"$sql_insert\"" . chr(59) . "\n";
echo "$" . "sql_update = " . "\"$sql_update\"" . chr(59) . "\n\n\n";
//echo "$mysql_connect\n";
//echo "$mysql_query_s\n";
echo "$while_result\n";
echo "$while_row\n	$"."recordSet->MoveNext()". chr(59)."\n}" . chr(59) . "\n";
echo "$mysql_query_u\n";
echo "$mysql_query_i\n";

?>
</pre>

<?php
if (!$is_view_source){
	$t = ob_get_contents();
	ob_end_clean();
	highlight_string($t);
}
?>
