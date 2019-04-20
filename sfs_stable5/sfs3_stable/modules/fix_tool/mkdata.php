<?php
//$Id: mkdata.php 7707 2013-10-23 12:13:23Z smallduh $
// 載入系統設定檔
require_once "config.php";

sfs_check();

$filename="table.xml";
header("Content-disposition: attachment; filename=$filename");
header("Content-type: application/octetstream ; Charset=Big5");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

header("Expires: 0");
echo "<?xml version=\"1.0\" encoding=\"BIG5\"?>\n";
echo "<Tables>\n";
$da_arr=array("Field","Type","Null","Key","Default");
$sql='SHOW TABLES;';
$res=$CONN->Execute($sql) or user_error("SHOW TABLES失敗！<br>$sql",256);
while(! $res->EOF){
	$table_name=$res->fields[0];
	echo "<$table_name>\n";
	echo "<Fields>\n";
	//echo "<Name>$table_name</Name>\n";
	$sql2="SHOW COLUMNS FROM `$table_name`;";
	$res2=$CONN->Execute($sql2) or user_error("SHOW COLUMNS失敗！<br>$sql2",256);
	while(! $res2->EOF){
		reset($da_arr);
		$field_name=$res2->fields[0];		
		echo "<$field_name>\n";
		foreach($da_arr as $d) if($d<>'Field') { echo "<".$d.">".$res2->fields[$d]."</".$d.">\n"; }
		echo "</$field_name>\n";		
		$res2->movenext();
	}
	echo "</Fields>\n";
	echo "</$table_name>\n";
	$res->movenext();
}
echo "</Tables>\n";
?>
