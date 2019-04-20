<?php

// =====<< 以下為各種 function >>=====================================================================================

//服務項目表單===================================================================================================================
function service_table ($sn="",$year_seme="",$service_date="",$department="",$sponsor="",$item="其他類",$memo="") {
	global $ITEM;
	
	$minday=(substr($year_seme,3,1)=='1')?(substr($year_seme,0,3)+1911).'0801':(substr($year_seme,0,3)+1912).'0201';
	$maxday=(substr($year_seme,3,1)=='1')?(substr($year_seme,0,3)+1912).'0131':(substr($year_seme,0,3)+1912).'0731';
	
	$maxday=($maxday>date('Ymd'))?date('Ymd'):$maxday;
	
	if ($service_date=='') $service_date=date("Y-m-d");
	
	//取得校內各單位部門資料
 ?>
  <input type="hidden" name="save" value="0">
 
  <table border="0" width="100%" style="border-collapse:collapse" bordercolor="#800000" cellpadding="3">
  	<?php
  	//若為新建資料, 列出表單, 否則僅列出資料供查看
  	if ($sn=="" or $sn=="new" or $_POST['mode']=='update_service') {
  	?>
   <tr>
    <td width="70">服務日期</td><td>
    	<input type="text" name="service_date" id="service_date" size="10" value="<?php echo $service_date;?>">
 		<script type="text/javascript">
		new Calendar({
  		    inputField: "service_date",
   		    dateFormat: "%Y-%m-%d",
    	    trigger: "service_date",
 	        min: <?php echo $minday;?>,
    			max: <?php echo $maxday;?>,
    	    bottomBar: true,
    	    weekNumbers: false,
    	    showTime: 24,
    	    onSelect: function() {this.hide();}
		    });
		</script>
    </td>
   </tr>
   <tr>
    <td width="70">登錄單位</td><td>
    <select name="department" size="1" onchange="document.myform.sponsor.value=this.options[ this.selectedIndex ].text ">
   	<?php
   	
    $sql_select = "select room_id,room_name from school_room where enable='1'";
	  $result = mysql_query($sql_select);
	
	while ($row=mysql_fetch_row($result)) {
		list($room_id,$room_name)=$row;
		?>
		<option value="<?php echo $room_id;?>" <?php if ($room_id==$department) echo " selected";?>><?php echo $room_name;?></option>
	<?php
	} // end while
	?>
	</select>
    </td>
   </tr>
   <tr>
    <td width="70">主辦單位</td>
    <td><input type="text" name="sponsor" value="<?php echo $sponsor;?>"></td>
   </tr>
   <tr>
    <td width="70">服務類型</td><td>
    	    <select name="item" size="1">
						<?php 
						  $c=0;
						  foreach ($ITEM as $K) {
						   ?>
									<option value="<?php echo $K;?>" <?php if ($K==$item) { echo " selected"; $c=1;}?>><?php echo $K;?></option>
						   <?php
						  }
						    if ($c==0 and $item!='') {
						    	?>
						    	<option value="<?php echo $item;?>"  selected><?php echo $item;?></option>
						    	<?php
						    }

						?>
					</select>	
   	</td>
   </tr>
   <tr>
    <td width="70">服務內容</td><td><input type="text" name="memo" size="40"  value="<?php echo $memo;?>"></td>
   </tr>
   <?php
   } else{
   	
   	?>
   	<tr><td width="70">服務日期</td><td><?php echo $service_date;?><input type="hidden" name="service_date" value="<?php echo $service_date;?>"></td></tr>
   	<tr><td width="70">登錄單位</td><td><?php echo getPostRoom($department);?></td></tr>
   	<tr><td width="70">主辦單位</td><td><?php echo $sponsor;?></td></tr>
   	<tr><td width="70">服務類型</td><td><?php echo $item;?><input type="hidden" name="item" value="<?php echo $item;?>"></td></tr>
   	<tr><td width="70">服務內容</td><td><?php echo $memo;?><input type="hidden" name="memo" value="<?php echo $memo;?>"></td></tr> 
   	<input type="hidden" name="check_del" value="">  	
	<?php
   } // end if  $sn==""
   ?>
   <tr>
    <td width="70">服務時間</td><td><input type="text" name="init_time" size="5" value="<?php echo $_POST['init_time'];?>">分鐘<input type="button" value="全體預填此時間" onclick="tagtime()"></td>
   </tr>

  </table>
 <?php
} // end function

