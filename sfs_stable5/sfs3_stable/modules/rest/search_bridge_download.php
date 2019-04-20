<?php
// $Id: output_xml.php 8928 2016-07-20 18:11:45Z smallduh $

include_once "xml_function.php";

//���o�ҲհѼƪ����O�]�w
$m_arr = get_module_setup('toxml');
extract($m_arr,EXTR_OVERWRITE);

require "../toxml/class.php";

//�u���̷s�@�� , �� move_date �Ƨ�
$sql_select = "select a.*,b.stud_name,b.stud_person_id from stud_move a,stud_base b where a.school_id='".$params['request_edu_id']."' and a.student_sn=b.student_sn and a.move_kind=8 and b.stud_person_id='".$params['stud_person_id']."' order by move_date desc limit 1";
$recordSet=$CONN->Execute($sql_select) or die($sql_select);

if ($recordSet->RecordCount()>0) {
	$row=$recordSet->fetchRow();
	//�O�_�L��
	if (strtotime(date("Y-m-d"))>strtotime($row['download_deadline']." 23:59:59")) {
        $data = array();
        $SERVICE['result'] = -1;
        $SERVICE['message'] = "�U�������w�L!";
    } elseif ($row['download_times']>=$row['download_limit']) {
        $data = array();
        $SERVICE['result'] = -1;
        $SERVICE['message'] = "�w�W�L�U�����ƭ���! (".$row['download_limit']."��)";
    } else {
		$SERVICE['result']=1;
        $row['from_school_name']=$SCHOOL_BASE['sch_cname'];
        $SERVICE['resource_student']=$row;   //�U���ǥ͸�T
		$student_sn=$row['student_sn'];
        //�U�����ƥ[1
        $download_times=$row['download_times']+1;
        $CONN->Execute("update `stud_move` set download_times='$download_times' where move_id='{$row['move_id']}'");
	}
} else {
	$data=array();
	$SERVICE['result']=-1;
	$SERVICE['message']="�d�L���ǥ���X�O��!";
}



//�p�G�T�w��XXML�ɮ�

if ($SERVICE['result']==1) {

	$stud_arr[$student_sn]=$student_sn;

            $xml_obj=new sfsxmlfile();
            $xml_obj->student_sn=$stud_arr;

	 	    $xml_obj->output();

            //�ǮեN�X $school_edu_id
            $smarty->assign("school_edu_id",$SCHOOL_BASE['sch_id']);
            //���y���
            $smarty->assign("data_arr",$xml_obj->out_arr);
            //�ʧO�}�C
            $smarty->assign("sex_arr",array("1"=>"�k","2"=>"�k"));
            //�����O�}�C (�Ƶ��Ȥ�����)
            $smarty->assign("stud_kind_arr",stud_kind());
            //�ҷ����O�}�C
            $smarty->assign("id_kind_arr",stud_country_kind());
            //�ǥͯZ�ũʽ�}�C
            $smarty->assign("class_kind_arr",stud_class_kind());

            //�ǥͯS��Z���O�}�C
            $smarty->assign("spe_kind_arr",stud_spe_kind());
            //�ǥͯS��Z�W�ҩʽ�}�C
            $smarty->assign("spe_class_id_arr",stud_spe_class_id());
            //�ǥͯS��Z�Z�O�}�C
            $smarty->assign("spe_class_kind_arr",stud_spe_class_kind());
            //�ꤤ�p�P�w SFS 4.0 �����ץ�
            $smarty->assign("jhores",$IS_JHORES);
            //�J�Ǹ��}�C
            $smarty->assign("preschool_status_arr",stud_preschool_status());

            //���׷~�}�C
            $smarty->assign("grad_kind_arr",grad_kind());

            //�s�\�}�C
            $smarty->assign("is_live_arr",is_live());
            //�P�����Y�}�C
            $smarty->assign("f_rela_arr",fath_relation());
            //�P�����Y�}�C
            $smarty->assign("m_rela_arr",moth_relation());
            //�P���@�H���Y�}�C
            $smarty->assign("g_rela_arr",guardian_relation());
            //�Ǿ��}�C
            $smarty->assign("edu_kind_arr",edu_kind());
            //�S�̩j�f�}�C
            $smarty->assign("bs_calling_kind_arr",bs_calling_kind());

            //�ͲP���ɦҼ{�]���}�C
            $factor_items=array('self'=>'�ӤH�]��','env'=>'���Ҧ]��','info'=>'��T�]��');
            foreach($factor_items as $item=>$title){
                $factors[$item]=SFS_TEXT($title);
            }
            $smarty->assign("factors",$factors);

            //����U�Ǵ����X�u���
            $query="select * from seme_course_date order by seme_year_seme,class_year";
            $res=$CONN->Execute($query);
            while(!$res->EOF) {
                $current_seme_year_seme=$res->fields[seme_year_seme];
                $row_data=$res->FetchRow();
                $seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']]=$row_data['days'];
            }
            $smarty->assign("seme_course_date_arr",$seme_course_date_arr);

            //�Nsmarty��X����ƥ�cache��
            ob_start();
            $smarty->display("student_3_0.tpl");
            $xmls=ob_get_contents();
            ob_end_clean();
            //ob_clean();
            //�N�ŭȥHnull���N
            $xmls=str_replace("><",">null<",$xmls);
            $xmls=str_replace("> <",">null<",$xmls);

            $data=$xmls;

}

?>