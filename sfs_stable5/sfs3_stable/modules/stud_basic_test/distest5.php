<?php

// $Id: distest5.php 8373 2015-03-30 06:44:32Z chiming $

include "select_data_config.php";
include "../../include/sfs_case_score.php";

sfs_check();

chk_tbl();

//決定領域(學科)出現順序
$ss_link=array(1=>"chinese",2=>"english",3=>"language",4=>"math",5=>"social",6=>"nature",7=>"art",8=>"health",9=>"complex");
//$ss_link=array(1=>"chinese",2=>"english",3=>"language",4=>"math",5=>"nature",6=>"social",7=>"health",8=>"art",9=>"complex");
if ($IS_JHORES==0)
	$f_year=5;
else
	$f_year=2;

if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
	$_POST[year_seme]=$year_seme;
} else {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
//檢查成績是否已封存
$chk=chk_dis($sel_year);
if ($chk[2]) header("Location: chart.php");

if ($_POST['cy']==2 || $_POST['cy']==3 || $_POST['cy']==4 || $_POST['cy']==5) {
	$starty=0;
	$y_arr=array(1=>"一上",2=>"一下",3=>"二上",4=>"二下",5=>"三上");
} else {
	$starty=1;
	$y_arr=array(1=>"二上",2=>"二下",3=>"三上");
}
$s_arr=array(1=>"國",2=>"英",3=>"語",4=>"數",5=>"社",6=>"自",7=>"藝",8=>"健",9=>"綜",10=>"總");
//$s_arr=array(1=>"國",2=>"英",3=>"語",4=>"數",5=>"自",6=>"社",7=>"健",8=>"藝",9=>"綜",10=>"總");

if ($_POST[year_name]) {
	$seme_class=intval($_POST[year_name])."%";
	$query="select distinct seme_class from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$seme_class%' order by seme_class";
	$res=$CONN->Execute($query);
	$class_arr=array();
	while(!$res->EOF) {
		$class_arr[$res->fields['seme_class']]=$res->fields['seme_class'];
		$res->MoveNext();
	}
	$smarty->assign("class_arr",$class_arr);

	if ($_POST['clean']) {
		$CONN->Execute("drop table temp_tcc_score");
		$creat_table_sql="CREATE TABLE `temp_tcc_score` (
			`student_sn` int(10) unsigned NOT NULL default '0',
			`seme` varchar(4) NOT NULL default '',
			`ss_no` int(6) unsigned NOT NULL default '0',
			`score` float NOT NULL default '0.0',
			`pr` int(6) NOT NULL default '0',
			`sp_score` float NOT NULL default '0.0',
			`sp_pr` int(6) NOT NULL default '0',
			PRIMARY KEY (student_sn,seme,ss_no)
		)";
		$CONN->Execute($creat_table_sql);
	} elseif ($_POST['class_no'] && $_POST['act']=="cal") {
		$query="select a.* from stud_seme_dis a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='".$_POST['class_no']."' and b.stud_study_cond in ($cal_str) order by a.seme_class,a.seme_num";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$seme_class=$res->fields[seme_class];
			$sn[]=$res->fields[student_sn];
			$res->MoveNext();
		}
		$stud_study_year=get_stud_study_year($seme_year_seme,$_POST['year_name']);
		for ($i=$starty;$i<=$f_year;$i++) {
			for ($j=1;$j<=2;$j++) {
				$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
			}
		}
		array_pop($semes);
		//計算之小數位數
		$nnum=2;
		$fin_score=cal_fin_score($sn,$semes);
		$s_num=count($semes);//學期數
		foreach($sn as $i) {
			reset($ss_link);
			foreach($ss_link as $jj => $j) {
				if ($j=="language") continue;
				reset($semes);
				$score=0;
				foreach($semes as $kk => $k) {
					//分數計算採四捨五入
					$sc=round($fin_score[$i][$j][$k]['score'],$nnum);
					$score+=$sc;
					//寫入分數
					$CONN->Execute("insert into temp_tcc_score (student_sn,seme,ss_no,score) values ('$i','$kk','$jj','$sc')");
				}
				$CONN->Execute("insert into temp_tcc_score (student_sn,seme,ss_no,score) values ('$i','$s_num','$jj','".round($score/$s_num,$nnum)."')");
			}
		}
		//計算語文領域不加權平均
		$query="select student_sn,seme,sum(score) as sc from temp_tcc_score where ss_no in ('1','2') group by student_sn,seme order by student_sn,seme";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			//寫入分數
			$sc=$res->fields['sc'];
			$CONN->Execute("insert into temp_tcc_score (student_sn,seme,ss_no,score) values ('".$res->fields['student_sn']."','".$res->fields['seme']."','3','".round(($sc/2),$nnum)."')");
			$res->MoveNext();
		}
		//計算各科不加權平均
		$ss_num=count($ss_link);//科目數
		$query="select student_sn,ss_no,sum(score) as sc from temp_tcc_score group by student_sn,ss_no order by student_sn,ss_no";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			//寫入分數
			$sc=$res->fields['sc'];
			$CONN->Execute("insert into temp_tcc_score (student_sn,seme,ss_no,score) values ('".$res->fields['student_sn']."','$s_num','".$res->fields['ss_no']."','".round($sc/$s_num,$nnum)."')");
			$res->MoveNext();
		}
		//計算各學期不加權平均(學期總分/8)
		$query="select student_sn,seme,sum(score) as sc from temp_tcc_score where ss_no not in ('3') group by student_sn,seme order by student_sn,seme";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			//寫入分數
			$sc=$res->fields['sc'];
			$CONN->Execute("insert into temp_tcc_score (student_sn,seme,ss_no,score) values ('".$res->fields['student_sn']."','".$res->fields['seme']."','".($ss_num+1)."','".round($sc/($ss_num-1),$nnum)."')");
			$res->MoveNext();
		}
		//最後的總平均再以各學期平均重算
		$query="select student_sn,sum(score) as sc from temp_tcc_score where ss_no='".($ss_num+1)."' and seme<'$s_num' group by student_sn order by student_sn";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			//寫入分數
			$sc=$res->fields['sc'];
			$CONN->Execute("update temp_tcc_score set score='".round(($sc/$s_num),$nnum)."' where student_sn='".$res->fields['student_sn']."' and seme='$s_num' and ss_no='".($ss_num+1)."'");
			$res->MoveNext();
		}		
		header("Content-type: text/html; charset=big5");
		echo $_POST['class_no']."...計算完成!";
		exit;
	} elseif ($_POST['act']=="sort") {
		//百分比排序
		//先處理部份學期成績採計學生
		$query="select * from stud_seme_dis where seme_year_seme='$seme_year_seme' and (stud_kind in ('2','7') or sp_cal=1)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$s=$res->fields['student_sn'];
			$cal=array();
			$en_score=array();
			for($i=0;$i<3;$i++) if ($res->fields['enable'.$i]) $cal[]=$i;
			if (count($cal)>0) {
				foreach($cal as $en_seme) {
					$query2="select * from temp_tcc_score where seme='$en_seme' and student_sn='$s' order by ss_no";
					$res2=$CONN->Execute($query2);
					while(!$res2->EOF) {
						$en_score[$res2->fields['ss_no']]+=$res2->fields['score'];
						$res2->MoveNext();
					}
				}
				foreach($en_score as $ss_no=>$sc) {
					$query2="update temp_tcc_score set score='".round($sc/count($cal),2)."' where student_sn='$s' and seme='3' and ss_no='$ss_no'";
					$res2=$CONN->Execute($query2);
				}
			}
			$res->MoveNext();
		}

		//取出入學年
		$stud_study_year=get_stud_study_year($seme_year_seme,$_POST['year_name']);

		//取出採計學期
		for ($i=$starty;$i<=$f_year;$i++) {
			for ($j=1;$j<=2;$j++) {
				$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
			}
		}
		array_pop($semes);
		for($i=1;$i<=count($y_arr);$i++) {
			for($j=1;$j<=count($s_arr);$j++) {
				$temp_arr[$i.$j]=$y_arr[$i].$s_arr[$j];
			}
			$temp2_arr[$i]=$semes[$i-1];
		}
		$s_num=count($semes);//學期總數
		$query="select count(*) as nums from stud_seme_dis where seme_year_seme='$seme_year_seme' and cal>'0'";
		$res=$CONN->Execute($query);
		$stud_num=$res->fields['nums'];//參與成績排序的學生總數
		$aper=$stud_num/100;//每個學生所佔的百分比
		for($i=1;$i<=count($s_arr);$i++) {
			$query="select a.* from temp_tcc_score a left join stud_seme_dis b on a.student_sn=b.student_sn where a.ss_no='$i' and a.seme='$s_num' and b.cal>0 and b.seme_year_seme='$seme_year_seme' order by a.score desc";
			$res=$CONN->Execute($query);
			$j=1;//目前百分比
			$n=0;//目前總人數
			$osc=0;//目前分數
			while(!$res->EOF) {
				$n++;
				$score=$res->fields['score'];
				if ($osc<>$score) {
					$osc=$score;
					while(ceil($aper*$j)<$n) $j++;
				}
				$pr=$j;
				$CONN->Execute("update temp_tcc_score set pr='$pr' where student_sn='".$res->fields['student_sn']."' and seme='".$res->fields['seme']."' and ss_no='".$res->fields['ss_no']."'");
				$res->MoveNext();
			}
		}
		
		//處理特種學生成績
		//科目陣列
		$s_arr=array(1=>"本國語文",2=>"英語",3=>"語文平均",4=>"數學",5=>"社會",6=>"自然與生活科技",7=>"藝術與人文",8=>"健康與體育",9=>"綜合活動",10=>"學期成績平均");
		$query="select * from stud_seme_dis where seme_year_seme='$seme_year_seme' and seme_class like '$seme_class' and sp_kind>'0'";
		$res=$CONN->Execute($query);
		$myplus=array();
		while(!$res->EOF) {
			$sn[]=$res->fields['student_sn'];
			$myplus[$res->fields['student_sn']]=$plus_arr[$res->fields['sp_kind']];
			$res->MoveNext();
		}
		if (count($sn)>0) {
			$allsn="'".implode("','",$sn)."'";
			$query="select * from temp_tcc_score where student_sn in ($allsn)";
			$res=$CONN->Execute($query);
			$rowdata=array();
			while(!$res->EOF) {
				$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']]['score']=$res->fields['score'];
				$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']]['pr']=$res->fields['pr'];
				$res->MoveNext();
			}

			foreach($rowdata as $s=>$d) {
				reset($s_arr);
				foreach($s_arr as $ss_no=>$dd) {
					$plus=1+$myplus[$s]/100;
					$sc=$rowdata[$s][3][$ss_no][score]*$plus;
					$query="select * from temp_tcc_score where seme='3' and ss_no='$ss_no' and score>='$sc' order by pr desc limit 0,1";
					$res=$CONN->Execute($query);
					$upr=$res->fields['pr'];
					$mypr=($upr=="")?1:$upr;
					$query="update temp_tcc_score set sp_score='$sc', sp_pr='$mypr' where seme='3' and ss_no='$ss_no' and student_sn='$s'";
					$res=$CONN->Execute($query);
				}
			}
		}
	}
}

