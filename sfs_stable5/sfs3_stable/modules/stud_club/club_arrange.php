<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("社團活動 - 社團編班");

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
$c_curr_class=$_POST['c_curr_class'];
$CLASS_name=$school_kind_name[$c_curr_class]; //中文，如一年，二年...

//取得學期社團設定
$SETUP=get_club_setup($c_curr_seme);


//預設為本學期社團
if ($CLUB['year_seme']=="") $CLUB['year_seme']=$c_curr_seme;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}
//POST後之動作 ================================================================
if ($_POST['mode']=='arrange') {

//開始編班
  $c_curr_class=$_POST['c_curr_class']; //年級
  //2013.09.10 ====[若不允許同時參加多個社團, 已被手動指定社團, 卻也選社團的學生, 不能參與編班]============== 
  if ($SETUP['multi_join']==0) {
  	//取得本年級的所有學生
  	$query="select student_sn from stud_seme where seme_year_seme='$c_curr_seme' and seme_class like '".$c_curr_class."%%'";
  	$res=mysql_query($query);
  	while ($row=mysql_fetch_array($res,1)) {
  		$sql="select student_sn from association where seme_year_seme='$c_curr_seme' and student_sn='".$row['student_sn']."'";
  		$res_stud=mysql_query($sql);
  		//若有資料
  		if (mysql_num_rows($res_stud)) {
  			//逐一比對 association 裡是否已有這個學生, 若有, 將 選課暫存資料註記為已編班 , 但不更動 arranged 資料 , 除非尚未編班
  			$query="update stud_club_temp set arranged='1' where year_seme='$c_curr_seme' and student_sn='".$row['student_sn']."' and arranged='0'";  //
  			mysql_query($query);
  		}
  	} // end while  
  } // end if ($SETUP['multi_join']==0)
  //=========================================================================================================
  $RECORD="\n".date("Y-m-d H:i:s")."進行".$school_kind_name[$c_curr_class]."級編班 <br>"; //編班記錄
  
  //取出所有本學期，本年級且開放選課的社團
  $query="select * from stud_club_base where year_seme='$c_curr_seme' and (club_class='$c_curr_class' or club_class='100') and club_open='1' order by club_class,club_name";
  $result=mysql_query($query);
  //以陣列記錄社團資料
  $club_all=0; //計數
  $club_for_this_class=0; //可提供給本年級選課的社團數,用於最後落選者編排部分，只能編入該年級的社團，不能編入跨年級社團
  while ($row=mysql_fetch_array($result)) {
  	$club_all++;
  	if ($row['club_class']==$c_curr_class) $club_for_this_class++;
    $club_sn[$club_all]=$row['club_sn'];
  	$club_name[$club_all]=$row['club_name'];
  	$club_student_num[$club_all]=$row['club_student_num']; //第$club_all社團編班需求總名額
  	$stud_boy_num[$club_all]=$row['stud_boy_num']; //第$club_all社團編班需求男生名額
  	$stud_girl_num[$club_all]=$row['stud_girl_num']; //第$club_all社團編班需求女生名額
  	$ignore_sex[$club_all]=$row['ignore_sex'];
  	//已編班的學生數
  	$stud_number=get_club_student_num($c_curr_seme,$row['club_sn']);
  	$club_arranged_stud_num[$club_all]=$stud_number[0]; //第$club_all社團目前總數
  	$club_arranged_boy_num[$club_all]=$stud_number[1]; //第$club_all社團目前男生數
  	$club_arranged_girl_num[$club_all]=$stud_number[2]; //第$club_all社團目前女生數
  }
  //以志願序跑社團編班，兩個迴圈
  //志願迴圈
  for ($arr=1;$arr<=$SETUP['choice_num'];$arr++) {
    $RECORD.="<font color=red>第".$arr."志願編班</font><br>";
    //社團迴圈
    for ($the_club=1;$the_club<=$club_all;$the_club++) {
    	$RECORD.="<font color=blue>社團".$the_club."：".$club_name[$the_club].", 己編名額".$club_arranged_stud_num[$the_club]."/".$club_student_num[$the_club]."</font><br>";
    	if ($club_arranged_stud_num[$the_club]>=$club_student_num[$the_club]) {
    	  $RECORD.="編班名額已滿<br>";
    	  continue;
    	} else {
    		
    	  //開始針對此社團編班
    	  if ($ignore_sex[$the_club]==0) {
    	  
    	  //1.編男生，檢查在本志願選擇本社團的男生數，有沒有大於需求，有->用亂數取足，沒有->全入選
    	  if ($stud_boy_num[$the_club]>$club_arranged_boy_num[$the_club]) { //男生仍有名額, 但名額必須小於總剩餘名額
    	    $query="select a.*,c.stud_sex from stud_club_temp a,stud_seme b,stud_base c where a.year_seme='$c_curr_seme' and b.seme_year_seme='$c_curr_seme' and a.club_sn='".$club_sn[$the_club]."' and a.choice_rank='$arr' and a.arranged='0' and a.student_sn=b.student_sn and b.seme_class like '".$c_curr_class."%%' and a.student_sn=c.student_sn and c.stud_sex=1";
    	    $need=(($stud_boy_num[$the_club]-$club_arranged_boy_num[$the_club])<=($club_student_num[$the_club]-$club_arranged_stud_num[$the_club]))?$stud_boy_num[$the_club]-$club_arranged_boy_num[$the_club]:$club_student_num[$the_club]-$club_arranged_stud_num[$the_club];
    	    arrange_run($the_club,$query,$need,"男生");
    	  } // end if 男生仍有名額
    	  //2.編女生，檢查在本志願選擇本社團的女生數，有沒有大於需求，有->用亂數取足，沒有->全入選
				if ($stud_girl_num[$the_club]>$club_arranged_girl_num[$the_club]) { //女生仍有名額
    	    $query="select a.*,c.stud_sex from stud_club_temp a,stud_seme b,stud_base c where a.year_seme='$c_curr_seme' and b.seme_year_seme='$c_curr_seme' and a.club_sn='".$club_sn[$the_club]."' and a.choice_rank='$arr' and a.arranged='0' and a.student_sn=b.student_sn and b.seme_class like '".$c_curr_class."%%' and a.student_sn=c.student_sn and c.stud_sex=2";
    	    $need=(($stud_girl_num[$the_club]-$club_arranged_girl_num[$the_club])<=($club_student_num[$the_club]-$club_arranged_stud_num[$the_club]))?$stud_girl_num[$the_club]-$club_arranged_girl_num[$the_club]:$club_student_num[$the_club]-$club_arranged_stud_num[$the_club];
    	    arrange_run($the_club,$query,$need,"女生");
    	  }
    	  
    	  } // end if ($ignore_sex==0) //若忽略性別設定為 0
    	  
    	  //3.檢查本學期，本社團，本志願，且該年級 arranged=0 的學生有那些人,不分男女, 補足到滿
				if ($club_student_num[$the_club]>$club_arranged_stud_num[$the_club]) { //仍有名額
    	    $query="select a.*,c.stud_sex from stud_club_temp a,stud_seme b,stud_base c where a.year_seme='$c_curr_seme' and b.seme_year_seme='$c_curr_seme' and a.club_sn='".$club_sn[$the_club]."' and a.choice_rank='$arr' and a.arranged='0' and a.student_sn=b.student_sn and b.seme_class like '".$c_curr_class."%%' and a.student_sn=c.student_sn";
    	    $need=$club_student_num[$the_club]-$club_arranged_stud_num[$the_club];
    	    arrange_run($the_club,$query,$need,"不限男女");
    	  }
    	  /***
    	  $result=mysql_query($query);
    	  $the_choice_stud_num=mysql_num_rows($result); //此志願,選此社團人數
    	  if ($the_choice_stud_num==0) {
    	    continue;
    	  }else{
    	  	//以陣列記下學生 student_sn ,變數 array=> the_choice_stud[]
    	  	$i=0; $arr_assign=0; //本志願編入人數
    	    while ($row=mysql_fetch_array($result)) {
    	     $i++;
    	     $the_choice_stud[$i]=$row['student_sn'];   //取得本年級本志願選此社團的學生
    	    } //end while
    	    
    	    if ($the_choice_stud_num<=$club_student_num[$the_club]-$club_arranged_stud_num[$the_club]) {
    	    	//人數小於剩餘名額，全數入選，否則取亂數
    	       for ($i=1;$i<=$the_choice_stud_num;$i++) {
    	            if (choice_this_stud($c_curr_seme,$club_sn[$the_club],$club_name[$the_club],$the_choice_stud[$i],$arr)) {
     	               $club_arranged_stud_num[$the_club]++; //成功編班, 人數加1  ,把學生預選所有志願的 arranged =1 ,下一次才不會被編班
     	               echo get_stud_name($the_choice_stud[$i])." ";
     	               $arr_assign++;
    	            }    	           
    	       } // end for
    	    }else{
    	    	//以亂數決定
    	    	for ($i=1;$i<=$the_choice_stud_num;$i++) {
    	    	  $stud_choiced[$i]=0; //是否已被選的標籤
    	    	}
    	    	$target_num=$club_student_num[$the_club]-$club_arranged_stud_num[$the_club];//目標人數
    	    	$count_num=0; //計數已選到的人數
    	    	do {
    	    	  //取亂數
    	    	  $R=rand(1,$the_choice_stud_num);
    	    	  if ($stud_choiced[$R]==0) {
    	    	  	$stud_choiced[$R]=1;
    	    	  	$count_num++;
    	    	  	if (choice_this_stud($c_curr_seme,$club_sn[$the_club],$club_name[$the_club],$the_choice_stud[$R],$arr)) {
    	    	  	  $club_arranged_stud_num[$the_club]++; //成功編班, 人數加1  
    	    	  	  echo get_stud_name($the_choice_stud[$R])." ";
    	    	  	  $arr_assign++;
    	    	  	}  // function 志願確認入選
    	    	  } //end if    	    	  
    	    	} while ($count_num<$target_num); //end while
    	    	
    	    } // end if $the_choice_stud_num<=$club_student_num[$the_club]-$club_arranged_stud_num[$the_club]
    	    echo "<font color=red>==>不限男女, 編入".$arr_assign."人</font>";
    	  } // end if $the_choice_stud_num==0 是否有學生選這個社團 3.不限男女
    	  ***/
    	} // enf if 編班人數已滿
    	$RECORD.="<br>";
   } // end for the_club
  } // end for 志願數
  
  //列出目前已編情形 **debug用******************************************
  //for ($the_club=1;$the_club<=$club_all;$the_club++) {
  // echo $club_name[$the_club]." 需求: 總".$club_student_num[$the_club]."男".$stud_boy_num[$the_club]." 女".$stud_girl_num[$the_club]." ; 已編:總".$club_arranged_stud_num[$the_club]." 男".$club_arranged_boy_num[$the_club]." 女".$club_arranged_girl_num[$the_club]."<br>";
  //}
  //********************************************************************
  //檢查落選或未選課者是否自動編班
  if ($_POST['choice_auto']==1) {
  $RECORD.="<br>檢查落選名單<br>";
  check_choice_not_arrange(); //得到全域變數 $student_not_choice[$seme_class][$seme_num]
   foreach ($student_choice_not_arrange as $class=>$STUDENT) {
		 	  $RECORD.="<br><font color='#0000FF'>※".$CLASS_name.sprintf('%d',substr($class,1,2))."班 全數落選名單：<br>";
		 	  $RECORD.="<table border=0>";
		 	  	 foreach ($STUDENT as $num=>$student_sn) {
							 $RECORD.="<tr><td style='font-size:10pt'>";
							 $RECORD.=$num.get_stud_name($student_sn);
							 $stud_choice_all=get_stud_choice($c_curr_seme,$student_sn);
							 foreach ($stud_choice_all as $K=>$my_club_sn) {
							   $C=get_club_base($my_club_sn);
								  $RECORD.=" ".$K.".".$C['club_name'];  	
								}
							//編班
							$arr_ok=0;  $RAND=0;
							do {
    	    	  //取亂數
    	    	  $R=rand(1,$club_for_this_class); //僅限本年級的社團
    	    	  $RAND++;
    	    	  
    	    	  //女生有剩名額
    	    	  if ($arr_ok==0 and $student_choice_not_arrange_sex[$class][$num]==2 and $stud_girl_num[$R]>$club_arranged_girl_num[$R] and $club_student_num[$R]>$club_arranged_stud_num[$R]) {
    	    	  	if (choice_this_stud($c_curr_seme,$club_sn[$R],$club_name[$R],$student_sn,0)) {
    	    	  	  $club_arranged_stud_num[$R]++; //成功編班, 人數加1  
     	    	  	  if ($student_choice_not_arrange_sex[$class][$num]==1) $club_arranged_boy_num[$R]++;
     	            if ($student_choice_not_arrange_sex[$class][$num]==2) $club_arranged_girl_num[$R]++; 
    	    	  	  $RECORD.="==>男".$club_arranged_boy_num[$R].",女".$club_arranged_girl_num[$R].",編入(女) ".$club_name[$R];
    	    	  	  $arr_ok=1;
    	    	  	} // function 志願確認入選
    	    	  } //end if    	    	  
    	    	  
    	    	  //男生有剩名額
    	    	  if ($arr_ok==0 and $student_choice_not_arrange_sex[$class][$num]==1 and $stud_boy_num[$R]>$club_arranged_boy_num[$R] and $club_student_num[$R]>$club_arranged_stud_num[$R]) {
    	    	  	if (choice_this_stud($c_curr_seme,$club_sn[$R],$club_name[$R],$student_sn,0)) {
    	    	  	  $club_arranged_stud_num[$R]++; //成功編班, 人數加1  
     	    	  	  if ($student_choice_not_arrange_sex[$class][$num]==1) $club_arranged_boy_num[$R]++;
     	            if ($student_choice_not_arrange_sex[$class][$num]==2) $club_arranged_girl_num[$R]++; 
    	    	  	  $RECORD.="==>男".$club_arranged_boy_num[$R].",女".$club_arranged_girl_num[$R].",編入(男) ".$club_name[$R];
    	    	  	  $arr_ok=1;
    	    	  	} // function 志願確認入選
    	    	  } //end if    	    	  
    	    	  
    	    	  //不分男女 , 跑了50次, 仍沒有性別符合可插入, 則不分男女, 直接安插
    	    	  if ($RAND>50 and $arr_ok==0 and $club_student_num[$R]>$club_arranged_stud_num[$R]) {
    	    	  	if (choice_this_stud($c_curr_seme,$club_sn[$R],$club_name[$R],$student_sn,0)) {
    	    	  	  $club_arranged_stud_num[$R]++; //成功編班, 人數加1  
     	    	  	  if ($student_choice_not_arrange_sex[$class][$num]==1) $club_arranged_boy_num[$R]++;
     	            if ($student_choice_not_arrange_sex[$class][$num]==2) $club_arranged_girl_num[$R]++; 
    	    	  	  $RECORD.="==>男".$club_arranged_boy_num[$R].",女".$club_arranged_girl_num[$R].",編入 ".$club_name[$R];
    	    	  	  $arr_ok=1;
    	    	  	} // function 志願確認入選
    	    	  } //end if    	    	  
    	    	  
    	    	} while ($arr_ok==0); //end while					
								
							 $RECORD.="</td></tr>";
		 	  	 }
		 	  	 $RECORD.="</table>";	 	  	
		  }

 //檢查剩餘未選
   check_arrange(); //得到全域變數 $student_not_choice[$seme_class][$seme_num]未選課名單
      foreach ($student_not_choice as $class=>$STUDENT) {
		 	  $RECORD.="<br><font color='#0000FF'>※".$CLASS_name.sprintf('%d',substr($class,1,2))."班 未選課名單：<br>";
		 	  $RECORD.="<table border='0'><tr><td style='font-size:10pt'>";
		 	  	 foreach ($STUDENT as $num=>$student_sn) {
							 $RECORD.=$num.get_stud_name($student_sn);
							//編班
							$arr_ok=0; $RAND=0;
							do {
    	    	  //取亂數
    	    	  $R=rand(1,$club_for_this_class); //僅限本年級的社團
    	    	  $RAND++;
    	    	  //女生有剩名額
    	    	  if ($arr_ok==0 and $student_not_choice_sex[$class][$num]==2 and $stud_girl_num[$R]>$club_arranged_girl_num[$R] and $club_student_num[$R]>$club_arranged_stud_num[$R]) {
    	    	  	if (choice_this_stud($c_curr_seme,$club_sn[$R],$club_name[$R],$student_sn,0)) {
    	    	  	  $club_arranged_stud_num[$R]++; //成功編班, 人數加1  
     	    	  	  if ($student_not_choice_sex[$class][$num]==1) $club_arranged_boy_num[$R]++;
     	            if ($student_not_choice_sex[$class][$num]==2) $club_arranged_girl_num[$R]++; 
    	    	  	  $RECORD.="==>男".$club_arranged_boy_num[$R].",女".$club_arranged_girl_num[$R].",編入(女) ".$club_name[$R]."<br>";
    	    	  	  $arr_ok=1;
    	    	  	} // function 志願確認入選
    	    	  } //end if    	    	  
    	    	  
    	    	  //男生有剩名額
    	    	  if ($arr_ok==0 and $student_not_choice_sex[$class][$num]==1 and $stud_boy_num[$R]>$club_arranged_boy_num[$R] and $club_student_num[$R]>$club_arranged_stud_num[$R]) {
    	    	  	if (choice_this_stud($c_curr_seme,$club_sn[$R],$club_name[$R],$student_sn,0)) {
    	    	  	  $club_arranged_stud_num[$R]++; //成功編班, 人數加1  
     	    	  	  if ($student_not_choice_sex[$class][$num]==1) $club_arranged_boy_num[$R]++;
     	            if ($student_not_choice_sex[$class][$num]==2) $club_arranged_girl_num[$R]++; 
    	    	  	  $RECORD.="==>男".$club_arranged_boy_num[$R].",女".$club_arranged_girl_num[$R].",編入(男) ".$club_name[$R]."<br>";
    	    	  	  $arr_ok=1;
    	    	  	} // function 志願確認入選
    	    	  } //end if    	    	  
    	    	  
    	    	  //不分男女, 跑了100次, 仍沒有性別符合可插入, 則不分男女, 直接安插
    	    	  if ($RAND>100 and $arr_ok==0 and $club_student_num[$R]>$club_arranged_stud_num[$R]) {
    	    	  	if (choice_this_stud($c_curr_seme,$club_sn[$R],$club_name[$R],$student_sn,0)) {
    	    	  	  $club_arranged_stud_num[$R]++; //成功編班, 人數加1  
     	    	  	  if ($student_not_choice_sex[$class][$num]==1) $club_arranged_boy_num[$R]++;
     	            if ($student_not_choice_sex[$class][$num]==2) $club_arranged_girl_num[$R]++; 
    	    	  	  $RECORD.="==>男".$club_arranged_boy_num[$R].",女".$club_arranged_girl_num[$R].",編入".$club_name[$R]."<br>";
    	    	  	  $arr_ok=1;
    	    	  	} // function 志願確認入選
    	    	  } //end if    	    	  
    	    	  
    	    	} while ($arr_ok==0); //end while					
	  	  	 } // end foreach
		 	  	$RECORD.="</td></tr></table>";
		 	  	
		  }
  } // end if choice_auto==1
  
  //記錄編班過程
  $Write_Record=addslashes($RECORD."<br>".$SETUP['arrange_record']);
  $query="update stud_club_setup set arrange_record='$Write_Record' where year_seme='$c_curr_seme'";
  if (mysql_query($query)) {
  	?>
  	<table border="0" width="100%">
  	 <tr>
  	  <td valign="top" >
  	  <?php
  	  echo $RECORD;
  	  ?>
  	  </td>
  	 </tr>
  	</table>
  	<?php   
  } else {
  	echo "寫入編班記錄失敗!";
  	exit();
  }
 exit();
}




