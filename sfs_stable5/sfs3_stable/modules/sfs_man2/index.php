<?php

// $Id: index.php 8863 2016-04-06 16:38:14Z qfon $

//設定檔載入檢查
require "config.php";
// 認證檢查
sfs_check();

//執行動作判斷
if ($_POST[do_key]=="取回預設值") {
	get_sfs_module_set($_POST[pm_name],1);
	header("location: $_SERVER[PHP_SELF]?msn=$_POST[msn]&set_msn=$_POST[set_msn]&mode=$_POST[mode]");
}
elseif($_POST[act]=="儲存"){
	save();
	if ($_POST[of_group]<>$_POST[msn])
		$_POST[msn] = $_POST[of_group];
	header("location: $_SERVER[PHP_SELF]?msn=$_POST[msn]&set_msn=$_POST[set_msn]&mode=$_POST[mode]");
}elseif($_POST[act]=="save_prob"){
	save_prob($prob_data);
	header("location: $_SERVER[PHP_SELF]?msn=$_POST[msn]&set_msn=$_POST[set_msn]&mode=$_POST[mode]");
}elseif($_GET[act]=="del_power"){
	del_power($_GET[p_id],$_GET[set_msn]);
	header("location: $_SERVER[PHP_SELF]?msn=$_GET[msn]&set_msn=$_GET[set_msn]&mode=$_GET[mode]");
}elseif($_GET[act]=="del"){
	del_module($_GET[set_msn]);
	header("location: $_SERVER[PHP_SELF]?msn=$_GET[msn]&set_msn=$_GET[set_msn]&mode=$_GET[mode]");
}elseif($_POST[act]=="updateAll"){
	updateAll($_POST[prob_data]);
	header("location: $_SERVER[PHP_SELF]?msn=$_POST[msn]");
}elseif($_POST[act]=="updateMV"){
	updateMV($_POST[pm_value]);
	header("location: $_SERVER[PHP_SELF]?msn=$_POST[msn]&set_msn=$_POST[set_msn]&mode=$_POST[mode]");
}else{
	$main=&main_form();
}


//秀出網頁
head("學務程式設定");
echo $main;
foot();

//主要表格
function &main_form(){
	global $CONN,$school_menu_p;
	$tool_bar=&make_menu($school_menu_p);
	//列出主要的大模組
	$prob_list=list_parent_prob($_REQUEST[msn]);
	if($_REQUEST[mode]=="setup"){
		$setup_form=&setup_form($_REQUEST[set_msn]);
	}elseif($_REQUEST[mode]=="setup_v"){
		$setup_form=&set_v_form($_REQUEST[set_msn]);
	}
	$main="
	$tool_bar
	<table cellspacing='0' cellpadding='0'>
	<tr><td valign='top'>$prob_list</td>
	<td width='10'></td>
	<td valign='top'>$setup_form</td></tr>
	</table>";
	return $main;
}


