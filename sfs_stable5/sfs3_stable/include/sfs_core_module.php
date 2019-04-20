<?php

// $Id: sfs_core_module.php 5999 2010-08-19 03:36:10Z brucelyc $

//取得該分類下的一層的所有啟動的模組詳細資料
function get_module($msn="") {
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//檢查資料庫更新程式
	include dirname(__FILE__)."/sfs_upgrade_list.php";

	$arr = array();

	//取得目前路徑下的模組
	if($msn=="other"){
		//取得其擁有權限的模組與分類
		$all_power=get_prob_power($_SESSION['session_tea_sn'],$_SESSION['session_who']);
		$ok_power=array_keys($all_power);

		//取得該身份應使用的搜尋條件
		$who_where=who_chk($_SESSION['session_tea_sn'],$_SESSION['session_who'] );

		//取得授權給該使用者可使用的分類或模組權限
		$sql_select = "select pro_kind_id from pro_check_new where $who_where";
		$recordSet=$CONN->Execute($sql_select)  or trigger_error("資料連結錯誤：".$sql_select, E_USER_ERROR);
		while(list($m)=$recordSet->FetchRow()){
			$where.="msn=$m or ";
		}

		// 上式未必有抓到資料
		if ($where) $where="of_group!=0 and (".substr($where,0,-4).")"; else return array();


	}else{
		$where=(empty($msn))?"kind='分類' and of_group='0'":"of_group='$msn' order by sort";

	}

	$sql_select="select * from sfs_module where islive='1' and $where";

	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$i=0;
	while($m=$recordSet->FetchRow()){
		$ofgroup=$m[of_group];
		if(in_array($ofgroup,$ok_power)) continue;
		$arr[$i]= $m;
		$i++;
	}
	return $arr;
}

//檢查使用者狀態
function check_user_state() {
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//配合中心端版本改變,記錄學校ID by hami 2003-3-26
	$session_prob = get_session_prot();
	//檢查使用者狀態
	$query = "select pu_state from pro_user_state where teacher_sn='{$_SESSION['session_tea_sn']}' and pu_state=2";
	$result = $CONN->Execute($query) or trigger_error("SQL 錯誤<Br>$query",E_USER_ERROR);
	if ($result->RecordCount() == 0) {
		//刪除超過一天記錄
		$CONN->Execute("delete from pro_user_state where now()-pu_time>1000000");
	}
	else {
		//重新取得模組
		$_SESSION[$session_prob]=get_prob_power($_SESSION['session_tea_sn'],$_SESSION['session_who']);
		$query = "update pro_user_state set pu_state=1 where teacher_sn='{$_SESSION['session_tea_sn']}'";
		$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	}
	return ;

}

//取得模組資料
function get_main_prob($id="",$pro_islive="",$name=""){
    global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$and_pro_islive=($pro_islive==1)?"and islive=1":"";
	if(!empty($id)){
		$w="msn='$id'";
	}elseif(!empty($name)){
		$w="dirname='$name'";
	}else{
		return;
	}

	// init $main
	$main=array();

	$sql_select = "SELECT * FROM sfs_module where $w $and_pro_islive";
    // 要檢查是否讀取成功?
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$main=$recordSet->FetchRow();
	return $main;
}

//取得某模組底下模組的編號
function get_parent_prob($id=""){
    global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = "SELECT * FROM sfs_module where of_group='$id' order by sort";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$main=array();
	while($all=$recordSet->FetchRow()){
		$main[]=$all;
	}
	return $main;
}


