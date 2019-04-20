<?php
//
// 取得系統中某班課表
//

// 引入 函式庫

include_once "../../include/sfs_case_score.php";
//require_once "../../include/sfs_core_globals.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";

//傳入的函式 $params['class'] 101 , 102 ..... 701, 702 ... 等 , 再轉為 class_id , 或直接傳入 class_id
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


    $sql_select = "select course_id,teacher_sn,cooperate_sn,day,sector,ss_id,room from score_course where class_id='" . $class_id . "' order by day,sector";

    $recordSet = $CONN->Execute($sql_select) or user_error("錯誤訊息：", $sql_select, 256);
    while (list($course_id, $teacher_sn, $cooperate_sn, $day, $sector, $ss_id, $room) = $recordSet->FetchRow()) {
        $k = $day . "_" . $sector;
        $a[$k] = $ss_id;          //get_ss_name("","","短",$a[$k])
        $b[$k] = $teacher_sn;     //get_teacher_name($b[$k])
        $co[$k] = $cooperate_sn;  //get_teacher_name($b[$k])
        $r[$k] = $room;


        //2017.10.24 改為2維方式來呈現星期和節的課表內容
        $data[$day][$sector]['subject'] = get_ss_name("", "", "短", $a[$k]);    //科目
        $data[$day][$sector]['teacher'] = get_teacher_name($b[$k]);          //教師
        $data[$day][$sector]['teacher_sn'] = $b[$k];                         //教師sn
        $data[$day][$sector]['co_teacher'] = get_teacher_name($co[$k]);      //協同教師
        $data[$day][$sector]['co_teacher_sn'] = $co[$k];                     //協同教師 sn
        $data[$day][$sector]['room'] = $room;                                //上課地點

    }


   //回傳，上課期間才有課表
    $data['st_start'] = $st_start;
    $data['st_end'] = $st_end;
    $data['class_id'] = $class_id;
}
