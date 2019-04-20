<?php 
// $Id: seme_date.php 7736 2013-10-29 23:47:44Z smallduh $ 

// 載入設定檔
include "config.php";
include "../../include/sfs_case_PLlib.php"; 
$postBtn = "設定";

// 認證檢查
sfs_check();

if ($_POST[do_key]==$postBtn) {
	//$days=count($_POST['day']);	
	$i=0;
	foreach($_POST['day'] as $k=>$v) {
	 if ($v=='1') $i++;
	} 
	$days=$i; 													//學生上學日數	 

	$content=serialize($_POST['day']);
 	$sql_insert = "replace into seme_course_date (seme_year_seme,class_year,days,school_days) values ('$_POST[seme_year_seme]','$_POST[class_year]','$days','".$content."')";
	$CONN->Execute($sql_insert) or trigger_error("SQL 錯誤",E_USER_ERROR);
	$INFO="已於".date("Y-m-d H:i:s")."儲存一筆.";
	//echo $sql_insert;
}


// 印出頁頭
head("上課日設定");

// 模組選單
$tool_bar=&make_menu($school_menu_p);

//編修
if($_GET[do_key]=='edit' || $_POST[seme_year_seme]<>''){
	if ($_GET[do_key]=='edit'){
		$seme_year_seme = $_GET[seme_year_seme];
		$class_year = $_GET[class_year];
	}
	else {
		$seme_year_seme = $_POST[seme_year_seme];
		$class_year = $_POST[class_year];
	}
	//echo $sql_select;
}

//刪除
if($_GET[do_key]=='del'){
	$seme_year_seme = $_GET[seme_year_seme];
	$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
	if($curr_year_seme=$seme_year_seme) {
		$class_year = $_GET[class_year];
		if ($class_year) {
			$sql = "delete from seme_course_date where seme_year_seme='$seme_year_seme' and class_year='$class_year'";
			$recordSet = $CONN->Execute($sql) or trigger_error("刪除學期上課日數SQL錯誤 <br>$sql",E_USER_ERROR);
		}
		$INFO="已於".date("Y-m-d H:i:s")."刪除一筆.";
	}
	//echo $sql_select;
}


if($seme_year_seme==''){
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
}

$sql_select = "select seme_year_seme,days,class_year,school_days from seme_course_date where seme_year_seme='$seme_year_seme' and class_year='$class_year'";
$recordSet = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤 <br>$sql_select",E_USER_ERROR);
$DAY['School_days']=0;
if ($recordSet->Recordcount()>0){
	$days = $recordSet->fields["days"];
	$class_year = $recordSet->fields["class_year"];
	//$DAY=get_school_days($seme_year_seme,$class_year); //取得上課日期詳細設定 , 放在 $DAY 陣列中
  $DAY=unserialize($recordSet->fields['school_days']); 			//解開變成陣列
	$i=0;
	foreach($DAY as $k=>$v) {
	 if ($v=='1') $i++;
	} 
	$DAY['School_days']=$i; 													//學生上學日數	 
}

echo $tool_bar;
?> 

<table BORDER=0 CELLPADDING=10 CELLSPACING=0 CLASS="tableBg" WIDTH="100%" ALIGN="CENTER"> 
<tr>
<td >
<form name="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
  <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body" >


<tr>
	<td align="right" CLASS="title_sbody1">學年學期</td>
	<td CLASS="gendata">
	<?php
	$class_seme = get_class_seme();
	$sel1 = new drop_select();
	$sel1->id= $seme_year_seme;
	$sel1->has_empty=false;
	$sel1->is_submit = true;
	$sel1->s_name="seme_year_seme";
	$sel1->arr = $class_seme;
	$sel1->do_select();

	?>
	</td>	
</tr>

<tr>
	<td align="right" CLASS="title_sbody1">年級</td>
	<td CLASS="gendata">
	<?php
	$class_year_array= year_base(substr($seme_year_seme,0,-1),substr($seme_year_seme,-1)); 
	$sel1 = new drop_select();
	$sel1->id= $class_year;
	$sel1->has_empty=true;
	$sel1->is_submit = true;
	$sel1->s_name="class_year";
	$sel1->arr = $class_year_array;
	$sel1->do_select();

	?>
	</td>	
</tr>



<tr>
	<td align="right" CLASS="title_sbody1">上課日數</td>
	<td CLASS="gendata"><?php echo $days ?></td>
</tr>
<tr>
	<td align="right" CLASS="title_sbody1">上課日期<br>(請直接點選)</td>
	<td CLASS="gendata">
		<!-- 依月分日期列表勾選 -->
	<table border=0 id="school_day_select">