if (($_POST['show'] || $_POST['htm'] || $_POST['out5'] || $_POST['out5s'] || $_POST['out'] || $_POST['out_chc'] || $_POST['out_ct'] || $_POST['LOCK']) && $_POST['year_name']) {
	$seme_class=$_POST[year_name]."%";
	$query="select a.*,b.stud_id,b.stud_name,b.stud_person_id,b.stud_sex,b.stud_birthday,a.zip,a.addr,a.tel,a.cell,a.parent from stud_seme_dis a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$seme_class' order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$seme_class=$res->fields[seme_class];
		$s=$res->fields[student_sn];
		$sn[]=$s;
		$show_sn[$seme_class][$res->fields[seme_num]]=$res->fields[student_sn];
		$stud_data[$s]['stud_name']=$res->fields[stud_name];
		$stud_data[$s]['stud_id']=$res->fields[stud_id];
		$stud_data[$s]['stud_person_id']=$res->fields[stud_person_id];
		$stud_data[$s]['stud_sex']=$res->fields[stud_sex];
		$stud_data[$s]['stud_addr']=$res->fields["addr"];
		$stud_data[$s]['stud_tel']=$res->fields["tel"];
		$stud_data[$s]['stud_cell']=$res->fields["cell"];
		$stud_data[$s]['addr_zip']=$res->fields["zip"];
		$stud_data[$s]['parent_name']=$res->fields["parent"];
		$stud_data[$s]['area1']=$res->fields['area1'];
		$stud_data[$s]['area2']=$res->fields['area2'];
		$stud_data[$s]['stud_kind']=$res->fields['stud_kind'];
		$stud_data[$s]['hand_kind']=$res->fields['hand_kind'];
		$stud_data[$s]['sp_kind']=$res->fields['sp_kind'];
		$stud_data[$s]['lowincome']=$res->fields['lowincome'];
		$stud_data[$s]['unemployed']=$res->fields['unemployed'];
		$stud_data[$s]['midincome']=$res->fields['midincome'];
		$stud_data[$s]['enable0']=$res->fields['enable0'];
		$stud_data[$s]['enable1']=$res->fields['enable1'];
		$stud_data[$s]['enable2']=$res->fields['enable2'];
		$stud_data[$s]['sp_cal']=$res->fields['sp_cal'];
		$d_arr=explode("-",$res->fields[stud_birthday]);
		$dd=$d_arr[0]-1911;
		if (($_POST['out'] && $_POST['cy']==4) || $_POST['out5'] || $_POST['out5s']) {
			$stud_data[$s]['stud_birthday1']=$dd;
			$stud_data[$s]['stud_birthday2']=$d_arr[1];
			$stud_data[$s]['stud_birthday3']=$d_arr[2];
		} else {
			$stud_data[$s]['stud_birthday']=$dd." 年 ".sprintf("%02d",$d_arr[1])." 月 ".sprintf("%02d",$d_arr[2])." 日";
		}
		$res->MoveNext();
	}

	//取出入學年
	$stud_study_year=get_stud_study_year($seme_year_seme,$_POST['year_name']);

	//取出採計學期
	for ($i=$starty;$i<=$f_year;$i++) {
		for ($j=1;$j<=2;$j++) {
			$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
		}
	}
	array_pop($semes);
	for($i=1;$i<=count($y_arr);$i++) {
		for($j=1;$j<=count($s_arr);$j++) {
			$temp_arr[$i.$j]=$y_arr[$i].$s_arr[$j];
		}
		$temp2_arr[$i]=$semes[$i-1];
	}
	$s_num=count($semes);
	$query="select * from temp_tcc_score";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$s=$res->fields['student_sn'];
		$rowdata[$s][$res->fields['seme']][$res->fields['ss_no']]['score']=$res->fields['score'];
		$rowdata[$s][$res->fields['seme']][$res->fields['ss_no']]['pr']=$res->fields['pr'];
		$rowdata[$s][$res->fields['seme']][$res->fields['ss_no']]['sp_score']=$res->fields['sp_score'];
		$rowdata[$s][$res->fields['seme']][$res->fields['ss_no']]['sp_pr']=$res->fields['sp_pr'];
		$res->MoveNext();
	}
	$semes[$s_num]="avg";
	$smarty->assign("pry",$s_num);
}

