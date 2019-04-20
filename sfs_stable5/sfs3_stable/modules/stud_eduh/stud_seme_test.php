<?php 

// $Id: stud_seme_test.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "config.php";
// 認證檢查
sfs_check();

//升級檢查 
require "module-upgrade.php";

$this_year = sprintf("%03d",curr_year());
//echo $this_year;
//目前學年學期
$this_seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$sel_seme_year_seme = $_POST[sel_seme_year_seme];
if ($sel_seme_year_seme=='')
	$sel_seme_year_seme = $this_seme_year_seme;

$do_key = $_GET[do_key];
if ($do_key == '')
	$do_key = $_POST[do_key];


$c_curr_class = $_POST[c_curr_class];
$c_curr_seme = $_POST[c_curr_seme];
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

	
	
if ($do_key == $newBtn) {
	$seme_year_seme = $_POST[sel_seme_year_seme];
	if ($seme_year_seme =='')
		$seme_year_seme = $this_seme_year_seme;
	if ($_POST['all_class']) { //複製到全班
		$query  = "SELECT  a.stud_id  FROM  stud_base a,stud_seme b where a.stud_id=b.stud_id  and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'   ";   //SQL 命令		
		$res= $CONN->Execute($query);
		while($row = $res->fetchrow()) {
				$stud_temp_id= $row['stud_id'];
				$sql_insert = "insert into stud_seme_test (seme_year_seme,stud_id,st_numb,st_name,st_score_numb,st_data_from,st_chang_numb,st_name_long,teacher_sn) values ('$seme_year_seme','$stud_temp_id','$_POST[st_numb]','$_POST[st_name]','$_POST[st_score_numb]','$_POST[st_data_from]','$_POST[st_chang_numb]','$_POST[st_name_long]','$_SESSION[session_tea_sn]')";
				$CONN->Execute($sql_insert) or die($sql_insert);
		}
	}
	else {
		$sql_insert = "insert into stud_seme_test (seme_year_seme,stud_id,st_numb,st_name,st_score_numb,st_data_from,st_chang_numb,st_name_long,teacher_sn) values ('$seme_year_seme','$_POST[stud_id]','$_POST[st_numb]','$_POST[st_name]','$_POST[st_score_numb]','$_POST[st_data_from]','$_POST[st_chang_numb]','$_POST[st_name_long]','$_SESSION[session_tea_sn]')";
		$CONN->Execute($sql_insert) or die($sql_insert);
	}
	$st_numb = ""; 
	$st_name = ""; 
	$st_score_numb = "";
	$st_data_from = "";
	$st_chang_numb = "";
	$st_name_long = "";

	//回到目前學年
	$sel_this_year = $this_year;		
 }
 elseif ($do_key == $editBtn) {	
	$sql_update = "update stud_seme_test set st_numb='$_POST[st_numb]',st_name='$_POST[st_name]',st_score_numb='$_POST[st_score_numb]',st_data_from='$_POST[st_data_from]',st_chang_numb='$_POST[st_chang_numb]',st_name_long='$_POST[st_name_long]' where st_id=$_POST[st_id]";
	$CONN->Execute($sql_update) or die($sql_update);
 }
 elseif ($_POST['act'] =="delete") {
	$query = "delete  from stud_seme_test where st_id='$_POST[st_id]' and teacher_sn='$_SESSION[session_tea_sn]'";
	$CONN->Execute($query);
 }
 elseif ($_POST['act'] == 'edit') {	
	$sql_select = "select st_id,seme_year_seme,stud_id,st_numb,st_name,st_score_numb,st_data_from,st_chang_numb,st_name_long,teacher_sn from stud_seme_test where st_id='$_POST[st_id]'";

	$recordSet = $CONN->Execute($sql_select) or die ($sql_select);

	while (!$recordSet->EOF) {

		$st_id = $recordSet->fields["st_id"];
		$seme_year_seme = $recordSet->fields["seme_year_seme"];
		$stud_id = $recordSet->fields["stud_id"];
		$st_numb = $recordSet->fields["st_numb"];
		$st_name = $recordSet->fields["st_name"];
		$st_score_numb = $recordSet->fields["st_score_numb"];
		$st_data_from = $recordSet->fields["st_data_from"];
		$st_chang_numb = $recordSet->fields["st_chang_numb"];
		$st_name_long = $recordSet->fields["st_name_long"];
		$teacher_sn = $recordSet->fields["teacher_sn"];

		$recordSet->MoveNext();
	};
}


if ($stud_id=='')
	$stud_id= $_GET[stud_id];
if ($stud_id=='')
	$stud_id= $_POST[stud_id];



// 印出頁頭
head();
// 欄位資訊
$field_data = get_field_info("stud_seme_test");
//選單連結字串
$linkstr = "stud_id=$stud_id&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";
//模組選單
print_menu($menu_p,$linkstr);


//儲存後到下一筆
if ($_POST[chknext])
	$stud_id = $_POST[nav_next];	
