<?php
                                                                                                                             
// $Id: exam_new.php 8742 2016-01-08 13:57:14Z qfon $

// --系統設定檔
include "exam_config.php";

if(!checkid(substr($_SERVER[PHP_SELF],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}

$curr_class_id = sprintf("%03s%d",curr_year(),curr_seme());
$session_tea_name = addslashes($_SESSION['session_tea_name']);
if($_POST[key] =='新增'){
	$temp_arr = $_POST[e_kind_id];
	for ($i=0 ;$i<count($temp_arr);$i++) {
		
		//$sql_insert = "insert into exam (exam_id,exam_name,exam_memo,exam_isopen,exam_isupload,e_kind_id,teach_id,teach_name)values ('','$_POST[exam_name]','$_POST[exam_memo]','$_POST[exam_isopen]','$_POST[exam_isupload]','$temp_arr[$i]','$_SESSION[session_log_id]','$session_tea_name')";
 		//$CONN->Execute($sql_insert) or die($sql_insert);

//mysqli
$mysqliconn = get_mysqli_conn();	
$sql_insert = "insert into exam (exam_id,exam_name,exam_memo,exam_isopen,exam_isupload,e_kind_id,teach_id,teach_name)values ('',?,?,?,?,'$temp_arr[$i]','$_SESSION[session_log_id]','$session_tea_name')";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_insert);
$stmt->bind_param('ssss', $_POST[exam_name],$_POST[exam_memo],$_POST[exam_isopen],$_POST[exam_isupload]);
$stmt->execute();
$stmt->close();
///mysqli	
		
		//		echo $sql_insert;
 	}
header("Location: exam.php");
  
}
//取得班級名稱陣列
$class_name = class_base();
  
//目前有作業的班級
$query = "select class_id,e_kind_id from exam_kind where class_id like '$curr_class_id%' group by class_id order by class_id ";
$result = $CONN->Execute($query);
$class_select = "<select name=e_kind_id[] size=6 multiple>"; //班級選項
while (!$result->EOF) {
	$temp_class = substr($result->fields[class_id],-3);
	$class_select .= "<option value=\"".$result->fields[1]."\">$class_name[$temp_class]";
	$result->MoveNext();

}
$class_select .= "</select>";
include "header.php";
?>
<h3>新增作業</h3>
<script language="JavaScript">
function checkok()
{
	var OK=true
	if(document.eform.exam_name.value == "")
	{
		OK=false;
		str = '作業名稱不可空白';
	}
	
	if (OK == false)
	{
		alert(str)
	}	
	return OK
}

//-->
</script>

<form name=eform action="<?php echo $_SERVER[PHP_SELF] ?>" method="post" onSubmit="return checkok()">
<table border=1>
<tr>
	<td>班級(可多選)</td><td>
		<?php echo $class_select; ?>
	</td>
</tr>



<tr>
	<td>作業名稱</td><td>
		<input type="text" size="60" maxlength="60" name="exam_name" value="<?php echo $exam_name ?>">
	</td>
</tr>



<tr>
	<td>說明</td><td>
		<textarea name="exam_memo" cols=40 rows=5 wrap=virtual><?php echo $exam_memo ?></textarea>
	</td>
</tr>



<tr>
	<td>
		開始展示</td><td>
		<input type="checkbox" name="exam_isopen" value="1"> 是
	</td>
</tr>

<tr>
	<td>
		開始上傳作業</td><td>
		<input type="checkbox" name="exam_isupload" value="1"> 是
	</td>
</tr>


<tr>
	<td colspan=2 align=center>
		<input type="submit" name="key" value="新增">
		&nbsp;&nbsp;<input type="button"  value= "回上頁" onclick="history.back()">
	</td>
</tr>



</table>
</form>
<?php include "footer.php"; ?>


