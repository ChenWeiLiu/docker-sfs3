<?php

//填寫競賽記錄表單
function form_race_record($race_record) {
	global $level_array,$squid_array,$R_select,$N_select,$sch_id;	
?>
<table border="1" width="750" style="border-collapse:collapse" cellpadding="3" cellspacing="3" bordercolor="#c0c0c0" bgcolor="#FFFFCC">
	<tr>
	 <td bgcolor="#CCFFCC">*學年度</td>
	  <td bgcolor="#DCFFDC" colspan="3">
	 	 <input type="text" name="year" value="<?php echo $race_record['year'];?>" size="3">
	 	</td>
	 </td>
	</tr>
	<tr>
	 <td bgcolor="#CCFFCC">*類別</td>
	 <td bgcolor="#DCFFDC" colspan="3">
	 		<select name="nature" <?php if (substr($sch_id,0,2)=='13') echo "onchange=\"SelectR_name(1)\"";?>>
	 	  		<option value="">請選擇類別</option>
	 	   <?php
	 	    foreach ($N_select as $k=>$v) {
	 	     ?>
	 	     	<option value="<?php echo $v;?>"<?php if ($race_record['nature']==$v) echo " selected";?>><?php echo $v;?></option>
	 	     <?php
	 	    }
	 	   ?>	 		
	 		</select>
	 </td>	 
	</tr>
	
	<tr>
	 <td width="75">競賽範圍</td>
	 <td width="300">
	 		<select size="1" name="level" <?php if (substr($sch_id,0,2)=='13') echo "onchange=\"SelectR_name(1)\"";?>>
	 	   <?php
	 	    foreach ($level_array as $k=>$v) {
	 	     ?>
	 	     <option value="<?php echo $k;?>"<?php if ($race_record['level']==$k) echo " selected";?>><?php echo $v;?></option>
	 	     <?php
	 	    }
	 	   ?>	 		
	 		</select>	   
	 	
	 	</td>
		<td width="75">競賽性質</td>
	 	<td width="200">
	     <input type="radio" name="squad" value="1"<?php if ($race_record['squad']==1) echo " checked"; ?>>個人賽
	     <input type="radio" name="squad" value="2"<?php if ($race_record['squad']==2) echo " checked"; ?>>團體賽

	 	</td>
	</tr>	
	<tr>
	 <td>競賽名稱</td>
	 <td>
	 	<?php 
	 	 if (substr($sch_id,0,2)=='13') {
			?>
	 		<select size="1" name="r_name" onchange="Check_select()">
	 			<?php
	 			if ($race_record['name']!="") {
	 			?>
	 				<Option value="<?php echo $race_record['name'];?>" selected><?php echo $race_record['name'];?></option>			
	 		  <?php
	 			} else {
	 			?>
	 			<Option value="">請選擇競賽名稱</option>			
	 		 <?php } ?>
			</select>
			<?php
	 	 } else {
	     ?>
	     	<input type="text" name="r_name" value="<?php echo $race_record['name'];?>" size="30">
	     <?php	 
	 	 }
	 	?>
	 </td>
	 <td>得獎名次</td>
	 <td>
	 			<select size="1" name="rank">
	 				<option value=''>---</option>
	 				<?php
	 				foreach ($R_select as $R) {
	 					?>
	 					<option value="<?php echo $R;?>"<?php if ($R==$race_record['rank']) echo " selected";?>><?php echo $R;?></option>
	 					<?php	 				
	 				}	 				
	 				?>
	 			</select>
	 
	 </td>
	</tr>
	<tr>	
	 <td>證書字號</td>
	 <td>
	 	<input type="text" name="word" value="<?php echo $race_record['word'];?>" size="20">
	 </td>		 
	 <td>證書日期</td>
	 <td><input type="text" name="certificate_date" value="<?php echo $race_record['certificate_date'];?>" size="12"></td>
	</tr>
	<tr>
		<td>高中免試權重</td>
		<td>
		  <input type="text" name="weight" value="<?php echo $race_record['weight'];?>" size="6">
		</td>
	  <td>五專免試權重</td>
	  <td>
	    <input type="text" name="weight_tech" value="<?php echo $race_record['weight_tech'];?>" size="6">
	  </td>
	</tr>
	<tr>
	 <td>主辦單位</td>
	 <td><input type="text" name="sponsor" value="<?php echo $race_record['sponsor'];?>" size="30"></td>
	 <td>備註</td>
	 <td><input type="text" name="memo" value="<?php echo $race_record['memo'];?>" size="30"></td>
	</tr>
</table>

<?php
}


