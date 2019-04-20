<?php

// $Id: boardadmin.php 5310 2009-01-10 07:57:56Z hami $

//設定檔載入檢查
include "board_man_config.php";

// --認證 
sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
	foreach ( $_POST as $keyinit => $valueinit) {
		$$keyinit = $valueinit;
	}
	foreach ( $_GET as $keyinit => $valueinit) {
		$$keyinit = $valueinit;
	}
}

switch($key) {
	case "確定新增" :
	$bk_id=$new_bk_id;
	$sql_check="select * from jboard_kind where bk_id='$bk_id'";
	$res=$CONN->Execute($sql_check);
	if ($res->RecordCount()>0) {
		$INFO="分類區代碼重覆了, 請重新訂定!";	
		$key="新增分類區";
		$row['board_name']=$board_name;
		$row['board_date']=$board_date;
		$row['board_k_id']=$board_k_id;
		$row['board_last_date']=$board_last_date;
		$row['board_is_upload']=$board_is_upload;
		$row['board_is_public']=$board_is_public;
		$row['board_admin']=$board_admin;
		$row['bk_order']=$bk_order;
		$row['board_is_sort']=$board_is_sort;
		$row['position']=$position;
		$row['board_is_coop_edit']=$board_is_coop_edit;
		$row["synchronize"]=$synchronize;
		$row["synchronize_days"]=$synchronize_days;
		
	} else {
	 $sql_insert = "insert into jboard_kind (bk_id,board_name,board_date,board_k_id,board_last_date,board_is_upload,board_is_public,board_admin,bk_order,board_is_sort,position,board_is_coop_edit,synchronize,synchronize_days) values ('$bk_id','$board_name','$board_date','$board_k_id','$board_last_date','$board_is_upload','$board_is_public','$board_admin','$bk_order','$board_is_sort','$position','$board_is_coop_edit','$synchronize','$synchronize_days')";
	 mysql_query($sql_insert,$conID) or die($sql_insert);
  }
	break;
	case "確定修改" :
	$sql_update = "update jboard_kind set board_name='$board_name ',board_date='$board_date',board_k_id='$board_k_id',board_last_date='$board_last_date',board_is_upload='$board_is_upload',board_is_public='$board_is_public',board_admin='$board_admin',bk_order='$bk_order',board_is_sort='$board_is_sort',position='$position',board_is_coop_edit='$board_is_coop_edit',synchronize='$synchronize',synchronize_days='$synchronize_days' where bk_id='$bk_id' ";
	mysql_query($sql_update,$conID) or die ($sql_update);
	//更改代碼
	if ($new_bk_id!=$bk_id) {
	 	$sql_check="select * from jboard_kind where bk_id='$new_bk_id'";
	  $res=$CONN->Execute($sql_check);
	 if ($res->RecordCount()>0) {
		$INFO="分類區代碼重覆了, 請重新訂定! 無法更新代碼!";   
   } else {
   	//修改 jboard_kind 的 bk_id
   	  $sql="update jboard_kind set bk_id='$new_bk_id' where bk_id='$bk_id'";
   	  $res=$CONN->Execute($sql);
   	//修改 jboard_check 的 pro_kind_id
   	  $sql="update jboard_check set pro_kind_id='$new_bk_id' where pro_kind_id='$bk_id'";
   	  $res=$CONN->Execute($sql);
   	//修改 jboard_p 的 bk_id
   	  $sql="update jboard_p set bk_id='$new_bk_id' where bk_id='$bk_id'";
   	  $res=$CONN->Execute($sql);
   }
	}
	
	break;
	case "確定刪除" :
	$sql_update = "delete from jboard_kind  where bk_id='$bk_id'";	
	mysql_query($sql_update,$conID) or die ($sql_update);
	break;
}

if ($key != "新增分類區"){
//  --目前資料
	$query = "select * from jboard_kind where bk_id ='$bk_id' ";
	$result = mysql_query ($query,$conID) or die ($query); 
	$row = mysql_fetch_array($result);
	
	if ($_POST['continue_insert']) $key='新增分類區';
}
else
$query = "select bk_id,board_name from jboard_kind order by bk_order,bk_id limit 0,1 ";
	
$bk_id = $row["bk_id"];
$board_name = $row["board_name"];
$board_date = $row["board_date"];
$board_k_id = $row["board_k_id"];
$board_last_date = $row["board_last_date"];
$board_is_upload = $row["board_is_upload"];
$board_is_sort = $row["board_is_sort"];
$board_is_public = $row["board_is_public"];
$board_is_coop_edit = $row["board_is_coop_edit"];
$board_admin = $row["board_admin"];
$position = $row["position"];
$synchronize = $row["synchronize"];
$synchronize_days = ($row["synchronize_days"]=='')?30:$row["synchronize_days"];
$bk_order = ($row["bk_order"]>0)?$row["bk_order"]:100;

if($board_date ==0)
	$board_date = date("Y-m-j");
