<?php 

// $Id: stud_kinfolk.php 6481 2011-08-17 12:47:59Z infodaes $

// 載入設定檔
include "config.php";
// 認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//按鍵處理 
switch ($do_key){	
	case $postBtn: //新增
		$query="select stud_id from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$stud_id=$res->fields[stud_id];
		$sql_insert = "insert into stud_kinfolk (stud_id,kin_name,kin_calling,kin_phone,kin_hand_phone,kin_email,student_sn) values ('$stud_id','$kin_name','$kin_calling','$kin_phone','$kin_hand_phone','$kin_email','$student_sn')";
		$CONN->Execute($sql_insert) or die ($sql_insert);
		//記錄 log
		sfs_log("stud_kinfolk","insert");
	break;
	case "delete": //刪除
		$CONN->Execute("delete from stud_kinfolk where kin_id='$kin_id'");
		//記錄 log
		sfs_log("stud_kinfolk","delete");
	break;
	case $editBtn: //修改
		$query = " select kin_id from stud_kinfolk where student_sn='$student_sn' order by kin_id";
		$result = $CONN->Execute($query);
		while(!$result->EOF) {	
			$temp_id = $result->fields[0];	
			$kin_id = "kin_id_$temp_id";			
			$kin_name = "kin_name_$temp_id";
			$kin_calling = "kin_calling_$temp_id";
			$kin_phone = "kin_phone_$temp_id";
			$kin_hand_phone = "kin_hand_phone_$temp_id";
			$kin_email = "kin_email_$temp_id";			
			$sql_update = "update stud_kinfolk set kin_name='".$$kin_name."',kin_calling='".$$kin_calling."',kin_phone='".$$kin_phone."',kin_hand_phone='".$$kin_hand_phone."',kin_email='".$$kin_email."' where kin_id=$temp_id";
			$CONN->Execute ($sql_update) or die ($sql_update);
			$result->MoveNext();
		}
		//記錄 log
		sfs_log("stud_kinfolk","update","$stud_id");
		
	$ckey = $editModeBtn ;//設為修改模式
	break;	
}

head();
//欄位資訊
$field_data = get_field_info("stud_kinfolk");
//選單連結字串
$linkstr = "student_sn=$student_sn&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";


//模組選單
print_menu($menu_p,$linkstr);

//更改班級
if ($c_curr_class=="")
	// 利用 $IS_JHORES 來 區隔 國中、國小、高中 的預設值
	$c_curr_class = sprintf("%03s_%s_%02s_%02s",curr_year(),curr_seme(),$default_begin_class + round($IS_JHORES/2),1);
else {
	$temp_curr_class_arr = explode("_",$c_curr_class); //091_1_02_03
	$c_curr_class = sprintf("%03s_%s_%02s_%02s",substr($c_curr_seme,0,3),substr($c_curr_seme,-1),$temp_curr_class_arr[2],$temp_curr_class_arr[3]);
}
	
if($c_curr_seme =='')
	$c_curr_seme = sprintf ("%03s%s",curr_year(),curr_seme()); //現在學年學期

//更改學期
if ($c_curr_seme != "")
	$curr_seme = $c_curr_seme;

$c_curr_class_arr = explode("_",$c_curr_class);
$seme_class = intval($c_curr_class_arr[2]).$c_curr_class_arr[3];

	//儲存後到下一筆
if ($chknext)
	$student_sn = $nav_next;	
	$query = "select a.student_sn,a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.student_sn='$student_sn' and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'";
	$res = $CONN->Execute($query) or die($res->ErrorMsg());
	//未設定或改變在職狀況或刪除記錄後 到第一筆
	if ($student_sn =="" || $res->RecordCount()==0) {	
		$temp_sql = "select a.student_sn,a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";
		$res = $CONN->Execute($temp_sql) or die($temp_sql);
		$student_sn = $res->fields[0];
	}
	$stud_id = $res->fields[1];
	$stud_name = $res->fields[2];
?> 
<script language="JavaScript">

function checkok()
{
	var OK=true;	
	document.myform.nav_next.value = document.gridform.nav_next.value;	
	return OK
}

function setfocus(element) {
	element.focus();
 return;
}
//-->
</script>


<table border="0" width="100%" cellspacing="0" cellpadding="0" CLASS="tableBg" >
<tr>
<td valign=top align="right">
<?php
//建立左邊選單   
//顯示學期
$class_seme_p = get_class_seme(); //學年度	
$upstr = "<select name=\"c_curr_seme\" onchange=\"this.form.submit()\">\n";
while (list($tid,$tname)=each($class_seme_p)){
	if ($curr_seme== $tid)
      		$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
      	else
      		$upstr .= "<option value=\"$tid\">$tname</option>\n";
}
$upstr .= "</select><br>"; 
	
$s_y = substr($c_curr_seme,0,3);
$s_s = substr($c_curr_seme,-1);

//顯示班級
	$tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class);
	$upstr .= $tmp;

	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "student_sn";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL 命令
	//echo $grid1->sql_str;	
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($student_sn,$upstr,$downstr); // 顯示畫面   
?> 
     </td>
    <td width="100%" valign=top bgcolor="#CCCCCC">
