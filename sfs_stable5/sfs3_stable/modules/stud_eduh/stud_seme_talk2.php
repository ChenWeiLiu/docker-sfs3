<?php

// $Id: stud_seme_talk2.php 8516 2015-09-03 01:15:41Z infodaes $

// 載入設定檔
include "config.php";
// 認證檢查
sfs_check();

$m_arr = get_sfs_module_set("stud_class");
extract($m_arr, EXTR_OVERWRITE);

$this_year = sprintf("%03d",curr_year());

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
$c_curr_seme = $_GET[c_curr_seme];
if($c_curr_seme=='')
	$c_curr_seme = $_POST[c_curr_seme];


$do_key = $_GET[do_key];
if ($do_key == '')
	$do_key = $_POST[do_key];

$interview=$_POST[interview]?$_POST[interview]:$_SESSION['session_tea_name'];	

$char_replace=array("<"=>"＜",">"=>"＞","'"=>"’","\""=>"”");
foreach($char_replace as $key=>$value){
	$_POST[sst_name]=str_replace($key,$value,$_POST[sst_name]);
	$_POST[sst_main]=str_replace($key,$value,$_POST[sst_main]);
	$_POST[sst_memo]=str_replace($key,$value,$_POST[sst_memo]);
}

switch($do_key) {
	//新增確定
	case $newBtn:

	$seme_year_seme = $_POST[sel_seme_year_seme];
	if ($seme_year_seme =='')
		$seme_year_seme = $this_seme_year_seme;
	$sql_insert = "insert into stud_seme_talk (seme_year_seme,stud_id,sst_date,sst_name,sst_main,sst_memo,teach_id,interview,interview_method) values ('$sel_seme_year_seme','$_POST[stud_id]','$_POST[sst_date]','$_POST[sst_name]','$_POST[sst_main]','$_POST[sst_memo]','{$_SESSION['session_tea_sn']}','$interview','$_POST[interview_method]')";
	$CONN->Execute($sql_insert) or die($sql_insert);
	$sst_date ='';
	$sst_name ='';
	$sst_main ='';
	$sst_memo ='';

	//回到目前學年
	$_REQUEST['sel_this_year']= substr($seme_year_seme,0,-1);
	break;

	//刪除 
	case "delete":
	//$query = "delete from stud_seme_talk where sst_id='$_GET[sst_id]' and teach_id='$_SESSION[session_tea_sn]'" ;
	$query = "delete from stud_seme_talk where sst_id='$_GET[sst_id]'" ;
	$CONN->Execute($query);
	break;

	//檢視 / 修改
	case "edit":
	$sql_select = "select * from stud_seme_talk where sst_id='$_GET[sst_id]'";
	$recordSet = $CONN->Execute($sql_select);

	if (!$recordSet->EOF) {
		$sst_id = $recordSet->fields["sst_id"];
		$seme_year_seme = $recordSet->fields["seme_year_seme"];
		$stud_id = $recordSet->fields["stud_id"];
		$sst_date = $recordSet->fields["sst_date"];
		$sst_name = $recordSet->fields["sst_name"];
		$sst_main = $recordSet->fields["sst_main"];		
		$sst_memo = $recordSet->fields["sst_memo"];
		$interview = $recordSet->fields["interview"];
		$interview_method = $recordSet->fields["interview_method"];
		$teach_id = $recordSet->fields["teach_id"];
	}

	break;
	
	//確定修改
	case $editBtn:
	$sql_update = "update stud_seme_talk set seme_year_seme='$_POST[seme_year_seme]',sst_date='$_POST[sst_date]',sst_name='$_POST[sst_name]',sst_main='$_POST[sst_main]',sst_memo='$_POST[sst_memo]',interview='$interview',interview_method='$_POST[interview_method]' where sst_id='$_POST[sst_id]'";
	$CONN->Execute($sql_update) or die($sql_update);
	break;
	
}

//印出頁頭
head();
include ("$SFS_PATH/include/sfs_oo_overlib.php");
$ol  = new overlib($SFS_PATH_HTML."include");
$ol->ol_capicon=$SFS_PATH_HTML."images/componi.gif";

//欄位資訊
$field_data = get_field_info("stud_seme_talk");
///選單連結字串
$linkstr = "stud_id=$stud_id&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";

