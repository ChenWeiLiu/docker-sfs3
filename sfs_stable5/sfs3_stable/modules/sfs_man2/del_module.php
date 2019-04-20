<?php

// $Id: del_module.php 7030 2012-12-05 07:08:45Z hami $

//設定檔載入檢查
require "config.php";
// 認證檢查
sfs_check();


//執行動作判斷
if($_POST[act]=="delme"){
	$n=del_prob();
	header("Location: $_SERVER[PHP_SELF]?msn=$_POST[msn]&mode=vew_log&n=$n");
}else{
	$main=&main_form();
}


//秀出網頁
head("學務程式設定--移除模組");
echo $main;
foot();

//主要表格
function &main_form(){
	global $CONN,$school_menu_p;
	$tool_bar=&make_menu($school_menu_p);
	//列出主要的大模組
	$prob_list=list_parent_prob($_REQUEST[msn]);

	if($_REQUEST[mode]=="del"){
		$del_form=&del_form($_GET[set_msn]);
	}elseif($_REQUEST[mode]=="vew_log"){
		$del_form=&view_log("",$_GET[n],0);
	}

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
	<table cellspacing='0' cellpadding='0'>
	<tr><td valign='top'>$prob_list</td></tr>
	<tr><td height=5></td></tr>
	<tr><td valign='top'>$del_form</td></tr>
	</table>";
	return $main;
}


//列出主要的大模組
function list_parent_prob($curr_msn=0){
	global $CONN,$SFS_PATH,$MODULE_DIR;

	$sql_select="select * from sfs_module where of_group='$curr_msn' order by sort";

	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while ($data=$recordSet->FetchRow()) {
		$all[]=$data;
	}

	//取得某模組底下模組的編號
	$data="";
	foreach($all as $m){
		$mmsn=$m[msn];
				
		$checked=($m[islive]=='1')?"checked":"";

		//取得該模組在資料庫中底下的模組陣列
		$child_prob_num=sizeof(get_parent_prob($mmsn));
		
		

		if($m[islive]=='0'){
			$color="#989898";
		}elseif($child_prob_num>0){
			$color="#8000ff";
		}else{
			$color="#000000";
		}
		
		$tool=($m[kind]=="分類" and $child_prob_num > 0)?"":"<a href='$_SERVER[PHP_SELF]?set_msn=$mmsn&msn=$curr_msn&mode=del'>移除</a>";

		$real_dir_name=($m[kind]=="模組")?"〈".$m[dirname]."〉":"";
		
		$url=($child_prob_num>0)?"<a href='$_SERVER[PHP_SELF]?msn=$mmsn'><font color='$color'>".$m[showname].$real_dir_name."</font></a>":"<font color='$color'>".$m[showname].$real_dir_name."</font>";


		$color=($mmsn==$_REQUEST[set_msn])?"#FFFB8A":"#FFFFFF";
		$data.="
		<tr bgcolor='$color' class='small'>
		<td nowrap>
			<font color='darkGreen'>[".$m[kind]."]</font>
			$url
			<input type='hidden' name='prob_data[$mmsn][showname]' value='$m[showname]' $checked>
		</td>
		<td align='center'  nowrap>
			$tool
		</td>
		</tr>";
	}

	
	//上一層按鈕
	$up_link=get_up_path($curr_msn);
	$up_link="<a href='$up_link'><img src='images/up.gif' alt='' border='0'></a>";
	$up=get_module_location($curr_msn,"首頁",1);

	$main="
	<table cellspacing='1' cellpadding='4' bgcolor='blue'>
	<tr bgcolor='red'><td>
		<table width='100%' cellspacing='0' cellpadding='4' class='small'>
		<tr bgcolor='#FFFFFF'><td>$up</td><td>$up_link</td></tr>
		<tr bgcolor='#E7E7E7'>
		<td align='center' nowrap>模組名稱</td>
		<td align='center' nowrap>模組管理</td>
		</tr>
		$data
		</table>
	</td></tr>
	</table>
	";
	return $main;
}