$query = "select a.stud_id,a.stud_name from stud_base a,stud_seme b where a.stud_id=b.stud_id and a.stud_id='$stud_id' and (a.stud_study_cond=0 or a.stud_study_cond=5)  and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'";
$res = $CONN->Execute($query) or die($res->ErrorMsg());
//未設定或改變在職狀況或刪除記錄後 到第一筆
if ($stud_id =="" || $res->RecordCount()==0) {
	$temp_sql = "select a.stud_id,a.stud_name from stud_base a,stud_seme b where a.stud_id=b.stud_id  and  (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";
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

<body onload="setfocus(document.myform.st_numb)">

<table BORDER=0 CELLPADDING=0 CELLSPACING=0 CLASS="tableBg" WIDTH="100%" > 
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
	$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.stud_id=b.stud_id  and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL 命令   
	$downstr = "<input type='hidden' name='sel_seme_year_seme' value='{$_POST['sel_seme_year_seme']}'";
	$grid1->do_query(); //執行命令   
	
	$grid1->print_grid($stud_id,$upstr,$downstr); // 顯示畫面   
 

?>
    </td>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post"  <?php
	//當mnu筆數為0時 讓 form 為 disabled
	if ($grid1->count_row==0 && !($key == $newBtn || $key == $postBtn))  
		echo " disabled "; 
	?> onsubmit="checkok()"  > 


<!- ------------------ 輸入表單開始 ------------------------------ !>
  <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body" >
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
		echo sprintf("%d學年第%d學期 %s--%s (%s)",substr($c_curr_seme,1,2),substr($c_curr_seme,-1),$class_list_p[$c_curr_seme],$stud_name,$stud_id);

	//判斷是否為個人記錄	
	if ($teach_id == $_SESSION[session_tea_sn] || $teach_id=='') {
			
		if ($_POST[chknext])
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input id='chknext'  type=checkbox name=chknext value=1 >";
    			
    		echo "<label for='chknext'>自動跳下一位</label> &nbsp;";
			
    		if ($_POST['act'] == 'edit'){
    			echo "<input type=\"submit\" name=\"do_key\" value=\"$editBtn\"> <input type=\"hidden\" name=\"ss_id\" value=\"$ss_id\">";
    		}
    		else {
    			echo "<input id='all_class' type=checkbox  name='all_class' value=1 >";    			
    			echo "<label for='all_class'>複製到全班</label> &nbsp;";
    			echo"<input type=\"submit\" name=\"do_key\" value=\"$newBtn\">";
    		}
	}
?>
	</td>	
</tr>
<tr>

	<td align="right" CLASS="title_sbody1"><?php echo $field_data[st_numb][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="st_numb" value="<?php echo $st_numb ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[st_name][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="st_name" value="<?php echo $st_name ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[st_score_numb][d_field_cname] ?></td>

	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="st_score_numb" value="<?php echo $st_score_numb ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[st_data_from][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="40" maxlength="40" name="st_data_from" value="<?php echo $st_data_from ?>"></td>
</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[st_chang_numb][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="20" maxlength="20" name="st_chang_numb" value="<?php echo $st_chang_numb ?>"></td>

</tr>


<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[st_name_long][d_field_cname] ?></td>
	<td CLASS="gendata"><input type="text" size="40" maxlength="40" name="st_name_long" value="<?php echo $st_name_long ?>"></td>
</tr>

</table>
<input type="hidden" name="stud_id" value="<?php echo $stud_id ?>">
<input type="hidden" name="st_id" value="<?php echo $st_id ?>">
<input type="hidden" name="seme_year_seme" value="<?php echo $seme_year_seme ?>">
<input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme ?>">
<input type="hidden" name="c_curr_class" value="<?php echo $c_curr_class ?>">
<input type="hidden" name="act">
<input type=hidden name=nav_next >
</FORM>
<center><b>心理測驗記錄</b></center> 

<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr><td>學期</td><td>測驗編號</td><td>測驗簡稱</td><td>成績編號</td><td>轉換表編號</td><td>建檔者</td><td>動作</td></tr>
<?php
$sql_select = "select st_id,seme_year_seme,stud_id,st_numb,st_name,st_score_numb,st_data_from,st_chang_numb,st_name_long,teacher_sn from stud_seme_test where stud_id='$stud_id' order by seme_year_seme ,st_id desc  ";
$recordSet = $CONN->Execute($sql_select) or die($sql_select);
while (!$recordSet->EOF) {
	$st_id = $recordSet->fields["st_id"];
	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$stud_id = $recordSet->fields["stud_id"];
	$st_numb = $recordSet->fields["st_numb"];
	$st_name = $recordSet->fields["st_name"];
	$st_score_numb = $recordSet->fields["st_score_numb"];
	$st_data_from = $recordSet->fields["st_data_from"];
	$st_chang_numb = $recordSet->fields["st_chang_numb"];
	$st_name_long = $recordSet->fields["st_name_long"];
	$teacher_sn = $recordSet->fields["teacher_sn"];
	$name = get_teacher_name($teacher_sn);
	$seme_str = substr($seme_year_seme,0,3)."學年第".substr($seme_year_seme,-1)."學期";
	if($ii++ % 2 ==0)
		echo "<tr class=\"nom_1\">";
	else
		echo "<tr class=\"nom_2\">";
		
	echo "<td>$seme_str</td><td>$st_numb</td><td>$st_name</td><td>$st_score_numb</td><td>$st_chang_numb</td><td>$name</td><td >&nbsp;";

if($seme_year_seme == $sel_seme_year_seme) {
	
		if ($teacher_sn == $_SESSION[session_tea_sn]) {
			echo  "<input type=\"button\"  onclick=\"sel_st($st_id)\"  value=\"檢視/修改\" >";
			echo  " <input type=\"button\"  onclick=\"del_st($st_id)\"  value=\"刪除\" >";						
		}
		else {
				echo " <a href=\"{$_SERVER['PHP_SELF']}?do_key=edit&st_id=$st_id\"   >檢視</a> "  ;
		}
			
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

<script type="text/javascript">
function  sel_st(st) {
	var form = document.myform;
	form.act.value = 'edit';	
	form.st_id.value = st;
	form.submit();
}

function  del_st(st) {
	if (confirm('確定刪除?')) {
		var form = document.myform;
		form.act.value = 'delete';	
		form.st_id.value = st;
		form.submit();
	}
}
</script>