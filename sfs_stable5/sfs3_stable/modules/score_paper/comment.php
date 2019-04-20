<?php

// $Id: comment.php 5310 2009-01-10 07:57:56Z hami $

/* 取得學務系統設定檔 */
include "config.php";
sfs_check();
 
/************************主要內容*************************/ 

$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id

//延續狀態設定
if(is_null($_REQUEST[add_one])){
	$_REQUEST[add_one]=1;
}

//主要函式
$main=&list_comment($_REQUEST[cq]);

echo "<html><body bgcolor='#E7F3FF'><style>body,td,input,select,textarea{font-size: 12px; }</style>
$main
</body></html>";



/**********************************************************/


//以下是函式
//秀出上層選單列表
function &list_comment($cq){
	global $CONN,$comment,$data,$teacher_id,$send_comm,$add_comment,$is_modify;
	
	$data_kind=array('','類別','等級','評語');
	
	$tmp_kind="";
	$tmp_level="";

	//取得共同的教師評語以及該教師評語
	$comm_length_select=get_kind($teacher_id);

	//選擇共同的教師以及該教師等級
	$level_select=get_level($teacher_id);
	
	//取得評語下拉選單
	$comment_select=get_comm($teacher_id);
	
	
	$sel="select comm from comment where serial='$_REQUEST[comment]' and kind='$_REQUEST[comm_length]' and level='$_REQUEST[level]'";
	$sel_comment=$CONN->Execute($sel);
	list($the_comment) = $sel_comment->FetchRow();
	$end=substr($_REQUEST[comm],-2);
	if($_REQUEST[comm]!="" and $end!='。' and $end!='，' and $the_comment!="" and $_REQUEST[add_one]==1){
		$the_comment='，'.$the_comment;
	}
	
	$word=($_REQUEST[add_one]==1)?$_REQUEST[comm].$the_comment:$the_comment;

	$mainc.=$word;
	
		
	$main="	<form name='myform' method='post' action='{$_SERVER['PHP_SELF']}'>
	$comm_length_select
	$level_select
	$comment_select
	<br>
	<input type='hidden' name='add_one' value='$_REQUEST[add_one]'>\n
	<input type='hidden' name='cq' value='$cq'>
	<textarea name='comm' cols='50' rows='5' wrap='soft' style='width:100%;height:100px'>$mainc</textarea>	
	</form>
	<form name='back' action='input.php' method='post'>
	<input type='button' name='send_comm_back' value='確定' 
	onClick=\"window.opener.document.col1.".$cq.".value='".$mainc."';setTimeout('window.close()',100);\">（100字以內）
	</form>";
	return  $main;
}


//取得共同的以及該教師評語種類
function get_kind($teacher_id=0){
	global $CONN;
	if(empty($teacher_id))return;
	$sel="select * from comment_kind where kind_teacher_id='0' or kind_teacher_id='$teacher_id'";
	
	$comm_len=$CONN->Execute($sel);
	while(!$comm_len->EOF){
		$tmp_value=$comm_len->fields[0];
		$tmp_name=$comm_len->fields[2];
		$selected=($_REQUEST[comm_length]==$tmp_value)?"selected":"";
		$len.="<option value='$tmp_value' $selected>$tmp_name</option>\n";
		if($selected=='selected') $tmp_kind=$tmp_name;
		$comm_len->MoveNext();
	}
	
	$comm_length_select="
	類別：
	<select name='comm_length' onChange='submit()'>
	<option value=''>選擇類別</option>
	$len
	</select>";
	return $comm_length_select;
}

//選擇共同的教師以及該教師等級
function get_level($teacher_id=0){
	global $CONN;
	if(empty($teacher_id) or empty($_REQUEST[comm_length]))return;
	$sel="select * from comment_level where level_teacher_id='0' or level_teacher_id='$teacher_id'";
	
	$comm_lev=$CONN->Execute($sel);
	while(!$comm_lev->EOF){
		$tmp_value=$comm_lev->fields[0];
		$tmp_name=$comm_lev->fields[2];
		$selected=($_REQUEST[level]==$tmp_value)?"selected":"";
		$select.="<option value='$tmp_value' $selected>$tmp_name</option>\n";
		if($selected=='selected') $tmp_level=$tmp_name;
		$comm_lev->MoveNext();
	}
	
	$level_select="
	等級：
	<select name='level' onChange='submit()'>
	<option value=''></option>
	$select
	</select>";
	return $level_select;
}

//取得評語
function get_comm($teacher_id=0){
	global $CONN;
	if(empty($_REQUEST[comm_length]) or empty($_REQUEST[level]) or empty($teacher_id))return;
	
	$sel="select serial,comm from comment where kind='$_REQUEST[comm_length]' and level='$_REQUEST[level]' and (teacher_id='0' or teacher_id='$teacher_id')";
	$comm_text=$CONN->Execute($sel);
	while(!$comm_text->EOF){
		$c=(strlen($comm_text->fields[1])<=8)?$comm_text->fields[1]:substr($comm_text->fields[1],0,8)."...";
		$ser=$comm_text->fields[0];
		$selected=($_REQUEST[comment]==$comm_text->fields[0])?"selected":"";
		$comment_line.="<option value='$ser' $selected>$c</option>\n";
		$comm_text->MoveNext();
	}
	$comm_act=($_REQUEST[add_one]==0)?"1'>不延續評語":"0'>延續評語";
	$comment_select.="
	評語：<select name='comment' onChange='submit()'>
	<option value=''>選擇評語</option>
	$comment_line
	</select>
	[狀態：<a href='{$_SERVER['PHP_SELF']}?comm_length=$_REQUEST[comm_length]&level=$_REQUEST[level]&comment=$_REQUEST[comment]&cq=$_REQUEST[cq]&add_one=$comm_act</a>]\n";
	return $comment_select;
}
?>