//製作勾選學生的表格 =====================================================================================================================
function student_select($c_curr_class) {
	//傳進 c_curr_class  100_2_07_01 學年度_學期_年級_班
	$seme_year_seme=substr($c_curr_class,0,3).substr($c_curr_class,4,1);
	$seme_class=sprintf("%d",substr($c_curr_class,6,2).substr($c_curr_class,9,2));
	$query="select a.seme_num,a.student_sn,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and a.student_sn=b.student_sn and (b.stud_study_cond=0 or b.stud_study_cond=5) order by a.seme_num";
	$result=mysql_query($query)
	
	
?>
    <table border="1" bordercolor="#800000" style="border-collapse:collapse" width="800">
  <?php
  $i=0;
  while ($row=mysql_fetch_array($result)) {
  		$i++;
  		if ($i%5==1) echo "<tr>";
  		//檢查是否已有此生
  		 if ($_GET['sn']!="" and $_GET['sn']!="new" and check_exist_service_stud($_GET['sn'],$row['student_sn'])) {
  		 	?>
  		<td width="150" style="font-size:10pt" bgcolor="#FFCCCC">
  			‧
  			<?php echo $row['seme_num'].".".$row['stud_name'];?>
  			(<font color=blue><?php echo getService_min($_GET['sn'],$row['student_sn']);?></font>分鐘)<br>
  			註記：<font color="red"><?php echo getService_studmemo($_GET['sn'],$row['student_sn']);?></font>
  		</td>
  		 	
  		 	<?php
  		 } else {
  	?>
  		<td width="150" style="font-size:10pt">
  			<input type='checkbox' name='selected_stud[<?php echo $row['student_sn'];?>]' value='<?php echo $row['stud_name'];?>' id='stud_selected'>
  			<?php echo $row['seme_num'].".".$row['stud_name'];?>
  			(<input type="text" style="font-size:9pt;color:#0000FF" name="time_stud[<?php echo $row['student_sn'];?>]" size="2">分鐘)<br>
  			註記：<input type="text" name="studmemo[<?php echo $row['student_sn'];?>]" size="12" style="font-size:9pt;color:#0000FF">
  		</td>
    <?php
      } // end if check_exist_service_stud
    if ($i%5==0) echo "</tr>";
   } // end while
    if ($i%5>0) {
     for ($j=$i%5+1;$j<=5;$j++) { echo "<td></td>"; }
     echo "</tr>";
    }
  ?>
  
  </table>
<?php
} // end function

//列出本學期已登錄的學習服務供選擇=============================================================================================================
function list_pastservice($year_seme) {
	$confirm[0]="<font color='#CCCCCC'>無<font>";
	$confirm[1]="<font color='#FF0000'>V</font>";
	$query="select * from stud_service where year_seme='$year_seme' and update_sn='".$_SESSION['session_tea_sn']."' order by service_date desc";
	$result=mysql_query($query);
	?>
  <table border="1" bordercolor="#000000" style="border-collapse:collapse" width="100%">
	 <tr>
	 <td valign="top">
	<table border="0" widtn="100%">
	 	<tr bgcolor="#FFCCFF">
	  		<td width="70" style="font-size:9pt">日期</td>
	  		<td width="60" style="font-size:9pt">主辦單位</td>
	  		<td width="70" style="font-size:8pt">服務類型</td>
	  		<td width="140" style="font-size:9pt">服務內容</td>
	  		<td style="font-size:9pt" width="30">人數</td>
	  		<td style="font-size:9pt" width="50">認證</td>
	 	</tr>
	 </table>
	 	<div style="OVERFLOW:auto; HEIGHT: 200px; " border="1">
			<table border="0" widtn="400" cellspacing="0">
				<?php
				while ($row=mysql_fetch_array($result)) {
				$stud_num=getService_num($row['sn']);
				?>
				<tr bgcolor="#FFFFFF" style="cursor:hand" onclick="window.location='<?php echo $_SERVER['PHP_SELF'];?>?sn=<?php echo $row['sn'];?>'" onmouseover="setPointer(this, 'over', '#FFFFFF', '#CCFFCC', '#FFCC99')" onmouseout="setPointer(this, 'out', '#FFFFFF', '#CCFFCC', '#FFCC99')" onmousedown="setPointer(this, 'click', '#FFFFFF', '#CCFFCC', '#FFCC99')" <?php if ($row['sn']==$_GET['sn']) echo "style='color:#FF0000'";?>>
		 			<td width="70" style="font-size:9pt"><?php echo $row['service_date'];?></td>
					<td width="60" style="font-size:9pt"><?php echo $row['sponsor'];?></td>		
	  		  <td width="70" style="font-size:9pt"><?php echo $row['item'];?></td>
					<td width="140" style="font-size:9pt"><?php echo $row['memo'];?></td>
					<td style="font-size:9pt" width="30"><?php echo getService_num($row['sn']);?></td>
					<td style="font-size:9pt" width="30"><?php echo $confirm[$row['confirm']];?></td>
				</tr>
				<?php
				} // en	d while
	?>
			</table>
		</div>
	</td>
    </tr>
	</table>	
	<?php
}



