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
	$file=$UPLOAD_PATH."12basic_ptc/aspiration.csv";
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
	$sql_select="select student_sn from 12basic_ptc where academic_year=$academic_year";
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
	$sql_select="select student_sn,aspiration,aspiration_datetime,aspiration_memo from 12basic_ptc where academic_year=$academic_year";
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
	$sql_select="select student_sn,score_remote,score_kind_id,kind_id_memo from 12basic_ptc where academic_year=$academic_year";
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

function get_student_id($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,kind_id,disability_id,free_id,id_memo from 12basic_ptc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$kind_free=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$kind_free[$student_sn]['kind_id']=$recordSet->fields['kind_id'];
		$kind_free[$student_sn]['disability_id']=$recordSet->fields['disability_id'];		
		$kind_free[$student_sn]['free_id']=$recordSet->fields['free_id'];
		$kind_free[$student_sn]['id_memo']=$recordSet->fields['id_memo'];
		
		$recordSet->MoveNext();
	}
	return $kind_free;	
}

function get_student_card_no($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,card_no from 12basic_ptc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$kind_free=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$kind_free[$student_sn]['card_no']=$recordSet->fields['card_no'];
		$recordSet->MoveNext();
	}
	return $kind_free;	
}

function get_student_disadvantage($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,score_disadvantage,disadvantage_memo from 12basic_ptc where academic_year=$academic_year"; //,score_remote
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$disadvantage=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		//$disadvantage[$student_sn]['remote']=$recordSet->fields['score_remote'];
		$disadvantage[$student_sn]['disadvantage']=$recordSet->fields['score_disadvantage'];
		$disadvantage[$student_sn]['score']=$recordSet->fields['score_disadvantage']; //$recordSet->fields['score_remote']+
		$disadvantage[$student_sn]['disadvantage_memo']=$recordSet->fields['disadvantage_memo'];
		$recordSet->MoveNext();
	}
	return $disadvantage;	
}


function get_student_balance($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,score_balance_health,score_balance_art,score_balance_complex,balance_memo from 12basic_ptc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$balance=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$balance[$student_sn]['score_balance_health']=$recordSet->fields['score_balance_health'];
		$balance[$student_sn]['score_balance_art']=$recordSet->fields['score_balance_art'];
		$balance[$student_sn]['score_balance_complex']=$recordSet->fields['score_balance_complex'];
		$balance[$student_sn]['score']=$recordSet->fields['score_balance_health']+$recordSet->fields['score_balance_art']+$recordSet->fields['score_balance_complex'];
		$balance[$student_sn]['balance_memo']=$recordSet->fields['balance_memo'];
		$recordSet->MoveNext();
	}
	return $balance;	
}

function get_student_diversification($academic_year)
{
	global $CONN,$diversification_score_max;
	//取得前已開列學生資料
	$sql_select="select student_sn,score_service,score_fault,score_competetion,score_fitness,score_fitness_assign,diversification_memo from 12basic_ptc where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$diversification=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$diversification[$student_sn]['score_service']=$recordSet->fields['score_service'];
		$diversification[$student_sn]['score_fault']=$recordSet->fields['score_fault'];
		$diversification[$student_sn]['score_competetion']=$recordSet->fields['score_competetion'];
		$diversification[$student_sn]['score_fitness']=$recordSet->fields['score_fitness'];
		$diversification[$student_sn]['score_fitness_assign']=$recordSet->fields['score_fitness_assign'];
		$fitness_score=($recordSet->fields['score_fitness_assign'] > $recordSet->fields['score_fitness'])?$recordSet->fields['score_fitness_assign']:$recordSet->fields['score_fitness'];
		$diversification[$student_sn]['score']=$recordSet->fields['score_service']+$recordSet->fields['score_fault']+$recordSet->fields['score_competetion']+$fitness_score;
			$diversification[$student_sn]['score']=min($diversification[$student_sn]['score'],$diversification_score_max);
		$diversification[$student_sn]['diversification_memo']=$recordSet->fields['diversification_memo'];
		$recordSet->MoveNext();
	}
	return $diversification;
}



