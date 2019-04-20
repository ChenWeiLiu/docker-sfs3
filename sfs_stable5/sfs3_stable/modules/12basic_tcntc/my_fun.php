<?php


//產生科目名單
function get_exam_subject_data($nature)
{
	$exam_array=array('W'=>'寫作','C'=>'國文','E'=>'英語','M'=>'數學','S'=>'社會','N'=>'自然');
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
	//取出班級名稱陣列
	$class_base=class_base($work_year_seme);
	
	$class_list="<select name='$select_name' onchange='this.form.submit()'><option value=''>*請選擇班級*</option>";
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
	$file=$UPLOAD_PATH."12basic_tcntc/aspiration.csv";
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


function get_student_list($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn from 12basic_tcntc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
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
	//取得前已開列學生資料
	$sql_select="select student_sn,aspiration,aspiration_datetime,aspiration_memo from 12basic_tcntc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
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
	//取得前已開列學生資料
	$sql_select="select student_sn,score_remote,score_kind_id,kind_id_memo from 12basic_tcntc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
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
	//取得前已開列學生資料
	$sql_select="select student_sn,kind_id,free_id from 12basic_tcntc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$kind_free=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$kind_free[$student_sn]['kind_id']=$recordSet->fields['kind_id'];
		$kind_free[$student_sn]['free_id']=$recordSet->fields['free_id'];
		$recordSet->MoveNext();
	}
	return $kind_free;	
}


function get_student_disadvantage($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,score_remote,score_disadvantage,disadvantage_memo from 12basic_tcntc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
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


function get_student_diversification($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,score_balance_health,score_balance_art,score_balance_complex,score_association,score_service,score_fault,score_reward,diversification_memo from 12basic_tcntc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$diversification=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$diversification[$student_sn]['score_balance_health']=$recordSet->fields['score_balance_health'];
		$diversification[$student_sn]['score_balance_art']=$recordSet->fields['score_balance_art'];
		$diversification[$student_sn]['score_balance_complex']=$recordSet->fields['score_balance_complex'];
		$diversification[$student_sn]['score_association']=$recordSet->fields['score_association'];
		$diversification[$student_sn]['score_service']=$recordSet->fields['score_service'];
		$diversification[$student_sn]['score_fault']=$recordSet->fields['score_fault'];
		$diversification[$student_sn]['score_reward']=$recordSet->fields['score_reward'];
		$diversification[$student_sn]['score']=$recordSet->fields['score_balance_health']+$recordSet->fields['score_balance_art']+$recordSet->fields['score_balance_complex']+$recordSet->fields['score_association']+$recordSet->fields['score_service']+$recordSet->fields['score_fault']+$recordSet->fields['score_reward'];
		$diversification[$student_sn]['diversification_memo']=$recordSet->fields['diversification_memo'];
		$recordSet->MoveNext();
	}
	return $diversification;	
}

function get_student_score_balance($sn)
{
	global $CONN,$work_year_seme,$graduate_year,$balance_score,$balance_score_max,$balance_semester,$balance_area;
	$score_balance=array();
	$fin_score=cal_fin_score($sn,$balance_semester);
	
	//判定級分
	foreach($fin_score as $student_sn=>$score_data)
	{
		foreach($balance_area as $key=>$value)
		{
			$score_balance[$student_sn][$value]['avg']=$score_data[$value]['avg']['score'];
			$score_balance[$student_sn][$value]['bonus']=($score_data[$value]['avg']['score']>=60)?$balance_score:0;
		}
	}	
	return $score_balance;	
}

/*
function get_student_score_balance($sn)
{
	global $CONN,$work_year_seme,$graduate_year,$balance_score,$balance_score_max,$balance_semester,$balance_area;
	$score_balance=array();
	$fin_score=cal_fin_score($sn,$balance_semester);
	
	//判定級分
	foreach($fin_score as $student_sn=>$score_data)
	{
		foreach($balance_area as $key=>$value)
		{
			$score_balance[$student_sn][$value]=($score_data[$value]['avg']>=60)?$balance_score:0;
		}
	}	
	return $score_balance;	
}
*/

