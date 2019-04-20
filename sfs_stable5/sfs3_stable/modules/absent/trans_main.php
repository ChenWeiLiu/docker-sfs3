<?php

// $Id: trans_main.php 8914 2016-06-22 03:35:53Z qfon $
	//新增一個 EasyZip 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile("settings.xml");
	$ttt->addfile("meta.xml");
	$data=$ttt->read_file(dirname(__FILE__)."/$oo_path/styles.xml");
	$sql="select * from school_base";
	$rs=$CONN->Execute($sql);
	$school_name=$rs->fields['sch_cname'];
	$sch_addr=$rs->fields['sch_addr'];
	$sch_post_num=$rs->fields['sch_post_num'];
	$school_tel=$rs->fields['sch_phone'];
	$temp_arr["school_name"]=$school_name;
	$temp_arr["sel_year"]=$sel_year;
	$temp_arr["sel_seme"]=$sel_seme;
	$temp_arr["sel_week"]=$week_num;
	$today=getdate(mktime(0,0,0,date("m"),date("d"),date("Y")));
	$temp_arr["t_year"]=$today[year]-1911;
	$temp_arr["t_month"]=$today[mon];
	$temp_arr["t_day"]=$today[mday];
	$temp_arr["pm"]=$pm;
	$replace_data = $ttt->change_temp($temp_arr,$data,0);
	$ttt->add_file($replace_data,"styles.xml");
	if ($act!="列印通知書(固定1~7節)") {
		//讀出 content.xml
		$data=$ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");
	}

	//將 content.xml 的 tag 取代
	//取得該班有幾節課
	$sel_year=intval($sel_year);
	$sel_seme=intval($sel_seme);
	$sql = "select sections,class_year from score_setup where year = '$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
	while (!$rs->EOF) {
		$i=$rs->fields['class_year'];
		$all_sections[$i] = $rs->fields['sections'];
		$rs->MoveNext();
	}
	$sql="select c_name,c_sort from school_class where year='$sel_year' and semester='$sel_seme' and enable=1 order by c_sort";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$class_cname[$rs->fields['c_sort']]=$rs->fields['c_name'];
		$rs->MoveNext();
	}
	$d=explode("-",$weeks_array[$week_num]);
	$wmt=mktime(0,0,0,$d[1],$d[2],$d[0]);
	if ($dd[mon]>7)
		$ky=1911;
	else
		$ky=1912;
	for ($i=1;$i<=6;$i++) {
		$dd=getdate($wmt+86400*$i);
		$wd[$i]=sprintf("%04d-%02d-%02d",$dd[year],$dd[mon],$dd[mday]);
		$dw[$wd[$i]]=$i;
		$temp_arr["m".$i]=$dd[mon];
		$temp_arr["d".$i]=$dd[mday];
	}
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$start_day=curr_year_seme_day($sel_year,$sel_seme);
	$sql="select a.*,b.seme_num,c.stud_name from stud_absent a, stud_seme b, stud_base c where a.date >= '$start_day[st_start]' and a.date <= '$wd[6]' and a.stud_id=b.stud_id and b.student_sn=c.student_sn and b.seme_year_seme='$seme_year_seme' $notsection order by a.class_id,b.seme_num,a.date,a.section";
	$rs=$CONN->Execute($sql);
	if ($rs->recordcount() > 0) {
		$m=0;
		while (!$rs->EOF) {
			$ad=$rs->fields['date'];
			$id=$rs->fields['stud_id'];
			if ($stud_id[$m]!=$id) {
				$m++;
				$stud_id[$m]=$id;
				$stud_name[$m]=addslashes($rs->fields['stud_name']);
			}
			$class_id=explode("_",$rs->fields['class_id']);
			$class[$m][year]=intval($class_id[2]);
			$class[$m][name]=intval($class_id[3]);
			$class[$m][num]=intval($rs->fields['seme_num']);
			switch ($rs->fields['absent_kind']) {
				case '事假':
					$abskind=1;
					break;
				case '病假':
					$abskind=2;
					break;
				case '曠課':
					$abskind=3;
					break;
				default:
					$abskind=5;
					break;
			}
			$section=$rs->fields['section'];
			if ($section=='uf' || $section=='df') {
				if ($abskind==3) {
					if ($wd[1] <= $ad) {
						$enable[$m]=3;
						$absent[$m][$dw[$ad]][4]++;
						$absent[$m][7][4]++;
					}
					$absent_total[$id][4]++;
				}
			} elseif ($section=="allday") {
				if ($wd[1] <= $ad) {
					if (empty($enable[$m])) $enable[$m]=1;
					if ($abskind==3) {
						$enable[$m]=3;
						$absent[$m][$dw[$ad]][4]+=2;
						$absent[$m][7][4]+=2;
					}
					$absent[$m][$dw[$ad]][$abskind]+=$all_sections[$class[$m][year]];
					$absent[$m][7][$abskind]+=$all_sections[$class[$m][year]];
				}
				if ($abskind==3) $absent_total[$id][4]+=2;
				$absent_total[$id][$abskind]+=$all_sections[$class[$m][year]];
			} else {
				if ($wd[1] <= $ad) {
					if ($abskind==3)
						$enable[$m]=3;
					elseif (empty($enable[$m]))
						$enable[$m]=1;
					$absent[$m][$dw[$ad]][$abskind]++;
					$absent[$m][7][$abskind]++;
				}
				$absent_total[$id][$abskind]++;
			}
			$rs->MoveNext();
		}
	}
	if ($act=="列印通知書(固定1~7節)") {
		$sql="select * from school_room where room_id='3'";
		$rs=$CONN->Execute($sql);
		$room_name=$rs->fields['room_name'];
		$replace_data=$ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");
		//reset($enable);
		while(list($i,$v)=each($enable)) {
			if ($v!=3) continue;
			$content_body=$ttt->read_file(dirname(__FILE__)."/$oo_path/content_body.xml");
			$temp_arr="";
			$temp_arr["school_name"]=$school_name;
			$temp_arr["sch_addr"]=$sch_addr;
			$temp_arr["sch_post_num"]=$sch_post_num;
			$temp_arr["school_tel"]=$school_tel;
			$temp_arr["room_name"]=$room_name;
			$One=$stud_id[$i];
			$sql="select * from stud_base where stud_id='$One' and ($sel_year - stud_study_year between 0 and 9)";
			$rs=$CONN->Execute($sql);
			if (strlen($stud)<6) $s=" ";
			$temp_arr["stud_id"] = $One.$s;
			$temp_arr["stud_name"] = $rs->fields['stud_name'];
			$curr_class_num = $rs->fields['curr_class_num'];
			$year_name=substr($curr_class_num,0,-4);
			$temp_arr["year_name"]=$class_name_kind_1[$year_name-$IS_JHORES];
			$temp_arr["class_num"]=substr($curr_class_num,-4,-2);
			$temp_arr["site_num"]=substr($curr_class_num,-2);
			$temp_arr["stud_addr"] = $rs->fields['stud_addr_2'];
			$temp_arr["addr_zip"] = ($rs->fields['addr_zip']=='')?"□□□":$rs->fields['addr_zip'];
			$sql="select guardian_name from stud_domicile where student_sn='".$rs->fields['student_sn']."'";
			$rs=$CONN->Execute($sql);
			$temp_arr["guardian_name"] = $rs->fields['guardian_name'];
			$fday=mktime(0,0,0,$month,$day,$year);
			$dd=getdate($fday);
			$fday-=($dd[wday]-1)*86400;
			for ($j=1;$j<=7;$j++) {
				//取得該學生資料
				$smkt=$fday+($j-1)*86400;
				$syear=date("Y",$smkt);
				$smonth=date("m",$smkt);
				$sday=date("d",$smkt);
				$did=date("Y-m-d",$smkt);
				$aaa=getOneDaydata($One,$syear,$smonth,$sday);
				$temp_arr["d".$j]=($syear-1911).".".$smonth.".".$sday;
				if ($aaa[allday]) {
					$aaa[uf]=$aaa[allday];
					$aaa[df]=$aaa[allday];
					for ($k=1;$k<=$all_sections[$year_name];$k++) $aaa[$k]=$aaa[allday];
				}
				$temp_arr["a"."_".$j."1"]=empty($aaa[uf])?"---":$aaa[uf];
				$temp_arr["a"."_".$j."9"]=empty($aaa[df])?"---":$aaa[df];
				for ($k=1;$k<=$all_sections[$year_name];$k++) $temp_arr["a"."_".$j.($k+1)]=empty($aaa[$k])?"---":$aaa[$k];
			}
			$today=date("Y-m-d",mktime (0,0,0,date("m"),date("d"),date("Y")));
			$ldd=explode("-",$today);
			$temp_arr["date"] = ($ldd[0]-1911).".".$ldd[1].".".$ldd[2];
			$replace_data.=$ttt->change_temp($temp_arr,$content_body,0)."<text:p text:style-name=\"break_page\"/>";
		}
		$replace_data.=$ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot.xml");
	} else {
	$pp=1;
	$temp="";
	//reset($enable);
	while(list($i,$v)=each($enable)) {
		if ($act=="分班列印" && $temp!="" && ($class[$i][year]!=$ocy || $class[$i][name]!=$ocn) && ($pp % $report_line)!=1) {
			for ($j=($pp % $report_line);$j<$report_line;$j++) {
				$temp.="<table:table-row><table:table-cell table:style-name=\"表格1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P6\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P6\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P6\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.d2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.e2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.e2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.e2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.e2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.m2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell></table:table-row>";
			}
		     $temp.="<table:table-row><table:table-cell table:style-name=\"表格1.A3\" table:value-type=\"string\"><text:p text:style-name=\"P7\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P7\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P7\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.d3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.e3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.e3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.e3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.e3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.m3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell></table:table-row>";
			$pp=1;
		
		}
		if (($pp % $report_line)!=0)
			$temp.="<table:table-row><table:table-cell table:style-name=\"表格1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P6\">".substr($class_year[$class[$i][year]],0,2)."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P6\">".$class[$i][name]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P6\">".$class[$i][num]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".stripslashes($stud_name[$i])."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][1][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][1][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][1][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][1][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][1][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.m2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][5]."</text:p></table:table-cell></table:table-row>";
		else
			$temp.="<table:table-row><table:table-cell table:style-name=\"表格1.A3\" table:value-type=\"string\"><text:p text:style-name=\"P7\">".substr($class_year[$class[$i][year]],0,2)."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P7\">".$class[$i][name]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P7\">".$class[$i][num]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\">".stripslashes($stud_name[$i])."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P8\">".$absent[$i][1][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\">".$absent[$i][1][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\">".$absent[$i][1][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\">".$absent[$i][1][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\">".$absent[$i][1][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][2][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][3][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][4][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][5][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent[$i][6][5]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][1]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][2]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][3]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][4]."</text:p></table:table-cell><table:table-cell table:style-name=\"表格1.m3\" table:value-type=\"string\"><text:p text:style-name=\"P4\">".$absent_total[$stud_id[$i]][5]."</text:p></table:table-cell></table:table-row>";
		$pp++;
		$ocy=$class[$i][year];
		$ocn=$class[$i][name];
	}
	for ($j=($pp % $report_line);$j<$report_line;$j++) {
		$temp.="<table:table-row><table:table-cell table:style-name=\"表格1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P6\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P6\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P6\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.m2\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell></table:table-row>";
	}
	$temp.="<table:table-row><table:table-cell table:style-name=\"表格1.A3\" table:value-type=\"string\"><text:p text:style-name=\"P7\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P7\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P7\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P8\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.B3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"表格1.m3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell></table:table-row>";

	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data,0);
	$replace_data.=iconv("Big5","UTF-8//IGNORE",$temp)."<text:p text:style-name=\"P2\"/></table:table></office:body></office:document-content>";
	}
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	$fl="chart_".$sel_year."_".$sel_seme."_".$week_num;
	header("Content-disposition: attachment; filename=$fl.sxw");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
	header("Cache-Control: max-age=0");
	header("Pragma: public");

	header("Expires: 0");

	echo $sss;

	exit;
?>