//if ($stud_id=='')
//	$stud_id= $_GET[stud_id];
//if ($stud_id=='')
//	$stud_id= $_POST[stud_id];



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
if($c_curr_seme =='')
	$c_curr_seme = sprintf ("%03s%s",curr_year(),curr_seme()); //現在學年學期

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
<body onload="setfocus(document.myform.sst_name)">
<table border="0" width="100%" cellspacing="0" cellpadding="0" CLASS="tableBg" >
<tr>
<td valign=top align="right">

<?php
  	//建立左邊選單   
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

	$tmp=&get_class_select($s_y,$s_s,"","c_curr_class","this.form.submit",$c_curr_class);
	$upstr .= $tmp;

	$temparr = class_base();   
	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn  and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL 命令   

	$downstr = "<input type=\"hidden\" name=\"sel_seme_year_seme\" value=\"$_POST[sel_seme_year_seme]\"><input type=\"hidden\" name=\"sel_this_year\" value=\"$_REQUEST[sel_this_year]\">";
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($stud_id,$upstr,$downstr); // 顯示畫面   
 

?>
     </td>
     
    <td width="100%" valign=top bgcolor="#CCCCCC">
<form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post" onsubmit="checkok()" <?php
	//當mnu筆數為0時 讓 form 為 disabled
	if ($grid1->count_row==0 && !($do_key == $newBtn || $do_key == $postBtn))  
		echo " disabled ";

	?> > 
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
<td class=title_mbody colspan=5 align=center  background="images/tablebg.gif" >
<?php 
	echo "<input type=\"hidden\" name=\"stud_id\" value=\"$stud_id\">"; 
	//允許修改上學期資料
	if ($old_year_is_edit) {
		$sel = new drop_select();
		$sel->s_name ="sel_seme_year_seme";
		$sel->id = $sel_seme_year_seme;
		$sel->is_submit = true;
		$sel->has_empty = false;
		$sel->arr = get_class_seme();
		$sel->do_select();
		echo sprintf(" --%s (%s)",$stud_name,$stud_id);
	}
	else   	
		echo sprintf("%d學年第%d學期 %s--%s (%s)",$s_y,$s_s,$class_list_p[$c_curr_seme],$stud_name,$stud_id);

	if ($_POST[chknext])
		echo "<input type=checkbox name=chknext value=1 checked>";
	else
		echo "<input type=checkbox name=chknext value=1 >";

	echo "自動跳下一位 &nbsp;";
	if (intval(substr($sel_seme_year_seme,0,-1))==curr_year() || $old_year_is_edit) {
		echo ($do_key == 'edit')?"<input type=\"submit\" name=\"do_key\" value=\"$editBtn\"> <input type=\"hidden\" name=\"sst_id\" value=\"$sst_id\">":"<input type=\"submit\" name=\"do_key\" value=\"$newBtn\">";
	}
?>
	</td>	
</tr>


<tr>
    <td align="right" CLASS="title_sbody1">學年學期</td>
    <td CLASS="gendata"><input type="text" size="10" maxlength="10" name="seme_year_seme" value="<?php echo $seme_year_seme?$seme_year_seme:$this_seme_year_seme ?>"></td>
</tr>



<tr>
    <td align="right" CLASS="title_sbody1">記錄日期</td>
<?php if ($sst_date=='') $sst_date = date("Y-m-d"); ?>
    <td CLASS="gendata"><input type="text" size="10" maxlength="10" name="sst_date" value="<?php echo $sst_date ?>"></td>
</tr>

<tr>
    <td align="right" CLASS="title_sbody1">訪談者</td>
    <td CLASS="gendata"><input type="text" size="20" maxlength="20" name="interview" value="<?php echo $interview ?>"></td>
</tr>


<tr id="nor_form6">
    <td align="right" CLASS="title_sbody1">訪談方式</td>
	<?php 
		$im="<select name='interview_method'><option value=''></option>";
		$methods=explode(',',$interview_methods);

		foreach($methods as $key=>$value) {
			$selected=($value == $interview_method)?'selected':'';
			$im.="<option value='$value' $selected>$value</option>";		
		}
		$im.="</select><font size=1 color='red'> (選項由班級學籍管理模組變數中設定!)</font>";

	?>
    <td CLASS="gendata"><?php echo $im ?></td>
