<?php
include "config.php";

sfs_check();

$SEX[1]="男";
$SEX[2]="女";

//秀出網頁
head("體適能管理 - 快速貼上身高體重");
$tool_bar=&make_menu($menu_p);
//列出選單
echo $tool_bar;

//程式開始
$seme_class=$_POST['seme_class'];
$stud_data=$_POST['stud_data'];

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}
?>

<form name="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<table border="0" width="100%">
 <tr>
  <td><br>本程式是為了能將健康中心的資料能快速匯入系統而設計，若貼上的資料格式有誤，匯入的資料則不保證百分之百正確，較保險的方法還是一筆一筆慢慢輸入。</td>
 </tr>	
 <tr>
  <td><br>匯入的資料會存入<font color="#FF0000"><?php echo curr_year()."學年第".curr_seme()."學期</font> 的學生資料";?><br></td>
 </tr>
 <tr>
 	 <td style="color:#800000">
 	 	請選擇資料要存入的班級：
 	 	<select name="seme_class" size="1" onchange="javascript:document.myform.submit()">
 	 		<option value="">請選擇班級</option>
 	 		<?php
 	 	
 	 		$query="select distinct seme_class from stud_seme where seme_year_seme='$c_curr_seme' order by seme_class";
 	 		$res=mysql_query($query);
 	 		while ($row=mysql_fetch_row($res)) {
 	 		 list($SEME_CLASS)=$row;
 	 		 $class_id=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),substr($SEME_CLASS,0,1),substr($SEME_CLASS,1,2));
 	 		 $query="select c_year,c_name,c_kind from school_class where class_id='$class_id'";
 	 		 $res_class=mysql_query($query);
 	 		 if (mysql_num_rows($res_class)) {
 	 		 list($c_year,$c_name,$c_kind)=mysql_fetch_row($res_class);
 	 		 ?>
 	 		  <option value="<?php echo $SEME_CLASS;?>"<?php if ($SEME_CLASS==$seme_class) echo " selected";?>><?php echo $school_kind_name[$c_year]."".$c_name."班";?></option>
 	 		 <?php
 	 		 } // end if
 	 		}
 	 		
 	 		?>
 	 </td>
 	</tr>
