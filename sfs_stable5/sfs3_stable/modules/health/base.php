<?php

// $Id: base.php 7707 2013-10-23 12:13:23Z smallduh $

// 取得設定檔
include "config.php";

sfs_check();
//print_r($_POST);
if ($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
if ($_POST['sel_year']) $sel_year=intval($_POST['sel_year']);
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;

if ($_POST['act']=="xml" && $_POST['id'] && $_POST['acc'] && $_POST['pass']) {
	$_POST['acc']=str_replace("'", "", trim($_POST['acc']));
	$pass=md5($_POST['pass']);
	if ($_POST['acc'] && $_POST['pass']) {
		$host="163.17.40.13";
		$url="/checkmyid.php";
		$fp=fsockopen($host, 80, $errno, $errstr, 10);
		$str="id=".$_POST['acc']."&pass=".$pass;
		fputs($fp, "POST $url HTTP/1.1\r\nHost: $host\r\nUser-Agent: Mozilla/4.0 (compatibal; MSIE 6.0; windows NT 5.1)\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-length: ".strlen($str)."\r\n\r\n$str");
		while(!feof($fp)){
			$m=fgets($fp,1024);
			$m_arr=explode("<url>",$m);
			if (count($m_arr)>1) {
				$up_url=$m_arr[1];
			}
			$m_arr=array();
			$m_arr=explode("<msg>",$m);
			if (count($m_arr)>1) {
				$err_msg=$m_arr[1];
			}
		}
		fclose($fp);

		if ($up_url!="") {
			$id=str_replace("'", "", trim($_POST['id']));
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$id);
			$health_data->get_all();
			$seme_class=$id;
			$dk_arr=hDiseaseKind();
			$sdk_arr=hSeriousDiseaseKind();
			$bk_arr=hBodyMindKind();
			$bl_arr=hBodyMindLevel();
			$checks_item_arr=get_checks_item();
			$in_arr=get_insurance();
			$exp_arr=array("worm"=>"寄生蟲","uri"=>"尿液");
			$exp_item_arr=array("pro"=>"尿蛋白","glu"=>"尿糖","bld"=>"潛血","ph"=>"酸鹼度");
			$hstatus_arr=array("1"=>"正常","2"=>"異常");
			$checks_diag_arr=array("0"=>"無異狀","1"=>"異常","2"=>"複檢無異狀或不需要治療","3"=>"已痊癒","4"=>"矯治中");
			$checks_part_arr=array("Oph"=>"眼","Ent"=>"耳鼻喉","Hea"=>"頭頸","Pul"=>"胸","Dig"=>"腹","Spi"=>"脊柱四肢","Uro"=>"泌尿生殖","Der"=>"皮膚","Ora"=>"口腔");
			$tee_chk_arr=array("0"=>"無異狀","1"=>"齲齒","2"=>"缺牙","3"=>"已矯治","4"=>"待拔牙","5"=>"阻生牙","6"=>"贅生牙");
			include "2xml.php";
			$s=get_school_base();
			$sch_id=$s[sch_id];
			$health=$xml;
			$data["health"]=encrypt(make_key($_POST['pass']),$health);
			$data["school"]=$sch_id;
			$data["user"]=$_POST['acc'];
			$data["class"]=$id;
			$data["md5"]=md5($_POST['pass']);
			$_SESSION['up_url']="/healthdata/collect.php";
			$msg=postdata($host,$_SESSION['up_url'],$data);
			$st_arr=explode(" ",microtime());
			srand($st_arr[1].$st_arr[0]);
			$color_arr=array("red","pink","green","orange","grey","purple");
			echo "<span style='color:blue;'>".$id."</span> -- <span style='color:".$color_arr[rand(0,5)].";'>success</span> !";
		} else {
			echo "<span style='color:red;'>".$err_msg."</span>";
		}
	} else {
		echo "<span style='color:red;'>Account or password empty !</span>";
	}
	exit;
}

$sub_menu_arr=array("請選擇作業項目","緊急連絡名冊","郵寄名條","萬豐Web版健康資料匯入","萬豐視窗版健康資料匯入","世醫版健康資料匯入","教育部上傳資料匯出","上傳健康資料","健檢資料匯出","預防接種資料匯出","匯入健檢資料");
$sub_menu=sub_menu($sub_menu_arr,$_POST['sub_menu_id']);
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_menu=class_menu($sel_year,$sel_seme,$_POST['class_name']);
//判別學生實際在籍學期
$myear=($IS_JHORES==0)?6:3;

