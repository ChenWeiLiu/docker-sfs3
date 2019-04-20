<?php

// $Id: stud_demote.php 8494 2015-08-21 08:31:03Z smallduh $

// 載入設定檔
include "stud_move_config.php";
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
$stud_study_year = $sel_year-$sel_class_year+1+$IS_JHORES; //就讀年
$temp = $sel_year - $stud_study_year  ;

//按鍵處理
switch($do_key) {
	case $postMoveBtn :
		$update_ip = getip();
		$move_date = ChtoD($move_date);
		$move_c_date = ChtoD($move_c_date);
		if(abs(intval(substr($stud_class,0,1)) - intval(substr($demote_class,0,1)))>1) {
		  echo "升降級請勿超過(含)2級!";
		  exit();
		}
		
		if(intval(substr($stud_class,0,1)) < intval(substr($demote_class,0,1)))
			$move_kind = 9; //升級
		else
			$move_kind = 10; //降級
		//加入異動記錄
		$sql_select="select stud_id from stud_base where student_sn='$student_sn'";
		$res=$CONN->Execute($sql_select);
		$stud_id=$res->fields['stud_id'];		
		$sql_insert = "insert into stud_move (stud_id,move_kind,move_year_seme,move_date,move_c_unit,move_c_date,move_c_word,move_c_num,update_id,update_ip,update_time,student_sn) values ('$stud_id','$move_kind','$curr_seme','$move_date','$move_c_unit','$move_c_date','$move_c_word','$move_c_num','$update_id','$update_ip','".date("Y-m-d G:i:s")."','$student_sn')";
		//echo $sql_insert;
		//exit();
		$CONN->Execute($sql_insert) or die ($sql_insert);
		
		// 就讀年
		//$stud_study_year = $curr_year-substr($sel_year,0,1)+1 ;
		$tempyear = curr_year() - (substr($demote_class,0,1)-$IS_JHORES) +1;
		//取得原就讀年 2015.08.10 by smallduh  因10年學號重覆, 若不連同比對原入學年 , 會把 10年前學號重覆的學生也改掉
		$orgyear= curr_year() - (substr($stud_class,0,1)-$IS_JHORES) +1;

		$temp = curr_year() - $tempyear ;
		$query1 = "select max(curr_class_num) as mm from stud_base where curr_class_num like '$demote_class%' ";
		$result1 = $CONN->Execute($query1) or die($query1) ;
		$max_site_num=$result1->fields[0];
		$new_site_num=sprintf("%02d",substr($max_site_num,-2)+1);
		$num = $demote_class.$new_site_num;
		//echo $demote_class;
		//exit();
		//$sql_update = "update stud_base set stud_study_year ='$tempyear' ,curr_class_num = '$num' where stud_id='$stud_id' and stud_study_year='$orgyear'";
	  $sql_update = "update stud_base set stud_study_year ='$tempyear' ,curr_class_num = '$num' where student_sn='$student_sn'";
	//	echo "<BR>".$sql_update;
		$CONN->Execute($sql_update) or die ($sql_update);
		
		//學年記錄修改
		$c_curr_seme = sprintf("%04d",$curr_seme);
		$seme_class=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($demote_class,0,-2),substr($demote_class,-2));
		$rs=$CONN->Execute("select c_name from school_class where class_id='$seme_class' and enable=1");
		$seme_class_name=$rs->fields[c_name];
		//$query = "update stud_seme set seme_num='$new_site_num',seme_class='$demote_class' , seme_class_name='$seme_class_name' where seme_year_seme ='$c_curr_seme' and  seme_class ='$stud_class' and stud_id='$stud_id'";
		$query = "update stud_seme set seme_num='$new_site_num',seme_class='$demote_class' , seme_class_name='$seme_class_name' where seme_year_seme ='$c_curr_seme' and  seme_class ='$stud_class' and student_sn='$student_sn'";
		$CONN->Execute($query) or die($query);
	break;

	case "delete" :
	  //取出升降級資料
	  $query ="select * from stud_move where move_id ='$move_id'";
	  $res=$CONN->Execute($query);
	  $move_kind=$res->fields['move_kind'];
	  $student_sn=$res->fields['student_sn'];
	
		$query ="delete from stud_move where move_id ='$move_id'";
		$CONN->Execute($query)or die ($query);

		//學生基本資料表修改
		//$tempyear = $sel_year-substr($stud_class,0,-2)+1;
		$tempyear = curr_year() - (substr($stud_class,0,1)-$IS_JHORES) +1;
		
		$query1 = "select max(curr_class_num) as mm from stud_base where curr_class_num like '$stud_class%' ";
		$result1 = $CONN->Execute($query1) or die($query1) ;
		$max_site_num=$result1->fields[0];
		$new_site_num=sprintf("%02d",substr($max_site_num,-2)+1);
		$num = $stud_class.$new_site_num;
		//$sql_update = "update stud_base set stud_study_year ='$tempyear',curr_class_num = '$num' where stud_id='$stud_id'";
    $sql_update = "update stud_base set stud_study_year ='$tempyear',curr_class_num = '$num' where student_sn='$student_sn'";
    //echo $sql_update;
    //exit(); 
		$CONN->Execute($sql_update) or die ($sql_update);
		
		//學年記錄修改
		$c_curr_seme = sprintf("%04d",$curr_seme);
		$query = "update stud_seme set seme_num='$new_site_num',seme_class='$stud_class'  where seme_year_seme ='$c_curr_seme' and stud_id='$stud_id'";
		$CONN->Execute($query)or die($query);
	break;
}

