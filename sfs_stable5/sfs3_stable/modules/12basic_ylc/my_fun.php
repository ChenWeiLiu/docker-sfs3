<?php
//���ͬ�ئW��
function get_exam_subject_data($nature)
{
	$exam_array=array('W'=>'�g�@','C'=>'���','E'=>'�^�y','M'=>'�ƾ�','S'=>'���|','N'=>'�۵M');
	switch ($nature) {
		case 0:
			$result=$exam_array;
			break;
		case 1:
			foreach($exam_array as $key=>$value) $result.="$value,";
			break;
		case 2:
			foreach($exam_array as $key=>$value) $result.="<td>$value</td>";
			break;
	}
	return $result;	
}

function get_recent_semester_select($select_name,$default)
{
	$seme_list=get_class_seme();
	$recent_semester="<select name='$select_name' onchange='this.form.submit()'>";
	$curr_year=curr_year();
	foreach($seme_list as $key=>$value){
		$thisyear=substr($key,0,-1);
		$thisseme=substr($key,-1);
//		if($thisseme==2)
		if($curr_year-$thisyear<3)
		$recent_semester.="<option ".($key==$default?"selected":"")." value=$key>$value</option>";
	}
	$recent_semester.="</select>";
	
	return $recent_semester;	

}


function get_semester_graduate_select($select_name,$work_year_seme,$graduate_year,$default)
{
	//���X�Z�ŦW�ٰ}�C
	$class_base=class_base($work_year_seme);
	
	$class_list="<select name='$select_name' onchange='this.form.submit()'><option value=''>*�п�ܯZ��*</option>";
	foreach($class_base as $key=>$value){
		$class_year=substr($key,0,-2);
		if($class_year==$graduate_year){
			$selected=($default==$key)?'selected':'';
			if($selected) $class_id=$key;
			$class_list.="<option value=$key $selected>$value</option>";	
		}
	}
	$class_list.="</select>";
	return $class_list;
}

function get_csv_reference($method=0)
{
	global $UPLOAD_PATH;
	$file=$UPLOAD_PATH."12basic_ylc/aspiration.csv";
	if(file_exists($file))
	{
		$fd=fopen($file,"r");
		rewind($fd);
		$i=0;
		while($tt=sfs_fgetcsv($fd,2000,",")) {
			if($i>0){
				if($method) {
					$result[$tt[0]]=$tt[1];
				} else $result[]=$tt;
			} else $i++;
		}
	}
	return $result;	
}

//���w�Ǧ~�w�g�}�C���ǥͲM��
function get_student_list($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$listed=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$listed[$student_sn]=$student_sn;
		$recordSet->MoveNext();
	}
	return $listed;	
}

function get_student_aspiration($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,aspiration,aspiration_datetime,aspiration_memo from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$aspiration=array();
	while(!$recordSet->EOF)
	{
		if($recordSet->fields['aspiration']){
			$student_sn=$recordSet->fields['student_sn'];
			$aspiration[$student_sn]['aspiration_datetime']=$recordSet->fields['aspiration_datetime'];
			$aspiration[$student_sn]['aspiration_memo']=$recordSet->fields['aspiration_memo'];
			$aspiration[$student_sn]['aspiration_original']=$recordSet->fields['aspiration'];
			$aspiration_data=explode("\r\n",$recordSet->fields['aspiration']);
			foreach($aspiration_data as $key=>$value) $aspiration[$student_sn]['aspiration'][$key]=$value;
		}
		$recordSet->MoveNext();
	}
	return $aspiration;	
}

function get_student_kind_id($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,score_remote,score_kind_id,kind_id_memo from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$kind_id=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$kind_id[$student_sn]['remote']=$recordSet->fields['score_remote'];
		$kind_id[$student_sn]['kind_id']=$recordSet->fields['score_kind_id'];
		$kind_id[$student_sn]['score']=$recordSet->fields['score_remote']+$recordSet->fields['score_kind_id'];
		$kind_id[$student_sn]['kind_id_memo']=$recordSet->fields['kind_id_memo'];
		$recordSet->MoveNext();
	}
	return $kind_id;	
}

function get_student_kind_free($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,card_no,kind_id,disability_id,free_id from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$kind_free=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$kind_free[$student_sn]['card_no']=$recordSet->fields['card_no'];
		$kind_free[$student_sn]['kind_id']=$recordSet->fields['kind_id'];
		$kind_free[$student_sn]['disability_id']=$recordSet->fields['disability_id'];
		$kind_free[$student_sn]['free_id']=$recordSet->fields['free_id'];
		$recordSet->MoveNext();
	}
	return $kind_free;	
}


