<?php 

// $Id: stud_sick.php 6055 2010-08-31 02:08:48Z brucelyc $

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
$tea1 = new stud_sick_p_class();

//按鍵處理 
switch ($key){	
	case $editBtn: //修改
	$tea1->edit("stud_id",$stud_id);
	break;	
}

//----------------------------------------

//儲存後到下一筆
if ($chknext)
	$stud_id = $nav_next;	
		
$tea1->query("select stud_sick_p.* ,stud_base.stud_id,stud_base.stud_name,stud_base.curr_class_num from stud_base left join  stud_sick_p on stud_sick_p.stud_id=stud_base.stud_id where stud_base.stud_id='$stud_id' and stud_base.curr_class_num like '$class_num%'"); 
//未設定或改變在職狀況或刪除記錄後 到第一筆
if ($stud_id =="" || $stud_id != $tea1->Record[stud_id]) {
	$result= mysql_query("select stud_base.stud_id from stud_base where  stud_base.curr_class_num like '$class_num%' order by curr_class_num limit 0,1");
	$row = mysql_fetch_row($result);	
	$tea1->query("select stud_sick_p.* ,stud_base.stud_id,stud_base.stud_name,stud_base.curr_class_num from stud_base left join stud_sick_p on stud_sick_p.stud_id=stud_base.stud_id where stud_base.stud_id='$row[0]' and  stud_base.curr_class_num like '$class_num%' "); 	
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
	$grid1->sql_str = "select stud_id,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num from stud_base where   curr_class_num like '$class_num%' and stud_study_cond=0 order by curr_class_num";   //SQL 命令 
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
	
	<td class=title_mbody align=center colspan=2>
	<?php 
		echo sprintf("%2d 號 -- %s (%s)",substr($tea1->Record[curr_class_num],-2),$tea1->Record[stud_name],$tea1->Record[stud_id]);
	?>
	</td>	
</tr>

<tr>
<td class=title_sbody1 nowrap >重大疾病</td>
<td>
<?php echo $tea1->get_sick_p($stud_id,$mode="checkbox") ; ?>
</td>
</tr>

<?php	
	echo "<tr><td colspan=2 align=center>";
	if ($chknext)
    		echo "<input type=checkbox name=chknext value=1 checked >";			
    	else
    		echo "<input type=checkbox name=chknext value=1 >";
    			
    	echo "自動跳下一位 &nbsp;&nbsp;";
	echo "<input type=\"submit\" name=\"key\" value=\"$editBtn\" onClick=\"return checkok();\">";
	echo "</td></tr>";		
	
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
