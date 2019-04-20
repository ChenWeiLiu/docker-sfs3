<?php

// $Id: distest4.php 9240 2018-05-04 03:25:04Z igogo $

// --系統設定檔
include "select_data_config.php";

//認證
sfs_check();

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
if ($_POST['step']=="" && $_POST['year_name']) $_POST['next']=1;
if ($_POST['next']) $_POST['step']+=1;

if ($_POST['year_name']) {
	//取出入學年
	$stud_study_year=get_stud_study_year($seme_year_seme,$_POST['year_name']);

	//取出檢查用學期資料
	for ($i=1;$i<=2;$i++) {
		for ($j=1;$j<=2;$j++) {
			$semes[]=($stud_study_year+$i).$j;
		}
	}
	array_pop($semes);

	//檢查成績是否已封存
	$chk=chk_dis($sel_year,$semes);
	if ($chk[3]) header("Location: chart.php");
	else $semes=array();

	//刪除轉學生多餘成績
	if ($_POST['del'] && $_POST['step']==3) {
		//先算出要處理的學期
		$seme_arr=array();
		$tbl_arr=array();
		for($i=$stud_study_year;$i<=$stud_study_year+2;$i++) {
			for($j=1;$j<=2;$j++) {
				$seme_arr[]=$i.$j;
				$tbl_arr[$i.$j]="score_semester_".$i."_".$j;
			}
		}
		$seme_str="and move_year_seme in ('".implode("','",$seme_arr)."')";
		//取出所有轉入學生資料
		$query="select * from stud_move where move_kind='2' $seme_str order by move_date";
		$res=$CONN->Execute($query);
		$sn_arr=array();
		$rowdata=array();
		while(!$res->EOF) {
			reset($seme_arr);
			foreach($seme_arr as $d) {
				if ($d < $res->fields['move_year_seme']) $rowdata[$d][]=$res->fields['student_sn'];
			}
			$res->MoveNext();
		}
		//刪除不應存在的資料
		foreach($rowdata as $s=>$d) {
			if (count($d)>0) {
				$sn_str="'".implode("','",$d)."'";
				$query="delete from ".$tbl_arr[$s]." where student_sn in ($sn_str)";
				$CONN->Execute($query);
			}
		}
		//處理完陣列清空
		$rowdata=array();
	}

	if (intval($_POST['step']==0)) $_POST['step']=1;
	switch($_POST['step']) {
		case 1:
			if ($stud_study_year) {
				for($i=$stud_study_year;$i<=$stud_study_year+2;$i++) {
					for($j=1;$j<=2;$j++) {
						$y_arr[sprintf("%03d",$i).$j]="&nbsp; ".$i."學年度第".$j."學期 &nbsp;";
					}
				}
				$smarty->assign("year_arr",$y_arr);
				//取出己存紀錄
				$temp_arr=array();
				$query="select b.year,b.semester from dis_score_ss a left join score_ss b on a.ss_id=b.ss_id where a.year='$sel_year' order by b.year,b.semester";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$seme=sprintf("%03d",$res->fields['year']).$res->fields['semester'];
					$temp_arr[$seme]=$seme;
					$res->MoveNext();
				}
				$smarty->assign("seme_arr",$temp_arr);
			}
			break;
		case 2:
			if (count($_POST['seme'])>0 && $stud_study_year) {
				$temp_arr=array();
				//取出科目名陣列
				$subj_arr=array();
				$query="select * from score_subject order by subject_id";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$subj_arr[$res->fields['subject_id']]=$res->fields['subject_name'];
					$res->MoveNext();
				}
				foreach($_POST['seme'] as $k=>$v) {
					if (strlen($v)==4) {
						$year=intval(substr($v,0,3));
						$semester=intval(substr($v,-1,1));
						if ($year && $semester) {
							$query="select * from score_ss where year='$year' and semester='$semester' and class_year='".($year-$stud_study_year+$IS_JHORES+1)."' and enable='1' and print='1' and need_exam='1' order by sort,sub_sort";
							$res=$CONN->Execute($query);
							while(!$res->EOF) {
								$temp_arr[$res->fields['ss_id']][year]=$year;
								$temp_arr[$res->fields['ss_id']][semester]=$semester;
								if ($res->fields['subject_id']) $temp_arr[$res->fields['ss_id']][name]=$subj_arr[$res->fields['subject_id']];
								else $temp_arr[$res->fields['ss_id']][name]=$subj_arr[$res->fields['scope_id']];
								$temp_arr[$res->fields['ss_id']][class_id]=$res->fields['class_id'];
								$res->MoveNext();
							}
							$query="select ss_id,count(ss_id) as n from score_semester_".$year."_".$semester." where test_kind='定期評量' group by ss_id";
							$res=$CONN->Execute($query);
							while(!$res->EOF) {
								if ($temp_arr[$res->fields['ss_id']][year]==$year) $temp_arr[$res->fields['ss_id']][num]=$res->fields['n'];
								$res->MoveNext();
							}
						}
					}
				}
				$smarty->assign("ss_arr",$temp_arr);
				//取出己存紀錄
				$subj_no_arr=array("chinese"=>1,"english"=>2,"math"=>3,"nature"=>4,"social"=>5);
				$query="select * from dis_score_ss order by ss_id";
				$res=$CONN->Execute($query);
				$temp_arr=array();
				while(!$res->EOF) {
					$temp_arr[$res->fields['ss_id']]=$subj_no_arr[$res->fields['subject']];
					$res->MoveNext();
				}
				$smarty->assign("m_arr",array(0=>"不採計",1=>"本國語文",2=>"英語",3=>"數學",4=>"自然與生活科技",5=>"社會"));
				$smarty->assign("subj_arr",$temp_arr);
				$smarty->assign("seme_arr",$_POST['seme']);
			}
			break;
		case 3:
			if (count($_POST['seme'])>0) {
				//來自步驟二
				foreach($_POST['seme'] as $d) {
					$y=substr($d,0,3);
					$j=substr($d,-1,1);
					$semes[$d]=$y."學年度第".$j."學期";
				}

				//更新免試入學科目表
				$query="delete from dis_score_ss where year='$sel_year' and class_year='".$_POST['year_name']."'";
				$CONN->Execute($query);

				//對應科目陣列
				$subj_arr=array(1=>"chinese",2=>"english",3=>"math",4=>"nature",5=>"social");
				foreach($_POST['sel'] as $ss_id => $subj_id) {
					if ($subj_id) {
						$query="insert into dis_score_ss (ss_id,subject,year,class_year) values ('$ss_id','".$subj_arr[$subj_id]."','$sel_year','".$_POST['year_name']."')";
						$CONN->Execute($query);
					}
				}
			} else {
				//按下確定儲存
				$query="select b.year,b.semester from dis_score_ss a left join score_ss b on a.ss_id=b.ss_id where a.year='$sel_year' order by b.year,b.semester";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$d=sprintf("%03d",$res->fields['year']).$res->fields['semester'];
					$semes[$d]=sprintf("%03d",$res->fields['year'])."學年度第".$res->fields['semester']."學期";
					$res->MoveNext();
				}

				//先把成績都改成不採計
				if (count($_POST['sn'])>0) {
					$sn_str="'".implode("','",$_POST['sn'])."'";
					$query="update score_semester_move set enable='0' where student_sn in ($sn_str)";
					$CONN->Execute($query);
				}
				//處理成績採計與否
				if (count($_POST['sel'])>0) {
					foreach($_POST['sel'] as $sn=>$v) {
						foreach($v as $ys=>$vv) {
							$year=intval(substr($ys,0,3));
							$semester=substr($ys,-1,1);
							foreach($vv as $t=>$vvv) {
								//再把勾選的成績改成採計
								$query="update score_semester_move set enable='1' where student_sn='$sn' and year='$year' and semester='$semester' and test_sort='$t'";
								$CONN->Execute($query);
							}
						}
					}
				}
			}
	
			//取出各學期定考次數
			foreach($semes as $d=>$v) {
				$y=substr($d,0,3);
				$j=substr($d,-1,1);
				$db[]="score_semester_$y_$j";
				$query="select * from score_setup where year='".intval($y)."' and semester='$j' and class_year='".($y-$stud_study_year+$IS_JHORES+1)."' and enable='1'";
				$res=$CONN->Execute($query);
				$times[$d]=$res->fields['performance_test_times'];
			}

			//取出所有轉入學生資料
			$query="select * from stud_move where move_kind='2' order by move_date";
			$res=$CONN->Execute($query);
			$sn_arr=array();
			$rowdata=array();
			while(!$res->EOF) {
				$sn_arr[]=$res->fields['student_sn'];
				$rowdata[$res->fields['student_sn']][move_year_seme]=sprintf("%04d",$res->fields['move_year_seme']);
				$rowdata[$res->fields['student_sn']][move_date]=$res->fields['move_date'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$sn_str="'".implode("','",$sn_arr)."'";
				$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '".$_POST['year_name']."%' and student_sn in ($sn_str)";
				$res=$CONN->Execute($query);
				$sn_arr=array();
				while(!$res->EOF) {
					$sn_arr[]=$res->fields['student_sn'];
					$rowdata[$res->fields['student_sn']][seme_class]=$res->fields['seme_class'];
					$rowdata[$res->fields['student_sn']][seme_num]=$res->fields['seme_num'];
					$res->MoveNext();
				}
				if (count($sn_arr)>0) {
					$sn_str="'".implode("','",$sn_arr)."'";
					$query="select * from stud_base where student_sn in ($sn_str) and stud_study_cond in ('0','5','15') order by curr_class_num";
					$res=$CONN->Execute($query);
					$sn_arr=array();
					while(!$res->EOF) {
						$sn_arr[]=$res->fields['student_sn'];
						$rowdata[$res->fields['student_sn']][stud_name]=$res->fields['stud_name'];
						$rowdata[$res->fields['student_sn']][stud_sex]=$res->fields['stud_sex'];
						$res->MoveNext();
					}
					//取出採計與否資料
					$query="select * from score_semester_move where student_sn in ($sn_str) order by student_sn,year,semester,test_sort,enable";
					$res=$CONN->Execute($query);
					$osn=0;
					while(!$res->EOF) {
						$sn=$res->fields['student_sn'];
						if ($sn!=$osn) {
							if ($osn>0) $rowdata[$osn]['testdata']=$temp_arr;
							$temp_arr=array();
							$osn=$sn;
						}
						$temp_arr[(sprintf("%03d",$res->fields['year']).$res->fields['semester'])][$res->fields['test_sort']]=$res->fields['enable'];
						$res->MoveNext();
					}
					if ($osn>0) $rowdata[$osn]['testdata']=$temp_arr;
				}
			}
			$smarty->assign("sn_arr",$sn_arr);
			$smarty->assign("rowdata",$rowdata);
			$smarty->assign("semes",$semes);
			$smarty->assign("times",$times);
			break;
		case 4:
			if ($_POST['act']=="cal") {
				//取出學生資料
				$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class = '".$_POST['class_no']."'";
				$res=$CONN->Execute($query);
				$sn_arr=array();
				while(!$res->EOF) {
					$sn_arr[]=$res->fields['student_sn'];
					$res->MoveNext();
				}
				if (count($sn_arr)>0) {
					$sn_str="'".implode("','",$sn_arr)."'";
					$query="select * from stud_base where student_sn in ($sn_str) and stud_study_cond in ('0','5','15') order by curr_class_num";
					$res=$CONN->Execute($query);
					$sn_arr=array();
					while(!$res->EOF) {
						$sn_arr[]=$res->fields['student_sn'];
						$res->MoveNext();
					}
				}
				//取出科目對應資料
				$query="select a.ss_id,a.subject,b.year,b.semester from dis_score_ss a left join score_ss b on a.ss_id=b.ss_id where a.year='$sel_year' and a.class_year='".$_POST['year_name']."' order by b.year,b.semester";
				$res=$CONN->Execute($query);
				$subj=array();
				$subj_arr=array();
				while(!$res->EOF) {
					$d=sprintf("%03d",$res->fields['year']).$res->fields['semester'];
					if ($semes[$d]=="") $semes[$d]="score_semester_".$res->fields['year']."_".$res->fields['semester'];
					$subj[$res->fields['ss_id']]=$res->fields['subject'];
					$subj_arr[$d][]=$res->fields['ss_id'];
					$res->MoveNext();
				}
				$move_subj=array(1=>"chinese",2=>"english",3=>"math",4=>"nature",5=>"social");
				//取出段考資料
				$rowdata=array();
				if (count($sn_arr)>0) {
					$sn_str="'".implode("','",$sn_arr)."'";
					//取出轉入前段考成績(非本校)
					$query="select * from score_semester_move where student_sn in ($sn_str) and test_kind='定期評量' and enable='1' order by student_sn,year,semester";
					$res=$CONN->Execute($query);
					while(!$res->EOF) {
						$sc=($res->fields['score']=="-100")?0:$res->fields['score'];
						$rowdata[$res->fields['student_sn']][(sprintf("%03d",$res->fields['year']).$res->fields['semester'])][$move_subj[$res->fields['ss_id']]][$res->fields['test_sort']][]=$sc;
						$res->MoveNext();
					}
					//取出本校段考成績
					foreach($semes as $d => $tbl) {
						$y=substr($d,0,3);
						$s=substr($d,-1,1);
						if (count($subj_arr[$d])>0)
							$subj_str="and ss_id in ('".implode("','",$subj_arr[$d])."')";
						else
							$subj_str="";
						$query="select * from $tbl where student_sn in ($sn_str) $subj_str and test_kind='定期評量' and test_sort<200";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							//如果沒有輸入成績, 則認定為0分
							$sc=($res->fields['score']=="-100")?0:$res->fields['score'];
							$rowdata[$res->fields['student_sn']][$d][$subj[$res->fields['ss_id']]][$res->fields['test_sort']][]=$sc;
							$res->MoveNext();
						}
					}
				}
				//計算各平均
				if (count($rowdata)>0) {
					foreach($rowdata as $sn=>$d) {
						$rowdata2=array();
						foreach($d as $sy=>$dd) {
							$y=intval(substr($sy,0,3));
							$s=substr($sy,-1,1);
							foreach($dd as $sj=>$ddd) {
								if ($sj=="avg") continue;
								$i=0;
								$sgall=0;
								foreach($ddd as $sg=>$dddd) {
									if ($sg=="avg") continue;
									$ii=0;
									$sall=0;
									foreach($dddd as $n=>$sc) {
										//統計單科單階段成績數
										$ii++;
										//統計單科單階段總分
										$sall+=$sc;
									}
									//統計單科階段數
									$i++;
									//計算單科階段平均
									$avg=round($sall/$ii,2);
									$rowdata[$sn][$sy][$sj][$sg][avg]=$avg;
									$CONN->Execute("insert into dis_stage (year,semester,student_sn,subject,stage,score) values ('$y','$s','$sn','$sj','$sg','$avg')");
									//統計單科單學期總分
									$sgall+=$avg;
								}
								//計算單科單學期平均
								$avg=round($sgall/$i,2);
								$rowdata[$sn][$sy][$sj][avg]=$avg;
								$CONN->Execute("insert into dis_stage (year,semester,student_sn,subject,stage,score) values ('$y','$s','$sn','$sj','avg','$avg')");
								$rowdata2[$sj][$sy]=$avg;
							}
						}
						//計算各科總平均
						$snall=0;
						foreach($rowdata2 as $sj=>$dd) {
							$i=0;
							$sjavg=0;
							foreach($dd as $sy=>$sc) {
								//統計單科學期數
								$i++;
								//統計單科總分
								$sjavg+=$sc;
							}
							//計算單科平均
							$avg=round($sjavg/$i,2);
							$CONN->Execute("insert into dis_stage (year,semester,student_sn,subject,stage,score) values ('999','1','$sn','$sj','avg','$avg')");
							$snall+=$avg;
						}
						//計算總平均, 目前統一除以五(也就是以五科計算)
						$CONN->Execute("insert into dis_stage (year,semester,student_sn,subject,stage,score) values ('999','1','$sn','avg','avg','".round($snall/5,2)."')");
					}
				}
				header("Content-type: text/html; charset=big5");
				echo $_POST['class_no']."...計算完成!";
				exit;
			}

			//清空資料表
			$CONN->Execute("delete from dis_stage");
			//取得班級陣列
			$seme_class=intval($_POST[year_name])."%";
			$query="select distinct seme_class from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$seme_class%' order by seme_class";
			$res=$CONN->Execute($query);
			$class_arr=array();
			while(!$res->EOF) {
				$class_arr[$res->fields['seme_class']]=$res->fields['seme_class'];
				$res->MoveNext();
			}
			$smarty->assign("class_arr",$class_arr);
			break;
		case 5:
			$s_arr=array(1=>"國",2=>"英",3=>"數",4=>"自",5=>"社");
			$y_arr=array(1=>"二上",2=>"二下",3=>"三上");
			$ss_map=array("chinese"=>1,"english"=>2,"math"=>3,"nature"=>4,"social"=>5,"avg"=>6);
			//取出學生資料
			$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
			$seme_class=$_POST[year_name]."%";
			$query="select a.*,b.stud_name,b.stud_person_id,b.stud_sex,b.stud_birthday from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$seme_class' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
			$res=$CONN->Execute($query);
			$sn=array();
			$show_sn=array();
			$stud_data=array();
			while(!$res->EOF) {
				$seme_class=$res->fields[seme_class];
				$sn[]=$res->fields[student_sn];
				$show_sn[$seme_class][$res->fields[seme_num]]=$res->fields[student_sn];
				$stud_data[$res->fields[student_sn]][stud_name]=$res->fields[stud_name];
				$stud_data[$res->fields[student_sn]][stud_id]=$res->fields[stud_id];
				$stud_data[$res->fields[student_sn]][stud_person_id]=$res->fields[stud_person_id];
				$stud_data[$res->fields[student_sn]][stud_sex]=$res->fields[stud_sex];
				$d_arr=explode("-",$res->fields[stud_birthday]);
				$dd=$d_arr[0]-1911;
				$stud_data[$res->fields[student_sn]][stud_birthday]=$dd." 年 ".sprintf("%02d",$d_arr[1])." 月 ".sprintf("%02d",$d_arr[2])." 日";
				$res->MoveNext();
			}
			$stud_num=count($sn);

			$temp_arr=array();
			//列印校對單與證明單
			if ($_POST['CHK'] || $_POST['CRT'] || $_POST['LOCK']) {
				//統計男女生人數
				$query="select count(b.student_sn) as num,b.stud_sex from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '".$_POST[year_name]."%' and b.stud_study_cond in ('0','15') group by b.stud_sex order by b.stud_sex";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$s="stud_num_".$res->fields['stud_sex'];
					$$s=$res->fields['num'];
					$smarty->assign("sex".$res->fields['stud_sex'],$res->fields['num']);
					$res->MoveNext();
				}
				for ($i=1;$i<=2;$i++) {
					for ($j=1;$j<=2;$j++) {
						$temp_arr[sprintf("%03d",$stud_study_year+$i).$j]=($stud_study_year+$i)."學年度第".$j."學期";
					}
				}
				array_pop($temp_arr);
				$temp_arr2=array();
				$query="select distinct concat(year,semester,stage),year,semester,stage from dis_stage";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					for($i=1;$i<=$res->fields['stage'];$i++) $temp_arr2[(sprintf("%03d",$res->fields['year']).$res->fields['semester'])][$i]=$i;
					$res->MoveNext();
				}
				//取出成績
				$query="select * from dis_stage";
				$res=$CONN->Execute($query);
				$rowdata=array();
				while(!$res->EOF) {
					$sc=($res->fields['score']=="-100")?0:$res->fields['score'];
					$rowdata[$res->fields['student_sn']][(sprintf("%03d",$res->fields['year']).$res->fields['semester'])][$res->fields['stage']][$res->fields['subject']][score]=$sc;
					$res->MoveNext();
				}

				foreach($ss_map as $sj=>$sno) {
					$query="select * from dis_stage where stage='avg' and subject='$sj' and year='999' and semester='1' order by score desc";
					$res=$CONN->Execute($query);
					$j=1;
					$j1=1;
					$j2=1;
					$opr=0;
					$opr1=0;
					$opr2=0;
					$osc=0;
					$osc1=0;
					$osc2=0;
					while(!$res->EOF) {
						$score=$res->fields['score'];
						if ($_POST['cy']==2) {
							//判斷是男女生
							$sex=$stud_data[$res->fields[student_sn]][stud_sex];
							$s="stud_num_".$sex;
							$jj="j".$sex;
							$p="opr".$sex;
							$o="osc".$sex;
							//計算全年級前百分比(彰化區)
							if ($osc<>$score) {
								$osc=$score;
								$opr=$j;//在此用作記錄名次
							}
							//計算男女生前百分比
							if ($$o<>$score) {
								$$o=$score;
								$$p=$$jj;
							}
							//無條件進入
							$rowdata[$res->fields['student_sn']][sprintf("%03d",$res->fields['year']).$res->fields['semester']]['avg'][$res->fields['subject']][pr]=ceil($opr/$stud_num*100);
							$rowdata[$res->fields['student_sn']][sprintf("%03d",$res->fields['year']).$res->fields['semester']]['avg'][$res->fields['subject']][pr2]=ceil($$p/$$s*100);
						} else {
							//計算pr值(中投區)
							if ($stud_num<100)
								$pr=intval(98*($stud_num-$j)/($stud_num-1))+1;
							else
								$pr=intval(99*($stud_num-$j)/$stud_num)+1;
							if ($opr!=0) {
								//如果分數與前一人相同, PR值卻不同時, PR值應與前一人同
								if ($oscore==$score && $opr!=$pr) $pr=$opr;
								else {
									$oscore=$score;
									$opr=$pr;
								}
							}
							$rowdata[$res->fields['student_sn']][sprintf("%03d",$res->fields['year']).$res->fields['semester']]['avg'][$res->fields['subject']][pr]=$pr;
						}
						$j++;
						$$jj++;
						$res->MoveNext();
					}
				}

				//封存成績
				if ($_POST['LOCK']) {
					foreach($rowdata as $sn=>$d) {
						foreach($d as $sys=>$dd) {
							$y=intval(substr($sys,0,-1));
							$s=substr($sys,-1,1);
							foreach($dd as $stage=>$ddd) {
								foreach($ddd as $subj=>$dddd) {
									$query="insert into dis_stage_fin (year,semester,student_sn,subject,stage,score) value ('$y','$s','$sn','$subj','$stage','".$dddd['score']."')";
									$CONN->Execute($query);
									if ($dddd['pr']) {
										$query="insert into dis_stage_fin (year,semester,student_sn,subject,stage,score) value ('$y','$s','$sn','$subj','pr','".$dddd['pr']."')";
										$CONN->Execute($query);
									}
								}
							}
						}
					}
					header("Location: chart.php");
				}
				$smarty->assign("sch_arr",get_school_base());
				$smarty->assign("student_sn",$show_sn);
				$smarty->assign("stud_data",$stud_data);
				$smarty->assign("s_arr",array("chinese"=>"本國語文","english"=>"英語","math"=>"數學","nature"=>"自然與科技","social"=>"社會"));
				$smarty->assign("seme_arr",$temp_arr);
				$smarty->assign("stage_arr",$temp_arr2);
				$smarty->assign("rowdata",$rowdata);
				$smarty->assign("sex0",$stud_num);
				if ($_POST['CRT'] && $_POST['cy']==2)
					$smarty->display("stud_basic_test_distest4_print_chc.tpl");
				elseif ($_POST['CRT'] && $_POST['cy']==1)
					$smarty->display("stud_basic_test_distest4_print_tcc.tpl");
				else
					$smarty->display("stud_basic_test_distest4_print.tpl");
				exit;
			}
			//取出學期資料
			for($i=1;$i<=count($y_arr);$i++) {
				for($j=1;$j<=count($s_arr);$j++) {
					$temp_arr[$i.$j]=$y_arr[$i].$s_arr[$j];
				}
			}
			for ($i=1;$i<=2;$i++) {
				for ($j=1;$j<=2;$j++) {
					$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
				}
			}
			array_pop($semes);
			//取出成績
			$query="select * from dis_stage";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sc=($res->fields['score']=="-100")?0:$res->fields['score'];
				$rowdata[$res->fields['student_sn']][sprintf("%03d",$res->fields['year']).$res->fields['semester']][$ss_map[$res->fields['subject']]][score]=$sc;
				$res->MoveNext();
			}
			foreach($ss_map as $sj=>$sno) {
				$query="select * from dis_stage where stage='avg' and subject='$sj' and year='999' and semester='1' order by score desc";
				$res=$CONN->Execute($query);
				$j=1;
				$opr=0;
				$osc=0;
				while(!$res->EOF) {
					$score=$res->fields['score'];
					//計算pr值(中投區)
					if ($stud_num<100)
						$pr=intval(98*($stud_num-$j)/($stud_num-1))+1;
					else
						$pr=intval(99*($stud_num-$j)/$stud_num)+1;
					if ($opr!=0) {
						//如果分數與前一人相同, PR值卻不同時, PR值應與前一人同
						if ($oscore==$score && $opr!=$pr) $pr=$opr;
						else {
							$oscore=$score;
							$opr=$pr;
						}
					}
					$rowdata[$res->fields['student_sn']][sprintf("%03d",$res->fields['year']).$res->fields['semester']][$ss_map[$res->fields['subject']]][pr]=$pr;
					$j++;
					$res->MoveNext();
				}
			}
			//匯出Excel表
			if ($_POST['XLS']) {
				$s=get_school_base();
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(1);
				$x->addSheet("Sheet1");
				$x->setRowText(array("學校代碼","班級","座號","學號","姓名","身分證號","性別","生日","二上國","二上英","二上數","二上自","二上社","二上健","二上藝","二上綜","二上總","二下國","二下英","二下數","二下自","二下社","二下健","二下藝","二下綜","二下總","三上國","三上英","三上數","三上自","三上社","三上健","三上藝","三上綜","三上總","國平","英平","數平","自平","社平","健平","藝平","綜平","總平","國PR","英PR","數PR","自PR","社PR","健PR","藝PR","綜PR","總PR","二上國定","二上英定","二上數定","二上自定","二上社定","二上5定平","二下國定","二下英定","二下數定","二下自定","二下社定","二下5定平","三上國定","三上英定","三上數定","三上自定","三上社定","三上5定平","國定平","英定平","數定平","自定平","社定平","5定平","國定PR","英定PR","數定PR","自定PR","社定PR","5定PR","家長姓名","電話","郵遞區號","地址","報名學校代碼","科別代碼","報名身分","特種身分","二上語","二下語","三上語","語平均","語PR"));
				foreach($show_sn as $seme_class => $d) {
					foreach($d as $site => $sn) {
						$cno=substr($seme_class,-2,2);
						$row_arr=array($s[sch_id],$cno,$site,$stud_data[$sn][stud_id],$stud_data[$sn][stud_name],$stud_data[$sn][stud_person_id],$stud_data[$sn][stud_sex],$stud_data[$sn][stud_birthday]);
						for($i=0;$i<45;$i++) $row_arr[]="";
						foreach($semes as $i => $si) {
							foreach($s_arr as $j => $sl) {
								$row_arr[]=$rowdata[$sn][$si][$j][score];
							}
							$row_arr[]="";
						}
						foreach($s_arr as $j => $sl) {
							$row_arr[]=$rowdata[$sn][9991][$j][score];
						}
						$row_arr[]=$rowdata[$sn][9991][6][score];
						foreach($s_arr as $j => $sl) {
							$row_arr[]=$rowdata[$sn][9991][$j][pr];
						}
						$row_arr[]=$rowdata[$sn][9991][6][pr];
						for($i=0;$i<8;$i++) $row_arr[]="";
						$data_arr[]=$row_arr;
					}
				}
				$x->items=$data_arr;
				$x->writeSheet();
				$x->process();
				exit;
			}
			$smarty->assign("student_sn",$show_sn);
			$smarty->assign("stud_data",$stud_data);
			$smarty->assign("rowdata",$rowdata);
			$smarty->assign("s_arr",$s_arr);
			$smarty->assign("col_arr",$temp_arr);
			$smarty->assign("semes",$semes);
			break;
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","免試入學報表-99薦送"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name]));
//$smarty->assign("seme_year_seme",$seme_year_seme);
$step_str=array(
	1=>"《步驟一》選擇所要處理成績的學年學期",
	2=>"《步驟二》設定各學年學期中所要處理的對應科目",
	3=>"《步驟三》選擇轉入生採計成績 <input type=\"submit\" name=\"save\" value=\"確定儲存\">",
	4=>"《步驟四》計算成績 <input type=\"button\" id=\"calBtn\" value=\"開始計算\" OnClick=\"cal();\">",
	5=>"《步驟五》顯示結果 <input type=\"submit\" name=\"XLS\" value=\"匯出XLS檔\"> <input type=\"submit\" name=\"CHK\" value=\"列出校對單\"> ");
$smarty->assign("step_str",$step_str[intval($_POST['step'])]);
$smarty->register_function('t2c', 't2c');
$smarty->display("stud_basic_test_distest4.tpl");

function t2c($params, &$smarty)
{
	$times=$params['times'];
	$seme=$params['semes'];
	$sn=$params['sn'];
	$kind=$params['kind'];
	$enable=$params['enable'];
	$temp_str="";
	for($i=1;$i<=$times;$i++) {
		$temp_str.="<input type=\"checkbox\" id=\"sel".$kind."_".$seme.$i."_".$sn."\" name=\"sel[$sn][$seme][$i]\" ".(($enable[$i] || $kind==3)?"checked":"").($kind==3?" disabled":"").">$i ";
	}
	return $temp_str;
}
?>