//體適能紀錄
/*  103年度的
function count_student_score_fitness($sn_array)
{
        global $CONN,$fitness_score_one,$fitness_score_one_max,$fitness_addon,$fitness_semester,$fitness_keyword,$work_year,$fitness_score_disability;
        $score_fitness=array();
        foreach($sn_array as $student_sn){
                $sql_select="SELECT prec1,prec2,prec3,prec4,c_curr_seme FROM fitness_data WHERE student_sn=$student_sn AND c_curr_seme IN ($fitness_semester) AND organization like '%$fitness_keyword%' ORDER BY c_curr_seme";
                $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗，有可能是未安裝體適能(fitness)模組！<br>$sql_select",256);
                while(!$recordSet->EOF) {
                        $passed=0;      //通過項目次數
                        $medal='';      //獎章
                        $g=0;           //金
                        $s=0;           //銀
                        $c=0;           //銅
                        for($i=0;$i<=3;$i++) {
                                $my_pre=$recordSet->fields[$i];
                                if($my_pre>=85) { $g++; $s++; $c++; $passed++; }
                                        elseif($my_pre>=75) { $s++; $c++; $passed++; }
                                        elseif($my_pre>=50) { $c++; $passed++; }
                                        elseif($my_pre>=25) $passed++;  //通過門檻標準  程式現設為25%以上
                        }
                        //判定獎章
                        if($g==4) $medal="gold"; elseif($s==4) $medal="silver"; elseif($c==4) $medal="copper";
                        //判定積分
                        $myscore=min($fitness_score_one_max,$fitness_score_one*$passed);
                        $myscore+=$fitness_addon[$medal];

                        $score_fitness[$student_sn]=max($myscore,$score_fitness[$student_sn]);
                        $recordSet->MoveNext();
                }
				
				//身心障礙給4分($fitness_score_disability) 如果體適能記錄高於四分 取其高值
				$sql="SELECT disability_id FROM 12basic_ptc WHERE academic_year=$work_year AND student_sn=$student_sn";
				$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
				if($res->fields[0]) {
					$score_fitness[$student_sn]=max($fitness_score_disability,$score_fitness[$student_sn]);
				}
        }
        return $score_fitness;
}
*/
function count_student_score_fitness($sn_array)
{
        global $CONN,$fitness_score_one,$fitness_score_one_max,$fitness_addon,$fitness_date_limit,$fitness_keyword,$work_year,$fitness_score_disability,$fitness_score_test_all;
        $score_fitness=array();
        foreach($sn_array as $student_sn){
				$sql_select="SELECT prec1,prec2,prec3,prec4,c_curr_seme,test_y,test_m FROM fitness_data WHERE student_sn=$student_sn AND organization like '%$fitness_keyword%' ORDER BY c_curr_seme";  //AND c_curr_seme IN ($fitness_semester)  改為以日期限定  // AND up_date<='$fitness_date_limit'
                $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗，有可能是未安裝體適能(fitness)模組！<br>$sql_select",256);
                while(!$recordSet->EOF) {
                        $ym=sprintf("%03d-%02d",$recordSet->fields['test_y'],$recordSet->fields['test_m']);
						if($ym <= $fitness_date_limit) {
							$passed=0;  //通過項目次數
							$tested=0;	//完成檢測項目數
							for($i=0;$i<=3;$i++) {
									$my_pre=$recordSet->fields[$i];
									if($my_pre) $tested++;
									if($my_pre>=25) $passed++;  //通過門檻標準  程式現設為25%以上
							}
							//判定積分
							$myscore=$fitness_score_one*$passed;
							if($tested<4) $myscore=0; else $myscore+=$fitness_score_test_all;

							$score_fitness[$student_sn]=max($myscore,$score_fitness[$student_sn]);
						}
                        $recordSet->MoveNext();
                }
				
				
				//身心障礙給8分($fitness_score_disability) 如果體適能記錄高於8分 取其高值
				$sql="SELECT disability_id FROM 12basic_ptc WHERE academic_year=$work_year AND student_sn=$student_sn";
				$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
				if($res->fields[0]) {
					$score_fitness[$student_sn]=max($fitness_score_disability,$score_fitness[$student_sn]);
				}
        }
        return $score_fitness;
}

