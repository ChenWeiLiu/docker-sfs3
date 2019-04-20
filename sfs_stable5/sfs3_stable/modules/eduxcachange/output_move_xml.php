<?php
// $Id: output_xml.php 7713 2013-10-25 02:29:35Z smallduh $

require "config.php";
require "class.php";
require "chinese.lib.php";

sfs_check();

$move_kind_arr=array("0"=>" -- �п�� -- ","8"=>"�ծ�","5"=>"���~");

$all_reward=$_POST['all_reward'];


//�Y����ܾǦ~�Ǵ��A�i����Ψ��o�Ǧ~�ξǴ�
if(!empty($_POST[year_seme])){
	$sel_year=intval(substr($_POST[year_seme],0,-1));
	$sel_seme=substr($_POST[year_seme],-1,1);
	$year_seme=$_POST[year_seme];
} else {
	$sel_year=curr_year(); //�ثe�Ǧ~
	$sel_seme=curr_seme(); //�ثe�Ǵ�
	$year_seme=sprintf("%03d",$sel_year).$sel_seme;
}
//���o�ǮեN�X
$school_base=get_school_base();
$smarty->assign("sch_id",$school_base['sch_id']);

//�������O���
$sel1=new drop_select();
$sel1->s_name="move_kind";
$sel1->id=$_POST[move_kind];
$sel1->arr=$move_kind_arr;
$sel1->has_empty=false;
$sel1->is_submit=true;
$smarty->assign("move_kind_sel",$sel1->get_select());

//�Ǵ����
$sel1=new drop_select();
$sel1->s_name="year_seme";
$sel1->id=$year_seme;
$sel1->arr=get_class_seme();
$sel1->has_empty=false;
$sel1->is_submit=true;
$smarty->assign("year_seme_sel",$sel1->get_select());

if ($_POST[move_kind]=="8") {
		$smarty->assign("form_kind","1");
} else {
		$smarty->assign("form_kind","2");
}
$query="select a.*,b.stud_name from stud_move a ,stud_base b where a.student_sn=b.student_sn and a.move_year_seme='".intval($year_seme)."' and a.move_kind='$_POST[move_kind]' order by a.move_date desc,a.stud_id desc";
//���X�Ҧ��O��
$res=$CONN->Execute($query) or die($query);
$smarty->assign("stud_move",$res->GetRows());

if ($_POST[out_arr]) {
	$xml_obj=new sfsxmlfile();
	$xml_obj->student_sn=$_POST[choice];
	$xml_obj->output();
	$smarty->assign("data_arr",$xml_obj->out_arr);
	$smarty->assign("sex_arr",array("1"=>"�k","2"=>"�k"));
	echo "<pre>";
	print_r($xml_obj->out_arr);
	echo "</pre>";
	exit;
}

