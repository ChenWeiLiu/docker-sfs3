<?php
// $Id: generate.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include_once "config.php";
sfs_check();

$sn_col=$_POST['sn_col'];
if(empty($sn_col))user_error("沒有指定索引欄位，可能會引起程式錯誤。",256);
$col_default=$_POST['default'];
$table=$_POST['table'];
$field_name=$_POST['field_name'];
$Cname=$_POST['Cname'];
$use=$_POST['use'];
$input_type=$_POST['input_type'];
$size=$_POST['size'];
$use_default=$_POST['use_default'];
$isfun=$_POST['isfun'];
$maxlen=$_POST['maxlen'];
$is_multiple=$_POST['is_multiple'];
$module_file_name=$_POST['module_file_name'];

//------------------------------------------------------------------------------

$sql_insert = "insert into $table (";
$sql_update = "update $table set ";
$sql_select = "select * from $table";

//取得資料欄位
foreach($field_name as $x=>$name){   
	module_maker_col_add($table,$field_name[$x],$Cname[$x],$col_default[$x]);
	if ($use[$x]==1) {   
		$form_main.=make_col($input_type[$x],$field_name[$x],$Cname[$x],$size[$x],$maxlen[$x],$isfun[$x],$use_default[$x],$col_default[$x],$is_multiple[$x]);
		
		// 新增的 SQL
		$sql_insert_fields .= $field_name[$x] . ",";
		$sql_insert_values .="'". "$"."data[" . $field_name[$x] . "]',";
		// 更新的 SQL
		$sql_update .= $field_name[$x] . "='"."$"."data[". $field_name[$x] . "]',";
		// while 時的一些資料
		$while_row_array[]="$".$field_name[$x];
		$while_data.="<td>\$$field_name[$x]</td>";
		$while_data_text.="<td>$Cname[$x]</td>";
		
		if(!empty($col_default[$x])){
			if($input_type[$x]=="select" or $input_type[$x]=="checkbox"){
				foreach($use_default[$x] as $dk=>$dv){
					$mdefault="\"$dk\"=>\"$dv\"";
				}
				$default_op[]="\"$x\"=>array($mdefault)";
			}elseif($isfun[$x]!=1){
				$default_op[]="\"$x\"=>\"$col_default[$x]\"";
			}
		}
   }
}

$default_array=implode(",",$default_op);

//重整各個SQL語法
$sql_insert_fields 	= substr($sql_insert_fields,0,strlen($sql_insert_fields)-1);
$sql_insert_values 	= substr($sql_insert_values,0,strlen($sql_insert_values)-1);
$sql_update 		= substr($sql_update,0,strlen($sql_update)-1);
$while_row		= implode(",",$while_row_array);
// rest
$sql_insert			= $sql_insert . $sql_insert_fields . ") values (" . $sql_insert_values . ")";
$sql_select			= ereg_replace ("\*",$sql_insert_fields,$sql_select);



$while_data="<tr bgcolor='#FFFFFF'>$while_data<td nowrap><a href='\$_SERVER[PHP_SELF]?act=modify&$sn_col=\$$sn_col'>修改</a> | <a href='\$_SERVER[PHP_SELF]?act=del&$sn_col=\$$sn_col'>刪除</a></td></tr>";


$while_data_text="<tr bgcolor='#E6E9F9'>$while_data_text<td>功能</td></tr>";
$whileForm="
	\$tool_bar
	<table width='96%' cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>
	$while_data_text
	\$data
	</table>";

//製作缺少的函數
$need_function=make_fun($field_name,$isfun,$col_default);

$content="<?php
// \$Id\$

include \"config.php\";
sfs_check();

//主選單設定
\$school_menu_p=(empty(\$school_menu_p))?array():\$school_menu_p;

//預設值設定
\$col_default=array($default_array);


\$act=\$_REQUEST[act];

