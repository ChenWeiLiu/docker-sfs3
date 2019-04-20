<?php

// $Id: stud_seme_talk2.php 8991 2016-10-19 03:35:23Z igogo $

// 載入設定檔
include "config.php";
// 認證檢查
head("輔導--資料查補");
sfs_check();

//取得任教班級代號
$class_num = get_teach_class();
if ($class_num == '') {
	head("權限錯誤");
	stud_class_err();
	foot();
	exit;
}

$this_year = sprintf("%03d",curr_year());
//目前學年學期
$this_seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$sel_seme_year_seme = $_POST[sel_seme_year_seme];
if ($sel_seme_year_seme=='')
	$sel_seme_year_seme = $this_seme_year_seme;

$stud_id = $_GET[stud_id];
if ($stud_id == '')
	$stud_id = $_POST[stud_id];

//igogo
if(strlen($stud_id)==0){
	$stud_id="null";	
}

$do_key = $_GET[do_key];
if ($do_key == '')
	$do_key = $_POST[do_key];
	
$interview=$_POST[interview]?$_POST[interview]:$_SESSION['session_tea_name'];	

switch($do_key) {
	//新增確定
	case $newBtn:

	$seme_year_seme = $_POST[sel_seme_year_seme];
	if ($seme_year_seme =='')
		$seme_year_seme = $this_seme_year_seme;
	$sql_insert = "insert into stud_seme_talk (seme_year_seme,stud_id,sst_date,sst_name,sst_main,sst_memo,teach_id,interview) values ('$sel_seme_year_seme','$_POST[stud_id]','$_POST[sst_date]','$_POST[sst_name]','$_POST[sst_main]','$_POST[sst_memo]','{$_SESSION['session_tea_sn']}','$interview')";
	$CONN->Execute($sql_insert) or die($sql_insert);
	$sst_date ='';
	$sst_name ='';
	$sst_main ='';
	$sst_memo ='';

	//回到目前學年
	$sel_this_year= $this_year;
	break;

	//刪除 
	case "delete":
	$query = "delete from stud_seme_talk where sst_id='$_GET[sst_id]' and teach_id='$_SESSION[session_tea_sn]'" ;
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
		
		//igogo
		if(strlen($stud_id)==0){
			$stud_id="null";	
		}

		$sst_date = $recordSet->fields["sst_date"];
		$sst_name = $recordSet->fields["sst_name"];
		$sst_main = $recordSet->fields["sst_main"];		
		$sst_memo = $recordSet->fields["sst_memo"];
		$interview = $recordSet->fields["interview"];
		$teach_id = $recordSet->fields["teach_id"];
	}

	break;
	
	//確定修改
	case $editBtn:
	$sql_update = "update stud_seme_talk set sst_date='$_POST[sst_date]',interview='$interview',sst_name='$_POST[sst_name]',sst_main='$_POST[sst_main]',sst_memo='$_POST[sst_memo]',teach_id='{$_SESSION['session_tea_sn']}' where sst_id='$_POST[sst_id]'";
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

if ($stud_id=='')
	$stud_id= $_GET[stud_id];
if ($stud_id=='')
	$stud_id= $_POST[stud_id];
	
//igogo
if(strlen($stud_id)==0){
	$stud_id="null";	
}

//模組選單
print_menu($menu_p,$linkstr);

$help_text="
	本程式僅列出班級本學期有轉出異動之學生，提供導師補建這類學生[訪談紀錄]用。||普通學生之[訪談紀錄]，請至[班級學生管理]模組下輸入。";
$help=&help($help_text);
echo $help;

if(!$c_curr_seme)
	$c_curr_seme = sprintf ("%03d%d",curr_year(),curr_seme()); //現在學年學期


