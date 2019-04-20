<?php

// $Id: output_xml.php 7713 2013-10-25 02:29:35Z smallduh $

require "config.php";
require "class.php";
require "chinese.lib.php";

$sel_year = curr_year(); //�ثe�Ǧ~
$sel_seme = curr_seme(); //�ثe�Ǵ�
$year_seme = sprintf("%03d", $sel_year) . $sel_seme; //�{�b���Ǧ~�Ǵ�ex.1041,1042

//���ߺݤ䴩
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}
$temp_dir=$UPLOAD_PATH."eduxcachange/";

//���o�ǮեN�X
$school_base = get_school_base();
$smarty->assign("sch_id", $school_base['sch_id']);

//���X�Ҧ��O��
//(�u�C�@�Ӧ~�Ŵ��t��)
$stud_query = "select * from stud_base where stud_study_cond in (0,15) and substring(curr_class_num,1,1)=5 order by curr_class_num";
//$stud_query="select * from stud_base where stud_study_cond in (0,15) order by curr_class_num";
$stud_res = $CONN->Execute($stud_query) or die($stud_query);
$stud_arr = array();
while (!$stud_res->EOF) {
      array_push($stud_arr, $stud_res->fields[student_sn]);
      $stud_res->MoveNext(); 
}



//�p�G�T�w��XXML�ɮ�
//�Ӹ�O��	
//$sn=implode(",",array_keys($stud_arr));
//$test=pipa_log("XML�ץX�@�~\r\n�ǥͬy�����G$sn\r\n");
$xml_obj = new sfsxmlfile();
$xml_obj->student_sn = $stud_arr;
$xml_obj->output();

//igogo ���N���utf8,����ަ�chinese.lib.php
$obj = new Sfs3Data;

$ary = $obj->array_big5_to_utf8($xml_obj->out_arr);
//���y���
$xml_obj->out_arr = $ary;
$smarty->assign("data_arr", $xml_obj->out_arr);

//�ʧO�}�C
$sex_arr = $obj->array_big5_to_utf8(array("1" => "�k", "2" => "�k"));
//$smarty->assign("sex_arr",array("1"=>"�k","2"=>"�k"));
$smarty->assign("sex_arr", $sex_arr);

//�����O�}�C (�Ƶ��Ȥ�����)
$stud_kind_arr = $obj->array_big5_to_utf8(stud_kind());
$smarty->assign("stud_kind_arr", $stud_kind_arr);

//�ҷ����O�}�C
$stud_country_kind = $obj->array_big5_to_utf8(stud_country_kind());
$smarty->assign("id_kind_arr", $stud_country_kind);

//�ǥͯZ�ũʽ�}�C
$stud_class_kind = $obj->array_big5_to_utf8(stud_class_kind());
$smarty->assign("class_kind_arr", $stud_class_kind);

//�ǥͯS��Z���O�}�C
$stud_spe_kind = $obj->array_big5_to_utf8(stud_spe_kind());
$smarty->assign("spe_kind_arr", $stud_spe_kind);

//�ǥͯS��Z�W�ҩʽ�}�C
$stud_spe_class_id = $obj->array_big5_to_utf8(stud_spe_class_id());
$smarty->assign("spe_class_id_arr", $stud_spe_class_id);


//�ǥͯS��Z�Z�O�}�C
$stud_spe_class_kind = $obj->array_big5_to_utf8(stud_spe_class_kind());
$smarty->assign("spe_class_kind_arr", $stud_spe_class_kind);


//�ꤤ�p�P�w SFS 4.0 �����ץ�
$smarty->assign("jhores", $IS_JHORES);

//�J�Ǹ��}�C
$stud_preschool_status = $obj->array_big5_to_utf8(stud_preschool_status());
$smarty->assign("preschool_status_arr", $stud_preschool_status);

//���׷~�}�C
$grad_kind = $obj->array_big5_to_utf8(grad_kind());
$smarty->assign("grad_kind_arr", $grad_kind);

//�s�\�}�C
$is_live = $obj->array_big5_to_utf8(is_live());
$smarty->assign("is_live_arr", $is_live);

//�P�����Y�}�C
$fath_relation = $obj->array_big5_to_utf8(fath_relation());
$smarty->assign("f_rela_arr", $fath_relation);

//�P�����Y�}�C
$moth_relation = $obj->array_big5_to_utf8(moth_relation());
$smarty->assign("m_rela_arr", $moth_relation);

//�P���@�H���Y�}�C
$guardian_relation = $obj->array_big5_to_utf8(guardian_relation());
$smarty->assign("g_rela_arr", $guardian_relation);

//�Ǿ��}�C
$edu_kind = $obj->array_big5_to_utf8(edu_kind());
$smarty->assign("edu_kind_arr", $edu_kind);

//�S�̩j�f�}�C
$bs_calling_kind = $obj->array_big5_to_utf8(bs_calling_kind());
$smarty->assign("bs_calling_kind_arr", $bs_calling_kind);

//�ͲP���ɦҼ{�]���}�C
$factor_items = array('self' => '�ӤH�]��', 'env' => '���Ҧ]��', 'info' => '��T�]��');
foreach ($factor_items as $item => $title) {
    $factors[$item] = SFS_TEXT($title);
}

$factors = $obj->array_big5_to_utf8($factors);
$smarty->assign("factors", $factors);

//����U�Ǵ����X�u���
$query = "select * from seme_course_date order by seme_year_seme,class_year";
$res = $CONN->Execute($query);
while (!$res->EOF) {
    $current_seme_year_seme = $res->fields[seme_year_seme];
    $row_data = $res->FetchRow();
    $seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']] = $row_data['days'];
}
//print_r($seme_course_date_arr);
$smarty->assign("seme_course_date_arr", $seme_course_date_arr);

//���ɭӮ׸�Ʀ]���e�A�����p���t�μȮɤ��洫, �Hnull�ȳB�z
//echo "<pre>";	
//print_r($xml_obj->out_arr);
//echo "</pre>";	
//exit;
//$filename=$SCHOOL_BASE['sch_id'].$school_long_name.date('Ymd')."_XML_3_0�洫���.xml";
$filename_xml = $SCHOOL_BASE['sch_id'] . "_XML_5.xml";
//�Nsmarty��X����ƥ�cache��
ob_start();
$smarty->display("eduxcachange.tpl");
$xmls = ob_get_contents();
ob_end_clean();

//�N�ŭȥHnull���N
//$xmls = str_replace("><", ">null<", $xmls);

//�নUnicode���X�ɮ�
//echo iconv("Big5","UTF-8",$xmls);
$xml_file = file_put_contents($temp_dir . $filename_xml, $xmls);
unset($xmls);
exit;
?>
