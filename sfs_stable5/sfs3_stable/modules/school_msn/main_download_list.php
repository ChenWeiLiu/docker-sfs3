<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');

mysql_query("SET NAMES 'utf8'");

if (!isset($_SESSION['MSN_LOGIN_ID'])) {
  echo "<Script language=\"JavaScript\">window.close();</Script>";
	exit();
}


$folder=$_GET['folder'];


?>
<html>
<head>
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<title>檔案下載</title>
</head>
<body>
<table border="0" width="100%">
	<tr>
		<td align="left" style="font-size:10pt" style="color:#FF0000">§請選擇檔案類別</td>
		<td align="right"><input type="button" value="關閉" name="B3" onClick="window.close()"></td>
 </tr>
</table>
<table border="1" style="border-collapse:collapse" color="#800000" cellpadding="2">
 <?php
 $query="select * from sc_msn_folder where idnumber!='private' order by idnumber";
 $result=mysql_query($query);
 $i=0;
 while ($row=mysql_fetch_array($result)) {
 $i++;
 if ($i%5==1) echo "<tr>";
  ?>
  <td style="font-size:10pt"<?php if ($folder==$row['idnumber']) echo " bgcolor='#FFCCFF'";?>><img src="./images/folder.png" border="0"  align="absmiddle"><a href="main_download_list.php?folder=<?php echo $row['idnumber'];?>"><?php echo $row['foldername'];?></a></td>
  <?php
 if ($i%5==5) echo "</tr>";
 }
 ?>
 
</table>
<?php
if ($folder!='' and $folder!='private') {
?>
<table border="1" cellpadding="0" cellspacing="0" width="100%" bordercolorlight="#800000" bordercolordark="#FFFFFF" bordercolor="#FFFFFF">
    <tr>
      <td width="60" bgcolor="#FFFFCC" style="font-size:10pt" align="center">日期</td>
      <td width="60" bgcolor="#FFCCCC" style="font-size:10pt" align="center">上傳者</td>
      <td width="240" bgcolor="#CCFFCC" style="font-size:10pt" align="center">檔案說明</td>
      <td bgcolor="#FFCCCC" style="font-size:10pt" align="center">檔名</td>
    </tr>
<?php
$query="select id,idnumber,teach_id,post_date,data from sc_msn_data where to_id='' and data_kind=2 and folder='$folder' order by post_date desc";
$result=mysql_query($query);

while ($row=mysql_fetch_row($result)) {
 list($id,$idnumber,$teach_id,$post_date,$data)=$row;
 
    $query_file="select filename,filename_r,file_download from sc_msn_file where idnumber='".$idnumber."'";
    $result_file=mysql_query($query_file);
 if (mysql_num_rows($result_file)) {
  $name=get_name_state($teach_id);
  $data=AddLink2Text($data);
  ?>
   <tr>
      <td width="60" style="font-size:10pt" align="center"><?php echo substr($post_date, 0, 10);  ;?></td>
      <td width="60" style="font-size:10pt" align="center"><?php echo $name[0];?>(<?php echo $teach_id;?>)</td>
      <td width="240" style="font-size:10pt"><?php echo $data;?></td>
      <td style="font-size:10pt" align="left">
   <?php
    $j=0;
    while ($row_file=mysql_fetch_row($result_file)) {
    	list($filename,$filename_r,$file_download)=$row_file;
    	$j++;
  ?>
  
    <?php echo $filename_r;?>&nbsp;
  	<a href="main_download.php?set=<?php echo $filename;?>">下載<?php echo $j;?></a>
  	(<?php echo $file_download;?>次)
  	<br>
     <?php
        } // end while file
     ?>  
       </td>
    </tr> 
<?php

 }// end if mysql_num_rows
}
?>
  </table>
  <?php
 } // end if 
  ?>
</body>
</html>
