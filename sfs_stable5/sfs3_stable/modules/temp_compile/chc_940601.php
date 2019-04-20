<?php
//$Id: chc_940601.php 9256 2018-05-26 09:41:19Z chiming $
/*引入學務系統設定檔*/
require "config.php";
//require "../../include/sfs_core_schooldata.php";
//秀出網頁布景標頭

sfs_check();
////------輸出編班中心要的檔案--------////
if ($_POST && $_POST[class_num]!=''){
	if (strlen($_POST[class_num]) > 3 )  backe("所填班級數過多！請修改後再送出！");
	if ($_POST[teacher]!='') {
	$teacher=ereg_replace("\n", "", $_POST[teacher]);
	$teacher=ereg_replace("\r", "",$teacher);
	$teacher=explode(',',$teacher);
	if (count($teacher)!=$_POST[class_num]) backe("所填教師數".count($teacher)."人與班級數不符！請修改後再送出！");
	foreach($teacher as $name ){
		if (strlen($name)< 4 )  backe("部分教師姓名空缺！");
		}
		//國小
		if(!empty($_POST['AA'])) get_stu_out($_POST[class_num],$teacher);
        //國中
		if(!empty($_POST['AA-1'])) get_stu_out($_POST[class_num],$teacher,'',$limit_stud_kind="and stud_kind !='1'");

	}
	else{
		//國小
        if(!empty($_POST['AA'])) get_stu_out($_POST[class_num]);
        //國中
        if(!empty($_POST['AA-1'])) get_stu_out($_POST[class_num],'','',$limit_stud_kind="and stud_kind !='1'");

	}
	
	exit;
}


////------輸入編班中心產生的檔案,再做更新處理--------////

if ($_POST[uptype]=='sum_grade' and $_FILES[upfile][error]==0) {
	$chk=explode("_",$_FILES[upfile][name]);// => 95_000000_20060709_OK.csv
	$school_info=get_school_base();
	$sch_id=$school_info[sch_id];//學校編號
	$now_year=$chk[0];//學年度
	if($chk[1]!=$sch_id) backe2("非本校編號".$sch_id."檔案，拒絕輸入！");
	if( $chk[3]!='OK.csv') backe2("非編班中心處理過的檔案，拒絕輸入！");

	$LineArray = file($_FILES[upfile][tmp_name]);
//	if( !ereg('"',$LineArray[0])) echo "以explode模式";

//含雙引號的處理模式
	if( ereg('"',$LineArray[0])) {
		unset($LineArray);
		$arys=array('流水號','班級','座號','身分証字號');
		$handle=fopen($_FILES[upfile][tmp_name], "r");
		$csv_head = sfs_fgetcsv($handle, 1000, ","); //-- 第1列的資料丟棄
		$csv_head = sfs_fgetcsv($handle, 1000, ","); //-- 第2列導師
		$csv_head = sfs_fgetcsv($handle, 1000, ","); //-- 第3列欄位標題
		foreach ($arys as $fld) {
			$pos = array_search($fld, $csv_head);
			if ($pos !== false) $csv_get[$fld]=$pos;
			}
			$count_update=0;
		while (($data = sfs_fgetcsv($handle, 1000, ",")) !== FALSE) {
			$stud = array();
			$temp_id=$stud[temp_id]=trim($data[$csv_get[流水號]]);
			$class_year=$stud[class_year]=$IS_JHORES+1;
			$class_sort=$stud[class_sort]=trim($data[$csv_get[班級]]);
			$class_site=$stud[class_site]=trim($data[$csv_get[座號]]);
			$person_id=$stud[person_id]=trim($data[$csv_get[身分証字號]]);
			//-- 資料完整才存入資料表中 new_stud
			if (!empty($temp_id) and !empty($class_sort) and !empty($class_site) and !empty($person_id)) {
				$SQL = " update new_stud set class_year='{$class_year}',class_sort='{$class_sort}', class_site='{$class_site}' where temp_id='{$temp_id}' and stud_study_year='{$now_year}' and  stud_person_id='{$person_id}'";
				$rs = $CONN->Execute($SQL) or die($SQL);
				$count_update++;
			}
		}
		fclose($handle);
	} else {

	//不含雙引號的處理模式
		$Stu = array_slice($LineArray,3);
		$class_year=$IS_JHORES+1;//決定年級
		$count_update=0;
		foreach ($Stu as $line ){
			if( !ereg(',',$line)) continue;//去除空白行
			$stu_tmp = explode(",",$line);
			$temp_id=trim($stu_tmp[0]);//臨時編號
			$class_sort=$stu_tmp[1];//班級
			$class_site=$stu_tmp[2];//座號
			$person_id=trim($stu_tmp[5]);
			if (!empty($temp_id) and !empty($class_sort) and !empty($class_site) and !empty($person_id)) {
				$SQL = "update new_stud set class_year='{$class_year}',class_sort='{$class_sort}', class_site='{$class_site}' where temp_id='{$temp_id}' and stud_study_year='{$now_year}' and  stud_person_id='{$person_id}'";
				$rs = $CONN->Execute($SQL) or die($SQL);
				$count_update++;
				}
		}
	}
	echo "<HTML><HEAD><META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=big5\"></HEAD><BODY>\n";
	echo"<BR><BR><BR><BR><CENTER><form>";
	echo "<input type='button' name='b1' value='共更新了 $count_update 筆資料\n 按下後繼續' onclick=\"location.href='".$_SERVER[PHP_SELF]."'\" style='font-size:16pt;color:red'></form></CENTER>";
	die();
}


