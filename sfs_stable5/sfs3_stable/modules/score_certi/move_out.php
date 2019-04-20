<?php

// $Id: move_out.php 8693 2015-12-25 03:53:51Z qfon $

        //新增一個 zipfile 實例
        $ttt = new EasyZIP;

        // 設定 檔案目錄
        $ttt->setPath($oo_path);

        // 加入整個目錄
        $ttt->addDir("META-INF");

        // 加入檔案
        $ttt -> addFile("styles.xml");
        $ttt -> addFile("meta.xml");
        $ttt -> addFile("settings.xml");

	//讀出 content.xml 
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代
	$sql="select * from school_base";
	$rs=$CONN->Execute($sql);
	$temp_arr["school_name"] = $rs->fields['sch_cname'];
	$student_sn=intval($student_sn);
	$sql="select student_sn,stud_name,stud_birthday,stud_study_year from stud_base where student_sn='$student_sn'";
	$rs=$CONN->Execute($sql);
	$stud_id=$rs->fields['stud_id'];
	$s1=$rs->fields['stud_study_year'];
	$temp_arr["student_name"] = $rs->fields['stud_name'];
	$temp_arr["stud_id"] = $stud_id;
	$d=explode("-",$rs->fields['stud_birthday']);
	$temp_arr["by"]=num2str(intval($d[0])-1911);
	$temp_arr["bm"]=num2str($d[1]);
	$temp_arr["bd"]=num2str($d[2]);
	$temp_arr["iy"]="　";
	$temp_arr["im"]="　";
	$temp_arr["id"]="　";
	$temp_arr["gy"]="　";
	$temp_arr["gm"]="　";
	$temp_arr["gd"]="　";
	$temp_arr["ty"]=num2str(intval($today[0])-1911);
	$temp_arr["tm"]=num2str(intval($today[1]));
	$temp_arr["td"]=num2str(intval($today[2]));
	$temp_arr["s2"]=$s1+1;
	$temp_arr["s3"]=$s1+2;
	$temp_arr["c_word"]=$c_word;
	$temp_arr["c_num"]=$c_num;
	$temp_arr["m_year"]=num2str(curr_year()-$s1+1);
	$temp_arr["m_seme"]=num2str(curr_seme());
	$temp_arr["m_content"]=$m_content;
	$temp_arr["m_reason"]=$m_reason;
	$sql="select subject_id,subject_name from score_subject where enable='1'";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$subject_id=addslashes($rs->fields['subject_id']);
		$subject_arr[$subject_id]=($subject_arr[$subject_id]=="")?$rs->fields['subject_name']:$subject_arr[$subject_id];
		$rs->MoveNext();
	}
	$std_ss=array("1"=>"國文","2"=>"英語","3"=>"數學","4"=>"自然與生活科技","5"=>"社會","6"=>"藝術與人文","7"=>"健康與體育","8"=>"綜合活動");
	$nor_arr=array("有待改進"=>'1',"需再加油"=>'2',"表現尚可"=>'3',"表現良好"=>'4',"表現優異"=>'5');
	$arr_nor=array('1'=>"有待改進",'2'=>"需再加油",'3'=>"表現尚可",'4'=>"表現良好",'5'=>"表現優異");
	for ($i=1;$i<=3;$i++) {
		$year=$s1+$i-1;
		$year_name=$i+6;
		$temp_arr["s".$i]=$year;
		for ($j=1;$j<=2;$j++) {
			$year_seme=sprintf("%03d%1d",$year,$j);
			$sql="select seme_class from stud_seme where seme_year_seme='$year_seme' and stud_id='$stud_id'";
			$rs=$CONN->Execute($sql);
			$seme_class=$rs->fields['seme_class'];
			$class=($seme_class=="")?"":substr($seme_class,1,2);
			$class_id=sprintf("%03d_%1d_%02d_%02d",$year,$j,$year_name,$class);
			$ss_num_arr = get_ss_num_arr($class_id);
			$sql="select * from stud_seme_score where seme_year_seme='$year_seme' and student_sn='$student_sn'";
			$rs=$CONN->Execute($sql);
			$max_ss=8;
			$score="";
			$memo="";
			$ssval="";
			$std_ss[9]="";
			$std_ss[10]="";
			$std_ss[11]="";
			while (!$rs->EOF) {
				$ss_id=$rs->fields['ss_id'];
				$ssscore=$rs->fields['ss_score'];
				if ($ssscore) $score[$ss_id]=$ssscore;
				$ssmemo=addslashes($rs->fields['ss_score_memo']);
				if ($ssmemo!="") $memo[$ss_id]=$ssmemo;
				$rs->MoveNext();
			}
			$sql="select ss_id,ss_val from stud_seme_score_oth where seme_year_seme='$year_seme' and stud_id='$stud_id' and ss_kind='努力程度'";
			$rs=$CONN->Execute($sql);
			$ss_id=$rs->fields['ss_id'];
			if ($ss_id!="") {
				while (!$rs->EOF) {
					$ss_id=$rs->fields['ss_id'];
					$ss_val=addslashes($rs->fields['ss_val']);
					if ($ss_val != "") $ssval[$ss_id]=$ss_val;
					$rs->MoveNext();
				}
			}
			$sql="select ss_id,scope_id,subject_id,rate from score_ss where year='$year' and semester='$j' and class_year='$year_name' and enable='1' and need_exam='1' order by sort";
			$rs=$CONN->Execute($sql);
			$wtime="";
			$ss_arr="";
			$rate="";
			$ss_score="";
			$mmemo="";
			$sval="";
			$v="";
			while (!$rs->EOF) {
				$scope_id=$rs->fields['scope_id'];
				$subject_id=$rs->fields['subject_id'];
				$ss_id=$rs->fields['ss_id'];
				$rrate=intval($rs->fields['rate']);
				//特別處理彈性課程
				if ($scope_id=='8') {
					$max_ss++;
					$std_ss[$max_ss]=$subject_arr[$subject_id];
					$scope_id=$subject_id;
					$subject_id='0';
				}
				if ($scope_id=='1') {
					//語文領域
					$wtime[$subject_arr[$subject_id]]=intval($ss_num_arr[$ss_id]);
					if ($score[$ss_id]!="") {
						$ss_score[$subject_arr[$subject_id]]=$score[$ss_id];
						$rate[$subject_arr[$subject_id]]=$rrate;
					}
					$mmemo[$subject_arr[$subject_id]]=$memo[$ss_id];
					$sval[$subject_arr[$subject_id]]=$ssval[$ss_id];
				} elseif ($subject_id=='0') {
					//無分科領域
					$wtime[$subject_arr[$scope_id]]=intval($ss_num_arr[$ss_id]);
					if ($score[$ss_id]!="") {
						$ss_score[$subject_arr[$scope_id]]=$score[$ss_id]*$rrate;
						$rate[$subject_arr[$scope_id]]=$rrate;
					}
					$mmemo[$subject_arr[$scope_id]]=$memo[$ss_id];
					$sval[$subject_arr[$scope_id]]=$ssval[$ss_id];
				} else {
					//有分科領域
					$wtime[$subject_arr[$scope_id]]+=intval($ss_num_arr[$ss_id]);
					if ($score[$ss_id]!="") {
						$ss_score[$subject_arr[$scope_id]]+=$score[$ss_id]*$rrate;
						$rate[$subject_arr[$scope_id]]=$rate[$subject_arr[$scope_id]]+$rrate;
					}
					$dd=$mmemo[$subject_arr[$scope_id]];
					$d=(($dd != "") && (substr($dd,-2,2) != "。"))?"。":"";
					$mmemo[$subject_arr[$scope_id]].=$d.$memo[$ss_id];
					if ($ssval[$ss_id]) {
						$vv=$v[$scope_id];
						$sval[$subject_arr[$scope_id]]=$arr_nor[round(($nor_arr[$sval[$subject_arr[$scope_id]]]*$vv+$nor_arr[$ssval[$ss_id]])/($vv+1))];
						$v[$scope_id]+=1;
					}
				}
				$rs->MoveNext();
			}
			$sum_c=0;
			$sum_s=0;
			$sql="select rule from score_setup where year='$year' and semester='$j' and class_year='$year_name' and enable='1'";
			$rs=$CONN->Execute($sql);
			$rule=$rs->fields['rule'];
			for ($k=1;$k<=11;$k++) {
				$ss=$std_ss[$k];
				if ($wtime[$ss]) {
					$temp_arr["c".$k."_".$i.$j]=$wtime[$ss];
					$sum_c+=$wtime[$ss];
				} else
					$temp_arr["c".$k."_".$i.$j]="---";
				if ($ss_score[$ss])	{
					$ssscore=number_format($ss_score[$ss]/$rate[$ss],2);
					$temp_arr["s".$k."_".$i.$j]=$ssscore;
					$temp_arr["f".$k."_".$i.$j]=sc2str($ssscore,$rule);
					$sum_s+=$ssscore*$wtime[$ss];
				} else {
					$temp_arr["s".$k."_".$i.$j]="---";
					$temp_arr["f".$k."_".$i.$j]="---";
				}
				if ($mmemo[$ss])	
					$temp_arr["m".$k."_".$i.$j]=$mmemo[$ss];
				else
					$temp_arr["m".$k."_".$i.$j]="---";
				if ($sval[$ss])
					$temp_arr["v".$k."_".$i.$j]=$sval[$ss];
				else
					$temp_arr["v".$k."_".$i.$j]="---";
			}
			if ($sum_c)
				$temp_arr["cavg_".$i.$j]=$sum_c;
			else
				$temp_arr["cavg_".$i.$j]="---";
			if ($sum_s) {
				$ssscore=number_format($sum_s/$sum_c,2);
				$temp_arr["savg_".$i.$j]=$ssscore;
				$temp_arr["favg_".$i.$j]=sc2str($ssscore,$rule);
			} else {
				$temp_arr["savg_".$i.$j]="---";
				$temp_arr["favg_".$i.$j]="---";
			}
			for ($k=9;$k<=11;$k++) {
				//顯示彈性課程科目
				if ($std_ss[$k])
					$temp_arr["b".$k."_".$i.$j]=$std_ss[$k];
				else
					$temp_arr["b".$k."_".$i.$j]="---";
			}
			//處理日常表現成績及文字描述
			$student_sn=intval($student_sn);
			$sql="select ss_score,ss_score_memo from stud_seme_score_nor where seme_year_seme='$year_seme' and student_sn='$student_sn'";
			$rs=$CONN->Execute($sql);
			$ss_score=$rs->fields['ss_score'];
			if ($ss_score) {
				$temp_arr["s12_".$i.$j]=$ss_score;
				$temp_arr["f12_".$i.$j]=sc2str($ss_score,$rule);
			} else {
				$temp_arr["s12_".$i.$j]="---";
				$temp_arr["f12_".$i.$j]="---";
			}
			$memo=addslashes($rs->fields['ss_score_memo']);
			if ($memo=="")
				$temp_arr["m12_".$i.$j]="---";
			else
				$temp_arr["m12_".$i.$j]=$memo;
			$sql="select ss_id,ss_val from stud_seme_score_oth where seme_year_seme='$year_seme' and stud_id='$stud_id' and ss_kind='生活表現評量' order by ss_id";
			$rs=$CONN->Execute($sql);
			$k=1;
			$norval="";
			while (!$rs->EOF) {
				$ss_id=$rs->fields['ss_id'];
				$ss_val=$rs->fields['ss_val'];
				if (($ss_id==$k) && ($ss_val)){
					$norval.=$nor_arr[$ss_val].",";
					$k++;
					$rs->MoveNext();
				} elseif ($ss_id<$k) {
					$rs->MoveNext();
				} else {
					$norval.="--,";
					$k++;
				}
			}
			if ($norval)
				$norval=substr($norval,0,strlen($norval)-1);
			else
				$norval="---";
			$temp_arr["v12_".$i.$j]=$norval;
		}
	}

	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data);
	
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = $ttt->file();

	//以串流方式送出 ooo.sxw
	$fl="chart_".$stud_id."_".$oo_path;
	header("Content-disposition: attachment; filename=$fl.sxw");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;
	
	exit;	

?>