if ($board_is_upload == "1"){
	$board_is_upload_c1 = " checked ";
	$board_is_upload_c2 = "";
}
else{
	$board_is_upload_c2 = " checked ";
	$board_is_upload_c1 = "";
}
if ($board_is_public == "1"){
	$board_is_public_c1 = " checked ";
	$board_is_public_c2 = "";
}
else{
	$board_is_public_c2 = " checked ";
	$board_is_public_c1 = "";
}

if ($board_is_sort == "1"){
	$board_is_sort_c1 = " checked ";
	$board_is_sort_c2 = "";
}
else{
	$board_is_sort_c2 = " checked ";
	$board_is_sort_c1 = "";
}

if ($board_is_coop_edit == "1"){
	$board_is_coop_edit_c1 = " checked ";
	$board_is_coop_edit_c2 = "";
}
else{
	$board_is_coop_edit_c2 = " checked ";
	$board_is_coop_edit_c1 = "";
}


$board_k_id_p = array("處室單位版","班級版區");

//  --程式檔頭
head();
//選單連結字串
$linkstr = "bk_id=$bk_id";
print_menu($menu_p,$linkstr); 

?>
<body  onload="setfocus()">
<script language="JavaScript"><!--
function setfocus() {
<?php
	if ($key == "新增分類區" or $bk_id =="")
		echo "document.eform.elements[0].focus();";
	else
		echo "document.eform.elements[1].focus();";
?>
return;
}

function checkok()
{
    var OK=true
    if      (document.eform.new_bk_id.value == "" )
     {
             OK=false;
       str= "分類區代號不可留空！請再詳填！";
     }
     if      (document.eform.board_name.value == "")
     {
             OK=false;
       str= "分類區名稱不可留空！請再詳填！";
     }
    if (OK == false)        {
     alert(str)
    }
       return OK
}
// -->
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
 <tr><td valign=top bgcolor="#CCCCCC">
 <table border="0" width="100%" cellspacing="0" cellpadding="0" >
    <tr>
<?php
if ($key == "刪除"){
	echo "<td align=center>";
	echo "<form action=\"$_SERVER[PHP_SELF]\" name=eform method=\"post\">";
	echo "  <input type=hidden name=\"bk_id\" value=\"$bk_id\">";	
	echo sprintf ("<br>確定刪除 <B><font color=red>%s</font></B>",$board_name);
	echo "<br><br><input type=\"submit\" name=\"key\" value=\"確定刪除\">";
	echo "</td></tr></form></table>";
	echo "</td></tr></table>";
	foot();
	exit;
}
?>
      <td  valign="top" >    
	<?php      
	
	//建立左邊選單	
	/*
	$grid1 = new sfs_grid_menu;  //建立選單	   
	//$grid1->bgcolor = $gridBgcolor;  // 顏色   
	//$grid1->row = $gri ;	     //顯示筆數
	$grid1->key_item = "bk_id";  // 索引欄名  	
	$grid1->display_item = array("bk_order","bk_id","board_name");  // 顯示欄名   	
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select bk_id,board_name,bk_order from jboard_kind order by bk_order,bk_id";   //SQL 命令   
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($bk_id); // 顯示畫面   
  */

?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
<select name="bk_id" onchange="this.form.submit()" size="20">
	<option value="">-[順序]-名稱(代碼)────</option>

