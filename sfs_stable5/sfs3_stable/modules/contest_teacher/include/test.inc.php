<?php
//讀取競賽者資料 return array
function get_contest_user($tsn,$student_sn) {
$query="select a.*,b.stud_id,b.stud_name,b.email_pass,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.student_sn=b.student_sn and a.student_sn=c.student_sn and a.tsn=d.tsn and d.year_seme=c.seme_year_seme and a.tsn='".$tsn."' and a.student_sn='".$student_sn."'";
 $result=mysql_query($query);
 $STID=mysql_fetch_array($result,1);
 return $STID;
}

//讀取競賽資料 return array
function get_test_setup($tsn) {
 $query="select * from contest_setup where tsn='$tsn'";
 $result=mysql_query($query);
 $TEST=mysql_fetch_array($result,1);
 //取得已報名人數
 $query="select count(*) as num from contest_user where tsn='$tsn'";
 $result=mysql_query($query);
 list($N)=mysql_fetch_row($result);
 $TEST['testuser_num']=$N;
 //取徥查資料比賽題本題數
 $query="select count(*) from contest_ibgroup where tsn='".$TEST['tsn']."'";
 list($N)=mysql_fetch_row(mysql_query($query));
 $TEST['search_ibgroup']=$N;
 //取資查資料比賽題庫總數
 $query="select count(*) from contest_itembank";
 list($N)=mysql_fetch_row(mysql_query($query));
 $TEST['search_itembank']=$N;
 return $TEST;
}

//條列競賽
function test_list($query) {
	global $PHP_CONTEST;
	$TEST=mysql_query($query);
?>
   <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0">
   	<tr bgcolor="#FFFFCC">
   		<td style="font-size:10pt;color:#800000" width="40" align="center">管理</td>
   		<td style="font-size:10pt;color:#800000" align="center">競賽標題</td>
   		<td style="font-size:10pt;color:#800000" width="100" align="center">競賽類別</td>
   		<td style="font-size:10pt;color:#800000" width="50" align="center">密碼</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">報名人數</td>
   		<td style="font-size:10pt;color:#800000" width="120" align="center">開始時間</td>
   		<td style="font-size:10pt;color:#800000" width="120" align="center">結束時間</td>
   	</tr>	
 <?php
    while ($row=mysql_fetch_array($TEST,1)) {
    	$query="select count(*) as num from contest_user where tsn='".$row['tsn']."'";
			list($N)=mysql_fetch_row(mysql_query($query));
  	?>
   	<tr>
   		<td style="font-size:10pt" align="center"><img src="images/edit.png" border="0" style="cursor:hand" onclick="document.myform.option1.value='<?php echo $row['tsn'];?>';document.myform.act.value='listone';document.myform.submit();"></td>
  		<td style="font-size:10pt"><?php echo $row['title'];?></td>
  		<td style="font-size:10pt" align="center"><?php echo $PHP_CONTEST[$row['active']];?></td>
  		<td style="font-size:10pt" align="center"><?php echo $row['password'];?></td>
  		<td style="font-size:10pt" align="center"><?php echo $N;?></td>
  		<td style="font-size:10pt" align="center"><?php echo $row['sttime'];?></td>
  		<td style="font-size:10pt" align="center"><?php echo $row['endtime'];?></td>
  	</tr>
    	<?php
    }
 ?>
   </table>
<?php
} // end function


//簽要抬頭細目
function title_simple($TEST) {
	global $PHP_CONTEST;
?>
  <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="5">
  	<tr>
  		<td width="80" align="center" style="font-size:10pt;color:#800000">競賽標題</td>
  		<td style="font-size:10pt"><?php echo $TEST['title'];?></td>
  	</tr>
  	<tr>
  		<td width="80" align="center" style="font-size:10pt;color:#800000">競賽題目</td>
  		<td style="font-size:10pt"><?php echo $TEST['qtext'];?></td>
  	</tr>
  	<tr>
  		<td width="80" align="center" style="font-size:10pt;color:#800000">競賽類別</td>
  		<td style="font-size:10pt"><?php echo $PHP_CONTEST[$TEST['active']];?></td>
  	</tr>
  </table>

<?php
}


