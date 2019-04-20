<?php

//根據班級取得任課班的所有學期
function get_class_seme_select($class_num) {
	global $IS_JHORES;	
	$data_arr=array();	
	$I=substr($class_num,0,1)-$IS_JHORES-1;	
	
	for ($i=0;$i<=$I;$i++) {
	  $now_year=curr_year()-$i;
 	  if ($i>0 or curr_seme()==2) {
	  	$k=sprintf("%03d",$now_year)."2";
	  	$v=$now_year."學年度第2學期";
	    $data_arr[$k]=$v;
	  } //end if

	  $k=sprintf("%03d",$now_year)."1";
	  $v=$now_year."學年度第1學期";
	  $data_arr[$k]=$v;
	  
	}	// end for
	
	return $data_arr; 

} //end function

//取得所有成績單 array
function get_report($mode,$the_seme,$seme_class="",$page="") {
 global $CONN,$PAGES;
 global $school_kind_name;
 switch ($_SESSION['session_who']) {
 	case '教師':
 	 $sql="select * from class_report_setup where seme_year_seme='$the_seme' and teacher_sn='{$_SESSION['session_tea_sn']}'";
 	 if ($seme_class) {
 	 $sql.=" and seme_class='$seme_class'";
 	 }
 	 $sql.=" order by title";
   if ($page>0) {
   	$st=($page-1)*$PAGES;
    $sql.=" limit ".$st.",".$PAGES;
   }
 	break;
 	case '學生': 
 		switch ($mode) {
			case 'list': //必須有開放瀏覽
 	 			$sql="select * from class_report_setup where seme_year_seme='$the_seme' and seme_class='$seme_class' and open_read=1 order by title";
			break;
			case 'input': //必須有開放輸入且為小老師
 	 			$sql="select * from class_report_setup where seme_year_seme='$the_seme' and seme_class='$seme_class' and open_input=1 and student_sn='{$_SESSION['session_tea_sn']}' order by title";
			break;
 		}	
 	break;
 }
	 $res=$CONN->Execute($sql);
	 $rec=$res->GetRows();
	 
	 foreach ($rec as $k=>$v) {
	 	$rec[$k]['seme_class_cname']=$school_kind_name[substr($v['seme_class'],0,1)].sprintf('%d',substr($v['seme_class'],1,2))."班";
		//已包含的成績筆數
		$sql="select count(*) from class_report_test where report_sn='{$rec[$k]['sn']}'";
		$res=$CONN->Execute($sql);
		$test_num=$res->fields[0];
		$rec[$k]['test_num']=$test_num; //成績數
	 }
	 
	 return $rec;
}

//取得成績單設定
function get_report_setup($the_report) {
	global $CONN,$school_kind_name;
	$sql="select * from class_report_setup where sn='$the_report'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:".$sql);
	if ($res) {
		$report=$res->FetchRow();
		$report['seme_class_cname']=$school_kind_name[substr($report['seme_class'],0,1)].sprintf('%d',substr($report['seme_class'],1,2))."班";

		return $report;
	}else{
	 echo "傳送參數錯誤!";
	 exit();
	}
}

