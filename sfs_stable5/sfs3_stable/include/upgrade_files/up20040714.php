<?php

//$Id: up20040714.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//更正期中成績表的class_id錯誤
$sel_year=curr_year();
$sel_seme=curr_seme();
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$score_semester="score_semester_".$sel_year."_".$sel_seme;
$query="select * from $score_semester where 1=0";
if ($CONN->Execute($query)) {
	$query="delete from $score_semester where score='-100'";
	$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	$query="select count(student_sn) as c,student_sn,ss_id,test_kind,test_sort from $score_semester group by student_sn,ss_id,test_kind,test_sort having count(student_sn) > 1 order by c desc";
	$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	if ($res) {
		while(!$res->EOF) {
			if (intval($res->fields[c])<2) break; 
			$student_sn=$res->fields[student_sn];
			$ss_id=$res->fields[ss_id];
			$test_kind=$res->fields[test_kind];
			$test_sort=$res->fields[test_sort];
			$query_chk="select score_id from $score_semester where student_sn='$student_sn' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort' order by update_time desc,score desc";
			$res_chk=$CONN->Execute($query_chk) or trigger_error($query_chk,E_USER_ERROR);
			$i=0;
			while(!$res_chk->EOF) {
				if ($i>0) {
					$query_del="delete from $score_semester where score_id='".$res_chk->fields[score_id]."'";
					$CONN->Execute($query_del) or trigger_error($query_del,E_USER_ERROR);
				}
				$i++;
				$res_chk->MoveNext();
			}
			$res->MoveNext();
		}
	}
	$query="select student_sn,seme_class from stud_seme where seme_year_seme='$seme_year_seme' order by seme_class,seme_num";
	$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	while(!$res->EOF) {
		$seme_class=$res->fields[seme_class];
		$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($seme_class,0,-2),substr($seme_class,-2,2));
		$stud_arr[$class_id].="'".$res->fields[student_sn]."',";
		$res->MoveNext();
	}
	while(list($class_id,$all_sn)=each($stud_arr)){
		if ($all_sn) {
			$all_sn=substr($all_sn,0,-1);
			$query_update="update $score_semester set class_id='$class_id' where student_sn in ($all_sn)";
			$CONN->Execute($query_update) or trigger_error($query_update,E_USER_ERROR);
		}
	}
	$query="alter table $score_semester drop primary key, add primary key (student_sn,ss_id,test_kind,test_sort)";
	$CONN->Execute($query);
}
?>