//列出某服務已登錄的學生=======================================================================================================================
function list_service_stud($sn) {
	$S=getService_one($sn);
	$query="select year_seme from stud_service where sn='$sn'";
	$result=mysql_query($query);
	list($c_curr_seme)=mysql_fetch_row($result);

	$class_array=class_base($c_curr_seme);
	//依班級列出名單
	
	?>
	<input type="hidden" name="update" value="0">
	<table border="0" width="800" cellspacing="0">
	   <tr bgcolor="#FFCCFF">
	   		<td style="color:#800000">※本服務已登錄的學生</td>
	   		<td align="right">
	   			  <?php
	   			  if ($S['confirm']==0) {
	   			  	?>
	   		    <input type="button" value="調整勾選學生的服務時間"  onclick="document.myform.update.value=1;document.myform.submit();">
	   		    <?php
	   		    } else {
	   		    	?>
	   		    <input type="button" value="調整勾選學生的服務時間" style="color:#CCCCCC">	
	   		    	<?php
	   		    }
	   		    ?>
	   		</td>
	   	</tr>
	</table>
	
	<table border="0" width="800"  cellspacing="0" cellpadding="3">

	   <?php
		$query="select distinct b.seme_class from stud_service_detail a ,stud_seme b, stud_service c   where a.item_sn='$sn'  and a.student_sn=b.student_sn and a.item_sn=c.sn and b.seme_year_seme=c.year_seme order by b.seme_class";
	   $result=mysql_query($query);
	   //開始依班級列出
	   while ($class_array=mysql_fetch_row($result)) {
	   	  list($classid)=$class_array;
		   $C=sprintf('%03d_%d_%02d_%02d',substr($c_curr_seme,0,3),substr($c_curr_seme,-1,1),substr($classid,0,1),substr($classid,1,2));
		   $class_base=class_id_2_old($C);
		   $class_name=$class_base[5]; //班名稱 一年1班, 一年2班....
		   echo "<tr><td width=80 valign='top'>".$class_base[5]."</td><td style='font-size:9pt' valign='top'>";
		   //取出班級學生
		   $query="select a.*,b.stud_name,c.seme_num,c.seme_class from stud_service_detail a,stud_base b,stud_seme c where a.item_sn='$sn' and a.student_sn=b.student_sn and a.student_sn=c.student_sn and c.seme_year_seme='$c_curr_seme' and c.seme_class='$classid' and (b.stud_study_cond=0 or b.stud_study_cond=5) order by c.seme_num";
       $res_class=mysql_query($query);
       ?>
       <table border="0" width="100%" cellspacing="0" cellpadding="0">
       <?php
       $i=0;
		   while ($row_class=mysql_fetch_array($res_class)) {
  	   	$i++;
	 	     if ($i%5==1) echo "<tr>";
	   	 ?>
	   	  <td width="141" style="font-size:10pt" valign="top">
	   	  	<input type="checkbox" name="update_stud[<?php echo $row_class['student_sn'];?>]" value="<?php echo $row_class['seme_class'];?>" style="font-size:9pt">
	   	  	<?php echo $row_class['seme_num'].$row_class['stud_name'];?>(<?php echo $row_class['minutes'];?>分)<img style="cursor:hand" onclick="if(window.confirm('真的要刪除嗎？')){ del_stud(<?php echo $row_class['student_sn'];?>)}" src="./images/del.png" title="直接刪除">
	   	  	<?php
	   	  	  if ($row_class['studmemo']!="") {
	   	  	 ?>
	   	  	   <br>---><font color="red"><?php echo $row_class['studmemo'];?></font>
	   	  	 <?php 
	   	  	  }
	   	  	?>

	   	  </td>
			<?php
		     if ($i%5==0) echo "</tr>";
		   }	//while row_class		
		   	 if ($i%5>0) {
              for ($j=$i%5+1;$j<=5;$j++) { echo "<td>&nbsp;</td>"; }
              echo "</tr>";
    		 }
  
		   echo "</table></td></tr>";
	   } // end while class_array	   
		     
		    ?>
		    </td>
		    </tr>
	</table>
	<?php
	
}

