<?php
//$Id: del_comment.php 8238 2014-12-11 08:32:12Z chiming $
require_once("config.php");
//使用者認證
sfs_check();
if ($_POST['form_act']=='del_comm' && $_POST['ACT']!='no' && $_POST['ACT']!='' ){
	$Show_SQL='';
	$link=mysql_pconnect($mysql_host,$mysql_user,$mysql_pass) or die("無法連線");
	mysql_select_db($mysql_db,$link);
	$SQL="SHOW TABLES ";
	$rs=mysql_query($SQL,$link) or die($SQL);
	while ($row = mysql_fetch_array ($rs)){
		$SQL="ALTER TABLE `$row[0]` COMMENT ='' ";
		if ($_POST['ACT']=='list') {$Show_SQL=$Show_SQL.$SQL."<br>";}
    	if ($_POST['ACT']=='run_sql') {mysql_query($SQL,$link) or die($SQL);}
    	}
	if ($_POST['ACT']=='run_sql') header("Location:".$_SERVER['PHP_SELF']);
}
if ($_GET['act']=='ToMyISAM'){
	$a=array('student_view','teacher_course_view','teacher_post_view');
	$link=mysql_pconnect($mysql_host,$mysql_user,$mysql_pass) or die("無法連線");
	mysql_select_db($mysql_db,$link);
	$SQL="SHOW TABLE STATUS FROM  $mysql_db ";
	$rs=mysql_query($SQL,$link) or die($SQL);
	while ($row = mysql_fetch_array ($rs)){
	//if ($row['Engine']!='MyISAM' and !in_array($row['Name'],$a)){ 
	if ($row['Engine']=='InnoDB' and !in_array($row['Name'],$a)){ 
		//$SQL="use ".$mysql_db.";ALTER TABLE `".$row['Name']."` ENGINE=MYISAM; flush privileges;";
		$SQL="ALTER TABLE `".$row['Name']."` ENGINE=MYISAM";
		$rs1=mysql_query($SQL,$link) or die($SQL);
		}
	}
	mysql_query("flush privileges",$link);
	header("Location:".$_SERVER['PHP_SELF']);
}

head("資料庫備份輔助");
print_menu($school_menu_p);
?>
<div align="center"><h2>清除資料表的Comment註解</h2></div>
<table width=80% align=center>
<TR><td colspan=2 align=center>
<font  color="#FF0000">
本程式用於清除資料表的comment備註，以解決某些資料表的<font  color="#0000FF">
備註編碼</font>與<font  color="#0000FF">資料編碼</font>不一致的情況。<br>
程式的作法是掃描所有資料表並清除它們的備註，
請注意，清除後是無法復原的喔！<br>
所以您只要執行過一次，就可以使用下面的指令進行備份，而備份下來的資料也能正確的回復。</font><br>
參考語法：
<br>
mysqldump&nbsp; -u帳號&nbsp; -p密碼&nbsp; --default-character-set=latin1&nbsp; sfs3&nbsp; >&nbsp; sfs_DB.sql
</TD></TR>
<TR><td colspan=2 align=center>&nbsp;</TD></TR>
<FORM METHOD=POST ACTION='<?=$_SERVER[PHP_SELF]?>'>
<TR><TD align=right>學籍資料庫名稱:</TD><TD><?=$mysql_db?></TD></TR>
<TR><TD align=right>執行動作</TD><TD>
<select name="ACT" >
<option value="no">--未選擇--</option>
<option value="list">列示清除語法</option>
<option value="run_sql">執行清除動作</option>
</select>
</TD></TR>
<TR><td colspan=2 align=center>
<INPUT TYPE='hidden' Name='form_act' value=''>
<INPUT TYPE='reset' Value='重設' class=bur2 >
<INPUT TYPE='button' value='填好送出' onclick="if( window.confirm('確定刪除所有資料表的備註？確定？')){this.form.form_act.value='del_comm'; this.form.submit()}"><br>
◎◎◎<b><font color="#0000FF">本程式只要執行過一次即可</font></b>◎◎◎
</td></tr>
</FORM>
<TR><td colspan=2 style='font-size:9pt'>
<?php if ($_POST['ACT']=='list') echo $Show_SQL; ?>
<TR><td colspan=2 style='font-size:14pt'>
InnoDB格式的資料表如下：
</td></tr>
<TR><td colspan=2 style='font-size:10pt'>
<?php
$a=array('student_view','teacher_course_view','teacher_post_view');
$link=mysql_pconnect($mysql_host,$mysql_user,$mysql_pass) or die("無法連線");
mysql_select_db($mysql_db,$link);
$SQL="SHOW TABLE STATUS FROM  $mysql_db ";
$rs=mysql_query($SQL,$link) or die($SQL);
$ck='N';
// $Show_SQL='';
while ($row = mysql_fetch_array ($rs)){
if (!in_array($row['Name'],$a) and $row['Engine']=='InnoDB'){
	echo $row['Name'].'<br>';$ck='Y';
	//echo $row['Engine']."<br>";
}

//if ($row['Engine']!='MyISAM' and !in_array($row['Name'],$a)) $Show_SQL[]=$row['Name'];	
//echo "ALTER TABLE `".$row['Name']."`  ENGINE=MyISAM; flush privileges;<br>";
//echo "ALTER TABLE `".$row['Name']."` ENGINE=INNODB; flush privileges;<br>";
}
if ($ck=='Y'){
echo "<INPUT TYPE='button' value='將上述資料表全部改為MyISAM,以方便備份。' onclick=\"if( window.confirm('確定資料表改為MyISAM？確定？')){location.href='del_comment.php?act=ToMyISAM';}\">";
}else{
echo "<b style='color:red;'>沒有任何InnoDB的資料表</b><br>
InnoDB的資料表不支援以拷貝的方式備份<br>
改為MyISAM,備份及回復上會較方便。
";}
?></td></tr>
</table>

<?foot();?>

