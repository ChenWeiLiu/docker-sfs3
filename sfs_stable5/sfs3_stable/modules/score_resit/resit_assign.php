<?php	
header('Content-type: text/html;charset=big5');
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
//驗證是否登入
sfs_check(); 

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取補考學期別設定
$sql="select * from resit_seme_setup limit 1";
$res=$CONN->Execute($sql);
$SETUP=$res->fetchrow();
$C_year_seme=substr($SETUP['now_year_seme'],0,3)."學年度 第 ".substr($SETUP['now_year_seme'],-1)." 學期";
//目前處理的學年學期
$sel_year = substr($SETUP['now_year_seme'],0,3);
$sel_seme = substr($SETUP['now_year_seme'],-1);

//已選定的年級
$Cyear=$_POST['Cyear'];

//抓取本學期所有課程設定(領域－分科) 3維陣列 $scope_subject[scope][][]
$scope_subject=get_year_seme_scope($sel_year,$sel_seme,$Cyear);


//確認可補考的年級
//例如: 以國中而言, 現今學年 103 , 若啟用 102學年, 只能考該年的一年級和二年級, 因為三年級已畢業
// 國中或國小判定 $IS_JHORES=6 (國中) , $IS_JHORES=0 (國小)
if ($IS_JHORES==6) {
	$SY=$curr_year-3;   //以103為例, 基準點為 100
} else {
	$SY=$curr_year-6;   //以103為例, 基準點為 97
}

//製作年級選單
$sy_circle=$sel_year-$SY;	
$now_cy=3-$sy_circle;

$class_year_list="
  <select size=\"1\" name=\"Cyear\" onchange=\"this.form.opt1.value='';this.form.opt2.value='';this.form.act.value='';this.form.submit()\">
   <option value=''>請選擇年級</option>";
   for ($i=1;$i<=$sy_circle;$i++) {
    $CY=$i+$IS_JHORES;
    $NCY=$CY+$now_cy;
    $class_year_list.="<option value='$CY'".(($CY==$Cyear)?" selected":"").">".$school_kind_name[$CY]."級 (目前就讀".$school_kind_name[$NCY]."級)</option>";
   }    
$class_year_list.="
  </select>
