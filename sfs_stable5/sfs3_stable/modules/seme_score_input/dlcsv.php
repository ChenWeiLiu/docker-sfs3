<?php
// $Id: dlcsv.php 7710 2013-10-23 12:40:27Z smallduh $

// �ޤJ SFS3 ���禡�w

include_once "../../include/config.php";

require_once "../../include/sfs_case_studclass.php";

require_once "../../include/sfs_case_score.php";
// �{��
sfs_check();

//
// �z���{���X�Ѧ��}�l


//�����ܼ��ഫ��*****************************************************
$Hseme_year_seme=($_GET['Hseme_year_seme'])?$_GET['Hseme_year_seme']:$_POST['Hseme_year_seme'];
$Hstud_seme_class=($_GET['Hstud_seme_class'])?$_GET['Hstud_seme_class']:$_POST['Hstud_seme_class'];
//$point=($_GET['point'])?$_GET['point']:$_POST['point'];
$ss_id_A=($_GET['ss_id_A'])?$_GET['ss_id_A']:$_POST['ss_id_A'];
$Submit2=($_GET['Submit2'])?$_GET['Submit2']:$_POST['Submit2'];
$Submit3=($_GET['Submit3'])?$_GET['Submit3']:$_POST['Submit3'];
$Submit4=($_GET['Submit4'])?$_GET['Submit4']:$_POST['Submit4'];
$Submit5 = $_REQUEST[Submit5];
//********************************************************************
//�~�Ű}�C
$school_kind_name=array("���X��","�@�~","�G�~","�T�~","�|�~","���~","���~","�@�~","�G�~","�T�~","�@�~","�~�G","�T�~");

//�E�~�@�e�зǻ��ά�ذ}�C
$standard_scope=array(1=>"�y��",2=>"�ƾ�",3=>"�۵M�P�ͬ����",4=>"���|",5=>"���N�P�H��",6=>"�ͬ��ҵ{",7=>"���d�P��|",8=>"��X����",9=>"�u�ʽҵ{",10=>"��`�ͬ����{",11=>"����y��",12=>"�m�g�y��",13=>"�^�y");

