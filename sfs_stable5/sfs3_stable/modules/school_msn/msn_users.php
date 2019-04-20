<?php
//$Id$
include "config.php";
include_once ('my_functions.php');
//認證
sfs_check();

//秀出網頁布景標頭
head("使用者狀態");
//主選單設定
$tool_bar=&make_menu($MODULE_MENU);

//儲存設定
if ($_POST['act']=='save') {
  $sql="update sc_msn_online set is_upload='0',is_email='0',is_showpic='0'";
  $res=$CONN->Execute($sql) or die("Error! sql=".$sql);
  foreach ($_POST['teach_setup'] as $teach_id=>$S) {
    $is_upload=$S['is_upload'];
    $is_email=$S['is_email'];
    $is_showpic=$S['is_showpic'];
    
    $sql="update sc_msn_online set is_upload='$is_upload',is_email='$is_email',is_showpic='$is_showpic' where teach_id='$teach_id'";
    $res=$CONN->Execute($sql) or die("Error! sql=".$sql);
    
  }
  
  $INFO="已於 ".date("Y-m-d H:i:s")."進行儲存...";
  
} // end if 

//列出選單
  echo $tool_bar;

$CONN->Execute("SET NAMES 'utf8'");

$O[1]="<font color=red>在線上</font>";
$O[0]="離線";
 //取得資料夾

$sql="select * from sc_msn_online order by teach_id";
//$sql="select * from sc_msn_folder order by idnumber";
$res=$CONN->Execute($sql);
$USERS=$res->GetRows();
 
 $CONN->Execute("SET NAMES 'latin1'");

?>
<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
<input type="hidden" name="act" value="">
<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">

校園MSN使用者登錄記錄及功能設定 -- 
<input type="button" value="儲存設定" onclick="document.myform.act.value='save';document.myform.submit()"><br>
<font color="red">
<?php
echo $INFO;
?>
</font>
<table border="1" style="border-collapse:collapse" color="#800000" cellpadding="2">
 <tr bgcolor="#CCCCFF">
 	<td align="center">帳號</td>
 	<td align="center">姓名</td>
 	<td align="center">使用次數</td>
 	<td align="center">最後時間</td>
 	<td align="center">登入IP</td>
 	<td align="center">目前狀態</td>
 	<td align="center">可用功能</td>
 </tr>
 <?php
 foreach ($USERS as $user) {
 	$bgcolor=($user['ifonline']==1)?"#FFEFEF":"#FFFFFF";
 	$sql="select name from teacher_base where teach_id='".$user['teach_id']."' and teach_condition=0";
 	$res=$CONN->Execute($sql);
 	if ($res->RecordCount()==0) continue;
 	$name=$res->fields[0];
 ?>
 <tr bgcolor="<?php echo $bgcolor;?>" style="font-size:10pt">
 	<td align="center"><?php echo $user['teach_id'];?></td>
 	<td align="center"><?php echo $name;?></td>
 	<td align="center"><?php echo $user['hits'];?></td>
 	<td align="center"><?php echo $user['lasttime'];?></td>
 	<td align="center"><?php echo $user['from_ip'];?></td>
 	<td align="center"><?php echo $O[$user['ifonline']];?></td>
 	<td>
 	 <input type="checkbox"	value='1' name="teach_setup[<?php echo $user['teach_id'];?>][is_upload]" <?php if ($user['is_upload']==1) echo "checked";?>>檔案分享
 	 <input type="checkbox"	value='1' name="teach_setup[<?php echo $user['teach_id'];?>][is_email]" <?php if ($user['is_email']==1) echo "checked";?>>發E-mail
 	 <input type="checkbox"	value='1' name="teach_setup[<?php echo $user['teach_id'];?>][is_showpic]" <?php if ($user['is_showpic']==1) echo "checked";?>>電子看板(圖)
 	</td>
 </tr>
 <?php
 }  // end foreach
 ?>

</table>
</form>