";


 		if($Cyear>2){
			$ss_link=array("語文"=>"language","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
			$link_ss=array("language"=>"語文","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
		} else {
			$ss_link=array("語文"=>"language","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
			$link_ss=array("language"=>"語文","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
		}
// POST後執行的程式
//設定試卷，ajax,執行後 exit
if ($_POST['act']=='setup_paper') {	
  $scope=$_POST['scope'];
	$sql="select * from resit_paper_setup where seme_year_seme='".$SETUP['now_year_seme']."' and class_year='$Cyear' and scope='$scope'";	
	$res=$CONN->Execute($sql);
  if ($res->recordcount()) {
   $start_time=$res->fields['start_time'];
   $end_time=$res->fields['end_time'];
   $timer_mode=$res->fields['timer_mode'];
   $timer=$res->fields['timer'];
   $relay_answer=$res->fields['relay_answer'];
   $items=$res->fields['items'];
   $double_papers=$res->fields['double_papers'];
   $item_mode=$res->fields['item_mode'];
   $top_marks=$res->fields['top_marks'];
  } else {
   $start_time=date('Y-m-d H:i:00');
   $end_time=date('Y-m-d H:i:00');
   $timer_mode=0;
   $timer=45;
   $relay_answer=0;
   $items=0;
   $double_papers=0;
   $top_marks=100;
  }	
   //製作各分科出題數表單
   //讀取分析出題數設定
   $subject_items=get_scope_subject_items($SETUP['now_year_seme'],$Cyear,$scope);
   $subject_items_input="
    <table border='0' >    
   ";
   foreach ($scope_subject['ALL'][$scope] as $k=>$v) {
   	$subject_items[$k]=($subject_items[$k]=="" or $subject_items[$k]<0)?(20):$subject_items[$k];
   	$subject_items_input.="<tr><td width='20'>&nbsp;</td><td>".$v['subject']." </td><td><input type='text' name='subject_".$k."' size='5' value='".$subject_items[$k]."'>題 <font size=2>(加權:".$v['rate'].", 已命".$v['items']."題)</font></td></tr>";
   }
   $subject_items_input.="</table>";
   
   
   $main="
   <input type='hidden' name='scope' value='$scope'>
   <table border='0' cellpadding='3'>
   	<tr>
   	  <td colspan='2' style='color:#800000'><b>".$link_ss[$scope]."領域</b>試卷設定</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>考試開始時間</td>
   		<td valign='top'><input type='text' size='20' name='start_time' value='$start_time'></td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>領卷結束時間</td>
   		<td valign='top'><input type='text' size='20' name='end_time' value='$end_time'></td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>計時模式</td>
   		<td valign='top'>
   		    <input type='radio' name='timer_mode' value='0'".(($timer_mode==0)?" checked":"").">個別計時
   		    <input type='radio' name='timer_mode' value='1'".(($timer_mode==1)?" checked":"").">同時計時
   		</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>計時時間</td>
   		<td valign='top'><input type='text' size='5' name='timer' value='$timer'>分鐘</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>出題模式</td>
   		<td valign='top'>
   		<table border='1' style='border-collapse:collapse' bordercolor='#000000'>
   			<tr>
   				<td>
   		      <input type='radio' name='item_mode' value='0'".(($item_mode==0)?" checked":"").">隨機取<input type='text' size='5' name='items' value='$items'>題成卷(輸入0表示全部)
   		    </td>
   		   </tr>
   		   <tr>
   		   	<td>
   		   	  <input type='radio' name='item_mode' value='1'".(($item_mode==1)?" checked":"").">依所含分科出題<br>
						$subject_items_input						
					</td>
   				</tr>
   			</table>
   		</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>開放學生查詢作答</td>
   		<td valign='top'>
   		    <input type='radio' name='relay_answer' value='0'".(($relay_answer==0)?" checked":"").">否
   		    <input type='radio' name='relay_answer' value='1'".(($relay_answer==1)?" checked":"").">是
   		    <br><font size=2>(若要開放，建議考試完畢後再開啟)</font>
   		</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>斷線後是否可再領卷</td>
   		<td valign='top'>
   		    <input type='radio' name='double_papers' value='0'".(($double_papers==0)?" checked":"").">否
   		    <input type='radio' name='double_papers' value='1'".(($double_papers==1)?" checked":"").">是
   		    <br><font size=2>(預設「否」，可避免異地登入同帳號重覆領卷)</font>
   		</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>試卷總分</td>
   		<td valign='top'><input type='text' size='5' name='top_marks' value='$top_marks'>分</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>儲存後重算成績</td>
   		<td valign='top'><input type='checkbox' name='reset_score'></td>
   	</tr>

   </table>

   ";

	echo $main;
  exit();
} // end if ($_POST['act']=='setup_paper')

//儲存試卷設定
if ($_POST['act']=='setup_paper_submit') {
		
  $scope=$_POST['scope'];
	$sql="select * from resit_paper_setup where seme_year_seme='".$SETUP['now_year_seme']."' and class_year='$Cyear' and scope='$scope'";	
	$res=$CONN->Execute($sql);
	
	$start_time=$_POST['start_time'];
	$end_time=$_POST['end_time'];
	$timer_mode=$_POST['timer_mode'];
	$timer=$_POST['timer'];
	$items=$_POST['items'];
	$item_mode=$_POST['item_mode'];
	$relay_answer=$_POST['relay_answer'];
	$double_papers=$_POST['double_papers'];
	$top_marks=$_POST['top_marks'];
	//echo "<pre>";
	//print_r($_POST);
	//exit();
	if ($res->recordcount()) {
	  $sql="update resit_paper_setup set start_time='$start_time',end_time='$end_time',timer_mode='$timer_mode',timer='$timer',items='$items',relay_answer='$relay_answer',double_papers='$double_papers',item_mode='$item_mode',top_marks='$top_marks' where seme_year_seme='".$SETUP['now_year_seme']."' and class_year='$Cyear' and scope='$scope'";
    $res=$CONN->Execute($sql) or die ('Error! Query='.$sql);
  
    //是否重算分數
    $reset_score_txt="";
    if ($_POST['reset_score']==1) {
  	  $paper_setup=get_paper_sn($SETUP['now_year_seme'],$Cyear,$scope);
      $sql="select sn,items,answers from resit_exam_score where paper_sn='".$paper_setup['sn']."' and complete='1'";  
      $res=$CONN->Execute($sql)or die ('Error! Query='.$sql);
      $i=0;  //計數
      while ($row=$res->fetchRow()) {
        $i++;
        $items=unserialize($row['items']);
				$answers=unserialize($row['answers']);
				//逐題評分
				$item_count=0;
				$item_correct=0;
				foreach ($items as $k=>$v) {
    	  	$item_count++;
    	  	// $v 為試題的 sn
    	  	$sql="select answer from resit_exam_items where sn='$v'";
        	$res_ans=$CONN->Execute($sql) or die($sql);
        	$row_ans=$res_ans->fetchRow();
					if ($row_ans['answer']==$answers[$k]) $item_correct++;					  
    
        } // end foreach
				$score=($item_correct/$item_count)*$top_marks;
        $score=round($score,2);
				$sql_score="update resit_exam_score set score='$score' where sn='".$row['sn']."'";
				$res_score=$CONN->Execute($sql_score)or die ('Error! Query='.$sql_score);
				//echo $sql_score."<br>";
      } // end while
      
      $reset_score_txt="已重算 ".$i." 位學生的成績!";
      
    } // end if $_POST['reset_score']==1

	} else {
	  $sql="insert into resit_paper_setup (seme_year_seme,class_year,scope,start_time,end_time,timer_mode,timer,items,relay_answer,double_papers,item_mode,top_marks) values ('".$SETUP['now_year_seme']."','$Cyear','$scope','$start_time','$end_time','$timer_mode','$timer','$items','$relay_answer','$double_papers','$item_mode','$top_marks')";
	  $res=$CONN->Execute($sql) or die ('Error! Query='.$sql);
	}

	//儲存分科設定
	if ($item_mode=='1') {
		$subject=$_POST['subject'];		
		$SS=explode(";",$subject);
				
		foreach ($SS as $v) {
			$V=explode(",",$v);
			//檢查是否已存在
		   $sql="select sn from resit_scope_subject where seme_year_seme='".$SETUP['now_year_seme']."' and cyear='$Cyear' and scope='$scope' and subject_id='".$V[0]."'";
		   $res=$CONN->Execute($sql) or user_error("讀取分科設定錯誤! $sql",256);
		   if ($res->RecordCount()==1) {
		     $sn=$res->fields['sn'];		     
		     $sql="update resit_scope_subject set items='".$V[1]."' where sn='$sn'";
		     $res=$CONN->Execute($sql) or user_error("儲存分科設定錯誤! $sql",256);
		   } else {
		   	 $seme_year_seme=$SETUP['now_year_seme'];
		   	 $subject_id=$V[0];
		   	 $subject=$scope_subject['ALL'][$scope][$subject_id]['subject'];
		   	 $items=$V[1];
		   	 $sql="insert into resit_scope_subject (seme_year_seme,cyear,scope,subject_id,subject,items) values ('$seme_year_seme','$Cyear','$scope','$subject_id','$subject','$items')";
		     $res=$CONN->Execute($sql) or user_error("儲存分科設定錯誤! $sql",256);
		   } // end if $res->RecoreCount()  
		} // end foreach   	
	} // end if ($item_mode==1)

	echo "<font color=red>".$link_ss[$scope]."</font>領域試卷設定儲存完畢! <br> $reset_score_txt";
	
  exit();
}


//儲存試題
if ($_POST['act']=='edit_paper_submit') {		 
	$opt2=$_POST['opt2'];
	$item_scope=$_POST['item_scope'];
	$paper_setup=get_paper_sn($SETUP['now_year_seme'],$Cyear,$item_scope);
  $item_sn=$_POST['item_sn'];
  
  $question=trim($_POST['question']);
  $cha=trim($_POST['cha']);
  $chb=trim($_POST['chb']);
  $chc=trim($_POST['chc']);
  $chd=trim($_POST['chd']);
  $answer=$_POST['answer'];
  $subject=$_POST['subject'];

	//處理圖片 取得 $fig_q,$fig_a,$fig_b,$fig_c,$fig_d 五個值
	$fig_array=array("q","a","b","c","d");
	foreach ($fig_array as $v) {
		$target_fig="thefig_".$v;
		$target_fig_name="fig_".$v;
		${$target_fig_name}="";
	   if ($_FILES[$target_fig]!="") {	   	
	   	//檢驗副檔名
      $expand_name=explode(".",$_FILES[$target_fig]['name']);
      $nn=count($expand_name)-1;
      $ATTR=strtolower($expand_name[$nn]); //轉小寫副檔名
      if ($ATTR=='jpg' or $ATTR=='png') {
          $img_info = getimagesize($_FILES[$target_fig]['tmp_name']);
    			$xx   = $img_info['0'];
    			$yy   = $img_info['1'];
					$imgtype=$_FILES[$target_fig]['type'];
					
          $sFP=fopen($_FILES[$target_fig]['tmp_name'],"r");				//載入檔案
       		$sFile=addslashes(fread($sFP,filesize($_FILES[$target_fig]['tmp_name'])));
       		$sFile=base64_encode($sFile);
    			
    			$sql="insert into resit_images (filetype,xx,yy,content) values ('$imgtype','$xx','$yy','$sFile')";
					$res=$CONN->Execute($sql);					
		     	list(${$target_fig_name})=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
      } 
     } //end if	
	} // end foreach

  //echo "'$fig_q','$fig_a','$fig_b','$fig_c','$fig_d'";
  //exit();

  if ($item_sn=='') {
	 //新增試題
	 $sql="insert into resit_exam_items (paper_sn,question,cha,chb,chc,chd,fig_q,fig_a,fig_b,fig_c,fig_d,answer,subject) values ('".$paper_setup['sn']."','$question','$cha','$chb','$chc','$chd','$fig_q','$fig_a','$fig_b','$fig_c','$fig_d','$answer','$subject')";
	 $res=$CONN->Execute($sql) or die ("Error! SQL=".$sql);
	 //取得最後的 sn , 以顯示最後編輯的試題	
	 list($Last_item_sn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
  } else {
   $item_org=get_item($item_sn);
   //若有附圖,刪除原圖
   	$fig_array=array("q","a","b","c","d");
	  foreach ($fig_array as $v) {
		 $target_fig="fig_".$v;
		 if ($item_org[$target_fig]>0) {
		   if (${$target_fig}>0 or $_POST['del_fig'][$v]) {
		     $CONN->Execute("delete from resit_images where sn='".$item_org[$target_fig]."'");
		   } else {
		     ${$target_fig}=$item_org[$target_fig];
		   }
		 }
		} // end foreach
		
		$sql="update resit_exam_items set question='$question',cha='$cha',chb='$chb',chc='$chc',chd='$chd',fig_q='$fig_q',fig_a='$fig_a',fig_b='$fig_b',fig_c='$fig_c',fig_d='$fig_d',answer='$answer',subject='$subject' where sn='$item_sn'";
		$res=$CONN->Execute($sql) or die ("修改試題失敗! SQL=".$sql);
	
   //編輯試題        
   $Last_item_sn=$item_sn; 
  }
  //保持編輯試題狀態
  $_POST['act']=($opt2!='')?$opt2:'edit_paper';  
} // end if edit_paper_submit


//儲存試題
if ($_POST['act']=='edit_paper_submit_new') {
	$opt2=$_POST['opt2'];
	$item_scope=$_POST['item_scope'];
	$paper_setup=get_paper_sn($SETUP['now_year_seme'],$Cyear,$item_scope);
	$item_sn=$_POST['item_sn'];

	$question=trim($_POST['question']);
	$cha=trim($_POST['cha']);
	$chb=trim($_POST['chb']);
	$chc=trim($_POST['chc']);
	$chd=trim($_POST['chd']);
	$answer=$_POST['answer'];
	$subject=$_POST['subject'];

	if ($item_sn=='') {
		//新增試題
		$sql="insert into resit_exam_items (paper_sn,question,cha,chb,chc,chd,fig_q,fig_a,fig_b,fig_c,fig_d,answer,subject) values ('".$paper_setup['sn']."','$question','$cha','$chb','$chc','$chd','$fig_q','$fig_a','$fig_b','$fig_c','$fig_d','$answer','$subject')";
		$res=$CONN->Execute($sql) or die ("Error! SQL=".$sql);
		//取得最後的 sn , 以顯示最後編輯的試題
		list($Last_item_sn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	} else {
		$item_org=get_item($item_sn);
		//若有附圖,刪除原圖
		$fig_array=array("q","a","b","c","d");
		foreach ($fig_array as $v) {
			$target_fig="fig_".$v;
			if ($item_org[$target_fig]>0) {
				if (${$target_fig}>0 or $_POST['del_fig'][$v]) {
					$CONN->Execute("delete from resit_images where sn='".$item_org[$target_fig]."'");
				} else {
					${$target_fig}=$item_org[$target_fig];
				}
			}
		} // end foreach

		$sql="update resit_exam_items set question='$question',cha='$cha',chb='$chb',chc='$chc',chd='$chd',fig_q='$fig_q',fig_a='$fig_a',fig_b='$fig_b',fig_c='$fig_c',fig_d='$fig_d',answer='$answer',subject='$subject' where sn='$item_sn'";
		$res=$CONN->Execute($sql) or die ("修改試題失敗! SQL=".$sql);

		//編輯試題
		$Last_item_sn=$item_sn;
	}
	//保持編輯試題狀態
	$_POST['act']=($opt2!='')?$opt2:'edit_paper_new';
} // end if edit_paper_submit



//修改試題
if ($_POST['act']=='edit_paper_update') {		 
  $item_sn=$_POST['item_sn'];
	$item_scope=$_POST['item_scope'];
	$item=get_item($item_sn);
	//修改完要返回的動作
  $opt2=$_POST['opt2'];
  //保持編輯試題狀態
  $_POST['act']='edit_paper_new';
}

//刪除試題
if ($_POST['act']=='edit_paper_delete') {		 
  $item_sn=$_POST['item_sn'];
	$item_scope=$_POST['item_scope']; 
	$item_org=get_item($item_sn);	
   //若有附圖,刪除原圖
   	$fig_array=array("q","a","b","c","d");
	  foreach ($fig_array as $v) {
		 $target_fig="fig_".$v;
		 if ($item_org[$target_fig]>0) {
		     $CONN->Execute("delete from resit_images where sn='".$item_org[$target_fig]."'");
		 }
		} // end foreach
		//刪除試題
 	  $CONN->Execute("delete from resit_exam_items where sn='".$item_org['sn']."'");
	//刪除完要返回的動作
  $_POST['act']=$_POST['opt2'];  
} // end if $_POST['act']=='edit_paper_delete'

//儲存快貼的試題
if ($_POST['act']=='paste_paper_save') {

	$item_scope=$_POST['item_scope'];
	$paper_setup=get_paper_sn($SETUP['now_year_seme'],$Cyear,$item_scope);
  
  foreach ($_POST[field] as $I=>$P) {
		$save=0;
	   if ($_POST['save_it'][$I]==1) {
			foreach ($P as $k=>$v) {
			  if ($_POST['to_field'][$k]!='none') {
			   $save=1;
			   $f=$_POST['to_field'][$k];
			   ${$f}=$v;
			  } // end if
			} // end foreach ($P as $k=>$v)

			if ($question=='' and $cha=='' and $chb=='' and $chc=='' and $chd=='' and $answer=='') continue;

			$sql="insert into resit_exam_items (paper_sn,question,cha,chb,chc,chd,answer) values ('".$paper_setup['sn']."','$question','$cha','$chb','$chc','$chd','$answer')";
			$res=$CONN->Execute($sql) or die("儲存發生錯誤了! SQL=".$sql);
	   } // end if ($_POST['save_it'][$I]==1)
  } // end foreach ($_POST[field] as $I=>$P)
  //切換為列出試題
  $_POST['act']='list_paper';  
} // end if edit_paper_submit

//調整解答 - 儲存
if ($_POST['act']=='list_paper_answer_save') {		 
	$item_scope=$_POST['item_scope'];
  
  foreach ($_POST['answer'] as $sn=>$v) {
    $sql="update resit_exam_items set answer='$v' where sn='$sn'";
    $res=$CONN->Execute($sql) or die('儲存解答失敗！SQL='.$sql);
  } // end foreach ($_POST[field] as $I=>$P)
  //切換為列出試題
  $_POST['act']='list_paper';  
} // end if list_paper_answer_save

//設定試題分科 - 儲存
if ($_POST['act']=='list_paper_subject_save') {		 
	$item_scope=$_POST['item_scope'];  
  foreach ($_POST['ch_subject'] as $sn=>$v) {
    $sql="update resit_exam_items set subject='$v' where sn='$sn'";
    $res=$CONN->Execute($sql) or die('儲存試題分科設定失敗！SQL='.$sql);
  } // end foreach ($_POST[field] as $I=>$P)
  //切換為列出試題
  $_POST['act']='list_paper';  
} // end if list_paper_answer_save


//匯出試題 
 if ($_POST['act']=='download_paper') {
			$scope=$_POST['opt1'];
 			$main=$SETUP['now_year_seme']."-".$Cyear."-".$scope."\r\n";
 			$sql="select a.* from resit_exam_items a, resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='".$SETUP['now_year_seme']."' and b.class_year='$Cyear' and b.scope='$scope'";
 			$res=$CONN->Execute($sql);
 			$row=$res->GetRows();
 			foreach ($row as $K) {
       $main.=$K['sort']."\r\n";
       $main.=$K['question']."\r\n";
       $main.=$K['cha']."\r\n";
       $main.=$K['chb']."\r\n";
       $main.=$K['chc']."\r\n";
       $main.=$K['chd']."\r\n";
       
       $fig_array=array("q","a","b","c","d");
       foreach ($fig_array as $v) {
		    $target_fig_name="fig_".$v;
        $ssn=$K[$target_fig_name];
        if ($ssn!="") {
       		$query="select filetype,xx,yy,content from resit_images where sn='".$ssn."'";
			 		$res=$CONN->Execute($query);
			 		$filetype=$res->fields['filetype'];
			 		$xx=$res->fields['xx'];
			 		$yy=$res->fields['yy'];
			 		$picture=$res->fields['content'];
			 		$main.=$filetype.",".$xx.",".$yy."\r\n";
			 		$main.=$picture."\r\n";
			 	} else {
			 	  $main.="\r\n\r\n";
			 	} // end if
       } // end foreach

       $main.=$K['answer']."\r\n";
       $main.=$K['subject']."\r\n";
 			}

		$filename=$SETUP['now_year_seme']."_".$Cyear."年級_".$link_ss[$scope]."考題檔.wit";
		
		//以串流方式送出
		header("Content-disposition: attachment; filename=$filename");
		header("Content-type: application/vnd.sun.xml.writer");
		header("Cache-Control: max-age=0");
		header("Pragma: public");
		header("Expires: 0");

		echo $main;  
  exit();
  
  } // end if $_POST['act']=='download_paper'

//匯入試題 - submit
if ($_POST['act']=='upload_paper_submit') {
	$item_scope=$_POST['item_scope'];
	$paper_setup=get_paper_sn($SETUP['now_year_seme'],$Cyear,$item_scope);

	//開始轉檔
  $aFile=fopen($_FILES['thefile']['tmp_name'],"r");
  $try_title=preg_replace("/\\r\\n/","",fgets($aFile,1024));
  
  $C=explode("-",$try_title);
  //檢驗是否為 wit檔  
	if (!in_array($C[2], $ss_link)) {
    echo "檔案格式不符！";
    exit();
	}
  
  while (!feof($aFile)) {
   $sort=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $question=addslashes(preg_replace("/\\r\\n/","",fgets($aFile,2048000)));
   $cha=addslashes(preg_replace("/\\r\\n/","",fgets($aFile,2048000)));
   $chb=addslashes(preg_replace("/\\r\\n/","",fgets($aFile,2048000)));
   $chc=addslashes(preg_replace("/\\r\\n/","",fgets($aFile,2048000)));
   $chd=addslashes(preg_replace("/\\r\\n/","",fgets($aFile,2048000)));
   $fig_q_filetype=addslashes(preg_replace("/\\r\\n/","",fgets($aFile,2048000)));
   $fig_q_content=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $fig_a_filetype=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $fig_a_content=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $fig_b_filetype=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $fig_b_content=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $fig_c_filetype=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $fig_c_content=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $fig_d_filetype=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $fig_d_content=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $answer=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   $subject=preg_replace("/\\r\\n/","",fgets($aFile,2048000));
   if ($question=='') continue;

   //先存入圖片
    $fig_array=array("q","a","b","c","d");
    foreach ($fig_array as $v) {
		    $fig_filetype="fig_".$v."_filetype";
		    $fig_content="fig_".$v."_content";
		    $target_fig_name="fig_".$v;
		    if (${$fig_content}!="") {
		    	$F=explode(",",${$fig_filetype});
		    	$filetype=$F[0];
		    	$xx=$F[1];
		    	$yy=$F[2];
		    	$content=${$fig_content};
    			$sql="insert into resit_images (filetype,xx,yy,content) values ('$filetype','$xx','$yy','$content')";
					$res=$CONN->Execute($sql) or die("失入圖片失敗! SQL=".$sql);					
		     	list(${$target_fig_name})=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		     	//echo $target_fig_name.'='.${$target_fig_name};
		     	//exit();
		    } else {
		     ${$target_fig_name}='';
		    }
    } // end foreach
   
   //存入試題
	  $sql="insert into resit_exam_items (paper_sn,question,cha,chb,chc,chd,fig_q,fig_a,fig_b,fig_c,fig_d,answer,subject) values ('".$paper_setup['sn']."','$question','$cha','$chb','$chc','$chd','$fig_q','$fig_a','$fig_b','$fig_c','$fig_d','$answer','$subject')";
    $res=$CONN->Execute($sql) or die("儲存發生錯誤了! SQL=".$sql);
  } // end while (!feof($aFile))
	$_POST['act']='list_paper';
}

//**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題

head();
//列出選單
echo $tool_bar;
?>
<form name="myform" id="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="opt1" value="">
	<input type="hidden" name="opt2" value="<?php echo $opt2;?>">
<?php
 echo "<font color=red>補考學期別：".$C_year_seme."</font><br>";
 echo "請選擇命題的年級：".$class_year_list;
 
 if ($Cyear!="") { 
 	?>
 <table border="0" width="100%">
  <tr>
  	<!--左畫面 -->
    <td width="660" valign="top" rowspan="2">
    	
 		<table border="1"  style="border-collapse:collapse;font-size:10pt" bordercolor="#111111" cellpadding="3" width="100%">
 		 <tr bgcolor="#FFCCFF" width="100%">
 			<td align="center">領域別</td>
 			<td align="center">題數 </td>
 			<td align="center">操作</td>
 		 </tr>
 		<?php
 		foreach ($ss_link as $k=>$v) {
 			
 			if ($_POST['opt1']!="") {
 			  $display=($_POST['opt1']==$v)?"table-row":"none";
 			  //目前操作領域別改由 $item_scope 記錄
 			  $item_scope=$_POST['opt1'];
 			} else { 				
 			  $display="table-row"; 			  
 			}
 			
 			//計算本領域題數
 			$sql="select a.* from resit_exam_items a, resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='".$SETUP['now_year_seme']."' and b.class_year='$Cyear' and b.scope='$v'";
 			$res=$CONN->Execute($sql);
 			$num=$res->RecordCount();
			//確認已啟用，否則後三顆鈕 disable 					
 			$sql="select * from resit_paper_setup where seme_year_seme='".$SETUP['now_year_seme']."' and class_year='$Cyear' and scope='$v'";	
			$res=$CONN->Execute($sql);
			$disabled=($res->recordcount()==0)?"disabled":"";
 		  ?>
 		  <tr width="100%" class="scope_table" id="<?php echo $v;?>" style="background-Color:#FFFFFF;display:<?php echo $display;?>">
 		    <td><?php echo $k;?></td>
 		    <td align="center"><?php echo $num;?></td>
 				<td align="center">
 					<input type="button" value="設定" class="setup_paper" id="btn-<?php echo $v;?>-setup">
 					<input type="button" value="快貼" class="paste_paper" id="btn-<?php echo $v;?>-paste" <?php echo $disabled;?>>
 					<input type="button" value="線上命題(傳統)" class="edit_paper" id="btn-<?php echo $v;?>-edit" <?php echo $disabled;?>>
					<input type="button" value="線上命題(可貼圖)" class="edit_paper_new" id="btn-<?php echo $v;?>-editnew" <?php echo $disabled;?>>
 					<input type="button" value="匯出" class="download_paper" id="btn-<?php echo $v;?>-download" <?php echo $disabled;?>>
					<input type="button" value="匯入" class="upload_paper" id="btn-<?php echo $v;?>-upload" <?php echo $disabled;?>>
 					<input type="button" value="檢視" class="list_paper" id="btn-<?php echo $v;?>-list" <?php echo $disabled;?>>
 				</td>
 		  </tr>
 		  <?php
 		} 		
 		?>
 	  </table>
    </td>
  	<!--右畫面 -->
    <td valign="top">
		<span id="show_right"></span>
    </td>
  </tr>
  <tr>
  	<td>
  		<div id="setup_paper_readme" style="display:none">
  			<input type="button" id="setup_paper_submit" value="儲存設定"><br>
  		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>說明:<br>
   1.當採用「個別計時」模式時，學生皆可獲得相同的計時時間作答。<br>
   2.當採用「同時計時」模式時，學生於相同的時間結束考試。<br>
   3.若「學期補考設定」的領卷模式設定為「依下列設定時段內開放<br>所有試卷」，則此處考試時間相關設定無任何作用。<br>
      </font>
      </div>
  	</td>
  </tr> 
 </table>
 <?php
 if ($_POST['act']=='edit_paper') {
 ?>
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
 <table border="0">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<?php form_item($item);?>
 	  </span>
 	  </td>
 	</tr>
 	<tr>
  	<td>
  		<div id="edit_paper_readme" style="display:table-row">
  			<input type="button" id="edit_paper_submit" value="儲存試題">
  			<input type="button" id="edit_paper_end" value="結束命題">
  			<br>
  			<?php
  			 if ($Last_item_sn) {
  			?>
  				<table border='1' bordercolor='#FFFFFF' cellspacing='0' bordercolordark='#FFFFFF' bordercolorlight='#800000'>
   					<tr bgcolor='#FFCC66'>
    				 	<td style='font-size:10pt;color:#0000cc'><img src='.\images\filefind.png'>檢視先前試題</td>
   					</tr>
   					<tr>
     					<td><?php echo show_item($Last_item_sn);?></td>
   					</tr>
  				</table>
  			<?php
  		  } //end if ($Last_item_sn)
  			?>
  		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>編輯試題說明:<br>
      1.試題可上傳附圖，系統並未限制附圖大小，但為了版面美觀及閱讀的舒適度，請適度調整試題的附圖大小。<br>
      2.題幹的附圖，寬度盡可能不超過 400px；選目的寬度盡可能不超過200px。<br>
      3.選項若含圖片，建議先利用繪圖軟體調整四個選項的圖片大小(寬及高)相近。<br>
      4.如果該領域已有學生參加補考，試題資料庫切勿隨意更動，以免檢視試卷時無法索引試題。
      </font>
      </div>
  	</td>
  </tr> 
 </table> 	
 	<?php
  
  } // end if $_POST['act']=='edit_paper'

	 if ($_POST['act']=='edit_paper_new') {
		 ?>
		 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
		 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
		 <table border="0">
			 <tr>
				 <td>
 	  <span id="show_buttom">
 	  	<?php form_item_new($item);?>
 	  </span>
				 </td>
			 </tr>
			 <tr>
				 <td>
					 <div id="edit_paper_readme" style="display:table-row">
						 <input type="button" id="edit_paper_submit_new" value="儲存試題">
						 <input type="button" id="edit_paper_end" value="結束命題">
						 <br>
						 <?php
						 if ($Last_item_sn) {
							 ?>
							 <table border='1' bordercolor='#FFFFFF' cellspacing='0' bordercolordark='#FFFFFF' bordercolorlight='#800000'>
								 <tr bgcolor='#FFCC66'>
									 <td style='font-size:10pt;color:#0000cc'><img src='.\images\filefind.png'>檢視先前試題</td>
								 </tr>
								 <tr>
									 <td><?php echo show_item($Last_item_sn);?></td>
								 </tr>
							 </table>
							 <?php
						 } //end if ($Last_item_sn)
						 ?>
						 <font size='2' color='#0000cc'>
							 <img src='./images/filefind.png'>編輯試題說明:<br>
							 1.試題的題幹及選目皆可利用截圖軟體，用複製∕貼上方式進行貼圖，方便您處理一些特殊題型，如：數學方程式。<br>
							 2.為了版面美觀及閱讀的舒適度，請適度調整試題的附圖大小。<br>
							 3.如果該領域已有學生參加補考，試題資料庫切勿隨意更動，以免檢視試卷時無法索引試題。
						 </font>
					 </div>
				 </td>
			 </tr>
		 </table>
		 <?php

	 } // end if $_POST['act']=='edit_paper_new'

//匯入試題 
 if ($_POST['act']=='upload_paper') {
?>			
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <table border="0" cellpadding="3">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<input type="file" name="thefile" size="25">
	  </span>
 	  </td>
 	</tr>
 	<tr>
  	<td>
  		<div id="edit_paper_readme" style="display:table-row">
  			<input type="button" id="upload_paper_submit" value="上傳檔案">
  			<input type="button" id="edit_paper_end" value="離開">
  			<br>
  		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>匯入試題說明:<br>
      1.注意！您只能匯入附檔名為 .wit格式的試題檔。<br>
      2.當您希望不同學期年度能使用同一份試卷時，可利用匯出功能，即可得到 wit 格式的檔案。<br>
      </font>
      </div>
  	</td>
  </tr> 
 </table> 	
			
 
<?php  
} // end if $_POST['act']=='upload_paper'

 //快貼試題
 if ($_POST['act']=='paste_paper' or $_POST['act']=='paste_paper_submit') {

	 $paste_items=stripslashes($_POST['paste_items']);

	 $cut[0]=($_POST['cut'][0]=='')?".":$_POST['cut'][0];
	 $cut[1]=($_POST['cut'][1]=='')?"(A)":$_POST['cut'][1];
	 $cut[2]=($_POST['cut'][2]=='')?"(B)":$_POST['cut'][2];
	 $cut[3]=($_POST['cut'][3]=='')?"(C)":$_POST['cut'][3];
	 $cut[4]=($_POST['cut'][4]=='')?"(D)":$_POST['cut'][4];
	 $cut[5]=($_POST['cut'][5]=='')?"":$_POST['cut'][5];
	 $cut[6]=($_POST['cut'][6]=='')?"":$_POST['cut'][6];
	 $cut[7]=($_POST['cut'][7]=='')?"":$_POST['cut'][7];
	 $cut[8]=($_POST['cut'][8]=='')?"":$_POST['cut'][8];

	 if ($_POST['confirm_collection']==1) {

		 //去除前後空白
		 $PAST_DATA = trim($paste_items);
		//去掉跟隨別的擠在一塊的空白
		 $PAST_DATA = preg_replace('/\s(?=\s)/', '', $PAST_DATA);
		//去掉非space 的空白，用一個空格代替
		 $PAST_DATA = preg_replace('/[\r\t]/', '', $PAST_DATA);

		 //全形轉半形
		 $PAST_DATA = nf_to_wf($PAST_DATA,0);

		 $buffer = explode("\n",$PAST_DATA);  //以換行符號, 把資料切割
		 $i=0;
		 $New_DATA="";
		 //題號前有 ()1. 自動排版
		 if ($_POST['collection_type']==2) {
			 foreach ($buffer as $v) {
				 //先用 . 切割，　然後檢查 ) 之後, 若為數字, 表示為新的一題
				 $h1=explode(".",$v);
				 if (count($h1)==2) {  //有切到
					 $h2=explode(")",$h1[0]);
					 if (count($h2)==2) {   //有切到, 再檢查是否為數字
						 if ($h2[1]>0) {
							 if ($i>0) $New_DATA.="\n";
							 $i++;
							 $New_DATA.=$v;
						 } else {
							 $New_DATA.=$v;
						 }
					 } else {
						 $New_DATA.=$v;
					 }
				 } else {              //沒切到
					 $New_DATA.=$v;
				 }

			 } // end foreach
		 }

		 //題號為數字開頭, 小數點隔開
		 if ($_POST['collection_type']==1) {

			 foreach ($buffer as $v) {
				 $head=explode(".",$v);
				 //print_r($head);
				 //以每一題為 nn.() 作判斷
				 if ($head[0]>0) {
					 if ($i>0) $New_DATA.="\n";
					 $i++;
					 $New_DATA.=$v;
				 } else {
					 $New_DATA.=$v;
				 }
			 }
		 }
		 //僅進行全形半形轉換
		 if ($_POST['collection_type']==3) {
			 $New_DATA=$PAST_DATA;
		 }

		 //協助去開開頭 ()
		 if ($_POST['collection_type']==4) {

			 foreach ($buffer as $v) {

				 if (substr($v,0,3)=="( )") {
					 $New_DATA.=substr($v,3)."\n";
				 } elseif (substr($v,0,2)=="()") {
					 $New_DATA.=substr($v,2)."\n";
				 } else {
					 $New_DATA.=$v."\n";
				 }
			 }
		 }

		 //回存到表單變數
		 $paste_items=$New_DATA;


	 }


	 ?>
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
 <input type="hidden" name="confirm_collection" value=0>
 <table border="0" cellpadding="3" width="100%">
 	<tr>
 	  <td>
 	  <div id="show_buttom">
 	  	<div>
			<span style="color:red">◎快貼多題文字試題</span>
			<div>
				<textarea name="paste_items" style="width:100%;height: 150px" ><?php echo $paste_items;?></textarea>
			</div>
			<div>
              <span style="color:#0055aa">
                <input type="button" id="paste_paper_composing" value="自動排版">
                  <input type="radio" name="collection_type" value="3" checked>僅把全形轉換成半形 &nbsp;
                  <input type="radio" name="collection_type" value="1">依「數字+小數點」排版&nbsp;
                  <input type="radio" name="collection_type" value="2">依「括號+數字+小數點」排版&nbsp;
                  <input type="radio" name="collection_type" value="4">去除每行開頭的「( )」
                　
              </span>
              <span style="float: right">
                  <input type="button" class="btn btn-xs btn-default" value="清除貼文" onclick="document.myform.past_items.value=''">
              </span>

			</div>
		</div>
		<div>
			<div style="margin-top: 10px">
				<span style="color:blue">請確認斷行分析文字符號：</span>
			</div>
			<div>
				第1斷行符號：<input type='text' name='cut[]' value='<?= $cut[0];?>' size='10'><br>
				第2斷行符號：<input type='text' name='cut[]' value='<?= $cut[1];?>' size='10'><br>
				第3斷行符號：<input type='text' name='cut[]' value='<?= $cut[2];?>' size='10'><br>
				第4斷行符號：<input type='text' name='cut[]' value='<?= $cut[3];?>' size='10'><br>
				第5斷行符號：<input type='text' name='cut[]' value='<?= $cut[4];?>' size='10'><br>
				第6斷行符號：<input type='text' name='cut[]' value='<?= $cut[5];?>' size='10'><br>
				第7斷行符號：<input type='text' name='cut[]' value='<?= $cut[6];?>' size='10'><br>
				第8斷行符號：<input type='text' name='cut[]' value='<?= $cut[7];?>' size='10'><br>
				第9斷行符號：<input type='text' name='cut[]' value='<?= $cut[8];?>' size='10'>
			</div>
		</div>
	  </div>
 	  </td>
 	</tr>
 	<tr>
  	<td>
  		<div id="edit_paper_readme" style="display:table-row">
  			<input type="button" id="paste_paper_submit" value="分析試題">
  			<input type="button" id="edit_paper_end" value="離開">
  			<br>
  		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>快貼試題說明:<br>
      1.採用快貼方式，可同時建立多題文字型試題，使用分析試題前，請先利用「自動排版」功能整理試題。<br>
      2.關於附圖部分，必須儲存完畢後再採修改試題方式逐題複製/貼上。<br>
      3.貼上的文字必須為一題一行的格式。<br>
      4.快貼完畢，可利用【檢視】的功能，進行解答的調整或設定每一題的分科。     
      </font>
      </div>
  	</td>
  </tr> 
 </table> 	
 	<?php
  
  } // end if $_POST['act']=='edit_paper'


//匯入試題進行文字分析, 確認沒問題, 可按儲存
if ($_POST['act']=='paste_paper_submit') {	
	
	$items=stripslashes($_POST['paste_items']);

	$buffer = explode("\n",$items);  //以換行符號, 把資料切割

	//開始
	$i=0;
	$j=0;
	foreach ($_POST['cut'] as $C) {
		if ($C!="") {
			$j++;
		} else {
			break;   //遇到空白, 表示以下不再分析
		}
	}
	$j_max=$j+1;

	foreach ($buffer as $P )  {
		$i++;
		//以斷行符號作為資料切格依據, 最多15個
		$j=0;
		//$j_max=0;
		$pj=0;
		$P=trim($P); //去除前後空白
		foreach ($_POST['cut'] as $C) {
			$j++;
			if ($j==$j_max) {
				if ($pj==0) $P_item[$i][$j]=$P; //剩餘文字
				break;
			}
			if ($C!="") {

				$NewP=explode($C,$P,2);    //以條件 $C 切兩段
				$P_item[$i][$j]=trim($NewP[0]);
				//$P=trim($NewP[1]);
				//如果這個條件沒切到,
				//if ($P=='') {
				if (count($NewP)==1) {
					if ($pj==0) {
						if ($j<$j_max) {
							$pj=$j;    //記下目前切的欄位位置
							$P=$P_item[$i][$j];     //因為沒切到, 以目前的資料繼續往下切
						}
					} else {
						//前面已經有發生沒切到的情形, 目前這個欄位又沒切到.
						if ($j<$j_max) {
							$P = $P_item[$i][$j];
							$P_item[$i][$j] = '';
						} else {  //已經到最後欄位了
							$P_item[$i][$j] = '';   //把這個欄位也清除, 本資料應歸於前面沒切到的那個欄位
						}
					}
				} else {
					$P=trim($NewP[1]);
					//有切到
					if ($pj>0) {
						$P_item[$i][$j]='';
						$P_item[$i][$pj]=trim($NewP[0]);   //第一段資料歸到前一個欄位
					}
					$pj=0;
				}
			}
		} // end foreach

	} // end foreach


	//開始組合成 from
	$content="";
	for ($I=1;$I<=$i;$I++) {
	 //欄位
	 $content_tr=$content_td="";
	 for ($J=1;$J<=$j_max;$J++) {
	  $content_td.="<td><input type='text' size='12' name='field[$I][$J]' value='".$P_item[$I][$J]."'></td>";
	 }
	 //列
	 $content_tr="
	  <tr class='paste_table'>
	   <td align='center'><input type='checkbox' name='save_it[$I]' value='1' checked></td>
	   $content_td
	  </tr>
	 ";
	 $content.=$content_tr;
	}
	
	//標題欄
	 for ($J=1;$J<=$j_max;$J++) {
	  $content_title.="
	  <td>
  		<select size='1' name='to_field[$J]'>
    		<option value='none'>不儲存</option>
    		<option value='question'>題幹</option>
    		<option value='cha'>選目A</option>
    		<option value='chb'>選目B</option>
    		<option value='chc'>選目C</option>
    		<option value='chd'>選目D</option>
    		<option value='answer'>解答</option>
  		</select>	  
	  </td>";
	 }
	$content_title="<tr bgcolor='#FFCC66'><td>儲存</td>$content_title</tr>";
	$main="
	  <table border='0'>
	  $content_title
	  $content
	  </table>
	";
	//開始呈現

  echo $main;
  ?>
  <input type="button" id="paste_paper_save" value="儲存試題">
	<input type="button" id="edit_paper_end" value="離開">
	<br>
 		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>操作說明:<br>
      1.請選定每一欄位要對應的試題項目。<br>
      2.如果該欄位資料要捨棄，請選擇「不儲存」。<br>
      3.注意，對應的欄位資料請勿重覆，以免資料庫出錯!      
      </font>

  <?php
}



 //檢視試題 
 if ($_POST['act']=='list_paper') {
 ?>
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
 <table border="0">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<input type="button" id="list_paper_end" value="結束檢視">
 	  	<input type="button" id="list_paper_answer" value="調整解答">
 	  	<input type="button" id="list_paper_subject" value="設定試題分科">
 	  	<table border='0'>
 	  	
		<?php
		$i=0;
 			$sql="select a.sn from resit_exam_items a, resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='".$SETUP['now_year_seme']."' and b.class_year='$Cyear' and b.scope='$item_scope'";
 			$res=$CONN->Execute($sql);
 			$row=$res->GetRows();
 			foreach ($row as $K) {
 			  $sn=$K['sn'];
 			  $i++;
				?>
				<tr><td><hr></td></tr>
				<tr>
					<td><?php echo show_item($sn,0,'',$i);?></td>
				</tr>
				<?php 			  
 			}
		?>
		</table>
 	  </span>
 	  </td>
 	</tr>
 </table> 	
 	<?php
  
  } // end if $_POST['act']=='list_paper'
 
 //檢視試題 - 調整解答
 if ($_POST['act']=='list_paper_answer') {
 ?>
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
 <table border="0">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<input type="button" id="list_paper_end" value="結束檢視">
 	  	<input type="button" style="color:#FF0000" id="list_paper_answer_save" value="儲存解答">
 	  	<table border='0'> 	  	
		<?php
		$i=0;
 			$sql="select a.sn from resit_exam_items a, resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='".$SETUP['now_year_seme']."' and b.class_year='$Cyear' and b.scope='$item_scope'";
 			$res=$CONN->Execute($sql);
 			$row=$res->GetRows();
 			foreach ($row as $K) {
 				$i++;
 			  $sn=$K['sn'];
				?>
				<tr><td><hr></td></tr>
				<tr>
					<td><?php echo show_item($sn,1,'',$i);?></td>
				</tr>
				<?php 			  
 			}
		?>
		</table>
 	  </span>
 	  </td>
 	</tr>
 </table> 	
 	<?php  
  } // end if $_POST['act']=='list_paper_answer' 

 //檢視試題 - 設定試題分科
 if ($_POST['act']=='list_paper_subject') {
 ?>
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
 <table border="0">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<input type="button" id="list_paper_end" value="結束檢視">
 	  	<input type="button" style="color:#FF0000" id="list_paper_subject_save" value="儲存分科設定">
 	  	<table border='0'> 	  	
		<?php
		$i=0;
 			$sql="select a.sn from resit_exam_items a, resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='".$SETUP['now_year_seme']."' and b.class_year='$Cyear' and b.scope='$item_scope'";
 			$res=$CONN->Execute($sql) or die($sql);
 			$row=$res->GetRows();
 			foreach ($row as $K) {
 				$i++;
 			  $sn=$K['sn'];
				?>
				<tr><td><hr></td></tr>
				<tr>
					<td><?php echo show_item($sn,3,'',$i);?></td>
				</tr>
				<?php 			  
 			}
		?>
		</table>
 	  </span>
 	  </td>
 	</tr>
 </table> 	
 	<?php  
  } // end if $_POST['act']=='list_paper_subject' 



 
 } //end if $Cyear 
?>
</form>
<?php
//  --程式檔尾
foot();


function nf_to_wf($strs, $types){  //全形半形轉換
	$nft = array(
			"(", ")", "[", "]", "{", "}", ".",
		//",", ";", ":",
			"-",  "!", "@", "#", "$", "%", "&", "|", "\\",
			"/", "+", "=", "*", "~",
		//"`", "'", "\"","?",
			"<", ">",
			"^", "_",
			"0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
			"a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
			"k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
			"u", "v", "w", "x", "y", "z",
			"A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
			"K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
			"U", "V", "W", "X", "Y", "Z",
			" "
	);
	$wft = array(
			"（", "）", "〔", "〕", "｛", "｝", "﹒",
		//"，", "；", "：",
			"－",  "！", "＠", "＃", "＄", "％", "＆", "｜", "﹨",
			"∕", "＋", "＝", "＊", "?",
		//"、", "、", "?","？",
			"＜", "＞",
			"︿", "＿",
			"０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
			"ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
			"ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
			"ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
			"Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
			"Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
			"Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
			"　"
	);

	if ($types == '1'){
		// 轉全形
		$strtmp = str_replace($nft, $wft, $strs);
	}else{
		// 轉半形
		$strtmp = str_replace($wft, $nft, $strs);
	}
	return $strtmp;
}


?>

<Script> 
 <?php
 foreach ($ss_link as $v) {
  $JavaArray.="\"".$v."\",";
 }
 $JavaArray=substr($JavaArray,0,strlen($JavaArray)-1);
 ?>
 //定義所有領域
 var AllScope=[<?php echo $JavaArray;?>]; 

//滑鼠移出移入
$(".scope_table").hover(function(){
	 $(this).css("background-color","#FFFFAA");
	},function(){
	 $(this).css("background-color","#FFFFFF");	
})

//滑鼠移出移入
$(".paste_table").hover(function(){
	 $(this).css("background-color","#AAFFAA");
	},function(){
	 $(this).css("background-color","#FFFFFF");	
})

//滑鼠移出移入
$(".items_table").hover(function(){
	 $(this).css("background-color","#AAAAFF");
	},function(){
	 $(this).css("background-color","#FFFFFF");	
})

//設定試卷
$(".setup_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var scope=NewArray[1];
	var act='setup_paper';
	var Cyear='<?php echo $Cyear;?>';
	   
  $.ajax({
   	type: "post",
    url: 'resit_assign.php',
    data: { act:act,scope:scope,Cyear:Cyear },
    dataType: "text",
    error: function(xhr) {
      alert('ajax request 發生錯誤!');
    },
    success: function(response) {
    	$('#show_right').html(response);
      $('#show_right').fadeIn(); 
      setup_paper_readme.style.display='block';
      setup_paper_readme.style.display='block';
      //for (index = 0; index < AllScope.length; ++index) {
      //  var ss=AllScope[index];        
      //	document.getElementById(ss).style.display = 'block';         
			//}
			
    } // end success
	});   // end $.ajax
})

//儲存試卷設定
$("#setup_paper_submit").click(function(){
	var paper_mode=<?php echo $SETUP['paper_mode'];?>;
	var act='setup_paper_submit';
	var scope=document.myform.scope.value;
	var start_time=document.myform.start_time.value;
	var end_time=document.myform.end_time.value;
	var Cyear='<?php echo $Cyear;?>';
	var timer=document.myform.timer.value;
	var items=document.myform.items.value;
	var top_marks=document.myform.top_marks.value;
	//是否重算分數
	if (document.myform.reset_score.checked) {
	  var reset_score=1;
	} else {
		var reset_score=0;
	}
	
	//取得 timer_mode , 由於是利用 ajax 動態產生的畫面，這邊無法使用 jQuery 取值
	for (var i=0; i<myform.timer_mode.length; i++) {
   if (myform.timer_mode[i].checked)
   {
      var timer_mode = myform.timer_mode[i].value;
   }
  }
  //取得 relay_answer
	for (var i=0; i<myform.relay_answer.length; i++) {
   if (myform.relay_answer[i].checked)
   {
      var relay_answer = myform.relay_answer[i].value;
   }
  }
  
  //取得 double_papers
	for (var i=0; i<myform.double_papers.length; i++) {
   if (myform.double_papers[i].checked)
   {
      var double_papers = myform.double_papers[i].value;
   }
  }	
  //取得 item_mode
	for (var i=0; i<myform.item_mode.length; i++) {
   if (myform.item_mode[i].checked)
   {
      var item_mode = myform.item_mode[i].value;
   }
  }	  
  //取得分科題數設定
  var i =0;
  var strSubject='';
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,8)=='subject_') {
      var kk=document.myform.elements[i].name;
      var vv=document.myform.elements[i].value;
      var SubjectArray=kk.split("_");
      strSubject=strSubject+SubjectArray[1]+','+vv+';';
    }
    i++;
  }
  
  var s=strSubject.length-1;
  strSubject=strSubject.substr(0,s);

   	//考試時間比較
   if (paper_mode==0) {
   	starttime=start_time.replace(/-/g, "/"); 
   	starttime=(Date.parse(starttime)).valueOf() ; // 直接轉換成Date型別所代表的值
   	endtime=end_time.replace(/-/g, "/"); 
   	endtime=(Date.parse(endtime)).valueOf() ; // 直接轉換成Date型別所代表的值
    if (starttime>=endtime) {
     alert ("領卷結束時間不得早於或等於開始時間!");
     return false;
    }	
    
    //同時計時，考試結束時間為開始加計時，領卷結束時間不能大於或等於考試結束時間
    //個別計時無此限制
    if (timer_mode=='1') {
    	var test_end_time=starttime+timer*60*1000;
    	if (test_end_time<=endtime) {
    	  alert ("領卷結束時間超過或等於考試結束時間(開始時間+計時時間，不合理!!");
    	  return false;
    	}
    }
   } else {
   	 alert("注意！\n\n由於學期補考中的設定選擇「依設定時段內開放所有試卷」\n因此此處不檢查考試開始時間及領卷結束時間的合理性\n此外，考試中一律採「個別計時」。\n\n每位學生的考試計時時間為"+timer+"分鐘");
   } // end if paper_mode==0

	$.ajax({
   	type: "post",
    url: 'resit_assign.php',
    data: { act:act,scope:scope,Cyear:Cyear,start_time:start_time,end_time:end_time,timer:timer,items:items,timer_mode:timer_mode,relay_answer:relay_answer,double_papers:double_papers,item_mode:item_mode,subject:strSubject,top_marks:top_marks,reset_score:reset_score },
    //data : postData,
    dataType: "text",
    error: function(xhr) {
      alert('ajax request 發生錯誤!');
    },
    success: function(response) {
    	$('#show_right').html(response);
      $('#show_right').fadeIn(); 
      setup_paper_readme.style.display='none';
      for (index = 0; index < AllScope.length; ++index) {
        var ss=AllScope[index];        
        //document.getElementById(ss).style.display = 'block';
        //把按鈕的 disabled 取消
        if (ss==scope) {
          var btnID="btn-"+ss+"-paste";
          document.getElementById(btnID).disabled = false;         
          var btnID="btn-"+ss+"-edit";
          document.getElementById(btnID).disabled = false;         
          var btnID="btn-"+ss+"-list";
          document.getElementById(btnID).disabled = false;         
          var btnID="btn-"+ss+"-upload";
          document.getElementById(btnID).disabled = false;         
        }         
			}
    }
	});   // end $.ajax 
 
})


//線上命題
$(".edit_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var scope=NewArray[1];
	  
  document.myform.act.value='edit_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

});

 //線上命題
 $(".edit_paper_new").click(function(){
	 var btnID=$(this).attr("id");
	 var NewArray = new Array();
	 var NewArray = btnID.split("-");
	 var scope=NewArray[1];

	 document.myform.act.value='edit_paper_new';
	 document.myform.opt1.value=scope;

	 document.myform.submit();

 })



//匯出試題
$(".download_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var scope=NewArray[1];
	  
  document.myform.act.value='download_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

})