//讀取競賽記錄 二維陣列 傳入條件, 是否限學期,教師, 學生, 沒有傳入則全部取出
function get_race_record($c_curr_seme='',$teacher_sn='',$student_sn='') {
	
 global $CONN;
 
 $students=array();
 $sql_limit=array();

//是否限日期 
 if ($c_curr_seme!="") {
  //計算該學期的日期區間
 $year=sprintf("%d",substr($c_curr_seme,0,3));
 $seme=substr($c_curr_seme,-1);
//起始日
 $sql="select day from school_day where year='$year' and seme='$seme' and day_kind='start'";
 /* 原始 php 的 MySQL 函式 
 $res=mysql_query($sql);
 list($st_date)=mysql_fetch_row($res);
 */
 /* ADODB 寫法 */
 $res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
 $st_date=$res->fields[0];
 
 //結束日
 $sql="select day from school_day where year='$year' and seme='$seme' and day_kind='end'";
 /* 原始 php 的 MySQL 函式 
 $res=mysql_query($sql);
 list($end_date)=mysql_fetch_row($res);
 */
  /* ADODB 寫法 */
 $res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
 $end_date=$res->fields[0];
 
 $sql_limit[]="certificate_date>='$st_date' and certificate_date<='$end_date'";

}
//是否限老師
if ($teacher_sn!="") {
  $sql_limit[]="update_sn='$teacher_sn'";
}

//是否限學生
if ($student_sn!="") {
  $sql_limit[]="student_sn='$student_sn'";
}

 //組合 sql 指令 
 $query="select * from `career_race`";
 $i=0;
 //把 sql 條件加上去
 foreach ($sql_limit as $v) {
  $i++;
  $query.=($i==1)?" where ".$v:" and ".$v;
 }
 $query.=" order by certificate_date";
 
 /* php 的MySQL函式寫法 
 $res=mysql_query($query);
 while ($row=mysql_fetch_array($res,1)) {
*/
 /* ADODB 的寫法 */
 $res=$CONN->Execute($query) or die("SQL錯誤:$query");;
 while ($row=$res->FetchRow()) {			//讀取一筆, 並放入陣列 $row 中 

   $student_sn=$row['student_sn'];
   $sn=$row['sn'];
   
   //讀入競賽資料
   foreach($row as $k=>$v) {
     $students[$sn][$k]=$v;
   }
   
   //讀入學生目前班級資料
   //$sql="select stud_name,curr_class_num from stud_base where student_sn='$student_sn'";
   //$sql新增stud_study_cond
   $sql="select stud_name,curr_class_num,stud_study_cond from stud_base where student_sn='$student_sn'";
   /* php 的 MySQL 函式寫法  
   $res_stud=mysql_query($sql);
   $row_stud=mysql_fetch_array($res_stud,1);
   $students[$sn]['stud_name']=$row_stud['stud_name'];
	 $students[$sn]['seme_class']=substr($row_stud['curr_class_num'],0,3);	   
	 $students[$sn]['seme_num']=substr($row_stud['curr_class_num'],3,2);	 
  */
   
   /* ADODB 的寫法 */
   $res_stud=$CONN->Execute($sql) or die("SQL錯誤:$sql");
   $students[$sn]['stud_name']=$res_stud->Fields('stud_name');
	 $students[$sn]['seme_class']=substr($res_stud->Fields('curr_class_num'),0,3);	   
	 $students[$sn]['seme_num']=substr($res_stud->Fields('curr_class_num'),3,2);	
     //新增 $students[$sn]['stud_study_cond']
	 $students[$sn]['stud_study_cond']=($res_stud->Fields('stud_study_cond')==5)?"畢業":"在學";
	   
 } // end while
 
 return $students;

} // end function 