function get_student_disadvantage($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,score_remote,score_disadvantage,disadvantage_memo from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$disadvantage=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$disadvantage[$student_sn]['remote']=$recordSet->fields['score_remote'];
		$disadvantage[$student_sn]['disadvantage']=$recordSet->fields['score_disadvantage'];
		$disadvantage[$student_sn]['score']=$recordSet->fields['score_remote']+$recordSet->fields['score_disadvantage'];
		$disadvantage[$student_sn]['disadvantage_memo']=$recordSet->fields['disadvantage_memo'];
		$recordSet->MoveNext();
	}
	return $disadvantage;	
}


function get_student_balance($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,score_balance_health,score_balance_art,score_balance_complex from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$diversification=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$diversification[$student_sn]['score_balance_health']=$recordSet->fields['score_balance_health'];
		$diversification[$student_sn]['score_balance_art']=$recordSet->fields['score_balance_art'];
		$diversification[$student_sn]['score_balance_complex']=$recordSet->fields['score_balance_complex'];
		$diversification[$student_sn]['score']=$recordSet->fields['score_balance_health']+$recordSet->fields['score_balance_art']+$recordSet->fields['score_balance_complex'];
		$diversification[$student_sn]['balance_memo']=$recordSet->fields['balance_memo'];
		$recordSet->MoveNext();
	}
	return $diversification;	
}


function get_student_diversification($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,score_balance_health,score_balance_art,score_balance_complex,score_competetion,score_fitness,diversification_memo from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$diversification=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$diversification[$student_sn]['score_balance_health']=$recordSet->fields['score_balance_health'];
		$diversification[$student_sn]['score_balance_art']=$recordSet->fields['score_balance_art'];
		$diversification[$student_sn]['score_balance_complex']=$recordSet->fields['score_balance_complex'];

		$diversification[$student_sn]['score_competetion']=$recordSet->fields['score_competetion'];
		$diversification[$student_sn]['score_fitness']=$recordSet->fields['score_fitness'];
		$diversification[$student_sn]['score']=$recordSet->fields['score_balance_health']+$recordSet->fields['score_balance_art']+$recordSet->fields['score_balance_complex']+$recordSet->fields['score_competetion']+$recordSet->fields['score_fitness'];
		$diversification[$student_sn]['diversification_memo']=$recordSet->fields['diversification_memo'];
		$recordSet->MoveNext();
	}
	return $diversification;
}

function get_student_moral($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,score_reward,score_absence,score_fault,moral_memo from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$moral=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$moral[$student_sn]['score_reward']=$recordSet->fields['score_reward'];
		$moral[$student_sn]['score_fault']=$recordSet->fields['score_fault'];
		$moral[$student_sn]['score_absence']=$recordSet->fields['score_absence'];
		$moral[$student_sn]['moral_memo']=$recordSet->fields['moral_memo'];
		$recordSet->MoveNext();
	}
	return $moral;
}


function get_graduate_data($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,grad_kind from grad_stud where stud_grad_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$graduate=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$graduate[$student_sn]=$recordSet->fields['grad_kind'];
		$recordSet->MoveNext();
	}
	return $graduate;
}

//�ǥ;��~�Ǵ����g����
function count_student_reward($student_sn)
{
	global $CONN,$reward_semester,$fault_start_semester;
	$reward=array();
	$sql="SELECT reward_year_seme,reward_kind,reward_cancel_date FROM reward WHERE student_sn='$student_sn' AND reward_bonus=1 AND reward_year_seme IN ($reward_semester) ORDER BY reward_year_seme";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	while(!$res->EOF) {
		$reward_year_seme=$res->fields['reward_year_seme'];
		$reward_kind=$res->fields['reward_kind'];
		switch ($reward_kind) {
			case 1:
				$reward[$reward_year_seme][1]++;
				break;
			case 2:
				$reward[$reward_year_seme][1]+=2;
				break;
			case 3:
				$reward[$reward_year_seme][3]++;
				break;
			case 4:
				$reward[$reward_year_seme][3]+=2;
				break;
			case 5:
				$reward[$reward_year_seme][9]++;
				break;
			case 6:
				$reward[$reward_year_seme][9]+=2;
				break;
			case 7:
				$reward[$reward_year_seme][9]+=3;
				break;
			case -1:
				$reward[$reward_year_seme][-1]++;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) {$reward['fault_cancel'][-1]++;}
				break;
			case -2:
				$reward[$reward_year_seme][-1]+=2;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-1]+=2;
				break;
			case -3:
				$reward[$reward_year_seme][-3]++;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-3]++;
				break;
			case -4:
				$reward[$reward_year_seme][-3]+=2;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-3]+=2;
				break;
			case -5:
				$reward[$reward_year_seme][-9]++;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-9]++;
				break;
			case -6:
				$reward[$reward_year_seme][-9]+=2;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-9]+=2;
				break;
			case -7:
				$reward[$reward_year_seme][-9]+=3;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-9]+=3;
				break;
		}
		$res->MoveNext();
	}
	return $reward;
}