//競賽細目
function test_main($tsn,$admin) {
	global $PHP_CONTEST,$MANAGER,$CONN;
  $query="select * from contest_setup where tsn='".$tsn."'";
  $TEST=mysql_fetch_array(mysql_query($query),1);

?>
   <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="5">
  	<tr>
  		<td width="80" align="center" style="font-size:10pt;color:#800000">競賽標題</td>
  		<td style="font-size:10pt"><?php echo $TEST['title'];?></td>
  	</tr>
    <tr>
	   <td width="80" align="center" style="font-size:10pt;color:#800000">競賽類別</td>
	   <td style="font-size:10pt"><?php echo $PHP_CONTEST[$TEST['active']];?></td>
    </tr>
	   <?php
	   if (($TEST['active']>1 and $TEST['active']<5) or $TEST['active']>6) {
		   ?>
		   <tr>
			   <td width="80" align="center" style="font-size:10pt;color:#800000">競賽題目</td>
			   <td style="font-size:10pt"><?php if ($admin==1) { echo "<font color='#FF0000'>".$TEST['qtext']."</font>"; } else { echo "競賽開始才公佈!"; }?></td>
		   </tr>
	   <?php
	   }
	   //中打或英打
	   if ($TEST['active']>4 and $TEST['active']<7) {
		   $sql="select article from contest_typebank where id='".$TEST['type_id_1']."'";
		   $res=$CONN->Execute($sql);
		   $type1=$res->fields[0];
		   $sql="select article from contest_typebank where id='".$TEST['type_id_2']."'";
		   $res=$CONN->Execute($sql);
		   $type2=$res->fields[0];
		   ?>
		   <tr>
			   <td width="80" align="center" style="font-size:10pt;color:#800000">競賽題目</td>
			   <td style="font-size:10pt">
				   <?php
				   if ($admin==1) {
					   echo "第一篇：<font color='#FF0000'>".$type1."</font><br>第二篇：<font color='#FF0000'>".$type2."</font>";
				   } else {
					   echo "競賽開始才公佈!"; }
				   ?>
			   </td>
		   </tr>
		   <?php
	   }

		if ($MANAGER) {
		?>
  	<tr>
  		<td width="80" align="center" style="font-size:10pt;color:#800000">競賽密碼</td>
  		<td style="font-size:10pt;font-color:#0000FF"><?php echo $TEST['password'];?></td>
  	</tr>
  	<?php
  	}
  	?>
   	<tr>
  		<td width="80" align="center" style="font-size:10pt;color:#800000">開始日期</td>
  		<td style="font-size:10pt"><?php echo $TEST['sttime'];?></td>
  	</tr>
  	<tr>
  		<td width="80" align="center" style="font-size:10pt;color:#800000">結束日期</td>
  		<td style="font-size:10pt"><?php echo $TEST['endtime'];?></td>
  	</tr>
  	<?php
  	
  	?>
  	<tr>
  		<td width="80" align="center" valign="top" style="font-size:10pt;color:#800000">競賽說明</td>
  		<td style="font-size:10pt"><?php echo showhtml($TEST['memo']);?></td>
  	</tr>
  	<?php
  	if ($TEST['active']>1) {
  	?>
  	<tr>
  		<td width="80" align="center" valign="top" style="font-size:10pt;color:#800000">評分細目</td>
  		<td style="font-size:10pt">
  			<?php
       $query="select * from contest_score_setup where tsn='".$TEST['tsn']."'";
       $result=mysql_query($query);
       if (mysql_num_rows($result)) {
        while ($row=mysql_fetch_array($result,1)) {
         echo $row['sco_text']."&nbsp;&nbsp;";
        }
 			 } else {
 			  echo "僅評總分100%";
 			 }
  			 ?>
  			</td>
  	</tr>
  	
  	<?php
  	}
  	?>
   </table>
<?php
} // end function

