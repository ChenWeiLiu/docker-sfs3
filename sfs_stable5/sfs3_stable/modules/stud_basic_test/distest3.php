<?php

// $Id: distest3.php 9240 2018-05-04 03:25:04Z igogo $

include "select_data_config.php";
include "../../include/sfs_case_score.php";

sfs_check();

if (intval($_POST['cy'])==0 || intval($_POST['cy'])>5) $_POST['cy']=1;
$ss_link=array(1=>"chinese",2=>"english",3=>"language",4=>"math",5=>"nature",6=>"social",7=>"health",8=>"art",9=>"complex");
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
$s_arr=array(1=>"國",2=>"英",3=>"語",4=>"數",5=>"自",6=>"社",7=>"健",8=>"藝",9=>"綜",10=>"總");

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
		$CONN->Execute("drop table temp_kh_score");
		$creat_table_sql="CREATE TABLE `temp_kh_score` (
			`student_sn` int(10) unsigned NOT NULL default '0',
			`seme` varchar(4) NOT NULL default '',
			`ss_no` int(6) unsigned NOT NULL default '0',
			`score` float NOT NULL default '0.0',
			PRIMARY KEY (student_sn,seme,ss_no)
		)";
		$CONN->Execute($creat_table_sql);
	} elseif ($_POST['class_no'] && $_POST['act']=="cal") {
		$query="select a.* from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='".$_POST['class_no']."' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$seme_class=$res->fields[seme_class];
			$sn[]=$res->fields[student_sn];
			$res->MoveNext();
		}
		$query="select stud_study_year from stud_base where student_sn='".pos($sn)."'";
		$res=$CONN->Execute($query);
		$stud_study_year=$res->fields[0];
		for ($i=$starty;$i<=$f_year;$i++) {
			for ($j=1;$j<=2;$j++) {
				$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
			}
		}
		array_pop($semes);
		//計算之小數位數
		//中投區: 2位, 竹苗區: 1位
		if ($_POST['cy']==4)
			$nnum=1;
		else
			$nnum=2;
		$fin_score=cal_fin_score($sn,$semes);
		$s_num=count($semes);
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
					$CONN->Execute("insert into temp_kh_score (student_sn,seme,ss_no,score) values ('$i','$kk','$jj','$sc')");
				}
				$CONN->Execute("insert into temp_kh_score (student_sn,seme,ss_no,score) values ('$i','$s_num','$jj','".round($score/$s_num,$nnum)."')");
			}
		}
		//計算語文領域不加權平均
		$query="select student_sn,seme,sum(score)/2 as avg from temp_kh_score where ss_no in ('1','2') group by student_sn,seme order by student_sn,seme";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			//寫入分數
			$CONN->Execute("insert into temp_kh_score (student_sn,seme,ss_no,score) values ('".$res->fields['student_sn']."','".$res->fields['seme']."','3','".round($res->fields['avg'],$nnum)."')");
			$res->MoveNext();
		}
		//計算各科不加權平均
		$ss_num=count($ss_link);
		$query="select student_sn,ss_no,sum(score)/$s_num as avg from temp_kh_score group by student_sn,ss_no order by student_sn,ss_no";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			//寫入分數
			$CONN->Execute("insert into temp_kh_score (student_sn,seme,ss_no,score) values ('".$res->fields['student_sn']."','$s_num','".$res->fields['ss_no']."','".round($res->fields['avg'],$nnum)."')");
			$res->MoveNext();
		}
		//計算各學期不加權平均
		$query="select student_sn,seme,sum(score)/".($ss_num-2)." as avg from temp_kh_score where ss_no not in ('1','2') group by student_sn,seme order by student_sn,seme";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			//寫入分數
			$CONN->Execute("insert into temp_kh_score (student_sn,seme,ss_no,score) values ('".$res->fields['student_sn']."','".$res->fields['seme']."','".($ss_num+1)."','".round($res->fields['avg'],$nnum)."')");
			$res->MoveNext();
		}
		header("Content-type: text/html; charset=big5");
		echo $_POST['class_no']."...計算完成!";
		exit;
	}
}

