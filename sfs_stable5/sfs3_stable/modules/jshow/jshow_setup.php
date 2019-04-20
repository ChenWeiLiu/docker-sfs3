<?php
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//送出後的動作
//新增分類區
if ($_POST['act']=='insert') {
   $id_name=$_POST['id_name'];
   $memo=$_POST['memo'];
   $max_width=$_POST['max_width'];
   $max_height=$_POST['max_height'];
 	 $display_mode=$_POST['display_mode'];
   if ($id_name!="" and $memo!="" and $max_width>0 and $max_height>0) {
     $sql="insert into jshow_setup (id_name,memo,max_width,max_height,display_mode) values ('$id_name','$memo','$max_width','$max_height','$display_mode')";
     $res=$CONN->Execute($sql) or die ("SQL Error! query=".$sql);
   }
	$INFO="已於 ".date("Y-m-d H:i:s")." 新增分類區『".$id_name."』!";
	
} 
 
//確定修改
if($_POST['act']=="update") {
   	$kind_id=$_POST['kind_id'];
   	$id_name=$_POST['id_name'];
		$memo=$_POST['memo'];
		$max_width=$_POST['max_width'];
		$max_height=$_POST['max_height'];
		$display_mode=$_POST['display_mode'];
   	$sql="update jshow_setup set id_name='$id_name',memo='$memo',max_width='$max_width',max_height='$max_height',display_mode='$display_mode' where kind_id='".$_POST['kind_id']."'";
   	$res=$CONN->Execute($sql) or die ("SQL Error! query=".$sql);
   	$INFO="已於 ".date("Y-m-d H:i:s")." 進行儲存!";
   	$_POST['act']="edit";
} // end if


//刪除
if ($_POST['act']=='delete') {
   	$sql="select * from jshow_setup where kind_id='".$_POST['kind_id']."'";
   	$res=$CONN->Execute($sql) or die ("SQL Error! query=".$sql);
   	$id_name=$res->fields['id_name'];
  	$sql="delete from jshow_setup where kind_id='".$_POST['kind_id']."'";
  	$res=$CONN->Execute($sql) or die("SQL Error! sql=".$sql);
  	$_POST['kind_id']="";
  	$_POST['act']="";
  	$INFO="已於 ".date("Y-m-d H:i:s")." 刪除分類區「".$id_name."」!";
  	
}

//編輯
if($_POST['act']=="edit") {
	/*
   	$sql="select * from jshow_setup where kind_id='".$_POST['kind_id']."'";
   	$res=$CONN->Execute($sql) or die ("SQL Error! query=".$sql);
   	$kind_id=$res->fields['kind_id'];
   	$id_name=$res->fields['id_name'];
		$memo=$res->fields['memo'];
		$max_width=$res->fields['max_width'];
		$max_height=$res->fields['max_height'];
		$display_mode=$res->fields['display_mode'];
		//統計本分類有幾張圖
		$sql="select count(*) from jshow_pic where kind_id='".$_POST['kind_id']."'";
   	$res=$CONN->Execute($sql) or die ("SQL Error! query=".$sql);
		$Number_pic=$res->fields[0];
		*/
		$row=get_setup($_POST['kind_id']);
		
		if (count($row)>0) {
		  foreach ($row as $k=>$v) { ${$k}=$v; }
		}		
		
} // end if


//秀出網頁
head("分類區管理");

$tool_bar=&make_menu($menu_p);

//列出選單
echo $tool_bar;


$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
  echo "抱歉！本功能需管理權才能操作！";
  exit();
}

