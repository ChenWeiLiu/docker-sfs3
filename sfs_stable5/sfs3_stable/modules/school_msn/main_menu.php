<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');


//$_SESSION['MSN_LOGIN_ID'] 登入帳號

?>
<head>
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<title>訊息</title>
<style>
A:link {font-size:9pt;color:#ff0000; text-decoration: none}
A:visited {font-size:9pt;color: #ff0000; text-decoration: none;}
A:hover {font-size:9pt;color: #ffff00; text-decoration: underline}
input.selectoption {
 background-color:#FFCCFF;
 border-color:#0000FF;
 border-width:1pt;
 padding: 1 1 1 1;
 color:#800000;
 font-size:8pt;
 cursor:hand
}
</style>
</head>
<body bgcolor="#ccccff" leftmargin="0" topmargin="0">
<table border="0" cellspacing="0" width="100%" cellpadding="0" valign="center">
  <tr>
    <td width="100%" align="left" style="font-size:8pt">

<?php
if ($_GET['act']=='logout') {
	mysql_query("SET NAMES 'utf8'");
	$query="update sc_msn_online set ifonline='0' where teach_id='".$_SESSION['MSN_LOGIN_ID']."'";
	mysql_query($query);
  $_SESSION['MSN_LOGIN_ID']="";
  echo "<Script>reload()</Script>";
}

if ($_GET['act']=='login') {
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" name="form1" onsubmit="return checkdata()">
	帳:<input type='text' name='log_id' tabindex="1" size='7' style="font-size:10pt">
	密:<input type='password' name='log_pass' tabindex="2" size='7' style="font-size:10pt">
	<input type="submit" name="act" tabindex="3" value="登入" title="請使用學務系統帳號">
	<input type="button" value="放棄" tabindex="4"  onclick="window.location='main_menu.php'">
</form>
<?php
}

if ($_POST['act']=='登入') {
	
$log_id=$_POST['log_id']; 
$log_pass=pass_operate($_POST['log_pass']);
if ($IS_UTF8==0) mysql_query("SET NAMES 'latin1'");
$query="select teacher_sn, login_pass from teacher_base where teach_condition=0 and teach_id='$log_id' and login_pass='$log_pass' and teach_id<>''";
$result=mysql_query($query);

if (mysql_num_rows($result)) {

	$_SESSION['MSN_LOGIN_ID']=$log_id;
	//記錄上線
   mysql_query("SET NAMES 'utf8'");
	 $my_ip=$_SERVER['REMOTE_ADDR'];
   $onlinetime=date("Y-m-d H:i:s");
	 mysql_query("SET NAMES 'utf8'");
   $query="select * from sc_msn_online where teach_id='$log_id'";
   $result=mysql_query($query);
   //已登入過 MSN 
   if (mysql_num_rows($result)) {
   	  $row=mysql_fetch_array($result,1);
   	  $hits=$row['hits'];
   	  $_SESSION['is_email']=$row['is_email'];
   	  $_SESSION['is_showpic']=$row['is_showpic'];
   	  $_SESSION['is_upload']=$row['is_upload'];
   	  $hits++;
   	  $query="update sc_msn_online set onlinetime='".date("Y-m-d H:i:s")."',ifonline='1',hits='$hits' where teach_id='$log_id'";
      mysql_query($query);
   }else{
   	if ($IS_UTF8==0) {
   		mysql_query("SET NAMES 'latin1'");
    	$name=big52utf8(get_teacher_name_by_id($_SESSION['MSN_LOGIN_ID']));
    } else {
      $name=get_teacher_name_by_id($_SESSION['MSN_LOGIN_ID']);
    }
  	  mysql_query("SET NAMES 'utf8'");
   	  $query="insert into sc_msn_online (teach_id,name,from_ip,lasttime,onlinetime,ifonline,state,hits) values ('".$_SESSION['MSN_LOGIN_ID']."','".$name."','".$my_ip."','".$onlinetime."','".$onlinetime."','1','上線','1')";
    if (!mysql_query($query)) {
      echo "$query=".$query;
      exit;
    }
   }
  
	//自動清理過期的留言, 私人訊息
	$query="select * from sc_msn_data where to_days(curdate()-".$PRESERVE_DAYS.")>(to_days(post_date)) and data_kind=1";
  //只刪除已讀取的
  if ($CLEAN_MODE) $query.=" and ifread=1";
  
  $result=mysql_query($query);
  
  while ($row=mysql_fetch_array($result,1)) {
   //刪除本篇留言附檔
   delete_file($row['idnumber'],$row['to_id']);
   $query="delete from sc_msn_data where id='".$row['id']."'";
   mysql_query($query);
  }//end while
 } else {
 	$INFO="-帳號或密碼錯誤！";  
 } // end if (mysql_num_rows($result))

} // end if 登入
?>
<img border="0" src="./images/start.gif" align="absmiddle">
<img style="cursor:pointer" border="1" src="./images/reload.jpg" onclick="parent.location.reload()" title="更新畫面" align="absmiddle">
<?php
//若有登入
if ($_SESSION['MSN_LOGIN_ID']!="") {
	
//取得教師等級
if ($IS_UTF8==0) mysql_query("SET NAMES 'latin1'");
$query="select teacher_sn from teacher_base where teach_id='".$_SESSION['MSN_LOGIN_ID']."'";
$result=mysql_query($query);
list($teacher_sn)=mysql_fetch_row($result);
$query="select post_kind from teacher_post where teacher_sn='".$teacher_sn."'";
$result=mysql_query($query);
list($POST_KIND)=mysql_fetch_row($result);

mysql_query("SET NAMES 'utf8'");	

 $MyName=get_name_state($_SESSION['MSN_LOGIN_ID']);	
 ?>

<img style="cursor:pointer" border="1" src="./images/post.jpg" onclick="msg_post();" title="提供「校內訊息交流」內容或傳遞「私人訊息」" align="absmiddle">
<img style="cursor:pointer" border="1" src="./images/download.jpg"  onclick="download();" title="下載檔案" align="absmiddle">
<img style="cursor:pointer" border="1" src="./images/manage.jpg" onclick="msg_manage();" title="管理個人訊息" align="absmiddle">
<img style="cursor:pointer" border="1" src="./images/online.jpg" onclick="msg_online();" title="查看誰在線上" align="absmiddle">
<img style="cursor:pointer" border="1" src="./images/state.jpg"  onclick="state();" title="設定我的狀態" align="absmiddle">
<?php
if ($m_arr['portfolio']) {
	?>
 <img style="cursor:pointer" border="1" src="./images/myweb.jpg" onclick="web();" title="教師網頁" align="absmiddle">
 <?php	
 }
 ?>
<img style="cursor:pointer" border="1" src="./images/logout.jpg" onclick="window.location='main_menu.php?act=logout'" title="登出" align="absmiddle">
<?php
 echo "-".$MyName[1];
} else {

  if($is_home_ip ) {
  ?>
  <img style="cursor:pointer" border="1" src="./images/online.jpg" onclick="msg_online_withoutlogin();" title="查看誰在線上" align="absmiddle">
  <?php
  }
  ?>
 <input type="button" value="登入" onclick="window.location='main_menu.php?act=login'" title="請使用學務系統帳號登入，登入才能享完整功能">
 <?php
 echo $INFO;
} // end if login
?>
</td>
</tr>
</table>
</body>
</html>
<Script language="JavaScript">
<?php
	if (($_POST['act']=='登入' and $_SESSION['MSN_LOGIN_ID']) or ($_GET['act']=='logout' and $_SESSION['MSN_LOGIN_ID']==""))  
	  echo "parent.location.reload();";
?>

//重整畫面
function reload()
{
window.main.replace('main_window.php');
}

//發送訊息
function msg_post()
{
 if(window.flagWindow) flagWindow.focus();
 flagWindow=window.open('main_message.php?act=post','MessagePost','width=450,height=560,resizable=1,toolbar=no,scrollbars=auto');
}
//檔案下載
function download()
{
 if(window.flagWindow) flagWindow.focus();
 flagWindow=window.open('main_download_list.php','DownLoad','width=780,height=560,resizable=1,toolbar=no,scrollbars=yes');
}
//訊息管理
function msg_manage()
{
 if(window.flagWindow) flagWindow.focus();	
 flagWindow=window.open('main_mlist.php','MessageManage','width=800,height=560,resizable=1,toolbar=no,scrollbars=yes');
}
//誰在線上
function msg_online()
{
 if(window.flagWindow) flagWindow.focus();	
 flagWindow=window.open('main_online.php','MessagePost','width=450,height=560,resizable=1,toolbar=no,scrollbars=auto');
}
function msg_online_withoutlogin()
{
 if(window.flagWindow) flagWindow.focus();	 
 flagWindow=window.open('main_online_withoutlogin.php','MessagePost','width=450,height=560,resizable=1,toolbar=no,scrollbars=auto');
}

//我的狀態
function state()
{
 if(window.flagWindow) flagWindow.focus();
 flagWindow=window.open('main_state.php','MessagePost','width=450,height=560,resizable=1,toolbar=no,scrollbars=auto');
}

//訊息管理
function web()
{
 if(window.flagWindow) flagWindow.focus();	
 flagWindow=window.open('main_teachers_web.php','MessageManage','width=800,height=560,resizable=1,toolbar=no,scrollbars=yes');
}

//登入驗證
function checkdata() {
sFlag=true
if (document.form1.log_id.value=='')
  {
  	document.form1.log_id.focus()
  	sFlag=false
  }
 if (document.form1.log_pass.value=='')
  {
  	document.form1.log_pass.focus()
  	sFlag=false
  }
if (sFlag)
  {
  return sFlag
  }else{
  alert('帳號密碼不能是空白！')
  return sFlag
  }
}

 if(window.flagWindow) flagWindow.focus();

<?php
if ($_GET['act']=='login') echo "document.form1.log_id.focus();";
?>

</Script>
