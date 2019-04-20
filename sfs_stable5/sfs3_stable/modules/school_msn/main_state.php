<?php
header('Content-type: text/html; charset=utf-8');
include ('config.php');
include_once ('my_functions.php');

mysql_query("SET NAMES 'utf8'");

if ($_POST['set']=="" and $_SESSION['MSN_LOGIN_ID']!="") {
$query="select * from sc_msn_online where teach_id='".$_SESSION['MSN_LOGIN_ID']."'";
$result=mysql_query($query);
$row=mysql_fetch_array($result,1);
?>
<html>
<head>
<title>變更目前我的狀態</title>
<style>
A:link {font-size:9pt;color:#ff0000; text-decoration: none}
A:visited {font-size:9pt;color: #ff0000; text-decoration: none;}
A:hover {font-size:9pt;color: #ffff00; text-decoration: underline}
td {font-size:12pt}
</style>
</head>
<script language="javascript">
		window.resizeTo(450,560)
  var XX=screen.availWidth
  var YY=screen.availHeight

	<?php
        if ($POSITION=="") $POSITION=0;
        switch ($POSITION) {
          case 0:  //右上
        		echo "var PX=XX-(390+450); \n";
        		echo "var PY=0;\n";
          break;
          case 1:  //左上
        		echo "var PX=391; \n";
        		echo "var PY=0;\n";
          break;

          case 2:  //正中
        		echo "var PX=0; \n";
        		echo "var PY=0;\n";
          break;

          case 3:  //右下
        		echo "var PX=XX-(390+450); \n";
        		echo "var PY=YY-560;\n";
          break;
        	
          case 4:  //左下
        		echo "var PX=391; \n";
        		echo "var PY=YY-560;\n";
          break;
        }
   ?>

window.moveTo(PX,PY);
function checkdata()
{
if (document.form1.state.value=='')
  {
 	alert('請輸入您的狀態！')
  document.form1.state.focus();
    return false;
  }else{
  	return true;
  }
}
</Script>
<body bgcolor="#99CCFF">
<table border="0" width="100%">
	<form name="form1" method="post" action="main_state.php" onsubmit="return checkdata()">
   <input type="hidden" name="set" value="updatting">
	 <tr>
	 	<td style="color:#FF0000">§變更狀態提示</td>
	 	<td align="left">
	 		<input type="text" value="<?php echo $row['state'];?>" name="state" size="10">
	 	  <input type="submit" value="送出" name="B1">
	 	  <input type="button" value="關閉" name="B2" onclick="window.close()"> 
	 	</td>
	</tr>
	 <tr>
	 	<td style="color:#FF0000">§私訊聲音提示</td>
	 	<td align="left" style="font-size:10pt">
	 	  <input type="radio" value="1" name="sound" <?php if ($row['sound']==1) echo "checked";?>>開啟
	 	  <input type="radio" value="0" name="sound" <?php if ($row['sound']==0) echo "checked";?>>關閉
	 	</td>
	</tr>
	<tr>
	 	<td style="color:#FF0000">§提示音種類</td>
	 	<td align="left" style="font-size:10pt">
	 	  <input type="radio" value="sound1" name="sound_kind" <?php if ($row['sound_kind']=='sound1') echo "checked";?>>泡泡
	 	  <input type="radio" value="sound2" name="sound_kind" <?php if ($row['sound_kind']=='sound2') echo "checked";?>> 敲擊
	 	  <input type="radio" value="sound3" name="sound_kind" <?php if ($row['sound_kind']=='sound3') echo "checked";?>> 錯誤
	 	  <input type="radio" value="sound4" name="sound_kind" <?php if ($row['sound_kind']=='sound4') echo "checked";?>> 電話鈴
	 	  <input type="radio" value="sound5" name="sound_kind" <?php if ($row['sound_kind']=='sound5') echo "checked";?>> 警示	  
	 	</td>
	</tr>
	</form>
</table>
<br>
◎可直接輸入或利用點選以下預設狀態再送出
<table width="100%" border="0">
	<tr>
		<td><input type="radio" value="1" name="R1" onclick="document.form1.state.value='上線'">上線</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='忙碌'">忙碌</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='上課中'">上課中</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='開會中'">開會中</td>
	</tr>
	<tr>	
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='有空'">有空</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='閒晃'">閒晃</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='可聊天'">可聊天</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='休息'">休息</td>
	</tr>
	<tr>	
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='高興'">高興</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='快樂'">快樂</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='生氣'">生氣</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='忿怒'">忿怒</td>
	</tr>
	<tr>	
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='難過'">難過</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='傷心'">傷心</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='發呆'">發呆</td>
		<td><input type="radio" value="2" name="R1" onclick="document.form1.state.value='無聊'">無聊</td>
	</tr>

</table>	
</body>
</html>	
<?php
exit();
}

if ($_POST['set']=="updatting") {
	$query="update sc_msn_online set state='".$_POST['state']."',sound='".$_POST['sound']."',sound_kind='".$_POST['sound_kind']."' where teach_id='".$_SESSION['MSN_LOGIN_ID']."'";
  mysql_query($query);
?>
<Script language="JavaScript">
	opener.window.location.reload(); //父視窗更新
	window.close();
</Script>
<?php
exit();
} //
?>