//列出學生(縮小,不編輯)==================
function list_service_stud_noedit($sn) {
	$S=getService_one($sn);
	$query="select year_seme from stud_service where sn='$sn'";
	$result=mysql_query($query);
	list($c_curr_seme)=mysql_fetch_row($result);

	$class_array=class_base($c_curr_seme);
	//依班級列出名單
	
	?>
	<table border="0" width="100%"  cellspacing="0" cellpadding="0">

	   <?php
		$query="select distinct b.seme_class from stud_service_detail a ,stud_seme b, stud_service c where a.item_sn='$sn'  and a.student_sn=b.student_sn and a.item_sn=c.sn and b.seme_year_seme=c.year_seme order by b.seme_class";
	   $result=mysql_query($query);
	   //開始依班級列出
	   while ($class_array=mysql_fetch_row($result)) {
	   	  list($classid)=$class_array;
		   $C=sprintf('%03d_%d_%02d_%02d',substr($c_curr_seme,0,3),substr($c_curr_seme,-1,1),substr($classid,0,1),substr($classid,1,2));
		   $class_base=class_id_2_old($C);
		   $class_name=$class_base[5]; //班名稱 一年1班, 一年2班....
		   echo "<tr><td style='color:#0000FF'>".$class_base[5]."</td></tr><tr><td>";
		   //取出班級學生
		   $query="select a.*,b.stud_name,c.seme_num,c.seme_class from stud_service_detail a,stud_base b,stud_seme c where a.item_sn='$sn' and a.student_sn=b.student_sn and a.student_sn=c.student_sn and c.seme_year_seme='$c_curr_seme' and c.seme_class='$classid' and (b.stud_study_cond=0 or b.stud_study_cond=5) order by c.seme_num";
       $res_class=mysql_query($query);
       ?>
       <table border="0" width="100%" cellspacing="0" cellpadding="0">
       <?php
       $i=0;
		   while ($row_class=mysql_fetch_array($res_class)) {
  	   	$i++;
	 	     if ($i%3==1) echo "<tr>";
	   	 ?>
	   	  <td style="font-size:10pt" valign="top" width="113">
	   	  		<?php echo $row_class['seme_num'].$row_class['stud_name'];?>(<?php echo $row_class['minutes'];?>分)
	   	  </td>
			<?php
		     if ($i%3==0) echo "</tr>";
		   }	//while row_class		
		   	 if ($i%3>0) {
              for ($j=$i%3+1;$j<=3;$j++) { echo "<td width='113'>&nbsp;</td>"; }
              echo "</tr>";
    		 }
  
		   echo "</table></td></tr>";
	   } // end while class_array	   
		     
		    ?>
		    </td>
		    </tr>
	</table>
	<?php
	


}



//調整某服務已登錄學生的服務時間============================================================================================================================
function update_service_stud($sn) {
?>
	<input type="hidden" name="check_update" value="0">
	<table border="0" width="800" cellspacing="0">
	   <tr bgcolor="#FFCCFF">
	   		<td style="color:#800000">※重設本服務已登錄的學生資料</td>
	   		<td align="right">
	   		    <input type="button" style="color:#FF0000" value="重設下列學生的服務資料"  onclick="document.myform.check_update.value=1;document.myform.submit();">
	   		</td>
	   	</tr>
	</table>
<?php	
	//傳入 $_POST['update_stud']['student_sn']=$class
	 $c_curr_seme=getService_year_seme($sn);

	 
	//先統計有那幾個班被勾選 $CLASS  ( array )
	$i=0; $p_class="";
	foreach ($_POST['update_stud'] as $student_sn=>$classid) {
	 if ($classid!=$p_class) {
	  $i++;
	  $j=0;
	  $p_class=$classid;
	   $CLASS[$i]=$p_class;	 	
	 }
	 if ($classid==$p_class) {
	 	$j+=1;
	 	 $CLASS_stud[$classid][$j]=$student_sn;
	 }
	}
	?>
	<table border="0" width="800">
		<?php
		foreach ($CLASS as $classid) {
 		   $C=sprintf('%03d_%d_%02d_%02d',substr($c_curr_seme,0,3),substr($c_curr_seme,-1,1),substr($classid,0,1),substr($classid,1,2));
		   $class_base=class_id_2_old($C);
		   $class_name=$class_base[5]; //班名稱 一年1班, 一年2班....
		   echo "<tr><td width=80 valign='top'>".$class_base[5]."</td><td style='font-size:9pt' valign='top'>";
			?>
			 <table border="0" width="100%" cellspacing="0" cellpadding="0">
       <?php
       $i=0;
       foreach ($CLASS_stud[$classid] as $student_sn) {
		   	//取得該生基本資料		   	
		   	$STUD=getService_stud_base($sn,$student_sn);
  	   	$i++;
	 	     if ($i%5==1) echo "<tr>";
	   	 ?>
	   	  <td width="141" style="font-size:10pt" valign="top">
	   	  	<?php echo $STUD['seme_num'].".".$STUD['stud_name'];?>
  			(<input type="text" style="font-size:9pt;color:#0000FF" name="time_stud[<?php echo $STUD['student_sn'];?>]" size="2" value="<?php echo $STUD['minutes'];?>">分鐘)<br>
  			註記：<input type="text" name="studmemo[<?php echo $STUD['student_sn'];?>]" size="10" style="font-size:9pt;color:#0000FF" value="<?php echo $STUD['studmemo'];?>">

	   	  </td>
			<?php
		     if ($i%5==0) echo "</tr>";
		   }	//foreach $$classid
		   	 if ($i%5>0) {
              for ($j=$i%5+1;$j<=5;$j++) { echo "<td>&nbsp;</td>"; }
              echo "</tr>";
    		 }
  
		   echo "</table></td></tr>";
	   } // end foreach CLASS array	   
		     
		    ?>
	</table>
</form>
			<?php
			
} // end function