//列出主要的大模組
function list_parent_prob($curr_msn=0){
	global $CONN,$SFS_PATH,$MODULE_DIR ,$CDCLOGIN;

	//真實路徑下的模組
	$real_dir=real_dir_array($MODULE_DIR);


	$sql_select="select * from sfs_module where of_group='$curr_msn' order by sort";

	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while ($data=$recordSet->FetchRow()) {
		$all[]=$data;
	}

	//取得某模組底下模組的編號
	$data="";
	$CDCArray = CDC::getAuthKind(1);
	foreach($all as $m){
		$mmsn=$m[msn];
		$db_dir[]=$m["dirname"];
		$checked=($m[islive]=='1')?"checked":"";
		$checked_open=($m[isopen]=='1')?"checked":"";

		//取得該模組在資料庫中底下的模組陣列
		$child_prob_num=sizeof(get_parent_prob($mmsn));

		$tool="<a href='$_SERVER[PHP_SELF]?msn=$curr_msn&set_msn=$mmsn&mode=setup'>管理</a>";

		if(!in_array($m[dirname],$real_dir) and $m[kind]=="模組"){
			$color="red";
			$tool="<a href='$_SERVER[PHP_SELF]?act=del&set_msn=$mmsn&msn=$curr_msn'>刪除</a>";
		}elseif($m[islive]=='0'){
			$color="#989898";
		}elseif($child_prob_num>0){
			$color="#8000ff";
		}else{
			$color="#000000";
		}

		$real_dir_name=($m[kind]=="模組")?"〈".$m[dirname]."〉":"";
		
		$url=($child_prob_num>0)?"<a href='$_SERVER[PHP_SELF]?msn=$mmsn'><font color='$color'>".$m[showname].$real_dir_name."</font></a>":"<font color='$color'>".$m[showname].$real_dir_name."</font>";

		//判斷是否為標準模組，且在 pro_module 中有變數控管，才會秀出 "調整" 的連結
		$setup_v=(is_stand_module($MODULE_DIR,$m[dirname]) && in_pro_module($m[dirname]))?"<a href='$_SERVER[PHP_SELF]?msn=$curr_msn&set_msn=$mmsn&mode=setup_v'>調整</a>":"";

		$color=($mmsn==$_REQUEST[set_msn])?"#FFFB8A":"#FFFFFF";
		$data.="
		<tr bgcolor='$color' class='small'>
		<td nowrap>
			<input type='text' name='prob_data[$mmsn][sort]' value='$m[sort]' size='1' class='small'>
		</td>
		<td nowrap>
			<input type='checkbox' name='prob_data[$mmsn][islive]' value='1' $checked>
		</td>
		<td nowrap>
			<input type='checkbox' name='prob_data[$mmsn][isopen]' value='1' $checked_open>
		</td>
		<td nowrap>
			$url
			<input type='hidden' name='prob_data[$mmsn][showname]' value='$m[showname]' $checked>
		</td>";
		
		if ($CDCLOGIN) {
			if ($m[kind]=="模組") 
			$data .= "<td nowrap>".$CDCArray[$m['auth_kind']].'</td>';
			else
				$data .= "<td nowrap></td>";
		}
		
		$data .= "<td align='center'  nowrap>
			$tool
		</td>
		<td align='center' nowrap>
		$setup_v
		</td>
		</tr>";
	}

	
	//上一層按鈕
	$up_link=get_up_path($curr_msn);
	$up_link="<a href='$up_link'><img src='images/up.gif' alt='' border='0'></a>";
	$up=get_module_location($curr_msn,"首頁",1);

	$main="

	<table cellspacing='1' cellpadding='4' bgcolor='#0000FF'>
	<form action='$_SERVER[PHP_SELF]' method='post'>
	<tr bgcolor='#FFFFFF'><td>
		<table width='100%' cellspacing='0' cellpadding='2' class='small'>
		<tr bgcolor='#FFFFFF'><td colspan='4'>$up</td><td>$up_link</td></tr>
		<tr bgcolor='#E7E7E7'>
		<td align='center' nowrap>順序</td>
		<td align='center' nowrap>啟動</td>
		<td align='center' nowrap>開放瀏覽</td>
		<td align='center' nowrap>模組名稱</td>";
		if ($CDCLOGIN)
			$main .= "<td align='center' nowrap>認證強度</td>";
	
		$main .= "<td align='center' nowrap>模組管理</td>
		<td align='center' nowrap>變數調整</td>
		</tr>
		$data
		</table>
		<input type='hidden' name='act' value='updateAll'>
		<input type='hidden' name='msn' value='$curr_msn'>
		<div align='center'><input type='submit' value='儲存'></div>
	</td></tr>
	</form>
	</table>
	";
	return $main;
}