</table>
</form>	
	<?php
	if ($seme_class and $c_curr_seme) {
	?>
<form name="myform1" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">	
	<input type="hidden" name="seme_class" value="<?php echo $seme_class;?>">
	  <table border="0">
    <tr>
    	<td style="font-size:10pt">將資料由 EXCEL中複製/貼上，然後按送出即可，注意欄位對應(在EXCEL看的格式應如下所示，第6欄(含)後不計)：<br>
    		<table border="1" style="border-collapse:collapse" bordercolor="#000000">
    		 <tr>
    		  <td>座號</td>
    		  <td>姓名</td>
    		  <td>性別</td>
    		  <td>身高</td>
    		  <td>體重</td>
    		 </tr> 
    		 <tr>
    		  <td>1</td>
    		  <td>林xx</td>
    		  <td>男</td>
    		  <td>164.5</td>
    		  <td>53.6</td>
    		 </tr> 
    		 <tr>
    		  <td>1</td>
    		  <td>吳xx</td>
    		  <td>女</td>
    		  <td>148.4</td>
    		  <td>37.8</td>
    		 </tr> 
    		</table>
    系統只會截取每前五欄資料,並比對第1及第2欄,然後將第4及第5欄視為身高及體重.<br>
    		<textarea name="stud_data" cols="80" rows="10"></textarea>
    	</td>
    </tr>
    </table>
    <input type="submit" value="送出資料">(檢查座別姓名對應無誤即可送出)
<table border="1" style="border-collapse:collapse" bordercolor="#CCCCCC">
	<tr  bgcolor="#FFCCFF">
		<!--
		<td width="40" style="font-size:10pt" align="center">索引</td>
		-->
		<td width="30" style="font-size:10pt" align="center">座號</td>
		<td width="50" style="font-size:10pt" align="center">姓名</td>
		<td width="30" style="font-size:10pt" align="center">姓別</td>
		<td width="50" style="font-size:10pt" align="center">身高</td>
		<td width="50" style="font-size:10pt" align="center">體重</td>
		
		<td width="80" style="font-size:10pt" align="center">更新身高</td>
		<td width="80" style="font-size:10pt" align="center">更新體重</td>

		<td width="120" style="font-size:10pt" align="center">狀態</td>
	</tr>
	
<?php
//有資料的話先分析身高體重
if ($stud_data) {
	$data_arr=explode("\n",$stud_data);
	for ($i = 0 ; $i < count($data_arr); $i++ ) {
		//去掉前後空白
	 $data_arr[$i] = trim($data_arr[$i]);
	 //去掉跟隨別的擠在一塊的空白
   $data_arr[$i] = preg_replace('/\s(?=\s)/','', $data_arr[$i]);
   $data_arr[$i] = preg_replace('/[\n\r\t]/', ' ', $data_arr[$i]);

   //變成二維陣列
   $stud_arr_my[$i]=explode(" ",$data_arr[$i]); 
   //echo $stud_arr_my[$i][0].",".$stud_arr_my[$i][1].",".$stud_arr_my[$i][3].",".$stud_arr_my[$i][4]."<br>"; 
	}

}


		$query="select a.student_sn,a.stud_name,a.stud_sex,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class' and a.stud_study_cond in ($in_study) order by seme_num";
    $result=mysql_query($query);
    while ($row=mysql_fetch_row($result)) {
    	list($student_sn,$stud_name,$stud_sex,$seme_num)=$row;
    	?>
		<tr>
    	<!--
    	<td style="font-size:10pt" align="center"><?php echo $student_sn;?></td>
    	-->
    	<td style="font-size:10pt" align="center"><?php echo $seme_num;?></td>
    	<td style="font-size:10pt" align="center"><?php echo $stud_name;?></td>
    	<td style="font-size:10pt" align="center"><?php echo $SEX[$stud_sex];?></td>
    	<?php
     //檢查 fitness_data table 裡有沒有本學期資料
   			$query="select count(student_sn),tall,weigh from fitness_data where student_sn='".$student_sn."' and c_curr_seme='$c_curr_seme'";
   			$result_chk=mysql_query($query);
   			list($ok,$tall,$weigh)=mysql_fetch_row($result_chk);
   			//如果沒有資料, 自動insert 新的
   			if ($ok==0) {
   				if (mysql_query("insert into fitness_data (c_curr_seme,student_sn) values ('$c_curr_seme','".$student_sn."')")) {
					  $INFO= "=>尚無身高及體重資料";
					}else{
					  echo "<font color=#FF000>本學期體適能資料建立失敗，請洽系統管理員!</font>";
						exit();					  
					}
        }// end if $ok==0 有沒有建立資料
   			?>
   			<td style="font-size:10pt" align="center"><?php echo $tall;?></td>
   			<td style="font-size:10pt" align="center"><?php echo $weigh;?></td>
   			<?php

         if ($stud_data) {
					//檢查有沒有符合的資料
					$chk=0;
					for ($i=0;$i < count($data_arr); $i++ ) {
					 if ($stud_arr_my[$i][0]==$seme_num and $stud_arr_my[$i][1]==trim($stud_name)) {
					 	 $Newtall=$stud_arr_my[$i][3];
					 	 $Newweigh=$stud_arr_my[$i][4];
					 	  $chk=1;
					 	  $query_up="update fitness_data set tall='".$stud_arr_my[$i][3]."',weigh='".$stud_arr_my[$i][4]."' where student_sn='".$student_sn."' and c_curr_seme='".$c_curr_seme."'";
					 	  if (mysql_query($query_up)) {
					      $INFO="更新完成";
					    } else {
					      $INFO="更新失敗!";
					    }
					 	  break;
					 } 
         }// end for
         if ($chk==0) {
            $INFO="沒有比對到座號及姓名資料! 未更新!";
         }

        }//end if 有貼上資料
        ?>
		 	 <td align="center"><?php echo $Newtall;?> </td>
		 	 <td align="center"><?php echo $Newweigh;?> </td>
       <td><?php echo $INFO;?></td>
      </tr>
        <?php    	
    } // end while
    
    ?>
  </table>

    <?php
} // end if 
?>


</form>