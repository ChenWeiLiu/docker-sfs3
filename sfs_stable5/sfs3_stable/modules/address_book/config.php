<?php
//$Id: config.php 6064 2010-08-31 12:26:33Z infodaes $
include_once "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";
require_once "./module-cfg.php";
//require_once "./module-upgrade.php";

//取得模組參數的類別設定
$m_arr =&get_module_setup("address_book");

$sex_arr= array(1=>"男" ,2 =>"女") ;
$room_kind=room_kind();
$title_kind=title_kind();
$birth_place_array=birth_state();
$class_name_arr = class_base() ;
$guardian_relation=guardian_relation();
$is_live=is_live();
$blood_arr=blood();
$obtain_arr=stud_obtain_kind();
$safeguard_arr=stud_safeguard_kind();


//取得教師所處處室
$my_sn=$_SESSION['session_tea_sn'];
$my_name=$_SESSION['session_tea_name'];
$sql="select post_office,teach_title_id from teacher_post where teacher_sn=$my_sn;";
$rs=$CONN->Execute($sql) or die("無法取得您的所在處室!<br>$sql");
$my_room=$room_kind[($rs->fields['post_office'])];
$my_title=$title_kind[($rs->fields['teach_title_id'])];

//echo $my_room;
//print_r($room_kind);

//設定可選用類別
$nature_array=array('student'=>'學生','teacher'=>'教師');
$nature=$_POST['nature']?$_POST['nature']:'student';

//非模組管理員禁用項目禁用
$forbid=$nature.'_forbid';
$forbid=$m_arr[$forbid];

$nature_radio="<hr>※類別:";
foreach($nature_array as $key=>$value)
{
	$nature_selected=$key==$nature?'checked':'';
	$nature_radio.="<input type='radio' value='$key' name='nature' $nature_selected  onclick='this.form.target_sn.value=\"\"; this.form.act.value=\"\"; this.form.action=\"$_SERVER[SCRIPT_NAME]\"; this.form.target=\"_self\"; this.form.submit();'>$value";
}

//定義可用欄位
switch ($nature) {
    case 'student':
        $fields_array=array('a.class_id'=>'班級代號','a.class_name'=>'班級名稱','a.grade'=>'年級','a.curr_class_num'=>'座號','a.stud_id'=>'學號','a.stud_name'=>'學生姓名','a.stud_name_eng'=>'學生英文姓名','a.stud_sex'=>'性別','a.stud_person_id'=>'身份證字號','a.stud_blood_type'=>'血型','a.stud_country'=>'國籍','a.addr_zip'=>'郵遞區號','a.stud_addr_1'=>'戶籍地址','a.stud_addr_2'=>'聯絡地址','a.stud_birthday'=>'出生年月日','a.year'=>'出生年','a.month'=>'出生月','a.day'=>'出生日','a.stud_tel_1'=>'戶籍電話','a.stud_tel_2'=>'聯絡電話','a.stud_tel_3'=>'行動電話','a.stud_study_year'=>'入學年','a.stud_preschool_name'=>'入學前幼稚園','a.stud_mschool_name'=>'入學前國小','a.addr_move_in'=>'戶籍遷入日期','a.enroll_school'=>'入學學校','a.stud_birth_place'=>'出生地','a.obtain'=>'學籍取得原因','a.safeguard'=>'個案保護類別','b.fath_name'=>'父親姓名','b.fath_alive'=>'父親存歿\','b.fath_birthyear'=>'父_年次','b.fath_occupation'=>'父_職業','b.fath_unit'=>'父_服務單位','b.fath_work_name'=>'父_職稱','b.fath_hand_phone'=>'父_行動電話','b.moth_name'=>'母親姓名','b.moth_alive'=>'母親存歿\','b.moth_birthyear'=>'母_年次','b.moth_occupation'=>'母_職業','b.moth_unit'=>'母_服務單位','b.moth_work_name'=>'母_職稱','b.moth_hand_phone'=>'母_行動電話','b.guardian_name'=>'監護人姓名','b.guardian_relation'=>'與監護人關係','b.guardian_email'=>'監護人_電子郵件','b.guardian_unit'=>'監護人_服務單位','b.guardian_work_name'=>'監護人_職稱','b.guardian_hand_phone'=>'監護人_行動電話','b.grandfath_name'=>'祖父姓名','b.grandfath_alive'=>'祖父存歿\','b.grandmoth_name'=>'祖母姓名','b.grandmoth_alive'=>'祖母存歿\','a.stud_kind'=>'學生身分類別');
        break;
    case 'teacher':
        $fields_array=array('a.teacher_sn'=>'系統編號','a.teach_id'=>'代號','a.teach_person_id'=>'身份證字號','a.name'=>'姓名','a.sex'=>'性別','a.birthday'=>'出生年月日','a.birth_place'=>'出生地','a.address'=>'地址','a.home_phone'=>'家庭電話','a.cell_phone'=>'行動電話','b.email'=>'電子郵件');
        break;
}

$curr_year_seme=$_POST['seme_year_seme']?$_POST['seme_year_seme']:sprintf('%03d%d',curr_year(),curr_seme());
$curr_year=intval(substr($curr_year_seme,0,3));
$curr_seme=intval(substr($curr_year_seme,-1));
$page_break ="<P style='page-break-after:always'>&nbsp;</P>";

?>
