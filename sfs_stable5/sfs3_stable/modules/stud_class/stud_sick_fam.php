<?php 

// $Id: stud_sick_fam.php 6055 2010-08-31 02:08:48Z brucelyc $

// 載入設定檔
include "stud_reg_config.php";
// 認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//教師任教班級
$class_num = get_teach_class();

if ($class_num=='') {
	head("權限錯誤");	
	stud_class_err();
	foot();
	exit;
}

// 建立類別
$tea1 = new stud_sick_f_class();

$fam_sick_kind_p =fam_sick_kind(); //取得病史資料		
//按鍵處理 
switch ($key){	
	case $postBtn: //新增	
		$chk_str = $tea1->chk_str;
		reset($fam_sick_kind_p);
		$temp_value ="";
		while(list($tid,$tname)=each($fam_sick_kind_p)) {
			$temp =$chk_str."_$tid";
			if ($GLOBALS[$temp])
				$temp_value .= "$tid,";
		}
		if ($temp_value) {
		 	$sql_insert = "insert into stud_sick_f (stud_id,s_calling,sick) values ('$stud_id','$s_calling','$temp_value')";
		 	mysql_query($sql_insert) or die ($sql_insert);
		 }
		 
	break;
	case "delete": //新增
		$tea1->delete("sick_id",$sick_id);
	break;
	case $editBtn: //修改		
		$query = " select sick_id from stud_sick_f where stud_id='$stud_id' order by sick_id";
		$result = mysql_query($query);
		while($row = mysql_fetch_row($result)) {
			$chk_str = "fam_$row[0]"; // 辨別值
			reset($fam_sick_kind_p);
			$temp_value ="";
			while(list($tid,$tname)=each($fam_sick_kind_p)) {
				$temp =$chk_str."_$tid";				
				if ($GLOBALS[$temp])
					$temp_value .= "$tid,";
			}
			if($temp_value) {				
				//$sick_id = "$chk_str"."sick_id_$row[0]";
				$s_calling = "s_calling_$row[0]";				
				$sql_update = "update stud_sick_f set s_calling='".$$s_calling."',sick='$temp_value' where sick_id=$row[0]";
				mysql_query ($sql_update) or die ($sql_update);
			}
			else
				mysql_query("delete from stud_sick_f  where sick_id=$row[0]");
		}
	$ckey = $editModeBtn ;//設為修改模式
	break;	
}

//----------------------------------------

//儲存後到下一筆
if ($chknext)
	$stud_id = $nav_next;	
		
$tea1->query("select stud_sick_f.* ,stud_base.stud_id,stud_base.stud_name,stud_base.curr_class_num from stud_base left join stud_sick_f on stud_sick_f.stud_id=stud_base.stud_id where stud_base.stud_id='$stud_id' and   stud_base.curr_class_num like '$class_num%'  order by stud_sick_f.sick_id "); 
//$tea1->debug();
//未設定或改變在職狀況或刪除記錄後 到第一筆
if ($stud_id =="" || $stud_id != $tea1->Record[stud_id]) {
	$result= mysql_query("select stud_base.stud_id from stud_base where  stud_base.curr_class_num like '$class_num%' order by curr_class_num limit 0,1");
	$row = mysql_fetch_row($result);	
	$tea1->query("select stud_sick_f.* ,stud_base.stud_id,stud_base.stud_name,stud_base.curr_class_num from stud_base left join stud_sick_f on stud_sick_f.stud_id=stud_base.stud_id where stud_base.stud_id='$row[0]' and  stud_base.curr_class_num like '$class_num%'  order by stud_sick_f.sick_id "); 	
}

$stud_id = $tea1->Record[stud_id];

//印出檔頭

head();

//選單連結字串
$linkstr = "stud_id=$stud_id&sel=$sel&curr_seme=$curr_seme";
print_menu($student_menu_p,$linkstr);

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

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr><td valign=top bgcolor="#CCCCCC">
 <table border="0" width="100%" cellspacing="0" cellpadding="0" >
    <tr>
      <td  valign="top" >    
	<?php       
	//建立左邊選單	
	$temparr = class_base();   
	$upstr = $temparr[$class_num]; 
	
	// source in include/PLlib.php    
	$grid1 = new sfs_grid_menu;  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select stud_id,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num from stud_base where  curr_class_num like '$class_num%' and stud_study_cond=0 order by curr_class_num";   //SQL 命令 
	$grid1->do_query(); //執行命令
	if ($key == $newBtn || $key == $postBtn)    
		$grid1->disabled=1;
	$downstr = "<input type=hidden name=ckey value=\"$ckey\">";
	$grid1->print_grid($stud_id,$upstr,$downstr); // 顯示畫面    

