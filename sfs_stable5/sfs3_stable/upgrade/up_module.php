<?php
include "../include/config.php";
include "../include/sfs_case_PLlib.php";
include "update_function.php";
set_time_limit(600) ;
$oth_arr = array("book"=>"圖書管理系統","compclass"=>"空堂預約系統","docup"=>"文件資料庫","board"=>"校務佈告欄");
if ($_POST[do_key]=='執行升級') {
	switch ($_POST[sel_key]){
		case "book":

		if(check_field($mysql_db,$conID,'borrow','student_sn')){
			trigger_error("圖書管理系統 已經升級!",E_USER_ERROR);
			break;
		} else {
			up_teacher_sn("borrow","stud_id","student_sn");
		//	up_student_sn("borrow");
		}
		break;
	
		case "compclass":
		if(check_field($mysql_db,$conID,'compclass','teacher_sn')){
			trigger_error("空堂預約系統 已經升級!",E_USER_ERROR);
			break;
		} else 
			up_teacher_sn("compclass");
		break;

		case "docup":
		if(check_field($mysql_db,$conID,'docup_owerid','teacher_sn')){
			trigger_error("空堂預約系統 已經升級!",E_USER_ERROR);
			break;
		} else  {
			up_teacher_sn("docup","docup_owerid");
			up_teacher_sn("docup_p","docup_p_ownerid");
		}
		
		break;
		case "board":
		if(check_field($mysql_db,$conID,'board_check','teacher_sn')){
			trigger_error("校務佈告欄程式 已經升級!",E_USER_ERROR);
			break;
		} else  {
			up_teacher_sn("board_check");
			up_teacher_sn("board_p","b_own_id");
		}
		
		break;

	}
	$is_upgrade = true;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>模組升級</title>
</head>
<body>
<?php
if($is_upgrade) {
	echo "<script>\n
	confirm('您已經完成系統 ".$oth_arr[$_POST[sel_key]]." 升級動作 \\n按確定鍵進行其他模組升級');\n
	</script>\n";
}

?>

<h3>SFS3 模組升級說明</h3>
<form action="up_module.php" method="POST">
<table cellpadding="0" bgcolor="#BEE0EE" width="600">
<tr><td>
您已經完成了系統基本的升級動作，在下表中，選擇您要升級的模組。 
</td></tr>
<tr>
<td>

<?php
while(list($id,$val) = each($oth_arr))  {
	echo "<input name='sel_key' type='radio' value='$id'> $val ( $id )<br>";

}

?>
<input type="submit" name="do_key" value="執行升級">
</td></tr>

</table>
</form>
</body>
</html>