//列出競賽記錄
function list_race_record($race_record,$del_box=0,$update_url=0,$post_action="") {
	global $level_array,$squad_array,$school_kind_name;
	global $module_manager;
 ?>
 	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>
  	<tr align='center' bgcolor='#ffcccc'>
  		<?php
  		if ($del_box) {
  		?>
  		<td width="5"><input type="checkbox" name="check_all" value="1" onclick="check_select('check_all','check_it');"></td>
		  <?php 
		  } 
  		if ($update_url) {
  		?>
  		<td width="30" style="font-size:10pt">操作</td>
		  <?php } ?>

			<td width="15">NO.</td>
			<td width="15">狀況</td>
			<td width="60">班級</td>
			<td width="30" style="font-size:10pt">座號</td>
			<td width="80">姓名</td>
			<td bgcolor="#ccffcc">學年度</td><td bgcolor="#ccffcc">競賽類別</td>
			<td width="120" colspan="2">範圍性質</td><td>競賽名稱</td><td>得獎名次</td><td>證書日期</td><td>主辦單位</td>
			<td>字號</td><td bgcolor="#ccffcc">高中免試權重</td><td bgcolor="#ccffcc">五專免試權重</td><td>備註</td>
		</tr>
			<?php
			$i=0;
			foreach ($race_record as $sn=>$race) {
	    $i++;
	    ?>
	    <tr<?php if ($_POST['act']=='edit' and $_POST['option1']==$sn) echo " bgcolor='#FFFF00'";?>>
		  		<?php
		  		if ($del_box) {
  				?>
  				<td align="center"><input type="checkbox" name="check_it[]" value="<?php echo $race['sn'];?>"></td>
				  <?php 
				  }			
  				if ($update_url) {
  				?>
  				<td>
  					<img src="images/edit.png" style="cursor:hand" onclick="<?php if ($post_action!="") { echo "document.myform.action='$post_action';";} ?>document.myform.act.value='edit';document.myform.option1.value='<?php echo $race['sn'];?>';document.myform.submit();">
  					<img src="images/del.png"  style="cursor:hand" onclick="if (confirm('您確定要刪除嗎?')) { document.myform.act.value='DeleteOne'; document.myform.option1.value='<?php echo $race['sn'];?>'; document.myform.submit(); } ">
  				</td>
		  		<?php } ?>
					<td><?php echo $i;?></td>
					<td><?php echo $race['stud_study_cond'];?></td>
					<td><?php echo $school_kind_name[substr($race['seme_class'],0,1)].sprintf('%d',substr($race['seme_class'],1,2))."班";?></td>
					<td><?php echo $race['seme_num'];?></td>
					<td><?php echo $race['stud_name'];?></td>
					<td><?php echo $race['year'];?></td>
					<td align='left'><?php echo $race['nature'];?></td>
					<td><?php echo $level_array[$race['level']];?></td>
					<td><?php echo $squad_array[$race['squad']];?></td>
					<td align='left'><?php echo $race['name'];?></td>
					<td><?php echo $race['rank'];?></td>
					<td><?php echo $race['certificate_date'];?></td>
					<td align='left'><?php echo $race['sponsor'];?></td>
					<td align='left'><?php echo $race['word'];?></td>
					<td align='center'><?php echo $race['weight'];?></td>
					<td align='center'><?php echo $race['weight_tech'];?></td>
					<td align='left'><?php echo $race['memo'];?></td>
			</tr>	    
	    <?php
			}
	?>
	
	</table>
	<?php
} // end functions
?>
 
<Script Language="JavaScript">
 function check_select(SOURCE,STR) {
	var j=0;
	while (j < document.myform.elements.length)  {
	 if (document.myform.elements[j].name==SOURCE) {
	  if (document.myform.elements[j].checked) {
	   k=1;
	  } else {
	   k=0;
	  }	
	 }
	 	j++;
	}
	
  var i =0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,STR.length)==STR) {
      document.myform.elements[i].checked=k;
    }
    i++;
  }
 } // end function
 
</Script>
