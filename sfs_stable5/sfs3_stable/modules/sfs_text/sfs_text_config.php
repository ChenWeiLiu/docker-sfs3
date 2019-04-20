<?php

// $Id: sfs_text_config.php 6572 2011-10-07 16:29:49Z infodaes $

//載入系統設定檔
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";
include "module-upgrade.php";

$postBtn = "新增確定";
$editBtn = "修改確定";
//上層選單
$menu_p = array("st1.php"=>"學生選項","st3.php"=>"成績選項","st4.php"=>"程式模組選項","st5.php"=>"平時成績項目參照");

//get parent name
function get_text_parent_name ($t_parent) {
	global $p;
	$res="";
	$temp = explode (",",$t_parent);
	foreach ($temp as $val) {
		if($val){
			$query = "select t_id,t_name from sfs_text where t_id='$val' ";
			$result = mysql_query($query);		
			$row = mysql_fetch_row($result);				
			$res .="<a href={$_SERVER['PHP_SELF']}?this_item=$row[0]&p=$p>$row[1]</a> > ";
		}
	}
	return $res;
}

function delete_item($t_id) {
	$query = "select t_id from sfs_text where p_id='$t_id' ";
	$result = mysql_query($query);
	while($row = mysql_fetch_row($result))
		delete_item ($row[0]);
	mysql_query("delete from sfs_text where t_id='$t_id'");
}
?>
