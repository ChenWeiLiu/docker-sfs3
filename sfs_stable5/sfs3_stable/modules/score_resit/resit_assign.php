<?php	
header('Content-type: text/html;charset=big5');
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//���o�]�w��
include_once "config.php";
//���ҬO�_�n�J
sfs_check(); 

//�s�@��� ( $school_menu_p�}�C�]�w�� module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//Ū���ɦҾǴ��O�]�w
$sql="select * from resit_seme_setup limit 1";
$res=$CONN->Execute($sql);
$SETUP=$res->fetchrow();
$C_year_seme=substr($SETUP['now_year_seme'],0,3)."�Ǧ~�� �� ".substr($SETUP['now_year_seme'],-1)." �Ǵ�";
//�ثe�B�z���Ǧ~�Ǵ�
$sel_year = substr($SETUP['now_year_seme'],0,3);
$sel_seme = substr($SETUP['now_year_seme'],-1);

//�w��w���~��
$Cyear=$_POST['Cyear'];

//������Ǵ��Ҧ��ҵ{�]�w(���Ф���) 3���}�C $scope_subject[scope][][]
$scope_subject=get_year_seme_scope($sel_year,$sel_seme,$Cyear);


//�T�{�i�ɦҪ��~��
//�Ҧp: �H�ꤤ�Ө�, �{���Ǧ~ 103 , �Y�ҥ� 102�Ǧ~, �u��ҸӦ~���@�~�ũM�G�~��, �]���T�~�Ťw���~
// �ꤤ�ΰ�p�P�w $IS_JHORES=6 (�ꤤ) , $IS_JHORES=0 (��p)
if ($IS_JHORES==6) {
	$SY=$curr_year-3;   //�H103����, ����I�� 100
} else {
	$SY=$curr_year-6;   //�H103����, ����I�� 97
}

//�s�@�~�ſ��
$sy_circle=$sel_year-$SY;	
$now_cy=3-$sy_circle;

$class_year_list="
  <select size=\"1\" name=\"Cyear\" onchange=\"this.form.opt1.value='';this.form.opt2.value='';this.form.act.value='';this.form.submit()\">
   <option value=''>�п�ܦ~��</option>";
   for ($i=1;$i<=$sy_circle;$i++) {
    $CY=$i+$IS_JHORES;
    $NCY=$CY+$now_cy;
    $class_year_list.="<option value='$CY'".(($CY==$Cyear)?" selected":"").">".$school_kind_name[$CY]."�� (�ثe�NŪ".$school_kind_name[$NCY]."��)</option>";
   }    
$class_year_list.="
  </select>
";


 		if($Cyear>2){
			$ss_link=array("�y��"=>"language","�ƾ�"=>"math","�۵M�P�ͬ����"=>"nature","���|"=>"social","���d�P��|"=>"health","���N�P�H��"=>"art","��X����"=>"complex");
			$link_ss=array("language"=>"�y��","math"=>"�ƾ�","nature"=>"�۵M�P�ͬ����","social"=>"���|","health"=>"���d�P��|","art"=>"���N�P�H��","complex"=>"��X����");
		} else {
			$ss_link=array("�y��"=>"language","�ƾ�"=>"math","���d�P��|"=>"health","�ͬ�"=>"life","��X����"=>"complex");
			$link_ss=array("language"=>"�y��","math"=>"�ƾ�","health"=>"���d�P��|","life"=>"�ͬ�","complex"=>"��X����");
		}
