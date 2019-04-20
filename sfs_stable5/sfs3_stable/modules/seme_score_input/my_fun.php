<?php
// $Id: my_fun.php 5310 2009-01-10 07:57:56Z hami $

function file_menu($path_name="",$id="",$s_name="file_name",$ff_name="",$ext_name="") {
	global $CONN;

	$file_arr=array();
	$flen=strlen($ff_name);
	$elen=strlen($ext_name);
	$fp=opendir($path_name);
	while(gettype($file=readdir($fp))!=boolean){
		if (is_file("$path_name/$file")) {
			if (($ff_name=="" || substr($file,0,$flen)==$ff_name) && ($ext_name=="" || substr($file,($elen*(-1)),$elen)==$ext_name)){
				$file_arr[$file]=$file;
			}
		}
	}
	closedir($fp);
	
	$obj = new drop_select();
	$obj->s_name = $s_name;
	$obj->top_option = "選擇檔案";
	$obj->id = $id;
	$obj->arr = $file_arr;
	$obj->is_submit = true;
	return $obj->get_select();
}

function subj_menu($class=array()) {
	$obj = new drop_select();
	$obj->s_name = "sel_subj[xxx]";
	$obj->top_option = "選擇匯入科目";
	$obj->arr = $class;
	$obj->is_submit = false;
	return $obj->get_select();
}
?>