//�ǥͼ��g����(��Ǵ��έp)
function count_student_allReward($student_sn)
{
	global $CONN,$reward_semester,$fault_start_semester;
	$reward=array();
	$sql="SELECT reward_year_seme,reward_kind,reward_cancel_date FROM reward WHERE student_sn='$student_sn' AND reward_bonus=1 AND reward_year_seme IN ($reward_semester) ORDER BY reward_year_seme";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	while(!$res->EOF) {
		$reward_year_seme=$res->fields['reward_year_seme'];
		$reward_kind=$res->fields['reward_kind'];
		switch ($reward_kind) {
			case 1:
				$reward[1]++;
				break;
			case 2:
				$reward[1]+=2;
				break;
			case 3:
				$reward[3]++;
				break;
			case 4:
				$reward[3]+=2;
				break;
			case 5:
				$reward[9]++;
				break;
			case 6:
				$reward[9]+=2;
				break;
			case 7:
				$reward[9]+=3;
				break;
			case -1:
				$reward[-1]++;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) {$reward['fault_cancel'][-1]++;}
				break;
			case -2:
				$reward[-1]+=2;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-1]+=2;
				break;
			case -3:
				$reward[-3]++;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-3]++;
				break;
			case -4:
				$reward[-3]+=2;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-3]+=2;
				break;
			case -5:
				$reward[-9]++;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-9]++;
				break;
			case -6:
				$reward[-9]+=2;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-9]+=2;
				break;
			case -7:
				$reward[-9]+=3;
				if($res->fields['reward_cancel_date']!='0000-00-00' && $res->fields['reward_year_seme']>=$fault_start_semester) $reward['fault_cancel'][-9]+=3;
				break;
		}
		$res->MoveNext();
	}
	return $reward;
}


//�ǥͼ��g����
function get_student_reward_list($student_sn)
{
	global $CONN,$reward_semester,$fault_start_semester;
	$reward=array();
	$sql="SELECT reward_kind,reward_year_seme,reward_date,reward_reason,reward_base,reward_cancel_date,reward_bonus FROM reward WHERE student_sn='$student_sn' AND reward_year_seme IN ($reward_semester) ORDER BY reward_year_seme,reward_date";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$i=1;
	while(!$res->EOF) {
		$reward[$i]['reward_kind']=$res->fields['reward_kind'];		//���g���O
		$reward[$i]['reward_year_seme']=$res->fields['reward_year_seme'];		//�Ǵ��O
		$f = explode(",",$reward_semester);
		if($res->fields['reward_year_seme']==substr($f[0],1,-1)) $reward[$i]['reward_grade']="7-1"; elseif($res->fields['reward_year_seme']==substr($f[1],1,-1)) $reward[$i]['reward_grade']="7-2"; elseif($res->fields['reward_year_seme']==substr($f[2],1,-1)) $reward[$i]['reward_grade']="8-1"; elseif($res->fields['reward_year_seme']==substr($f[3],1,-1)) $reward[$i]['reward_grade']="8-2"; elseif($res->fields['reward_year_seme']==substr($f[4],1,-1)) $reward[$i]['reward_grade']="9-1"; else $reward[$i]['reward_grade']="";		//�~��
		$reward[$i]['reward_date']=$res->fields['reward_date'];		//���g���
		$reward[$i]['reward_reason']=$res->fields['reward_reason'];		//���g�ƥ�
		$reward[$i]['reward_base']=$res->fields['reward_base'];		//���g�̾�
		if(($res->fields['reward_kind']<=-1)&&($res->fields['reward_cancel_date']!='0000-00-00')) $reward[$i]['reward_cancel_date']=$res->fields['reward_cancel_date']; else $reward[$i]['reward_cancel_date']="";		//�P�L���
		$reward[$i]['mark']=($res->fields['reward_bonus']==1 || $res->fields['reward_kind']<=-1)?'V':'';		//�İO
		$i++;
		$res->MoveNext();
	}
	return $reward;
}


