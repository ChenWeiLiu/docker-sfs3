<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');

	//設定上傳圖片路徑
	$img_path = "photo/teacher";

?>
<head>
	 <title>校園MSN-選擇發送訊息的對象</title>
</head>
<?php
if (!isset($_SESSION['MSN_LOGIN_ID'])) {
  echo "<Script language=\"JavaScript\">window.close();</Script>";
	exit();
}

$form_name=$_GET['form_name'];
$item_name=$_GET['item_name'];
$selected_text=$_GET['selected_text'];


//列出教職員id
// ====================================================================
$POST_KIND=array("","校長","教師兼主任","主任","教師兼組長","組長","導師","專任教師","實習教師","試用教師","代理/代課教師","兼任教師","職員","護士","警衛","工友");
?>
<form name="form0" method="post" action="<?php echo $_SERVER['php_self']?>">
<table border="0" cellspacing="0" width="100%" bgcolor="#FFFFFF" bordercolor="#FFFFFF" style="border-collapse:collapse">
<tr>
 <td style="color:#FF0000">
 	◎名單篩選條件:<input type="text" size="10" name="master_subjects" value="<?php echo $_POST['master_subjects'];?>">
 	<input type="button" value="篩選" onclick="document.form0.submit()"><font size="2" color="#000000">(請輸入科目別,如:國文、自然....，未輸入則依職務列出)</font>
 
 </td>
</tr>
</table>
</form>
<table border="0" cellspacing="0" width="100%" bgcolor="#FFFFFF" bordercolor="#FFFFFF" style="border-collapse:collapse">
<tr>
 <td align="left" style="color:blue">
 	§請選擇要發送訊息的對象：
 <?php
 if ($_GET['email']==1) echo "未設定 E-mail信箱者無法勾選!";
 ?>
  <br>
  <input type="button" value="送出資料" onclick="select_item()">
	<input type="button" value="全選" onclick="check_select_all()">
  <input type="button" value="全部不選" onclick="check_disable()">
</td>
</tr>
 <form name="form1" method="post" action="<?php echo $_SERVER['php_self']?>" onsubmit="return false">
<tr>
<td colspan="2">
 <table border="0" cellspacing="0" width="100%" bordercolor="#FFFFFF" style="border-collapse:collapse">