//表單_競賽
function form_contest($TEST) {
	global $PHP_CONTEST,$PHP_MEMO,$CONN;
	$sql="select id,article from contest_typebank where kind='1'";
	$res=$CONN->Execute($sql);
	$type1=$res->getRows();
	$sql="select id,article from contest_typebank where kind='2'";
	$res=$CONN->Execute($sql);
	$type2=$res->getRows();

	?>
   <table border="0" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="5">
  	<tr>
  		<td width="80" align="right" style="color:#800000">競賽標題</td>
  		<td><input type="text" name="title" size="70" value="<?php echo $TEST['title'];?>"></td>
  	</tr>
	   <tr>
		   <td width="80" align="right" style="color:#800000">競賽類別</td>
		   <td>
			   <?php
			   if ($_POST['act']=='update') {
				   echo $PHP_CONTEST[$TEST['active']];
				   ?>
				   <input type="hidden" name="active" value="<?php echo $TEST['active'];?>">
				   <?php
			   } else {
				   ?>
				   <select size="1" name="active" onchange="automemo()">
					   <option value="0"<?php if ($TEST['active']==0) { echo " selected"; } ?> style="color:#FF0000">--請選擇類別--</option>
					   <?php
					   foreach ($PHP_CONTEST as $k=>$v) {
						   if ($k==1 or $k==3 ) continue;  // 2017 年取消查資料比賽及動畫繪圖比賽
						   ?>
						   <option value="<?= $k ?>"<?php if ($TEST['active']==$k) { echo " selected"; } ?>><?php echo $PHP_CONTEST[$k];?></option>
					   		<?php
					   }
					   ?>
				   </select>
				   <?php
			   }
			   ?>
		   </td>
	   </tr>
  	<tr id="test_article" style="display:<?php echo (($TEST['active']<5 and $TEST['active']>1) or $TEST['active']>6)?"table-row":"none";?>">
  		<td width="80" align="right" style="color:#800000">競賽題目<br><font size=2>(可加入html標籤)</font></td>
  		<td><textarea name="qtext" cols="70" rows="6"><?php echo $TEST['qtext'];?></textarea></td>
  	</tr>
	   <tr id="type1_article" style="display:<?php echo ($TEST['active']==5)?"table-row":"none";?>">
		   <td width="80" align="right" style="color:#800000" valign="top">中打文章</td>
		   <td>
			   <div>
			   <span style="border-style: solid;border-width:thin">第1篇</span>
			   <select size="1" name="c_type_id_1">
				   <option value="">請選擇</option>
				   <?php
				   foreach ($type1 as $v) {
					   ?>
						<option value="<?= $v['id'] ?>" <?php if ($v['id']==$TEST['type_id_1']) echo " selected";?>><?= $v['article']?></option>
				   		<?php
				   }
				   ?>

			   </select>
			   </div>
			   <div style="margin-top: 5px">
			   <span style="border-style: solid;border-width:thin">第2篇</span>
			   <select size="1" name="c_type_id_2">
				   <option value="">請選擇</option>
				   <?php
				   foreach ($type1 as $v) {
					   ?>
					   <option value="<?= $v['id'] ?>" <?php if ($v['id']==$TEST['type_id_2']) echo " selected";?>><?= $v['article']?></option>
					   <?php
				   }
				   ?>
			   </select>
			   </div>
		   </td>
	   </tr>
	   <tr id="type2_article" style="display:<?php echo ($TEST['active']==6)?"table-row":"none";?>">
		   <td width="80" align="right" style="color:#800000" valign="top">英打文章</td>
		   <td>
			   <div>
			   <span style="border-style: solid;border-width:thin">第1篇</span>
			   <select size="1" name="e_type_id_1">
				   <option value="">請選擇</option>
				   <?php
				   foreach ($type2 as $v) {
					   ?>
					   <option value="<?= $v['id'] ?>" <?php if ($v['id']==$TEST['type_id_1']) echo " selected";?>><?= $v['article']?></option>
					   <?php
				   }
				   ?>

			   </select>
			   </div>
			   <div style="margin-top: 5px">
			   <span style="border-style: solid;border-width:thin">第2篇</span>
			   <select size="1" name="e_type_id_2">
				   <option value="">請選擇</option>
				   <?php
				   foreach ($type2 as $v) {
					   ?>
					   <option value="<?= $v['id'] ?>" <?php if ($v['id']==$TEST['type_id_2']) echo " selected";?>><?= $v['article']?></option>
					   <?php
				   }
				   ?>
			   </select>
				   </div>
		   </td>
	   </tr>

   	<tr>
  		<td width="80" align="right" style="color:#800000">競賽密碼</td>
  		<td><input type="text" name="password" size="4" value="<?php echo $TEST['password'];?>"><font size=2 color=red>(最多四個字元, 保留空白則參加本競賽不需密碼)</font></td>
  	</tr>	
   	<tr>
  		<td width="80" align="right" style="color:#800000">開始日期</td>
  		<td>
  			<input type="text" name="sday" id="sday" size="10" value="<?php echo substr($TEST['sttime'],0,10);?>">
  			<button id="start_date">...</button>
					<script type="text/javascript">
				    new Calendar({
  		      inputField: "sday",
   		      dateFormat: "%Y-%m-%d",
    		    trigger: "start_date",
    		    bottomBar: true,
    		    weekNumbers: false,
    		    showTime: 24,
    		    onSelect: function() {this.hide();}
				    });
					</script>

  			時間：<?php SelectTime('stime_hour',substr($TEST['sttime'],-8,2),24);?>點<?php SelectTime('stime_min',substr($TEST['sttime'],-5,2),60);?>分
  			</td>
  	</tr>
  	<tr>
  		<td width="80" align="right" style="color:#800000">結束日期</td>
  		<td>
  			<input type="text" name="eday" id="eday" size="10" value="<?php echo substr($TEST['endtime'],0,10);?>">
  			<button id="end_date">...</button>
					<script type="text/javascript">
				    new Calendar({
  		      inputField: "eday",
   		      dateFormat: "%Y-%m-%d",
    		    trigger: "end_date",
    		    bottomBar: true,
    		    weekNumbers: false,
    		    showTime: 24,
    		    onSelect: function() {this.hide();}
				    });
					</script>

  			時間：<?php SelectTime('etime_hour',substr($TEST['endtime'],-8,2),24);?>點<?php SelectTime('etime_min',substr($TEST['endtime'],-5,2),60);?>分
  		</td>
  	</tr>
  	<tr>
  		<td width="80" align="right" valign="top" style="color:#800000">競賽說明</td>
  		<td><textarea rows="10" name="memo" cols="70"><?php echo $TEST['memo'];?></textarea></td>
  	</tr>
  	<tr>
  		<td width="80" align="right" valign="top" style="color:#800000">開放評分</td>
  		<td> 
  			<input type="radio" name="open_judge" value="0"<?php if (@$TEST['open_judge']==0) { echo " checked"; }?>>關閉
  			<input type="radio" name="open_judge" value="1"<?php if (@$TEST['open_judge']==1) { echo " checked"; }?>>開啟
  			<font style="color:#FF0000;font-size:10pt">(※請確認競賽已結束, 再利用競賽管理功能將此選項打開。)</font>
  		</td>
  	</tr>
  	<tr>
  		<td width="80" align="right" valign="top" style="color:#800000">公佈成績</td>
  		<td>
  			<input type="radio" name="open_review" value="0"<?php if (@$TEST['open_review']==0) { echo " checked"; }?>>關閉
  			<input type="radio" name="open_review" value="1"<?php if (@$TEST['open_review']==1) { echo " checked"; }?>>開啟
  			<font style="color:#FF0000;font-size:10pt">(※請確認評審已評分完畢, 並指定得獎作品, 再利用競賽管理功能將此選項打開。)</font>
  		</td>
  	</tr>
  	<tr>
  		<td width="80" align="right" valign="top" style="color:#800000">允許刪除</td>
  		<td>
  			<input type="radio" name="delete_enable" value="0"<?php if (@$TEST['delete_enable']==0) { echo " checked"; }?>>關閉
  			<input type="radio" name="delete_enable" value="1"<?php if (@$TEST['delete_enable']==1) { echo " checked"; }?>>開啟
  			<font style="color:#FF0000;font-size:10pt">(※若開啟, 將提示「刪除競賽」功能選項。)</font>
  		</td>
  	</tr>

  	<?php
  	//若為新增模式, 是否採用預設評分細項
    if ($_POST['act']=="insert") {
    ?>
  	<tr>
  		<td width="80" align="right" valign="top" style="color:#800000">評分細項</td>
  		<td><input type="checkbox" name="init_score_setup" value="1" checked>啟用預設細項 <font style="color:#FF0000;font-size:10pt">(※若沒有勾項, 競賽設定裡將預設只有「總分」這個項目。)</font></td>
  	</tr>
    <?php
    } elseif ($TEST['active']>1) {
    ?>
 		<tr>
  		<td width="80" align="right" valign="top" style="color:#800000">評分細項</td>
  		<td>
    <?php
    //取出本競賽的評分細項, 會用到 act, sco
      $query="select * from contest_score_setup where tsn='".$TEST['tsn']."'";
      $result=mysql_query($query);
      if (mysql_num_rows($result)) {
    	?>  	
  			<table border="0" cellspacing="2">
  		   <tr>
  		   	<?php
  		   	 while ($row=mysql_fetch_array($result,1)) {
  		   	  ?>
  		   	  <td>
  		   	  	<table border="1"  bgcolor="#FFFF99" bordercolor="#FF9900" style="border-collapse: collapse">
  		   	    <tr><td style="color:#0066FF;font-size:10pt"><?php echo $row['sco_text'];?><a style="cursor:hand" onclick="if (confirm('您確定要\n刪除<?php echo $row['sco_text'];?>?')) { document.myform.option2.value='<?php echo $row['sco_sn'];?>';check_test_form('del_score_setup'); }"> <img src="./images/del.png"  border="0"></a></td></tr>
  		   	    </table>	
  		   	  </td>
  		   	  <?php
  		   	 }
  		   	?>
  		   </tr>
  		  </table>
  		  <?php
  		   }
  		  ?>
  		  <!---新增評分細目的表單 --->
  		  <table border="0" width="100%">
  		   <tr>
  		   	<td><input type="text" name="sco_text" value="" size="20"><input type="button" value="新增細目" onclick="if (document.myform.sco_text.value!='') { check_test_form('add_score_setup'); }"></td>
  		  </tr>
  		  </table>
  		</td>
  	</tr>
    	<?php
      
    } //end if $mode
  	?>
  </table>

<?php
} // end function


