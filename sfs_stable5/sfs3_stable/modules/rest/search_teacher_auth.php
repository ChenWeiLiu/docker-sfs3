<?php
//
// ���o�Юv�{�Ҹ�T�\��
// �̾ڱЮv�������Ҧr�� , �^��  teacher_base ���� login_pass ��,
//  login_pass ���s�X�L������ȡA�ĤT�����ε{���ݦۦ�N��檺�K�X�s�X����
//
//  $params['teach_person_id'] �����Ҧr�� �ǤJ�������Ҧr���w�i��B�z, ���H���X�ǰe
//
//
    //�u���̷s�@�� , �� move_date �Ƨ�
    $sql_select = "select login_pass from teacher_base  where sha2(teach_person_id, 256) = '".$params['teach_person_id']."' and teach_person_id!=''";
    $recordSet=$CONN->Execute($sql_select) or die($sql_select);

    if ($recordSet->RecordCount()>0) {
        $row=$recordSet->fetchRow();
        $SERVICE['result']=1;
        $data['login_pass']=$row['login_pass'];             //�`�N, ��^�ȥ����O array
    } else {
        //$data=array();
        $SERVICE['result']=-1;
        $SERVICE['message']="�d�L���H!";
        $data="";
    }
