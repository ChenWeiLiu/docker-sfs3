<?php 

// $Id: stud_bs_self.php 7094 2013-01-28 07:28:15Z hsiao $

// 載入設定檔
include "config.php";

// 認證檢查
sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}


head("學生資料自建");

//欄位資訊
$field_data = get_field_info("stud_brother_sister");

//模組選單
print_menu($menu_p);

//只限當學期
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();

//取得登入學生的學號和流水號
$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='".$_SESSION['session_log_id']."'";
$res=$CONN->Execute($query);
$student_sn=$res->fields['student_sn'];
if ($student_sn) {
	$query="select * from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query);
	if ($res->fields['stud_study_cond']!="0") {
		$student_sn="";
	} else {
		$stud_name=$res->fields['stud_name'];
	}
}

//刪除紀錄
if ($_POST['del_id']) {
	$query = "delete from stud_brother_sister where stud_id='".$_SESSION['session_log_id']."' and bs_id='".$_POST['del_id']."'";
	if ($CONN->Execute($query)) {
		//記錄 log
		sfs_log("stud_brother_sister","delete",$_SESSION['session_log_id']);
	}
}

//按鍵處理 
$ckey=$_POST['ckey'];
switch ($_POST['do_key']){	
	case $postBtn: //新增
		$sql_insert = "insert into stud_brother_sister (stud_id,bs_name,bs_calling,bs_gradu,bs_birthyear,student_sn) values ('".$_SESSION['session_log_id']."','".$_POST['bs_name']."','".$_POST['bs_calling']."','".$_POST['bs_gradu']."','".$_POST['bs_birthyear']."','$student_sn')";
		$CONN->Execute($sql_insert) or die ($sql_insert);
		//記錄 log
		sfs_log("stud_brother_sister","insert",$_SESSION['session_log_id']);
	break;
	case $editBtn: //修改
		$query = "select bs_id from stud_brother_sister where student_sn='$student_sn' order by bs_id";
		$result = $CONN->Execute($query);
		while(!$result->EOF) {		
			$bs_id = "bs_id_".$result->fields[0];			
			$bs_name = "bs_name_".$result->fields[0];
			$bs_calling = "bs_calling_".$result->fields[0];
			$bs_gradu = "bs_gradu_".$result->fields[0];
			$bs_birthyear = "bs_birthyear_".$result->fields[0];
			$sql_update = "update stud_brother_sister set bs_name='".$_POST[$bs_name]."',bs_calling='".$_POST[$bs_calling]."',bs_gradu='".$_POST[$bs_gradu]."',bs_birthyear='".$_POST[$bs_birthyear]."' where bs_id=".$result->fields[0];
			$CONN->Execute ($sql_update) or die ($sql_update);
			$result->MoveNext();
		}
		//記錄 log
		sfs_log("stud_brother_sister","update",$_SESSION['session_log_id']);
	break;	
}

?>
<script language="JavaScript">
<!--
function delRecord(a,b)
{
	if (confirm('確定刪除 '+a+' 記錄?')){
		document.myform.del_id.value=b;
		document.myform.submit();
	}
}
//-->
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0" CLASS="tableBg" >
<tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
<form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post"> 
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
<td class=title_mbody colspan=5 align=center  background="images/tablebg.gif" >
	<?php echo $stud_name."  (學號：".$_SESSION['session_log_id'].")";?>
	</td>	
</tr>
<tr><td colspan=5 align=center>
<?php
	echo ($ckey == $editModeBtn)? "<input type=submit name=\"ckey\" value=\"$browseModeBtn\">": "<input type=submit name=\"ckey\" value=\"$editModeBtn\">";
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"ckey\" value=\"$newBtn\">";
	if ($ckey==$editModeBtn) 
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"do_key\" value=\"$editBtn\">";
?>	
</td></tr>
<tr><td><?php echo $field_data[bs_name][d_field_cname] ?></td>
	<td><?php echo $field_data[bs_calling][d_field_cname] ?></td>
	<td><?php echo $field_data[bs_gradu][d_field_cname] ?></td>
	<td><?php echo $field_data[bs_birthyear][d_field_cname] ?></td>
	<td>動作</td>
</tr>
<?php
	//新增模式
	if ($ckey==$newBtn || $key == $postBtn){
			echo "<tr>";
			echo "<td> <input name=bs_name size=10 ></td><td>";
			//稱謂
			$sel1 = new drop_select(); //選單類別 		
			$sel1->s_name = "bs_calling"; //選單名稱
			$sel1->id = 0;
			$sel1->arr = bs_calling_kind(); //內容陣列			
			$sel1->do_select();
			echo "</td>";
			echo "<td><input name=bs_gradu size=10 ></td>";
			echo "<td><input name=bs_birthyear size=4 ></td>";
			echo "<td><input type=submit name=\"do_key\" value=\"$postBtn\"></td>";
			echo "</tr>";
	}
?>
<?php	 

	$sql_select = "select bs_id,stud_id,bs_name,bs_calling,bs_gradu,bs_birthyear from stud_brother_sister where student_sn='$student_sn'";
	$recordSet = $CONN->Execute($sql_select) or die($sql_select);
	while (!$recordSet->EOF) {

		$bs_id = $recordSet->fields["bs_id"];
		$bs_name = $recordSet->fields["bs_name"];
		$bs_calling = $recordSet->fields["bs_calling"];
		$bs_gradu = $recordSet->fields["bs_gradu"];
		$bs_birthyear = $recordSet->fields["bs_birthyear"];
		$bs_calling_kind_p = bs_calling_kind(); //稱謂	
			
		$ti = ($i%2)+1;	
		if ($bs_id) {
			echo "<tr class=nom_$ti >";
			if ($ckey==$editModeBtn && $bs_id) { //修改模式
				echo "<td> <input name=bs_name_$bs_id size=10 value=\"".$bs_name."\"></td><td>";
				//稱謂
				$sel1 = new drop_select(); //選單類別
				$sel1->s_name = "bs_calling_$bs_id"; //選單名稱
				$sel1->id = intval($bs_calling);
				$sel1->arr = bs_calling_kind(); //內容陣列			
				$sel1->do_select();				
				echo "</td>";
				echo "<td><input name=bs_gradu_$bs_id size=10 value=\"".$bs_gradu."\"></td>";
				echo "<td><input name=bs_birthyear_$bs_id size=4 value=\"".$bs_birthyear."\"></td>";
			}
			else {
				echo "<td>".$bs_name."</td><td>";
				echo $bs_calling_kind_p[$bs_calling];			
				echo "</td><td>".
				$bs_gradu."</td><td>".
				$bs_birthyear."</td>";
			}
			echo "<td> <input type=\"button\" value=\"刪除\" onClick=\"delRecord('$bs_name',$bs_id);\"> </td>";
			echo "</tr>";
		}		
		$recordSet->MoveNext();
	}
?>

</table>
    　</td>
<input type="hidden" name="del_id">
</form>
  </tr>
</table>

<?php
foot();
?>
