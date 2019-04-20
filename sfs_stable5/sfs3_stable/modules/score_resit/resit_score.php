<?php	
header('Content-type: text/html;charset=big5');
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
require_once "../../include/sfs_case_excel.php";

//驗證是否登入
sfs_check(); 

$s=get_school_base();
$school_name=$s['sch_cname']; //學校名稱

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
 		if($Cyear>2){
			$ss_link=array("語文"=>"language","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
			$link_ss=array("language"=>"語文","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
		} else {
			$ss_link=array("語文"=>"language","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
			$link_ss=array("language"=>"語文","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
		}

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

// ajax 檢視已補考名單
if ($_POST['act']=='html_resit_list') {
	$S['go']='補考中';
	$S['ready']='未補考';
	$S['tested']='補考完';
 	//領域別
 	// $Cyesr : 年級
	$scope=$_POST['scope'];
	$opt1=$_POST['opt1'];
	$seme_year_seme=$SETUP['now_year_seme'];
  //抓取班級設定裡的班級名稱
	$class_base= class_base($curr_year_seme);
	
	//讀取已補考名單
	switch ($opt1) {
	  case 'ready':
			$sql="select a.*,c.stud_id,c.stud_name,c.curr_class_num,c.email_pass from resit_exam_score a,resit_paper_setup b,stud_base c where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.student_sn=c.student_sn and entrance='0' and complete='0' order by curr_class_num";
	  break;
	  case 'go':
			$sql="select a.*,c.stud_id,c.stud_name,c.curr_class_num,c.email_pass from resit_exam_score a,resit_paper_setup b,stud_base c where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.student_sn=c.student_sn and entrance='1' and complete='0' order by curr_class_num";	  
	  break;
	  case 'tested':
			$sql="select a.*,c.stud_id,c.stud_name,c.curr_class_num,c.email_pass from resit_exam_score a,resit_paper_setup b,stud_base c where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.student_sn=c.student_sn and complete='1' order by curr_class_num";
	  break;

	}
	$res=$CONN->Execute($sql) or die($sql);
	while ($row=$res->FetchRow()) {
		$student_sn=$row['student_sn'];
		$curr_class_num=$row['curr_class_num'];
		$seme_class=substr($curr_class_num,0,3);
		$seme_num=substr($curr_class_num,-2);
		
		$main.="
			<tr>
	     <td style='font-size:10pt' align='center'>".$class_base[$seme_class]."</td>
	     <td style='font-size:10pt' align='center'>".$seme_num."</td>
	     <td style='font-size:10pt' align='center'>".$row['stud_name']."</td>
	     <td style='font-size:10pt' align='center'>".$row['org_score']."</td>
	     ";
	     
	   if ($opt1=="ready") {
			$main.="
			<td style='font-size:9pt'>".$row['subjects']."</td>	   
			<td style='font-size:9pt'>".$row['stud_id']."</td>	
			<td style='font-size:9pt'>".$row['email_pass'];	  
	   } elseif ($opt1=="go") {
	  	$main.="   
			 <td style='font-size:9pt'>".$row['subjects']."</td>	   
	     <td style='font-size:9pt'>".$row['entrance_time'];		
	   } else {
	  	$main.="   
	     <td style='font-size:9pt'>".$row['entrance_time']."</td>		
	  	 <td style='font-size:9pt'>".$row['complete_time']."</td>
	     <td style='font-size:10pt".(($row['score']<60)?";color:red":"")."' align='center'>".$row['score'];
		 }

		if ($row['complete']==1) {
		 $main.=" <a href='resit_list_paper.php?seme_year_seme=$seme_year_seme&Cyear=$Cyear&scope=$scope&sn=".$row['sn']."' target='_blank' title='瀏覽《".$row['stud_name']."》的作答'><img src='images/filefind.png'></a></td>
			</tr>";
		} else {
		 $main.="</td>
			</tr>";
		}
		

	}
	  $title="	  
	 <table border=\"0\" width=\"100%\" cellspacing=\"3\" cellpadding=\"2\">
  	<tr>
   	  <td colspan='5' style='color:#800000'><b>".$link_ss[$scope]."領域</b> - [<font color=blue>".$S[$opt1]."</font>]名單</td>
   	</tr>
	   <tr bgcolor=\"#FFCCCC\">
	     <td style='font-size:10pt'>班級</td>
	     <td style='font-size:10pt'>座號</td>
	     <td style='font-size:10pt'>姓名</td>
	     <td style='font-size:10pt'>原成績</td>";
	  if ($opt1=="ready") {
	  	$title.="
	  	<td style='font-size:10pt'>不及格分科</td>
	  	<td style='font-size:10pt'>學號</td>
	  	<td style='font-size:10pt'>登入密碼</td>
	  	";
	  } elseif ($opt1=="go") {
	  	$title.="	 
	  	 <td style='font-size:10pt'>不及格分科</td>    
	     <td style='font-size:10pt'>領卷時間</td>
	     ";
	  }else {
	  	$title.="	     
	  	 <td style='font-size:10pt'>領卷時間</td>
	     <td style='font-size:10pt'>完成時間</td>
	     <td style='font-size:10pt'>補考成績</td>
			";  
	  }	
	     
     $title.="</tr>";
	   
	 $main=$title.$main."</table>"; 
	  
 
  echo $main;
  exit();

}

//匯出不及格名單
if ($_POST['act']=='output_resit_name') {
  
	//領域別
	$scope=$_POST['opt1'];
	
  $seme_year_seme=$SETUP['now_year_seme'];
  $year=substr($seme_year_seme,0,3);
  $semester=substr($seme_year_seme,3,1);
 //抓取班級設定裡的班級名稱
	$class_base= class_base($curr_year_seme);
	$stud_sn=array();
	
	//抓取本學期所有課程設定(領域－分科)
	$scope_subject=get_year_seme_scope($year,$semester,$Cyear);

  //以本年度學生資料去抓 student_sn , 以免抓不到後來才轉入的學生 student_sn	
	$Now_Cyear=$Cyear+$now_cy;
	$query="select a.student_sn,a.stud_id,a.stud_name,a.curr_class_num,a.stud_addr_2,a.stud_tel_2,a.stud_tel_3,a.addr_zip,c.guardian_name from stud_base a,stud_seme b,stud_domicile c where a.student_sn=b.student_sn and b.student_sn=c.student_sn and b.seme_year_seme='$curr_year_seme' and a.curr_class_num like '".$Now_Cyear."%' and stud_study_cond in ('0','15') order by curr_class_num";
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
		
		$student_data[$student_sn]['stud_name']=$res->fields['stud_name'];
		$student_data[$student_sn]['stud_id']=$res->fields['stud_id'];
		$student_data[$student_sn]['stud_addr_2']=$res->fields['stud_addr_2'];
		$student_data[$student_sn]['stud_tel_2']=$res->fields['stud_tel_2'];
		$student_data[$student_sn]['stud_tel_3']=$res->fields['stud_tel_3'];
		$student_data[$student_sn]['addr_zip']=$res->fields['addr_zip'];
		$student_data[$student_sn]['guardian_name']=$res->fields['guardian_name'];
		
		$student_data[$student_sn]['class_name']=$class_base[$seme_class];

		$res->MoveNext();
	} // end while
	
	$semes[]=$seme_year_seme;  //目前學期
	//抓取領域成績
	$sel_year=substr($seme_year_seme,0,3);
	$sel_seme=substr($seme_year_seme,-1);

	$fin_score=cal_fin_score($stud_sn,$semes,"",$strs,1);

 //全部領域
 if ($scope=="ALL") {
  
  //匯出
  if ($_POST['opt2']=='') {
   $x=new sfs_xls();
	 $x->setUTF8();
	 $x->filename=substr($seme_year_seme,0,3)."學年度第".substr($seme_year_seme,-1).'學期應補考學生名單.xls';
	 $x->setBorderStyle(1);
	 $x->addSheet("應補考名單");
	 $x->items[0]=array('學號','目前班級','目前座號','姓名','語文','數學','自然','社會','健體','藝文','綜合','應補考領域','應補考領域數','已補考領域','家長姓名','市內電話','行動電話','郵遞區號','通訊地址');
  }
  
	foreach ($stud_sn as $student_sn) {
    
    
    //取得學生當學期的班級座號 , 做出 $class_id (2016.01.06 因應班級課程)
    $sql_class_num="select seme_class from stud_seme where student_sn='".$student_sn."' and seme_year_seme='".$seme_year_seme."'";
 	  $res_class_num=$CONN->Execute($sql_class_num);
 	  if ($res_class_num->RecordCount()==1) {
 	    $seme_class=$res_class_num->fields['seme_class'];
     	$class_year=substr($seme_class,0,1);
     	$class_num=substr($seme_class,1,2);			  
	    $class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$class_year,$class_num);
 	  } else {
 	    $seme_class="";
 	    $class_id="ALL";
 	  }
 	  
 	  
 	  //讀取學生所有分科成績
 	  $ss_score=array();
  	  $sql_ss_score="select ss_id,ss_score from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn='$student_sn'";
			$res_ss_score=$CONN->Execute($sql_ss_score) or die($sql_ss_score);
			
			while ($row_ss_score=$res_ss_score->fetchRow()) {
			  $ss_id=$row_ss_score['ss_id'];
			  $ss_score[$ss_id]=$row_ss_score['ss_score'];
			}
	   
    //檢查是否有任一科不及格
    $language=$math=$nature=$social=$health=$art=$complex="";
    $resit_scope=$resit_tested="";
	  $put_it=0;
	  $resit_number=0;
	  $memo=array();
	  foreach ($ss_link as $v=>$S) {
	  	${$S}=$fin_score[$student_sn][$S][$seme_year_seme]['score'];
	  	//某領域不及格, 檢查分科細目
	   if ($fin_score[$student_sn][$S][$seme_year_seme]['score']<60) {
	     $put_it=1;
	     $resit_number++;
	     $resit_scope.="【".$v;
	     
	     //讀取分科
	     //此生是否為班級課程 , 若無則用 ALL 課程
	     $target_id=(count($scope_subject[$class_id][$S])>0)?$class_id:"ALL"; 
	     $resit_subject="";
	     if (count($scope_subject[$target_id][$S])>1) {
	      foreach ($scope_subject[$target_id][$S] as $V) {
    
	     	  $now_subject_ss_id=$V['ss_id'];
					if ($ss_score[$now_subject_ss_id]<60) {
					  $resit_subject.=$V['subject'].",";  //分科中文名
						/* 2015.03.22 因為欄位編排問題，把分科任課老師做到單一領域名單輸入
					  //讀取老師任課老師
		        if ($seme_class) {
		        	$class_year=substr($seme_class,0,1);
		        	$class_num=substr($seme_class,1,2);			  
					    $class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$class_year,$class_num);
					    $sql_subject_teacher="select teacher_sn from score_course where class_id='$class_id' and ss_id='$now_subject_ss_id'";
							$res_subject_teacher=$CONN->Execute($sql_subject_teacher);
							$teacher_sn=$res_subject_teacher->fields['teacher_sn'];
							$subject_teacher=get_teacher_name($teacher_sn);
					  } else {
					    $subject_teacher="轉入";   //轉學生，無科任老師
					  }
					  */
					}
	      }  // end foreach
	      $memo[$S]="<font size=1>".substr($resit_subject,0,strlen($resit_subject)-1)."</font>";
				$resit_subject="(".substr($resit_subject,0,strlen($resit_subject)-1).")";
	     } else {
	       $memo[$S]="<font size=1>".$v."</font>";
	     }
	     $resit_scope.=$resit_subject."】";
	   } else {
	    $memo[$S]="及格";
	   }
	   //已補考
	   	$sql="select a.score from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and a.student_sn='$student_sn' and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$S' and a.complete='1'";
			$res=$CONN->Execute($sql) or die($sql);
			if ($res->recordcount()) {
	      $resit_tested.="【".$v."】";
		  }
	  }
	  
	  if ($put_it==1) {
			if ($_POST['opt2']=='') {
			 $x->items[]=array($student_data[$student_sn]['stud_id'],$student_data[$student_sn]['class_name'],$student_data[$student_sn]['seme_num'],$student_data[$student_sn]['stud_name'],$language,$math,$nature,$social,$health,$art,$complex,$resit_scope,$resit_number,$resit_tested,$student_data[$student_sn]['guardian_name'],$student_data[$student_sn]['stud_tel_2'],$student_data[$student_sn]['stud_tel_3'],$student_data[$student_sn]['addr_zip'],$student_data[$student_sn]['stud_addr_2']);
  		} elseif ($_POST['opt2']=='print') {
  			
  			$main='  			
  			<TABLE style="border-collapse: collapse; margin: auto; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 16pt 標楷體;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 20pt;">
          <TD colSpan="9" style="font: 20pt 標楷體; font-weight: bold;">'.$school_name.'</TD>
		</TR>
        <TR style="height: 20pt;">
          <TD colSpan="9" style="font: 20pt 標楷體; font-weight: bold;"><span style="font-family: Times New Roman; font-weight: bold;">'.curr_year().'學年度第<span style="font-family: Times New Roman; font-weight: bold;">'.curr_seme().'學期「補行評量」通知書</TD>
		</TR>
        <TR style="height: 20pt; font-size: 12pt;">
          <TD colSpan="9" style="font: 14pt 標楷體; text-align: left;"><BR>
		  壹、依據教育部「國民小學及國民中學學生成績評量準則」及相關規定辦理。<BR><BR>
		  貳、注意事項：<BR>
			'.$_POST['note_list'].'
		  參、檢視 貴子弟入學以來各學期七大學習領域成績平均， <B>貴子弟有部份領<BR>
		  　　域未達及格標準</B>，特予此通知書通知家長及 貴子弟，敬請家長共同協助<BR>
		  　　督導學生課業學習，以期 貴子弟補行評量順利，達到成績及格標準。<BR><BR>
		  肆、本次補行評量之學期範圍為：'.substr($seme_year_seme,0,3).'學年度 第'.substr($seme_year_seme,-1).'學期。<BR><BR>
		  伍、貴子弟該學期學習領域成績明細<BR><BR>
		  <span style="font-size: 18pt;">
		  '.$student_data[$student_sn]['class_name'].'</B></span><span style="font-size: 18pt;"><B> '.$student_data[$student_sn]['seme_num'].'</B></span> 號 <span style="font-size: 18pt;"><B>'.$student_data[$student_sn]['stud_name'].'</B></span>
		   <BR>
		  </TD>
		</TR>
        <TR style="height: 27pt;font-size: 12pt; background-color: #EEEEEE;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; text-align: center;" colspan="2">學習領域</TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;">語文</TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;">數學</TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;">自然與<br>生活科技</TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;">社會</TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;">健康與<br>體育</TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;">藝術與<br>人文</TD>
          <TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" >綜合</TD>
		</TR>
    <TR style="height: 28pt;font-size: 16pt;">
			<TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; font-size: 12pt;" colSpan="2">成績</TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$fin_score[$student_sn]['language'][$seme_year_seme]['score'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$fin_score[$student_sn]['math'][$seme_year_seme]['score'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$fin_score[$student_sn]['nature'][$seme_year_seme]['score'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$fin_score[$student_sn]['social'][$seme_year_seme]['score'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$fin_score[$student_sn]['health'][$seme_year_seme]['score'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$fin_score[$student_sn]['art'][$seme_year_seme]['score'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$fin_score[$student_sn]['complex'][$seme_year_seme]['score'].'</span></TD>
		</TR>
    <TR style="height: 28pt;font-size: 16pt;">
			<TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; font-size: 12pt;" colSpan="2">備註</TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$memo['language'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$memo['math'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$memo['nature'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$memo['social'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$memo['health'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$memo['art'].'</span></TD>
			<TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">'.$memo['complex'].'</span></TD>
		</TR>
        <TR style="height: 0pt;">
          <TD style="border-top: windowtext 1.5pt solid;" colSpan="9"></TD>
		</TR>
        <TR style="height: 20pt; font-size: 12pt;">
          <TD colSpan="9" style="font: 14pt 標楷體; text-align: right;">教務處　　敬上<BR>中華民國 '.(date('Y')-1911).' 年 '.date('m').' 月 '.date('d').' 日</TD>
		</TR>		
		<TR>
		  <TD colSpan="9" style="height: 20pt; text-align: left;"></TD>
		</TR>
    <TR style="height: 20pt;">
       <TD colSpan="9" style="font: 18pt 標楷體; font-weight: bold; border-top: windowtext 0.75pt dashed;">
		  <BR><span style="font-family: Times New Roman; font-weight: bold;">'.curr_year().'</span>學年度第<span style="font-family: Times New Roman; font-weight: bold;">'.curr_seme().'</span>學期「補行評量」通知書家長回執聯
		  </TD>
		</TR>
        <TR style="height: 15pt; font-size: 12pt;">
          <TD colSpan="9" style="font: 14pt 標楷體; text-align: left;"><BR>
		  本人為 <span style="font-size: 18pt;"><B>'.$student_data[$student_sn]['class_name'].'</B></span><span style="font-size: 18pt;"><B> '.$student_data[$student_sn]['seme_num'].'</B></span> 號 <span style="font-size: 18pt;"><B>'.$student_data[$student_sn]['stud_name'].'</B></span> 學生家長，接獲教務處的「補行評量通知書」，已詳細閱讀並了解學生學習狀況。<BR>
		  </TD>
		</TR>
        <TR style="height: 20pt; font-size: 12pt;">
          <TD colSpan="9" style="font: 14pt 標楷體; text-align: right;"><BR>
		  家長簽章：<U>　　　　　　　　　</U>（簽名時請簽全名）<BR>
		  </TD>
		</TR>
        <TR>
          <TD>&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
		</TR>
		</TBODY>
	  </TABLE>
	</TD>
  </TR>
  </TBODY>
</TABLE>';
  		 echo $main;
  		
  		}
  	} // end if
   } // end foreach 
   
  
 
 //單一領域
 } else {
	$x=new sfs_xls();
	$x->setUTF8();
	$x->filename=$seme_year_seme.$link_ss[$scope].'不及格學生名單.xls';
	$x->setBorderStyle(1);
	$x->addSheet($link_ss[$scope]."不及格");
	$x->items[0]=array('學號','目前班級','目前座號','姓名');
	//讀取分科
	$data_length=3;
	foreach ($scope_subject['ALL'][$scope] as $V) {
	 $data_length++;
	 $x->items[0][$data_length]=$V['subject'];
 	 $data_length++;
	 $x->items[0][$data_length]="任課教師";
	}
	$add_array=array('學期分數','補考分數','家長姓名','市內電話','行動電話','郵遞區號','通訊地址');
	foreach ($add_array as $v) {
 	 $data_length++;
	 $x->items[0][$data_length]=$v;
	}

  $add_data_id=0;
	foreach ($stud_sn as $student_sn) {
		if ($fin_score[$student_sn][$scope][$seme_year_seme]['score']<60) {
			
		//取得學生當學期的班級座號
    $sql_class_num="select seme_class from stud_seme where student_sn='".$student_sn."' and seme_year_seme='".$seme_year_seme."'";
 	  $res_class_num=$CONN->Execute($sql_class_num);
 	  if ($res_class_num->RecordCount()==1) {
 	    $seme_class=$res_class_num->fields['seme_class'];
          $class_year=substr($seme_class,0,1);
          $class_num=substr($seme_class,1,2);
          $class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$class_year,$class_num);
 	  } else {
 	    $seme_class="";
 	  }
			
			//讀取學生所有分科成績
 	    $ss_score=array();
  	  $sql_ss_score="select ss_id,ss_score from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn='$student_sn'";
			$res_ss_score=$CONN->Execute($sql_ss_score) or die($sql_ss_score);
			
			while ($row_ss_score=$res_ss_score->fetchRow()) {
			  $ss_id=$row_ss_score['ss_id'];
			  $ss_score[$ss_id]=$row_ss_score['ss_score'];
			}
			
			$add_data_id++;  //excel 長度計數　
			//先存入前4欄
			$x->items[$add_data_id]=array($student_data[$student_sn]['stud_id'],$student_data[$student_sn]['class_name'],$student_data[$student_sn]['seme_num'],$student_data[$student_sn]['stud_name']);
			$data_length=3;
			//放入分科成績
            //2015.11.18 檢查是否該班級有班級課程
            $target_id=($scope_subject[$class_id][$scope]=='')?"ALL":$class_id;
			foreach ($scope_subject[$target_id][$scope] as $V) {
	     	  $now_subject_ss_id=$V['ss_id'];
					$score=$ss_score[$now_subject_ss_id];  //分科分數
					//讀取老師任課老師
		            if ($seme_class) {
					    $sql_subject_teacher="select teacher_sn from score_course where class_id='$class_id' and ss_id='$now_subject_ss_id'";
						$res_subject_teacher=$CONN->Execute($sql_subject_teacher);
						$teacher_sn=$res_subject_teacher->fields['teacher_sn'];
						$subject_teacher=get_teacher_name($teacher_sn);
				    } else {
					    $subject_teacher="轉入生";   //轉學生，無科任老師
				    } // end if ($seme_class)
	 			$data_length++;
	 			$x->items[$add_data_id][$data_length]=$score;
 	 			$data_length++;
	 			$x->items[$add_data_id][$data_length]=$subject_teacher;
					
	    }  // end foreach
	
			
    	//是否有補考成績
			$sql="select a.* from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and a.student_sn='$student_sn' and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.complete='1'";
			$res=$CONN->Execute($sql) or die($sql);
			if ($res->recordcount()==0) {
			  $resit_score="";
			} else {
		    $resit_score=$res->fields['score'];		  
		  }
			//補上最後的資料		  
			$add_array=array($fin_score[$student_sn][$scope][$seme_year_seme]['score'],$resit_score,$student_data[$student_sn]['guardian_name'],$student_data[$student_sn]['stud_tel_2'],$student_data[$student_sn]['stud_tel_3'],$student_data[$student_sn]['addr_zip'],$student_data[$student_sn]['stud_addr_2']);
			foreach ($add_array as $v) {
 	 			$data_length++;
	 			$x->items[$add_data_id][$data_length]=$v;
			} // end foreach

  	} // end if
  } // end foreach //下一位學生
 } // end if $scope=='ALL'
 
  if ($_POST['opt2']=='') {
		$x->writeSheet();
		$x->process();
  }
  
  exit();

}  // end if 匯出不及格名單


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

// POST後執行的程式


//計算各領域不及格人數
if ($Cyear!="") {
		if ($_POST['act']=='get_all_resit_name') {
		 $all_students=count_scope_fail($Cyear,$SETUP['now_year_seme'],$ss_link,$link_ss);
		 $INFO="該學年學生總數 $all_students 人，已自學期成績資料庫中更新補考名單!";		 
	  } 
	  	$seme_year_seme=$SETUP['now_year_seme'];
	   foreach ($ss_link as $scope) {
	   	//不及格人數
	     $sql="select count(*) as num from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope'";
			 $res=$CONN->Execute($sql) or die ("讀取人數發生錯誤！SQL=".$sql);
			 $fail['still'][$scope]=$res->fields['num'];
			//已補考人數 
	     $sql="select count(*) as num from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.complete='1'";
			 $res=$CONN->Execute($sql) or die ("讀取人數發生錯誤！SQL=".$sql);
			 $fail['tested'][$scope]=$res->fields['num'];
			//待補考人數
	     $sql="select count(*) as num from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.complete='0'";
			 $res=$CONN->Execute($sql) or die ("讀取人數發生錯誤！SQL=".$sql);
			 $fail['ready'][$scope]=$res->fields['num'];			 
	   } // end foreach	   
		
} // end if $Cyear!="";


/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();
//列出選單
echo $tool_bar;
?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="opt1" value="">
	<input type="hidden" name="opt2" value="">
<?php
 echo "<font color=red>補考學期別：".$C_year_seme."</font><br>";
 echo "請選擇要檢視的年級：".$class_year_list;
 
 if ($Cyear!="") { 
 	?>
 <table border="0">
  <tr>
  	<!--左畫面 -->
    <td valign="top">
 	  <table border="1" style="border-collapse:collapse;font-size:10pt" bordercolor="#111111" cellpadding="3">
 		<tr bgcolor="#FFCCFF">
 			<td>領域別</td>
 			<td>不及格</td>
 			<td>已補考</td>
 			<td>待補考</td>
 			<td>檢視操作</td>
 		</tr>
 		<?php
 		foreach ($ss_link as $k=>$v) {
 		  ?>
 		  <tr>
 		    <td><?php echo $k;?></td>
 		    <td><?php echo $fail['still'][$v];?></td>
 		    <td><?php echo $fail['tested'][$v];?></td>
 				<td><?php echo $fail['ready'][$v];?></td>
 				<td>
 					<input type="button" value="未補考" class="html_resit_list" id="btn_<?php echo $v;?>_ready">
					<input type="button" value="補考中" class="html_resit_list" id="btn_<?php echo $v;?>_go">
 					<input type="button" value="補考完" class="html_resit_list" id="btn_<?php echo $v;?>_tested">
 					<input type="button" value="匯出名單" class="output_resit_name" id="<?php echo $v;?>">
 				</td>
 		  </tr>
 		  <?php
 		} 		
 		?>
 		<tr>
 				<td colspan="5" align="center">
 					<input type="button" value="更新補考名單" class="get_all_resit_name">
 					<input type="button" value="匯出所有領域名單" id="output_resit_name_all">
 					<input type="button" value="列印通知單" id="print_resit_name_all">
 				</td>
 		</tr>
 	  </table>
     <?php 
     if ($INFO) {
     echo "<br><font color=red>$INFO</font>";
     }
     ?>
   	 <div id="waiting" style="display:none">
   	 	<br><font color='red'>資料處理中，請稍候.....</font><br>
     </div> 
     <table border="0" width="520">
 	    <tr>
 	     <td style='font-size:10pt;color:#800000'>※列印通知單之注意事項（請依需要自行修訂）</td>
 	    </tr>
 	    <tr>
 	     <td>
 	     <?php
 	      $input_data="
一、評量範圍以該學期教學內容為原則。<BR>
二、除有不可抗力因素外，<B>逾期未參加者，視同放棄補行評量之機會</B>。<BR>
三、本次補行評量對象為<U><B>學習領域學期成績未達丙等（六十分）</B></U>之學生。<BR>
四、依規定<B>補行評量及格者，該學習領域成績以六十分計</B>。<BR>
五、補行評量時程與地點，另行公告之。<BR><BR>";
 	     ?>
 	     <textarea style="width:100%;font-size:10pt" rows="6" name="note_list"><?php echo $input_data;?></textarea>
 	     </td>
 	    </tr>
 	  </table>
 	  	
 		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>說明:<br>
   1.匯出資料皆採用 Excel 格式，以供套印各類通知單。<br>
   2.本統計表若有錯誤或第一次啟用本學期補考，請按<input type="button" value="更新補考名單" class="get_all_resit_name">重取名單。<br>
	 3.如果需要各分科的成績及任課教師名單，請由各領域「匯出名單」。<br>
	 <font color=red>4.注意！本表應在補考進行前匯出，如果已利用 makeup_exam 模組進行補考成績擇優，<br>則匯出的領域成績及分科成績為擇優後的成績。</font>
   </font>

    </td>
  	<!--右畫面 -->
    <td valign="top">
		<span id="show_right"></span>
    </td>
 </table> 	
 	<?php
 } //end if $Cyear 
?>
</form>
<?php
//  --程式檔尾
foot();
?>

<Script>

//匯出不及格名單 , 依領域
$(".output_resit_name").click(function(){
	var scope=$(this).attr("id");
	document.myform.act.value="output_resit_name";
	document.myform.opt1.value=scope;
	document.myform.target="";
	document.myform.submit();
	document.myform.act.value="";
})

//匯出不及格名單
$("#output_resit_name_all").click(function(){
	var scope=$(this).attr("id");
	document.myform.act.value="output_resit_name";
	document.myform.opt1.value="ALL";
	document.myform.target="";
	document.myform.submit();
	document.myform.act.value="";
})

//列印通知單
$("#print_resit_name_all").click(function(){
	var scope=$(this).attr("id");
	document.myform.act.value="output_resit_name";
	document.myform.opt1.value="ALL";
	document.myform.opt2.value="print";
	document.myform.target="_blank";
	document.myform.submit();
	document.myform.act.value="";
	document.myform.opt2.value="";
	document.myform.target="";
})

//重新計算取得補考名單
$(".get_all_resit_name").click(function(){
	document.myform.act.value="get_all_resit_name";
	waiting.style.display="block";
	document.myform.submit();
	document.myform.act.value="";
})

//檢視已補考名單
$(".html_resit_list").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array(3);
　var NewArray = btnID.split("_");
  var scope=NewArray[1];
  var opt1=NewArray[2];
	var act='html_resit_list';
	var Cyear='<?php echo $_POST['Cyear'];?>';
  
    $.ajax({
   	type: "post",
    url: 'resit_score.php',
    data: { act:act,scope:scope,opt1:opt1,Cyear:Cyear },
    dataType: "text",
    error: function(xhr) {
      alert('ajax request 發生錯誤!');
    },
    success: function(response) {
    	$('#show_right').html(response);
      $('#show_right').fadeIn(); 
			
    } // end success
	});   // end $.ajax


})


</Script>