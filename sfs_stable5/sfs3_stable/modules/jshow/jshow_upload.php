<?php
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//送出後的動作
if ($_POST['act']=='upload') {
 //確實有檔案
    if ($_FILES['thefile']['name']!="") {
     //檔案處理
     $FILENAME=process_upload_file($_POST['kind_id']);
     
     if ($FILENAME[0]) {
 
            //開始存入
            $kind_id=$_POST['kind_id'];
            $sub=$_POST['sub'];
            $memo=$_POST['memo'];
            $sort=$_POST['sort'];
            $url=$_POST['url'];
            $upload_day=date("Y-m-d H:i:s");
            $teacher_sn=$_SESSION['session_tea_sn'];
            $filename=$FILENAME[0];
             
            $sql="insert into jshow_pic (kind_id,sub,memo,filename,display,display_sub,display_memo,upload_day,teacher_sn,sort,url) values ('$kind_id','$sub','$memo','$filename','1','0','0','$upload_day','$teacher_sn','$sort','$url')";
						$CONN->Execute($sql) or die ("SQL Error! sql=".$sql);            
						
						$INFO="上傳成功!";
						            
     } else {
       $INFO=$FILENAME[1];
     }     
		} else {
		  $INFO="上傳檔案失敗!";
		}
		$_POST['act']="";
} // end if

//更新
if ($_POST['act']=='update') {
 $id=$_POST['opt1'];
   $kind_id=$_POST['kind_id'];
   $sub=$_POST['sub'];
   $memo=$_POST['memo'];
   $sort=$_POST['sort'];
   $url=$_POST['url'];
   $upload_day=date("Y-m-d H:i:s");
   $teacher_sn=$_SESSION['session_tea_sn'];
   if ($_FILES['thefile']['name']!="") {
     $FILENAME=process_upload_file($_POST['kind_id']);
   } else {
     $FILENAME="";
   }
 if ($sub!="" and $memo!="") {
  //沒更新圖檔
  if ($FILENAME[0]=="") {
   //儲存
    $sql="update jshow_pic set sub='$sub',memo='$memo',sort='$sort',url='$url' where id='$id'";
    $res=$CONN->Execute($sql) or die ("SQL Error! sql=".$sql);
    $INFO=$FILENAME[1]." 僅進行文字更新!";
  } else {
 	//有更新檔案, 刪除舊檔
 	 $sql="select filename from jshow_pic where id='$id'";
   $res=$CONN->Execute($sql) or die("SQL Error! sql=".$sql);
   $filename=$res->fields['filename'];
   $a=explode(".",$filename);
 	 $filename_s=$a[0]."_s.".$a[1];
 	 $filename_a=$a[0]."_a.".$a[1];
   unlink($USR_DESTINATION.$filename);
   unlink($USR_DESTINATION.$filename_s);
   unlink($USR_DESTINATION.$filename_a);
 	
   $filename=$FILENAME[0];
 	
  //儲存
    $sql="update jshow_pic set sub='$sub',memo='$memo',sort='$sort',filename='$filename',url='$url' where id='$id'";
    $res=$CONN->Execute($sql) or die ("SQL Error! sql=".$sql);
    $INFO="已儲存更新!";
  } // end if else 
 } // end if ($sub!="" and $memo!="") 
  $_POST['act']="";
} //if ($_POST['act']=='update')


//刪除
if ($_POST['act']=='delete') {
  $sql="select * from jshow_pic where id='".$_POST['opt1']."'";
  $res=$CONN->Execute($sql) or die("SQL Error! sql=".$sql);
  $filename=$res->fields['filename'];
  $a=explode(".",$filename);
 	$filename_s=$a[0]."_s.".$a[1];
 	$filename_a=$a[0]."_a.".$a[1];
  unlink($USR_DESTINATION.$filename);
  unlink($USR_DESTINATION.$filename_s);
  unlink($USR_DESTINATION.$filename_a);
  $sql="delete from jshow_pic where id='".$_POST['opt1']."'";
  $res=$CONN->Execute($sql) or die("SQL Error! sql=".$sql);
  $_POST['act']='';
}

