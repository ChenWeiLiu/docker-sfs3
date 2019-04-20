<?php
//$Id:$

function year_seme_menu($sel_year,$sel_seme,$id_name="year_seme",$arr=array(),$other_script="") {
	global $CONN;

	$scys = new drop_select();
	$scys->s_name =$id_name;
	$scys->top_option = "選擇學期";
	$scys->id = sprintf("%03d",$sel_year).$sel_seme;
	$scys->arr = $arr;
	$scys->is_submit = true;
	$scys->other_script = $other_script;
	return $scys->get_select();
}

function class_year_menu($sel_class_year,$other_script="") {
	global $CONN, $class_year;

	$scys = new drop_select();
	$scys->s_name ="class_year";
	$scys->top_option = "選擇年級";
	foreach($class_year as $k=>$v) if(intval($k)>0) $c_arr[$k] = $v."級";
	if ($c_arr[$sel_class_year] == "") $sel_class_year="";
	$scys->id = $sel_class_year;
	$scys->arr = $c_arr;
	$scys->is_submit = true;
	$scys->other_script = $other_script;
	return $scys->get_select();
}

function get_school_name() {
	global $IS_JHORES;

	$s=get_school_base();
//	return $s['sch_sheng'].(($IS_JHORES>0 && $s['sch_attr_id']=="公立")?"立":"").(($IS_JHORES==0 && $s['sch_attr_id']=="公立")?$s['sch_local_name']:"").$s['sch_cname'];
	return $s['sch_cname'];
}

function get_scope_ename() {
	return array('language'=>'語文', 'math'=>'數學', 'nature'=>'自然', 'social'=>'社會', 'health'=>'健體', 'art'=>'藝文', 'complex'=>'綜合');
}

function get_link_check() {
	return array('language'=>'語文', 'math'=>'數學', 'nature'=>'自然', 'social'=>'社會', 'health'=>'健康', 'art'=>'藝術', 'complex'=>'綜合');
}

function import_makeup_exam($data_arr=array()) { //陣列內容 data_arr[$student_sn][$seme_year_seme][$scope_ename]=$score
	global $CONN, $_SESSION;

	$scope_ename_arr = get_scope_ename();
	$i=0;
	$now=date("Y-m-d H:i:s");
	if(count($data_arr)>0) {
		foreach($data_arr as $student_sn=>$d) {
			foreach($d as $seme_year_seme=>$dd) {
				foreach($dd as $scope_ename=>$score) {
					if($scope_ename_arr[$scope_ename]<>"" && floatval($score)>=0 && floatval($score)<=100 && $score<>"") {
						$query="select * from makeup_exam_scope where student_sn='$student_sn' and seme_year_seme='$seme_year_seme' and scope_ename='$scope_ename'";
						$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
						if($res->RecordCount()>0) {
							$query="update makeup_exam_scope set nscore='".floatval($score)."', has_score='1', update_time='$now', teacher_sn='".$_SESSION['session_tea_sn']."' where student_sn='$student_sn' and seme_year_seme='$seme_year_seme' and scope_ename='$scope_ename'";
							$res=$CONN->Execute($query) or user_error("寫入失敗！<br>$query",256);
							$i++;
						}
					}
				}
			}
		}
	}
	return $i;
}

