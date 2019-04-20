<?php
                                                                                                                             
// $Id: stud_chg.php 8741 2016-01-07 06:30:22Z qfon $

//載入設定檔
include "exam_config.php";
//session_start();
if ($_SESSION[session_stud_id] == "" ){	
	$exename = $_SERVER[REQUEST_URI];
	include "checkid.php";
	exit;
}

//mysqli
$mysqliconn = get_mysqli_conn();

if ($_POST[key] == "修改") {
	$stud_sit_num = sprintf("%X-%X",$_POST[seat_col],$_POST[seat_row]);
	$stud_pass = chop ($_POST[stud_pass]); //去除空白
	//檢查密碼函式 (設定在 PLib.php)
	if (!chk_pass($stud_pass ,3,10)) {
		$str ="<B><font color=red size=4 >$stud_pass</font>  為不合法密碼，請重新輸入";
		echo $str;
		redir( $_SERVER[PHP_SELF] ,3);
		exit;
	}
	
	
//mysqli
$sql_update = "update exam_stud_data set stud_pass=?,stud_sit_num=?,stud_num=?,stud_memo=?, stud_c_time=? where stud_id ='$_SESSION[session_stud_id]' ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('sssss', $stud_pass,$stud_sit_num,$_POST[stud_num],$_POST[stud_memo],$stud_c_time);
$stmt->execute();
$stmt->close();

 if ($mysqliconn->affected_rows==1){
	
	 	$str ="你的新密碼為&nbsp;<B><font color=red size=4 >$stud_pass</font></b>";
		$str .=" , 三秒鐘後回到首頁";
		echo $str;
		redir( "exam_list.php" ,3);
		exit;	
	 
 }

///mysqli	
	
	/*
	$sql_update = "update exam_stud_data set stud_pass='$stud_pass',stud_sit_num='$stud_sit_num',stud_num='$_POST[stud_num]',stud_memo='$_POST[stud_memo]', stud_c_time='$stud_c_time' where stud_id ='$_SESSION[session_stud_id]' ";
	$result_ok = $CONN->Execute($sql_update) or die($sql_update);
	
	if ($result_ok) {
		$str ="你的新密碼為&nbsp;<B><font color=red size=4 >$stud_pass</font></b>";
		$str .=" , 三秒鐘後回到首頁";
		echo $str;
		redir( "exam_list.php" ,3);
		exit;	
	}
	*/
	
}

include "header.php";
echo "<h3>$exam_title</h3>\n";
echo "<b>更改 $session_stud_name 個人資料 </b>";
echo "&nbsp;｜&nbsp;<a href=\"exam_list.php\">回首頁</a>";
	
		
$sql_select = "select stud_pass,stud_sit_num,stud_num,stud_memo from exam_stud_data where stud_id ='$_SESSION[session_stud_id]' ";
$result = $CONN->Execute($sql_select);
$stud_pass = $result->fields["stud_pass"];
$stud_sit_num = $result->fields["stud_sit_num"];
$stud_num = $result->fields["stud_num"];
$stud_memo = $result->fields["stud_memo"];
if ($stud_sit_num != ""){	
	$temp_s = explode ("-", $stud_sit_num);
	$s_col = hexdec($temp_s[0]); //排
	$s_row = hexdec($temp_s[1]); //列	
}

?>
<hr>
<form action="<?php echo $_SERVER[PHP_SELF] ?>" method=post >
<table border =1>

<tr>
	<td align="right" valign="top">座號</td>
	<td><input type="text" size="6" maxlength="6" name="stud_num" value="<?php echo $stud_num ?>"></td>
</tr>

<tr>
	<td align="right" valign="top">密碼<BR>(使用英文或數字，在 3 至 10 個字內)</td>
	<td><input type="text" size="10" maxlength="10" name="stud_pass" value="<?php echo $stud_pass ?>"></td>
</tr>

<tr>
	<td align="right" valign="top">教室座位</td>
	<td>
	<?php
	echo "第<select name=seat_col>";
	for ($i=1;$i<=$class_cols ;$i++) {		
		if ($s_col == $i )
			echo "<option value=\"$i\" selected >$i</option>\n";
		else
			echo "<option value=\"$i\" >$i</option>\n";
	}
	echo "</select>排&nbsp;";
	echo "<select name=seat_row>";
	for ($i=1;$i<= $class_rows ;$i++) {		
		if ($s_row == $i )
			echo "<option value=\"$i\" selected >$i</option>\n";
		else
			echo "<option value=\"$i\">$i</option>\n";
	}
	echo "</select>列";
	?>
	</td>
</tr>
<tr>
	<td align="right" valign="top">自我簡介</td>
	<td><input type="text" size="60" maxlength="80" name="stud_memo" value="<?php echo $stud_memo ?>"></td>

</tr>

<tr>
	<td colspan=2 align=center>
	<input type="submit"  name="key" value="修改">&nbsp;&nbsp;<input type="reset" ></td>

</tr>

</table>
</form>
	
<?php include "footer.php"; ?>
