<?php
// $Id: function.php 8863 2016-04-06 16:38:14Z qfon $

//真實路徑下的目錄陣列
function real_dir_array($dir){
	global $CONN,$SFS_PATH;

	if (!$dir) user_error("沒有傳入dir參數！請檢查！",256);

	$HIDDEN_DIR=non_display_path();
	$HIDDEN_DIR[]=".";
	$HIDDEN_DIR[]="..";
	if ($handle = opendir($dir)) {
		while (false != ($file = readdir($handle))) {
			if(in_array($file,$HIDDEN_DIR))continue;
			if(is_dir($dir."/".$file)){
				$real_dir[]=$file;
			}
		}

		closedir($handle);
	}
	return $real_dir;
}


//取得舊模組說明檔
function get_auth_txt($module=""){
	global $SFS_PATH;
	$log="";
	$fpath_str = $SFS_PATH."/modules/".$module."/author.txt";
	if (is_file ($fpath_str)){
	$fd = fopen($fpath_str, "r");
		while ($buffer = fgets($fd, 4096)){
			$log.=$buffer."<BR>";
		}
		fclose($fd);
	}
	if(empty($log))$log="無任何說明。";
	return $log;
}


//找出模組的分類
function get_of_group($curr_msn="",$name="of_group",$sel_group=0,$kind="模組",$show_all=0){
	$option=get_msn_of_group($curr_msn,0,$sel_group,$show_all);
	if($kind == "分類")
		$theData="<select name='$name'><option>首頁</option>$option</select>";
	else
		$theData="<select name='$name'>$option</select>";

	return $theData;
}

//分類遞迴
function get_msn_of_group($curr_msn="",$group=0,$sel_group=0,$show_all=0){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    $curr_msn=intval($curr_msn);
	$all=($show_all=='1')?"":"and msn<>'$curr_msn'";
	$group=intval($group);
	$sql_select="select msn,showname from sfs_module where kind='分類' and of_group='$group' $all order by sort";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($msn,$showname)=$recordSet->FetchRow()){
		$name[$msn]=$showname;
		$module_name_arr[$msn] = $dirname;
	}

	if(empty($name) or sizeof($name)<=0)return;
	foreach($name as $msn=>$showname){
		$selected=($sel_group==$msn)?"selected":"";
		$module_name=get_module_path($group);
		$option.="<option value='$msn' $selected> $module_name / $showname</option>\n";
		$option.=get_msn_of_group($curr_msn,$msn,$sel_group,$show_all);
	}

	return $option;
}



