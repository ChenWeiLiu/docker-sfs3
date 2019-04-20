<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("社團活動 - 列印尚未填寫自我省思的名單");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

if ($_SESSION['session_who'] != "教師") {
	echo "很抱歉！本功能模組為教師專用！";
	exit();
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//取得學期社團設定
$SETUP=get_club_setup($c_curr_seme);

//目前選定年級，100指未指定
$c_curr_class=$_POST['c_curr_class'];

//取得任教班級代號
$class_num = get_teach_class();

//檢驗是否有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}

//若有管理權, 可檢查每一個班
?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="club_class" value="">
<table border="0" width="800">
	<tr>
	  <!--左列視窗, 學期社團列表 -->
	  <td width="100%" valign="top" style="color:#FF00FF;font-size:10pt">
	  	<select name='c_curr_class' onchange="document.myform.submit()">
	  		<option value="" style="color:#FF00FF">請選擇..</option>
	  	<?php
			    $class_year_array=get_class_year_array(sprintf('%d',substr($c_curr_seme,0,3)),sprintf('%d',substr($c_curr_seme,-1)));
                foreach ($class_year_array as $K=>$class_year_name) {
                	?>
                	<option value="<?php echo $K;?>" style="color:#FF00FF;font-size:10pt" <?php if ($c_curr_class==$K) echo "selected";?>><?php echo $school_kind_name[$K];?>級(<?php echo get_club_num($c_curr_seme,$K);?>)</option>
                	<?php
                }	
			?>
		</select>全年級各班未填寫自我省思的名單
	  </td>
	</tr>
</table>
</form>
<?php

	  //顯示某班級名單 ================================================================
	  
	  if ($_POST['c_curr_class']!="") {
	  	$CLASS_name=$school_kind_name[$c_curr_class];
	  	//先取出本學期所有學生, 逐筆檢驗
     $query="select student_sn,seme_class,seme_num from stud_seme where seme_year_seme='$c_curr_seme' and seme_class like '".$c_curr_class."%%' order by seme_class,seme_num";
     $result=mysql_query($query);
			while ($row=mysql_fetch_row($result)) {
			  list($student_sn,$seme_class,$seme_num)=$row;
			  //檢查有沒有寫自我省思
			   $query_fb="select stud_feedback from association where seme_year_seme='$c_curr_seme' and student_sn='$student_sn' and club_sn!=''"; 
			   $res=mysql_query($query_fb);
			   if (mysql_num_rows($res)==0) {
			    $student_not_arrange[$seme_class][$seme_num]=$student_sn; //未編班名單
			    $student_not_feedback[$seme_class][$seme_num]=$student_sn; //未寫省思名單
			   } else {
			    list($fb)=mysql_fetch_row($res);
			    if ($fb=='') {	
			     $student_not_feedback[$seme_class][$seme_num]=$student_sn; //未寫省思名單
			    }
			   }
	   }
 		  foreach ($student_not_feedback as $class=>$STUDENT) {
		 	  echo "<br><font color='#0000FF'>※".$CLASS_name.sprintf('%d',substr($class,1,2))."班 未寫社團自我省思名單：<br>";
		 	  ?>
		 	  <table border="0" width="800">
		 	  	<?php
		 	  	$i=0;
		 	  	 foreach ($STUDENT as $num=>$student_sn) {
							$i++;
							if ($i%10==1) echo "<tr>";
							 echo "<td style='font-size:10pt'>".$num.".".get_stud_name($student_sn)."</td>";
							if ($i%10==0) echo "</tr>";
		 	  	 }
		 	  	?>
		 	  </table>
		 	  <?php
		 	  if (count($student_not_arrange[$class])>0) {
		 	   echo "<font color=red>注意! 本班未參加社團活動名單：";
		 	   foreach ($student_not_arrange[$class] as $student_sn) {
		 	   	 echo " ".get_stud_name($student_sn);
		 	   
		 	   }
		 	  }
		  }

	   
	   
	  } // end if ($_POST['c_curr_class']!="")
	  


		?>
	  