// 模組設定參照 : 抓取模組設定檔 module-cfg.php
// 這個函式已經準備去掉了，請勿再使用。請改用 get_module_setup()
function get_sfs_module_set($module_name='',$del=0) {
        global $CONN,$SFS_PATH;
        if ($module_name==''){
                $temp = get_store_path();
                $temp_arr = explode("/",$temp);
                $module_name=$temp_arr[count($temp_arr)-1];
        }
        $is_get_set = false;
        if ($del==1) { //刪除記錄
                $query = "delete from pro_module_main where pm_name='$module_name'";
                $CONN->Execute($query);
                $query = "delete from pro_module where pm_name='$module_name'";
                $CONN->Execute($query);
                $is_get_set =true;
        }
        else {
                $query = "select b.pm_item,b.pm_value from pro_module_main a ,pro_module b where a.pm_name=b.pm_name and a.pm_name='$module_name'";
                $res = $CONN->Execute($query);
                //已有預設值
                if (!$res->EOF) {
                        while(!$res->EOF) {
                                $res_arr[$res->fields[0]] = $res->fields[1];
                                $res->MoveNext();
                        }
                        return $res_arr;
                }
                else
                        $is_get_set =true;
        }

	 //加入預設值
        if ($is_get_set) {
		$default_set = $SFS_PATH."/modules/".$module_name."/module-cfg.php";
		if (!file_exists($default_set))
			trigger_error("找不到預設的檔案 $default_set", E_USER_ERROR);
		require "$default_set";
		while(list($id,$arr) = each($SFS_MODULE_SETUP)) {
			$pm_item = trim($arr['var']);
			$pm_memo = addslashes(trim($arr['msg']));
			if (is_array($arr['value'])){
				$temp_value = array_keys($arr['value']);
				$pm_value = addslashes($temp_value[0]);
			}
			else
				$pm_value = addslashes(trim($arr['value']));
			$query = "insert into pro_module(pm_name,pm_item,pm_memo,pm_value) values('$module_name','$pm_item','$pm_memo','$pm_value')";
			$CONN->Execute($query);
		}
		//刪除錯誤資料
		$CONN->Execute("delete from pro_module_main where pm_name=''");
		//加入 pro_module_main 資料
		$MODULE_PRO_KIND_NAME = addslashes($MODULE_PRO_KIND_NAME);
		if ($MODULE_UPDATE=="") $MODULE_UPDATE="0000-00-00";
		$query = "replace into pro_module_main(pm_name,m_display_name,m_ver,m_create_date) values('$module_name','$MODULE_PRO_KIND_NAME','$MODULE_UPDATE_VER','$MODULE_UPDATE')";
		$CONN->Execute($query) or trigger_error($query, E_USER_ERROR);

		$query = "select pm_item,pm_value from pro_module where pm_name='$module_name'";
		$res = $CONN->Execute($query);
		while(!$res->EOF) {
			$res_arr[$res->fields[0]] = $res->fields[1];
			$res->MoveNext();
		}
		return $res_arr;
	}

}


//取得該身份應使用的搜尋條件
function who_chk($sn="",$who=""){
	global $CONN,$conID;
	if($who=="教師"){
		//先取得該教師屬於哪個處室、哪個職稱
		$sql_select = "select teach_title_id,post_office from teacher_post where teacher_sn='$sn'";
		$recordSet=$CONN->Execute($sql_select)  or trigger_error("資料連結錯誤：".$sql_select, E_USER_ERROR);
		list($teach_title_id,$post_office) = $recordSet->FetchRow();

		$where="((id_kind='教師' and id_sn='$sn') or (id_kind='職稱' and id_sn='$teach_title_id') or (id_kind='處室' and id_sn='$post_office') or (id_kind='處室' and id_sn='99'))";
	}elseif($who=="家長"){
		$where="(id_kind='其他' and id_sn='$sn') or (id_kind='其他' and id_sn='0')";
	}elseif($who=="學生"){
		$where="(id_kind='學號' and id_sn='$sn') or (id_kind='學號' and id_sn='0') ";
	}elseif($who=="其他"){
		$where="id_kind='其他' and id_sn='$sn'";
	}
	//$where .=" and (p_end_date is null or p_end_date >= now())";

	//過渡時期作法，檢查學校是否有更新權限資料表，有加上權限到期日
	$fields = mysql_list_fields($mysql_db, "pro_check_new", $conID);
	$columns = mysql_num_fields($fields);
	$chk_end_date=false;
	for ($i = 0; $i < $columns; $i++) {
		if(mysql_field_name($fields, $i) =="p_end_date"){
			$chk_end_date=true;
		}
	}

	if($chk_end_date){$where .=" and (p_end_date is null or p_end_date >= now())";}

	return $where;
}


