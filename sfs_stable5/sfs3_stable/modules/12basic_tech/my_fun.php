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
	$recent_semester="<select name='$select_name' onchange=\"this.form.target=''; this.form.submit();\">";
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
	
	$class_list="<select name='$select_name' onchange=\"this.form.target=''; this.form.submit();\"><option value=''>*請選擇班級*</option>";
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
	$file=$UPLOAD_PATH."12basic_tech/aspiration.csv";
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
	$sql_select="select student_sn from 12basic_tech where academic_year=$academic_year";
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
	$sql_select="select student_sn,aspiration,aspiration_datetime,aspiration_memo from 12basic_tech where academic_year=$academic_year";
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
	$sql_select="select student_sn,score_remote,score_kind_id,kind_id_memo from 12basic_tech where academic_year=$academic_year";
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
	$sql_select="select student_sn,kind_id,disability_id,free_id,id_memo from 12basic_tech where academic_year=$academic_year";
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
	$sql_select="select student_sn,card_no from 12basic_tech where academic_year=$academic_year";
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

/*
function get_student_disadvantage($academic_year)
{
	global $CONN,$stud_free_arr,$stud_free_rate;
	//取得前已開列學生資料
	$sql_select="select student_sn,score_remote,score_disadvantage,disadvantage_memo from 12basic_tech where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$disadvantage=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$disadvantage[$student_sn]['disadvantage']=$recordSet->fields['score_disadvantage'];
		$disadvantage[$student_sn]['disadvantage_name']=$stud_free_arr[$disadvantage[$student_sn]['disadvantage']];
		$disadvantage[$student_sn]['score']=$stud_free_rate[$recordSet->fields['score_disadvantage']];
		$disadvantage[$student_sn]['disadvantage_memo']=$recordSet->fields['disadvantage_memo'];
		$recordSet->MoveNext();
	}
	return $disadvantage;	
}
*/

function get_student_disadvantage($academic_year)
{
        global $CONN,$stud_free_arr,$stud_free_rate;
        //取得前已開列學生資料
        $id_array=get_student_id($academic_year);

        $sql_select="select student_sn,score_remote,score_disadvantage,disadvantage_memo from 12basic_tech where academic_year=$academic_year";
        $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
        $disadvantage=array();
        while(!$recordSet->EOF)
        {
                $student_sn=$recordSet->fields['student_sn'];
                $free_id=$id_array[$student_sn][free_id];
                $disadvantage[$student_sn]['disadvantage']=$free_id;
                $disadvantage[$student_sn]['disadvantage_name']=$stud_free_arr[$free_id];
                $disadvantage[$student_sn]['score']=$stud_free_rate[$free_id];
                $disadvantage[$student_sn]['disadvantage_memo']=$recordSet->fields['disadvantage_memo'];
                $recordSet->MoveNext();
        }

        return $disadvantage;
}


function get_student_balance($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,score_balance_health,score_balance_art,score_balance_complex,balance_memo from 12basic_tech where academic_year=$academic_year";
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
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,score_service,score_fault,score_competetion,score_fitness,diversification_memo from 12basic_tech where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$diversification=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$diversification[$student_sn]['score_service']=$recordSet->fields['score_service'];
		$diversification[$student_sn]['score_fault']=$recordSet->fields['score_fault'];
		$diversification[$student_sn]['score_competetion']=$recordSet->fields['score_competetion'];
		$diversification[$student_sn]['score_fitness']=$recordSet->fields['score_fitness'];
		$diversification[$student_sn]['score']=$recordSet->fields['score_service']+$recordSet->fields['score_fault']+$recordSet->fields['score_competetion']+$recordSet->fields['score_fitness'];
		$diversification[$student_sn]['diversification_memo']=$recordSet->fields['diversification_memo'];
		$recordSet->MoveNext();
	}
	return $diversification;
}



