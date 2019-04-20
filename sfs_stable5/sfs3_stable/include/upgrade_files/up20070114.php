<?php

//$Id: up20070114.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//¦X¨ÖÀË®Öªí¤å¦r
$query = "select * from stud_seme_score_nor_chk where 1=0";
$res=$CONN->Execute($query);
if ($res) {
	include_once dirname(__FILE__)."/../sfs_case_score.php";
	$query="select student_sn,seme_year_seme,count(*) from stud_seme_score_nor_chk group by student_sn,seme_year_seme";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$sn=$res->fields['student_sn'];
		$seme_year_seme=$res->fields['seme_year_seme'];
		$res2=$CONN->Execute("select ss_score_memo from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$sn' and ss_id='0'");
		if (trim($res2->fields['ss_score_memo'])=="") {
			$sel_year=intval(substr($seme_year_seme,0,-1));
			$sel_seme=substr($seme_year_seme,-1,1);
			$chk_value=get_chk_value($sn,$sel_year,$sel_seme,"","value");
			merge_chk_text($sel_year,$sel_seme,$sn,$chk_value);
		}
		$res->MoveNext();
	}
}
?>