// POST����檺�{��
//�]�w�ը��Aajax,����� exit
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
   //�s�@�U����X�D�ƪ���
   //Ū�����R�X�D�Ƴ]�w
   $subject_items=get_scope_subject_items($SETUP['now_year_seme'],$Cyear,$scope);
   $subject_items_input="
    <table border='0' >    
   ";
   foreach ($scope_subject['ALL'][$scope] as $k=>$v) {
   	$subject_items[$k]=($subject_items[$k]=="" or $subject_items[$k]<0)?(20):$subject_items[$k];
   	$subject_items_input.="<tr><td width='20'>&nbsp;</td><td>".$v['subject']." </td><td><input type='text' name='subject_".$k."' size='5' value='".$subject_items[$k]."'>�D <font size=2>(�[�v:".$v['rate'].", �w�R".$v['items']."�D)</font></td></tr>";
   }
   $subject_items_input.="</table>";
   
   
   $main="
   <input type='hidden' name='scope' value='$scope'>
   <table border='0' cellpadding='3'>
   	<tr>
   	  <td colspan='2' style='color:#800000'><b>".$link_ss[$scope]."���</b>�ը��]�w</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>�Ҹն}�l�ɶ�</td>
   		<td valign='top'><input type='text' size='20' name='start_time' value='$start_time'></td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>��������ɶ�</td>
   		<td valign='top'><input type='text' size='20' name='end_time' value='$end_time'></td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>�p�ɼҦ�</td>
   		<td valign='top'>
   		    <input type='radio' name='timer_mode' value='0'".(($timer_mode==0)?" checked":"").">�ӧO�p��
   		    <input type='radio' name='timer_mode' value='1'".(($timer_mode==1)?" checked":"").">�P�ɭp��
   		</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>�p�ɮɶ�</td>
   		<td valign='top'><input type='text' size='5' name='timer' value='$timer'>����</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>�X�D�Ҧ�</td>
   		<td valign='top'>
   		<table border='1' style='border-collapse:collapse' bordercolor='#000000'>
   			<tr>
   				<td>
   		      <input type='radio' name='item_mode' value='0'".(($item_mode==0)?" checked":"").">�H����<input type='text' size='5' name='items' value='$items'>�D����(��J0���ܥ���)
   		    </td>
   		   </tr>
   		   <tr>
   		   	<td>
   		   	  <input type='radio' name='item_mode' value='1'".(($item_mode==1)?" checked":"").">�̩ҧt����X�D<br>
						$subject_items_input						
					</td>
   				</tr>
   			</table>
   		</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>�}��ǥͬd�ߧ@��</td>
   		<td valign='top'>
   		    <input type='radio' name='relay_answer' value='0'".(($relay_answer==0)?" checked":"").">�_
   		    <input type='radio' name='relay_answer' value='1'".(($relay_answer==1)?" checked":"").">�O
   		    <br><font size=2>(�Y�n�}��A��ĳ�Ҹէ�����A�}��)</font>
   		</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>�_�u��O�_�i�A���</td>
   		<td valign='top'>
   		    <input type='radio' name='double_papers' value='0'".(($double_papers==0)?" checked":"").">�_
   		    <input type='radio' name='double_papers' value='1'".(($double_papers==1)?" checked":"").">�O
   		    <br><font size=2>(�w�]�u�_�v�A�i�קK���a�n�J�P�b�����л��)</font>
   		</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>�ը��`��</td>
   		<td valign='top'><input type='text' size='5' name='top_marks' value='$top_marks'>��</td>
   	</tr>
   	<tr>
   		<td valign='top' align='right'>�x�s�᭫�⦨�Z</td>
   		<td valign='top'><input type='checkbox' name='reset_score'></td>
   	</tr>

   </table>

   ";

	echo $main;
  exit();
} // end if ($_POST['act']=='setup_paper')

//�x�s�ը��]�w
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
  
    //�O�_�������
    $reset_score_txt="";
    if ($_POST['reset_score']==1) {
  	  $paper_setup=get_paper_sn($SETUP['now_year_seme'],$Cyear,$scope);
      $sql="select sn,items,answers from resit_exam_score where paper_sn='".$paper_setup['sn']."' and complete='1'";  
      $res=$CONN->Execute($sql)or die ('Error! Query='.$sql);
      $i=0;  //�p��
      while ($row=$res->fetchRow()) {
        $i++;
        $items=unserialize($row['items']);
				$answers=unserialize($row['answers']);
				//�v�D����
				$item_count=0;
				$item_correct=0;
				foreach ($items as $k=>$v) {
    	  	$item_count++;
    	  	// $v �����D�� sn
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
      
      $reset_score_txt="�w���� ".$i." ��ǥͪ����Z!";
      
    } // end if $_POST['reset_score']==1

	} else {
	  $sql="insert into resit_paper_setup (seme_year_seme,class_year,scope,start_time,end_time,timer_mode,timer,items,relay_answer,double_papers,item_mode,top_marks) values ('".$SETUP['now_year_seme']."','$Cyear','$scope','$start_time','$end_time','$timer_mode','$timer','$items','$relay_answer','$double_papers','$item_mode','$top_marks')";
	  $res=$CONN->Execute($sql) or die ('Error! Query='.$sql);
	}

	//�x�s����]�w
	if ($item_mode=='1') {
		$subject=$_POST['subject'];		
		$SS=explode(";",$subject);
				
		foreach ($SS as $v) {
			$V=explode(",",$v);
			//�ˬd�O�_�w�s�b
		   $sql="select sn from resit_scope_subject where seme_year_seme='".$SETUP['now_year_seme']."' and cyear='$Cyear' and scope='$scope' and subject_id='".$V[0]."'";
		   $res=$CONN->Execute($sql) or user_error("Ū������]�w���~! $sql",256);
		   if ($res->RecordCount()==1) {
		     $sn=$res->fields['sn'];		     
		     $sql="update resit_scope_subject set items='".$V[1]."' where sn='$sn'";
		     $res=$CONN->Execute($sql) or user_error("�x�s����]�w���~! $sql",256);
		   } else {
		   	 $seme_year_seme=$SETUP['now_year_seme'];
		   	 $subject_id=$V[0];
		   	 $subject=$scope_subject['ALL'][$scope][$subject_id]['subject'];
		   	 $items=$V[1];
		   	 $sql="insert into resit_scope_subject (seme_year_seme,cyear,scope,subject_id,subject,items) values ('$seme_year_seme','$Cyear','$scope','$subject_id','$subject','$items')";
		     $res=$CONN->Execute($sql) or user_error("�x�s����]�w���~! $sql",256);
		   } // end if $res->RecoreCount()  
		} // end foreach   	
	} // end if ($item_mode==1)

	echo "<font color=red>".$link_ss[$scope]."</font>���ը��]�w�x�s����! <br> $reset_score_txt";
	
  exit();
}


//�x�s���D
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

	//�B�z�Ϥ� ���o $fig_q,$fig_a,$fig_b,$fig_c,$fig_d ���ӭ�
	$fig_array=array("q","a","b","c","d");
	foreach ($fig_array as $v) {
		$target_fig="thefig_".$v;
		$target_fig_name="fig_".$v;
		${$target_fig_name}="";
	   if ($_FILES[$target_fig]!="") {	   	
	   	//������ɦW
      $expand_name=explode(".",$_FILES[$target_fig]['name']);
      $nn=count($expand_name)-1;
      $ATTR=strtolower($expand_name[$nn]); //��p�g���ɦW
      if ($ATTR=='jpg' or $ATTR=='png') {
          $img_info = getimagesize($_FILES[$target_fig]['tmp_name']);
    			$xx   = $img_info['0'];
    			$yy   = $img_info['1'];
					$imgtype=$_FILES[$target_fig]['type'];
					
          $sFP=fopen($_FILES[$target_fig]['tmp_name'],"r");				//���J�ɮ�
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
	 //�s�W���D
	 $sql="insert into resit_exam_items (paper_sn,question,cha,chb,chc,chd,fig_q,fig_a,fig_b,fig_c,fig_d,answer,subject) values ('".$paper_setup['sn']."','$question','$cha','$chb','$chc','$chd','$fig_q','$fig_a','$fig_b','$fig_c','$fig_d','$answer','$subject')";
	 $res=$CONN->Execute($sql) or die ("Error! SQL=".$sql);
	 //���o�̫᪺ sn , �H��̫ܳ�s�誺���D	
	 list($Last_item_sn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
  } else {
   $item_org=get_item($item_sn);
   //�Y������,�R�����
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
		$res=$CONN->Execute($sql) or die ("�ק���D����! SQL=".$sql);
	
   //�s����D        
   $Last_item_sn=$item_sn; 
  }
  //�O���s����D���A
  $_POST['act']=($opt2!='')?$opt2:'edit_paper';  
} // end if edit_paper_submit


//�x�s���D
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
		//�s�W���D
		$sql="insert into resit_exam_items (paper_sn,question,cha,chb,chc,chd,fig_q,fig_a,fig_b,fig_c,fig_d,answer,subject) values ('".$paper_setup['sn']."','$question','$cha','$chb','$chc','$chd','$fig_q','$fig_a','$fig_b','$fig_c','$fig_d','$answer','$subject')";
		$res=$CONN->Execute($sql) or die ("Error! SQL=".$sql);
		//���o�̫᪺ sn , �H��̫ܳ�s�誺���D
		list($Last_item_sn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	} else {
		$item_org=get_item($item_sn);
		//�Y������,�R�����
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
		$res=$CONN->Execute($sql) or die ("�ק���D����! SQL=".$sql);

		//�s����D
		$Last_item_sn=$item_sn;
	}
	//�O���s����D���A
	$_POST['act']=($opt2!='')?$opt2:'edit_paper_new';
} // end if edit_paper_submit



//�ק���D
if ($_POST['act']=='edit_paper_update') {		 
  $item_sn=$_POST['item_sn'];
	$item_scope=$_POST['item_scope'];
	$item=get_item($item_sn);
	//�ק粒�n��^���ʧ@
  $opt2=$_POST['opt2'];
  //�O���s����D���A
  $_POST['act']='edit_paper_new';
}

//�R�����D
if ($_POST['act']=='edit_paper_delete') {		 
  $item_sn=$_POST['item_sn'];
	$item_scope=$_POST['item_scope']; 
	$item_org=get_item($item_sn);	
   //�Y������,�R�����
   	$fig_array=array("q","a","b","c","d");
	  foreach ($fig_array as $v) {
		 $target_fig="fig_".$v;
		 if ($item_org[$target_fig]>0) {
		     $CONN->Execute("delete from resit_images where sn='".$item_org[$target_fig]."'");
		 }
		} // end foreach
		//�R�����D
 	  $CONN->Execute("delete from resit_exam_items where sn='".$item_org['sn']."'");
	//�R�����n��^���ʧ@
  $_POST['act']=$_POST['opt2'];  
} // end if $_POST['act']=='edit_paper_delete'

//�x�s�ֶK�����D
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
			$res=$CONN->Execute($sql) or die("�x�s�o�Ϳ��~�F! SQL=".$sql);
	   } // end if ($_POST['save_it'][$I]==1)
  } // end foreach ($_POST[field] as $I=>$P)
  //�������C�X���D
  $_POST['act']='list_paper';  
} // end if edit_paper_submit

//�վ�ѵ� - �x�s
if ($_POST['act']=='list_paper_answer_save') {		 
	$item_scope=$_POST['item_scope'];
  
  foreach ($_POST['answer'] as $sn=>$v) {
    $sql="update resit_exam_items set answer='$v' where sn='$sn'";
    $res=$CONN->Execute($sql) or die('�x�s�ѵ����ѡISQL='.$sql);
  } // end foreach ($_POST[field] as $I=>$P)
  //�������C�X���D
  $_POST['act']='list_paper';  
} // end if list_paper_answer_save

//�]�w���D���� - �x�s
if ($_POST['act']=='list_paper_subject_save') {		 
	$item_scope=$_POST['item_scope'];  
  foreach ($_POST['ch_subject'] as $sn=>$v) {
    $sql="update resit_exam_items set subject='$v' where sn='$sn'";
    $res=$CONN->Execute($sql) or die('�x�s���D����]�w���ѡISQL='.$sql);
  } // end foreach ($_POST[field] as $I=>$P)
  //�������C�X���D
  $_POST['act']='list_paper';  
} // end if list_paper_answer_save


//�ץX���D 
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

		$filename=$SETUP['now_year_seme']."_".$Cyear."�~��_".$link_ss[$scope]."���D��.wit";
		
		//�H��y�覡�e�X
		header("Content-disposition: attachment; filename=$filename");
		header("Content-type: application/vnd.sun.xml.writer");
		header("Cache-Control: max-age=0");
		header("Pragma: public");
		header("Expires: 0");

		echo $main;  
  exit();
  
  } // end if $_POST['act']=='download_paper'

//�פJ���D - submit
if ($_POST['act']=='upload_paper_submit') {
	$item_scope=$_POST['item_scope'];
	$paper_setup=get_paper_sn($SETUP['now_year_seme'],$Cyear,$item_scope);

	//�}�l����
  $aFile=fopen($_FILES['thefile']['tmp_name'],"r");
  $try_title=preg_replace("/\\r\\n/","",fgets($aFile,1024));
  
  $C=explode("-",$try_title);
  //����O�_�� wit��  
	if (!in_array($C[2], $ss_link)) {
    echo "�ɮ׮榡���šI";
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

   //���s�J�Ϥ�
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
					$res=$CONN->Execute($sql) or die("���J�Ϥ�����! SQL=".$sql);					
		     	list(${$target_fig_name})=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		     	//echo $target_fig_name.'='.${$target_fig_name};
		     	//exit();
		    } else {
		     ${$target_fig_name}='';
		    }
    } // end foreach
   
   //�s�J���D
	  $sql="insert into resit_exam_items (paper_sn,question,cha,chb,chc,chd,fig_q,fig_a,fig_b,fig_c,fig_d,answer,subject) values ('".$paper_setup['sn']."','$question','$cha','$chb','$chc','$chd','$fig_q','$fig_a','$fig_b','$fig_c','$fig_d','$answer','$subject')";
    $res=$CONN->Execute($sql) or die("�x�s�o�Ϳ��~�F! SQL=".$sql);
  } // end while (!feof($aFile))
	$_POST['act']='list_paper';
}

//**************** �}�l�q�X���� ******************/
//�q�X SFS3 ���D

head();
//�C�X���
echo $tool_bar;
?>
<form name="myform" id="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="opt1" value="">
	<input type="hidden" name="opt2" value="<?php echo $opt2;?>">
<?php
 echo "<font color=red>�ɦҾǴ��O�G".$C_year_seme."</font><br>";
 echo "�п�ܩR�D���~�šG".$class_year_list;
 
 if ($Cyear!="") { 
 	?>
 <table border="0" width="100%">
  <tr>
  	<!--���e�� -->
    <td width="660" valign="top" rowspan="2">
    	
 		<table border="1"  style="border-collapse:collapse;font-size:10pt" bordercolor="#111111" cellpadding="3" width="100%">
 		 <tr bgcolor="#FFCCFF" width="100%">
 			<td align="center">���O</td>
 			<td align="center">�D�� </td>
 			<td align="center">�ާ@</td>
 		 </tr>
 		<?php
 		foreach ($ss_link as $k=>$v) {
 			
 			if ($_POST['opt1']!="") {
 			  $display=($_POST['opt1']==$v)?"table-row":"none";
 			  //�ثe�ާ@���O��� $item_scope �O��
 			  $item_scope=$_POST['opt1'];
 			} else { 				
 			  $display="table-row"; 			  
 			}
 			
 			//�p�⥻����D��
 			$sql="select a.* from resit_exam_items a, resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='".$SETUP['now_year_seme']."' and b.class_year='$Cyear' and b.scope='$v'";
 			$res=$CONN->Execute($sql);
 			$num=$res->RecordCount();
			//�T�{�w�ҥΡA�_�h��T���s disable 					
 			$sql="select * from resit_paper_setup where seme_year_seme='".$SETUP['now_year_seme']."' and class_year='$Cyear' and scope='$v'";	
			$res=$CONN->Execute($sql);
			$disabled=($res->recordcount()==0)?"disabled":"";
 		  ?>
 		  <tr width="100%" class="scope_table" id="<?php echo $v;?>" style="background-Color:#FFFFFF;display:<?php echo $display;?>">
 		    <td><?php echo $k;?></td>
 		    <td align="center"><?php echo $num;?></td>
 				<td align="center">
 					<input type="button" value="�]�w" class="setup_paper" id="btn-<?php echo $v;?>-setup">
 					<input type="button" value="�ֶK" class="paste_paper" id="btn-<?php echo $v;?>-paste" <?php echo $disabled;?>>
 					<input type="button" value="�u�W�R�D(�ǲ�)" class="edit_paper" id="btn-<?php echo $v;?>-edit" <?php echo $disabled;?>>
					<input type="button" value="�u�W�R�D(�i�K��)" class="edit_paper_new" id="btn-<?php echo $v;?>-editnew" <?php echo $disabled;?>>
 					<input type="button" value="�ץX" class="download_paper" id="btn-<?php echo $v;?>-download" <?php echo $disabled;?>>
					<input type="button" value="�פJ" class="upload_paper" id="btn-<?php echo $v;?>-upload" <?php echo $disabled;?>>
 					<input type="button" value="�˵�" class="list_paper" id="btn-<?php echo $v;?>-list" <?php echo $disabled;?>>
 				</td>
 		  </tr>
 		  <?php
 		} 		
 		?>
 	  </table>
    </td>
  	<!--�k�e�� -->
    <td valign="top">
		<span id="show_right"></span>
    </td>
  </tr>
  <tr>
  	<td>
  		<div id="setup_paper_readme" style="display:none">
  			<input type="button" id="setup_paper_submit" value="�x�s�]�w"><br>
  		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>����:<br>
   1.���ĥΡu�ӧO�p�ɡv�Ҧ��ɡA�ǥͬҥi��o�ۦP���p�ɮɶ��@���C<br>
   2.���ĥΡu�P�ɭp�ɡv�Ҧ��ɡA�ǥͩ�ۦP���ɶ������ҸաC<br>
   3.�Y�u�Ǵ��ɦҳ]�w�v������Ҧ��]�w���u�̤U�C�]�w�ɬq���}��<br>�Ҧ��ը��v�A�h���B�Ҹծɶ������]�w�L����@�ΡC<br>
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
  			<input type="button" id="edit_paper_submit" value="�x�s���D">
  			<input type="button" id="edit_paper_end" value="�����R�D">
  			<br>
  			<?php
  			 if ($Last_item_sn) {
  			?>
  				<table border='1' bordercolor='#FFFFFF' cellspacing='0' bordercolordark='#FFFFFF' bordercolorlight='#800000'>
   					<tr bgcolor='#FFCC66'>
    				 	<td style='font-size:10pt;color:#0000cc'><img src='.\images\filefind.png'>�˵����e���D</td>
   					</tr>
   					<tr>
     					<td><?php echo show_item($Last_item_sn);?></td>
   					</tr>
  				</table>
  			<?php
  		  } //end if ($Last_item_sn)
  			?>
  		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>�s����D����:<br>
      1.���D�i�W�Ǫ��ϡA�t�Ψå�������Ϥj�p�A�����F�������[�ξ\Ū���ξA�סA�оA�׽վ���D�����Ϥj�p�C<br>
      2.�D�F�����ϡA�e�׺ɥi�ण�W�L 400px�F��ت��e�׺ɥi�ण�W�L200px�C<br>
      3.�ﶵ�Y�t�Ϥ��A��ĳ���Q��ø�ϳn��վ�|�ӿﶵ���Ϥ��j�p(�e�ΰ�)�۪�C<br>
      4.�p�G�ӻ��w���ǥͰѥ[�ɦҡA���D��Ʈw�����H�N��ʡA�H�K�˵��ը��ɵL�k���޸��D�C
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
						 <input type="button" id="edit_paper_submit_new" value="�x�s���D">
						 <input type="button" id="edit_paper_end" value="�����R�D">
						 <br>
						 <?php
						 if ($Last_item_sn) {
							 ?>
							 <table border='1' bordercolor='#FFFFFF' cellspacing='0' bordercolordark='#FFFFFF' bordercolorlight='#800000'>
								 <tr bgcolor='#FFCC66'>
									 <td style='font-size:10pt;color:#0000cc'><img src='.\images\filefind.png'>�˵����e���D</td>
								 </tr>
								 <tr>
									 <td><?php echo show_item($Last_item_sn);?></td>
								 </tr>
							 </table>
							 <?php
						 } //end if ($Last_item_sn)
						 ?>
						 <font size='2' color='#0000cc'>
							 <img src='./images/filefind.png'>�s����D����:<br>
							 1.���D���D�F�ο�جҥi�Q�κI�ϳn��A�νƻs�A�K�W�覡�i��K�ϡA��K�z�B�z�@�ǯS���D���A�p�G�ƾǤ�{���C<br>
							 2.���F�������[�ξ\Ū���ξA�סA�оA�׽վ���D�����Ϥj�p�C<br>
							 3.�p�G�ӻ��w���ǥͰѥ[�ɦҡA���D��Ʈw�����H�N��ʡA�H�K�˵��ը��ɵL�k���޸��D�C
						 </font>
					 </div>
				 </td>
			 </tr>
		 </table>
		 <?php

	 } // end if $_POST['act']=='edit_paper_new'

//�פJ���D 
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
  			<input type="button" id="upload_paper_submit" value="�W���ɮ�">
  			<input type="button" id="edit_paper_end" value="���}">
  			<br>
  		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>�פJ���D����:<br>
      1.�`�N�I�z�u��פJ���ɦW�� .wit�榡�����D�ɡC<br>
      2.���z�Ʊ椣�P�Ǵ��~�ׯ�ϥΦP�@���ը��ɡA�i�Q�ζץX�\��A�Y�i�o�� wit �榡���ɮסC<br>
      </font>
      </div>
  	</td>
  </tr> 
 </table> 	
			
 