//體適能紀錄
function count_student_score_fitness($sn_array)
{
        global $CONN,$ADODB_FETCH_MODE,$fitness_score_one,$fitness_score_max,$fitness_addon,$fitness_semester,$work_year;
        $ADODB_FETCH_MODE = ADODB_FETCH_BOTH;
		$score_fitness=array();
        $temp_fitness=array();
		
        foreach($sn_array as $student_sn){
				//檢查是否是身心障礙生
				$sql="SELECT kind_id FROM 12basic_tech WHERE academic_year=$work_year AND student_sn=$student_sn";
				$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
				if($res->fields[0]==10) {
					$score_fitness[$student_sn]['bonus']=$fitness_score_max;
					$score_fitness[$student_sn]['1']=1;
					$score_fitness[$student_sn]['2']=1;
					$score_fitness[$student_sn]['3']=1;
					$score_fitness[$student_sn]['4']=1;					
				} else {					
					$sql_select="SELECT prec1,prec2,prec3,prec4,c_curr_seme FROM fitness_data WHERE student_sn=$student_sn AND c_curr_seme IN ($fitness_semester) ORDER BY c_curr_seme"; //不限定檢測單位
					$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗，有可能是未安裝體適能(fitness)模組！<br>$sql_select",256);
					while(!$recordSet->EOF) {
							$c_curr_seme=$recordSet->fields[c_curr_seme];
							$passed=0;              //通過項目次數
							for($i=0;$i<=3;$i++) {
									$my_pre=$recordSet->fields[$i];
									$a=$i+1;
									if($my_pre<25) $temp_fitness[$student_sn][$c_curr_seme][$a]=0; else { $passed++; $temp_fitness[$student_sn][$c_curr_seme][$a]=1; } //通過門檻標準  程式現設為25%以上
							}
							//判定積分
							$myscore=$fitness_score_one*$passed;
							$temp_fitness[$student_sn][$c_curr_seme][bonus]=min($fitness_score_max,$myscore);

							//取單學期最高成績
							if($score_fitness[$student_sn]['bonus']<=$temp_fitness[$student_sn][$c_curr_seme]['bonus']) $score_fitness[$student_sn]=$temp_fitness[$student_sn][$c_curr_seme];
							$recordSet->MoveNext();
					}
				}
        }
        return $score_fitness;
}

//學生歷年學期競賽紀錄
function count_student_score_competetion($sn_array)
{
	global $CONN,$race_score,$race_score_max,$spe_item_arr,$spe_bonus_arr,$squad_weight;

	$score_competetion=array();	
	$sn_list=implode(',',$sn_array);
	$sql="SELECT * FROM career_race WHERE level<=4 AND student_sn IN ($sn_list) ORDER BY student_sn";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$i++;
		$student_sn=$res->fields['student_sn'];
		$level=$res->fields['level'];	//範圍
		$squad=$res->fields['squad'];	//性質
		$name=$res->fields['name'];		//競賽名稱
		$rank=$res->fields['rank'];		//名次
		$certificate_date=$res->fields['certificate_date'];		//證書日期
		//$weight=$res->fields['weight'];		//權重
		$weight=$res->fields['weight_tech'];		//權重

		$score_competetion[$student_sn]['detail'][$i]['level']=$level;
		$score_competetion[$student_sn]['detail'][$i]['squad']=$squad;
		$score_competetion[$student_sn]['detail'][$i]['name']=$name;
		$score_competetion[$student_sn]['detail'][$i]['rank']=$rank;
		$score_competetion[$student_sn]['detail'][$i]['certificate_date']=$certificate_date;
		$score_competetion[$student_sn]['detail'][$i]['sponsor']=$res->fields['sponsor'];
		$score_competetion[$student_sn]['detail'][$i]['memo']=$res->fields['memo'];
		$score_competetion[$student_sn]['detail'][$i]['word']=$res->fields['word'];

		if($weight){
			//先處理特殊情形
			$spe_flag=0;
			foreach($spe_item_arr as $item_name){
				if(strpos($name,$item_name)!==false){
					$score_competetion[$student_sn]['detail'][$i]['bonus']=$spe_bonus_arr[$rank]*$squad_weight[$squad]; //*$weight
					$spe_flag=1;
				}
			}
			//再判定正常情形
			if(!$spe_flag) $score_competetion[$student_sn]['detail'][$i]['bonus']=$race_score[$level][$rank]*$squad_weight[$squad]*$weight;
		} else $score_competetion[$student_sn]['detail'][$i]['bonus']=0;

		$score_competetion[$student_sn]['score']+=$score_competetion[$student_sn]['detail'][$i]['bonus'];
		$score_competetion[$student_sn]['score']=min($score_competetion[$student_sn]['score'],$race_score_max);

		$res->MoveNext();
	}

	
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