function get_student_fault($sn_array)
{
	global $CONN,$fault_none,$fault_warning,$fault_peccadillo,$fault_semester,$fault_date_limit;
	$fault=array();
	//$fault_semester_list=implode(',',$fault_semester);	
	foreach($sn_array as $student_sn){
		$fault_count=0;
		//抓取學生未銷過的獎懲紀錄
		//$sql="SELECT reward_year_seme,reward_kind FROM reward WHERE student_sn='$student_sn' AND reward_kind<0 AND reward_cancel_date='0000-00-00' AND reward_year_seme IN ($fault_semester_list) ORDER BY reward_year_seme";
		$sql="SELECT reward_year_seme,reward_kind FROM reward WHERE student_sn='$student_sn' AND reward_kind<0 AND reward_cancel_date='0000-00-00' AND reward_date<='$fault_date_limit' ORDER BY reward_year_seme";  //  2016-1-6 改為以日期限定
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF)
		{
			$fault_kind=$res->fields['reward_kind'];
			//$reward[$student_sn]['fault']++;  //計算犯過次數	
			switch ($fault_kind) {
				case -1:
					$fault_count++;
					break;
				case -2:
					$fault_count+=2;
					break;
				case -3:
					$fault_count+=3;
					break;
				case -4:
					$fault_count+=6;
					break;
				case -5:
					$fault_count+=9;
					break;
				case -6:
					$fault_count+=18;
					break;
				case -7:
					$fault_count+=27;
					break;
			}
			$res->MoveNext();
		}
		//無記過紀錄判定級分
		if(!$fault_count) $fault[$student_sn]=$fault_none;
			elseif($fault_count<3) $fault[$student_sn]=$fault_warning; else $fault[$student_sn]=0;
	}
	return $fault;
}


function get_student_reward($sn_array)
{
	global $CONN,$reward_score,$reward_score,$reward_score_max,$reward_semester,$reward_date_limit;
	$reward=array();
	//$reward_semester_list=implode(',',$reward_semester);
	foreach($sn_array as $student_sn){
		$reward_count=0;
		//抓取學生未銷過的獎懲紀錄
		//$sql="SELECT reward_year_seme,reward_kind FROM reward WHERE student_sn='$student_sn' AND reward_kind>0 AND reward_year_seme IN ($reward_semester_list) ORDER BY reward_year_seme";
		$sql="SELECT reward_year_seme,reward_kind FROM reward WHERE student_sn='$student_sn' AND reward_kind>0 AND reward_date<='$reward_date_limit' ORDER BY reward_year_seme";
		
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF)
		{
			$reward_kind=$res->fields['reward_kind'];
			switch ($reward_kind) {
				case 1:
					$reward_count+=$reward_score[1];
					break;
				case 2:
					$reward_count+=$reward_score[1]*2;
					break;
				case 3:
					$reward_count+=$reward_score[3];
					break;
				case 4:
					$reward_count+=$reward_score[3]*2;
					break;
				case 5:
					$reward_count+=$reward_score[9];
					break;
				case 6:
					$reward_count+=$reward_score[9]*2;
					break;
				case 7:
					$reward_count+=$reward_score[9]*3;
					break;
			}
			$res->MoveNext();
		}
		$reward[$student_sn]=min($reward_score_max,$reward_count);	//取最高限
	}	
	return $reward;
}

//配合103入學招生系統需要
function get_student_reward_list($work_year)
{
	global $CONN,$reward_score,$reward_score,$reward_score_max,$reward_semester,$reward_date_limit;
	$reward=array();
	//$reward_semester_list=implode(',',$reward_semester);
	//$sql="SELECT student_sn,reward_year_seme,reward_kind FROM reward WHERE student_sn IN (select student_sn from 12basic_tcntc where academic_year='$work_year') AND reward_kind>0 AND reward_year_seme IN ($reward_semester_list) ORDER BY reward_year_seme";
	$sql="SELECT student_sn,reward_year_seme,reward_kind FROM reward WHERE student_sn IN (select student_sn from 12basic_tcntc where academic_year='$work_year') AND reward_kind>0 AND reward_date<='$reward_date_limit' ORDER BY reward_year_seme";
	
	
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$student_sn=$res->fields['student_sn'];
		$reward_kind=$res->fields['reward_kind'];
		switch ($reward_kind) {
			case 1:
				$reward[$student_sn][1]+=1;
				break;
			case 2:
				$reward[$student_sn][1]+=2;
				break;
			case 3:
				$reward[$student_sn][3]+=1;
				break;
			case 4:
				$reward[$student_sn][3]+=2;
				break;
			case 5:
				$reward[$student_sn][9]+=1;
				break;
			case 6:
				$reward[$student_sn][9]+=2;
				break;
			case 7:
				$reward[$student_sn][9]+=3;
				break;
		}
		$res->MoveNext();
	}
	return $reward;
}