<?php  
} // end if $_POST['act']=='upload_paper'

 //�ֶK���D
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

		 //�h���e��ť�
		 $PAST_DATA = trim($paste_items);
		//�h�����H�O�����b�@�����ť�
		 $PAST_DATA = preg_replace('/\s(?=\s)/', '', $PAST_DATA);
		//�h���Dspace ���ťաA�Τ@�ӪŮ�N��
		 $PAST_DATA = preg_replace('/[\r\t]/', '', $PAST_DATA);

		 //������b��
		 $PAST_DATA = nf_to_wf($PAST_DATA,0);

		 $buffer = explode("\n",$PAST_DATA);  //�H����Ÿ�, ���Ƥ���
		 $i=0;
		 $New_DATA="";
		 //�D���e�� ()1. �۰ʱƪ�
		 if ($_POST['collection_type']==2) {
			 foreach ($buffer as $v) {
				 //���� . ���ΡA�@�M���ˬd ) ����, �Y���Ʀr, ���ܬ��s���@�D
				 $h1=explode(".",$v);
				 if (count($h1)==2) {  //������
					 $h2=explode(")",$h1[0]);
					 if (count($h2)==2) {   //������, �A�ˬd�O�_���Ʀr
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
				 } else {              //�S����
					 $New_DATA.=$v;
				 }

			 } // end foreach
		 }

		 //�D�����Ʀr�}�Y, �p���I�j�}
		 if ($_POST['collection_type']==1) {

			 foreach ($buffer as $v) {
				 $head=explode(".",$v);
				 //print_r($head);
				 //�H�C�@�D�� nn.() �@�P�_
				 if ($head[0]>0) {
					 if ($i>0) $New_DATA.="\n";
					 $i++;
					 $New_DATA.=$v;
				 } else {
					 $New_DATA.=$v;
				 }
			 }
		 }
		 //�ȶi����Υb���ഫ
		 if ($_POST['collection_type']==3) {
			 $New_DATA=$PAST_DATA;
		 }

		 //��U�h�}�}�Y ()
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

		 //�^�s������ܼ�
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
			<span style="color:red">���ֶK�h�D��r���D</span>
			<div>
				<textarea name="paste_items" style="width:100%;height: 150px" ><?php echo $paste_items;?></textarea>
			</div>
			<div>
              <span style="color:#0055aa">
                <input type="button" id="paste_paper_composing" value="�۰ʱƪ�">
                  <input type="radio" name="collection_type" value="3" checked>�ȧ�����ഫ���b�� &nbsp;
                  <input type="radio" name="collection_type" value="1">�̡u�Ʀr+�p���I�v�ƪ�&nbsp;
                  <input type="radio" name="collection_type" value="2">�̡u�A��+�Ʀr+�p���I�v�ƪ�&nbsp;
                  <input type="radio" name="collection_type" value="4">�h���C��}�Y���u( )�v
                �@
              </span>
              <span style="float: right">
                  <input type="button" class="btn btn-xs btn-default" value="�M���K��" onclick="document.myform.past_items.value=''">
              </span>

			</div>
		</div>
		<div>
			<div style="margin-top: 10px">
				<span style="color:blue">�нT�{�_����R��r�Ÿ��G</span>
			</div>
			<div>
				��1�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[0];?>' size='10'><br>
				��2�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[1];?>' size='10'><br>
				��3�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[2];?>' size='10'><br>
				��4�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[3];?>' size='10'><br>
				��5�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[4];?>' size='10'><br>
				��6�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[5];?>' size='10'><br>
				��7�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[6];?>' size='10'><br>
				��8�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[7];?>' size='10'><br>
				��9�_��Ÿ��G<input type='text' name='cut[]' value='<?= $cut[8];?>' size='10'>
			</div>
		</div>
	  </div>
 	  </td>
 	</tr>
 	<tr>
  	<td>
  		<div id="edit_paper_readme" style="display:table-row">
  			<input type="button" id="paste_paper_submit" value="���R���D">
  			<input type="button" id="edit_paper_end" value="���}">
  			<br>
  		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>�ֶK���D����:<br>
      1.�ĥΧֶK�覡�A�i�P�ɫإߦh�D��r�����D�A�ϥΤ��R���D�e�A�Х��Q�Ρu�۰ʱƪ��v�\���z���D�C<br>
      2.������ϳ����A�����x�s������A�ĭק���D�覡�v�D�ƻs/�K�W�C<br>
      3.�K�W����r�������@�D�@�檺�榡�C<br>
      4.�ֶK�����A�i�Q�Ρi�˵��j���\��A�i��ѵ����վ�γ]�w�C�@�D������C     
      </font>
      </div>
  	</td>
  </tr> 
 </table> 	
 	<?php
  
  } // end if $_POST['act']=='edit_paper'