//學生歷年學期競賽紀錄
function count_student_score_competetion($sn_array)
{
	global $CONN,$race_score,$race_score_max;

	$score_competetion=array();	
	$sn_list=implode(',',$sn_array);
	$sql="SELECT * FROM career_race WHERE level<=4 AND student_sn IN ($sn_list) ORDER BY student_sn";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	/* 原判定
	while(!$res->EOF)
	{
		$i++;
		$student_sn=$res->fields['student_sn'];
		$level=$res->fields['level'];	//範圍
		$squad=$res->fields['squad'];	//性質
		$name=$res->fields['name'];		//競賽名稱
		$rank=$res->fields['rank'];		//名次
		$certificate_date=$res->fields['certificate_date'];		//證書日期
		$weight=$res->fields['weight'];		//權重

		$score_competetion[$student_sn]['detail'][$i]['level']=$level;
		$score_competetion[$student_sn]['detail'][$i]['squad']=$squad;
		$score_competetion[$student_sn]['detail'][$i]['name']=$name;
		$score_competetion[$student_sn]['detail'][$i]['rank']=$rank;
		$score_competetion[$student_sn]['detail'][$i]['certificate_date']=$certificate_date;
		$score_competetion[$student_sn]['detail'][$i]['sponsor']=$res->fields['sponsor'];
		$score_competetion[$student_sn]['detail'][$i]['memo']=$res->fields['memo'];
		$score_competetion[$student_sn]['detail'][$i]['word']=$res->fields['word'];

		$score_competetion[$student_sn]['detail'][$i]['bonus']=$race_score[$level][$rank]*$weight;
		$score_competetion[$student_sn]['score']+=$score_competetion[$student_sn]['detail'][$i]['bonus'];
		$score_competetion[$student_sn]['score']=min($score_competetion[$student_sn]['score'],$race_score_max);

		$res->MoveNext();
	}
	*/
	//新判定  同一類別同年度取最高分
	while(!$res->EOF)
	{
		$i++;
		$student_sn=$res->fields['student_sn'];
		$level=$res->fields['level'];	//範圍
		$squad=$res->fields['squad'];	//性質
		$name=$res->fields['name'];		//競賽名稱
		$rank=$res->fields['rank'];		//名次
		$certificate_date=$res->fields['certificate_date'];		//證書日期
		$weight=$res->fields['weight'];		//權重
		
		if($weight) {   //屏東區只要是有值就採計
			$year=$res->fields['year'];		//年度
			$nature=$res->fields['nature'];		//類別

			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['level']=$level;
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['squad']=$squad;
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['name']=$name;
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['rank']=$rank;
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['weight']=$weight;
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['certificate_date']=$certificate_date;
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['sponsor']=$res->fields['sponsor'];
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['memo']=$res->fields['memo'];
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['word']=$res->fields['word'];
			//單項分數
			$score_competetion[$student_sn]['detail'][$year][$nature][$i]['bonus']=$race_score[$level][$rank];   //$race_score[$level][$rank]*$weight 他區使用
			//類別最高分
			$score_competetion[$student_sn]['detail'][$year][$nature]['nature_bonus']=max($score_competetion[$student_sn]['detail'][$year][$nature]['nature_bonus'],$score_competetion[$student_sn]['detail'][$year][$nature][$i]['bonus']);
		}
		$res->MoveNext();
	}
	//判定應得分數
	foreach($score_competetion as $student_sn=>$detail){
		foreach($detail['detail'] as $year=>$nature){
			foreach($nature as $key=>$value){
				$score_competetion[$student_sn]['score']+=$value['nature_bonus'];
				$score_competetion[$student_sn]['score']=min($score_competetion[$student_sn]['score'],$race_score_max);	
			}
		}
	}
	//新判定end	
	return $score_competetion;
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

function count_student_score_fault($sn_array)
{
	global $CONN,$fault_none,$fault_warning,$fault_peccadillo,$reward_date_limit;
	$fault=array();
	$fault_semester_list=implode(',',$fault_semester);	
	foreach($sn_array as $student_sn){
		$fault_count=0;
		//抓取學生未銷過的獎懲紀錄   ////2014-11-23校對新增日期限定
		$sql="SELECT reward_year_seme,reward_kind FROM reward WHERE student_sn='$student_sn' AND reward_date<='$reward_date_limit' AND reward_kind<0 AND reward_cancel_date='0000-00-00' ORDER BY reward_year_seme";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF)
		{
			$fault_kind=$res->fields['reward_kind'];
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
		//無記過紀錄判定積分
		if(!$fault_count) $fault[$student_sn]=$fault_none;
			elseif($fault_count<3) $fault[$student_sn]=$fault_warning; elseif($fault_count<6) $fault[$student_sn]=$fault_peccadillo; else $fault[$student_sn]=0;
	}
	return $fault;
}

function get_student_score_balance($sn)
{
	global $CONN,$work_year_seme,$graduate_year,$balance_score,$balance_score_max,$balance_semester,$balance_area;
	$score_balance=array();
	$fin_score=cal_fin_score($sn,$balance_semester);
	
	//判定積分
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

function count_student_score_service($sn_array)  //屏東區使用
{
	global $CONN,$association_leader,$leader_allowed,$class_leader,$leader_semester,$association_semester;
	$service=array();			
	$sn_list=implode(',',$sn_array);
	//班級幹部
	$sql="select * from career_self_ponder where student_sn IN ($sn_list) and id='3-2'";
 	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$sn=$res->fields['student_sn'];
		$ponder_array=unserialize($res->fields['content']);
		foreach($ponder_array as $seme_key=>$data){
			if(in_array($seme_key,$leader_semester)) {  //限定採認學期
				foreach($data as $key=>$value){
					if($key<>'data'){
						foreach($value as $leader_name){
							if($leader_name and array_search($leader_name,$leader_allowed))	$service[$sn]['leader']+=$class_leader;
							if($leader_name=='特殊服務表現') $service[$sn]['leader']--;  //104年度特殊服務表現積分為2分  幹部為3分  相差1分  須扣掉
						}
					}
				}
			}
		}
		$res->MoveNext();
	}	
	
	//社團	
	$sql="SELECT student_sn FROM association WHERE student_sn IN ($sn_list) AND stud_post='社長' AND seme_year_seme IN ($association_semester)";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
			$sn=$res->fields['student_sn'];
			if($sn) $service[$sn]['association']+=$association_leader;
			$res->MoveNext();
	}

				
	//特殊表現---已於班級幹部中處理
	
	return $service;	
}