//儲存後到下一筆
if ($chknext)
	$stud_id = $nav_next;	

		//igogo
		if(strlen($stud_id)==0){
			$stud_id="null";	
		}


	$query = "select a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_id=b.stud_id and a.stud_id='$stud_id' and a.stud_study_cond<>0  and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num'";
	$res = $CONN->Execute($query) or die($res->ErrorMsg());
	//未設定或改變在職狀況或刪除記錄後 到第一筆
	if ($stud_id =="" || $res->RecordCount()==0) {	
		$temp_sql = "select a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_id=b.stud_id  and  a.stud_study_cond<>0 and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num' order by b.seme_num ";
		$res2 = $CONN->Execute($temp_sql) or die($temp_sql);
		$stud_id = $res2->fields[0];

		//igogo
		if(strlen($stud_id)==0){
			$stud_id="null";	
		}

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
	
	
	$temparr = class_base();   
	$upstr = $temparr[$class_num]; 
	$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond<>0 and  b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num' order by b.seme_num ";   //SQL 命令

  $grid1->do_query(); //執行命令   
	$downstr = "";
	$grid1->print_grid($stud_id,$upstr,$downstr); // 顯示畫面   
  

?>
     </td>
     
    <td width="100%" valign=top bgcolor="#CCCCCC">
<form name ="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="checkok()" <?php
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
		echo sprintf("%d學年第%d學期 %s--%s (%s)",substr($c_curr_seme,1,2),substr($c_curr_seme,-1),$class_list_p[$c_curr_seme],$stud_name,$stud_id);

	//判斷是否為個人記錄	
	if ($teach_id == $_SESSION[session_tea_sn] || $teach_id=='') {
			
		if ($_POST[chknext])
    			echo "<input type=checkbox name=chknext value=1 checked >";			
    		else
    			echo "<input type=checkbox name=chknext value=1 >";
    			
    		echo "自動跳下一位 &nbsp;";
				echo ($do_key == 'edit')?"<input type=\"submit\" name=\"do_key\" value=\"$editBtn\"> <input type=\"hidden\" name=\"sst_id\" value=\"$sst_id\">":"<input type=\"submit\" name=\"do_key\" value=\"$newBtn\">";
	}
    ?>
	</td>	
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



<tr>
    <td align="right" CLASS="title_sbody1">連絡對象</td>
    <td CLASS="gendata"><input type="text" size="20" maxlength="20" name="sst_name" value="<?php echo $sst_name ?>"></td>
</tr>


<tr>
    <td align="right" CLASS="title_sbody1">連絡事項</td>
    <td CLASS="gendata"><input type="text" size="40" maxlength="40" name="sst_main" value="<?php echo $sst_main ?>"></td>
</tr>


<tr>
    <td align="right" CLASS="title_sbody1">內容要點</td>
    <td><textarea name="sst_memo" cols=40 rows=5 wrap=virtual><?php echo $sst_memo ?></textarea></td>
</tr>
</table>

<input type="hidden" name=nav_next>

<br>顯示 
<?php 
	$sel_this_year = $_POST[sel_this_year]; 
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
?> 輔導訪談記錄 , <a href="stud_talk_detail.php?stud_id=<?php echo $stud_id ?>" target="show_all" >列出全部記錄</a>

</FORM>
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr><td>學期</td><td>記錄日期</td><td>連絡對象</td><td>連絡事項</td><td>訪談者</td><td>建檔者</td><td>動作</td></tr>
<?php
 $sql_select = "select a.sst_id,a.seme_year_seme,a.stud_id,a.sst_date,a.sst_name,a.sst_main,a.sst_memo,b.name from stud_seme_talk a left join teacher_base b on a.teach_id=b.teacher_sn where  a.seme_year_seme like '$sel_this_year%' and a.stud_id='$stud_id' order by a.seme_year_seme desc ,a.sst_id desc ";

$recordSet = $CONN->Execute($sql_select) or die($sql_select); 

while (!$recordSet->EOF) {

 	$sst_id = $recordSet->fields["sst_id"];
	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$sst_date = $recordSet->fields["sst_date"];
	$sst_name = $recordSet->fields["sst_name"];
	$sst_main = $recordSet->fields["sst_main"];
	$sst_memo = $recordSet->fields["sst_memo"];
	$interview = $recordSet->fields["interview"];
	$name = $recordSet->fields["name"];
    	$seme_str = substr($seme_year_seme,0,3)."學年第".substr($seme_year_seme,-1)."學期";
	if($ii++ % 2 ==0)
		echo "<tr class=\"nom_1\">";
	else
		echo "<tr class=\"nom_2\">";
		
	echo "<td>$seme_str</td><td>$sst_date</td><td>$sst_name</td><td>$sst_main</td><td>$interview</td><td>$name</td><td >&nbsp;";
	if($sel_this_year == $this_year || $old_year_is_edit) {
		echo " <a href=\"{$_SERVER['PHP_SELF']}?do_key=edit&sst_id=$sst_id\"";
		$ol->pover($sst_name,$sst_memo);
		echo ">檢視 / 修改</a>&nbsp;|&nbsp;<a href=\"{$_SERVER['PHP_SELF']}?do_key=delete&sst_id=$sst_id&stud_id=$stud_id\" onClick=\"return confirm('確定刪除?');\">刪除</a>";
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