//欄位資訊
$field_data = get_field_info("stud_move");	

//印出檔頭
head();
print_menu($student_menu_p);
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

	if(document.myform.move_kind.value=='')
	{	alert('未選擇類別');
		OK=false;
	}	

	return OK
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
    <form name ="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" >
  <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr>
<td class=title_mbody colspan=2 align=center > 學生升降級作業 </td>
</tr>
<tr>
<td class=title_sbody2 >選擇班級</td>
<td>
	<?php
		$curr_seme_temp = sprintf("%04d",$curr_seme);
		$class_list_p = class_base($curr_seme_temp); //班級列表
		$sel1 = new drop_select(); //選單類別

		if ($stud_class)			
			$sel1->id = $stud_class;
		$sel1->s_name = "stud_class"; //選單名稱	
		$sel1->arr = $class_list_p; //內容陣列
		$sel1->is_submit = true;
		$sel1->has_empty =false;
		$sel1->do_select();
	  ?>	    
    </td>
</tr>
<tr>
	<td class=title_sbody2>選擇學生</td>
	<td>
	<?php 
	// source in include/PLlib.php    
	$grid1 = new sfs_grid_menu;  //建立選單	   
	$grid1->bgcolor = $gridBgcolor;  // 顏色   
	$grid1->row = 1 ;	     //顯示筆數
	$grid1->width = 1 ;	     //顯示寬	
	$grid1->dispaly_nav = false; // 顯示下方按鈕
	$grid1->bgcolor ="FFFFFF";
	$grid1->nodata_name ="沒有學生";
	$grid1->top_option = "-- 選擇學生 --";
	$grid1->key_item = "student_sn";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color"); //男女生別
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select student_sn,stud_id,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num from stud_base where stud_study_year='$stud_study_year' and curr_class_num like '$stud_class%' and stud_study_cond=0 order by curr_class_num";   //SQL 命令 
	$grid1->do_query(); //執行命令

	$downstr = "<input type=hidden name=ckey value=\"$ckey\">";
	$grid1->print_grid($stud_id,$upstr,$downstr); // 顯示畫面    
  ?>	
	</td>
</tr>
<tr>
	<td class=title_sbody2>升(降)至年級</td>
	<td>
	<?php 
		$class_year=year_base();
		while ( list($key,$val)=each($class_year)) {
			if (intval($key) <> substr($stud_class,0,1))	
				$temp_y[]= intval($key);			
		}		
		$class_list_p = class_base('',$temp_y); //班級列表
		$sel1 = new drop_select(); //選單類別
		if ($stud_class)			
			$sel1->id = $demote_class;
		$sel1->s_name = "demote_class"; //選單名稱	
		$sel1->arr = $class_list_p; //內容陣列
				
		$sel1->has_empty =false;
		$sel1->do_select();
	  ?>	    
    
	
	</td>
	
</tr>
<tr>
	<td class=title_sbody2>生效日期</td>
	<td> 民國 <input type="text" size="10" maxlength="10" name="move_date" value="<?php echo DtoCh() ?>"></td>
</tr>

<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[move_c_unit][d_field_cname] ?></td>
	<td><input type="text" size="30" maxlength="30" name="move_c_unit" value="<?php echo $tea1->Record[move_c_unit] ?>"></td>
</tr>

<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[move_c_date][d_field_cname] ?></td>
	<td> 民國 <input type="text" size="10" maxlength="10" name="move_c_date"  value="<?php echo DtoCh() ?>"></td>
</tr>

<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[move_c_word][d_field_cname] ?></td>
	<td><input type="text" size="20" maxlength="20" name="move_c_word" value="<?php echo $tea1->Record[move_c_word] ?>"></td>
</tr>

<tr>
	<td align="right" CLASS="title_sbody1"><?php echo $field_data[move_c_num][d_field_cname] ?></td>
	<td><input type="text" size="14" maxlength="14" name="move_c_num" value="<?php echo $tea1->Record[move_c_num] ?>"></td>