//列出競賽報名名單
function list_user($tsn,$act) {
	global $UPLOAD_U;
	$C[0]="#FFE8FF";
	$C[1]="#E8FFE8";
	//取出競賽設定
  $TEST=get_test_setup($tsn);
  //取出名單
	$query="select a.*,b.stud_id,b.stud_name,b.email_pass,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.student_sn=b.student_sn and a.student_sn=c.student_sn and a.tsn=d.tsn and d.year_seme=c.seme_year_seme and a.tsn='".$tsn."' and ifgroup='' order by seme_class,seme_num";
	$result=mysql_query($query);
	
?>
   <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="1">
   	<tr bgcolor="#FFFFCC">
   		<td style="font-size:10pt;color:#800000" width="50" align="center">管理</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">帳號(學號)</td>
   		<td style="font-size:10pt;color:#800000" width="60" align="center">姓名</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">班級</td>
   		<td style="font-size:10pt;color:#800000" width="40" align="center">座號</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">登入密碼</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">登入次數</td>
   		<td style="font-size:10pt;color:#800000" width="130" align="center">最後登入</td>
   		<td style="font-size:10pt;color:#800000" >競賽記錄</td>
   	</tr>	
 <?php
    $j=0;
    while ($row=mysql_fetch_array($result,1)) {
    	$j++;
    	$j=$j%2;
    	//組員資料
    	//$query="select * from contest_user where tsn='".$tsn."' and ifgroup='".$row['stid']."' order by class_num";
    	$query="select a.*,b.stud_id,b.stud_name,b.email_pass,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.student_sn=b.student_sn and a.student_sn=c.student_sn and a.tsn=d.tsn and d.year_seme=c.seme_year_seme and a.tsn='".$tsn."' and ifgroup='".$row['student_sn']."' order by seme_class,seme_num";
    	$GROUPS=mysql_query($query);
    	//作答記錄
    	
    	if ($TEST['active']==1) {
			//查資料
			$REC = get_stud_record1_info($TEST, $row['student_sn']);
			//打字比賽
		} elseif ($TEST['active']>4 and $TEST['active']<7 ) {
			$REC = get_stud_record_type($TEST, $row['student_sn']);
		}else{
    	 //上傳作品
    	 $REC=get_stud_record2_info($TEST,$row['student_sn']);
    	}
     	//班級轉中文
    	$class_id=sprintf('%03d_%d_%02d_%02d',substr($TEST['year_seme'],0,3),substr($TEST['year_seme'],3,1),substr($row['seme_class'],0,1),substr($row['seme_class'],1,2));
  	  $class_data=class_id_2_old($class_id);
  	?>
   	<tr bgcolor="<?php echo $C[$j];?>">
   		<td style="font-size:10pt;color:#000000" align="center">
   			<?php
			//沒有競賽記錄才能刪除
   			if ($REC[0]==0) {
   				if (mysql_num_rows($GROUPS)==0) {
   				?>
   				<a style="cursor:hand" title="刪除" onclick="if (confirm('您確定要\n刪除「<?php echo $row['stud_id'].$row['stud_name'];?>」?')) { document.myform.act.value='deleteuser';document.myform.option2.value='<?php echo $row['student_sn'];?>';document.myform.submit(); }"><img src="./images/del.png"  border="0"></a>
   					<?php
   				}
   					?>
   				<a style="cursor:hand" title="為此生設定組員" onclick="document.myform.act.value='editgroup';document.myform.option2.value='<?php echo $row['student_sn'];?>';document.myform.submit();"><img src="images/group.jpg" border="0" width="17" height="16"></a>
				  <?php
   			}else{
   				?>
   				**
   				<?php
   			}
   			?>
   			</td>
   		<td style="font-size:10pt;color:#000000" align="center" width="80"><?php echo @$row['stud_id'];?></td>
   		<td style="font-size:10pt;color:#000000" align="center" width="60"><?php echo @$row['stud_name'];?></td>
   		<td style="font-size:10pt;color:#000000" align="center" width="80"><?php echo $class_data[5];?></td>
   		<td style="font-size:10pt;color:#000000" align="center" width="40"><?php echo @$row['seme_num'];?></td>
   		<td style="font-size:10pt;color:#000000" align="center" width="80"><?php echo @$row['email_pass'];?></td>
   		<td style="font-size:10pt;color:#000000" align="center" width="80"><?php echo @$row['logintimes'];?></td>
   		<td style="font-size:8pt;color:#000000" align="center" width="130"><?php echo @$row['lastlogin'];?></td>
   		<td style="font-size:10pt;color:#000000" ><?php echo $REC[1];?>
			<?php
			//允許清除登入記錄
			if ($TEST['active']==5 or $TEST['active']==6) {
				if ($REC[2]>0) {
					?>
					<a style="cursor:hand" title="刪除最後的登入檢測記錄" onclick="if (confirm('您確定要\n清除<?php echo $row['stud_name'];?>? 的 第<?php echo $REC[2];?>次登入檢測記錄? \n\n 請務必確認該生目前並未進行檢測中!!')) { document.myform.act.value='cleartyperec';document.myform.option2.value='<?php echo $row['student_sn'];?>';document.myform.submit(); }"><img src="./images/del.png"  border="0">
					<?php
				}
			}
			?>
		</td>
  	</tr>
    	<?php
    	if (mysql_num_rows($GROUPS)>0) {
    		?>
    		 <tr>
   				<td style="font-size:8pt;color:#000000" align="right"  bgcolor="#FFFFFF">
   				組員=>
   				</td>
   				<td colspan="8">
   				<table border="0" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="1">

    		<?php
    	while ($row=mysql_fetch_array($GROUPS,1)) {
    		  //班級轉中文
    			$class_id=sprintf('%03d_%d_%02d_%02d',substr($TEST['year_seme'],0,3),substr($TEST['year_seme'],3,1),substr($row['seme_class'],0,1),substr($row['seme_class'],1,2));
  	  		$class_data=class_id_2_old($class_id);
    		?>
    		<tr bgcolor="<?php echo $C[$j];?>">
   				<td style="font-size:10pt;color:#000000" align="center" width="80">
   					<a style="cursor:hand" title="刪除" onclick="if (confirm('您確定要\n刪除<?php echo $row['stud_id'];?>?')) { document.myform.act.value='deleteuser';document.myform.option2.value='<?php echo $row['student_sn'];?>';document.myform.submit(); }"><img src="./images/del.png"  border="0"></a>
   						<?php echo @$row['stud_id'];?>
   					</td>
   				<td style="font-size:10pt;color:#000000" align="center" width="60"><?php echo @$row['stud_name'];?></td>
   				<td style="font-size:10pt;color:#000000" align="center" width="80"><?php echo $class_data[5];?></td>
   				<td style="font-size:10pt;color:#000000" align="center" width="40"><?php echo @$row['seme_num'];?></td>
   				<td style="font-size:10pt;color:#000000" align="center" width="80"><?php echo @$row['email_pass'];?></td>
   				<td style="font-size:10pt;color:#000000" align="center" width="80"><?php echo @$row['logintimes'];?></td>
   				<td style="font-size:8pt;color:#000000" align="center" width="130"><?php echo @$row['lastlogin'];?></td>
   				<td style="font-size:10pt;color:#000000" align="center" width="100">&nbsp;</td>
  		</tr>
    		<?php
    	 } // end while GROUPS
    	?>
     </table>
    	</td>
    	
    </tr>
    	<?php
    	 } // end if 
    } // end while
 ?>
   </table>
<?php
} // end function