//取得模組權限資料
function get_chk_prob($id=""){
	global $CONN,$set_id,$pro_kind_id,$all_id,$set_id;
	$room=room_kind();
	$title=title_kind();
	$power=array("一般","管理權","核章權","免二層核章權");
	$sql_select = "SELECT * FROM pro_check_new where pro_kind_id='$id'";
	$recordSet=$CONN->Execute($sql_select);
	while(list($p_id,$proid,$id_kind,$id_sn,$is_admin,$login_kind)=$recordSet->FetchRow()){
		if($id_kind=="教師"){
			$name=get_teacher_name($id_sn);
		}elseif($id_kind=="職稱"){
			$name=$title[$id_sn];
		}elseif($id_kind=="處室"){
			$name=($id_sn==99)?"所有教師":$room[$id_sn];
		}elseif($id_kind=="學號"){
			$name=($id_sn=='0')?"所有學生":stud_name($id_sn);
		}elseif($id_kind=="其他"){
			$name=($id_sn=='0')?"所有家長":get_parent_name($id_sn);
		}
		
		$row.="<tr bgcolor='#FFFFFF'>
		<td>$name</td>
		<td>$power[$is_admin]</td>
		<td><a href='$_SERVER[PHP_SELF]?act=del_power&p_id=$p_id&msn=$_REQUEST[msn]&mode=$_REQUEST[mode]&set_msn=$_REQUEST[set_msn]'>刪除</a></td>
		</tr>";
	}
	if(empty($row))return;
	$main="
	<table cellspacing='1' cellpadding='2' bgcolor='#C0C0C0' class='small'>
	<tr bgcolor='#E8E8E8'><td>授權對象</td><td>權限種類</td><td>功能</td></tr>
	$row
	</table>";
	return $main;
}

//設定表單
function &setup_form($set_id=""){
	global $SFS_PATH,$MODULE_DIR ,$CDCLOGIN;
	//取得模組資料
	$data=get_main_prob($set_id);
	//取得模組權限資料
	$data_chk=get_chk_prob($set_id);
	//取得舊模組說明檔
	$log=get_auth_txt($data[dirname]);

	$is_stand_module=is_stand_module($MODULE_DIR,$data[dirname]);
	$stand_txt=($is_stand_module)?"<font color='#358E1F'>標準模組</font>":"<font color='red'>非標準模組</font>";
	if($is_stand_module){
		//include_once $MODULE_DIR.$data[dirname]."/module-cfg.php";
		include($MODULE_DIR.$data[dirname]."/module-cfg.php");
		$log2="
		『標準模組所含資訊』<br>
		最後更新版本：
		$MODULE_UPDATE_VER<br>
		 最後更新日期：
		$MODULE_UPDATE<br>
		";
	}

	$author=(empty($data[author]))?"":"〈作者： ".$data[author]."〉";

	//認證方式選單
	$auth_set="<select name=\"auth_kind\"  size=\"1\" style=\"background-color:#FFFFFF;font-size:13px\">";
	foreach(CDC::getAuthKind() as $cdc_id=>$cdc_value) {
		if ($data['auth_kind'] == $cdc_id)
			$auth_set .= "<option value='$cdc_id' selected='true'>$cdc_value</option>";
		else
			$auth_set .= "<option value='$cdc_id' >$cdc_value</option>";
	}
		

	//權限設定選單
	$power_set=power_set($_REQUEST[id_kind],"id_kind","id_sn","is_admin",$MODULE_MAN,$MODULE_MAN_DESCRIPTION);
	
	$checked=($data[isopen]=='1')?"checked":"";

	$module_name=($data[kind]=="模組")?"<tr><td bgcolor='#F1F2E6'>$stand_txt</td><td>
		$data[dirname] $author</td></tr>":"";
	if ($_POST[of_group]<>'')
		$of_group_ss = $_POST[of_group];
	else
		$of_group_ss = $data[of_group];
	$of_group=get_of_group($set_id,"of_group",$of_group_ss,$data[kind]);

	$main="
	<table cellspacing='1' cellpadding='4' bgcolor='#C0C0C0'><tr bgcolor='#FFFFFF'><td>
		<form action='$_SERVER[PHP_SELF]' method='post'>
		<input type='hidden' name='msn' value='$_REQUEST[msn]'>
		<input type='hidden' name='set_msn' value='$set_id'>
		<input type='hidden' name='mode' value='$_REQUEST[mode]'>

		<table cellspacing='0' cellpadding='4' bgcolor='#FFFFFF' class='small'>
		$module_name
		<tr><td bgcolor='#F1F2E6'>中文名稱</td><td>
		<input type='text' name='showname' value='$data[showname]'></td></tr>
		<tr><td bgcolor='#F1F2E6'>所屬分類</td><td>
		$of_group</td></tr>
		<tr><td bgcolor='#F1F2E6'>是否開放進入</td><td>
		<input type='checkbox' name='isopen' value='1' $checked>
		允許一般網友進入瀏覽</td></tr>";
		if ($CDCLOGIN and $data['kind']=='模組') 
		$main .="<tr bgcolor='#E4A68B'><td>認證強度</td><td>$auth_set</td></tr>";
		
		$main .="
		<tr bgcolor='#FFDFDF'><td>模組授權</td><td>$power_set<input type='submit' name='act' value='儲存'></td></tr>
		<tr bgcolor='#FFDFDF'><td colspan='2'>$data_chk</td></tr>
		<tr bgcolor='#DFEFD8'><td colspan='2'>$log</td></tr>
		<tr bgcolor='#FFFB8A'><td colspan='2'>$log2</td></tr>

		</table></form>
	</td></tr></table>";
	  
	

	return $main;
}
//儲存所有模組設定資訊
function save_prob($prob_data=array()){
	global $CONN;
	//儲存模組本身設定部分
	foreach($prob_data as $pro_kind_id=>$data){
		$islive=($data[islive]=='1')?"1":"0";
		$str="update sfs_module set sort='$data[sort]', islive='$islive' where msn='$data[msn]'";
		$CONN->Execute($str) or user_error($str, 256);
	}
	return ;
}

