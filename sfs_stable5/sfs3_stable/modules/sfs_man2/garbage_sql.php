<?php

// $Id: garbage_sql.php 5310 2009-01-10 07:57:56Z hami $

//設定檔載入檢查
require "config.php";
// 認證檢查
sfs_check();

//執行動作判斷
if ($_POST[act]=="還原") {
	garbage2normall($_POST[tbl_name],$_POST[have_dbname]);
	header("location: $_SERVER[PHP_SELF]");
}elseif($_POST[act]=="清空"){
	clear_garbage($_POST[tbl_name]);
	header("location: $_SERVER[PHP_SELF]");
}else{
	$main=&main_form();
}


//秀出網頁
head("資料回收桶");
echo $main;
foot();

//找出目前的垃圾資料
function &main_form(){
	global $school_menu_p,$mysql_db,$CONN;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	//取得所有表單名稱
	$result = mysql_list_tables($mysql_db);

	if (!$result) {
		user_error("無法取得資料表資料。",256);
	}
	
	//取得資料庫中所有表格
	while ($row=mysql_fetch_row($result)) {
		$all_db_tbl[]=$row[0];
	}

	$i=0;
	foreach($all_db_tbl as $db_table_name){
		
		//分割表格名稱
		$tbl=explode("_",$db_table_name);
		
		if($tbl[0]=="garbage"){
			
			$tblname=substr($db_table_name,19);
			
			//是否表格中已有該表新表
			$have_dbname[$db_table_name]=(in_array($tblname,$all_db_tbl))?"有":"無";
			
			
			$del_time=date("Y-m-d H:i:s（星期 w）",$tbl[1]);
			
			$color=($_GET[vDBname]==$db_table_name)?"#FFFF10":"white";
			
			$recordSet=$CONN->Execute("SELECT count(*) FROM $db_table_name");
			list($num_rows) =$recordSet->FetchRow();

			$option.="<tr bgcolor='$color'><td>
			<input type='checkbox' name='tbl_name[]' id='d$i' value='$db_table_name'>
			<a href='$_SERVER[PHP_SELF]?vDBname=$db_table_name'>$tblname</a></td><td>$num_rows</td><td>$del_time
			</td><td>$have_dbname[$db_table_name]</td></tr>";
			$i++;
		}

	}

	mysql_free_result($result);
	
	if(!empty($_GET[vDBname])){
		$sql_select="select * from $_GET[vDBname]";
		$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
		while($datan=$recordSet->FetchRow()){
		$DBdata="";
			foreach($datan as $k=>$v){
				if(is_int($k))continue;
				$DBdata.="$v ";
			}
			$allDB.="$DBdata\n";
		}
		$DBall="<tr>
		<td valign='top' colspan=4>
		<textarea  cols='30' rows='6' class='small' style='width: 100%'>$allDB</textarea>
		</td></tr>";
	}
	
	if(empty($option))return "$tool_bar<table  width=350 cellspacing='1' cellpadding='4' bgcolor='#2653A7'>
	<tr bgcolor='#ffffff'><td>
	目前回收桶中無任何資料
	</td></tr></table>";
	
	$main="
	<script>
	<!--
	function sel_all() {
		var i =0;

		while (i < document.dbform.elements.length)  {
			a=document.dbform.elements[i].id.substr(0,1);
			if (a=='d') {
				document.dbform.elements[i].checked=true;
			}
			i++;
		}
	}
	-->
	</script>
	$tool_bar
	<table cellspacing='1' cellpadding='4' bgcolor='#2653A7'>
	<form action='$_SERVER[PHP_SELF]' method='POST' name='dbform'>   
	<tr bgcolor='#C4DCF8'><td>資料表名稱</td><td>資料數</td><td>移除時間</td><td>新表</td></tr>
	$option
	$DBall
	</table>
	<br>
	<input type='hidden' name='have_dbname' value='$have_dbname'>
	<input type='button' value='全選' OnClick='sel_all();'>
	<input type='submit' name='act' value='還原'>
	<input type='submit' name='act' value='清空'>
 	</form>
	";
	return $main;
}

//還原
function garbage2normall($tbl_array=array(),$have_dbname=array()){
	global $school_menu_p,$mysql_db,$CONN;
	
	foreach($tbl_array as $tbl_name){
		//原來的名稱
		$new_dbname=substr($tbl_name,19);
		
		//看看現在資料庫由無同名資料表
		if($have_dbname[$tbl_name]=="有"){
			user_error("資料庫中已有相同名稱的資料表 $new_dbname ，需移除之才能還原。",256);
		}else{
			chang_dbname($tbl_name,$new_dbname);
		}
	}
}

//清空
function clear_garbage($tbl_array=array()){
	global $CONN;
	foreach($tbl_array as $tbl_name){
		$str="DROP TABLE IF EXISTS $tbl_name";
		$CONN->Execute($str) or user_error($str, 256);
	}
}
?>
