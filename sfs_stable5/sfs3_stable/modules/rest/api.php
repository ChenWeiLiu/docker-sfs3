<?php
include_once "config.php";

/*
 * RESTful Server �ݵ{��
 *
 */

$SERVICE['result']=1;									//1��ܦ��\, 0 ��ܥ���
$SERVICE['message']="";                                 //�^���T��
$SERVICE['request_method']=$_SERVER['REQUEST_METHOD'];	//���o  �I�s�ݪ� method
$SERVICE['request_ip']=getClientIP();					//���o  �I�s�ݪ�IP


//���ߺݾ���
if ($SFS_IS_CENTER_VER==1) {
//�̩I�s��k�B�z
    switch ($SERVICE['request_method']) {
        case 'POST':
            $edu_id = base64_decode($_POST['edu_id']);
            $SERVICE['result']=change_CONN();
            break;
        case 'GET':
            $edu_id = $_GET['edu_id'];
            $SERVICE['result']=change_CONN();
            break;
        default:
            $SERVICE['result'] = -1;
            $SERVICE['message']="���ߺݨt�ΡA�ʥF�ǮեN�X!";
    }
}  // end if ($SFS_IS_CENTER_VER==1)

 if ($SERVICE['result']==1) {

//���o�I�s�ݪ��{�Ҹ�T
        $Header = array();
        foreach (getallheaders() as $name => $v) {
            $Header[$name] = $v;
        }

        $sql = "select * from rest_manage where s_id='" . $Header['S_ID'] . "' and s_pwd='" . $Header['S_PWD'] . "'";
        $res = $CONN->Execute($sql);

        if ($res->RecordCount()) {
            $row = $res->fetchRow();

            $allow_ip = explode(",", $row['allow_ip']);            //���b���i�s�J�� ip
            $priv_get = explode(",", $row['method_get']);       //���b���i�ϥΪ� get
            $priv_post = explode(",", $row['method_post']);     //���b���i�ϥΪ� post

            //�ˬd�I�s�ݬO�_�����\�� ip
            if (matchIP($SERVICE['request_ip'], $allow_ip)) {

                //�̩I�s��k�B�z
                switch ($SERVICE['request_method']) {
                    case 'POST':
                        //���o Client �� POST �L�Ӫ����
                        $params = array();
                        foreach ($_POST as $name => $v) {
                            $params[$name] = base64_decode($v);
                        }
                        if (in_array($params['search'], $priv_post)) {
                            switch (trim($params['search'])) {
                                case 'year_seme':  //���o�Ǧ~�Ǵ�
                                    require('search_year_seme.php');
                                    break;
                                case 'curr_year_seme':  //���o�ثe�Ǧ~�ξǴ�
                                    require('search_curr_year_seme.php');
                                    break;
                                case 'classroom':  //���o�Ǵ��Z�ŦC��
                                    require('search_classroom.php');
                                    break;
                                case 'class_table':  //���o�Y�Z�ŽҪ�
                                    require('search_class_table.php');
                                    break;
                                case 'class_tuneup':  //�d�ߥi�սҸ`��
                                    require('search_class_tuneup.php');
                                    break;
                                case 'teacher_table':  //���o�Y�Z�ŽҪ�
                                    require('search_teacher_table.php');
                                    break;
                                case 'class_students_list':  //���o�Y�Z�W��C��
                                    require('search_class_students_list.php');
                                    break;
                                case 'teachers_list':  //���o�b¾�Юv�W��C��
                                    require('search_teachers_list.php');
                                    break;
                                case 'teacher_title':  //���o¾�ٰ}�C
                                    require('search_teacher_title.php');
                                    break;
                                case 'room_office':  //���o�B�ǰ}�C
                                    require('search_room_office.php');
                                    break;
                                case 'stud_status':  //���o�b�y�ǥͼƲέp
                                    require('search_stud_status.php');
                                    break;
                                case 'person_id':  //�̨����Ҩ��o�Y�Юv���
                                    require('search_person_id.php');
                                    break;
                                case 'bridge_check':  //�̨����Ҭd����X�͸�T
                                    require('search_bridge_check.php');
                                    break;
                                case 'bridge_download':  //���o�ǥ;��y���
                                    require('search_bridge_download.php');
                                    break;
                                case 'teacher_auth':  //���o�Юv�K�X�����
                                    require('search_teacher_auth.php');
                                    break;
                                default:
                                    $SERVICE['result'] = -1;
                                    $SERVICE['message'] = "POST�Ѽƿ��~!";
                            } // end switch
                        } else {
                            $SERVICE['result'] = -1;
                            $SERVICE['message'] = "POST�Ѽƿ��~! ";
                        }
                        break;

                    case 'GET':
                        //���o Client �� GET �L�Ӫ���� �ѼƤ��� decode_base64
                        $params = array();
                        foreach ($_GET as $name => $v) {
                            $params[$name] = $v;
                        }
                        if (in_array($params['search'], $priv_get)) {
                            switch ($params['search']) {
                                case 'year_seme':  //���o�Ǧ~�Ǵ�
                                    require('search_year_seme.php');
                                    break;
                                case 'curr_year_seme':  //���o�ثe�Ǧ~�ξǴ�
                                    require('search_curr_year_seme.php');
                                    break;
                                case 'classroom':  //���o���Ǵ��Z�ŦC��
                                    require('search_classroom.php');
                                    break;
                                case 'teacher_title':  //���o¾�ٰ}�C
                                    require('search_teacher_title.php');
                                    break;
                                case 'room_office':  //���o�B�ǰ}�C
                                    require('search_room_office.php');
                                    break;
                                case 'check_link':  //�ˬd�s�u
                                    require('search_check_link.php');
                                    break;
                                default:
                                    $SERVICE['result'] = -1;
                                    $SERVICE['message'] = "GET �Ѽƿ��~(2)!";
                            } // end switch
                        } else {
                            $SERVICE['result'] = -1;
                            $SERVICE['message'] = "GET �Ѽƿ��~(1)!";
                        }
                        break;

                    default:
                        $SERVICE['result'] = -1;
                        $SERVICE['message'] = "���~���I�s��k!";
                } // end switch


            } else {
                $SERVICE['result'] = -1;
                $SERVICE['message'] = "Forbidden Http Service from " . $SERVICE['request_ip'] . "! ";
            } //end if else matchIP($SERVICE['request_ip'],$allow_ip)

        } else {
            $SERVICE['result'] = -1;
            $SERVICE['message'] = "Forbidden Http Service!";
        }
 } // end if $SERVICE['result']==1


