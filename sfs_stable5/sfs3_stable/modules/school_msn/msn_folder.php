<?php
//$Id$
include "config.php";
include_once ('my_functions.php');
//認證
sfs_check();


//秀出網頁布景標頭
head("檔案夾設定");
//主選單設定
$tool_bar=&make_menu($MODULE_MENU);

//列出選單
  echo $tool_bar;

$CONN->Execute("SET NAMES 'utf8'");

//新增
if ($_POST['act']=='insert') {
 $foldername=trim(big52utf8($_POST['foldername']));
 if ($foldername!='') {
 	$idnumber="F".date("y").date("m").date("d").date("H").date("i").date("s");
 //測試代碼是否重覆
	do {
	 $a=floor(rand(0,9));
	 $idnumber_test=$idnumber.$a;
	 $query="select id from sc_msn_folder where idnumber='".$idnumber_test."'";
	 $result=$CONN->Execute($query);
	 $exist=$result->RecordCount();
	} while ($exist>0);

 $idnumber=$idnumber_test;
  $sql="insert into sc_msn_folder (idnumber,foldername,open_upload) values ('$idnumber','$foldername','1')";
  $res=$CONN->Execute($sql) or die ('SQL Error! query='.$sql);
 }	

} // end if ($_POST['act']=='insert')

//修改
if ($_POST['act']=='update') {
 $foldername=trim(big52utf8($_POST['update_name']));
	$idnumber=$_POST['option1'];
  $sql="update sc_msn_folder set foldername='$foldername' where idnumber='$idnumber'";
  $res=$CONN->Execute($sql) or die ('SQL Error! query='.$sql);

} // end if ($_POST['act']=='insert')

//刪除
if ($_POST['act']=='delete') {
 
	$idnumber=$_POST['option1'];
  $sql="delete from sc_msn_folder where idnumber='$idnumber'";
  $res=$CONN->Execute($sql) or die ('SQL Error! query='.$sql);

} // end if ($_POST['act']=='insert')


 //取得資料夾

$sql="select * from sc_msn_folder where open_upload='1' order by idnumber";
//$sql="select * from sc_msn_folder order by idnumber";
$res=$CONN->Execute($sql);
$folders=$res->GetRows();

?>
<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
<input type="hidden" name="act" value="">
<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
新增「檔案分享」功能的檔案夾類別：<input type="text" size="20" value="" name="foldername">
<input type="button" value="確定新增" onclick="if (document.myform.foldername.value!='') { document.myform.act.value='insert';document.myform.submit(); } ">
<br><br>
<table border="0" width="100%">
	<tr>
		<td align="left" style="font-size:10pt" style="color:#FF0000">§系統內已建立的檔案夾</td>
 </tr>
</table>
<table border="1" style="border-collapse:collapse" color="#800000" cellpadding="2">
 <tr bgcolor="#CCCCFF">
 	<td align="center">編號</td>
 	<td align="center">檔案夾名稱</td>
 	<td align="center">資料筆數</td>
 	<td align="center">編輯</td>
 </tr>
 <?php
	$i=0;
 foreach ($folders as $FOLDER) {
   $sql="select count(*) from sc_msn_data where folder='".$FOLDER['idnumber']."'";
   $res=$CONN->Execute($sql);
   $num=$res->fields[0];
   $i++;
   if ($_POST['act']=='edit' and $_POST['option1']==$FOLDER['idnumber']) {
 		?>
 		<tr bgcolor="#FFCCCC">
 			<td align="center"><?php echo $i;?></td>
 			<td><input type="text" name="update_name" value="<?php echo iconv("UTF-8","big5",$FOLDER['foldername']);?>"></td>
 			<td align="center"><?php echo $num;?></td>
 			<td>
 			 <input type="button" value="儲存" onclick="if (document.myform.update_name.value!='') { document.myform.option1.value='<?php echo $FOLDER['idnumber'];?>';document.myform.act.value='update';document.myform.submit(); } ">
 			</td>
 		</tr>
 
	<?php   	
   } else {
 	?>
 <tr>
 	<td align="center"><?php echo $i;?></td>
 	<td><?php echo iconv("UTF-8","big5",$FOLDER['foldername']);?></td>
 	<td align="center"><?php echo $num;?></td>
 	<td>
 	  <input type="button" value="修改" onclick="document.myform.option1.value='<?php echo $FOLDER['idnumber'];?>';document.myform.act.value='edit';document.myform.submit(); ">
 	  <?php 
 	   if ($num==0) {
 	  ?>
 	  <input type="button" value="刪除" onclick="if (confirm('您確定要刪除：\n<?php echo $FOLDER['foldername'];?>?')) { document.myform.option1.value='<?php echo $FOLDER['idnumber'];?>';document.myform.act.value='delete';document.myform.submit(); } ">
 	  <?php
 	   }
 	  ?>
 	</td>
 </tr>
 <?php
   } // end if
 } // end foreach

 ?>
</table>
</form>