<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

//載入班級設定
include_once "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

sfs_check();

$act=$_POST[act];
$name=$_POST[name];

//執行動作判斷
if($act=="列出全校資料"){
	$result=find_data("","all");
}elseif($act=="查詢"){
	$result=find_data($name);
}
if ($_POST[sel]){
	$result=chg_form($_POST[sel]);
}
$main=pre_form();

//秀出網頁
head("教師密碼查詢");
echo $main;
echo $result;
foot();

//搜尋介面
function pre_form(){
	$main="
	<script>
	<!--
	function sort_kind(a){
		document.pass_form.sort.value=a;
		document.pass_form.act.value='列出全校資料';
		document.pass_form.submit();
	}
	function act_kind(a){
		document.pass_form.act.value=a;
		document.pass_form.submit();
	}
	function sel_act(a){
		document.repass_form.act.value=a;
		document.repass_form.submit();
	}
	//-->
	</script>
	<table>
	<form name='pass_form' action='{$_SERVER['PHP_SELF']}' method='post'>
	<tr><td><input type='button' value='列出全校資料' OnClick=\"act_kind('列出全校資料')\"></td></tr>
	<tr><td>
	輸入教師姓名：
	<input type='text' name='name' size='10'><input type='button' value='查詢' OnClick=\"act_kind('查詢')\"><input type='hidden' name='sort' value=''><input type='hidden' name='act' value=''></td></tr>
	</form>
	</table>";
	return $main;
}

//搜尋資料
function find_data($name="",$mode=""){
	global $CONN,$sort;
	$post_office_p = room_kind();
	$class_name = class_base();
	if ($mode=="all") {
		$wherestr = " order by";
		switch($sort) {
			case "post" :
				$wherestr .= " b.post_office, b.post_kind,";
			break;
			case "title" :
				$wherestr .= " d.teach_title_id,";
			break;
			case "name" :
				$wherestr .= " a.name,";
			break;
			default :
			break;
		}
		$wherestr .= " b.teach_title_id, b.class_num";
	} else {
		$wherestr = " and a.name='$name'";
	}
	$query="
	SELECT a.teacher_sn,a.teach_id,a.name,a.login_pass, b.post_kind, b.post_office, d.title_name ,b.class_num 
	FROM teacher_base a , teacher_post b, teacher_title d 
	where a.teacher_sn = b.teacher_sn  
	and b.teach_title_id = d.teach_title_id  
	and a.teach_condition = 0 " . $wherestr ;
	
	$recordSet = $CONN->Execute($query) or user_error($query,256);
	while (list($teacher_sn,$teach_id,$name,$login_pass,$post_kind,$s_unit,$title_name,$class_num)=$recordSet->FetchRow()){
		if (strpos($post_office_p[$s_unit],'科任')){
			$post="&nbsp" ;
		}elseif($class_num) {//級任 
			$post =$class_name[$class_num] ;
        	}else{
			$post=$post_office_p[$s_unit] ;
		}
		
		$data.="<tr bgcolor='#FFFFFF'>
		<td>$post</td>
		<td>$title_name</td>
		<td>$name</td>
		<td>$teach_id</td>
		<td><input type='submit' name='sel[$teacher_sn]' value='回復成預設密碼'></td>
		</tr>";
	}
	if ($mode=="all") $mode="列出全校資料";
	$main="
	<table cellspacing='1' cellpadding='4' bgcolor='#000000'>
	<form name='repass_form' action='{$_SERVER['PHP_SELF']}' method='post'>
	<tr bgcolor='#E1E1FF'><td><a href=\"#\" OnClick=\"sort_kind('post')\">處室</a></td><td><a href=\"#\" OnClick=\"sort_kind('title')\">職稱</a></td><td><a href=\"#\" OnClick=\"sort_kind('name')\">教師姓名</a></td><td>教師代號</td><td>動作</td></tr>
	$data
	</form>
	</table>";
	return $main;
}

//回復密碼成預設值
function chg_form($sel=array()){
	global $CONN,$DEFAULT_LOG_PASS;
	while(list($k,$v)=each($sel)){
		$CONN->Execute("update teacher_base set login_pass='".pass_operate($DEFAULT_LOG_PASS)."' where teacher_sn='$k'");
		$res=$CONN->Execute("select * from teacher_base where teacher_sn='$k'");
		return $res->fields[name]."的密碼已回復成系統預設值「".$DEFAULT_LOG_PASS."」";
	}
}