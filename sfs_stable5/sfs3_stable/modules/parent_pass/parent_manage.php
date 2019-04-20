<?php
// $Id: parent_manage.php 5310 2009-01-10 07:57:56Z hami $
// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 叫用 SFS3 的版頭
head("家長帳號管理");

// 認證
sfs_check();

//
// 您的程式碼由此開始

//全域變數轉換區*****************************************************
$var_name=($_GET['var_name'])?$_GET['var_name']:$_POST['var_name'];
$act=($_GET['act'])?$_GET['act']:$_POST['act'];
//$mode=($_GET['mode'])?$_GET['mode']:$_POST['mode'];
$name=($_GET['name'])?$_GET['name']:$_POST['name'];
$chg_cond=($_GET['chg_cond'])?$_GET['chg_cond']:$_POST['chg_cond'];
$parent_id=($_GET['parent_id'])?$_GET['parent_id']:$_POST['parent_id'];
$cond=($_GET['cond'])?$_GET['cond']:$_POST['cond'];
//********************************************************************

if(empty($act))$act="";

if($chg_cond=="yes_chg"){
//變更家長的使用狀態
	if($cond==0) $sql="delete from parent_auth where parent_id='$parent_id' ";
	else $sql="update parent_auth set enable='$cond' where parent_id='$parent_id'";
	$CONN->Execute($sql)  or trigger_error("SQL語法錯誤： $sql_st", E_USER_ERROR);
} 


//執行動作判斷
if($act=="列出全部家長資料"){
	$result=find_data("","all");
}elseif($act=="查詢"){
	$result=find_data($name);
}

$main=pre_form();

//橫向選單標籤
echo print_menu($MENU_P);

echo $main;
echo $result;


//搜尋介面
function pre_form(){
	$main="
	<table>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<tr><td><input type='submit' name='act' value='列出全部家長資料'></td></tr>
	<tr><td>
	輸入家長姓名：
	<input type='text' name='name' size='10'>
	<input type='submit' name='act' value='查詢'>	
	</td></tr>
	</form>
	</table>";
	return $main;
}

//搜尋資料
function find_data($name="",$mode=""){
	global $CONN,$act;
		
	$wherestr=($mode=="all")?" where  sd.guardian_p_id=pa.parent_id   GROUP BY pa.parent_id order by sd.stud_id":" where sd.guardian_name='$name' and sd.guardian_p_id=pa.parent_id GROUP BY pa.parent_id";
	$query="select sd.guardian_name , sd.guardian_p_id ,pa.* from parent_auth as pa ,  stud_domicile as sd ".$wherestr;  		
	$recordSet = $CONN->Execute($query) or user_error($query,256);
	$i=0;
	//$date="<form action='{$_SERVER['PHP_SELF']}' method='post'>";
	while (!$recordSet->EOF){
		$parent_name[$i]=$recordSet->fields[guardian_name];
		$parent_id[$i]=$recordSet->fields[parent_id];
		$login_id[$i]=$recordSet->fields[login_id];
		$parent_pass[$i]=$recordSet->fields[parent_pass];		
		$enable[$i]=$recordSet->fields[enable];
		if($enable[$i]=='1') $checked1[$i]="checked";
		elseif($enable[$i]=='2') $checked2[$i]="checked";
		elseif($enable[$i]=='3') $checked3[$i]="checked";
		else $checked0[$i]="checked";
		$data.="<tr bgcolor='#FFFFFF'>		
		<td>$parent_name[$i]</td>
		<td>$login_id[$i]</td>
		<td>$parent_pass[$i]</td>		
		<td><form action='{$_SERVER['PHP_SELF']}' method='post'>
			<input type='radio' $checked1[$i] name='cond' value='1' onchange='this.form.submit()'>尚未啟動
			<input type='radio' $checked2[$i] name='cond' value='2' onchange='this.form.submit()'>啟用
			<input type='radio' $checked3[$i] name='cond' value='3' onchange='this.form.submit()'>停用
			<input type='radio' $checked0[$i] name='cond' value='0' onchange='this.form.submit()'>刪除
		</td><input type='hidden' name='chg_cond' value='yes_chg'><input type='hidden' name='parent_id' value='$parent_id[$i]'><input type='hidden' name='name' value='$name'><input type='hidden' name='mode' value='$mode'><input type='hidden' name='act' value='$act'></form>
		</tr>";
		$i++;
		$recordSet->MoveNext();
	}
	//$data.="<input type='hidden' name='name' value='$name'><input type='hidden' name='mode' value='$mode'><input type='hidden' name='act' value='$act'></form>";
	$main="
	<table cellspacing='1' cellpadding='4' bgcolor='#000000'>
	<tr bgcolor='#E1E1FF'><td>家長姓名</td><td>家長帳號</td><td>密碼</td><td>狀態</td></tr>
	$data
	</table>";
	return $main;
}

// SFS3 的版尾
foot();

?>