//執行動作判斷
if(\$act==\"insert\"){
	".$table."_add(\$_POST[data]);
	header(\"location: \$_SERVER[PHP_SELF]?act=listAll\");
}elseif(\$act==\"update\"){
	".$table."_update(\$_POST[data],\$_POST[$sn_col]);
	header(\"location: \$_SERVER[PHP_SELF]?act=listAll\");
}elseif(\$act==\"del\"){
	".$table."_del(\$_GET[$sn_col]);
	header(\"location: \$_SERVER[PHP_SELF]?act=listAll\");
}elseif(\$act==\"listAll\"){
	\$main=&".$table."_listAll();
}elseif(\$act==\"modify\"){
	\$main=&".$table."_mainForm(\$_GET[$sn_col],\"modify\");
}else{
	\$main=&".$table."_mainForm(\$_POST[$sn_col]);
}


//秀出網頁
head(\"{showname}\");
echo \$main;
foot();

//主要輸入畫面
function &".$table."_mainForm(\$$sn_col=\"\",\$mode){
	global \$school_menu_p,\$col_default;
	
	if(\$mode==\"modify\" and !empty(\$$sn_col)){
		\$dbData=get_".$table."_data(\$$sn_col);
	}
	
	if(is_array(\$dbData) and sizeof(\$dbData)>0){
		foreach(\$dbData as \$a=>\$b){
			\$DBV[\$a]=(!is_null(\$b))?\$b:\$col_default[\$a];
		}
	}else{
		\$DBV=\$col_default;
	}
	
	\$submit=(\$mode==\"modify\")?\"update\":\"insert\";
	
	//相關功能表
	\$tool_bar=&make_menu(\$school_menu_p);
	
	\$main=\"
	\$tool_bar
	
	<table cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>
	<form action='\$_SERVER[PHP_SELF]' method='post'>
	$form_main
	</table>
	<input type='hidden' name='$sn_col' value='\$$sn_col'>
	<input type='hidden' name='act' value='\$submit'>
	<input type='submit' value='送出'>
	</form>

	<a href='\$_SERVER[PHP_SELF]?act=listAll'>列出全部</a>
	\";
	return \$main;
}

//新增
function ".$table."_add(\$data){
	global \$CONN;
	".multiple_var($is_multiple)."
	\$sql_insert = \"$sql_insert\";
	\$CONN->Execute(\$sql_insert) or user_error(\"新增失敗！<br>\$sql_insert\",256);
	\$$sn_col=mysql_insert_id();
	return \$$sn_col;
}

//更新
function ".$table."_update(\$data,\$$sn_col){
	global \$CONN;
	".multiple_var($is_multiple)."
	\$sql_update = \"$sql_update  where $sn_col='\$$sn_col'\";
	\$CONN->Execute(\$sql_update) or user_error(\"更新失敗！<br>\$sql_update\",256);
	return \$$sn_col;
}

//刪除
function ".$table."_del(\$$sn_col=\"\"){
	global \$CONN;
	\$sql_delete = \"delete from $table where $sn_col='\$$sn_col'\";
	\$CONN->Execute(\$sql_delete) or user_error(\"刪除失敗！<br>\$sql_delete\",256);
	return true;
}

//列出所有
function &".$table."_listAll(){
	global \$CONN,\$school_menu_p;
	//相關功能表
	\$tool_bar=&make_menu(\$school_menu_p);
	\$sql_select=\"$sql_select\";
	\$recordSet=\$CONN->Execute(\$sql_select) or user_error(\"讀取失敗！<br>\$sql_select\",256);
	while (list($while_row)=\$recordSet->FetchRow()) {
		\$data.=\"$while_data\";
	}
	\$main=\"$whileForm\";
	return \$main;
}

//取得某一筆資料
function get_".$table."_data(\$$sn_col){
	global \$CONN;
	\$sql_select=\"$sql_select where $sn_col='\$$sn_col'\";
	\$recordSet=\$CONN->Execute(\$sql_select) or user_error(\"讀取失敗！<br>\$sql_select\",256);
	\$theData=\$recordSet->FetchRow();
	return \$theData;
}

$need_function
?>";
//exit;

//開個檔案寫入資料
@unlink($UPLOAD_PATH.$FormData[table_name]."_".$FormData[index_page]);
$fp = fopen ($UPLOAD_PATH.$table."_".$module_file_name, "aw") or user_error("檔案開啟錯誤，請檢查！",256);
fputs($fp, $content); 
fclose($fp); 

header("location: index2.php?table=$table&index_page=$module_file_name");


//製造函數
function make_fun($field_name=array(),$isfun=array(),$col_default=array()){
	foreach($field_name as $x=>$name){ 
		$f=explode("(",$col_default[$x]);
		$funname=trim($f[0]);
		if($isfun[$x]=='1' and !function_exists($funname)){
			$fun.=fun_model($col_default[$x]);
		}
	}
	return $fun;
}

//函數模型
function fun_model($funname=""){
	$fun_main="
//".$funname." 函數說明
function ".$funname."{
	global \$CONN;
	\$main=\"\";
	return \$main;
}
";
	return $fun_main;
}

//製造表單
function make_col($input_type,$field_name,$Cname,$size,$maxlen,$isfun,$use_default,$col_default,$is_multiple){
	if(empty($Cname))$Cname=$field_name;
	switch ($input_type) {
	
	//複選核取欄位
	case "checkbox":
	//如果複選，加入name 加入 []
	$array_mark=($is_multiple)?"[]":"";
	
	$op=explode(";",$col_default);
	foreach($op as $v){
		$checked=(in_array($v,$use_default))?"checked":"";
		$option.="<input type='checkbox' name='data[$field_name]".$array_mark."' value='$v' $checked>$v";
	}
	$col="
	<tr bgcolor='#FFFFFF'>
	<td>$Cname</td>
	<td>$option</td>
	</tr>
	";
	break;

	//單選核取欄
	case "radio":
	$op=explode(";",$col_default);
	foreach($op as $v){
		$checked=($v==$use_default)?"checked":"";
		$option.="<input type='radio' name='data[$field_name]' value='$v' $checked>$v";
	}
	$col="
	<tr bgcolor='#FFFFFF'>
	<td>$Cname</td>
	<td>$option</td>
	</tr>
	";
	break;
	
	//隱藏欄位
	case "hidden":
	$v=($isfun[$field_name]=="1")?"\".".stripslashes($col_default).".\"":"\$DBV[$field_name]";
	$col="
	<input type='hidden' name='data[$field_name]' value='$v'>	
	";
	break;
	
	//文字輸入表單
	case "text":
	$v=($isfun[$field_name]=="1")?"\".".stripslashes($col_default).".\"":"\$DBV[$field_name]";
	
	$col="
	<tr bgcolor='#FFFFFF'>
	<td>$Cname</td>
	<td><input type='text' name='data[$field_name]' value='$v' size='$size' maxlength='$maxlen'></td>
	</tr>
	";
	break;
	
	//密碼輸入表單
	case "password":

	$col="
	<tr bgcolor='#FFFFFF'>
	<td>$Cname</td>
	<td><input type='password' name='data[$field_name]' value='\$DBV[$field_name]' size='$size' maxlength='$maxlen'></td>
	</tr>
	";
	break;
	
	//文字區塊
	case "textarea":
	$col="
	<tr bgcolor='#FFFFFF'>
	<td>$Cname</td>
	<td><textarea name='data[$field_name]' cols='$size' rows='$maxlen'>\$DBV[$field_name]</textarea>
	</td>
	</tr>
	";
	break;
	
	//下拉選單
	case "select":
	//如果複選，加入name 加入 []
	$array_mark=($is_multiple)?"[]":"";
	$multiple=($is_multiple)?"multiple":"";
	
	$op=explode(";",$col_default);
	foreach($op as $v){
		$selected=(in_array($v,$use_default))?"selected":"";
		$option.="<option value='$v' $selected>$v\n";
	}
	
	$vv=($isfun[$field_name]=="1")?"\".".stripslashes($col_default).".\"":$option;
	
	$col="
	<tr bgcolor='#FFFFFF'>
	<td>$Cname</td>
	<td>
	<select name='data[$field_name]$array_mark' $multiple>
	$vv
	</select>
	</td>
	</tr>
	";
	break;
	
	//檔案輸入表單
	case "file":

	$col="
	<tr bgcolor='#FFFFFF'>
	<td>$Cname</td>
	<td><input type='file' name='data[$field_name]' value='\$DBV[$field_name]' size='$size'></td>
	</tr>
	";
	break;
	
	//單純顯示
	case "display":
	$v=($isfun[$field_name]=="1")?"\".".stripslashes($col_default).".\"":"\$DBV[$field_name]";
	$col="
	<tr bgcolor='#FFFFFF'>
	<td>$Cname</td>
	<td>$v<input type='hidden' name='data[$field_name]' value='$v'>	</td>
	</tr>
	";
	break;
	}
return $col;
}

//複選機制，新增或更新時，將複選欄位值用「,」兜起來
function multiple_var($is_multiple=array()){
	foreach($is_multiple as $field_name=>$v){
		if($v=='1'){
			$main.="\$vvv=implode(\",\",\$data[$field_name]);
	\$data[$field_name]=\$vvv;";
		}
	}
	
	return $main;
}

//先將欄位資料存入資料庫
function module_maker_col_add($table,$ename,$cname,$col_default){
	global $CONN;
	$sql_insert = "replace into module_maker_col (table_name,ename,cname,default_txt) values ('$table','$ename','$cname','$col_default')";
	$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
	$mmscs=mysql_insert_id();
	return $mmscs;
}

?>