</tr>

<tr>
    <td align="right" CLASS="title_sbody1">連絡對象</td>
    <td CLASS="gendata"><input type="text" size="20" maxlength="20" name="sst_name" value="<?php echo $sst_name ?>"></td>
</tr>


<tr>
    <td align="right" CLASS="title_sbody1">連絡事項</td>
    <td CLASS="gendata"><input type="text" size="40" maxlength="40" name="sst_main" value="<?php echo $sst_main ?>"></td>
</tr>


<tr>
    <td align="right" CLASS="title_sbody1">內容要點<br><br><font size=1 color="red">不可使用半形的 & < > " ' 等符號，請使用標準的標點符號。</font></td>
    <td><textarea name="sst_memo" cols=40 rows=5 wrap=virtual><?php echo $sst_memo ?></textarea></td>
</tr>



</table>

<input type="hidden" name=nav_next>

<input type="hidden" name=c_curr_seme value='<?php echo $c_curr_seme ?>'>
<input type="hidden" name=c_curr_class value='<?php echo $c_curr_class ?>'>
<br>顯示 
<?php
	$sel_this_year = $_REQUEST['sel_this_year']; 
	if ($sel_this_year == '')
		$sel_this_year = $this_year;
	$sel = new drop_select();
	$sel->arr =  get_class_year(1,0,'d');
	$sel->s_name = "sel_this_year";
	$sel->id = $sel_this_year;
	$sel->has_empty=false;
	$sel->is_submit = true;
	$sel->do_select();
	echo "<b>$stud_name</b> ";
?> 輔導訪談記錄 

</FORM>
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr><td>學期</td><td>記錄日期</td><td>連絡對象</td><td>連絡事項</td><td>訪談者</td><td>訪談方式</td><td>建檔者</td><td>動作</td></tr>
<?php
$sql_select = "select a.*,b.name from stud_seme_talk a left join teacher_base b on a.teach_id=b.teacher_sn where  a.seme_year_seme like '$sel_this_year%' and a.stud_id='$stud_id' order by a.seme_year_seme desc ,a.sst_id desc ";

$recordSet = $CONN->Execute($sql_select) or die($sql_select); 

while (!$recordSet->EOF) {

	$sst_id = $recordSet->fields["sst_id"];
	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$stud_id = $recordSet->fields["stud_id"];
	$sst_date = $recordSet->fields["sst_date"];
	$sst_name = $recordSet->fields["sst_name"];
	$sst_main = $recordSet->fields["sst_main"];
	$sst_memo = $recordSet->fields["sst_memo"];
	$interview = $recordSet->fields["interview"];
	$interview_method = $recordSet->fields["interview_method"];
	$name = $recordSet->fields["name"];
    	$seme_str = substr($seme_year_seme,0,3)."學年第".substr($seme_year_seme,-1)."學期";
	$oth_link = "c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme&sel_this_year=".substr($seme_year_seme,0,-1);
	if($ii++ % 2 ==0)
		echo "<tr class=\"nom_1\">";
	else
		echo "<tr class=\"nom_2\">";
		
	echo "<td>$seme_str</td><td>$sst_date</td><td>$sst_name</td><td>$sst_main</td><td>$interview</td><td>$interview_method</td><td>$name</td><td >&nbsp;";
	if($sel_this_year == $this_year || $old_year_is_edit) {
		echo " <a href=\"{$_SERVER['SCRIPT_NAME']}?do_key=edit&sst_id=$sst_id&$oth_link\"";
		$ol->pover($sst_name,$sst_memo);
		echo ">檢視 / 修改</a>&nbsp;|&nbsp;<a href=\"{$_SERVER['SCRIPT_NAME']}?do_key=delete&sst_id=$sst_id&stud_id=$stud_id&$oth_link\" onClick=\"return confirm('確定刪除?');\">刪除</a>";
	}
	else
	{
		echo " <a ";
		$ol->pover($sst_name,$sst_memo);
		echo " ><img src=\"images/icon1.gif\"></a>";
	}
	echo "</td></tr>";
	
	$recordSet->MoveNext();
};

?>

</table>
</TD>
</TR>
</TABLE>

<?php
//印出頁尾
foot();
?>