//儲存模組設定資訊
function save(){
	global $CONN,$UPLOAD_PATH;

	//儲存權限部分
	if(!empty($_POST[id_kind]) and !is_null($_POST[id_sn])){
		if($_POST[id_kind]=="家長")$_POST[id_kind]="其他";
		$str="INSERT INTO pro_check_new (pro_kind_id,id_kind,id_sn,is_admin) VALUES ('$_POST[set_msn]','$_POST[id_kind]','$_POST[id_sn]','$_POST[is_admin]')";
		$CONN->Execute($str) or user_error($str, 256);
	}

	//儲存模組本身設定部分
	$isopen=($_POST[isopen]=='1')?"1":"0";
	$str="update sfs_module set showname='$_POST[showname]', isopen='$isopen',of_group='$_POST[of_group]' , auth_kind='{$_POST['auth_kind']}'  where msn='$_POST[set_msn]'";
	$CONN->Execute($str) or user_error($str, 256);
	
	//重新產生路徑表
	unlink($UPLOAD_PATH."Module_Path.txt");
	Creat_Module_Path();
	
	//重設使用者狀態
	reset_user_state();
	return ;
}

//刪除模組
function del_module($set_id){
	global $CONN,$_SERVER;

	//刪除模組權限設定部分
	$str="delete from sfs_module where msn='$set_id'";
	$CONN->Execute($str) or user_error($str, 256);
	//重設使用者狀態
	reset_user_state();

	return ;
}



//刪除某權限設定
function del_power($p_id,$set_id){
	global $CONN,$_SERVER;

	//刪除模組權限設定部分
	$str="delete from pro_check_new where p_id='$p_id'";
	$CONN->Execute($str) or user_error($str, 256);
	//重設使用者狀態
	reset_user_state();

	return ;
}