//�פJ���D�i���r���R, �T�{�S���D, �i���x�s
if ($_POST['act']=='paste_paper_submit') {	
	
	$items=stripslashes($_POST['paste_items']);

	$buffer = explode("\n",$items);  //�H����Ÿ�, ���Ƥ���

	//�}�l
	$i=0;
	$j=0;
	foreach ($_POST['cut'] as $C) {
		if ($C!="") {
			$j++;
		} else {
			break;   //�J��ť�, ���ܥH�U���A���R
		}
	}
	$j_max=$j+1;

	foreach ($buffer as $P )  {
		$i++;
		//�H�_��Ÿ��@����Ƥ���̾�, �̦h15��
		$j=0;
		//$j_max=0;
		$pj=0;
		$P=trim($P); //�h���e��ť�
		foreach ($_POST['cut'] as $C) {
			$j++;
			if ($j==$j_max) {
				if ($pj==0) $P_item[$i][$j]=$P; //�Ѿl��r
				break;
			}
			if ($C!="") {

				$NewP=explode($C,$P,2);    //�H���� $C ����q
				$P_item[$i][$j]=trim($NewP[0]);
				//$P=trim($NewP[1]);
				//�p�G�o�ӱ���S����,
				//if ($P=='') {
				if (count($NewP)==1) {
					if ($pj==0) {
						if ($j<$j_max) {
							$pj=$j;    //�O�U�ثe��������m
							$P=$P_item[$i][$j];     //�]���S����, �H�ثe������~�򩹤U��
						}
					} else {
						//�e���w�g���o�ͨS���쪺����, �ثe�o�����S�S����.
						if ($j<$j_max) {
							$P = $P_item[$i][$j];
							$P_item[$i][$j] = '';
						} else {  //�w�g��̫����F
							$P_item[$i][$j] = '';   //��o�����]�M��, ��������k��e���S���쪺�������
						}
					}
				} else {
					$P=trim($NewP[1]);
					//������
					if ($pj>0) {
						$P_item[$i][$j]='';
						$P_item[$i][$pj]=trim($NewP[0]);   //�Ĥ@�q����k��e�@�����
					}
					$pj=0;
				}
			}
		} // end foreach

	} // end foreach


	//�}�l�զX�� from
	$content="";
	for ($I=1;$I<=$i;$I++) {
	 //���
	 $content_tr=$content_td="";
	 for ($J=1;$J<=$j_max;$J++) {
	  $content_td.="<td><input type='text' size='12' name='field[$I][$J]' value='".$P_item[$I][$J]."'></td>";
	 }
	 //�C
	 $content_tr="
	  <tr class='paste_table'>
	   <td align='center'><input type='checkbox' name='save_it[$I]' value='1' checked></td>
	   $content_td
	  </tr>
	 ";
	 $content.=$content_tr;
	}
	
	//���D��
	 for ($J=1;$J<=$j_max;$J++) {
	  $content_title.="
	  <td>
  		<select size='1' name='to_field[$J]'>
    		<option value='none'>���x�s</option>
    		<option value='question'>�D�F</option>
    		<option value='cha'>���A</option>
    		<option value='chb'>���B</option>
    		<option value='chc'>���C</option>
    		<option value='chd'>���D</option>
    		<option value='answer'>�ѵ�</option>
  		</select>	  
	  </td>";
	 }
	$content_title="<tr bgcolor='#FFCC66'><td>�x�s</td>$content_title</tr>";
	$main="
	  <table border='0'>
	  $content_title
	  $content
	  </table>
	";
	//�}�l�e�{

  echo $main;
  ?>
  <input type="button" id="paste_paper_save" value="�x�s���D">
	<input type="button" id="edit_paper_end" value="���}">
	<br>
 		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>�ާ@����:<br>
      1.�п�w�C�@���n���������D���ءC<br>
      2.�p�G������ƭn�˱�A�п�ܡu���x�s�v�C<br>
      3.�`�N�A����������ƽФŭ��СA�H�K��Ʈw�X��!      
      </font>

  <?php
}



 //�˵����D 
 if ($_POST['act']=='list_paper') {
 ?>
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
 <table border="0">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<input type="button" id="list_paper_end" value="�����˵�">
 	  	<input type="button" id="list_paper_answer" value="�վ�ѵ�">
 	  	<input type="button" id="list_paper_subject" value="�]�w���D����">
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
 
 //�˵����D - �վ�ѵ�
 if ($_POST['act']=='list_paper_answer') {
 ?>
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
 <table border="0">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<input type="button" id="list_paper_end" value="�����˵�">
 	  	<input type="button" style="color:#FF0000" id="list_paper_answer_save" value="�x�s�ѵ�">
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

 //�˵����D - �]�w���D����
 if ($_POST['act']=='list_paper_subject') {
 ?>
 <input type="hidden" name="item_scope" value="<?php echo $item_scope;?>">
 <input type="hidden" name="item_sn" value="<?php echo $item['sn'];?>">
 <table border="0">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<input type="button" id="list_paper_end" value="�����˵�">
 	  	<input type="button" style="color:#FF0000" id="list_paper_subject_save" value="�x�s����]�w">
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
//  --�{���ɧ�
foot();


function nf_to_wf($strs, $types){  //���Υb���ഫ
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
			"�]", "�^", "�e", "�f", "�a", "�b", "�O",
		//"�A", "�F", "�G",
			"��",  "�I", "�I", "��", "�C", "�H", "��", "�U", "�B",
			"�A", "��", "��", "��", "?",
		//"�B", "�B", "?","�H",
			"��", "��",
			"�s", "��",
			"��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
			"��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
			"��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
			"��", "��", "�@", "�A", "�B", "�C",
			"��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
			"��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
			"��", "��", "��", "��", "��", "��",
			"�@"
	);

	if ($types == '1'){
		// �����
		$strtmp = str_replace($nft, $wft, $strs);
	}else{
		// ��b��
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
 //�w�q�Ҧ����
 var AllScope=[<?php echo $JavaArray;?>]; 

//�ƹ����X���J
$(".scope_table").hover(function(){
	 $(this).css("background-color","#FFFFAA");
	},function(){
	 $(this).css("background-color","#FFFFFF");	
})

//�ƹ����X���J
$(".paste_table").hover(function(){
	 $(this).css("background-color","#AAFFAA");
	},function(){
	 $(this).css("background-color","#FFFFFF");	
})

//�ƹ����X���J
$(".items_table").hover(function(){
	 $(this).css("background-color","#AAAAFF");
	},function(){
	 $(this).css("background-color","#FFFFFF");	
})

//�]�w�ը�
$(".setup_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var scope=NewArray[1];
	var act='setup_paper';
	var Cyear='<?php echo $Cyear;?>';
	   
  $.ajax({
   	type: "post",
    url: 'resit_assign.php',
    data: { act:act,scope:scope,Cyear:Cyear },
    dataType: "text",
    error: function(xhr) {
      alert('ajax request �o�Ϳ��~!');
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

//�x�s�ը��]�w
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
	//�O�_�������
	if (document.myform.reset_score.checked) {
	  var reset_score=1;
	} else {
		var reset_score=0;
	}
	
	//���o timer_mode , �ѩ�O�Q�� ajax �ʺA���ͪ��e���A�o��L�k�ϥ� jQuery ����
	for (var i=0; i<myform.timer_mode.length; i++) {
   if (myform.timer_mode[i].checked)
   {
      var timer_mode = myform.timer_mode[i].value;
   }
  }
  //���o relay_answer
	for (var i=0; i<myform.relay_answer.length; i++) {
   if (myform.relay_answer[i].checked)
   {
      var relay_answer = myform.relay_answer[i].value;
   }
  }
  
  //���o double_papers
	for (var i=0; i<myform.double_papers.length; i++) {
   if (myform.double_papers[i].checked)
   {
      var double_papers = myform.double_papers[i].value;
   }
  }	
  //���o item_mode
	for (var i=0; i<myform.item_mode.length; i++) {
   if (myform.item_mode[i].checked)
   {
      var item_mode = myform.item_mode[i].value;
   }
  }	  
  //���o�����D�Ƴ]�w
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

   	//�Ҹծɶ����
   if (paper_mode==0) {
   	starttime=start_time.replace(/-/g, "/"); 
   	starttime=(Date.parse(starttime)).valueOf() ; // �����ഫ��Date���O�ҥN������
   	endtime=end_time.replace(/-/g, "/"); 
   	endtime=(Date.parse(endtime)).valueOf() ; // �����ഫ��Date���O�ҥN������
    if (starttime>=endtime) {
     alert ("��������ɶ����o����ε���}�l�ɶ�!");
     return false;
    }	
    
    //�P�ɭp�ɡA�Ҹյ����ɶ����}�l�[�p�ɡA��������ɶ�����j��ε���Ҹյ����ɶ�
    //�ӧO�p�ɵL������
    if (timer_mode=='1') {
    	var test_end_time=starttime+timer*60*1000;
    	if (test_end_time<=endtime) {
    	  alert ("��������ɶ��W�L�ε���Ҹյ����ɶ�(�}�l�ɶ�+�p�ɮɶ��A���X�z!!");
    	  return false;
    	}
    }
   } else {
   	 alert("�`�N�I\n\n�ѩ�Ǵ��ɦҤ����]�w��ܡu�̳]�w�ɬq���}��Ҧ��ը��v\n�]�����B���ˬd�Ҹն}�l�ɶ��λ�������ɶ����X�z��\n���~�A�Ҹդ��@�߱ġu�ӧO�p�ɡv�C\n\n�C��ǥͪ��Ҹխp�ɮɶ���"+timer+"����");
   } // end if paper_mode==0

	$.ajax({
   	type: "post",
    url: 'resit_assign.php',
    data: { act:act,scope:scope,Cyear:Cyear,start_time:start_time,end_time:end_time,timer:timer,items:items,timer_mode:timer_mode,relay_answer:relay_answer,double_papers:double_papers,item_mode:item_mode,subject:strSubject,top_marks:top_marks,reset_score:reset_score },
    //data : postData,
    dataType: "text",
    error: function(xhr) {
      alert('ajax request �o�Ϳ��~!');
    },
    success: function(response) {
    	$('#show_right').html(response);
      $('#show_right').fadeIn(); 
      setup_paper_readme.style.display='none';
      for (index = 0; index < AllScope.length; ++index) {
        var ss=AllScope[index];        
        //document.getElementById(ss).style.display = 'block';
        //����s�� disabled ����
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


//�u�W�R�D
$(".edit_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var scope=NewArray[1];
	  
  document.myform.act.value='edit_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

});

 //�u�W�R�D
 $(".edit_paper_new").click(function(){
	 var btnID=$(this).attr("id");
	 var NewArray = new Array();
	 var NewArray = btnID.split("-");
	 var scope=NewArray[1];

	 document.myform.act.value='edit_paper_new';
	 document.myform.opt1.value=scope;

	 document.myform.submit();

 })



//�ץX���D
$(".download_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var scope=NewArray[1];
	  
  document.myform.act.value='download_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

})

//�פJ���D
$(".upload_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var scope=NewArray[1];
	  
  document.myform.act.value='upload_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

})

//�פJ���D - submit
$("#upload_paper_submit").click(function(){
	 
	if (document.myform.thefile.value=='') {
	  alert('�z�å�����ɮ�!');
	  return false;
	}
	 
	document.myform.opt1.value=document.myform.item_scope.value;
  document.myform.act.value='upload_paper_submit';
  document.myform.submit();

})

//�x�s���D , ���}���~�Ż��C��
$("#edit_paper_submit").click(function(){
	//�]�w opt1 ���Y���, �H�K�C������ܸӻ��
	document.myform.opt1.value=document.myform.item_scope.value;
  document.myform.act.value='edit_paper_submit';

  chk_submit=check_form_item();

  if (chk_submit) {
	 document.myform.submit();
	}

});

 //�x�s���D , ���}���~�Ż��C��
 $("#edit_paper_submit_new").click(function(){
	 //�]�w opt1 ���Y���, �H�K�C������ܸӻ��
	 document.myform.opt1.value=document.myform.item_scope.value;
	 document.myform.act.value='edit_paper_submit_new';

	 chk_submit=check_form_item_new();

	 if (chk_submit) {
		 document.myform.submit();
	 }

 })

//�ק���D
$(".edit_paper_update").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var item_sn=NewArray[1];

	//�]�w opt1 ���Y���, �H�K�C������ܸӻ��
	document.myform.opt1.value=document.myform.item_scope.value;
	document.myform.opt2.value='<?php echo $_POST['act'];?>';
  document.myform.act.value='edit_paper_update';
  document.myform.item_sn.value=item_sn;
	document.myform.submit();
})

