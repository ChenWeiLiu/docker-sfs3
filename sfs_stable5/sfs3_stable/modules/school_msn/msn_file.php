<?php
//$Id$
include "config.php";
include_once ('my_functions.php');
//認證
sfs_check();

//秀出網頁布景標頭
head("檔案分析");
//主選單設定
$tool_bar=&make_menu($MODULE_MENU);

//列出選單
echo $tool_bar;

  mysql_query("SET NAMES 'utf8'");

if ($_POST['act']=='del') {
 foreach($_POST['tag_del'] as $filename) {
	$sql="select a.* from sc_msn_data a,sc_msn_file b where b.filename='".$filename."' and a.idnumber=b.idnumber";
  $res=mysql_query($sql);
  if (mysql_num_rows($res)==0) {
   unlink($download_path.$filename);
  } else {    	 	  //刪除附檔
   	$row=mysql_fetch_array($res,1); 
   	delete_file ($row['idnumber'],$row['to_id']);
   	$query="delete from sc_msn_data where id='".$row['id']."'";
  	mysql_query($query);
  }  
 } // end foreach
 
}

//讀取資料夾中所有實際存在的檔案
$file_list=glob($download_path."*.*");
$file_check=array();
foreach ($file_list as $filename) {
	$file=explode("/",$filename);
	$f=count($file)-1;
  $file_check[$file[$f]]=0; // filename
}

//取得資料夾
$CONN->Execute("SET NAMES 'utf8'");
$sql="select * from sc_msn_folder order by idnumber";
$res=$CONN->Execute($sql);
$folders=$res->GetRows();

//echo "<pre>";
//print_r($folders);
//exit();

$ALLSIZE=0;


?>
<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
<input type="hidden" name="act" value="">
<?php
foreach ($folders as $FOLDER) {
  $ALLSIZE_this=0;

	$CONN->Execute("SET NAMES 'utf8'");
	$sql="select a.*,b.filename,filename_r from sc_msn_data a,sc_msn_file b where a.idnumber=b.idnumber and a.folder='".$FOLDER['idnumber']."'";
  $res=$CONN->Execute($sql);
  if ($res->RecordCount()>0) {
	 ?>
 		◎檔案夾：<?php echo iconv("UTF-8","big5",$FOLDER['foldername']);?><br>
	 	<table border="1" width="100%" bordercolor="#000000" style="border-collapse:collapse">
  		<tr bgcolor='#FFCCCC'>
   			<td style="font-size:10pt">檔案</td>
   			<td width="100" style="font-size:10pt">大小</td>
   			<td width="50" style="font-size:10pt">上傳者</td>
   			<td width="100" style="font-size:10pt">日期</td>
   			<td width="50" style="font-size:10pt">類別</td>
   			<td width="50" style="font-size:10pt">對象</td>
   			<td style="font-size:10pt">說明</td>
   			<td width"50"><input type="button" value="刪除" style="font-size:10pt" onclick="if (confirm('您確定要:\n刪除勾選的檔案? (同屬於相同訊息內的所有檔案會一併刪除)')) { document.myform.act.value='del';document.myform.submit();}"></td>
  		</tr>
		<?php
   		while ($row=$res->fetchRow($res)) {
				mysql_query("SET NAMES 'latin1'");
				$name=get_teacher_name_by_id($row['teach_id']);
				$to_name=get_teacher_name_by_id($row['to_id']);
				$file_check[$row['filename']]=1;
				$ALLSIZE+=filesize($download_path.$row['filename']);
		?>	
  		<tr>
   			<td style="font-size:10pt"><?php echo iconv("UTF-8","big5",$row['filename_r']);?></td>
   			<td style="font-size:10pt"><?php echo ShowBytes(filesize($download_path.$row['filename']));?></td>
   			<td style="font-size:10pt"><?php echo $name;?></td>
   			<td style="font-size:10pt"><?php echo $row['post_date'];?></td>
   			<td style="font-size:10pt"><?php echo $row['data_kind'];?></td>
   			<td style="font-size:10pt"><?php echo $to_name;?></td>
   			<td style="font-size:10pt"><?php echo iconv("UTF-8","big5",$row['data']);?></td>
   			<td><input type="checkbox" name="tag_del[]" value="<?php echo $row['filename'];?>"></td>
  		</tr>
		<?php	
   		} // end while
   		?>
   	</table>
   		<?php
  } // end if

mysql_query("SET NAMES 'utf8'");

} // end foreach folder



//=====================================

//列出遺失無法索引的檔案
 foreach($file_list as $filename) {
	//條件 $filename
	$file=explode("/",$filename);
	$f=count($file)-1;
	if ($file_check[$file[$f]]==1) continue; 
	?>
	<br>本檔案遺失索引：<input type="checkbox" name="tag_del[]" value="<?php echo $file[$f];?>"><?php echo $file[$f];?> (<?php echo ShowBytes(filesize($filename)); ?>)
	<?php
	$ALLSIZE+=filesize($filename);
	
 }
?>
<br><br>佔用系統總容量 : <?php echo ShowBytes($ALLSIZE);?>&nbsp;&nbsp;
<input type="button" value="刪除所有勾選的檔案" style="font-size:10pt" onclick="if (confirm('您確定要:\n刪除勾選的檔案? (同屬於相同訊息內的所有檔案會一併刪除)')) { document.myform.act.value='del';document.myform.submit();}">
<?php
//佈景結尾
foot();

function ShowBytes($size) {   
   $size=doubleval($size);   
   $sizes= array(   
       " Bytes",   
       " KB",   
       " MB",   
       " GB",   
       " TB"  
   );   
   if($size== 0) {   
       return('n/a');   
   } else{   
       $i= floor( log($size, 1024) );   
       return(round( $size/pow(1024, $i), 2) . $sizes[$i]);   
   }   
}  
?>