//�p�G�T�w��XXML�ɮ�
if ($_POST[output_xml]) {
	//�Ӹ�O��	
	$sn=implode(",",array_keys($_POST[choice]));
	$test=pipa_log("XML�ץX�@�~\r\n�ǥͬy�����G$sn\r\n");
	
	$xml_obj=new sfsxmlfile();
	$xml_obj->student_sn=$_POST[choice];
	$xml_obj->output();

	//igogo ���N���utf8,����ަ�chinese.lib.php
	$obj = new Sfs3Data;

	//���y���
	$xml_obj->out_arr = $obj->array_big5_to_utf8($xml_obj->out_arr);
	$smarty->assign("data_arr",$xml_obj->out_arr);

	//�ʧO�}�C
	$sex_arr = $obj->array_big5_to_utf8(array("1"=>"�k","2"=>"�k"));
	//$smarty->assign("sex_arr",array("1"=>"�k","2"=>"�k"));
	$smarty->assign("sex_arr",$sex_arr);

	//�����O�}�C (�Ƶ��Ȥ�����)
	$stud_kind_arr = $obj->array_big5_to_utf8(stud_kind());
	$smarty->assign("stud_kind_arr",$stud_kind_arr);

	//�ҷ����O�}�C
	$stud_country_kind =  $obj->array_big5_to_utf8(stud_country_kind());
	$smarty->assign("id_kind_arr",$stud_country_kind);

	//�ǥͯZ�ũʽ�}�C
	$stud_class_kind =  $obj->array_big5_to_utf8(stud_class_kind());
	$smarty->assign("class_kind_arr",$stud_class_kind);
	
	//�ǥͯS��Z���O�}�C
	$stud_spe_kind = $obj->array_big5_to_utf8(stud_spe_kind());
	$smarty->assign("spe_kind_arr",$stud_spe_kind);

	//�ǥͯS��Z�W�ҩʽ�}�C
	$stud_spe_class_id = $obj->array_big5_to_utf8(stud_spe_class_id());
	$smarty->assign("spe_class_id_arr",$stud_spe_class_id);


	//�ǥͯS��Z�Z�O�}�C
	$stud_spe_class_kind = $obj->array_big5_to_utf8(stud_spe_class_kind());
	$smarty->assign("spe_class_kind_arr",$stud_spe_class_kind);


	//�ꤤ�p�P�w SFS 4.0 �����ץ�
	$smarty->assign("jhores",$IS_JHORES);

	//�J�Ǹ��}�C
	$stud_preschool_status = $obj->array_big5_to_utf8(stud_preschool_status());
	$smarty->assign("preschool_status_arr",$stud_preschool_status);
	
	//���׷~�}�C
	$grad_kind = $obj->array_big5_to_utf8(grad_kind());
	$smarty->assign("grad_kind_arr",$grad_kind);

	//�s�\�}�C
	$is_live = $obj->array_big5_to_utf8(is_live());
	$smarty->assign("is_live_arr",$is_live);

	//�P�����Y�}�C
	$fath_relation = $obj->array_big5_to_utf8(fath_relation());
	$smarty->assign("f_rela_arr",$fath_relation);

	//�P�����Y�}�C
	$moth_relation = $obj->array_big5_to_utf8(moth_relation());
	$smarty->assign("m_rela_arr",$moth_relation);

	//�P���@�H���Y�}�C
	$guardian_relation = $obj->array_big5_to_utf8(guardian_relation());
	$smarty->assign("g_rela_arr",$guardian_relation);

	//�Ǿ��}�C
	$edu_kind = $obj->array_big5_to_utf8(edu_kind());
	$smarty->assign("edu_kind_arr",$edu_kind);

	//�S�̩j�f�}�C
	$bs_calling_kind = $obj->array_big5_to_utf8(bs_calling_kind());
	$smarty->assign("bs_calling_kind_arr",$bs_calling_kind);
	
	//�ͲP���ɦҼ{�]���}�C
	$factor_items=array('self'=>'�ӤH�]��','env'=>'���Ҧ]��','info'=>'��T�]��');
	foreach($factor_items as $item=>$title){
		$factors[$item]=SFS_TEXT($title);				
	}

	$factors = $obj->array_big5_to_utf8($factors);
	$smarty->assign("factors",$factors);
	
	//����U�Ǵ����X�u���
	$query="select * from seme_course_date order by seme_year_seme,class_year";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$current_seme_year_seme=$res->fields[seme_year_seme];
		$row_data=$res->FetchRow();
		$seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']]=$row_data['days'];
	}
	//print_r($seme_course_date_arr);
	$smarty->assign("seme_course_date_arr",$seme_course_date_arr);
	
	//���ɭӮ׸�Ʀ]���e�A�����p���t�μȮɤ��洫, �Hnull�ȳB�z
	
//echo "<pre>";	
//print_r($xml_obj->out_arr);
//echo "</pre>";	
//exit;

	//$filename=$SCHOOL_BASE['sch_id'].$school_long_name.date('Ymd')."_XML_3_0�洫���.xml";
	$filename_zip=$SCHOOL_BASE['sch_id']."_XML_".date('Ymd').".zip";
	$filename_xml=$SCHOOL_BASE['sch_id']."_XML_".date('Ymd').".xml";
	$temp_dir=$UPLOAD_PATH.$path_str."eduxcachange/";
	if (!is_dir($temp_dir)) mkdir($temp_dir,0700);
	//�Nsmarty��X����ƥ�cache��
	ob_start();
	$smarty->display("eduxcachange.tpl");
	$xmls=ob_get_contents();
	ob_end_clean();
	
	//�N�ŭȥHnull���N
	$xmls=str_replace("><",">null<",$xmls);

	//�নUnicode���X�ɮ�
	//echo iconv("Big5","UTF-8",$xmls);
	$xml_file = file_put_contents($temp_dir.$filename_xml, $xmls);
    unset($xmls);
	
	//���Xzip��
	if($xml_file === FALSE){
      die("�����ɮץ���");
    }else{
	  $zip = new ZipArchive;
      $xml_zip = $zip->open($temp_dir.$filename_zip,ZipArchive::CREATE);
      if($xml_zip === TRUE) {
        $zip->addFile($temp_dir.$filename_xml, $filename_xml);
        $zip->close();
        unlink($temp_dir.$filename_xml);
      }else{
        unlink($temp_dir.$filename_xml);
        die("�������Y�ɥ���");
      }
    }
    echo basename($filename_zip);
	header("Content-disposition: attachment; filename=$filename_zip");
	header('Content-Type: application/zip');
	header("Content-Type:text/xml; charset=utf-8");
  
  //�]�� IE 6,7,8 �b SSL �Ҧ��U�L�k�U���A���� no-cache �אּ�H�U
	header("Cache-Control: max-age=0");
	header("Pragma: public");
	header("Expires: 0");
	header('Content-Length: ' . filesize($temp_dir.$filename_zip));
    readfile($temp_dir.$filename_zip);
	exit;
}

//�ꤤ�[�J�ͲP���ɿ�X�ﶵ
$checked=$IS_JHOES?'checked':'';
$career_checkbox="<input type='checkbox' name='career' value=1 $checked>��X�ꤤ�ͲP���ɤ�U���(�ݦ��w�ˬ����Ҳ�)";
$smarty->assign("career_checkbox",$career_checkbox);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","XML�洫�@�~");
$smarty->assign("SFS_MENU",$toxml_menu);
$smarty->display("toxml_output_xml.tpl");
?>
