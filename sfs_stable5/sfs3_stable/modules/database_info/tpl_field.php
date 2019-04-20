<?php 

// $Id: tpl_field.php 8141 2014-09-23 08:16:42Z smallduh $

// 載入設定檔
include "database_info_config.php";
// 認證檢查
sfs_check();

$d_table_name = ($_POST['d_table_name']) ? $_POST['d_table_name'] : $_GET['d_table_name'];

// 建立類別
//$tea1 = new m_unit_class();
//印出檔頭
if (isset($d_table_name))
	$tablename = $d_table_name;
else
	header ("Location: index.php");
head();
?>
<script language="JavaScript">
   <!--
   function CheckAll()
   {
      for (var i=0;i<document.myform.elements.length;i++)
      {
         var e = document.myform.elements[i];
         if (e.type == 'checkbox' && e.name != 'allbox')
            e.checked = document.myform.allbox.checked;
      }
   }
   function OpConfirm(text)
   {   
      for (var i=0;i<document.myform.elements.length;i++)
      {
         var e = document.myform.elements[i];
         if (e.type == 'checkbox' && e.name != 'allbox' && e.checked == 1 )
            return confirm(text);
      }
      return false;
   }
   //-->
</script>

<?php
$sql_get_tables = "SHOW FIELDS FROM $tablename  ";
$recordSet = $CONN->Execute($sql_get_tables) or die($sql_get_tables);
//確定
if ($_POST[do_key]==$btnPost){
	$query = "delete from sys_data_field where d_table_name='$tablename' ";
	$CONN->Execute($query);
	while (!$recordSet->EOF){
		$d_field_name = $recordSet->fields[Field];
		$d_field_cname = "cname_".$recordSet->fields[Field];		
		
		$d_field_type =  addslashes($recordSet->fields[Type]);
		$d_field_order = "order_".$recordSet->fields[Field];
		$d_is_display = "check_".$recordSet->fields[Field];
		$d_field_xml = "xml_".$recordSet->fields[Field];
		$query = "insert into sys_data_field (d_table_name ,d_field_name,d_field_cname,d_field_type,d_field_order,d_is_display,d_field_xml ) values('$tablename','".$d_field_name."','".$_POST[$d_field_cname]."','".$d_field_type."','".$_POST[$d_field_order]."','".$_POST[$d_is_display]."','".$_POST[$d_field_xml]."')";
		//$CONN->debug=true;
		//echo $query;
		$CONN->Execute($query) or die($query);			
		
		$recordSet->MoveNext();
	}
}

if (isset($tablename)){
	$query = "select d_table_name ,d_field_name ,d_field_cname ,d_field_type ,d_field_order ,d_is_display,d_field_xml from sys_data_field WHERE d_table_name = '$tablename' order by d_field_name ";
	$dataSet = $CONN->Execute($query);
	//echo $query;
	while(!$dataSet->EOF){
		$data[$dataSet->fields[d_field_name]][d_field_name]=$dataSet->fields[d_field_name];
		$data[$dataSet->fields[d_field_name]][d_field_type]=$dataSet->fields[d_field_type];
		$data[$dataSet->fields[d_field_name]][d_field_cname]=$dataSet->fields[d_field_cname];
		
		$data[$dataSet->fields[d_field_name]][d_field_order]=$dataSet->fields[d_field_order];
		$data[$dataSet->fields[d_field_name]][d_field_xml]=$dataSet->fields[d_field_xml];
		$data[$dataSet->fields[d_field_name]][d_is_display]= $dataSet->fields[d_is_display];
		
		
		$dataSet->MoveNext();
	}
}


//模組選單
print_menu($menu_p);
?>  
<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 class="main_body" WIDTH="100%" ALIGN="CENTER"> 
<tr>
<td>
<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 BGCOLOR="#E6E6FA" WIDTH="100%" ALIGN="CENTER"> 
<TR>
<TD>
<TABLE BORDER="0" BGCOLOR="#FFFFFF" WIDTH="100%" CELLPADDING="2" CELLSPACING="0" align=center >

<TR >
		<td CLASS="grid" valign=top width=100>
		<!-- 資料表選單 -->
