<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');

mysql_query("SET NAMES 'utf8'");

if (!isset($_SESSION['MSN_LOGIN_ID'])) {
  echo "<Script language=\"JavaScript\">window.close();</Script>";
	exit();
}

$set=($_POST['set']=='')?'my_msg':$_POST['set'];

//按下了刪除鈕
if ($_POST['act']=='del') {
 //刪電子圖文
 if ($set=='my_pic') {
  foreach($_POST['id'] as $id) {
     	$query="select * from sc_msn_board_pic where id='$id'"; 
 		 	$result=mysql_query($query);
  		$row=mysql_fetch_array($result,1);
  		  //縮圖
  		  $a=explode(".",$row['filename']);
  	   	$filename_s=$a[0]."_s.".$a[1];
  		 unlink($UPLOAD_PIC.$row['filename']);
       unlink($UPLOAD_PIC.$filename_s);
			$query="delete from sc_msn_board_pic where id='$id'";
  		mysql_query($query);
  } // end foreach
 //刪其他的
 } else {
  foreach($_POST['id'] as $id) {
    	$query="select * from sc_msn_data where id='$id'"; //不能用 idnumber , 一封訊息傳給多人時, 會用同一 個idnumber, 然後也對應同一個檔案
 		 	$result=mysql_query($query);
  		$row=mysql_fetch_array($result,1);
  		if ($row['data_kind']==1 or $row['data_kind']==2) {
  	 	  //刪除附檔
   		  delete_file ($row['idnumber'],$row['to_id']);
   	  }
			$query="delete from sc_msn_data where id='$id'";
  		mysql_query($query);
  } // end foreach 
 } // end if else $_POST['set']
 
   
} // end if del



switch ($set) {
 case 'my_msg':  //別人給我的訊息
  $query="select * from sc_msn_data where to_id='".$_SESSION['MSN_LOGIN_ID']."'  and data_kind=1 order by post_date desc";
 break;
 case 'my_msg_post':  //我發的訊息
  $query="select * from sc_msn_data where teach_id='".$_SESSION['MSN_LOGIN_ID']."' and data_kind=1 order by post_date desc";
 break;
 case 'my_file':  //我分享的檔案
  $query="select * from sc_msn_data where teach_id='".$_SESSION['MSN_LOGIN_ID']."' and to_id='' and data_kind=2 order by post_date desc";
 break;
 case 'my_ann':  //我發的公告
  $query="select * from sc_msn_data where teach_id='".$_SESSION['MSN_LOGIN_ID']."' and to_id='' and data_kind=0 order by post_date desc";
 break;
 case 'my_pic':  //我發的電子圖文
  $query="select * from sc_msn_board_pic where teach_id='".$_SESSION['MSN_LOGIN_ID']."' order by stdate desc";
 break;
}

$result=mysql_query($query);

$IF_READ[0]="-";
$IF_READ[1]="已讀取";


?>
<html>
<head>
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<title>管理我的訊息</title>
</head>
<body>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
<input type="hidden" name="set" value="<?php echo $_POST['set'];?>">
<table border="0">
	<tr>
		<td>
			<table border="1" style="border-collapse:collapse" bordercolor="#000000">
			 <tr>
			  <td bgcolor="#FFCCFF" style="font-size:10pt;color:#0000FF">私人的</td>
			 </tr>
			</table>
		</td>
		<td style="font-size:10pt">	
			<input type="radio" name="set" value="my_msg" onclick="document.form1.submit()"<?php if ($set=='my_msg') echo " checked";?>>別人給我的
			<input type="radio" name="set" value="my_msg_post" onclick="document.form1.submit()"<?php if ($set=='my_msg_post') echo " checked";?>>我給別人的 &nbsp;&nbsp;　
		</td>
		<td style="font-size:10pt">
			<table border="1" style="border-collapse:collapse" bordercolor="#000000">
			 <tr>
			  <td bgcolor="#FFCCFF" style="font-size:10pt;color:#0000FF">公開的</td>
			 </tr>
			</table>
		</td>
		<td style="font-size:10pt">
			<input type="radio" name="set" value="my_file" onclick="document.form1.submit()"<?php if ($set=='my_file') echo " checked";?>>檔案
			<input type="radio" name="set" value="my_ann" onclick="document.form1.submit()"<?php if ($set=='my_ann') echo " checked";?>>校內訊息
			<input type="radio" name="set" value="my_pic" onclick="document.form1.submit()"<?php if ($set=='my_pic') echo " checked";?>>電子圖文

			<input type="button" style="font-size:10px" value="關閉" name="B3" onClick="window.close()">
		</td>
 </tr>
