<?php

	//檢查登入身份--網管及指定處室可通過,例 chk_login('學務處,訓導處');chk_login('教務處');
	function chk_login($Room) {
		global $CONN;
		//檢查網管權限
		
		$SQL="SELECT id_sn,pro_kind_id  FROM  pro_check_new where id_sn='{$_SESSION[session_tea_sn]}' and pro_kind_id='1' ";
		$rs=&$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		//$arr=$rs->GetArray();
		if ($rs->RecordCount()===1) return ;//通過直接離開

		$SQL="SELECT a.teacher_sn, a.name,c.room_name, d.title_name  FROM 
		teacher_base a, teacher_post b,	school_room c, teacher_title d 
		where a.teacher_sn = '{$_SESSION[session_tea_sn]}' and a.teacher_sn = b.teacher_sn 
		and b.post_office=c.room_id and b.teach_title_id =d.teach_title_id  ";
		$rs=&$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$arr=$rs->GetArray();
		// 非授權人員,直接離開
		if ($rs->RecordCount()===0) backe("※※※非授權人員！※※※");
		$ary=$arr[0];$err='Y';$switch=0;

		if (preg_match("/,/",$Room)) $switch=1;
		if ($switch==0){if ($ary['room_name']!='' && $ary['room_name']==$Room ) $err='N';	}
		if ($switch==1):
			$A=explode(",",$Room);
			foreach($A as $R){if ($ary['room_name']!='' && $ary['room_name']==$R ) $err='N';	}				
		endif;

		if ($err=='Y') backe("※※非".$Room."人員！※※");		
		}

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
	$file=$UPLOAD_PATH."chc_basic12/aspiration.csv";
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
	$sql_select="select student_sn from chc_basic12 where academic_year=$academic_year";
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
	$sql_select="select student_sn,aspiration,aspiration_datetime,aspiration_memo from chc_basic12 where academic_year=$academic_year";
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
	$sql_select="select student_sn,score_remote,score_kind_id,kind_id_memo from chc_basic12 where academic_year=$academic_year";
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
	$sql_select="select student_sn,kind_id,free_id from chc_basic12 where academic_year=$academic_year";
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
	$sql_select="select student_sn,score_remote,score_disadvantage,disadvantage_memo from chc_basic12 where academic_year=$academic_year";
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
	$sql_select="select student_sn,score_balance_health,score_balance_art,score_balance_complex,score_association,score_service,score_fault,score_reward,diversification_memo from chc_basic12 where academic_year=$academic_year";
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


function get_student_reward($sn_array)
{
	global $CONN,$work_year,$graduate_year,$fault_none,$fault_warning,$fault_peccadillo,$reward_score,$reward_score_max;
	$reward=array();
	foreach($sn_array as $student_sn){
		//預設值
		$reward[$student_sn]['bonus'][1]=$fault_none;
		$reward[$student_sn]['bonus'][2]=0;		
		//抓取學生未銷過的獎懲紀錄
		$sql="SELECT reward_year_seme,reward_kind FROM reward WHERE student_sn='$student_sn' AND reward_cancel_date='0000-00-00' ORDER BY student_sn,reward_year_seme";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF)
		{
			$reward_kind=$res->fields['reward_kind'];
			if($reward_kind<0) $reward[$student_sn]['fault']++;  //計算犯過次數	
			
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
					$reward[$student_sn][-1]++;
					break;
				case -2:
					$reward[$student_sn][-1]+=2;
					break;
				case -3:
					$reward[$student_sn][-3]++;
					break;
				case -4:
					$reward[$student_sn][-3]+=2;
					break;
				case -5:
					$reward[$student_sn][-9]++;
					break;
				case -6:
					$reward[$student_sn][-9]+=2;
					break;
				case -7:
					$reward[$student_sn][-9]+=3;
					break;
			}
			$res->MoveNext();
		}
	}
	//無記過紀錄判定級分
	foreach($reward as $student_sn=>$data){
		if(!$reward[$student_sn]['fault']) $reward[$student_sn]['bonus'][1]=$fault_none; else
			if($reward[$student_sn][-9] or $reward[$student_sn][-3]) $reward[$student_sn]['bonus'][1]=0;
				else $reward[$student_sn]['bonus'][1]=$fault_peccadillo;
		
		//獎勵記錄判定級分
		$reward[$student_sn]['bonus'][2]=$reward[$student_sn][1]*$reward_score[1]+$reward[$student_sn][3]*$reward_score[3]+$reward[$student_sn][9]*$reward_score[9];
		$reward[$student_sn]['bonus'][2]=($reward[$student_sn]['bonus'][2]>$reward_score_max)?$reward_score_max:$reward[$student_sn]['bonus'][2];	//取最高限
	}

	return $reward;
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
			$score_balance[$student_sn][$value]=($score_data[$value]['avg']>=60)?$balance_score:0;
		}
	}	
	return $score_balance;	
}

function get_student_association()
{
	global $CONN,$work_year_seme,$graduate_year,$association_semester_score_qualtified,$association_semester_score,$association_score_max;
	$association=array();
	$sql="SELECT * FROM association where student_sn IN (select student_sn from stud_seme where seme_year_seme='$work_year_seme' and seme_class like '$graduate_year%') ORDER BY student_sn,seme_year_seme,score";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$sn=$recordSet->fields['student_sn'];
		$semester=$res->fields['seme_year_seme'];
		$association[$sn][$semester][$i]['name']=$res->fields['association_name'];
		$association[$sn][$semester][$i]['score']=$res->fields['score'];
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
		$association[$student_sn]['bonus']=($association[$student_sn]['bonus']>$association_score_max)?$association_score_max:$association[$student_sn]['bonus']; //取最高限	
		
	}
	return $association;	
}


function get_student_service()
{
	global $CONN,$work_year_seme,$graduate_year,$service_semester_minutes,$service_semester_score;
	$service=array();
	$sql="SELECT a.student_sn,a.minutes,b.year_seme FROM stud_service_detail a INNER JOIN stud_service b ON a.item_sn=b.sn WHERE a.student_sn IN (select student_sn from stud_seme where seme_year_seme='$work_year_seme' and seme_class like '$graduate_year%') ORDER BY student_sn,year_seme";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$sn=$recordSet->fields['student_sn'];
		$semester=$res->fields['seme_year_seme'];
		$minutes=$res->fields['minutes'];
		$service[$sn][$semester]+=$minutes;
		$res->MoveNext();
	}
	//判定級分
	foreach($service as $student_sn=>$semester){
		foreach($semester as $seme=>$minutes){
				if($minutes>=$service_semester_minutes) $service[$sn]['bonus']+=$service_semester_score;   //服務分鐘數達到標準就計分
		}
		$service[$student_sn]['bonus']=($service[$student_sn]['bonus']>$service_score_max)?$service_score_max:$service[$student_sn]['bonus'];	//取最高限
	}
	return $service;	
}

function get_student_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$student_data=array();
	$sql="SELECT a.sn,b.*,year(b.stud_birthday)-1911 AS birth_year,month(b.stud_birthday) AS birth_month,day(b.stud_birthday) AS birth_day FROM chc_basic12 a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
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
	$sql="SELECT a.sn,b.* FROM chc_basic12 a INNER JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
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
	$sql="SELECT student_sn,score_exam_w,score_exam_c,score_exam_m,score_exam_e,score_exam_s,score_exam_n FROM chc_basic12 WHERE academic_year='$work_year'";
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
	$sql="SELECT * FROM chc_basic12 WHERE academic_year='$work_year'";
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


?>