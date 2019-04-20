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
	$sql_insert = "insert into board_kind (bk_id,board_name,board_date,board_k_id,board_last_date,board_is_upload,board_is_public,board_admin) values ('$bk_id','$board_name','$board_date','$board_k_id','$board_last_date','$board_is_upload','$board_is_public','$board_admin')";
	mysql_query($sql_insert,$conID) or die($sql_insert);
	break;
	case "確定修改" :
	$sql_update = "update board_kind set board_name='$board_name ',board_date='$board_date',board_k_id='$board_k_id',board_last_date='$board_last_date',board_is_upload='$board_is_upload',board_is_public='$board_is_public',board_admin='$board_admin'where bk_id='$bk_id' ";
	mysql_query($sql_update,$conID) or die ($sql_update);
	break;
	case "確定刪除" :
	$sql_update = "delete  from board_kind  where bk_id='$bk_id'";	
	mysql_query($sql_update,$conID) or die ($sql_update);
	break;
}

if ($key != "新增版區"){
//  --目前資料
	$query = "select * from board_kind where bk_id ='$bk_id' ";
	$result = mysql_query ($query,$conID) or die ($query); 
	$row = mysql_fetch_array($result);
}
else
$query = "select bk_id,board_name from board_kind order by bk_id limit 0,1 ";
	
$bk_id = $row["bk_id"];
$board_name = $row["board_name"];
$board_date = $row["board_date"];
$board_k_id = $row["board_k_id"];
$board_last_date = $row["board_last_date"];
$board_is_upload = $row["board_is_upload"];
$board_is_public = $row["board_is_public"];
$board_admin = $row["board_admin"];

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
	if ($key == "新增版區" or $bk_id =="")
		echo "document.eform.elements[0].focus();";
	else
		echo "document.eform.elements[1].focus();";
?>
return;
}

function checkok()
{
    var OK=true
    if      (document.eform.bk_id.value == "" )
     {
             OK=false;
       str= "版區代號不可留空！請再詳填！";
     }
     if      (document.eform.board_name.value == "")
     {
             OK=false;
       str= "版區名稱不可留空！請再詳填！";
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
	$grid1 = new sfs_grid_menu;  //建立選單	   
	//$grid1->bgcolor = $gridBgcolor;  // 顏色   
	//$grid1->row = $gri ;	     //顯示筆數
	$grid1->key_item = "bk_id";  // 索引欄名  	
	$grid1->display_item = array("bk_id","board_name");  // 顯示欄名   	
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select bk_id,board_name from board_kind order by bk_id";   //SQL 命令   
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($bk_id); // 顯示畫面   

	?>
     </td></tr></table>
  </td>
 <!--- 右邊選單 ---->
<td width="100%" valign=top bgcolor="#CCCCCC">

<form action="<?php echo $PHP_SELF ?>" name=eform method="post" onsubmit="return checkok()"> 
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<?php
if ($key == "新增版區" or $key == "確定新增" or $bk_id == ""){
?>
  <td align="right"  nowrap>版區代號</td>
	<td><input type="text" size="12" maxlength="12" name="bk_id" value="<?php echo $bk_id ?>"></td>
  <?php	
  }
 else
{
?> 
   <input type="hidden" name="bk_id" value="<?php echo $bk_id ?>">
   <td align="right"  nowrap>版區代號</td>
	<td><?php echo $bk_id ?></td>
	
  <?php	
 }
	
 ?> 
</tr>
 
	<td align="right"  nowrap>版區名稱</td>
	<td><input type="text" size="20" maxlength="20" name="board_name" value="<?php echo $board_name ?>"></td>
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
	<td align="right" >使用期限</td>
	<td><input type="text" size="10" maxlength="10" name="board_last_date" value="<?php echo $board_last_date ?>">(格式：2000-08-01)</td>
</tr>


<tr>
	<td align="right" >開放上傳檔案</td>
	<td align="left" >
	<input type="radio" name="board_is_upload" value="1" <?php echo $board_is_upload_c1 ?>>是&nbsp;&nbsp;
	<input type="radio" name="board_is_upload" value="0" <?php echo $board_is_upload_c2 ?>>否
	</td>
</tr>


<tr>
	<td align="right" >列在校務版</td>
	<td align="left" ><input type="radio" name="board_is_public" value="1" <?php echo $board_is_public_c1 ?>>是&nbsp;&nbsp;
	 <input type="radio" name="board_is_public" value="0" <?php echo $board_is_public_c2 ?>>否
	</td>
</tr>

<tr>
	<td align="center" valign="middle" bgcolor="#c0c0c0" colspan =2 BGCOLOR=#cbcbcb >
<?php	
	if ($bk_id == "")
		echo "<input type=submit name=key value=\"確定新增\">  ";
	else if ($key != "新增版區" ){
		echo "<input type=submit name=key value=\"確定修改\">  ";
		echo "<input type=submit name=key value=\"刪除\">  ";
		echo "<input type=submit name=key value=\"新增版區\">  ";
	}
	else{
		echo "<input type=submit name=key value=\"確定新增\">";
	}

?>
	</td>
</tr>

</table>
</TD></TR>
</TABLE>
<?php
	foot();
?>
