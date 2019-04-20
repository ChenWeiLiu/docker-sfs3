<?php

// $Id: sfs_core_systext.php 6136 2010-09-13 08:21:37Z brucelyc $

//系統選項文字
function SFS_TEXT($t_kind) {
	global $CONN,$SFS_PATH_HTML,$SFS_PATH;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	// init $res
	$res=array();

	$result = $CONN->Execute("select d_id,t_name from sfs_text where t_kind='$t_kind' and p_id > 0 order by t_order_id") or trigger_error("sfs_text 資訊表已更動! <a href=\"$SFS_PATH_HTML"."upgrade/change_sfs_text.php\">按此執行更新資料表</a>",E_USER_ERROR);
	//找不到時,取預設值
	if ($result->EOF) {
		$path = "$SFS_PATH/".get_store_path()."/module-cfg.php";
		include "$path";
		// 新增 sfs_text 記錄($SFS_TEXT_SETUP也是在module-cfg中設定)
		if(is_array($SFS_TEXT_SETUP)){
			while (list($id,$val) = each($SFS_TEXT_SETUP)) {
				$pm_g_id = trim($val['g_id']);
				$pm_item = trim($val['var']);
				$pm_arr = $val['s_arr'];
				join_sfs_text($pm_g_id,$pm_item,$pm_arr) or trigger_error("$pm_item, 無法加入選項清單 !", E_USER_ERROR);
			}
		}
		$result = $CONN->Execute("select d_id,t_name from sfs_text where t_kind='$t_kind' and p_id > 0 order by t_order_id") or trigger_error("sfs_text 資訊表已更動! <a href=\"$SFS_PATH_HTML"."upgrade/change_sfs_text.php\">按此執行更新資料表</a>",E_USER_ERROR);
	}
	while (!$result->EOF){
		$res[$result->fields[0]] = $result->fields[1];
		$result->MoveNext();
	}
	 return $res;
}

/**
 * 對CSV進行處理
 * @param resource handle
 * @param int length
 * @param string delimiter
 * @param string enclosure
 * @return 文件內容或FALSE。
 */
function sfs_fgetcsv(&$handle, $length = null, $d = ",", $e = '"') {
    $d = preg_quote($d);
    $e = preg_quote($e);

    $_line = "";
    $eof=false;
    while ($eof != true) {
        $_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
        $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
        if ($itemcnt % 2 == 0)
            $eof = true;
    }
   $_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));

    $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
    preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
    $_csv_data = $_csv_matches[1];

    for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
        $_csv_data[$_csv_i] = preg_replace("/^" . $e . "(.*)" . $e . "$/s", "$1", $_csv_data[$_csv_i]);
        $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
    }
    return empty ($_line) ? false : $_csv_data;
}
?>