//�ǥͼ��g����
function get_student_reward($sn_array)
{
	global $CONN,$fault_none,$fault_warning,$fault_peccadillo,$reward_score,$reward_score_max,$reward_semester,$fault_start_semester;
	$reward=array();
	foreach($sn_array as $student_sn){
		//�w�]��
		$reward[$student_sn]['bonus'][1]=$fault_none;
		$reward[$student_sn]['bonus'][2]=0;		
		//����ǥͥ��P�L�����g���� (���Y�ۼ��g������l��ƶi����έp �����Ǵ��έp�� �Ъ`�N��ǥͬ����ɵn���D)
		$sql="SELECT reward_year_seme,reward_kind FROM reward WHERE student_sn='$student_sn' AND reward_cancel_date='0000-00-00' AND reward_bonus=1 AND reward_year_seme IN ($reward_semester) ORDER BY reward_year_seme";
		$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
		while(!$res->EOF)
		{
			$reward_kind=$res->fields['reward_kind'];
			//if($reward_kind<0) $reward[$student_sn]['fault']++;  //�p��ǹL����
			if(($reward_kind<0)&&($res->fields['reward_year_seme']>=$fault_start_semester)) $reward[$student_sn]['fault']++;  //�p��ǹL����	
			
			switch ($reward_kind) {
				case 1:
					$reward[$student_sn][1]++;
					break;
				case 2:
					$reward[$student_sn][1]+=2;
					break;
				case 3:
					$reward[$student_sn][3]++;
					break;
				case 4:
					$reward[$student_sn][3]+=2;
					break;
				case 5:
					$reward[$student_sn][9]++;
					break;
				case 6:
					$reward[$student_sn][9]+=2;
					break;
				case 7:
					$reward[$student_sn][9]+=3;
					break;
				case -1:
					//$reward[$student_sn][-1]++;
					if($res->fields['reward_year_seme']>=$fault_start_semester) {$reward[$student_sn][-1]++;}
					break;
				case -2:
					//$reward[$student_sn][-1]+=2;
					if($res->fields['reward_year_seme']>=$fault_start_semester) {$reward[$student_sn][-1]+=2;}
					break;
				case -3:
					//$reward[$student_sn][-3]++;
					if($res->fields['reward_year_seme']>=$fault_start_semester) {$reward[$student_sn][-3]++;}
					break;
				case -4:
					//$reward[$student_sn][-3]+=2;
					if($res->fields['reward_year_seme']>=$fault_start_semester) {$reward[$student_sn][-3]+=2;}
					break;
				case -5:
					//$reward[$student_sn][-9]++;
					if($res->fields['reward_year_seme']>=$fault_start_semester) {$reward[$student_sn][-9]++;}
					break;
				case -6:
					//$reward[$student_sn][-9]+=2;
					if($res->fields['reward_year_seme']>=$fault_start_semester) {$reward[$student_sn][-9]+=2;}
					break;
				case -7:
					//$reward[$student_sn][-9]+=3;
					if($res->fields['reward_year_seme']>=$fault_start_semester) {$reward[$student_sn][-9]+=3;}
					break;
			}
			$res->MoveNext();
		}
	}
	//�L�O�L�����P�w�Ť�
	foreach($reward as $student_sn=>$data){
		if(!$reward[$student_sn]['fault']) $reward[$student_sn]['bonus'][1]=$fault_none; else
			if($reward[$student_sn][-9] or $reward[$student_sn][-3]) $reward[$student_sn]['bonus'][1]=0; else
				if($reward[$student_sn][-1]>=3) $reward[$student_sn]['bonus'][1]=0; else
					$reward[$student_sn]['bonus'][1]=$fault_warning;
		
		//���y�O���P�w�Ť�
		$reward[$student_sn]['bonus'][2]=$reward[$student_sn][1]*$reward_score[1]+$reward[$student_sn][3]*$reward_score[3]+$reward[$student_sn][9]*$reward_score[9];
		$reward[$student_sn]['bonus'][2]=min($reward[$student_sn]['bonus'][2],$reward_score_max);	//���̰���
	}
	return $reward;
}


//�ǥ;��~�Ǵ��X�ʮu����
function count_student_seme_abs($stud_id)
{
	global $CONN,$absence_semester;
	$absence=array();
	$sql="SELECT seme_year_seme,abs_days FROM stud_seme_abs WHERE stud_id={$stud_id} AND abs_kind=3 AND seme_year_seme IN ($absence_semester) ORDER BY seme_year_seme";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	while(!$res->EOF) {
		$seme_year_seme=$res->fields['seme_year_seme'];
		$abs_days=$res->fields['abs_days'];
		$absence[$seme_year_seme]=$abs_days;
		$res->MoveNext();
	}
	return $absence;
}

