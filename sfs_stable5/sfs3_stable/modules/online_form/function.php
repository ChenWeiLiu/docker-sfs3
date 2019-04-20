<?php

// $Id: function.php 7848 2014-01-10 01:02:25Z infodaes $

//進入某一填報表單
function &view_form($ofsn,$mode="add"){
	global $CONN,$SD,$ESN;
	//找出填報資訊
	$str="select * from form_all where ofsn=$ofsn";
	$recordSet=$CONN->Execute($str)or die($str);
	$f=$recordSet->FetchRow();

	$title=$f[of_title];
	$man=get_teacher_name($f[teacher_sn]);
	//取得該填報資料的附加檔
	$allfile =& getFormFile("ofsn",$ofsn);


	$data1="
	<tr bgcolor='#FFFFFF'><td colspan='2'>說明：".nl2br($f[of_text])."</td></tr>
	<tr><td bgcolor='#E3F3FD' class='small'>
	填報開始日期： $f[of_start_date]<br>
	填報截止日期： $f[of_dead_line]
	</td>
	<td bgcolor='#E3F3FD' class='small'>
	調查者：".$man."<br>
	啟用時間：".$f[of_date]."<br>
	</td></tr>
	<tr bgcolor='#FFFFFF'><td colspan='2' class='small'>附件： $allfile</td></tr>
	";

	//找出填報欄位
	if($mode=="modify"){
		$data2=&get_ofsn_col($ofsn,$_SESSION['session_tea_sn']);
		$str ="SELECT schfi_sn FROM form_fill_in WHERE ofsn=$ofsn and teacher_sn={$_SESSION['session_tea_sn']}";
		$recordSet=$CONN->Execute($str)or die($str);
		list($schfi_sn)=$recordSet->FetchRow();

		$hidden="<input type='hidden' name='schfi_sn' value='$schfi_sn'>";
		$actv="update_in";
	}else{
		$data2=&get_ofsn_col($ofsn);
		$hidden="";
		$actv="sign_in";
	}

	if(empty($_SESSION['session_tea_sn'])){
		$send="";
	}else{
		$send="<input type='submit' value='送出'>";
	}

	$main="
	<div align='center'><font color='#A23B32' size=5>$title</font></div>
	<table align='center' cellpadding=4  cellspacing=1 bgcolor='#CDD5FF'  width='96%'>
	$data1
	</table>

	<table align='center' cellpadding=4  cellspacing=1 bgcolor='#1E3B89' width='96%'>
	 <tr bgcolor='#F7F7F7'><td>
		<table width='100%' cellpadding=4  cellspacing=0>
		<form action='{$_SERVER['PHP_SELF']}' method='POST' enctype='multipart/form-data'><tbody>
		$data2
		</table>
	</td></tr>	</table>
	<br>

	<input type='hidden' name='teacher_sn' value='{$_SESSION['session_tea_sn']}'>
	<input type='hidden' name='ofsn' value='$ofsn'>
	$hidden
	<input type='hidden' name='act' value='$actv'>
	<center>$send</center>
	</form>";
	return $main;
}

//找出填報欄位
function &get_ofsn_col($ofsn,$teacher_sn=0){
	global $CONN;
	$str="select * from form_col where ofsn=$ofsn order by col_sort";
	$recordSet=$CONN->Execute($str)or die($str);
	$n=1;
	while($c=$recordSet->FetchRow()){

		if(!empty($teacher_sn)){
			$v=get_someone_value($teacher_sn,$c[col_sn]);
		}

		$note=(!empty($c[col_text]))?"<font color='blue' size=2>（".$c[col_text]."）</font>":"";

		if($c[col_dataType]=="bool" && !empty($c[col_value])){

			$cv=explode(";",$c[col_value]);
			$c_form="";
			for($i=0;$i<sizeof($cv);$i++){
				$checked=(!empty($v) and $cv[$i]==$v)?"checked":"";
				$c_form.="<input type='radio' name='col[".$c[col_sn]."]' value='$cv[$i]' $checked>$cv[$i]<br>";
			}
			$col_form=$c_form;

		}
		elseif($c[col_dataType]=='file'){
			$col_form="<input type='file' name='col_".$c[col_sn]."' >";
		}
		else{

			$sign_v=(empty($v))?$c[col_value]:$v;
			$col_form="<input type='text' name='col[".$c[col_sn]."]' value='$sign_v'>";
		}

		$data2.="<tr bgcolor='#E5E5E5'><td valign='top' align='center'>($n)</td><td valign='top'>$c[col_title]<br>$note</td></tr>
		<tr><td></td><td valign='top'>$col_form</td></tr>
		";
		$n++;
	}
	return $data2;
}




//取得某一學校對某一題的答案
function get_someone_value($teacher_sn,$col_sn){
	global $CONN;
	$sql_select="select value from form_value where col_sn=$col_sn and teacher_sn=$teacher_sn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	list($value)=$recordSet->FetchRow();
	return $value;
}

//取得某一學校填報時間
function get_someone_time($teacher_sn,$ofsn){
	global $CONN;
	$sql_select="select  fill_time from form_fill_in where ofsn=$ofsn and teacher_sn=$teacher_sn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	list($value)=$recordSet->FetchRow();
	return $value;
}

//取得某一欄位所有人答案的分類次數
function get_someone_value_count($col_sn){
	global $CONN;
	$sql_select="select value,count(value_sn) from form_value where col_sn=$col_sn group by value";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while(list($v,$c)=$recordSet->FetchRow()){
		$value.=$v."：".$c."<br>";
	}
	return $value;
}



//取得某一欄位所有人答案的加總
function get_someone_value_sum($col_sn){
	global $CONN;
	$sql_select="select sum(value) from form_value where col_sn=$col_sn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	list($value)=$recordSet->FetchRow();
	return "共計：".$value;
}

//取得某一欄位所有人答案的平均
function get_someone_value_avg($col_sn){
	global $CONN;
	$sql_select="select count(value_sn),sum(value) from form_value where col_sn=$col_sn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	list($c,$v)=$recordSet->FetchRow();
	if(empty($c))return "平均：0";
	$value=$v."/".$c."=".round($v/$c,2);
	return "平均：".$value;
}
?>