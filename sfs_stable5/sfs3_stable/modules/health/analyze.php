<?php

// $Id: analyze.php 6808 2012-06-22 08:14:46Z smallduh $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","身高體重分析","視力統計分析","牙齒統計分析","預防接種統計","傷病日誌統計","護理工作期報表","健康檢查結果統計","個人疾病史統計");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

if ($_POST['sub_menu_id']!=$_POST['pre_sub_id']) $_POST['input_item']="";
switch ($_POST['sub_menu_id']) {
	case "1":
		$work_menu_arr=array("請選擇作業項目","班級明細表","身高分析圖","體重分析圖","生長指數分析圖","指定條件查詢","肥胖學生查詢","身高體重未檢名冊","生長遲緩名冊");
		switch ($_POST['input_item']) {
			case "1":
			case "5":
			case "6":
			case "8":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
					$health_data->get_wh();
					$smarty->assign("Bid_arr",$Bid_arr);
					$smarty->assign("health_data",$health_data);
					if ($_POST['input_item']=="1") $smarty->assign("ifile","health_analyze_wh_class.tpl");
				}
				if ($_POST['input_item']=="5") {
					if ($_POST['minh'] && $_POST['minh']<$minh) $_POST['minh']=$minh; 
					if ($_POST['maxh'] && $_POST['maxh']>$maxh) $_POST['maxh']=$maxh; 
					if ($_POST['minw'] && $_POST['minw']<$minw) $_POST['minw']=$minw; 
					if ($_POST['maxw'] && $_POST['maxw']>$maxw) $_POST['maxw']=$maxw; 
					$smarty->assign("ifile","health_analyze_wh_class2.tpl");
				}
				if ($_POST['input_item']=="6") $smarty->assign("ifile","health_analyze_wh_class3.tpl");
				if ($_POST['input_item']=="8") $smarty->assign("ifile","health_analyze_wh_stunting.tpl");
				break;
			case "2":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
					$health_data->get_wh();
					$cal_arr=array();
					for($i=15;$i<=46;$i++) {
						for($j=1;$j<=3;$j++) {
							$cal_arr[$i][$j]=0;
						}
					}
					$minh=0;
					$maxh=0;
					while(list($sn,$v)=each($health_data->health_data)) {
						if ($v[$_POST['year_seme']]['height']>0) {
							$h=$v[$_POST['year_seme']]['height'];
							$data_arr[$health_data->stud_base[$sn][stud_sex]][nums]+=1;
							$data_arr[$health_data->stud_base[$sn][stud_sex]][value]+=$h;
							$cal_arr[floor($h/5)][$health_data->stud_base[$sn][stud_sex]]++;
							if ($minh==0 || $minh>$h) $minh=$h;
							if ($maxh==0 || $maxh<$h) $maxh=$h;
						}
					}
					$minh=floor($minh/5);
					$maxh=floor($maxh/5);
					$xlabel=array();
					$ydata=array();
					while(list($k,$v)=each($cal_arr)) {
						if ($k>=$minh && $k<=$maxh) {
							$s=($k*5)."-".($k*5+5);
							$xlabel[]=$s;
							$ydata[0][]=$v[1];
							$ydata[1][]=$v[2];
							$ydata[2][]=$v[1]+$v[2];
						}
					}
					while(list($i,$v)=each($data_arr)) {
						$data_arr[$i][avg]=$data_arr[$i][value]/$data_arr[$i][nums];
					}
					$data_arr[3][nums]=$data_arr[1][nums]+$data_arr[2][nums];
					$data_arr[3][avg]=($data_arr[1][value]+$data_arr[2][value])/$data_arr[3][nums];
					reset($health_data->health_data);
					while(list($sn,$v)=each($health_data->health_data)) {
						if ($v[$_POST['year_seme']]['height']>0) {
							$data_arr[$health_data->stud_base[$sn][stud_sex]][std]+=pow($v[$_POST['year_seme']]['height']-$data_arr[$health_data->stud_base[$sn][stud_sex]][avg],2);
							$data_arr[3][std]+=pow($v[$_POST['year_seme']]['height']-$data_arr[3][avg],2);
						}
					}
					reset($data_arr);
					while(list($i,$v)=each($data_arr)) {
						$data_arr[$i][std]=sqrt($data_arr[$i][std]/$data_arr[$i][nums]);
					}
					$smarty->assign("data_arr",$data_arr);
					$smarty->assign("ifile","health_analyze_h_bar.tpl");
					//畫圖
					$sch=get_school_base();
					//session_register("ydata");
					$_SESSION["ydata"]=$ydata;
					//session_register("mtitle");
					$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 身高分析長條圖";
					//session_register("xtitle");
					$_SESSION["xtitle"]="身高 (公分)";
					//session_register("ytitle");
					$_SESSION["ytitle"]="人數 (人)";
					//session_register("legend");
					$_SESSION["legend"]=array("男生","女生","合計");
					//session_register("xlabel");
					$_SESSION["xlabel"]=$xlabel;
					//session_register("graph_kind");
					if ($_POST["graph_kind"]=="") $_POST["graph_kind"]="bar";
					$_SESSION["graph_kind"]=$_POST["graph_kind"];
				}
				break;
			case "3":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
					$health_data->get_wh();
					$cal_arr=array();
					for($i=2;$i<=30;$i++) {
						for($j=1;$j<=3;$j++) {
							$cal_arr[$i][$j]=0;
						}
					}
					$minw=0;
					$maxw=0;
					while(list($sn,$v)=each($health_data->health_data)) {
						if ($v[$_POST['year_seme']]['weight']>0) {
							$w=$v[$_POST['year_seme']]['weight'];
							$data_arr[$health_data->stud_base[$sn][stud_sex]][nums]+=1;
							$data_arr[$health_data->stud_base[$sn][stud_sex]][value]+=$w;
							$cal_arr[floor($w/5)][$health_data->stud_base[$sn][stud_sex]]++;
							if ($minw==0 || $minw>$w) $minw=$w;
							if ($maxw==0 || $maxw<$w) $maxw=$w;
						}
					}
					$minw=floor($minw/5);
					$maxw=floor($maxw/5);
					$xlabel=array();
					$ydata=array();
					while(list($k,$v)=each($cal_arr)) {
						if ($k>=$minw && $k<=$maxw) {
							$s=($k*5)."-".($k*5+5);
							$xlabel[]=$s;
							$ydata[0][]=$v[1];
							$ydata[1][]=$v[2];
							$ydata[2][]=$v[1]+$v[2];
						}
					}
					while(list($i,$v)=each($data_arr)) {
						$data_arr[$i][avg]=$data_arr[$i][value]/$data_arr[$i][nums];
					}
					$data_arr[3][nums]=$data_arr[1][nums]+$data_arr[2][nums];
					$data_arr[3][avg]=($data_arr[1][value]+$data_arr[2][value])/$data_arr[3][nums];
					reset($health_data->health_data);
					while(list($sn,$v)=each($health_data->health_data)) {
						if ($v[$_POST['year_seme']]['weight']>0) {
							$data_arr[$health_data->stud_base[$sn][stud_sex]][std]+=pow($v[$_POST['year_seme']]['weight']-$data_arr[$health_data->stud_base[$sn][stud_sex]][avg],2);
							$data_arr[3][std]+=pow($v[$_POST['year_seme']]['weight']-$data_arr[3][avg],2);
						}
					}
					reset($data_arr);
					while(list($i,$v)=each($data_arr)) {
						$data_arr[$i][std]=sqrt($data_arr[$i][std]/$data_arr[$i][nums]);
					}
					$smarty->assign("data_arr",$data_arr);
					$smarty->assign("ifile","health_analyze_w_bar.tpl");
					//畫圖
					$sch=get_school_base();
					//session_register("ydata");
					$_SESSION["ydata"]=$ydata;
					//session_register("mtitle");
					$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 體重分析長條圖";
					//session_register("xtitle");
					$_SESSION["xtitle"]="體重 (公斤)";
					//session_register("ytitle");
					$_SESSION["ytitle"]="人數 (人)";
					//session_register("legend");
					$_SESSION["legend"]=array("男生","女生","合計");
					//session_register("xlabel");
					$_SESSION["xlabel"]=$xlabel;
					//session_register("graph_kind");
					if ($_POST["graph_kind"]=="") $_POST["graph_kind"]="bar";
					$_SESSION["graph_kind"]=$_POST["graph_kind"];
				}
				break;
			case "4":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
					$health_data->get_wh();
					$xlabel=array();
					for($i=0;$i<=3;$i++) {
						$xlabel[$i]=$Bid_arr[$i];
						for($j=1;$j<=3;$j++) {
							$cal_arr[$i][$j]=0;
						}
					}
					while(list($sn,$v)=each($health_data->health_data)) {
						if ($v[$_POST['year_seme']]['BMI']>0) {
							$b=$v[$_POST['year_seme']]['Bid'];
							$cal_arr[$b][$health_data->stud_base[$sn][stud_sex]]++;
						}
					}
					$ydata=array();
					while(list($k,$v)=each($cal_arr)) {
						$ydata[0][]=$v[1];
						$ydata[1][]=$v[2];
						$ydata[2][]=$v[1]+$v[2];
					}
					$smarty->assign("ifile","health_analyze_b_bar.tpl");
					//畫圖
					$sch=get_school_base();
					//session_register("ydata");
					$_SESSION["ydata"]=$ydata;
					//session_register("mtitle");
					$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 生長指數分析長條圖";
					//session_register("xtitle");
					$_SESSION["xtitle"]="體重狀況";
					//session_register("ytitle");
					$_SESSION["ytitle"]="人數 (人)";
					//session_register("legend");
					$_SESSION["legend"]=array("男生","女生","合計");
					//session_register("xlabel");
					$_SESSION["xlabel"]=$xlabel;
					//session_register("xclabel");
					$_SESSION["xclabel"]=1;
					//session_register("graph_kind");
					if ($_POST["graph_kind"]=="") $_POST["graph_kind"]="bar";
					$_SESSION["graph_kind"]=$_POST["graph_kind"];
				}
				break;
		case "7":
			$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
			if ($_POST['class_name']) {
				$health_data=new health_chart();
				if ($_POST['update']) $health_data->update_wh($_POST['update']);
				if (strlen($_POST['class_name'])==1)
					$class_name_str="and seme_class like '".$_POST['class_name']."%'";
				elseif ($_POST['class_name']=="all")
					$class_name_str="";
				else
					$class_name_str="and seme_class='".$_POST['class_name']."'";
				$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."' $class_name_str";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn[$res->fields['student_sn']]=array();
					$res->MoveNext();
				}
				if (count($sn)>0) {
					$query="select * from health_wh where year='$sel_year' and semester='$sel_seme'";
					$res=$CONN->Execute($query);
					while(!$res->EOF) {
						$sn[$res->fields['student_sn']][weight]=$res->fields['weight'];
						$sn[$res->fields['student_sn']][height]=$res->fields['height'];
						$res->MoveNext();
					}
					$sn_arr=array();
					foreach ($sn as $s => $v) {
						if (intval($v[weight])==0 or intval($v[weight])==0) $sn_arr[]=$s;
					}
					$health_data->set_stud($sn_arr,$sel_year,$sel_seme);
					$health_data->get_wh();
				}
				$smarty->assign("ifile","health_wh_unmeasure.tpl");
				$smarty->assign("mfile","health_measure_date.tpl");
				$smarty->assign("health_data",$health_data);
			}
			break;
			}
		break;
	case "2":
		$work_menu_arr=array("請選擇作業項目","視力班級明細表","度數班級明細表","全校統計表","視力統計名冊","視力未檢名冊","視力統計圖表","就醫矯治追蹤統計","結果統計表","裸視視力結果統計表","通知單統計表","指定條件查詢");
		switch ($_POST['input_item']) {
			case "1":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
					$health_data->get_sight();
					$smarty->assign("health_data",$health_data);
					$smarty->assign("ifile","health_analyze_sight_class.tpl");
				}
				break;
			case "3":
				$class_menu="";
				$health_data=new health_chart();
				$health_data->get_stud_base($sel_year,$sel_seme,"all");
				$health_data->get_sight();
				while(list($seme_class,$v)=each($health_data->stud_data)) {
					while(list($seme_num,$vv)=each($v)) {
						$year_name=substr($seme_class,0,strlen($seme_class)-2);
						$class_name=substr($seme_class,-2,2);
						$sex=$health_data->stud_base[$vv[student_sn]][stud_sex];
						$r_sight_o=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][r][sight_o];
						$l_sight_o=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][l][sight_o];
						if ($r_sight_o<>"" && $l_sight_o<>"") {
							$ytemp[$class_name][$year_name][0][$sex]++;
						}
						if (($r_sight_o<>"" && $r_sight_o<0.9) || ($l_sight_o<>"" && $l_sight_o<0.9)) {
							$ytemp[$class_name][$year_name][1][$sex]++;
						}
						$ytemp[$class_name][$year_name][2][$sex]=0;
						$ytemp[$class_name][$year_name][3][$sex]=0;
					}
				}
				for($i=0;$i<4;$i++) $kdata[]=$i;
				for($i=1;$i<3;$i++) $sdata[]=$i;
				$smarty->assign("class_year",$class_year);
				$smarty->assign("health_data",$health_data);
				$smarty->assign("rowdata",$ytemp);
				$smarty->assign("kdata",$kdata);
				$smarty->assign("sdata",$sdata);
				$smarty->assign("ifile","health_analyze_sight_all.tpl");
				break;
			case "4":
				$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
				if ($_POST['class_name']) {
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
					$health_data->get_sight();
					switch($_POST['status_id']) {
						case 0:
						
							break;
						case 1:
						
							break;
						case 2:
							while(list($seme_class,$v)=each($health_data->stud_data)) {
								while(list($seme_num,$vv)=each($v)) {
									$year_name=substr($seme_class,0,strlen($seme_class)-2);
									$class_name=substr($seme_class,-2,2);
									$sn=$vv[student_sn];
									$r_sight_o=$health_data->health_data[$sn][$_POST['year_seme']][r][sight_o];
									$l_sight_o=$health_data->health_data[$sn][$_POST['year_seme']][l][sight_o];
									if ($r_sight_o>=0.9 && $l_sight_o>=0.9) $rowdata[]=array($year_name,$class_name,$seme_num,$sn,$health_data->stud_base[$sn][stud_name],$health_data->stud_base[$sn][stud_sex],$r_sight_o,$l_sight_o,$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][r][sight_r],$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][l][sight_r]);
								}
							}
							break;
						case 3:
							break;
						case 4:
						
							break;
						case 5:
						
							break;
						case 6:
						
							break;
					}
				}
				$sight_chk_status=array("視力不良(不含矯正)","視力不良(含矯正)","裸視正常","裸視異常","矯正正常","矯正異常","全部");
				$sight_value=array("1.0"=>"未達1.0","0.9"=>"未達0.9","0.8"=>"未達0.8","0.7"=>"未達0.7","0.6"=>"未達0.6","0.5"=>"未達0.5","0.4"=>"未達0.4","0.3"=>"未達0.3","0.2"=>"未達0.2","0.1"=>"未達0.1");
				if ($_POST['o_value']=="") $_POST['o_value']=0.9;
				if ($_POST['r_value']=="") $_POST['r_value']=0.5;
				$smarty->assign("sight_chk_status",$sight_chk_status);
				$smarty->assign("sight_value",$sight_value);
				$smarty->assign("ifile","health_analyze_sight_class2.tpl");
				break;
			case "6":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
					$health_data->get_sight();
					$xlabel=array("男生","女生","合計");
					$cal_arr=array();
					$ydata=array();
					for($i=0;$i<=4;$i++) for($j=0;$j<=2;$j++) $ydata[$i][$j]=0;
					while(list($sn,$v)=each($health_data->health_data)) {
						if ($v[$_POST['year_seme']]['r']['sight_o']>0.8 && $v[$_POST['year_seme']]['l']['sight_o']>0.8) $cal_arr[0][$health_data->stud_base[$sn][stud_sex]]++;
						if ($v[$_POST['year_seme']]['r']['sight_o']<=0.8 || $v[$_POST['year_seme']]['l']['sight_o']<=0.8) $cal_arr[1][$health_data->stud_base[$sn][stud_sex]]++;
						if ($v[$_POST['year_seme']]['r']['sight_r']>0.8 && $v[$_POST['year_seme']]['l']['sight_r']>0.8) $cal_arr[2][$health_data->stud_base[$sn][stud_sex]]++;
						if (($v[$_POST['year_seme']]['r']['sight_o']<=0.8 && $v[$_POST['year_seme']]['r']['sight_r']<=0.8) || ($v[$_POST['year_seme']]['l']['sight_o']<=0.8 && $v[$_POST['year_seme']]['l']['sight_r']<=0.8)) {
							if ($v[$_POST['year_seme']]['r']['sight_r']==0 && $v[$_POST['year_seme']]['l']['sight_r']==0)
								$cal_arr[4][$health_data->stud_base[$sn][stud_sex]]++;
							else
								$cal_arr[3][$health_data->stud_base[$sn][stud_sex]]++;
						}
					}
					while(list($k,$v)=each($cal_arr)) {
						foreach($v as $i=>$j) $ydata[$k][$i-1]=$j;
						$ydata[$k][2]=$v[0]+$v[1];
					}
					$smarty->assign("ifile","health_graph_sel.tpl");
					//畫圖
					$sch=get_school_base();
					//session_register("ydata");
					$_SESSION["ydata"]=$ydata;
					//session_register("mtitle");
					$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 視力檢查結果統計圖";
					//session_register("xtitle");
					$_SESSION["xtitle"]="性別";
					//session_register("ytitle");
					$_SESSION["ytitle"]="人數 (人)";
					//session_register("legend");
					$_SESSION["legend"]=array("裸視正常","裸視不良","矯正正常","矯正不良","未矯正");
					//session_register("xlabel");
					$_SESSION["xlabel"]=$xlabel;
					//session_register("xclabel");
					$_SESSION["xclabel"]=1;
					//session_register("graph_kind");
					if ($_POST["graph_kind"]=="") $_POST["graph_kind"]="bar";
					$_SESSION["graph_kind"]=$_POST["graph_kind"];
				}
				break;
		}
		break;
	case "3":
		$smarty->assign("ifile","health_analyze_teesem.tpl");
		break;
	case "4":
		$work_menu_arr=array("請選擇作業項目","接種人數統計","持卡及接種率統計","補種人數統計");
		
		switch ($_POST['input_item']) {
			case "1":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$smarty->assign("ifile","health_analyze_inject_count1.tpl");
				}
				break;
			case "2":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$smarty->assign("ifile","health_analyze_inject_count2.tpl");
				}
				break;
			case "3":
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$smarty->assign("ifile","health_analyze_inject_count3.tpl");
				}
				break;
		}
		break;
	case "5":
		$year_seme_menu="";
		if ($_POST['class_name']=="") $_POST['class_name']="all";
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		$work_menu_arr=array("請選擇作業項目","受傷部位","受傷地點","事故傷害","疾病症狀","處理方式","受傷地點比較分析表","傷病統計報表");
		if ($_POST['input_item']) {
			$d=curr_year_seme_day($sel_year,$sel_seme);
			if ($_POST['start_analyze']=="" && $_POST['start_date']=="") $_POST['start_date']=$d['st_start'];
			if ($_POST['start_analyze']=="" && $_POST['end_date']=="") $_POST['end_date']=$d['st_end'];
			$smarty->assign("ifile","health_analyze_accident.tpl");
			$smarty->assign("sel_year",$sel_year);
			$smarty->assign("sel_seme",$sel_seme);
		}
		if ($_POST['start_analyze']) {
			$wh_str="";
			if ($_POST['start_date']) $wh_str.=" and sign_time>='".$_POST['start_date']."'";
			if ($_POST['end_date']) $wh_str.=" and sign_time<='".$_POST['end_date']." 23:59:59'";
			if ($wh_str) {
				$wh_str="where".substr($wh_str,4);
			}
			switch ($_POST['input_item']) {
				case "1":
					$apart=get_accident_item(0,"health_accident_part");
					$xlabel=array_keys($apart);
					$query="select id from health_accident_record $wh_str";
					$res=$CONN->Execute($query);
					$temp_arr=array();
					while(!$res->EOF) {
						$temp_arr[]=$res->fields['id'];
						$res->MoveNext();
					}
					if (count($temp_arr)>0) {
						$temp_str="'".implode("','",$temp_arr)."'";
						$query="select count(id) as n,part_id from health_accident_part_record where id in ($temp_str) group by part_id";
						$res=$CONN->Execute($query);
						$temp_arr=array();
						while(!$res->EOF) {
							$temp_arr[$res->fields['part_id']]=$res->fields['n'];
							$res->MoveNext();
						}
						$x_arr=array();
						foreach($xlabel as $v) {
							$ydata[0][]=intval($temp_arr[$v]);
							$x_arr[]=$apart[$v];
						}
						$smarty->assign("ifile","health_graph.tpl");
						//畫圖
						$sch=get_school_base();
						//session_register("ydata");
						$_SESSION["ydata"]=$ydata;
						//session_register("mtitle");
						$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度 學生受傷部位統計分析長條圖";
						//session_register("xtitle");
						$_SESSION["xtitle"]="受傷部位";
						//session_register("ytitle");
						$_SESSION["ytitle"]="人數 (人)";
						//session_register("xlabel");
						$_SESSION["xlabel"]=$x_arr;
						//session_register("xclabel");
						$_SESSION["xclabel"]=1;
						//session_register("horizontal");
						$_SESSION["horizontal"]=true;
						//session_register("graph_kind");
						$_SESSION["graph_kind"]="bar";
					}
					break;
				case "2":
					$aplace=get_accident_item(0,"health_accident_place");
					$xlabel=array_keys($aplace);
					$query="select count(id) as n,place_id from health_accident_record $wh_str group by place_id";
					$res=$CONN->Execute($query);
					$temp_arr=array();
					while(!$res->EOF) {
						$temp_arr[$res->fields['place_id']]=$res->fields['n'];
						$res->MoveNext();
					}
					$x_arr=array();
					foreach($xlabel as $v) {
						$ydata[0][]=intval($temp_arr[$v]);
						if (strlen($aplace[$v])>8) $aplace[$v]=substr($aplace[$v],0,8)."\n".substr($aplace[$v],8);
						$x_arr[]=$aplace[$v];
					}
					$smarty->assign("ifile","health_graph.tpl");
					//畫圖
					$sch=get_school_base();
					//session_register("ydata");
					$_SESSION["ydata"]=$ydata;
					//session_register("mtitle");
					$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度 學生受傷地點統計分析長條圖";
					//session_register("xtitle");
					$_SESSION["xtitle"]="受傷地點";
					//session_register("ytitle");
					$_SESSION["ytitle"]="人數 (人)";
					//session_register("xlabel");
					$_SESSION["xlabel"]=$x_arr;
					//session_register("xclabel");
					$_SESSION["xclabel"]=1;
					//session_register("horizontal");
					$_SESSION["horizontal"]=true;
					//session_register("graph_kind");
					$_SESSION["graph_kind"]="bar";
					break;
				case "3":
				case "4":
					if ($_POST['input_item']==3) {
						$mtitle="事故傷害";
						$atitle="傷害類別";
					} else {
						$mtitle="疾病症狀";
						$atitle="症狀類別";
					}
					$astatus=get_accident_item(0,"health_accident_status");
					$xlabel=array_keys($astatus);
					$query="select id from health_accident_record $wh_str";
					$res=$CONN->Execute($query);
					$temp_arr=array();
					while(!$res->EOF) {
						$temp_arr[]=$res->fields['id'];
						$res->MoveNext();
					}
					if (count($temp_arr)>0) {
						$temp_str="'".implode("','",$temp_arr)."'";
						$query="select count(id) as n,status_id from health_accident_status_record where id in ($temp_str) group by status_id";
						$res=$CONN->Execute($query);
						$temp_arr=array();
						while(!$res->EOF) {
							$temp_arr[$res->fields['status_id']]=$res->fields['n'];
							$res->MoveNext();
						}
						$x_arr=array();
						foreach($xlabel as $k => $v) {
							if (($_POST['input_item']==3 && $k<10) || ($_POST['input_item']==4 && $k>9 && $k<24)) {
								$ydata[0][]=intval($temp_arr[$v]);
								$x_arr[]=$astatus[$v];
							}
						}
						$smarty->assign("ifile","health_graph.tpl");
						//畫圖
						$sch=get_school_base();
						//session_register("ydata");
						$_SESSION["ydata"]=$ydata;
						//session_register("mtitle");
						$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度 學生".$mtitle."統計分析長條圖";
						//session_register("xtitle");
						$_SESSION["xtitle"]=$atitle;
						//session_register("ytitle");
						$_SESSION["ytitle"]="人數 (人)";
						//session_register("xlabel");
						$_SESSION["xlabel"]=$x_arr;
						//session_register("xclabel");
						$_SESSION["xclabel"]=1;
						//session_register("horizontal");
						$_SESSION["horizontal"]=true;
						//session_register("graph_kind");
						$_SESSION["graph_kind"]="bar";
					}
					break;
				case "5":
					$aattend=get_accident_item(0,"health_accident_attend");
					$xlabel=array_keys($aattend);
					$query="select id from health_accident_record $wh_str";
					$res=$CONN->Execute($query);
					$temp_arr=array();
					while(!$res->EOF) {
						$temp_arr[]=$res->fields['id'];
						$res->MoveNext();
					}
					if (count($temp_arr)>0) {
						$temp_str="'".implode("','",$temp_arr)."'";
						$query="select count(id) as n,attend_id from health_accident_attend_record where id in ($temp_str) group by attend_id";
						$res=$CONN->Execute($query);
						$temp_arr=array();
						while(!$res->EOF) {
							$temp_arr[$res->fields['attend_id']]=$res->fields['n'];
							$res->MoveNext();
						}
						$x_arr=array();
						foreach($xlabel as $v) {
							$ydata[0][]=intval($temp_arr[$v]);
							$x_arr[]=$aattend[$v];
						}
						$smarty->assign("ifile","health_graph.tpl");
						//畫圖
						$sch=get_school_base();
						//session_register("ydata");
						$_SESSION["ydata"]=$ydata;
						//session_register("mtitle");
						$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度 學生傷病處理統計分析長條圖";
						//session_register("xtitle");
						$_SESSION["xtitle"]="處理方式";
						//session_register("ytitle");
						$_SESSION["ytitle"]="人數 (人)";
						//session_register("xlabel");
						$_SESSION["xlabel"]=$x_arr;
						//session_register("xclabel");
						$_SESSION["xclabel"]=1;
						//session_register("horizontal");
						$_SESSION["horizontal"]=true;
						//session_register("graph_kind");
						$_SESSION["graph_kind"]="bar";
					}
					break;
				case "6":
					$aplace=get_accident_item(0,"health_accident_place");
					$apart=get_accident_item(0,"health_accident_part");
					$astatus=get_accident_item(0,"health_accident_status");
					$temp_arr=array();
					$num_arr=array();
					$query="select * from health_accident_record $wh_str";
					$res=$CONN->Execute($query);
					while(!$res->EOF) {
						$temp_arr[$res->fields['place_id']][]=$res->fields['id'];
						$res->MoveNext();
					}
					foreach($temp_arr as $place_id => $d) {
						$str="'".implode("','",$d)."'";
						$query="select count(id) as n,part_id from health_accident_part_record where id in ($str) group by part_id";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							$num_arr[$place_id][$res->fields['part_id']]=$res->fields['n'];
							$num_arr[$place_id]['all']+=$res->fields['n'];
							$num_arr['all'][$res->fields['part_id']]+=$res->fields['n'];
							$num_arr['all']['all']+=$res->fields['n'];
							$res->MoveNext();
						}
					}
					$smarty->assign("rowdata",$num_arr);
					$num_arr=array();
					reset($temp_arr);
					foreach($temp_arr as $place_id => $d) {
						$str="'".implode("','",$d)."'";
						$query="select count(id) as n,status_id from health_accident_status_record where id in ($str) group by status_id";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							if ($res->fields['status_id']<=10) {
								$num_arr[$place_id][$res->fields['status_id']]=$res->fields['n'];
								$num_arr[$place_id]['all']+=$res->fields['n'];
								$num_arr['all'][$res->fields['status_id']]+=$res->fields['n'];
								$num_arr['all']['all']+=$res->fields['n'];
							}
							$res->MoveNext();
						}
					}
					$smarty->assign("rowdata2",$num_arr);
					$smarty->assign("aplace",$aplace);
					$smarty->assign("apart",$apart);
					$smarty->assign("astatus",$astatus);
					$smarty->assign("ifile","health_analyze_accident_place.tpl");
					break;
				case "7":
					$sdate=($sel_year+1911)."-08-01";
					$edate=($sel_year+1912)."-08-01";
					$query="select count(a.id) as n,mid(a.sign_time,6,2) as m,b.stud_sex from health_accident_record a left join stud_base b on a.student_sn=b.student_sn where a.sign_time>='$sdate' and a.sign_time<'$edate' group by m,b.stud_sex";
					$res=$CONN->Execute($query) or die($query);
					while(!$res->EOF) {
						$temp_arr['sex'][$res->fields['stud_sex']][intval($res->fields['m'])]=$res->fields['n'];
						$temp_arr['sex'][3][intval($res->fields['m'])]+=$res->fields['n'];
						$temp_arr['sex'][$res->fields['stud_sex']]['total']+=$res->fields['n'];
						$temp_arr['sex'][3]['total']+=$res->fields['n'];
						$res->MoveNext();
					}
					$query="select count(id) as n,mid(sign_time,6,2) as m,place_id from health_accident_record where sign_time>='$sdate' and sign_time<'$edate' group by m,place_id";
					$res=$CONN->Execute($query) or die($query);
					while(!$res->EOF) {
						$place_id=$res->fields['place_id'];
						if ($place_id>10) $place_id=999;
						$temp_arr['place'][$place_id][intval($res->fields['m'])]=$res->fields['n'];
						$temp_arr['place'][$place_id]['total']+=$res->fields['n'];
						$res->MoveNext();
					}
					$query="select count(a.id) as n,mid(a.sign_time,6,2) as m,b.part_id from health_accident_record a,health_accident_part_record b where a.id=b.id and a.sign_time>='$sdate' and a.sign_time<'$edate' group by m,b.part_id";
					$res=$CONN->Execute($query) or die($query);
					while(!$res->EOF) {
						$part_id=$res->fields['part_id'];
						if ($part_id<=15) {
							$temp_arr['part'][$part_id][intval($res->fields['m'])]=$res->fields['n'];
							$temp_arr['part'][$part_id]['total']+=$res->fields['n'];
						}
						$res->MoveNext();
					}
					$query="select count(a.id) as n,mid(a.sign_time,6,2) as m,b.status_id from health_accident_record a,health_accident_status_record b where a.id=b.id and a.sign_time>='$sdate' and a.sign_time<'$edate' group by m,b.status_id";
					$res=$CONN->Execute($query) or die($query);
					while(!$res->EOF) {
						$status_id=$res->fields['status_id'];
						if ($status_id<=24) {
							$temp_arr['status'][$status_id][intval($res->fields['m'])]=$res->fields['n'];
							$temp_arr['status'][$status_id]['total']+=$res->fields['n'];
						}
						$res->MoveNext();
					}
					$query="select count(a.id) as n,mid(a.sign_time,6,2) as m,b.attend_id from health_accident_record a,health_accident_attend_record b where a.id=b.id and a.sign_time>='$sdate' and a.sign_time<'$edate' group by m,b.attend_id";
					$res=$CONN->Execute($query) or die($query);
					while(!$res->EOF) {
						$attend_id=$res->fields['attend_id'];
						if ($attend_id<=9) {
							$temp_arr['attend'][$attend_id][intval($res->fields['m'])]=$res->fields['n'];
							$temp_arr['attend'][$attend_id]['total']+=$res->fields['n'];
						}
						$res->MoveNext();
					}
					$query="select sum(obs_min) as n,mid(sign_time,6,2) as m from health_accident_record where sign_time>='$sdate' and sign_time<'$edate' group by m";
					$res=$CONN->Execute($query) or die($query);
					while(!$res->EOF) {
						$temp_arr['min'][intval($res->fields['m'])]=$res->fields['n'];
						$temp_arr['min']['total']+=$res->fields['n'];
						$res->MoveNext();
					}
					$smarty->assign("ifile","health_analyze_accident_count.tpl");
					break;
			}
		}
		break;
	case "7":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		$work_menu_arr=array("請選擇作業項目","健康檢查班級明細表","結果暨矯治追蹤統計表","健康檢查未檢名單","尿液檢查班級明細表","蟯蟲檢查班級明細表","頭蝨檢查班級明細表","特殊疾病統計");
		switch ($_POST['input_item']) {
			case 2:
				if ($_POST['year_seme'] && $_POST['class_name']) {
					$health_data=new health_chart();
					$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
					$temp_arr=array();
					if (count($health_data->snb_arr)>0) {
						$snb_str="'".implode("','",$health_data->snb_arr)."'";
						$query="select subject,no,status,count(*) as num from health_checks_record where year='$sel_year' and semester='$sel_seme' and student_sn in ($snb_str) group by subject,no,status";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							$temp_arr[1][$res->fields['subject']][$res->fields['no']][$res->fields['status']]=$res->fields['num'];
							$res->MoveNext();
						}
					}
					if (count($health_data->sng_arr)>0) {
						$sng_str="'".implode("','",$health_data->sng_arr)."'";
						$query="select subject,no,status,count(*) as num from health_checks_record where year='$sel_year' and semester='$sel_seme' and student_sn in ($sng_str) group by subject,no,status";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							$temp_arr[2][$res->fields['subject']][$res->fields['no']][$res->fields['status']]=$res->fields['num'];
							$res->MoveNext();
						}
					}
					$health_data->get_sight();
					$smarty->assign("ifile","health_analyze_check_count.tpl");
					$smarty->assign("rowdata",$temp_arr);
					$smarty->assign("bnum",count($health_data->snb_arr));
					$smarty->assign("gnum",count($health_data->sng_arr));
				}
				break;
		}
		break;
	case "8":
		$_POST['input_item']=" ";
		$class_menu="";
		$query="select distinct di_id from health_disease order by di_id";
		$res=$CONN->Execute($query);
		$smarty->assign("dis_arr",$res->GetRows());
		$query="select student_sn from health_disease";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sn_arr[]=$res->fields['student_sn'];
			$res->MoveNext();
		}
		if (count($sn_arr)>0) {
			$sn_str="'".implode("','",$sn_arr)."'";
			$sn_arr=array();
			$query="select * from stud_seme where seme_year_seme='".$_POST['year_seme']."' and student_sn in ($sn_str)";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn_arr[substr($res->fields['seme_class'],0,1)][]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			if (count($sn_arr)>0) {
				$temp_arr=array();
				while(list($k,$v)=each($sn_arr)) {
					if (count($sn_arr[$k])>0) {
						$sn_str="'".implode("','",$sn_arr[$k])."'";
						$query="select count(student_sn) as n,di_id from health_disease where student_sn in ($sn_str) group by di_id";
						$res=$CONN->Execute($query);
						while(!$res->EOF) {
							$temp_arr[$k][$res->fields['di_id']]=$res->fields['n'];
							$temp_arr['all'][$res->fields['di_id']]+=$res->fields['n'];
							$res->MoveNext();
						}
					}
				}
			}
		}
		$smarty->assign("rowdata",$temp_arr);
		$smarty->assign("class_year",$class_year);
		$smarty->assign("disease_kind_arr",hDiseaseKind());
		$smarty->assign("ifile","health_other_count.tpl");
		break;
}
if ($work_menu_arr)
	$smarty->assign("work_menu",sub_menu($work_menu_arr,$_POST['input_item'],"input_item"));

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","統計分析作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_analyze.tpl");
?>