if (($_POST['show'] || $_POST['htm'] || $_POST['out'] || $_POST['LOCK']) && $_POST['year_name']) {
	$seme_class=$_POST[year_name]."%";
	$query="select a.*,b.stud_name,b.stud_person_id,b.stud_sex,b.stud_birthday,b.addr_zip,b.stud_addr_1,b.stud_tel_1 from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$seme_class' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$seme_class=$res->fields[seme_class];
		$sn[]=$res->fields[student_sn];
		$show_sn[$seme_class][$res->fields[seme_num]]=$res->fields[student_sn];
		$stud_data[$res->fields[student_sn]][stud_name]=$res->fields[stud_name];
		$stud_data[$res->fields[student_sn]][stud_id]=$res->fields[stud_id];
		$stud_data[$res->fields[student_sn]][stud_person_id]=$res->fields[stud_person_id];
		$stud_data[$res->fields[student_sn]][stud_sex]=$res->fields[stud_sex];
		$stud_data[$res->fields[student_sn]][stud_addr_1]=$res->fields[stud_addr_1];
		$stud_data[$res->fields[student_sn]][stud_tel_1]=$res->fields[stud_tel_1];
		$stud_data[$res->fields[student_sn]][addr_zip]=$res->fields[addr_zip];
		$d_arr=explode("-",$res->fields[stud_birthday]);
		$dd=$d_arr[0]-1911;
		if ($_POST['out'] && $_POST['cy']==4) {
			$stud_data[$res->fields[student_sn]][stud_birthday1]=$dd;
			$stud_data[$res->fields[student_sn]][stud_birthday2]=$d_arr[1];
			$stud_data[$res->fields[student_sn]][stud_birthday3]=$d_arr[2];
		} else {
			$stud_data[$res->fields[student_sn]][stud_birthday]=$dd." 年 ".sprintf("%02d",$d_arr[1])." 月 ".sprintf("%02d",$d_arr[2])." 日";
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
	$stud_num=count($sn);
	$smarty->assign("sex0",$stud_num);
	$rowdata=array();
	for($i=1;$i<=count($s_arr);$i++) {
		$query="select * from temp_kh_score where ss_no='$i' and seme='$s_num' order by score desc";
		$res=$CONN->Execute($query);
		$j=1;
		$opr=0;
		$osc=0;
		while(!$res->EOF) {
			$score=$res->fields['score'];
			$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']][score]=$score;
			if ($_POST['cy']==2 || $_POST['cy']==4 || $_POST['cy']==5) {
				//計算百分比(彰化區、竹苗區、臺東區)
				if ($i==count($s_arr)) {
					if ($osc<>$score) {
						$osc=$score;
						$opr=$j;//在此用作記錄名次
					}
					//無條件進入
					$pr=ceil($opr/$stud_num*100);
				}
				$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']][pr]=$pr;
			} elseif ($_POST['cy']==3) {
				//計算排序(台南區)
				if ($i==count($s_arr)) {
					if ($osc<>$score) {
						$osc=$score;
						$opr=$j;//在此用作記錄名次
					}
					$pr=$opr;
				}
				$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']][pr]=$pr;
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
				$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']][pr]=$pr;
			}
			
			$j++;
			$res->MoveNext();
		}
	}
	$query="select * from temp_kh_score";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$rowdata[$res->fields['student_sn']][$res->fields['seme']][$res->fields['ss_no']][score]=$res->fields['score'];
		$res->MoveNext();
	}
	$semes[$s_num]="avg";
	$smarty->assign("pry",$s_num);
}