//�R�����D
$(".edit_paper_delete").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var item_sn=NewArray[1];
  
  confirm_delete=confirm("�z�T�w�n�R�����D�H\n�y�����G"+item_sn);
  
  if (confirm_delete) {
		//�]�w opt1 ���Y���, �H�K�C������ܸӻ��
		document.myform.opt1.value=document.myform.item_scope.value;
		document.myform.opt2.value='<?php echo $_POST['act'];?>';
  	document.myform.act.value='edit_paper_delete';
  	document.myform.item_sn.value=item_sn;
		document.myform.submit();
  }
})

//�����R�D , ���}���~�Ż��C��
$("#edit_paper_end").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var scope=NewArray[1];
	 
	for (index = 0; index < AllScope.length; ++index) {
    var ss=AllScope[index];        
  	document.getElementById(ss).style.display = 'table-row';         
  }
  //�M���R�D��html , �קK�~�e
	$('#show_buttom').html("");
	edit_paper_readme.style.display='none'; 	

})

//�����˵� , ���}���~�Ż��C��
$("#list_paper_end").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var scope=NewArray[1];
	 
	for (index = 0; index < AllScope.length; ++index) {
    var ss=AllScope[index];        
  	document.getElementById(ss).style.display = 'table-row';         
  }
  //�M���R�D��html , �קK�~�e
	$('#show_buttom').html("");

})