<?php
	$query = "select * from jboard_kind order by bk_order,bk_id ";
	$result= $CONN->Execute($query) or die ($query);
	while( $row = $result->fetchRow()){
		$P=($row['position']>0)?"".str_repeat("|--",$row['position']):"";
		
		if ($row["bk_id"] == $bk_id  ){
			echo sprintf(" <option style='color:%s' value=\"%s\" selected>[%05d] %s%s(%s)</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row["board_name"],$row["bk_id"]);
			$board_name = $row["board_name"];
		}
		else
			echo sprintf(" <option style='color:%s' value=\"%s\">[%05d] %s%s(%s)</option>",$position_color[$row['position']],$row["bk_id"],$row['bk_order'],$P,$row["board_name"],$row["bk_id"]);
	}
	echo "</select>";

	?>
</form>
     </td></tr></table>
  </td>
 <!--- 右邊選單 ---->
<td width="100%" valign=top bgcolor="#CCCCCC">

<form action="<?php echo $PHP_SELF ?>" name=eform method="post" onsubmit="return checkok()"> 
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<?php
//if ($key == "新增分類區" or $key == "確定新增" or $bk_id == ""){
?>
  <td align="right"  nowrap>分類區代號</td>
	<td><input type="text" size="36" maxlength="36" name="new_bk_id" value="<?php echo $bk_id ?>"></td>
	<input type="hidden" name="bk_id" value="<?php echo $bk_id ?>">
  <?php	
  /*
  } else {
?> 
   <input type="hidden" name="bk_id" value="<?php echo $bk_id ?>">
   <td align="right"  nowrap>分類區代號</td>
	<td><?php echo $bk_id ?></td>
	
  <?php	
  */
 //}
	
 ?> 
</tr> 
	<td align="right"  nowrap>分類區名稱</td>
	<td><input type="text" size="36" maxlength="72" name="board_name" value="<?php echo $board_name ?>"></td>
</tr>


<tr>
	<td align="right" >開版日期</td>
	<td><input type="text" size="10" maxlength="10" name="board_date" value="<?php echo $board_date ?>">(格式：2000-08-01)</td>
</tr>


<tr>
	<td align="right" >類別</td>
	<td align="left" >
	<select name=board_k_id>
<?php
	for ($i=0;$i< count($board_k_id_p);$i++)
	if ($i == $board_k_id)
		echo "<option value=\"$i\" selected >".$board_k_id_p[$i];
	else
		echo "<option value=\"$i\">".$board_k_id_p[$i];
?>
	</select>
	</td>
</tr>
<tr>
	<td align="right" >分類區序號</td>
	<td><input type="text" size="10" maxlength="10" name="bk_order" value="<?php echo $bk_order; ?>">(格式：數字 1-99999)</td>
</tr>
<tr>
	<td align="right" >分類區層級</td>
	<td><select size="1" name="position">
	 <?php
	  for($i=0;$i<10;$i++) {
		?>
		<option value="<?php echo $i;?>"<?php if ($i==$position) echo " selected";?>><?php echo $position_array[$i];?></option>		
		<?php
	  }
	 ?>	
		</select>(格式：數字 0-9，讓編輯列表可內縮，方便閱讀)
	</td>
</tr>

<tr>
	<td align="right" >使用期限</td>
	<td><input type="text" size="10" maxlength="10" name="board_last_date" value="<?php echo $board_last_date; ?>">(格式：2000-08-01)</td>
</tr>


<tr>
	<td align="right" >開放上傳檔案</td>
	<td align="left" >
	<input type="radio" name="board_is_upload" value="1" <?php echo $board_is_upload_c1 ?>>是&nbsp;&nbsp;
	<input type="radio" name="board_is_upload" value="0" <?php echo $board_is_upload_c2 ?>>否
	</td>
</tr>
<tr>
	<td align="right" >開放調整文章順序</td>
	<td align="left" >
	<input type="radio" name="board_is_sort" value="1" <?php echo $board_is_sort_c1 ?>>是&nbsp;&nbsp;
	<input type="radio" name="board_is_sort" value="0" <?php echo $board_is_sort_c2 ?>>否
	</td>
</tr>

<tr>
	<td align="right" >本分類是否公開</td>
	<td align="left" >
		<input type="radio" name="board_is_public" value="1" <?php echo $board_is_public_c1 ?>>是&nbsp;&nbsp;
	  <input type="radio" name="board_is_public" value="0" <?php echo $board_is_public_c2 ?>>否
	 (若「是」,則未經授權使用者也能瀏覽文件)
	</td>
</tr>

<tr>
	<td align="right" >本分類是否可共編文件</td>
	<td align="left" >
		<input type="radio" name="board_is_coop_edit" value="1" <?php echo $board_is_coop_edit_c1 ?>>是&nbsp;&nbsp;
	  <input type="radio" name="board_is_coop_edit" value="0" <?php echo $board_is_coop_edit_c2 ?>>否
	(若「否」,則只能編輯自己的文件)
	</td>
</tr>
<tr>
	<td align="right" >同步呈現板區</td>
	<td align="left" ><input type="text" size="10" maxlength="10" name="synchronize" value="<?php echo $synchronize; ?>">(輸入分類區代碼)</td>
</tr><tr>
	<td align="right" >同步期限</td>
	<td align="left" ><input type="text" size="10" maxlength="10" name="synchronize_days" value="<?php echo $synchronize_days; ?>">(輸入期限)</td>
</tr>
<tr>
	<td align="center" valign="middle" bgcolor="#c0c0c0" colspan ="2" BGCOLOR=#cbcbcb >
<?php	
	if ($bk_id == "") {
		echo "<input type=submit name=key value=\"確定新增\">  ";
			?>
			<font size="2"> 
			<input type="checkbox" name="continue_insert" value="1"<?php if ($_POST['continue_insert']) echo " checked";?>>持續開啟本畫面</font>
			<?php
	} else if ($key != "新增分類區" ){
		echo "<input type=submit name=key value=\"確定修改\">  ";
		echo "<input type=submit name=key value=\"刪除\">  ";
		echo "<input type=submit name=key value=\"新增分類區\">  ";
	} else {
		echo "<input type=submit name=key value=\"確定新增\">";
		?>
		<font size="2"> 
		<input type="checkbox" name="continue_insert" value="1"<?php if ($_POST['continue_insert']) echo " checked";?>>持續開啟本畫面</font>
		<?php 
	}

?>
	</td>
</tr>
<tr>
	<td style='font-size:9pt' colspan ="2">
	說明：<br>
	1.《開放調整文章順序》文章列表時預設是以發佈時間做為排序條件，若啟用，則此分類區管理者可任意調整文章順序。<br>
	2.《本分類是否公開》若選擇「否」，未經授權的使用者將看不到此分類區。<br>
	</td>
</tr>

</table>
</TD></TR>
<tr>
	<td style="color:#FF0000"><?php echo $INFO;?></td>
</tr>
</TABLE>

<?php
	foot();
?>
