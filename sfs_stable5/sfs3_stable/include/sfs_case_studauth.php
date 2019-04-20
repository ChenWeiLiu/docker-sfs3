<?php

// $Id: sfs_case_studauth.php 5351 2009-01-20 00:39:21Z brucelyc $
// 取代 stu_chk.php
	//載入檢查	
	include_once "config.php";
	$teach_id = $_SESSION['session_log_id'];

// 不需要 register_globals
	if (!ini_get('register_globals')) {
		ini_set("magic_quotes_runtime", 0);
		extract( $_POST );
		extract( $_GET );
		extract( $_SERVER );
	}

	if(!checkid($chkpath,1))//授權管理檢查
	{
		head();  
		include ($SFS_PATH."/rlogin.php");
		foot();
		exit;
	 }
	if ($sel == "delete")
	{
		$query = "delete from pro_check_new where p_id='$pc_id'";
		$CONN->Execute($query);
	}
	if($key == "確定增加")
	{
		$query = "select student_sn,stud_name from stud_base where stud_id ='$stud_id' and curr_class_num <> '' ";
		$result = $CONN->Execute($query);
		if ($result->RecordCount() > 0)
		{
			$use_date = $now;
			$student_sn = $result->fields[student_sn];
			$sql_insert = "insert into pro_check_new (pro_kind_id,id_kind,id_sn,set_sn,p_start_date,p_end_date,oth_set) values ($pro_kind_id,'學號','$student_sn','$_SESSION[session_tea_sn]','$use_date','$use_last_date','$class_num')";
			$CONN->Execute($sql_insert) or die ($sql_insert);

		}
		else 
		{
			$notfount = "<font color=red><b>找不到學號 $stud_id </b></font>";
		}
	}

	$dirname =  explode("/",get_store_path($chkpath));

	if (checkid($chkpath,1)){	
		$dbquery = "select msn,showname from sfs_module  where dirname='$dirname[1]'"; 
		$result = $CONN->Execute($dbquery) or die("<br>資料庫連結錯誤<br>\n $dbquery");
		$msn = $result->fields["msn"];
		$showname = $result->fields["showname"];
		$use_last_date =  date("Y-m-d",mktime (0,0,0,date("m")+1,date("d"),date("Y"))); 
	}

head("授權學生操作");	
?>

<body  onload="setfocus()">
<script language="JavaScript"><!--
function setfocus() {
      document.cform.stud_id.focus();
      return;
 }
// --></script>
<center>
<form name="cform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
<input type="hidden" name="pro_kind_id" value="<?php echo $msn ?>">
<input type="hidden" name="teach_id" value="<?php echo $teach_id ?>">
<input type="hidden" name="chkpath" value="<?php echo $chkpath ?>">


<H3><b><font color=red><?php echo $showname ?></font></B> 授權下列學生操作</H3>

<?php if ($notfount !="") echo $notfount;?>

<table bgcolor=#CCFF66 >

<tr>
	<td	align="right" valign="top">學生學號</td>
	<td><input type="text" size="10" maxlength="20"	name="stud_id" value=""></td>
</tr>
<?php
//授權班級
if ($set_class == 1){
	//end($class_year);
	list ($key1, $val1) = each ($class_year);
	list ($key2, $val2) = each ($class_name);

	
	echo"<tr>
	<td	align=\"right\" valign=\"top\">授權修改班級</td>
	<td><input type=\"text\" size=\"10\" maxlength=\"20\"	name=\"class_num\" >例:";
	
	echo sprintf("%s%02d 代表 %s%s班",$key1,$key2,$val1,$val2);
	echo"</td>
	</tr>	
	<input type=\"hidden\" name=\"set_class\" value=\"1\">";
}
?>
<tr>
	<td	align="right" valign="top">使用期限</td>
	<td><input type="text" size="10" maxlength="10"	name="use_last_date" value="<?php echo $use_last_date ?>"> 格式(2000-11-1)</td>
</tr>
<tr>
	<td align="center" colspan=2><hr size=1><input type="submit" name="key" value="確定增加"></td>
</tr>
</table>
</form>
<!-- 列出目前學生 -->
<?php
$sql_select= "select b.*,a.stud_id,a.stud_name,a.email_pass from stud_base a,pro_check_new b where a.student_sn=b.id_sn and b.pro_kind_id='$msn' and b.id_kind='學號'";
$result = $CONN->Execute($sql_select) or die ($sql_select);
if ($result->RecordCount()>0 ) {
	echo "<TABLE border=1>
		<TR >
		<TD>學號</TD>
		<TD>密碼</TD>
		<TD>姓名</TD>
		<TD>授權班級</TD>
		<TD>截止日期</TD>
		<TD>授權教師</TD>
		<TD>刪除</TD>
	</TR>";

	while(!$result->EOF){
		$pc_id = $result->fields["p_id"];
		$pro_kind_id = $result->fields["pro_kind_id"];
		$student_sn = $result->fields["student_sn"];
		$teach_id =$result->fields["set_sn"];
		$use_date =$result->fields["p_start_date"];
		$stud_id =$result->fields["stud_id"];
		$stud_name =$result->fields["stud_name"];
		$email_pass =$result->fields["email_pass"];
		$class_num =$result->fields["oth_set"];
		if ($class_num =="")
			$class_num="-";
		$use_last_date = $result->fields["p_end_date"];
		$query = "select name from teacher_base where teacher_sn='$teach_id'";
		$result3 = $CONN->Execute($query);
		$teacher_name= $result3->fields[0];
		($i++ % 2 ==0)?	$bgcolor = "#CCFFFF":$bgcolor = "#CCCCFF";
		echo"<TR bgcolor=$bgcolor >
			<TD>$stud_id</TD>
			<TD>$email_pass</TD>
			<TD>$stud_name</TD>
			<TD>$class_num</TD>
			<TD>$use_last_date</TD>
			<TD>$teacher_name</TD>		
			<TD><A HREF=\"{$_SERVER['SCRIPT_NAME']}?set_class=$set_class&chkpath=$chkpath&sel=delete&pc_id=$pc_id&stud_id=$stud_id\">刪除</A></TD>
	</TR>";
		$result->MoveNext();
	}
	echo "</TABLE>";
}
echo "<a href=\"$SFS_PATH_HTML"."modules/$dirname[1]/\">返回 $showname </a></center><br>";
?>

<?php foot(); ?>