//匯入試題
$(".upload_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var scope=NewArray[1];
	  
  document.myform.act.value='upload_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

})

//匯入試題 - submit
$("#upload_paper_submit").click(function(){
	 
	if (document.myform.thefile.value=='') {
	  alert('您並未選擇檔案!');
	  return false;
	}
	 
	document.myform.opt1.value=document.myform.item_scope.value;
  document.myform.act.value='upload_paper_submit';
  document.myform.submit();

})

//儲存試題 , 打開本年級領域列表
$("#edit_paper_submit").click(function(){
	//設定 opt1 為某領域, 以便列表僅顯示該領域
	document.myform.opt1.value=document.myform.item_scope.value;
  document.myform.act.value='edit_paper_submit';

  chk_submit=check_form_item();

  if (chk_submit) {
	 document.myform.submit();
	}

});

 //儲存試題 , 打開本年級領域列表
 $("#edit_paper_submit_new").click(function(){
	 //設定 opt1 為某領域, 以便列表僅顯示該領域
	 document.myform.opt1.value=document.myform.item_scope.value;
	 document.myform.act.value='edit_paper_submit_new';

	 chk_submit=check_form_item_new();

	 if (chk_submit) {
		 document.myform.submit();
	 }

 })

//修改試題
$(".edit_paper_update").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var item_sn=NewArray[1];

	//設定 opt1 為某領域, 以便列表僅顯示該領域
	document.myform.opt1.value=document.myform.item_scope.value;
	document.myform.opt2.value='<?php echo $_POST['act'];?>';
  document.myform.act.value='edit_paper_update';
  document.myform.item_sn.value=item_sn;
	document.myform.submit();
})

