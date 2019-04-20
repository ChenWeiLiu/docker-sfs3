<?php

// $Id: board_all.php 5310 2009-01-10 07:57:56Z hami $

if(!$is_load)  //載入檢查
	include "board_man_config.php";

//目前類別   
function prog_menu ($sql_select,$sel_id,$sel_name){
	$curr_id = $sel_id;
	$curr_name = $sel_name;
	global $conID,$$curr_id,$$curr_name,$curr_next;		
	
	$result = mysql_query ($sql_select,$conID)or die($sql_select);
	$tol_num = mysql_num_rows($result);
	if ($tol_num > 0){
		$temp_menu ="<table><form name=\"mform\" method=\"post\">
		<tr><td align=right><font size=2>總筆數:$tol_num</font></td></tr><tr><td ><select name=$sel_id  size=16 onchange=\"document.mform.submit()\">";
		while ($row = mysql_fetch_array($result)) {
			$id = $row["$sel_id"];
			$name = $row["$sel_name"];
			if ($$curr_id =="") 
				$$curr_id = $id ; //預設一筆
			if ($flag==1) {
			$curr_next = $id; $flag=0;
			} //記錄下一位
			if ($id == $$curr_id ){
				$temp_menu .="<option value=\"$id\" selected >$id--$name</option>\n";
				$$curr_name = $name;
				$flag = 1;
			}
			else
				$temp_menu .="<option value=\"$id\">$id--$name</option>\n";

		};
		$temp_menu .="</td></tr>";
	}
	else
		$temp_menu .= "<table><tr><td> </td></tr>";

	$temp_menu .= "</form></table>"; 
	
	return $temp_menu;
}

//程式項目
function prog_prog($key_prob,$sel_id){ 
	$curr_id = $sel_id;	  
	global $prob,$$curr_id;
	echo "<table align=center  bgcolor=#D0DCE0 ><tr>";
	$i =1;
	while ( list( $key, $val ) = each( $prob ) ){
	if ($key_prob == $i++)
		echo "<td bgcolor=yellow ><a href=\"$key?$curr_id=".$$curr_id."\">$val</a></td>"; 
	else
		echo "<td><a href=\"$key?$curr_id=".$$curr_id."\">$val</a></td>";   
	} 
	echo "</tr></table>";
} 
  
?>