//刪除的確認表格
function &del_form($set_msn=""){
	global $MODULE_DIR,$CONN;
	//取得模組資料
	$m=get_main_prob($set_msn);
	$of_group=get_module_path($m[msn],"首頁",0);
	$in_use=($m[islive]=='1')?"使用中":"停用中";

	
	
	if($m[kind]=="分類"){
		$stand_txt="<font color='blue'>模組分類</font>";
		//取得該模組在資料庫中底下的模組陣列
		$child_prob_num=sizeof(get_parent_prob($m[msn]));
		$disabled=($child_prob_num > 0)?"disabled":"";
		$disabled_text=($child_prob_num > 0)?"底下尚有模組不可移除":"移除分類";
	
	}else{
		$is_stand_module=is_stand_module($MODULE_DIR,$m[dirname]);

		$stand_txt=($is_stand_module)?"<font color='#358E1F'>標準模組</font>":"<font color='red'>非標準模組</font>";
	
		if($is_stand_module){
			include_once $MODULE_DIR.$m[dirname]."/module-cfg.php";
			if(sizeof($MODULE_TABLE_NAME)>=1){
				foreach($MODULE_TABLE_NAME as $dbname){
					if(empty($dbname))continue;
					$sql_select="select count(*) from $dbname";
					//$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
					$recordSet=$CONN->Execute($sql_select);
					$i=0;
					if($recordSet){
						list($datan)=$recordSet->FetchRow();
						$view=(empty($datan))?"":"<a href='$_SERVER[PHP_SELF]?set_msn=$_GET[set_msn]&msn=$_GET[msn]&mode=$_GET[mode]&vDBname=$dbname'>觀看資料</a>";
						$view=(!empty($_GET[vDBname]) and $dbname==$_GET[vDBname])?"<a href='$_SERVER[PHP_SELF]?set_msn=$_GET[set_msn]&msn=$_GET[msn]&mode=$_GET[mode]'>關閉</a>":$view;
						$DBlist.="<tr bgcolor='white'><td bgcolor='#EAEAEA'>
					<input type='checkbox' name='delDB[]' id='d$i' value='$dbname'>移除 $dbname
					</td><td>$datan $view</td></tr>";
						$i++;
					}else{
						$DBlist.="<tr bgcolor='white'><td colspan=2><font color='red'>$dbname  不存在，可能已被移除。</font></td></tr>";
					}
					
				}
			}
		}else{
			$DBlist.="<tr bgcolor='white'><td colspan=2>".$stand_txt."，無法偵測！</td></tr>";
		}

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
			$DBall="<td valign='top'><textarea cols='30' rows='10' class='small'>$allDB</textarea></td>";
		}
		
		$disabled=($SYS_MODULE || $m[dirname]=='sfs_man2')?"disabled":"";
		$disabled_text=($SYS_MODULE || $m[dirname]=='sfs_man2')?"系統模組不可移除":"丟到垃圾桶";
	}
	

	

	$main="
	<table cellspacing='0' cellpadding='4' ><tr><td valign='top'>
	<form action='$_SERVER[PHP_SELF]' method='POST' name='dbform'>

		<table cellspacing='1' cellpadding='4' bgcolor='#E0E0E0' class='small'>
		<tr bgcolor='#FBBFAE'><td colspan=2>您所要移除的模組資料如下：</td></tr>
		<tr bgcolor='white'><td bgcolor='#EAEAEA'>模組名稱</td><td>$of_group</td></tr>
		<tr bgcolor='white'><td bgcolor='#EAEAEA'>實際目錄</td><td> $m[dirname] ($stand_txt) $in_use</td></tr>
		<tr bgcolor='#FBBFAE'><td colspan=2>和 $m[dirname] 相關的資料庫</td></tr>
		$DBlist
		</table>
	</td>
	$DBall
	<td valign='top'>

	<input type='hidden' name='set_msn' value='$_GET[set_msn]'>
	<input type='hidden' name='msn' value='$m[of_group]'>
	<input type='hidden' name='dirname' value='$m[dirname]'>
	<input type='hidden' name='act' value='delme'>
	<input type='submit' value='$disabled_text' $disabled><br>
	<input type='button' value='選取所有資料表' $disabled OnClick='sel_all();'>
	</td></tr></table>
	</form>
	";
	return $main;
}


