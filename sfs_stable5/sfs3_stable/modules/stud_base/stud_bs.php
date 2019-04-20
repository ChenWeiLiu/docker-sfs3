<?php 

// $Id: stud_bs.php 6481 2011-08-17 12:47:59Z infodaes $

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

if ($_GET['stud_id'] && $_GET['c_curr_seme']) {
	$query="select student_sn from stud_seme where seme_year_seme='".$_GET['c_curr_seme']."' and stud_id='".$_GET['stud_id']."'";
	$res=$CONN->Execute($query);
	if ($res->fields['student_sn']) $student_sn=$res->fields['student_sn'];
}

//按鍵處理 
switch ($do_key){	
	case $postBtn: //新增
		$query="select stud_id from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($query);
		$stud_id=$res->fields[stud_id];
		$sql_insert = "insert into stud_brother_sister (stud_id,bs_name,bs_calling,bs_gradu,bs_birthyear,student_sn) values ('$stud_id','$bs_name',$bs_calling,'$bs_gradu','$bs_birthyear','$student_sn')";
		$CONN->Execute($sql_insert) or die ($sql_insert);
		//記錄 log
		sfs_log("stud_brother_sister","insert");
	break;
	case "delete": //新增
		$query = "delete from stud_brother_sister where bs_id='$bs_id'";
		$CONN->Execute($query);
		//記錄 log
		sfs_log("stud_brother_sister","delete");

	break;
	case $editBtn: //修改
		$query = " select bs_id from stud_brother_sister where student_sn='$student_sn' order by bs_id";
		$result = $CONN->Execute($query);
		while(!$result->EOF) {		
			$bs_id = "bs_id_".$result->fields[0];			
			$bs_name = "bs_name_".$result->fields[0];
			$bs_calling = "bs_calling_".$result->fields[0];
			$bs_gradu = "bs_gradu_".$result->fields[0];
			$bs_birthyear = "bs_birthyear_".$result->fields[0];
			$sql_update = "update stud_brother_sister set bs_name='".$$bs_name."',bs_calling='".$$bs_calling."',bs_gradu='".$$bs_gradu."',bs_birthyear='".$$bs_birthyear."' where bs_id=".$result->fields[0];
			$CONN->Execute ($sql_update) or die ($sql_update);
			$result->MoveNext();
		}
		//記錄 log
		sfs_log("stud_brother_sister","update","$stud_id");

	$ckey = $editModeBtn ;//設為修改模式
	break;	
}
head();
//欄位資訊
$field_data = get_field_info("stud_brother_sister");
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
	$query = "select a.student_sn,a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.student_sn='$student_sn' and (a.stud_study_cond=0 or a.stud_study_cond=5)  and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'";
	$res = $CONN->Execute($query) or die($res->ErrorMsg());
	//未設定或改變在職狀況或刪除記錄後 到第一筆
	if ($student_sn =="" || $res->RecordCount()==0) {	
		$temp_sql = "select a.student_sn,a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn  and  (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";
		$res = $CONN->Execute($temp_sql) or die($temp_sql);
	}

		$student_sn = $res->fields[0];
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
<td class=title_mbody colspan=5 align=center >
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
<tr><td colspan=5 align=center>
<?php
	echo ($ckey == "$editModeBtn"  )? "<input type=submit name=\"ckey\" value=\"$browseModeBtn\">": "<input type=submit name=\"ckey\" value=\"$editModeBtn\">";
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"ckey\" value=\"$newBtn\">";
	if ($ckey==$editModeBtn) 
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"do_key\" value=\"$editBtn\" onClick=\"return checkok();\" >";
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
	if ($ckey==$newBtn|| $key == $postBtn){
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

	$sql_select = "select bs_id,student_sn,bs_name,bs_calling,bs_gradu,bs_birthyear from stud_brother_sister where student_sn='$student_sn'";
	$recordSet = $CONN->Execute($sql_select) or die($sql_select);
	while (!$recordSet->EOF) {

		$bs_id = $recordSet->fields["bs_id"];
		$student_sn = $recordSet->fields["student_sn"];
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
				echo "<td> <a href=\"{$_SERVER['SCRIPT_NAME']}?do_key=delete&bs_id=$bs_id&ckey=$ckey&$linkstr\" onClick=\"return confirm('確定刪除 ".$bs_name." 記錄?');\">刪除</a></td>";
			}
			else {
				echo "<td>".$bs_name."</td><td>";
				echo $bs_calling_kind_p[$bs_calling];			
				echo "</td><td>".
				$bs_gradu."</td><td>".
				$bs_birthyear."</td>";
				echo "<td> <a href=\"{$_SERVER['SCRIPT_NAME']}?do_key=delete&bs_id=$bs_id&ckey=$ckey&$linkstr\" onClick=\"return confirm('確定刪除 ".$bs_name." 記錄?');\">刪除</a></td>";
			}
			echo "</tr>";
		}		
		$recordSet->MoveNext();
	}
	if ($ckey == $editModeBtn && $bs_id) {		
		echo "<tr><td colspan=5 align=center>";
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