</tr>

<tr>
    <td width="100%" align="center"  colspan="5" >
    <input type="hidden" name="update_id" value="<?php echo $_SESSION['session_log_id'] ?>">
    <?php	
    	echo "<input type=submit name=\"do_key\" value =\"$postMoveBtn\" onClick=\"return checkok();\">";   	
    ?>
    </td>
  </tr>
</table>
   　</td>
  </tr>
<TR>
	<TD>
	<?php
		reset($out_arr);
		while(list($tid,$tname)=each($demote_arr))
			$temp_move_kind .="a.move_kind = $tid or ";
		$temp_move_kind = substr($temp_move_kind,0,-3);
		
		//組合 SQL 語法
		//$query = "select a.*,b.stud_name,b.curr_class_num from stud_move a ,stud_base b where a.stud_id=b.stud_id and a.move_year_seme='$curr_seme'  and ( $temp_move_kind ) order by a.move_date desc ";
		$query = "select a.*,b.stud_name,b.curr_class_num from stud_move a ,stud_base b where a.student_sn=b.student_sn and a.move_year_seme='$curr_seme'  and ( $temp_move_kind ) order by a.move_date desc ";
    //echo $query;
    //exit();
		$result = $CONN->Execute($query) or die ($query);
		if (!$result->EOF) {
			echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"2\" bordercolorlight=\"#333354\" bordercolordark=\"#FFFFFF\"  width=\"100%\" class=main_body >";
			echo "<tr><td colspan=8 class=title_top1 align=center >本學期升降級學生</td></tr>";
			echo "
			<TR class=title_mbody >
				<TD>異動類別</TD>
				<TD>生效日期</TD>
				<TD>學號</TD>
				<TD>姓名</TD>
				<TD>班級</TD>
				<TD>核准單位</TD>
				<TD>字號</TD>
				<TD>編修</TD>
			</TR>";
		}

		while(!$result->EOF) {
			$move_id = $result->fields["move_id"];
			$move_kind = $result->fields["move_kind"];
			$stud_id = $result->fields["stud_id"];		
			$stud_name = $result->fields["stud_name"];
			$class_num = substr($result->fields["curr_class_num"],0,3);
			$stud_clss = $class_list_p[$class_num];
			$move_year_seme = $result->fields["move_year_seme"];
			$move_date = $result->fields["move_date"];
			$move_c_date = $result->fields["move_c_date"];
			$move_c_unit = $result->fields["move_c_unit"];
			$move_c_word = $result->fields["move_c_word"];
			$move_c_num = $result->fields["move_c_num"];
			$curr_seme_temp = sprintf("%03d",$curr_seme);
			$edit_data = $SFS_PATH_HTML."studentreg/stud_reg/stud_list.php?stud_id=$stud_id&sel=$class_num&curr_seme=$curr_seme_temp";
			echo ($i++ %2)?"<TR class=nom_1>":"<TR class=nom_2>";
			
			//依升降級, 分析取消時要回到哪一級
			if ($move_kind==9) {
				//升級, 要降回來
			  $stud_class=sprintf("%d%02d",substr($class_num,0,1)-1,substr($class_num,1,2));
			} elseif ($move_kind==10) {
				//降級, 要升回來
			  $stud_class=sprintf("%d%02d",substr($class_num,0,1)+1,substr($class_num,1,2));
			} else {
			  echo "發生錯誤!";
			  exit();
			}
			
			//$tempyear = substr($stud_id,0,2);
			$tempyear = curr_year() - (substr($stud_class,0,1)-$IS_JHORES) +1;
			$temp = curr_year() - $tempyear +1;
      
            

            $stud_class_cname[year]=substr($stud_class,0,1)."年";
            $back_class=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($stud_class,0,1),substr($stud_class,1,2));
            $back_rs=$CONN->Execute("select c_name from school_class where class_id='$back_class' and enable=1");
            $stud_class_cname[ben]=$back_rs->fields[c_name]."班";
            $stud_class_c_name=$stud_class_cname[year].$stud_class_cname[ben];
			echo "			
					<TD>$demote_arr[$move_kind]</TD>
					<TD>$move_date</TD>
					<TD>$stud_id</TD>
					<TD>$stud_name</TD>
					<TD>$stud_clss</TD>					
					<TD>$move_c_unit</TD>
					<TD>$move_c_date $move_word $move_c_num</TD>
				<TD><a href=\"{$_SERVER['PHP_SELF']}?do_key=delete&move_id=$move_id&stud_id=$stud_id&stud_class=$stud_class\" onClick=\"return confirm('確定取消 $stud_name 記錄 ?\\n$stud_name 將被編回 $stud_class_c_name\\n若將被編回的班級不正確，請自行調整『選擇班級』欄位');\">取消</a></TD>
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