//�p�G���G�O���\�A��n�^�Ǫ���Ʀs�J
if ($SERVICE['result']) {
		$SERVICE['data']=$data;
}

//�O�_��utf8
if ($params['character']=='UTF-8') { 
	  $SERVICE=array_big5_to_utf8($SERVICE);	  
}
		
//��n�^�Ǫ���ƥH base64 �s�X
$SERVICE=array_base64_encode($SERVICE);

//���ưe�X
//echo json_encode($SERVICE,JSON_PRETTY_PRINT);
echo json_encode($SERVICE);

exit();


//���ߺݾ���, �����Ǯ� database
function change_CONN() {
    global $S_mysql_host, $S_mysql_user, $S_mysql_pass, $S_mysql_db;
    global $edu_id;
    global $CONN,$CONN_M;
    //�D�n��Ʈw
    //$CONN_M = &ADONewConnection($DB_TYPE);  # create a connection
    $CONN_M->Connect($S_mysql_host, $S_mysql_user, $S_mysql_pass, $S_mysql_db) or die("ERROR");# connect to postgresSQL, agora db
    //���o�Ǯճs�u��Ʈw�]�w
    $query = "select a.*,b.server_ip,b.db_ip,b.db_user,b.db_pass from school a ,server b where a.server_id=b.server_id and a.is_open=1 and sch_id='$edu_id'";
    $res = $CONN_M->Execute($query) or die($query);
    if ($res->RecordCount() == 1) {
        $mysql_db = "s" . $edu_id;
        $mysql_host = $res->fields['db_ip'];
        $mysql_user = $res->fields['db_user'];
        $mysql_pass = $res->fields['db_pass'];
        //�s�u�Ǯո�Ʈw
        $CONN->Connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);# connect to postgresSQL, agora db
        return 1;
    } else {
        return 0;
    }
}