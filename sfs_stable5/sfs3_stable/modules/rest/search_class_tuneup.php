<?php
//
// 查詢某日可調課節次
//

// 引入 函式庫

include_once "../../include/sfs_case_score.php";
//require_once "../../include/sfs_core_globals.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";

//傳入的函式
/*   $params['class_id'] 班級   或 $params['class'] (會取當學期)
 *   $params['week']     星期幾
 *   $params['sector']   第幾節
 *   $params['new_day']  調到幾月幾日
 *
 *   系統回傳, 該日哪幾節可以調  array
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
    $st_start = $res->fields['day'];    //學期開始
    $sql = "select day from school_day where year='$sel_year' and seme='$sel_seme' and day_kind='st_end'";
    $res = $CONN->Execute($sql);
    $st_end = $res->fields['day'];      //學期結束

    //回傳，上課期間才有課表
    $data['st_start'] = $st_start;
    $data['st_end'] = $st_end;


    //先取得該班課表
    $sql_select = "select course_id,teacher_sn,cooperate_sn,day,sector,ss_id,room from score_course where class_id='" . $class_id . "' order by day,sector";
    $recordSet = $CONN->Execute($sql_select) or user_error("錯誤訊息：", $sql_select, 256);
    while (list($course_id, $teacher_sn, $cooperate_sn, $day, $sector, $ss_id, $room) = $recordSet->FetchRow()) {
        $k = $day . "_" . $sector;
        $a[$k] = $ss_id;          //get_ss_name("","","短",$a[$k])
        $b[$k] = $teacher_sn;     //get_teacher_name($b[$k])
        $co[$k] = $cooperate_sn;  //get_teacher_name($b[$k])
        $r[$k] = $room;

        //2017.10.24 改為2維方式來呈現星期和節的課表內容
        $class_table[$day][$sector]['subject'] = get_ss_name("", "", "短", $a[$k]);    //科目
        $class_table[$day][$sector]['teacher'] = get_teacher_name($b[$k]);          //教師
        $class_table[$day][$sector]['teacher_sn'] = $b[$k];                         //教師sn
        $class_table[$day][$sector]['co_teacher'] = get_teacher_name($co[$k]);      //協同教師
        $class_table[$day][$sector]['co_teacher_sn'] = $co[$k];                     //協同教師 sn
        $class_table[$day][$sector]['room'] = $room;                                //上課地點

    }

    //要調的那一節 的老師
    $the_teacher_sn=$class_table[$org_week][$org_sector]['teacher_sn'];
    //要調課的老師的課表
    $the_teacher_table=teacher_table($the_teacher_sn);


    //目標日為星期幾
    $new_week=date("w",strtotime($new_day." 00:00:00"));

    //檢查該日每一節, 該節 要調課的老師不能有課, 該節被調的老師, 在  $params['week'] , $params['sector'] 不能有課
    $data=array();

    foreach ($class_table[$new_week] as $new_sector=>$v) {
        //檢查, 如果這一節要調課的老師沒課才處理 (如果自己有課, 就不能調)
        if ($the_teacher_table[$new_week][$new_sector]['subject']=='') {
            //被調課老師的 teacher_sn
            $tuneup_teacher_sn=$class_table[$new_week][$new_sector]['teacher_sn'];
            //取得被調課老師的課表, 該師在 $org_week , $org_sector 不能有課
            $tuneup_teacher_table=teacher_table($tuneup_teacher_sn);
            //沒課, 列入調課目標
            if ($tuneup_teacher_table[$org_week][$org_sector]['subject']=='') {
                $data[$new_week][$new_sector]['subject']=$tuneup_teacher_table[$new_week][$new_sector]['subject'];
                $data[$new_week][$new_sector]['teacher']=get_teacher_name($tuneup_teacher_sn);
                $data[$new_week][$new_sector]['teacher_sn']=$tuneup_teacher_sn;
            }

        }

    } // end foreach

}



//老師的課表
function teacher_table($teacher_sn) {

    global $CONN,$sel_year,$sel_seme;

    $data=array();

    $sql_select = "select course_id,class_id,day,sector,ss_id,room,c_kind from score_course where year='$sel_year' and semester='$sel_seme' and (teacher_sn='" . $teacher_sn . "' or cooperate_sn='" . $teacher_sn . "') order by day,sector";
    $recordSet = $CONN->Execute($sql_select) or user_error("錯誤訊息：", $sql_select, 256);
    while (list($course_id, $class_id, $day, $sector, $ss_id, $room, $c_kind) = $recordSet->FetchRow()) {

        $k = $day . "_" . $sector;
        $a[$k] = $ss_id;
        $b[$k] = $class_id;
        $room[$k] = $room;
        $course_id_arr[$k] = $course_id;
        //記錄是否為兼課  0:一般  1:兼課
        $c_kind_arr[$k] = $c_kind;

        //取得班級資料
        $the_class = get_class_all($b[$k]);
        $class_name = ($the_class[name] == "班") ? "" : $the_class[name];

        $data[$day][$sector]['subject'] = get_ss_name("", "", "短", $a[$k]);    //科目
        $data[$day][$sector]['class_name'] = $class_name;                       //班級
        $data[$day][$sector]['class_id'] = $class_id;                           //$class_id
        $data[$day][$sector]['room'] = $room[$k];                               //上課地點

    } // end while

    return $data;
}