//取得某服務內容
function getService_one($sn) {
   $query="select * from stud_service where sn='$sn'";
	$result=mysql_query($query);
	$S=mysql_fetch_array($result);
 return $S; // return array
}


//取得某服務的學期============================================================================================================================
function getService_year_seme($sn) {
 $query="select year_seme from stud_service where sn='$sn'";
 $result=mysql_query($query);
 list($year_seme)=mysql_fetch_row($result);
 return $year_seme;
	
}
//取得某服務的登錄人數=====================================================================================================================
function getService_num($sn) {
 $query="select count(*) from stud_service_detail where item_sn='$sn'";
 $result=mysql_query($query);
 list($num)=mysql_fetch_row($result);
 return $num;
}

//取得某服務的某學生的服務時間=================================================================================================================
function getService_min($sn,$student_sn) {
 $query="select minutes from stud_service_detail where item_sn='$sn' and student_sn='$student_sn'";
 $result=mysql_query($query);
 list($min)=mysql_fetch_row($result);
 return $min;
}

//取得某服務的某學生的服務時間==================================================================================================================
function getService_allmin($student_sn,$year_seme) {
 $query="select sum(minutes) from stud_service_detail a,stud_service b where a.student_sn='$student_sn' and b.year_seme='$year_seme' and a.item_sn=b.sn and b.confirm=1";
 $result=mysql_query($query);
 list($min)=mysql_fetch_row($result);
 return $min;
}


//取得某服務的某學生的服務註記==================================================================================================================
function getService_studmemo($sn,$student_sn) {
 $query="select studmemo from stud_service_detail where item_sn='$sn' and student_sn='$student_sn'";
 $result=mysql_query($query);
 list($studmemo)=mysql_fetch_row($result);
 return $studmemo;
}

//取得某服務某學生的基本資料 學期班級, 座號, 姓名, 服務詳細內容等. 關聯資料表 stud_base a, stud_seme b, stud_service c, stud_service_detail d===============
function getService_stud_base($sn,$student_sn) {
	$query="select a.stud_name,b.seme_class,b.seme_num,c.service_date,c.department,c.item,c.memo,c.sponsor,d.* from stud_base a, stud_seme b, stud_service c , stud_service_detail d where c.sn='$sn' and c.year_seme=b.seme_year_seme and c.sn=d.item_sn and d.student_sn=b.student_sn and d.student_sn=a.student_sn and d.student_sn='$student_sn'";
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	return $row;
}


//傳回服務單位 ==================================================================================================================
function getPostRoom($room_id) {
  global $CONN;
  $sql_select = "select room_name from school_room where room_id='$room_id'";
  $result=$CONN->Execute($sql_select);
  $room_name=$result->fields['room_name'];	
  return $room_name;
}

//刪除某服務的某位學生登錄==================================================================================================================
function delService_stud($sn,$student_sn) {
	$query="delete from stud_service_detail where item_sn='$sn' and student_sn='$student_sn'";
	if (!mysql_query($query)) {
		echo "Error! Query=".$query;
		exit();
	} else {
		return true;
	} 
}

