<?php

// $Id: stud_eduh_list.php 7724 2013-10-28 07:46:32Z smallduh $

// 引入您自己的 config.php 檔
require "config.php";

// 認證檢查
sfs_check();

//目前學年學期
$this_seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$sel_seme_year_seme = $_POST[sel_seme_year_seme];

if ($sel_seme_year_seme=='')
	$sel_seme_year_seme = $this_seme_year_seme;

$stud_id = $_GET[stud_id];
if ($stud_id == '')
	$stud_id = $_POST[stud_id];
$c_curr_class=$_GET[c_curr_class];
if($c_curr_class=='')
	$c_curr_class = $_POST[c_curr_class];
$c_curr_seme = $_REQUEST[c_curr_seme];

switch($_POST[do_key]) {
	case $editBtn:
	for ($i=1;$i<=11;$i++) {
		$sse_temp =",";	
		$sse_arr = "sse_s".$i;
		if (count($_POST["sse_s".$i])>0) {
			//修正 while(list($v,$k)=each($array)) 用法 2013.10.28
			reset($_POST["sse_s".$i]);
			while(list($tid,$tname)=each($_POST["sse_s".$i]))
				$sse_temp .= $tname.",";
		
			$$sse_arr = $sse_temp;
		}
	}
	$sql_insert = "replace into stud_seme_eduh (seme_year_seme,stud_id,sse_relation,sse_family_kind,sse_family_air,sse_farther,sse_mother,sse_live_state,sse_rich_state,sse_s1,sse_s2,sse_s3,sse_s4,sse_s5,sse_s6,sse_s7,sse_s8,sse_s9,sse_s10,sse_s11) values ('$_POST[sel_seme_year_seme]','$stud_id','$_POST[sse_relation]','$_POST[sse_family_kind]','$_POST[sse_family_air]','$_POST[sse_farther]','$_POST[sse_mother]','$_POST[sse_live_state]','$_POST[sse_rich_state]','$sse_s1','$sse_s2','$sse_s3','$sse_s4','$sse_s5','$sse_s6','$sse_s7','$sse_s8','$sse_s9','$sse_s10','$sse_s11')";
	$CONN->Execute($sql_insert) or die ($sql_insert);
	
	break;
}


//印出檔頭
head();
//欄位資訊
$field_data = get_field_info("stud_seme_eduh");


//選單連結字串
$linkstr = "stud_id=$stud_id&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";
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
if ($_POST[chknext])
        $stud_id = $_POST[nav_next];
$query = "select a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_id='$stud_id' and (a.stud_study_cond=0 or a.stud_study_cond=5)  and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'";
$res = $CONN->Execute($query) or die($res->ErrorMsg());
//未設定或改變在職狀況或刪除記錄後 到第一筆
if ($stud_id =="" || $res->RecordCount()==0) {
	$temp_sql = "select a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn  and  (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";
		$res = $CONN->Execute($temp_sql) or die($temp_sql);
		$stud_id = $res->fields[0];
}
                                                                                                                    
$stud_name = $res->fields[1];



$sql_select = "select seme_year_seme,stud_id,sse_relation,sse_family_kind,sse_family_air,sse_farther,sse_mother,sse_live_state,sse_rich_state,sse_s1,sse_s2,sse_s3,sse_s4,sse_s5,sse_s6,sse_s7,sse_s8,sse_s9,sse_s10,sse_s11 from stud_seme_eduh where stud_id='$stud_id' and seme_year_seme='$sel_seme_year_seme'";
$recordSet = $CONN->Execute($sql_select) or die ($sql_select);