//成績單的表單
function form_report($REP) {
	
	global $CONN,$M_SETUP;
	
 //取得目前所有班級
 $class_array=class_base();
 $class_num=get_teach_class();
 
 //若無代入值, 則自動指定任教班級
 $REP['seme_class']=($REP['seme_class']=='')?$class_num:$REP['seme_class'];
 
 //若有班級, 取得學生
 if ($REP['seme_class']!="") {
  $select_students=get_seme_class_students($REP['seme_year_seme'],$REP['seme_class']);
 }
 
?>
	<input type="hidden" name="seme_year_seme" value="<?php echo $REP['seme_year_seme'];?>">
	<table border="0" width="600"  bgcolor="#FFFFCC">
		<tr>
			<td width="100">成績單名稱</td>
			<td><input type="text" name="title" value="<?php echo $REP['title'];?>" size="50"></td>
		</tr>	
		<tr>
			<td>
				班級
			</td>
			<td>
				<select size="1" name="seme_class" onchange="document.myform.change_class.value=1;document.myform.submit()">
					<option value="">---</option>
					<?php
					 foreach ($class_array as $k=>$v) {
					 ?>
					 <option value="<?php echo $k;?>" <?php if ($k==$REP['seme_class']) echo "selected";?>><?php echo $v;?></option>
					 <?php
					 }
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				成績單小老師
			</td>
			<td>
				<select size="1" name="student_sn">
					<option value="">---</option>
					<?php
					 foreach ($select_students as $k=>$v) {
					 ?>
					 <option value="<?php echo $v['student_sn'];?>" <?php if ($v['student_sn']==$REP['student_sn']) echo "selected";?>><?php echo $v['seme_num'].$v['stud_name'];?></option>
					 <?php
					 }
					?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>開放小老師登錄成績</td>
			<td>
					<input type="radio" name="open_input" value="0"<?php if ($REP['open_input']==0) echo " checked";?>>關閉
					<input type="radio" name="open_input" value="1"<?php if ($REP['open_input']==1) echo " checked";?>>啟用
			</td>
		</tr>
		<tr>
			<td>開放學生查詢</td>
			<td>
					<input type="radio" name="open_read" value="0"<?php if ($REP['open_read']==0) echo " checked";?>>關閉
					<input type="radio" name="open_read" value="1"<?php if ($REP['open_read']==1) echo " checked";?><?php if ($M_SETUP['limit_open']==0) echo " disabled";?>>啟用
					<?php if ($M_SETUP['limit_open']==0) echo "<font size=2 color=red><i>系統預設無法調整</i></font>"; ?>
			</td>
		</tr>
		<tr>
			<td>成績單樣式</td>
			<td>
					<input type="radio" name="rep_classmates" value="0"<?php if ($REP['rep_classmates']==0) echo " checked";?>>個人
					<input type="radio" name="rep_classmates" value="1"<?php if ($REP['rep_classmates']==1) echo " checked";?><?php if ($M_SETUP['limit_classmates']==0) echo " disabled";?>>全班
					<?php if ($M_SETUP['limit_classmates']==0) echo "<font size=2 color=red><i>系統預設無法調整</i></font>"; ?>
			</td>
		</tr>
		<tr>
			<td>成績單提示總分</td>
			<td>
					<input type="radio" name="rep_sum" value="0"<?php if ($REP['rep_sum']==0) echo " checked";?>>關閉
					<input type="radio" name="rep_sum" value="1"<?php if ($REP['rep_sum']==1) echo " checked";?>>開啟
			</td>
		</tr>
		<tr>
			<td>成績單提示平均</td>
			<td>
					<input type="radio" name="rep_avg" value="0"<?php if ($REP['rep_avg']==0) echo " checked";?>>關閉
					<input type="radio" name="rep_avg" value="1"<?php if ($REP['rep_avg']==1) echo " checked";?>>開啟
			</td>
		</tr>
		<tr>
			<td>成績單提示排名</td>
			<td>
					<input type="radio" name="rep_rank" value="0"<?php if ($REP['rep_rank']==0) echo " checked";?>>關閉
					<input type="radio" name="rep_rank" value="1"<?php if ($REP['rep_rank']==1) echo " checked";?><?php if ($M_SETUP['limit_rank']==0) echo " disabled";?>>開啟
					<?php if ($M_SETUP['limit_rank']==0) echo "<font size=2 color=red><i>系統預設無法調整</i></font>"; ?>
			</td>
		</tr>	
	</table>
<?php
} //end form_report($REP)

