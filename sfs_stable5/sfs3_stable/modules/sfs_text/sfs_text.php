<?php

// $Id: sfs_text.php 7941 2014-03-18 07:53:40Z infodaes $

include "sfs_text_config.php";
//檢查權限
sfs_check();

head("選項清單設定");
print_menu($menu_p);

$p =$_GET[p];
$key =$_POST[key];
if ($key =='')
	$key=$_GET[key];

//按鍵處理
switch($key) {
	case $postBtn :			
		$sql_insert = "insert into sfs_text (d_id,g_id,t_name,t_parent,p_id,p_dot,t_kind,t_order_id) values ('$_POST[d_id]','$_POST[g_id]','$_POST[t_name]','$_POST[t_parent]','$_POST[p_id]','$_POST[p_dot]','$_POST[t_kind]','$_POST[t_order_id]')";
		$CONN->Execute($sql_insert) or trigerr_error("sql 語法錯誤 $sql_insert",E_USER_ERROR);
	break;
	case $editBtn :
		$query = "update sfs_text set d_id='$_POST[d_id]',t_name='$_POST[t_name]',t_order_id='$_POST[t_order_id]' where t_id='$_POST[e_t_id]'";
		$CONN->Execute($query) or die ($query);
	break;
	case "delete":
		delete_item($_GET[t_id]);
	break;
}

//建立 treemenu 類別
$tree1 = new TreeMenu();
$tree1 ->default_p = "0";

//左邊 menu 選單

$query = "select * ,concat(t_parent,t_id) as oo  from sfs_text where g_id='$g_id' order by t_kind ,oo,d_id ";
//echo $query ."<BR>";
$result = $CONN->Execute($query) or die ($query);
$row_num = $result->RecordCount();
$doexe[] = ".選擇類別 ($row_num)";
$this_item = $_GET[this_item];
if ($row_num) {
	while(!$result->EOF) {
		$result2 = $CONN->Execute("select count(t_id) as cc from sfs_text where p_id='".$result->fields[t_id]."' ");
		$tol = "" ;
		if(!$result2->EOF) {
			if ($result2->fields[0])
				$tol = "(".$result2->fields[0].")";
		}
		
		if (!$this_item && $i++ == 0)
			$this_item = $result->fields[t_id];
		$dot = "..".$result->fields[p_dot];		
		if ($this_item == $result->fields[t_id])
			$doexe[] = $dot."<b>".$result->fields[t_name]."$tol</b>^^$_SERVER[PHP_SELF]?this_item=".$result->fields[t_id]."&g_id=$g_id";
		else
			$doexe[] = $dot.$result->fields[t_name]."$tol^^$_SERVER[PHP_SELF]?this_item=".$result->fields[t_id]."&g_id=$g_id";
	
		$result->Movenext();
	}
}

$query = "select * from sfs_text where t_id='$this_item' ";
$result = $CONN->Execute($query) or die ($query);
$t_id = $result->fields["t_id"];
$p_id = $result->fields["t_id"];
$t_order_id = $result->fields["t_order_id"];
$d_id = $result->fields["d_id"];
$g_id = $result->fields["g_id"];
$t_parent = $result->fields[t_parent];
$p_dot = $result->fields[p_dot];
$t_kind = $result->fields[t_kind];
$pos = strlen($p_dot);


$dot_str = ".";
for($i=0;$i<$pos;$i++)
	$dot_str .= ".";
	
$t_parent_old = $t_parent;
$t_parent = $t_parent.$t_id.",";

$t_name = $result->fields["t_name"];
 
//右邊內容

$tree1->doexe= $doexe;
$tree1->oth_link= "this_item=$t_id&g_id=$g_id";
//$tree1->debug();
?>
<script language="JavaScript"><!--
function setfocus() {
      document.myform.d_id.focus();
      return;
 }

