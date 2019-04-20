<?php
//����class
class chc_seme_advance{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $stu;//�ǥ͸��
	var $subj;//��ذ}�C
	var $rule;//����
	var $TotalSco;//�U�q�Ҥ���
	var $kind;

	//�غc�禡
	function chc_seme_advance($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//��l��
	function init() {}
	//�{��
	function process() {
		$this->all();
	}
	//���
	function display($tpl){
		$ob=new drop($this->CONN);
		$this->select=&$ob->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//�^�����
	function all(){
		if ($_GET[class_id]=='') return;
		$this->class_id=$_GET[class_id];
		$this->stu=$this->get_stu();
		$this->subj=$this->get_subj("seme");
		$this->sco=$this->get_sco($_GET[kind]);
		$this->kind=$this->get_kind($_GET[kind]);

		foreach($this->stu as $sn => $value){
         if(isset($this->sco[$sn]) AND isset($this->stu[$sn])){
            $this->stu[$sn] = $this->stu[$sn]+$this->sco[$sn];
         }
      }

	}
	
	function get_kind($a){

	    if ($a==1)$obj_kind="�w��";
		if ($a==2)$obj_kind="����";
		if ($a==3)$obj_kind="[�w��+����]";
		
		return $obj_kind;
	}	
	
	

/* ���ǥͰ}�C,����stud_base���Pstud_seme��*/
	function get_stu(){
		$CID=split("_",$this->class_id);//093_1_01_01
		$year=$CID[0];
		$seme=$CID[1];
		$grade=$CID[2];//�~��
		$class=$CID[3];//�Z��
		$CID_1=$year.$seme;
		$CID_2=sprintf("%03d",$grade.$class);
		$SQL="select 	a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond  from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
		$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		$obj_stu=array();
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$obj_stu[$ro->student_sn] = get_object_vars($ro);
		}
		return $obj_stu;
	}

/*����ذ}�C�G��score_subject����������W��,��score_ss�������ӯZ���  score_ss��rate���ܥ[�v  print����  need_exam�p��  enable�ϥ�  */
function get_subj($type='') {
	switch ($type) {
		case 'all':
		$add_sql=" ";break;
		case 'seme':
		$add_sql=" and need_exam='1' and enable='1' and  rate > 0  ";break;//�����Z��
		case 'stage':
		$add_sql=" and need_exam='1'  and print='1' and enable='1' and  rate > 0  ";break;//���q��,����
		case 'no_test':
		$add_sql=" and need_exam='1'  and print!='1' and enable='1'  and  rate > 0  ";break; //���άq�Ҫ�
		default:
		$add_sql=" and enable='1'  and  rate > 0  ";break;
	}
	$CID=split("_",$this->class_id);//093_1_01_01
	$year=$CID[0];//094_2_01_04
	$seme=$CID[1];
	$grade=$CID[2];
	$class=$CID[3];
	$CID_1=sprintf("%03d",$year)."_".$seme."_".$grade."_".$class;

	$SQL="select * from score_ss where class_id='$CID_1' $add_sql order by print desc,sort,sub_sort ";
   $rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);

	if ($rs->RecordCount()==0){
		$SQL="select * from score_ss where class_id='' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."' $add_sql order by print desc,sort,sub_sort ";
      $rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		$All_ss=&$rs->GetArray();
	}
	else{$All_ss=&$rs->GetArray();}

		//����ؤ���W��
		$SQL="select subject_id,subject_name from score_subject ";
		$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$subj_name[$ro->subject_id] = $ro->subject_name;
		}

		//���Ҹզ��Ƴ]�w�Ӧ� score_setup ��
		$SQL="SELECT * FROM `score_setup` where  year='".($year+0)."' and  semester='".($seme+0)."' and class_year='".($grade+0)."' ";
		$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL."�i��O���Z�|���]�w");//echo $SQL;
		$score_setup=$rs->GetRowAssoc(FALSE);