//列出競賽報名名單
function list_user_print($tsn,$act) {
	global $PHP_CONTEST;

	$C[0]="#FFE8FF";
	$C[1]="#E8FFE8";
	//取出競賽設定
  $TEST=get_test_setup($tsn);
   
  //取出名單
	$query="select a.*,b.stud_id,b.stud_name,b.email_pass,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.student_sn=b.student_sn and a.student_sn=c.student_sn and a.tsn=d.tsn and d.year_seme=c.seme_year_seme and a.tsn='".$tsn."' and ifgroup='' order by seme_class,seme_num";
	$result=mysql_query($query);
	

	$Table_Fieles=count($_POST['print_chk']);  //要印的欄位數
   
   //印出表格抬頭
   print_contest_title($TEST);
   print_title();	

    $j=0;$t=-1;
    while ($row=mysql_fetch_array($result,1)) {
    	$j++; $t++;
    	//第1筆不算
      if ($_POST['table_page_break']>0 and $j%$_POST['table_page_break']==1 and $j>1) {
         echo "</table><P STYLE='page-break-before: always;'>&nbsp;</P>";
         if ($_POST['table_page_title']) {  //重印標題
         	 print_contest_title($TEST);
         } 
         print_title(); 
      }

    	//組員資料
    	//$query="select * from contest_user where tsn='".$tsn."' and ifgroup='".$row['stid']."' order by class_num";
    	$query="select a.*,b.stud_id,b.stud_name,b.email_pass,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.student_sn=b.student_sn and a.student_sn=c.student_sn and a.tsn=d.tsn and d.year_seme=c.seme_year_seme and a.tsn='".$tsn."' and ifgroup='".$row['student_sn']."' order by seme_class,seme_num";
    	$GROUPS=mysql_query($query);
    	$GROUPS_num=mysql_num_rows($GROUPS);

    	//統計作答記錄
    	if ($TEST['active']==1) {
    	 //查資料
     	 $REC=get_stud_record1_info($TEST,$row['student_sn']);
    	 $row['record']=$REC[1];
       $row['score']=$REC[3];   	 
    	 
    	}else{
    	 //上傳作品
     	 $REC=get_stud_record2_info($TEST,$row['student_sn']);
    	 $row['record']=($REC[0]==1)?"已上傳":"未上傳!";
    	 $row['score']=($REC[2]==0)?"未評分":$REC[3]; 
    	 
     	 //取得評語
     	 $row['prize_memo']=get_prize_memo($TEST['tsn'],$row['student_sn']); 
    	     	 
    	}
    	
  	?>
   	<tr class="mytr<?php echo $t%2;?>">
   		<td align="center"><?php echo $j;?></td>
 			<?php
 			 $rowspan_num=$GROUPS_num+1;
 			 foreach ($_POST['print_chk'] as $K=>$VAL) {
 			   $Key=$RR[$K];   //stud_name ,seme_class , seme_num...... etc.
 			   if ($VAL=='seme_class') {
 			    //班級轉中文
    			$class_id=sprintf('%03d_%d_%02d_%02d',substr($TEST['year_seme'],0,3),substr($TEST['year_seme'],3,1),substr($row['seme_class'],0,1),substr($row['seme_class'],1,2));
  	  		$class_data=class_id_2_old($class_id);
					$row['seme_class']=$class_data[5];
 			   }
 			 ?>
 			   <td width="<?php $WW[$K];?>" align="center"<?php if ($K>3 and $GROUPS_num>0) echo " rowspan='$rowspan_num'";?>><?php echo $row[$VAL];?></td>
 			 <?php
 			 }
 			?>
 	  </tr>
    	<?php
    	if ($GROUPS_num > 0) {
    		?>
 
    		<?php
    	while ($row=mysql_fetch_array($GROUPS,1)) {
    		$j++;
    		?>
   			<tr class="mytr<?php echo $t%2;?>">
   				<td align="center"><?php echo $j;?></td>
     		<?php
    		foreach($_POST['print_chk'] as $K=>$VAL) {
			   if ($VAL=='seme_class') {
 			    //班級轉中文
    			$class_id=sprintf('%03d_%d_%02d_%02d',substr($TEST['year_seme'],0,3),substr($TEST['year_seme'],3,1),substr($row['seme_class'],0,1),substr($row['seme_class'],1,2));
  	  		$class_data=class_id_2_old($class_id);
					$row['seme_class']=$class_data[5];
 			   }

    		  if ($K<4) {
    		  ?>
 			   <td width="<?php $WW[$K];?>" align="center"<?php if ($K>3 and $GROUPS_num>0) echo " rowspan='$rowspan_num'";?>><?php echo $row[$VAL];?></td>
    		  <?php
    		  }
    		}
    		?>
    		</tr>
    	<?php
    	 } // end while $GROUPS
      } // end if 
   } // end while 
 ?>
   </table>
<?php
} // end function

