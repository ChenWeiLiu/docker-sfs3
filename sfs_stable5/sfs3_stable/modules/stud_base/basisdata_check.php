<?php

include "config.php";

sfs_check();


$class_name_arr=class_base();

$check_array=array(stud_name=>"姓名",stud_name_eng=>"英文姓名",stud_sex=>"性別",stud_birthday=>"出生年月日",stud_blood_type=>"血型",stud_birth_place=>"出生地",stud_kind=>"學生身分別",stud_country=>"國籍",stud_country_kind=>"證照種類",stud_person_id=>"身分證號碼",stud_country_name=>"僑居地",stud_addr_1=>"戶籍地址",stud_addr_2=>"連絡地址",stud_tel_1=>"戶籍電話",stud_tel_2=>"連絡電話",stud_tel_3=>"行動電話",stud_mail=>"電子郵件",stud_class_kind=>"班級性質",stud_spe_kind=>"特殊班類別",stud_spe_class_kind=>"特殊班班別",stud_spe_class_id=>"特殊班上課性質",stud_preschool_status=>"入學前幼稚園",stud_preschool_id=>"幼稚園學校代號",stud_preschool_name=>"幼稚園名稱",stud_Mschool_status=>"入學資格",stud_mschool_id=>"國小學校代號",stud_mschool_name=>"國小名稱",stud_study_year=>"入學年",enroll_school=>"入學學校"
,fath_name=>"父親姓名",fath_birthyear=>"父親出生年次",fath_alive=>"父親存歿\",fath_relation=>"與父關係",fath_p_id=>"父親身分證證照",fath_education=>"父親教育程度",fath_grad_kind=>"父親教育程度類別",fath_occupation=>"父親職業",fath_unit=>"父親服務單位",fath_work_name=>"父親職稱",fath_phone=>"父親電話(公)",fath_home_phone=>"父親電話(宅)",fath_hand_phone=>"父親行動電話",fath_email=>"父親電子郵件"
,moth_name=>"母親姓名",moth_birthyear=>"母親出生年次",moth_alive=>"母親存歿\",moth_relation=>"與母關係",moth_p_id=>"母親身分證證照",moth_education=>"母親教育程度",moth_grad_kind=>"母親教育程度類別",moth_occupation=>"母親職業",moth_unit=>"母親服務單位",moth_work_name=>"母親職稱",moth_phone=>"母親電話(公)",moth_home_phone=>"母親電話(宅)",moth_hand_phone=>"母親行動電話",moth_email=>"母親電子郵件"
,guardian_name=>"監護人姓名",guardian_phone=>"監護人連絡電話",guardian_address=>"監護人連絡地址",guardian_relation=>"與監護人關係",guardian_p_id=>"監護人身分證證照",guardian_unit=>"監護人服務單位",guardian_work_name=>"監護人職稱",guardian_hand_phone=>"監護人行動電話",guardian_email=>"監護人電子郵件",grandfath_name=>"祖父姓名",grandfath_alive=>"祖父存歿\",grandmoth_name=>"祖母姓名",grandmoth_alive=>"祖母存歿\");
$default_check_item=array(stud_name=>"1",stud_name_eng=>"1",stud_sex=>"1",stud_birthday=>"1",stud_blood_type=>"",stud_birth_place=>"1",stud_kind=>"1",stud_country=>"1",stud_country_kind=>"1",stud_person_id=>"1",stud_country_name=>"",stud_addr_1=>"1",stud_addr_2=>"1",stud_tel_1=>"1",stud_tel_2=>"1",stud_tel_3=>"",stud_mail=>"",stud_class_kind=>"1",stud_spe_kind=>"",stud_spe_class_kind=>"",stud_spe_class_id=>"",stud_preschool_status=>"",stud_preschool_id=>"",stud_preschool_name=>"",stud_Mschool_status=>"1",stud_mschool_id=>"",stud_mschool_name=>"",stud_study_year=>"1",enroll_school=>"1",fath_name=>"1",fath_birthyear=>"1",fath_alive=>"1",fath_relation=>"1",fath_p_id=>"",fath_education=>"",fath_grad_kind=>"",fath_occupation=>"1",fath_unit=>"1",fath_work_name=>"",fath_phone=>"",fath_home_phone=>"",fath_hand_phone=>"1",fath_email=>"",moth_name=>"1",moth_birthyear=>"1",moth_alive=>"1",moth_relation=>"1",moth_p_id=>"",moth_education=>"",moth_grad_kind=>"",moth_occupation=>"1",moth_unit=>"1",moth_work_name=>"",moth_phone=>"",moth_home_phone=>"",moth_hand_phone=>"1",moth_email=>"",guardian_name=>"1",guardian_phone=>"1",guardian_address=>"",guardian_relation=>"1",guardian_p_id=>"",guardian_unit=>"1",guardian_work_name=>"1",guardian_hand_phone=>"1",guardian_email=>"",grandfath_name=>"",grandfath_alive=>"",grandmoth_name=>"",grandmoth_alive=>"");
$error_array=array();

if($_POST[go]){
	//抓取編班記錄裡的學生列表(只抓目前在學學生)
	$sql="select a.*,b.* from stud_base a left join stud_domicile b on a.student_sn=b.student_sn where stud_study_cond in (0,15) order by curr_class_num";
	$res=$CONN->Execute($sql) or trigger_error("SQL語法錯誤：$sql", E_USER_ERROR);
	while(!$res->EOF){
		$stud_id=$res->fields[stud_id];
		$student_sn=$res->fields[student_sn];
		$stud_name=$res->fields[stud_name];
		$grade=substr($res->fields[curr_class_num],0,-4);
		$curr_class_num=$res->fields[curr_class_num];
		$class_id=substr($curr_class_num,0,-2);
		$class_name=$class_name_arr[$class_id];
		
		foreach($default_check_item as $key=>$value){
			if($_POST[$key])
			if(! $res->fields[$key]){
				$error_array[$class_id][$curr_class_num][stud_name]=$stud_name;
				$error_array[$class_id][$curr_class_num][class_name]=$class_name;			
				$error_array[$class_id][$curr_class_num][error].=$check_array[$key].',';	
			}
		}
		$res->MoveNext();
	}


	//開始列表
	$showdata="<table border=1 cellpadding=3 cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr align='center' bgcolor='#ffcccc'><td>目前班級</td><td>座號</td><td>姓名</td><td>未填寫項目</td></tr>";
	$class_info='※班級人數統計：';			
	foreach($error_array as $class_id=>$students){
		$class_count=count($students);
		$class_info.="{$class_name_arr[$class_id]}($class_count);";
		foreach($students as $curr_class_num=>$value){
			$class_no=substr($curr_class_num,-2);
			$showdata.="<tr align='center'><td width=70>{$value[class_name]}</td><td width=30>$class_no</td><td width=70>{$value[stud_name]}</td><td align='left'>{$value[error]}</td></tr>";
		}
	}
	$showdata.="</table>";
	echo "$showdata<br><font color='red' size=2>$class_info</font>";
	exit;
}

$year_seme=$_POST[year_seme]?$_POST[year_seme]:sprintf("%03d%d",curr_year(),curr_seme());

head("學籍基本資料完整性檢查");
print_menu($menu_p);


//設定要檢查的欄位
$check_item_list="<table border=1 cellpadding=3 cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
	<tr align='center' bgcolor='#ffcccc'><td>要檢查的欄位</td></tr>";
$item_list='';
foreach($check_array as $key=>$value){
	$checked=$default_check_item[$key]?' checked':'';
	$color=$default_check_item[$key]?'red':'grey';
	$item_list.="<input type='checkbox' name='$key'$checked onclick=''><font color='$color'>$value</font> ";
}
$check_item_list.="<tr><td>$item_list</td></tr><tr align='center'><td><input type='submit' name='go' style='border-width:2px; cursor:hand; font-size=16px; color:black; width:150; background:#aaffaa;' value='按我開始檢查'></td></tr></table>";

echo "<form name='myform' method='post' target='_BLANK'>$check_item_list</form>";
foot();

?>
