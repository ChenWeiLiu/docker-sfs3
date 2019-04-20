<?php
	// $Id: stud_move_list.php 8759 2016-01-13 12:50:36Z qfon $
	//新增一個 zipfile
	$ttt= new easyZIp;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
        $ttt->addFile("meta.xml");
	$ttt->addFile("settings.xml");
        $data=$ttt->read_file(dirname(__FILE__)."/$oo_path/styles.xml");
	$sql="select * from school_base";
	$rs=$CONN->Execute($sql);
	$temp_sty["school_name"]=$rs->fields['sch_cname'];
	$temp_sty["sel_year"]=$sel_year;
	$temp_sty["sel_seme"]=$sel_seme;
	$temp_sty["move_kind"]=$move_kind_arr[$move_kind];
	$today=explode("-",date("Y-m-d",mktime(date("m"),date("d"),date("Y"))));
	$temp_sty["year"]=strval($today[0]-1911);
	$temp_sty["month"]=strval($today[1]);
	$sex_arr=array("1"=>"男","2"=>"女");
	$move_id=intval($move_id);
	$query="select * from stud_move where move_id='$move_id'";
	$res=$CONN->Execute($query);
	$move_year_seme=$res->fields[move_year_seme];
	$seme_year_seme=sprintf("%04d",$move_year_seme);
	$query="select a.*,b.student_sn,b.seme_class from stud_move a left join stud_seme b on b.seme_year_seme='$seme_year_seme' and b.student_sn=a.student_sn where a.move_id in ($all_move_id) order by a.stud_id";
	$res=$CONN->Execute($query);
	$class_year=0;
	while (!$res->EOF) {
                $id=$res->fields[stud_id];
                $move_date[$id]=DtoCh($res->fields[move_date]);
                $dd=explode("-",$move_date[$id]);
                $move_date[$id]=$dd[0].".".$dd[1];
                $move_reason[$id]=iconv("Big5","UTF-8//IGNORE",$res->fields[reason]);
                $student_sn=$res->fields[student_sn];
                $seme_class[$id]=substr($res->fields[seme_class],-3,1);
                if ($student_sn=="") {
                        $query="select * from stud_base where stud_id='$id' order by stud_study_year desc";
                        $res_stud=$CONN->Execute($query);
                        $student_sn=$res_stud->fields['student_sn'];
                        $seme_class[$id]=substr($res_stud->fields['curr_class_num'],0,1);
                }
                if ($seme_class[$id]=="0") {
                        $query="select * from stud_seme where student_sn='$student_sn' order by seme_year_seme desc";
                        $res_stud=$CONN->Execute($query);
                        $seme_class[$id]=substr($res_stud->fields[seme_class],-3,1);
                }
                if ($class_year==0) {
                        $class_year=$seme_class[$id];
                        if ($class_year>$IS_JHORES) $class_year-=$IS_JHORES;
                        $temp_sty["class_year"]=strval($class_year);
                        $dd=explode("-",DtoCh($res->fields[move_c_date]));
                        $move_c_num=$res->fields[move_c_num];
                        if ($move_c_num>0) {
                                $temp_sty["move_c_date"]=$dd[0].".".$dd[1].".".$dd[2];
                                $temp_sty["move_c_unit"]=$res->fields[move_c_unit];
                                $temp_sty["move_c_word"]=$res->fields[move_c_word]."字第";
                                $temp_sty["move_c_num"]=$move_c_num."號";
                        } else {
                                $temp_sty["move_c_date"]="";
                                $temp_sty["move_c_unit"]="";
                                $temp_sty["move_c_word"]="";
                                $temp_sty["move_c_num"]="";
                        }
                }
		$query_stud="select * from stud_base where student_sn='$student_sn'";
		$res_stud=$CONN->Execute($query_stud);
		$stud_name[$id]= $ttt->change_str($res_stud->fields[stud_name]);
		$stud_sex[$id]= $ttt->change_str($sex_arr[$res_stud->fields[stud_sex]]);
		$stud_person_id[$id]=$res_stud->fields[stud_person_id];
		$stud_birthday[$id]=DtoCh($res_stud->fields[stud_birthday]);
		$dd=explode("-",$stud_birthday[$id]);
		$stud_birthday[$id]=$dd[0].".".$dd[1].".".$dd[2];
                //20131008->修正異動情形取錯欄位情形
		$stud_mschool_name[$id]=($newin=="1")?$ttt->change_str($res_stud->fields[stud_mschool_name]):$ttt->change_str($res->fields[school]);
		
		$stud_addr_1[$id]= $ttt->change_str($res_stud->fields[stud_addr_1]);
		$res->MoveNext();
	}

	$replace_data=$ttt->change_temp($temp_sty,$data);
	$ttt->add_file($replace_data,"styles.xml");
	$data=$ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	$temp="";
	$nums=1;
	$ov="";
	foreach($seme_class as $k=>$v) {
		if ($ov!="" && $ov!=$v && $oo_path=="move_in") break;
		if ($nums<20) {
			if ($oo_path=="move_in")
				$temp.="<table:table-row table:style-name=\"chart1.2\"><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$k."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_name[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$stud_sex[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_person_id[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$stud_birthday[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$move_date[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_mschool_name[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.H2\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_addr_1[$k]."</text:p></table:table-cell></table:table-row>";
			else
				$temp.="<table:table-row table:style-name=\"chart1.2\"><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$k."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_name[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".($seme_class[$k]-$IS_JHORES)."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.D2\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$sel_seme."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$move_date[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_mschool_name[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.G2\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$move_reason[$k]."</text:p></table:table-cell></table:table-row>";
		} else {
			if ($oo_path=="move_in")
				$temp.="<table:table-row table:style-name=\"chart1.2\"><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$k."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_name[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$stud_sex[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_person_id[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$stud_birthday[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.F3\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$move_date[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_mschool_name[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.H1\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_addr_1[$k]."</text:p></table:table-cell></table:table-row>";
			else
				$temp.="<table:table-row table:style-name=\"chart1.2\"><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$k."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_name[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".($seme_class[$k]-$IS_JHORES)."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.D3\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$sel_seme."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P2\">".$move_date[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.F3\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$stud_mschool_name[$k]."</text:p></table:table-cell><table:table-cell table:style-name=\"chart1.G1\" table:value-type=\"string\"><text:p text:style-name=\"P3\">".$move_reason[$k]."</text:p></table:table-cell></table:table-row>";
		}

		$nums=$nums % 20 + 1;
		$ov=$v;
	}
	if ($nums>1) {
		for ($i=$nums;$i<=20;$i++) {
			if ($i<20) {
				if ($oo_path=="move_in")
					$temp.="<table:table-row table:style-name=\"chart1.2\"><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.H2\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell></table:table-row>";
				else
					$temp.="<table:table-row table:style-name=\"chart1.2\"><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.D2\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.E2\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.G2\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell></table:table-row>";
			} else {
				if ($oo_path=="move_in")
					$temp.="<table:table-row table:style-name=\"chart1.2\"><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P2\"/></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P3\"/></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P2\"/></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.F3\" table:value-type=\"string\"><text:p text:style-name=\"P2\"/></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P3\"/></table:table-cell><table:table-cell table:style-name=\"chart1.H1\" table:value-type=\"string\"><text:p text:style-name=\"P3\"></text:p></table:table-cell></table:table-row>";
				else
					$temp.="<table:table-row table:style-name=\"chart1.2\"><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P2\"/></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P3\"/></table:table-cell><table:table-cell table:style-name=\"chart1.A1\" table:value-type=\"string\"><text:p text:style-name=\"P2\"/></table:table-cell><table:table-cell table:style-name=\"chart1.D3\" table:value-type=\"string\"><text:p text:style-name=\"P2\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.E3\" table:value-type=\"string\"><text:p text:style-name=\"P4\"></text:p></table:table-cell><table:table-cell table:style-name=\"chart1.F3\" table:value-type=\"string\"><text:p text:style-name=\"P2\"/></table:table-cell><table:table-cell table:style-name=\"chart1.G1\" table:value-type=\"string\"><text:p text:style-name=\"P3\"/></table:table-cell></table:table-row>";
			}
		}
	}
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data2 = $ttt->change_temp($temp_arr,$data);
	if ($oo_path=="move_in")
		$replace_data2.=$temp."</table:table><text:p text:style-name=\"P4\"/></office:body></office:document-content>";
	else
		$replace_data2.=$temp."</table:table><text:p text:style-name=\"P5\"/></office:body></office:document-content>";

	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data2,"content.xml");

	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$oo_path.sxw");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;
	exit;
?>
