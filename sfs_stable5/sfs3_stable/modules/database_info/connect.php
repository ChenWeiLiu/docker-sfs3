<?php

// $Id: connect.php 5310 2009-01-10 07:57:56Z hami $

include "database_info_config.php";
sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


head();
print_location();
$ado_memo = array("../../pnadodb/readme.htm"=>"ADODB »¡©ú");
$menu_p = array_merge($menu_p,$ado_memo);
print_menu($menu_p);
	$SQL = "select * from $table";
	
	$result = mysql_db_query($mysql_db,$SQL,$conID);
	
	$x = mysql_num_fields($result);
	
	include("tpl/form_head.tpl");
	echo "<input type=hidden name=table value=$table>\n";
	
	$i = 0;
	// $table = mysql_field_table($result, $i);
	while ($i < $x) 
	{
	    $type  = strtolower(mysql_field_type  ($result, $i));
	    $name  = strtolower(mysql_field_name  ($result, $i));
	    $len   = 			mysql_field_len   ($result, $i);
	    $flags = 			mysql_field_flags ($result, $i);

		include("tpl/form_row.tpl");

	    $i++;
	}

	// Read the templates
	$Dir = "styles/";	
	$handle = openDir($Dir);
	
	while ($file = readDir($handle))
	{	
		if (strchr( $file, "tpl"))
		{	$stylename = substr( $file, 0, strlen( $file)-strlen( strchr( $file, ".")));
			$styleoptions .= "<option value=$stylename>$stylename\n";
		}
	}
	closeDir();


	include("tpl/form_foot.tpl");

foot();

?>