//列印競賽抬頭
function print_contest_title($TEST) {
	global $PHP_CONTEST;
    ?>
    <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#000000" cellpadding="3">
  	<tr>
  		<td width="80" align="center" style="font-size:14pt;color:#800000">競賽標題</td>
  		<td style="font-size:14pt"><?php echo $TEST['title'];?></td>
  	</tr>
  	<tr>
  		<td width="80" align="center" style="font-size:14pt;color:#800000">競賽題目</td>
  		<td style="font-size:14pt"><?php if ($_POST['show_question']==1) echo $TEST['qtext'];?></td>
  	</tr>
  	<tr>
  		<td width="80" align="center" style="font-size:14pt;color:#800000">競賽類別</td>
  		<td style="font-size:14pt"><?php echo $PHP_CONTEST[$TEST['active']];?></td>
  	</tr>
  	<tr>
  		<td width="80" align="center" style="font-size:14pt;color:#800000">競賽日期</td>
  		<td style="font-size:14pt"><?php echo $TEST['sttime']."～".$TEST['endtime'];?></td>
  	</tr>

  </table>
  <table border="0" width="100%"><tr><td>&nbsp;</td></tr></table>
  <?php
}

//列印表格抬頭
function print_title() {
	//欄寬	
	$WW=array(0=>70,1=>80,2=>70,3=>40,4=>80,5=>80,6=>140,7=>120,8=>100,9=>100,10=>150,11=>120,12=>60,13=>80,14=>$_POST['mytitle_width']);
	//標題
	$PP[0]="學號";					
	$PP[1]="姓名";						
	$PP[2]="班級";
	$PP[3]="座號";
	$PP[4]="登入密碼";
	$PP[5]="登入次數";
	$PP[6]="最後登入";
	$PP[7]="競賽記錄";
	$PP[8]="競賽成績";
	$PP[9]="得獎名次";
	$PP[10]="評審評語";
	$PP[11]="簽名欄";
	$PP[12]="點名欄";
	$PP[13]="備註欄";
	$PP[14]=$_POST['mytitle_text'];
	?>
     <table border="1" style="border-collapse: collapse" bordercolor="#000000" cellpadding="<?php echo $_POST['table_padding'];?>">
   	<tr bgcolor="#FFFFCC">
   		<td width="40" align="center" style="font-family:標楷體;font-size:14pt">序號</td>
   		<?php

   		foreach ($_POST['print_chk'] as $K=>$VAL) {
   			
   		  ?>
   		   <td width="<?php echo $WW[$K];?>" align="center" style="font-family:標楷體;font-size:14pt"><?php echo $PP[$K];?></td>
   		  <?php
   		}
        ?>
    	</tr>	
    	<?php
}


