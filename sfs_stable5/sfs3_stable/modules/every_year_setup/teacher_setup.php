<?php

// $Id: teacher_setup.php 5310 2009-01-10 07:57:56Z hami $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//錯誤設定
if($error==1){
	$act="error";
	$error_title="無年級設定";
	$error_main="找不到 $sel_year 學年度，第 $sel_seme 學期的年級設定，故您無法使用此功能。<ol><li>請先到『<a href='".$SFS_PATH_HTML."school_affairs/every_year_setup/class_year_setup.php'>班級設定</a>』設定年級以及班級資料。<li>以後記得每一學期的學期出都要設定一次喔！</ol>";
}

//執行動作判斷
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="儲存"){
	update_teacher($sel_year,$sel_seme,$seme_class,$teacher_sn);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_teacher&Cyear=$Cyear");
}elseif($act=="開始設定" or $act=="set_teacher" or $act=="modify_teacher"){
	if($act=="開始設定")$act="set_teacher";
	$main=&list_class($sel_year,$sel_seme,$Cyear,"",$act,$seme_class);
}elseif($act=="view" or $act=="觀看設定"){
	$main=&list_class($sel_year,$sel_seme,$Cyear,"view",$act,$seme_class);
}elseif($act=="列出所有年級" or $act=="viewall"){
	$main=&list_all_class($sel_year,$sel_seme);
}elseif($act=="setup_view"){
	$main=&setup_view();
}else{
	$main=&class_form($sel_year,$sel_seme);
}


//秀出網頁
head("導師設定");
echo $main;
foot();

/*
函式區
*/

//基本設定表單
function &class_form($sel_year,$sel_seme){
	global $school_menu_p,$IS_JHORES;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	$dts=($IS_JHORES==6)?"導師":"級任老師";

	//說明
	$help_text="
	請選擇一個學年、學期以做設定。||
	<span class='like_button'>開始設定</span> 會開始進行該學年學期的".$dts."設定。||
	<span class='like_button'>觀看設定</span> 會列出該學年學期的".$dts."設定。||
	<span class='like_button'>列出所有年級</span> 會列出該學年學期所有年級的".$dts."設定。
	";
	$help=&help($help_text);

	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu");
	
	//取得年級選單
	$class_year_list=&get_class_year_select($sel_year,$sel_seme,$Cyear);
	
	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td>請選擇欲設定的學年度：</td><td>$date_select</td></tr>
		<tr><td>請選擇欲設定的年級：</td><td>$class_year_list</td></tr>
		<tr><td colspan='2'><input type='submit' name='act' value='開始設定'>
		<input type='submit' name='act' value='觀看設定'>
		<input type='submit' name='act' value='列出所有年級'>
		<INPUT TYPE='button' Value='快速編修' onclick=\"location.href='chc_teacher.v2.php'\">
		</td></tr>
		</form>
		</table>
	</td></tr>
	</table>
	<br>
	$help
	";
	return $main;
}


