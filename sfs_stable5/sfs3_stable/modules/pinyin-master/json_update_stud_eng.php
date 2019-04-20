<?php
/*引入學務系統設定檔*/
include "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
if (empty($_SESSION['session_tea_sn'])){
	print "系統發生錯誤";
	exit;
}

$_POST = json_decode(file_get_contents('php://input'), true);
//print (sizeof($_POST));

$flag=1;
foreach($_POST as $users => $user){
		$sql = "UPDATE stud_base SET stud_name_eng=? WHERE stud_id=?";
		$rs=$CONN->Execute($sql,array($user['name'],$user['id']));

		if (!$rs) { $flag =0 ; }
	
}

if($flag==1){
	$msg = sprintf("共%d筆資料更新成功",sizeof($_POST));
} else{
	$msg = "資料寫入失敗";
}
print $msg;
