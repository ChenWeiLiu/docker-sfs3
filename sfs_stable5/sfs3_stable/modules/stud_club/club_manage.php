<?php
header('Content-type: text/html;charset=big5');
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//ajax輸出
if ($_POST['act']=='list_choice_rank') {

 $club_sn=$_POST['club_sn'];
 $rank=$_POST['rank'];
 $CLUB=get_club_base($club_sn);
 $students=get_students_by_club_choice_rank($club_sn,$rank);
 $show="";
 $show="<font color=blue>《".$CLUB['club_name']."》 第".$rank."志願名單 </font><br><br>";
 $show.="<table border='0' style='font-size:10pt'>";
 $i=0;
 foreach ($students as $S) {
 	$i++;
 	if ($i%5==1) $show.="<tr>";
  $show.="<td>".$S['curr_class_num'].$S['stud_name']."</td>";
  if ($i%5==0) $show.="</tr>";
 }
 $show.="</table><br>共計 $i 人";
 echo $show;
 exit();
}


//秀出網頁
head("社團活動 - 社團管理");

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

//目前選定年級，100指未指定
$c_curr_class=($_POST['c_curr_class']!="")?$_POST['c_curr_class']:"100";
$school_kind_name[100]="跨年";

//取得學期社團設定
$SETUP=get_club_setup($c_curr_seme);
if ($SETUP['error'] and $_POST['mode']!='setting') $_POST['mode']='setup'; //尚未進行期初設定

//預設為本學期社團
if ($CLUB['year_seme']=="") $CLUB['year_seme']=$c_curr_seme;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}
//POST後之動作 ================================================================
if ($_POST['mode']=="update_club_name_start") {
	    $N=0;
      $query="select * from association where seme_year_seme='$c_curr_seme' and club_sn!=''";
      $res=mysql_query($query);
      while ($row=mysql_fetch_array($res)) {
      	$query="select club_name from stud_club_base where club_sn='".$row['club_sn']."'";
				$result=mysql_query($query);
				list($club_name)=mysql_fetch_row($result);
				//2013.08.29 強制 addslashes 
				$query="update association set association_name='".AddSlashes($club_name)."' where sn='".$row['sn']."'";
				//$query="update association set association_name='".SafeAddSlashes($club_name)."' where sn='".$row['sn']."'";
			  //$query="update association set association_name='".$club_name."' where sn='".$row['sn']."'";
				if (mysql_query($query)) {
					$N++;
				} else {
				  echo "錯誤發生了！query=$query";
				  exit();
				}	      	
      	
      } // end while
      $INFO="總共重新載入(登錄)了 $N 位學生的社團成績中的社團名稱。<br>若無必要, 不必再重新這個動作。";
      $_POST['mode']="update_club_name";
}  



//學期初設定 ********************************************************************
if ($_POST['mode']=="setting") {
	
 $year_seme=$_POST['year_seme'];
 	$choice_sttime=$_POST['choice_sttime_date']." ".$_POST['choice_sttime_hour'].":".$_POST['choice_sttime_min'].":00";
	$choice_endtime=$_POST['choice_endtime_date']." ".$_POST['choice_endtime_hour'].":".$_POST['choice_endtime_min'].":00";
  $choice_num=$_POST['choice_num'];
  $choice_over=$_POST['choice_over'];
  if ($_POST['choice_over']=='0') { $choice_num=1; $_POST['choice_over']=1; }
  $choice_auto=$_POST['choice_auto'];
  $student_num=$_POST['student_num'];
  $show_score=$_POST['show_score'];
  $show_feedback=$_POST['show_feedback'];
  $show_teacher_feedback=$_POST['show_teacher_feedback'];
  $teacher_double=$_POST['teacher_double'];
  $multi_join=$_POST['multi_join'];
  $update_sn=$_SESSION['session_tea_sn'];
  //檢查資料是否已建立
  if (mysql_num_rows(mysql_query("select * from stud_club_setup where year_seme='$year_seme'"))==0) {
    mysql_query("insert into stud_club_setup (year_seme) values ('$year_seme')");
  }
  
	   $query="update stud_club_setup set choice_sttime='$choice_sttime',choice_endtime='$choice_endtime',choice_num='$choice_num',choice_over='$choice_over',choice_auto='$choice_auto',student_num='$student_num',show_score='$show_score',show_feedback='$show_feedback',show_teacher_feedback='$show_teacher_feedback',update_sn='$update_sn',teacher_double='$teacher_double',multi_join='$multi_join' where year_seme='$year_seme'";
     if (mysql_query($query)) {
		 
		  $INFO="在".date("Y-m-d H:i:s")."已修改了【".getYearSeme($year_seme)."】的期初設定";
			//將動作改為setup
			$_POST['mode']="setup";
			
	  }else{
	 	
		echo "登錄社團發生錯誤! Query=".$query;
		exit();
   }
}

