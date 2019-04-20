<?php
include_once ('config.php');
include_once ('my_functions.php');

$Now=date("Y-m-d");

mysql_query("SET NAMES 'utf8'");


//刪除已過期的
$query="select * from sc_msn_board_pic where enddate<'$Now' and show_off=0";
$res=mysql_query($query);
while ($row=mysql_fetch_array($res,1)) {
    unlink($UPLOAD_PIC.$row['filename']);
     $query="delete from sc_msn_board_pic where filename='".$row['filename']."'";
     mysql_query($query);
}

$query="select * from sc_msn_board_pic where stdate<='$Now' and enddate>='$Now' and show_off=0 limit 1";
$res=mysql_query($query);
if (mysql_num_rows($res)==0) {
	$query="update sc_Msn_board_pic set show_off=0";
	mysql_query($query);
	?>
  <Script Language="javascript">
   window.location.href='main_showdata.php';
  </Script>

  <?php
  exit();
} else {
  $row=mysql_fetch_array($res,1);
  $query="update sc_msn_board_pic set show_off=1 where id='".$row['id']."'";
  mysql_query($query);
}

//header('Content-type: text/html; charset=utf-8');

$SEC=$row['delay_sec']*1000;

?>
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Meta http-equiv="Page-Enter" content="blendTrans(Duration=3.0)"> 
<title>電子圖文展示</title> 
</head> 
<body bgcolor="#000000" style="overflow: hidden">

<table height="100%" width="100%">
 <tr>
  <td valign="center" align="center" bgcolor="#000000">
	<table border="0" width="100%">
	<tr>
	 <td align="center" valign="center">
	 <?php
	 if (substr($row['filename'],-3)=='swf' or substr($row['filename'],-3)=='wmv') {
	 ?>
	 <embed src="<?php echo $UPLOAD_PIC_URL.$row['filename'];?>" width=800 height=600 type=application/x-shockwave-flash Wmode="transparent">
		<?php
	} else {
		?>
	 	<img src="<?php echo $UPLOAD_PIC_URL.$row['filename'];?>" border="0">
  <?php
   }
  ?>	   		   		
	 </td>
		</tr>
		<tr>
	 		<td align="center" style="font-size:28pt" style="color:#FFFFFF;font-family:標楷體"><?php echo $row['file_text'];?></td>
		</tr>
	</table> 
  
  </td>
  </tr>	
 </table> 
</body> 
</html> 

<Script language="JavaScript">

 var s=function () {  window.location.href='main_showpic.php';   }
  //延遲多久再執行一次
  setTimeout(s, <?php echo $SEC;?>);

</Script>

 