<?php
if ($_POST['master_subjects']=="") {
//依職別取得資料
for ($kind=1;$kind<=count($POST_KIND);$kind++) {
 $i=0; //紀錄本類別人數
 $query="select a.teacher_sn,c.teach_id,c.name from teacher_post a,teacher_title b,teacher_base c where a.post_kind=".$kind." and a.teach_title_id=b.teach_title_id and a.teacher_sn=c.teacher_sn and c.teach_condition=0 order by b.room_id,b.rank";
 if ($kind==6) {
 	$query="select a.teacher_sn,a.class_num,c.teach_id,c.name from teacher_post a,teacher_title b,teacher_base c where a.post_kind=".$kind." and a.teach_title_id=b.teach_title_id and a.teacher_sn=c.teacher_sn and c.teach_condition=0 order by a.class_num";
 }
 //echo $query;
 $result=$CONN->Execute($query);
 if ($result->RecordCount()>0) {
 	?>
  <tr><td colspan="5" style="color:#800000"><b>職別：  <?php echo $POST_KIND[$kind]; ?> </b></td></tr>
 	<tr>
  		<td>
  			<table border="0">
 	<?php
  while ($row=$result->fetchRow()) {
  	$email=get_teacher_email_by_id($row['teach_id']);
  	$f_color=($_GET['email']==1 and $email=="")?"#CCCCCC":"blue";
  	?>
  	
  		
  		<?php
  			$i++;  if ($i%7==1) echo "<tr>";
				$teacher_sn=$row['teacher_sn'];
       ?>
        
        <td style="font-size:10pt" align="center">
        	<table border="1"  style="border-collapse:collapse">
        		<?php
        		/*
        		<tr>
        			<td align="center" width="130" height="180">
        				<?php
        				 //印出照片    	
    						if (file_exists($UPLOAD_PATH."/$img_path/".$teacher_sn)&& $teacher_sn<>'') {
    							echo "<img src=\"".$UPLOAD_URL."$img_path/$teacher_sn\" width=\"120\"><br>";
								} else {
									echo "<font size=2>沒有照片</font><br>";
								}
        				?>
        			</td>
        		</tr>
        		*/
        		?>
        		<tr>
        			<td align="center" style="font-size:11pt;color:<?php echo $f_color;?>">
        				<input type="checkbox" name="sendid[]" value="<?php echo $row['teach_id'];?>" style="width:9pt;height:9pt" <?php if ($_GET['email']==1 and $email=="") echo "disabled";?>>
        				
        				<?php
  				        if ($kind==6 and $row['class_num']>0) echo $row['class_num']-600;
        					echo big52utf8($row['name']);
        				?>
        				
        			</td>
        		</tr>
        	</table>
         </td>
        	<?php
      		if ($i%7==0) echo "</tr>";
 	}// end while
	?> 
		</table>
  	</td>
  </tr>
  <?php
 	
 } // end if $result->RecordCount() 
 
} // end for
//依科目篩選教師
}else{
 $master_subjects=iconv("UTF-8", "big5",$_POST['master_subjects']);
 $query="select a.teacher_sn,c.teach_id,c.name from teacher_post a,teacher_title b,teacher_base c where c.master_subjects like '%".$master_subjects."%' and a.teach_title_id=b.teach_title_id and a.teacher_sn=c.teacher_sn and c.teach_condition=0 order by c.name";
 $result=$CONN->Execute($query);
 if ($result->RecordCount()>0) {
 	?>
 	<tr>
  		<td>
  			<table border="0">
 	<?php
  while ($row=$result->fetchRow()) {
  	$email=get_teacher_email_by_id($row['teach_id']);
  	$f_color=($_GET['email']==1 and $email=="")?"#CCCCCC":"blue";
  	?>
  	
  		
  		<?php
  			$i++;  if ($i%7==1) echo "<tr>";
				$teacher_sn=$row['teacher_sn'];
       ?>
        
        <td style="font-size:10pt" align="center">
        	<table border="1"  style="border-collapse:collapse">
        		<?php
        		/*
        		<tr>
        			<td align="center" width="130" height="180">
        				<?php
        				 //印出照片    	
    						if (file_exists($UPLOAD_PATH."/$img_path/".$teacher_sn)&& $teacher_sn<>'') {
    							echo "<img src=\"".$UPLOAD_URL."$img_path/$teacher_sn\" width=\"120\"><br>";
								} else {
									echo "<font size=2>沒有照片</font><br>";
								}
        				?>
        			</td>
        		</tr>
        		*/
        		?>
        		<tr>
        			<td align="center" style="font-size:11pt;color:<?php echo $f_color;?>">
        				<input type="checkbox" name="sendid[]" value="<?php echo $row['teach_id'];?>" style="width:9pt;height:9pt" <?php if ($_GET['email']==1 and $email=="") echo "disabled";?>>
        				
        				<?php
  				        if ($kind==6 and $row['class_num']>0) echo $row['class_num']-600;
        					echo big52utf8($row['name']);
        				?>
        				
        			</td>
        		</tr>
        	</table>
         </td>
        	<?php
      		if ($i%7==0) echo "</tr>";
 	}// end while
	?> 
		</table>
  	</td>
  </tr>
  <?php 
 }// end if $result->RecordCount()
 
} // end if else

?>  	

</table>
</td>
</tr>
<tr>
	<td colspan="2">   
<input type="button" value="送出資料" onclick="select_item()">
<input type="button" value="全選" onclick="check_select_all()">
<input type="button" value="全部不選" onclick="check_disable()">
</td>
</tr>
 </form>
</table>

 <script language="javascript">
function select_item(){
 var strSelect='';
 var i =0;
 while (i < document.form1.elements.length)  {
  var e = document.form1.elements[i];
  if(e.checked==true && e.name.substr(0,6)=='sendid') strSelect+=";"+e.value;
	i++;
 }
 if (strSelect) {
  strSelect=strSelect.substr(1,strSelect.length-1);
 }
 opener.document.<?php echo $form_name;?>.<?php echo $item_name;?>.value=strSelect;
 window.close();
}

function check_old() {
 var checked_str=opener.document.<?php echo $form_name;?>.<?php echo $item_name;?>.value;
 check_student=checked_str.split(';');
 for(var i=0; i<check_student.length; i++) {
   for (var j=0; j<document.form1.elements.length;j++) {
    if (document.form1.elements[j].value==check_student[i]) {
      document.form1.elements[j].checked=true;
    }
   }
 }
}

function check_disable()
{
	var i =0;
 	while (i < document.form1.elements.length)  {
    if (document.form1.elements[i].name.substr(0,6)=='sendid') {
    	document.form1.elements[i].checked=false;
    }  
  	i++;
  } //end while
}

function check_select_all()
{
	var i =0;
 	while (i < document.form1.elements.length)  {
    if (document.form1.elements[i].name.substr(0,6)=='sendid') {
    	if (document.form1.elements[i].disabled==false) document.form1.elements[i].checked=true;
    }  
  	i++;
  } //end while
}


check_old();

</script>