$pass_score=$_POST['pass_score'];

//統一設定社團通過分數 ********************************************************************
if ($_POST['mode']=="setting_pass_score") {
	
 $year_seme=$_POST['c_curr_seme'];
 
  //檢查資料是否已建立
  if (mysql_num_rows(mysql_query("select * from stud_club_setup where year_seme='$year_seme'"))==0) {
    mysql_query("insert into stud_club_setup (year_seme) values ('$year_seme')");
  }
       
	   $query="update stud_club_base set pass_score='$pass_score',update_sn='$update_sn' where year_seme='$year_seme'";
     if (mysql_query($query)) {
		  
		  $seme_club_num=get_seme_club_num($year_seme);
		 
		  $INFO="在".date("Y-m-d H:i:s")."已修改了【".getYearSeme($year_seme)."】共計".$seme_club_num."個社團的通過分數, 全部設定為 ".$pass_score." 分!";
			//將動作改為setup_pass_score
			$_POST['mode']="setup_pass_score";
			
	  }else{
	 	
		echo "發生錯誤! Query=".$query;
		exit();
   }
}


//新增社團 ********************************************************************
if ($_POST['mode']=="inserting") {

	make_club_post(); //將 POST值轉存到變數
	
	$club_name=$club_name;
	$club_memo=$club_memo;
	   
	   $query="insert into stud_club_base (year_seme,club_name,club_teacher,club_class,club_open,club_student_num,club_memo,club_location,update_sn,stud_boy_num,stud_girl_num,ignore_sex) values ('$year_seme','$club_name','$club_teacher','$club_class','$club_open','$club_student_num','$club_memo','$club_location','$update_sn','$stud_boy_num','$stud_girl_num','$ignore_sex')";
     if (mysql_query($query)) {
     		
     	list($club_sn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		 
		  $INFO="在".date("Y-m-d H:i:s")."已新增社團【".$club_name."】";
			//將動作改為顯示此社團
			$_POST['mode']="list";
			$_POST['club_sn']=$club_sn;
			
	  }else{
	 	
		echo "登錄社團發生錯誤! Query=".$query;
		exit();
		
	  }

}

//更改社團資料 目標: $_SESSION['club_sn'] ,避免 POST的 sn 資料被修改*************
if ($_POST['mode']=="updating") {
	
	make_club_post(); //將 POST值轉存到變數
	
	$club_name=$club_name;
	$club_memo=$club_memo;
	   
	   $query="update stud_club_base set year_seme='$year_seme',club_name='$club_name',club_teacher='$club_teacher',pass_score='$pass_score',club_class='$club_class',club_open='$club_open',club_student_num='$club_student_num',club_memo='$club_memo',club_location='$club_location',update_sn='$update_sn',stud_boy_num='$stud_boy_num',stud_girl_num='$stud_girl_num',ignore_sex='$ignore_sex' where club_sn='".$_SESSION['club_sn']."'";
     if (mysql_query($query)) {
     		
		  $INFO="在".date("Y-m-d H:i:s")."編修社團【".$club_name."】!";
			//將動作改為顯示此社團
			$_POST['mode']="list";
			$_POST['club_sn']=$_SESSION['club_sn'];
			
	  }else{
	 	
		echo "編修社團發生錯誤! Query=".$query;
		exit();
		
	  }

}

//手動增加社員******************************************************************
if ($_POST['mode']=="adding_members") {
	/*** 2012/06/22 不需檢查, 已改在 module.sql 安裝時即檢查
	//資料庫 association資料表 , 社團 $_POST['club_sn']
	if (chk_if_exist_table("association")) {
		//檢查欄位 club_sn 是否存在
		$query="select club_sn from association limit 1";
		if (!mysql_query($query)) {
      mysql_query("ALTER TABLE `association` ADD `update_sn` int(10) unsigned NOT NULL;");
      mysql_query("ALTER TABLE `association` ADD `club_sn` INT(10) UNSIGNED NOT NULL");
      mysql_query("ALTER TABLE `association` ADD `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP");
		}
	}else{
	  create_table_association(); //自動建立學生的社團資料表
	}
	***/
	 //開始存入學生
	$CLUB=get_club_base($_SESSION['club_sn']);
	$seme_year_seme=$CLUB['year_seme'];
  foreach ($_POST['selected_stud'] as $student_sn=>$name) {
  	//檢查是否已有這個學生, 若無, 則存入
  	if (chk_if_exist_stud($CLUB['club_sn'],$student_sn)==0) {
     $query="insert into association (student_sn,seme_year_seme,association_name,score,description,club_sn) values ('$student_sn','".$CLUB['year_seme']."','".SafeAddSlashes($CLUB['club_name'])."','','','".$CLUB['club_sn']."')";
     $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
    }
  } // end foreach
	$_POST['mode']="list";
	$_POST['club_sn']=$_SESSION['club_sn'];
	
}
//刪除勾選的社員******************************************************************
if ($_POST['mode']=="del_members") {
	$CLUB=get_club_base($_SESSION['club_sn']);
  foreach ($_POST['selected_stud'] as $student_sn) {
     $query="delete from  association where seme_year_seme='".$CLUB['year_seme']."' and club_sn='".$CLUB['club_sn']."' and student_sn='$student_sn'";
     $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
  } // end foreach
	$_POST['mode']="list";
	$_POST['club_sn']=$_SESSION['club_sn'];
}
//複製上學期的社團******************************************************************
if ($_POST['mode']=="copying") {
	//依各年級社團
	$class_year_array=get_class_year_array(sprintf('%d',substr($last_seme,0,3)),sprintf('%d',substr($last_seme,-1)));
	$class_year_array[100]="100";
	
	foreach ($class_year_array as $club_class=>$VAL) { 
  	$POST_CLUB_KEY="copy_club_".$club_class;
   	$POST_STUD_KEY="copy_stud_".$club_class;

	 //接下來, 帶入 $POST[$POST_CLUB_KEY];
  foreach ($_POST[$POST_CLUB_KEY] as $last_club_sn=>$val) {
    //$lsdt_club_sn  要複製的社團 club_sn
    $CLUB=get_club_base($last_club_sn);
    //轉換變數
    $year_seme=$c_curr_seme;
    $club_name=SafeAddSlashes($CLUB['club_name']);
    $club_teacher=$CLUB['club_teacher'];
    //非跨年級社團, 且該社團的學期別為第2學期, 年級加1, 3年級或6年級不提供複製
    $club_class=(substr($CLUB['year_seme'],-1)=='2' and $CLUB['club_class']!='100')?$CLUB['club_class']+1:$CLUB['club_class'];
    $club_open=$CLUB['club_open'];
    $club_student_num=$CLUB['club_student_num'];
    $club_memo=SafeAddSlashes($CLUB['club_memo']);
    $club_location=$CLUB['club_location'];
    $update_sn=$_SESSION['session_tea_sn'];
    $stud_boy_num=$CLUB['stud_boy_num'];
    $stud_girl_num=$CLUB['stud_girl_num'];    

    $query="insert into stud_club_base (year_seme,club_name,club_teacher,club_class,club_open,club_student_num,club_memo,club_location,update_sn,stud_boy_num,stud_girl_num) values ('$year_seme','$club_name','$club_teacher','$club_class','$club_open','$club_student_num','$club_memo','$club_location','$update_sn','$stud_boy_num','$stud_girl_num')";
     if (mysql_query($query)) {
     	list($club_sn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	   } else {
	    echo "複製社團失敗! query=$query";
	    exit();
	   }
    
    if ($_POST[$POST_STUD_KEY][$last_club_sn]==1) {
      //取得社團所有學生
       $query="select a.student_sn from association a,stud_base b where a.club_sn='$last_club_sn' and a.student_sn=b.student_sn and (b.stud_study_cond=0 or b.stud_study_cond=2)";
       $res=mysql_query($query);
       if (mysql_num_rows($res)>0) {
        while ($row=mysql_fetch_array($res)) {
           if (chk_if_exist_stud($club_sn,$row['student_sn'])==0) {
           $query="insert into association (student_sn,seme_year_seme,association_name,score,description,club_sn) values ('".$row['student_sn']."','".$year_seme."','".$club_name."','','','".$club_sn."')";
           $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
           } // end if         
        } // end while
       } // end if num_rows>0 
    } // end if $_POST['copy_stud'][$last_club_sn]==1 
    
  } // end foreach ($_POST[$POST_CLUB_KEY]
	
	} // end foreach $class_year_array 
	
  $_POST['mode']=='';
  
} // end if 複製社團

//刪除社團******************************************************************
if ($_POST['mode']=="del_club") {
	$CLUB=get_club_base($_SESSION['club_sn']);
  //刪除學生資料
  $query="delete from association where seme_year_seme='".$CLUB['year_seme']."' and club_sn='".$CLUB['club_sn']."'";
  $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
  //刪除預選資料
  $query="delete from stud_club_temp where year_seme='".$CLUB['year_seme']."' and club_sn='".$CLUB['club_sn']."'";
  $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
  //刪除社團資料
  $query="delete from stud_club_base where year_seme='".$CLUB['year_seme']."' and club_sn='".$CLUB['club_sn']."'";
  $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
  $INFO="已刪除由".get_teacher_name($CLUB['club_teacher'])."指導的【".$CLUB['club_name']."】!";
	$_POST['mode']="";
	$_POST['club_sn']=$_SESSION['club_sn']="";
}
//=============================================================================

//檢查是否有選定社團
if ($_POST['club_sn']!="") $c_curr_class=get_club_class($_POST['club_sn']);

?>
<table bgcolor="#CCCCCC">
	<tr>
  <td>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<!-- mode 參數 insert , update ,在 submit前進行 mode.value 值修改 -->
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="club_sn" value="">
<table border="0" width="100%">
	<tr>
		<!--主功能表列(橫跨左右兩視窗), 提示 select 切換學期及其他功能 -->
		<td width="100%">
		<select name="c_curr_seme" onchange="this.form.submit()">
			<?php
			while (list($tid,$tname)=each($class_seme_p)){
    	?>
    		<option value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   		<?php
    	} // end while
    	?>
    </select> 
		<input type="button" value="本學期期初設定" onclick="document.myform.mode.value='setup';document.myform.submit()">
		<input type="button" value="新增社團" onclick="document.myform.mode.value='insert';document.myform.submit()">
		<input type="button" value="統一設定標準" onclick="document.myform.mode.value='setup_pass_score';document.myform.submit()">
		<input type="button" value="社團總表" onclick="document.myform.mode.value='listall';document.myform.submit()">
		<input type="button" value="更正成績單的社團名稱" onclick="document.myform.mode.value='update_club_name';document.myform.submit()">
		<?php
		//由於避免畢業學生在複雜學員的問題, 只有目前學期能複製上學期社團
		if ($c_curr_seme==sprintf('%03d%1d',$curr_year,$curr_seme)) {
		?>
		<input type="button" value="複製上學期社團" onclick="document.myform.mode.value='club_copy';document.myform.submit()">
    <?php
    } //end if
    if ($SETUP['arrange_record']!='') {
    ?>
    <input type="button" value="編班記錄" onclick="document.myform.mode.value='list_record';document.myform.submit()">
		<input type="button" value="清除編班資料" style="color:#0000FF" onclick="club_clear()">
    <?php
    }
    ?>
		<!--第一列左側, 功能表之後提示最後動作訊息 $INFO -->
	
		</td>
	</tr>
  </table>
	<table border="0" width="800">
	<tr>
	  <!--左列視窗, 學期社團列表 -->
	  <td width="160" valign="top" style="color:#FF00FF;font-size:10pt">
	  	<select name="c_curr_class" onchange="document.myform.club_sn.value='';document.myform.mode.value='';document.myform.submit()">
	  		<optgroup style="color:#FF00FF" label="請選擇..">
	  	<?php
			    $class_year_array=get_class_year_array(sprintf('%d',substr($c_curr_seme,0,3)),sprintf('%d',substr($c_curr_seme,-1)));
                foreach ($class_year_array as $K=>$class_year_name) {
                	?>
                	<option value="<?php echo $K;?>" style="color:#FF00FF;font-size:10pt" <?php if ($c_curr_class==$K) echo "selected";?>><?php echo $school_kind_name[$K];?>級(<?php echo get_club_num($c_curr_seme,$K);?>)</option>
                	<?php
                }	
			?>
									<option value="100" style="color:#FF00FF;font-size:10pt" <?php if ($c_curr_class=='100') echo "selected";?>>跨年級(<?php echo get_club_num($c_curr_seme,100);?>)</option>
		</select>社團列表
			<?php
	  	//傳入參數 1001 , 1002 等, 年度學期
	  	list_club_select($c_curr_seme,$c_curr_class);
	  	?>
	  </td>
	  <!--左列視窗結尾 -->
	  <!--右列視窗, 主畫面 -->
		<td valign="top" align="left">
		<?php
		if (isset($INFO)) {
		 echo "<font style='color:#FF0000;font-size:10pt'>$INFO</font><br>";
		}
		//期初社團設定 ********************************************************************
		if ($_POST['mode']=="setup") {
	   	//
	   	echo "<font color='#800000'>請輸入【".getYearSeme($c_curr_seme)."】社團的期初設定:</font>";
			form_club_setup($c_curr_seme);
			?>
			<table border="0" width="100%">
	   	<tr>
	   		<td align="right"><input type="button" value="確定修改設定" style="color:#FF0000" onclick="document.myform.mode.value='setting';document.myform.submit()"></td>
	   	</tr>
	   </table>

		<?php
		readme();
		}	 // end if setup
		//統一設定社團通過標準 ********************************************************************
		if ($_POST['mode']=="setup_pass_score") {
	   	echo "<font color='#800000'>§統一訂定【".getYearSeme($c_curr_seme)."】全體社團的通過標準:</font>";
			?>
		<table border="0" width="100%">
			 <tr>
			   <td>共計<?php echo get_seme_club_num($c_curr_seme);?>個社團，請輸入通過分數<input type="text" name="pass_score" value="60" size="3"></td>
			 </tr>
			  <tr>
			   <td>說明：</td>
			  </tr>			  
 			  <tr>
			   <td>1.本功能是將所有社團同時輸入統一分數，您仍可針對個別社團進行分數調整。</td>
			  </tr>			  
 			  
 			  <tr>
			   <td>2.學生參加社團活動，若期末評分未達此標準，則無法得到社團認證。</td>
			  </tr>			  
 			  <tr>
			   <td>3.若貴校不需認證，則請輸入 0 分。</td>
			  </tr>
		</table>
			
		<table border="0" width="100%">
	   	<tr>
	   		<td><input type="button" value="確定修改" style="color:#FF0000" onclick="document.myform.mode.value='setting_pass_score';document.myform.submit()"></td>
	   	</tr>
	   </table>
  
		<?php
		}	 // end if setup		
		
		
		//重新更正成績單的社團名稱**********************************************************88*****
		if ($_POST['mode']=="update_club_name") {
	    $query="select * from association where seme_year_seme='$c_curr_seme' and club_sn!=''";
     	$res=mysql_query($query);
     	$N=mysql_num_rows($res);
     	?>
		 <table border="0" width="100%">
        <tr>
   			 <td>
     			本學期為 <?php echo substr($c_curr_seme,0,3);?>學年度第 <?php echo substr($c_curr_seme,3,1);?> 學期<br>
     			社團成績資料表中, 共含有 <?php echo $N;?> 位學生資料, 其成績記錄是經由 SFS3 社團活動模組所建立.<br>
     			您要重新載入(更正)學生參加的社團名稱嗎? <input type="button" value="是, 請重新載入" onclick="document.myform.mode.value='update_club_name_start';document.myform.submit();"><br>
     		<br>
     			<font color=blue>註：如果您曾經更動過社團名稱，發現學生成績單上的社團名稱與實際不符，就必須執行本程式進行更正。</font>
     		<br>
    		</td>
  		</tr>
  		<tr>
    		<td style="color:#FF0000"><br>
     		<?php echo $INFO;?>
    		</td>
  		</tr>
 		 </table>
    <?php	 
		} // end if update_club_name		 
		 
	  //顯示所有社團設定(社團總表) ================================================================
	  if ($_POST['mode']=="listall") {
	  	
	    listall_club($c_curr_seme);
	  }
	  //顯示某社團 ================================================================
	  if ($_POST['mode']=="list") {
	  	//友善提醒
			list_class_info($c_curr_seme,$c_curr_class);
				  	
	  	$_SESSION['club_sn']=$_POST['club_sn']; //存入 SESSION
	  	
	  	list_club($_POST['club_sn']);	
	  	?>
	   <!-- 針對單一社團的功能表 -->
	   <table border="0" width="100%">
	   	<tr>
	   		<td >
	   			<input type="button" value="編修本社團基本資料" style="color:#0000FF" onclick="club_update(<?php echo $_POST['club_sn'];?>)">
	   			<input type="button" value="手動指定社團學員" style="color:#0000FF" onclick="add_members(<?php echo $_POST['club_sn'];?>)">
	   			<input type="button" value="刪除本社團資料" style="color:#0000FF" onclick="del_club(<?php echo $_POST['club_sn'];?>)">
	   		</td>
	   	</tr>
	   </table>	  	
	  	<?php  	
	  	//顯示目前社員名單
	  	if (list_club_members($_POST['club_sn'])) {
	  	?>
	   <table border="0" width="100%">
	   	<tr>
	   		<td align="right">
	   			<input type="button" value="刪除勾選的社團學員" style="color:#FF0000" onclick="del_members(<?php echo $_POST['club_sn'];?>)">
	   		</td>
	   	</tr>
	   </table>	  	
	  	<?php
	    } // end if list_club_members
	  }
		//新增社團模式 ==============================================================
		if ($_POST['mode']=="insert") {	   	
	   	//列出表單, 傳入 $CLUB array
	   	echo "<font color='#800000'>請輸入新增【".getYearSeme($CLUB['year_seme'])."】社團的基本資料:</font>";
	   	//以下為預設值
	   	$CLUB['club_class']=$_POST['c_curr_class'];
	   	$CLUB['club_student_num']=$SETUP['student_num'];
	   	$CLUB['stud_boy_num']=round($SETUP['student_num']/2);
	   	$CLUB['stud_girl_num']=$SETUP['student_num']-$CLUB['stud_boy_num'];
	   	$CLUB['club_open']=1;
	   	$CLUB['pass_score']=60;
	   	$CLUB['club_memo']='本社團成立的主要目的為...，預計每位同學最終能得到...的能力。';
	     form_club($CLUB);
	  ?>
	   <table border="0" width="100%">
	   	<tr>
	   		<td align="right"><input type="button" value="新增一個社團" style="color:#FF0000" onclick="document.myform.mode.value='inserting';check_before_club_post()"></td>
	   	</tr>
	   	<tr>
	   		<td style="color:#FF0000">※注意! 如果是跨年級的社團, 建議使用手動方式指定學員。 如果開放選課, 則在編班時先進行編班的年級會有優先編入的情況。</td>
	   	</tr>
	   </table>
	   <Script language="JavaScript">
	     document.myform.club_name.focus();
	   </Script>
	  <?php
	  } // end if ($_POST['mode']=="insert") =======================================
	  	
	  //編修社團模式 ==============================================================
		if ($_POST['mode']=="update") {
			$CLUB=get_club_base($_POST['club_sn']);	   	
	   	//列出表單, 傳入 $CLUB array
	   	echo "<font color='#800000'>請輸入編修【".getYearSeme($CLUB['year_seme'])."】社團的基本資料:</font>";
	     form_club($CLUB);
	  ?>
	   <table border="0" width="100%">
	   	<tr>
	   		<td align="right"><input type="button" value="確定修改本社團資料" style="color:#FF0000" onclick="document.myform.mode.value='updating';check_before_club_post()"></td>
	   	</tr>
	   </table>
	   <Script language="JavaScript">
	     document.myform.club_name.focus();
	   </Script>
	  <?php
	  } // end if ($_POST['mode']=="insert") =======================================
	  
	 	//複製上學期社團 =============================================================
	  if ($_POST['mode']=="club_copy") {
	  	$last_seme=($curr_seme==2)?sprintf('%03d%1d',$curr_year,1):sprintf('%03d%1d',$curr_year-1,2);
	   	
	  	?>
 	    複製【<?php echo getYearSeme($last_seme);?>】的社團資料至【<?php echo getYearSeme($c_curr_seme);?>】:<br>
	  	<?php
		   if ($curr_seme==1) {
		   ?>
		   <br>
		   <table border="1" style="border-collapse:collapse" bordercolor="#FF0000" width="100%">
		    <tr>
		     <td>
		      說明:<br>
		      上學期為第2學期, 複製後各年級的社團會自動加1個年級, 但由於最高年級的社團學生已畢業, 不提供複製。若有複製學生資料, 轉出學生不會複製。
		     </td>
		    </tr>
		   </table>
		   <?php
		   }
			
			$class_year_array=get_class_year_array(sprintf('%d',substr($last_seme,0,3)),sprintf('%d',substr($last_seme,-1)));
			$class_year_array[100]="100";
			
			$Y[0]="否";
	    $Y[1]="是";

	    foreach ($class_year_array as $club_class=>$class_year_name) {
	    	
	    	$POST_CLUB_KEY="copy_club_".$club_class;
	    	$POST_STUD_KEY="copy_stud_".$club_class;
	    	
	    	//第1學期時, 最高年級由於學生已畢業, 跳過
	    	if (($club_class=='9' or $club_class=='6') and $curr_seme==1) continue;
	    	$query="select * from stud_club_base where year_seme='$last_seme' and club_class='$club_class' order by club_name";
				$result=mysql_query($query);
			  if (mysql_num_rows($result)) {
			  	
			  	echo "<br>※".$school_kind_name[$club_class]."級社團";
			  	
	    	?>
	    	<table border="1" style="border-collapse:collapse" bordercolor="#800000" cellpadding="3" width="100%">
			 	  <tr bgcolor="#FFCCFF">
			 	    <td width="50" style="font-size:10pt;color:#000000" align="center"><input type="checkbox" name="init_<?php echo $POST_CLUB_KEY;?>" value="1" onclick="check_copy('init_<?php echo $POST_CLUB_KEY;?>','<?php echo $POST_CLUB_KEY;?>');">選取</td>
			 	    <td width="60" style="font-size:8pt;color:#000000" align="center"><input type="checkbox" name="init_<?php echo $POST_STUD_KEY;?>" value="1" onclick="check_copy('init_<?php echo $POST_STUD_KEY;?>','<?php echo $POST_STUD_KEY;?>');">含學生</td>
			 	  	<td width="180" style="font-size:10pt;color:#000000">社團名稱</td>
			 	  	<td width="60" style="font-size:10pt;color:#000000" align="center">指導老師</td>
			 	  	<td width="60" style="font-size:10pt;color:#000000">上課地點</td>
			 	  	<td width="30" style="font-size:10pt;color:#000000" align="center">名額</td>
			 	  	<td width="50" style="font-size:9pt;color:#000000" align="center">已編學員</td>
			 	  	<td width="50" style="font-size:10pt;color:#000000" align="center">可選課</td>
			 	  </tr>		    	
	    	<?php
			  	  while ($row=mysql_fetch_array($result)) {
			 	   	$stud_number=get_club_student_num($row['year_seme'],$row['club_sn']);
			 	   	//檢查是否社團在本學期已重覆
			 	   	$query="select * from stud_club_base where year_seme='$c_curr_seme' and club_teacher='".$row['club_teacher']."' and club_class='".$row['club_class']."' and club_name='".$row['club_name']."'";
			 	   	$res_double=mysql_query($query);
			 	   	$DOUBLE=(mysql_num_rows($res_double)>0)?1:0;
			 	    ?>
			 	  <tr>
			 	  	<?php
			 	  	 if ($DOUBLE) {
			 	  	?>
						<td style="color:#FF0000;font-size:10pt" align="center">己存在</td>
			 	  	<td align="center">-</td>			 	  	
			 	  	<?php
			 	  	 } else {
			 	  	?>
			 	  	<td align="center"><input type="checkbox" name="<?php echo $POST_CLUB_KEY;?>[<?php echo $row['club_sn'];?>]" value="1"></td>
			 	  	<td align="center"><input type="checkbox" name="<?php echo $POST_STUD_KEY;?>[<?php echo $row['club_sn'];?>]" value="1"></td>
			 	  	<?php
			 	    }
			 	  	?>
			 	  	<td style="font-size:10pt;color:#000000"><?php echo $row['club_name'];?></td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo get_teacher_name($row['club_teacher']);?></td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo $row['club_location'];?></td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo $row['club_student_num'];?></td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo $stud_number[0];?> (<font color="#0000FF"><?php echo $stud_number[1];?></font>,<font color="#FF6633"><?php echo $stud_number[2];?></font>)</td>
			 	  	<td style="font-size:10pt;color:#000000" align="center"><?php echo $Y[$row['club_open']];?></td>
				  </tr>
				  	<?php		
				  } // end while	
				  ?>
				</table>
				  <?php
				  				    
			  } // if mysql_num_rows($result)	    	
      } // end foreach	
		 ?>
		 <table border="0" width="100%">
		   <tr>
		     <td><input type="button" value="開始複製" onclick="document.myform.mode.value='copying';document.myform.submit();"></td>
		   </tr>
		 </table>
		 <?php
		} //end if club_copy

//
//清除已編班名單 ********************************************************************
if ($_POST['mode']=="club_clear") {
	
 $year_seme=$_POST['c_curr_seme'];
 
 /*
		把所有開放選修社團名單全部清除 
 */
  echo "清除".$year_seme."的編班資料! <br>";
  
 	$query="select * from stud_club_base where year_seme='$year_seme' and club_open=1 order by club_class";
 	$res=$CONN->Execute($query);
 	while ($row=$res->Fetchrow()) {
 	 $club_sn=$row['club_sn'];
 	 $club_name=$row['club_name'];
 	 $club_class=$row['club_class'];
 	 $sql="select * from association where club_sn='$club_sn' and seme_year_seme='$year_seme'";
 	 $res_stud=$CONN->Execute($sql);
 	 $stud_num=$res_stud->RecordCount();  //人數
 	 $sql="delete from association where club_sn='$club_sn' and seme_year_seme='$year_seme'";
 	 $res_del=$CONN->Execute($sql) or die("刪除失敗!");
	 echo $school_kind_name[$club_class]."級社團：".$club_name."，已編".$stud_num."人 =>清除 <br>";
 	
 	}
  echo "清除學生選社志願序註記資料!<br>";
  $sql="update stud_club_temp set arranged=0 where year_seme='$year_seme'";
	$res=$CONN->Execute($sql) or die("清除失敗!");  
  echo "清除編班記錄!<br>";
  $sql="update stud_club_setup set arrange_record='' where year_seme='$year_seme'";
	$res=$CONN->Execute($sql) or die("清除失敗!");  

}

	 
	  	
	  //手動指定某社團學員 ================================================================
	  if ($_POST['mode']=="add_members") {
	  	list_club($_POST['club_sn']);	
	  	list_students_select($_POST['club_sn']);
	  	?>
	   <!-- 針對單一社團的功能表 -->
	   <table border="0" width="100%">
	   	<tr>
	   		<td align="right"><input type="button" value="確定新增勾選的社團學員" style="color:#FF0000" onclick="document.myform.mode.value='adding_members';document.myform.submit()"></td>
	   	</tr>
	   </table>	  	
	  	<?php  	
	  } // end if 
	  //顯示編班記錄
	  if ($_POST['mode']=="list_record") {
	   
	    echo $SETUP['arrange_record'];
	    
	  }
		?>
	  </td>
	  <!--右列視窗結尾 -->
	</tr>
</table>
</form>
  </td>
	</tr>
</table>
<div id="domMessage" style="display:none;overflow:auto">
    <table border='0' width='100%'>
        <tr>
            <td align='right'>
                <img src='images/close.png' width='20' style='cursor:pointer' class='RemoveBlock' title='關閉視窗'>
            </td>
    </table>
   <center>
    <span id="showboard">這是原始資料</span>
    </center>
</div>
<Script>
		function club_clear() {
			if_confirm=confirm('您確定要清除所有編班資料？\n（注意！本動作會把本學期「所有開放選課的社團」名單全數清除！）');
			if (if_confirm) {
			 document.myform.mode.value='club_clear';
			 document.myform.submit();
			} else {
			 return false;
			}
		}		
		
		//列出某志願名單
		$(".list_choice_rank").click(function(){
			 var btnID=$(this).attr("id");			 
			 var NewArray = btnID.split("_");
       var club_sn=NewArray[0];
       var rank=NewArray[1];
	     var act='list_choice_rank';
	     
    	$.ajax({
   		  type: "post",
    	  url: 'club_manage.php',
    	  data: { act:act,club_sn:club_sn,rank:rank },
    	  dataType: "text",
    	  error: function(xhr) {
      	 alert('ajax request 發生錯誤!');
    	  },
    	  success: function(response) {
    	   $('#showboard').html(response);
         $('#showboard').fadeIn(); 			
    	  } // end success
	    });   // end $.ajax
	    
			NowShow();

			 return false;
		});
		

$('.RemoveBlock').click(function(){
         $.unblockUI();
         return false;
});

function NowShow(){
     $.blockUI({
            	message: $('#domMessage'),
            	centerX: true,
              centerY: false,
              css: { top: '100px' }
     });
}

</Script>