//���Z����(�ثe����)
		$this->rule=$this->get_rule($score_setup[rule]);
		//��z��ذ}�C
		$obj_SS=array();
		$ii=count($All_ss);
		for($i=0;$i<$ii;$i++){
			$key=$All_ss[$i][ss_id];//����
			// $obj_SS[$key]=$All_ss[$i];//�����}�C,�Ȥ���
			$obj_SS[$key][rate]=$All_ss[$i][rate];//�[�v
			if ($All_ss[$i]["print"]=='1') {
            $obj_SS[$key][mon_test]=$score_setup[performance_test_times];
         }else{
            $obj_SS[$key][mon_test]='0';
         }
//			$obj_SS[$key][mon_test]=$All_ss[$i]["print"];//�O�_����
			$obj_SS[$key][sc]=$subj_name[$All_ss[$i][scope_id]];//���W��
			$obj_SS[$key][sb]=$subj_name[$All_ss[$i][subject_id]];//��ئW��
			($obj_SS[$key][sb]=='') ? $obj_SS[$key][sb]=$obj_SS[$key][sc]:"";
		}
	return $obj_SS;
	}

	function get_rule($rule) {
		$rule=str_replace(" ","",$rule);
		$rules = explode("\n",$rule);
		for ($i=0; $i<count($rules); $i++){
			$ary[$i]=explode("_", $rules[$i]);}
		return 	$ary;

	}
	//���Ҧ����Z
	function get_sco($a){
		$ss=join(",",array_keys($this->subj));
		$stu=join(",",array_keys($this->stu));
		$YSGC=split("_",$this->class_id);
		$tb="score_semester_".($YSGC[0]+0)."_".($YSGC[1]+0);
		$SQL="select score_id,class_id,student_sn,ss_id,score,test_name,test_kind,test_sort from `$tb` where  student_sn in ($stu) and  ss_id in ($ss) ";
		$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL."�i��O�ҵ{�εL�ǥ͸��");
		$All_sco=&$rs->GetArray();
		
				
		foreach ($All_sco as $sco){
			$sn=$sco[student_sn];
			$ss_id=$sco[ss_id];
			$test_sort=$sco[test_sort];

			if ($a==1)
			{
			 if ($sco[test_kind]=='�w�����q'){
             $kind='mon';
			
             $TotalSco[$sn][$test_sort]+=$sco[score];
             $TotalSco[$sn][TopMarks.'_'.$test_sort]+=100;  //�p��ӥͪ������O�h��
	         }
			}
			
			if ($a==2)
			{
			 if ($sco[test_kind]=='���ɦ��Z'){
             $kind='nor';
             $TotalSco[$sn][$test_sort]+=$sco[score];
             $TotalSco[$sn][TopMarks.'_'.$test_sort]+=100;  //�p��ӥͪ������O�h��
	         }	
			}
			
			
			if($a==3)
			{
				
				$CID=split("_",$this->class_id);//093_1_01_01
			    $year=(int)$CID[0];				
		        $seme=$CID[1];
		        $grade=(int)$CID[2];//�~��
		        $class=$CID[3];//�Z��
				
		      ///////////////////////////////////////////////////////////////		
			  $sql="select * from score_setup where class_year='$grade' and year='$year' and semester='$seme'";
	          $rs=&$this->CONN->Execute($sql) or die("�L�k�d�ߡA�y�k:".$sql."�i��O�ҵ{�εL�ǥ͸��");
	         
			  
			  $score_mode= $rs->fields['score_mode'];
			  
			  $performance_test_times= $rs->fields['performance_test_times'];
			  
	          if ($score_mode=="all" || $performance_test_times==1)
			  {
			  $test_ratio=explode("-",$rs->fields['test_ratio']);
	          $sratio=$test_ratio[0]*0.01;
	          $nratio=$test_ratio[1]*0.01;	
			  }
			  else
			  {
			    $test_rv=explode(",",$rs->fields['test_ratio']);
				
		        for($j=0;$j<$performance_test_times;$j++)
                {
				$jj=$j+1;
			    $rv=explode("-",$test_rv[$j]);
			    $sratioi[$jj]=$rv[0]*0.01;
			    $nratioi[$jj]=$rv[1]*0.01;	
	
		        }		

                $sratio=$sratioi[$test_sort];
	            $nratio=$nratioi[$test_sort];					
				  
			  }
				///////////////////////////////////////////////////////  
	
			 
			 if ($sco[test_kind]=='�w�����q'){
 		
               $TotalSco[$sn][$test_sort]+=$sco[score]*$sratio;
               
	         }
			  
			   if ($sco[test_kind]=='���ɦ��Z'){

              $TotalSco[$sn][$test_sort]+=$sco[score]*$nratio;
              //$TotalSco[$sn][TopMarks.'_'.$test_sort]+=100;  //�p��ӥͪ������O�h��
	          }	
			  
			   $TotalSco[$sn][TopMarks.'_'.$test_sort]+=100;  //�p��ӥͪ������O�h��
			   
			   
			   
			   
			   
			}
			
			
			
         if ($sco[test_kind]=='���ɦ��Z') $kind='nor';
			if ($sco[test_kind]=='���Ǵ�') $kind='all';
			$Vsco[$sn][$ss_id][$test_sort][$kind]=$sco[score];
			//debug_msg("��".__LINE__."�� sco ", $sco);
			//debug_msg("��".__LINE__."�� TopMarks ", $TopMarks);
		}

      foreach ($TotalSco as $sn => $sco){
		  
         $TotalSco[$sn][diff2]="--";
         $TotalSco[$sn][diff3]="--";
         $TotalSco[$sn][sco_order1]="--";
         $TotalSco[$sn][sco_order2]="--";
         $TotalSco[$sn][sco_order3]="--";
         $TotalSco[$sn][diff_order2]="--";
         $TotalSco[$sn][diff_order3]="--";
         $TotalSco[$sn][diff_org_order2]="--";
         $TotalSco[$sn][diff_org_order3]="--";

         $sk=array_keys($sco);
         $start=max($sk);
         $startkey[]=$start;
         while($start>0){
            $pre_test=$start-1;
            if($pre_test>0){
               $diff=$sco[$start]-$sco[$pre_test];
               if($diff>0){  //���i�B�~�p��
                  $GainRate=$diff*100/$TotalSco[$sn][TopMarks.'_'.$start];  //�i�h�B���
                  $GainRate=sprintf("%.2f", $GainRate);
               }else{
                  $GainRate="--";
               }
               $TotalSco[$sn][diff.$start]=$diff;
               $TotalSco[$sn][GainRate.$start]=$GainRate;
               //$TotalSco[$sn][TopMarks.$start]=$sco[$start][TopMarks];
               ${DiffOrder.$start}[$sn]=$diff;
            }
            ${EachSco.$start}[$sn]=$sco[$start];
            $start--;
         }
      }
      //debug_msg("��".__LINE__."�� TotalSco ", $TotalSco);

      $start=max($startkey);
      while($start>0){
         //�U�q�ҦW���ƦW
         arsort(${EachSco.$start});
         //$pre=$start-1;
         $sco_order=0;  //�����ƦW
         $pre_value=0;
         $sco_order_add=0;
         foreach(${EachSco.$start} as $key => $value){  //�i�h�B�W��
            if($pre_value==$value){
               $sco_order_add++;
            }else{
               $sco_order=$sco_order+$sco_order_add;
               $sco_order_add=0;
               $sco_order++;
            }
            $TotalSco[$key][sco_order.$start]=$sco_order;

            $pre_value=$value;

         }

         $start--;
      }


      $start=max($startkey);

      foreach($TotalSco as $sn => $value){
         if($TotalSco[$sn][sco_order3]!="--" AND $TotalSco[$sn][sco_order2]!="--"){
            $TotalSco[$sn][diff_order3]=($TotalSco[$sn][sco_order3]-$TotalSco[$sn][sco_order2])*(-1);  //�i�h�B�W��
         }
         if($TotalSco[$sn][sco_order2]!="--" AND $TotalSco[$sn][sco_order1]!="--"){
            $TotalSco[$sn][diff_order2]=($TotalSco[$sn][sco_order2]-$TotalSco[$sn][sco_order1])*(-1);  //�i�h�B�W��
         }
      }

      return $TotalSco;

   }

	//�Ǧ^�ӥ͸Ӭ�Ӷ��q���Z//
	function sco($sn,$ss,$test_sort,$kind){
		$sco=ceil($this->sco[$sn][$ss][$test_sort][$kind]);
		if ($sco < 60) { return "<font color=#FF0000> $sco</font>";}
		else{	return $sco;}
	}
	//�Ǧ^�ӥͤ�`���Z//
	function sco_nor($sn){
		$sco=ceil($this->sco[$sn][nor]);
		if ($sco < 60) { return "<font color=#FF0000> $sco</font>";}
		else{	return $sco;}
	}


}


?>