//學生報名
function contest_add_user($tsn,$STUDENT) {
 	$query="select * from contest_user where tsn='$tsn' and student_sn='".$STUDENT['student_sn']."'";
 	 if (mysql_num_rows(mysql_query($query))>0) {
    $INFO=$STUDENT['seme_class'].sprintf('%02d',$STUDENT['seme_num']).$STUDENT['stud_name']."已重覆報名, 不存入!!!";	
 	 } else {
 	  $query="insert into contest_user (tsn,student_sn) values ('".$_POST['option1']."','".$STUDENT['student_sn']."')";
    if (mysql_query($query)) {
    	$INFO="報名成功：".$STUDENT['seme_class'].sprintf('%02d',$STUDENT['seme_num']).$STUDENT['stud_name'];
    } else {
     echo "Error! query=".$query;
     exit();
    }   
   } // end if mysql_num_rows

   return $INFO;
   
} // end function contest_add_user


//自題庫補足100題作為題本
function get100($tsn,$ToNum) {
	//查詢題目表中是否已有題目********************************************* 
	$TEST=get_test_setup($tsn);

  $query="select ibsn from contest_ibgroup where tsn='".$tsn."'";
  $result=mysql_query($query);
  $N=mysql_num_rows($result);
  $start=1;
	if ($N>$ToNum) {
	 $INFO="錯誤! 目前題庫總題數小於題本目標題數!!";
	 $start=0;
	}

	if ($TEST['search_ibgroup']>=$ToNum) {
	 $INFO="錯誤! 目前題本內的題數已大於或等於設定的題本目標題數!!";
	 $start=0;
	}

	if ($start) {
	
	
	list($IB)=mysql_fetch_row(mysql_query('select count(*) as num from contest_itembank'));
	$IB-=1;

 //亂數取每一題的ibsn	
 while (mysql_num_rows(mysql_query("select ibsn from contest_ibgroup where tsn='$tsn'"))<$ToNum) {
 	//檢驗是否重覆
  do {
   $D=0;
   $IN=rand(0,$IB);
   list($ibsn)=mysql_fetch_row(mysql_query("select ibsn from contest_itembank limit ".$IN.",1"));
   $query="select count(*) as num from contest_ibgroup where tsn='$tsn' and ibsn='$ibsn'";
	 $result=mysql_query($query);
	 $row=mysql_fetch_row($result);
	 list($D)=$row;
  } while ($D>0);
  //寫入題目代碼
  $query="select * from contest_itembank where ibsn='$ibsn'";
  $res=mysql_query($query);
  $row=mysql_fetch_array($res);
  $query="insert into contest_ibgroup (tsn,ibsn,question,ans,ans_url) values ('$tsn','$ibsn','".SafeAddSlashes($row['question'])."','".SafeAddSlashes($row['ans'])."','".$row['ans_url']."')";
  mysql_query($query);
 } // end while
 //寫入出題序
 $query="select ibsn from contest_ibgroup where tsn='$tsn'";
 $result=mysql_query($query);
 $tsort=0;
 while ($row=mysql_fetch_row($result)) {
  list($ibsn)=$row;
  $tsort++;
  mysql_query("update contest_ibgroup set tsort='$tsort' where tsn='$tsn' and ibsn='$ibsn'");
 } //end while
 } // end if start==1
 
 return $INFO;
 
} // end function