function get_student_particular($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,particular_score,particular_memo from 12basic_tech where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$particular=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$myscore=$recordSet->fields['particular_score']?$recordSet->fields['particular_score']:0;
		$particular[$student_sn]['score']=$myscore;
		$particular[$student_sn]['memo']=$recordSet->fields['particular_memo'];
		if($myscore>=90) $particular[$student_sn]['bonus']=3;
			else if($myscore>=80) $particular[$student_sn]['bonus']=2;
			else if($myscore>=60) $particular[$student_sn]['bonus']=1;
			else $particular[$student_sn]['bonus']=0;
		$recordSet->MoveNext();
	}
	return $particular;
}


function get_student_others($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,others_item,others_memo from 12basic_tech where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$others=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$others[$student_sn]['item']=unserialize($recordSet->fields['others_item']);
		$others[$student_sn]['memo']=$recordSet->fields['others_memo'];
		$recordSet->MoveNext();
	}
	return $others;
}


function get_student_signup($academic_year)
{
	global $CONN;
	//取得前已開列學生資料
	$sql_select="select student_sn,signup_north,signup_central,signup_south,signup_memo from 12basic_tech where academic_year=$academic_year";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$signup=array();
	while(!$recordSet->EOF)
	{
		$student_sn=$recordSet->fields['student_sn'];
		$signup[$student_sn]['item']['north']=$recordSet->fields['signup_north'];
		$signup[$student_sn]['item']['central']=$recordSet->fields['signup_central'];
		$signup[$student_sn]['item']['south']=$recordSet->fields['signup_south'];
		$signup[$student_sn]['memo']=$recordSet->fields['signup_memo'];
		$recordSet->MoveNext();
	}
	return $signup;
}



function count_student_score_fault($sn_array)
{
	global $CONN,$fault_none,$fault_warning,$fault_peccadillo,$reward_score,$reward_date_limit;
	$fault=array();
	$sn_list=implode(',',$sn_array);
	//抓取學生未銷過的獎懲紀錄
	$sql="SELECT student_sn,reward_year_seme,reward_kind FROM reward WHERE student_sn IN ($sn_list) AND reward_date<='$reward_date_limit' AND reward_cancel_date='0000-00-00'";  //10/28 16:00 與技專校院招生策進總會確認：銷過>的紀錄當作沒發生
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$student_sn=$res->fields['student_sn'];
		$fault_kind=$res->fields['reward_kind'];
		switch ($fault_kind) {
				case 1:
						$fault[$student_sn]['1']++;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']+1;
						break;
				case 2:
						$fault[$student_sn]['1']+=2;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']+2;
						break;
				case 3:
						$fault[$student_sn]['3']++;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']+3;
						break;
				case 4:
						$fault[$student_sn]['3']+=2;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']+6;
						break;
				case 5:
						$fault[$student_sn]['9']++;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']+9;
						break;
				case 6:
						$fault[$student_sn]['9']+=2;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']+18;
						break;
				case 7:
						$fault[$student_sn]['9']+=3;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']+27;
						break;

				case -1:
						$fault[$student_sn]['a']++;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']-1;
						break;
				case -2:
						$fault[$student_sn]['a']+=2;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']-2;
						break;
				case -3:
						$fault[$student_sn]['b']++;
						$fault[$student_sn]['flag_peccadillo']+=1;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']-3;
						break;
				case -4:
						$fault[$student_sn]['b']+=2;
						$fault[$student_sn]['flag_peccadillo']+=2;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']-6;
						break;
				case -5:
						$fault[$student_sn]['c']++;
						$fault[$student_sn]['flag_peccadillo']+=3;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']-9;
						break;
				case -6:
						$fault[$student_sn]['c']+=2;
						$fault[$student_sn]['flag_peccadillo']+=6;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']-18;
						break;
				case -7:
						$fault[$student_sn]['c']+=3;
						$fault[$student_sn]['flag_peccadillo']+=9;
						$fault[$student_sn]['fault_count']=$fault[$student_sn]['fault_count']-27;
						break;
		}
		$res->MoveNext();
	}

	foreach($fault as $student_sn=>$data){
		//日常生活表現判定積分
		if($data['flag_peccadillo']>0) $fault[$student_sn]['bonus']=0; else {
						if($data['fault_count']>=9) $fault[$student_sn]['bonus']=$reward_score[9]; //累積至大功(9支嘉獎以上)
										elseif($data['fault_count']>=3) $fault[$student_sn]['bonus']=$reward_score[3];  //累積至小功(3支嘉獎以上)
										elseif($data['fault_count']>=1) $fault[$student_sn]['bonus']=$reward_score[1];  //有嘉獎紀錄
		}
		if($data['fault_count']<0) $fault[$student_sn]['bonus']=0; else $fault[$student_sn]['bonus']=max($fault_none,$fault[$student_sn]['bonus']);  //功過相抵後無懲處紀錄			
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
			$score_balance[$student_sn][$value]['avg']=floor($score_data[$value]['avg']['score']);
			$score_balance[$student_sn][$value]['bonus']=($score_balance[$student_sn][$value]['avg']>=60)?$balance_score:0;
		}
	}	
	return $score_balance;
}