<form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post"  <?php
	//當mnu筆數為0時 讓 form 為 disabled
	if ($grid1->count_row==0 && !($do_key == $newBtn || $do_key == $postBtn))  
		echo " disabled "; 
	?> > 
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
	
	<td class=title_mbody colspan=6 align=center>
	<?php 
		echo sprintf("%d學年第%d學期 %s--%s (%s)",substr($c_curr_seme,0,-1),substr($c_curr_seme,-1),$class_list_p[$c_curr_seme],$stud_name,$stud_id);
	    	if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "自動跳下一位";
    
    ?>
	</td>	
</tr>
<tr><td colspan=6 align=center>
<?php
	echo ($ckey == "$editModeBtn" )? "<input type=submit name=\"ckey\" value=\"$browseModeBtn\">": "<input type=submit name=\"ckey\" value=\"$editModeBtn\">";
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"ckey\" value=\"$newBtn\">";
	if ($ckey==$editModeBtn && $kin_id) 
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"do_key\" value=\"$editBtn\" onClick=\"return checkok();\" >";
?>	
</td></tr>
<tr><td><?php echo $field_data[kin_name][d_field_cname] ?></td>
	<td><?php echo $field_data[kin_calling][d_field_cname] ?></td>
	<td><?php echo $field_data[kin_phone][d_field_cname] ?></td>
	<td><?php echo $field_data[kin_hand_phone][d_field_cname] ?></td>
	<td><?php echo $field_data[kin_email][d_field_cname] ?></td>
	<td>動作</td>
</tr>
<?php
	//與監護人關係
	$sel1 = new drop_select(); //選單				
	$sel1->arr = guardian_relation(); //內容陣列	
	//新增模式
	if ($ckey==$newBtn||$do_key == $postBtn){
			echo "<tr>";
			echo "<td><input name=kin_name size=12 maxlength=20></td>";
			echo "<td>";
				$sel1->s_name = "kin_calling"; //選單名稱				
				$sel1->id = $kin_calling;
				$sel1->do_select();
			echo "</td>";
			
			echo "<td><input name=kin_phone size=12 maxlength=20></td>";
			echo "<td><input name=kin_hand_phone size=12 maxlength=20></td>";
			echo "<td><input name=kin_email size=20 maxlength=30></td>";
			echo "<td><input type=submit name=do_key value=\"$postBtn\"></td>";
			echo "</tr>";
	}
?>
<?php	 
	
	$sql_select = "select * from stud_kinfolk where student_sn='$student_sn'";
	$recordSet = $CONN->Execute($sql_select) or die($sql_select);
	
	while (!$recordSet->EOF) {

		$kin_id = $recordSet->fields["kin_id"];
		$student_sn = $recordSet->fields["student_sn"];
		$kin_name = $recordSet->fields["kin_name"];
		$kin_calling = $recordSet->fields["kin_calling"];
		$kin_phone = $recordSet->fields["kin_phone"];
		$kin_hand_phone = $recordSet->fields["kin_hand_phone"];
		$kin_email = $recordSet->fields["kin_email"];

		
		$ti = ($i%2)+1;	
		if ($kin_id) {
				

			echo "<tr class=nom_$ti >";
			if ($ckey==$editModeBtn  && $kin_id) { //修改模式
				
				echo "<td><input name=kin_name_$kin_id size=12 maxlength=20 value=\"".$kin_name."\"></td>";
				echo "<td>";
				$sel1->s_name = "kin_calling_$kin_id"; //選單名稱				
				$sel1->id = $kin_calling;
				$sel1->do_select();
				echo "</td>";
				echo "<td><input name=kin_phone_$kin_id size=12 maxlength=20 value=\"".$kin_phone."\"></td>";
				echo "<td><input name=kin_hand_phone_$kin_id size=12 maxlength=20 value=\"".$kin_hand_phone."\"></td>";
				echo "<td><input name=kin_email_$kin_id size=20 maxlength=30 value=\"".$kin_email."\"</td>";
				echo "<td> <a href=\"{$_SERVER['SCRIPT_NAME']}?do_key=delete&kin_id=$kin_id&ckey=$ckey&$linkstr\" onClick=\"return confirm('確定刪除 ".$kin_name." 記錄?');\">刪除</a></td>";
			}
			else {
				echo "<td>".$kin_name."</td>";
				echo "<td>";
				echo $sel1->arr[$kin_calling];
				echo "</td>";
				echo "<td>".$kin_phone."</td>";
				echo "<td>".$kin_hand_phone."</td>";
				echo "<td>".$kin_email."</td>";
				echo "<td> <a href=\"{$_SERVER['SCRIPT_NAME']}?do_key=delete&kin_id=$kin_id&ckey=$ckey&$linkstr\" onClick=\"return confirm('確定刪除 ".$tea1->Record[kin_name]." 記錄?');\">刪除</a></td>";				
			}
			echo "</tr>";
		}		
		$recordSet->MoveNext();
	}
	if ($ckey == $editModeBtn && $kin_id) {		
		echo "<tr><td colspan=6 align=center>";
		if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "自動跳下一位 &nbsp;&nbsp;";
		echo "<input type=\"submit\" name=\"do_key\" value=\"$editBtn\" onClick=\"return checkok();\">";
		echo "</td></tr>";		
	}
?>
</table>
    　</td>
  </tr>
</table>
<input type="hidden" name="student_sn" value="<?php echo $student_sn ?>">
<input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme ?>">
<input type="hidden" name="c_curr_class" value="<?php echo $c_curr_class ?>">
<input type=hidden name=nav_next >

</form>
<?php
foot();
?>