//�X�ʮu�έp��
function get_student_seme_abs($absence_semester)
{
	global $CONN,$absence_score_array;
	$absence=array();
	//����ǥ��m�Ҳέp
	$sql="SELECT stud_id,SUM(abs_days) FROM stud_seme_abs WHERE abs_kind=3 AND seme_year_seme IN ($absence_semester) GROUP BY stud_id";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	while(!$res->EOF)
	{
		$stud_id=$res->fields[0];
		$counter=$res->fields[1];
		$absence[$stud_id]=$absence_score_array[$counter];
		$res->MoveNext();
	}
	return $absence;
}

//�ǥ;��~�Ǵ����žǲߤ���-����health,����art,��Xcomplex
function get_student_score_balance($student_sn)
{
	global $CONN,$work_year_seme,$graduate_year,$balance_semester,$balance_area;
	$balance=array();
	$fin_score=cal_fin_score(array($student_sn),$balance_semester);
	//�O������
	foreach($fin_score as $student_sn=>$score_data) {
		foreach($balance_area as $key=>$value) {
			foreach($balance_semester as $key2=>$value2) {
				$balance[$value][$value2]=$score_data[$value][$value2]['score'];
			}
			$balance[$value]['avg']=$score_data[$value]['avg']['score'];
		}
	}
	return $balance;
}

//���žǲ�-����health,����art,��Xcomplex
function count_student_score_balance($sn)
{
	global $CONN,$work_year_seme,$graduate_year,$balance_score,$balance_score_max,$balance_semester,$balance_area;
	$score_balance=array();
	$fin_score=cal_fin_score($sn,$balance_semester);
	
	//�P�w�Ť�
	foreach($fin_score as $student_sn=>$score_data)
	{
		foreach($balance_area as $key=>$value)
		{
			$score_balance[$student_sn][$value]=($score_data[$value]['avg']['score']>=60)?$balance_score:0;
		}
	}	
	return $score_balance;	
}

//�ǥ;��~�Ǵ��v�ɬ���
function get_student_score_competetion($student_sn)
{
	global $CONN,$work_year_seme,$race_score;
	$competetion=array();
	$sql="SELECT level,squad,name,rank,certificate_date,sponsor,word,weight,year FROM career_race WHERE student_sn='{$student_sn}' AND level<=4 ORDER BY certificate_date";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$i=1;
	while(!$res->EOF) {
		$competetion[$i]['level']=$res->fields['level'];		//�d��
		$competetion[$i]['squad']=$res->fields['squad'];		//�ʽ�
		$competetion[$i]['year']=$res->fields['year'];                //�Ǧ~��
		$competetion[$i]['name']=$res->fields['name'];		//�v�ɦW��
		$competetion[$i]['rank']=$res->fields['rank'];		//�W��
		$competetion[$i]['certificate_date']=$res->fields['certificate_date'];		//�ҮѤ��
		$competetion[$i]['sponsor']=$res->fields['sponsor'];		//�D����
		$competetion[$i]['word']=$res->fields['word'];		//�ҮѦr��
		$competetion[$i]['weight']=$res->fields['weight'];		//�v��
		//���v�ɩʽ���v���p��
		if($res->fields['squad']==1) {		//�ӤH��
			if($res->fields['weight']==1) $competetion[$i]['score']=$race_score[$res->fields['level']][$res->fields['rank']];		//����
			$competetion[$i]['mark']='V';		//�İO
			
		} elseif($res->fields['squad']==2) {		//������
			switch($res->fields['weight']) {
				case 0.5:		//4�H�H�W
					$competetion[$i]['score']=$race_score[$res->fields['level']][$res->fields['rank']]*$res->fields['weight'];		//����
					$competetion[$i]['mark']='V';		//�İO
					break;
				case 0.25:		//20�H�H�W
					$competetion[$i]['score']=$race_score[$res->fields['level']][$res->fields['rank']]*$res->fields['weight'];		//����
					$competetion[$i]['mark']='V';		//�İO
					break;
			}
		} else {
			$competetion[$i]['score']='';		//����
			$competetion[$i]['mark']='';		//�İO
		}
		$i++;
		$res->MoveNext();
	}
	return $competetion;
}

