<?php
//
// �d�߬Y��i�սҸ`��
//

// �ޤJ �禡�w

include_once "../../include/sfs_case_score.php";
//require_once "../../include/sfs_core_globals.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";

//�ǤJ���禡
/*   $params['class_id'] �Z��   �� $params['class'] (�|����Ǵ�)
 *   $params['week']     �P���X
 *   $params['sector']   �ĴX�`
 *   $params['new_day']  �ը�X��X��
 *
 *   �t�Φ^��, �Ӥ���X�`�i�H��  array
 */

 $org_week=$params['week'];
 $org_sector=$params['sector'];
 $new_day=$params['new_day'];

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

    //�^�ǡA�W�Ҵ����~���Ҫ�
    $data['st_start'] = $st_start;
    $data['st_end'] = $st_end;


    //�����o�ӯZ�Ҫ�
    $sql_select = "select course_id,teacher_sn,cooperate_sn,day,sector,ss_id,room from score_course where class_id='" . $class_id . "' order by day,sector";
    $recordSet = $CONN->Execute($sql_select) or user_error("���~�T���G", $sql_select, 256);
    while (list($course_id, $teacher_sn, $cooperate_sn, $day, $sector, $ss_id, $room) = $recordSet->FetchRow()) {
        $k = $day . "_" . $sector;
        $a[$k] = $ss_id;          //get_ss_name("","","�u",$a[$k])
        $b[$k] = $teacher_sn;     //get_teacher_name($b[$k])
        $co[$k] = $cooperate_sn;  //get_teacher_name($b[$k])
        $r[$k] = $room;

        //2017.10.24 �אּ2���覡�ӧe�{�P���M�`���Ҫ��e
        $class_table[$day][$sector]['subject'] = get_ss_name("", "", "�u", $a[$k]);    //���
        $class_table[$day][$sector]['teacher'] = get_teacher_name($b[$k]);          //�Юv
        $class_table[$day][$sector]['teacher_sn'] = $b[$k];                         //�Юvsn
        $class_table[$day][$sector]['co_teacher'] = get_teacher_name($co[$k]);      //��P�Юv
        $class_table[$day][$sector]['co_teacher_sn'] = $co[$k];                     //��P�Юv sn
        $class_table[$day][$sector]['room'] = $room;                                //�W�Ҧa�I

    }

    //�n�ժ����@�` ���Ѯv
    $the_teacher_sn=$class_table[$org_week][$org_sector]['teacher_sn'];
    //�n�սҪ��Ѯv���Ҫ�
    $the_teacher_table=teacher_table($the_teacher_sn);


    //�ؼФ鬰�P���X
    $new_week=date("w",strtotime($new_day." 00:00:00"));

    //�ˬd�Ӥ�C�@�`, �Ӹ` �n�սҪ��Ѯv���঳��, �Ӹ`�Q�ժ��Ѯv, �b  $params['week'] , $params['sector'] ���঳��
    $data=array();

    foreach ($class_table[$new_week] as $new_sector=>$v) {
        //�ˬd, �p�G�o�@�`�n�սҪ��Ѯv�S�Ҥ~�B�z (�p�G�ۤv����, �N�����)
        if ($the_teacher_table[$new_week][$new_sector]['subject']=='') {
            //�Q�սҦѮv�� teacher_sn
            $tuneup_teacher_sn=$class_table[$new_week][$new_sector]['teacher_sn'];
            //���o�Q�սҦѮv���Ҫ�, �Ӯv�b $org_week , $org_sector ���঳��
            $tuneup_teacher_table=teacher_table($tuneup_teacher_sn);
            //�S��, �C�J�սҥؼ�
            if ($tuneup_teacher_table[$org_week][$org_sector]['subject']=='') {
                $data[$new_week][$new_sector]['subject']=$tuneup_teacher_table[$new_week][$new_sector]['subject'];
                $data[$new_week][$new_sector]['teacher']=get_teacher_name($tuneup_teacher_sn);
                $data[$new_week][$new_sector]['teacher_sn']=$tuneup_teacher_sn;
            }

        }

    } // end foreach

}



//�Ѯv���Ҫ�
function teacher_table($teacher_sn) {

    global $CONN,$sel_year,$sel_seme;

    $data=array();

    $sql_select = "select course_id,class_id,day,sector,ss_id,room,c_kind from score_course where year='$sel_year' and semester='$sel_seme' and (teacher_sn='" . $teacher_sn . "' or cooperate_sn='" . $teacher_sn . "') order by day,sector";
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

    } // end while

    return $data;
}