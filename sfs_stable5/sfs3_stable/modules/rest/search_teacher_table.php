<?php
//
// ���o�t�Τ��Юv�Ҫ�
//

// �ޤJ �禡�w

include_once "../../include/sfs_case_score.php";
//require_once "../../include/sfs_core_globals.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";

$sel_year=curr_year();
$sel_seme=curr_seme();

$sql="select day from school_day where year='$sel_year' and seme='$sel_seme' and day_kind='st_start'";
$res=$CONN->Execute($sql);
$st_start=$res->fields['day'];
$sql="select day from school_day where year='$sel_year' and seme='$sel_seme' and day_kind='st_end'";
$res=$CONN->Execute($sql);
$st_end=$res->fields['day'];

   $sql_select = "select course_id,class_id,day,sector,ss_id,room,c_kind from score_course where year='$sel_year' and semester='$sel_seme' and (teacher_sn='" . $params['teacher_sn'] . "' or cooperate_sn='" . $params['teacher_sn'] . "') order by day,sector";
    $recordSet = $CONN->Execute($sql_select) or user_error("���~�T���G", $sql_select, 256);
    while (list($course_id, $class_id, $day, $sector, $ss_id, $room, $c_kind) = $recordSet->FetchRow()) {

        $k = $day . "_" . $sector;
        $a[$k] = $ss_id;
        $b[$k] = $class_id;
        $room[$k] = $room;
        $course_id_arr[$k] = $course_id;
        //�O���O�_���ݽ�  0:�@��  1:�ݽ�
        $c_kind_arr[$k] = $c_kind;

        //���o�Z�Ÿ��
        $the_class = get_class_all($b[$k]);
        $class_name = ($the_class[name] == "�Z") ? "" : $the_class[name];

        $data[$day][$sector]['subject'] = get_ss_name("", "", "�u", $a[$k]);    //���
        $data[$day][$sector]['class_name'] = $class_name;                       //�Z��
        $data[$day][$sector]['class_id'] = $class_id;                           //$class_id
        $data[$day][$sector]['room'] = $room[$k];                               //�W�Ҧa�I

    }

//�^�� �A�W�Ҵ����~���Ҫ�
$data['st_start']=$st_start;
$data['st_end']=$st_end;




