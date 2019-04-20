<?php

// $Id: form_admin.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if($_POST['act']=='開始複製')
{
	$old_ofsn=$_POST['item'];
	if($old_ofsn)
	{
		//開始複製項目
		$sql_select="select * from form_all where ofsn=$old_ofsn";
		$res=$CONN->Execute($sql_select) or die($sql_select);
		if($res->recordCount()){
			$of_title=date('Y-m-d h:i:s').'複製-'.$res->fields[of_title];
			//$of_start_date=$res->fields[of_start_date];
			//$of_dead_line=$res->fields[of_dead_line];
			$of_text=$res->fields[of_text];
			$of_who=$res->fields[of_who];
			$teacher_sn=$_SESSION['session_tea_sn'];
			$of_communication=$res->fields[of_communication];			
			
			$sql_select="INSERT INTO form_all SET of_title='$of_title',of_start_date=CURDATE(),of_dead_line=CURDATE(),of_text='$of_text',of_who='$of_who',teacher_sn='$teacher_sn',of_communication='$of_communication',of_date=NOW(),enable=0";
			$res=$CONN->Execute($sql_select) or die($sql_select);
		}	
		//開始複製細目
		$sql_select="select ofsn from form_all where of_title='$of_title'";
		$res=$CONN->Execute($sql_select) or die($sql_select);
		$ofsn=$res->fields[ofsn];
		
		$sql_select="select * from form_col where ofsn=$old_ofsn";
		$res=$CONN->Execute($sql_select) or die($sql_select);
		if($res->recordcount()){
			$insert_sql.="INSERT INTO form_col(ofsn,col_title,col_text,col_dataType,col_value,col_chk,col_function,col_sort) VALUES ";
			while(!$res->EOF) {
				$col_title=$res->fields[col_title];
				$col_text=$res->fields[col_text];
				$col_dataType=$res->fields[col_dataType];
				$col_value=$res->fields[col_value];
				$col_chk=$res->fields[col_chk];
				$col_function=$res->fields[col_function];
				$col_sort=$res->fields[col_sort];
				$insert_sql.="($ofsn,'$col_title','$col_text','$col_dataType','$col_value','$col_chk','$col_function','$col_sort'),";
				$res->MoveNext();
			}
			$insert_sql=substr($insert_sql,0,-1);
			$CONN->Execute($insert_sql) or die($insert_sql);
		}
		//轉移到編輯畫面  form_admin.php?act=edit_form&ofsn=$ofsn
		header("location:form_admin.php");
	}
}


//秀出網頁
head("線上填報-複製");
echo print_menu($school_menu_p);
$main="<form name='form_item' method='post' action='$_SERVER[PHP_SELF]'>";

//擷取既有項目
$sql_select="select * from form_all order by ofsn desc limit 100";
$res=$CONN->Execute($sql_select) or die($sql_select);
while(!$res->EOF) {
	$ofsn=$res->fields[ofsn];
	$of_title=$res->fields[of_title];
	$of_start_date=$res->fields[of_start_date];
	$of_dead_line=$res->fields[of_dead_line];
	$main.="<input type='radio' value='$ofsn' name='item'>[ $of_start_date ~ $of_dead_line ] $of_title<BR>";
	$res->MoveNext();
}

$main.="<BR><input type='submit' name='act' value='開始複製' onclick='return confirm(\"確定要複製？\")'></form>";
echo $main;
foot();



?>
