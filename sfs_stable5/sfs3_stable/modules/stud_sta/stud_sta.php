<?php

// $Id: stud_sta.php 8952 2016-08-29 02:23:59Z infodaes $

// 載入設定檔
include "config.php";
include  "sfs_oo_date2.php";
// 認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}


if(!$stud_class)
	$stud_class =$default_begin_class; //預設班級

$curr_seme = curr_year().curr_seme(); //現在學年學期
$sel_year = curr_year(); //選擇學年
$sel_seme = curr_seme(); //選擇學期
$sel_class_year = substr($stud_class,0,1); //選擇年級
$sel_class_name = substr($stud_class,-2); //選擇班級
$stud_study_year = $sel_year-$sel_class_year+1; //就讀年
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
if ($stud_id) {
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$stud_id'";
	$res=$CONN->Execute($query);
	$student_sn=$res->fields['student_sn'];
}

//按鍵處理
switch($key) {
	case $postProve :
		$set_ip = getip();
		$prove_date = $pro_date;
		$move_c_date = $reward_ndate;
		if ($purpose==''){
		   $purpose='　';
		   }
		//$reward_div="1";
		//加入異動記錄 ChtoD()
		$sql_insert = "insert into stud_sta (prove_id,stud_id,prove_year_seme,purpose,prove_date,set_id,set_ip,prove_cancel,student_sn) values ('$prove_id','$stud_id','$curr_seme','$purpose','$prove_date','$set_id','$set_ip','$prove_cancel','$student_sn')";
		$CONN->Execute($sql_insert) or die ($sql_insert);
		
		//if($reward_kind == "99") { //刪除
		//	$sql_update = "delete from reward where stud_id='$stud_id'";
		//}
		//else
			//$sql_update = "update reward set stud_study_cond ='$reward_kind' where stud_id='$stud_id'";
		//$CONN->Execute($sql_update) or die ($sql_update);
	break;

	case "cancel" :
		$prove_cancel=$_GET[prove_cancel];
		
		//字串轉整數
		$prove_id=(integer)($prove_id);
		


		$sql_update = "update stud_sta  set prove_cancel=$prove_cancel where prove_id=$prove_id";
		$CONN->Execute($sql_update) or die ($sql_update);	
	
    break;
    	
    	case "print" :
			
    break;
}

//欄位資訊
$field_data = get_field_info("stud_sta");

// 日期函式
$seldate = new date_class("myform");
$seldate->demo ="";

//日期檢查javascript 函式
$seldate->date_javascript();

//生日
$prove_date = $seldate->date_add("pro_date",$pro_date);

$seldate->do_check();

//印出檔頭
head();
echo make_menu($school_menu_p);
//print_menu($student_menu_p);
?>

<script language="JavaScript">
function checkok()
{
	
	var OK=true;	
	if(document.myform.stud_class.value==0)
	{	alert('未選擇班級');
		OK=false;
	}	
	if(document.myform.stud_id.value=='')
	{	alert('未選擇學生');
		OK=false;
	}	

	
	if (OK == true){
		OK=date_check();
	   }
	
	return OK;
	
	
	
}


function setfocus(element) {
	element.focus();
 return;
}
//-->
</script>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="myform" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post" >
  <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
<td class=title_mbody colspan=2 align=center > 學生在學證明書 </td>
</tr>
<tr>
<td align="right" class=title_sbody2  >　　　　　選擇班級</td>
<td>
	<?php 
		//列出班級		
		echo  get_class_select($sel_year,$sel_seme,"","stud_class","this.form.submit",$stud_class);

	  ?>	    
    </td>
</tr>
<tr>
	<td class=title_sbody2>選擇學生</td>
	<td>
	<?php 
	// source in include/PLlib.php    
	$temp_arr = explode("_",$stud_class);
	$temp_class = intval($temp_arr[2]).$temp_arr[3];
	$grid1 = new sfs_grid_menu;  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = 1 ;	     //顯示筆數
	$grid1->width = 1 ;	     //顯示寬	
	$grid1->dispaly_nav = false; // 顯示下方按鈕
	$grid1->bgcolor ="FFFFFF";
	$grid1->nodata_name ="沒有學生";
	$grid1->top_option = "-- 選擇學生 --";
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select stud_id,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num from stud_base where  curr_class_num like '$temp_class%' and stud_study_cond=0 order by curr_class_num";   //SQL 命令 
	$grid1->do_query(); //執行命令