//取出所有經授權的分類區
$P=get_jshow_checked_id();


//秀出網頁
head("Joomla!首頁秀圖管理-圖片上傳管理");

$tool_bar=&make_menu($menu_p);

//列出選單
echo $tool_bar;

//列出第一頁的圖片
$PAGE=($_GET['page']=='')?1:$_GET['page'];

$doit=($_POST['act']=='')?"上傳一張新圖":"更新圖片";

?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>"  enctype="multipart/form-data" >
 <input type="hidden" name="act" value="">
 <input type="hidden" name="opt1" value="<?php echo $_POST['opt1'];?>">
 <table border="0">
 	<tr>
 		<td>圖檔上傳至
 			<select size="1" name="kind_id" id="SELECT_kind_id">
 				<option value="">選擇分類區</option>
 			<?php
 			foreach ($P as $p) {
 			?>
 			  <option value="<?php echo $p['kind_id'];?>" <?php if ($_POST['kind_id']==$p['kind_id']) { echo "selected";} ?>><?php echo $p['id_name'];?></option>
 			<?php
 			}
 			?> 	
 			</select>
 		</td>
 	</tr>
  </table>
  <?php
 if ($_POST['kind_id']!="") {
 	  //取得分類區設定
  	$row=get_setup($_POST['kind_id']);
		if (count($row)>0) {
		  foreach ($row as $k=>$v) { ${$k}=$v; }
		}	
 	//上傳新圖
  	if ($_POST['act']=='') {
  		//預設值
  		$PIC['sort']=100;
  ?>
  <table border="0">
 	<tr>
 		<td>
 		 <?php form_upload($PIC,$memo); ?>
 		</td>
 	</tr>
 	<tr>
 		<td>
 			<input type="button" value="<?php echo $doit;?>" onclick="check_upload('upload')">
 		</td>
 	</tr>
 	</table>
 <?php
   }  // end if ($_POST['act']=='')
  
  //edit 修改
  if ($_POST['act']=='edit') {
  	$PIC=get_one_pic($_POST['opt1']);
  ?>
  <table border="0">
 	<tr>
 		<td>
 		 <?php form_upload($PIC,$memo); ?>
 		</td>
 	</tr>
 	<tr>
 		<td>
 			<input type="button" value="<?php echo $doit;?>" onclick="check_upload('update')">
 		</td>
 	</tr>
</table>
  <?php	
  	
   
 }
 ?>
 
 <table border="0">
 	<tr>
 	 <td style="color:#FF0000"><?php echo $INFO;?></td>
 	</tr>
 </table>

	<?php
	//列出上傳表單

	//列出已上傳的圖片
	show_upload($_POST['kind_id']);

} // end if ($POST['kind_id']=="")
?>
</form>
<Script>
 function confirm_delete(b_id,info) {
  
  var confirm_del=confirm("您要定要：\n刪除「"+info+"」?");
  
  if (confirm_del) {
    document.myform.act.value="delete";
    document.myform.opt1.value=b_id;
    document.myform.submit();
  } else {
  	return false
  }
 
 }
 
//選擇分類區時
$("#SELECT_kind_id").change(function(){
 document.myform.act.value="";
 document.myform.submit();
});


function check_upload(themode) {
 document.myform.act.value=themode;
 //alert(themode);
 
 if (document.myform.sub.value=='') {
   alert('請輸入主題!');
   document.myform.sub.focus();
   return false;
 }
 
 if (document.myform.memo.value=='') {
   alert('請輸入說明!');
   document.myform.memo.focus();
   return false;
 } 
 
  if (document.myform.thefile.value=='' && themode=='upload') {
   alert('必須選定檔案!');
   return false;
 }
 
 document.myform.submit();
 
}

</Script>