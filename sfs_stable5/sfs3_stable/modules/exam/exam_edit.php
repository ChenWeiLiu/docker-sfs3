<?php                                                                                                                             
// $Id: exam_edit.php 8742 2016-01-08 13:57:14Z qfon $

// --系統設定檔
include "exam_config.php";

if(!checkid(substr($_SERVER[PHP_SELF],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}

//mysqli
$mysqliconn = get_mysqli_conn();

$exam_id=$_GET[exam_id];
if($exam_id=='')
	$exam_id = $_POST[exam_id];
//檢查是否為作者
$exam_id=intval($exam_id);
$query = "select exam_id from exam where exam_id='$exam_id' and teach_id = '$_SESSION[session_log_id]'";
$result = $CONN->Execute($query);
if ($result->RecordCount() == 0 ) {
	echo "你非作者，無權修改或刪除!!" ;
	redir("exam.php",3) ;
	exit;
}


if ($_GET[sel] =="delete"){
	include "header.php";
	echo "<form action=\"$_SERVER[PHP_SELF]\" method=\"post\">\n"; 
	echo "確定刪除 <font color=red>".stripslashes ($_GET[exam_name])."</font> ？<br>";
	echo "<input type=\"hidden\" name=\"exam_id\" value=\"$_GET[exam_id]\">\n";
	echo "<input type=\"submit\" name=\"key\" value=\"確定刪除\" >\n";
	echo "&nbsp;&nbsp;<input type=button  value= \"回上頁\" onclick=\"history.back()\">";
	echo "</form>";
	include "footer.php";
	exit;
}
if ($_POST[key] =="確定刪除") {
	//刪除該作業目錄所有資料
	$e_path = $upload_path."/e_".$exam_id; 
	if (is_dir($e_path))
		exec( "rm -rf $e_path", $val );
	//刪除作業	
	/*
	$sql_update = " delete from exam ";
	$sql_update .= " where exam_id='$exam_id' ";
	$CONN->Execute($sql_update)  or die ($sql_update);
	*/

///mysqli	
$sql_update = " delete from exam ";
$sql_update .= " where exam_id=? ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('s', $exam_id);
$stmt->execute();
$stmt->close();
///mysqli
	
	//刪除學生上傳記錄
	/*
	$sql_update = " delete from exam_stud ";	
	$sql_update .= " where exam_id='$exam_id' ";
	$CONN->Execute($sql_update)  or die ($sql_update);
	*/
///mysqli	
$sql_update = " delete from exam_stud ";	
$sql_update .= " where exam_id=? ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('s', $exam_id);
$stmt->execute();
$stmt->close();
///mysqli
	header ("Location: exam.php");
	exit;
}
if ($_POST[key] =="修改"){
	/*
	$sql_update = "update exam set exam_id='$_POST[exam_id]',exam_name='$_POST[exam_name]',exam_memo='$_POST[exam_memo]',exam_isopen='$_POST[exam_isopen]',e_kind_id='$_POST[e_kind_id]' ,teach_id='$_SESSION[session_log_id]',teach_name='$_SESSION[session_tea_name]' ";
	$sql_update .= " where exam_id='$_POST[exam_id]' ";	
	$CONN->Execute($sql_update)  or die ($sql_update);  
	*/
	
///mysqli	
$sql_update = "update exam set exam_id=?,exam_name=?,exam_memo=?,exam_isopen=?,e_kind_id=? ,teach_id='$_SESSION[session_log_id]',teach_name='$_SESSION[session_tea_name]' ";
$sql_update .= " where exam_id=? ";	
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('ssssss',$_POST[exam_id],$_POST[exam_name],$_POST[exam_memo],$_POST[exam_isopen],$_POST[e_kind_id],$_POST[exam_id]);
$stmt->execute();
$stmt->close();
///mysqli	
	header ("Location: exam.php");
	exit;
}
$_GET[exam_id]=intval($_GET[exam_id]);
$sql_select = "select exam.*  from exam,exam_kind ";
$sql_select .=" where exam.e_kind_id=exam_kind.e_kind_id and exam.exam_id='$_GET[exam_id]' ";  
$result = $CONN->Execute($sql_select);

while (!$result->EOF) {
	$exam_id = $result->fields["exam_id"];
	$exam_name = $result->fields["exam_name"];
	$exam_memo = $result->fields["exam_memo"];
	$exam_isopen = $result->fields["exam_isopen"];
	//$exam_source_isopen = $result->fields["exam_source_isopen"];
	$e_kind_id = $result->fields["e_kind_id"];
	if ($result->fields["exam_isopen"]=='1')
		$exam_isopen = " checked ";
	else
		$exam_isopen = " ";
	$result->MoveNext();	
};

$temp_year = substr($_GET[class_id],4,1); //取得年級	
$temp_class = sprintf("%02d",substr($class_id,5)); //取得班級
include "header.php";

?>
修改作業內容
<form action ="<?php echo $_SERVER[PHP_SELF] ?>" method="post" >
<input type=hidden name=exam_id value="<?php echo $exam_id ?>">
<table>
<tr>
	<td>班級：<?php echo "$class_year[$temp_year]$class_name[$temp_class]班" ?>
	</td>
</tr>


<tr>
	<td>作業名稱<br>
		<input type="text" size="60" maxlength="60" name="exam_name" value="<?php echo $exam_name ?>">
	</td>
</tr>



<tr>
	<td>說明<br>
		<textarea name="exam_memo" cols=40 rows=5 wrap=virtual><?php echo $exam_memo ?></textarea>
	</td>
</tr>



<tr>
	<td>
		開始展示<br>
		<input type="checkbox" name="exam_isopen" value="1" <?php echo $exam_isopen ?>>
	</td>
</tr>


<tr>
	<td>
		<input type= "hidden" name = "e_kind_id" value="<?php echo $e_kind_id ?>">
		<input type="submit" name="key" value="修改">
		&nbsp;&nbsp;<input type="button"  value= "回上頁" onclick="history.back()">
	</td>
</tr>



</table>
</form>

<?php include "footer.php"; ?>