if (!$recordSet->EOF) {

	$sel_seme_year_seme = $recordSet->fields["seme_year_seme"];
	$stud_id = $recordSet->fields["stud_id"];
	$sse_relation = $recordSet->fields["sse_relation"];
	$sse_family_kind = $recordSet->fields["sse_family_kind"];
	$sse_family_air = $recordSet->fields["sse_family_air"];
	$sse_farther = $recordSet->fields["sse_farther"];
	$sse_mother = $recordSet->fields["sse_mother"];
	$sse_live_state = $recordSet->fields["sse_live_state"];
	$sse_rich_state = $recordSet->fields["sse_rich_state"];
	$sse_s1 = $recordSet->fields["sse_s1"];
	$sse_s2 = $recordSet->fields["sse_s2"];
	$sse_s3 = $recordSet->fields["sse_s3"];
	$sse_s4 = $recordSet->fields["sse_s4"];
	$sse_s5 = $recordSet->fields["sse_s5"];
	$sse_s6 = $recordSet->fields["sse_s6"];
	$sse_s7 = $recordSet->fields["sse_s7"];
	$sse_s8 = $recordSet->fields["sse_s8"];
	$sse_s9 = $recordSet->fields["sse_s9"];
	$sse_s10 = $recordSet->fields["sse_s10"];
	$sse_s11 = $recordSet->fields["sse_s11"];

}
else {

	unset($sse_relation);
	unset($sse_family_kind);
	unset($sse_family_air);
	unset($sse_farther);
	unset($sse_mother);
	unset($sse_live_state);
	unset($sse_rich_state);
	unset($sse_s1);
	unset($sse_s2);
	unset($sse_s3);
	unset($sse_s4);
	unset($sse_s5);
	unset($sse_s6);
	unset($sse_s7);
	unset($sse_s8);
	unset($sse_s9);
	unset($sse_s10);
	unset($sse_s11);

}

		

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
 
<table BORDER=0 CELLPADDING=0 CELLSPACING=0 CLASS="tableBg" WIDTH="100%" > 
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

//if ($_GET[c_curr_class] <>''){
//	$c_curr_class = sprintf("%03d_%d_%02d_%02d",$s_y,$s_s,substr($_GET[c_curr_class],0,-2),substr($_GET[c_curr_class],-2));
//}
//顯示班級
	$tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class);
	$upstr .= $tmp;

	$grid1 = new ado_grid_menu($SCRIPT_NAME,$URI,$CONN);  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn  and (a.stud_study_cond=0 or a.stud_study_cond=5) and b.seme_year_seme='$curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL 命令   
//	echo $grid1->sql_str;	
	$downstr = "<input type=\"hidden\" name=\"sel_seme_year_seme\" value=\"$_POST[sel_seme_year_seme]\">";
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($stud_id,$upstr,$downstr); // 顯示畫面   



?>
    </td>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post"  <?php
	//當mnu筆數為0時 讓 form 為 disabled
	if ($grid1->count_row==0 && !($key == $newBtn || $key == $postBtn))  
		echo " disabled "; 
	?> > 


<! -- 輸入表單開始 --!>

<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body" >
<tr>
<td class=title_mbody colspan=5 align=center background="images/tablebg.gif" >
	<?php 
		$sel = new drop_select();
		$sel->s_name ="sel_seme_year_seme";
		$sel->id = $sel_seme_year_seme;
		$sel->is_submit = true;
		$sel->has_empty = false;
		$sel->arr = get_class_seme();
		$sel->do_select();
		echo sprintf(" --%s (%s)",$stud_name,$stud_id);

//		if ($sel_seme_year_seme == $this_seme_year_seme )
		    	echo "&nbsp;&nbsp;<input type=submit name=do_key value =\"$editBtn\" onClick=\"return checkok();\">&nbsp;&nbsp;";
    		if ($_POST[chknext])
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "自動跳下一位";
    
    ?>
	</td>	
