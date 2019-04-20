<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

//秀出網頁
head("網路應用競賽 - 競賽管理");

sfs_check();

?>
<script type="text/javascript" src="./include/tr_functions.js"></script>
<script type="text/javascript" src="../../javascripts/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="../../javascripts/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="../../javascripts/JSCal2-1.9/src/css/jscal2.css">

<?php
$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);

//目前選定學期
  $c_curr_seme=($_POST['c_curr_seme']!="")?$_POST['c_curr_seme']:sprintf('%03d%1d',$curr_year,$curr_seme);
//目前選定學期
//$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//目前日期時間, 用於比對消息有效期限
$Now=date("Y-m-d H:i:s");

if (!$MANAGER) {
 echo "<font color=red>抱歉! 你沒有管理權限, 系統禁止你繼續操作本功能!!!</font>";
 exit();
}

//檢驗三個競賽的學生上傳目錄是否存在, 未存在自動建立
for ($i=2;$i<=4;$i++) {
 if (!file_exists($UPLOAD_P[$i])) {
	 if (!file_exists($UPLOAD_BASE)) {
 		 mkdir($UPLOAD_BASE,0777);
 	}
     mkdir(substr($UPLOAD_P[$i],0,strlen($UPLOAD_P[$i])-1),0777);
 }
}//end for

//POST 送出後,主程式操作開始 

