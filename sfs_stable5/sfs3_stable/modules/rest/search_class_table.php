<?php
//
// ���o�t�Τ��Y�Z�Ҫ�
//

// �ޤJ �禡�w

include_once "../../include/sfs_case_score.php";
//require_once "../../include/sfs_core_globals.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";

//�ǤJ���禡 $params['class'] 101 , 102 ..... 701, 702 ... �� , �A�ର class_id , �Ϊ����ǤJ class_id
if ($params['class']!='') {
    $sel_year=curr_year();
    $sel_seme=curr_seme();
    $class_id=sprintf("%03d_%1d_%02d_%02d",$sel_year,$sel_seme,substr($params['class'],0,1),substr($params['class'],1,2));
} elseif ($params['class_id']!='') {
    $class_id=$params['class_id'];
    $sel_year=substr($class_id,0,3);
    $sel_seme=substr($class_id,4,1);
} else {
    $class_id="";
}

if ($class_id!='') {

    $sql = "select day from school_day where year='$sel_year' and seme='$sel_seme' and day_kind='st_start'";
    $res = $CONN->Execute($sql);
    $st_start = $res->fields['day'];    //�Ǵ��}�l
    $sql = "select day from school_day where year='$sel_year' and seme='$sel_seme' and day_kind='st_end'";
    $res = $CONN->Execute($sql);
    $st_end = $res->fields['day'];      //�Ǵ�����


    $sql_select = "select course_id,teacher_sn,cooperate_sn,day,sector,ss_id,room from score_course where class_id='" . $class_id . "' order by day,sector";

    $recordSet = $CONN->Execute($sql_select) or user_error("���~�T���G", $sql_select, 256);
    while (list($course_id, $teacher_sn, $cooperate_sn, $day, $sector, $ss_id, $room) = $recordSet->FetchRow()) {
        $k = $day . "_" . $sector;
        $a[$k] = $ss_id;          //get_ss_name("","","�u",$a[$k])
        $b[$k] = $teacher_sn;     //get_teacher_name($b[$k])
        $co[$k] = $cooperate_sn;  //get_teacher_name($b[$k])
        $r[$k] = $room;


        //2017.10.24 �אּ2���覡�ӧe�{�P���M�`���Ҫ��e
        $data[$day][$sector]['subject'] = get_ss_name("", "", "�u", $a[$k]);    //���
        $data[$day][$sector]['teacher'] = get_teacher_name($b[$k]);          //�Юv
        $data[$day][$sector]['teacher_sn'] = $b[$k];                         //�Юvsn
        $data[$day][$sector]['co_teacher'] = get_teacher_name($co[$k]);      //��P�Юv
        $data[$day][$sector]['co_teacher_sn'] = $co[$k];                     //��P�Юv sn
        $data[$day][$sector]['room'] = $room;                                //�W�Ҧa�I

    }


   //�^�ǡA�W�Ҵ����~���Ҫ�
    $data['st_start'] = $st_start;
    $data['st_end'] = $st_end;
    $data['class_id'] = $class_id;
}
