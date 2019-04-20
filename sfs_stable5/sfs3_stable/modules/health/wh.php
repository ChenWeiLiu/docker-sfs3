<?php

// $Id: wh.php 6808 2012-06-22 08:14:46Z smallduh $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['df_item']=="") $_POST['df_item']="default_jh";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","身高體重未測量名單","身高體重通知單","班級身高體重清單","班級身高體重視力清單","生長發育統計表","體位判讀結果統計表","年級身高統計圖","年級體重統計圖","體位判讀結果統計圖","生長遲緩名冊 / 通知單","課桌椅型號統計表");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST['sub_menu_id']) {
	case "1":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			if ($_POST['update'] && $_POST['save']) $health_data->update_wh($_POST['update']);
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
				$query="select * from health_WH where year='$sel_year' and semester='$sel_seme'";
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
			if ($_POST['print']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(3);
				$x->setRowText(array("年級","班級","座號","姓名","身高","體重"));
				$x->items=get_whs($health_data,$_POST['year_seme'],"wh",1);
				$x->writeFile();
				$x->process();
				exit;
			}
			$smarty->assign("ifile","health_wh_unmeasure.tpl");
			$smarty->assign("mfile","health_measure_date.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "2":
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			if ($_POST['print'] && count($_POST['student_sn']>0)) {
				foreach($_POST['student_sn'] as $s) $sn[]=$s;
				$health_data->set_stud($sn,$sel_year,$sel_seme);
				$health_data->get_wh();
				$smarty->assign("health_data",$health_data);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("year_data",year_base($sel_year,$sel_seme));
				$smarty->assign("class_data",class_name($sel_year,$sel_seme));
				$smarty->assign("Bid_arr",$Bid_arr);
				$smarty->display("WHnotification.tpl");
				exit;
			}
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_wh();
			$smarty->assign("ifile","health_wh_noti.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "3":
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$rowtext=array("年級","班級","座號","姓名","身高","體重","BMI","體位判讀");
			if ($_POST['table']) $rowtext[]="課桌椅型號";
			if ($_POST['ods_all']) {
				$health_data->get_stud_base($sel_year,$sel_seme,substr($_POST['class_name'],0,1));
				$health_data->get_wh();
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$x->setRowText($rowtext);
				$x->items=get_whs($health_data,$_POST['year_seme'],"whb",1);
				$x->writeFile();
				$x->process();
				exit;
			}
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_wh();
			if ($_POST['xls']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(6);
				$x->setRowText($rowtext);
				$x->addSheet($_POST['class_name']);
				$x->items=get_whs($health_data,$_POST['year_seme'],"whb");
				$x->writeSheet();
				$x->process();
				exit;
			}
			if ($_POST['ods']) {
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$x->setRowText($rowtext);
				$x->addSheet($_POST['class_name']);
				$x->items=get_whs($health_data,$_POST['year_seme'],"whb");
				$x->writeSheet();
				$x->process();
				exit;
			}
			$smarty->assign("ifile","health_wh_class_list.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "4":
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$rowtext=array("年級","班級","座號","姓名","身高","體重","BMI","體位判讀","裸視右","裸視左","矯正右","矯正左");
			if ($_POST['ods_all']) {
				$health_data->get_stud_base($sel_year,$sel_seme,substr($_POST['class_name'],0,1));
				$health_data->get_wh();
				$health_data->get_sight();
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$x->setRowText($rowtext);
				$x->items=get_whs($health_data,$_POST['year_seme'],"whs",1);
				$x->writeFile();
				$x->process();
				exit;
			}
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_wh();
			$health_data->get_sight();
			if ($_POST['xls']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(6);
				$x->setRowText($rowtext);
				$x->addSheet($_POST['class_name']);
				$x->items=get_whs($health_data,$_POST['year_seme'],"whs");
				$x->writeSheet();
				$x->process();
				exit;
			}
			if ($_POST['ods']) {
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$x->setRowText($rowtext);
				$x->addSheet($_POST['class_name']);
				$x->items=get_whs($health_data,$_POST['year_seme'],"whs");
				$x->writeSheet();
				$x->process();
				exit;
			}
			$smarty->assign("ifile","health_whs_class_list.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "5":
		$class_menu="";
		$health_data=new health_chart();
		$health_data->get_stud_base($sel_year,$sel_seme,"all");
		$health_data->get_wh();
		$class_arr=year_base($sel_year,$sel_seme);
		while(list($seme_class,$v)=each($health_data->stud_data)) {
			while(list($seme_num,$vv)=each($v)) {
				$year_name=substr($seme_class,0,strlen($seme_class)-2);
				$sex=$health_data->stud_base[$vv[student_sn]][stud_sex];
				if ($health_data->health_data[$vv[student_sn]][$_POST['year_seme']][height]) {
					$ytemp[$year_name][$sex][height]+=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][height];
					$ytemp[$year_name][$sex][hnums]++;
				}
				if ($health_data->health_data[$vv[student_sn]][$_POST['year_seme']][weight]) {
					$ytemp[$year_name][$sex][weight]+=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][weight];
					$ytemp[$year_name][$sex][wnums]++;
				}
				if ($health_data->health_data[$vv[student_sn]][$_POST['year_seme']][BMI]) {
					$ytemp[$year_name][$sex][BMI]+=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][BMI];
					$ytemp[$year_name][$sex][bnums]++;
				}
			}
		}
		while(list($year_name,$v)=each($ytemp)) {
			while(list($sex,$vv)=each($v)) {
				$ytemp[$year_name][$sex][havg]=round($ytemp[$year_name][$sex][height]/$ytemp[$year_name][$sex][hnums],2);
				$ytemp[$year_name][$sex][wavg]=round($ytemp[$year_name][$sex][weight]/$ytemp[$year_name][$sex][wnums],2);
				$ytemp[$year_name][$sex][bavg]=round($ytemp[$year_name][$sex][BMI]/$ytemp[$year_name][$sex][bnums],2);
			}
		}
		$smarty->assign("data_arr",$ytemp);
		if ($_POST['print']) {
			$smarty->assign("school_data",get_school_base());
			$smarty->display("health_wh_count_print.tpl");
			exit;
		} else
			$smarty->assign("ifile","health_wh_count.tpl");
		break;
	case "6":
		$class_menu="";
		$health_data=new health_chart();
		$health_data->get_stud_base($sel_year,$sel_seme,"all");
		$health_data->get_wh();
		$class_arr=year_base($sel_year,$sel_seme);
		while(list($seme_class,$v)=each($health_data->stud_data)) {
			while(list($seme_num,$vv)=each($v)) {
				$year_name=substr($seme_class,0,-2);
				$sex=$health_data->stud_base[$vv['student_sn']]['stud_sex'];
				if ($health_data->health_data[$vv['student_sn']][$_POST['year_seme']]['BMI']) {
					$b=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']]['Bid'];
					$data_arr[$year_name][$sex][$b]++;
					$data_arr[$year_name][$sex]['all']++;
					$data_arr[$year_name]['all'][$b]++;
					$data_arr[$year_name]['all']['all']++;
					$data_arr['all']['all'][$b]++;
					$data_arr['all']['all']['all']++;
				}
			}
		}
		$smarty->assign("data_arr",$data_arr);
		$smarty->assign("sex_arr",array("1"=>"男","2"=>"女","all"=>"小計"));
		if ($_POST['print']) {
			$smarty->assign("school_data",get_school_base());
			$smarty->display("health_wh_body_count_print.tpl");
			exit;
		} else 
			$smarty->assign("ifile","health_wh_body_count.tpl");
		break;
	case "7":
	case "8":
		$class_menu="";
		$_POST['class_name']=" ";
		$health_data=new health_chart();
		$query="select student_sn from stud_seme where seme_year_seme='".$_POST['year_seme']."'";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sn[]=$res->fields['student_sn'];
			$res->MoveNext();
		}
		if (count($sn)>0) {
			$sn_str="'".implode("','",$sn)."'";
			$sn=array();
			$query="select student_sn from stud_base where stud_study_cond='0' and student_sn in ($sn_str)";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn[]=$res->fields['student_sn'];
				$res->MoveNext();
			}
			$health_data->set_stud($sn,$sel_year,$sel_seme);
			$health_data->get_wh();
			$sch=get_school_base();
			if ($_POST['sub_menu_id']==7) {
				$hvalue="height";
				$hcvalue="身高";
				$hyvalue="身高 (公分)";
			} else {
				$hvalue="weight";
				$hcvalue="體重";
				$hyvalue="體重 (公斤 )";
			}
			$ydata=array();
			$ytemp=array();
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$year_name=substr($seme_class,0,strlen($seme_class)-2);
					$ytemp[$year_name][$health_data->stud_base[$vv[student_sn]][stud_sex]][value]+=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][$hvalue];
					$ytemp[$year_name][$health_data->stud_base[$vv[student_sn]][stud_sex]][nums]++;
				}
			}
			$dy=($IS_JHORES==0)?1:7;
			while(list($year_name,$v)=each($ytemp)) {
				while(list($sex,$vv)=each($v)) {
					$ytemp[$year_name][$sex][avg]=$vv[value]/$vv[nums];
					$ytemp[$year_name][3][value]+=$vv[value];
					$ytemp[$year_name][3][nums]+=$vv[nums];
				}
				$xlabel[]=$year_name;
				$ytemp[$year_name][3][avg]=$ytemp[$year_name][3][value]/$ytemp[$year_name][3][nums];
				$ydata[0][$year_name-$dy]=$ytemp[$year_name][1][avg];
				$ydata[1][$year_name-$dy]=$ytemp[$year_name][2][avg];
				$ydata[2][$year_name-$dy]=$ytemp[$year_name][3][avg];
			}
			//畫圖
			//session_register("ydata");
			$_SESSION["ydata"]=$ydata;
			//session_register("mtitle");
			$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 年級平均".$hcvalue."統計圖";
			//session_register("xtitle");
			$_SESSION["xtitle"]="年級";
			//session_register("ytitle");
			$_SESSION["ytitle"]=$hyvalue;
			//session_register("legend");
			$_SESSION["legend"]=array("男生","女生","全部");
			//session_register("xlabel");
			$_SESSION["xlabel"]=$xlabel;
			//session_register("num_format");
			$_SESSION["num_format"]="%.1f";
			//session_register("graph_kind");
			if ($_POST["graph_kind"]=="") $_POST["graph_kind"]="bar";
			$_SESSION["graph_kind"]=$_POST["graph_kind"];
			$smarty->assign("ifile","health_graph_sel.tpl");
		}
		break;
	case "9":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
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
			//畫圖
			$sch=get_school_base();
			//session_register("ydata");
			$_SESSION["ydata"]=$ydata;
			//session_register("mtitle");
			$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度第".$sel_seme."學期 體位判讀結果統計圖";
			//session_register("xtitle");
			$_SESSION["xtitle"]="體位";
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
			$smarty->assign("ifile","health_graph_sel.tpl");
		}
		break;
	case "10":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			if ($_POST['noti']) {
				$sn_arr=explode(",",$_POST['ssn']);
				while(list($k,$s)=each($sn_arr)) {
					if ($s) $sn[]=$s;
				}
				$health_data->set_stud($sn,$sel_year,$sel_seme);
				$health_data->get_wh();
				$smarty->assign("health_data",$health_data);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("year_data",year_base($sel_year,$sel_seme));
				$smarty->assign("class_data",class_name($sel_year,$sel_seme));
				$smarty->display("Growthnotification.tpl");
				exit;
			}
			if ($_POST['xls']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(3);
				$x->setRowText(array("年級","班級","座號","姓名","身高","體重","體位","GHD","BMI","實歲","診斷代號","其他診斷","醫院"));
				$health_data=new health_chart();
				$sn_arr=explode(",",$_POST['ssn']);
				while(list($k,$s)=each($sn_arr)) {
					if ($s) $sn[]=$s;
				}
				$health_data->set_stud($sn,$sel_year,$sel_seme);
				$health_data->get_wh();
				$temp_arr=array();
				while(list($seme_class,$v)=each($health_data->stud_data)) {
					while(list($seme_num,$vv)=each($v)) {
						$a=array();
						$seme_year=substr($seme_class,0,-2);
						$seme_name=substr($seme_class,-2,2);
						$sn=$vv[student_sn];
						$hh=$health_data->health_data[$sn][$_POST['year_seme']];
						$temp_arr["chart"][]=array($seme_year,$seme_name,$seme_num,$health_data->stud_base[$sn][stud_name],$hh[height],$hh[weight],$Bid_arr[$hh[Bid]],$hh[GHD],$hh[BMI],$hh['years']);
					}
				}
				$x->items=$temp_arr;
				$x->writeFile();
				$x->process();
			}
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$health_data->get_wh();
			$class_arr=year_base($sel_year,$sel_seme);
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$year_name=substr($seme_class,0,strlen($seme_class)-2);
					$sex=$health_data->stud_base[$vv[student_sn]][stud_sex];
					if ($health_data->health_data[$vv[student_sn]][$_POST['year_seme']][height]) {
						$ytemp[$year_name][$sex][height]+=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][height];
						$ytemp[$year_name][$sex][hnums]++;
					}
					if ($health_data->health_data[$vv[student_sn]][$_POST['year_seme']][weight]) {
						$ytemp[$year_name][$sex][weight]+=$health_data->health_data[$vv[student_sn]][$_POST['year_seme']][weight];
						$ytemp[$year_name][$sex][wnums]++;
					}
				}
			}
			while(list($year_name,$v)=each($ytemp)) {
				while(list($sex,$vv)=each($v)) {
					$ytemp[$year_name][$sex][havg]=round($ytemp[$year_name][$sex][height]/$ytemp[$year_name][$sex][hnums],2);
					$ytemp[$year_name][$sex][wavg]=round($ytemp[$year_name][$sex][weight]/$ytemp[$year_name][$sex][wnums],2);
				}
			}
			$smarty->assign("data_arr",$ytemp);
			$smarty->assign("ifile","health_wh_stunting.tpl");
			$smarty->assign("health_data",$health_data);
		}
		break;
	case "11":
		$class_menu="";
		$health_data=new health_chart();
		$health_data->get_stud_base($sel_year,$sel_seme,"all");
		$health_data->get_wh();
		$data_arr=array();
		for($i=40;$i<=230;$i+=5) $tb_arr[$i]=0;
		while(list($seme_class,$v)=each($health_data->stud_data)) {
			while(list($seme_num,$vv)=each($v)) {
				$y=substr($seme_class,0,-2);
				$c=substr($seme_class,-2,2);
				$h=ceil(($health_data->health_data[$vv[student_sn]][$_POST['year_seme']][height]-1)/5)*5;
				if ($h>0) {
					$data_arr[$y][$c][$h]++;
					$data_arr[$y][$c][all]++;
					$data_arr[$y][all][$h]++;
					$data_arr[$y][all][all]++;
					$data_arr[all][all][$h]++;
					$data_arr[all][all][all]++;
					$tb_arr[$h]++;
				}
			}
		}
		$maxh=0;
		$minh=0;
		while(list($k,$v)=each($tb_arr)) {
			if ($v>0 && $minh==0) $minh=$k;
			if ($v>0 && $k>$maxh) $maxh=$k;
		}
		$tb_arr=array();
		for($i=$minh;$i<=$maxh;$i+=5) $tb_arr[$i]=$i;
		$smarty->assign("data_arr",$data_arr);
		$smarty->assign("tb_arr",$tb_arr);
		$smarty->assign("ifile","health_table_count.tpl");
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學生身高體重作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->assign("Bid_arr",$Bid_arr);
$smarty->display("health_wh.tpl");
?>
