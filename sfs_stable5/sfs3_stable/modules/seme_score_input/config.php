<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

include_once "../../include/config.php";
require_once "./module-cfg.php";
require_once "../../include/sfs_case_studclass.php";
require_once "../../include/sfs_case_score.php";

//取得資料表stud_seme的所有入學年度和學期
function  stud_seme_year_seme(){
    global $CONN;
    $sql="select seme_year_seme  from stud_seme group by seme_year_seme order by seme_year_seme";
    $rs=$CONN->Execute($sql);
	while(!$rs->EOF){
        $seme_year_seme[]= $rs->fields['seme_year_seme'];		
        $rs->MoveNext();
    }
return  $seme_year_seme;
}

//取得資料表stud_seme該學年度的所有班級
function  stud_seme_class($seme_year_seme=""){
    global $CONN;
        $year= intval(substr($seme_year_seme,0,-1));
        $semester = substr($seme_year_seme,-1);
        $sql="select class_id,c_name  from school_class where year='$year' and semester=$semester order by c_year, c_sort";
        $rs=$CONN->Execute($sql);
        while(!$rs->EOF){
                $class_id = $rs->fields[class_id];
                $class_arr = explode("_",$class_id);
                $AAA[seme_class][]= sprintf("%d%s",$class_arr[2],$class_arr[3]);
                $AAA[seme_class_name][]=$rs->fields[1];
        $rs->MoveNext();
    }
return  $AAA;
}
?>
