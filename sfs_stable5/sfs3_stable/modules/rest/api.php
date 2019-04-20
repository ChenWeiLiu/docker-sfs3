<?php
include_once "config.php";

/*
 * RESTful Server 端程式
 *
 */

$SERVICE['result']=1;									//1表示成功, 0 表示失敗
$SERVICE['message']="";                                 //回應訊息
$SERVICE['request_method']=$_SERVER['REQUEST_METHOD'];	//取得  呼叫端的 method
$SERVICE['request_ip']=getClientIP();					//取得  呼叫端的IP


//中心端機器
if ($SFS_IS_CENTER_VER==1) {
//依呼叫方法處理
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
            $SERVICE['message']="中心端系統，缺乏學校代碼!";
    }
}  // end if ($SFS_IS_CENTER_VER==1)

 if ($SERVICE['result']==1) {

//取得呼叫端的認證資訊
        $Header = array();
        foreach (getallheaders() as $name => $v) {
            $Header[$name] = $v;
        }

        $sql = "select * from rest_manage where s_id='" . $Header['S_ID'] . "' and s_pwd='" . $Header['S_PWD'] . "'";
        $res = $CONN->Execute($sql);

        if ($res->RecordCount()) {
            $row = $res->fetchRow();

            $allow_ip = explode(",", $row['allow_ip']);            //本帳號可連入的 ip
            $priv_get = explode(",", $row['method_get']);       //本帳號可使用的 get
            $priv_post = explode(",", $row['method_post']);     //本帳號可使用的 post

            //檢查呼叫端是否為允許的 ip
            if (matchIP($SERVICE['request_ip'], $allow_ip)) {

                //依呼叫方法處理
                switch ($SERVICE['request_method']) {
                    case 'POST':
                        //取得 Client 端 POST 過來的資料
                        $params = array();
                        foreach ($_POST as $name => $v) {
                            $params[$name] = base64_decode($v);
                        }
                        if (in_array($params['search'], $priv_post)) {
                            switch (trim($params['search'])) {
                                case 'year_seme':  //取得學年學期
                                    require('search_year_seme.php');
                                    break;
                                case 'curr_year_seme':  //取得目前學年及學期
                                    require('search_curr_year_seme.php');
                                    break;
                                case 'classroom':  //取得學期班級列表
                                    require('search_classroom.php');
                                    break;
                                case 'class_table':  //取得某班級課表
                                    require('search_class_table.php');
                                    break;
                                case 'class_tuneup':  //查詢可調課節次
                                    require('search_class_tuneup.php');
                                    break;
                                case 'teacher_table':  //取得某班級課表
                                    require('search_teacher_table.php');
                                    break;
                                case 'class_students_list':  //取得某班名單列表
                                    require('search_class_students_list.php');
                                    break;
                                case 'teachers_list':  //取得在職教師名單列表
                                    require('search_teachers_list.php');
                                    break;
                                case 'teacher_title':  //取得職稱陣列
                                    require('search_teacher_title.php');
                                    break;
                                case 'room_office':  //取得處室陣列
                                    require('search_room_office.php');
                                    break;
                                case 'stud_status':  //取得在籍學生數統計
                                    require('search_stud_status.php');
                                    break;
                                case 'person_id':  //依身分證取得某教師資料
                                    require('search_person_id.php');
                                    break;
                                case 'bridge_check':  //依身分證查詢轉出生資訊
                                    require('search_bridge_check.php');
                                    break;
                                case 'bridge_download':  //取得學生學籍資料
                                    require('search_bridge_download.php');
                                    break;
                                case 'teacher_auth':  //取得教師密碼雜湊值
                                    require('search_teacher_auth.php');
                                    break;
                                default:
                                    $SERVICE['result'] = -1;
                                    $SERVICE['message'] = "POST參數錯誤!";
                            } // end switch
                        } else {
                            $SERVICE['result'] = -1;
                            $SERVICE['message'] = "POST參數錯誤! ";
                        }
                        break;

                    case 'GET':
                        //取得 Client 端 GET 過來的資料 參數不用 decode_base64
                        $params = array();
                        foreach ($_GET as $name => $v) {
                            $params[$name] = $v;
                        }
                        if (in_array($params['search'], $priv_get)) {
                            switch ($params['search']) {
                                case 'year_seme':  //取得學年學期
                                    require('search_year_seme.php');
                                    break;
                                case 'curr_year_seme':  //取得目前學年及學期
                                    require('search_curr_year_seme.php');
                                    break;
                                case 'classroom':  //取得本學期班級列表
                                    require('search_classroom.php');
                                    break;
                                case 'teacher_title':  //取得職稱陣列
                                    require('search_teacher_title.php');
                                    break;
                                case 'room_office':  //取得處室陣列
                                    require('search_room_office.php');
                                    break;
                                case 'check_link':  //檢查連線
                                    require('search_check_link.php');
                                    break;
                                default:
                                    $SERVICE['result'] = -1;
                                    $SERVICE['message'] = "GET 參數錯誤(2)!";
                            } // end switch
                        } else {
                            $SERVICE['result'] = -1;
                            $SERVICE['message'] = "GET 參數錯誤(1)!";
                        }
                        break;

                    default:
                        $SERVICE['result'] = -1;
                        $SERVICE['message'] = "錯誤的呼叫方法!";
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


//如果結果是成功，把要回傳的資料存入
if ($SERVICE['result']) {
		$SERVICE['data']=$data;
}

//是否轉utf8
if ($params['character']=='UTF-8') { 
	  $SERVICE=array_big5_to_utf8($SERVICE);	  
}
		
//把要回傳的資料以 base64 編碼
$SERVICE=array_base64_encode($SERVICE);

//把資料送出
//echo json_encode($SERVICE,JSON_PRETTY_PRINT);
echo json_encode($SERVICE);

exit();


//中心端機器, 切換學校 database
function change_CONN() {
    global $S_mysql_host, $S_mysql_user, $S_mysql_pass, $S_mysql_db;
    global $edu_id;
    global $CONN,$CONN_M;
    //主要資料庫
    //$CONN_M = &ADONewConnection($DB_TYPE);  # create a connection
    $CONN_M->Connect($S_mysql_host, $S_mysql_user, $S_mysql_pass, $S_mysql_db) or die("ERROR");# connect to postgresSQL, agora db
    //取得學校連線資料庫設定
    $query = "select a.*,b.server_ip,b.db_ip,b.db_user,b.db_pass from school a ,server b where a.server_id=b.server_id and a.is_open=1 and sch_id='$edu_id'";
    $res = $CONN_M->Execute($query) or die($query);
    if ($res->RecordCount() == 1) {
        $mysql_db = "s" . $edu_id;
        $mysql_host = $res->fields['db_ip'];
        $mysql_user = $res->fields['db_user'];
        $mysql_pass = $res->fields['db_pass'];
        //連線學校資料庫
        $CONN->Connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);# connect to postgresSQL, agora db
        return 1;
    } else {
        return 0;
    }
}