//取得登入者可使用的模組權限
function get_prob_power($sn="",$who=""){
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$msn_array=array();
	//取得該身份應使用的搜尋條件
	$where=who_chk($sn,$who);
	//取得授權給該使用者可使用的分類或模組權限
	$sql_select = "select pro_kind_id from pro_check_new where $where";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error("資料連結錯誤：".$sql_select, E_USER_ERROR);


	// init $ok_prob
	$ok_prob=array();

	while(list($pro_kind_id) = $recordSet->FetchRow()){
		//$prob[$pro_kind_id]=$is_admin;

		//看此$pro_kind_id是屬於分類還是模組
		$this_prob=get_main_prob($pro_kind_id);

		if($this_prob[kind]=="分類"){
			//找出底下所有分類以及模組，以便授權給他;
			$ok_prob_array=parent_prob_poser($pro_kind_id,$who,$sn);
			$ok_prob[$pro_kind_id]=$this_prob[of_group];
			foreach($ok_prob_array as $a=>$b){
				$ok_prob[$a]=$b;
			}
		}elseif($this_prob[kind]=="模組"){
			//假如是模組，表示該模組是額外授權給該使用者的;
			$ok_prob[$pro_kind_id]=$this_prob[of_group];
		}
	}
	return $ok_prob;
}

//取得某分類底下所有模組以及次分類的編號
function parent_prob_poser($pro_kind_id="",$who,$sn){
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if (!$who) user_error("沒有傳入參數！請檢查。",256);
	if (!$sn) user_error("沒有傳入參數！請檢查。",256);

	$sql_select = "select msn, isopen,of_group,kind from sfs_module where of_group='$pro_kind_id'";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error("資料連結錯誤：".$sql_select, E_USER_ERROR);

	// init $main
	$main=array();

	while(list($id,$isopen,$of_group,$kind) = $recordSet->FetchRow()){
		//該模組的使用者相關設定。

		if($kind=="分類"){
			//看看該分類有無授權，若沒額外授權表示繼承上個分類的權限，換言之就是允許使用
			$have_power=check_kind_have_power($id);
			if(!$have_power){
				//沒有額外授權的話，也將該分類授權，並往下找
				$main[$id]=$of_group;
				$mainkind=parent_prob_poser($id,$who,$sn);
				foreach($mainkind as $a=>$b){
					$main[$a]=$b;
				}
			}
		}elseif($kind=="模組"){
			$main[$id]=$of_group;
		}
	}
	return $main;
}

//看看該分類有無額外授權，若沒額外授權表示記乘上個分類的權限，換言之就是允許使用
function check_kind_have_power($msn){
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	// 確定有傳入參數
	if (!$msn) user_error("沒有傳入參數！請檢查。",256);

	//取得授權給該使用者可使用的分類或模組權限
	$sql_select = "select pro_kind_id from pro_check_new where pro_kind_id=$msn";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error("資料連結錯誤：".$sql_select, E_USER_ERROR);

	while(list($pro_kind_id) = $recordSet->FetchRow()){
		if($pro_kind_id) return true;
	}
	return false;
}

/*
//取得登入者可使用的模組權限
function get_prob_power($sn="",$who=""){
	global $CONN;
	$msn_array=array();

	if($who=="教師"){
		//先取得該教師屬於哪個處室、哪個職稱
		$sql_select = "select teach_title_id,post_office from teacher_post where teacher_sn='$sn'";
		$recordSet=$CONN->Execute($sql_select)  or trigger_error("資料連結錯誤：".$sql_select, E_USER_ERROR);
		list($teach_title_id,$post_office) = $recordSet->FetchRow();

		$where="((id_kind='教師' and id_sn='$sn') or (id_kind='職稱' and id_sn='$teach_title_id') or (id_kind='處室' and id_sn='$post_office') or (id_kind='處室' and id_sn='99'))";
		$kind_who="教師";
	}elseif($who=="家長"){
		$where="(id_kind='其他' and id_sn='$sn') or (id_kind='其他' and id_sn='0')";
		$kind_who="其他";
	}elseif($who=="學生"){
		$where="id_kind='學生' and id_sn='$sn'";
		$kind_who="學生";
	}elseif($who=="其他"){
		$where="id_kind='其他' and id_sn='$sn'";
		$kind_who="其他";
	}
	//取得授權給該教師或處室或職稱的模組
	$sql_select = "select pro_kind_id,is_admin from pro_check_new where $where";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error("資料連結錯誤：".$sql_select, E_USER_ERROR);

	while(list($pro_kind_id,$is_admin) = $recordSet->FetchRow()){
		if(in_array($pro_kind_id,$msn_array)){
		//如果只模組編號一樣，那麼以is_admin=1作為主要紀錄
			if($prob[$pro_kind_id]==0 and $is_admin=='1'){
				$prob[$pro_kind_id]=$is_admin;
			}
		}else{
			$prob[$pro_kind_id]=$is_admin;
			$msn_array[]=$pro_kind_id;
		}

		//找出該編號底下所有編號
		$c_prob=parent_prob_poser($pro_kind_id,$kind_who,$sn);
		$c_prob=substr($c_prob,0,-1);
		$child_prob=explode(",",$c_prob);
		if(sizeof($child_prob)>0){
			foreach($child_prob as $child_id){
				$prob[$child_id]=get_prob_power_set($child_id,$kind_who,$sn);
			}
		}
	}
	return $prob;
}



//取得某個模組，某人的授權狀態
function get_prob_power_set($pro_kind_id,$id_kind="",$id_sn=""){
	global $CONN;
	$sql_select = "select is_admin from pro_check_new where pro_kind_id='$pro_kind_id' and id_kind='$id_kind' and id_sn='$id_sn'";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error("資料連結錯誤：".$sql_select, E_USER_ERROR);
	list($is_admin) = $recordSet->FetchRow();
	if(empty($is_admin))$is_admin=0;
	return $is_admin;
}
*/