</tr>
<tr>
	<td colspan=2>
	<table  cellspacing="5" cellpadding="0" >
	<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_relation][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel = new drop_select();
	$sel->s_name = "sse_relation";
	$sel->arr = sfs_text("父母關係");
	$sel->id = $sse_relation;
	$sel->do_select();
	?>
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_family_kind][d_field_cname] ?></td>
	<td CLASS="gendata">

	<?php 
	$sel->s_name = "sse_family_kind";
	$sel->arr = sfs_text("家庭類型");
	$sel->id = $sse_family_kind;
	$sel->do_select();
	?>
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_family_air][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_family_air";
	$sel->arr = sfs_text("家庭氣氛");
	$sel->id = $sse_family_air;
	$sel->do_select();
	?>

	</td>
	</tr>
	</table>


	</td>
</tr>
<tr>
	<td colspan=2>
	<table  cellspacing="5" cellpadding="0" >

	<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_farther][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_farther";
	$sel->arr = sfs_text("管教方式");
	$sel->id = $sse_farther;
	$sel->do_select();
	?>

	</td>

	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_mother][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_mother";
	$sel->arr = sfs_text("管教方式");
	$sel->id = $sse_mother;
	$sel->do_select();
	?>

	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_live_state][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_live_state";
	$sel->arr = sfs_text("居住情形");
	$sel->id = $sse_live_state;
	$sel->do_select();
	?>
	</td>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_rich_state][d_field_cname] ?></td>

	<td CLASS="gendata">
	<?php 
	$sel->s_name = "sse_rich_state";
	$sel->arr = sfs_text("經濟狀況");
	$sel->id = $sse_rich_state;
	$sel->do_select();
	?>

	</td>
	</tr>
	</table>

	</td>
</tr>	


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s1][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk = new checkbox_class();
	$chk->css = "gendata";
	$chk->s_name="sse_s1";
	$chk->is_color= true;
	$chk->id = $sse_s1;
	$chk->arr = sfs_text("喜愛困難科目");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s2][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s2;
	$chk->s_name="sse_s2";
	$chk->css = "gendata";
	$chk->arr = sfs_text("喜愛困難科目");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>

</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s3][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s3;
	$chk->s_name="sse_s3";
	$chk->css = "gendata";
	$chk->arr = sfs_text("特殊才能");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s4][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s4;
	$chk->s_name="sse_s4";
	$chk->css = "gendata";
	$chk->arr = sfs_text("興趣");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>

</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s5][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s5;
	$chk->s_name="sse_s5";
	$chk->css = "gendata";
	$chk->arr = sfs_text("生活習慣");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s6][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s6;
	$chk->s_name="sse_s6";
	$chk->css = "gendata";
	$chk->arr = sfs_text("人際關係");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>

<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s7][d_field_cname] ?></td>
	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s7;
	$chk->s_name="sse_s7";
	$chk->css = "gendata";
	$chk->arr = sfs_text("外向行為");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s8][d_field_cname] ?></td>

	<td CLASS="gendata">
	<?php
	$chk->id = $sse_s8;
	$chk->s_name="sse_s8";
	$chk->css = "gendata";
	$chk->arr = sfs_text("內向行為");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s9][d_field_cname] ?></td>
	<td CLASS="gendata">

	<?php
	$chk->id = $sse_s9;
	$chk->s_name="sse_s9";
	$chk->css = "gendata";
	$chk->arr = sfs_text("學習行為");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s10][d_field_cname] ?></td>
	<td CLASS="gendata">

	<?php
	$chk->id = $sse_s10;
	$chk->s_name="sse_s10";
	$chk->css = "gendata";
	$chk->arr = sfs_text("不良習慣");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>


<tr>

	<td align="right" CLASS="title_sbody1"><?php echo $field_data[sse_s11][d_field_cname] ?></td>
	<td CLASS="gendata">

	<?php
	$chk->id = $sse_s11;
	$chk->s_name="sse_s11";
	$chk->css = "gendata";
	$chk->arr = sfs_text("焦慮行為");
	$chk->cols=6;
	$chk->do_select();
	?>
	</td>
</tr>
<input type="hidden" name="stud_id" value="<?php echo $stud_id ?>">
<input type="hidden" name="seme_year_seme" value="<?php echo $sel_seme_year_seme ?>">
<input type="hidden" name=c_curr_seme value='<?php echo $c_curr_seme ?>'>
<input type="hidden" name=c_curr_class value='<?php echo $c_curr_class ?>'>
<input type=hidden name=nav_next >

</table>
</form>

<!------------------- 輸入表單結束 ------------------------------ !>

</td>
</tr>
</table>
<?php 
//印出尾頭
foot();
?> 
