<?php

// $Id: teach_connect.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "teach_config.php";

// 不需要 register_globals
/*
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
*/
if($_GET['teacher_sn']) $teacher_sn=$_GET['teacher_sn'];
if($_POST['teacher_sn']) $teacher_sn=$_POST['teacher_sn'];
$sel=$_GET['sel'];
$email=$_POST['email'];
$email2=$_POST['email2'];
$email3=$_POST['email3'];
$selfweb=$_POST['selfweb'];
$selfweb2=$_POST['selfweb2'];
$classweb=$_POST['classweb'];
$classweb2=$_POST['classweb2'];
$icq=$_POST['icq'];
$update_id=$_POST['update_id'];
$nav_next=$_POST['nav_next'];
$do_key=$_POST['do_key'];
$chknext=$_POST['chknext'];

//更改在職狀態
if ($c_sel != "")
	$sel = $c_sel;
else if ($sel=="")
	$sel = 0 ; //預設選取在職狀況

// 認證檢查
sfs_check();

switch ($do_key) {
	case $editBtn :

    	// 先檢查該教師的 teacher_sn 是否存在? 否則無法更新教師的網路資料
    	$query="select teacher_sn from teacher_connect where teacher_sn='$teacher_sn'";
    	$rs=&$CONN->Execute($query);
    
	if (!$rs) {
	  	print $CONN->ErrorMsg();
	} else {
      		if ($rs->fields[teacher_sn]) {
			$query = "update teacher_connect set email='$email', email2='$email2', email3='$email3', selfweb='$selfweb', selfweb2='$selfweb2', classweb='$classweb', classweb2='$classweb2', ICQ='$icq' where teacher_sn='$teacher_sn'";
      		} else {
			$query="insert into teacher_connect values('$teacher_sn','$email','$email2','$email3','$selfweb','$selfweb2','$classweb','$classweb2','$icq')";
      		}
      	
		$CONN->Execute($query) or die($query);
    	}

	break;
	
}


//印出檔頭
head("教師網路資料");
//欄位資訊
$field_data = get_field_info("teacher_connect");

//選單連結字串
$linkstr = "teacher_sn=$teacher_sn&sel=$sel";
//印出選單
print_menu($teach_menu_p,$linkstr);
//儲存後到下一筆
if ($chknext)
	$teacher_sn = $nav_next;	

$query = "select teacher_sn from teacher_base where teacher_sn='$teacher_sn' and teach_condition ='$sel'";
$res = $CONN->Execute($query) or die($query);

//未設定或改變在職狀況或刪除記錄後 到第一筆
if ($teacher_sn =="" || $teacher_sn != $res->fields[teacher_sn]) {
	$result= $CONN->Execute("select teacher_base.teacher_sn,teacher_base.teach_condition from teacher_base left join teacher_post on teacher_base.teacher_sn=teacher_post.teacher_sn where  teacher_base.teach_condition ='$sel' limit 0,1");
	$teacher_sn = $result->fields[0];	
}	
$sql_select = "select a.name,b.* from teacher_base a left join teacher_connect b on a.teacher_sn=b.teacher_sn where a.teacher_sn='$teacher_sn'";
$recordSet = $CONN->Execute($sql_select) or die ($sql_select);
while (!$recordSet->EOF) {

  	// 以下這一要去掉，否則一開始 $teacher_sn 會被空值取代 
  	//$teacher_sn = $recordSet->fields["teacher_sn"];
	
	$name = $recordSet->fields["name"];
	$email = $recordSet->fields["email"];
	$email2 = $recordSet->fields["email2"];
	$email3 = $recordSet->fields["email3"];
	$selfweb = $recordSet->fields["selfweb"];
	$selfweb2 = $recordSet->fields["selfweb2"];
	$classweb = $recordSet->fields["classweb"];
	$classweb2 = $recordSet->fields["classweb2"];
	$icq = $recordSet->fields["ICQ"];

	$recordSet->MoveNext();
};


?>
<script language="JavaScript">
function checkok()
{
	document.myform.nav_next.value = document.gridform.nav_next.value;
	return true;
}
//-->
</script>

<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#cccccc>
<tr><td valign=top>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td align="right" valign="top" bgcolor="#CCCCCC">
    <?php    
	//建立左邊選單 
	$remove_p = remove(); //在職狀況    
	$upstr = "顯示<select name=\"c_sel\" onchange=\"this.form.submit()\">\n"; 
      	while (list($tid,$tname)=each($remove_p)){
      		if ($sel== $tid)
      			$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
      		else
      			$upstr .= "<option value=\"$tid\">$tname</option>\n";
      	}
	$upstr .= "</select>"; 
	$downstr = "<hr size=1>"; 

	$grid1 = new sfs_grid_menu;  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "teacher_sn";  // 索引欄名  	
	$grid1->display_item = array("teacher_sn","name");  // 顯示欄名 
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示		
	$grid1->sql_str = "select teacher_sn,name,sex from teacher_base where teach_condition='$sel' order by teacher_sn";   //SQL 命令 
	
	$grid1->do_query(); //執行命令 
	if ($key == $newBtn || $key == $postBtn) 
		$grid1->disabled=1; 
	$grid1->print_grid($teacher_sn,$upstr,$downstr); // 顯示畫面 

?>  
</td></tr></table>  
</td>
<td width="100%" valign="top">
<form name="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body" >


<tr>
	<td colspan=2>
	<B><?php echo "$teacher_sn -- $name" ?></b></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[email][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="50" maxlength="50" name="email" value="<?php echo $email ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[email2][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="50" maxlength="50" name="email2" value="<?php echo $email2 ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[email3][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="50" maxlength="50" name="email3" value="<?php echo $email3 ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[selfweb][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="50" maxlength="50" name="selfweb" value="<?php echo $selfweb ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[selfweb2][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="50" maxlength="50" name="selfweb2" value="<?php echo $selfweb2 ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[classweb][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="50" maxlength="50" name="classweb" value="<?php echo $classweb ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[classweb2][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="50" maxlength="50" name="classweb2" value="<?php echo $classweb2 ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1" nowrap><?php echo $field_data[ICQ][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="icq" value="<?php echo $icq ?>"></td>
</tr>

<tr>
	
	<td colspan="4" align=center>
	<input type="hidden" name="update_id" value="<?php echo $_SESSION['session_log_id'] ?>">
	<input type="hidden" name="teacher_sn" value="<?php echo $teacher_sn ?>">
	<?php 
		if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    	
    	?>
    	 自動跳下一位 &nbsp;&nbsp<input type=hidden name=nav_next >
	<input type=submit name="do_key" value ="<?php echo $editBtn ?>" onClick="return checkok();">
	</td>
</tr>

</table>
</FORM>
</TD>
</TR>
</TABLE>
<?php 
//印出尾頭
foot();
?> 