//針對已存在的服務項目, 增減學生前進行檢查 $act=1時顯示結果, act=0時，傳回true或false
function check_exist_service($sn,$act="") {
		$item_sn=$sn;
		 $query="select * from stud_service where sn='$item_sn'";
		 $result=mysql_query($query);
		 if (mysql_num_rows($result)) {
		 	$row=mysql_fetch_array($result);
		 	if ($act) {
			 echo "編輯<font color=blue>".$row['service_date']." 在 【".$row['sponsor']."】進行《".$row['item']."》服務...</font><br>\n";
			}else{
			 return true;
			}
		 }else{
		 	if ($act) {
  		 	echo "未取得對應的服務事實 (sn= $item_sn ), 無法將學生資料進行登錄!";
		    exit();	
	   	}else{
	   	  return false;
	   	}
		 }
}

//檢驗某服務是否已登錄某生==================================================================================================================
function check_exist_service_stud($sn,$student_sn) {
	$query="select * from stud_service_detail where student_sn='$student_sn' and item_sn='$sn'";
	$result=mysql_query($query);
	if (mysql_num_rows($result)) {
	 return true;
	} else {
	 return false;
	}
	
}



//列出某學期全班學生供點選查詢服務總表==================================================================================================================
function student_service_select($classid,$c_curr_seme) {
	$query="select a.*, b.seme_class,b.seme_num from stud_base a,stud_seme b where b.seme_class='$classid' and b.seme_year_seme='$c_curr_seme' and a.student_sn=b.student_sn order by b.seme_num";
	$result=mysql_query($query);

?>

  <table border="1" bordercolor="#800000" style="border-collapse:collapse" width="800">
  <?php
  $i=0;
  while ($row=mysql_fetch_array($result)) {
  		$i++;
  		if ($i%5==1) echo "<tr>";
		$stud_Service_min=getService_allmin($row['student_sn'],$c_curr_seme);
		$H=round($stud_Service_min/60,2);
		//$M=$stud_Service_min%60;
  	?>
  		<td><input type="checkbox" name="STUD[<?php echo $row['student_sn'];?>]" value="<?php echo $row['seme_num'];?>" <?php if ($_POST['STUD'][$row['student_sn']]==$row['seme_num']) echo "checked";?>>
  			<?php echo $row['seme_num'];?>.<a href="javascript:check_stud('<?php echo $row['student_sn'];?>')"><?php echo $row['stud_name'];?></a> <font size=2>( <?php echo $H;?> 小時)</font></td>
    <?php
    if ($i%5==5) echo "</tr>";
   } // end foreach
    if ($i%5>0) {
     for ($j=$i%5+1;$j<=5;$j++) { echo "<td></td>"; }
    }
  ?>
  
  </table>
<?php
} // end function


//傳回學某學生某學期的個資 ==================================================================================================================
function getStudent_seme($student_sn,$c_curr_seme){
		$query="select a.*,b.seme_class,b.seme_num from stud_base a,stud_seme b where a.student_sn='$student_sn' and a.student_sn=b.student_sn and b.seme_year_seme='$c_curr_seme'";
		$result=mysql_query($query);
		$student=mysql_fetch_array($result);
	return $student;
}

