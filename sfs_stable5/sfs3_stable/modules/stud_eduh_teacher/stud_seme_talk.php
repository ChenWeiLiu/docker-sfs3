<?php

// $Id: stud_seme_talk2.php 7309 2013-06-09 11:42:05Z smallduh $

// 載入設定檔
include "config.php";

$m_arr = get_sfs_module_set("stud_class");
extract($m_arr, EXTR_OVERWRITE);

// 認證檢查
sfs_check();

//印出頁頭
head();
include ("$SFS_PATH/include/sfs_oo_overlib.php");
$ol  = new overlib($SFS_PATH_HTML."include");
$ol->ol_capicon=$SFS_PATH_HTML."../stud_class/images/componi.gif";

//欄位資訊
$field_data = get_field_info("stud_seme_talk");
///選單連結字串
$linkstr = "stud_id=$stud_id&c_curr_class={$_REQUEST['class_num']}&c_curr_seme=$c_curr_seme";

$stud_id=$_REQUEST[stud_id];

//模組選單
print_menu($menu_p,$linkstr);

//取得班級代號
$class_selected=explode('_',$_REQUEST['class_num']);
$class_num = sprintf("%d%02d",$class_selected[2],$class_selected[3]);;

$this_year = sprintf("%03d",curr_year());
//目前學年學期
$this_seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$sel_seme_year_seme = $_POST[sel_seme_year_seme];
if ($sel_seme_year_seme=='')
	$sel_seme_year_seme = $this_seme_year_seme;

$stud_id = $_GET[stud_id];
if ($stud_id == '')
	$stud_id = $_POST[stud_id];


$do_key = $_GET[do_key];
if ($do_key == '')
	$do_key = $_POST[do_key];
	
	
$interview=$_POST[interview]?$_POST[interview]:$_SESSION['session_tea_name'];	

	
//進行字元替換  避免特殊字元造成報表錯誤
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
	$sql_update = "update stud_seme_talk set sst_date='$_POST[sst_date]',interview='$interview',interview_method='$_POST[interview_method]',sst_name='$_POST[sst_name]',sst_main='$_POST[sst_main]',sst_memo='$_POST[sst_memo]',teach_id='{$_SESSION['session_tea_sn']}' where sst_id='$_POST[sst_id]'";
	$CONN->Execute($sql_update) or die($sql_update);
	break;
	
	//資料快貼
	case ' 送出快貼資料 ':
	//有資料的話
if ($_POST['stud_data']) {
	$data_arr=explode("\n",$_POST['stud_data']);
 //開始處理
 	for ($i = 0 ; $i < count($data_arr); $i++ ) {
		//去掉前後空白
	 //$data_arr[$i] = trim($data_arr[$i]);
	 //去掉跟隨別的擠在一塊的空白
   //$data_arr[$i] = preg_replace('/\s(?=\s)/','', $data_arr[$i]);
   //$data_arr[$i] = preg_replace('/[\n\r\t]/', ' ', $data_arr[$i]);

   //變成二維陣列
   $student=explode("\t",$data_arr[$i]);  //某筆學生的資料
   if (count($student)==5) { //5個欄位都有資料再處理
   	foreach ($student as $k=>$v) {
   	 $student[$k]=trim($v);  //去掉前後空白
   	}   	 
   				$sql_query="insert into stud_seme_talk (seme_year_seme,stud_id,sst_date,sst_name,sst_main,sst_memo,teach_id,interview,interview_method) values ('$sel_seme_year_seme','$_POST[stud_id]','".$student[0]."','".$student[2]."','".$student[3]."','".$student[4]."','{$_SESSION['session_tea_sn']}','".$student[1]."','".$student[4]."')";
   				$CONN->Execute($sql_query) or die($sql_query);
          //echo $sql_query."<br>";
   }
	} // end for
} // end if stud_date	
	
	break;
	
	
}

if(!$c_curr_seme)
	$c_curr_seme = sprintf ("%03d%d",curr_year(),curr_seme()); //現在學年學期

	$c_curr_class = $_POST[c_curr_class];