//起始畫面
?>
<form name="myform1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<!-- mode 參數 -->
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="club_class" value="">

<table border="0" width="100%">
<tr>
	<!-- 左畫面 -->
  <td colspan="2" >選擇編班年級
	  	<select name='c_curr_class' onchange="document.myform1.submit()">
	  		<option value="" style="color:#FF00FF">請選擇..</option>
	  	<?php
			    $class_year_array=get_class_year_array(sprintf('%d',substr($c_curr_seme,0,3)),sprintf('%d',substr($c_curr_seme,-1)));
                foreach ($class_year_array as $K=>$class_year_name) {
                	if (get_club_num($c_curr_seme,$K)) {
                	?>
                	<option value="<?php echo $K;?>" style="color:#FF00FF;font-size:10pt" <?php if ($_POST['c_curr_class']==$K) echo "selected";?>><?php echo $school_kind_name[$K];?>級(<?php echo get_club_num($c_curr_seme,$K);?>)</option>
                	<?php
                  }
                }	
			?>
		</select>
		
	</td>
  </tr>
	<tr>
		<td width="650" valign="top">
		<?php
		if (isset($_POST['c_curr_class'])) {
			
			?>
			<font color="#0000FF">※<?php echo $CLASS_name;?>級社團列表與目前選填情形</font>
			<?php
		  list_class_club_choice_detail($c_curr_seme,$c_curr_class,1,1); //列出年級社團選課明細
		  ?>
		  <font color="#0000FF">※不限年級社團列表與目前選填情形</font>
		  <?php
		  list_class_club_choice_detail($c_curr_seme,'100',1,1); //列出年級社團選課明細
		}
		?>
  </td>
  <!-- 右畫面 -->

  <td valign="top">
  	<?php
  	if (isset($_POST['c_curr_class'])) {
  	?>
  	<font color="#0000FF">※<?php echo $CLASS_name;?>級社團學生明細</font>
  	<?php
  	   //列出學生總數，已選課學生，未選課學生（不含已編班學生），已編班學生，未編班學生
  	   //功能表：開始編班，列出未編班學生
  	   //開始編班時，已編班學生將不會再動，列出勾選項目 club_choice_auto , 是否自動把未選課學生編入班級
  	 
  	 //檢測年級編班情形，全部用全域變數處理
     check_arrange();
     
  	?>
  	<table border="1" style="border-collapse:collapse" bordercolor="#800000" width="250">
  	  <tr>
  	  	<td style="font-size:10pt;color:#0000FF" width="120" bgcolor="#CCFFFF">全年級人數</td><td align="center"><?php echo $CLASS_num;?></td>
  	  </tr>	
  	  <tr>
  	  	<td style="font-size:10pt;color:#0000FF" width="120" bgcolor="#CCFFFF">已選課人數</td><td align="center"><?php echo $CLASS_choiced;?></td>
  	  </tr>
   	  <tr>
  	  	<td style="font-size:10pt;color:#0000FF" width="120" bgcolor="#CCFFFF">已編班人數</td><td align="center"><?php echo $CLASS_arranged;?></td>
  	  </tr>	
  	  <tr>
  	  	<td style="font-size:10pt;color:#0000FF" width="120" bgcolor="#CCFFFF">未編班人數</td><td align="center"><?php echo $CLASS_not_arranged;?></td>
  	  </tr>	
  	  <tr>
  	  	<td style="font-size:10pt;color:#0000FF" width="120" bgcolor="#CCFFFF">未選課人數<br>(不含已編班者)</td><td style="color:#FF0000" align="center"><?php echo $CLASS_not_choiced;?></td>
  	  </tr>	
  	</table>
  	<?php
  	//可選課時段 時分秒月日年 2012-06-01 12:12:00
		$StartSec=date("U",mktime(substr($SETUP['choice_sttime'],11,2),substr($SETUP['choice_sttime'],14,2),0,substr($SETUP['choice_sttime'],5,2),substr($SETUP['choice_sttime'],8,2),substr($SETUP['choice_sttime'],0,4)));
		$EndSec=date("U",mktime(substr($SETUP['choice_endtime'],11,2),substr($SETUP['choice_endtime'],14,2),0,substr($SETUP['choice_endtime'],5,2),substr($SETUP['choice_endtime'],8,2),substr($SETUP['choice_endtime'],0,4)));
		$nowsec=date("U",mktime(date("H"),date("i"),0,date("n"),date("j"),date("Y")));
		 if ($EndSec>$nowsec) {
	  		echo "<font color=red>選課活動尚在進行，無法進行編班!!</font>";
		 } else {
			//提供的名額是否足以編班
			 if ($CLASS_not_arranged <= $club_for_stud_num) {
		   	?>
		  	<br>
			  <font color="#FF0000"><input type="checkbox" name="choice_auto" value="1"<?php if ($SETUP['choice_auto']==1) echo " checked";?>>最後落選者或未選課者自動編班</font><br>
			  <input type="button" value="開始編班" onclick="confirm_start()">
			  <?php
		   } else {
		    echo "<font color=red>需求人數: $CLASS_not_arranged , 實際可供編班人數 : $club_for_stud_num , 無法編班。</font>";  
		   }
		 
		} 	
		//
		  foreach ($student_not_choice as $class=>$STUDENT) {
		 	  echo "<br><font color='#0000FF'>※".$CLASS_name.sprintf('%d',substr($class,1,2))."班 未選課名單：<br>";
		 	  ?>
		 	  <table border="0" width="250">
		 	  	<?php
		 	  	$i=0;
		 	  	 foreach ($STUDENT as $num=>$student_sn) {
							$i++;
							if ($i%4==1) echo "<tr>";
							 echo "<td style='font-size:10pt'>".get_stud_name($student_sn)."</td>";
							if ($i%4==0) echo "</tr>";
		 	  	 }
		 	  	?>
		 	  </table>
		 	  <?php
		 	
		  }

  	
    } // end if 
  	?>
  </td>
