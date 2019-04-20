<?php
// $Id: grouping.php 5310 2009-01-10 07:57:56Z hami $

include_once "config.php";
sfs_check();
$group_selected=$_POST['group_selected'];
$new_description=$_POST['new_description'];
$cols_count=5;

if($_POST['go']=='儲存修改'){
	if($group_selected) {
		$kind_array=$_POST['kind'];
		foreach($kind_array as $value) $kind_list.="$value,";
		$kind_list=','.$kind_list;
		$update_sql="UPDATE stud_kind_group SET description='$new_description',kind_list='$kind_list' WHERE sn=$group_selected";
		$recordSet=$CONN->Execute($update_sql) or user_error("讀取失敗！<br>$update_sql",256);
	}
}

if($_POST['go']=='複製到新群組'){
	if($group_selected) {
		$kind_array=$_POST['kind'];
		foreach($kind_array as $value) $kind_list.="$value,";
		$kind_list=','.$kind_list;
		$insert_sql="INSERT INTO stud_kind_group SET description='$new_description',kind_list='$kind_list'";
		$recordSet=$CONN->Execute($insert_sql) or user_error("讀取失敗！<br>$insert_sql",256);
	}
}

if($_POST['go']=='刪除'){
		$del_sql="DELETE FROM stud_kind_group WHERE sn=$group_selected";
		$recordSet=$CONN->Execute($del_sql) or user_error("讀取失敗！<br>$del_sql",256);
}

//秀出網頁
head("群組類別設定");

//橫向選單標籤
echo print_menu($MENU_P,$linkstr);

if(checkid($_SERVER['SCRIPT_FILENAME'],1)) {
//取得群組名單
$group_sql="SELECT * FROM stud_kind_group ORDER BY description";
$recordSet=$CONN->Execute($group_sql) or user_error("讀取失敗！<br>$group_sql",256);
$group_select="<select name='group_selected' onchange='this.form.submit();'><option value='-'>---------選擇群組---------</option>";

while(!$recordSet->EOF){
	$sn=$recordSet->fields['sn'];
	$description=$recordSet->fields['description'];
	$selected='';
	if($group_selected==$sn) {
		$selected='selected';
		$kind_list=$recordSet->fields['kind_list'];
		$description_selected=$description;
	}
	$group_select.="<option $selected value=$sn>$description</option>";
	$recordSet->MoveNext();
}
$group_select.="</select>";

//取得學生身份列表
$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);

while(!$recordSet->EOF){
		$curr_row=($recordSet->currentrow()+1) % $cols_count;
        $d_id=$recordSet->fields['d_id'];

		$t_name=$recordSet->fields['t_name'];
		if(strpos($kind_list,",$d_id,")>-1) {
			$checked='checked';
			$col_bgcolor='#CCCCCC';
		} else {
			$checked='';
			$col_bgcolor='#FFCCCC';
		}
//echo "<BR>$d_id --- $checked";	
		$kind_checkbox_list.=($curr_row==1)?"<tr>":"";
		$kind_checkbox_list.="<td bgcolor='$col_bgcolor'><input type='checkbox' name='kind[]' value=$d_id $checked>($d_id)$t_name</td>";
		
		$recordSet->MoveNext();
		//判斷是否增加列結尾標籤
		if($curr_row==$cols_count or $recordSet->EOF) $table_body.="</tr>";
}

$listdata="<table width='100%' cellspacing='1' cellpadding='3'>
             <form name='my_form' method='post' action='$_SERVER[PHP_SELF]'>
			 <tr bgcolor=#CFCFAA><td colspan=$cols_count><input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'> <img border='0' src='images/pin.gif'>身份別群組列表：$group_select  
			 <input type='submit' name='go' value='儲存修改' onClick=\"\$new_group=prompt('請輸入修改後的群組名稱?','$description_selected'); if(\$new_group) { document.my_form.new_description.value=\$new_group;} else return false;\">
			 <input type='reset' name='reset' value='回復原設定'>
			 <input type='hidden' name='new_description' value=''>
			 <input type='submit' name='go' value='複製到新群組' onClick=\"\$new_group=prompt('請輸入新群組名稱?',''); if(\$new_group) { document.my_form.new_description.value=\$new_group; } else return false;\">
			 <input type='submit' name='go' value='刪除' onclick='return confirm(\"真的要刪除[$description_selected]?\")'>

			 </td></tr>
			 <tr><td>$kind_checkbox_list</td></tr>";

$listdata.="</form></table>";
echo $listdata;
echo "<script>
function tagall(status) {
  var i =0;
  while (i < document.my_form.elements.length)  {
    if (document.my_form.elements[i].name=='kind[]') {
      document.my_form.elements[i].checked=status;
    }
    i++;
  }
}
</script>";
} else { echo "<h2><center><BR><BR><font color=#FF0000>您並非模組管理員，無法使用本功能!</font></center></h2>"; } 
foot();
?>
