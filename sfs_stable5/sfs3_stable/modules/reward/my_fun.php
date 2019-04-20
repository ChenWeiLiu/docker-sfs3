<?php

// $Id: my_fun.php 8767 2016-01-13 13:15:56Z qfon $


function cal_rew($sel_year,$sel_seme,$stud_id) {
	global $CONN;
	
	$reward_year_seme=$sel_year.$sel_seme;
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	//2013.12.27 取得 student_sn , 以便即時統計時立即加入 student_sn
	$stud_id=substr($stud_id,0,7);
	$sql="select student_sn from stud_seme where stud_id='$stud_id' and seme_year_seme='$seme_year_seme'";
	$res=$CONN->Execute($sql);
	$student_sn=$res->fields['student_sn'];
	
  //2013.02.05 加上條件, 已銷過的不統計
	$sql="select * from reward where reward_year_seme='$reward_year_seme' and stud_id='$stud_id' and reward_cancel_date='0000-00-00' order by reward_kind";
	$rs=$CONN->Execute($sql);
	if ($rs) {
		if ($rs->recordcount()>0) {
			while (!$rs->EOF) {
				$reward_kind=intval($rs->fields['reward_kind']);
				$ow=($reward_kind>0)?0:3;
				$ork=abs($reward_kind);
				switch ($ork) {
					case 1:
						$stud_rew[$stud_id][3+$ow]++;  //喜獎或警告加1
						break;
					case 2:
						$stud_rew[$stud_id][3+$ow]+=2;  //喜獎或警告加2
						break;
					case 3:
						$stud_rew[$stud_id][2+$ow]++;   //小功或小過加1
						break;
					case 4:
						$stud_rew[$stud_id][2+$ow]+=2;  //小功或小過加2
						break;
					case 5:
						$stud_rew[$stud_id][1+$ow]++;   //大功或大過加1
						break;
					case 6:
						$stud_rew[$stud_id][1+$ow]+=2;  //大功或大過加2
						break;
					case 7:
						$stud_rew[$stud_id][1+$ow]+=3;  //大功或大過加3
						break;
				}
				$rs->MoveNext();
			}
		}
	}
	for ($i=1;$i<=6;$i++) {
		$val=$stud_rew[$stud_id][$i];
		$rs_c=$CONN->Execute("select * from stud_seme_rew where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and sr_kind_id='$i'");
		if ($rs_c->recordcount() > 0)
			$CONN->Execute("update stud_seme_rew set sr_num='$val' where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and sr_kind_id='$i'");
		else
			$CONN->Execute("insert into stud_seme_rew (seme_year_seme,stud_id,sr_kind_id,sr_num,student_sn) values ('$seme_year_seme','$stud_id','$i','$val','$student_sn')");
	}
}
?>