function checkok() {	
	if (document.myform.d_id.value=='' || document.myform.t_name.value =='' ) {
		  alert('編號及內容不可為空白');
		  document.myform.d_id.focus();
		  return false;
	}
	else
		return true;
}
// -->
</script>
<body  onload="setfocus()">
<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC">
  <tr><td valign=top  width=200 nowrap> 
   <?php
   	
   	$tree1->print_tree($p);
   ?>
   </td>
   <td valign=top width=100%>
   <!------ right content --->
    <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
    	<form name=myform method=post action=<?php echo $PHP_SELF ?> >
    	<tr class=title_mbody><td colspan=4 align=left><?php echo get_text_parent_name($t_parent_old).$t_name ?></td></tr>
    	<tr class=title_sbody1><td align=center>排序</td><td align=center>代號(20個字元內)</td><td align=center>內容</td><td align=center>動作</td></tr>
    	<tr>
    	<input type="hidden"  name="t_parent" value="<?php echo sprintf("%s",$t_parent) ?>">
    	<input type="hidden"  name="p_dot" value="<?php echo $dot_str ?>">    
    	<input type="hidden"  name="t_kind" value="<?php echo $t_kind ?>">        	
    	<input type="hidden"  name="g_id" value="<?php echo $g_id ?>">        	
    	<input type="hidden"  name="p_id" value="<?php echo $t_id ?>">
    	<input type="hidden"  name="p" value="<?php echo $p ?>">    	
    	<input type="hidden"  name="this_item" value="<?php echo $this_item ?>">
    	<?php
    		if ($key=="edit"){
    			$query = "select t_id,d_id,t_name,t_order_id from sfs_text where t_id='$_GET[e_t_id]' ";
    			$result = $CONN->Execute($query);
    			while(!$result->EOF) {
				$vt_id = $result->fields["t_id"];
				$vd_id = $result->fields["d_id"];
				$vt_name = $result->fields["t_name"];
				$vt_order_id = $result->fields["t_order_id"];
				$result->MoveNext();
			}
			echo "<input type=\"hidden\" name=\"e_t_id\" value=\"$vt_id\" >";
		}
		
		if ($g_id<>''){	
			echo '
    			<td><input type="text" size="5" maxlength="6" name="t_order_id" value="'.$vt_order_id.'" ></td>
    			<td><input type="text" size="10" maxlength="20" name="d_id" value="'.$vd_id.'" ></td>
	    		<td><input type="text" size="50" maxlength="100" name="t_name" value="'.$vt_name.'"></td>';
			if ($key == 'edit') $temp_btn = $editBtn; else $temp_btn =$postBtn;
		    	echo '<td align=center><input name="key" type="submit" value="'.$temp_btn.'" onClick="return checkok();" ></td>';
		}
    	?>
    	</tr>
    	<?php 
    		//列出    		    		
    		$query = "select * from sfs_text where p_id='$this_item' and p_id<>'' order by t_order_id,d_id ";
		$result = $CONN->Execute($query) or die ($query);
		while(!$result->EOF) {
			$t_id = $result->fields["t_id"];
			$d_id = $result->fields["d_id"];
			$t_order_id = $result->fields["t_order_id"];
			$t_parent = $result->fields["t_parent"];
			$t_name = $result->fields["t_name"];
			echo "<tr><td>$t_order_id</td><td>$d_id</td><td>$t_name</td><td align=center>";
			//if($_GET[key] <> "edit")
				echo "<a href=\"$_SERVER[PHP_SELF]?key=edit&e_t_id=$t_id&this_item=$this_item&p=$p\">修改</a>&nbsp;&nbsp;";			
			echo "<a href=\"$_SERVER[PHP_SELF]?key=delete&t_id=$t_id&this_item=$this_item&p=$p\" onClick=\"return confirm('確定刪除 $t_name ?');\">刪除</a>";
			echo "</td></tr>";
			$result->MoveNext();
		}			
    	?>	
	
		   	
   	</form>
   
   	</table>
   <!------ end content --->   	
   </td>
   </tr>
</table>
<?php foot(); ?>
