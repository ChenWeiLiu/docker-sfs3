<?php 

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "database_info_config.php";
// 認證檢查
sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


// 建立類別
//$tea1 = new m_unit_class();
//印出檔頭
head();

$sql_get_tables = "SHOW TABLES FROM $mysql_db ";

$recordSet = $CONN->Execute($sql_get_tables);
	
switch($do_key) {
//確定
	case $btnPost:
	while (!$recordSet->EOF){
		
		$table = $recordSet->fields[0];
		$cname = "cname_".$recordSet->fields[0];			
		$table_group = "group_".$recordSet->fields[0];
		if($$table_group <>'') {
			$query = "replace into sys_data_table (d_table_name ,d_table_cname,d_table_group) values('$table','".trim($$cname)."','".trim($$table_group)."')";
			$CONN->Execute($query) or die ($query);
		}
		$recordSet->MoveNext();
	}
	break;
	case "delete":
	$query = "delete from sys_data_table where d_table_name='$d_table_name'";
	$CONN->Execute($query) or die($query);	
	break;
}
if(isset($sel_d_table_name)) {
	$query = "select d_table_group from sys_data_table where d_table_name='$sel_d_table_name'"; 
	$res = $CONN->Execute($query) or die($query);
	$d_table_group_id = $res->fields[0];	
}

//取得資料表群組
$query = "select d_table_group,count(d_table_name) as cc from sys_data_table group by d_table_group  desc ";
$res = $CONN->Execute($query) or die($query);
$temp_table_group = $res->fields[d_table_group];
while(!$res->EOF) {
		$group_arr[$res->fields[d_table_group]] = $res->fields[d_table_group]." - ".$res->fields[cc];	
	$res->MoveNext();
}
	$group_arr[none] = "未歸類";
if (!isset($d_table_group_id))
	$d_table_group_id = $temp_table_group;
if ($d_table_group_id=='')
	$d_table_group_id='none';
if($d_table_group_id=='none')
	$query = "select d_table_name ,d_table_cname,d_table_group  from sys_data_table  order by d_table_name ";
else
	$query = "select d_table_name ,d_table_cname,d_table_group  from sys_data_table where d_table_group='$d_table_group_id' order by d_table_name ";
$dataSet = $CONN->Execute($query) or die ($query);
while(!$dataSet->EOF){
	$data[$dataSet->fields[d_table_name]][cname]=$dataSet->fields[d_table_cname];
	$data[$dataSet->fields[d_table_name]][group]=$dataSet->fields[d_table_group];
	$dataSet->MoveNext();
}
//模組選單
print_menu($menu_p);
?>  

<TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0 class=main_body WIDTH="100%" ALIGN="CENTER"> 
<tr>
<td>
<form name="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method=post>
<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 BGCOLOR="#E6E6FA" WIDTH="100%" ALIGN="CENTER"> 
<TR>
<TD>
<TABLE BORDER="0" BGCOLOR="#FFFFFF" WIDTH="100%" CELLPADDING="2" CELLSPACING="0" align=center >

<TR CLASS="genlisthead">
 <td colspan=4  class="genlistheadt">	
<?php
echo ($mode == "修改模式")? "<input type=submit name=mode value=\"瀏覽模式\">&nbsp;<input type=submit name=mode value=\"修改模式\" disabled> &nbsp;<input type=submit name=do_key value=\"$btnPost\">":"<input type=submit name=mode value=\"瀏覽模式\" disabled>&nbsp<input type=submit name=mode value=\"修改模式\" >";

echo "			</td>		
		</tr>
		<tr class=\"genlistheadt\" >
			<td >資料表名稱</td>
			<td >中文資料表名稱</td>
			<td >群組: ";
$sel = new drop_select();
$sel->s_name = "d_table_group_id";
$sel->id= $d_table_group_id;
$sel->arr = $group_arr;
$sel->has_empty = false;
$sel->is_submit = true;
$sel->do_select();

echo "			</td>
			<td >動作</td>
			
";
	
echo "		</tr>";
$recordSet->MoveFirst()	;
while (!$recordSet->EOF) {
	$table = $recordSet->fields[0];
	if((!@in_array($table,array_keys($data))&& $d_table_group_id=='none') || ( $d_table_group_id<>'none' && $data[$table][group] <>'')) {
		$bgcolor = $cfgBgcolorOne;
		$i++ % 2 ? 0 : $bgcolor = $cfgBgcolorTwo;
		$enc_table = urlencode($table);
		$query = "?d_table_name=$table";
		if ($mode == "修改模式"){
			$d_table_cname	="<input type=text name=\"cname_$table\" value=\"".$data[$table][cname]."\" >";
			$d_table_group	="<input type=text name=\"group_$table\" value=\"".$data[$table][group]."\" >";		
		}
		else {
			$d_table_cname	=$data[$table][cname];
			$d_table_group	=$data[$table][group];		
		}
		?>
		<tr bgcolor=<?php echo $bgcolor; ?>>
		<td ><b><a href="<?php echo "tpl_field.php$query"; ?>"><?php echo $table;?></a></b></td>
	
		<td ><?php echo $d_table_cname;?></td>
		<td ><?php echo $d_table_group;?></td>
		<td ><a href="<?php echo "{$_SERVER['PHP_SELF']}?do_key=delete&d_table_name=$table&d_table_group_id=$d_table_group_id"; ?>" onClick="return confirm('確定刪除 <?php echo $data[$table][cname] ?> ?');">刪除</a></td>
		</tr>
	<?php
	}
	$recordSet->MoveNext();
}

echo "</table>\n";	
echo "</form>";
echo "</td></tr></table>";
echo "</td></tr></table>";

//印出檔頭
foot();
?> 