//刪除試題
$(".edit_paper_delete").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var item_sn=NewArray[1];
  
  confirm_delete=confirm("您確定要刪除試題？\n流水號："+item_sn);
  
  if (confirm_delete) {
		//設定 opt1 為某領域, 以便列表僅顯示該領域
		document.myform.opt1.value=document.myform.item_scope.value;
		document.myform.opt2.value='<?php echo $_POST['act'];?>';
  	document.myform.act.value='edit_paper_delete';
  	document.myform.item_sn.value=item_sn;
		document.myform.submit();
  }
})

//結束命題 , 打開本年級領域列表
$("#edit_paper_end").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var scope=NewArray[1];
	 
	for (index = 0; index < AllScope.length; ++index) {
    var ss=AllScope[index];        
  	document.getElementById(ss).style.display = 'table-row';         
  }
  //清除命題區html , 避免誤送
	$('#show_buttom').html("");
	edit_paper_readme.style.display='none'; 	

})

//結束檢視 , 打開本年級領域列表
$("#list_paper_end").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var scope=NewArray[1];
	 
	for (index = 0; index < AllScope.length; ++index) {
    var ss=AllScope[index];        
  	document.getElementById(ss).style.display = 'table-row';         
  }
  //清除命題區html , 避免誤送
	$('#show_buttom').html("");

})

