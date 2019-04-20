<?php

//取得各領域要計分的分科 , 傳回三維陣列  
// $data[scope][subject_id][subject]=分科名稱
// $data[scope][subject_id][link_ss]=領域
// $data[scope][subject_id][rate]=加權
// $data[scope][subject_id][ss_id]=課程代碼
// $data[scope][subject_id][items]=已命題數

function get_year_seme_scope($year,$semester,$class_year) {
	 	
	 	global $CONN;
	 	
	 	//定義七大領域
	 	if($class_year>2){
			$ss_link=array("語文-本國語文"=>"language","語文-鄉土語文"=>"language","語文-英語"=>"language","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
			//$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
		} else {
			$ss_link=array("語文-本國語文"=>"language","語文-鄉土語文"=>"language","語文-英語"=>"language","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
			//$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
		} 	

		//讀取各分科的名稱
		$sql="select * from score_subject ";
		$res=$CONN->Execute($sql);
		while ($row=$res->fetchRow()) {
 			$subject[$row['subject_id']]=$row['subject_name'];
		}
	
		//讀取學期課程設定
 		$query="select * from score_ss where year='$year' and semester='$semester' and class_year='$class_year' and enable='1' and need_exam='1' order by link_ss,sort,sub_sort";
		$res=$CONN->Execute($query) or die("讀取課程設定發生錯誤, SQL=".$query);
        //2015.11.18 因應班級課程的使用修改  把各分科的設定定義為 ALL 或 class_id 的子陣列
		while ($row=$res->fetchRow()) {
            $class_id=($row['class_id']=='')?"ALL":$row['class_id'];
			$link_ss=$row['link_ss'];   //領域中文名
			$SCOPE=$ss_link[$link_ss];	//轉換成英文
			
			//$scope_id=$row['scope_id']; //領域id
					
			$subject_id=($row['subject_id']>0)?$row['subject_id']:$row['scope_id']; //分科名稱的id，若為0，以領域id顯示
			
			//開始記錄
			$scope_main[$class_id][$SCOPE][$subject_id]['subject']=$subject[$subject_id];
			$scope_main[$class_id][$SCOPE][$subject_id]['link_ss']=$SCOPE;
			$scope_main[$class_id][$SCOPE][$subject_id]['rate']=$row['rate'];
			$scope_main[$class_id][$SCOPE][$subject_id]['ss_id']=$row['ss_id'];
            //$scope_main[$SCOPE][$i][$subject_id]['class_id']=;
			
		} // end while
		
			//讀取分科題數
			$seme_year_seme=$year.$semester;
			foreach ($ss_link as $scope) {
				$paper_setup=get_paper_sn($seme_year_seme,$class_year,$scope);
				if ($paper_setup['sn']>0) {
				foreach ($scope_main['ALL'][$scope] as $k=>$v) {
			  	$sql="select count(*) as num from resit_exam_items where paper_sn='".$paper_setup['sn']."' and subject='".$v['subject']."'";
			    $res=$CONN->Execute($sql) or user_error("讀取錯誤! $sql",256);
			    $num=$res->fields['num'];
			    $scope_main['ALL'][$scope][$k]['items']=$num;
				} // end foreach
			  } // end if
			} // end foreach
		
		return $scope_main;
		
} // end function

//讀取某學期,年級,領域各分科題數設定
function get_scope_subject_items($seme_year_seme,$Cyear,$scope) {
	global $CONN;
	$subject=array();
	$sql="select * from resit_scope_subject where seme_year_seme='$seme_year_seme' and cyear='$Cyear' and scope='$scope'";
	$res=$CONN->Execute($sql) or die ('讀取分科題數設定錯誤 SQL='.$sql);
	while ($row=$res->fetchrow()) {
		$id=$row['subject_id'];
		$items=$row['items'];
	  $subject[$id]=$items;
	}
	
	return $subject;
	
}


//計算學期各領域不及格人數, 並寫入補考資料庫中
function count_scope_fail($Cyear,$seme_year_seme,$ss_link,$link_ss) {
	global $CONN,$now_cy,$curr_year_seme;
 //依勾選的年級 , 先讀取名單
 //抓取班級設定裡的班級名稱
	$class_base= class_base($curr_year_seme);
	$stud_sn=array();
  //$query="select a.*,b.stud_name,b.stud_person_id from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$Cyear%' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
	//$res=$CONN->Execute($query);
  //以本年度學生資料去抓 student_sn , 以免抓不到後來才轉入的學生 student_sn	
	$Now_Cyear=$Cyear+$now_cy;
	$query="select a.student_sn,a.stud_id,a.stud_name,a.stud_person_id,a.curr_class_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$curr_year_seme' and a.curr_class_num like '".$Now_Cyear."%' and stud_study_cond in ('0','15') order by curr_class_num";
  $res=$CONN->Execute($query) or die ("讀取學生基本資料發生錯誤! SQL=".$query);	
	
	//學生總人數
	$student_all=$res->recordcount(); 
	
	while(!$res->EOF) {
		$student_sn=$res->fields['student_sn'];
		$stud_sn[]=$student_sn;
		$curr_class_num=$res->fields['curr_class_num'];
		$seme_class=substr($curr_class_num,0,3);
		
		$student_data[$student_sn]['seme_class']=substr($curr_class_num,0,3);
		$student_data[$student_sn]['seme_num']=substr($curr_class_num,-2);
		
		
		$student_data[$student_sn]['stud_person_id']=$res->fields['stud_person_id'];
		$student_data[$student_sn]['stud_name']=$res->fields['stud_name'];
		$student_data[$student_sn]['stud_id']=$res->fields['stud_id'];
		
		$student_data[$student_sn]['class_name']=$class_base[$seme_class];
		
		//echo $student_sn.",".$res->fields['stud_name']."<br>";
		
		$res->MoveNext();
	} // end while

 	$semes[]=$seme_year_seme;  //目前學期
	//抓取領域成績
	$sel_year=substr($seme_year_seme,0,3);
	$sel_seme=substr($seme_year_seme,-1);
	//$fin_score=cal_fin_score($stud_sn,$semes,"",array($sel_year,$sel_seme,$Cyear),$percision);
	$fin_score=cal_fin_score($stud_sn,$semes,"",$strs,1);
  //讀取課程設定
  $scope_subject=get_year_seme_scope($sel_year,$sel_seme,$Cyear);

  //統計各領域不級格人數 , 依領域跑迴圈  
  foreach ($link_ss as $scope=>$v) {
  	//讀取本領域試卷設定，沒有的話自動建立，以便名單能對應試卷
  	$paper_setup=get_paper_sn($seme_year_seme,$Cyear,$scope,1); 	
  //依學生 student_sn 跑迴圈，依次檢查領域成績
   foreach ($stud_sn as $student_sn) {
    //不及格, 人數加1
   	if ($fin_score[$student_sn][$scope][$seme_year_seme]['score']<60) {
    	//未參加補考, 待補考人數加1
			$sql="select a.* from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and a.student_sn='$student_sn' and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope'";
			$res=$CONN->Execute($sql) or die($sql);
			 //補考資料庫中沒此人
			if ($res->recordcount()==0) {
				//取得此生不及格的分科
				$subjects="";
				foreach ($scope_subject['ALL'][$scope] as $subject_id=>$v) {
				  //讀出成績
				  $ss_id=$v['ss_id'];
				  $ss_score_sql="select ss_score from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='$ss_id'";
					$ss_score_res=$CONN->Execute($ss_score_sql) or die('讀取學期成績發生錯誤!');
          $ss_score=$ss_score_res->fields['ss_score'];
          if ($ss_score<60) $subjects.=$v['subject'].",";
				} // end foreach
				if ($subjects!="") $subjects=substr($subjects,0,strlen($subjects)-1);
				$sql="insert into resit_exam_score (student_sn,paper_sn,org_score,subjects) values ('$student_sn','".$paper_setup['sn']."','".$fin_score[$student_sn][$scope][$seme_year_seme]['score']."','$subjects')";
				$res=$CONN->Execute($sql) or die($sql);
			} else {
			 //有此人，更正原始分數
				$sql="update resit_exam_score set org_score='".$fin_score[$student_sn][$scope][$seme_year_seme]['score']."' where student_sn='$student_sn' and paper_sn='".$paper_setup['sn']."'";
				$res=$CONN->Execute($sql) or die($sql);
			}
		} else {
			//原始成績是及格的, 如果此生尚未補考, 就把名單刪除
			$sql="delete from resit_exam_score where student_sn='$student_sn' and paper_sn='".$paper_setup['sn']."' and complete=0";
			$res=$CONN->Execute($sql) or die($sql);
		
		}// end if score<60
   } // end foreach
  }

  return $student_all;
  
}

//取得試卷的設定值
function get_paper_sn($seme_year_seme,$class_year,$scope,$auto_insert=0) {
	global $CONN;
 	$sql="select * from resit_paper_setup where seme_year_seme='$seme_year_seme' and class_year='$class_year' and scope='$scope'";	
	$res=$CONN->Execute($sql) or die($sql);
	
	if ($res->RecordCount()==0 and $auto_insert==1) {
	 //預設值
	 $start_time=date('Y-m-d H:i:00');
   $end_time=date('Y-m-d H:i:00');
   $timer_mode=0;
   $timer=45;
   $relay_answer=0;
   $items=0;
   $double_papers=0;
		$sql="insert into resit_paper_setup (seme_year_seme,class_year,scope,start_time,end_time,timer_mode,timer,relay_answer,items,double_papers) values ('$seme_year_seme','$class_year','$scope','$start_time','$end_time','$timer_mode','$timer','$relay_answer','$items','$double_papers')";
	  $res=$CONN->Execute($sql) or die ('Error! Query='.$sql);
 		$sql="select * from resit_paper_setup where seme_year_seme='$seme_year_seme' and class_year='$class_year' and scope='$scope'";	
		$res=$CONN->Execute($sql) or die($sql);
	}
	
	$row=$res->fetchRow();
  return $row;
}


//試題表單 $item=array()
function form_item($item) {
 global $scope_subject,$item_scope;
 //echo "<pre>";
 //echo $item_scope;
 //print_r($scope_subject);
 //echo "</pre>";
 //exit();
 
 ?>
 <table border="1" cellpadding="2" cellspacing="0" bordercolor="#800000" bgcolor="#FFFFFF" style="border-collapse: collapse">
   <tr>
     <td width="70" bgcolor="#FFCC66" valign="top"><font color="#0000FF">題幹</font></td>
     <td width="730"bgcolor="#FFE7B3">
      <textarea name="question" cols="78" rows="5"><?php echo $item['question'];?></textarea>
			<br>附圖 <input type="file" size="26" name="thefig_q">
			<?php
			 if ($item['fig_q']) {
			?>
			<font color="#FF0000">(圖)</font>
			<input type="checkbox" name="del_fig[q]" value="1">刪圖
			<?php
			 } // end if fig_q
			?>
    </td>
  </tr>
  <tr id="cha">
      <td width="70" bgcolor="#A2FFA2"><font color="#0000FF">選目(A)</font></td>
      <td bgcolor=#CCFFCC>
					<input size="49" name="cha" value="<?php echo $item['cha'];?>">
					附圖<input type="file" size="10" id="thefig_a" name="thefig_a">
			<?php
			 if ($item['fig_a']) {
			?>
			<font color="#FF0000">(圖)</font>
			<input type="checkbox" id="del_fig_a" name="del_fig[a]" value="1">刪圖
			<?php
			 } // end if fig_a
			?>
      </td>
  </tr>
  <tr id="chb">
      <td width="70" bgcolor="#A2FFA2"><font color="#0000FF">選目(B)</font></td>
      <td bgcolor="#CCFFCC">
					<input size="49" name="chb" value="<?php echo $item['chb'];?>">
					附圖<input type="file" size="10" id="thefig_b" name="thefig_b">
			<?php
			 if ($item['fig_b']) {
			?>
			<font color="#FF0000">(圖)</font>
			<input type="checkbox" id="del_fig_b" name="del_fig[b]" value="1">刪圖
			<?php
			 } // end if fig_a
			?>					
      </td>
  </tr>
  <tr id="chc">
     	<td width="70" bgcolor="#A2FFA2"><font color="#0000FF">選目(C)</font></td>
      <td width="564" bgcolor="#CCFFCC">
					<input size="49" name="chc" value="<?php echo $item['chc'];?>">
					附圖<input type="file" size="10" id="thefig_c" name="thefig_c">
			<?php
			 if ($item['fig_c']) {
			?>
			<font color="#FF0000">(圖)</font>
			<input type="checkbox" id="del_fig_c" name="del_fig[c]" value="1">刪圖
			<?php
			 } // end if fig_a
			?>					
      </td>
    </tr>
    <tr id="chd">
      <td width="70" bgcolor="#A2FFA2"><font color="#0000FF">選目(D)</font></td>
      <td bgcolor="#CCFFCC">
					<input size="49" name="chd" value="<?php echo $item['chd'];?>">
					附圖<input type="file" size="10" id="thefig_d" name="thefig_d">
			<?php
			 if ($item['fig_d']) {
			?>
			<font color="#FF0000">(圖)</font>
			<input type="checkbox" id="del_fig_d" name="del_fig[d]" value="1">刪圖
			<?php
			 } // end if fig_a
			?>					
      </td>
    </tr>
    <tr id="answer">
      <td width="70" bgcolor="#A2FFA2"><font color="#0000FF">標準答案</font></td>
      <td bgcolor="#CCFFCC">
				<input type="radio" name="answer" value="A"<?php if ($item['answer']=="A") echo " checked";?>>(A)&nbsp;
				<input type="radio" name="answer" value="B"<?php if ($item['answer']=="B") echo " checked";?>>(B)&nbsp;
				<input type="radio" name="answer" value="C"<?php if ($item['answer']=="C") echo " checked";?>>(C)&nbsp;
				<input type="radio" name="answer" value="D"<?php if ($item['answer']=="D") echo " checked";?>>(D)
      </td>
    </tr>
    <tr id="subject">
      <td width="70" bgcolor="#A2FFA2"><font color="#0000FF">分科別</font></td>
      <td bgcolor="#CCFFCC">
				<select size="1" name="subject">
				  <option value="">無設定</option>
				  <?php
				   foreach ($scope_subject['ALL'][$item_scope] as $scope=>$v) {
				   ?>
				   	<option value="<?php echo $v['subject'];?>"<?php if ($item['subject']==$v['subject']) echo " selected";?>><?php echo $v['subject'];?></option>
				   <?php
				   }
				  ?>
				</select>
      </td>
    </tr>
  </table>
 <?php
} // end function

//試題表單 $item=array()
function form_item_new($item) {
    global $scope_subject,$item_scope;
    //echo "<pre>";
    //echo $item_scope;
    //print_r($scope_subject);
    //echo "</pre>";
    //exit();

    ?>
    <script src="../../include/ckeditor/ckeditor.js"></script>
    <div>
        <span>◎題幹　（ 螢幕截圖程式 <a href="./images/FSCapture.exe" style="color:#0000DD">下載</a> ）</span>
     <span>
         <textarea name="question" id="question" rows="5"><?php echo $item['question'];?></textarea>
     </span>
    </div>
    <div style="width:100%">
        <div style="float: left">
             <span>◎選目A</span>
             <span>
                 <textarea name="cha" id="cha" ><?php echo $item['cha'];?></textarea>
             </span>
        </div>
        <div style="float: left">
             <span>◎選目B</span>
             <span>
                 <textarea name="chb" id="chb"><?php echo $item['chb'];?></textarea>
             </span>
        </div>
    </div>
    <div style="width:100%">
        <div style="float: left">
             <span>◎選目C</span>
             <span>
                 <textarea name="chc" id="chc"><?php echo $item['chc'];?></textarea>
             </span>
        </div>
        <div style="float: left">
             <span>◎選目D</span>
             <span>
                 <textarea name="chd" id="chd"><?php echo $item['chd'];?></textarea>
             </span>
        </div>
    </div>
    <div style="clear: both">
        <div style="width: 100%;">
        <span>◎標準答案：
            <input type="radio" name="answer" value="A"<?php if ($item['answer']=="A") echo " checked";?>>(A)&nbsp;
                    <input type="radio" name="answer" value="B"<?php if ($item['answer']=="B") echo " checked";?>>(B)&nbsp;
                    <input type="radio" name="answer" value="C"<?php if ($item['answer']=="C") echo " checked";?>>(C)&nbsp;
                    <input type="radio" name="answer" value="D"<?php if ($item['answer']=="D") echo " checked";?>>(D)
        </span>
        </div>
        <div style="width: 100%;">
        <span>◎分科別：
            <select size="1" name="subject">
                <option value="">無設定</option>
                <?php
                foreach ($scope_subject['ALL'][$item_scope] as $scope=>$v) {
                    ?>
                    <option value="<?php echo $v['subject'];?>"<?php if ($item['subject']==$v['subject']) echo " selected";?>><?php echo $v['subject'];?></option>
                    <?php
                }
                ?>
            </select>
            </span>
        </div>

    </div>

    <Script language="JavaScript">

        CKEDITOR.replace("cha",{
            height : '80px',
            toolbar : 'choice'
        });
        CKEDITOR.replace("chb",{
            height : '80px',
            toolbar : 'choice'
        });
        CKEDITOR.replace("chc",{
            height : '80px',
            toolbar : 'choice'
        });
        CKEDITOR.replace("chd",{
            height : '80px',
            toolbar : 'choice'
        });
        CKEDITOR.replace("question",{
            height : '120px',
            toolbar : 'question'
        });
    </Script>
    <?php
} // end function



//讀取試題
function get_item($sn) {
 global $CONN;
 $sql="select * from resit_exam_items where sn='$sn'";
 $res=$CONN->Execute($sql) or die($sql);
 $row=$res->fetchRow();
 return $row;
}

//顯示試題
//$update_answer 0 呈現答案
//							 1 調整試題，以下拉式選單呈現
//							 2 呈現 $stud_ans的答案，並比對，出現○或╳
//							 3 調整分科
function show_item($sn,$update_answer=0,$stud_ans="",$site_num="") {
	
//	echo "顯示試題".$sn;
//	exit();
	
 global $CONN,$item_scope,$scope_subject;
 $sql="select * from resit_exam_items where sn='$sn'";
 $res=$CONN->Execute($sql) or die($sql);
 $row=$res->fetchRow();
 
 //檢查本份試卷有沒有作答記錄
 $sql="select count(*) as num from resit_exam_score where paper_sn='".$row['paper_sn']."' and entrance='1'";
 $res=$CONN->Execute($sql) or die($sql);
 $NO_DEL=$res->fields['num'];
 
 if ($NO_DEL==0) {
  $del_url="<img src='./images/del.png' class='edit_paper_delete' id='item-".$row['sn']."'>";  
 } else {
  $del_url="";
 }

 $bg_A=$bg_B=$bg_C=$bg_D="#FFFFFF";
 $target_bg="bg_".$row['answer'];
 $$target_bg="#FFCCCC";  
 
 //決定顯示型式(題幹圖大於400, 換行, 選目小於200,同一行, 200~400,分兩行,大於400各一行 )
 $fig_array=array("q","a","b","c","d");
  foreach ($fig_array as $v) {
		$target_fig_name="fig_".$v;
		$X="xx_".$v;
		$ssn=$row[$target_fig_name];
		if ($ssn) {
			$sql="select xx from resit_images where sn='$ssn'";
			$res=$CONN->Execute($sql) or die('無法讀取圖片width值! SQL='.$sql);
			${$X}=$res->fields['xx'];
    } else {
    	${$X}=0;
    }
  } // end foreach
  
  //顯示本題分科, 或調整分科 
  if ($update_answer==3) { 
   $subject="
    <select size='1' name='ch_subject[".$row['sn']."]'>
      <option value=''>無設定</option>";
    foreach ($scope_subject['ALL'][$item_scope] as $v) {
      $subject.="<option value='".$v['subject']."'".(($v['subject']==$row['subject'])?" selected":"").">".$v['subject']."</option>";
    }   
    $subject.="</select>";
    
    $update_answer=0;
  } else {
   $subject=$row['subject'];
  } // end if else update_answer==3
  
  
  //題幹版型
  if ($xx_q > 400) {
    $HTML_q="
     <tr>
       <td>".$row['question']."《<font size='2'>".$row['sn'].", ".$subject.", </font><img src='./images/edit.png' class='edit_paper_update' id='item-".$row['sn']."'> $del_url 》</td>
     </tr>
     <tr>
       <td><img src=\"img_show.php?sn=".$row['fig_q']."\"></td>
     </tr>";
  } elseif ($xx_q==0) {
    $HTML_q="
     <tr>
       <td>".$row['question']."《<font size='2'>".$row['sn'].", ".$subject.", </font><img src='./images/edit.png' class='edit_paper_update' id='item-".$row['sn']."'> $del_url 》</td>
     </tr>";
  } else {
     $HTML_q="
     <tr>
       <td valign='top'>".$row['question']."《<font size='2'>".$row['sn'].", ".$subject.", </font><img src='./images/edit.png' class='edit_paper_update' id='item-".$row['sn']."'> $del_url 》</td>
       <td valign='top' align='right'><img src=\"img_show.php?sn=".$row['fig_q']."\"></td>
     </tr>";   
  } // end if 題幹
  
  //選目板型
  if ($xx_a==0 and $xx_b==0 and $xx_c==0 and $xx_d==0) {
    //1列
    $HTML_choice="
    <tr>
      <td valign='top' bgcolor='$bg_A'>(A)".$row['cha']."</td><td valign='top' bgcolor='$bg_B'>(B)".$row['chb']."</td><td valign='top' bgcolor='$bg_C'>(C)".$row['chc']."</td><td valign='top' bgcolor='$bg_D'>(D)".$row['chd']."</td>
    </tr>
    ";
  } elseif($xx_a+$xx_b+$xx_c+$xx_d<800) {
    //1列
    $HTML_choice="
    <tr>
      <td width='25%' valign='top' bgcolor='$bg_A'>(A)".$row['cha'].(($row['fig_a']>0)?"<br><img src='img_show.php?sn=".$row['fig_a']."'>":"")."</td>
      <td width='25%' valign='top' bgcolor='$bg_B'>(B)".$row['chb'].(($row['fig_b']>0)?"<br><img src='img_show.php?sn=".$row['fig_b']."'>":"")."</td>
      <td width='25%' valign='top' bgcolor='$bg_C'>(C)".$row['chc'].(($row['fig_c']>0)?"<br><img src='img_show.php?sn=".$row['fig_c']."'>":"")."</td>
      <td width='25%' valign='top' bgcolor='$bg_D'>(D)".$row['chd'].(($row['fig_d']>0)?"<br><img src='img_show.php?sn=".$row['fig_d']."'>":"")."</td>
    </tr>
    ";
  } elseif($xx_a>200 and $xx_a<400) {
    //2列
    $HTML_choice="
    <tr>
      <td width='50%' valign='top' bgcolor='$bg_A'>(A)".$row['cha'].(($row['fig_a']>0)?"<br><img src='img_show.php?sn=".$row['fig_a']."'>":"")."</td>
      <td width='50%' valign='top' bgcolor='$bg_B'>(B)".$row['chb'].(($row['fig_b']>0)?"<br><img src='img_show.php?sn=".$row['fig_b']."'>":"")."</td>
		</tr>
		<tr>
      <td width='50%' valign='top' bgcolor='$bg_C'>(C)".$row['chc'].(($row['fig_c']>0)?"<br><img src='img_show.php?sn=".$row['fig_c']."'>":"")."</td>
      <td width='50%' valign='top' bgcolor='$bg_D'>(D)".$row['chd'].(($row['fig_d']>0)?"<br><img src='img_show.php?sn=".$row['fig_d']."'>":"")."</td>
    </tr>
    ";
  } else {
    //4列
    $HTML_choice="
    <tr>
      <td valign='top' bgcolor='$bg_A'>(A)".$row['cha'].(($row['fig_a']>0)?"<br><img src='img_show.php?sn=".$row['fig_a']."'>":"")."</td>
     </tr>
     <tr>
      <td valign='top' bgcolor='$bg_B'>(B)".$row['chb'].(($row['fig_b']>0)?"<br><img src='img_show.php?sn=".$row['fig_b']."'>":"")."</td>
     </tr>
     <tr>
      <td valign='top' bgcolor='$bg_C'>(C)".$row['chc'].(($row['fig_c']>0)?"<br><img src='img_show.php?sn=".$row['fig_c']."'>":"")."</td>
     </tr>
     <tr>
      <td valign='top' bgcolor='$bg_D'>(D)".$row['chd'].(($row['fig_d']>0)?"<br><img src='img_show.php?sn=".$row['fig_d']."'>":"")."</td>
    </tr>
    ";
  } // end if 選目版型
  
  //整合題幹和選目
  
  switch($update_answer){
  	//正常檢視
  	case 0:
  $main="
  <table border='0' width='800' cellspacing='0' cellpadding='0'>
   <tr>
   <td rowspan='2' valign='top' width='60' align='center'>( <font color=red>".$row['answer']."</font> )".$site_num.".</td>
   <td>
    <table border='0' width='100%'>    
    $HTML_q
    </table>
   </td>
   </tr>
   <tr>
    <td>
     <table border='0' width='100%'>
     $HTML_choice
     </table>
    </td>
    </tr>
  </table> 
  ";
  	break;
  	//調整解答
  	case 1:
  	$ans_select="
  	 <select size='1' name='answer[".$row['sn']."]'>
  	   <option value='' style='color:#FF0000'>-</option>
  	   <option value='A'".(($row['answer']=='A')?" selected":"").">A</option>
  	   <option value='B'".(($row['answer']=='B')?" selected":"").">B</option>
  	   <option value='C'".(($row['answer']=='C')?" selected":"").">C</option>
  	   <option value='D'".(($row['answer']=='D')?" selected":"").">D</option>  	   
  	 </select>
  	";
  $main="
  <table border='0' width='800' cellspacing='0' cellpadding='0'>
   <tr>
   <td rowspan='2' valign='top' width='60' align='center'>$ans_select".$site_num.".</td>
   <td>
    <table border='0' width='100%'>    
    $HTML_q
    </table>
   </td>
   </tr>
   <tr>
    <td>
     <table border='0' width='100%'>
     $HTML_choice
     </table>
    </td>
    </tr>
  </table> 
  ";  
  	break;
  	
	//比對學生作答  	
  	case 2:
			if ($row['answer']==$stud_ans) { 
			 $check_ans="<img src='./images/right.jpg'>";
			} else {
			 $check_ans="<img src='./images/wrong.png'>";
			}
  $main="
  <table border='0' width='800' cellspacing='0' cellpadding='0'>
   <tr>
   <td valign='top' width='60' align='center'>( <font color=green>".$stud_ans."</font> )".$site_num.".</td>
   <td>
    <table border='0' width='100%'>    
    $HTML_q
    </table>
   </td>
   </tr>
   <tr>
    <td valign='top' width='60' align='center'>$check_ans</td>
    <td>
     <table border='0' width='100%'>
     $HTML_choice
     </table>
    </td>
    </tr>
  </table> 
  ";  

  	break;

  
  
  } 
  
 
  return $main; 
   
}


//製作試題
function make_item_style($num,$row=array()) {
	
//	echo "顯示試題".$sn;
//	exit();
	
 global $CONN;

 //決定顯示型式(題幹圖大於400, 換行, 選目小於200,同一行, 200~400,分兩行,大於400各一行 )
 $fig_array=array("q","a","b","c","d");
  foreach ($fig_array as $v) {
		$target_fig_name="fig_".$v;
		$X="xx_".$v;
		$ssn=$row[$target_fig_name];
		if ($ssn) {
			$sql="select xx from resit_images where sn='$ssn'";
			$res=$CONN->Execute($sql) or die('無法讀取圖片width值! SQL='.$sql);
			${$X}=$res->fields['xx'];
    } else {
    	${$X}=0;
    }
  } // end foreach
  
  $row['question'] = preg_replace("/錯誤/",'<font color=red><u>錯誤</u></font>',$row['question']);
  
  //題幹版型
  if ($xx_q > 400) {
    $HTML_q="
     <tr>
       <td>".$row['question']." <font size=2>【".$row['subject']."】</font></td>
     </tr>
     <tr>
       <td><img src=\"img_show.php?sn=".$row['fig_q']."\"></td>
     </tr>";
  } elseif ($xx_q==0) {
    $HTML_q="
     <tr>
       <td>".$row['question']." <font size=2>【".$row['subject']."】</font></td>
     </tr>";
  } else {
     $HTML_q="
     <tr>
       <td valign='top'>".$row['question']." <font size=2>【".$row['subject']."】</font></td>
       <td valign='top' align='right'><img src=\"img_show.php?sn=".$row['fig_q']."\"></td>
     </tr>";   
  } // end if 題幹
  
  //進行選目亂數調換, 取回$rand_choice[1]='' , $rand_choice[2]='' ....
  $n1=strpos(" ".$row['chd'],"以上皆"); //找看看第四個選目是否為以上階
  if ($n1==1) {
   $rand_choice=make_rand(array(1=>'A',2=>'B',3=>'C'));
   $rand_choice[4]='D';  
  } else {
   $rand_choice=make_rand(array(1=>'A',2=>'B',3=>'C',4=>'D'));
  }
  
  //轉小寫
  for ($i;$i<=4;$i++) {
    $choice_key=strtolower($rand_choice[$i]);
    $a='ch'.$choice_key;
    $b='fig_'.$choice_key;
    $choice[$i]=$row[$a];       //選項文字
    $choice_fig[$i]=$row[$b];   //選項圖片
  }
  
  //選項 1-4 ,實際選項 $rand_choice[$i] , 呈現文字 $choice[$i] 呈現圖片 $choice_fig[$i]
  
  
  //選目板型
  if ($xx_a==0 and $xx_b==0 and $xx_c==0 and $xx_d==0) {
    //1列
    $HTML_choice="
    <tr>
      <td valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[1]."'>(A)".$choice[1]."</td>
      <td valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[2]."'>(B)".$choice[2]."</td>
      <td valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[3]."'>(C)".$choice[3]."</td>
      <td valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[4]."'>(D)".$choice[4]."</td>
    </tr>
    ";
  } elseif($xx_a+$xx_b+$xx_c+$xx_d<800) {
    //1列
    $HTML_choice="
    <tr>
      <td width='25%' valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[1]."'>(A)".$choice[1].(($choice_fig[1]>0)?"<br><img src='img_show.php?sn=".$choice_fig[1]."'>":"")."</td>
      <td width='25%' valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[2]."'>(B)".$choice[2].(($choice_fig[2]>0)?"<br><img src='img_show.php?sn=".$choice_fig[2]."'>":"")."</td>
      <td width='25%' valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[3]."'>(C)".$choice[3].(($choice_fig[3]>0)?"<br><img src='img_show.php?sn=".$choice_fig[3]."'>":"")."</td>
      <td width='25%' valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[4]."'>(D)".$choice[4].(($choice_fig[4]>0)?"<br><img src='img_show.php?sn=".$choice_fig[4]."'>":"")."</td>
    </tr>
    ";
  } elseif($xx_a>200 and $xx_a<400) {
    //2列
    $HTML_choice="
    <tr>
      <td width='50%' valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[1]."'>(A)".$choice[1].(($choice_fig[1]>0)?"<br><img src='img_show.php?sn=".$choice_fig[1]."'>":"")."</td>
      <td width='50%' valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[2]."'>(B)".$choice[2].(($choice_fig[2]>0)?"<br><img src='img_show.php?sn=".$choice_fig[2]."'>":"")."</td>
		</tr>
		<tr>
      <td width='50%' valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[3]."'>(C)".$choice[3].(($choice_fig[3]>0)?"<br><img src='img_show.php?sn=".$choice_fig[3]."'>":"")."</td>
      <td width='50%' valign='top'><input type='radio' name='answers[$num]' value='".$rand_choice[4]."'>(D)".$choice[4].(($choice_fig[4]>0)?"<br><img src='img_show.php?sn=".$choice_fig[4]."'>":"")."</td>
    </tr>
    ";
  } else {
    //4列
    $HTML_choice="
    <tr>
      <td><input type='radio' name='answers[$num]' value='".$rand_choice[1]."'>(A)".$choice[1].(($choice_fig[1]>0)?"<br><img src='img_show.php?sn=".$choice_fig[1]."'>":"")."</td>
     </tr>
     <tr>
      <td><input type='radio' name='answers[$num]' value='".$rand_choice[2]."'>(B)".$choice[2].(($choice_fig[2]>0)?"<br><img src='img_show.php?sn=".$choice_fig[2]."'>":"")."</td>
     </tr>
     <tr>
      <td><input type='radio' name='answers[$num]' value='".$rand_choice[3]."'>(C)".$choice[3].(($choice_fig[3]>0)?"<br><img src='img_show.php?sn=".$choice_fig[3]."'>":"")."</td>
     </tr>
     <tr>
      <td><input type='radio' name='answers[$num]' value='".$rand_choice[4]."'>(D)".$choice[4].(($choice_fig[4]>0)?"<br><img src='img_show.php?sn=".$choice_fig[4]."'>":"")."</td>
    </tr>
    ";
  } // end if 選目版型
  
  //整合題幹和選目
  $main="
  <table border='0'  width='100%' cellspacing='0' cellpadding='0'>
   <tr id=\"tr".$num."\" class=\"bg_0\" onMouseOver=\"OverLine('tr".$num."',$num)\" onMouseOut=\"OutLine('tr".$num."',$num)\" onClick=\"ClickLine('tr".$num."',$num)\">
   <td rowspan='2' valign='top' width='30' align='center'>$num.</td>
   <td>
    <table border='0' width='100%' class='test_item' style='font-size:12pt'>    
    $HTML_q
    </table>
   </td>
   </tr>
   <tr>
    <td>
     <table border='0' width='100%' class='test_item' style='font-size:12pt'>
     $HTML_choice
     </table>
    </td>
    </tr>
  </table> 
  ";
  
  return $main; 
   
}

//選目亂數 (傳入 a,b,c 或 a,b,c,d
function make_rand($rand_choice=array()) {
	
  $return_choice=array();
  $M=count($rand_choice);
  $i=0;
  do {
   $a=mt_rand(1,$M);
   if ($rand_choice[$a]!="") {
    $i++;
    $return_choice[$i]=$rand_choice[$a];
		$rand_choice[$a]="";
   }
  } while ($i<$M);  
  return $return_choice;
}

//圖檔處理
function ImageResize($from_filename, $save_filename, $in_width=400, $in_height=300, $quality=100)
{
    $allow_format = array('jpeg', 'png', 'gif');
    $sub_name = $t = '';

    // Get new dimensions
    $img_info = getimagesize($from_filename);
    $width    = $img_info['0'];
    $height   = $img_info['1'];
    $imgtype  = $img_info['2'];
    $imgtag   = $img_info['3'];
    $bits     = $img_info['bits'];
    $channels = $img_info['channels'];
    $mime     = $img_info['mime'];

    list($t, $sub_name) = split('/', $mime);
    if ($sub_name == 'jpg') {
        $sub_name = 'jpeg';
    }

    if (!in_array($sub_name, $allow_format)) {
        return false;
    }

    
    // 取得縮在此範圍內的比例
    $percent = getResizePercent($width, $height, $in_width, $in_height);
    $new_width  = $width * $percent;
    $new_height = $height * $percent;

    // Resample
    $image_new = imagecreatetruecolor($new_width, $new_height);

    // $function_name: set function name
    //   => imagecreatefromjpeg, imagecreatefrompng, imagecreatefromgif
    /*
    // $sub_name = jpeg, png, gif
    $function_name = 'imagecreatefrom' . $sub_name;

    if ($sub_name=='png')
        return $function_name($image_new, $save_filename, intval($quality / 10 - 1));

    $image = $function_name($filename); //$image = imagecreatefromjpeg($filename);
    */
    
    
    //$image = imagecreatefromjpeg($from_filename);
    
    $function_name = 'imagecreatefrom'.$sub_name;
    $image = $function_name($from_filename);

    imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    return imagejpeg($image_new, $save_filename, $quality);
     
}

/**
 * 抓取要縮圖的比例
 * $source_w : 來源圖片寬度
 * $source_h : 來源圖片高度
 * $inside_w : 縮圖預定寬度
 * $inside_h : 縮圖預定高度
 *
 * Test:
 *   $v = (getResizePercent(1024, 768, 400, 300));
 *   echo 1024 * $v . "\n";
 *   echo  768 * $v . "\n";
 */
function getResizePercent($source_w, $source_h, $inside_w, $inside_h)
{
    if ($source_w < $inside_w && $source_h < $inside_h) {
        return 1; // Percent = 1, 如果都比預計縮圖的小就不用縮
    }

    $w_percent = $inside_w / $source_w;
    $h_percent = $inside_h / $source_h;

    return ($w_percent > $h_percent) ? $h_percent : $w_percent;
}
 

?>