//儲存後到下一筆
if ($chknext)
	$stud_id = $nav_next;	
	$query = "select a.stud_id,a.stud_name,a.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_id='$stud_id' and a.stud_study_cond=0 and b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num'";	
	$res = $CONN->Execute($query) or die($res->ErrorMsg());
	//未設定或改變在職狀況或刪除記錄後 到第一筆
	if ($stud_id =="" || $res->RecordCount()==0) {	
		$temp_sql = "select a.stud_id,a.stud_name from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond=0 and b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num' order by b.seme_num ";
		$res2 = $CONN->Execute($temp_sql) or die($temp_sql);
		$stud_id = $res2->fields[0];
	}

$stud_name = $res->fields[1];
$student_sn = $res->fields[2]; // by smallduh 2012/10/05




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

function check_length(element,limit)
{
	limit= limit * 2;
	if (element.value.length > limit)
	{ 
		element.value = element.value.substr(0,limit-1);
		alert("超過限定的字符將被刪去！");
		alert("將被儲存的內容~~\n\n"+element.value);
		element.focus();
	}
}

function nor_form() {
	nor_form0.style.display="block";
  nor_form1.style.display="block";
  nor_form2.style.display="block";
  nor_form3.style.display="block";
  nor_form4.style.display="block";
  nor_form5.style.display="block";
  nor_form6.style.display="block";
  past_form1.style.display="none";
  past_form2.style.display="none";
  past_form3.style.display="none";
}