//班級成績表單
function form_class_score($REP_SETUP,$TEST_SETUP,$SCORE) {
		//取得所有科目名稱
		$subject_arr=&get_subject_name_arr();
		//取得學生
	  $STUD=get_seme_class_students($REP_SETUP['seme_year_seme'],$REP_SETUP['seme_class']);
	  //考試日期限制 (本學期的第一天至今天)
	  $minday=(substr($REP_SETUP['seme_year_seme'],3,1)=='1')?(substr($REP_SETUP['seme_year_seme'],0,3)+1911).'0801':(substr($REP_SETUP['seme_year_seme'],0,3)+1912).'0201';
		$maxday=(substr($REP_SETUP['seme_year_seme'],3,1)=='1')?(substr($REP_SETUP['seme_year_seme'],0,3)+1912).'0131':(substr($REP_SETUP['seme_year_seme'],0,3)+1912).'0731';
		$maxday=($maxday>date('Ymd'))?date('Ymd'):$maxday;
	  ?>
<table border="0">
	<tr>
		<td valign="top">
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>
	  	<tr>
	  		<td align="center" bgcolor="#FFCCFF">-</td>
	  		<td align="center" bgcolor="#CCFFCC">日期</td>
	  		<td align="center" bgcolor="#CCFFCC">
 			    	<input type="text" name="test_date" id="test_date" onkeydown="moveit2(this,event);" size="10" value="<?php echo $TEST_SETUP['test_date'];?>">
	  				<script type="text/javascript">
							new Calendar({
  		    		inputField: "test_date",
   		    		dateFormat: "%Y-%m-%d",
    	    		trigger: "test_date",
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
	  		<td align="center" bgcolor="#FFCCFF">-</td>
	  		<td align="center" bgcolor="#CCFFCC">科目</td>
	  		<td align="center" bgcolor="#CCFFCC"><input type="text" id="SS_1" onfocus="set_ower(this,1)" onBlur="unset_ower(this)" Name="subject" size="10" onkeydown="moveit2(this,event);" value="<?php echo $TEST_SETUP['subject'];?>"></td>
	  	</tr>	
	  	<tr>
	  		<td align="center" bgcolor="#FFCCFF">-</td>
	  		<td align="center" bgcolor="#CCFFCC">範圍</td>
	  		<td align="center" bgcolor="#CCFFCC"><input type="text" id="SS_2" onfocus="set_ower(this,2)" onBlur="unset_ower(this)" name="memo" size="10" onkeydown="moveit2(this,event);" value="<?php echo $TEST_SETUP['memo'];?>"></td>
	  	</tr>	
	  	<tr>
	  		<td align="center" bgcolor="#FFCCFF">座號</td>
	  		<td align="center" bgcolor="#CCFFCC">加權</td>
	  		<td align="left" bgcolor="#CCFFCC"><input type="text" id="SS_3" onfocus="set_ower(this,3)" onBlur="unset_ower(this)" name="rate" size="5" onkeydown="moveit2(this,event);" value="<?php echo $TEST_SETUP['rate'];?>"></td>
	  	</tr>	

			<?php
			$i=3;
			foreach ($STUD as $V) {
				$i++;
			?>
	  	<tr>
	  		<td><?php echo $V['seme_num'];?></td>
	  		<td><?php echo $V['stud_name'];?></td>
	  		<td><input type="text" id="SS_<?php echo $i;?>" onfocus="set_ower(this,<?php echo $i;?>)" onBlur="unset_ower(this)" name="score[<?php echo $V['student_sn'];?>]" value="<?php echo $SCORE[$V['student_sn']];?>" onkeydown="moveit2(this,event);" size=5></td>
	  	</tr>			
			<?php
			} //end foreach
			$SS=$i;
			?>	  
	  </table>
		</td>			  	
		<!-- 列出參考科目供選擇 -->
		<td valign="top" style="color:#0000FF">
		科目名稱參考:(直接用滑鼠選擇, 或自行手動輸入)
		<table border="1" style="border-collapse:collapse" bordercolor="#CCCCCC">
			<?php
			 $i=0;
			 foreach ($subject_arr as $k=>$v) {
			  $i++;
			  if ($i%5==1) echo "<tr>";
			  ?>
			  	<td width="90" class="bg_0" id="td_<?php echo $i;?>" onMouseOver="OverLine('td_<?php echo $i;?>')" onMouseOut="OutLine('td_<?php echo $i;?>')" onclick="document.myform.subject.value='<?php echo $v['subject_name'];?>'"><?php echo $v['subject_name'];?></td>
			  <?php
			  if ($i%5==0) echo "</tr>";
			 }
			?>
		
		</table>
		
		</td>	  	
 	</tr>
</table>
	  	
	  <?php
	  
	  return $SS;
	
} // end function

//列出成績單 list_class_score(設定值,要不要顯示操作列,總分,平均,排名,是否只統計勾選考試,是否出現勾選考試列表單)
function list_class_score($REP_SETUP,$CON,$SUM="",$AVG="",$RANK="",$REAL_SUM=0,$EDIT_REAL_SUM=0) {
	global $CONN;
		//取得學生
	  $STUD=get_seme_class_students($REP_SETUP['seme_year_seme'],$REP_SETUP['seme_class']);
	  //取得所有考試
	  $TESTS=get_report_test_all($REP_SETUP['sn']);	  
	  //取得所有成績
	  $SCORES=get_report_score_all($REP_SETUP['sn'],$REAL_SUM);	  
	  
	  //不列入統計時的顏色
	  $CC[0]="#888888";
	  $CC[1]="#000000";	
	  $W_COLOR=array();  

	  
	  //統計是否要總分、平均、排名等, 以便多印欄位
	  $II=0;
	  if ($SUM==1) $II++;
	  if ($AVG==1) $II++;
	  if ($RANK==1) $II++;
	  
	  $B=($_POST['act']=='output')?0:2;
	  
	  ?>
<table border="0">
	<tr>
		<td valign="top">
		<table border='<?php echo $B;?>' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>
			<?php
				//出現可勾選是否列入統計表單
				if ($EDIT_REAL_SUM==1) {
					?>
					<tr>
	  				<td align="center" colspan="2" bgcolor="#FFFFCC">列入統計否?</td>
	  				<?php
						foreach ($TESTS as $test_setup) {
	  		 		?>
 	  				<td align="center" bgcolor="#FFFFCC">
							<input type="checkbox" name="real_sum[<?php echo $test_setup['sn'];?>]" value="1"<?php if ($test_setup['real_sum']==1) echo " checked";?><?php if ($REP_SETUP['locked']==1) echo " disabled";?>>
	  				</td>
	  		 		<?php 
	  				} // end foreach
	  				?>
						<td colspan="3" align="center">
							<input type="button" value="重設列入統計的成績" style="color:#FF0000" onclick="document.myform.act.value='check_real_sum';document.myform.submit()"<?php if ($REP_SETUP['locked']==1) echo " disabled";?>>
						</td>
					</tr>
					<?php
				} // end if ($EDIT_REAL_SUM==1)

			if ($CON) {
			?>
	  	<tr>
	  		<td align="center" bgcolor="#FFCCFF">-</td>
	  		<td align="center" bgcolor="#FFFFCC">操作</td>
	  		<?php
				foreach ($TESTS as $test_setup) {
	  		 ?>
 	  		<td align="center" bgcolor="#FFFFCC">
 	  			<?php
 	  			if ($REP_SETUP['locked']==0) {
 	  			?>
 	  				<img src="images/edit.png" style="cursor:hand" title="編輯" onclick="document.myform.act.value='edit';document.myform.option1.value='<?php echo $test_setup['sn'];?>';document.myform.submit();">
  					<img src="images/del.png"  style="cursor:hand" title="刪除" onclick="if (confirm('您確定要刪除「<?php echo $test_setup['test_date'].$test_setup['subject'];?>」嗎?')) { document.myform.act.value='DeleteOne'; document.myform.option1.value='<?php echo $test_setup['sn'];?>'; document.myform.submit(); } ">
	  			<?php
	  			}
	  			?>
	  		</td>
	  		 <?php 
	  		} // end foreach
	  		//多印欄位
	  		if ($II)	for ($i=1;$i<=$II;$i++) { echo "<td bgcolor=\"#FFCCCC\">&nbsp;</td>"; }
	  		?>
			
			</tr>				
			<?php
			} // end if ($CON)
			?>
	  	<tr>
	  		<td align="center" bgcolor="#FFCCFF">-</td>
	  		<td align="center" bgcolor="#CCFFCC">日期</td>
	  		<?php
				foreach ($TESTS as $test_setup) {
					$W_COLOR[$test_setup['sn']]=($REAL_SUM==0)?$CC[1]:$CC[$test_setup['real_sum']];
	  		 ?>
 	  		<td align="center" bgcolor="#CCFFCC" style="color:<?php echo $W_COLOR[$test_setup['sn']];?>"><?php echo $test_setup['test_date'];?></td>
	  		 <?php 
	  		} // end foreach
	  		//選擇性欄位
				if ($SUM) { echo "<td rowspan=\"4\" bgcolor=\"#FFCCCC\" align=center>總分</td>";	}
				if ($AVG) { echo "<td rowspan=\"4\" bgcolor=\"#FFCCCC\" align=center>平均</td>";	}
				if ($RANK) { echo "<td rowspan=\"4\" bgcolor=\"#FFCCCC\" align=center>名次</td>";	}
	  		?>
			</tr>
	  		<td align="center" bgcolor="#FFCCFF">-</td>
	  		<td align="center" bgcolor="#CCFFCC">科目</td>
	  		<?php
				foreach ($TESTS as $test_setup) {
  		 ?>
 	  		<td align="center" bgcolor="#CCFFCC" style="color:<?php echo $W_COLOR[$test_setup['sn']];?>"><?php echo $test_setup['subject'];?></td>
	  		 <?php 
	  		} // end foreach
	  		?>
	  	</tr>	
	  	<tr>
	  		<td align="center" bgcolor="#FFCCFF">-</td>
	  		<td align="center" bgcolor="#CCFFCC">範圍</td>
	  		<?php
				foreach ($TESTS as $test_setup) {
  		 ?>
 	 	  		<td align="center" bgcolor="#CCFFCC" style="color:<?php echo $W_COLOR[$test_setup['sn']];?>"><?php echo $test_setup['memo'];?></td>
	  		 <?php 
	  		} // end foreach
	  		?>
			</tr>
	  	<tr>
	  		<td align="center" bgcolor="#FFCCFF">座號</td>
	  		<td align="center" bgcolor="#CCFFCC">加權</td>
	  		<?php
				foreach ($TESTS as $test_setup) {
  		 ?>
 	 	  		<td align="center" bgcolor="#CCFFCC" style="color:<?php echo $W_COLOR[$test_setup['sn']];?>"><?php echo $test_setup['rate'];?></td>
	  		 <?php 
	  		} // end foreach
	  		?>
			</tr>

				<?php
			foreach ($STUD as $V) {
			?>
	  	<tr>
	  		<td><?php echo $V['seme_num'];?></td>
	  		<td><?php echo $V['stud_name'];?></td>
	  		<?php	  		
				foreach ($TESTS as $test_setup) {
  		 ?> 		  		 
 	  		<td align="center" style="color:<?php echo $W_COLOR[$test_setup['sn']];?>"><?php echo $SCORES[$V['student_sn']][$test_setup['sn']];?></td>
	  		 <?php 
	  		}
				if ($SUM) { echo "<td align=\"center\">".$SCORES[$V['student_sn']]['sum']."</td>";	}
				if ($AVG) { echo "<td align=\"center\">".number_format($SCORES[$V['student_sn']]['avg'],2)."</td>";	}
				if ($RANK) { echo "<td align=\"center\">".$SCORES[$V['student_sn']]['rank']."</td>";	}
	  		?>
	  	</tr>			
			<?php
			} //end foreach
			?>	  
	  </table>
		</td>	  	
 	</tr>
</table>	  	
	  <?php
	
} // end function

//列出成績單 list_class_score(設定值,要不要顯示操作列,總分,平均,排名)
function list_class_score_personal($REP_SETUP,$CON,$SUM="",$AVG="",$RANK="",$REAL_SUM=0) {
	global $CONN;
	  $TESTS=get_report_test_all($REP_SETUP['sn']);	  
	  //取得所有成績
	  $SCORES=get_report_score_all($REP_SETUP['sn'],$REAL_SUM);
	  $CC[0]="#888888";
	  $CC[1]="#000000";	
	  $W_COLOR=array();  
?>
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>
	<tr bgcolor="#FFCCFF">
		<td align="center">日期</td>
		<td align="center">科目</td>
		<td align="center">範圍</td>
		<td align="center">成績</td>
	</tr>
	<?php
	foreach ($TESTS as $test_setup) {
		$W_COLOR[$test_setup['sn']]=($REAL_SUM==0)?$CC[1]:$CC[$test_setup['real_sum']];
 	?> 
	<tr style="color:<?php echo $W_COLOR[$test_setup['sn']];?>">
		<td><?php echo $test_setup['test_date'];?></td>
		<td align="center"><?php echo $test_setup['subject'];?></td>
		<td><?php echo $test_setup['memo'];?></td>
		<td align="center"><?php echo $SCORES[$_SESSION['session_tea_sn']][$test_setup['sn']];?></td>
	</tr>
  <?php	
  } // end foreach
  //統計資訊
  	if ($SUM) {
  	?>
		<tr bgcolor="#FFCCCCCC">
			<td colspan="3" align="right">總分</td>
			<td align="center"><?php echo $SCORES[$_SESSION['session_tea_sn']]['sum'];?></td>
		</tr>
  	<?php
  	} // end if
  	if ($AVG) {
  	?>
		<tr bgcolor="#FFCCCCCC">
			<td colspan="3" align="right">平均</td>
			<td align="center"><?php echo number_format($SCORES[$_SESSION['session_tea_sn']]['avg'],2);?></td>
		</tr>
  	<?php
  	} // end if
  	if ($RANK) {
  	?>
		<tr bgcolor="#FFCCCCCC">
			<td colspan="3" align="right">名次</td>
			<td align="center"><?php echo $SCORES[$_SESSION['session_tea_sn']]['rank'];?></td>
		</tr>
  	<?php
  	} // end if  
	?>
</table>

<?php
} // end function

//取得成績單中的某次考試設定
function get_report_test($sn) {
  global $CONN;
  $sql="select * from class_report_test where sn='$sn'";
  $res=$CONN->Execute($sql) or die("SQL錯誤:".$sql);
  
  $test_setup=$res->FetchRow();
  
  return $test_setup;
  
}

//取得成績單中的所有考試設定依日期排列
function get_report_test_all($report_sn) {
	global $CONN;
	$sql="select * from class_report_test where report_sn='$report_sn' order by test_date";
	$res=$CONN->Execute($sql);
	$T=$res->GetRows();
	
	return $T;

}

//取得成績單中的所有成績 傳回 $SCORE[student_sn][test_sn]=score ,$REAL_SUM=1 時, 僅統計 real_sum=1的資料, 注意, 和 list function 搭配使用
function get_report_score_all($report_sn,$REAL_SUM=0) {
  global $CONN;
  //取得所有的考試
  $TESTS=get_report_test_all($report_sn);
  //依每次考試, 依序取出所有 student_sn 的成績
  foreach ($TESTS as $test_setup) {
  	//本次測試是否列入計分
  	$real_sum[$test_setup['sn']]=$test_setup['real_sum'];
  	//本次測驗所佔加權
  	$rate[$test_setup['sn']]=$test_setup['rate'];
   	//取得該次考試成績, 傳回 $S['student_sn']=score
   	$S=get_report_score($test_setup['sn']);
   		foreach ($S as $student_sn=>$score) {
   			$SCORE[$student_sn][$test_setup['sn']]=$score;
   		}// end foreach  
  } // end foreach
  
	//計算總分,平均
	$RANK=array();
	foreach ($SCORE as $student_sn=>$SS) {
	  $sum=0;
	  $rate_sum=0;
	  $all_tests=0;
	  foreach ($SS as $test_sn=>$v) {
	  	if ($REAL_SUM==1) {   //只統計有勾選的成績
	  		if ($real_sum[$test_sn]==1) {
	  			//$all_tests++;
	  		  $sum+=$v;  //實際總分
	  		  //加權計算
	  		  $all_tests+=$rate[$test_sn];    //總加權額
	  		  $rate_sum+=$v*$rate[$test_sn];  //分數乘上加權
				}  	
	  	}else{
	  		  $sum+=$v;  //實際總分
	  		  //加權計算
	  		  $all_tests+=$rate[$test_sn];    //總加權額
	  		  $rate_sum+=$v*$rate[$test_sn];  //分數乘上加權
	  	}	   
	  }
		$SCORE[$student_sn]['sum']=$sum;
		$SCORE[$student_sn]['avg']=round($rate_sum/$all_tests,2);
		$RANK[$student_sn]=$rate_sum/$all_tests; //把平均值記在 RANK array  中
		
	}
	//求排名
  arsort($RANK);
  $i=0; //目前名次
  $j=0;
  $l_score=0;
  foreach ($RANK as $student_sn=>$v) {
		$j++; 
  	if ($v!=$l_score) {
  		$i=$j;  		
  	} // end if
  	$SCORE[$student_sn]['rank']=$i;
  	$l_score=$v;  	
  } // end foreach

  return $SCORE;
  
}

 
//取得某次考試的所有成績 傳回 $SCORE['student_sn']=$score
function get_report_score($test_sn) {
  global $CONN;
  $sql="select * from class_report_score where test_sn='$test_sn'";
  $res=$CONN->Execute($sql) or die("SQL錯誤:".$sql);
  
  $S=$res->GetRows();
  
  $SCORE=array();
  
  foreach ($S as $V) { 
   		$SCORE[$V['student_sn']]=$V['score'];  
  } // end foreach
  
  return $SCORE;
  
} // end function


//成績單列表頁次
function select_pages($the_page) {
	//$_POST['option2']記載選定的page
	global $CONN,$PAGES,$the_seme;  //每頁幾筆
	$sql="select count(*) as pages  from class_report_setup where seme_year_seme='$the_seme' and teacher_sn='{$_SESSION['session_tea_sn']}'";
	$res=$CONN->Execute($sql);
	
	$ALL=$res->fields['pages'];
	
	$all_pages=ceil($ALL/$PAGES);
  
	for ($i=1;$i<=$all_pages;$i++) {
		
	 
	 if ($i==$the_page) {
	   echo "[".$i."]";
	 } else {
	  ?>
	   <a onclick="document.myform.option2.value='<?php echo $i;?>';document.myform.submit()" style="cursor:hand;color:#0000FF" title="第<?php echo $i;?>頁"><?php echo $i;?></a>
	  <?php
	 } // end if
	 
	} // end for
	
} // end function

//取得某班級學生的所有名單 2維陣列
function get_seme_class_students($the_seme,$seme_class) {
	
	global $CONN;
	
	$query="select a.student_sn,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$the_seme' and a.seme_class='$seme_class' and a.student_sn=b.student_sn order by seme_num";
	$res_stud_list=$CONN->Execute($query) or die("SQL錯誤:".$query);
	$select_students=$res_stud_list->GetRows();
	
	return $select_students;
	
}