function get_student_direction($sn_array)  //屏東區使用
{
	global $CONN;
	$sn_list=implode(',',$sn_array);
	//抓取生涯選擇方向參照表
	//$direction_items=SFS_TEXT('生涯選擇方向');
	//取得既有資料
	$direction=array();
	$query="select student_sn,direction from career_view where student_sn in ($sn_list)";
	$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	$record_count=$res->RecordCount();	
	$direction_initial=array(1=>'self',2=>'parent',3=>'teacher');
	while(!$res->EOF){
		$student_sn=$res->fields['student_sn'];
		$direction_array=unserialize($res->fields['direction']);
		//以第一志願判定
		$direction[$student_sn]=$direction_array['item'][1];
		$res->MoveNext();
	}
	return $direction;
}

function get_student_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$student_data=array();
	$sql="SELECT a.sn,b.*,year(b.stud_birthday)-1911 AS birth_year,month(b.stud_birthday) AS birth_month,day(b.stud_birthday) AS birth_day,b.stud_study_cond FROM 12basic_ptc a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
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
	$sql="SELECT a.sn,b.* FROM 12basic_ptc a INNER JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
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
	$sql="SELECT student_sn,score_exam_w,score_exam_c,score_exam_m,score_exam_e,score_exam_s,score_exam_n,exam_memo FROM 12basic_ptc WHERE academic_year='$work_year'";
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

function get_student_personality($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$exam_data=array();
	$sql="SELECT student_sn,score_my_aspiration,score_domicile_suggestion,score_guidance_suggestion,personality_memo FROM 12basic_ptc WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
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


function get_final_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$final_data=array();
	$sql="SELECT * FROM 12basic_ptc WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$fields=$rs->fields;
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		foreach($fields as $key=>$value) 
		{
				$final_data[$student_sn][$key]=$rs->fields[$key];
		}
		
		//體適能計算值與指定值特別判斷
		if($final_data[$student_sn]['score_fitness_assign']>$final_data[$student_sn]['score_fitness']) $final_data[$student_sn]['score_fitness']=$final_data[$student_sn]['score_fitness_assign'];
		
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

function get_editable_sn($work_year)
{
	global $CONN;
	$editable_sn=array();
	$sql="SELECT student_sn FROM 12basic_ptc WHERE academic_year='$work_year' and editable='1'";
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
	$sql="SELECT editable,count(*) as counter FROM 12basic_ptc WHERE academic_year='$work_year' GROUP BY editable";
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


//抓取學生學期就讀班級
function get_student_seme($student_sn){
	global $CONN;
	$stud_seme_arr=array();
	$table=array('stud_seme_import','stud_seme');
	foreach($table as $key=>$value){
		$query="select * from $value where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		while(!$res->EOF){
			$stud_grade=substr($res->fields['seme_class'],0,-2);
			$year_seme=$res->fields['seme_year_seme'];
			$semester=substr($year_seme,-1);	
			$seme_key=$stud_grade.'-'.$semester;
			$stud_seme_arr[$seme_key]=$year_seme;

			$res->MoveNext();
		}
	}
	//進行排序
	asort($stud_seme_arr);
	return $stud_seme_arr;
}

?>