//清除100題本
function clear100($tsn) {
   $query="delete from contest_ibgroup where tsn='$tsn'";
   if (mysql_query($query)) {
    //更新id 編碼
  	mysql_query("optimize table contest_ibgroup");
  	mysql_query("alter table contest_ibgroup drop id");
  	mysql_query("alter table contest_ibgroup add id int(5) auto_increment not null primary key first");
   }else{
    echo "Error! Query=$query";
    exit();
   }
}

//列出題庫供勾選製成題本
function list_itembank_for_choice($tsn) {
	//查詢題目表中是否已有題目********************************************* 
	global $CONN;
	$TEST=get_test_setup($tsn);
	?>
   <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0">
  	<tr>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="10" align="center">&nbsp;</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="40" align="center">編號</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" align="center">題　目　內　容</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="20%" align="center">參考解答</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="10%" align="center">參考網址</td>
  	</tr>

	<?php
	//取出題庫所有題目
	$query="select * from contest_itembank";
	$res=$CONN->Execute($query);
	$i=0;
	while ($row=$res->fetchRow()) {
		$i++;
	 //檢驗本題是否已存在題本中
	 $query="select count(*) as num from contest_ibgroup where tsn='$tsn' and ibsn='".$row['ibsn']."'";
	 $result=mysql_query($query);
	 $row_double=mysql_fetch_row($result);
	 list($D)=$row_double;
	 if ($D>0) { $DIS="disabled"; $BG="bgcolor='#CCCCCC'"; } else { $DIS=""; $BG=""; }
   	?>
  	<tr <?php echo $BG;?>>
      <td style="font-size:10pt;color:#000000" width="10" align="center"><input type="checkbox" name="select_ibgroup[]" value="<?php echo $row['ibsn'];?>" <?php echo $DIS;?>></td>
  		<td style="font-size:10pt;color:#000000" width="40" align="center"><?php echo $i;?></td>
  		<td style="font-size:10pt;color:#000000" ><?php echo $row['question'];?></td>
  		<td style="font-size:10pt;color:#000000" width="20%"><?php echo $row['ans'];?></td>
  		<td style="font-size:10pt;color:#000000" width="10%" align="center"><?php echo $ans_url;?></td>
  	</tr>
  	<?php
	} // end while
  	?>
  	</table>
	<?php
} // end function


//列出題本

function list_test_ibgroup($tsn) {
	
	?>
   <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0">
  	<tr>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="10" align="center">&nbsp;</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="40" align="center">編號</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" align="center">題　目　內　容</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="20%" align="center">參考解答</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="10%" align="center">參考網址</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="50" align="center">操作</td>
  	</tr>

   	<?php
   	$query="select * from contest_ibgroup where tsn='$tsn' order by tsort";
   	$result=mysql_query($query);
   	while ($row=mysql_fetch_array($result)) {
   		   	 	$ans_url=($row['ans_url']=='')?"無":"<a href='".$row['ans_url']."' target='_blank'>".瀏覽."</a>";

   	?>
  	<tr>
      <td style="font-size:10pt;color:#000000" width="10" align="center"><input type="checkbox" name="select_ibgroup[]" value="<?php echo $row['ibsn'];?>"></td>
  		<td style="font-size:10pt;color:#000000" width="40" align="center"><?php echo $row['tsort'];?></td>
  		<td style="font-size:10pt;color:#000000" ><?php echo $row['question'];?></td>
  		<td style="font-size:10pt;color:#000000" width="20%"><?php echo $row['ans'];?></td>
  		<td style="font-size:10pt;color:#000000" width="10%" align="center"><?php echo $ans_url;?></td>
  		<td style="font-size:10pt;color:#000000" width="10" align="center">
  					<img src="images/del.png" border="0" style="cursor:hand" onclick="if (confirm('您確定要:\n刪除第<?php echo $row['tsort'];?>題?')) { document.myform.act.value='search_delete_one';document.myform.option2.value='<?php echo $row['ibsn'];?>';document.myform.submit(); }">
  		</td>
  	</tr>
  	<?php
  	} 
  	?>
  	</table>
    <input type="button" value="刪除勾選的題目" onclick="document.myform.act.value='search_delete_select';document.myform.submit();">
<?php
}

function chk_ifgroup($TEST,$student_sn) {
    	//統計作答記錄 , 若有作答記錄, 不得被指定為組員
    	$DEL=0;
    	if ($TEST['active']==1) {
    	 //查資料
    	 $query="select count(*) as num from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$student_sn."'";
    	 list($N)=mysql_fetch_row(mysql_query($query));
    	 if ($N==0) { $DEL=1; }
    	}else{
    	 //上傳作品
    	 //查資料
    	 $query="select filename from contest_record2 where tsn='".$TEST['tsn']."' and student_sn='".$student_sn."'";
    	 list($FILE)=mysql_fetch_row(mysql_query($query));
    	 if ($FILE=="") {
     	   $DEL=1;
    	  } 
    	}
    	//檢驗此生是否為別組組長
    	if ($DEL==1) {
    	$query="select id from contest_user where tsn='".$TEST['tsn']."' and ifgroup='".$student_sn."'";
    	  if (mysql_num_rows(mysql_query($query))>0) {
    	   $DEL=0;
    	  }
      }
      return $DEL;
}

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

//底下為 java function =================================================================================
?>