//	echo $grid1->sql_str;
	$downstr = "<input type=hidden name=ckey value=\"$ckey\">";
	$grid1->print_grid($stud_id,$upstr,$downstr); // 顯示畫面    
	
  ?>	
	</td>
</tr>

<tr>
	<td align="right" CLASS="title_sbody2">證明目的</td>
	<td><input type="text" size="30" maxlength="30" name="purpose" value="<?php echo $tea1->Record[purpose] ?>"></td>
</tr>

<tr>
	<td class=title_sbody2>證明日期</td>
	
	<td><?php echo "$prove_date";?>
</tr>

<tr>
    <td width="100%" align="center"  colspan="5" >
    <input type="hidden" name="set_id" value="<?php echo $_SESSION['session_log_id'] ?>">

    <?php	
    	echo "<input type=submit name=key value =\"$postProve\" onClick=\"return checkok();\">";   	
    ?>
    </td>
  </tr>
</table>
   　</td>
  </tr>
<TR>
	<TD>
	<?php
		//reset($reward_good_arr);
		//while(list($tid,$tname)=each($reward_good_arr))
		//	$temp_reward_kind .="a.reward_kind = $tid or ";
		//$temp_reward_kind = substr($temp_reward_kind,0,-3);
		$query = "select a.*,b.stud_name,b.curr_class_num from stud_sta a ,stud_base b where a.student_sn=b.student_sn and a.prove_year_seme='$curr_seme' order by a.prove_date";

		$result = $CONN->Execute($query) or die ($query);
		if (!$result->EOF) {
			echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\" bordercolorlight=\"#333354\" bordercolordark=\"#FFFFFF\"  width=\"100%\" class=main_body >";
			echo "<tr><td colspan=9 class=title_top1 align=center >本學期開立之在學證明</td></tr>";
			echo "
			<TR class=title_mbody >
				<TD>文號</TD>
				<TD>學號</TD>
				<TD>姓名</TD>
				<TD>班級</TD>
				<TD>證明目的</TD>
				<TD>證明日期</TD>
				<TD>效力</TD>
				<TD>列印</TD>
			</TR>";
		}
		$class_list_p = class_base();
		while(!$result->EOF) {
			$prove_id = $result->fields["prove_id"];
			$prove_id2 = sprintf("%03d",$prove_id);
			$stud_id = $result->fields["stud_id"];		
			$stud_name = $result->fields["stud_name"];
			$class_num = substr($result->fields["curr_class_num"],0,3);
			$stud_clss = $class_list_p[$class_num];
			$prove_year_seme = $result->fields["prove_year_seme"];
			$prove_date = DtoCh($result->fields["prove_date"]);
			$purpose = $result->fields["purpose"];
			$prove_cancel = $result->fields["prove_cancel"];
			$curr_seme_temp = sprintf("%03d",$curr_seme);
			echo ($i++ %2)?"<TR class=nom_1>":"<TR class=nom_2>";
			echo "			
					<TD>$prove_id2</TD>
					<TD>$stud_id</TD>
					<TD>$stud_name</TD>
					<TD>$stud_clss</TD>
					<TD>$purpose</TD>
					<TD>$prove_date</TD>";
			if ($prove_cancel ==0) {
				$prove_sta= "<a href=\"stud_sta.php?key=cancel&stud_id=$stud_id&prove_id=$prove_id&prove_cancel=1\" onClick=\"return confirm('確定將 $stud_name 在學證明書作廢 ？');\">有效</a>"; 
				}
			else  {
				$prove_sta= "<a href=\"stud_sta.php?key=cancel&stud_id=$stud_id&prove_id=$prove_id&prove_cancel=0\" onClick=\"return confirm('確定使 $stud_name 在學證明書生效 ？');\"><font color=red>作廢</a>";
				}
			echo "<td>$prove_sta</td>";
			echo "<td><a href=\"stud_sta_rep.php?stud_id=$stud_id&prove_id=$prove_id\"  onClick=\"return confirm('確定列印 $stud_name 在學證明書 ？');\">證明書</a></TD>
				</TR>";
		
			$result->MoveNext();
		}
	?>
	</table>
	</TD>
</TR>
<TR>
	<TD></TD>
</TR>

</table>
</form>

<?php foot(); ?>