//取得某一筆模組的名稱
function get_module_name($msn){
	global $CONN;

	if (!$msn) user_error("沒有傳入模組代碼！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    $msn=intval($msn);
	$sql_select="select showname from sfs_module where msn='$msn'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	list($showname)=$recordSet->FetchRow();
	return $showname;
}

//授權選單
function power_set($curr_id_kind="",$name_1="id_kind",$name_2="id_sn",$name_3="is_admin",$MODULE_MAN,$MODULE_MAN_DESCRIPTION,$mode=0){
	$id_kind_array=array("處室"=>"處室","職稱"=>"職稱","教師"=>"教師","學號"=>"學生","家長"=>"家長");
	
	//授權模式
	$sel1 = new drop_select();
	$sel1->s_name = $name_1;
	$sel1->has_empty = true;
	$sel1->id = $curr_id_kind;
	$sel1->arr = $id_kind_array;
	$sel1->is_submit = true;
	$id_kind_sel=$sel1->get_select();
	if ($mode) return $id_kind_sel;
	
	if($curr_id_kind=="處室"){
		$room=room_kind();
		$room[99]="所有教師";
		//處室
		$sel1 = new drop_select();
		$sel1->s_name = "$name_2";
		$sel1->has_empty = true;
		$sel1->arr = $room;
		$select=$sel1->get_select();
	}elseif($curr_id_kind=="職稱"){
		//職稱
		$sel1 = new drop_select();
		$sel1->s_name = "$name_2";
		$sel1->has_empty = true;
		$sel1->arr = title_kind();
		$select=$sel1->get_select();
	}elseif($curr_id_kind=="教師"){
		$select=&select_teacher("$name_2");
	}elseif($curr_id_kind=="學號"){
		$select="（空白欄位請輸入學生的學號）<br><input type='text' name='$name_2' size='6'>
		<input type='checkbox' name='$name_2' value='0' checked>授權給所有學生";
	}elseif($curr_id_kind=="家長"){
		$select="（空白欄位請輸入家長的流水編號）<br><input type='text' name='$name_2' size='6'>
		<input type='checkbox' name='$name_2' value='0' checked>授權給所有家長";
	}elseif(!empty($curr_id_kind)){
		$select="<input type='text' name='$name_2' size='8'>";
	}else{
		$select="";
	}
	
	//$root=(!empty($select) && $MODULE_MAN && $curr_id_kind=="教師")?"
	//prolin92-8-19修改放寬可以職稱
	$root=(!empty($select) && $MODULE_MAN && ($curr_id_kind=="教師" or $curr_id_kind=="職稱") )?"
        <select name='$name_3' size='1'>
        <option value='0' selected>一般權限</option>
        <option value='1'>管理權限</option>
		<option value='2'>核章權限</option>
		<option value='3'>免二層核章權</option>
        </select><br>權限說明: $MODULE_MAN_DESCRIPTION<br>":"";
        $main="$id_kind_sel $select $root";

//	$root=(!empty($select))?"<input type='hidden' name='$name_3' value='0'>
//	":"";
	$main="$id_kind_sel $select $root";
	return $main;
}


//判定模組是否為標準模組
function is_stand_module($dir,$dirname){
	if(file_exists($dir."/".$dirname."/module-cfg.php")){
		return true;
	}else{
		return  false;
	}
}

//重設使用者狀態
function reset_user_state() {
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//更新目前線上使用者狀態為重新取得授權代號
	$CONN->Execute("update pro_user_state set pu_state=2 where pu_state=1") or user_error("執行更新失敗！",256);
	return ;
}

//判斷某一分類的最後一個排序編號
function get_sort($of_group=0){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    $of_group=intval($of_group);
	$sql_select="select max(sort) from sfs_module where of_group='$of_group'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	list($sort)=$recordSet->FetchRow();
	$sort+=1;
	return $sort;
}



//該模組的上一層連結
function get_up_path($curr_msn=0){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if(empty($curr_msn))return "$_SERVER[PHP_SELF]";
	$curr_msn=intval($curr_msn);
	$sql_select="select of_group,showname from sfs_module where  msn='$curr_msn' order by sort";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	list($of_group,$showname)=$recordSet->FetchRow();
	$url="$_SERVER[PHP_SELF]?msn=$of_group";
	return $url;
}

//模組路徑階層路徑
function get_module_location($curr_msn=0,$home_name="首頁",$needlink=0){
    global $CONN,$SFS_PATH_HTML;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

        if(empty($curr_msn)){
                $m_name=($needlink)?"<a href='$_SERVER[PHP_SELF]'>$home_name</a>":$home_name;
                return $m_name;
        }
		$curr_msn=intval($curr_msn);
        $sql_select="select of_group,showname,kind from sfs_module where  msn='$curr_msn' order by sort";
        $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
        list($of_group,$showname,$kind)=$recordSet->FetchRow();
        $pre_path=get_module_location($of_group,$home_name,$needlink);
	if ($curr_msn == $_GET[msn])
        	$p.=($needlink)?$pre_path." / $showname":$pre_path."/ $showname";
	else
	        $p.=($needlink)?$pre_path." / <a href='$_SERVER[PHP_SELF]?msn=$curr_msn'>$showname</a>":$pre_path."/ $showname";

        return $p;
}

// 判斷該模組是否在 pro_module 中有委託變數控管
function in_pro_module($dirname) {

	return get_sfs_module_set($dirname);
	/*
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if ($dirname) {
		$sql="select pm_id from pro_module where pm_name='$dirname'";
		$recordSet=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		list($id)=$recordSet->FetchRow();
		if ($id) return true; else return false;
	}

	return false;
	*/
}
	
?>