if (@$_POST['act']=="inserting") {
 
 //資料內容
 	$title=$_POST['title'];
 	$qtext=$_POST['qtext'];
	$sttime=$_POST['sday']." ".$_POST['stime_hour'].":".$_POST['stime_min'].":00";
	$endtime=$_POST['eday']." ".$_POST['etime_hour'].":".$_POST['etime_min'].":00";
	$memo=$_POST['memo'];
	$active=$_POST['active'];

	if ($active==5) {
	  $type_id_1=$_POST['c_type_id_1'];
      $type_id_2=$_POST['c_type_id_2'];
	}
    if ($active==6) {
	  $type_id_1=$_POST['e_type_id_1'];
      $type_id_2=$_POST['e_type_id_2'];
	}
	$password=$_POST['password'];
	$delete_enable=$_POST['delete_enable'];
	@$open_judge=$_POST['open_judge'];
	@$open_review=$_POST['open_review'];
	
  $query="insert into contest_setup (year_seme,title,qtext,sttime,endtime,memo,active,open_judge,open_review,password,delete_enable,type_id_1,type_id_2) values ('$c_curr_seme','$title','$qtext','$sttime','$endtime','$memo','$active','$open_judge','$open_review','$password','$delete_enable','$type_id_1','$type_id_2')";
  if (mysql_query($query)) {
 
   //取回最後的 auto_increat 的ID值
  list($tsn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
 
  	//是否啟用評分預設細項
  	if ($_POST['init_score_setup']==1) {
         if ($active==2 or $active==3) {
              $i=0;
              foreach ($SCORE_PAINT as $sco_text) {
               $i++;
               $sco_sn="s".date("y").date("m").date("d").date("H").date("i").date("s").$i;
               $query="insert into contest_score_setup (tsn,sco_sn,sco_text) values ('$tsn','$sco_sn','$sco_text')";
               mysql_query($query);
              }
         } // end if $active
         if ($active==4) {
              $i=0;
              foreach ($SCORE_IMPRESS as $sco_text) {
               $i++;
               $sco_sn="s".date("y").date("m").date("d").date("H").date("i").date("s").$i;
               $query="insert into contest_score_setup (tsn,sco_sn,sco_text) values ('$tsn','$sco_sn','$sco_text')";
               mysql_query($query);
              }
         } // end if $active
         //Scratch動畫預設評分項目
  	     if ($active==7) {
              $i=0;
              foreach ($SCORE_SCRATCH_ANI as $sco_text) {
               $i++;
               $sco_sn="s".date("y").date("m").date("d").date("H").date("i").date("s").$i;
               $query="insert into contest_score_setup (tsn,sco_sn,sco_text) values ('$tsn','$sco_sn','$sco_text')";
               mysql_query($query);
              }

         } // end if $active

  	} // end if init_score_setup
    
    $_POST['act']='';
    
   } else {
   	echo "Error! query=".$query;
  }

} // end if $_POST['act']=inserting


//刪除競賽資料 (事先檢查皆無報名資料才能 , 所以不用檢查有無學生上傳檔案)
if (@$_POST['act']=="delete") { 
 $TEST=get_test_setup($_POST['option1']);
  if ($TEST['active']==1) {
	//刪除查資料比賽內的題目
 $query="delete from contest_ibgroup where tsn='".$_POST['option1']."'";
  mysql_query($query);
  //更新id 編碼
  mysql_query("optimize test_ibgroup");
  mysql_query("alter table test_ibgroup id");
  mysql_query("alter table test_ibgroup add id int(5) auto_increment not null primary key first");
 
 //刪除作答記錄 contest_record1
  $query="delete from contest_record1 where tsn='".$_POST['option1']."'";
  mysql_query($query);
  //更新id 編碼
  mysql_query("optimize contest_record1");
  mysql_query("alter table contest_record1 id");
  mysql_query("alter table contest_record1 add id int(6) auto_increment not null primary key first");

 } else {
 	//作品上傳類
 	//contest_record2 , filename己上傳的檔案
 	$query="select filename from contest_record2 where tsn='".$_POST['option1']."'";
 	$res=mysql_query($query);
 	while ($F=mysql_fetch_array($res,1)) {
 	   	  unlink ($UPLOAD_P[$TEST['active']].$F['filename']);
  	   	if ($TEST['active']==2) {
  	   		$a=explode(".",$F['filename']);
  	   		$filename_s=$a[0]."_s.".$a[1];
  	   		unlink ($UPLOAD_P[$TEST['active']].$filename_s); //刪除縮圖案
  	    } // end if active=2
 	} // end while
 	 mysql_query("delete from contest_record2 where tsn='".$_POST['option1']."'");
 	
 	//細項評分記錄
  //contest_score_user ,先select contest_score_setup裡tsn=$_POST['option1']的sco_sn
	  $query="select sco_sn from contest_score_setup where tsn='".$_POST['option1']."'";
	  $res=mysql_query($query);
 	  while ($row=mysql_fetch_array($res,1)) {
 	   $sql_del="delete from contest_score_user where sco_sn='".$row['sco_sn']."'";
 	   mysql_query($sql_del);
 	  } // end while
	
 	//contest_score_setup 細項評分設定
 	  mysql_query("delete from contest_score_setup where tsn='".$_POST['option1']."'");
 	
 	//contest_score_record2	總分及評語
 	  mysql_query("delete from contest_score_record2 where tsn='".$_POST['option1']."'");  
 } // end if $TEST['active']
  
 //刪除競賽報名記錄 contest_user
   mysql_query("delete from contest_user where tsn='".$_POST['option1']."'");
 //刪除競賽評審設定 contest_judge_user
   mysql_query("delete from contest_judge_user where tsn='".$_POST['option1']."'");  
 //刪除競賽記錄
 $query="delete from contest_setup where tsn='".$_POST['option1']."'";
 mysql_query($query);
 $_POST['act']='';
  
} // end if $_POST['act']=='delete'

//修改評分細項
if (@$_POST['act']=='updating' or @$_POST['act']=='add_score_setup' or @$_POST['act']=='del_score_setup') {

 //資料內容
 	$title=$_POST['title'];
 	$qtext=$_POST['qtext'];
	$sttime=$_POST['sday']." ".$_POST['stime_hour'].":".$_POST['stime_min'].":00";
	$endtime=$_POST['eday']." ".$_POST['etime_hour'].":".$_POST['etime_min'].":00";
	$memo=$_POST['memo'];
	$active=$_POST['active'];
	@$open_judge=$_POST['open_judge'];
	@$open_review=$_POST['open_review'];
	$password=$_POST['password'];
	$delete_enable=$_POST['delete_enable'];

    if ($active==5) {
	  $type_id_1=$_POST['c_type_id_1'];
      $type_id_2=$_POST['c_type_id_2'];
	}
    if ($active==6) {
	  $type_id_1=$_POST['e_type_id_1'];
      $type_id_2=$_POST['e_type_id_2'];
	}
	
  $query="update contest_setup set title='$title',qtext='$qtext',sttime='$sttime',endtime='$endtime',memo='$memo',active='$active',open_judge='$open_judge',open_review='$open_review',password='$password',delete_enable='$delete_enable',type_id_1='$type_id_1',type_id_2='$type_id_2' where tsn='".$_POST['option1']."'";

  if (mysql_query($query)) {
  	//若有新增評分細目
  	switch ($_POST['act']) {
  	 case 'add_score_setup':
  	   $sco_sn="s".date("y").date("m").date("d").date("H").date("i").date("s");
       //測試代碼是否重覆
	     do {
	      $a=floor(rand(10,99));
	      $sco_sn_test=$sco_sn.$a;
	      $query="select count(*) as num from contest_score_setup where sco_sn='".$sco_sn_test."'";
	      $res=mysql_query($query);
	      list($exist)=mysql_fetch_row($res);
     	} while ($exist>0);
	     $sco_sn=$sco_sn_test;
       //資料內容
 	     $sco_text=$_POST['sco_text'];
  	   $query="insert into contest_score_setup (tsn,sco_sn,sco_text) values ('".$_POST['option1']."','$sco_sn','$sco_text')";
  	   mysql_query($query); 
			 $_POST['act']="update";
  	 break;
  	 case 'del_score_setup':
			 $query="delete from contest_score_setup where sco_sn='".$_POST['option2']."'";
       mysql_query($query);
       //更新id 編碼
		   mysql_query("optimize contest_score_setup");
  		 mysql_query("alter table contest_score_setup id");
  		 mysql_query("alter table contest_score_setup add id int(5) auto_increment not null primary key first");
       $_POST['act']="update";
  	 break;
  	 default:
      $_POST['act']='listone'; 	 
  	}
  	
   } else {
   	echo "Error! query=".$query;
   	exit();
  }
} // end if update

//設定查資料比賽題庫組

   //清除100題
  if ($_POST['act']=='clear100') {
   clear100($_POST['option1']);
   $_POST['act']='search';
  }
  
   //補足100題
  if ($_POST['act']=='get100') {
   $INFO=get100($_POST['option1'],$_POST['item_total']);
   $_POST['act']='search';
  }

  //刪除1題
  if  ($_POST['act']=='search_delete_one') {
    $query="delete from contest_ibgroup where tsn='".$_POST['option1']."' and ibsn='".$_POST['option2']."'";
    mysql_query($query);
    $_POST['act']="search";
  }
    
  //刪除所有勾選的題目
  if ($_POST['act']=='search_delete_select') {
    foreach ($_POST['select_ibgroup'] as $ibsn) {
    	$query="delete from contest_ibgroup where tsn='".$_POST['option1']."' and ibsn='$ibsn'";
    	mysql_query($query);
    } // end foreach
    $_POST['act']="search";
  }

  //儲存勾選的題目
  if ($_POST['act']=='save_choice') {
  	foreach ($_POST['select_ibgroup'] as $ibsn) {
  	  //寫入題目代碼
  		$query="select * from contest_itembank where ibsn='$ibsn'";
  		$res=mysql_query($query);
  		$row=mysql_fetch_array($res);
  		$query="insert into contest_ibgroup (tsn,ibsn,question,ans,ans_url) values ('".$_POST['option1']."','$ibsn','".SafeAddSlashes($row['question'])."','".SafeAddSlashes($row['ans'])."','".$row['ans_url']."')";
  		mysql_query($query);
 		} // end foreach
 		//寫入出題序
 		$query="select ibsn from contest_ibgroup where tsn='".$_POST['option1']."'";
 		$result=mysql_query($query);
 		$tsort=0;
 		while ($row=mysql_fetch_row($result)) {
  		list($ibsn)=$row;
  		$tsort++;
  		mysql_query("update contest_ibgroup set tsort='$tsort' where tsn='".$_POST['option1']."' and ibsn='$ibsn'");
 		} //end while
    $_POST['act']="search";
	} // end if ($_POST['act']=='save_choice')

//以學號報名一人
if ($_POST['act']=='edituser_add_by_stud_id') {
 //限定本學期該學號,且正常就學中的學生, 取得 studnent_sn
 $query="select stud_name,seme_class,seme_num,a.student_sn from stud_base a,stud_seme b where a.stud_study_cond in (0,15) and b.seme_year_seme='$c_curr_seme' and a.student_sn=b.student_sn and a.stud_id='".$_POST['stud_id']."'";
 $res=mysql_query($query);
 if (mysql_num_rows($res)==1) {
 	$row=mysql_fetch_array($res,1);
  //參數 傳入 $tsn 及 報名學生 array
  $INFO=contest_add_user($_POST['option1'],$row);
  
 } elseif (mysql_num_rows($res)>1) {
  $INFO="學生資料庫異常, 學號人數超過1人, 請通知系統管理員!";
 } else {
  $INFO="查無此學生! ";
 }
 $_POST['option2']=$_POST['act'];
 $_POST['act']='edituser';
}

//以班級座號報名一人
if ($_POST['act']=='edituser_add_by_classnum') {
 $query="select stud_name,seme_class,seme_num,a.student_sn from stud_base a,stud_seme b where a.stud_study_cond in (0,15) and b.seme_year_seme='$c_curr_seme' and a.student_sn=b.student_sn and b.seme_class='".substr($_POST['classnum'],0,3)."' and b.seme_num=".substr($_POST['classnum'],3,2);
 $res=mysql_query($query);
 if (mysql_num_rows($res)==1) {
 	$row=mysql_fetch_array($res,1);
  //參數 傳入 $tsn 及 報名學生 array
  $INFO=contest_add_user($_POST['option1'],$row);
 } elseif (mysql_num_rows($res)>1) {
  $INFO="學生資料庫異常, 學號人數超過1人, 請通知系統管理員!";
 } else {
  $INFO="查無此學生! ";
 }
 $_POST['option2']=$_POST['act'];
 $_POST['act']='edituser';
}

//整班報名
if ($_POST['act']=='edituser_class_add') {
 $i=0;
 foreach ($_POST['class_id'] as $class_id) {
	$seme_class=sprintf("%d%02d",substr($class_id,6,2),substr($class_id,9,2));
  $query="select a.student_sn,stud_name,seme_class,seme_num from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$c_curr_seme' and a.seme_class='$seme_class' and b.stud_study_cond in (0,15)";
  $res=mysql_query($query);
  while ($STUDENT=mysql_fetch_array($res,1)) {
    $INFO=contest_add_user($_POST['option1'],$STUDENT);
    if (substr($INFO,0,4)=='報名') $i++;
  } // end while
 } // end foreach class_id	
	
	$INFO="共成功報名".$i."位學生!";
 $_POST['option2']=$_POST['act'];
 $_POST['act']='edituser';

} // end if edituser_class_add

//刪除1人 , 有競賽記錄就不得刪除 , 所以此處不用管競賽記錄的清除。
if ($_POST['act']=='deleteuser') {
 $query="delete from contest_user where tsn='".$_POST['option1']."' and student_sn='".$_POST['option2']."'";
 $CONN->Execute($query);
 $_POST['act']=$_POST['return'];
}

//刪除1個打字檢測記錄
if ($_POST['act']=='cleartyperec') {
    $query="select * from contest_typerec where race_id='".$_POST['option1']."' and student_sn='".$_POST['option2']."'";
	$res=$CONN->Execute($query);
	if ($res->RecordCount()>0) {
		$row=$res->fetchrow();
		$s=0;
		if ($row['sttime_1']!='0000-00-00 00:00:00') $s++;
		if ($row['sttime_2']!='0000-00-00 00:00:00') $s++;
		$CLEAR_KEY1="sttime_".$s;
		$CLEAR_KEY2="endtime_".$s;
        $sql="update `contest_typerec` set ".$CLEAR_KEY1."='0000-00-00 00:00:00',".$CLEAR_KEY2."='0000-00-00 00:00:00',score_correct='".$row['correct_1']."',score_speed='".$row['speed_1']."' where race_id='".$_POST['option1']."' and student_sn='".$_POST['option2']."'";
        $res=$CONN->Execute($sql) or die("Erroe! SQL=".$sql);
	}
 $_POST['act']=$_POST['return'];
}


//設定組員 ****************************************************
  if (@$_POST['act']=="editgroup_update") {
  	//先清掉原本是此生組員的記錄
  	$query="update contest_user set ifgroup='' where tsn='".$_POST['option1']."' and ifgroup='".$_POST['option2']."'";
  	mysql_query($query);
   foreach ($_POST['ifgroup'] as $student_sn) {
  	$query="update contest_user set ifgroup='".$_POST['option2']."' where tsn='".$_POST['option1']."' and student_sn='".$student_sn."'";
    mysql_query($query);
   }
   $_POST['act']='edituser';
  }
  
//設定評審老師 ****************************************************
  if (@$_POST['act']=="judge_add") {
    foreach ($_POST['judge_teacher'] as $teacher_sn) {
      $query="insert into contest_judge_user (teacher_sn,tsn) values ('$teacher_sn','".$_POST['option1']."')";
      mysql_query($query);
    }
    $_POST['act']='judge';
  }

  if (@$_POST['act']=="judge_del") {
      $query="delete from contest_judge_user where teacher_sn='".$_POST['option2']."' and tsn='".$_POST['option1']."'";
      mysql_query($query);
           
      //將評分記錄也刪除
      $query="delete from contest_score_record2 where teacher_sn='".$_POST['option2']."' and tsn='".$_POST['option1']."'";
      mysql_query($query); 
      
    	//細項評分記錄
     	//contest_score_user ,先select contest_score_setup裡tsn=$_POST['option1']的sco_sn
	  	$query="select sco_sn from contest_score_setup where tsn='".$_POST['option1']."'";
	  	$res=mysql_query($query);
 	  	while ($row=mysql_fetch_array($res,1)) {
 	   		$sql_del="delete from contest_score_user where sco_sn='".$row['sco_sn']."' and teacher_sn='".$_POST['option2']."'";
 	   		mysql_query($sql_del);
 	  	} // end while      
      
      $_POST['act']=$_POST['return'];
  }

//界面呈現開始, 全部包在 <form>裡 , act動作 , option1, option2 參數2個 return返回前一個動作
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
 <input type="hidden" name="return" value="<?php echo $_POST['act'];?>">
<?php
//預設為列出競賽明細
if ($_POST['act']=='') {
	?>
	<table border="0" width="100%">
	 <tr>
	  <td>
			<select name="c_curr_seme" onchange="this.form.submit()">
			<?php
			 foreach ($class_seme_p as $tid=>$tname) {
    	?>
    		<option value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   		<?php
    	} // end while
    	?>
    </select> 
     <?php
      if ($c_curr_seme==sprintf('%03d%1d',$curr_year,$curr_seme)) {
     ?>
	  	<input type="button" value="新增一個競賽" onclick="document.myform.act.value='insert';document.myform.submit();">
	  	<?php
	  	}
	  	?>
	  	系統時間：<?php echo date("Y-m-d H:i:s");?>
	  </td>
	 </tr>
	</table>
	<hr>
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．正在進行的競賽</td>
  	</tr>
  </table>
  <?php
  //正在進行
  $query="select * from contest_setup where sttime<='$Now' and endtime>'$Now' and year_seme='$c_curr_seme'";
    test_list($query);
  ?>
  <br>
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．尚未開始的競賽</td>
  	</tr>
  </table>
  <?php
  //還沒開始
  $query="select * from contest_setup where sttime>'$Now' and year_seme='$c_curr_seme'";
   test_list($query);
  ?>
  <br>
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．已結束的競賽</td>
  	</tr>
  </table>
  <?php
  //已結束
  $query="select * from contest_setup where endtime<='$Now' and year_seme='$c_curr_seme'";
  test_list($query);
  ?>
<?php
} // end if act==''

//新增一個競賽
if ($_POST['act']=='insert') {
?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．新增競賽項目</td>
  	</tr>
  </table>
  <?php
  $YEAR=date("Y")-1911;
  $TEST['tsn']='';
  $TEST['title']=$YEAR.'年度網路應用競賽校內初賽';
  $TEST['qtext']='';
  $TEST['sttime']=date("Y-m-d H:i:00");
  $TEST['endtime']=date("Y-m-d H:i:00");
  $TEST['memo']='';
  $TEST['active']='0';
  form_contest($TEST);
  ?>
  <table border="0" width="100%">
   	<tr>
  		<td style="font-size:10pt;color:#FF0000">
  		 <input type="button" value="送出" onclick="check_test_form('inserting')">  		 	
  		 <input type="reset" value="清除重寫">
  		 <input type="button" style="color:#FF00FF" value="回上一頁" onclick="document.myform.act.value='';document.myform.submit();">
  		 注意！請在新增競賽後，再進行其他設定(包括競賽報名、競賽評分細目等)。
  		</td>
  	</tr>
  </table>
  <Script Languge="JavaScript">document.myform.title.focus();</Script>
 <?php
}// end if act==insert


//設定競賽內容
if ($_POST['act']=='update') {
?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．設定競賽內容</td>
  	</tr>
  </table>
  <?php
  $TEST=get_test_setup($_POST['option1']);
  form_contest($TEST);
  ?>
  <table border="0" width="100%">
   	<tr>
  		<td style="font-size:10pt;color:#FF0000">
  		 <input type="button" value="送出設定" onclick="check_test_form('updating')">  		 	
  		 <input type="reset" value="清除重寫">
  		 <input type="button" style="color:#FF00FF" value="回上一頁" onclick="document.myform.act.value='listone';document.myform.submit();">
  		</td>
  	</tr>
  </table>
  <Script Languge="JavaScript">document.myform.title.focus();</Script>
 <?php
}// end if act==update


//進行某競賽的管理
if ($_POST['act']=='listone') {
	$TEST=get_test_setup($_POST['option1']);
	?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．競賽內容管理  	</tr>
  </table>
  <?php
  test_main($_POST['option1'],1); //第二個參數設為1, 直接顯示題目, 設為0不顯示題目(用於比賽前公告)
  ?>
  <table border="0">
   <tr>
     <td>
      <?php list_judge_user($_POST['option1']);?>
     </td>
   </tr>
   <tr>
   	 <td style="color:#FF0000;font-size:10pt">※本競賽目前設定：《評分功能：<?php echo $OPEN[$TEST['open_judge']];?>》《成績公佈：<?php echo $OPEN[$TEST['open_review']];?>》</td>
   </tr>
  </table>
  <table border="0" width="100%">
  	<tr>
  		<td>
  			<input type="button" value="重設本競賽內容" onclick="document.myform.act.value='update';document.myform.submit();">
  			<input type="button" value="競賽報名管理" onclick="document.myform.act.value='edituser';document.myform.submit();">
  			<input type="button" value="指定評審老師" onclick="document.myform.act.value='judge';document.myform.submit();">
  			<?php
  			if ($TEST['active']==1) {
  			?>
  			<input type="button" value="設定查資料比賽題本(已有<?php echo $TEST['search_ibgroup'];?>題)" onclick="document.myform.act.value='search';document.myform.submit();">
  			<?php
  			}
  			if ($TEST['testuser_num']==0 or $TEST['delete_enable']==1) {
  			?>
  			<input type="button" style="color:#FF0000" value="刪除競賽" onclick="if (confirm('您確定要:\n刪除本競賽?\n注意! 連同所有上傳作品、作答記錄...等皆一併刪除!!!')) { document.myform.act.value='delete';document.myform.submit(); }">
  			<?php
  			}
  			?>
  			<input type="button" value="列印名單" onclick="document.myform.act.value='print_user';document.myform.submit();">
  			<input type="button" style="color:#FF00FF" value="回到競賽管理列表" onclick="document.myform.act.value='';document.myform.submit();">
  		</td>
  	</tr>
  </table>
  <table border="0" width="100%">
   <tr>
    <td>※本競賽已報名名單：共計<?php echo $TEST['testuser_num'];?>人</td>	
   </tr>
  </table>
  <?php

  list_user($_POST['option1'],$_POST['act']);

} // end if act=listone

//列印名單
if ($_POST['act']=='print_user') {
?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．列印名單</td>
  	</tr>
  </table>
	<?php
	$TEST=get_test_setup($_POST['option1']);
  title_simple($TEST); 
  ?>
  <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
  <br>
  <font color="#800000">※請勾選要印出的項目</font>
  <table border="1" width="100%" style="border-collapse:collapse" bordercolor="#d0d0d0">
  	<tr>
  	  <td width="40" align="center" style="color:#800000">題目</td><td><input type="checkbox" name="show_question" value="1">要列印出來</td>
  	</tr>
  	<tr>
  		<td width="40" align="center" style="color:#800000">項目</td>
  		 <td>
  		 	<table border="0">
  		 	  <tr>
  	  		  <input type="hidden" name="print_chk[0]" value="stud_id">
  		  		<td><input type="checkbox" name="print_chk[1]" value="stud_name" checked>姓名</td>
  		  		<td><input type="checkbox" name="print_chk[2]" value="seme_class" checked>班級</td>
  		  		<td><input type="checkbox" name="print_chk[3]" value="seme_num" checked>座號</td>
  		  		<td><input type="checkbox" name="print_chk[4]" value="email_pass">登入密碼</td>
  		  		<td><input type="checkbox" name="print_chk[5]" value="logintimes">登入次數</td>
   		  		<td><input type="checkbox" name="print_chk[6]" value="lastlogin">最後登入時間</td>
   		  	</tr>
   		  	<tr>
						<td><input type="checkbox" name="print_chk[7]" value="record">競賽記錄</td>
						<td><input type="checkbox" name="print_chk[8]" value="score">競賽成績</td>
						<td><input type="checkbox" name="print_chk[9]" value="prize_text">得獎名次</td>
						<td><input type="checkbox" name="print_chk[10]" value="prize_memo">評審評語</td>
 					</tr>
 					<tr>
						<td><input type="checkbox" name="print_chk[11]" value="sign">簽名欄</td>
						<td><input type="checkbox" name="print_chk[12]" value="chk" checked>點名欄</td>
						<td><input type="checkbox" name="print_chk[13]" value="memo">備註欄</td>
  		 	  	<td colspan="3"><input type="checkbox" name="print_chk[14]" value="mytitle"><input type="text" size="10" name="mytitle_text">(欄寬<input type="text" name="mytitle_width" size="3" value="100">)<font size="2" color="#FF0000"><--自訂欄位</font></td>
  		 	  </tr>
  		 	</table>				
			</td>
		</tr>
		<tr>
   		 <td width="40" align="center" style="color:#800000">表格</td>
				<td>欄位的大小<input type="text" name="table_padding" value="4" size="3">，每幾筆要分頁 <input type="text" name="table_page_break" value="25" size="2"> ，每頁都印標題：<input type="checkbox" name="table_page_title" value="1" checked> 是</td>
    </tr>
  </table>
  <table border="0">
  	<tr>
  		<td>
  			<input type="button" value="印出名單" onclick="document.myform.target='_blank';document.myform.action='ct_print_user.php';document.myform.submit();">
  			<input type="button" style="color:#FF00FF" value="回上一頁" onclick="document.myform.target='';document.myform.action='<?php echo $_SERVER['PHP_SELF'];?>';document.myform.act.value='listone';document.myform.submit();">
  		</td>
  	</tr>
  </table>
  <table border="0" width="100%">
   <tr>
    <td>※本競賽已報名名單：共計<?php echo $TEST['testuser_num'];?>人</td>	
   </tr>
  </table>
  <?php

  list_user($_POST['option1'],$_POST['act']);

} // end if act=print_user

//設定查資料比賽題本 =========================================================================
if ($_POST['act']=='search') {
	$_POST['item_total']=($_POST['item_total']=='' or $_POST['item_total']=='0')?100:$_POST['item_total'];
?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">

  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．設定查資料比賽題本</td>
  	</tr>
  </table>
  <?php
  $TEST=get_test_setup($_POST['option1']);
  title_simple($TEST);   
  ?>
  <hr>
  <table border="0" width="100%">
  	<tr>
  	  <td style="color:#800000">
  	  ※目前系統中題庫共有 <?php echo $TEST['search_itembank'];?> 題，本測驗使用的題本中有 <?php echo $TEST['search_ibgroup'];?> 題
  	  </td>
  	</tr>
  	<tr>
  		<td style="color:#0000FF">
  			題本目標：<input type="text" name="item_total" value="<?php echo $_POST['item_total'];?>" style="color:#FF0000" size="5">題 &nbsp;&nbsp;
  			<input type="button" style="font-size:10pt;font-family:新細明體" value="亂數補足題本總數" onclick="document.myform.act.value='get100';document.myform.submit();">
				<input type="button" style="font-size:10pt;font-family:新細明體" value="手動勾選題目" onclick="document.myform.act.value='list_itembank_for_choice';document.myform.submit();">
  			<input type="button" style="font-size:10pt;font-family:新細明體" value="清除題本所有題目" onclick="document.myform.act.value='clear100';document.myform.submit();">
   			<input type="button" style="color:#FF00FF;font-size:10pt;font-family:新細明體" value="返回管理頁面" onclick="document.myform.act.value='listone';document.myform.submit();">
  			<font color="#FF0000"><?php echo $INFO;?></font>
  		</td>
  	</tr>
  </table>
  <?php
   list_test_ibgroup($_POST['option1']);
   
} // end if act='search'


//設定查資料比賽題本 =========================================================================
if ($_POST['act']=='list_itembank_for_choice') {
	$_POST['item_total']=($_POST['item_total']=='' or $_POST['item_total']=='0')?100:$_POST['item_total'];
?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">

  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．手動勾選查資料比賽題本</td>
  	</tr>
  </table>
  <?php
  $TEST=get_test_setup($_POST['option1']);
  title_simple($TEST);   
  ?>
  <hr>
  <table border="0" width="100%">
  	<tr>
  	  <td style="color:#800000">
  	  ※目前系統中題庫共有 <?php echo $TEST['search_itembank'];?> 題，本測驗使用的題本中有 <?php echo $TEST['search_ibgroup'];?> 題
  	  </td>
  	</tr>
  	<tr>
  		<td style="color:#0000FF">
  			<input type="button" style="font-size:10pt;font-family:新細明體" value="儲存勾選的題目後離開" onclick="document.myform.act.value='save_choice';document.myform.submit();">
   			<input type="button" style="color:#FF00FF;font-size:10pt;font-family:新細明體" value="返回上一頁" onclick="document.myform.act.value='search';document.myform.submit();">
  			<font color="#FF0000"><?php echo $INFO;?></font>
  		</td>
  	</tr>
  </table>
  <?php
   list_itembank_for_choice($_POST['option1']);
   
} // end if act='search_choice_itembank'

//競賽名單管理 =======================================
if (@$_POST['act']=="edituser") {
  $TEST=get_test_setup($_POST['option1']);
  ?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
  <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．競賽報名管理</td>
  	</tr>
  </table>
  <?php
  title_simple($TEST);   
  ?>
  <hr>
 <table border="0" width="100%">
   <tr>
    <td align="left" width="250" colspan="2" style="color:#800000">※本測驗已報名人數共計 <?php echo $TEST['testuser_num'];?> 人</td>
    <td align="left"><input type="button" value="以班級為單位報名" onclick="document.myform.act.value='edituser_class';document.myform.submit();"></td>
   </tr>
   <tr>
    <td align="right" width="110">輸入學號</td>
    <td width="140"><input type="text" id="stud_id" name="stud_id" size="10" value="">=></td>
    <td align="left"><input type="button" value="以學號報名1人" onclick="if (document.myform.stud_id.value!='') { document.myform.act.value='edituser_add_by_stud_id';document.myform.submit(); }"></td>
   </tr>
   <tr>
    <td align="right" width="110">輸入班級座號</td>
    <td width="140"><input type="text" id="classnum" name="classnum" size="10" value="">=></td>
    <td align="left"><input type="button" value="以班級座號報名1人" onclick="if (document.myform.classnum.value!='') { document.myform.act.value='edituser_add_by_classnum';document.myform.submit(); }">(格式:班級+座號,如70101表一年1班1號)</td>
   </tr>
   <tr>
    <td><input type="button" style="color:FF00FF" value="回上一頁" align="center" onclick="document.myform.act.value='listone';document.myform.submit();"></td>
    <td colspan="2" style="color:#FF0000"><?php echo $INFO;?></td>
   </tr>
 </table>
 <script>
 <?php
  if ($_POST['option2']=='edituser_add_by_stud_id') {
  	echo "document.myform.stud_id.focus();\n";
  	
  } elseif($_POST['option2']=='edituser_add_by_classnum') {
    echo "document.myform.classnum.focus();\n";
  }
 ?>
 $('input:text').bind("keydown", function(e) { 
    if (e.which == 13)  
    { //Enter key 
      e.preventDefault(); //Skip default behavior of the enter key 
      var thisID=$(this).attr("id");
      if (thisID=='stud_id') {
        if (document.myform.stud_id.value!='') { 
        	document.myform.act.value='edituser_add_by_stud_id';
        	document.myform.submit(); 
        }
      } 
      if (thisID=='classnum') {
      	if (document.myform.classnum.value!='') { 
      		document.myform.act.value='edituser_add_by_classnum';
      		document.myform.submit(); 
      	}
      }
      
    } 
  }); 
 </script>
	
<?php
  list_user($_POST['option1'],$_POST['act']);
} // end if act=edituser

//整班報名
if ($_POST['act']=='edituser_class') {

  $TEST=get_test_setup($_POST['option1']);
  title_simple($TEST);   

?>
<hr>
※請勾選班級進行全班報名 (本競賽已報名人數共計 <?php echo $TEST['testuser_num'];?> 人)	
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
<table border="0" >
 <tr>	
 	<?php
	//從 school_class 找出班級, 依年級
	$query="SELECT DISTINCT c_year FROM `school_class` WHERE year ='".curr_year()."' AND semester ='".curr_seme()."' order by c_year";
	$res_year=mysql_query($query);
	while ($row_year=mysql_fetch_array($res_year)) {
 	?>
 	<td>
 	<table border="1" style="border-collapse:collapse" bordercolor="#800000">
 		<tr bgcolor="#FFCCFF">
 	    <td>報名</td>
 	    <td>班級</td>
 		  <td>人數</td>
 		</tr>
 	<?php
 	//列出每一年級的班級
 		$query="select class_id,c_year,c_name,c_kind  from `school_class` where c_year='".$row_year['c_year']."' and  year ='".curr_year()."' AND semester ='".curr_seme()."' order by class_id";
 		$res_class=mysql_query($query);
 		while($row_class=mysql_fetch_array($res_class)) {
 			$c_year=$row_class['c_year'];
 			$c_name=$row_class['c_name'];
 			$seme_class=sprintf("%d%02d",substr($row_class['class_id'],6,2),substr($row_class['class_id'],9,2));
 			list($class_stud_num)=mysql_fetch_row(mysql_query("select count(*) from stud_seme a,stud_base b where a.student_sn=b.student_sn and b.stud_study_cond in (0,15) and a.seme_year_seme='$c_curr_seme' and a.seme_class='$seme_class'"));
 	   ?>
 	  <tr>
 	    <td style="font-size:10pt;color:" align="center"><input type="checkbox" name="class_id[]" value="<?php echo $row_class['class_id'];?>"></td>
 	    <td><?php echo $school_kind_name[$c_year]."".$c_name."班";?></td>
 		  <td><?php echo $class_stud_num;?>人</td>
 		</tr>
 	   
 	   <?php
   	} // end while $row_class
  	?>
  </table>
  </td>
  <?php
  } // end while $row_year
?> 
 </tr>
</table>
<input type="button" value="報名以上勾選班級" onclick="document.myform.act.value='edituser_class_add';document.myform.submit();">
<input type="button" value="回上一頁" onclick="document.myform.act.value='edituser';document.myform.submit();">
<br>※註：已報名名單並不會重覆登錄．
<?php
}


//編輯組員
if ($_POST['act']=='editgroup') {
?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
   <table border="0" width="100%">
  	<tr>
  		<td style="color:#0000FF">．編輯競賽組員名單</td>
  	</tr>
  </table>
  <?php
  //競賽資料
  $TEST=get_test_setup($_POST['option1']);
  //組長資料
  $Stud=get_contest_user($_POST['option1'],$_POST['option2']);
  
  title_simple($TEST);   

?>
  <hr>
  <table border="1" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="3">
    <tr>
   		<td style="font-size:10pt;color:#800000" width="80" align="center" bgcolor="#FFCCCC">帳號(姓名)</td>
   		<td style="font-size:10pt;color:#800000" width="150" align="center"><?php echo $Stud['stud_id'];?>(<?php echo $Stud['stud_name'];?>)</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center" bgcolor="#FFCCCC">班級座號</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center"><?php echo $Stud['seme_class'].sprintf('%02d',$Stud['seme_num']);?></td>
   	</tr>
   </table>
   <table border="0" width="100%">
   	<tr>
   		<td>※請從下表中勾選此生的組員...
   		<input type="button" value="返回" onclick="document.myform.act.value='edituser';document.myform.submit();"></td>
		</tr>
 </table>
  <table border="1" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="1">
   	<tr bgcolor="#FFFFCC">
   		<td style="font-size:10pt;color:#800000" width="50" align="center">管理</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">帳號(學號)</td>
   		<td style="font-size:10pt;color:#800000" width="60" align="center">姓名</td>
   		<td style="font-size:10pt;color:#800000" width="40" align="center">班級</td>
   		<td style="font-size:10pt;color:#800000" width="40" align="center">座號</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">登入密碼</td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center">登入次數</td>
   		<td style="font-size:10pt;color:#800000" width="130" align="center">最後登入</td>
   	</tr>	
  <?php
  //取出名單 , 尚未被指定為組員者或為本人之組員, 且非本人
 	$query="select a.*,b.stud_id,b.stud_name,b.email_pass,c.seme_class,c.seme_num from contest_user a,stud_base b,stud_seme c,contest_setup d where a.student_sn=b.student_sn and a.student_sn=c.student_sn and a.tsn=d.tsn and d.year_seme=c.seme_year_seme and a.tsn='".$_POST['option1']."' and (a.ifgroup='' or a.ifgroup='".$_POST['option2']."') and a.student_sn!='".$_POST['option2']."' order by c.seme_class,c.seme_num";
  
  $result=mysql_query($query);
    while ($row=mysql_fetch_array($result,1)) {
     
     if (chk_ifgroup($TEST,$row['student_sn'])) { //無作答記錄 , 且本身沒有組員(非組長)
    ?>
   	<tr bgcolor="#FFFFFF">
   		<td style="font-size:10pt;color:#800000" width="50" align="center"><input type="checkbox" name="ifgroup[]" value="<?php echo $row['student_sn'];?>"<?php if ($row['ifgroup']==$_POST['option2']) { echo "checked";}?>></td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center"><?php echo @$row['stud_id'];?></td>
   		<td style="font-size:10pt;color:#800000" width="60" align="center"><?php echo @$row['stud_name'];?></td>
   		<td style="font-size:10pt;color:#000000" align="center" width="40"><?php echo @$row['seme_class'];?></td>
   		<td style="font-size:10pt;color:#000000" align="center" width="40"><?php echo @$row['seme_num'];?></td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center"><?php echo @$row['email_pass'];?></td>
   		<td style="font-size:10pt;color:#800000" width="80" align="center"><?php echo @$row['logintimes'];?></td>
   		<td style="font-size:10pt;color:#800000" width="130" align="center"><?php echo @$row['lastlogin'];?></td>
   	</tr>	
 		<?php
 		 } // end if DEL==1
    } // end while
  ?>
   </table>
   <table width="100%" border="0">
   	<tr>
   		<td><input type="button" value="送出設定" onclick="document.myform.act.value='editgroup_update';document.myform.submit();"></td>
   	</tr>
  </form>
   </table>
<?php
} // end if editgroup

//設定評審
if ($_POST['act']=='judge') {
  $TEST=get_test_setup($_POST['option1']);
	  title_simple($TEST); 

 //職稱類別
 $title_kind_p = post_kind();
 list_judge_user($_POST['option1']);
?>
 <input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme;?>">
 <table border="0" width="100%">
   <tr>
    <td>※請勾選本競賽的評審老師...
     <input type="button" value="送出勾選名單" onclick="document.myform.act.value='judge_add';document.myform.submit()">	
     <input type="button" value="返回上一頁" style="color:#FF00FF" onclick="document.myform.act.value='listone';document.myform.submit()">
    </td>
   </tr>
 </table>
 <table border="1" style="border-collapse:collapse" bordercolor="#600000" width="800">
 	 <tr>
 	 	<td>
 	 		<?php
 foreach ($title_kind_p as $kind=>$post_title) {
    //取出資料, 但排除 contest_judge 有的 teacher_sn
   $query="select a.teacher_sn,a.class_num,b.name from teacher_post a,teacher_base b where a.teacher_sn=b.teacher_sn and a.post_kind='$kind' and b.teach_condition=0 and a.teacher_sn not in (select teacher_sn from contest_judge_user where tsn='".$_POST['option1']."')";
   $query.=($post_title=='導師')?" order by a.class_num":" order by b.name";
 
   ?>
   <table border="0" width="100%">
     <tr><td style="color:#0000FF">職別：<?php echo $post_title;?></td></tr>
   </table>
   <table border="0" cellspacing="0" width="820">

   <?php
   
   $result=mysql_query($query);
   $i=0;
   while ($row=mysql_fetch_array($result,1)) {
   $i++;
   if ($i%10==1) echo "<tr>";
     $p=($post_title=='導師')?$row['class_num'].$row['name']:$row['name'];
     ?>
     <td width="80" style="font-size:10pt"><input type="checkbox" name="judge_teacher[]" value="<?php echo $row['teacher_sn'];?>"><?php echo $p;?></td>
     
     <?php
   if ($i%10==0) echo "</tr>";  
   } // end while
   if ($i%10>0) {
    for ($j=$i%10;$j<10;$j++) { echo "<td width='80'>&nbsp;</td>"; }
    echo "</tr>";	
   }
   
   echo "</table>";
 } // end foreach
 ?>
			</td> 
		</tr>
  </table>
  <?php
} // end if act=judge

?>
</form>
<?php
foot();
?>

 <Script language="JavaScript">
   function check_test_form(ACT) {
   	//競賽日期比較 sday+stime_hour+stime_min
   	var starttime=document.myform.sday.value+" "+document.myform.stime_hour.value+":"+document.myform.stime_min.value+":00";
   	starttime=starttime.replace(/-/g, "/"); 
   	starttime=(Date.parse(starttime)).valueOf() ; // 直接轉換成Date型別所代表的值
   	var endtime=document.myform.eday.value+" "+document.myform.etime_hour.value+":"+document.myform.etime_min.value+":00";
    var active=document.myform.active.value;
   	endtime=endtime.replace(/-/g, "/");
   	endtime=(Date.parse(endtime)).valueOf() ; // 直接轉換成Date型別所代表的值

    if (starttime>=endtime) {
     alert ("競賽結束時間不得早於或等於開始時間!");
     return false;
    }	
   	
    if (document.myform.title.value=='') {
    	alert("請輸入競賽標題!");
    	document.myform.title.focus();
    	return false;
    }

    if (document.myform.qtext.value=='' && document.myform.active.value>1 && document.myform.active.value<5 ) {
    	alert("請輸入競賽題目，題目在競賽開始後才會公佈讓登入學生看到!");
    	document.myform.qtext.focus();
      return false;
    }

    if (document.myform.active.value==5 && (document.myform.c_type_id_1.value=='' || document.myform.c_type_id_2.value=='') ) {
    	alert("請選擇中文文章!");
      return false;
    }

    if (document.myform.active.value==6 && (document.myform.e_type_id_1.value=='' || document.myform.e_type_id_2.value=='') ) {
    	alert("請選擇英文文章!");
      return false;
    }


    if (document.myform.active.value==0) {
    	alert("請選擇競賽類別!");
    	document.myform.active.focus();
      return false;
    }

    	document.myform.act.value=ACT;
    	document.myform.submit();

   }
   
   function automemo() { 
   	var strMEMO = new Array();  
    strMEMO[1]="<?php echo $PHP_MEMO[1];?>";
    strMEMO[2]="<?php echo $PHP_MEMO[2];?>";
    strMEMO[3]="<?php echo $PHP_MEMO[3];?>";
    strMEMO[4]="<?php echo $PHP_MEMO[4];?>";
    strMEMO[5]="<?php echo $PHP_MEMO[5];?>";
    strMEMO[6]="<?php echo $PHP_MEMO[6];?>";
    strMEMO[7]="<?php echo $PHP_MEMO[7];?>";

    var intN=document.myform.active.value;

    document.myform.memo.value=strMEMO[intN];

    if (intN<5 || intN>6) {
      $("#test_article").css("display","table-row");
      $("#type1_article").css("display","none");
      $("#type2_article").css("display","none");
    }
    if (intN==1) {
      $("#test_article").css("display","none");
      $("#type1_article").css("display","none");
      $("#type2_article").css("display","none");
    }
    //若選了中打
    if (intN==5) {
      $("#test_article").css("display","none");
      $("#type1_article").css("display","table-row");
      $("#type2_article").css("display","none");
    }
    //若選了英打
    if (intN==6) {
      $("#test_article").css("display","none");
      $("#type1_article").css("display","none");
      $("#type2_article").css("display","table-row");
    }

   }
 
</Script>

