<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

include_once "config.php";

sfs_check();


if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


//執行動作判斷
if($act=="授權"){
	chang_root($new_root_sn);
	header("location:".$_SERVER[PHP_SELF]);
}elseif($act=="del"){
	del_root($p_id);
	header("location:".$_SERVER[PHP_SELF]);
}else{
	$main=cr_form();
}


//秀出網頁
head("學務系統資料備份");
echo $main;
foot();


/*
函式區
*/

//基本設定表單
function cr_form(){
	global $school_menu_p,$CONN;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	
	$the_root=who_is_root();
	$power=array("一般","管理權");
	
	foreach($the_root as $id_sn => $root){
		$t_array[]=$id_sn;
		
		$id_kind=$root[id_kind];
		$is_admin=$root[is_admin];
		$p_id=$root[p_id];
		
		if($id_kind=="教師"){
			$name=get_teacher_name($id_sn);
		}elseif($id_kind=="職稱"){
			$title=title_kind();
			$name=$title[$id_sn];
		}elseif($id_kind=="處室"){
			$room=room_kind();
			$name=($id_sn==99)?"所有教師":$room[$id_sn];
		}elseif($id_kind=="學號"){
			$name=stud_data($id_sn);
		}elseif($id_kind=="其他"){
			$name="其他";
		}
		
		$data.="<tr bgcolor='#FFFFFF'>		
		<td nowrap>$id_kind</td>
		<td nowrap>$name</td>
		<td nowrap>$power[$is_admin]</td>
		<td nowrap><a href='$_SERVER[PHP_SELF]?act=del&p_id=$p_id'>解除</a></td>
		</tr>";
	}
	
	$del_old_root="
	目前具有本站網管權限者如下：
	<table width='90%' cellspacing='1' cellpadding='3' bgcolor='#FFD2FF' class='small'>
	<tr><td nowrap>授權對象</td>
	<td nowrap>被授權者</td>
	<td nowrap>權限種類</td>
	<td nowrap>解除權限</td></tr>
	$data
	</table>
	";
	
	
	//製作教師選單
	$sql_select = "select name,teacher_sn from teacher_base where teach_condition='0'";
	$recordSet=$CONN->Execute($sql_select);
	$option="<option value=''></option>";
	while (list($name,$teacher_sn) = $recordSet->FetchRow()) {
		$disabled=(in_array($teacher_sn,$t_array))?"disabled":"";
		$option.="<option value='$teacher_sn' $disabled>$name</option>\n";
	}
	
	$main="
	$tool_bar
	<table cellspacing='0' cellpadding='4' class='small'>
	<tr bgcolor='#FFFFFF'><td valign='top'>
	<form method='post' action='$_SERVER[PHP_SELF]'>
	請選擇一位教師作為本站新網管：<br>
	<select name='new_root_sn'>
	$option
	</select>老師 
	<input type='submit' name='act' value='授權'>
	</td></tr><tr>
	<td valign='top'>$del_old_root</td>
	</tr></table>
	</form>
	";
	return $main;
}

//授權
function chang_root($new_root_sn=""){
	global $CONN;
	$sql_insert = "insert into pro_check_new 
	(pro_kind_id,id_kind,id_sn) values('1','教師',$new_root_sn)";
	$CONN->Execute($sql_insert) or user_error("授權失敗！<br>$sql_insert",256);
	return true;
}

//移除權限
function del_root($p_id=""){
	global $CONN;
	$man=who_is_root();
	if(sizeof($man)<='1')user_error("至少要有一名網管存在，故您無法移除此網管權限。",256);
	$sql_delete = "delete from pro_check_new 
	where p_id='$p_id'";
	$CONN->Execute($sql_delete) or user_error("移除授權失敗！<br>$sql_delete",256);
	return true;
}
?>