switch ($_POST['sub_menu_id']) {
	case "1":
		$rowtext=array("班級","座號","學號","姓名","性別","身份證字號","出生日期","連絡地址","連絡電話","連絡人");
		if ($_POST['ods_all']) {
			require_once "../../include/sfs_case_ooo.php";
			$x=new sfs_ooo();
			$temp_class[]=substr($_POST['class_name'],0,1);
			$temp_arr=class_base(sprintf("%03d",$sel_year).$sel_seme,$temp_class);
			$x->setRowText($rowtext);
			while(list($k,$v)=each($temp_arr)) {
				$x->addSheet($k);
				$health_data=new health_chart();
				$health_data->get_stud_base($sel_year,$sel_seme,$k);
				$x->items=get_stud_list($health_data,$_POST['year_seme']);
				$x->writeSheet();
			}
			$x->process();
			exit;
		}
		if ($_POST['xls_all']) {
			require_once "../../include/sfs_case_excel.php";
			$x=new sfs_xls();
			$x->setUTF8();
			$x->setBorderStyle(1);
			$temp_class[]=substr($_POST['class_name'],0,1);
			$temp_arr=class_base(sprintf("%03d",$sel_year).$sel_seme,$temp_class);
			while(list($k,$v)=each($temp_arr)) {
				$x->addSheet($k);
				$health_data=new health_chart();
				$health_data->get_stud_base($sel_year,$sel_seme,$k);
				$x->items=get_stud_list($health_data,$_POST['year_seme']);
				$x->writeSheet();
			}
			$x->process();
			exit;
		}
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$smarty->assign("ifile","health_base_stud_list.tpl");
			$smarty->assign("health_data",$health_data);
			if ($_POST['csv']) {
				header("Content-disposition: attachment; filename=".$_POST['class_name']."緊急連絡名冊.csv");
				header("Content-type: text/x-csv ; Charset=Big5");
				//header("Pragma: no-cache");
								//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

				header("Expires: 0");
				$smarty->display("health_stud_list_csv.tpl");
				exit;
			}
			if ($_POST['xls']) {
				require_once "../../include/sfs_case_excel.php";
				$x=new sfs_xls();
				$x->setUTF8();
				$x->setBorderStyle(1);
				$x->addSheet($_POST['class_name']);
				$x->setRowText($rowtext);
				$x->items=get_stud_list($health_data,$_POST['year_seme']);
				$x->writeSheet();
				$x->process();
				exit;
			}
			if ($_POST['ods']) {
				require_once "../../include/sfs_case_ooo.php";
				$x=new sfs_ooo();
				$x->addSheet($_POST['class_name']);
				$x->setRowText($rowtext);
				$x->items=get_stud_list($health_data,$_POST['year_seme']);
				$x->writeSheet();
				$x->process();
				exit;
			}
		}
		break;
	case "2":
		if ($_POST['class_name']) {
			$health_data=new health_chart();
			if ($_POST['print'] && count($_POST['student_sn']>0)) {
				foreach($_POST['student_sn'] as $s) $sn[]=$s;
				$health_data->set_stud($sn,$sel_year,$sel_seme);
				$smarty->assign("health_data",$health_data);
				$smarty->assign("school_data",get_school_base());
				$smarty->assign("year_data",year_base($sel_year,$sel_seme));
				$smarty->assign("class_data",class_name($sel_year,$sel_seme));
				$smarty->display("PostList.tpl");
				exit;
			}
			$health_data->get_stud_base($sel_year,$sel_seme,$_POST['class_name']);
			$smarty->assign("health_data",$health_data);
			$smarty->assign("ifile","health_base_post.tpl");
		}
		break;
	case "3":
		$file_kind_arr=array(""=>"請選擇檔案類別","health_wh"=>"身高體重資料檔","health_sight"=>"視力檢查資料檔","health_teeth"=>"牙齒檢查資料檔","health_accident"=>"學生傷病資料檔","health_inject"=>"預防接種");
		$year_seme_menu="";
		$class_menu="";
		$_POST['class_name']=" ";
		$cyear=($IS_JHORES)?9:6;
		//處理檔案上傳動作
		if ($_POST['doup_key']) {
			set_upload_path($path_str);
			$tmp_path=check_upload_file($_FILES['upload_file'],array("CSV"));
			if (is_uploaded_file($_FILES['upload_file']['tmp_name']) && !$_FILES['upload_file']['error'] && $_FILES['upload_file']['size'] >0 && $tmp_path!=""){
				move_uploaded_file($tmp_path.basename($_FILES['upload_file']['tmp_name']),$temp_path.strtoupper($_FILES['upload_file']['name']));
			}
		}
		$chk_arr=array_keys($file_kind_arr);
		if ($_POST['file_name'] && !$_POST['fkind']) $_POST['file_name']="";
		if ($_POST['fkind'] && in_array($_POST['fkind'],$chk_arr) && $_POST['file_name']) {
			$file_name=$temp_path.basename($_POST['file_name']);
			$fp=fopen($file_name,"r");
			switch($_POST['fkind']) {
				case "health_wh":
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							if ($tt[1]>$IS_JHORES && $tt[1]<=$cyear) {
								$query="select * from stud_base where stud_person_id='".$tt[0]."'";
								$res=$CONN->Execute($query);
								$student_sn=$res->fields['student_sn'];
								if ($student_sn && $tt[3] && $tt[4]) {
									$tt[1]+=$res->fields['stud_study_year']-1-$IS_JHORES;
									$tt[5]=(intval($tt[1])+1911+(($tt[2]==2)?1:0)).(($tt[2]==2)?"-09-20":"-02-20");
									$query="replace into health_WH (year,semester,student_sn,weight,height,measure_date,update_date,teacher_sn) values ('$tt[1]','$tt[2]','$student_sn','$tt[3]','$tt[4]','$tt[5]','".$today."','".$_SESSION['session_tea_sn']."')";
									$res=$CONN->Execute($query);
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							if ($tt[1]>$IS_JHORES && $tt[1]<=$cyear) {
								$query="select * from stud_base where stud_person_id='".$tt[0]."'";
								$res=$CONN->Execute($query);
								$student_sn=$res->fields['student_sn'];
								if ($student_sn && $tt[3] && $tt[4]) break;
							}
						}
						$tt[1]+=$res->fields['stud_study_year']-1-$IS_JHORES;
						$tt[5]=(intval($tt[1])+1911+(($tt[2]==1)?0:1)).(($tt[2]==1)?"-09-20":"-02-20");
						$smarty->assign("stud_id",$res->fields['stud_id']);
						$smarty->assign("stud_name",$res->fields['stud_name']);
						$smarty->assign("rowdata",$tt);
					}
					break;
				case "health_sight":
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							if ($tt[1]>$IS_JHORES && $tt[1]<=$cyear) {
								$query="select * from stud_base where stud_person_id='".$tt[0]."'";
								$res=$CONN->Execute($query);
								$student_sn=$res->fields['student_sn'];
								if ($student_sn) {
									$tt[1]+=$res->fields['stud_study_year']-1-$IS_JHORES;
									$tt[15]=(intval($tt[1])+1911+(($tt[2]==1)?0:1)).(($tt[2]==1)?"-09-20":"-02-20");
									for($i=3;$i<=6;$i++) {
										if ($tt[$i]>0) $tt[$i]=sprintf("%1.1f",$tt[$i]/10);
									}
									if ($tt[4]) {
										$query="replace into health_sight (year,semester,student_sn,side,sight_o,sight_r,measure_date,update_date,teacher_sn) values ('$tt[1]','$tt[2]','$student_sn','r','$tt[4]','$tt[6]','$tt[15]','".$today."','".$_SESSION['session_tea_sn']."')";
										$res=$CONN->Execute($query);
									}
									if ($tt[3]) {
										$query="replace into health_sight (year,semester,student_sn,side,sight_o,sight_r,measure_date,update_date,teacher_sn) values ('$tt[1]','$tt[2]','$student_sn','l','$tt[3]','$tt[5]','$tt[15]','".$today."','".$_SESSION['session_tea_sn']."')";
										$res=$CONN->Execute($query);
									}
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							if ($tt[1]>$IS_JHORES && $tt[1]<=$cyear) {
								$query="select * from stud_base where stud_person_id='".$tt[0]."'";
								$res=$CONN->Execute($query);
								$student_sn=$res->fields['student_sn'];
								if ($student_sn) break;
							}
						}
						$tt[1]+=$res->fields['stud_study_year']-1-$IS_JHORES;
						$tt[15]=(intval($tt[1])+1911+(($tt[2]==1)?0:1)).(($tt[2]==1)?"-09-20":"-02-20");
						for($i=3;$i<=6;$i++) {
							if ($tt[$i]>0) $tt[$i]=sprintf("%1.1f",$tt[$i]/10);
						}
						$smarty->assign("stud_id",$res->fields['stud_id']);
						$smarty->assign("stud_name",$res->fields['stud_name']);
						$smarty->assign("rowdata",$tt);
					}
					break;
				case "health_teeth":
					$sym_arr=array("D"=>"1","M"=>"2","F"=>"3","T"=>"4","d"=>"1","m"=>"2","f"=>"3","e"=>"4");
					$ttt_arr=array("4"=>"7","5"=>"8","6"=>"1","7"=>"2","8"=>"3","9"=>"4","10"=>"5","15"=>"6");
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							if ($tt[1]>$IS_JHORES && $tt[1]<=$cyear) {
								$query="select * from stud_base where stud_person_id='".$tt[0]."'";
								$res=$CONN->Execute($query);
								$student_sn=$res->fields['student_sn'];
								if ($student_sn) {
									$y=$res->fields['stud_study_year']+$tt[2]-$IS_JHORES-1;
									if (strlen($tt[13])>0) {
										for($i=0;$i<strlen($tt[13]);$i+=3) {
											$no=substr($tt[13],$i*3,2);
											$s=substr($tt[13],$i*3+2,1);
											$query="replace into health_teeth (year,semester,student_sn,no,status,update_date,teacher_sn) values ('$y','".$tt[2]."','$student_sn','T".$no."','".$sym_arr[$s]."','".$today."','".$_SESSION['session_tea_sn']."')";
											$res=$CONN->Execute($query);
										}
									}
									$dis=0;
									while(list($k,$v)=each($ttt_arr)) {
										if ($tt[$k]) {
											$query="replace into health_checks_record (year,semester,student_sn,subject,no,status,update_date,teacher_sn) values ('".$tt[1]."','".$tt[2]."','$student_sn','Ora','$v','1','".$today."','".$_SESSION['session_tea_sn']."')";
											$res=$CONN->Execute($query);
											$dis++;
										}
									}
									if ($dis==0) {
										$query="replace into health_checks_record (year,semester,student_sn,subject,no,status) values ('".$tt[1]."','".$tt[2]."','$student_sn','Ora','0','1')";
										$res=$CONN->Execute($query);
									}
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							if ($tt[0]=="PID" && $tt[1]=="GradeID" && $tt[2]=="Sem" && $tt[3]=="Tee" && $tt[4]=="T01") $ok=1;
							if ($tt[1]>$IS_JHORES && $tt[1]<=$cyear) {
								$query="select * from stud_base where stud_person_id='".$tt[0]."'";
								$res=$CONN->Execute($query);
								$student_sn=$res->fields['student_sn'];
								if ($student_sn) break;
							}
						}
						if ($student_sn) {
							$smarty->assign("stud_id",$res->fields['stud_id']);
							$smarty->assign("stud_name",$res->fields['stud_name']);
							$smarty->assign("ok",$ok);
							$smarty->assign("rowdata",$tt);
						}
					}
					break;
				case "health_inject":
					if ($_POST['sure']) {
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							if ($tt[0]=="PID" && $tt[1]=="InjectID" && $tt[2]=="Pred") $ok=1;
							$query="select * from stud_base where stud_person_id='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) break;
						}
						if ($student_sn) {
							$smarty->assign("stud_id",$res->fields['stud_id']);
							$smarty->assign("stud_name",$res->fields['stud_name']);
							$smarty->assign("ok",$ok);
							$smarty->assign("rowdata",$tt);
						}
					}
					break;
			}
			fclose($fp);
		}
		$smarty->assign("file_kind_arr",$file_kind_arr);
		$smarty->assign("file_menu",file_menu($temp_path,$_POST['file_name'],"file_name","","CSV"));
		$smarty->assign("ifile","health_import_WEB.tpl");
		break;
	case "4":
		$file_kind_arr=array(""=>"請選擇檔案類別","health_wh"=>"身高體重資料檔(hWH)","health_sight"=>"視力檢查資料檔(hSight)","health_teeth"=>"牙齒檢查資料檔(hTeeSem)","health_accident"=>"學生傷病資料檔(hAccident)");
		$year_seme_menu="";
		$class_menu="";
		$_POST['class_name']=" ";
		//處理檔案上傳動作
		if ($_POST['doup_key']) {
			set_upload_path($path_str);
			$tmp_path=check_upload_file($_FILES['upload_file'],array("CSV"));
			if (is_uploaded_file($_FILES['upload_file']['tmp_name']) && !$_FILES['upload_file']['error'] && $_FILES['upload_file']['size'] >0 && $tmp_path!=""){
				move_uploaded_file($tmp_path.basename($_FILES['upload_file']['tmp_name']),$temp_path.strtoupper($_FILES['upload_file']['name']));
			}
		}
		$chk_arr=array_keys($file_kind_arr);
		if ($_POST['file_name'] && !$_POST['fkind']) $_POST['file_name']="";
		if ($_POST['fkind'] && in_array($_POST['fkind'],$chk_arr) && $_POST['file_name']) {
			$file_name=$temp_path.basename($_POST['file_name']);
			$fp=fopen($file_name,"r");
			switch($_POST['fkind']) {
				case "health_wh":
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$ttid=(intval($tt[0])>0)?$tt[0]:trim(substr($tt[0],1,strlen($tt[0])-1));
							$query="select student_sn from stud_base where stud_id='".$ttid."' and stud_study_year <= '".$tt[1]."' and stud_study_year+$myear > '".$tt[1]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn && $tt[3] && $tt[4]) {
								if ($tt[5]) {
									$tt_tmp=explode("/",$tt[5]);
									$tt[5]=sprintf("%04d-%02d-%02d",(intval($tt_tmp[0])+1911),$tt_tmp[1],$tt_tmp[2]);
								}
								$query="replace into health_WH (year,semester,student_sn,weight,height,measure_date,update_date,teacher_sn) values ('$tt[1]','$tt[2]','$student_sn','$tt[3]','$tt[4]','$tt[5]','".$today."','".$_SESSION['session_tea_sn']."')";
								$res=$CONN->Execute($query);
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$ttid=(intval($tt[0])>0)?$tt[0]:trim(substr($tt[0],1,strlen($tt[0])-1));
							$query="select * from stud_base where stud_id='".$ttid."' and stud_study_year <= '".$tt[1]."' and stud_study_year+$myear > '".$tt[1]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) break;
						}
						if ($tt[5]) {
							$tt_tmp=explode("/",$tt[5]);
							$tt[5]=sprintf("%04d-%02d-%02d",(intval($tt_tmp[0])+1911),$tt_tmp[1],$tt_tmp[2]);
						}
						$smarty->assign("stud_id",$res->fields['stud_id']);
						$smarty->assign("stud_name",$res->fields['stud_name']);
						$smarty->assign("rowdata",$tt);
					}
					break;
				case "health_sight":
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$ttid=(intval($tt[0])>0)?$tt[0]:trim(substr($tt[0],1,strlen($tt[0])-1));
							$query="select * from stud_base where stud_id='".$ttid."' and stud_study_year <= '".$tt[1]."' and stud_study_year+$myear > '".$tt[1]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								if ($tt[9]) {
									$tt_tmp=explode("/",$tt[9]);
									$tt[9]=sprintf("%04d-%02d-%02d",(intval($tt_tmp[0])+1911),$tt_tmp[1],$tt_tmp[2]);
								}
								for($i=3;$i<=6;$i++) {
									if ($tt[$i]>0) $tt[$i]=sprintf("%1.1f",$tt[$i]/10);
								}
								if ($tt[4]) {
									$query="replace into health_sight (year,semester,student_sn,side,sight_o,sight_r,measure_date,update_date,teacher_sn) values ('$tt[1]','$tt[2]','$student_sn','r','$tt[4]','$tt[6]','$tt[9]','".$today."','".$_SESSION['session_tea_sn']."')";
									$res=$CONN->Execute($query);
								}
								if ($tt[3]) {
									$query="replace into health_sight (year,semester,student_sn,side,sight_o,sight_r,measure_date,update_date,teacher_sn) values ('$tt[1]','$tt[2]','$student_sn','l','$tt[3]','$tt[5]','$tt[9]','".$today."','".$_SESSION['session_tea_sn']."')";
									$res=$CONN->Execute($query);
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$ttid=(intval($tt[0])>0)?$tt[0]:trim(substr($tt[0],1,strlen($tt[0])-1));
							$query="select * from stud_base where stud_id='".$ttid."' and stud_study_year <= '".$tt[1]."' and stud_study_year+$myear > '".$tt[1]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) break;
						}
						if ($tt[9]) {
							$tt_tmp=explode("/",$tt[9]);
							$tt[9]=sprintf("%04d-%02d-%02d",(intval($tt_tmp[0])+1911),$tt_tmp[1],$tt_tmp[2]);
						}
						for($i=3;$i<=6;$i++) {
							if ($tt[$i]>0) $tt[$i]=sprintf("%1.1f",$tt[$i]/10);
						}
						$smarty->assign("stud_id",$res->fields['stud_id']);
						$smarty->assign("stud_name",$res->fields['stud_name']);
						$smarty->assign("rowdata",$tt);
					}
					break;
				case "health_teeth":
					$sym_arr=array("D"=>"1","M"=>"2","F"=>"3","T"=>"4","d"=>"1","m"=>"2","f"=>"3","e"=>"4");
					$ttt_arr=array("4"=>"7","6"=>"8","9"=>"1","10"=>"2","11"=>"3","12"=>"4");
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$ttid=(intval($tt[0])>0)?$tt[0]:trim(substr($tt[0],1,strlen($tt[0])-1));
							$query="select * from stud_base where stud_id='".$ttid."' and stud_study_year <= '".$tt[1]."' and stud_study_year+$myear > '".$tt[1]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								$stud_study_year=$res->fields['stud_study_year'];
								if ($stud_study_year<=$tt[1] && ($stud_study_year+$myear-1)>=$tt[1]) {
									for($i=0;$i<=19;$i++) {
										$w=substr($tt[15],$i,1);
										if ($sym_arr[$w]) {
											$j=floor(($i)/5)+5;
											$k=($i % 5)+1;
											if ($j==5 || $j==8) $k=6-$k;
											$query="replace into health_teeth (year,semester,student_sn,no,status,update_date,teacher_sn) values ('".$tt[1]."','".$tt[2]."','$student_sn','T".$j.$k."','".$sym_arr[$w]."','".$today."','".$_SESSION['session_tea_sn']."')";
											$res=$CONN->Execute($query);
										}
									}
									for($i=0;$i<=31;$i++) {
										$w=substr($tt[16],$i,1);
										if ($sym_arr[$w]) {
											$j=floor(($i)/8)+1;
											$k=($i % 8)+1;
											if ($j==1 || $j==4) $k=9-$k;
											$query="replace into health_teeth (year,semester,student_sn,no,status,update_date,teacher_sn) values ('".$tt[1]."','".$tt[2]."','$student_sn','T".$j.$k."','".$sym_arr[$w]."','".$today."','".$_SESSION['session_tea_sn']."')";
											$res=$CONN->Execute($query);
										}
									}
									$dis=0;
									while(list($k,$v)=each($ttt_arr)) {
										if ($tt[$k]) {
											$query="replace into health_checks_record (year,semester,student_sn,subject,no,status,update_date,teacher_sn) values ('".$tt[1]."','".$tt[2]."','$student_sn','Ora','$v','1','".$today."','".$_SESSION['session_tea_sn']."')";
											$res=$CONN->Execute($query);
											$dis++;
										}
									}
									if ($dis==0) {
										$query="replace into health_checks_record (year,semester,student_sn,subject,no,status) values ('".$tt[1]."','".$tt[2]."','$student_sn','Ora','0','1')";
										$res=$CONN->Execute($query);
									}
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$ttid=(intval($tt[0])>0)?$tt[0]:trim(substr($tt[0],1,strlen($tt[0])-1));
							$query="select * from stud_base where stud_id='".$ttid."' and stud_study_year <= '".$tt[1]."' and stud_study_year+$myear > '".$tt[1]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) break;
						}
						if ($student_sn) {
							$query="select * from stud_base where student_sn='$student_sn'";
							$res=$CONN->Execute($query);
							if ($res->fields['stud_study_year']<=$tt[1] && ($res->fields['stud_study_year']+$myear-1)>=$tt[1]) {
								$status_str=array();
								$ttotal=0;
								for($i=0;$i<=19;$i++) {
									$w=substr($tt[15],$i,1);
									if ($sym_arr[$w]) {
										$j=floor(($i)/5)+5;
										$k=($i % 5)+1;
										if ($j==5 || $j==8) $k=6-$k;
										$status_str[0].=$j.$k.$w." ";
									}
									$ttotal++;
								}
								for($i=0;$i<=31;$i++) {
									$w=substr($tt[16],$i,1);
									if ($sym_arr[$w]) {
										$j=floor(($i)/8)+1;
										$k=($i % 8)+1;
										if ($j==1 || $j==4) $k=9-$k;
										$status_str[1].=$j.$k.$w." ";
									}
									$ttotal++;
								}
							}
							$smarty->assign("stud_id",$res->fields['stud_id']);
							$smarty->assign("stud_name",$res->fields['stud_name']);
							$smarty->assign("ok",(($ttotal==52)?true:false));
							$smarty->assign("status_str",$status_str);
							$smarty->assign("rowdata",$tt);
						}
					}
					break;
				case "health_accident":
					$place_arr=array("1"=>"操場","2"=>"遊戲器材","3"=>"教室","4"=>"走廊","5"=>"樓梯","6"=>"校外","99"=>"其他");
					$part_arr=array("9"=>"頭","10"=>"眼","11"=>"口腔","12"=>"顏面","13"=>"耳鼻喉","14"=>"胸","15"=>"腹","16"=>"背","17"=>"手","18"=>"腳","46"=>"腰","47"=>"臀","48"=>"會陰","53"=>"頸");
					$istatus_arr=array("19"=>"擦傷","20"=>"刺傷","21"=>"裂割","22"=>"挫撞","23"=>"扭傷","24"=>"灼燙","25"=>"骨折","26"=>"叮咬","27"=>"舊傷","28"=>"外科其他");
					$ostatus_arr=array("29"=>"流鼻血","30"=>"暈眩","31"=>"頭痛","32"=>"發燒","33"=>"噁嘔","34"=>"胃痛","35"=>"腹瀉","36"=>"牙痛","37"=>"經痛","38"=>"內科其他","49"=>"氣喘","50"=>"腹痛","51"=>"疹癢","52"=>"眼疾");
					$attend_arr=array("39"=>"傷處","40"=>"冰枕","41"=>"觀察","42"=>"通知","43"=>"校方送醫","44"=>"衛教","45"=>"其他處理","54"=>"家長帶回");
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						$teacher_sn=$_SESSION['session_tea_sn'];
						$place_arr=array("1"=>"1","2"=>"2","3"=>"3","4"=>"5","5"=>"6","6"=>"10","99"=>"999");
						$part_arr=array("9"=>"1","10"=>"7","11"=>"9","12"=>"8","13"=>"10","14"=>"4","15"=>"5","16"=>"6","17"=>"11","18"=>"13","46"=>"12","47"=>"14","48"=>"15","53"=>"2");
						$istatus_arr=array("19"=>"1","20"=>"2","21"=>"2","22"=>"4","23"=>"5","24"=>"6","25"=>"8","26"=>"7","27"=>"9","28"=>"10");
						$ostatus_arr=array("29"=>"21","30"=>"12","31"=>"14","32"=>"11","33"=>"13","34"=>"16","35"=>"18","36"=>"15","37"=>"19","38"=>"24","49"=>"20","50"=>"17","51"=>"22","52"=>"23");
						$attend_arr=array("39"=>"1","40"=>"2","41"=>"4","42"=>"5","43"=>"7","44"=>"8","45"=>"999","54"=>"6");
						$temp_arr=get_accident_item(0,"health_accident_place");
						$rid_arr=get_accident_item(2,"health_accident_reason");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$ttid=(intval($tt[1])>0)?$tt[1]:trim(substr($tt[1],1,strlen($tt[1])-1));
							$query="select * from stud_base where stud_id='".$ttid."' and stud_study_year <= '".$tt[4]."' and stud_study_year+$myear > '".$tt[4]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								$stud_study_year=$res->fields['stud_study_year'];
								if ($tt[8]=="上午") $tt[8]="10:00:00";
								if ($tt[8]=="中午") $tt[8]="12:30:00";
								if ($tt[8]=="下午") $tt[8]="14:00:00";
								$place_id=$place_arr[$tt[10]];
								if ($place_id=="") $place_id=999;
								$reason_id=$rid_arr[$tt[12]];
								if ($reason_id=="" && $tt[12]!="") {
									$_POST['new_item']=$tt[12];
									$reason_id=get_accident_item(1,"health_accident_reason");
									$_POST['new_item']="";
									$rid_arr=get_accident_item(2,"health_accident_reason");
								}
								$query="insert into health_accident_record (year,semester,student_sn,sign_time,place_id,reason_id,memo,teacher_sn) values ('$tt[1]','$tt[2]','$student_sn','".(intval($tt[3])+1911)."-".$tt[4]."-".$tt[5]." ".$tt[8]."','$place_id','$reason_id','".addslashes($tt[17])."','$teacher_sn')";
								$res=$CONN->Execute($query);
								$new_id=$CONN->Insert_ID();
								$part_id=$part_arr[$tt[9]];
								if ($part_id=="") $part_id=999;
								$CONN->Execute("insert into health_accident_part_record (id,part_id) values ('$new_id','$part_id')");
								$status_id=$istatus_arr[$tt[11]];
								if ($status_id=="" && $tt[11]!="") $status_id=10;
								if ($status_id) $CONN->Execute("insert into health_accident_status_record (id,status_id) values ('$new_id','$status_id')");
								$status_id=$ostatus_arr[$tt[13]];
								if ($status_id=="" && $tt[13]!="") $status_id=24;
								if ($status_id) $CONN->Execute("insert into health_accident_status_record (id,status_id) values ('$new_id','$status_id')");
								$attend_id=$attend_arr[$tt[14]];
								if ($attend_id=="" && $tt[14]!="") $attend_id=999;
								if ($attend_id) $CONN->Execute("insert into health_accident_attend_record (id,status_id) values ('$new_id','$attend_id')");
								$attend_id2=$attend_arr[$tt[15]];
								if ($attend_id2=="" && $tt[15]!="") $attend2_id=999;
								if ($attend_id==999 && $attend_id2==999) $attend2_id=0;
								if ($attend_id) $CONN->Execute("insert into health_accident_attend_record (id,status_id) values ('$new_id','$attend_id2')");
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$ttid=(intval($tt[1])>0)?$tt[1]:trim(substr($tt[1],1,strlen($tt[1])-1));
							$query="select * from stud_base where stud_id='".$ttid."' and stud_study_year <= '".$tt[4]."' and stud_study_year+$myear > '".$tt[4]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) break;
						}
						if ($student_sn) {
							$smarty->assign("stud_id",$res->fields['stud_id']);
							$smarty->assign("stud_name",$res->fields['stud_name']);
							$smarty->assign("place_arr",$place_arr);
							$smarty->assign("part_arr",$part_arr);
							$smarty->assign("istatus_arr",$istatus_arr);
							$smarty->assign("ostatus_arr",$ostatus_arr);
							$smarty->assign("attend_arr",$attend_arr);
							$smarty->assign("rowdata",$tt);
						}
					}
					break;
			}
			fclose($fp);
		}
		$smarty->assign("file_kind_arr",$file_kind_arr);
		$smarty->assign("file_menu",file_menu($temp_path,$_POST['file_name'],"file_name","","CSV"));
		$smarty->assign("ifile","health_import_WIN.tpl");
		break;
	case "5":
		$file_kind_arr=array(""=>"請選擇檔案類別","health_mapping"=>"學生基本資料檔","health_wh"=>"身高體重資料檔","health_sight"=>"視力檢查資料檔","health_teeth"=>"牙齒檢查資料檔","health_accident"=>"學生傷病資料檔");
		$year_seme_menu="";
		$class_menu="";
		$_POST['class_name']=" ";
		//處理檔案上傳動作
		if ($_POST['doup_key']) {
			set_upload_path($path_str);
			$tmp_path=check_upload_file($_FILES['upload_file'],array("CSV"));
			if (is_uploaded_file($_FILES['upload_file']['tmp_name']) && !$_FILES['upload_file']['error'] && $_FILES['upload_file']['size'] >0 && $tmp_path!=""){
				move_uploaded_file($tmp_path.basename($_FILES['upload_file']['tmp_name']),$temp_path.strtoupper($_FILES['upload_file']['name']));
			}
		}
		$chk_arr=array_keys($file_kind_arr);
		if ($_POST['file_name'] && !$_POST['fkind']) $_POST['file_name']="";
		if ($_POST['fkind'] && in_array($_POST['fkind'],$chk_arr) && $_POST['file_name']) {
			$file_name=$temp_path.basename($_POST['file_name']);
			$fp=fopen($file_name,"r");
			switch($_POST['fkind']) {
				case "health_mapping":
					if ($_POST['sure']) {
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select student_sn from stud_base where stud_id='".$tt[1]."' order by student_sn desc";
							$res=$CONN->Execute($query);
							$sn=$res->fields['student_sn'];
							if ($sn) {
								$CONN->Execute("insert into health_mapping (student_sn,m_sn) values ('$sn','".$tt[3]."')");
							}
							$_POST['fkind']="";
							$_POST['file_name']="";
						}
					} else {
						//抓第一筆資料
						$smarty->assign("rowdata",sfs_fgetcsv($fp,2000,","));
					}
					break;
				case "health_wh":
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select * from health_mapping where m_sn='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								$query="select stud_study_year from stud_base where student_sn='$student_sn'";
								$res=$CONN->Execute($query);
								$stud_study_year=$res->fields['stud_study_year'];
								if (($stud_study_year<=$tt[1]) && (($stud_study_year+$myear-1)>=$tt[1]) && $tt[7] && $tt[8]) {
									$query="replace into health_WH (year,semester,student_sn,weight,height,measure_date,update_date,teacher_sn) values ('$tt[1]','".(($tt[2]=="上")?1:2)."','$student_sn','$tt[8]','$tt[7]','".(intval($tt[4])+1911)."-".$tt[5]."-".$tt[6]."','".$today."','".$_SESSION['session_tea_sn']."')";
									$res=$CONN->Execute($query);
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select * from health_mapping where m_sn='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) break;
						}
						$query="select * from stud_base where student_sn='$student_sn'";
						$res=$CONN->Execute($query);
						$smarty->assign("stud_id",$res->fields['stud_id']);
						$smarty->assign("stud_name",$res->fields['stud_name']);
						$smarty->assign("rowdata",$tt);
					}
					break;
				case "health_sight":
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select * from health_mapping where m_sn='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								$query="select stud_study_year from stud_base where student_sn='$student_sn'";
								$res=$CONN->Execute($query);
								$stud_study_year=$res->fields['stud_study_year'];
								if ($stud_study_year<=$tt[1] && ($stud_study_year+$myear-1)>=$tt[1]) {
									if ($tt[8]==0) $tt[8]="";
									if ($tt[8]>0 && $tt[6]=="") $tt[6]=0;
									$query="replace into health_sight (year,semester,student_sn,side,sight_o,sight_r,measure_date,update_date,teacher_sn) values ('$tt[1]','".(($tt[2]=="上")?1:2)."','$student_sn','r','$tt[6]','$tt[8]','".(intval($tt[3])+1911)."-".$tt[4]."-".$tt[5]."','".$today."','".$_SESSION['session_tea_sn']."')";
									$res=$CONN->Execute($query);
									if ($tt[9]==0) $tt[9]="";
									if ($tt[9]>0 && $tt[7]=="") $tt[7]=0;
									$query="replace into health_sight (year,semester,student_sn,side,sight_o,sight_r,measure_date,update_date,teacher_sn) values ('$tt[1]','".(($tt[2]=="上")?1:2)."','$student_sn','l','$tt[7]','$tt[9]','".(intval($tt[3])+1911)."-".$tt[4]."-".$tt[5]."','".$today."','".$_SESSION['session_tea_sn']."')";
									$res=$CONN->Execute($query);
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select * from health_mapping where m_sn='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								$query="select * from stud_base where student_sn='$student_sn'";
								$res=$CONN->Execute($query);
								if ($res->fields['stud_study_year']<=$tt[1] && ($res->fields['stud_study_year']+$myear-1)>=$tt[1]) break;
							}
						}
						$smarty->assign("stud_id",$res->fields['stud_id']);
						$smarty->assign("stud_name",$res->fields['stud_name']);
						$smarty->assign("rowdata",$tt);
					}
					break;
				case "health_teeth":
					$sym_arr=array("S"=>"","D"=>"1","M"=>"2","F"=>"3","T"=>"4");
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						$no_arr=array("0"=>"18","1"=>"21","2"=>"31","3"=>"48","4"=>"55","5"=>"61","6"=>"71","7"=>"85");
						$o_arr=array("0"=>-1,"1"=>1,"2"=>1,"3"=>-1,"4"=>-1,"5"=>1,"6"=>1,"7"=>-1);
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select * from health_mapping where m_sn='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								$query="select stud_study_year from stud_base where student_sn='$student_sn'";
								$res=$CONN->Execute($query);
								$stud_study_year=$res->fields['stud_study_year'];
								if ($stud_study_year<=$tt[1] && ($stud_study_year+$myear-1)>=$tt[1]) {
									$dis=0;
									for($i=1;$i<=4;$i++) {
										if ($tt[13+$i]==1) {
											$query="replace into health_checks_record (year,semester,student_sn,subject,no,status,update_date,teacher_sn) values ('$tt[1]','".(($tt[2]=="上")?1:2)."','$student_sn','Ora','$i','1','".(intval($tt[3])+1911)."-".$tt[4]."-".$tt[5]."','".$_SESSION['session_tea_sn']."')";
											$res=$CONN->Execute($query);
											$dis++;
										}
									}
									if ($dis==0) {
										$query="replace into health_checks_record (year,semester,student_sn,subject,no,status) values ('$tt[1]','".(($tt[2]=="上")?1:2)."','$student_sn','Ora','0','1')";
										$res=$CONN->Execute($query);
									}
									for($i=0;$i<=7;$i++) {
										for($j=0;$j<=strlen($tt[6+$i]);$j++) {
											$w=substr($tt[6+$i],$j,1);
											if ($sym_arr[$w]) {
												$no=$no_arr[$i]+$o_arr[$i]*$j;
												$query="replace into health_teeth (year,semester,student_sn,no,status,update_date,teacher_sn) values ('$tt[1]','".(($tt[2]=="上")?1:2)."','$student_sn','T".$no."','".$sym_arr[$w]."','".(intval($tt[3])+1911)."-".$tt[4]."-".$tt[5]."','".$_SESSION['session_tea_sn']."')";
												$res=$CONN->Execute($query);
											}
										}
									}
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select * from health_mapping where m_sn='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								$query="select * from stud_base where student_sn='$student_sn'";
								$res=$CONN->Execute($query);
								if ($res->fields['stud_study_year']<=$tt[1] && ($res->fields['stud_study_year']+$IS_JHORES-1)>=$tt[1]) {
									$status_str="";
									$ts=array();
									for($i=0;$i<=7;$i++) {
										for($j=0;$j<=strlen($tt[6+$i]);$j++) {
											$w=substr($tt[6+$i],$j,1);
											if ($w=="S" || $sym_arr[$w]) {
												$ttotal++;
												$ts[$sym_arr[$w]]++;
											}
										}
									}
									break;
								}
							}
						}
						$smarty->assign("stud_id",$res->fields['stud_id']);
						$smarty->assign("stud_name",$res->fields['stud_name']);
						$smarty->assign("ok",(($ttotal==52)?true:false));
						$smarty->assign("ts",$ts);
						$smarty->assign("rowdata",$tt);
					}
					break;
				case "health_accident":
					if ($_POST['sure']) {
						$today=date("Y-m-d H:i:s");
						$teacher_sn=$_SESSION['session_tea_sn'];
						$part_arr=array("頭"=>"1","眼"=>"7","口腔"=>"9","顏面"=>"8","耳鼻喉"=>"10","頸"=>"2","胸"=>"4","腹"=>"5","背"=>"6","腰"=>"12","臀"=>"14","會陰"=>"15","手"=>"11","腳"=>"13");
						$place_arr=array("運動場"=>"1","遊戲器材"=>"2","教室"=>"3","走廊"=>"5","樓梯"=>"6","校外"=>"10","其他"=>"999");
						$istatus_arr=array("擦傷"=>"1","刺傷"=>"2","裂割傷"=>"2","挫撞傷"=>"4","扭傷"=>"5","灼燙傷"=>"6","骨折"=>"8","叮咬傷"=>"7","舊傷"=>"9","其他"=>"10");
						$ostatus_arr=array("流鼻血"=>"21","暈眩"=>"12","頭痛"=>"14","發燒"=>"11","噁心嘔吐"=>"13","腹痛"=>"17","腹瀉"=>"18","牙痛"=>"15","經痛"=>"19","氣喘"=>"20","胃痛"=>"16","疹癢"=>"22","眼疾"=>"23","其他"=>"24");
						$attend_arr=array("傷口處理"=>"1","冰敷枕使用"=>"2","休息觀察"=>"4","告知家長"=>"5","家長帶回"=>"6","校方送醫"=>"7","衛生教育"=>"8","其他"=>"999","回教室上課"=>"0","導師送醫"=>"7","緊急送醫"=>"7");
						$temp_arr=get_accident_item(0,"health_accident_place");
						$rid_arr=get_accident_item(2,"health_accident_reason");
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select * from health_mapping where m_sn='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($tt[1]==0) {
								if ($tt[4]>1 && $tt[4]<8) {
									$tt[1]=intval($tt[3])-1;
									$tt[2]=2;
								} else {
									$tt[1]=intval($tt[3]);
									$tt[2]=1;
								}
							} else {
								if ($tt[2]=="上")
									$tt[2]=1;
								elseif ($tt[2]=="下")
									$tt[2]=2;
							}
							if ($student_sn) {
								$query="select stud_id,stud_study_year from stud_base where student_sn='$student_sn'";
								$res=$CONN->Execute($query);
								$stud_study_year=$res->fields['stud_study_year'];
								if ($stud_study_year<=$tt[1] && ($stud_study_year+$myear-1)>=$tt[1]) {
									if ($tt[8]=="上午") $tt[8]="10:00:00";
									if ($tt[8]=="中午") $tt[8]="12:30:00";
									if ($tt[8]=="下午") $tt[8]="14:00:00";
									$place_id=$place_arr[$tt[10]];
									if ($place_id=="") $place_id=999;
									$reason_id=$rid_arr[$tt[12]];
									if ($reason_id=="" && $tt[12]!="") {
										$_POST['new_item']=$tt[12];
										$reason_id=get_accident_item(1,"health_accident_reason");
										$_POST['new_item']="";
										$rid_arr=get_accident_item(2,"health_accident_reason");
									}
									$query="insert into health_accident_record (year,semester,student_sn,sign_time,place_id,reason_id,memo,teacher_sn) values ('$tt[1]','$tt[2]','$student_sn','".(intval($tt[3])+1911)."-".$tt[4]."-".$tt[5]." ".$tt[8]."','$place_id','$reason_id','".addslashes($tt[17])."','$teacher_sn')";
									$res=$CONN->Execute($query);
									$new_id=$CONN->Insert_ID();
									$part_id=$part_arr[$tt[9]];
									if ($part_id=="") $part_id=999;
									$CONN->Execute("insert into health_accident_part_record (id,part_id) values ('$new_id','$part_id')");
									$status_id=$istatus_arr[$tt[11]];
									if ($status_id=="" && $tt[11]!="") $status_id=10;
									if ($status_id) $CONN->Execute("insert into health_accident_status_record (id,status_id) values ('$new_id','$status_id')");
									$status_id=$ostatus_arr[$tt[13]];
									if ($status_id=="" && $tt[13]!="") $status_id=24;
									if ($status_id) $CONN->Execute("insert into health_accident_status_record (id,status_id) values ('$new_id','$status_id')");
									$attend_id=$attend_arr[$tt[14]];
									if ($attend_id=="" && $tt[14]!="") $attend_id=999;
									if ($attend_id) $CONN->Execute("insert into health_accident_attend_record (id,status_id) values ('$new_id','$attend_id')");
									$attend_id2=$attend_arr[$tt[15]];
									if ($attend_id2=="" && $tt[15]!="") $attend2_id=999;
									if ($attend_id==999 && $attend_id2==999) $attend2_id=0;
									if ($attend_id) $CONN->Execute("insert into health_accident_attend_record (id,status_id) values ('$new_id','$attend_id2')");
								}
							}
						}
					} else {
						//抓第一筆資料
						while($tt=sfs_fgetcsv($fp,2000,",")) {
							$query="select * from health_mapping where m_sn='".$tt[0]."'";
							$res=$CONN->Execute($query);
							$student_sn=$res->fields['student_sn'];
							if ($student_sn) {
								$query="select * from stud_base where student_sn='$student_sn'";
								$res=$CONN->Execute($query);
								if ($res->fields['stud_study_year']<=$tt[1] && ($res->fields['stud_study_year']+$myear-1)>=$tt[1]) break;
							}
						}
						$smarty->assign("stud_id",$res->fields['stud_id']);
						$smarty->assign("stud_name",$res->fields['stud_name']);
						$smarty->assign("rowdata",$tt);
					}
					break;
			}
			fclose($fp);
		} else {
			$query="select * from health_mapping where 1=0";
			$res=$CONN->Execute($query);
			if (!$res) {
				//建立學生對應資料表
				$query="CREATE TABLE if not exists health_mapping (
						student_sn int(10) unsigned NOT NULL default '0',
						m_sn int(10) unsigned NOT NULL default '0',
						PRIMARY KEY (student_sn)
						)";
				$CONN->Execute($query);
			}
		}
		$query="select * from health_mapping";
		$res=$CONN->Execute($query);
		$smarty->assign("havedb",$res->RecordCount());
		$smarty->assign("file_kind_arr",$file_kind_arr);
		$smarty->assign("file_menu",file_menu($temp_path,$_POST['file_name'],"file_name","","CSV"));
		$smarty->assign("ifile","health_import_SHL.tpl");
		break;
	case "6":
		$smarty->assign("year_menu",year_menu($sel_year,""));
		$s=get_school_base();
		$sch_id=$s[sch_id];
		$pre_year=sprintf("%03d",$sel_year);
		//身高體重檔
		if ($_POST['export1']) {
			ini_set('max_execution_time',0);
            ini_set('memory_limit','200M');
			require_once "../../include/sfs_case_excel.php";
			$x=new sfs_xls();
			$x->setUTF8();
			$x->setBorderStyle(0);
			$x->addSheet("國中小身高體重檢查");
			$x->filename="wh.xls";
			$temp_arr=array();
			for($i=1;$i<=18;$i++) $temp_arr[]=$i;
			$x->setRowText($temp_arr);
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,1,"all");
			$health_data->get_wh();
			$temp_arr=array();
			//print_r($health_data);exit;
			for($i=0;$i<5;$i++) $temp_arr[]=array();
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$sn=$vv[student_sn];
					$d=$health_data->stud_base[$sn];
					$seme_year=substr($seme_class,0,-2);
					$seme_name=intval(substr($seme_class,-2,2));
					$birthday=str_replace("-","/",$d['stud_birthday']);
					$stud_study_year=$d['stud_study_year']-$IS_JHORES;
					if ($health_data->health_data[$sn][$pre_year."1"][years]) {
						$a=array($sch_id,$d['stud_person_id'],$sel_year,1,$d['stud_sex'],$seme_year,$stud_study_year,$seme_name,$health_data->health_data[$sn][$pre_year."1"][weight],$health_data->health_data[$sn][$pre_year."1"][height],"","","",$birthday,$health_data->health_data[$sn][$pre_year."1"][years],$health_data->health_data[$sn][$pre_year."1"][Bid]-1,"","");
						$temp_arr[]=$a;
					}
				}
			}
			$x->items=$temp_arr;
			$x->writeSheet();
			$x->process();
			exit;
		}

		//視力檔
		if ($_POST['export2']) {
			ini_set('max_execution_time',0);
            ini_set('memory_limit','200M');
			require_once "../../include/sfs_case_excel.php";
			$x=new sfs_xls();
			$x->setUTF8();
			$x->setBorderStyle(0);
			$x->addSheet("國中小視力檢查");
			$x->filename="sight.xls";
			$temp_arr=array();
			for($i=1;$i<=22;$i++) $temp_arr[]=$i;
			$x->setRowText($temp_arr);
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,1,"all");
			$health_data->get_sight();
			$temp_arr=array();
			//print_r($health_data);exit;
			for($i=0;$i<5;$i++) $temp_arr[]=array();
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$sn=$vv[student_sn];
					$d=$health_data->stud_base[$sn];
					$seme_year=substr($seme_class,0,-2);
					$seme_name=intval(substr($seme_class,-2,2));
					$birthday=str_replace("-","/",$d['stud_birthday']);
					$stud_study_year=$d['stud_study_year']-$IS_JHORES;
					$r=$health_data->health_data[$sn][$pre_year."1"]['r'];
					$l=$health_data->health_data[$sn][$pre_year."1"]['l'];
					if ($health_data->health_data[$sn][$pre_year."1"]['r']['sight_o'] || $health_data->health_data[$sn][$pre_year."1"]['l']['sight_o']) $oo=1;
					else $oo=0;
					if ($health_data->health_data[$sn][$pre_year."1"]['r']['sight_r'] || $health_data->health_data[$sn][$pre_year."1"]['l']['sight_r']) $rr=1;
					else $rr=0;
					if ($oo || $rr) {
						$a=array($sch_id,$d['stud_person_id'],$sel_year,1,$d['stud_sex'],$seme_year,$stud_study_year,$seme_name,$health_data->health_data[$sn][$pre_year."1"]['r']['sight_o'],$health_data->health_data[$sn][$pre_year."1"]['l']['sight_o'],$health_data->health_data[$sn][$pre_year."1"]['r']['sight_r'],$health_data->health_data[$sn][$pre_year."1"]['l']['sight_r'],$oo,$rr,(($r['My'] || $l['My'])?1:0),(($r['Hy'] || $l['Hy'])?1:0),(($r['Amb'] || $l['Amb'])?1:0),(($r['Ast'] || $l['Ast'])?1:0),0,"","","");
						$temp_arr[]=$a;
					}
				}
			}
			$x->items=$temp_arr;
			$x->writeSheet();
			$x->process();
			exit;
		}

		//健康檢查檔
		if ($_POST['export3']) {
			ini_set('max_execution_time',0);
            ini_set('memory_limit','200M');
			require_once "../../include/sfs_case_excel.php";
			$x=new sfs_xls();
			$x->setUTF8();
			$x->setBorderStyle(0);
			$x->addSheet("國中小健康檢查");
			$x->filename="checks.xls";
			$temp_arr=array();
			for($i=1;$i<=134;$i++) $temp_arr[]=$i;
			$x->setRowText($temp_arr);
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,1,"all");
			$health_data->get_checks();
			$health_data->get_sight();
			$health_data->get_teeth();
			$health_data->get_uri();
			$health_data->get_worm();
			$health_data->get_checks_doctor();
			if ($health_data->checks_arr[(sprintf("%03d",$sel_year)."1")]) $chk_seme=(sprintf("%03d",$sel_year)."1");
			else $chk_seme=(sprintf("%03d",$sel_year)."2");
			$temp_arr=array();
			for($i=0;$i<5;$i++) $temp_arr[]=array();
			//牙齒狀態陣列
			$teesb=array("1"=>"D","2"=>"M","3"=>"F","4"=>"E","5"=>"G","6"=>"S");
			//實驗室檢查狀況陣列
			$wstatus=array(1=>0,2=>1,12=>2,22=>3,0=>9);
			$dnull="1970/01/01";
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$sn=$vv[student_sn];
					$d=$health_data->stud_base[$sn];
					$seme_year=substr($seme_class,0,-2);
					$seme_name=intval(substr($seme_class,-2,2));
					$birthday=str_replace("-","/",$d['stud_birthday']);
					$stud_study_year=$d['stud_study_year']-$IS_JHORES;
					$r=$health_data->health_data[$sn][$pre_year."1"]['r'];
					$l=$health_data->health_data[$sn][$pre_year."1"]['l'];
					$dd=$health_data->health_data[$sn][$chk_seme];
					$c=$health_data->health_data[$sn][$chk_seme]['checks'];
					$w=($health_data->health_data[$sn][$chk_seme]['exp']['worm'][2]['status'])*10+$health_data->health_data[$sn][$chk_seme]['exp']['worm'][1]['status'];
					$nid=array(""=>0,0=>0,1=>1,2=>2,3=>2,4=>3,9=>9);
					if ($c['Oph'][2]=="") continue;
					$t="";
					if ($dd) {
						reset($dd);
						foreach($dd as $kk=>$vv) {
							if (substr($kk,0,1)=="T") $t.=substr($kk,1,2).$teesb[$vv];
						}
					}
					$a=array(
						$sch_id,
						$d['stud_person_id'],
						$seme_year,
						$sel_year,
						1,
						$d['stud_sex'],
						$stud_study_year,
						$seme_name,
						"0",
						(($r['My'] || $l['My'])?1:0),
						(($r['Hy'] || $l['Hy'])?1:0),
						(($r['Amb'] || $l['Amb'])?1:0),
						(($r['Ast'] || $l['Ast'])?1:0),
						0,
						"",
						$nid[$c['Oph'][2]],
						$nid[$c['Oph'][3]],
						$nid[$c['Oph'][PS3]],
						$nid[$c['Oph'][4]],
						$nid[$c['Oph'][5]],
						$nid[$c['Oph'][6]],
						0,
						"",
						$nid[$c['Ent'][1]],
						$nid[$c['Ent'][PS1]],
						$nid[$c['Ent'][2]],
						$nid[$c['Ent'][3]],
						$nid[$c['Ent'][4]],
						$nid[$c['Ent'][5]],
						$nid[$c['Ent'][6]],
						$nid[$c['Ent'][7]],
						$nid[$c['Ent'][8]],
						$nid[$c['Ent'][9]],
						$nid[$c['Ent'][10]],
						0,
						"",
						$nid[$c['Hea'][1]],
						$nid[$c['Hea'][2]],
						$nid[$c['Hea'][3]],
						0,
						"",
						$nid[$c['Pul'][1]],
						$nid[$c['Pul'][2]],
						$nid[$c['Pul'][3]],
						$nid[$c['Pul'][4]],
						0,
						"",
						$nid[$c['Dig'][1]],
						$nid[$c['Dig'][2]],
						0,
						"",
						$nid[$c['Spi'][1]],
						$nid[$c['Spi'][2]],
						$nid[$c['Spi'][3]],
						$nid[$c['Spi'][4]],
						$nid[$c['Spi'][5]],
						0,
						"",
						$nid[$c['Uro'][1]],
						$nid[$c['Uro'][2]],
						$nid[$c['Uro'][3]],
						$nid[$c['Uro'][4]],
						0,
						"",
						$nid[$c['Der'][1]],
						$nid[$c['Der'][2]],
						$nid[$c['Der'][3]],
						$nid[$c['Der'][4]],
						$nid[$c['Der'][5]],
						$nid[$c['Der'][6]],
						0,
						"",
						$nid[$c['Ora'][7]],
						$nid[$c['Ora'][8]],
						$nid[$c['Ora'][1]],
						$nid[$c['Ora'][2]],
						$nid[$c['Ora'][5]],
						$nid[$c['Ora'][3]],
						$nid[$c['Ora'][4]],
						$nid[$c['Ora'][6]],
						0,
						"",
						$t,
						0,
						"",
						"N",
						$c['Ora']['hospital'],
						$c['Oph']['doctor'],
						$c['Ora']['doctor'],
						0,
						"",
						0,
						"",
						0,
						0,
						0,
						(($c['Ora']['date'])?str_replace("-","/",$c['Ora']['date']):$dnull),
						0,
						$wstatus[$w],
						0,
						(($health_data->health_data[$sn][$chk_seme]['exp']['worm'][1]['date'])?str_replace("-","/",$health_data->health_data[$sn][$chk_seme]['exp']['worm'][1]['date']):$dnull),
						($health_data->health_data[$sn][$chk_seme]['exp']['worm'][1]['med']),
						0,
						(($health_data->health_data[$sn][$chk_seme]['exp']['worm'][2]['date'])?str_replace("-","/",$health_data->health_data[$sn][$chk_seme]['exp']['worm'][2]['date']):$dnull),
						1,
						(($health_data->health_data[$sn][$chk_seme]['exp']['uri'][1]['date'])?str_replace("-","/",$health_data->health_data[$sn][$chk_seme]['exp']['uri'][1]['date']):$dnull),
						($health_data->health_data[$sn][$chk_seme]['exp']['uri'][1]['glu']),
						($health_data->health_data[$sn][$chk_seme]['exp']['uri'][1]['pro']),
						($health_data->health_data[$sn][$chk_seme]['exp']['uri'][1]['bld']),
						($health_data->health_data[$sn][$chk_seme]['exp']['uri'][1]['ph']),
						0,
						"2010/12/09",
						0,
						0,
						0,
						6,
						"",
						50,
						1,
						120,
						80,
						9,
						9,
						9,
						9,
						9,
						9,
						9,
						9,
						9,
						9,
						9,
						9,
						9
						);
					$temp_arr[]=$a;
				}
			}
			$x->items=$temp_arr;
			$x->writeSheet();
			$x->process();
			exit;
		}

		//個人疾病史
		if ($_POST['export5']) {
			ini_set('max_execution_time',0);
            ini_set('memory_limit','200M');
			require_once "../../include/sfs_case_excel.php";
			$x=new sfs_xls();
			$x->setUTF8();
			$x->setBorderStyle(0);
			$x->addSheet("國中小個人疾病史");
			$x->filename="disease.xls";
			$temp_arr=array();
			for($i=1;$i<=10;$i++) $temp_arr[]=$i;
			$x->setRowText($temp_arr);
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,1,"all");
			$health_data->get_disease();
			$temp_arr=array();
			for($i=0;$i<4;$i++) $temp_arr[]=array();
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$sn=$vv[student_sn];
					$d=$health_data->stud_base[$sn];
					if (is_array($d['disease'])) {
						foreach($d['disease'] as $dd) {
							$seme_year=substr($seme_class,0,-2);
							$seme_name=intval(substr($seme_class,-2,2));
							$birthday=str_replace("-","/",$d['stud_birthday']);
							$stud_study_year=$d['stud_study_year']-$IS_JHORES;
							$a=array($sch_id,$sel_year,$d['stud_person_id'],$dd,$stud_study_year,$seme_year,$seme_name,$d['stud_sex'],$seme_num);
							$temp_arr[]=$a;
						}
					}
				}
			}
			$x->items=$temp_arr;
			$x->writeSheet();
			$x->process();
			exit;
		}

		//傷病檔
		if ($_POST['export6']) {
			ini_set('max_execution_time',0);
            ini_set('memory_limit','200M');
			require_once "../../include/sfs_case_excel.php";
			$x=new sfs_xls();
			$x->setUTF8();
			$x->setBorderStyle(0);
			$x->addSheet("傷病");
			$x->filename="accident.xls";
			$temp_arr=array();
			for($i=1;$i<=63;$i++) $temp_arr[]=$i;
			$x->setRowText($temp_arr);
			$temp_arr=array();
			//print_r($health_data);exit;
			for($i=0;$i<5;$i++) $temp_arr[]=array();
			$query="select a.*,b.stud_person_id,b.stud_sex,b.curr_class_num,b.stud_study_year from health_accident_record a left join stud_base b on a.student_sn=b.student_sn where a.year='99' and a.semester='$sel_seme' order by a.sign_time";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				//取出時間
				$tt=substr($res->fields['sign_time'],-8,8);
				//判斷早中下午
				if ($tt>"13:00:00") $t=3;
				elseif ($tt>"12:00:00") $t=2;
				else $t=1;

				$id=$res->fields['id'];
				$temp_arr2=array();
				//取出其他資料
				$query="select * from health_accident_part_record where id='$id'";
				$res2=$CONN->Execute($query);
				while(!$res2->EOF) {
					$temp_arr2[part_id][$res2->fields['part_id']]=1;
					$res2->MoveNext();
				}
				$query="select * from health_accident_status_record where id='$id'";
				$res2=$CONN->Execute($query);
				while(!$res2->EOF) {
					$temp_arr2[status_id][$res2->fields['status_id']]=1;
					$res2->MoveNext();
				}
				$query="select * from health_accident_attend_record where id='$id'";
				$res2=$CONN->Execute($query);
				while(!$res2->EOF) {
					$temp_arr2[attend_id][$res2->fields['attend_id']]=1;
					$res2->MoveNext();
				}

				$a=array(
				$res->fields['stud_person_id'],
				$res->fields['stud_sex'],
				substr($res->fields['curr_class_num'],0,1),
				substr($res->fields['curr_class_num'],-4,2),
				substr($res->fields['curr_class_num'],-2,2),
				($res->fields['stud_study_year']-$IS_JHORES),
				$sel_seme,
				substr($res->fields['sign_time'],5,2),
				str_replace("-","/",substr($res->fields['sign_time'],0,10)),
				$t,
				($res->fields['place_id']==999?99:$res->fields['place_id']),
				$res->fields['temp'],
				$res->fields['obs_min'],
				0,
				(($temp_arr2[part_id][1]==1)?1:0),
				(($temp_arr2[part_id][7]==1)?1:0),
				(($temp_arr2[part_id][9]==1)?1:0),
				(($temp_arr2[part_id][8]==1)?1:0),
				(($temp_arr2[part_id][10]==1)?1:0),
				(($temp_arr2[part_id][4]==1)?1:0),
				(($temp_arr2[part_id][5]==1)?1:0),
				(($temp_arr2[part_id][6]==1)?1:0),
				(($temp_arr2[part_id][11]==1)?1:0),
				(($temp_arr2[part_id][12]==1)?1:0),
				(($temp_arr2[part_id][13]==1)?1:0),
				(($temp_arr2[part_id][14]==1)?1:0),
				(($temp_arr2[part_id][15]==1)?1:0),
				(($temp_arr2[part_id][3]==1)?1:0),
				(($temp_arr2[part_id][2]==1)?1:0),
				(($temp_arr2[status_id][1]==1)?1:0),
				(($temp_arr2[status_id][2]==1)?1:0),
				(($temp_arr2[status_id][4]==1)?1:0),
				(($temp_arr2[status_id][5]==1)?1:0),
				(($temp_arr2[status_id][6]==1)?1:0),
				(($temp_arr2[status_id][8]==1)?1:0),
				(($temp_arr2[status_id][3]==1)?1:0),
				(($temp_arr2[status_id][10]==1)?1:0),
				(($temp_arr2[status_id][7]==1)?1:0),
				(($temp_arr2[status_id][9]==1)?1:0),
				(($temp_arr2[status_id][21]==1)?1:0),
				(($temp_arr2[status_id][12]==1)?1:0),
				(($temp_arr2[status_id][14]==1)?1:0),
				(($temp_arr2[status_id][11]==1)?1:0),
				(($temp_arr2[status_id][13]==1)?1:0),
				(($temp_arr2[status_id][16]==1)?1:0),
				(($temp_arr2[status_id][18]==1)?1:0),
				(($temp_arr2[status_id][15]==1)?1:0),
				(($temp_arr2[status_id][19]==1)?1:0),
				(($temp_arr2[status_id][23]==1)?1:0),
				(($temp_arr2[status_id][20]==1)?1:0),
				(($temp_arr2[status_id][22]==1)?1:0),
				(($temp_arr2[status_id][17]==1)?1:0),
				(($temp_arr2[status_id][24]==1)?1:0),
				(($temp_arr2[attend_id][1]==1)?1:0),
				(($temp_arr2[attend_id][2]==1)?1:0),
				(($temp_arr2[attend_id][3]==1)?1:0),
				(($temp_arr2[attend_id][4]==1)?1:0),
				(($temp_arr2[attend_id][5]==1)?1:0),
				(($temp_arr2[attend_id][7]==1)?1:0),
				(($temp_arr2[attend_id][8]==1)?1:0),
				(($temp_arr2[attend_id][6]==1)?1:0),
				(($temp_arr2[attend_id][999]==1)?1:0),
				$res->fields['memo']
				);
				$temp_arr[]=$a;
				$res->MoveNext();
			}
			$x->items=$temp_arr;
			$x->writeSheet();
			$x->process();
			exit;
		}
		
		$smarty->assign('sch_id', $sch_id);
		$smarty->assign("ifile","health_export.tpl");
		break;
	case 7:
		$class_menu="";
		$smarty->assign('class_arr',class_base());
		$smarty->assign("ifile","health_trans.tpl");
		break;
	case 8:
		$class_menu="";
		if ($_POST['export']) {
			$health_data=new health_chart();
			if ($IS_JHORES)
				$health_data->get_stud_base($sel_year,$sel_seme,7);
			else {
				$health_data->get_stud_base($sel_year,$sel_seme,1);
				$health_data->get_stud_base($sel_year,$sel_seme,4);
			}
			require_once "../../include/sfs_case_excel.php";
			$sch_arr=get_school_base();
			$x=new sfs_xls();
			$x->setUTF8();
			$x->setBorderStyle(1);
			$x->addSheet("data");
			$x->setRowText(array("PID","SchoolID","GradeID","ClassID","Number","Name","Sex"));
			$temp_arr=array();
			while(list($seme_class,$v)=each($health_data->stud_data)) {
				while(list($seme_num,$vv)=each($v)) {
					$a=array();
					$seme_year=substr($seme_class,0,-2);
					$seme_name=intval(substr($seme_class,-2,2));
					$sn=$vv[student_sn];
					$d=$health_data->stud_base[$sn];
					$stud_sex=($d[stud_sex]==1)?"M":"F";
					$a=array($d['stud_person_id'],$sch_arr['sch_id'],$seme_year,$seme_name,$seme_num,$d['stud_name'],$stud_sex);
					$temp_arr[]=$a;
				}
			}
			$x->items=$temp_arr;
			$x->writeSheet();
			$x->process();
			exit;
		}
		$smarty->assign("ifile","health_base_checks.tpl");
		break;
	case 9:
		$class_menu="";
		if ($_POST['export']) {
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,"all");
			$sch=get_school_base();
			$filename=$sch['sch_sheng'].$sch['sch_cname_ss'].".csv";
			if(preg_match("/MSIE/i",$_SERVER['HTTP_USER_AGENT'])) {
				$filename=urlencode($filename);
			}
			header("Content-disposition: attachment; filename=$filename");
			header("Content-type: application/octetstream ; Charset=Big5");
			//header("Pragma: no-cache");
							//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

			header("Expires: 0");
			$smarty->assign("sch",$sch);
			$smarty->assign("health_data",$health_data);
			$smarty->display("health_base_export_csv.tpl");
			exit;
		}
		if ($_POST['xls']) {
			$sch=get_school_base();
			require_once "../../include/sfs_case_excel.php";
			$x=new sfs_xls();
			$x->setUTF8();
			$x->setBorderStyle(1);
			$health_data=new health_chart();
			$health_data->get_stud_base($sel_year,$sel_seme,"all");
			$x->mergeArr[]=array(0,0,0,9);
			$x->mergeArr[]=array(1,0,1,9);
			$x->mergeArr[]=array(2,0,2,9);
			$x->mergeArr[]=array(3,0,3,9);
			while(list($k,$v)=each($health_data->stud_data)) {
				$x->addSheet($k);
				$temp_arr=array();
				$temp_arr[]=array("附件2     「H1N1新型流感疫苗接種計畫」學生接種名冊","","","","","","","","","");
				$temp_arr[]=array($sch['sch_cname'],"","","","","","","","","");
				$temp_arr[]=array("接種對象：".(($IS_JHORES==0)?"■":"□")."國小  ".(($IS_JHORES==6)?"■":"□")."國中  □高中  □高職  □五專     接種日期：    年    月    日","","","","","","","","","");
				$temp_arr[]=array("班級：".intval(substr($k,0,-2))."年".intval(substr($k,-2,2))."班  學生總數：".count($v)."人","","","","","","","","","");
				$temp_arr[]=array("班號","姓名","身分證字號","性別","出生日期","體溫\r\n(℃)","接種劑次\r\n(1或2)","不接種原因說明","疫苗廠牌批號","備註");
				while(list($seme_num,$vv)=each($v)) {
					$sn=$vv[student_sn];
					$d_arr=explode("-",$health_data->stud_base[$sn][stud_birthday]);
					if (count($d_arr)==3) {
						$d_arr[0]-=1911;
						$birthday=implode("",$d_arr);
					} else {
						$birthday="";
					}
					$temp_arr[]=array(intval(substr($k,-2,2)),$health_data->stud_base[$sn][stud_name],$health_data->stud_base[$sn][stud_person_id],(($health_data->stud_base[$sn][stud_sex]==1)?"男":"女"),"$birthday","","","","","");
				}
				$x->items=$temp_arr;
				$x->writeSheet();
			}
			$x->process();
			exit;
		}
		$smarty->assign("ifile","health_base_export.tpl");
		break;
	case 10:
		if ($_FILES['upload_file']['size']) {
//			require_once $SPREADSHEET_PATH."Excel/Reader.php";
//			$x = new Spreadsheet_Excel_Reader();
//			$x->setOutputEncoding('Big5');
//			$x->read($tmp_path."\\".basename($_FILES['upload_file']['tmp_name']));
//			for ($i = 1; $i <= $x->sheets[0]['numRows']; $i++) {
//				for ($j = 1; $j <= $x->sheets[0]['numCols']; $j++) {
//					echo "\"".$x->sheets[0]['cells'][$i][$j]."\",";
//				}
//				echo "\n";
//			}
			$fp=fopen($tmp_path."/".basename($_FILES['upload_file']['tmp_name']),"r");
			$i=1;
			while($tt=sfs_fgetcsv($fp,4096,",")) {
//				echo $i."<br>";print_r($tt);echo "<br><br>";
				$i++;
			}
		}
		$smarty->assign("ifile","health_checks_import.tpl");
		break;
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sub_menu",$sub_menu);
$smarty->assign("year_seme_menu",$year_seme_menu);
$smarty->assign("class_menu",$class_menu);
$smarty->assign("module_name","學生健康資料處理");
$smarty->display("health_base.tpl");
?>
