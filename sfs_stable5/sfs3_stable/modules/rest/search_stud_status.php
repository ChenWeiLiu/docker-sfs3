<?php
//
// 取得班級人數統計 , 最後把資料存在 data 陣列中
//

$year=(empty($params['year']))?curr_year():$params['year'];
$semester=(empty($semester))?curr_seme():$params['semester'];

    $year=intval($year);
    $semester=intval($semester);
    $sql_select = "select class_sn,class_id,c_year,c_name,c_kind,c_sort from school_class where enable='1' and year='$year' and semester='$semester' order by c_year,c_sort";
    $recordSet=$CONN->Execute($sql_select) or die($sql_select);

    $i=0;
    while ($array = $recordSet->FetchRow()) {
        $c=$array[c_year].sprintf("%02d",$array[c_sort]);
        $sql_select2="select sum(stud_sex=1)as boy ,sum(stud_sex=2) as girl from stud_base where stud_study_cond in (0,15) and substring(curr_class_num,1,3)='$c'";
        $recordSet2=$CONN->Execute($sql_select2) or die($sql_select2);
        $sarray = $recordSet2->FetchRow();
        $Cyear=$array[c_year];
        $cclass[$array[c_sort]]=$array[c_name];

        $stud_all=(($sarray[boy]+$sarray[girl])>0)?$sarray[boy]+$sarray[girl]:"";
        $b=(!empty($sarray[boy]))?$sarray[boy]:"";
        $g=(!empty($sarray[girl]))?$sarray[girl]:"";

        if ($stud_all >0) {
        $i++;
            $data[$i][$array['class_id']]=$school_kind_name[$Cyear].$array[c_name]."班";
            $data[$i]['boy']=$b;
            $data[$i]['girl']=$g;
            $data[$i]['stud_all']=$stud_all;
        }
    }