function count_student_score_service($sn_array)  //五專使用
{
	global $CONN,$leader_allowed,$class_leader,$class_leader_excluded,$club_leader,$club_leader_excluded,$service_score,$service_minutes,$service_score_max,$leader_score_max;
	$service=array();
	$sn_list=implode(',',$sn_array);

	$semester_check=array();
	$sql="select * from career_self_ponder where student_sn IN ($sn_list) and id='3-2'";
 	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		$sn=$res->fields['student_sn'];
		$ponder_array=unserialize($res->fields['content']);
/*
echo '<pre>';		
print_r($ponder_array);	
echo '</pre>';
*/
		foreach($ponder_array as $seme_key=>$data){
			foreach($data as $key=>$value){
				if($key<>'data'){
					foreach($value as $leader_name){
						if($leader_name){
							if(!$semester_check[$sn][$seme_key]){ //同學期僅採計一次
								//echo '#'.$seme_key.'-'.$semester_check[$sn][$seme_key].'<br>';
								$leader_name='['.$leader_name.']';
								//echo '<br>'.$leader_name.'-'.$leader_excluded.'*';
								if(strpos($class_leader_excluded,$leader_name)===false){  //排除不允許的名稱									
									$service[$sn]['leader']+=$class_leader;
									//進行幹部最高分限定
									$service[$sn]['leader']=min($leader_score_max,$service[$sn]['leader']);
									$semester_check[$sn][$seme_key]=1;
								}
							}
						}
					}
				}
			}
		}
		$res->MoveNext();
	}	

	//抓取學生學期就讀對應表
	$stud_seme_arr=array();
	$table=array('stud_seme_import','stud_seme');
	foreach($table as $key=>$value){
		$query="select * from $value where student_sn IN ($sn_list)";
		$res=$CONN->Execute($query);
		while(!$res->EOF){
			$student_sn=$res->fields['student_sn'];
			$stud_grade=substr($res->fields['seme_class'],0,-2);
			$year_seme=$res->fields['seme_year_seme'];
			$semester=substr($year_seme,-1);	
			$seme_key=$stud_grade.'-'.$semester;
			$stud_seme_arr[$student_sn][$year_seme]=$seme_key;
			$res->MoveNext();
		}
	}

	
	//社團	
	$sql="SELECT seme_year_seme,student_sn,stud_post FROM association WHERE student_sn IN ($sn_list)";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
			$sn=$res->fields['student_sn'];
			$seme_year_seme=$res->fields['seme_year_seme'];
			$stud_post=$res->fields['stud_post'];
			
			$seme_key=$stud_seme_arr[$sn][$seme_year_seme];
			if($stud_post){
				if(!$semester_check[$sn][$seme_key]){ //同學期僅採計一次
					$leader_name='['.$stud_post.']';
					if(strpos($club_leader_excluded,$leader_name)===false){  //排除不允許的名稱
						$service[$sn]['leader']+=$club_leader;
						//進行幹部最高分限定
						$service[$sn]['leader']=min($leader_score_max,$service[$sn]['leader']);
						$semester_check[$sn][$seme_key]=1;
					}
				}
			}
			$res->MoveNext();
	}
	
	//服務學習  10/28 13:30向技專校院招生策進總會詢問  不分學期、不分校內外、不限定單一項目  採累積時數計算
	$sql="SELECT a.student_sn,a.minutes,b.year_seme,b.confirm FROM stud_service_detail a INNER JOIN stud_service b ON a.item_sn=b.sn WHERE a.student_sn IN ($sn_list) ORDER BY student_sn";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$res->EOF)
	{
		if($res->fields['confirm']){
			$sn=$res->fields['student_sn'];
			$semester=$res->fields['year_seme'];
			$minutes=$res->fields['minutes'];
			$service_time[$sn]+=$minutes;
		}
		$res->MoveNext();
	}

	//判定級分
	foreach($service_time as $student_sn=>$minutes){
		$service[$student_sn]['hours']=intval($minutes/60);
		$units=intval($minutes/$service_minutes);  //滿規定的計量小時
		$service[$student_sn]['service']=$service_score*$units;   
	}
	
	
	//進行合計級分判定
	foreach($service as $student_sn=>$data){
		$service[$student_sn]['bonus']=min($data['leader']+$data['service'],$service_score_max);	
	}
	

	return $service;	
}