</table>
<?php
//列出, 電子文
if ($set=='my_pic') {
?>
	<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolor="#800000" style="border-collapse:collapse">
    <tr>
      <td width="100" bgcolor="#FFCCCC" style="font-size:10pt" align="center">來自</td>
      <td width="100" bgcolor="#FFFFCC" style="font-size:10pt" align="center">起</td>
      <td width="100" bgcolor="#FFCCCC" style="font-size:10pt" align="center">迄</td>
      <td bgcolor="#CCFFCC" style="font-size:10pt" align="center">訊息內容</td>
      <td width="50" bgcolor="#FFCCCC" style="font-size:10pt" align="center">
       			<input type="button" style="font-size:10px;color:#FF0000;cursor:hand" value="刪除" onClick="if (confirm('您確定要: \刪除勾選的訊息?')) { document.form1.act.value='del';document.form1.submit(); } ">
      </td>
    </tr>

<?php
 while ($row=mysql_fetch_array($result,1)) {
 	$teach_id_name=get_name_state($row['teach_id']);
 	?>
    <tr>
      <td width="100" style="font-size:10pt" align="center"><?php echo $teach_id_name[0];?></td>
      <td width="100" style="font-size:10pt" align="center"><?php echo $row['stdate'];?></td>
      <td width="100" style="font-size:10pt" align="center"><?php echo $row['enddate'];?></td>
      <td style="font-size:10pt" align="center">
       <?php
         $a=explode(".",$row['filename']);
  	   	 $filename_s=$a[0]."_s.".$a[1];
  	   	 if ($a[1]=='swf') {
  	   	 	?>
  	   	 	<embed src="<?php echo $UPLOAD_PIC_URL.$row['filename'];?>" width=240 height=180 type=application/x-shockwave-flash Wmode="transparent"><br>
  	   	 	<?php
  	   	 } else {
  	   	  ?>
  	   	  <img src="<?php echo $UPLOAD_PIC_URL.$filename_s; ?>" border="0"><br>
  	   	  <?php
  	   	 }
  	   	 echo $row['file_text'];
       ?>
      </td>
      <td width="50" align="center"><input type="checkbox" name="id[]" value="<?php echo $row['id'];?>"></td>
    </tr>
 	
  <?php
 } // end while
} else { //非電子文
	?>
	<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolor="#800000" style="border-collapse:collapse">
    <tr>
      <td width="100" bgcolor="#FFFFCC" style="font-size:10pt" align="center">日期</td>
      <td width="100" bgcolor="#FFCCCC" style="font-size:10pt" align="center">來自</td>
      <td width="100" bgcolor="#FFCCCC" style="font-size:10pt" align="center">傳給</td>
      <td bgcolor="#CCFFCC" style="font-size:10pt" align="center">訊息內容</td>
      <td width="50" bgcolor="#FFCCCC" style="font-size:10pt" align="center">已閱讀</td>
      <td width="50" bgcolor="#FFCCCC" style="font-size:10pt" align="center">
       			<input type="button" style="font-size:10px;color:#FF0000;cursor:hand" value="刪除" onClick="if (confirm('您確定要: \刪除勾選的訊息?')) { document.form1.act.value='del';document.form1.submit(); } ">
      </td>
    </tr>

	<?php
 while ($row=mysql_fetch_array($result,1)) {
 //list($id,$idnumber,$teach_id,$to_id,$post_date,$last_date,$data,$ifread)=$row;
  $teach_id_name=get_name_state($row['teach_id']);
  $to_id_name=get_name_state($row['to_id']);

  $data=AddLink2Text($row['data']);
  
  //檢查是否有附檔
  $query_file="select filename,filename_r from sc_msn_file where idnumber='".$row['idnumber']."'";
  $result_file=mysql_query($query_file);
  
  ?>
    <tr>
      <td width="100" style="font-size:10pt" align="center"><?php echo $row['post_date'];?></td>
      <td width="100" style="font-size:10pt" align="center"><?php echo $teach_id_name[0];?></td>
      <td width="100" style="font-size:10pt" align="center"><?php echo $to_id_name[0];?></td>
      <td style="font-size:10pt"><?php echo $data;?>
			<?php
			  if (mysql_num_rows($result_file)) {
			 ?>
			  <br>
      	<font style="color:#0000FF">本訊息含附檔，檔名：
      		<?php 
      		while ($row_file=mysql_fetch_row($result_file)) {
      		 list($filename,$filename_r)=$row_file;
      		 echo "<br>".$filename_r;
      		 ?>
      　<a href="main_download.php?set=<?php echo $filename;?>">下載</a>	
      	<?php
      	   } // end while
      	   echo "</font>";
         } // end if 
      	?>
      </td>
      <td width="50" style="font-size:9pt" align="center">
      	<?php
      	 if ($set=='my_msg') {
      	   ?>
      	    <input type="button" value="回覆" style="font-size:9pt" onclick="relay(<?php echo $row['idnumber'];?>)">
      	   <?php
      	 } else { 
      	   echo $IF_READ[$row['ifread']];
      	 }
      	?>
      </td>
      <td width="50" style="font-size:10pt" align="center"><input type="checkbox" name="id[]" value="<?php echo $row['id'];?>"></td>
    </tr>
  
 <?php
 } // end while
} // end if else $set==
?>
  </table>
</form>
</body>
</html>
<Script language="JavaScript">
	
	top.moveTo(0,0);
	
	function confirm_delete() {
    var is_confirmed = confirm('您確認要 :\n刪除勾選的訊息嗎？');
    if (is_confirmed) {
     return true;
    }
    return false;
   }	
   
  function relay(idnumber)
  {
   flagWindow=window.open('main_message.php?act=read&set='+idnumber,'MessageRead','width=420,height=480,resizable=1,toolbar=no,scrollbars=auto');
  }
</Script>
