<?php

// $Id: class_import.php 7401 2013-08-02 11:46:56Z infodaes $

//載入設定檔
include "stud_year_config.php";

//認證檢查
sfs_check();

//檔案解析
$file_name=$_FILES['upload_file']['tmp_name'];

if ($file_name && $_FILES['upload_file']['size']>0 && $_FILES['upload_file']['error']==0) {
	//取出班級名稱對照
	$curr_year=curr_year();
	$curr_semester=curr_seme();
	$query="select c_year,c_sort,c_name from school_class where year=$curr_year and semester=$curr_semester and enable='1' order by c_sort";
	$res=$CONN->Execute($query) or die("SQL錯誤<br>$query");
	$class_arr=array();
	while(!$res->EOF) {
		$cyear=$res->fields['c_year'];
		$csort=$res->fields['c_sort'];
		$class_arr[$cyear][$csort]=$res->fields['c_name'];
		$res->MoveNext();
	}
	
	if($class_arr) {
		//取出 csv 的值
		$fp=fopen($file_name,"r");
		$tt=sfs_fgetcsv($fp, 2000, ",");
		$vs=array();
		chk_data($tt);

		//取出現有編班資料
		$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
		$query="select distinct (mid(seme_class,1,1)) as num from stud_seme where seme_year_seme='$seme_year_seme' group by num order by num";
		$res=$CONN->Execute($query) or die("SQL錯誤<br>$query");
		$err_arr=array();
		while(!$res->EOF) {
			$err_arr[$res->fields['num']]=$res->fields['num'];
			$res->MoveNext();
		}

		//開始匯入
		$msg="";
		while ($tt=sfs_fgetcsv($fp, 2000, ",")) {
			if ($tt[$vs[1]]<$IS_JHORES) $tt[$vs[1]]+=$IS_JHORES;
			$stud_id=$tt[$vs[0]];
			if ($err_arr[$tt[$vs[1]]]) $msg.=$err_arr[$tt[$vs[1]]]."年級已有資料, 學號 $stud_id 學生之編班資料不予匯入.<br>";
			else {
				$query="select * from stud_base where stud_id='$stud_id' and stud_study_cond='0' order by stud_study_year desc";
				$res=$CONN->Execute($query) or die("SQL錯誤<br>$query");
				if ($res->RecordCount()==0) $msg.="系統中找不到學號 $stud_id 學生之基本資料, 編班資料不予匯入.<br>";
				else {
					$student_sn=$res->fields['student_sn'];
					$cyear=intval($tt[$vs[1]]);
					$csort=intval(substr($tt[$vs[2]],-2));
					//檢查年級與班級資料的正確性
					if($cyear and $csort)
					{
						if($class_arr[$cyear][$csort])
						{
							$seme_class=$cyear.sprintf("%02d",$csort);
							$seme_num=intval($tt[$vs[3]]);
							$curr_class_num=$seme_class.sprintf("%02d",$seme_num);
							$seme_class_name=$class_arr[$cyear][$csort];
							$query="insert stud_seme(seme_year_seme,student_sn,stud_id,seme_class,seme_num,seme_class_name) values ('$seme_year_seme','$student_sn','$stud_id','$seme_class','$seme_num','$seme_class_name')";
							$res=$CONN->Execute($query) or die("SQL錯誤<br>$query");
							$msg.="學號 $stud_id 學生之編班資料寫入 $seme_class 班 $seme_num 號 OK!<br>";
							$query="update stud_base set curr_class_num='$curr_class_num' where student_sn='$student_sn'";
							$res=$CONN->Execute($query) or die("SQL錯誤<br>$query");
						} else $msg.="學號 $stud_id 學生之編班班級名稱尚未於學期初設定的班級設定中定義!<br>";
					} else $msg.="學號 $stud_id 學生之編班資料 年級與班級代號有誤!<br>";
				}
			}
		}
	} else echo "本學期尚未進行編班設定，無法匯入！";
	fclose($fp);
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","編班資料匯入");
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("msg",$msg);
$smarty->display("class_import.tpl");

function chk_data($kk) {
	global $vs;
	
	reset($kk);
	while (list($k,$v)=each($kk)) {
		$v=trim($v);
		switch ($v) {
			case "學號":
				$vs[0]=$k;
				break;
			case "年級":
				$vs[1]=$k;
				break;
			case "班級":
				$vs[2]=$k;
				break;
			case "座號":
				$vs[3]=$k;
				break;
			default:
				break;
		}
	}
}
?>