//秀出所有班級，$mode=view（只有一個表，無新增或修改工具）、clear_view（只有那個表，連連結工具都不要）
function &list_class($sel_year,$sel_seme,$Cyear="",$mode="",$act="",$seme_class=""){
	global $CONN,$school_kind_name,$school_menu_p,$IS_JHORES;

	$dts=($IS_JHORES==6)?"導師":"級任老師";
	
	//取得年級選單
	$class_year_list=&get_class_year_select($sel_year,$sel_seme,$Cyear,"jumpMenu");
	
	//找出該表中所有的年度與學期，要拿來作選單
	$other_link="act=$act&Cyear=$Cyear";
	$tmp=&get_ss_year($sel_year,$sel_seme,$other_link);
	$other_class_text=($mode=="clear_view")?"":$tmp;

	//取出該年級、該學年、該學期的班級列表
	$class_list="";
	$query = "select c_year,c_sort,c_name,teacher_1,teacher_2 from school_class where enable=1 and year=$sel_year and semester=$sel_seme order by c_year,c_sort";
	$res = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	$class_name=array();
	$teacher=array();
	$sel_year_arr = array_keys ($school_kind_name);
	while(!$res->EOF) {
		if (in_array ($res->fields[c_year], $sel_year_arr)) { //在選擇的年級中
			$class_name_id = sprintf("%d%02d",$res->fields[c_year],$res->fields[c_sort]);
			$class_name[$class_name_id]=$school_kind_name[$res->fields[c_year]].$res->fields[c_name]."班";
			$teacher[$class_name_id][1]=addslashes($res->fields[teacher_1]);
			$teacher[$class_name_id][2]=addslashes($res->fields[teacher_2]);
		}
		$res->MoveNext();
	}
	while (list($k,$v)=each($class_name)) {
		if (empty($Cyear) || ($Cyear==substr($k,0,-2))) {
			if ($teacher[$k][1]=="" && $sel_year==curr_year() && $sel_seme==curr_seme()) {
				$sql_select = "select a.class_num,b.name from teacher_post a,teacher_base b where a.teacher_sn=b.teacher_sn and a.class_num='$k'";
				$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
				$name=addslashes($recordSet->fields[name]);
				$teacher[$k][1]=$name;
				$class_year=substr($k,0,-2);
				$class_sort=substr($k,-2,2);
				$sql_update="update school_class set teacher_1='$name' where enable=1 and year='$sel_year' and semester='$sel_seme' and c_year='$class_year' and c_sort='$class_sort'";
				$recordSet=$CONN->Execute($sql_update);
			}
			//功能表（若是觀看狀態，則不秀出表單）
			$modify_tool=($mode=="view" or $mode=="clear_view")?"":"<td class='small' nowrap>
			<a href='{$_SERVER['PHP_SELF']}?act=modify_teacher&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&seme_class=$k'>
			<img src='images/edit.png' border=0 hspace=3>修改</a></td>";
			$teacher_list=get_teacher_list($sel_year,$sel_seme,stripslashes($teacher[$k][1]));
			$class_list.=($act=="modify_teacher" && $seme_class==$k)?"<tr bgcolor='white'><form method='post' action='{$_SERVER['PHP_SELF']}'><td align='center'>".$class_name[$k]."</td>
				<td align='center'><select name=teacher_sn>".$teacher_list."</select></td>
				<td><input type='submit' name='act' value='儲存'></td>
				<input type='hidden' name='sel_year' value='$sel_year'>
				<input type='hidden' name='sel_seme' value='$sel_seme'>
				<input type='hidden' name='Cyear' value='$Cyear'>
				<input type='hidden' name='seme_class' value='$k'>
				</form></tr>":"<tr bgcolor='white'><td align='center'>".$class_name[$k]."</td><td align='center'>".stripslashes($teacher[$k][1])."</td>$modify_tool</tr>";
		}
	}
	
	//編輯按鈕
	$edit_button=($mode=="")?"":"<tr><td>
	<a href='{$_SERVER['PHP_SELF']}?act=set_teacher&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear'>
	<img src='images/edit_ss.png' alt='進行編輯' width='84' height='24' border='0'></a>
	</td></tr>";
	
	//按鈕集
	$button="<table cellspacing=1 cellpadding=0 border='0' align='center'>
	$fast_copy_button
	$add_button
	$del_button
	$edit_button
	$auto_button
	</table>";

	//功能表（若是觀看狀態，則不秀出表單）
	$modify_tool_title=($mode=="view" or $mode=="clear_view")?"":"<td align='center'>功能</td>";

	//相關功能表
	$tool_bar = ($mode=="clear_view")?"":make_menu($school_menu_p);

	$class_table="
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4 class='small'>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	<tr><td colspan='7' align='center' bgcolor='#E1ECFF'>
	<font color='#607387'>
	<font color='#000000'>$sel_year</font> 學年
	<font color='#000000'>$semester_name</font>學期
	$class_year_list ".$dts."列表
	</font>
	</td></tr>
	</form>
	<tbody>
	<tr bgcolor='#E1ECFF'>
		<td align='center' nowrap>班級</td>
		<td align='center' nowrap>".$dts."姓名</td>
		$modify_tool_title
	</tr>
	$class_list
	</tbody>
	</table>";

	//主要秀出畫面
	$main="
	<script language='JavaScript'>
	function func(ss_id){
		var sure = window.confirm('確定要刪除？');
		if (!sure) {
			return;
		}
		location.href=\"{$_SERVER['PHP_SELF']}?act=del&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&ss_id=\" + ss_id;
	}

	function jumpMenu(){
		var dd, classstr ;
		if ((document.myform.Cyear.options[document.myform.Cyear.selectedIndex].value!='')) {
			location=\"{$_SERVER['PHP_SELF']}?act=view&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=\" + document.myform.Cyear.options[document.myform.Cyear.selectedIndex].value;
		}
	}

	</script>

	$tool_bar

	<table cellspacing=0 cellpadding=0 border='0'>
	<tr>
	<td valign='top'>$class_table</td>
	<td width='5'></td>
	<td valign='top'>
	$add_form
	$button
	</td>
	<td width='5'></td>
	<td valign='top'>$other_class_text</td>
	</tr>
	</table>
	$help
	";
	return $main;
}