//列出學生某學期的服務明細==================================================================================================================
function list_service($student_sn,$c_curr_seme,$class_name) {
	global $CONN;
	$STUDENT=getStudent_seme($student_sn,$c_curr_seme);
	$query="select a.student_sn,a.minutes,a.studmemo,b.service_date,b.department,b.sponsor,b.item,b.memo from stud_service_detail a,stud_service b where a.student_sn='$student_sn' and b.year_seme='$c_curr_seme' and a.item_sn=b.sn and b.confirm=1";
   $res=$CONN->execute($query);
	 $School_cname=get_school_cname();	     	
	?>
	<br>
	<table border="0" width="100%">
	  <tr>
	     <td align="center" style="font-size:16pt;font-family:標楷體">
	     	<?php
	     	 	echo $School_cname."學生服務學習紀錄表";	     	
	     	?>
	    </td>
	   <tr>
	   	  <td align="center"> 
	     	班級：<u><b>&nbsp;<?php echo $class_name;?>&nbsp;</b></u>&nbsp;&nbsp;&nbsp;座號：<u><b>&nbsp;<?php echo $STUDENT['seme_num'];?>&nbsp;</b></u> 姓名：<u><b>&nbsp;<?php echo $STUDENT['stud_name'];?>&nbsp;</b></u>
	     	</td>
	  </tr>
	</table>
	<table border="1" style="border-collapse:collapse" bordercolor="#800000" width="100%">
	  <tr bgcolor="#EEEEEE">
	    <td width="15%" align="center">日期</td>
	    <td width="45%" align="center">參加校內外公共服務學習事項及活動項目</td>
	    <td width="15%" align="center">時數</td>
	    <td width="15%" align="center">主辦單位</td>
	    <td width="10%" align="center">備註</td>
	  </tr>
	  <?php
	  $MIN=0;
	  while(!$res->EOF) {
	  	$minutes=$res->fields['minutes'];
		$service_date=$res->fields['service_date'];
		$department=$res->fields['department'];
		$sponsor=$res->fields['sponsor'];
		$item=$res->fields['item'];
		$memo=$res->fields['memo'];
		$studmemo=$res->fields['studmemo'];
		$MIN+=$minutes;
		?>
	  <tr>
	    <td align="center"><?php echo $service_date;?></td>
	    <td><?php echo $item;?>：<?php echo $memo;?></td>
	    <td align="center"><?php echo round($minutes/60,2);?></td>
	    <td align="center"><?php echo $sponsor;?></td>
	    <td align="center"><?php echo $studmemo;?></td>
	  </tr>
		
		<?php
		$res->MoveNext();
	  }
	  $HR=round($MIN/60,2);
	  ?>	  
	</table>
	<table border="0" width="100%">
		<tr>
			<td style="font-size:14pt;font-family:標楷體" valign="top">
			<u><b>&nbsp;<?php echo sprintf('%d',substr($c_curr_seme,0,3));?>&nbsp;</b></u>學年度 第<u><b>&nbsp;<?php echo substr($c_curr_seme,-1);?>&nbsp;</b></u>學期，服務學習共 <u><b>&nbsp;<?php echo $HR;?>&nbsp;</b></u>小時
			</td>
			<td width="150">
				<table border="1" style="border-collapse:collapse;border-style:dashed" bordercolor="#000000" width="150" height="150">
					<tr>
						<td width="150" height="150" valign="bottom" align="center" style="color:#c0c0c0;font-size:9pt">學務處核章</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<P STYLE="page-break-before:always">
	<?php
}

//列出學生全學期的服務明細==================================================================================================================
function list_service_all($student_sn,$class_name) {
	global $CONN,$school_long_name;

	$STUDENT=get_stud_base($student_sn);
	$query="select a.student_sn,a.minutes,a.studmemo,b.year_seme,b.service_date,b.department,b.sponsor,b.item,b.memo from stud_service_detail a,stud_service b where a.student_sn='$student_sn' and a.item_sn=b.sn and b.confirm=1 order by year_seme";
   $res=$CONN->execute($query);
	 	     	
	?>
	<br>
	<table border="0" width="100%">
	  <tr>
	     <td align="center" style="font-size:16pt;font-family:標楷體">
	     	<?php
	     	 	echo $school_long_name."學生服務學習紀錄表";	     	
	     	?>
	    </td>
	   <tr>
	   	  <td align="center"> 
	     	班級：<u><b>&nbsp;<?php echo $class_name;?>&nbsp;</b></u>&nbsp;&nbsp;&nbsp;座號：<u><b>&nbsp;<?php echo sprintf("%d",substr($STUDENT['curr_class_num'],3,2));?>&nbsp;</b></u> 姓名：<u><b>&nbsp;<?php echo $STUDENT['stud_name'];?>&nbsp;</b></u>
	     	</td>
	  </tr>
	</table>
	<table border="1" style="border-collapse:collapse" bordercolor="#800000" width="100%">
	  <tr bgcolor="#EEEEEE">
	    <td width="10%" align="center">學期</td>
	    <td width="15%" align="center">日期</td>
	    <td align="center">參加校內外公共服務學習事項及活動項目</td>
	    <td width="15%" align="center">時數</td>
	    <td width="15%" align="center">主辦單位</td>
	    <td width="10%" align="center">備註</td>
	  </tr>
	  <?php
	  $MIN=0;
	  while(!$res->EOF) {
	  $year_seme=$res->fields['year_seme'];
	  $minutes=$res->fields['minutes'];
		$service_date=$res->fields['service_date'];
		$department=$res->fields['department'];
		$sponsor=$res->fields['sponsor'];
		$item=$res->fields['item'];
		$memo=$res->fields['memo'];
		$studmemo=$res->fields['studmemo'];
		$MIN+=$minutes;
		?>
	  <tr>
	    <td align="center"><?php echo $year_seme;?></td>
	    <td align="center"><?php echo $service_date;?></td>
	    <td><?php echo $item;?>：<?php echo $memo;?></td>
	    <td align="center"><?php echo round($minutes/60,2);?></td>
	    <td align="center"><?php echo $sponsor;?></td>
	    <td align="center"><?php echo $studmemo;?></td>
	  </tr>
		
		<?php
		$res->MoveNext();
	  }
	  $HR=round($MIN/60,2);
	  ?>	  
	</table>
	<table border="0" width="100%">
		<tr>
			<td style="font-size:14pt;font-family:標楷體" valign="top">
			<u><b>總服務學習時數共 <u><b>&nbsp;<?php echo $HR;?>&nbsp;</b></u>小時
			</td>
			<td width="150">
				<table border="1" style="border-collapse:collapse;border-style:dashed" bordercolor="#000000" width="150" height="150">
					<tr>
						<td width="150" height="150" valign="bottom" align="center" style="color:#c0c0c0;font-size:9pt">學務處核章</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<P STYLE="page-break-before:always">
	<?php
}




