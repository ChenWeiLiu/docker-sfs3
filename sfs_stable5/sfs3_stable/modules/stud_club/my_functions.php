<?php
//學期社團列表
function list_club_select($c_curr_seme,$c_curr_class) {
	if ($c_curr_seme==sprintf('%03d%1d',curr_year(),curr_seme())) {
	  $query="select a.*,b.class_num from stud_club_base a, teacher_post b where a.year_seme='$c_curr_seme' and a.club_class='$c_curr_class' and a.club_teacher=b.teacher_sn order by b.teach_title_id,b.class_num,a.club_name";
	} else {
	  $query="select * from stud_club_base where year_seme='$c_curr_seme' and club_class='$c_curr_class' order by club_name";
	}
	$result=mysql_query($query);
	?>
	<select size="20" name="select_club_sn" onchange="document.myform.club_sn.value=document.myform.select_club_sn.value;document.myform.mode.value='list';document.myform.submit();">
		<optgroup style="color:#FF00FF" label="請選擇社團"></optgroup>	
	<?php
	while ($row=mysql_fetch_array($result)) {
	  ?>
	  <option value="<?php echo $row['club_sn'];?>"<?php if ($_POST['club_sn']==$row['club_sn']) echo " selected";?>><?php echo get_teacher_name($row['club_teacher'])."-".$row['club_name'];?></option>
	  <?php
	}
	?>
</select>
	<?php
	/*
	?>
  <table border="1" bordercolor="#000000" style="border-collapse:collapse" width="100%">
	 <tr>
	 <td>
	<table border="0" width="100%">
	 	<tr bgcolor="#FFCCFF">
	  		<td width="100" style="font-size:9pt;color:#000">社團名稱</td>
	  		<td width="60" style="font-size:9pt;color:#000">指導老師</td>
	 	</tr>
	 </table>
	 	<div  style="OVERFLOW: auto; HEIGHT: 400px; " border="1" >
			<table border="0" widtn="140">
				<?php
				while ($row=mysql_fetch_array($result)) {
				?>
				<tr bgcolor="#FFFFFF" style="cursor:hand" onclick="club_list(<?php echo $row['club_sn'];?>);" onmouseover="setPointer(this, 'over', '#FFFFFF', '#CCFFCC', '#FFCC99')" onmouseout="setPointer(this, 'out', '#FFFFFF', '#CCFFCC', '#FFCC99')" onmousedown="setPointer(this, 'click', '#FFFFFF', '#CCFFCC', '#FFCC99')" <?php if ($row['club_sn']==$_POST['club_sn']) echo "style='color:#FF0000'";?>>
		 			<td width="90" style="font-size:9pt;color:#000">
		 				<?php echo $row['club_name'];?>
		 			</td>
					<td width="40" style="font-size:9pt;color:#000">
						<?php echo get_teacher_name($row['club_teacher']);?>
					</td>		
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
	*/
	
	
} // end function

