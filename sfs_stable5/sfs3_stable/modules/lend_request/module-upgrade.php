<?php
// $Id: module-upgrade.php 5310 2009-01-10 07:57:56Z hami $
if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name =$upgrade_str."2012-03-13.txt";
if (!is_file($up_file_name)){
	$query = "SELECT DISTINCT manager_sn,equ_serial FROM equ_request";
	$res=$CONN->Execute($query);
	if($res) {
		while(!$res->EOF) {
			//找出資料
			$manager_sn=$res->fields['manager_sn'];
			$equ_serial=$res->fields['equ_serial'];
			$res2=$CONN->Execute("SELECT sn FROM equ_equipments WHERE manager_sn=$manager_sn AND serial='$equ_serial'");
			$sn=$res2->fields['sn'];
			$CONN->Execute("UPDATE equ_request SET equ_serial='$sn' WHERE equ_serial='$equ_serial' AND manager_sn=$manager_sn");
			
			$res->MoveNext();
		}
		$temp_str = "更新成功!\n";
	} else	$temp_str = "更新失敗 !\n";
	$temp_query = "將 equ_serial 改為記錄 物品流水序號 -- by infodaes(2012-03-13)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}
?>