//列出某班某學期所有學生的服務明細==================================================================================================================
function list_class_all($classid,$c_curr_seme,$class_name) {
	$query="select a.*, b.seme_class,b.seme_num from stud_base a,stud_seme b where b.seme_class='$classid' and b.seme_year_seme='$c_curr_seme' and a.student_sn=b.student_sn order by b.seme_num";
	$result=mysql_query($query);
	while ($row_stud=mysql_fetch_array($result)) {
		list_service($row_stud['student_sn'],$c_curr_seme,$class_name);
	}
}
//取得學校名稱 ==================================================================================================================
function get_school_cname() {
	$query="select sch_cname from school_base limit 1";
	$res=mysql_query($query);
	list($S)=mysql_fetch_row($res);
	
	return $S;
	
}

?>
<!--- 以下為 JavaScript function --->
<script language="javaScript">
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
  	
    if (document.myform.elements[i].name.substr(0,13)=='selected_stud') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}

function checkall(STR) {
	
	var j=0;
	if (document.myform.init_check.checked) { j=1; }
	
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,STR.length)==STR) {
      document.myform.elements[i].checked=j;
    }
    i++;
  }
}	

function tagtime() {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,9)=='time_stud') {
      document.myform.elements[i].value=document.myform.init_time.value;
    }
    i++;
  }
}

//刪除某學生
function del_stud(student_sn) {
	document.myform.check_del.value=student_sn;
	document.myform.submit();
}

//刪除某筆記錄
function confirm_delete() {
 check=confirm("您確定要刪除這筆記錄? \n 注意! 連同學生服務記錄將一併刪除。");
 if (check) {
 	document.myform.mode.value='delete_service';
 	document.myform.submit();
 } else {
  return false;
 }
}


//儲存前檢查資料
function check_save() {
 var save=1;
 var check_stud=0;
	if (document.myform.service_date.value=='') {
		alert('未輸入服務日期');
		save=0;
	}
	if (document.myform.memo.value=='') {
		alert('未輸入服務內容');
		save=0;
	}
  
  //檢查是否有勾選學生, 並輸入時間
  var i=0;
  var j=0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,13)=='selected_stud') {
      if (document.myform.elements[i].checked==true) {
      	j=i+1;
      	if (document.myform.elements[j].value!='' && document.myform.elements[j].value>0) {
      		check_stud=1;
      	}
      }
    }
    i++;
  }
  if (check_stud==0) {
  	alert('未勾選學生或輸入學生的服務時間(分鐘)格式有誤!');
    save=0;
  }
	
  if (save==1) {
  	document.myform.save.value=save;
  	document.myform.submit();
  }
}

//儲存前檢查資料
function check_update_stud() {
 var save=1;
   //檢查是否有勾選學生, 並輸入時間
  var i=0;
  var regex = /^-?\d+\.?\d*?$/;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,9)=='time_stud') {
      if (document.myform.elements[i].value<1 || document.myform.elements[i].value=="" || document.myform.elements[i].value.match(regex)==null) {
      	 save=0;
      }
    }
    i++;
  }
  if (save==0) {
  	alert('學生的服務時間(分鐘)輸入有誤!');
    save=0;
  }
	
  if (save==1) {
  	document.myform.check_update.value=save;
  	document.myform.submit();  	
  }
	
}

</script>
