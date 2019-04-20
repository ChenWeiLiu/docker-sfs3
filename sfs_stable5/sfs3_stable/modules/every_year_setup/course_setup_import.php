<?php
//$Id: course_setup_import.php 8669 2015-12-24 06:37:40Z qfon $
if (!is_object($CONN)) header("location:course_setup3.php");

if (defined('MYSQL_CHARACTER_CODE')) 
	$mysqlCharacterCode = MYSQL_CHARACTER_CODE;
else
	$mysqlCharacterCode = 'latin1';

//建立課表暫存表
$create_course_sql="
CREATE TABLE if not exists tmp_score_course (
  class_id varchar(11) NOT NULL default '',
  class_no smallint(5) unsigned NOT NULL default '0',
  class_year tinyint(2) unsigned NOT NULL default '0',
  class_name tinyint(2) unsigned NOT NULL default '0',
  day enum('0','1','2','3','4','5','6','7') NOT NULL default '0',
  sector tinyint(1) NOT NULL default '0',
  ss_id smallint(5) unsigned NOT NULL default '0',
  os_id smallint(5) unsigned NOT NULL default '0',
  os_name varchar(255) NOT NULL default '',
  teacher_sn mediumint(9) NOT NULL default '0',
  ot_id mediumint(9) NOT NULL default '0',
  ot_name varchar(20) NOT NULL default '',
  room varchar(10) NOT NULL default '',
  or_id smallint(5) unsigned NOT NULL default '0',
  or_name varchar(255) NOT NULL default '',
  c_kind varchar(1) NOT NULL default '',
  PRIMARY KEY (class_id,day,sector)
) DEFAULT CHARSET=$mysqlCharacterCode  ;";
$CONN->Execute($create_course_sql) or trigger_error($create_course_sql);

if ($_POST['status']) $_POST="";