//�v�ɬ���
function count_student_score_competetion()
{
	global $CONN,$work_year_seme,$race_score;
	$score_competetion=array();
	
	$sql_select="select student_sn,level,squad,rank,weight from career_race where level<=4";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡA���i��O���w�˥ͲP���ɬ����ҲաI<br>$sql_select",256);
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields[0];
		$level=$recordSet->fields[1];		//�v�ɽd��
		$squad=$recordSet->fields[2];		//�v�ɩʽ�G�ӤH��1,������2
		$rank=$recordSet->fields[3];		//�o���W��
		$weight=$recordSet->fields[4];		//�v��1,0.5,0.25
		//���v�ɩʽ���v���p��
		if($squad==1) {		//�ӤH��
			if($weight==1) $score_competetion[$student_sn]+=$race_score[$level][$rank];
		} else {		//������
			switch($weight) {
				case 0.5:		//4�H�H�W
					$score_competetion[$student_sn]+=$race_score[$level][$rank]*$weight;
					break;
				case 0.25:		//20�H�H�W
					$score_competetion[$student_sn]+=$race_score[$level][$rank]*$weight;
					break;
			}
		}
		/***
		if(($weight>=0)&&($weight<=1)) {
			$score_competetion[$student_sn]+=$race_score[$level][$rank]*$weight;
		} else {
			$score_competetion[$student_sn]+=$race_score[$level][$rank];
		}
		***/
		$recordSet->MoveNext();
	}
	
	return $score_competetion;	
}

//�ǥ;��~�Ǵ���A�����
function get_student_score_fitness($student_sn)
{
	global $CONN,$work_year_seme,$fitness_score_one,$fitness_score_one_max,$fitness_addon,$fitness_semester,$work_year_seme;
	$fitness=array();
	$sql="SELECT test1,prec1,test2,prec2,test3,prec3,test4,prec4,c_curr_seme FROM fitness_data WHERE student_sn=$student_sn AND c_curr_seme IN ($fitness_semester) ORDER BY c_curr_seme";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$medal='';		//����
	while(!$res->EOF) {
		$g=0;		//��
		$s=0;		//��
		$c=0;		//��
		$passed=0;		//�q�L���ئ���
		for($i=1;$i<=4;$i++) {
			$fitness[$res->fields['c_curr_seme']]['test'.$i]=$res->fields['test'.$i];		//����
			$fitness[$res->fields['c_curr_seme']]['prec'.$i]=$res->fields['prec'.$i];		//�ʤ���
			if($res->fields['prec'.$i]>=85) $g++;		//�����,85%�H�W
			if($res->fields['prec'.$i]>=75) $s++;		//�Ƚ��,75%�H�W
			if($res->fields['prec'.$i]>=50) $c++;		//�ɽ��,50%�H�W
			if($res->fields['prec'.$i]>=25) $passed++;  //�q�L���e�з�,25%�H�W
		}
		//�P�w����
		if($g==4) {
			$fitness[$res->fields['c_curr_seme']]['medal']="gold";
			$medal="gold";
		} elseif($s==4) {
			$fitness[$res->fields['c_curr_seme']]['medal']="silver";
			if ($meadl!="gold") $medal = "silver";
		} elseif($c==4) {
			$fitness[$res->fields['c_curr_seme']]['medal']="copper";
			if (($medal!="gold")&&($medal!="silver")) $medal = "copper";
		} else {
			$fitness[$res->fields['c_curr_seme']]['medal']="no";
		}
		$res->MoveNext();
	}
	$fitness['avg']['medal']=$medal;
	
	return $fitness;
}

//��A�����
function count_student_score_fitness($sn_array)
{
	global $CONN,$work_year_seme,$fitness_score_one,$fitness_score_one_max,$fitness_addon,$fitness_semester,$work_year_seme;
	$score_fitness=array();
	foreach($sn_array as $student_sn){
		//$sql_select="SELECT prec1,prec2,prec3,prec4 FROM fitness_data WHERE student_sn=$student_sn AND c_curr_seme='$work_year_seme'";
		$sql_select="SELECT prec1,prec2,prec3,prec4,c_curr_seme FROM fitness_data WHERE student_sn=$student_sn AND c_curr_seme IN ($fitness_semester) ORDER BY c_curr_seme";
		$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡA���i��O���w����A��(fitness)�ҲաI<br>$sql_select",256);
		$passed=0;		//�q�L���ئ���
		$arr_passed=array(0,0,0,0);		//�U���سq�L25%����
		$medal='';		//����
		while(!$recordSet->EOF) {
			$g=0;		//��
			$s=0;		//��
			$c=0;		//��
			for($i=0;$i<=3;$i++) {
				$my_pre=$recordSet->fields[$i];
				if($my_pre>=85) $g++;
				if($my_pre>=75) $s++;
				if($my_pre>=50) $c++;
				if($my_pre>=25) $arr_passed[$i]=1;		//�q�L���e�з�  �{���{�]��25%�H�W
			}
			//�P�w����
			if($g==4) $medal="gold"; elseif(($s==4)&&($medal!="gold")) $medal="silver"; elseif(($c==4)&&($medal!="gold")&&($medal!="silver")) $medal="copper";
			
			$recordSet->MoveNext();
		}
		//�P�w�Ť�
		for($i=0;$i<=3;$i++) {
			if($arr_passed[$i]==1) $passed++;
		}
		$score_fitness[$student_sn]=min($fitness_score_one_max,$fitness_score_one*$passed);
		$score_fitness[$student_sn]+=$fitness_addon[$medal];	
	}
	return $score_fitness;
}

