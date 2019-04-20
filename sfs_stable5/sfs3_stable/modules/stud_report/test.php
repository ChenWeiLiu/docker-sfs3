<?php
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";

$ttt = new zipfile;


switch($do_key) {
	case $postBtn:	
	$break ="<text:p text:style-name=\"break_page\"/>";
	$doc_end = "</office:body></office:document-content>";
	
	$ttt->add_dir("./oo/META-INF");
	$data = $ttt->read_file("./oo/META-INF/manifest.xml");

	$ttt->add_file($data,"/META-INF/manifest.xml");

	$data = $ttt->read_file("./oo/settings.xml");
	$ttt->add_file($data,"settings.xml");

	$data = $ttt->read_file("./oo/styles.xml");
	$ttt->add_file($data,"styles.xml");

	$data = $ttt->read_file("./oo/meta.xml");
	$ttt->add_file($data,"meta.xml");

	$data = $ttt->read_file("./oo/content.xml");

	$sql_select = "select a.*,b.fath_name,b.fath_birthyear,b.fath_alive,b.fath_education,b.fath_occupation,b.fath_unit,b.fath_phone,b.fath_hand_phone,b.moth_name,b.moth_birthyear,b.moth_alive,b.moth_education,b.moth_occupation,b.moth_unit,b.moth_phone,b.moth_hand_phone,b.guardian_name,b.guardian_relation,b.guardian_unit,b.guardian_hand_phone,b.guardian_phone,b.grandfath_name,b.grandfath_alive,b.grandmoth_name,b.grandmoth_alive  from stud_base a left join stud_domicile b on a.stud_id=b.stud_id  ";
	$sql_select .= " where a.stud_study_cond= 0 ";
	for ($ss=0;$ss < count ($sel_stud);$ss++)
		$temp_sel .= $sel_stud[$ss].",";
	$sql_select .= "and a.stud_id in (".substr($temp_sel,0,-1).") ";
	 
	$sql_select .= " order by a.curr_class_num ";	
	$recordSet = $CONN->Execute($sql_select)or die ($sql_select);	
	$i =0;
	while (!$recordSet->EOF) {
	
		$stud_id = $recordSet->fields["stud_id"];
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
		$fath_unit = $recordSet->fields["fath_unit"];
		$fath_phone = $recordSet->fields["fath_phone"];		
		$fath_hand_phone = $recordSet->fields["fath_hand_phone"];
		$moth_name = $recordSet->fields["moth_name"];
		$moth_birthyear = $recordSet->fields["moth_birthyear"];
		$moth_alive = $recordSet->fields["moth_alive"];
		$moth_relation = $recordSet->fields["moth_relation"];
		$moth_education = $recordSet->fields["moth_education"];	
		$moth_occupation = $recordSet->fields["moth_occupation"];
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
		$temp_arr["stud_birthday"]=sprintf("%d年%d月%d日",$bir_temp_arr[0],$bir_temp_arr[1],$bir_temp_arr[2]);
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
	
		//父親資料
		$temp_arr["fath_name"] = $fath_name ."  (".$is_live_arr[$fath_alive].")";	 
		$temp_arr["fath_birthyear"] = $fath_birthyear;
		$temp_arr["fath_education"] = $edu_kind_arr[$fath_education];
		$temp_arr["fath_occupation"] = $fath_occupation;
		$temp_arr["fath_unit"] = $fath_unit;
		$temp_arr["fath_phone"] = $fath_phone;
		$temp_arr["fath_hand_phone"] = $fath_hand_phone;
	
		//母親資料
		$temp_arr["moth_name"] = $moth_name ."  (".$is_live_arr[$moth_alive].")";
		$temp_arr["moth_birthyear"] = $$moth_birthyear;
		$temp_arr["moth_education"] = $edu_kind_arr[$moth_education];
		$temp_arr["moth_occupation"] = $moth_occupation;
		$temp_arr["moth_unit"] = $moth_unit;
		$temp_arr["moth_phone"] = $moth_phone;
		$temp_arr["moth_hand_phone"] = $moth_hand_phone;
	
		//監護人
		$temp_arr["guardian_name"]= $guardian_name;
		$temp_arr["guardian_relation"]= $guardian_relation_arr[$guardian_relation];
		$temp_arr["guardian_phone"]= $guardian_phone;
		$temp_arr["guardian_unit"]= $guardian_unit;
		$temp_arr["guardian_hand_phone"]= $guardian_hand_phone;
		
		//祖父母
		$temp_arr["grandfath_name"]= $grandfath_name ."  (".$is_live_arr[$grandfath_alive].")";
		$temp_arr["grandmoth_name"]= $grandmoth_name ."  (".$is_live_arr[$grandmoth_alive].")";
		
		
		//取代基本資料
		$sss = change_temp($temp_arr,$stud_base_rep);				
		echo $sss;	
	
		$recordSet->MoveNext();	
		//換頁
		if (!$recordSet->EOF)
			echo $break;
	}
	echo $foot;
	exit;	
	break;
		
}

$temp_arr["sch_cname"] = iconv("Big5","UTF-8",$SCHOOL_BASE[sch_cname]);
$temp_arr["stud_id"] = iconv("Big5","UTF-8","87001");
$temp_arr["stud_name"] = iconv("Big5","UTF-8", "許成功 ");







$data = change_temp($temp_arr,$data);	

//$data =  iconv("Big5","UTF-8",$data_cc);


$ttt->add_file($data,"content.xml");

$sss = $ttt->file();

header("Content-disposition: attachment; filename=ooo.sxw");
header("Content-type: application/octetstream");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
header("Expires: 0");

echo $sss;



//選擇班級

head();
echo "<form action=\"$PHP_SELF\" method=\"post\" name=\"myform\">";

$sel1 = new drop_select();
$sel1->top_option =  "選擇班級";
$sel1->s_name = "class_id";
$sel1->id = $class_id;
$sel1->is_submit = true;
$sel1->arr = $class_arr;
$sel1->do_select();

if($class_id<>'') {
	$class_id=intval($class_id);
	$query = "select stud_id,stud_name,curr_class_num from stud_base where stud_study_cond=0 and curr_class_num like '$class_id%' order by curr_class_num";
	$result = $CONN->Execute($query) or die ($query);
	if (!$result->EOF) {
		echo "<table border=1>";
		$ii=0;
		while (!$result->EOF) {
			$stud_id = $result->fields[stud_id];
			$stud_name = $result->fields[stud_name];
			$curr_class_num = substr($result->fields[curr_class_num],-2);
			if ($ii %2 ==0)
				$tr_class = "class=title_sbody1";
			else
				$tr_class = "class=title_sbody2";
			
			if ($ii % 5 == 0)
				echo "<tr $tr_class >";
			echo "<td ><input id=\"c_$stud_id\" type=\"checkbox\" name=\"sel_stud[]\" value=\"$stud_id\"><label for=\"c_$stud_id\">$curr_class_num. $stud_name</label></td>\n";
				
			if ($ii % 5 == 4)
				echo "</tr>";
			$ii++;
			$result->MoveNext();
		}
		echo"</table>";
		echo "  word 輸出 <input type=\"checkbox\" name=\"isword\" value=\"1\" checked>  ";
		echo "<input type=\"submit\" name=\"do_key\" value=\"$postBtn\">";
	}

}



foot();




function change_temp($arr,$source) {
	$temp_str = $source;
	while(list($id,$val) = each($arr)){
		$val iconv("Big5","UTF-8",$val);
		$temp_str = str_replace("{".$id."}", $val,$temp_str);
	}
	return $temp_str;
}
?>