</tr>
</table>
</form>
<form name="myform" method="post" action="club_manage.php">
	<!-- mode 參數 -->
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="club_sn" value="">
</form>


<Script language="JavaScript">
	function confirm_start() {
	 is_arrange=confirm('您確定要進行<?php echo $CLASS_name;?>級的社團編班？\n\n※注意！請確定所有資料設定皆正確無誤，編班過程只會針對未編班的學生進行編班，即已編班的學生不做任何更動。\n\n編班過程依各校人數, 可能會有點久, 請耐心等候。');
	 if (is_arrange) {
	 	document.myform1.mode.value="arrange";
	 	document.myform1.submit();
	 }else{
	  return false;
	 }
	}
</Script>

<?php
//參數 $query(sql指令),$NEED(最大需求人數),$limit(條件說明)
function arrange_run($the_club,$query,$NEED,$limit) {
   //echo "編班參數:".$query."<br>";
   global $c_curr_seme,$club_arranged_stud_num,$club_arranged_boy_num,$club_arranged_girl_num,$club_sn,$club_name,$arr;
    
   global $RECORD;
   
     	  $result=mysql_query($query);
    	  $the_choice_stud_num=mysql_num_rows($result); //此志願,選此社團人數
    	  if ($the_choice_stud_num>0) {
    	    
    	  	//以陣列記下學生 student_sn ,變數 array=> the_choice_stud[]
    	  	$i=0; $arr_assign=0; //本志願編入人數
    	    while ($row=mysql_fetch_array($result)) {
    	     $i++;
    	     $the_choice_stud[$i]=$row['student_sn'];   //取得本年級本志願選此社團的學生
    	     $the_choice_stud_sex[$i]=$row['stud_sex'];    	     
    	    } //end while
    	    
    	    if ($the_choice_stud_num<=$NEED) {
    	    	//人數小於剩餘名額，全數入選，否則取亂數
    	       for ($i=1;$i<=$the_choice_stud_num;$i++) {
    	            if (choice_this_stud($c_curr_seme,$club_sn[$the_club],$club_name[$the_club],$the_choice_stud[$i],$arr)) {
     	               $club_arranged_stud_num[$the_club]++; //成功編班, 人數加1  ,把學生預選所有志願的 arranged =1 ,下一次才不會被編班
     	               if ($the_choice_stud_sex[$i]==1) $club_arranged_boy_num[$the_club]++;
     	               if ($the_choice_stud_sex[$i]==2) $club_arranged_girl_num[$the_club]++;
     	               $RECORD.= get_stud_name($the_choice_stud[$i])." ";
     	               $arr_assign++;
    	            }    	           
    	       } // end for
    	    }else{
    	    	//以亂數決定
    	    	for ($i=1;$i<=$the_choice_stud_num;$i++) {
    	    	  $stud_choiced[$i]=0; //是否已被選的標籤
    	    	}
    	    	$target_num=$NEED;//目標人數
    	    	$count_num=0; //計數已選到的人數
    	    	do {
    	    	  //取亂數
    	    	  $R=rand(1,$the_choice_stud_num);
    	    	  if ($stud_choiced[$R]==0) {
    	    	  	$stud_choiced[$R]=1;
    	    	  	$count_num++;
    	    	  	if (choice_this_stud($c_curr_seme,$club_sn[$the_club],$club_name[$the_club],$the_choice_stud[$R],$arr)) {
    	    	  	  $club_arranged_stud_num[$the_club]++; //成功編班, 人數加1 
    	    	  	  if ($the_choice_stud_sex[$R]==1) $club_arranged_boy_num[$the_club]++;
     	            if ($the_choice_stud_sex[$R]==2) $club_arranged_girl_num[$the_club]++; 
    	    	  	  $RECORD.= get_stud_name($the_choice_stud[$R])." ";
    	    	  	  $arr_assign++;
    	    	  	}  // function 志願確認入選
    	    	  } //end if    	    	  
    	    	} while ($count_num<$target_num); //end while
    	    	
    	    } // end if $the_choice_stud_num<=$club_student_num[$the_club]-$club_arranged_stud_num[$the_club]
    	   $RECORD.= "<font color=red>==>條件:".$limit.", 編入".$arr_assign."人</font><br>";
    	  } // end if $the_choice_stud_num==0 是否有學生選這個社團 
}

?>