if ($_POST['LOCK']) {
	foreach($rowdata as $sn=>$d) {
		foreach ($d as $sm=>$dd) {
			foreach ($dd as $ss_no=>$ddd) {
				$query="insert into dis_score_fin (student_sn,year,seme,ss_no,score,pr) values ('$sn','$sel_year','$sm','$ss_no','".$ddd['score']."','".$ddd['pr']."')";
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
		$x->setRowText(array("學校代碼","班級","座號","學號","姓名","身分證號","性別","生日","二上國","二上英","二上數","二上自","二上社","二上健","二上藝","二上綜","二上總","二下國","二下英","二下數","二下自","二下社","二下健","二下藝","二下綜","二下總","三上國","三上英","三上數","三上自","三上社","三上健","三上藝","三上綜","三上總","國平","英平","數平","自平","社平","健平","藝平","綜平","總平","國PR","英PR","數PR","自PR","社PR","健PR","藝PR","綜PR","總PR","二上國定","二上英定","二上數定","二上自定","二上社定","二上5定平","二下國定","二下英定","二下數定","二下自定","二下社定","二下5定平","三上國定","三上英定","三上數定","三上自定","三上社定","三上5定平","國定平","英定平","數定平","自定平","社定平","5定平","國定PR","英定PR","數定PR","自定PR","社定PR","5定PR","家長姓名","電話","郵遞區號","地址","報名學校代碼","科別代碼","報名身分","特種身分","二上語","二下語","三上語","語平均","語PR"));
	foreach($show_sn as $seme_class => $d) {
		foreach($d as $site => $sn) {
			$cno=substr($seme_class,-2,2);
			if ($_POST['cy']==4)
				$row_arr=array($stud_data[$sn][stud_name],$stud_data[$sn][stud_sex],$stud_data[$sn][stud_person_id],$stud_data[$sn][stud_birthday1],$stud_data[$sn][stud_birthday2],$stud_data[$sn][stud_birthday3],"","","",3,$cno,$site,$s[sch_id],$s[sch_cname],($stud_study_year+3),"","",$stud_data[$sn][stud_tel_1],$stud_data[$sn][stud_tel_1],$stud_data[$sn][addr_zip],$stud_data[$sn][stud_addr_1]);
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
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","免試入學報表-99中區高中職"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name]));
$smarty->assign("seme_year_seme",$seme_year_seme);
$smarty->assign("semes",$semes);
$smarty->assign("student_sn",$show_sn);
$smarty->assign("stud_data",$stud_data);
$smarty->assign("rowdata",$rowdata);
$smarty->assign("col_arr",$temp_arr);
$smarty->assign("s_arr",$s_arr);
$smarty->assign("sch_arr",get_school_base());
$smarty->assign("ss_link",$ss_link);
if ($_POST['htm']) {
	if ($_POST['cy']==2) {
		$smarty->assign("s_arr",array(1=>"本國語文",2=>"英語",3=>"語文平均",4=>"數學領域",5=>"自然與生活科技領域",6=>"社會領域",7=>"健康與體育領域",8=>"藝術與人文領域",9=>"綜合活動領域",10=>"學期總成績"));
		$smarty->display("stud_basic_test_distest3_print_chc.tpl");
	} elseif ($_POST['cy']==5) {
		$smarty->assign("s_arr",array(1=>"本國語文",2=>"英語",3=>"語文平均",4=>"數學領域",5=>"自然與生活科技領域",6=>"社會領域",7=>"健康與體育領域",8=>"藝術與人文領域",9=>"綜合活動領域",10=>"學期總成績"));
		$smarty->display("stud_basic_test_distest3_print_ttct.tpl");
	} else {
		$smarty->assign("s_arr",array(1=>"本國語文",2=>"英語",3=>"語文平均",4=>"數學領域",5=>"自然與生活科技領域",6=>"社會領域",7=>"健康與體育領域",8=>"藝術與人文領域",9=>"綜合活動領域",10=>"學期總成績"));
		$smarty->display("stud_basic_test_distest3_print.tpl");
	}
} else
	$smarty->display("stud_basic_test_distest3.tpl");
?>