switch ($_POST['act']) {
	case "進行匯入作業":
		$ifile="every_year_setup_course_setup_import_sel.tpl";
		break;
	case "開始匯入檔案":
		if ($_POST['sys']=="")
			$ifile="every_year_setup_course_setup_import_sel.tpl";
		elseif ($_POST['sys']=="sing")
			$ifile="every_year_setup_course_setup_import_csv.tpl";
		elseif ($_POST['sys']=="stc")
			$ifile="every_year_setup_course_setup_import_stc.tpl";
		elseif ($_POST['sys']=="ya")
			$ifile="every_year_setup_course_setup_import_xls.tpl";
			//統計匯入班級
			$query="select count(class_id) from tmp_score_course";
			$res=$CONN->Execute($query);
			$smarty->assign("enable_class",$res->fields[0]);
		break;
	case "清除匯入資料":
		$delete_sql="drop table tmp_score_course";
		$CONN->Execute($delete_sql) or trigger_error($delete_sql);
		break;
	case "進行教師對應":
		$ifile="every_year_setup_course_setup_import_mapping_teacher.tpl";
		if ($_POST['in_sel'] && $_POST['map_sel']){
			//寫入教師對應資料
			$_POST['map_sel']=intval($_POST['map_sel']);
			$query="update tmp_score_course set teacher_sn='0' where teacher_sn='".$_POST['map_sel']."'";
			$CONN->Execute($query);
			$_POST['in_sel']=intval($_POST['in_sel']);
			$query="update tmp_score_course set teacher_sn='".$_POST['map_sel']."' where ot_id='".$_POST['in_sel']."'";
			$CONN->Execute($query);
		}elseif (count($_POST['clean_one'])>0) {
			//清除單一教師對應資料
			while(list($ot_id,$v)=each($_POST['clean_one'])) {
				$ot_id=intval($ot_id);
				$query="update tmp_score_course set teacher_sn='0' where ot_id='$ot_id'";
				$CONN->Execute($query);
			}
		}elseif ($_POST['clean_teacher']) {
			//清除所有教師對應資料
			$query="update tmp_score_course set teacher_sn='0'";
			$CONN->Execute($query);
		}elseif ($_POST['auto']) {
			//自動對應教師(先清除再對應)
			$query="update tmp_score_course set teacher_sn='0'";
			$CONN->Execute($query);
			$query="select distinct ot_id,ot_name from tmp_score_course";
			$res=$CONN->Execute($query);
			$temp_tname=array();
			while(!$res->EOF) {
				$temp_tname[$res->fields['ot_name']]=$res->fields['ot_id'];
				$res->MoveNext();
			}
			$query="select name,teacher_sn from teacher_base where teach_condition='0'";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				if ($temp_tname[$res->fields['name']]!="") $CONN->Execute("update tmp_score_course set teacher_sn='".$res->fields['teacher_sn']."' where ot_id='".$temp_tname[$res->fields['name']]."'");
				$res->MoveNext();
			}
		}

		//取出匯入教師資料
		$query="select distinct a.ot_id,a.ot_name,a.teacher_sn,b.name as teacher_name,b.sex from tmp_score_course a left join teacher_base b on a.teacher_sn=b.teacher_sn where a.ot_id<>'' order by a.ot_name,a.ot_id";
		$res=$CONN->Execute($query);
		$i=0;
		$all_sn=array();
		while(!$res->EOF) {
			if ($res->fields['teacher_sn']!=0) $all_sn[]=$res->fields['teacher_sn'];
			if (!($res->fields['teacher_sn']!=0 && $_POST['hide'])) {
				$t_data[$i]=$res->FetchRow();
				$i++;
			} else {
				$res->MoveNext();
			}
		}
		$smarty->assign("t_data",$t_data);
		$sn_str=(count($all_sn)>0)?"and a.teacher_sn not in ('".implode("','",$all_sn)."')":"";

		//取出現有教師資料
		$query="select a.*,b.* from teacher_base a left join teacher_post b on a.teacher_sn=b.teacher_sn where teach_condition='0' $sn_str order by a.name,a.teacher_sn";
		$res=$CONN->Execute($query);
		$i=0;
		while(!$res->EOF) {
			$tb_data[$i]=$res->FetchRow();
			$i++;
		}
		$smarty->assign("tb_data",$tb_data);

		//取出教師職稱
		$query="select * from teacher_title where enable='1'";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$tt_data[$res->fields['teach_title_id']]=$res->fields['title_name'];
			$res->MoveNext();
		}
		$smarty->assign("tt_data",$tt_data);

		//計算未對應教師數
		$query="select distinct ot_id from tmp_score_course where teacher_sn='0' and ot_id<>'0'";
		$res=$CONN->Execute($query);
		$smarty->assign("unmappings",$res->RecordCount());
		$query="select distinct ot_id from tmp_score_course where teacher_sn<>'0' and ot_id<>'0'";
		$res=$CONN->Execute($query);
		$smarty->assign("mappings",$res->RecordCount());
		break;
	case "進行課程對應":
		if ($_POST['c_year']=="") $_POST['c_year']="7";
		$ifile="every_year_setup_course_setup_import_mapping_course.tpl";
		if ($_POST['clean']) {
			//清除所有課程對應資料
			$query="update tmp_score_course set ss_id='0'";
			$CONN->Execute($query);
		} elseif (count($_POST['clean_os_id'])>0) {
			//清除匯入對應課程
			while(list($os_id,$v)=each($_POST['clean_os_id'])) {
				$os_id=intval($os_id);
				$query="update tmp_score_course set ss_id='0' where os_id='$os_id'";
				$CONN->Execute($query);
			}
		} elseif (count($_POST['clean_ss_id'])>0) {
			//清除系統對應課程
			while(list($ss_id,$v)=each($_POST['clean_ss_id'])) {
				$ss_id=intval($ss_id);
				$query="update tmp_score_course set ss_id='0' where ss_id='$ss_id'";
				$CONN->Execute($query);
			}
		} elseif ($_POST['in_sel'] && $_POST['map_sel']){
			//寫入對應課程
			$_POST['map_sel']=intval($_POST['map_sel']);
			$query="select class_id from score_ss where ss_id='".$_POST['map_sel']."'";
			$res=$CONN->Execute($query);
			if ($res->fields['class_id']=="") {
				//如果是年級課程, 則排除有班級課程之班級
				$_POST['c_year']=intval($_POST['c_year']);
				$query="select distinct class_id from score_ss where enable='1' and year='$sel_year' and semester='$sel_seme' and class_year='".$_POST['c_year']."' and class_id<>''";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$class_id_arr[]=$res->fields['class_id'];
					$res->MoveNext();
				}
				$class_id_str=(count($class_id_arr)==0)?"":"and class_id not in ('".implode("','",$class_id_arr)."')";
			} else {
				//否則只寫入該班級
				$class_id_str="and class_id='".$res->fields['class_id']."'";
			}
			//寫入對應班級之課程
			$_POST['map_sel']=intval($_POST['map_sel']);
			$_POST['in_sel']=intval($_POST['in_sel']);
			$_POST['c_year']=intval($_POST['c_year']);
			$query="update tmp_score_course set ss_id='".$_POST['map_sel']."' where os_id='".$_POST['in_sel']."' and class_year='".$_POST['c_year']."' $class_id_str";
			$res=$CONN->Execute($query);
		}
		//取出匯入課程
		$_POST['c_year']=intval($_POST['c_year']);
		$query="select distinct os_id,os_name from tmp_score_course where class_year='".$_POST['c_year']."' and os_name!='' order by os_id";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$s_data[$res->fields['os_id']]=$res->fields['os_name'];
			$res->MoveNext();
		}
		$smarty->assign("s_data",$s_data);
		//取出系統課程
		$query="select * from score_ss where enable='1' and year='$sel_year' and semester='$sel_seme' and class_year='".$_POST['c_year']."' order by class_id,sort,sub_sort";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$ss_data[$res->fields['ss_id']]['scope_id']=$res->fields['scope_id'];
			$ss_data[$res->fields['ss_id']]['subject_id']=$res->fields['subject_id'];
			$ss_data[$res->fields['ss_id']]['class_year']=$res->fields['class_year'];
			$ss_data[$res->fields['ss_id']]['class_id']=$res->fields['class_id'];
			$res->MoveNext();
		}
		$smarty->assign("ss_data",$ss_data);
		//取出所有科目名稱
		$query="select * from score_subject order by subject_id";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sb_data[$res->fields['subject_id']]=$res->fields['subject_name'];
			$res->MoveNext();
		}
		$smarty->assign("sb_data",$sb_data);
		//統計匯入課程
		$query="select count(os_id),os_id from tmp_score_course where class_year='".$_POST['c_year']."' group by os_id";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$so_data[$res->fields['os_id']]=$res->fields[0];
			$res->MoveNext();
		}
		$smarty->assign("so_data",$so_data);
		//統計對應課程
		$query="select count(ss_id),ss_id from tmp_score_course where class_year='".$_POST['c_year']."' group by ss_id";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sm_data[$res->fields['ss_id']]=$res->fields[0];
			$res->MoveNext();
		}
		$smarty->assign("sm_data",$sm_data);
		//統計未對應數
		$query="select distinct os_id from tmp_score_course where ss_id='0' and class_year='".$_POST['c_year']."'";
		$res=$CONN->Execute($query);
		$unmappings['subject']=$res->RecordCount();
		while(!$res->EOF) {
			$unmappings['os_id'][$res->fields['os_id']]=1;
			$res->MoveNext();
		}
		$query="select count(os_id) from tmp_score_course where ss_id='0'";
		$res=$CONN->Execute($query);
		$unmappings['sector']=$res->fields[0];
		$smarty->assign("unmappings",$unmappings);
		//統計各年級節次
		$query="select class_year,count(os_id) from tmp_score_course group by class_year";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$snum[$res->fields['class_year']]=$res->fields[1];
			$res->MoveNext();
		}
		$smarty->assign("snum",$snum);
		$smarty->assign("class_year",$class_year);
		break;
	case "寫入課表設定":
		//產生判斷班級課程的陣列
		$query="select distinct class_id from score_ss where year='$sel_year' and semester='$sel_seme' and enable='1' and class_id<>''";
		$res=$CONN->Execute($query);
		$class_id_arr=array();
		while(!$res->EOF) {
			$class_id_arr[$res->fields['class_id']]=1;
			$res->MoveNext();
		}
		//產生判斷正確課程的陣列
		$query="select * from score_ss where year='$sel_year' and semester='$sel_seme' and enable='1'";
		$res=$CONN->Execute($query);
		$ss_id_arr=array();
		while(!$res->EOF) {
			$id=($res->fields['class_id'])?$res->fields['class_id']:$res->fields['class_year'];
			$ss_id_arr[$id][]=$res->fields['ss_id'];
			$res->MoveNext();
		}
		$tmp_class_id=sprintf("%03d_%d",$sel_year,$sel_seme);
		$query="select * from tmp_score_course where class_id like '".$tmp_class_id."_%'order by class_id,day,sector";
		$res=$CONN->Execute($query);
		$err_msg="";
		while(!$res->EOF) {
			if ($class_id_arr[$res->fields['class_id']]) {
				$id=$res->fields['class_id'];
			} else {
				$id=$res->fields['class_year'];
			}
			if (in_array($res->fields['ss_id'],$ss_id_arr[$id])) {
				$query2="select course_id from score_course where year='$sel_year' and semester='$sel_seme' and class_year='".$res->fields['class_year']."' and class_name='".$res->fields['class_name']."' and day='".$res->fields['day']."' and sector='".$res->fields['sector']."'";
				$res2=$CONN->Execute($query2);
				$course_id=$res2->fields[0];
				if ($course_id)
					$query="update score_course set teacher_sn='".$res->fields['teacher_sn']."',ss_id='".$res->fields['ss_id']."',c_kind='".$res->fields['c_kind']."' where course_id='$course_id'";
				else
					$query="insert into score_course (year,semester,class_id,teacher_sn,class_year,class_name,day,sector,ss_id,c_kind) values ('$sel_year','$sel_seme','".$res->fields['class_id']."','".$res->fields['teacher_sn']."','".$res->fields['class_year']."','".$res->fields['class_name']."','".$res->fields['day']."','".$res->fields['sector']."','".$res->fields['ss_id']."','".$res->fields['c_kind']."')";
				if (!$CONN->Execute($query)) $err_msg.=$res->fields['class_year']."年".$res->fields['class_name']."班(週".$res->fields['day'].")-第".$res->fields['sector']."節課匯入錯誤<br>";
			} else {
				$err_msg.=$res->fields['class_year']."年".$res->fields['class_name']."班(週".$res->fields['day'].")-第".$res->fields['sector']."節並無代碼為".$res->fields['ss_id']."的科目<br>";
			}
			$res->MoveNext();
		}
		break;
}