function get_student_direction($sn_array)  //五專使用
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
	$sql="SELECT a.sn,b.*,year(b.stud_birthday)-1911 AS birth_year,month(b.stud_birthday) AS birth_month,day(b.stud_birthday) AS birth_day FROM 12basic_tech a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
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
	$sql="SELECT a.sn,b.* FROM 12basic_tech a INNER JOIN stud_domicile b ON a.student_sn=b.student_sn WHERE a.academic_year='$work_year'";
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
	global $CONN,$ADODB_FETCH_MODE,$exam_level_bonus;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$exam_data=array();
	$sql="SELECT student_sn,score_exam_w,score_exam_c,score_exam_m,score_exam_e,score_exam_s,score_exam_n,exam_memo,acad_exam_reg_num FROM 12basic_tech WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$fields=$rs->fields;
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		foreach($fields as $key=>$value) 
		{
			$exam_data[$student_sn][$key]=$rs->fields[$key];
		}
		//統計得分
		$exam_data[$student_sn]['bonus']=$exam_level_bonus[$exam_data[$student_sn]['score_exam_c']]+$exam_level_bonus[$exam_data[$student_sn]['score_exam_m']]+$exam_level_bonus[$exam_data[$student_sn]['score_exam_e']]+$exam_level_bonus[$exam_data[$student_sn]['score_exam_s']]+$exam_level_bonus[$exam_data[$student_sn]['score_exam_n']];
		$rs->MoveNext();
	}
	return $exam_data;
}


function get_exam_score($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$exam_data=array();
	$sql="SELECT * FROM career_exam WHERE student_sn IN (select student_sn FROM 12basic_tech WHERE academic_year='$work_year')";
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
	$sql="SELECT student_sn,score_adaptive_domicile,score_adaptive_tutor,score_adaptive_guidance,personality_memo FROM 12basic_tech WHERE academic_year='$work_year'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$fields=$rs->fields;
	while(!$rs->EOF)
	{
		$student_sn=$rs->fields['student_sn'];
		foreach($fields as $key=>$value) 
		{
			$personality_data[$student_sn][$key]=$rs->fields[$key];
		}
		$personality_data[$student_sn]['bonus']=$personality_data[$student_sn]['score_adaptive_domicile']+$personality_data[$student_sn]['score_adaptive_tutor']+$personality_data[$student_sn]['score_adaptive_guidance'];
		$rs->MoveNext();
	}
	return $personality_data;
}


function get_final_data($work_year)
{
	global $CONN,$ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$final_data=array();
	$sql="SELECT * FROM 12basic_tech WHERE academic_year='$work_year'";
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

function get_editable_sn($work_year)
{
	global $CONN;
	$editable_sn=array();
	$sql="SELECT student_sn FROM 12basic_tech WHERE academic_year='$work_year' and editable='1'";
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
	$sql="SELECT editable,count(*) as counter FROM 12basic_tech WHERE academic_year='$work_year' GROUP BY editable";
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