function get_student_association()
{
	global $CONN,$work_year,$association_semester_score_qualtified,$association_semester_score,$association_score_max,$association_date_limit;
	$association=array();
	//$sql="SELECT * FROM association where student_sn IN (select student_sn from stud_seme where seme_year_seme='$work_year_seme' and seme_class like '$graduate_year%') ORDER BY student_sn,seme_year_seme,score";
	$sql="SELECT * FROM association where student_sn IN (select student_sn from 12basic_tcntc where academic_year='$work_year') ORDER BY student_sn,seme_year_seme,score";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$sn=$res->fields['student_sn'];
		$semester=$res->fields['seme_year_seme'];
		$association[$sn][$semester]['name']=$res->fields['association_name'];
		$association[$sn][$semester]['score']=$res->fields['score'];
		$res->MoveNext();
	}
	//判定級分
	foreach($association as $student_sn=>$semester_data){
		$i=count($semester_data);
		if($i>=$association_semester_count){	//每個學期有達到參加社團數量才計分
			foreach($semester_data as $seme=>$index){
				foreach($index as $key=>$value){
					$my_score=$value['score'];
					if($my_score>=$association_semester_score_qualtified) $association[$student_sn]['bonus']+=$association_semester_score;   //成績達到標準就計分
				}
			}
		}
		$association[$student_sn]['bonus']=min($association_score_max,$association[$student_sn]['bonus']); //取最高限
	}
	return $association;
}


function get_student_service()
{
	global $CONN,$work_year,$service_semester_minutes,$service_semester_score,$service_score_max,$service_date_limit;
	$service=array();
	//$sql="SELECT a.student_sn,a.minutes,b.year_seme FROM stud_service_detail a INNER JOIN stud_service b ON a.item_sn=b.sn WHERE a.student_sn IN (select student_sn from stud_seme where seme_year_seme='$work_year_seme' and seme_class like '$graduate_year%') ORDER BY student_sn,year_seme";
	$sql="SELECT a.student_sn,a.minutes,b.year_seme,b.confirm FROM stud_service_detail a INNER JOIN stud_service b ON a.item_sn=b.sn WHERE a.student_sn IN (select student_sn from 12basic_tcntc where academic_year='$work_year') ORDER BY student_sn,year_seme";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		if($res->fields['confirm']){
			$sn=$res->fields['student_sn'];
			$semester=$res->fields['year_seme'];
			$minutes=$res->fields['minutes'];
			$service[$sn][$semester]+=$minutes;
		}
		$res->MoveNext();
	}
	//判定級分
	foreach($service as $student_sn=>$semester){
		foreach($semester as $seme=>$minutes){
			if($minutes>=$service_semester_minutes) $service[$student_sn]['bonus']+=$service_semester_score;   //服務分鐘數達到標準就計分
		}
		$service[$student_sn]['bonus']=min($service_score_max,$service[$student_sn]['bonus']);  //取最高限
	}
	return $service;
}


function get_student_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$student_data=array();
	$sql="SELECT a.sn,b.*,year(b.stud_birthday)-1911 AS birth_year,month(b.stud_birthday) AS birth_month,day(b.stud_birthday) AS birth_day,b.stud_study_cond FROM 12basic_tcntc a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
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


function get_domicile_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$domicile_data=array();
	$sql="SELECT a.sn,b.* FROM 12basic_tcntc a INNER JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
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
	$sql="SELECT student_sn,score_exam_w,score_exam_c,score_exam_m,score_exam_e,score_exam_s,score_exam_n FROM 12basic_tcntc WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
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


function get_final_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$final_data=array();
	$sql="SELECT * FROM 12basic_tcntc WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
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
	//取得前已開列學生資料
	$sql_select="select student_sn,kind_id,disability_id,free_id,id_memo,language_certified from 12basic_tcntc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
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


function get_editable_sn($work_year)
{
	global $CONN;
	$editable_sn=array();
	$sql="SELECT student_sn FROM 12basic_tcntc WHERE academic_year='$work_year' and editable='1'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
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
	$sql="SELECT editable,count(*) as counter FROM 12basic_tcntc WHERE academic_year='$work_year' GROUP BY editable";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$rs->EOF)
	{
		$editable=$rs->fields['editable']?$rs->fields['editable']:0;
		$editable_status[$editable]=$rs->fields['counter'];
		$rs->MoveNext();
	}

	$status="<font size=2 color='brown'><img src='./images/sealed.png' height=12>已封存人數：".$editable_status[0]." 　<img src='./images/off.png' height=12>未封存人數：".$editable_status[1].'</font>';

	return $status;
}


function get_graduate_data($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,grad_kind from grad_stud where stud_grad_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$graduate=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$graduate[$student_sn]=$recordSet->fields['grad_kind'];
		$recordSet->MoveNext();
	}
	return $graduate;
}

?>