//更新數個模組
function updateAll($dataAll){
	foreach($dataAll as $msn=>$data){
		update($data,$msn);
	}
	return true;
}


//更新
function update($data,$msn){
	global $CONN;
	$sql_update = "update sfs_module set showname='$data[showname]',sort='$data[sort]',isopen='$data[isopen]',islive='$data[islive]' where msn = '$msn'";
	$CONN->Execute($sql_update) or user_error("更新失敗！<br>$sql_update",256);
	return $msn;
}

//設定變數的表格
function &set_v_form($set_id){
	global $MODULE_DIR;
	//取得模組資料
	$data=get_main_prob($set_id);
	$main=&listAllModuleSetup($data[dirname]);
	return $main;
}


//列出某一模組的變數
function &listAllModuleSetup($pm_name=""){
	global $CONN,$MODULE_DIR;
	
	include_once $MODULE_DIR.$pm_name."/module-cfg.php";
	//轉換陣列變數
	if (count($SFS_MODULE_SETUP)>0)
		$module_arr = change_module_var_arr($SFS_MODULE_SETUP);

	$sql_select="select pm_id,pm_item,pm_memo,pm_value from pro_module where pm_name = '$pm_name' order by pm_id";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while (list($pm_id,$pm_item,$pm_memo,$pm_value)=$recordSet->FetchRow()) {
		$data.="<tr bgcolor='#FFFFFF'>
		<td nowrap>$pm_item<br/>$pm_memo</td>
		<td>";
		
		if (is_array($module_arr[$pm_item][value])) {
			$temp_sel = "<select name='pm_value[$pm_id]'>";
			while(list($key,$val) = each($module_arr[$pm_item][value])){
				if ($key == $pm_value)
					$temp_sel.="<option selected value='$key'>$val</option>";
				else
					$temp_sel.="<option value='$key'>$val</option>";
			}
			$temp_sel .= "</select>";
			$data .= $temp_sel;
		}
		else 
			$data .= "<input type='text' name='pm_value[$pm_id]' value='$pm_value'>";

		$date.="</td>
		</tr>";
	}
	$main="
	<table  cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>
	<form action='$_SERVER[PHP_SELF]' method='POST'>
  
	<tr bgcolor='#E6E9F9'><td>變數名稱<br/>變數說明</td><td>變數值</td></tr>
	$data
	<input type='hidden' name='msn' value='$_REQUEST[msn]'>
	<input type='hidden' name='set_msn' value='$_REQUEST[set_msn]'>
	<input type='hidden' name='mode' value='$_REQUEST[mode]'>
	<input type='hidden' name='pm_name' value='$pm_name'>
	<input type='hidden' name='act' value='updateMV'>
	</table>
	<p align='center'><input type='submit' value='儲存變數值'> &nbsp;&nbsp; <input type='submit' name='do_key' value='取回預設值' onClick=\"return confirm(' $pm_name \\n確定設為預設值  ?')\" > </p>
	</form>
	";
	return $main;
}

//轉換模組陣列
// var -- 模組變數
// msg -- 說明
// value  -- 值
// is_array -- 是否為陣列
function change_module_var_arr($arr) {

while (list($id,$module_arr)=each($arr)){
	$var = $module_arr['var'];
	$msg = $module_arr['msg'];
	$value = $module_arr['value'];

	$return_arr[$var][msg] = $msg;
	$return_arr[$var][value] = $value;
}
return $return_arr;

}

//更新某一模組的變數
function updateMV($pm_value){
	global $CONN;
	foreach($pm_value as $pm_id=>$v){
		$sql_update = "update pro_module set pm_value='$v' where pm_id = '$pm_id'";
		$CONN->Execute($sql_update) or user_error("更新失敗！<br>$sql_update",256);
	}
	return true;
}

?>