//列出班級學生供選擇
function list_students_select ($club_sn) {
	global $SETUP;
	$CLUB=get_club_base($club_sn);
	//取得年級資料
	$query= ($CLUB['club_class']<100)
	?"SELECT class_id,c_name FROM `school_class`  WHERE year='".sprintf('%d',substr($CLUB['year_seme'],0,3))."' and semester='".substr($CLUB['year_seme'],-1)."' and c_year='".$CLUB['club_class']."' order by class_id"
	:"SELECT class_id,c_name FROM `school_class`  WHERE year='".sprintf('%d',substr($CLUB['year_seme'],0,3))."' and semester='".substr($CLUB['year_seme'],-1)."'  order by class_id";
	$res_class=mysql_query($query);
	?>
	<Script language="JavaScript">
	   document.myform.mode.value="add_members";
	   document.myform.club_sn.value=<?php echo $_POST['club_sn'];?>
	</Script>
	<table border="0" width="100%">

			<td>
				<select name="class_id" size="1" onchange="document.myform.submit();">
					<option value="" style="color:#FF00FF">請選擇班級...</option>
					<?php				 
				  while ($row_class=mysql_fetch_array($res_class)) {
				  	?>
				  		<option value="<?php echo $row_class['class_id'];?>" <?php if ($row_class['class_id']==$_POST['class_id']) echo "selected";?>><?php echo get_seme_class_2_name(sprintf('%d',substr($row_class['class_id'],6,2)),$row_class['c_name']);?></option>
				  	<?php
				  } // end while
				?>
				</select>
				<?php
				 if (isset($_POST['class_id'])) {
				 ?>
				 <input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>
				 <?php
				 } // end if
				?>
			</td>
		</tr>
	</table> 	
	<?php
	//若有選擇, 則列出學生供勾選
	if (isset($_POST['class_id'])) {
		
   	$c_curr_class=$_POST['class_id'];
		$seme_year_seme=substr($c_curr_class,0,3).substr($c_curr_class,4,1);
		$seme_class=sprintf("%d",substr($c_curr_class,6,2).substr($c_curr_class,9,2));
		$query="select a.seme_num,a.student_sn,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and a.student_sn=b.student_sn and (b.stud_study_cond=0 or b.stud_study_cond=2) order by a.seme_num";
		$result=mysql_query($query)
		?>
    	<table border="1" bordercolor="#800000" style="border-collapse:collapse" width="600">
  		<?php
  		$i=0;
  		while ($row=mysql_fetch_array($result)) {
  			$i++;
  		if ($i%5==1) echo "<tr>";
  		//檢查是否已有此生
  		 if ( chk_if_exist_stud($_POST['club_sn'],$row['student_sn']) or ($SETUP['multi_join']==0 and check_student_joined_club($row['student_sn'],$seme_year_seme))) {
  		 	?>
  			<td width="100" style="font-size:10pt" bgcolor="#FFCCCC">‧<?php echo $row['seme_num'].".".$row['stud_name'];?>	</td>
  		 	<?php
  		 	//若仍未被選入此社團
  		 } else {
  			?>
  			<td width="100" style="font-size:10pt">
  				<input type='checkbox' name='selected_stud[<?php echo $row['student_sn'];?>]' value='<?php echo $row['stud_name'];?>' id='stud_selected'>
  				<?php echo $row['seme_num'].".".$row['stud_name'];?>
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
	
	} // end if isset($_POST['class_id'])
}// end function

//列出所有社團總表
function listall_club($year_seme) {

	global $school_kind_name;
	//取得學期社團設定
  $SETUP=get_club_setup($year_seme);

	$school_kind_name[100]="跨年";
	$class_year_array=get_class_year_array(sprintf('%d',substr($year_seme,0,3)),sprintf('%d',substr($year_seme,-1)));
	$class_year_array[100]="100";

	?>
	       
        <script src="include/jquery-1.6.2.min.js"></script> 
        <script src="include/jquery.idTabs.min.js"></script> 
        <script src="include/jquery.blockUI.js"></script>

    <div> 
        <ul class="idTabs"> 
        	<table border="1" style="border-collapse:collapse" bordercolor="#000">
        		<tr>
			<?php
      foreach ($class_year_array as $K=>$class_year_name) {
			  $query="select * from stud_club_base where year_seme='$year_seme' and club_class='$K' order by club_name";
			  $result=mysql_query($query);
			  if (mysql_num_rows($result)) {
			?>
                <td><a href="#<?php echo "class".$K;?>" style="font-size:10pt"><?php echo $school_kind_name[$K];?>級</a></td>
			<?php
			  } // end if
			} // end foreach
			?>
			</table>
            </ul> 
				<?php
	      foreach ($class_year_array as $K=>$class_year_name) {
	      	list_class_club_choice_detail($year_seme,$K,1,1); //列出年級社團選課明細
      } // end foreach	
    ?>
   </div>
    <?php
}

//列出某學期全班學生總表==================================================================================================================
function student_club_select($classid,$c_curr_seme) {
	$query="select a.*, b.seme_class,b.seme_num from stud_base a,stud_seme b where b.seme_class='$classid' and b.seme_year_seme='$c_curr_seme' and a.student_sn=b.student_sn order by b.seme_num";
	$result=mysql_query($query);

?>

  <table border="1" bordercolor="#800000" style="border-collapse:collapse" width="800">
  <?php
  $i=0;
  while ($row=mysql_fetch_array($result)) {
  		$i++;
  		if ($i%5==1) echo "<tr>";
  	?>
  		<td><input type="checkbox" name="STUD[<?php echo $row['student_sn'];?>]" value="<?php echo $row['seme_num'];?>" <?php if ($_POST['STUD'][$row['student_sn']]==$row['seme_num']) echo "checked";?>>
  			<?php echo $row['seme_num'];?>.<a href="javascript:check_stud('<?php echo $row['student_sn'];?>')"><?php echo $row['stud_name'];?></a></td>
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




function list_class_club_choice_detail($year_seme,$club_class,$show_link,$show_choice) {
	//取得學期社團設定
	$Y[0]="否";
	$Y[1]="是";

  $SETUP=get_club_setup($year_seme);

			  $query="select * from stud_club_base where year_seme='$year_seme' and club_class='$club_class' order by club_name";
			  $result=mysql_query($query);
			  if (mysql_num_rows($result)) {

				?>
			 	<div id="<?php echo "class".$club_class;?>"> 
			 	<table border="1" style="border-collapse:collapse" bordercolor="#800000" cellpadding="3">
			 	  <tr bgcolor="#FFCCFF">
			 	  	<td width="180" style="font-size:10pt;color:#000000">社團名稱</td>
			 	  	<td width="60" style="font-size:10pt;color:#000000" align="center">指導老師</td>
			 	  	<td width="60" style="font-size:10pt;color:#000000">上課地點</td>
			 	  	<td width="30" style="font-size:10pt;color:#000000" align="center">名額</td>
			 	  	<td width="50" style="font-size:9pt;color:#000000" align="center">已編學員</td>
			 	  	<td width="50" style="font-size:10pt;color:#000000" align="center">可選課</td>
				    <?php
				    if ($show_choice==0) {
				    ?>
				    <td style="font-size:10pt;color:#000000" align="center">社團簡介</td>
				    <?php
				    }
				    if ($show_choice==1) {
				     for ($i=1;$i<=$SETUP['choice_num'];$i++) {
				      echo "<td style=\"font-size:8pt\" align=\"center\">志願".$i."</td>";
				     }
				    } // end if
				    ?>
			 	  </tr>
			 	  <?php
			 	   while ($row=mysql_fetch_array($result)) {
			 	   	$stud_number=get_club_student_num($row['year_seme'],$row['club_sn']);
			 	    ?>
			 	  <tr>
			 	  	<td style="font-size:10pt;color:#0000FF">
			 	  	 <?php
			 	  	  if ($show_link) {
			 	  	 ?>
			 	  		<a style="cursor:hand;color:#0000FF" onclick="club_list(<?php echo $row['club_sn'];?>);"><?php echo $row['club_name'];?></a>
			 	  		<?php
			 	  	}else{
			 	  		 echo $row['club_name'];
			 	  	}
			 	  		?>
			 	  	</td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo get_teacher_name($row['club_teacher']);?></td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo $row['club_location'];?></td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo $row['club_student_num'];?></td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo $stud_number[0];?> (<font color="#0000FF"><?php echo $stud_number[1];?></font>,<font color="#FF6633"><?php echo $stud_number[2];?></font>)</td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo $Y[$row['club_open']];?></td>
				  	<?php
				  	//不顯示已選志願明細時, 列出簡介
				    if ($show_choice==0) {
				    	$w=preg_replace("/".chr(13).chr(10)."/","<br>".chr(13).chr(10),$row['club_memo']);
				    ?>
				    <td style="font-size:10pt;color:#000000"><?php echo $w;?></td>
				    <?php
				    } // end if show_choice==0
            //顯示已選志願明細時, 不列出簡介
				    if ($show_choice==1) {
					    for ($i=1;$i<=$SETUP['choice_num'];$i++) {
					     echo "<td align=\"center\" style=\"font-size:10pt\"><a style=\"cursor:pointer\" id=\"".$row['club_sn']."_".$i."\" class=\"list_choice_rank\">".get_club_choice_rank($row['club_sn'],$i)."</a></td>";
					    }
					  } // end if 
					    ?>
			 	  </tr>
			 	    <?php
			 	   } // end while
			 	  ?>
			 	</table>
			</div>
			<?php
			 } // end if mysql_num_rows
}
//列印名單
function print_name_list($year_seme,$club_sn) {
		
		global $school_kind_name;
		$school_kind_name[100]="跨年";

	//取得學生名單
 $query="select a.*,b.seme_class,b.seme_num,c.stud_name from association a,stud_seme b,stud_base c where a.seme_year_seme='$year_seme' and a.club_sn='".$club_sn."' and b.seme_year_seme='$year_seme' and a.student_sn=b.student_sn and a.student_sn=c.student_sn and (c.stud_study_cond=0 or c.stud_study_cond=2) order by seme_class,seme_num";
 $res=mysql_query($query);

$CLUB=get_club_base($club_sn);
?>
<Script language="JavaScript">
 function print_name() {
  flagWindow=window.open('club_print_kind.php?year_seme=<?php echo $year_seme;?>&club_sn=<?php echo $club_sn;?>','club_stud_name','width=380,height=420,resizable=1,toolbar=1,scrollbars=auto');
 }
</Script>
指導老師：<?php echo get_teacher_name($CLUB['club_teacher']);?><br>
社團名稱：【<?php echo $school_kind_name[$CLUB['club_class']];?>級】<?php echo $CLUB['club_name'];?><br>
上課地點：<?php echo $CLUB['club_location'];?><br>
<table border="1" style="border-collapse:collapse" bordercolor="#000000">
 <tr>
  <td align="center" style="color:#000000;font-size:10pt" rowspan="2" width="50">序號</td>
 	<td align="center" style="color:#000000" rowspan="2" width="120">班級</td>
 	<td align="center" style="color:#000000" rowspan="2" width="60">座號</td>
 	<td align="center" style="color:#000000" rowspan="2" width="100">姓名</td>
 	<td align="center" style="color:#000000" colspan="10">日期與成績</td>
</tr>
<tr>
	<?php
	for ($i=1;$i<=10;$i++) {
	 echo "<td width=50>&nbsp;</td>";
	}
	?>
 </tr>
 <?php
 $ii=0;
  while ($row=mysql_fetch_array($res)) {
  	$CLASS_name=$school_kind_name[substr($row['seme_class'],0,1)];
  	$ii++;
  ?>
  <tr>
  	<td align="center" style="font-size:10pt"><?php echo $ii;?></td> 
  	<td align="center"><?php echo $CLASS_name.sprintf('%d',substr($row['seme_class'],1,2))."班";?></td> 
  	<td align="center"><?php echo $row['seme_num'];?></td> 
  	<td align="center"><?php echo $row['stud_name'];?></td> 
	<?php
	for ($i=1;$i<=10;$i++) {
	 echo "<td>&nbsp;</td>";
	}
	?>
 </tr>  
  <?php 
  } // end while
 ?>
</table>
<?php
} // end function
//列印全班名單 100_2_08_10
function print_class_student($c_curr_seme,$c_curr_class,$show_score,$show_feedback) {
	global $school_kind_name;
  
  if ($_POST['mode']=="") {
   echo "<input type=\"button\" value=\"友善列印\" onclick=\"print_class()\">";
  }
	$club_class=sprintf('%d%02d',substr($c_curr_class,6,2),substr($c_curr_class,9,2));
	echo $school_kind_name[substr($club_class,0,1)].sprintf('%d',substr($club_class,1,2))."班";
	$query="select a.stud_name,b.seme_num,b.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_class='$club_class' and b.seme_year_seme='$c_curr_seme' and (a.stud_study_cond=0 or a.stud_study_cond=2) order by seme_num";
	
	$result=mysql_query($query);
	
	?>
	<table border="1" style="border-collapse:collapse" bordercolor="#000000" cellpadding="2">
		<tr bgcolor="#CCFFCC">
			<td width="50" align="center">座號</td>
		  <td width="80" align="center">姓名</td>
		  <td width="120" align="center">參加社團</td>
		  <?php
		  if ($show_score) {
		  ?>
			<td width="60" align="center">成績</td>
		  <td width="300" align="center">學習描述</td>
		  <?php
		  }
		  ?>
 		  <?php
		  if ($show_feedback) {
		  ?>
		  <td width="300" align="center">學生自我省思</td>
		  <?php
		  }
		  ?>

		</tr>
 <?php
	while ($row=mysql_fetch_array($result)) {
		?>
		 <tr>
		 	<td align="center"><?php echo $row['seme_num'];?></td>
		 	<td align="center"><?php echo $row['stud_name'];?></td>
		 	<td>
		 		 <?php
		 		 $i=0;$my_score="";$my_description="";$my_feedback="";
		 		 $my_club=get_student_join_club($row['student_sn'],$c_curr_seme);
		 		  	foreach ($my_club as $my_club_sn=>$My) {
	 					$i++;
	 					echo $My['club_name'];
	 					
	 					$S=get_student_score($row['student_sn'],$my_club_sn);
	 					$my_score[$i]=$S['score'];
	 					$my_feedback[$i]=$S['stud_feedback'];
	 					$my_description[$i]=$S['description'];
	 					if (count($my_club)>1 and $i<count($my_club)) echo "<br>";
					}		 		  
		 		  ?>
		 	</td>
		 	<?php
		  if ($show_score) {
		  ?>
			<td align="center">
				<?php 
				$i=0;
				foreach ($my_score as $score) {
				 $i++;
				 $score=($score==0)?"-":$score;
				 echo $score;
				 if (count($my_score)>1 and $i<count($my_score)) echo "<br>"; 
				}
				?>
				</td>
		  <td style="font-size:10pt">
				<?php 
				$i=0;
				foreach ($my_description as $description) {
				 $i++;
				 echo $description;
				 if (count($my_description)>1 and $i<count($my_description)) echo "<br>"; 
				}
				?>
	  	</td>
		  <?php
		  }
		  
		  if ($show_feedback) {
		  ?>
		  <td style="font-size:10pt">
				<?php 
				$i=0;
				foreach ($my_feedback as $feedback) {
				 $i++;
				 echo $feedback;
				 if (count($my_feedback)>1 and $i<count($my_feedback)) echo "<br>"; 
				}
				?>
	  	</td>
		  <?php
		  }
	}
	?>
	</table>
	<Script Language="JavaScript">
		function print_class() {
		  document.myform.target="_blank";
		  document.myform.mode.value="print";
		  document.myform.submit();
		}
	</Script>
	<?php
}

//友善提醒
function list_class_info($c_curr_seme,$c_curr_class) {
	//社團可供選擇人數
	$club_for_stud_number=club_for_stud_num($c_curr_class,$c_curr_seme);
	$club_for_stud_num=$club_for_stud_number[0];
	//取得該年級人數
	$CLASS_num=class_student_num($c_curr_class,$c_curr_seme);

?>
	<table border="1" style="border-collapse:collapse" bordercolor="#800000" cellpadding="3" width="100%">
		<tr bgcolor="#FFFFCC">
			<td style="color:#0000FF">友善提醒：本年級共計 <?php echo $CLASS_num;?> 位學生，全部社團剩餘 <?php echo $club_for_stud_num;?> 個開放選修名額。</td>
		</tr>
	</table>
<?php
}

//社團表單
function list_club ($club_sn) {
	$Y[0]="否";
	$Y[1]="是";
	global $school_kind_name,$SETUP;
	$query="select * from stud_club_base where club_sn='$club_sn'";
	$result=mysql_query($query);
	$CLUB=mysql_fetch_array($result);
	$stud_number=get_club_student_num($CLUB['year_seme'],$CLUB['club_sn']);
	?>
	<table border="1" style="border-collapse:collapse" bgcolor="#D8D8EB" bordercolor="#000000" cellpadding="3" width="100%">
		<tr>
			<td width="150" align="right" >學期</td>
			<td><?php echo getYearSeme($CLUB['year_seme']);?></td>
		</tr>
		<tr>
			<td width="150" align="right" >社團名稱</td>
			<td><?php echo $CLUB['club_name'];?></td>
		</tr>
		<tr>
			<td width="150" align="right" >社團指導老師</td>
			<td><?php echo get_teacher_name($CLUB['club_teacher']);?></td>
		</tr>
		<tr>
			<td width="150" align="right" >上課地點</td>
			<td><?php echo $CLUB['club_location'];?></td>
		</tr>
		<tr>
			<td width="150" align="right" >所屬年級</td>
			<td><?php
			if ($CLUB['club_class']<100) { 
			echo $school_kind_name[$CLUB['club_class']]."級";
			} else {
			 echo "跨年級";
			}
			?>
			</td>
		</tr>
		<tr>
			<td width="150" align="right" >預計開班人數</td>
			<td><?php echo $CLUB['club_student_num'];?>人 (男生：<?php echo $CLUB['stud_boy_num'];?>人，女生：<?php echo $CLUB['stud_girl_num'];?>人)
				<?php 
				 if ($CLUB['ignore_sex']) echo "<font color=red size=2>※本社團男女生設定不列入編班參考!</font>";
				?>
				</td>
		</tr>
		<tr>
			<td width="150" align="right" >目前編班人數</td>
			<td><?php echo $stud_number[0];?> 人 (男生: <?php echo $stud_number[1];?>人，女生：<?php echo $stud_number[2];?>人)</td>
		</tr>
  	<tr>
			<td width="150" align="right" >通過分數</td>
			<td><?php echo $CLUB['pass_score'];?>分 <font size=2 color=red>(搭配成績單輸出使用,達此分數才有社團認證)</font></td>
		</tr>

		<tr>
			<td width="150" align="right" >是否開放選修</td>
			<td><?php echo $Y[$CLUB['club_open']];?></td>
		</tr>
		<tr>
			<td width="150" align="right" style="font-size:10pt">開放選修起始時間</td>
			<td><?php echo $SETUP['choice_sttime'];?>
			</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:10pt">開放選修結束時間</td>
			<td><?php echo $SETUP['choice_endtime'];?></td>
		</tr>
		<tr>
			<td width="150" align="right" valign="top">社團簡介</td>
			<td><?php echo $CLUB['club_memo'];?></td>
		</tr>
	</table>	
	<?php
} // end function


//列出學生所有社團活動記錄
function list_club_record($student_sn) {
 global $CONN;
 	$query="select * from association where student_sn='$student_sn'";
  $res=$CONN->Execute($query) or die("SQL錯誤:$query");
  $i=0;
  $S=array();
  
  if ($res->NumRows()>0) {
  	while($row=$res->FetchRow()){
  		$i++;
  		foreach ($row as $k=>$v) {
  		 $S[$i][$k]=$v;
  		}
			$sql="select a.seme_class,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.student_sn='$student_sn' and a.seme_year_seme='".$row['seme_year_seme']."'";
			$res_stud=$CONN->Execute($sql) or die("SQL錯誤:$sql");
			$S[$i]['seme_class']=$res_stud->fields['seme_class'];
			$S[$i]['seme_num']=$res_stud->fields['seme_num'];
			$S[$i]['stud_name']=$res_stud->fields['stud_name'];
  	} // end while
  	
  	print_student_record($S);
  	
  }

}

//列表
function print_student_record($student) {
	global $school_long_name;
?>
	<table border="0" width="100%">
	  <tr>
	     <td align="center" style="font-size:16pt;font-family:標楷體">
	     	<?php
	     	 	echo $school_long_name."學生社團活動紀錄表";	     	
	     	?>
	    </td>
	</table>
	<table border="1" style="border-collapse:collapse" bordercolor="#800000" width="100%">
	  <tr bgcolor="#EEEEEE">
	    <td align="center">學期</td>
	    <td align="center">班級</td>
	    <td align="center">座號</td>
	    <td align="center">姓名</td>
	    <td align="center">參加社團</td>
	    <?php if ($_POST['score_list']) { ?>
	    <td align="center">成績</td>
	  	<?php } ?>
	    <td align="center">職務</td>
	    <td>指導老師評語</td>
	    <td>學生自我省思</td>
	  </tr>
	  <?php
		foreach ($student as $v) {
		?>
	  <tr>
	    <td align="center"><?php echo $v['seme_year_seme'];?></td>
	    <td align="center"><?php echo $v['seme_class'];?></td>
	    <td align="center"><?php echo $v['seme_num'];?></td>
	    <td align="center"><?php echo $v['stud_name'];?></td>
	    <td align="center"><?php echo $v['association_name'];?></td>
	    <?php if ($_POST['score_list']) { ?>
	    <td align="center"><?php echo $v['score'];?></td>
	  	<?php } ?>
	    <td align="center"><?php echo $v['stud_post'];?></td>
	    <td><?php echo $v['description'];?></td>
	    <td><?php echo $v['stud_feedback'];?></td>
	  </tr>
		
		<?php
	  }
	  
	  ?>	  
	</table>
	<table border="0" width="100%">
		<tr>
			<td valign="top">
				&nbsp;
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
	<p style='page-break-after:always'>&nbsp;</p>
<?php
} // end fucntion



//顯示社團學員名單
function list_club_members($club_sn) {
	global $CONN;
  $query="select a.student_sn, b.seme_class,b.seme_num,c.stud_name from association a,stud_seme b,stud_base c where a.club_sn='$club_sn' and a.student_sn=b.student_sn and a.student_sn=c.student_sn and a.seme_year_seme=b.seme_year_seme and (c.stud_study_cond=0 or c.stud_study_cond=2) order by seme_class,seme_num";
  $res=$CONN->Execute($query);
  $i=0;
  if ($res->NumRows()>0) {
  ?>
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF;font-size:10pt">※編列在此社團的學員:<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>
  			</td>
  	</tr>
  </table>
   <table border="1" width="100%" style="border-collapse:collapse" bordercolor="#CCCCCC">
  <?php
  while(!$res->EOF){

   $seme_class=$res->fields['seme_class'];
   $seme_num=$res->fields['seme_num'];
   $stud_name=$res->fields['stud_name'];
	 $student_sn=$res->fields['student_sn'];
	    
   $i++;
   if ($i%5==1) echo "<tr>";
   ?>
   <td style="font-size:9pt">
   	 <input type="checkbox" name="selected_stud[]" value="<?php echo $student_sn; ?>">
   	 <?php echo $i.".".$stud_name."(".$seme_class.sprintf('%02d',$seme_num).")";?>
   </td>
   <?php
   if ($i%5==0) echo "</tr>";
   $res->MoveNext();
  } // end while
  ?>
	</table>
  <?php
   return true;
  } else{
  	echo "<font color=red>※本社團尚未有學員編製!</red>";
  	return false;
  }
} // end function
//=====================================================================================
//社團期初設定
function form_club_setup ($year_seme) {
	$SETUP=get_club_setup($year_seme);
	?>
	<table border="1" style="border-collapse:collapse" bgcolor="#D8D8EB" bordercolor="#000000" cellpadding="3" width="100%">
		<tr>
			<td width="150" align="right" style="font-size:12pt;color:#800000">學期</td>
			<td><?php echo getYearSeme($SETUP['year_seme']);?></td>
			<input type="hidden" name="year_seme" value="<?php echo $SETUP['year_seme'];?>">
		</tr>
		<tr>
			<td width="150" align="right" style="font-size:12pt;color:#800000">開放選修起始時間</td>
			<td><input type="text" name="choice_sttime_date" id="choice_sttime_date" size="12" value="<?php echo substr($SETUP['choice_sttime'],0,10);?>">
					<script type="text/javascript">
				    new Calendar({
  		      		inputField: "choice_sttime_date",
   		     		dateFormat: "%Y-%m-%d",
    		   		trigger: "choice_sttime_date",
    		    	bottomBar: true,
    		    	weekNumbers: false,
    		    	showTime: 24,
    		    	onSelect: function() {this.hide();}
				    });
					</script>
	 				 <?php SelectTime('choice_sttime_hour',substr($SETUP['choice_sttime'],11,2),24);?>點
					 <?php SelectTime('choice_sttime_min',substr($SETUP['choice_sttime'],14,2),60);?>分
			</td>
		</tr>
		<tr>
			<td width="150" align="right" style="font-size:12pt;color:#800000">開放選修結束時間</td>
			<td>
				<input type="text" name="choice_endtime_date" id="choice_endtime_date" size="12"  value="<?php echo substr($SETUP['choice_endtime'],0,10);?>">
					<script type="text/javascript">
				    new Calendar({
  		      		inputField: "choice_endtime_date",
   		     		dateFormat: "%Y-%m-%d",
    		   		trigger: "choice_endtime_date",
    		    	bottomBar: true,
    		    	weekNumbers: false,
    		    	showTime: 24,
    		    	onSelect: function() {this.hide();}
				    });
					</script>
				<?php SelectTime('choice_endtime_hour',substr($SETUP['choice_endtime'],11,2),24);?>點
				<?php SelectTime('choice_endtime_min',substr($SETUP['choice_endtime'],14,2),60);?>分
			</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">預設每社團人數</td>
			<td><input type="text" name="student_num" value="<?php echo $SETUP['student_num'];?>" size="3">人</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">預設學生可選志願數</td>
			<td>
				 <select size="1" name="choice_num">
				 	<?php
				 	//最多可設定20個志願
				 	for ($i=1;$i<=20;$i++) {
				 	?>
				 	 <option value="<?php echo $i;?>"<?php if ($SETUP['choice_num']==$i) echo " selected";?>><?php echo $i;?></option>
				 	<?php
				 	}
				 	?>
				 </select>個 <font size=2 color=red>(請勿超過可選修的社團總數)</font>
				
				<!-- <input type="text" name="choice_num" value="<?php echo $SETUP['choice_num'];?>" size="3">個 --->
			</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">選修時允許超選</td>
			<td>
				<input type="radio" name="choice_over" value="0" <?php if ($SETUP['choice_over']==0) echo "checked";?> onclick="check_no_choice_over();">不允許 
				<input type="radio" name="choice_over" value="1" <?php if ($SETUP['choice_over']==1) echo "checked";?>>允許超選
				<br>
				<font size="2" color=red>例如：若某社團預計招收30人，是否允許超過30個學生預選這個社團，通常設為允許，若貴校採先搶先贏制則選不允許。</font>
			</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">落選學生自動編班</td>
			<td><input type="radio" name="choice_auto" value="0" <?php if ($SETUP['choice_auto']==0) echo "checked";?>>不自動編班 <input type="radio" name="choice_auto" value="1" <?php if ($SETUP['choice_auto']==1) echo "checked";?>>落選學生自動編入班級</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">允許一個指導老師指導多個社團</td>
			<td>
				<input type="radio" name="teacher_double" value="0" <?php if ($SETUP['teacher_double']==0) echo "checked";?>>不允許
				<input type="radio" name="teacher_double" value="1" <?php if ($SETUP['teacher_double']==1) echo "checked";?>>允許
			</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">允許一個學生參加多個社團</td>
			<td>
				<input type="radio" name="multi_join" value="0" <?php if ($SETUP['multi_join']==0) echo "checked";?>>不允許
				<input type="radio" name="multi_join" value="1" <?php if ($SETUP['multi_join']==1) echo "checked";?>>允許
				<br>
				<font size="2" color=red>當此處選擇不允許時，若一個學生已進行選課才被手動指定社團，則其選課資料將自動失效，不會被用於編班。</font>				
			</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">導師可查詢班級成績</td>
			<td>
				<input type="radio" name="show_score" value="0" <?php if ($SETUP['show_score']==0) echo "checked";?>>不開放
				<input type="radio" name="show_score" value="1" <?php if ($SETUP['show_score']==1) echo "checked";?>>開放
			</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">導師可查詢班級學生自我省思</td>
			<td>
				<input type="radio" name="show_feedback" value="0" <?php if ($SETUP['show_feedback']==0) echo "checked";?>>不開放
				<input type="radio" name="show_feedback" value="1" <?php if ($SETUP['show_feedback']==1) echo "checked";?>>開放
			</td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:12pt;color:#800000">指導老師可查詢學生自我省思</td>
			<td>
				<input type="radio" name="show_teacher_feedback" value="0" <?php if ($SETUP['show_teacher_feedback']==0) echo "checked";?>>不開放
				<input type="radio" name="show_teacher_feedback" value="1" <?php if ($SETUP['show_teacher_feedback']==1) echo "checked";?>>開放
			</td>
		</tr>

	</table>
	
	
	<?php
} // end function

//社團表單
function form_club ($CLUB) {
	global $school_kind_name,$SETUP;
	?>
	<table border="1" style="border-collapse:collapse" bgcolor="#D8D8EB" bordercolor="#000000" cellpadding="3" width="100%">
		<tr>
			<td width="150" align="right" >學期</td>
			<td><?php echo getYearSeme($CLUB['year_seme']);?></td>
			<input type="hidden" name="year_seme" value="<?php echo $CLUB['year_seme'];?>">
		</tr>
		<tr>
			<td width="150" align="right" >社團名稱</td>
			<td><input type="text" name="club_name" value="<?php echo $CLUB['club_name'];?>"></td>
		</tr>
		<tr>
			<td width="150" align="right" >社團指導老師</td>
			<td>
					<select name="club_teacher" size="1">
						<?php 
						 if ($CLUB['club_teacher']=="") {
						 	?>
						 	 <option value="" style="color:#FF00FF">請選擇...</option>
						 	<?php
						 }
						  $teacher_array=teacher_base();
						  foreach ($teacher_array as $teacher_sn=>$name) {
						  	  if (chk_if_exist_teacher($CLUB['year_seme'],$teacher_sn) and $teacher_sn!=$CLUB['club_teacher'] and $SETUP['teacher_double']==0) continue;
						  	?>
						  	 <option value="<?php echo $teacher_sn;?>"<?php if ($teacher_sn==$CLUB['club_teacher']) echo " selected";?>><?php echo $name; ?></option>
						  	<?php
						  }
						?>
					</select>
			</td>
		</tr>
		<tr>
			<td width="150" align="right" >所屬年級</td>
			<td>
			<?php
			    $class_year_array=get_class_year_array(sprintf('%d',substr($CLUB['year_seme'],0,3)),sprintf('%d',substr($CLUB['year_seme'],-1)));
                foreach ($class_year_array as $K=>$class_year_name) {
                	?>
                	<input type="radio" name="club_class" value="<?php echo $K;?>" <?php if ($CLUB['club_class']==$K) echo "checked";?>><?php echo $school_kind_name[$K];?>級 &nbsp;
                	<?php
                }			 
			?>
			  <input type="radio" name="club_class" value="100" <?php if ($CLUB['club_class']=='100') echo "checked";?>>跨年級 
			</td>
		</tr>
		<tr>
			<td width="150" align="right" >預計開班人數</td>
			<td>
				男生<input type="text" name="stud_boy_num" value="<?php echo $CLUB['stud_boy_num'];?>" size="3" onblur="chk_stud_num()">人, 				
				女生<input type="text" name="stud_girl_num" value="<?php echo $CLUB['stud_girl_num'];?>" size="3"  onblur="chk_stud_num()">人
				(總計<input type="text" style="color:#FF0000" name="club_student_num" value="<?php echo $CLUB['club_student_num'];?>" size="3"  onblur="chk_stud_num()">人)
			
			</td>
		</tr>
		<tr>
			<td width="150" align="right" >忽略性別編班</td>
			<td style="color:#FF0000;font-size:10pt"><input type="checkbox" name="ignore_sex" value="1" <?php if ($CLUB['ignore_sex']==1) echo "checked";?>>系統編班時忽略前項男女生人數設定</td>
		</tr>
		<tr>
			<td width="150" align="right" >上課地點</td>
			<td><input type="text" name="club_location" value="<?php echo $CLUB['club_location'];?>" size="20"></td>
		</tr>
		<tr>
			<td width="150" align="right" >通過標準</td>
			<td><input type="text" name="pass_score" value="<?php echo $CLUB['pass_score'];?>" size="3"><font size=2 color=red>(學生得分必須達此標準,才能獲得本社團認證)</font></td>
		</tr>

		<tr>
			<td width="150" align="right" >是否開放選修</td>
			<td><input type="radio" name="club_open" value="0" <?php if ($CLUB['club_open']==0) echo "checked";?>>不開放選修 <input type="radio" name="club_open" value="1" <?php if ($CLUB['club_open']==1) echo "checked";?>>開放選修</td>
		</tr>
		<tr>
			<td width="150" align="right" style="font-size:10pt">開放選修起始時間</td>
			<td><?php echo $SETUP['choice_sttime'];?></td>
		</tr>
		<tr>
			<td width="150" align="right"  style="font-size:10pt">開放選修結束時間</td>
			<td><?php echo $SETUP['choice_endtime'];?>
			</td>
		</tr>
		<tr>
			<td width="150" align="right" valign="top">社團簡介</td>
			<td><textarea name="club_memo" rows="10" cols="64"><?php echo $CLUB['club_memo'];?></textarea></td>
		</tr>
	</table>
	
	
	<?php
} // end function
//========================================================================================
function get_club_setup($year_seme) {
 $query="select * from stud_club_setup where year_seme='$year_seme'";
 $result=mysql_query($query);
 if (mysql_num_rows($result)) {
  $setup=mysql_fetch_array($result);
  $setup['error']=0;
 }else{
 	$setup['year_seme']=$year_seme;
 	$setup['choice_sttime']=date("Y-m-d 08:00:00");
 	$setup['choice_endtime']=date("Y-m-d 16:00:00");
 	$setup['choice_num']=5; //最多可選志願數
 	$setup['choice_over']=1; //預選時是否允許超收限制人數
 	$setup['choice_auto']=1; //排序完將未選到社團的學生自動排入尚有名額的社團
 	$setup['student_num']=32; //預設社團人數
 	$setup['show_score']=1;
 	$setup['show_feedback']=1;
 	$setup['show_teacher_feedback']=1;
 	$setup['teacher_double']=0;
 	$setup['multi_join']=0;  //2013.09.10 是否允許參加多個社團
 	$setup['error']=1;
 }
 return $setup;
}

//========================================================================================
//把 seme_class轉成中文 , 如:701轉成一年2班
function get_seme_class_2_name($c_year,$c_name) {
	global $school_kind_name;
	$NAME=$school_kind_name[$c_year].$c_name."班";
	return $NAME;	
}

//取得某學期社團數目
function get_seme_club_num($year_seme) {
	$query="select count(*) from stud_club_base where year_seme='$year_seme'";
	$result=mysql_query($query);
	list($num)=mysql_fetch_row($result);
	
	return $num;
	
}

//取得年級社團數目
function get_club_num($year_seme,$club_class) {
	$query="select count(*) from stud_club_base where year_seme='$year_seme' and club_class='$club_class'";
	$result=mysql_query($query);
	list($num)=mysql_fetch_row($result);
	
	return $num;
	
}

//取得社團學生人數
function get_club_student_num($year_seme,$club_sn) {
	$query="select * from association where seme_year_seme='$year_seme' and club_sn='$club_sn'";
  $num[0]=mysql_num_rows(mysql_query($query));
  
  //男生
	$query="select a.sn from association a,stud_base b where a.seme_year_seme='$year_seme' and a.club_sn='$club_sn' and a.student_sn=b.student_sn and b.stud_sex='1' and (b.stud_study_cond=0 or b.stud_study_cond=2)";
  $num[1]=mysql_num_rows(mysql_query($query));

  //女生
	$query="select a.sn from association a,stud_base b where a.seme_year_seme='$year_seme' and a.club_sn='$club_sn' and a.student_sn=b.student_sn and b.stud_sex='2' and (b.stud_study_cond=0 or b.stud_study_cond=2)";
  $num[2]=mysql_num_rows(mysql_query($query));

	return $num;
	
}

//取得社團基本資料
function get_club_base($club_sn) {
	$query="select * from stud_club_base where club_sn='$club_sn'";
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	
	return $row;
	
}

//取得社團所屬年級
function get_club_class($club_sn) {
	$query="select club_class from stud_club_base where club_sn='$club_sn'";
	$result=mysql_query($query);
	$row=mysql_fetch_row($result);
	
	list($club_class)=$row;
	
	return $club_class;
	
}

//依 sn 取得學生資料 array
function get_student($student_sn,$seme_year_seme) {
  $query="select a.student_sn,a.seme_class,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.student_sn='$student_sn' and a.seme_year_seme='$seme_year_seme' and b.student_sn='$student_sn'";
  $result=mysql_query($query);
  $row=mysql_fetch_array($result);
  
  return $row;

}

//取學生姓名
function get_stud_name($student_sn) {
  $query="select stud_name from stud_base where student_sn='$student_sn'";
  $result=mysql_query($query);
  $row=mysql_fetch_row($result);
   list($stud_name)=$row;
  return $stud_name;

}

//檢查學生是否參加社團
function check_student_joined_club($student_sn,$seme_year_seme) {
  $query="select * from association where student_sn='$student_sn' and seme_year_seme='$seme_year_seme'";
  $res=mysql_query($query);
  if (mysql_num_rows($res)>0) {
  	return true;
  } else {
  	return false;
  }
}

//取得學生參加的社團
function get_student_join_club($student_sn,$seme_year_seme="") {
	global $CONN;
  $query="select a.*,b.club_name from association a,stud_club_base b where a.student_sn='$student_sn' and a.club_sn=b.club_sn";
  if ($seme_year_seme!="") $query.=" and a.seme_year_seme='$seme_year_seme'";
  $query.=" order by seme_year_seme";
  //$result=mysql_query($query);
  
  $result=$CONN->Execute($query) or die("Error! Query=".$query);
  
  if ($result->RecordCount()) {
  	//整理該生的每個社團的資料
   while ($row=$result->FetchRow()) {
    $my_club[$row['club_sn']]['club_sn']=$row['club_sn'];
    $my_club[$row['club_sn']]['club_name']=$row['club_name'];
    $my_club[$row['club_sn']]['seme_year_seme']=$row['seme_year_seme'];
    $my_club[$row['club_sn']]['association_name']=$row['association_name'];
    $my_club[$row['club_sn']]['score']=$row['score'];
    $my_club[$row['club_sn']]['description']=$row['description'];
    $my_club[$row['club_sn']]['stud_post']=$row['stud_post'];
    $my_club[$row['club_sn']]['stud_feedback']=$row['stud_feedback'];
    }
		 
		return $my_club;  
  
  }else{
  	return false;
  }
} // end function

//取得學生社團成績
function get_student_score($student_sn,$club_sn) {
  $query="select * from association where student_sn='$student_sn' and club_sn='$club_sn'";
  $result=mysql_query($query);
  
  $row=mysql_fetch_array($result);
  
  return $row;
	
}

//某年級社團可供選擇人數
function club_for_stud_num($club_class,$year_seme) {
	//先取得總名額
	$query="select sum(club_student_num),sum(stud_boy_num),sum(stud_girl_num) from stud_club_base where year_seme='$year_seme' and club_open='1' and club_class='$club_class'"; 
	list($num[0],$num[1],$num[2])=mysql_fetch_row(mysql_query($query));
  
  //逐一扣除每一個社團已錄取名額
  $query="select club_sn from stud_club_base where year_seme='$year_seme' and club_open='1' and club_class='$club_class'"; 
  $result=mysql_query($query);
  while ($row=mysql_fetch_row($result)) {
   list($club_sn)=$row;
   $stud_number=get_club_student_num($year_seme,$club_sn);
   $num[0]=$num[0]-$stud_number[0]; //已參加該社團的學生數
   $num[1]=$num[1]-$stud_number[1]; //已參加該社團男生數
   $num[2]=$num[2]-$stud_number[2]; //已參加該社團女生數
  }
  return $num;
}


//取得某社團的某志願的預選志願數
function get_club_choice_rank($club_sn,$choice_rank) {
	$query="select count(*) from stud_club_temp where club_sn='$club_sn' and choice_rank='$choice_rank'";
	$result=mysql_query($query);
	list($num)=mysql_fetch_row($result);
	
	return $num;
	
}

//取得某學生某學期的某志願 club_sn
function get_seme_stud_choice_rank($year_seme,$student_sn,$choice_rank) {
 $query="select club_sn from stud_club_temp where year_seme='$year_seme' and student_sn='$student_sn' and choice_rank='$choice_rank'";
 $result=mysql_query($query);
 list($club_sn)=mysql_fetch_row($result);
 
 return $club_sn; 

}

//取得某社團某志願序的所有學生
function get_students_by_club_choice_rank($club_sn,$choice_rank) {
	global $CONN;
	$query="select a.student_sn,b.stud_name,b.curr_class_num from stud_club_temp as a,stud_base as b where a.club_sn='$club_sn' and a.student_sn=b.student_sn and a.choice_rank='$choice_rank' order by curr_class_num";
  $result=$CONN->Execute($query) or die ("讀取資料發生錯誤! sql=".$query);
  $row=$result->getRows();
 
 return $row; 

}


//取得該年級人數
function class_student_num($class,$year_seme) {
	$query="select count(*) from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_class like '".$class."%%' and b.seme_year_seme='$year_seme' and (a.stud_study_cond=0 or a.stud_study_cond=2)";
	list($num)=mysql_fetch_row(mysql_query($query));

  return $num;
}


//把 year_seme 轉換為中文
function getYearSeme($year_seme) {
	$text=sprintf('%d',substr($year_seme,0,3))."學年度第".substr($year_seme,-1)."學期";
	return $text;
}
//檢驗是否為導師
function chk_leader_teacher($c_curr_class) {
  $seme_class=sprintf('%d%02d',substr($c_curr_class,6,2),substr($c_curr_class,9,2));
  $query="select teacher_sn from teacher_post where class_num='$seme_class'";
  $result=mysql_query($query);
  list($teacher_sn)=mysql_fetch_row($result);
  
  if ($teacher_sn==$_SESSION['session_tea_sn']) {
  	return true;
  }else{
  	return false;
  }  
}

//檢驗某師是否已為社團老師
function chk_if_exist_teacher($year_seme,$teacher_sn) {
	$query="select count(*) from stud_club_base where year_seme='$year_seme' and club_teacher='$teacher_sn'";
	$result=mysql_query($query);
	list ($num)=mysql_fetch_row($result);
	
	return $num;
	
}

//檢驗某生是否已加入某社團
function chk_if_exist_stud($club_sn,$student_sn) {
	$query="select count(*) from association where club_sn='$club_sn' and student_sn='$student_sn'";
	$result=mysql_query($query);
	list ($num)=mysql_fetch_row($result);
	
	return $num;	
	
}

//做出時間下拉式選單 NAME表單項目名稱 , Time 預設時間 , Max 最大值  
function SelectTime($NAME,$Time,$Max) {
?>
<select size="1" name="<?php echo $NAME;?>">
	<?php
	for ($i=0;$i<$Max;$i++){
	?>
	<option value="<?php echo $i;?>"<?php if ($i==$Time) { echo " selected"; } ?>><?php echo $i;?></option>
	<?php
	}
	?>
	</select>
<?php
}

//取得表單資料
function make_club_post() {
	global $club_sn,$year_seme,$club_name,$club_teacher,$club_class,$club_open,$club_student_num,$club_memo,$club_location,$update_sn,$stud_boy_num,$stud_girl_num,$ignore_sex;
	$update_sn=$_SESSION['session_tea_sn'];
	foreach ($_POST as $K=>$VAR) {
		${$K}=$VAR;
	}
	$club_student_num=$stud_boy_num+$stud_girl_num;
}

function chk_if_exist_table($tbl)
{
	$tables = array();
	$q = @mysql_query("SHOW TABLES");
	while ($r = @mysql_fetch_array($q)) { 
		$tables[] = $r[0]; 
	}

	@mysql_free_result($q);

	if (in_array($tbl, $tables)) { 
  		return TRUE; 
  	} else { 
  	  return FALSE; 
  }
}  // end function

function create_table_association() {
 $query="
 CREATE TABLE IF NOT EXISTS `association` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `student_sn` varchar(10) NOT NULL,
  `seme_year_seme` varchar(4) NOT NULL,
  `association_name` varchar(40) NOT NULL,
  `score` float NOT NULL,
  `description` text NOT NULL,
  `club_sn` int(10) unsigned NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`)
) ;";

	mysql_query($query);

} // end function

//編班用的 function ========================================================================
//檢測年級編班情況
function check_arrange() {
 global $c_curr_class,$c_curr_seme;
 global $club_for_stud_num, $CLASS_num;
 global $CLASS_choiced, $CLASS_not_choiced, $CLASS_arranged, $CLASS_not_arranged; 
 
 global $student_not_choice; //array 未編班名單
 global $student_not_choice_sex; //array 未編班名單

  	  //社團可供選擇名額 , 必須足夠編班才行(要分男生,女生) , 已去除編班完成的名額 , 
  	  //原則上是否可以編班, 以總人數來看即可, 因為性別部分, 可能會因為手動指定而有差錯
  	  $club_for_stud_number=club_for_stud_num($c_curr_class,$c_curr_seme);
			$club_for_stud_num=$club_for_stud_number[0]; //總數
			$club_for_stud_boy=$club_for_stud_number[1]; //男生
			$club_for_stud_girl=$club_for_stud_number[2]; //女生
						
			//取得該年級人數
			$CLASS_num=class_student_num($c_curr_class,$c_curr_seme);
			
			//取得所有本年級學生, 再逐筆檢驗 
			
			$query="select a.student_sn,a.seme_class,a.seme_num,b.stud_sex from stud_seme a,stud_base b where a.seme_year_seme='$c_curr_seme' and a.seme_class like '".$c_curr_class."%%' and a.student_sn=b.student_sn  and (b.stud_study_cond=0 or b.stud_study_cond=2) order by seme_class,seme_num";
			$result=mysql_query($query);
			$CLASS_choiced=0; //
			$CLASS_not_choiced=$CLASS_num; //未選課人數
			$CLASS_arranged=0; //已編班學生數
			
			$i=0;
			while ($row=mysql_fetch_row($result)) {
			  list($student_sn,$seme_class,$seme_num,$stud_sex)=$row;
			  //檢查有沒有選課
			   $query_choice="select * from stud_club_temp where year_seme='$c_curr_seme' and student_sn='$student_sn'";
			  //檢查有沒有編班
			   $query_arrange="select * from association where seme_year_seme='$c_curr_seme' and student_sn='$student_sn' and club_sn!=''"; 
			   //已選課
			   if (mysql_num_rows(mysql_query($query_choice))) {
			   	 $CLASS_choiced++;
			     $CLASS_not_choiced--;
			   } elseif (mysql_num_rows(mysql_query($query_arrange))) {
			   	  //未選課, 但已編班
			      $CLASS_not_choiced--;
			   } else {   
			     $student_not_choice[$seme_class][$seme_num]=$student_sn; //未選課名單
			     $student_not_choice_sex[$seme_class][$seme_num]=$stud_sex; //未選課名單
			   }
			   
 			   if (mysql_num_rows(mysql_query($query_arrange))) {
			     $CLASS_arranged++;
			   }
			}  //end while
			
			$CLASS_not_arranged=$CLASS_num-$CLASS_arranged; //未編班人數
}

//檢測已選課, 但落選學生, 以全域變數運作
function check_choice_not_arrange() {
 global $c_curr_class,$c_curr_seme;
 global $club_for_stud_num, $CLASS_num;
 global $CLASS_choiced, $CLASS_not_choiced, $CLASS_arranged, $CLASS_not_arranged; 
 
 global $student_choice_not_arrange; //array 未編班名單
 global $student_choice_not_arrange_sex; //array 未編班名單
 
 $query="select a.student_sn,b.seme_class,b.seme_num,c.stud_sex from stud_club_temp a,stud_seme b,stud_base c where a.arranged='0' and a.year_seme='$c_curr_seme' and b.seme_year_seme='$c_curr_seme' and b.seme_class like '".$c_curr_class."%%' and a.student_sn=b.student_sn and a.student_sn=c.student_sn  and (c.stud_study_cond=0 or c.stud_study_cond=2)";
 $result=mysql_query($query);
  while ($row=mysql_fetch_row($result)) {
  	list($student_sn,$seme_class,$seme_num,$stud_sex)=$row;
    $student_choice_not_arrange[$seme_class][$seme_num]=$student_sn;
    $student_choice_not_arrange_sex[$seme_class][$seme_num]=$stud_sex;
  }
}


//將某學生編入某社團
function choice_this_stud($c_curr_seme,$club_sn,$club_name,$student_sn,$arr) {
 //檢查這個社團是否已有這個學生 , 編班人數不能加1, 傳回 false
  if (chk_if_exist_stud($club_sn,$student_sn)) {
     write_arranged_flag($c_curr_seme,$student_sn);
     return false;
  }
 //將學生編入此社團
   $query="insert into association (student_sn,seme_year_seme,association_name,club_sn) values ('$student_sn','$c_curr_seme','".addslashes($club_name)."','$club_sn')"; 
   if (mysql_query($query)) {
 //將此學生的選課志願資料皆註記為1
   write_arranged_flag($c_curr_seme,$student_sn);
 //若非落選強迫編班則將此志願註記為2 $arr為志願序, 表示是第幾志願選到課
   if ($arr>0) {
    $query="update stud_club_temp set arranged='2' where year_seme='$c_curr_seme' and student_sn='$student_sn' and club_sn='$club_sn' and choice_rank='$arr'";
    mysql_query($query);
   }
   return true; //單筆編班成功 , 傳回 true
  }else{
   echo "Error! Query=$query";
   exit();
  }
}

//註記某學生已編班
function write_arranged_flag($c_curr_seme,$student_sn) {
  $query="update stud_club_temp set arranged='1' where year_seme='$c_curr_seme' and student_sn='$student_sn'";
	if (!mysql_query($query)) {
	 echo "Error! query=$query";
	 exit();
	}else{
	 return true;
	}
}

//取得學生所選的社團
function get_stud_choice($c_curr_seme,$student_sn) {
 $query="select club_sn,choice_rank from stud_club_temp where year_seme='$c_curr_seme' and student_sn='$student_sn' order by choice_rank";
 $result=mysql_query($query);
 while ($row=mysql_fetch_row($result)) {
  list($club_sn,$choice_rank)=$row;
  $C[$choice_rank]=$club_sn;
 }
 
 return $C;
 
}

function readme() {
?>
	   <table border="0" width="100%">
	   	 <tr>
	   	  <td colspan="2" style="color:#0000FF">社團活動的運作建議流程:</td>
	   	</tr>
	   	<tr>
	   		<td align="center" width="200" style="color:#800000">《社團活動規劃》</td>
	   		<td style="font-size:10pt">在進行設定之前事先規劃好下列要點: <br>1.社團活動要利用什麼時間進行? 要開設幾個社團? 以本校的做法是每個星期四的班會及聯課活動時間進行, 然後開課方式是每個導師都要依自己的專長開一個社團, 例如: 藍球社、排球社等.<br>
	   			2.選課何時開始、何時結束, 記得到時要事先公告給學生<br>
	   			3.發調查表給各導師,請老師填寫開設社團的相關資料, 表格可參考系統內的「新增本學期的社團」的設定表單。<br>
	   		</td>
	   	</tr>
	   	<tr>
	   		<td align="center">↓</td>
	   		<td>&nbsp;</td>
	   	</tr>
	   	<tr>
	   		<td align="center" style="color:#800000">《進行社團期初設定》</td>
	   		<td style="font-size:10pt">將相關設定設好, 尤其要注意選課時間的設定</td>
	   	</tr>
	   	<tr>
	   		<td align="center">↓</td>
	   		<td>&nbsp;</td>
	   	</tr>
	   	<tr>
	   		<td align="center" style="color:#800000">《新增本學期的社團》</td>
	   		<td style="font-size:10pt">1.將自社團指導老師那邊收集回來的社團資料, 一筆一筆輸入。注意年級別要正確。<br>
	   			2.如果是踦年級的社團, 不建議開放選課, 請手動直接選擇學員, 否則編班時會有不公平的問題。<br>
	   		  3.如果跨年級社團仍要開放選課, 建議做法是依年級分成多個社團的方式處理。例如：合唱團是跨年級社團，可招收一年級和二年級的學生，總共要招收60位，那麼在系統這邊新增社團時，就分別針對一年級和二年級都各開設一個合唱團社團，兩個社團總人數仍維持60人即可。
	   		</td>
	   	</tr>
	   	<tr>
	   		<td align="center">↓</td>
	   		<td>&nbsp;</td>
	   	</tr>
	   	<tr>
	   		<td align="center" style="color:#800000">《進行選課》</td>
	   		<td style="font-size:10pt">1.提醒學生，選課的地方在【學生資料自建】模組的「社團活動」選項中。<br>
	   			2.提醒學生，選課時注意社團人數限制與已填寫志願數的情況。<br>
	   			3.提醒學生，落選或未選課者，仍會強制編班。<br>
	   			4.提醒學生，在選課結束前，隨時可以更改自己的志願序。所以要隨時注意自己喜歡的社團的志願填寫情況，若太熱門的社團，被選到的機率太低時，最好進行調整，以免最後全部落選，被編到不喜歡的社團。<br>
	   			5.管理者隨時可從【社團編班】功能裡，查詢還有那些同學尚未選課。
	   		</td>
	   	</tr>
	   	<tr>
	   		<td align="center">↓</td>
	   		<td>&nbsp;</td>
	   	</tr>
	   	<tr>
	   		<td align="center" style="color:#800000">《社團編班》</td>
	   		<td style="font-size:10pt">1.請依年級個別進行編班。<br>
	   			2.原則上編班程式會盡可能依預設設定好的性別人數進行編班，但未必能符合設定的結果。例如：藍球社原本預訂招收男女生各15人，但在選填志願時，若第一志願有就有男生30人選擇，女生卻只有10人選擇，則編班的結果仍會產生男生20人，女生10人的結果。<br>
	   			3.編班記錄會自動保留，供隨時查詢，請從【社團管理／編班記錄】進行查詢。
	   		</td>
	   	</tr>
	   	<tr>
	   		<td align="center">↓</td>
	   		<td>&nbsp;</td>
	   	</tr>
	   	<tr>
	   		<td align="center" style="color:#800000">《列印社團名單》</td>
	   		<td style="font-size:10pt">公告名單，到此已可開始社團活動的進行
	   		</td>
	   	</tr>
	   	<tr>
	   		<td align="center">↓</td>
	   		<td>&nbsp;</td>
	   	</tr>
	   	<tr>
	   		<td align="center" style="color:#800000">《社團成績輸入》</td>
	   		<td style="font-size:10pt">1.提醒老師，期末時要給學生一個成績，這個成績可以做為學生是否能夠得到本學期社團活動的認證，<font color=red>若未達標準，則期末的成績單中，學生的社團活動記錄將會是空白。</font><br>
	   			2.如果貴校不需要打成績，請在社團設定那邊，將通過標準設為0分。
	   		</td>
	   	</tr>
	   	<tr>
	   		<td align="center">↓</td>
	   		<td>&nbsp;</td>
	   	</tr>
	   	<tr>
	   		<td align="center" style="color:#800000">《學生自我省思》</td>
	   		<td style="font-size:10pt">提醒學生，期末結束前必須要到【學生資料自建】模組，填寫社團活動自我省思。
	   		</td>
	   	</tr>
	   </table>
<?php
}


//===<<以下為 JAVA Script function >>======================================================
?>
	<Script language="JavaScript">
		function club_list(club_sn) {
			document.myform.club_sn.value=club_sn;
			document.myform.mode.value='list';
			document.myform.submit();
		}
		function club_update(club_sn) {
			document.myform.club_sn.value=club_sn;
			document.myform.mode.value='update';
			document.myform.submit();
		}
		function add_members(club_sn) {
			document.myform.club_sn.value=club_sn;
			document.myform.mode.value='add_members';
			document.myform.submit();
		}
		function del_members(club_sn) {
			document.myform.club_sn.value=club_sn;
			document.myform.mode.value='del_members';
			document.myform.submit();
		}
		
		function del_club(club_sn) {
			document.myform.club_sn.value=club_sn;
			if_confirm=confirm('您確定要刪除這個社團？\n（注意！本社團所有學生資料和成績會一併刪除）');
			if (if_confirm) {
			 document.myform.mode.value='del_club';
			 document.myform.submit();
			} else {
			 return false;
			}
		}		
		
		function check_no_choice_over() {
			document.myform.choice_num.value='1';
			alert ('注意！當不允許超選模式打開時，學生可選志願數將只有１個。\n亦即社團選課模式會以類似「先搶先贏」的方式進行。');
		}
		
	  function tagall(status) {
  		var i =0;
  		while (i < document.myform.elements.length)  {
    		if (document.myform.elements[i].name.substr(0,13)=='selected_stud') {
      		document.myform.elements[i].checked=status;
    		}
    		i++;
  		}
		}
		
	//找到特定目標, 全選或全取消
	function check_copy(SOURCE,STR) {
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
		
		//CLUB 表單 POST前檢查資料
		function check_before_club_post() {
		  var save=1;
		  if (document.myform.club_name.value=='') {
		    alert('請輸入社團名稱!');
		    save=0;
		    document.myform.club_name.focus();
		    return false;
		  }
		  if (document.myform.club_teacher.value=='') {
		    alert('請選擇社團指導老師!');
		    save=0;
		    return false;
		  }
		  if (document.myform.club_location.value=='') {
		    alert('請輸入上課地點!');
		    save=0;
		    return false;
		  }
		  if (document.myform.club_memo.value=='') {
		    alert ('請簡單描寫社團簡介!');
		    save=0;
		    document.myform.club_memo.focus();
		    return false;
		  }
		  if (save==1) {
		    document.myform.submit();
		  }
		}		
		
		//統計人數
		function chk_stud_num() {
		 var student=document.myform.stud_boy_num.value*1+document.myform.stud_girl_num.value*1;	
		 document.myform.club_student_num.value=student;
		}
		
		
	</Script>		