function past_form() {
	nor_form0.style.display="none";
  nor_form1.style.display="none";
  nor_form2.style.display="none";
  nor_form3.style.display="none";
  nor_form4.style.display="none";
  nor_form5.style.display="none";
  nor_form6.style.display="none";
  past_form1.style.display="block";
  past_form2.style.display="block";
  past_form3.style.display="block";
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
	$s_y = substr($c_curr_seme,0,3);
	$s_s = substr($c_curr_seme,-1);	
	$upstr=get_course_class_select($s_y,$s_s,"class_num","this.form.submit",$_REQUEST['class_num']);
	
	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = $gridRow_num ;	     //顯示筆數   
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond=0 and b.seme_year_seme='$c_curr_seme' and b.seme_class='$class_num' order by b.seme_num";   //SQL 命令   
	$grid1->do_query(); //執行命令   
	//$downstr = "<br><font size=2><a href=\"stud_talk_class.php\" target=\"showclass\">顯示本學期記錄</font></a>";
	$grid1->print_grid($stud_id,$upstr,$downstr); // 顯示畫面   
  

?>
     </td>
     
    <td width="100%" valign=top bgcolor="#CCCCCC">
    	<?php 
    	//當mnu筆數為0時 讓 form 為 disabled 
    	?>
	
	<form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post" onsubmit="checkok()" <?php if ($grid1->count_row==0 && !($do_key == $newBtn || $do_key == $postBtn)) echo " disabled ";?>	>
 <input type="hidden" name="class_num" value="<?php echo $_REQUEST['class_num'];?>">
 
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr id="nor_form0">
<td class=title_mbody colspan=5 align=center  background="../stud_class/images/tablebg.gif" >
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
		echo sprintf("%d學年第%d學期 %s--%s (%s)",substr($c_curr_seme,0,-1),substr($c_curr_seme,-1),$class_list_p[$c_curr_seme],$stud_name,$stud_id);

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
    <input type="button" value="使用快貼新增" onclick="past_form()">
	</td>	
</tr>


<tr id="nor_form1">
    <td align="right" CLASS="title_sbody1">記錄日期</td>
<?php if ($sst_date=='') $sst_date = date("Y-m-d"); ?>
    <td CLASS="gendata"><input type="text" size="10" maxlength="10" name="sst_date" value="<?php echo $sst_date ?>"></td>
</tr>

<tr id="nor_form2">
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

<tr id="nor_form3">
    <td align="right" CLASS="title_sbody1">連絡對象</td>
    <td CLASS="gendata"><input type="text" size="12" maxlength="12" name="sst_name" value="<?php echo $sst_name ?>"></td>
</tr>


<tr  id="nor_form4">
    <td align="right" CLASS="title_sbody1">連絡事項</td>
    <td CLASS="gendata"><input type="text" size="16" maxlength="16" name="sst_main" value="<?php echo $sst_main ?>"></td>
</tr>


<tr id="nor_form5">
    <td align="right" CLASS="title_sbody1">內容要點<br><font size=1 color="red"><br>(超過<?php echo $talk_length; ?>個字會被自動刪除！)<br>不可使用半形的 & < > " ' 等符號，請使用標準的標點符號。</font></td>
    <td><textarea name="sst_memo" cols=40 rows=5 wrap=virtual onchange="check_length(this,<?php echo $talk_length; ?>);"><?php echo $sst_memo ?></textarea></td>
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
?> 輔導訪談記錄 , <a href="../stud_class/stud_talk_detail.php?student_sn=<?php echo $student_sn; ?>" target="show_all" >列出全部記錄</a>

<input type="hidden" name="stud_id" value="<?php echo $stud_id;?>">
<input type="hidden" name="sel_seme_year_seme" value="<?php echo $sel_seme_year_seme;?>">
<table border="0" width="100%">
 <tr id="past_form1" style="display:none">
  <td><input type="submit" name="do_key" value=" 送出快貼資料 "><input type="button" value="返回單筆新增" onclick="nor_form()"></td>
 </tr>
 <tr id="past_form2" style="display:none">
  <td><textarea name="stud_data" cols="100" rows="10"></textarea></td>
 </tr>
 <tr id="past_form3" style="display:none">
  <td>
   <table border="1" width="100%" style="border-collapsecollapse" bordercolor="#CCCCCC">
     <tr>
       <td style="color:#800000;font-size:9pt">
       ※說明 : 如果您習慣以 Excel 等方式管理學生訪談資料 (Excel範例檔:<a href="../stud_class/images/demo.xls">下載</a>) ,請直接選擇內容部分如圖, 複製/貼上再送出即可.<br>
       <img src="../stud_class/images/demo.jpg"><br>
       注意! 欄位的順序必須正確,一列為一筆資料,複製要輸入的內容, 再貼上即可.
       </td>
     </tr>
   </table>	
  </td>
 </tr>
</table>
</form>
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr><td>學期</td><td>記錄日期</td><td>連絡對象</td><td>連絡事項</td><td>訪談者</td><td>訪談方式</td><td>建檔者</td><td>動作</td></tr>
<?php
$sql_select = "select a.*,b.name from stud_seme_talk a left join teacher_base b on a.teach_id=b.teacher_sn where a.seme_year_seme like '$sel_this_year%' and a.stud_id='$stud_id' order by a.seme_year_seme desc ,a.sst_id desc";

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
	if($ii++ % 2 ==0)
		echo "<tr class=\"nom_1\">";
	else
		echo "<tr class=\"nom_2\">";
		
	echo "<td>$seme_str</td><td>$sst_date</td><td>$sst_name</td><td>$sst_main</td><td>$interview</td><td>$interview_method</td><td>$name</td><td >&nbsp;";
	if($sel_this_year == $this_year || $old_year_is_edit) {
		echo " <a href=\"{$_SERVER['SCRIPT_NAME']}?class_num={$_REQUEST[class_num]}&do_key=edit&sst_id=$sst_id\"";
		$ol->pover($sst_name,$sst_memo);
		echo ">檢視/修改</a>&nbsp;|&nbsp;<a href=\"{$_SERVER['SCRIPT_NAME']}?class_num={$_REQUEST[class_num]}&do_key=delete&sst_id=$sst_id&stud_id=$stud_id\" onClick=\"return confirm('確定刪除?');\">刪除</a>";
	}
	else
	{
		echo " <a ";
		$ol->pover($sst_name,$sst_memo);
		echo " ><img src=\"../stud_class/images/icon1.gif\"></a>";
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
