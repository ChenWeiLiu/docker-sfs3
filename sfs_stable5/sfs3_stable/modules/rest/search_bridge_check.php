<?php
//
// �d�߬O�_������X�ǥ�,�̫���Ʀs�b data �}�C��
//
//  $params['person_id'] �����Ҧr��
//
    //�u���̷s�@�� , �� move_date �Ƨ�
    $sql_select = "select a.*,b.stud_name,b.stud_person_id from stud_move a,stud_base b where a.school_id='".$params['request_edu_id']."' and a.student_sn=b.student_sn and a.move_kind=8 and b.stud_person_id='".$params['stud_person_id']."' order by move_date desc limit 1";
    $recordSet=$CONN->Execute($sql_select) or die($sql_select);

    if ($recordSet->RecordCount()>0) {
        $row=$recordSet->fetchRow();
        //�O�_�L��
        if (strtotime(date("Y-m-d"))>strtotime($row['download_deadline']." 23:59:59")) {
            $data=array();
            $SERVICE['result']=-1;
            $SERVICE['message']="�U�������w�L!";
        } elseif ($row['download_times']>=$row['download_limit']) {
            $data = array();
            $SERVICE['result'] = -1;
            $SERVICE['message'] = "�w�W�L�U�����ƭ���! (".$row['download_limit']."��)";
        } else {
            $data=$row;
        }
    } else {
        //$data=array();
        $SERVICE['result']=-1;
        $SERVICE['message']="�d�L���ǥ���X�O��!";
        $data="select a.*,b.stud_name,b.stud_person_id from stud_move a,stud_base b where a.school_id='".$params['request_edu_id']."' and a.student_sn=b.student_sn and a.move_kind=8 and b.stud_person_id='".$params['stud_person_id']."' order by move_date desc limit 1";
    }