//修改導師
function update_teacher($sel_year,$sel_seme,$seme_class,$teacher_sn){
	global $CONN;
	$sql="select name from teacher_base where teacher_sn='$teacher_sn'";
	$rs=$CONN->Execute($sql);
	$name=addslashes($rs->fields['name']);
	if ($sel_year==curr_year() && $sel_seme==curr_seme()) {
		$sql="update teacher_post set class_num='' where class_num='$seme_class'";
		$rs=$CONN->Execute($sql);
		$sql="update teacher_post set class_num='$seme_class' where teacher_sn='$teacher_sn'";
		$rs=$CONN->Execute($sql);
	}
	$c_year=substr($seme_class,0,-2);
	$c_sort=substr($seme_class,-2,2);
	$sql_update = "update school_class set teacher_1='$name' where year='$sel_year' and semester='$sel_seme' and c_year='$c_year' and c_sort='$c_sort' and enable=1";
	if($CONN->Execute($sql_update))		return true;
	return  false;
}

//列出所有年級的班級導師列表
function &list_all_class($sel_year,$sel_seme){
	global $school_menu_p,$SFS_PATH_HTML;
	$all_class=get_class_year_array($sel_year,$sel_seme);
	if(empty($all_class)){
		trigger_error("沒有年級設定無法進行，您必須先進行班級設定，才能使用此功能。<br>
		<a href='".$SFS_PATH_HTML."school_affairs/every_year_setup/class_year_setup.php'>開始班級設定</a>", E_USER_ERROR);
	}

	//相關功能表
	$tool_bar = ($mode=="clear_view")?"":make_menu($school_menu_p);

	while(list($class_year_val,$class_year_name)=each($all_class)){
		$main.=list_class($sel_year,$sel_seme,$class_year_val,"clear_view");
	}
	return $tool_bar.$main;
}

//列出所有年級的班級導師列表
function get_teacher_list($sel_year,$sel_seme,$teacher_name){
	global $CONN;

	//選單男生顯示顏色
	$fcolor[1] = "blue";
	//選單女生顯示顏色
	$fcolor[2] = "#FF6633";
	$teacher_list="";
	if ($sel_year==curr_year() && $sel_seme==curr_seme()) $w="where teach_condition='0'";
	$sql="select name,sex,teacher_sn from teacher_base $w order by name";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$name=addslashes($rs->fields['name']);
		$selected=($teacher_name==$name)?"selected":"";
		$teacher_list.="<option value='".$rs->fields['teacher_sn']."' $selected><font color='".$fcolor[$rs->fields['sex']]."'>".stripslashes($name)."</option>\n";
		$rs->MoveNext();
	}
	return $teacher_list;
}
?>