<?php
if ($_POST['class_year']!='') {
 if ($DAY['School_days']==0) echo "<font color=red>尚未設定上課日，系統預先勾選星期一至星期五，請確認後按下「設定」！</font>";
$sel_year=substr($seme_year_seme,0,3);
$sel_seme=substr($seme_year_seme,-1);
//取得資料庫中現存之學期之日期資料
$db_date=curr_year_seme_day($sel_year,$sel_seme);  //$db_date['start'] , $db_date['end'] , $db_date['st_start'] , $db_date['st_end']
//學期結束
$Dead_line=date("U",mktime(0,0,0,substr($db_date['end'],5,2),substr($db_date['end'],8,2),substr($db_date['end'],0,4)));
//開學日及結業日
$st_start_line=date("U",mktime(0,0,0,substr($db_date['st_start'],5,2),substr($db_date['st_start'],8,2),substr($db_date['st_start'],0,4)));
$st_end_line=date("U",mktime(0,0,0,substr($db_date['st_end'],5,2),substr($db_date['st_end'],8,2),substr($db_date['st_end'],0,4)));

$d1=explode('-',$db_date['start']);   //學期起始
$d2=explode('-',$db_date['end']);			//學期結束

//計算連結月數
if ($d1[1]>$d2[1]) { //有跨年
 $M_step=(12-$d1[1]+1)+$d2[1];
} else { //無跨年
 $M_step=$d2[1]-$d1[1]+1;
}

//開始, 依月分列出
$i=0; $j=0;
for ($M=$d1[1];$M<$d1[1]+$M_step;$M++) {
 $i++;
 if ($i%3==1) echo "<tr>";
 echo "<td align=\"center\" valign=\"top\">";
 $the_year=($M<=12)?$d1[0]:$d2[0];
 $the_mon=($M<=12)?$M:($M-12);
 //月分總日數
 $Mon_days=date("t",mktime(0,0,0,$the_mon,1,$the_year)); //本月有幾天
 $Mon_table="
 $the_year 年 $the_mon 月 <br>
  <table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>
   <tr>
    <td bgcolor='#FFCCCC' align='center'>日</td>
    <td bgcolor='#CCFFCC' align='center'>一</td>
    <td bgcolor='#CCFFCC' align='center'>二</td>
    <td bgcolor='#CCFFCC' align='center'>三</td>
    <td bgcolor='#CCFFCC' align='center'>四</td>
    <td bgcolor='#CCFFCC' align='center'>五</td>
    <td bgcolor='#CCCCFF' align='center'>六</td>
   </tr>
   <tr>
 ";
 //第1天是星期幾? 前面加空白
 $W_st=date("w",mktime(0,0,0,$the_mon,1,$the_year)); //起始點
 if ($W_st>0) {
  for ($w=0;$w<$W_st;$w++) { $Mon_table.="<td bgcolor='#DDDDDD'>&nbsp;</td>"; }
 }
 //開始
 for ($D=1;$D<=$Mon_days;$D++) {
   $j++;
   $W=date("w",mktime(0,0,0,$the_mon,$D,$the_year)); //星期幾
   $CHK_WORK=date("U",mktime(0,0,0,$the_mon,$D,$the_year));
   //學期中
   if ($CHK_WORK<=$Dead_line) {
   	 $chk_day=sprintf('%04d-%02d-%02d',$the_year,$the_mon,$D);
   	 $chk_if=($DAY[$chk_day]=='1')?"1":"0";
   	 if ($DAY['School_days']==0 and $CHK_WORK>=$st_start_line and $CHK_WORK<=$st_end_line  and $W>=1 and $W<=5) $chk_if='1';
     $chk_txt="<input type='hidden' name='day[$chk_day]' value='$chk_if'>";
   } else {
     $chk_if="0";
   }

   if ($W==0 and $D>1) $Mon_table.="<tr>";
   //上學日呈現背景
   $bg=($chk_if=='1')?"bgcolor='#FFFFCC'":"bgcolor='#DDDDDD'";
	 $Mon_table.="<td id='$chk_day' align='center' $bg>$D $chk_txt</td>";   
   if ($W==6) $Mon_table.="</tr>";    
     
 } // end for $Mon_days
 
 if ($W<6) {
  for ($j=$W+1;$j<=6;$j++) { $Mon_table.="<td bgcolor='#DDDDDD'>&nbsp;</td>";  }
 }
 $Mon_table.="</table>";
 
 echo $Mon_table;
 
 echo "</td>";
 if ($i%3==0) echo "</tr>";
} // end for $M=$d1[1]
} else {
	echo "<font color=red>請選擇要設定的年級！</font>";
} // end if ($_POST['class_year']!='')
?>
</table>	
	
<!-- 依月分日期列表勾選結束 -->	

	</td>
</tr>
<tr>
	<td align="center"  colspan=2>
	<input type="submit" name="do_key" value="<?php echo $postBtn ?>" onclick="if (document.myform.class_year.value=='') { return false; }">
	<font color=red size=2><?php echo $INFO;?></font>
	</td>
</tr>


</table>
</form>
</td>
</tr>
</table>

 <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class="main_body" >

<tr>
	<td  >學年學期</td>
	<td  >年級</td>
	<td  >上課日數</td>
	<td  >編修</td>
</tr>
<?php
$sql_select = "select seme_year_seme,days,class_year from seme_course_date where seme_year_seme='$seme_year_seme' order by seme_year_seme desc, class_year ";
$recordSet = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤",E_USER_ERROR);
while (!$recordSet->EOF) {

	$seme_year_seme = $recordSet->fields["seme_year_seme"];
	$days = $recordSet->fields["days"];
	$class_year = $recordSet->fields["class_year"];
	echo "<tr>
	<td CLASS=\"gendata\">$class_seme[$seme_year_seme]</td>
	<td CLASS=\"gendata\">$class_year</td>
	<td CLASS=\"gendata\">$days</td>
	<td CLASS=\"gendata\">
	<a href=\"$_SERVER[PHP_SELF]?do_key=edit&seme_year_seme=$seme_year_seme&class_year=$class_year\">修改</a>
	<a href=\"$_SERVER[PHP_SELF]?do_key=del&seme_year_seme=$seme_year_seme&class_year=$class_year\">刪除</a>
	</td>
	</tr>";

	$recordSet->MoveNext();
}

?>
</table>
<?php
//印出頁尾
foot();
?> 
<Script type="text/javascript">
	  $("td[id]").click(
    function() {
    	if ($(this).children("input").attr("value")=='1') {
		    $(this).children("input").attr("value","0");
		    $(this).css('background-color','#DDDDDD');
		   } else {
		    $(this).children("input").attr("value","1");
        $(this).css('background-color','#FFFFCC');
       }

    }
   );    
</Script>
