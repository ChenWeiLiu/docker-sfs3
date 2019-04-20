<?php

// $Id: my_fun.php 5853 2010-02-13 12:53:02Z infodaes $
//屬性處理----公  私  特
function get_type_select($my_type,$select_name){
	global $borrower_type;

	$result="<select name='$select_name'>";
	foreach($borrower_type as $key=>$value){
		$result.="<option".($key==$my_type?' selected':'')." value='$key'>$value</option>";
	}
	$result.="</select>";

	return $result;
}



function get_ooo_template($dir){

	//$dir_data=scandir($dir);
	$op = opendir($dir);
	while (false !== ($filename = readdir($op))) {
		$dir_data[] = $filename;
	}
	
	array_shift($dir_data);

	array_shift($dir_data);

	$result="<select name='ooo_template'>";

	foreach($dir_data as $value){

		if($value<>"" AND is_dir($dir."/".$value)) $result.="<option>$value</option>";		

	}

	$result.="</select>";



	return $result;

}

?>