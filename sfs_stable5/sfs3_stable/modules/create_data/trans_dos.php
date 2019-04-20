<?php

// $Id: trans_dos.php 6141 2010-09-14 03:17:12Z brucelyc $

//載入設定檔
include "create_data_config.php";
include "../../include/sfs_case_dataarray.php";
include "my_fun.php";

//認證檢查
sfs_check();

//處理檔案上傳
if ($_POST['doup_key']) {
	$file_name=strtoupper($_FILES['upload_file']['name']);
	if ($_FILES['upload_file']['size'] >0 && $file_name != "" && strstr($file_name,"XBASIC") && substr($file_name,-3,3) == "CSV"){
//		copy($_FILES['upload_file']['tmp_name'],$temp_path.$file_name);
		$k=file_get_contents($_FILES['upload_file']['tmp_name']);
		$k_arr=explode("\n",$k);
		$line1=count($k_arr);
		$k=iconv("Big5","UTF-8",$k);
		$k_arr=explode("\n",$k);
		$line2=count($k_arr);
		file_put_contents($temp_path.$file_name,$k);
		if ($line1>$line2) {
			$smarty->assign("line",$line2);
			$smarty->assign("brk_msg",iconv("UTF-8","Big5",$k_arr[$line2-1]));
			unlink($temp_path.$file_name);
		}
	}
}

//刪除檔案
if ($_POST['del']) {
	$file1=$temp_path."/".$_POST['file_name1'];
	unlink($file1);
	$_POST['file_name1']="";
}

//取得伺服器檔案選單
if (strlen($_POST['file_name1'])>5) {
	$sel_year=substr($_POST['file_name1'],6,2);
	$chk_file1="(".$sel_year."學年度)";
	$smarty->assign("chk_file1",$chk_file1);
}

//資料匯入
if ($_POST['import']) {
	$file1=$temp_path."/".$_POST['file_name1'];
	$fp=fopen($file1,"r");
	
	//抓欄位名檢查
	$arr=array();
	$cols=array();
	$cols=sfs_fgetcsv($fp,2000,",");
	$arr=chk_cols($cols);
	$s=get_school_base();
	$ZIP_ARR=get_addr_zip_arr();
	$r=guardian_relation();
	while(list($i,$v)=each($r)) {
		$RELA_ARR[$v]=$i;
	}

	$temp_msg="";
	if (!is_null($arr['stud_id']) && !is_null($arr['stud_name'])) {
		$ii=1;
		while($k=sfs_fgetcsv($fp, 2000, ",")) {
			while(list($i,$v)=each($k)) $k[$i]=iconv("UTF-8","Big5",$v);
			reset($arr);
			$stud_base=array();
			while(list($i,$v)=each($arr)) {
			if (is_array($v))
				while(list($j,$vv)=each($v)) {
					if ($k[$vv]!="") $stud_base[$i][$j]=trim($k[$vv]);
				}
			else
				$stud_base[$i]=trim(addslashes($k[$v]));
			}
			$stud_base['stud_study_year']=$sel_year;
			$stud_base['default']=$s['sch_sheng'];
			$stud_base['guardian_relation']=$RELA_ARR[$stud_base['guardian_relation']];
			$msg=check_student_data($stud_base);
			if ($msg!="") $temp_msg.=$msg."<br>";
			$ii++;
		}
	}
	fclose($fp);
	unlink($file1);
	$smarty->assign("err_msg",$temp_msg); 
//資料顯示
} elseif ($_POST['file_name1']) {
	$file1=$temp_path."/".$_POST['file_name1'];
	$fp=fopen($file1,"r");

	//抓欄位名檢查
	$arr=array();
	$cols=array();
	$cols=sfs_fgetcsv($fp,2000,",");
	$arr=chk_cols($cols);

	//確定欄位正確才進行處理
	if (!is_null($arr['stud_id']) && $arr['stud_name']) {
		$k=sfs_fgetcsv($fp, 2000, ",");
		while(list($i,$v)=each($k)) $k[$i]=iconv("UTF-8","Big5",$v);
		reset($arr);
		while(list($i,$v)=each($arr)) {
			if (is_array($v))
				while(list($j,$vv)=each($v)) {
					if ($k[$vv]!="") $stud_base[$i][$j]=$k[$vv];
				}
			else
				$stud_base[$i]=$k[$v];
		}
		$stud_base['stud_sex']=2-$stud_base['stud_sex'];
		$smarty->assign("stud_base",$stud_base);
		$smarty->assign("study_cond",array("0"=>"在籍","1"=>"轉入","2"=>"復學","5"=>"輟學","6"=>"轉出","7"=>"修業","8"=>"畢業"));
	}
	fclose($fp);
}

//資料導出到樣版檔
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","匯入省教育廳版校務系統學生基本資料"); 
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("file_menu1",file_menu($temp_path,$_POST['file_name1'],"file_name1","XBASIC","CSV"));
$smarty->display("create_data_trans_dos.tpl");

function chk_cols($cols) {

	while(list($i,$v)=each($cols)) {
		switch($v) {
			case "STUD_NO":
				$arr['stud_id']=$i;
				break;
			case "NAME":
				$arr['stud_name']=$i;
				break;
			case "ID_NO":
				$arr['stud_person_id']=$i;
				break;
			case "SEX":
				$arr['stud_sex']=$i;
				break;
			case "BIRTHDAY":
				$arr['stud_birthday']=$i;
				break;
			case "NATIVE":
				$arr['stud_birth_place']=$i;
				break;
			case "ENT_QUA":
				$arr['stud_mschool_name']=$i;
				break;
			case "REG_ADDR":
				$arr['stud_addr_1']=$i;
				break;
			case "PSNT_ADDR":
				$arr['stud_addr_2']=$i;
				break;
			case "TEL":
				$arr['stud_tel_2']=$i;
				break;
			case "GD_NAME":
				$arr['guardian_name']=$i;
				break;
			case "GD_RELATE":
				$arr['guardian_relation']=$i;
				break;
			case "GD_ADDR":
				$arr['guardian_address']=$i;
				break;
			case "GD_TEL_H":
				$arr['guardian_phone']=$i;
				break;
			case "GD_OCCUPT":
				$arr['phone']=$i;
				break;
			case "STATUS":
				$arr['stud_study_cond']=$i;
				break;
			default:
				if (substr($v,0,-1)=="SCHYEAR")
					$arr['seme_year'][substr($v,-1,1)]=$i;
				elseif (substr($v,0,-1)=="CLASS")
					$arr['seme_class'][substr($v,-1,1)]=$i;
				elseif (substr($v,0,-1)=="SEAT")
					$arr['seme_num'][substr($v,-1,1)]=$i;
		}
	}
	return $arr;
}
?>