?>
     </td></tr></table>
     </td>
    <td width="100%" valign=top bgcolor="#CCCCCC">
<form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post"  <?php
	//當mnu筆數為0時 讓 form 為 disabled
	if ($grid1->count_row==0 && !($key == $newBtn || $key == $postBtn))  
		echo " disabled "; 
	?> > 
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
	
	<td class=title_mbody colspan=6 align=center>
	<?php 
		echo sprintf("%2d 號 -- %s (%s)",substr($tea1->Record[curr_class_num],-2),$tea1->Record[stud_name],$tea1->Record[stud_id]);
	?>
	</td>	
</tr>
<tr><td colspan=6 align=center>
<?php
	echo ($ckey == "$editModeBtn" )? "<input type=submit name=\"ckey\" value=\"$browseModeBtn\">": "<input type=submit name=\"ckey\" value=\"$editModeBtn\">";
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"ckey\" value=\"$newBtn\">";
	if ($ckey==$editModeBtn && $sick_id) 
	echo "&nbsp;&nbsp;<input type=\"submit\" name=\"key\" value=\"$editBtn\" onClick=\"return checkok();\" >";
?>	
</td></tr>
<tr>	<td><?php echo $tea1->Record_cname[s_calling] ?></td>
	<td><?php echo $tea1->Record_cname[sick] ?></td>	
	<td>動作</td>
</tr>
<?php
	//新增模式
	if ($ckey==$newBtn||$key == $postBtn){
			echo "<tr>";			
			echo "<td><input name=s_calling size=6 maxlength=6></td>";
			echo "<td>";
			$tea1->get_sick_p($tea1->Record[sick_id],"checkbox",0);
			echo "</td>";
			echo "<td><input type=submit name=key value=\"$postBtn\"></td>";
			echo "</tr>";
	}
?>
<?php	 
	
	for ($i=0;$i<$tea1->num_rows;$i++) {
		$sick_id = $tea1->Record[sick_id];
		$ti = ($i%2)+1;	
		if ($sick_id) {
			echo "<tr class=nom_$ti >";
			if ($ckey==$editModeBtn  && $sick_id) { //修改模式				
				echo "<td><input name=s_calling_$sick_id size=6 maxlength=6 value=\"".$tea1->Record[s_calling]."\"></td>";
				echo "<td>";
				$tea1->chk_str = "fam_$sick_id"; //加入id 值辨別用 
				$tea1->get_sick_p($tea1->Record[sick_id]);
				echo "</td>";
				echo "<td> <a href=\"{$_SERVER['SCRIPT_NAME']}?key=delete&sick_id=$sick_id&ckey=$ckey&$linkstr\" onClick=\"return confirm('確定刪除 ".$tea1->Record[s_calling]." 記錄?');\">刪除</a></td>";
			}
			else {
				
				echo "<td>".$tea1->Record[s_calling]."</td>";				
				echo "<td>";
				$tea1->get_sick_p($tea1->Record[sick_id],"normal");
				echo "</td>";
				echo "<td> <a href=\"{$_SERVER['SCRIPT_NAME']}?key=delete&sick_id=$sick_id&ckey=$ckey&$linkstr\" onClick=\"return confirm('確定刪除 ".$tea1->Record[s_calling]." 記錄?');\">刪除</a></td>";				
			}
			echo "</tr>";
		}		
		$tea1->next_record();
	}
	if ($ckey == $editModeBtn && $sick_id) {		
		echo "<tr><td colspan=6 align=center>";
		if ($chknext)
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "自動跳下一位 &nbsp;&nbsp;";
		echo "<input type=\"submit\" name=\"key\" value=\"$editBtn\" onClick=\"return checkok();\">";
		echo "</td></tr>";		
	}
?>
</table>
    　</td>
  </tr>
</table>
<input type=hidden name=stud_id value="<?php echo $stud_id ?>">
<input type=hidden name=sel value="<?php echo $sel ?>">
<input type="hidden" name="curr_seme" value="<?php echo $curr_seme ?>">
<input type=hidden name=nav_next >
</form>
<?php
foot();
?>