function cal_better_score($seme_year_seme="", $class_year="", $scope_ename="") {
	global $CONN;

	$seme_year_seme=sprintf("%03d",intval(substr($seme_year_seme,0,-1))).intval(substr($seme_year_seme,-1,1));
	$class_year=intval($class_year);
	$scope_ename_arr = get_scope_ename();
	$link_check_arr = get_link_check();
	if ($seme_year_seme && $class_year>0 && $scope_ename_arr[$scope_ename]) {
		//先取出曾寫入學期成績表的學生流水號
		$query="select * from makeup_exam_score where seme_year_seme='$seme_year_seme' and class_year='$class_year' and scope_ename='$scope_ename' and chg='1'";
		$res=$CONN->Execute($query) or user_error("寫入失敗！<br>$query",256);
		$w_arr=array();
		while($rr=$res->FetchRow()) {
			$w_arr[$rr['student_sn']] = $rr['student_sn'];
		}
		//刪除所選學期, 年級, 領域尚未寫入學期成績表的資料
		$w_str = "'".implode("','",$w_arr)."'";
		$query="delete from makeup_exam_score where seme_year_seme='$seme_year_seme' and class_year='$class_year' and scope_ename='$scope_ename' and student_sn not in ($w_str)";
		$res=$CONN->Execute($query) or user_error("寫入失敗！<br>$query",256);
		//取出領域成績
		$query="select * from makeup_exam_scope where seme_year_seme='$seme_year_seme' and class_year='$class_year' and scope_ename='$scope_ename'";
		$res=$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
		while($rr=$res->FetchRow()) {
			//如果補行評量成績優於原成績, 進行擇優計算
			if ($rr['nscore']>$rr['oscore']) {
				$scope_score=$rr['nscore'];
				if ($scope_score>60) $scope_score=60;
				//echo $rr['oscore']."---".$rr['nscore']."<br>";
				$query2="select a.*,b.rate from stud_seme_score a left join score_ss b on a.ss_id=b.ss_id where a.seme_year_seme='$seme_year_seme' and a.student_sn='".$rr['student_sn']."' and b.enable='1' and b.link_ss like '".$link_check_arr[$scope_ename]."%'";
				$res2=$CONN->Execute($query2) or user_error("讀取失敗！<br>$query2",256);
				$temp_arr=array();
				while($rr2=$res2->FetchRow()) {
					$temp_arr[$rr2['ss_id']] = array('score'=>$rr2['ss_score'], 'rate'=>$rr2['rate']);
				//	echo $rr2['student_sn']."---".$rr2['ss_id']."--".$rr2['rate']."--".$rr2['ss_score']."<br>";
				}
				$new_arr=sort_arr($temp_arr);
				$new_arr2=array();
				//print_r($new_arr); echo "<br>";
				$all_cal = $scope_score * $new_arr['rate']['rate1'];
				foreach($new_arr as $ss_id=>$v) {
					if ($ss_id=="rate") continue;
					if ($v['score']>=60) {
						$all_cal -= $v['score'] * $v['rate'];
						$bscore = $v['score'];
					} elseif ($v['no']==1) {
						$bscore = intval( $all_cal / $v['rate'] *100) / 100;
					} else {
						$bscore = intval( $scope_score * $new_arr['rate']['rate1'] * $v['score'] * $v['rate'] / $new_arr['rate']['rate2'] *100) / 100;
						if ($bscore>=60) $bscore = 60;
						$all_cal -= $bscore * $v['rate'];
					}
					$new_arr2[$ss_id] = array('score'=>$bscore, 'rate'=>$v['rate']);
				}
				$now=date("Y-m-d H:i:s");
				$result = array();
				foreach($new_arr2 as $ss_id=>$v) {
					$query3="select * from makeup_exam_score where seme_year_seme='$seme_year_seme' and student_sn='".$rr['student_sn']."' and ss_id='$ss_id'";
					$res3=$CONN->Execute($query3) or user_error("讀取失敗！<br>$query3",256);
					if ($res3->RecordCount()==0) {
						$query3="insert into makeup_exam_score (seme_year_seme, student_sn, scope_ename, ss_id, class_year, oscore, nscore, rate, test, update_time, teacher_sn) values ('$seme_year_seme', '".$rr['student_sn']."', '$scope_ename', '$ss_id', '$class_year', '".$new_arr[$ss_id]['score']."', '".$v['score']."', '".$v['rate']."', 1, '$now', '".$_SESSION['session_tea_sn']."')";
						$CONN->Execute($query3) or user_error("寫入失敗！<br>$query3",256);
						$result['succ']++;
					} else
						$result['fail']++;
				}
				//修改makeup_exam_scope的act值
				$query3="update makeup_exam_scope set act='1' where seme_year_seme='$seme_year_seme' and class_year='$class_year' and scope_ename='$scope_ename' and student_sn='".$rr['student_sn']."'";
				$CONN->Execute($query3) or user_error("寫入失敗！<br>$query3",256);
				//print_r($new_arr2); echo "<br><br>";
			} else {
			//如果原成績優於補行評量成績, 僅寫入原成績代表已進行過補行評量
				$query2="select a.*,b.rate from stud_seme_score a left join score_ss b on a.ss_id=b.ss_id where a.seme_year_seme='$seme_year_seme' and a.student_sn='".$rr['student_sn']."' and b.enable='1' and b.link_ss like '".$link_check_arr[$scope_ename]."%'";
				$res2=$CONN->Execute($query2) or user_error("讀取失敗！<br>$query2",256);
				$now=date("Y-m-d H:i:s");
				while($rr2=$res2->FetchRow()) {
					$query3="insert into makeup_exam_score (seme_year_seme, student_sn, scope_ename, ss_id, class_year, oscore, nscore, rate, test, update_time, teacher_sn) values ('$seme_year_seme', '".$rr['student_sn']."', '$scope_ename', '".$rr2['ss_id']."', '$class_year', '".$rr2['ss_score']."', '".$rr2['ss_score']."', '".$rr2['rate']."', 1, '$now', '".$_SESSION['session_tea_sn']."')";
					$CONN->Execute($query3) or user_error("讀取失敗！<br>$query3",256);
				}
			}
		}
	}
	return $result;
}

