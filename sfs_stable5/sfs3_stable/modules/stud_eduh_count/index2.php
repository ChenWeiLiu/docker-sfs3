<?php

// $Id: index2.php 9211 2018-03-16 02:26:22Z smallduh $

include "config.php";
require "../../include/sfs_oo_zip2.php";
include_once "../../include/sfs_case_dataarray.php";
//認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//班級陣列
$class_arr = class_base();
$postBtn = "確定";
$postBtn_xls_online = "線上顯示";

if ($_REQUEST['do_key'] <> $postBtn)
	head("94輔導資料表");
//不需iconv 轉換陣列
$no_iconv_arr = array();

$ttt = new EasyZip;
$ttt->setPath('ooo2');

if (count ($sel_stud) >0 )
switch($do_key) {
	case $postBtn:
	$break ="<text:p text:style-name=\"P14\"/>";
	$doc_head = $ttt->read_file (dirname(__FILE__)."/ooo2/con_head");
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/ooo2/con_foot");
	$doc_main = $ttt->read_file(dirname(__FILE__)."/ooo2/con_main");
	$doc_brother_sister = $ttt->read_file(dirname(__FILE__)."/ooo2/brother_sister");
	$doc_sss_data = $ttt->read_file (dirname(__FILE__)."/ooo2/sss_data");
	$doc_sse_list_memo = $ttt->read_file (dirname(__FILE__)."/ooo2/sse_list_memo");
	$doc_sse_list_spe = $ttt->read_file (dirname(__FILE__)."/ooo2/sse_list_spe");


	$ttt->adddir("META-INF");
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

	//血型
	$blood_arr = blood();
	//出生地
	$birth_state_arr = birth_state();
	//性別
	$sex_arr = array("1"=>"男","2"=>"女");
	//存歿
	$is_live_arr = is_live();
	//與監護人關係
	$guardian_relation_arr = guardian_relation();
	//學歷
	$edu_kind_arr  = edu_kind();
	//學生身分別
	$stud_kind_arr = stud_kind();
	//與監護人關係
	$guardian_relation_arr = guardian_relation();
	//稱謂
	$bs_calling_kind_arr = bs_calling_kind();
	//父母關係
	$sse_relation_arr = sfs_text("父母關係");
	while(list($id,$val)= each($sse_relation_arr))
		$sse_relation_str .= "$id-$val,";
	//家庭類型
	$sse_family_kind_arr = sfs_text("家庭類型");
	while(list($id,$val)= each($sse_family_kind_arr))
		$sse_family_kind_str .= "$id-$val,";
	//家庭氣氛
	$sse_family_air_arr = sfs_text("家庭氣氛");
	while(list($id,$val)= each($sse_family_air_arr))
		$sse_family_air_str .= "$id-$val,";
	//管教方式
	$sse_farther_arr = sfs_text("管教方式");
	while(list($id,$val)= each($sse_farther_arr))
		$sse_farther_str .= "$id-$val,";

	//居住情形
	$sse_live_state_arr = sfs_text("居住情形");
	while(list($id,$val)= each($sse_live_state_arr))
		$sse_live_state_str .= "$id-$val,";
	//經濟狀況
	$sse_rich_state_arr = sfs_text("經濟狀況");
	while(list($id,$val)= each($sse_rich_state_arr))
		$sse_rich_state_str .= "$id-$val,";

	$sse_arr= array("1"=>"喜愛困難科目","2"=>"喜愛困難科目","3"=>"特殊才能","4"=>"興趣","5"=>"生活習慣","6"=>"人際關係","7"=>"外向行為","8"=>"內向行為","9"=>"學習行為","10"=>"不良習慣","11"=>"焦慮行為");

	while(list($id,$val)= each($sse_arr)){
		$temp_sse_arr = sfs_text("$val");
		${"sse_arr_$id"} = $temp_sse_arr;
		$temp_str ='';
		while(list($idd,$vall)= each($temp_sse_arr))
			$temp_str .= "$idd-$vall,";
		${"sse_str_$id"} = $temp_str;
	}

	//列印時間
	$print_time = $now;


	$temp_arr["sch_cname"]= $sch_cname;

	$sql_select = "select a.*,b.fath_name,b.fath_birthyear,b.fath_alive,b.fath_education,b.fath_occupation,b.fath_unit,b.fath_phone,b.fath_work_name,b.fath_hand_phone,b.moth_name,b.moth_birthyear,moth_work_name,b.moth_alive,b.moth_education,b.moth_occupation,b.moth_unit,b.moth_phone,b.moth_hand_phone,b.guardian_name,b.guardian_relation,b.guardian_unit,b.guardian_hand_phone,b.guardian_phone,b.guardian_address,b.grandfath_name,b.grandfath_alive,b.grandmoth_name,b.grandmoth_alive  from stud_base a left join stud_domicile b on a.student_sn=b.student_sn ";
	for ($ss=0;$ss < count ($sel_stud);$ss++)
		$temp_sel .= "'".$sel_stud[$ss]."',";
	$sql_select .= "where a.stud_id in (".substr($temp_sel,0,-1).") ";

	$sql_select .= " order by a.curr_class_num ";
	$recordSet = $CONN->Execute($sql_select)or die ($sql_select);
	$i =0;
	$data = '';

	while (!$recordSet->EOF) {
		$stud_id = $recordSet->fields["stud_id"];
		$student_sn = $recordSet->fields["student_sn"];
		$stud_name = $recordSet->fields["stud_name"];
		$stud_sex = $recordSet->fields["stud_sex"];
		$stud_birthday = $recordSet->fields["stud_birthday"];
		$stud_blood_type = $recordSet->fields["stud_blood_type"];
		$stud_birth_place = $recordSet->fields["stud_birth_place"];
		$stud_kind = $recordSet->fields["stud_kind"];
		$stud_country = $recordSet->fields["stud_country"];
		$stud_country_kind = $recordSet->fields["stud_country_kind"];
		$stud_person_id = $recordSet->fields["stud_person_id"];
		$stud_country_name = $recordSet->fields["stud_country_name"];
		$stud_addr_1= $recordSet->fields["stud_addr_1"];
		$stud_addr_2 = $recordSet->fields["stud_addr_2"];
		$stud_tel_1 = $recordSet->fields["stud_tel_1"];
		$stud_tel_2 = $recordSet->fields["stud_tel_2"];
		$stud_tel_3 = $recordSet->fields["stud_tel_3"];
		$stud_mail = $recordSet->fields["stud_mail"];
		$stud_class_kind = $recordSet->fields["stud_class_kind"];
		$stud_spe_kind = $recordSet->fields["stud_spe_kind"];
		$stud_spe_class_kind = $recordSet->fields["stud_spe_class_kind"];
		$stud_spe_class_id = $recordSet->fields["stud_spe_class_id"];
		$stud_preschool_status = $recordSet->fields["stud_preschool_status"];
		$stud_preschool_id = $recordSet->fields["stud_preschool_id"];
		$stud_preschool_name = $recordSet->fields["stud_preschool_name"];
		$stud_mschool_status = $recordSet->fields["stud_mschool_status"];
		$stud_mschool_id = $recordSet->fields["stud_mschool_id"];
		$stud_mschool_name = $recordSet->fields["stud_mschool_name"];
		$stud_study_year = $recordSet->fields["stud_study_year"];
		$curr_class_num = $recordSet->fields["curr_class_num"];
		$fath_name = $recordSet->fields["fath_name"];
		$fath_birthyear = $recordSet->fields["fath_birthyear"];
		$fath_alive = $recordSet->fields["fath_alive"];
		$fath_education = $recordSet->fields["fath_education"];
		$fath_occupation = $recordSet->fields["fath_occupation"];
		$fath_work_name = $recordSet->fields["fath_work_name"];
		$fath_unit = $recordSet->fields["fath_unit"];
		$fath_phone = $recordSet->fields["fath_phone"];
		$fath_hand_phone = $recordSet->fields["fath_hand_phone"];
		$moth_name = $recordSet->fields["moth_name"];
		$moth_birthyear = $recordSet->fields["moth_birthyear"];
		$moth_alive = $recordSet->fields["moth_alive"];
		$moth_relation = $recordSet->fields["moth_relation"];
		$moth_education = $recordSet->fields["moth_education"];
		$moth_occupation = $recordSet->fields["moth_occupation"];
		$moth_work_name = $recordSet->fields["moth_work_name"];
		$moth_unit = $recordSet->fields["moth_unit"];
		$moth_work_name = $recordSet->fields["moth_work_name"];
		$moth_phone = $recordSet->fields["moth_phone"];
		$moth_hand_phone = $recordSet->fields["moth_hand_phone"];
		$guardian_name = $recordSet->fields["guardian_name"];
		$guardian_phone = $recordSet->fields["guardian_phone"];
		$guardian_relation = $recordSet->fields["guardian_relation"];
		$guardian_unit = $recordSet->fields["guardian_unit"];
		$guardian_work_name = $recordSet->fields["guardian_work_name"];
		$guardian_hand_phone = $recordSet->fields["guardian_hand_phone"];
		$guardian_guardian_address = $recordSet->fields["guardian_address"];
		$grandfath_name = $recordSet->fields["grandfath_name"];
		$grandfath_alive = $recordSet->fields["grandfath_alive"];
		$grandmoth_name = $recordSet->fields["grandmoth_name"];
		$grandmoth_alive = $recordSet->fields["grandmoth_alive"];

		//學生身分別
		$stud_kind_temp='';
		$stud_kind_temp_arr = explode(",",$stud_kind);
		for ($iii=0;$iii<count($stud_kind_temp_arr);$iii++) {
			if ($stud_kind_temp_arr[$iii]<>'')
				$stud_kind_temp .= $stud_kind_arr[$stud_kind_temp_arr[$iii]].",";
		}

		$temp_arr["stud_kind"]= substr($stud_kind_temp,0,-1);


		//學生基本資料
		$bir_temp_arr = explode("-",DtoCh($stud_birthday));
		$temp_arr["stud_birthday"]=sprintf("民國%d年%d月%d日",$bir_temp_arr[0],$bir_temp_arr[1],$bir_temp_arr[2]);
		$temp_arr["stud_blood_type"]=$blood_arr[$stud_blood_type];
		$temp_arr["stud_sex"]=$sex_arr[$stud_sex];
		$temp_arr["stud_name"]=$stud_name;
		$temp_arr["stud_id"]=$stud_id;
		$temp_arr["study_begin_date"]=$study_begin_date;
		$temp_arr["stud_person_id"]=$stud_person_id;
		$temp_arr["stud_birth_place"]=$birth_state_arr[sprintf("%02d",$stud_birth_place)];
		$temp_arr["curr_year"]= Num2CNum(substr($curr_class_num,0,1));
		$temp_arr["curr_class"] = $class_name[substr($curr_class_num,1,2)];
		$temp_arr["curr_num"] = intval(substr($curr_class_num,-2))."號";
		$temp_arr["sch_cname"] = $SCHOOL_BASE[sch_cname];
		$temp_arr["stud_addr_1"] = $stud_addr_1;
		$temp_arr["stud_addr_2"] = $stud_addr_2;
		$temp_arr["stud_tel_1"] = $stud_tel_1;
		$temp_arr["stud_tel_2"] = $stud_tel_2;

		//直系血親
		$temp_arr[stud_parent] = "父: $fath_name($is_live_arr[$fath_alive])($fath_birthyear 生), 母:$moth_name($is_live_arr[$moth_alive])($moth_birthyear 生), 祖父:$grandfath_name($is_live_arr[$grandfath_alive]), 祖母:$grandmoth_name($is_live_arr[$grandmoth_alive])";

		//父母教育程度
		$temp_arr[stud_parent_edu]= "父 :$edu_kind_arr[$moth_education] ,母: $edu_kind_arr[$moth_education]";

		//監護人
		$temp_arr["aaaa"]= $guardian_name;
		$temp_arr["bbbb"]= $guardian_relation_arr[$guardian_relation];
		$temp_arr["cccc"]= $guardian_address;
		$temp_arr["dddd"]= "$guardian_phone $guardian_hand_phone";

		//家長
		$temp_arr[f_1]=$fath_name;
		$temp_arr[f_2]=$fath_occupation;
		$temp_arr[f_3]=$fath_work_name;
		$temp_arr[f_4]=$fath_unit;
		$temp_arr[f_5]=$fath_phone;
		$temp_arr[m_1]=$moth_name;
		$temp_arr[m_2]=$moth_occupation;
		$temp_arr[m_3]=$moth_work_name;
		$temp_arr[m_4]=$moth_unit;
		$temp_arr[m_5]=$moth_phone;

		$temp_arr[stud_study_year] = $stud_study_year;
		//兄弟姐妹
		$query = "select * from stud_brother_sister where student_sn='$student_sn' order by bs_birthyear";
		$bs_res = $CONN->Execute($query);

		$bs_data = '';
		$bs_arr = array();
		if($bs_res->EOF) {
			$bs_arr[b_1] = "-";
			$bs_arr[b_2] = "-";
			$bs_arr[b_3] = "-";
			$bs_arr[b_4] = "-";
			$bs_data .= change_temp($bs_arr,array(),$doc_brother_sister);

		}
		else {
			while(!$bs_res->EOF){
				$bs_arr[b_1] = $bs_calling_kind_arr[$bs_res->fields[bs_calling]];
				$bs_arr[b_2] = $bs_res->fields[bs_name];
				$bs_arr[b_3] = $bs_res->fields[bs_gradu];
				$bs_arr[b_4] = $bs_res->fields[bs_birthyear];
				$bs_data .= change_temp($bs_arr,array(),$doc_brother_sister);
				$bs_res->MoveNext();
			}
		}
		$temp_arr[brother_sister] = $bs_data;

		//取得學生輔導資料
		$stud_seme_arr = array();
		$sql_select = "select seme_year_seme,stud_id,sse_relation,sse_family_kind,sse_family_air,sse_farther,sse_mother,sse_live_state,sse_rich_state,sse_s1,sse_s2,sse_s3,sse_s4,sse_s5,sse_s6,sse_s7,sse_s8,sse_s9,sse_s10,sse_s11 from stud_seme_eduh where stud_id='$stud_id' order by seme_year_seme";
		$res_seme = $CONN->Execute($sql_select);
		while(!$res_seme->EOF){
			$temp_seme = $res_seme->fields[seme_year_seme];
			$stud_seme_arr[$temp_seme][sse_relation] = $res_seme->fields[sse_relation];
			$stud_seme_arr[$temp_seme][sse_family_kind] = $res_seme->fields[sse_family_kind];
			$stud_seme_arr[$temp_seme][sse_family_air] = $res_seme->fields[sse_family_air];
			$stud_seme_arr[$temp_seme][sse_farther] = $res_seme->fields[sse_farther];
			$stud_seme_arr[$temp_seme][sse_mother] = $res_seme->fields[sse_mother];
			$stud_seme_arr[$temp_seme][sse_live_state] = $res_seme->fields[sse_live_state];
			$stud_seme_arr[$temp_seme][sse_rich_state] = $res_seme->fields[sse_rich_state];
			$stud_seme_arr[$temp_seme][sse_s1] = $res_seme->fields[sse_s1];
			$stud_seme_arr[$temp_seme][sse_s2] = $res_seme->fields[sse_s2];
			$stud_seme_arr[$temp_seme][sse_s3] = $res_seme->fields[sse_s3];
			$stud_seme_arr[$temp_seme][sse_s4] = $res_seme->fields[sse_s4];
			$stud_seme_arr[$temp_seme][sse_s5] = $res_seme->fields[sse_s5];
			$stud_seme_arr[$temp_seme][sse_s6] = $res_seme->fields[sse_s6];
			$stud_seme_arr[$temp_seme][sse_s7] = $res_seme->fields[sse_s7];
			$stud_seme_arr[$temp_seme][sse_s8] = $res_seme->fields[sse_s8];
			$stud_seme_arr[$temp_seme][sse_s9] = $res_seme->fields[sse_s9];
			$stud_seme_arr[$temp_seme][sse_s10] = $res_seme->fields[sse_s10];
			$stud_seme_arr[$temp_seme][sse_s11] = $res_seme->fields[sse_s11];
			$res_seme->MoveNext();
		}


		//父母關係
		$bs_data ='';
		$bs_arr = array();
		$sssss ='';
		$no_iconv_arr[sse_list]=1;
		$no_iconv_arr[sss_data] =1; //不需轉換
		$no_iconv_arr[sse_memo_list] =1; //不需轉換
		$no_iconv_arr[sse_list_spe] =1; //不需轉換
		$no_iconv_arr[brother_sister] =1; //不需轉換

		reset($stud_seme_arr);
		$sssss ='<text:p text:style-name="P8">';
		while(list($vid,$vval) = each($stud_seme_arr)){
			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_relation_arr[$vval[sse_relation]]."</text:span>)";
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		}
		$sssss .='</text:p>';

		$bs_arr[sse_kind] = "父母關係";
		$bs_arr[sse_detail] = $sse_relation_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data = change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);

		//=================================
		reset($stud_seme_arr);
		$sssss ='<text:p text:style-name="P8">';
		while(list($vid,$vval) = each($stud_seme_arr)){
			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_family_kind_arr[$vval[sse_family_kind]]."</text:span>)";
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		}
		$sssss .='</text:p>';

		$bs_arr[sse_kind] = "家庭類型";
		$bs_arr[sse_detail] = $sse_family_kind_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);

		//=================================
		reset($stud_seme_arr);
		$sssss ='<text:p text:style-name="P8">';
		while(list($vid,$vval) = each($stud_seme_arr)){
			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_family_air_arr[$vval[sse_family_kind]]."</text:span>)";
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		}
		$sssss .='</text:p>';

		$bs_arr[sse_kind] = "家庭氣氛";
		$bs_arr[sse_detail] = $sse_family_air_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);

		//=================================
		reset($stud_seme_arr);
		$sssss ='<text:p text:style-name="P8">';
		while(list($vid,$vval) = each($stud_seme_arr)){
			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_farther_arr[$vval[sse_farther]]."</text:span>)";
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		}
		$sssss .='</text:p>';

		$bs_arr[sse_kind] = "父管教方式";
		$bs_arr[sse_detail] = $sse_farther_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);

		//=================================
		reset($stud_seme_arr);
		$sssss ='<text:p text:style-name="P8">';
		while(list($vid,$vval) = each($stud_seme_arr)){
			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_farther_arr[$vval[sse_mother]]."</text:span>)";
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		}
		$sssss .='</text:p>';

		$bs_arr[sse_kind] = "母管教方式";
		$bs_arr[sse_detail] = $sse_farther_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);

                //by misser 修改1 (刪除) ,因和上面重複
                //=================================
                /*
		reset($stud_seme_arr);
		$sssss ='<text:p text:style-name="P8">';
		while(list($vid,$vval) = each($stud_seme_arr)){
			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_farther_arr[$vval[sse_mother]]."</text:span>)";
			$sssss .= iconv("Big5","UTF-8",$temp_ss).", ";
		}
		$sssss .='</text:p>';

		$bs_arr[sse_kind] = "母管教方式";
		$bs_arr[sse_detail] = $sse_farther_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
                //by misser 修改1 結束
                */

		//=================================
		reset($stud_seme_arr);
		$sssss ='<text:p text:style-name="P8">';
		while(list($vid,$vval) = each($stud_seme_arr)){
			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_live_state_arr[$vval[sse_live_state]]."</text:span>)";
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		}
		$sssss .='</text:p>';

		$bs_arr[sse_kind] = "居住情形";
		$bs_arr[sse_detail] = $sse_live_state_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);


		//=================================
		reset($stud_seme_arr);
		$sssss ='<text:p text:style-name="P8">';
		while(list($vid,$vval) = each($stud_seme_arr)){
			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_rich_state_arr[$vval[sse_rich_state]]."</text:span>)";
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		}
		$sssss .='</text:p>';

		$bs_arr[sse_kind] = "經濟狀況";
		$bs_arr[sse_detail] = $sse_rich_state_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);

		$temp_arr[sss_data] = $bs_data;

		//=================================
		$bs_data = '';
		for($si=1;$si<=11;$si++){

			reset($stud_seme_arr);
			$sssss ='<text:p text:style-name="P8">';
			while(list($vid,$vval) = each($stud_seme_arr)){
				$temp_sse_arr = ${"sse_arr_$si"};
				$temp_str = ${"sse_str_$si"};
				$temp_id  = "sse_s$si";
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$tt_arr = explode(",",$vval[$temp_id]);
				$temp_ss='';
				foreach ($tt_arr as $VAL){
					if ($VAL<>'')
						$temp_ss .= $temp_sse_arr[$VAL].",";
				}
				if($temp_ss<>'')
					$temp_ss = substr($temp_ss,0,-1);
				$temp_ss = $seme_name.'(<text:span text:style-name="T6">'.$temp_ss.'</text:span>)';
				$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			}
			$sssss .='</text:p>';
			if($si==1)
				$bs_arr[sse_kind] = "最喜愛科目";
			else if($si==2)
				$bs_arr[sse_kind] = "最困難科目";
			else
				$bs_arr[sse_kind] = $sse_arr[$si];
			$bs_arr[sse_detail] = $temp_str;
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
		}
                $temp_arr[sss_data] .= $bs_data; //by misser,修改2 ,原本少 .，造成上面資料(家庭狀況等)未顯示

                //以下 by misser,修改3(新增) ,取得出缺席紀錄 ,原無
                //=================================
                if ($IS_JHORES==6){//國中，6學期
                   $stud_seme_new_arr=array("$stud_study_year"."1","$stud_study_year"."2","$stud_study_year"+"1"."1","$stud_study_year"+"1"."2","$stud_study_year"+"2"."1","$stud_study_year"+"2"."2");
                }
                else{//應該是小學，所以有12學期
                   $stud_seme_new_arr=array("$stud_study_year"."1","$stud_study_year"."2","$stud_study_year"+"1"."1","$stud_study_year"+"1"."2","$stud_study_year"+"2"."1","$stud_study_year"+"2"."2","$stud_study_year"+"3"."1","$stud_study_year"+"3"."2","$stud_study_year"+"4"."1","$stud_study_year"+"4"."2","$stud_study_year"+"5"."1","$stud_study_year"+"5"."2");
                }
                $bs_data = '';
		//reset($stud_seme_arr); 不用$stud_seme_arr，因為若某學期尚未建輔導紀錄，則
		//$stud_seme_arr就會少了某學期的資料，也就無法找出該學期的缺曠課。
		//所以用 上面的 $stud_seme_new_arr 代替。
		//以下獎懲亦同

                //取得假別
                $asb_arr=sfs_text("缺曠課類別");
        	while(list($id,$val)= each($asb_arr))
        		$asb_str .= "$id-$val,";

		//取得absent資料表中出缺席紀錄
		$sssss ='<text:p text:style-name="P8">';
		$temp_ss='';
                //下面$vval及$vid 位置和原先是對調的
                while(list($vval,$vid) = each($stud_seme_new_arr)){//依學期別
                        $year=(substr($vid,0,1)=='0')?substr($vid,1,-1):substr($vid,0,-1);
                        $this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss .= $seme_name.'(<text:span text:style-name="T6">';
			foreach ($asb_arr as $temp_kind){//再依假別尋找次數
       		            $sql_select = "select * from stud_absent where stud_id='$stud_id' and absent_kind='$temp_kind' and year='$year' and semester='$semester' order by year,semester";
                            $record=$CONN->Execute($sql_select) or die($sql_select);
                            $num=$record->RecordCount();
                            if ($num>0){;//如果找到，則傳回假別次數
                                $temp_ss.=$temp_kind.":".$num."節。 ";
                            }
                        }
                        $temp_ss.='</text:span>)';

		}
		$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		$sssss .='</text:p>';
		$bs_arr[sse_kind] = "缺曠課紀錄";
		$bs_arr[sse_detail] = $asb_str;
		$bs_arr[sse_list] = $sssss;
		$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
                $temp_arr[sss_data] .= $bs_data;
                // by misser 修改3(新增) 結束

                //以下 by misser,修改4(新增) ,取得獎懲紀錄 ,原無
                //=================================
                if ($IS_JHORES==6){//限國中，國小不用獎懲
  		   $bs_data = '';
		   reset($stud_seme_new_arr);
		   //取得獎懲類別 ,取自 reward 模組的config.php
            	$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
                   while(list($id,$val)= each($reward_arr))
        		$reward_str .= "$id-$val,";

		   //取得reward資料表中獎懲紀錄
		   $sssss ='<text:p text:style-name="P8">';
		   $temp_ss='';

		   while(list($vval,$vid) = each($stud_seme_new_arr)){
		        $year=(substr($vid,0,1)=='0')?substr($vid,1,-1):substr($vid,0,-1);
                        $this_year = (substr($vid,0,-1)-$stud_study_year)+1;
			$semester = substr($vid,-1);
			$seme_name = ($semester==1)?"上":"下";
			$seme_name = Num2CNum($this_year).$seme_name;
			$temp_ss .= $seme_name.'(<text:span text:style-name="T6">';

                        $sql_select = "select * from reward where stud_id='$stud_id' and reward_year_seme='$year$semester' order by reward_date";
                        $re_record=$CONN->Execute($sql_select) or die($sql_select);

                        while(!$re_record->EOF){
                                $temp_ss.= $reward_arr[$re_record->fields[reward_kind]];
                                $temp_ss.=":";
                                $temp.=$re_record->fields[reward_reason];
                                if ($re_record->fields[reward_cancel_date]!="" and $re_record->fields[reward_cancel_date]!="0000-00-00")
                                   $temp_ss.="**已銷過**";

                                $temp_ss.="　,";
                                $re_record->MoveNext();
                        }

                        $temp_ss.="</text:span>)";
		   }
		   $sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
		   $sssss .='</text:p>';
		   $bs_arr[sse_kind] = "獎懲紀錄";
		   $bs_arr[sse_detail] = $reward_str;
		   $bs_arr[sse_list] = $sssss;
		   $bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
                   $temp_arr[sss_data] .= $bs_data;
		}
                   // by misser 修改4(新增) 結束


		$bs_data = '';
		//輔導訪談記錄
		$query = "select seme_year_seme,sst_date,sst_name,sst_main,sst_memo,teach_id from stud_seme_talk where stud_id='$stud_id' order by seme_year_seme";
		$res_talk = $CONN->Execute($query) or die($query);
		$memo_arr = array();
		while(!$res_talk->EOF){
			$memo_arr[w_2]= $res_talk->fields[sst_date];
			$memo_arr[w_3]= $res_talk->fields[sst_name];
			$memo_arr[w_4]= $res_talk->fields[sst_main].":".$res_talk->fields[sst_memo];
			$memo_arr[w_5]= get_teacher_name($res_talk->fields[teach_id]);
			$this_year = (substr($res_talk->fields[seme_year_seme],0,-1)-$stud_study_year)+1;
			$semester = substr($res_talk->fields[seme_year_seme],-1);
			$seme_name = ($semester==1)?"上":"下";
			$memo_arr[w_1] = Num2CNum($this_year).$seme_name;
			$bs_data .= change_temp($memo_arr,array(),$doc_sse_list_memo);
			$res_talk->MoveNext();
		}
		;

		$temp_arr[sse_memo_list] = $bs_data;

		$bs_data = '';
		//特殊表現記錄
		$query = "select seme_year_seme,sp_date,sp_memo,teach_id from stud_seme_spe where stud_id='$stud_id' order by seme_year_seme";
		$res_talk = $CONN->Execute($query) or die($query);
		$memo_arr = array();
		while(!$res_talk->EOF){
			$memo_arr[s_2]= $res_talk->fields[sp_date];
			$memo_arr[s_3]= $res_talk->fields[sp_memo];
			$memo_arr[s_4]= get_teacher_name($res_talk->fields[teach_id]);
			$this_year = (substr($res_talk->fields[seme_year_seme],0,-1)-$stud_study_year)+1;
			$semester = substr($res_talk->fields[seme_year_seme],-1);
			$seme_name = ($semester==1)?"上":"下";
			$memo_arr[s_1] = Num2CNum($this_year).$seme_name;
			$bs_data .= change_temp($memo_arr,array(),$doc_sse_list_spe);
			$res_talk->MoveNext();
		}


		$temp_arr[sse_list_spe] = $bs_data;

		//入學學校 (尚未判斷國中小)
		$temp_arr["stud_mschool_name"]="";
		//畢業日期 (尚未判斷)
		$temp_arr["stud_grade_date"]="";
		//列印時間
		$temp_arr["print_time"]="列印時間: $now";
		$temp_arr["test_1"]="misser測試";
		//取代基本資料
		$data .= change_temp($temp_arr,$no_iconv_arr,$doc_main);

		$recordSet->MoveNext();
		//換頁
		if (!$recordSet->EOF)
			$data .= $break;
	}
	$sss = $doc_head.$data.$doc_foot;
	$ttt->add_file($sss,"content.xml");

	$sss = & $ttt->file();

	header("Content-disposition: attachment; filename=ooo2.sxw");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;

	exit;
	break;


	case $postBtn_xls_online:
	$break ="<text:p text:style-name=\"P14\"/>";
	$doc_head = $ttt->read_file (dirname(__FILE__)."/ooo2/con_head");
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/ooo2/con_foot");
	$doc_main = $ttt->read_file(dirname(__FILE__)."/ooo2/con_main");
	$doc_brother_sister = $ttt->read_file(dirname(__FILE__)."/ooo2/brother_sister");
	$doc_sss_data = $ttt->read_file (dirname(__FILE__)."/ooo2/sss_data");
	$doc_sse_list_memo = $ttt->read_file (dirname(__FILE__)."/ooo2/sse_list_memo");
	$doc_sse_list_spe = $ttt->read_file (dirname(__FILE__)."/ooo2/sse_list_spe");


	//血型
	$blood_arr = blood();
	//出生地
	$birth_state_arr = birth_state();
	//性別
	$sex_arr = array("1"=>"男","2"=>"女");
	//存歿
	$is_live_arr = is_live();
	//與監護人關係
	$guardian_relation_arr = guardian_relation();
	//學歷
	$edu_kind_arr  = edu_kind();
	//學生身分別
	$stud_kind_arr = stud_kind();
	//與監護人關係
	$guardian_relation_arr = guardian_relation();
	//稱謂
	$bs_calling_kind_arr = bs_calling_kind();
	//父母關係
	$sse_relation_arr = sfs_text("父母關係");
	while(list($id,$val)= each($sse_relation_arr))
		$sse_relation_str .= "$id-$val,";
	//家庭類型
	$sse_family_kind_arr = sfs_text("家庭類型");
	while(list($id,$val)= each($sse_family_kind_arr))
		$sse_family_kind_str .= "$id-$val,";
	//家庭氣氛
	$sse_family_air_arr = sfs_text("家庭氣氛");
	while(list($id,$val)= each($sse_family_air_arr))
		$sse_family_air_str .= "$id-$val,";
	//管教方式
	$sse_farther_arr = sfs_text("管教方式");
	while(list($id,$val)= each($sse_farther_arr))
		$sse_farther_str .= "$id-$val,";

	//居住情形
	$sse_live_state_arr = sfs_text("居住情形");
	while(list($id,$val)= each($sse_live_state_arr))
		$sse_live_state_str .= "$id-$val,";
	//經濟狀況
	$sse_rich_state_arr = sfs_text("經濟狀況");
	while(list($id,$val)= each($sse_rich_state_arr))
		$sse_rich_state_str .= "$id-$val,";

	$sse_arr= array("1"=>"喜愛困難科目","2"=>"喜愛困難科目","3"=>"特殊才能","4"=>"興趣","5"=>"生活習慣","6"=>"人際關係","7"=>"外向行為","8"=>"內向行為","9"=>"學習行為","10"=>"不良習慣","11"=>"焦慮行為");

	while(list($id,$val)= each($sse_arr)){
		$temp_sse_arr = sfs_text("$val");
		${"sse_arr_$id"} = $temp_sse_arr;
		$temp_str ='';
		while(list($idd,$vall)= each($temp_sse_arr))
			$temp_str .= "$idd-$vall,";
		${"sse_str_$id"} = $temp_str;
	}

	//列印時間
	$print_time = $now;


	$temp_arr["sch_cname"]= $sch_cname;

	$sql_select = "select a.*,b.fath_name,b.fath_birthyear,b.fath_alive,b.fath_education,b.fath_occupation,b.fath_unit,b.fath_phone,b.fath_work_name,b.fath_hand_phone,b.moth_name,b.moth_birthyear,moth_work_name,b.moth_alive,b.moth_education,b.moth_occupation,b.moth_unit,b.moth_phone,b.moth_hand_phone,b.guardian_name,b.guardian_relation,b.guardian_unit,b.guardian_hand_phone,b.guardian_phone,b.guardian_address,b.grandfath_name,b.grandfath_alive,b.grandmoth_name,b.grandmoth_alive  from stud_base a left join stud_domicile b on a.student_sn=b.student_sn ";
	for ($ss=0;$ss < count ($sel_stud);$ss++)
		$temp_sel .= "'".$sel_stud[$ss]."',";
	$sql_select .= "where a.stud_id in (".substr($temp_sel,0,-1).") ";

	$sql_select .= " order by a.curr_class_num ";
	$recordSet = $CONN->Execute($sql_select)or die ($sql_select);
	$i =0;
	$data = '';

	while (!$recordSet->EOF) {
		$stud_id = $recordSet->fields["stud_id"];
		$student_sn = $recordSet->fields["student_sn"];
		$stud_name = $recordSet->fields["stud_name"];
		$stud_sex = $recordSet->fields["stud_sex"];
		$stud_birthday = $recordSet->fields["stud_birthday"];
		$stud_blood_type = $recordSet->fields["stud_blood_type"];
		$stud_birth_place = $recordSet->fields["stud_birth_place"];
		$stud_kind = $recordSet->fields["stud_kind"];
		$stud_country = $recordSet->fields["stud_country"];
		$stud_country_kind = $recordSet->fields["stud_country_kind"];
		$stud_person_id = $recordSet->fields["stud_person_id"];
		$stud_country_name = $recordSet->fields["stud_country_name"];
		$stud_addr_1= $recordSet->fields["stud_addr_1"];
		$stud_addr_2 = $recordSet->fields["stud_addr_2"];
		$stud_tel_1 = $recordSet->fields["stud_tel_1"];
		$stud_tel_2 = $recordSet->fields["stud_tel_2"];
		$stud_tel_3 = $recordSet->fields["stud_tel_3"];
		$stud_mail = $recordSet->fields["stud_mail"];
		$stud_class_kind = $recordSet->fields["stud_class_kind"];
		$stud_spe_kind = $recordSet->fields["stud_spe_kind"];
		$stud_spe_class_kind = $recordSet->fields["stud_spe_class_kind"];
		$stud_spe_class_id = $recordSet->fields["stud_spe_class_id"];
		$stud_preschool_status = $recordSet->fields["stud_preschool_status"];
		$stud_preschool_id = $recordSet->fields["stud_preschool_id"];
		$stud_preschool_name = $recordSet->fields["stud_preschool_name"];
		$stud_mschool_status = $recordSet->fields["stud_mschool_status"];
		$stud_mschool_id = $recordSet->fields["stud_mschool_id"];
		$stud_mschool_name = $recordSet->fields["stud_mschool_name"];
		$stud_study_year = $recordSet->fields["stud_study_year"];
		$curr_class_num = $recordSet->fields["curr_class_num"];
		$fath_name = $recordSet->fields["fath_name"];
		$fath_birthyear = $recordSet->fields["fath_birthyear"];
		$fath_alive = $recordSet->fields["fath_alive"];
		$fath_education = $recordSet->fields["fath_education"];
		$fath_occupation = $recordSet->fields["fath_occupation"];
		$fath_work_name = $recordSet->fields["fath_work_name"];
		$fath_unit = $recordSet->fields["fath_unit"];
		$fath_phone = $recordSet->fields["fath_phone"];
		$fath_hand_phone = $recordSet->fields["fath_hand_phone"];
		$moth_name = $recordSet->fields["moth_name"];
		$moth_birthyear = $recordSet->fields["moth_birthyear"];
		$moth_alive = $recordSet->fields["moth_alive"];
		$moth_relation = $recordSet->fields["moth_relation"];
		$moth_education = $recordSet->fields["moth_education"];
		$moth_occupation = $recordSet->fields["moth_occupation"];
		$moth_work_name = $recordSet->fields["moth_work_name"];
		$moth_unit = $recordSet->fields["moth_unit"];
		$moth_work_name = $recordSet->fields["moth_work_name"];
		$moth_phone = $recordSet->fields["moth_phone"];
		$moth_hand_phone = $recordSet->fields["moth_hand_phone"];
		$guardian_name = $recordSet->fields["guardian_name"];
		$guardian_phone = $recordSet->fields["guardian_phone"];
		$guardian_relation = $recordSet->fields["guardian_relation"];
		$guardian_unit = $recordSet->fields["guardian_unit"];
		$guardian_work_name = $recordSet->fields["guardian_work_name"];
		$guardian_hand_phone = $recordSet->fields["guardian_hand_phone"];
		$guardian_guardian_address = $recordSet->fields["guardian_address"];
		$grandfath_name = $recordSet->fields["grandfath_name"];
		$grandfath_alive = $recordSet->fields["grandfath_alive"];
		$grandmoth_name = $recordSet->fields["grandmoth_name"];
		$grandmoth_alive = $recordSet->fields["grandmoth_alive"];

		//學生身分別
		$stud_kind_temp='';
		$stud_kind_temp_arr = explode(",",$stud_kind);
		for ($iii=0;$iii<count($stud_kind_temp_arr);$iii++) {
			if ($stud_kind_temp_arr[$iii]<>'')
				$stud_kind_temp .= $stud_kind_arr[$stud_kind_temp_arr[$iii]].",";
		}

		$temp_arr["stud_kind"]= substr($stud_kind_temp,0,-1);


		//學生基本資料
		$bir_temp_arr = explode("-",DtoCh($stud_birthday));
		$temp_arr["stud_birthday"]=sprintf("民國%d年%d月%d日",$bir_temp_arr[0],$bir_temp_arr[1],$bir_temp_arr[2]);
		$temp_arr["stud_blood_type"]=$blood_arr[$stud_blood_type];
		$temp_arr["stud_sex"]=$sex_arr[$stud_sex];
		$temp_arr["stud_name"]=$stud_name;
		$temp_arr["stud_id"]=$stud_id;
		$temp_arr["study_begin_date"]=$study_begin_date;
		$temp_arr["stud_person_id"]=$stud_person_id;
		$temp_arr["stud_birth_place"]=$birth_state_arr[sprintf("%02d",$stud_birth_place)];
		$temp_arr["curr_year"]= Num2CNum(substr($curr_class_num,0,1));
		$temp_arr["curr_class"] = $class_name[substr($curr_class_num,1,2)];
		$temp_arr["curr_num"] = intval(substr($curr_class_num,-2))."號";
		$temp_arr["sch_cname"] = $SCHOOL_BASE[sch_cname];
		$temp_arr["stud_addr_1"] = $stud_addr_1;
		$temp_arr["stud_addr_2"] = $stud_addr_2;
		$temp_arr["stud_tel_1"] = $stud_tel_1;
		$temp_arr["stud_tel_2"] = $stud_tel_2;
                $temp_arr["stud_mschool_name"]=$stud_mschool_name;
                $temp_arr["stud_grade_date"]=$stud_grade_date;

		//直系血親
		$temp_arr[stud_parent] = "父: $fath_name($is_live_arr[$fath_alive])($fath_birthyear 生), 母:$moth_name($is_live_arr[$moth_alive])($moth_birthyear 生), 祖父:$grandfath_name($is_live_arr[$grandfath_alive]), 祖母:$grandmoth_name($is_live_arr[$grandmoth_alive])";

		//父母教育程度
		$temp_arr[stud_parent_edu]= "父 :$edu_kind_arr[$moth_education] ,母: $edu_kind_arr[$moth_education]";

		//監護人
		$temp_arr["aaaa"]= $guardian_name;
		$temp_arr["bbbb"]= $guardian_relation_arr[$guardian_relation];
		$temp_arr["cccc"]= $guardian_address;
		$temp_arr["dddd"]= "$guardian_phone $guardian_hand_phone";

		//家長
		$temp_arr[f_1]=$fath_name;
		$temp_arr[f_2]=$fath_occupation;
		$temp_arr[f_3]=$fath_work_name;
		$temp_arr[f_4]=$fath_unit;
		$temp_arr[f_5]=$fath_phone;
		$temp_arr[m_1]=$moth_name;
		$temp_arr[m_2]=$moth_occupation;
		$temp_arr[m_3]=$moth_work_name;
		$temp_arr[m_4]=$moth_unit;
		$temp_arr[m_5]=$moth_phone;

		$temp_arr[stud_study_year] = $stud_study_year;

		//取得學生輔導資料
		$stud_seme_arr = array();
		$sql_select = "select seme_year_seme,stud_id,sse_relation,sse_family_kind,sse_family_air,sse_farther,sse_mother,sse_live_state,sse_rich_state,sse_s1,sse_s2,sse_s3,sse_s4,sse_s5,sse_s6,sse_s7,sse_s8,sse_s9,sse_s10,sse_s11 from stud_seme_eduh where stud_id='$stud_id' order by seme_year_seme";
		$res_seme = $CONN->Execute($sql_select);
		while(!$res_seme->EOF){
			$temp_seme = $res_seme->fields[seme_year_seme];
			$stud_seme_arr[$temp_seme][sse_relation] = $res_seme->fields[sse_relation];
			$stud_seme_arr[$temp_seme][sse_family_kind] = $res_seme->fields[sse_family_kind];
			$stud_seme_arr[$temp_seme][sse_family_air] = $res_seme->fields[sse_family_air];
			$stud_seme_arr[$temp_seme][sse_farther] = $res_seme->fields[sse_farther];
			$stud_seme_arr[$temp_seme][sse_mother] = $res_seme->fields[sse_mother];
			$stud_seme_arr[$temp_seme][sse_live_state] = $res_seme->fields[sse_live_state];
			$stud_seme_arr[$temp_seme][sse_rich_state] = $res_seme->fields[sse_rich_state];
			$stud_seme_arr[$temp_seme][sse_s1] = $res_seme->fields[sse_s1];
			$stud_seme_arr[$temp_seme][sse_s2] = $res_seme->fields[sse_s2];
			$stud_seme_arr[$temp_seme][sse_s3] = $res_seme->fields[sse_s3];
			$stud_seme_arr[$temp_seme][sse_s4] = $res_seme->fields[sse_s4];
			$stud_seme_arr[$temp_seme][sse_s5] = $res_seme->fields[sse_s5];
			$stud_seme_arr[$temp_seme][sse_s6] = $res_seme->fields[sse_s6];
			$stud_seme_arr[$temp_seme][sse_s7] = $res_seme->fields[sse_s7];
			$stud_seme_arr[$temp_seme][sse_s8] = $res_seme->fields[sse_s8];
			$stud_seme_arr[$temp_seme][sse_s9] = $res_seme->fields[sse_s9];
			$stud_seme_arr[$temp_seme][sse_s10] = $res_seme->fields[sse_s10];
			$stud_seme_arr[$temp_seme][sse_s11] = $res_seme->fields[sse_s11];
			$res_seme->MoveNext();
		}

		//入學學校 (尚未判斷國中小)
		//$temp_arr["stud_mschool_name"]="";
		//畢業日期 (尚未判斷)

		//$temp_arr["stud_grade_date"]="";
		//列印時間
		$temp_arr["print_time"]="列印時間: $now";
		$temp_arr["test_1"]="misser測試";
		//取代基本資料
		$data .= change_temp($temp_arr,$no_iconv_arr,$doc_main);

		$recordSet->MoveNext();
		//換頁
		if (!$recordSet->EOF)
			$data .= $break;
                //echo $data ;
        	//資料開始輸出

        	$data_index="<font size='3'>";
        	$data_word="<font size='4' color='blue' face='標楷體'>";

                echo "<table border='1' cellpadding='2' cellspacing='0'  bordercolorlight='#333354' bordercolordark='#FFFFFF' width='100%'>";
                echo "<tr><td colspan='11' align='center'><font color='green' size='4'>"."學生輔導紀錄表</font></td></tr>";
                echo "<td colspan='2'>".$data_index."姓名</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[stud_name]."</font></td>";
                echo "<td>".$data_index."性別</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[stud_sex]."</font></td>";
                echo "<td rowspan='4'>".$data_index."異動紀錄</font></td>";
                echo "<td colspan='3' rowspan='4'>".$data_word.$temp_arr[stud_kind]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."入學年</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[stud_study_year]."</font></td>";
                echo "<td>".$data_index."學號</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[stud_id]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."入學時學校</font></td>";
                echo "<td colspan='5'>".$data_word.$temp_arr[stud_mschool_name]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."畢業年月</font></td>";
                echo "<td colspan='5'>".$data_word.$temp_arr[stud_grade_date]."</font></td>";
                echo "</tr><tr><td colspan='11' align='center'><font size='4'>"."一、本人概況</font></td></tr>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."身分證編號</font></td>";
                echo "<td colspan='9'>".$data_word.$temp_arr[stud_person_id]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."出生</font></td>";
                echo "<td>".$data_index."出生地</font></td>";
                echo "<td colspan='4'>".$data_word.$temp_arr[stud_birth_place]."</font></td>";
                echo "<td>".$data_index."生日</font></td>";
                echo "<td colspan='3'>".$data_word.$temp_arr[stud_birthday]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."血型</font></td>";
                echo "<td colspan='9'>".$data_word.$temp_arr[stud_blood_type]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."戶籍地址</font></td>";
                echo "<td colspan='6'>".$data_word.$temp_arr[stud_addr_1]."</font></td>";
                echo "<td>".$data_index."電話</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[stud_tel_1]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."聯絡地址</font></td>";
                echo "<td colspan='6'>".$data_word.$temp_arr[stud_addr_2]."</font></td>";
                echo "<td>".$data_index."電話</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[stud_tel_2]."</font></td>";
                echo "</tr><tr><td colspan='11' align='center'><font size='4'>"."二、家庭狀況</font></td></tr>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."直系血親</font></td>";
                echo "<td colspan='9'>".$data_word.$temp_arr[stud_parent]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."父母教育程度</font></td>";
                echo "<td colspan='9'>".$data_word.$temp_arr[stud_parent_edu]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='3'>".$data_index."家長</font></td>";
                echo "<td>".$data_index."稱謂</font></td>";
                echo "<td>".$data_index."姓名</font></td>";
                echo "<td colspan='2'>".$data_index."職業</font></td>";
                echo "<td colspan='2'>".$data_index."工作機構</font></td>";
                echo "<td>".$data_index."職稱</font></td>";
                echo "<td>".$data_index."電話</font></td>";
                echo "<td>".$data_index."備註</font></td>";
                echo "</tr><tr>";
                echo "<td>".$data_index."父</font></td>";
                echo "<td>".$data_word.$temp_arr[f_1]."</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[f_2]."</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[f_3]."</font></td>";
                echo "<td>".$data_word.$temp_arr[f_4]."</font></td>";
                echo "<td>".$data_word.$temp_arr[f_5]."</font></td>";
                echo "</tr><tr>";
                echo "<td>".$data_index."母</font></td>";
                echo "<td>".$data_word.$temp_arr[m_1]."</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[m_2]."</font></td>";
                echo "<td colspan='2'>".$data_word.$temp_arr[m_3]."</font></td>";
                echo "<td>".$data_word.$temp_arr[m_4]."</font></td>";
                echo "<td>".$data_word.$temp_arr[m_5]."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='2'>".$data_index."監護人</font></td>";
                echo "<td colspan='9'>";
                echo $data_index."姓名："."</font>".$data_word.$temp_arr[aaaa]."</font>";
                echo $data_index."關係："."</font>".$data_word.$temp_arr[bbbb]."</font>";
                echo $data_index."通訊處："."</font>".$data_word.$temp_arr[cccc]."</font>";
                echo $data_index."電話："."</font>".$data_word.$temp_arr[dddd]."</font></td>";
                echo "</tr><tr>";
        	//兄弟姐妹
        	$query = "select * from stud_brother_sister where student_sn='$student_sn' order by bs_birthyear";
        	$bs_res = $CONN->Execute($query);
        	$temp=$bs_res->RecordCount();
        	$temp+=1;
                echo "<td colspan='2' rowspan='".$temp."'>".$data_index."兄弟姊妹</font></td>";
                echo "<td>".$data_index."稱謂</font></td>";
                echo "<td colspan='2'>".$data_index."姓名</font></td>";
                echo "<td colspan='4'>".$data_index."畢(肄)業夜校</font></td>";
                echo "<td colspan='2'>".$data_index."出生年次</font></td></tr>";
                while(!$bs_res->EOF){
        		$bs_arr[b_1] = $bs_calling_kind_arr[$bs_res->fields[bs_calling]];
        		$bs_arr[b_2] = $bs_res->fields[bs_name];
        		$bs_arr[b_3] = $bs_res->fields[bs_gradu];
        		$bs_arr[b_4] = $bs_res->fields[bs_birthyear];
                        echo "<td>".$data_word.$bs_arr[b_1]."</font></td>";
                        echo "<td colspan='2'>".$data_word.$bs_arr[b_2]."</font></td>";
                        echo "<td colspan='4'>".$data_word.$bs_arr[b_3]."</font></td>";
                        echo "<td colspan='2'>".$data_word.$bs_arr[b_4]."</font></td>";

                        echo "</tr><tr>";
                       	$bs_res->MoveNext();
        	}
                echo "</tr><tr>";
        	//父母關係
        	$sssss ='';
        	reset($stud_seme_arr);
        	while(list($vid,$vval) = each($stud_seme_arr)){
        		$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss = $seme_name.'('. $sse_relation_arr[$vval[sse_relation]].")";
        		$sssss .= $temp_ss.", ";
        	}
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."父母關係</font></td>";
                echo "<td colspan='9'>".$data_index.$sse_relation_str."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='9'>".$data_word.$sssss."</font></td>";
                //家庭類型
        	reset($stud_seme_arr);
        	$sssss ='';
        	while(list($vid,$vval) = each($stud_seme_arr)){
        		$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss = $seme_name.'('. $sse_family_kind_arr[$vval[sse_family_kind]].")";
        		$sssss .= $temp_ss.", ";
        	}
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."家庭類型</font></td>";
                echo "<td colspan='9'>".$data_index.$sse_family_kind_str."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='9'>".$data_word.$sssss."</font></td>";
                //家庭氣氛
        	reset($stud_seme_arr);
        	$sssss ='';
        	while(list($vid,$vval) = each($stud_seme_arr)){
        		$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss = $seme_name.'('. $sse_family_air_arr[$vval[sse_family_kind]].")";
        		$sssss .= $temp_ss.", ";
        	}
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."家庭氣氛</font></td>";
                echo "<td colspan='9'>".$data_index.$sse_family_air_str."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='9'>".$data_word.$sssss."</font></td>";
                //父管教方式
        	reset($stud_seme_arr);
        	$sssss ='';
        	while(list($vid,$vval) = each($stud_seme_arr)){
        		$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss = $seme_name.'('. $sse_farther_arr[$vval[sse_farther]].")";
        		$sssss .= $temp_ss.", ";
        	}
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."父管教方式</font></td>";
                echo "<td colspan='9'>".$data_index.$sse_farther_str."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='9'>".$data_word.$sssss."</font></td>";
                //母管教方式
        	reset($stud_seme_arr);
        	$sssss ='';
        	while(list($vid,$vval) = each($stud_seme_arr)){
        		$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss = $seme_name.'('. $sse_farther_arr[$vval[sse_mother]].")";
        		$sssss .= $temp_ss.", ";
        	}
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."母管教方式</font></td>";
                echo "<td colspan='9'>".$data_index.$sse_farther_str."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='9'>".$data_word.$sssss."</font></td>";
                //居住情形
        	reset($stud_seme_arr);
        	$sssss ='';
        	while(list($vid,$vval) = each($stud_seme_arr)){
        		$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss = $seme_name.'('. $sse_live_state_arr[$vval[sse_live_state]].")";
        		$sssss .= $temp_ss.", ";
        	}
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."居住情形</font></td>";
                echo "<td colspan='9'>".$data_index.$sse_live_state_str."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='9'>".$data_word.$sssss."</font></td>";
                //經濟狀況
        	reset($stud_seme_arr);
        	$sssss ='';
        	while(list($vid,$vval) = each($stud_seme_arr)){
        		$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss = $seme_name.'('. $sse_rich_state_arr[$vval[sse_rich_state]].")";
        		$sssss .= $temp_ss.", ";
        	}
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."經濟狀況</font></td>";
                echo "<td colspan='9'>".$data_index.$sse_rich_state_str."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='9'>".$data_word.$sssss."</font></td>";

        	$bs_data = '';
        	for($si=1;$si<=11;$si++){
        		reset($stud_seme_arr);
        		$sssss ='';
        		while(list($vid,$vval) = each($stud_seme_arr)){
        			$temp_sse_arr = ${"sse_arr_$si"};
        			$temp_str = ${"sse_str_$si"};
        			$temp_id  = "sse_s$si";
        			$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        			$semester = substr($vid,-1);
        			$seme_name = ($semester==1)?"上":"下";
        			$seme_name = Num2CNum($this_year).$seme_name;
        			$tt_arr = explode(",",$vval[$temp_id]);
        			$temp_ss='';
        			foreach ($tt_arr as $VAL){
        				if ($VAL<>'')
        					$temp_ss .= $temp_sse_arr[$VAL].",";
        			}
        			if($temp_ss<>'')
        				$temp_ss = substr($temp_ss,0,-1);
        			$temp_ss = $seme_name.'('.$temp_ss.')';
        			$sssss .= $temp_ss.", ";
        		}
        		if($si==1)
        			$bs_arr[sse_kind] = "最喜愛科目";
        		else if($si==2)
        			$bs_arr[sse_kind] = "最困難科目";
        		else
        			$bs_arr[sse_kind] = $sse_arr[$si];

                        echo "</tr><tr>";
                        echo "<td colspan='2' rowspan='2'>".$data_index.$bs_arr[sse_kind]."</font></td>";
                        echo "<td colspan='9'>".$data_index.$temp_str."</font></td>";
                        echo "</tr><tr>";
                        echo "<td colspan='9'>".$data_word.$sssss."</font></td>";
        	}
                //以下 by misser,修改3(新增) ,取得出缺席紀錄 ,原無
                //=================================
                if ($IS_JHORES==6){//國中，6學期
                   $stud_seme_new_arr=array("$stud_study_year"."1","$stud_study_year"."2","$stud_study_year"+"1"."1","$stud_study_year"+"1"."2","$stud_study_year"+"2"."1","$stud_study_year"+"2"."2");
                }
                else{//應該是小學，所以有12學期
                   $stud_seme_new_arr=array("$stud_study_year"."1","$stud_study_year"."2","$stud_study_year"+"1"."1","$stud_study_year"+"1"."2","$stud_study_year"+"2"."1","$stud_study_year"+"2"."2","$stud_study_year"+"3"."1","$stud_study_year"+"3"."2","$stud_study_year"+"4"."1","$stud_study_year"+"4"."2","$stud_study_year"+"5"."1","$stud_study_year"+"5"."2");
                }
                $bs_data = '';
        	//reset($stud_seme_arr); 不用$stud_seme_arr，因為若某學期尚未建輔導紀錄，則
        	//$stud_seme_arr就會少了某學期的資料，也就無法找出該學期的缺曠課。
        	//所以用 上面的 $stud_seme_new_arr 代替。
        	//以下獎懲亦同

                //取得假別
                $asb_arr=sfs_text("缺曠課類別");
        	while(list($id,$val)= each($asb_arr))
        		$asb_str .= "$id-$val,";

        	//取得absent資料表中出缺席紀錄
        	$sssss ='<text:p text:style-name="P8">';
        	$temp_ss='';
                //下面$vval及$vid 位置和原先是對調的
                while(list($vval,$vid) = each($stud_seme_new_arr)){//依學期別
                        $year=(substr($vid,0,1)=='0')?substr($vid,1,-1):substr($vid,0,-1);
                        $this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss .= $seme_name.'(';
        		foreach ($asb_arr as $temp_kind){//再依假別尋找次數
        	            $sql_select = "select * from stud_absent where stud_id='$stud_id' and absent_kind='$temp_kind' and year='$year' and semester='$semester' order by year,semester";
                            $record=$CONN->Execute($sql_select) or die($sql_select);
                            $num=$record->RecordCount();
                            if ($num>0){;//如果找到，則傳回假別次數
                                $temp_ss.=$temp_kind.":".$num."節。 ";
                            }
                        }
                        $temp_ss.=')';

        	}
        	$sssss .= $temp_ss.", ";
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."缺曠課紀錄</font></td>";
                echo "<td colspan='9'>".$data_index.$asb_str."</font></td>";
                echo "</tr><tr>";
                echo "<td colspan='9'>".$data_word.$sssss."</font></td>";

                //以下 by misser,修改4(新增) ,取得獎懲紀錄 ,原無
                //=================================
                if ($IS_JHORES==6){//限國中，國小不用獎懲
          	   $bs_data = '';
         	   reset($stud_seme_new_arr);
        	   //取得獎懲類別 ,取自 reward 模組的config.php
            	   $reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
                   while(list($id,$val)= each($reward_arr))
        	  	$reward_str .= "$id-$val,";

        	    //取得reward資料表中獎懲紀錄
        	    $sssss ='<text:p text:style-name="P8">';
        	    $temp_ss='';

        	    while(list($vval,$vid) = each($stud_seme_new_arr)){
        	        $year=(substr($vid,0,1)=='0')?substr($vid,1,-1):substr($vid,0,-1);
                        $this_year = (substr($vid,0,-1)-$stud_study_year)+1;
        		$semester = substr($vid,-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$seme_name = Num2CNum($this_year).$seme_name;
        		$temp_ss .= $seme_name.'(';

                        $sql_select = "select * from reward where stud_id='$stud_id' and reward_year_seme='$year$semester' order by reward_date";
                        $re_record=$CONN->Execute($sql_select) or die($sql_select);

                        while(!$re_record->EOF){
                                $temp_ss.= $reward_arr[$re_record->fields[reward_kind]];
                                $temp_ss.=":";
                                $temp.=$re_record->fields[reward_reason];
                                if ($re_record->fields[reward_cancel_date]!="" and $re_record->fields[reward_cancel_date]!="0000-00-00")
                                   $temp_ss.="**已銷過**";

                                $temp_ss.="　,";
                                $re_record->MoveNext();
                       }

                           $temp_ss.=")";
        	     }
        	     $sssss .= $temp_ss.", ";
                     echo "</tr><tr>";
                     echo "<td colspan='2' rowspan='2'>".$data_index."獎懲紀錄</font></td>";
                     echo "<td colspan='9'>".$data_index.$reward_str."</font></td>";
                     echo "</tr><tr>";
                     echo "<td colspan='9'>".$data_word.$sssss."</font></td>";
		}
                // by misser 修改4(新增) 結束

		//測驗紀錄
                echo "</tr><tr>";
                echo "<td colspan='2' rowspan='2'>".$data_index."測驗紀錄</font></td>";
                echo "<td><font size='1'>測驗名稱</font></td>";
                echo "<td><font size='1'>測驗日期</font></td>";
                echo "<td><font size='1'>原始分數</font></td>";
                echo "<td><font size='1'>常模樣本</font></td>";
                echo "<td><font size='1'>智商</font></td>";
                echo "<td><font size='1'>標準分數</font></td>";
                echo "<td><font size='1'>百分等級</font></td>";
                echo "<td colspan='2'><font size='1'>解釋</font></td>";
                echo "</tr><tr>";
                echo "<td><font size='1'>&nbsp;</font></td>";
                echo "<td><font size='1'>&nbsp;</font></td>";
                echo "<td><font size='1'>&nbsp;</font></td>";
                echo "<td><font size='1'>&nbsp;</font></td>";
                echo "<td><font size='1'>&nbsp;</font></td>";
                echo "<td><font size='1'>&nbsp;</font></td>";
                echo "<td><font size='1'>&nbsp;</font></td>";
                echo "<td colspan='2'><font size='1'>&nbsp;</font></td>";
        	//輔導訪談記錄
                echo "</tr><tr>";
                echo "<td colspan='11' align='center'>".$data_index."重要輔導紀錄</font></td>";
                echo "</tr><tr>";
                echo "<td>".$data_index."學期</font></td>";
                echo "<td>".$data_index."日期</font></td>";
                echo "<td>".$data_index."對象</font></td>";
                echo "<td colspan='7'>".$data_index."輔導內容要點</font></td>";
                echo "<td>".$data_index."紀錄者</font></td>";

        	$query = "select seme_year_seme,sst_date,sst_name,sst_main,sst_memo,teach_id from stud_seme_talk where stud_id='$stud_id' order by seme_year_seme";
        	$res_talk = $CONN->Execute($query) or die($query);
        	$memo_arr = array();
        	while(!$res_talk->EOF){
        		$memo_arr[w_2]= $res_talk->fields[sst_date];
        		$memo_arr[w_3]= $res_talk->fields[sst_name];
        		$memo_arr[w_4]= $res_talk->fields[sst_main].":".$res_talk->fields[sst_memo];
        		$memo_arr[w_5]= get_teacher_name($res_talk->fields[teach_id]);
        		$this_year = (substr($res_talk->fields[seme_year_seme],0,-1)-$stud_study_year)+1;
        		$semester = substr($res_talk->fields[seme_year_seme],-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$memo_arr[w_1] = Num2CNum($this_year).$seme_name;
                        echo "</tr><tr>";
                        echo "<td>".$data_word.$memo_arr[w_1]."</font></td>";
                        echo "<td>".$data_word.$memo_arr[w_2]."</font></td>";
                        echo "<td>".$data_word.$memo_arr[w_3]."</font></td>";
                        echo "<td colspan='7'>".$data_word.$memo_arr[w_4]."</font></td>";
                        echo "<td>".$data_word.$memo_arr[w_5]."</font></td>";
        		$res_talk->MoveNext();
        	}
        	//特殊表現記錄
                echo "</tr><tr>";
                echo "<td colspan='11' align='center'>".$data_index."特殊表現紀錄</font></td>";
                echo "</tr><tr>";
                echo "<td>".$data_index."學期</font></td>";
                echo "<td>".$data_index."紀錄日期</font></td>";
                echo "<td colspan='8'>".$data_index."優良表現事宜</font></td>";
                echo "<td>".$data_index."紀錄者</font></td>";

        	$query = "select seme_year_seme,sp_date,sp_memo,teach_id from stud_seme_spe where stud_id='$stud_id' order by seme_year_seme";
        	$res_talk = $CONN->Execute($query) or die($query);
        	$memo_arr = array();
        	while(!$res_talk->EOF){
        		$memo_arr[s_2]= $res_talk->fields[sp_date];
        		$memo_arr[s_3]= $res_talk->fields[sp_memo];
        		$memo_arr[s_4]= get_teacher_name($res_talk->fields[teach_id]);
        		$this_year = (substr($res_talk->fields[seme_year_seme],0,-1)-$stud_study_year)+1;
        		$semester = substr($res_talk->fields[seme_year_seme],-1);
        		$seme_name = ($semester==1)?"上":"下";
        		$memo_arr[s_1] = Num2CNum($this_year).$seme_name;
                        echo "</tr><tr>";
                        echo "<td>".$data_word.$memo_arr[s_1]."</font></td>";
                        echo "<td>".$data_word.$memo_arr[s_2]."</font></td>";
                        echo "<td colspan='8'>".$data_word.$memo_arr[s_3]."</font></td>";
                        echo "<td>".$data_word.$memo_arr[s_4]."</font></td>";

        		$res_talk->MoveNext();
        	}

                echo "</tr></table><p>";
	}

        exit;

	break;

}







//選擇班級

head();

print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='sel_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

	$help_text="
	本紀錄表另增線上顯示，餘功能與其他輔導紀錄表[如：全校學生輔導紀錄之紀錄表]相同，請視需要使用。";
	$help=&help($help_text);

echo $help;
echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\" name=\"myform\">";

$sel1 = new drop_select();
$sel1->top_option =  "選擇班級";
$sel1->s_name = "class_id";
$sel1->id = $class_id;
$sel1->is_submit = true;
$sel1->arr = $class_arr;
$sel1->do_select();

if($class_id<>'') {
	$query = "select stud_id,stud_name,curr_class_num,stud_study_cond from stud_base where stud_study_cond <> 5 and curr_class_num like '$class_id%' order by curr_class_num";
	$result = $CONN->Execute($query) or die ($query);
	if (!$result->EOF) {

 		echo '&nbsp;<input type="button" value="全選" onClick="javascript:tagall(1);">&nbsp;';
 		echo '<input type="button" value="取消全選" onClick="javascript:tagall(0);">';
		echo "<table border=1>";
		$ii=0;
		while (!$result->EOF) {
			$stud_id = $result->fields[stud_id];
			$stud_name = $result->fields[stud_name];
			$curr_class_num = substr($result->fields[curr_class_num],-2);
			$stud_study_cond = $result->fields[stud_study_cond];
			$move_kind ='';
			if ($stud_study_cond >0){//增加 { 記號 by misser 93.10.20
                                $move_kind_arr=study_cond();//本行新增 by misser 93.10.20
				$move_kind= "<font color=red>(".$move_kind_arr[$stud_study_cond].")</font>";
                        }//增加 } 記號 by misser 93.10.20
			if ($ii %2 ==0)
				$tr_class = "class=title_sbody1";
			else
				$tr_class = "class=title_sbody2";

			if ($ii % 5 == 0)
				echo "<tr $tr_class >";
			echo "<td ><input id=\"c_$stud_id\" type=\"checkbox\" name=\"sel_stud[]\" value=\"$stud_id\"><label for=\"c_$stud_id\">$curr_class_num. $stud_name $move_kind</label></td>\n";

			if ($ii % 5 == 4)
				echo "</tr>";
			$ii++;
			$result->MoveNext();
		}
		echo"</table>";

		echo "  sxw 格式(openoffice 檔案) 輸出 ";
		echo "<input type=\"submit\" name=\"do_key\" value=\"$postBtn\"><p>";
		echo "  線上輸出 ";
		echo "<input type=\"submit\" name=\"do_key\" value=\"$postBtn_xls_online\">";
		echo "<input type=\"hidden\" name=\"filename\" value=\"reg2_class{$class_id}.sxw\">";

	}

}



foot();


function change_temp($arr,$no_iconv_arr,$source) {
	$temp_str = $source;
	while(list($id,$val) = each($arr)){
		if (!$no_iconv_arr[$id])
			$val =iconv("Big5","UTF-8//IGNORE",$val);
		$temp_str = str_replace("{".$id."}", $val,$temp_str);
	}
	return $temp_str;
}
?>