// 提供模組作者取得被控管的 "模組變數"，該模組作者便可針對該變數的現況，做適當的程式動作
// 比如：原先每頁顯示留言筆數的預設值為 15 筆，現在經由 "模組設定" 修改為 10，那麼
// 模組程式對此要有感知，此函式就是提供模組作者，方便取得這些變數的現況，而不必每位模組
// 作者都要針對這部份寫一個函式。
//
// 參數規定：
//
// 傳入：變數 $pm_name : 模組的英文名稱，例值：lunch
// 傳回：$MSETUP 一維陣列的參考(reference)
//
// 呼叫法用例：
//
// $pm_name="lunch";
// $MSETUP =& get_module_setup($pm_name)
//
// 經上述呼叫之後，模組作者取用變數的方法：
//
// 假設控管變數有： page_num 、have_line
//
// 則：
//
// $page_num  = $MSETUP[page_num];
// $have_line = $MSETUP[have_line]
//

// 取得被控管的 "模組變數" key 及 值
function &get_module_setup($pm_name) {
	global $CONN;

	// 沒有傳入模組名稱，不予處理。
	if (!$pm_name) trigger_error("錯誤： 沒有傳入模組英文名稱! 請檢查!", E_USER_ERROR);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	// 傳回陣列予以初始化，以免無值時，傳回不當。
	$MSETUP=array();

	// 取出控管變數的名稱及值
	$sql="SELECT pm_item, pm_value FROM pro_module WHERE pm_name='$pm_name'";

	// 對 select 結果一定要檢查是否有取出東東
	if (!($res=$CONN->Execute($sql))) {
		print $CONN->ErrorMsg();
	} else {
		while ($ar=$res->FetchRow()) {
			$MSETUP[$ar[0]]=$ar[1];
		}
	}
	// access 型態的 function，一定要有傳回值
	return $MSETUP;
}