function write_makeup($seme_year_seme="", $scope_ename="", $sn_arr=array(), $act="") {
	global $CONN;

	$a_arr = array('undo', 'write');
	$s_arr = get_scope_ename();
	if ($seme_year_seme && in_array($act,$a_arr) && count($sn_arr)>0) {
		if ($s_arr[$scope_ename]<>"") $s_str = "and a.scope_ename='$scope_ename'";
		else $s_str = "";
		foreach($sn_arr as $k=>$v) $sn_arr[$k] = $k;
		$sn_str = "'".implode("','", $sn_arr)."'";
		$query="select a.*,b.ss_score from makeup_exam_score a left join stud_seme_score b on a.seme_year_seme=b.seme_year_seme and a.student_sn=b.student_sn and a.ss_id=b.ss_id where a.seme_year_seme='$seme_year_seme' $s_str and a.student_sn in ($sn_str)";
		$res = $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
		while($rr=$res->FetchRow()) {
			if ($act=="write") {
				//如果擇優後不相等, 表示成績應寫入學期表
				if ($rr['oscore']<>$rr['nscore']) {
					//如果與學期表不相等, 表示應進行寫入動作
					if ($rr['nscore']<>$rr['ss_score']) {
						$query2="update stud_seme_score set ss_score='".$rr['nscore']."' where seme_year_seme='$seme_year_seme' and student_sn='".$rr['student_sn']."' and ss_id='".$rr['ss_id']."'";
						$CONN->Execute($query2) or user_error("寫入失敗！<br>$query2",256);
						$query3="update makeup_exam_score set test='1', chg='1' where seme_year_seme='$seme_year_seme' and student_sn='".$rr['student_sn']."' and ss_id='".$rr['ss_id']."'";
						$CONN->Execute($query3) or user_error("寫入失敗！<br>$query3",256);
					}
				} else {
					//如果擇優後相等, 表示不必寫入學期表, 但要註記進行過補行評量
					$query3="update makeup_exam_score set test='1', chg='0' where seme_year_seme='$seme_year_seme' and student_sn='".$rr['student_sn']."' and ss_id='".$rr['ss_id']."'";
					$CONN->Execute($query3) or user_error("寫入失敗！<br>$query3",256);
				}
			} elseif ($act=="undo") {
				//如果擇優後不相等, 表示成績應還原
				if ($rr['oscore']<>$rr['nscore']) {
					//如果與學期表不相等, 表示應進行還原動作
					if ($rr['nscore']==$rr['ss_score']) {
						$query2="update stud_seme_score set ss_score='".$rr['oscore']."' where seme_year_seme='$seme_year_seme' and student_sn='".$rr['student_sn']."' and ss_id='".$rr['ss_id']."'";
						$CONN->Execute($query2) or user_error("寫入失敗！<br>$query2",256);
						$query3="update makeup_exam_score set chg='0' where seme_year_seme='$seme_year_seme' and student_sn='".$rr['student_sn']."' and ss_id='".$rr['ss_id']."'";
						$CONN->Execute($query3) or user_error("寫入失敗！<br>$query3",256);
					}
				} else {
					//如果擇優後相等, 表示不必寫入學期表
					$query3="update makeup_exam_score set chg='0' where seme_year_seme='$seme_year_seme' and student_sn='".$rr['student_sn']."' and ss_id='".$rr['ss_id']."'";
					$CONN->Execute($query3) or user_error("寫入失敗！<br>$query3",256);
				}
			}
		}
	}
}

function sort_arr($arr=array()) {
	$arr2 = $arr;
	$temp_arr = array();
	for($i=count($arr); $i>0; $i--) {
		$bk=0;
		$bs=-1;
		reset($arr);
		foreach($arr as $k=>$v) {
			$score=intval($v['score']);
			if ($score>$bs) {
				$bk=$k;
				$bs=$score;
			}
		}
		$temp_arr[$bk]=$arr[$bk];
		$temp_arr[$bk]['no']=$i;
		$arr[$bk]['score']=-999;
	}
	$rate1=0;
	$rate2=0;
	foreach($arr2 as $k=>$v) {
		$rate1 += $v['rate'];
		if ($v['score']<60) $rate2 += $v['rate'] * $v['score'];
	}
	$temp_arr['rate']=array('rate1'=>$rate1, 'rate2'=>$rate2);
	return $temp_arr;
}
?>