<?php
$query = "select d_table_name ,d_table_cname,d_table_group  from sys_data_table order by d_table_group,d_table_name ";
$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);
$grid1->key_item = "d_table_name";
$grid1->display_item = array("d_table_name","d_table_cname");
$grid1->width =100;
$grid1->row =12;
$grid1->color_index_item = "d_table_group";
$grid1->default_color = true; //使用預設顏色
//$grid1->display_color=array("學生資料"=>"red","單位"=>"blue","教育人員"=>"green","問卷"=>"#5533df","學生"=>"#550066");
$grid1->sql_str = $query;
$grid1->do_query(); //執行命令 
$upstr = "設定 <a href=\"index.php?sel_d_table_name=$d_table_name\">$d_table_name </a>";
$grid1->print_grid($d_table_name,$upstr); // 顯示畫面 

?>		
		</td>
<td valign=top >
<form action=<?php echo $_SERVER['PHP_SELF'] ?> name=myform method=post >
<img src="<?php echo "$URI/images/pixel_clear.gif" ?>" HEIGHT="2" width="100%">
<?php

echo "
	<table border=0 width=100%>\n
		<tr bgcolor=lightgrey>
			<td colspan=6  class=genlistheadt>	
";
		
echo ($_POST['mode'] == "修改模式")? "<input type=submit name=mode value=\"瀏覽模式\">&nbsp;<input type=submit name=mode value=\"修改模式\" disabled>&nbsp;<input type=submit name=do_key value=\"$btnPost\">":"<input type=submit name=mode value=\"瀏覽模式\" disabled>&nbsp<input type=submit name=mode value=\"修改模式\" >";

echo "		&nbsp;&nbsp;<a href=\"connect.php?table=$d_table_name\">產生程式碼</a></td>
		</tr>
		<tr bgcolor=lightgrey>
			<td align=center>欄位名稱</td>
			<td align=center>型態</td>
			<td align=center>中文欄名</td>
			<td align=center>XML TAG</td>
			<td align=center>顯示排序</td>
			<td align=center>是否顯示&nbsp;";			
			
			if ($_POST['mode'] == "修改模式")
				echo "<input name=\"allbox\" type=\"checkbox\" value=\"1\"  onClick=\"CheckAll();\">";
			
echo"			</td> ";
	
echo "		</tr>";
$recordSet->MoveFirst()	;
while (!$recordSet->EOF) {
	$bgcolor = $cfgBgcolorOne;
	$i++ % 2 ? 0 : $bgcolor = $cfgBgcolorTwo;
	$table = $tablename;
	$d_field_name = $recordSet->fields['Field'];
	$d_field_type = $data[$d_field_name][d_field_type];
	$d_table_name	 = urlencode($table);
	$query = "?tablename=$table";
	
	if ($_POST['mode'] == "修改模式") {	
		
		$d_field_cname	="<input type=text size=10 name=\"cname_$d_field_name\" value=\"".$data[$d_field_name][d_field_cname]."\" >";
		$d_field_xml	="<input type=text size=10 name=\"xml_$d_field_name\" value=\"".$data[$d_field_name][d_field_xml]."\" >";
		$d_field_order	="<input type=text size=2 name=\"order_$d_field_name\" value=\"".$data[$d_field_name][d_field_order]."\" >";
		if ($data[$d_field_name][d_is_display])
			$d_is_display	="<input type=checkbox name=\"check_$d_field_name\" value=1 checked >";
		else
			$d_is_display	="<input type=checkbox name=\"check_$d_field_name\" value=1 >";
	}
	else {
	
		$d_field_cname	=$data[$d_field_name][d_field_cname];
		$d_field_xml	=$data[$d_field_name][d_field_xml];
		$d_field_order	=$data[$d_field_name][d_field_order];
		
		if ($data[$d_field_name][d_is_display])
			$d_is_display = "yes";
		else
			$d_is_display = "no";
	}
	
	?>
	<tr bgcolor=<?php echo $bgcolor; ?>>
	
	<td ><b><?php echo $d_field_name;?></b></td>
	<td ><?php echo $d_field_type;?></td>
	<td ><b><?php echo $d_field_cname;?></b></td>
	<td ><b><?php echo $d_field_xml;?></b></td>
	<td ><?php echo $d_field_order;?></td>
	<td ><?php echo $d_is_display;?></td>
	</tr>
	<?php
	$recordSet->MoveNext();
}

echo "<input type=hidden name=d_table_name value=\"$d_table_name\">";

echo "</table>\n";	
echo "</form>";
?>
		</td>
	</tr>
</table>
</td></tr></table>
</td></tr></table>
<?php 
//印出檔頭
foot();
?> 