//檢視試題
$(".list_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var scope=NewArray[1];
	  
  document.myform.act.value='list_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

})

//檢視試題 - 調整解答
$("#list_paper_answer").click(function(){
  
  document.myform.act.value='list_paper_answer';
  document.myform.opt1.value=document.myform.item_scope.value;
  
  document.myform.submit();

})

//檢視試題 - 調整解答儲存
$("#list_paper_answer_save").click(function(){
  
  document.myform.act.value='list_paper_answer_save';
  document.myform.opt1.value=document.myform.item_scope.value;
  
  document.myform.submit();

})

//檢視試題 - 設定試題分科
$("#list_paper_subject").click(function(){
  
  document.myform.act.value='list_paper_subject';
  document.myform.opt1.value=document.myform.item_scope.value;
  
  document.myform.submit();

});

//檢視試題 - 設定試題分科儲存
$("#list_paper_subject_save").click(function(){
  
  document.myform.act.value='list_paper_subject_save';
  document.myform.opt1.value=document.myform.item_scope.value;
  
  document.myform.submit();

});

//匯入試題
$(".paste_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
　var NewArray = btnID.split("-");
  var scope=NewArray[1];
		
  document.myform.act.value='paste_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

});

//分析試題
$("#paste_paper_submit").click(function(){
	//設定 opt1 為某領域, 以便列表僅顯示該領域
	document.myform.opt1.value=document.myform.item_scope.value;
  document.myform.act.value='paste_paper_submit';

  if (document.myform.paste_items.value=='') {
   alert('未貼入任何文字!');
   return false;
  }  
	document.myform.submit();
});

 //分析試題
 $("#paste_paper_composing").click(function(){
	 //設定 opt1 為某領域, 以便列表僅顯示該領域
	 document.myform.opt1.value=document.myform.item_scope.value;
	 document.myform.act.value='paste_paper';
	 document.myform.confirm_collection.value=1;

	 if (document.myform.paste_items.value=='') {
		 alert('未貼入任何文字!');
		 return false;
	 }
	 document.myform.submit();
 });

