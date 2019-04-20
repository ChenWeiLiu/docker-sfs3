<?php

// $Id: accident.php 6808 2012-06-22 08:14:46Z smallduh $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['df_item']=="") $_POST['df_item']="default_jh";
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=($_POST['sel_year'])?intval($_POST['sel_year']):intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$sub_menu_arr=array("請選擇作業項目","傷病日誌查詢","傷病學年報表","傷病學年統計圖","受傷部位統計圖","受傷地點統計圖","事故傷害統計圖","疾病症狀統計圖","處理方式統計圖","觀察時間統計圖");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);

switch ($_POST[sub_menu_id]) {
	case "1":
		$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name'],"",1);
		$health_data=new health_chart();
		if ($_POST['del']) {
			$health_data->update_accident($_POST['update']);
		}
		if ($_POST[class_name]) {
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$sel_str=array();
			$sel_str['health_accident_record']=" and student_sn in (".$health_data->sn_str.")";
			while(list($k,$v)=each($_POST)){
				if ($v) {
					switch($k){
						case "health_accident_record":
							if ($_POST[$k]['start_date']) $sel_str[$k].=" and sign_time>='".$_POST[$k]['start_date']."'";
							if ($_POST[$k]['end_date']) $sel_str[$k].=" and sign_time<='".$_POST[$k]['end_date']." 23:59:59'";
							if ($_POST[$k]['place_id']) $sel_str[$k].=" and place_id='".$_POST[$k]['place_id']."'";
							if ($_POST[$k]['reason_id']) $sel_str[$k].=" and reason_id='".$_POST[$k]['reason_id']."'";
							break;
						case "health_accident_part_record":
							foreach($v['part_id'] as $i=>$d){
								$sel_str[$k].="  or part_id='$i'";
							}
							break;
						case "health_accident_status_record":
							foreach($v['status_id'] as $i=>$d){
								$sel_str[$k].="  or status_id='$i'";
							}
							break;
						case "health_accident_attend_record":
							foreach($v['attend_id'] as $i=>$d){
								$sel_str[$k].="  or attend_id='$i'";
							}
							break;
					}
				}
			}
			$temp_str="";
			while(list($k,$v)=each($sel_str)){
				if ($v && ($run==0 || ($temp_str!=""))) {
					$v=substr($v,4);
					$srh_str=($temp_str)?"and id in ($temp_str)":"";
					$query="select distinct id from $k where ($v) $srh_str";
					$res=$CONN->Execute($query);
					$temp_id=array();
					while(!$res->EOF) {
						$temp_id[]=$res->fields['id'];
						$res->MoveNext();
					}
					if (count($temp_id)>0) 
						$temp_str="'".implode("','",$temp_id)."'";
					else
						$temp_str="''";
				}
			}
			$srh_str=($temp_str)?"where id in ($temp_str)":"";
			if ($_POST['start_search'] || $srh_str) {
				$srh_str=($temp_str)?"where id in ($temp_str)":"";
				$query="select * from health_accident_record $srh_str order by sign_time desc";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$temp_arr=array();
					$sn=$res->fields[student_sn];
					$id=$res->fields['id'];
					$temp_arr[id]=$id;
					$temp_arr[sign_time]=$res->fields['sign_time'];
					$temp_arr[obs_min]=$res->fields['obs_min'];
					$temp_arr[temp]=$res->fields['temp'];
					$temp_arr[memo]=$res->fields['memo'];
					$temp_arr[place_id]=$res->fields['place_id'];
					$temp_arr[reason_id]=$res->fields['reason_id'];
					$query="select * from stud_base where student_sn='$sn'";
					$res2=$CONN->Execute($query);
					$temp_arr[stud_name]=$res2->fields['stud_name'];
					$curr_class_num=$res2->fields['curr_class_num'];
					$temp_arr['year']=substr($curr_class_num,0,-4);
					$temp_arr['class']=substr($curr_class_num,-4,2);
					$temp_arr['num']=substr($curr_class_num,-2,2);
					$query="select * from health_accident_part_record where id='$id'";
					$res2=$CONN->Execute($query);
					while(!$res2->EOF) {
						$temp_arr[part_id][]=$res2->fields['part_id'];
						$res2->MoveNext();
					}
					$query="select * from health_accident_status_record where id='$id'";
					$res2=$CONN->Execute($query);
					while(!$res2->EOF) {
						$temp_arr[status_id][]=$res2->fields['status_id'];
						$res2->MoveNext();
					}
					$query="select * from health_accident_attend_record where id='$id'";
					$res2=$CONN->Execute($query);
					while(!$res2->EOF) {
						$temp_arr[attend_id][]=$res2->fields['attend_id'];
						$res2->MoveNext();
					}
					$rowdata[]=$temp_arr;
					$res->MoveNext();
				}
			}
			$smarty->assign("rowdata",$rowdata);
			$smarty->assign("aplace",get_accident_item(0,"health_accident_place"));
			$smarty->assign("areason",get_accident_item(0,"health_accident_reason"));
			$smarty->assign("apart",get_accident_item(0,"health_accident_part"));
			$smarty->assign("astatus",get_accident_item(0,"health_accident_status"));
			$smarty->assign("aattend",get_accident_item(0,"health_accident_attend"));
			$smarty->assign("ifile","health_accident_search.tpl");
		}
		break;
	case "2":
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
		$smarty->assign("month_arr",array(8,9,10,11,12,1,2,3,4,5,6,7));
		$smarty->assign("rowdata",$temp_arr);
		$smarty->assign("ifile","health_accident_count.tpl");
		break;
	case "3":
		$xlabel=array(8,9,10,11,12,1,2,3,4,5,6,7);
		$sdate=($sel_year+1911)."-08-01";
		$edate=($sel_year+1912)."-08-01";
		$query="select count(a.id) as n,mid(a.sign_time,6,2) as m,b.stud_sex from health_accident_record a left join stud_base b on a.student_sn=b.student_sn where a.sign_time>='$sdate' and a.sign_time<'$edate' group by m,b.stud_sex";
		$res=$CONN->Execute($query) or die($query);
		$temp_arr=array(1=>array(),2=>array(),3=>array());
		while(!$res->EOF) {
			$temp_arr[$res->fields['stud_sex']][intval($res->fields['m'])]=$res->fields['n'];
			$temp_arr[3][intval($res->fields['m'])]+=$res->fields['n'];
			$res->MoveNext();
		}
		foreach($temp_arr as $k=>$v) {
			reset($xlabel);
			foreach($xlabel as $vv) {
				$ydata[$k-1][$vv]=intval($temp_arr[$k][$vv]);
			}
		}

		//畫圖
		$sch=get_school_base();
		//session_register("ydata");
		$_SESSION["ydata"]=$ydata;
		//session_register("mtitle");
		$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度 學生傷病統計分析長條圖";
		//session_register("xtitle");
		$_SESSION["xtitle"]="月份";
		//session_register("ytitle");
		$_SESSION["ytitle"]="人數 (人)";
		//session_register("xlabel");
		$_SESSION["xlabel"]=$xlabel;
		//session_register("xclabel");
		$_SESSION["xclabel"]=1;
		//session_register("legend");
		$_SESSION["legend"]=array("男生","女生","全部");
		//session_register("graph_kind");
		if ($_POST["graph_kind"]=="") $_POST["graph_kind"]="bar";
		$_SESSION["graph_kind"]=$_POST["graph_kind"];
		$smarty->assign("ifile","health_graph_sel.tpl");
		break;
	case "4":
		$apart=get_accident_item(0,"health_accident_part");
		$xlabel=array_keys($apart);
		$sdate=($sel_year+1911)."-08-01";
		$edate=($sel_year+1912)."-08-01";
		$query="select id from health_accident_record where sign_time>='$sdate' and sign_time<'$edate'";
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
	case "5":
		$aplace=get_accident_item(0,"health_accident_place");
		$xlabel=array_keys($aplace);
		$sdate=($sel_year+1911)."-08-01";
		$edate=($sel_year+1912)."-08-01";
		$query="select count(id) as n,place_id from health_accident_record where sign_time>='$sdate' and sign_time<'$edate' group by place_id";
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
	case "6":
	case "7":
		if ($_POST[sub_menu_id]==6) {
			$mtitle="事故傷害";
			$atitle="傷害類別";
		} else {
			$mtitle="疾病症狀";
			$atitle="症狀類別";
		}
		$astatus=get_accident_item(0,"health_accident_status");
		$xlabel=array_keys($astatus);
		$sdate=($sel_year+1911)."-08-01";
		$edate=($sel_year+1912)."-08-01";
		$query="select id from health_accident_record where sign_time>='$sdate' and sign_time<'$edate'";
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
				if (($_POST[sub_menu_id]==6 && $k<10) || ($_POST[sub_menu_id]==7 && $k>9 && $k<24)) {
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
	case "8":
		$aattend=get_accident_item(0,"health_accident_attend");
		$xlabel=array_keys($aattend);
		$sdate=($sel_year+1911)."-08-01";
		$edate=($sel_year+1912)."-08-01";
		$query="select id from health_accident_record where sign_time>='$sdate' and sign_time<'$edate'";
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
	case "9":
		$xlabel=array(8,9,10,11,12,1,2,3,4,5,6,7);
		$sdate=($sel_year+1911)."-08-01";
		$edate=($sel_year+1912)."-08-01";
		$query="select sum(obs_min) as n,mid(sign_time,6,2) as m from health_accident_record where sign_time>='$sdate' and sign_time<'$edate' group by m";
		$res=$CONN->Execute($query) or die($query);
		$temp_arr=array();
		while(!$res->EOF) {
			$temp_arr[intval($res->fields['m'])]=$res->fields['n'];
			$res->MoveNext();
		}
		foreach($xlabel as $v) $ydata[0][]=intval($temp_arr[$v]);
		//畫圖
		$sch=get_school_base();
		//session_register("ydata");
		$_SESSION["ydata"]=$ydata;
		//session_register("mtitle");
		$_SESSION["mtitle"]=$sch['sch_cname'].$sel_year."學年度 學生傷病觀察時間統計分析長條圖";
		//session_register("xtitle");
		$_SESSION["xtitle"]="月份";
		//session_register("ytitle");
		$_SESSION["ytitle"]="時間 (分鐘)";
		//session_register("xlabel");
		$_SESSION["xlabel"]=$xlabel;
		//session_register("xclabel");
		$_SESSION["xclabel"]=1;
		//session_register("graph_kind");
		if ($_POST["graph_kind"]=="") $_POST["graph_kind"]="bar";
		$_SESSION["graph_kind"]=$_POST["graph_kind"];
		$smarty->assign("ifile","health_graph_sel.tpl");
		break;
}

if ($_POST[sub_menu_id]>1) {
	$year_seme_menu=year_menu($sel_year);
	$class_menu="";
}
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","傷病處理作業");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->display("health_accident.tpl");
?>