//�˵����D
$(".list_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var scope=NewArray[1];
	  
  document.myform.act.value='list_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

})

//�˵����D - �վ�ѵ�
$("#list_paper_answer").click(function(){
  
  document.myform.act.value='list_paper_answer';
  document.myform.opt1.value=document.myform.item_scope.value;
  
  document.myform.submit();

})

//�˵����D - �վ�ѵ��x�s
$("#list_paper_answer_save").click(function(){
  
  document.myform.act.value='list_paper_answer_save';
  document.myform.opt1.value=document.myform.item_scope.value;
  
  document.myform.submit();

})

//�˵����D - �]�w���D����
$("#list_paper_subject").click(function(){
  
  document.myform.act.value='list_paper_subject';
  document.myform.opt1.value=document.myform.item_scope.value;
  
  document.myform.submit();

});

//�˵����D - �]�w���D�����x�s
$("#list_paper_subject_save").click(function(){
  
  document.myform.act.value='list_paper_subject_save';
  document.myform.opt1.value=document.myform.item_scope.value;
  
  document.myform.submit();

});

//�פJ���D
$(".paste_paper").click(function(){
	var btnID=$(this).attr("id");
	var NewArray = new Array();
�@var NewArray = btnID.split("-");
  var scope=NewArray[1];
		
  document.myform.act.value='paste_paper';
  document.myform.opt1.value=scope;
  
  document.myform.submit();

});