//丟到垃圾桶
function del_prob(){
	global $CONN,$MODULE_DIR, $UPLOAD_PATH;
	//找出和該模組有關的模組
	$str="select msn from sfs_module where of_group='$_POST[set_msn]'";
	$recordSet=$CONN->Execute($str) or user_error($str, 256);
	
	while(list($msn)=$recordSet->FetchRow()){
		//更新其群組設定
		$str="update sfs_module set of_group='0' where msn='$msn'";
		$CONN->Execute($str) or user_error($str, 256);
		$msg.="<li>更新 $msn 模組的群組為 0。</li>";
	}

	$str="select * from sfs_module where msn='$_POST[set_msn]'";
	$res = $CONN->Execute($str) or user_error($str, 256);
	
	// 刪除升級檔
	$path =  $UPLOAD_PATH.'upgrade/modules/'.$res->fields['dirname'];	
	if (is_dir($path)) {
		if ($dh2 = opendir($path."/".$file)) { 
				while (($file2 = readdir($dh2)) !== false) { 
					if($file2=="." or $file2=="..")
						continue;
					else {
						unlink($path."/".$file2);
					}					
				}
		} 
		closedir($dh2);
	}
	
	//刪除模組本身設定部分
	$str="delete from sfs_module where msn='$_POST[set_msn]'";
	$CONN->Execute($str) or user_error($str, 256);
	$msg.="<li>刪除 $_POST[dirname] 模組在sfs_module中的設定。</li>";

	//刪除模組權限設定部分
	$str="delete from pro_check_new where pro_kind_id='$_POST[set_msn]'";
	$CONN->Execute($str) or user_error($str, 256);
	$msg.="<li>刪除 $_POST[dirname] 模組在pro_check_new中的權限設定。</li>";

	// 移除 pro_module 記錄
	$sql = "DELETE FROM pro_module where pm_name='$_POST[dirname]'";
	$CONN->Execute($sql) or user_error($str, 256);
	$msg.="<li>刪除 $_POST[dirname] 模組在pro_module中的變數設定。</li>";


	//判斷是否為標準模組
	$is_stand_module=is_stand_module($MODULE_DIR,$_POST[dirname]);

	if($is_stand_module){
		include_once $MODULE_DIR.$_POST[dirname]."/module-cfg.php";

		$msg.="<li>$_POST[dirname] 是標準模組，開始進行選項及資料表更動。</li>";

		// 移除 sfs_text 記錄($SFS_TEXT_SETUP也是在module-cfg中設定)
		if(isset($SFS_TEXT_SETUP) and is_array($SFS_TEXT_SETUP)){
			for ($i=1; $i<=count($SFS_TEXT_SETUP); $i++) {
				$arr=$SFS_TEXT_SETUP[$i-1];
				$pm_g_id = trim($arr['g_id']);
				$pm_item = trim($arr['var']);
				$pm_arr = trim($arr['arr']);
				$sql = "DELETE FROM sfs_text WHERE t_kind='$pm_item' AND g_id=$pm_g_id";
				$CONN->Execute($sql);
			}
		}else{
			$msg.="<li>$_POST[dirname] 沒有 \$SFS_TEXT_SETUP 的設定。</li>";
		}

		//將資料表改名
		$delDB=$_POST['delDB'];
		if(sizeof($delDB)>0){
			//取得時間戳記
			$timestamp=time();
			foreach($delDB as $dbname){
				if(in_array($dbname,$MODULE_TABLE_NAME)){
					$new_dbname="garbage_".$timestamp."_".$dbname;
					chang_dbname($dbname,$new_dbname);
					$msg.="<li>把 $dbname 更名為 $new_dbname 。</li>";
				}else{
					$msg.="<li>所選的 $dbname 不在設定中，故不更名。</li>";
				}
			}
		}else{
			$msg.="<li>無刪除的資料表。</li>";
		}
	}else{
		$msg.="<li>$_POST[dirname] 非標準模組，不做資料表及選項更動。</li>";
	}

	//重設使用者狀態
	reset_user_state();
	
	$msg="<ol>$msg</ol>";
	$n=add_log($msg,"del_module");
	return $n;
}
?>