if ($_POST['upload']) {
	//上傳檔案
	$path_str = "temp/course/";
	set_upload_path($path_str);
	$temp_path = $UPLOAD_PATH.$path_str;
	$file_name=strtoupper($_FILES['upload_file']['name']);
	$n=explode(".",$file_name);
	$lastname=strtoupper(array_pop($n));
	$stc_arr=array("ClassNum","ClassTab","CoursNam","ClassCur","TeachNam");
	if (($_FILES['upload_file']['size'] >0 && $file_name != "" && ($lastname == "CSV" || $lastname == "XLS")) || in_array($_FILES['upload_file']['name'],$stc_arr)){
		copy($_FILES['upload_file']['tmp_name'],$temp_path.$_FILES['upload_file']['name']);
		$sel_file=$_FILES['upload_file']['name'];
		$file_name=$temp_path.$sel_file;
		$fp=fopen($file_name,"r");
		if ($_POST['file_name']=="ClassNum") {
			//匯入STC班級設定檔
			$i=1;
			while($tt=fgets($fp,2000)) {
				if (substr($tt,0,1)==" " && substr($tt,1)!=" ") {
					$class_arr[$i]=intval(substr($tt,1));
					$i++;
				}
			}
			if ($class_arr[1]>0 && !$_POST['force7']) {
				$j=$IS_JHORES;
				$enable=1;
			} elseif ($class_arr[7]>0) {
				$j=0;
				$enable=1;
			}
			if ($enable==1) {
				while(list($year_name,$class_num)=each($class_arr)) {
					if ($class_num>0) {
						for($class_name=1;$class_name<=$class_num;$class_name++) {
							for($day=1;$day<=5;$day++) {
								for($sector=1;$sector<=7;$sector++) {
									$c_year=$year_name+$j;
									$class_no=($c_year-$IS_JHORES).sprintf("%02d",$class_name);
									$class_id=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),$c_year,$class_name);
									$query="insert into tmp_score_course (class_id,class_no,class_year,class_name,day,sector) values ('$class_id','$class_no','$c_year','$class_name','$day','$sector')";
									$CONN->Execute($query);
								}
							}
						}
					}
				}
			}
		} elseif ($_POST['file_name']=="ClassTab") {
			//匯入STC課表設定檔
			//取出班級
			$query="select distinct class_id from tmp_score_course order by class_id";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$class_id_arr[]=$res->fields['class_id'];
				$res->MoveNext();
			}
			//匯入課表
			$c=0;
			while($tt=fgets($fp,2000)) {
				if ($class_id_arr[$c]=="") break;
				for($day=1;$day<=5;$day++) {
					for($sector=1;$sector<=7;$sector++) {
						$i=(($day-1)*8+$sector-1)*2;
						$query="update tmp_score_course set os_id='".(ord(substr($tt,$i,1))-48)."',c_kind='".substr($tt,$i+1,1)."' where day='$day' and sector='$sector' and class_id='".$class_id_arr[$c]."'";
						$CONN->Execute($query);
					}
				}
				$c++;
			}
		} elseif ($_POST['file_name']=="CoursNam") {
			//匯入STC科目名稱檔
			$i=1;
			while($tt=fgets($fp,2000)) {
				$query="update tmp_score_course set os_name='".addslashes(fgets($fp,2000))."' where os_id='$i'";
				$CONN->Execute($query);
				$i++;
			}
		} elseif ($_POST['file_name']=="ClassCur") {
			//匯入STC班級配課檔
			//取出班級
			$query="select distinct class_id from tmp_score_course order by class_id";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$class_id_arr[]=$res->fields['class_id'];
				$res->MoveNext();
			}
			//匯入配課教師代號
			$c=0;
			while($tt=fgets($fp,2000)) {
				$cc=fgets($fp,2000);
				if ($class_id_arr[$c]=="") break;
				for($i=0;$i<strlen($tt);$i++) {
					$ot_id=(ord(substr($cc,$i*2,1))-48)*50+ord(substr($cc,$i*2+1,1))-48;
					$query="update tmp_score_course set ot_id='$ot_id' where class_id='".$class_id_arr[$c]."' and os_id='".($i+1)."'";
					$CONN->Execute($query);
				}
				$c++;
			}
		} elseif ($_POST['file_name']=="TeachNam") {
			//匯入STC教師姓名檔
			$i=1;
			while($tt=fgets($fp,2000)) {
				$query="update tmp_score_course set ot_name='".addslashes(trim($tt))."' where ot_id='$i'";
				$CONN->Execute($query);
				$i++;
			}
		} elseif ($lastname == "CSV") {
			while($tt=sfs_fgetcsv($fp,2000,",")) {
				if (in_array("班級",$tt)) {
					chk_data($tt);
				} else {
					$c_year=intval(substr($tt[$vs[0]],0,1));
					if ($c_year<$IS_JHORES) $c_year+=$IS_JHORES;
					$class_name=intval(substr($tt[$vs[0]],-2,2));
					$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$c_year,$class_name);
					//寫入匯入課表
					$query="replace into tmp_score_course (class_id,class_no,class_year,class_name,day,sector,os_id,os_name,ot_id,ot_name,or_id,or_name) values ('$class_id','".$tt[$vs[0]]."','$c_year','$class_name','".$tt[$vs[1]]."','".$tt[$vs[2]]."','".$tt[$vs[3]]."','".addslashes($tt[$vs[4]])."','".$tt[$vs[5]]."','".addslashes($tt[$vs[6]])."','".$tt[$vs[7]]."','".addslashes($tt[$vs[8]])."')";
					$CONN->Execute($query);
				}
			}
		} else {
			require_once $SPREADSHEET_PATH."Excel/Reader.php";
			$xls = new Spreadsheet_Excel_Reader();
			$xls->setOutputEncoding('BIG5');
			$xls->read($temp_path.$_FILES['upload_file']['name']);
			if (in_array("班級",$xls->sheets[0]['cells'][1])) chk_data($xls->sheets[0]['cells'][1]);
			for ($i=2; $i<=$xls->sheets[0]['numRows']; $i++) {
				$c_year=intval(substr($xls->sheets[0]['cells'][$i][$vs[0]],0,1));
				if ($c_year<$IS_JHORES) $c_year+=$IS_JHORES;
				$class_name=intval(substr($xls->sheets[0]['cells'][$i][$vs[0]],-2,2));
				$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$c_year,$class_name);
				if ($xls->sheets[0]['cells'][$i][$vs[5]]) {
					for ($j=11; $j<=17; $j++) {
						if ($xls->sheets[0]['cells'][$i][$j]) {
							$query="replace into tmp_score_course (class_id,class_no,class_year,class_name,day,sector,os_id,os_name,ot_id,ot_name,or_id,or_name) values ('$class_id','".$xls->sheets[0]['cells'][$i][$vs[0]]."','$c_year','$class_name','".$xls->sheets[0]['cells'][$i][$vs[1]]."','".($j-10)."','".$xls->sheets[0]['cells'][$i][$vs[3]]."','".addslashes($xls->sheets[0]['cells'][$i][$vs[4]])."','".$xls->sheets[0]['cells'][$i][$vs[5]]."','".addslashes($xls->sheets[0]['cells'][$i][$vs[6]])."','".$xls->sheets[0]['cells'][$i][$vs[7]]."','".addslashes($xls->sheets[0]['cells'][$i][$vs[8]])."')";
							//echo $query."<br>";
						}
					}
				}
			}
		}
	}
}

