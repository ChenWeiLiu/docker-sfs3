<?php

// $Id: subject_setup.php 5310 2009-01-10 07:57:56Z hami $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if($act=="新增"){
	$main=add_subject($subject_name,$subject_kind,$subject_school);
	header("location: {$_SERVER['PHP_SELF']}?subject_kind=$subject_kind");
}elseif($act=="modify"){
	$main=list_subject($subject_kind,$subject_id);
}elseif($act=="儲存"){
	update_subject($subject_name,$subject_school,$subject_id);
}elseif($act=="del"){
	del_subject($subject_id);
}else{
	$main=list_subject($subject_kind,$subject_id);
}



head("設定科目");
echo $main;
foot();

//秀出所有學科領域
function list_subject($subject_kind="合科",$id=0){
	global $CONN,$school_kind_name,$school_kind_start,$school_kind_end,$school_menu_p;
	
	//取出學科
	$sql_select = "select subject_id,subject_name,subject_school  from score_subject where enable='1' and subject_kind='$subject_kind'";
	$recordSet1=$CONN->Execute($sql_select);

	while (!$recordSet1->EOF) {
		$subject_id = $recordSet1->fields["subject_id"];
		$subject_name = $recordSet1->fields["subject_name"];
		$subject_school = $recordSet1->fields["subject_school"];
		
		$checked=explode(",",$subject_school);
		$school_select="";
		$scope_school_table="";
		$scope_school_all_td="";
		
		//取得年級陣列
		$class_year_array=get_class_year_array();
		$school_kind_name_n=sizeof($class_year_array);

		//列出學校範圍選單
		while (list ($class_value, $class_name) = each ($class_year_array)) {
			$checked_result=(in_array($class_value,$checked))?"checked":"";
			$school_select.="<td style='font-size: 12px'><input type='checkbox' name='subject_school[]' value='$class_value' $checked_result><br>$class_name</td>";
			$scope_school_table.=(in_array($class_value,$checked))?"<td><img src='images/ok.png' width=16 height=14 border=0></td>":"<td></td>";
			$scope_school_all_td.="<td style='font-size: 12px'>$class_name</td>";
		}
		
		//判斷是否為修改狀態
		$subject.=($subject_id!=$id)?"
		<tr bgcolor='white'>
			<td>$subject_name</td>
			$scope_school_table
			<td align='center'><a href='{$_SERVER['PHP_SELF']}?act=modify&subject_id=$subject_id&subject_kind=$subject_kind'>修改</a></td>
			<td align='center'><a href=\"javascript:func($subject_id);\">刪除</a></td>
		</tr>
		":"
		<tr bgcolor='white'>
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
			<td><input type='text' name='subject_name' size='6' value='$subject_name'></td>
			$school_select
			<input type='hidden' name='subject_kind' value='$subject_kind'>
			<input type='hidden' name='subject_id' ' value='$subject_id'>
			<td><input type='submit' name='act' value='儲存'></td>
			<td><input type='reset' value='清除'></td>
		</form>
		</tr>
		";
		$recordSet1->MoveNext();
	}
	
	//尚未有資料時，也做一個學校年級選單
	if(empty($school_select)){
		for($i=$school_kind_start;$i<=$school_kind_end;$i++){
			$school_select.="<td style='font-size: 12px'><input type='checkbox' name='subject_school[]' value='$i'><br>$school_kind_name[$i]</td>";
			$scope_school_table.="<td></td>";
			$scope_school_all_td.="<td style='font-size: 12px'>$school_kind_name[$i]</td>";
		}
	}
	
	$tool_bar=&make_menu($school_menu_p);


	$main="
	<script language='JavaScript'>
	function func(subject_id){
	var sure = window.confirm('確定要刪除？');
	if (!sure) {
		return;
	}
	location.href=\"{$_SERVER['PHP_SELF']}?act=del&subject_kind=$subject_kind&subject_id=\" + subject_id;
	}
	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tbody>
	<tr bgcolor='#E1ECFF'>
		<td rowspan='2' align='center'>學科</td>
		<td colspan='$school_kind_name_n' align='center'>適用年級</td>
		<td  rowspan='2' align='center' colspan='2'>相關功能</td>
	</tr>
	<tr bgcolor='#E1ECFF'>$scope_school_all_td</tr>
	$subject
	<tr bgcolor='white'>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
		<td><input type='text' name='subject_name' size='6'></td>
		$school_select
		<input type='hidden' name='subject_kind' value='$subject_kind'>
		<td><input type='submit' name='act' value='新增'></td>
		<td><input type='reset' value='清除'></td>
	</form>

	</tbody>
	</table>
	";
	return $main;
}




//修改學科
function update_subject($subject_name,$school,$subject_id){
	global $CONN,$subject_kind;
	
	for($i=0;$i<sizeof($school);$i++){
		$subject_school.=$school[$i].",";
	}
	$subject_school=substr($subject_school,0,-1);

	$sql_update = "update score_subject set subject_name='$subject_name',subject_school='$subject_school' where subject_id = '$subject_id'";

	if($CONN->Execute($sql_update))	header("location: {$_SERVER['PHP_SELF']}?subject_kind=$subject_kind");
	return  false;
}


?>