//�V�O�{��
$oth_arr_score = array("���{�u��"=>5,"���{�}�n"=>4,"���{�|�i"=>3,"�ݦA�[�o"=>2,"���ݧ�i"=>1);
//�U���ɮ�
if($Hseme_year_seme && $Hstud_seme_class && ($Submit2=="�U�����Z����"|| $Submit3=="�U���U���r�ԭz����"|| $Submit4=="�U����`�ͬ����q����" || $Submit5 == "�U���V�O�{�ת���") && $ss_id_A){
	if ($Submit3 == "�U���U���r�ԭz����"){
		$filename=$Hseme_year_seme."_".$Hstud_seme_class."_memo.csv";
		$C_filename=intval(substr($Hseme_year_seme,0,-1))."�Ǧ~�ײ�".substr($Hseme_year_seme,-1)."�Ǵ�".$school_kind_name[substr($Hstud_seme_class,0,-2)].substr($Hstud_seme_class,-2)."�Z��r�y�z�פJ��";
	}
	elseif ($Submit4 == "�U����`�ͬ����q����"){
		$filename=$Hseme_year_seme."_".$Hstud_seme_class."_nor.csv";
		$C_filename=intval(substr($Hseme_year_seme,0,-1))."�Ǧ~�ײ�".substr($Hseme_year_seme,-1)."�Ǵ�".$school_kind_name[substr($Hstud_seme_class,0,-2)].substr($Hstud_seme_class,-2)."�Z��`�ͬ����q�פJ��";
	}
	elseif ($Submit5 == "�U���V�O�{�ת���"){
		$filename=$Hseme_year_seme."_".$Hstud_seme_class."_study.csv";
		$C_filename=intval(substr($Hseme_year_seme,0,-1))."�Ǧ~�ײ�".substr($Hseme_year_seme,-1)."�Ǵ�".$school_kind_name[substr($Hstud_seme_class,0,-2)].substr($Hstud_seme_class,-2)."�Z�V�O�{�׶פJ��";
	}
	else {
		$filename=$Hseme_year_seme."_".$Hstud_seme_class.".csv";
		$C_filename=intval(substr($Hseme_year_seme,0,-1))."�Ǧ~�ײ�".substr($Hseme_year_seme,-1)."�Ǵ�".$school_kind_name[substr($Hstud_seme_class,0,-2)].substr($Hstud_seme_class,-2)."�Z���Z�פJ��";
	}
    header("Content-disposition: attachment ; filename=$filename");
    header("Content-type: application/octetstream ; Charset=Big5");
    //header("Pragma: no-cache");
				//�t�X SSL�s�u�ɡAIE 6,7,8�U�������D�A�i��ק� 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
    header("Expires: 0");	
    //�Ĥ@��
   
    $table_score = "stud_seme_score";
    $head.=$C_filename."�᭱���Ʀr�d�U�O�ק�ΧR��";	
    if ($Submit4 == "�U����`�ͬ����q����"){
	$head.=",0";
	$table_score = "stud_seme_score_nor";
    }
    elseif ($Submit5 == "�U���V�O�{�ת���"){
	foreach($oth_arr_score as $id2=>$val2)
		$head.=" $id2=$val2 ";
	foreach($ss_id_A as $key0 => $value0)		
		$head.=",$value0"; 
	$table_score = "stud_seme_score_oth";
    }
    else {
	foreach($ss_id_A as $key0 => $value0){		
		$head.=",$value0"; 
	}
     }
	$head.="\n";

	$head.="�Ǹ�,�y��,�m�W";
	if ($Submit4 == "�U����`�ͬ����q����"){
		$head.=",��`�ͬ����q���Z,��`�ͬ����q��r�ԭz";
	}
	else {
		foreach($ss_id_A as $key => $value){
		if ($Submit3 == "�U���U���r�ԭz����")
			$subject_name=ss_id_to_subject_name($value)."-��r�y�z";
		else
			$subject_name=ss_id_to_subject_name($value);

			$head.=",$subject_name"; 
		}
	}
	
	$head.="\n";
	if ($Submit5 == '�U���V�O�{�ת���')
		$query = "select b.student_sn,c.ss_val,c.ss_id from stud_seme a ,stud_base b,$table_score c  where a.stud_id=b.stud_id and b.stud_id=c.stud_id and c.ss_kind='�V�O�{��' and b.stud_study_cond IN (0,5)  and a.seme_year_seme='$Hseme_year_seme' and a.seme_year_seme=c.seme_year_seme  and a.seme_class='$Hstud_seme_class' order by a.seme_num ";
	else
		// ���o���y���Z��
		$query = "select b.student_sn,c.ss_score,c.ss_id,c.ss_score_memo from stud_seme a ,stud_base b,$table_score c  where a.stud_id=b.stud_id and  a.student_sn=b.student_sn and  b.student_sn=c.student_sn and b.stud_study_cond IN(0,5)  and a.seme_year_seme='$Hseme_year_seme' and a.seme_year_seme=c.seme_year_seme  and a.seme_class='$Hstud_seme_class' order by a.seme_num ";
	$rs = $CONN->Execute($query) or die($query);
	$temp_student_arr = array();
	while(!$rs->EOF) {
		if ($Submit5 == '�U���V�O�{�ת���'){
			$temp_score_arr[$rs->fields[student_sn]][$rs->fields[ss_id]] = $oth_arr_score[$rs->fields[ss_val]];
		}
		else{
			$temp_score_arr[$rs->fields[student_sn]][$rs->fields[ss_id]] = $rs->fields[ss_score];
			$temp_score_memo_arr[$rs->fields[student_sn]][$rs->fields[ss_id]] = str_replace("\r\n","",$rs->fields[ss_score_memo]);
		}
		$temp_student_arr[$rs->fields[student_sn]][$rs->fields[ss_id]] = 1;
		$rs->MoveNext();
	}
	
	$sql="select a.stud_id , a.seme_num , b.stud_name,b.student_sn  from stud_seme a,stud_base b where a.stud_id=b.stud_id and a.student_sn=b.student_sn and  b.stud_study_cond IN(0,5) and a.seme_year_seme='$Hseme_year_seme' and a.seme_class='$Hstud_seme_class' order by a.seme_num ";
	$rs=$CONN->Execute($sql) or die($sql);
	$sel_year=intval(substr($Hseme_year_seme,0,-1));
	$sel_seme=substr($Hseme_year_seme,-1);
    $i=0;
	while(!$rs->EOF){
	       	$stud_id=$rs->fields['stud_id'];
		$student_sn=$rs->fields['student_sn'];
		$seme_num=$rs->fields['seme_num'];
		$stud_name=$rs->fields['stud_name'];							
		$head.=$stud_id.",".$seme_num.",\"".$stud_name."\"";
		if ($Submit4 == "�U����`�ͬ����q����"){				
			$head.=",".$temp_score_arr[$student_sn][0].",\"".$temp_score_memo_arr[$student_sn][0]."\""; 
		}else {	
			foreach($ss_id_A as $key1 => $value1){
				//�p�G�O���ɫh�s�W�@��
				if ($temp_student_arr[$student_sn][$value1]<>1){
					$query = "insert into stud_seme_score(seme_year_seme,student_sn,ss_id)values('$Hseme_year_seme',$student_sn,$value1)";	
					$CONN->Execute($query);
				}
				if ($Submit3 == "�U���U���r�ԭz����"){				
					$seme_score = "\"".$temp_score_memo_arr[$student_sn][$value1]."\"";
				}	
				else{
					$seme_score = $temp_score_arr[$student_sn][$value1];				
					if($seme_score<=0) $seme_score="";
				}
				$head.=",".$seme_score; 
			}
		}
		$head.="\n";
        
		$rs->MoveNext();
    }		
	echo $head;
}
else{
	header("Location:creat_table.php?Hseme_year_seme=$Hseme_year_seme&Hstud_seme_class=$Hstud_seme_class");

}
//��ss_id��X��ئW�٪����
function  ss_id_to_subject_name($ss_id){
    global $CONN;
    $sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2);
        $subject_name = $rs2->fields["subject_name"];
    }
    else{
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $subject_name = $rs4->fields["subject_name"];
    }
    return $subject_name;
}