//儲存試題
$("#paste_paper_save").click(function(){
	//設定 opt1 為某領域, 以便列表僅顯示該領域
	document.myform.opt1.value=document.myform.item_scope.value;
  document.myform.act.value='paste_paper_save';
	document.myform.submit();
});

//檢驗試題表單
function check_form_item() {

 if (document.myform.question.value=='') {
   alert('題幹未輸入!');
   return false;
 }
 if (document.myform.cha.value=='' && document.myform.thefig_a.value=='' && ($("#del_fig_a").length == 0 || $("#del_fig_a").attr('checked'))) {
   alert('選目(A)未輸入!');
   return false; 
 }

 if (document.myform.chb.value=='' && document.myform.thefig_b.value=='' && ($("#del_fig_b").length == 0 || $("#del_fig_b").attr('checked'))) {
   alert('選目(B)未輸入!');
   return false; 
 }

 if (document.myform.chc.value=='' && document.myform.thefig_c.value=='' && ($("#del_fig_c").length == 0 || $("#del_fig_c").attr('checked'))) {
   alert('選目(C)未輸入!');
   return false; 
 }

 if (document.myform.chd.value=='' && document.myform.thefig_d.value=='' && ($("#del_fig_d").length == 0 || $("#del_fig_d").attr('checked'))) {
   alert('選目(D)未輸入!');
   return false; 
 }
 //檢查解答有沒有點選
 var method =$("input[name='answer']:checked").val(); //radio 取值，注意寫法
 if( typeof(method) == "undefined"){ // 注意檢查完全沒有選取的寫法，這行是精華
   alert( "請選取解答！");
  return false;
 }

 return true;
 
}

 //檢驗試題表單
 function check_form_item_new() {

	 //要用 CKEDITOR 的物件直接調用, 不然會有第一次取不到, 要取第二次才取得到的問題
	 var S = CKEDITOR.instances.question;
	 var question=S.getData();

	 if (question=='') {
		 alert('題幹未輸入!');
		 $("#question").focus();
		 return false;
	 }

	 var S = CKEDITOR.instances.cha;
	 var cha=S.getData();
	 if (cha=='') {
		 alert('選目(A)未輸入!');
		 $("#cha").focus();
		 return false;
	 }

	 var S = CKEDITOR.instances.chb;
	 var chb=S.getData();
	 if (chb=='') {
		 alert('選目(B)未輸入!');
		 $("#chb").focus();
		 return false;
	 }

	 var S = CKEDITOR.instances.chc;
	 var chc=S.getData();
	 if (chc=='') {
		 alert('選目(C)未輸入!');
		 $("#chc").focus();
		 return false;
	 }

	 var S = CKEDITOR.instances.chd;
	 var chd=S.getData();
	 if (chd=='') {
		 alert('選目(D)未輸入!');
		 $("#chd").focus();
		 return false;
	 }
	 //檢查解答有沒有點選
	 var method =$("input[name='answer']:checked").val(); //radio 取值，注意寫法
	 if( typeof(method) == "undefined"){ // 注意檢查完全沒有選取的寫法，這行是精華
		 alert( "請選取解答！");
		 return false;
	 }

	 return true;

 }

</Script>