head("新生編班");
print_menu($menu_p);
##################陣列列示函式2##########################
// 1.smarty物件
//$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
$template_file= $SFS_PATH."/".get_store_path()."/chc_940601.htm";
$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
$smarty->display($template_file);
foot();


##################  輸出CSV函式  ##########################

function get_stu_out($class_num,$teach_ary='',$yy='',$limit_stud_kind=''){
	global $CONN ;
	if ($yy=='') $yy=curr_year()+1;
	$school_info=get_school_base();
	$sch_id=$school_info[sch_id];
	$SQL="select * from  new_stud where  stud_study_year='$yy' 
	and  sure_study='1' and  temp_id !='' {$limit_stud_kind} order by  temp_id ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0)  die("查無任何新生資料:".$SQL);
	$All=$rs->GetArray();
	$nn=array("流水號","班級","座號","性別","姓名","身分証字號","原就讀學校","編班類別","相關流水號","備註");
	$Str="校名,".$school_info[sch_cname_s].",班級數,".$class_num.",總人數,".$rs->RecordCount()."\n";
	if ($teach_ary==''){
		$Str.="\n";
		}else{
		$Str.=join(',',$teach_ary)."\n";
	}


	$Str.="流水號,班級,座號,性別,姓名,身分証字號,原就讀學校,編班類別,相關流水號,備註\n";
	for ($i=0;$i<$rs->RecordCount();$i++){
	if ($All[$i][stud_kind]=='') $All[$i][stud_kind]='0';
		$Str.=$All[$i][temp_id].',,,'.
			$All[$i][stud_sex].','.
			$All[$i][stud_name].','.
			$All[$i][stud_person_id].','.
			$All[$i][old_school].','.
			$All[$i][stud_kind].','.
			$All[$i][bao_id].",\n";
	}

	$filename = $yy."_".$sch_id."_".date("Ymd").".csv";

header("Content-disposition: attachment; filename=$filename");
header("Content-type: text/x-csv");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
header("Expires: 0");
echo $Str;
}

##################回上頁函式1#####################

function backe($st="未填妥!按下後回上頁重填!") {
echo"<BR><BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"window.close()\" style='font-size:18pt;color:red'>
	</form></CENTER>";
	exit;
	}
function backe2($st="未填妥!按下後回上頁重填!") {
	echo "<HTML><HEAD><META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=big5\">
<TITLE>$st</TITLE></HEAD><BODY background='images/bg.jpg'>\n";
echo"<BR><BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}

?>
