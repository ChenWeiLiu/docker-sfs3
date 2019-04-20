<?php
//$Id: query.php 7847 2014-01-09 05:51:44Z hami $
include "config.php";
include "../../include/sfs_case_dataarray.php";

//認證
sfs_check();

//秀出網頁布景標頭
head("依班級座號查詢");

//主要內容
print_menu($school_menu_p);

$passwd=$_POST[email_pass];

if (count($passwd)>0 && $_POST['opt']=='update') {
	while(list($student_sn,$email_pass)=each($passwd)) {
		$ldap_password = createLdapPassword($email_pass);
		$query="update stud_base set email_pass='$email_pass' , ldap_password='$ldap_password'  where student_sn='$student_sn'";
		$CONN->Execute($query) or die($query);
		
		$sql="select stud_id,curr_class_num,stud_name from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($sql);
		
		$rec="<tr><td>".$res->fields[stud_id]."</td><td>".$res->fields[stud_name]."</td><td>".$res->fields[curr_class_num]."</td></tr>";
				
	}
	$INFO="<br><font color=red>已修改以下學生的密碼</font><br>
	 	<table border=0 cellspacing=1 cellpadding=2 bgcolor=#9ebcdd class=small>
		<tr bgcolor=#c4d9ff>
			<td align='center'>學號</td>
			<td align='center'>班級座號</td>
			<td align='center'>姓名</td>
		</tr>
	".$rec."</table>";
}

//顯示選項
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="hidden" name="opt" value="">
◎依現有班級座號查詢密碼：
<input type="text" name="class" value="<?php echo $_POST['class'];?>" size="5">班
<input type="text" name="num" value="<?php echo $_POST['num'];?>" size="5">號
<input type="button" value="查詢" onclick="document.myform.opt.value='search';document.myform.submit()">
<br>
※說明：<br>
1.僅查詢目前在籍學生。<br>
2.國小年級為 1-6，例如：三年六班，請輸入 306。<br>
3.國中年級為 7-9，例如：三年六班，請輸入 906。<br>

<?php
echo $INFO;
if ($_POST['opt']=='search') {

 $class_num=$_POST['class'].sprintf('%02d',$_POST['num']);
  
 echo "<br>◎查詢條件：".$class_num."<br>";
 
 $sql="select * from stud_base where curr_class_num='$class_num' and stud_study_cond='0'";
 $res=$CONN->Execute($sql) or die("Error! SQL=".$sql);
 if ($res->RecordCount()>0) {
 ?>
 	<table border=0 cellspacing=1 cellpadding=2 bgcolor=#9ebcdd class=small>
		<tr bgcolor=#c4d9ff>
			<td align='center'>學號</td>
			<td align='center'>班級座號</td>
			<td align='center'>姓名</td>
			<td align='center'>密碼</td>
		</tr>
 <?php
	while(!$res->EOF) {
		$email_pass=$res->fields[email_pass];
		$student_sn=$res->fields[student_sn];
		$stud_id=$res->fields[stud_id];
		$curr_class_num=$res->fields[curr_class_num];
		$stud_name=$res->fields[stud_name];
		?>
		<tr bgcolor=#ffffff>
			<td align='center'><?php echo $stud_id;?></td>
			<td align='center'><?php echo $curr_class_num;?></td>
			<td align='center'><?php echo $stud_name;?></td>
			<td align='center'><input type="text" name="email_pass[<?php echo $student_sn;?>]" value="<?php echo $email_pass;?>"></td>
		</tr>		
		<?php
		$res->MoveNext();
	}
	?>
	 </table>
	 <input type="button" value="修改密碼" onclick="document.myform.opt.value='update';document.myform.submit()">
	 <?php
 } else {
  echo "<font color=red>查無此學生!<font>";
 }
}
?>
</form>
<?php
//佈景結尾
foot();
?>