?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="hidden" name="act" value="">
<input type="hidden" name="opt1" value="">
<table border="0" width="100%" cellspacing="0" cellpadding="3">
 <tr>
 		<!--- 左邊選單 ---->
 		<td valign=top bgcolor="#CCCCCC">
 		<table border="0" width="100%" cellspacing="0" cellpadding="0" >
    	<tr>
    		<td>
				<select id="SELECT_kind_id" name="kind_id" size="20">
					<optgroup style="color:#FF0000" label='請選擇分類區'></optgroup>
					<?php
					$query = "select * from jshow_setup ";
					$result= $CONN->Execute($query) or die ($query);
					while( $row = $result->fetchRow()){
						if ($row["kind_id"] == $kind_id ){
							echo sprintf(" <option value=\"%s\" selected>%s</option>",$row["kind_id"],$row["id_name"]);
						}	else {
							echo sprintf(" <option value=\"%s\">%s</option>",$row["kind_id"],$row["id_name"]);
						} // end else if
					}
					?>
				</select>
     		</td>
     	</tr>
    </table>
 		</td> 		
 		<!--- 右邊選單 ---->
		<td width="100%" valign="top" bgcolor="#CCCCCC">
			<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			  <tr id="LIST_BTN_insert" style="display:block">
					<td align="center" valign="middle" bgcolor="#c0c0c0" >
					  <input type="button" value="新增分類區" class="BTN_insert"> 
					</td>
			  </tr>
			  <tr id="LIST_FORM_edit" style="display:none">
			  	<td>
						<table>
						  <tr>
						    <td>分類區代碼(kind_id)</td>
						    <td style="color:red"><b><?php echo $kind_id;?></b></td>
						  </tr>
						  <tr>
						    <td>分類區名稱</td>
						    <td><input type="text" name="id_name" value="<?php echo $id_name;?>"></td>
						  </tr>
						  <tr>
						    <td>分類區說明</td>
						    <td><textarea rows="5" cols="60" name="memo"><?php echo $memo;?></textarea></td>
						  </tr>
						  <tr>
						    <td>圖片限制寬度</td>
						    <td><input type="text" name="max_width" value="<?php echo $max_width;?>"></td>
						  </tr>
						  <tr>
						    <td>圖片限制高度</td>
						    <td><input type="text" name="max_height" value="<?php echo $max_height;?>"></td>
						  </tr>
						  <tr>
						    <td>展圖模式</td>
						    <td>
						    	<select size="1" name="display_mode">
						    	 <option value="0"<?php if ($display_mode=="0") echo " selected";?>>資料庫內此分類圖片依序秀出</option> 
						    	 <option value="1"<?php if ($display_mode=="1") echo " selected";?>>資料庫內此分類圖片依亂數秀出</option>
						    	 <option value="2"<?php if ($display_mode=="2") echo " selected";?>>依指定日期秀出此分類特定圖片</option>
						    	</select>
						    	</td>
						  </tr>

						</table>				  	
			  	</td>
			  </tr>
			  <tr id="LIST_BTN_insert_submit" style="display:none">
					<td align="center" valign="middle" bgcolor="#FFFFCC" >
					  <input type="button" value="確定新增" id="BTN_insert_submit"> 
					</td>
			  </tr>
			  <tr id="LIST_BTN_update_submit" style="display:none">
					<td align="center" valign="middle" bgcolor="#FFCCCC">
					  <input type="button" value="確定修改" id="BTN_update_submit">
					  <?php
					  if ($Number_pic==0) {
					  ?> 
					    <input type="button" value="刪除此分類" id="BTN_delete_submit"> 
					  <?php 
					  } // end if 
					  ?>
					</td>
			  </tr>
			</table>
			<table border="0">
			  <tr>
 					<td style="color:#FF0000;font-size:9pt"><?php echo $INFO;?></td>
 				</tr>
			</table>			
	  </td>
 </tr>
 <tr id="LIST_BTN_insert2" style="display:none">
		<td align="middle" valign="middle" bgcolor="#CCCCCC">
			  <input type="button" value="新增分類區" class="BTN_insert"> 
		</td>
		<td align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;</td>
  </tr>

</table>

<?php
	foot();
?>
<Script>
	
$(document).ready(function(){
	var act='<?php echo $_POST['act'];?>';
   //修改模式
   if (act=='update' || act=='edit') {
   		$("#LIST_BTN_insert2").show();
     	$("#LIST_BTN_insert").hide();
  	 	$("#LIST_FORM_edit").show();
  		$("#LIST_BTN_update_submit").show();
   }  
}); 

	
//按下新增鈕時
$(".BTN_insert").click(function(){
  $("#LIST_BTN_insert").hide();
  $("#LIST_FORM_edit").show();
  $("#LIST_BTN_insert_submit").show();
	$("#LIST_BTN_update_submit").hide();
  document.myform.kind_id.value='';
	document.myform.id_name.value='';
	document.myform.memo.value='';
	document.myform.max_width.value='1024';
	document.myform.max_height.value='768';
	document.myform.id_name.focus();
});

//按下確定新增
$("#BTN_insert_submit").click(function(){
	if (document.myform.id_name.value=='') {
		alert("請輸入分類區名稱!");
    document.myform.id_name.focus();
	  return false;
	}
	if (document.myform.memo.value=='') {
		alert("請針對分類區進行說明, 以便使用者能明白此分類區注意事項!");
    document.myform.memo.focus();
	  return false;
	}
	if (document.myform.max_width.value=='') {
		alert("請輸入此分類區上傳圖檔的最大寬度!");
    document.myform.max_width.focus();
    return false;
	}
	if (document.myform.max_height.value=='') {
		alert("請輸入此分類區上傳圖檔的最大高度!");
    document.myform.max_height.focus();
	  return false;
	}
  document.myform.act.value="insert";
  document.myform.submit();
});

//按下確定修改
$("#BTN_update_submit").click(function(){
 document.myform.act.value="update";
 document.myform.submit();
});

//按下確定刪除
$("#BTN_delete_submit").click(function(){
	if (confirm("您確定要刪除分類區：『<?php echo $id_name;?>』?")) {
   document.myform.act.value="delete";
   document.myform.submit();
  }
  return false;
});

//選擇分類區時
$("#SELECT_kind_id").change(function(){
 document.myform.act.value="edit";
 document.myform.submit();
});

</Script>