function get_student_association()
{
	global $CONN,$work_year_seme,$graduate_year,$association_semester_score_qualtified,$association_semester_score,$association_score_max;
	$association=array();
	$sql="SELECT * FROM association where student_sn IN (select student_sn from stud_seme where seme_year_seme='$work_year_seme' and seme_class like '$graduate_year%') ORDER BY student_sn,seme_year_seme,score";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	while(!$res->EOF)
	{
		$sn=$recordSet->fields['student_sn'];
		$semester=$res->fields['seme_year_seme'];
		$association[$sn][$semester][$i]['name']=$res->fields['association_name'];
		$association[$sn][$semester][$i]['score']=$res->fields['score'];
		$res->MoveNext();
	}
	//�P�w�Ť�
	foreach($association as $student_sn=>$semester_data){
		$i=count($semester_data);
		if($i>=$association_semester_count){	//�C�ӾǴ����F��ѥ[���μƶq�~�p��
			foreach($semester_data as $seme=>$index){
				foreach($index as $key=>$value){
					$my_score=$value['score'];
					if($my_score>=$association_semester_score_qualtified) $association[$student_sn]['bonus']+=$association_semester_score;   //���Z�F��зǴN�p��
				}
			}
		}
		$association[$student_sn]['bonus']=($association[$student_sn]['bonus']>$association_score_max)?$association_score_max:$association[$student_sn]['bonus']; //���̰���	
		
	}
	return $association;	
}


function get_student_service()
{
	global $CONN,$work_year_seme,$graduate_year,$service_semester_minutes,$service_semester_score;
	$service=array();
	$sql="SELECT a.student_sn,a.minutes,b.year_seme FROM stud_service_detail a INNER JOIN stud_service b ON a.item_sn=b.sn WHERE a.student_sn IN (select student_sn from stud_seme where seme_year_seme='$work_year_seme' and seme_class like '$graduate_year%') ORDER BY student_sn,year_seme";
	$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	while(!$res->EOF)
	{
		$sn=$recordSet->fields['student_sn'];
		$semester=$res->fields['seme_year_seme'];
		$minutes=$res->fields['minutes'];
		$service[$sn][$semester]+=$minutes;
		$res->MoveNext();
	}
	//�P�w�Ť�
	foreach($service as $student_sn=>$semester){
		foreach($semester as $seme=>$minutes){
				if($minutes>=$service_semester_minutes) $service[$sn]['bonus']+=$service_semester_score;   //�A�Ȥ����ƹF��зǴN�p��
		}
		$service[$student_sn]['bonus']=($service[$student_sn]['bonus']>$service_score_max)?$service_score_max:$service[$student_sn]['bonus'];	//���̰���
	}
	return $service;	
}

//���o�ǥͰ򥻸��
function get_student_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$student_data=array();
	$sql="SELECT a.sn,b.*,year(b.stud_birthday)-1911 AS birth_year,month(b.stud_birthday) AS birth_month,day(b.stud_birthday) AS birth_day FROM 12basic_ylc a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$fields=$rs->fields;
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		foreach($fields as $key=>$value) 
		{
			$student_data[$student_sn][$key]=$rs->fields[$key];
		}
		$rs->MoveNext();
	}
	return $student_data;
}

//���o���@�H���
function get_domicile_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$domicile_data=array();
	$sql="SELECT a.sn,b.* FROM 12basic_ylc a INNER JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$fields=$rs->fields;
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		foreach($fields as $key=>$value) 
		{
			$domicile_data[$student_sn][$key]=$rs->fields[$key];
		}
		$rs->MoveNext();
	}
	return $domicile_data;
}

