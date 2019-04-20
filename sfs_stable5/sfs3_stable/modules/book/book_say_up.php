<?php
//$Id: book_say_up.php 8753 2016-01-13 12:40:19Z qfon $
include "book_config.php";

if (!checkid($_SERVER[SCRIPT_FILENAME],1)){
	include "header.php";
	echo "<h3>非管理者勿進入</h3>";
	include "footer.php";
	exit;
}
$postBtn = "確定";

//mysqli
$mysqliconn = get_mysqli_conn();

if ($_POST[do_key]==$postBtn){
	//修改
	$bs_title = AddSlashes($_POST[bs_title]);
	$bs_con = AddSlashes($_POST[bs_con]);
	if ($_POST[bs_id]<>''){
		/*
		$sql_update = "update book_say set bs_title='$_POST[bs_title]',bs_con='$_POST[bs_con]',us_id='$_SESSION[session_tea_sn]' where bs_id='$_POST[bs_id]'";
		$CONN->Execute($sql_update) or trigger_error($sql_update,E_USER_ERROR);
	     */
///mysqli
$sql_update = "update book_say set bs_title=?,bs_con=?,us_id='$_SESSION[session_tea_sn]' where bs_id=?";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('sss', $_POST[bs_title],$_POST[bs_con],$_POST[bs_id]);
$stmt->execute();
$stmt->close();
///mysqli
	
	}
	else {
		/*
		$sql_insert = "insert into book_say (bs_title,bs_con,us_id) values ('$_POST[bs_title]','$_POST[bs_con]','$_SESSION[session_tea_sn]')";
		$CONN->Execute($sql_insert) or trigger_error($sql_update,E_USER_ERROR);
        */
///mysqli
$sql_insert = "insert into book_say (bs_title,bs_con,us_id) values (?,?,'$_SESSION[session_tea_sn]')";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_insert);
$stmt->bind_param('ss', $_POST[bs_title],$_POST[bs_con]);
$stmt->execute();
$stmt->close();
///mysqli
		
		
	}

	header("Location: booksay_edit.php");
	exit;
}


include "header.php";
if ($_GET[do_key]=='edit'){
	$_GET[bs_id]=intval($_GET[bs_id]);
	$query = "select * from book_say where bs_id='$_GET[bs_id]'";
	$recordSet = $CONN->Execute($query);
	$bs_title = $recordSet->fields["bs_title"];
	$bs_con = $recordSet->fields["bs_con"];

}
?>
<table BORDER=0 CELLPADDING=10 CELLSPACING=0 CLASS="tableBg" WIDTH="100%" ALIGN="CENTER"> 
<tr>
<td >
<form name="myform_2" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="bs_id" value="<?php echo $_GET[bs_id] ?>">
  <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body" >

<tr>
	<td align="right" CLASS="title_sbody1">標題</td>
	<td CLASS="gendata"><input type="text" size="30" maxlength="30" name="bs_title" value="<?php echo $bs_title ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1">內容</td>
	<td><textarea name="bs_con" cols=40 rows=5 wrap=virtual><?php echo $bs_con ?></textarea></td>
</tr>

<tr>
	
	<td align=center colspan=2><input type="submit"  name="do_key" value="<?php echo $postBtn ?>"></td>
</tr>
</table>

</form>
</td>
</tr>

</table>

<?php
include "footer.php";
?>