if ($_POST['LOCK']) {
	$CONN->Execute("drop tables `dis_score_fin`");
	chk_fin();
	foreach($rowdata as $sn=>$d) {
		foreach ($d as $sm=>$dd) {
			foreach ($dd as $ss_no=>$ddd) {
				$query="insert into dis_score_fin (student_sn,year,seme,ss_no,score,pr,sp_score,sp_pr) values ('$sn','$sel_year','$sm','$ss_no','".$ddd['score']."','".$ddd['pr']."','".$ddd['sp_score']."','".$ddd['sp_pr']."')";
				$CONN->Execute($query);
			}
		}
	}
	header("Location: chart.php");
}

if ($_POST['out']) {
	$s=get_school_base();
	require_once "../../include/sfs_case_excel.php";
	$x=new sfs_xls();
	$x->setUTF8();
	$x->setBorderStyle(1);
	$x->addSheet("Sheet1");
	//欄位中文名稱
	if ($_POST['cy']==4)
		$x->setRowText(array("學生姓名","性別","身份證號","出生年","出生月","出生日","學生身分","身心障礙類別","是否免收報名","年級","班級","座號","報名國中代碼(6碼)","報名國中名稱","畢業年度","申請(薦送)高中名稱","家長姓名","電話","行動電話 號碼","郵遞區號","學生地址","國文","英語","語文領域五學期平均","數學領域五學期平均","社會領域五學期平均","自然與生活科技領域五學期平均","健康與體育領域五學期平均","藝術與人文領域五學期平均","綜合活動領域五學期平均","五學期七大領域平均成績(加權)","全校排名百分比(越低成績越好)"));
	else
		$x->setRowText(array("學校代碼","班級","座號","學號","姓名","身分證號","性別","生日","二上國","二上英","二上數","二上社","二上自","二上藝","二上健","二上綜","二上總","二下國","二下英","二下數","二下社","二下自","二下藝","二下健","二下綜","二下總","三上國","三上英","三上數","三上社","三上自","三上藝","三上健","三上綜","三上總","國平","英平","數平","社平","自平","藝平","健平","綜平","總平","國百分","英百分","數百分","社百分","自百分","藝百分","健百分","綜百分","總百分","二上國定","二上英定","二上數定","二上社定","二上自定","二上5定平","二下國定","二下英定","二下數定","二下社定","二下自定","二下5定平","三上國定","三上英定","三上數定","三上社定","三上自定","三上5定平","國定平","英定平","數定平","社定平","自定平","5定平","國定百分","英定百分","數定百分","社定百分","自定百分","5定百分","家長姓名","電話","郵遞區號","地址","報名學校代碼","科別代碼","報名身分","特種身分","二上語","二下語","三上語","語平均","語百分"));
	foreach($show_sn as $seme_class => $d) {
		foreach($d as $site => $sn) {
			$cno=substr($seme_class,-2,2);
			if ($_POST['cy']==4)
				$row_arr=array($stud_data[$sn]['stud_name'],$stud_data[$sn]['stud_sex'],$stud_data[$sn]['stud_person_id'],$stud_data[$sn]['stud_birthday1'],$stud_data[$sn]['stud_birthday2'],$stud_data[$sn]['stud_birthday3'],$stud_data[$sn]['stud_kind'],"","",3,$cno,$site,$s[sch_id],$s[sch_cname],($stud_study_year+3),"","",$stud_data[$sn][stud_tel_1],$stud_data[$sn][stud_tel_1],$stud_data[$sn][addr_zip],$stud_data[$sn][stud_addr_1]);
			else
				$row_arr=array($s[sch_id],$cno,$site,$stud_data[$sn][stud_id],$stud_data[$sn][stud_name],$stud_data[$sn][stud_person_id],$stud_data[$sn][stud_sex],$stud_data[$sn][stud_birthday]);
			if ($_POST['cy']==4) {
				foreach($s_arr as $j => $sl) {
					$row_arr[]=$rowdata[$sn][5][$j][score];
				}
				$row_arr[]=$rowdata[$sn][5][8][pr];
			} else {
				$row_arr2=array();
				foreach($semes as $i => $si) {
					foreach($s_arr as $j => $sl) {
						if ($j==3)
							$row_arr2[]=$rowdata[$sn][$i][$j][score];
						else
							$row_arr[]=$rowdata[$sn][$i][$j][score];
					}
				}
				foreach($s_arr as $j => $sl) {
					if ($j==3)
						$row_arr2[]=$rowdata[$sn][$i][$j][pr];
					else
						$row_arr[]=$rowdata[$sn][$i][$j][pr];
				}
				for($i=0;$i<30;$i++) $row_arr[]="";
				$row_arr[]="";
				$row_arr[]=$stud_data[$sn][stud_tel_1];
				$row_arr[]=$stud_data[$sn][addr_zip];
				$row_arr[]=$stud_data[$sn][stud_addr_1];
				for($i=0;$i<4;$i++) $row_arr[]="";
				foreach($row_arr2 as $d) $row_arr[]=$d;
			}
			$data_arr[]=$row_arr;
		}
	}
	$x->items=$data_arr;
	$x->writeSheet();
	$x->process();
	exit;
} elseif ($_POST['out_ct']) {
	//取出特殊生成績與比序
	$query="select * from temp_tcc_score where sp_score<>'0' and seme='3' order by student_sn,ss_no";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$temp_arr[$res->fields['student_sn']][$res->fields['ss_no']]['sp_score']=$res->fields['sp_score'];
		$temp_arr[$res->fields['student_sn']][$res->fields['ss_no']]['sp_pr']=$res->fields['sp_pr'];
		$res->MoveNext();
	}

	//取出特殊生加分比例
	$query="select * from stud_seme_dis where sp_kind>'0'";
	$res=$CONN->Execute($query);
	$myplus=array();
	while(!$res->EOF) {
		$myplus[$res->fields['student_sn']]=$plus_arr[$res->fields['sp_kind']];
		$res->MoveNext();
	}
	
	$s=get_school_base();
	require_once "../../include/sfs_case_excel.php";
	$x=new sfs_xls();
	$x->setUTF8();
	$x->setBorderStyle(1);
	$x->addSheet("Score");
	$x->filename="Score.xls";
	//欄位中文名稱
	$x->setRowText(array("1.身分證號","2.國語文(平均)","3.英語(平均)","4.數學(平均)","5.社會(平均)","6.自然與生活科技(平均)","7.藝術與人文(平均)","8.健康與體育(平均)","9.綜合活動(平均)","10.國語文(百分比)","11.英語(百分比)","12.數學(百分比)","13.社會(百分比)","14.自然與生活科技(百分比)","15.藝術與人文(百分比)","16.健康與體育(百分比)","17.綜合活動(百分比)","18.國二上平均","19.國二下平均","20.國三上平均","21.3學期總平均","22.3學期百分比","23.國語文(特百分比)","24.英語(特百分比)","25.數學(特百分比)","26.社會(特百分比)","27.自然與生活科技(特百分比)","28.藝術與人文(特百分比)","29.健康與體育(特百分比)","30.綜合活動(特百分比)","31.國二上平均(特)","32.國二下平均(特)","33.國三上平均(特)","34.3學期總平均(特)","35.3學期百分比(特)","36.國語文(8上)","37.英語(8上)","38.數學(8上)","39.社會(8上)","40.自然與生活科技(8上)","41.藝術與人文(8上)","42.健康與體育(8上)","43.綜合活動(8上)","44.國語文(8下)","45.英語(8下)","46.數學(8下)","47.社會(8下)","48.自然與生活科技(8下)","49.藝術與人文(8下)","50.健康與體育(8下)","51.綜合活動(8下)","52.國語文(9上)","53.英語(9上)","54.數學(9上)","55.社會(9上)","56.自然與生活科技(9上)","57.藝術與人文(9上)","58.健康與體育(9上)","59.綜合活動(9上)"));
	foreach($show_sn as $seme_class => $d) {
		foreach($d as $site => $sn) {
			$row_arr=array($stud_data[$sn][stud_person_id]);
			$row_arr2=array();
			$row_arr3=array();
			$avg_arr=array();
			foreach($semes as $i => $si) $avg=$i;
			foreach($s_arr as $j => $sl) $tol=$j;
			foreach($s_arr as $j => $sl) {
				if ($j==$tol)
					$tavg=$rowdata[$sn][$avg][$j][score];
				elseif ($j==3)
					$row_arr2[]=$rowdata[$sn][$avg][$j][score];
				else
					$row_arr[]=$rowdata[$sn][$avg][$j][score];
			}
			foreach($s_arr as $j => $sl) {
				if ($j==$tol) 
					$tpr=$rowdata[$sn][$avg][$j][pr];
				elseif ($j==3)
					$row_arr2[]=$rowdata[$sn][$avg][$j][pr];
				else
					$row_arr[]=$rowdata[$sn][$avg][$j][pr];
			}
			foreach($semes as $i => $si) {
				if ($i==$avg) continue;
				foreach($s_arr as $j => $sl) {
					if ($j==$tol)
						$avg_arr[]=$rowdata[$sn][$i][$j][score];
					elseif ($j==3)
						$row_arr2[]=$rowdata[$sn][$i][$j][score];
					else
						$row_arr3[]=$rowdata[$sn][$i][$j][score];
				}
			}
			foreach($avg_arr as $d) $row_arr[]=$d;
			$row_arr[]=$tavg;
			$row_arr[]=$tpr;
			foreach($s_arr as $j => $sl) {
				if ($j==$tol) 
					$sp_pr=$temp_arr[$sn][$j]['sp_pr'];
				elseif ($j==3)
					$row_arr2[]=$temp_arr[$sn][$j]['sp_pr'];
				else
					$row_arr[]=$temp_arr[$sn][$j]['sp_pr'];
			}
			if ($myplus[$sn]) {
				$row_arr[]=round($row_arr[17]*(1+$myplus[$sn]/100),2);
				$row_arr[]=round($row_arr[18]*(1+$myplus[$sn]/100),2);
				$row_arr[]=round($row_arr[19]*(1+$myplus[$sn]/100),2);
				$row_arr[]=round($temp_arr[$sn][10]['sp_score'],2);
				$row_arr[]=$temp_arr[$sn][10]['sp_pr'];
			} else
				for($i=1;$i<6;$i++) $row_arr[]="";
			foreach($row_arr3 as $d) $row_arr[]=$d;
			$data_arr[]=$row_arr;
			
		}
	}
	$x->items=$data_arr;
	$x->writeSheet();
	$x->process();
	exit;
} elseif ($_POST['out5']) {
	$s=get_school_base();
	require_once "../../include/sfs_case_excel.php";
	$x=new sfs_xls();
	$x->setUTF8();
	$x->setBorderStyle(1);
	$x->addSheet("student");
	//欄位中文名稱
	$x->setRowText(array("考區代碼","學校代碼","報名序號","學號","班級","座號","學生姓名","身分證號","性別","出生年","出生月","出生日","畢業學校","畢業年度","畢肄業","學生身分","身心障礙","分發區","低收入戶","失業勞工","中低收入戶","資料授權","家長姓名","緊急連絡電話","郵遞區號","地址","國文","英文","數學","社會","自然","健康與體育","藝術與人文","綜合活動","總平均","國文(特種生加分後)","英文(特種生加分後)","數學(特種生加分後)","社會(特種生加分後)","自然(特種生加分後)","健康與體育(特種生加分後)","藝術與人文(特種生加分後)","綜合活動(特種生加分後)","總平均(特種生加分後)"));
	foreach($show_sn as $seme_class => $d) {
		foreach($d as $site => $sn) {
			$cno=substr($seme_class,-2,2);
			$row_arr=array($stud_data[$sn]['area1'],$s['sch_id'],"",$stud_data[$sn]['stud_id'],$cno,$site,$stud_data[$sn]['stud_name'],$stud_data[$sn]['stud_person_id'],$stud_data[$sn]['stud_sex'],$stud_data[$sn]['stud_birthday1'],$stud_data[$sn]['stud_birthday2'],$stud_data[$sn]['stud_birthday3'],$s['sch_id'],($sel_year+1),1,$stud_data[$sn]['stud_kind'],$stud_data[$sn]['hand_kind'],$stud_data[$sn]['area1'],$stud_data[$sn]['lowincome'],$stud_data[$sn]['unemployed'],$stud_data[$sn]['midincome'],1,$stud_data[$sn]['parent_name'],$stud_data[$sn]['stud_tel'],$stud_data[$sn]['addr_zip'],$stud_data[$sn]['stud_addr']);
			$row_arr2=array();
			foreach($s_arr as $j => $sl) {
				if ($j==3)
					$row_arr2[]=$rowdata[$sn][3][$j][pr];
				else
					$row_arr[]=$rowdata[$sn][3][$j][pr];
			}
			if ($stud_data[$sn]['sp_kind']) {
				foreach($s_arr as $j => $sl) {
					if ($j==3)
						$row_arr2[]=$rowdata[$sn][3][$j][sp_pr];
					else
						$row_arr[]=$rowdata[$sn][3][$j][sp_pr];
				}
			} else
				for($i=0;$i<9;$i++) $row_arr[]="";
			//健體和藝文位置對調
			$p=$row_arr[32]; $row_arr[32]=$row_arr[31]; $row_arr[31]=$p;
			$p=$row_arr[40]; $row_arr[40]=$row_arr[41]; $row_arr[41]=$p;
			$data_arr[]=$row_arr;
		}
	}
	$x->items=$data_arr;
	$x->writeSheet();
	$x->process();
	exit;
} elseif ($_POST['out_chc']) {
	$s=get_school_base();
	require_once "../../include/sfs_case_excel.php";
	$x=new sfs_xls();
	$x->setUTF8();
	$x->setBorderStyle(1);
	$x->addSheet("Sheet1");
	//欄位中文名稱
	$x->setRowText(array("學生姓名","身分證字號","學生身分","身心障礙學生","學業成績總平均","學業成績全校排名百分比","國文成績總平均","國文成績全校排名百分比","英語成績總平均","英語成績全校排名百分比","數學成績總平均","數學成績全校排名百分比","社會成績總平均","社會成績全校排名百分比","自然成績總平均","自然成績全校排名百分比","藝術與人文成績總平均","藝術與人文成績全校排名百分比","健康與體育成績總平均","健康與體育成績全校排名百分比","綜合活動成績總平均","綜合活動成績全校排名百分比","學業成績總平均","學業成績全校排名百分比","國文成績總平均","國文成績全校排名百分比","英語成績總平均","英語成績全校排名百分比","數學成績總平均","數學成績全校排名百分比","社會成績總平均","社會成績全校排名百分比","自然成績總平均","自然成績全校排名百分比","藝術與人文成績總平均","藝術與人文成績全校排名百分比","健康與體育成績總平均","健康與體育成績全校排名百分比","綜合活動成績總平均","綜合活動成績全校排名百分比"));
	foreach($show_sn as $seme_class => $d) {
		foreach($d as $site => $sn) {
			$cno=substr($seme_class,-2,2);
			$row_arr=array($stud_data[$sn][stud_name],$stud_data[$sn][stud_person_id],"","","","");
			$row_arr2=array();
			foreach($s_arr as $j => $sl) {
				if ($j==3)
					$row_arr2[]=$rowdata[$sn][3][$j][pr];
				elseif ($j==10) {
					$row_arr[4]=$rowdata[$sn][3][$j][score];
					$row_arr[5]=$rowdata[$sn][3][$j][pr];
				} else {
					$row_arr[]=$rowdata[$sn][3][$j][score];
					$row_arr[]=$rowdata[$sn][3][$j][pr];
				}
			}
			//預留兩欄
			$row_arr[]="";
			$row_arr[]="";
			if ($stud_data[$sn]['sp_kind']) {
				foreach($s_arr as $j => $sl) {
					if ($j==3) {
						$row_arr2[]=$rowdata[$sn][3][$j][sp_score];
						$row_arr2[]=$rowdata[$sn][3][$j][sp_pr];
					} elseif ($j==10) {
						$row_arr[22]=$rowdata[$sn][3][$j][sp_score];
						$row_arr[23]=$rowdata[$sn][3][$j][sp_pr];
					} else {
						$row_arr[]=$rowdata[$sn][3][$j][sp_score];
						$row_arr[]=$rowdata[$sn][3][$j][sp_pr];
					}
				}
			} else
				for($i=0;$i<16;$i++) $row_arr[]="";
			$data_arr[]=$row_arr;
		}
	}
	$x->items=$data_arr;
	$x->writeSheet();
	$x->process();
	exit;
} elseif ($_POST['out5s']) {
	$s=get_school_base();
	require_once "../../include/sfs_case_excel.php";
	$x=new sfs_xls();
	$x->setUTF8();
	$x->setBorderStyle(1);
	$x->addSheet("Sheet1");
	//欄位中文名稱
	$x->setRowText(array("考區代碼","學校代碼","報名序號","學號","班級","座號","姓名","身分證統一編號","性別","出生年","出生月","出生日","畢業學校代碼","畢業年度","肄畢業狀況","學生身分","身心障礙考生","分發區","低收入戶","失業勞工子女","資料授權","家長姓名","緊急聯絡市話","郵遞區號","地址","緊急聯絡手機","免試學校代碼","國中報名序號","國文百分比","英語百分比","數學百分比","社會百分比","自然百分比","藝術百分比","健康百分比","綜合百分比","總成績百分比","特種身分","特國文百分比","特英語百分比","特數學百分比","特社會百分比","特自然百分比","特藝術百分比","特健康百分比","特綜合百分比","特總成績百分比","八上國","八上英","八上數","八上社","八上自","八上藝","八上健","八上綜","八下國","八下英","八下數","八下社","八下自","八下藝","八下健","八下綜","九上國","九上英","九上數","九上社","九上自","九上藝","九上健","九上綜"));
	foreach($show_sn as $seme_class => $d) {
		foreach($d as $site => $sn) {
			$cno=substr($seme_class,-2,2);
			$row_arr=array($stud_data[$sn]['area1'],$s['sch_id'],"",$stud_data[$sn][stud_id],$cno,$site,$stud_data[$sn][stud_name],$stud_data[$sn][stud_person_id],$stud_data[$sn][stud_sex],$stud_data[$sn][stud_birthday1],$stud_data[$sn][stud_birthday2],$stud_data[$sn][stud_birthday3],$s['sch_id'],($sel_year+1),1,$stud_data[$sn]['stud_kind'],$stud_data[$sn]['hand_kind'],$stud_data[$sn]['area2'],$stud_data[$sn]['lowincome'],$stud_data[$sn]['unemployed'],1,$stud_data[$sn][parent_name],$stud_data[$sn][stud_tel],$stud_data[$sn][addr_zip],$stud_data[$sn][stud_addr],$stud_data[$sn][stud_cell],"","");
			$row_arr2=array();
			foreach($s_arr as $j => $sl) {
				if ($j==3)
					$row_arr2[]=$rowdata[$sn][3][$j][pr];
				else
					$row_arr[]=$rowdata[$sn][3][$j][pr];
			}
			$row_arr[]=$stud_data[$sn]['sp_kind'];
			if ($stud_data[$sn]['sp_kind']) {
				foreach($s_arr as $j => $sl) {
					if ($j==3)
						$row_arr2[]=$rowdata[$sn][3][$j][sp_pr];
					else
						$row_arr[]=$rowdata[$sn][3][$j][sp_pr];
				}
			} else
				for($i=0;$i<9;$i++) $row_arr[]="";
			foreach($semes as $i => $si) {
				foreach($s_arr as $j => $sl) {
					if ($j==3 || $j>9 || $i==3)
						$row_arr2[]=$rowdata[$sn][$i][$j][score];
					else
						$row_arr[]=$rowdata[$sn][$i][$j][score];
				}
			}
			$data_arr[]=$row_arr;
		}
	}
	$x->items=$data_arr;
	$x->writeSheet();
	$x->process();
	exit;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","免試入學報表"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name]));
$smarty->assign("seme_year_seme",$seme_year_seme);
$smarty->assign("curr_year",curr_year());//加入學年度
$smarty->assign("semes",$semes);
$smarty->assign("student_sn",$show_sn);
$smarty->assign("stud_data",$stud_data);
$smarty->assign("rowdata",$rowdata);
$smarty->assign("col_arr",$temp_arr);
$smarty->assign("s_arr",$s_arr);
$smarty->assign("sch_arr",get_school_base());
$smarty->assign("ss_link",$ss_link);
if ($_POST['htm']) {
	$smarty->assign("s_arr",array(1=>"本國語文",2=>"英語",3=>"語文平均",4=>"數學",5=>"社會",6=>"自然與生活科技",7=>"藝術與人文",8=>"健康與體育",9=>"綜合活動",10=>"學期成績平均"));
//	$smarty->assign("s_arr",array(1=>"本國語文",2=>"英語",3=>"語文平均",4=>"數學",5=>"自然與生活科技",6=>"社會",7=>"健康與體育",8=>"藝術與人文",9=>"綜合活動",10=>"學期成績平均"));
	$smarty->display("stud_basic_test_distest5_print.tpl");
} else {
	$smarty->display("stud_basic_test_distest5.tpl");
}
?>