// 建立模組路徑表（存成php實體）
function Creat_Module_Path(){
    global $CONN,$UPLOAD_PATH,$SFS_PATH_HTML;
    // 更改 array 讀入方式
    if(file_exists($UPLOAD_PATH."Module_Path.txt"))
	return true;
	// 確定連線成立
     if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);


    //先找出所有分類
    $sql_select = "SELECT msn,showname,dirname,sort,isopen,islive,of_group FROM sfs_module where kind='分類'";
    $recordSet=$CONN->Execute($sql_select) or user_error("SQL語法錯誤： $sql_select",256);
    $MPath  = array();

    while (list($msn, $showname, $dirname, $sort, $isopen, $islive, $of_group) = $recordSet -> FetchRow()) {
	    $g=($main_kind[$of_group])?" $main_kind[$of_group] /":"";
		$main_kind[$msn]="$g <a href='".$SFS_PATH_HTML."index.php?_Msn=$msn'>$showname</a>";
        $kk[] = $msn;
        $MPath[$msn]=$main_kind[$msn];
    }

    $sql_select = "SELECT msn,showname,dirname,sort,isopen,islive,of_group FROM sfs_module where kind='模組'";

    $recordSet = $CONN -> Execute($sql_select);
    while (list($msn, $showname, $dirname, $sort, $isopen, $islive, $of_group) = $recordSet -> FetchRow()) {
       $kn = (in_array($of_group, $kk))?$main_kind[$of_group]:"無";
       $MPath[$msn]="$kn / <a href='".$SFS_PATH_HTML."modules/$dirname/'>$showname</a>";
    }
    $string = serialize($MPath);

	//檢查 data 等資料表是否有建立，某些檔案讀寫權是否已經修正
	if (@!opendir($UPLOAD_PATH)) {
		user_error("模組路徑檔（Module_Path.php）開啟錯誤，可能原因如下：<ol><li>您可能尚未建立 <font color='blue'><b>$UPLOAD_PATH</b></font> 目錄。</li><li><font color='blue'><b>$UPLOAD_PATH</b></font>  目錄的屬性未設定成<font color='red'><b>可寫入</b></font>！<ul><li>Linux 下：<font color='darkGreen'><b>chmod 777 $UPLOAD_PATH</b></font></li></ul></li></ol>",256);
	}
	if(!is_writable ($UPLOAD_PATH)){
		user_error("<font color='blue'><b>$UPLOAD_PATH</b></font> 目錄無法寫入。<br>Linux 下：<font color='darkGreen'><b>chmod 777 $UPLOAD_PATH</b></font>",256);
	}

	//開個檔案寫入資料
	$fp = fopen ($UPLOAD_PATH."Module_Path.txt", "aw") or user_error("無法開啟 $UPLOAD_PATH 目錄",256);
	fputs($fp, $string);
	fclose($fp);
	return true;
}

//自動取得模組標題
function get_module_title(){
	global $CONN,$SFS_PATH_HTML,$UPLOAD_PATH,$MODULE_DIR;
	//模組標題取得優先順序：資料庫模組的標題，module-cfg.php中的標題，都沒有則自訂標題
	$SCRIPT_NAME=$_SERVER[SCRIPT_NAME];
	$SN=explode("/",$SCRIPT_NAME);
	$dirname=$SN[count($SN)-2];

	//取出標題
    $sql_select = "SELECT showname FROM sfs_module where dirname='$dirname'";
    $recordSet=$CONN->Execute($sql_select) or user_error("SQL語法錯誤： $sql_select",256);
    list($title)= $recordSet -> FetchRow();

	if(empty($title)){
		include_once $MODULE_DIR.$dirname."/module-cfg.php";
		$title=$MODULE_PRO_KIND_NAME;
	}

	return $title;
}

// 資料表升級函式
//
// $sql 為升級SQL指令
// $chk_field_arr 為檢查異動欄位陣列,預設值為空陣列,即不檢查
// $chk_field_arr 為二維陣列,定義如下:
//     $chk_field_arr[0][table_name] 資料表名
//     $chk_field_arr[0][field_name] 欄位名
//     $chk_field_arr[0][field_type] 欄位型態 (空值代表不檢查)
//     $chk_field_arr[0][check_in_table] 欄位存在資料表中 (0 -> 不存在, 1 -> 存在)

function upgrade_table($sql,$chk_field_arr=array()) {
        global $CONN;
        if (count($chk_field_arr)==0) {
                return $CONN->Execute($sql);
        }
        else {
                for($i=0;$i<count($chk_field_arr);$i++){
                        //列出欄位資料
                        $res = $CONN->MetaColumns($chk_field_arr[$i][table_name]);

                        $temp_flag = 0;
                        foreach($res as $v) {
                                if($v->name ==  $chk_field_arr[$i][field_name]){
                                        $temp_field_type = $v->type;
                                        $temp_flag= 1;
                                        break;
                                }
                        }
                        if(!($chk_field_arr[$i][check_in_table] ^ $temp_flag)) {
                                if ($chk_field_arr[$i][field_type] =='')
                                        $do_query_flag = true;
                                else if($chk_field_arr[$i][field_type] != $temp_field_type)
                                        $do_query_flag = true;
                                else
					$do_query_flag = false;
                        }
                        else
                                $do_query_flag = false;

                }
                if ($do_query_flag)
                        return $CONN->Execute($sql);
                else
                        return false;
        }
}
?>