if ($ifile=="") {
	//統計對應狀況
	$query="select distinct class_no from tmp_score_course";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[c][0]=$res->RecordCount();
	$query="select distinct class_no from tmp_score_course where class_name<>'0'";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[c][1]=$res->RecordCount();
	$query="select distinct class_no from tmp_score_course where class_name='0'";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[c][2]=$res->RecordCount();
	$query="select distinct ot_id from tmp_score_course where ot_id<>'0'";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[t][0]=$res->RecordCount();
	$query="select distinct ot_id from tmp_score_course where teacher_sn<>'0' and ot_id<>'0'";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[t][1]=$res->RecordCount();
	$query="select distinct ot_id from tmp_score_course where teacher_sn='0' and ot_id<>'0'";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[t][2]=$res->RecordCount();
	$query="select distinct concat(class_id,os_id) from tmp_score_course";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[s][0]=$res->RecordCount();
	$query="select distinct concat(class_id,os_id) from tmp_score_course where ss_id<>'0'";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[s][1]=$res->RecordCount();
	$query="select distinct concat(class_id,os_id) from tmp_score_course where ss_id='0'";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[s][2]=$res->RecordCount();
	$query="select count(os_id) from tmp_score_course";
	$res=$CONN->Execute($query);
	if (is_object($res)) $data[ss][0]=$res->fields[0];
	$query="select count(os_id) from tmp_score_course where ss_id<>'0' and teacher_sn <>'0'";
	$res=$CONN->Execute($query);
	if (is_object($res)) {
		$data[ss][1]=$res->fields[0];
		$data[ss][2]=$data[ss][0]-$data[ss][1];
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","匯入課表"); 
$smarty->assign("SFS_MENU",$school_menu_p); 
$smarty->assign("year_seme_menu", class_ok_setup_year($sel_year,$sel_seme,"year_seme") ) ;
$smarty->assign("sel_year",$sel_year);
$smarty->assign("sel_seme",$sel_seme);
$smarty->assign("step",$work_step);
$smarty->assign("data",$data);
$smarty->assign("ifile",$ifile);
$smarty->display('every_year_setup_course_setup_import.tpl'); 

function chk_data($kk) {
	global $vs;
	
	reset($kk);
	while (list($k,$v)=each($kk)) {
		switch ($v) {
			case "class_no":
				$vs[0]=$k;
				break;
			case "星期":
				$vs[1]=$k;
				break;
			case "節次":
				$vs[2]=$k;
				break;
			case "科目":
				$vs[3]=$k;
				break;
			case "科目名稱":
				$vs[4]=$k;
				break;
			case "教師":
				$vs[5]=$k;
				break;
			case "教師名稱":
				$vs[6]=$k;
				break;
			case "教室":
				$vs[7]=$k;
				break;
			case "教室名稱":
				$vs[8]=$k;
				break;
			case "班級":
				if ($vs[0]=="") $vs[0]=$k;
				break;
		}
		$vv=intval(substr($v,-1,1));
		if (substr($v,0,2)=="節" && $vv>0) $vs[10+$vv]=10+$vv;
	}
}
?>