function get_exam_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$exam_data=array();
	$sql="SELECT student_sn,score_exam_w,score_exam_c,score_exam_m,score_exam_e,score_exam_s,score_exam_n,exam_memo FROM 12basic_ylc WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$fields=$rs->fields;
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		foreach($fields as $key=>$value) 
		{
				$exam_data[$student_sn][$key]=$rs->fields[$key];
		}
		$rs->MoveNext();
	}
	return $exam_data;
}

function get_student_personality($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$exam_data=array();
	$sql="SELECT student_sn,score_my_aspiration,score_domicile_suggestion,score_guidance_suggestion,personality_memo FROM 12basic_ylc WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$fields=$rs->fields;
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		foreach($fields as $key=>$value) 
		{
			$personality_data[$student_sn][$key]=$rs->fields[$key];
		}
		$rs->MoveNext();
	}
	return $personality_data;
}

//���o12basic_ylc�������
function get_final_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$final_data=array();
	$sql="SELECT * FROM 12basic_ylc WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$fields=$rs->fields;
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		foreach($fields as $key=>$value) 
		{
				$final_data[$student_sn][$key]=$rs->fields[$key];
		}
		$rs->MoveNext();
	}
	return $final_data;
}


function get_pic($stud_study_year,$stud_id)
{
	global $UPLOAD_PATH,$UPLOAD_URL,$pic_width;
	$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$stud_id; 
	if (file_exists($img)) $img_link="<img src='".$UPLOAD_URL."photo/student/".$stud_study_year."/".$stud_id."' width=$pic_width><br>"; else $img_link='';

	return $img_link;
}

function get_student_id($academic_year)
{
	global $CONN;
	//���o�e�w�}�C�ǥ͸��
	$sql_select="select student_sn,kind_id,disability_id,free_id,id_memo,language_certified from 12basic_ylc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("Ū�����ѡI<br>$sql_select",256);
	$kind_free=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$kind_free[$student_sn]['kind_id']=$recordSet->fields['kind_id'];
		$kind_free[$student_sn]['disability_id']=$recordSet->fields['disability_id'];		
		$kind_free[$student_sn]['free_id']=$recordSet->fields['free_id'];
		$kind_free[$student_sn]['id_memo']=$recordSet->fields['id_memo'];
		$kind_free[$student_sn]['language_certified']=$recordSet->fields['language_certified'];
		
		$recordSet->MoveNext();
	}
	return $kind_free;	
}

//���o���~�~��
function get_student_graduationYear($student_sn)
{
	global $CONN;
	$sql="SELECT seme_year_seme FROM stud_seme WHERE student_sn='$student_sn' ORDER BY seme_year_seme DESC LIMIT 1";
	$rs=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	$graduation_year = substr($rs->fields['seme_year_seme'],0,-1);
	
	return $graduation_year;
}

function get_editable_sn($work_year)
{
	global $CONN;
	$editable_sn=array();
	$sql="SELECT student_sn FROM 12basic_ylc WHERE academic_year='$work_year' and editable='1'";
	$rs=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		$editable_sn[$student_sn]=$student_sn;
		$rs->MoveNext();
	}
	return $editable_sn;
}

function get_sealed_status($work_year)
{
	global $CONN;
	$editable_status=array(0=>0,1=>0);
	$sql="SELECT editable,count(*) as counter FROM 12basic_ylc WHERE academic_year='$work_year' GROUP BY editable";
	$rs=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
	while(!$rs->EOF)
	{
		$editable=$rs->fields['editable']?$rs->fields['editable']:0;
		$editable_status[$editable]=$rs->fields['counter'];
		$rs->MoveNext();
	}

	$status="<font size=2 color='brown'><img src='./images/sealed.png' height=12>�w�ʦs�H�ơG".$editable_status[0]." �@<img src='./images/off.png' height=12>���ʦs�H�ơG".$editable_status[1].'</font>';

	return $status;
}

//�ץ�JavaScript�S���r
function JsEncode($value) {
	$value = (string)$value;
	$value = str_replace("\\","\\\\",$value);
	$value = str_replace("\"","\\\"",$value);
	$value = str_replace("'","\\'",$value);
	$value = str_replace("\r","\\r",$value);
	$value = str_replace("\n","\\n",$value);
	$value = str_replace("\t","\\t",$value);
	return $value;
}

//����javascriptĵ�i�T��
function AlertBox($msg) {
  	$data = "<script type=\"text/javascript\">\r\n";
  	$data .= "<!--\r\n";
  	$data .= "alert(\"" . JsEncode($msg) . "\");\r\n";
  	$data .= "// -->\r\n";
  	$data .= "</script>\r\n";
  	return $data;
}

?>