//���R���D
$("#paste_paper_submit").click(function(){
	//�]�w opt1 ���Y���, �H�K�C������ܸӻ��
	document.myform.opt1.value=document.myform.item_scope.value;
  document.myform.act.value='paste_paper_submit';

  if (document.myform.paste_items.value=='') {
   alert('���K�J�����r!');
   return false;
  }  
	document.myform.submit();
});

 //���R���D
 $("#paste_paper_composing").click(function(){
	 //�]�w opt1 ���Y���, �H�K�C������ܸӻ��
	 document.myform.opt1.value=document.myform.item_scope.value;
	 document.myform.act.value='paste_paper';
	 document.myform.confirm_collection.value=1;

	 if (document.myform.paste_items.value=='') {
		 alert('���K�J�����r!');
		 return false;
	 }
	 document.myform.submit();
 });

//�x�s���D
$("#paste_paper_save").click(function(){
	//�]�w opt1 ���Y���, �H�K�C������ܸӻ��
	document.myform.opt1.value=document.myform.item_scope.value;
  document.myform.act.value='paste_paper_save';
	document.myform.submit();
});

//������D����
function check_form_item() {

 if (document.myform.question.value=='') {
   alert('�D�F����J!');
   return false;
 }
 if (document.myform.cha.value=='' && document.myform.thefig_a.value=='' && ($("#del_fig_a").length == 0 || $("#del_fig_a").attr('checked'))) {
   alert('���(A)����J!');
   return false; 
 }

 if (document.myform.chb.value=='' && document.myform.thefig_b.value=='' && ($("#del_fig_b").length == 0 || $("#del_fig_b").attr('checked'))) {
   alert('���(B)����J!');
   return false; 
 }

 if (document.myform.chc.value=='' && document.myform.thefig_c.value=='' && ($("#del_fig_c").length == 0 || $("#del_fig_c").attr('checked'))) {
   alert('���(C)����J!');
   return false; 
 }

 if (document.myform.chd.value=='' && document.myform.thefig_d.value=='' && ($("#del_fig_d").length == 0 || $("#del_fig_d").attr('checked'))) {
   alert('���(D)����J!');
   return false; 
 }
 //�ˬd�ѵ����S���I��
 var method =$("input[name='answer']:checked").val(); //radio ���ȡA�`�N�g�k
 if( typeof(method) == "undefined"){ // �`�N�ˬd�����S��������g�k�A�o��O���
   alert( "�п���ѵ��I");
  return false;
 }

 return true;
 
}

 //������D����
 function check_form_item_new() {

	 //�n�� CKEDITOR �����󪽱��ե�, ���M�|���Ĥ@��������, �n���ĤG���~���o�쪺���D
	 var S = CKEDITOR.instances.question;
	 var question=S.getData();

	 if (question=='') {
		 alert('�D�F����J!');
		 $("#question").focus();
		 return false;
	 }

	 var S = CKEDITOR.instances.cha;
	 var cha=S.getData();
	 if (cha=='') {
		 alert('���(A)����J!');
		 $("#cha").focus();
		 return false;
	 }

	 var S = CKEDITOR.instances.chb;
	 var chb=S.getData();
	 if (chb=='') {
		 alert('���(B)����J!');
		 $("#chb").focus();
		 return false;
	 }

	 var S = CKEDITOR.instances.chc;
	 var chc=S.getData();
	 if (chc=='') {
		 alert('���(C)����J!');
		 $("#chc").focus();
		 return false;
	 }

	 var S = CKEDITOR.instances.chd;
	 var chd=S.getData();
	 if (chd=='') {
		 alert('���(D)����J!');
		 $("#chd").focus();
		 return false;
	 }
	 //�ˬd�ѵ����S���I��
	 var method =$("input[name='answer']:checked").val(); //radio ���ȡA�`�N�g�k
	 if( typeof(method) == "undefined"){ // �`�N�ˬd�����S��������g�k�A�o��O���
		 alert( "�п���ѵ��I");
		 return false;
	 }

	 return true;

 }

</Script>