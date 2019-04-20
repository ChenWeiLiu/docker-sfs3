<?php

// $Id: stud_birth.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
include "config.php";

//使用者認證

sfs_check();
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);

$stud_study_year = curr_year() - substr($class_name[0],0,1) +1;

head("生日月份名單") ;
print_menu($menu_p);

    $sqlstr = " select   month(stud_birthday) as TM , count(*) as TC
               from stud_base
               where stud_study_cond  = 0
               and  curr_class_num  like '$class_name[0]%'
               group by  TM " ;
    //echo $sqlstr ;
    $recordSet=$CONN->Execute($sqlstr);

    while (!$recordSet->EOF) {
         $monthN = $recordSet->fields["TM"] ;                        //月份
         $studN         = $recordSet->fields["TC"] ;                //人數
         $birth_array[$monthN] = $studN ;                //放在 [月份]陣列中
         $recordSet->MoveNext();
     }

     /*
     取得姓名、生日月份、生日(日)，按月份來排列
     select month(stud_birthday) as TM , stud_name , , DAYOFMONTH(stud_birthday) as TD
     from stud_base
     where condition= 0
     and class_num_8 like '407%'
     order by TM

     */
    $sqlstr = " select   month(stud_birthday) as TM , stud_name  , DAYOFMONTH(stud_birthday) as TD
               from stud_base
               where stud_study_cond  = 0
               and  curr_class_num  like '$class_name[0]%'
               order by  TM ,TD " ;
    //echo $sqlstr ;
    $recordSet=$CONN->Execute($sqlstr);

    while (!$recordSet->EOF) {
            $tm= $recordSet->fields["TM"] ;
            $s_name = $recordSet->fields["stud_name"] ;
            $s_birthday = $recordSet->fields["TD"] ;
            $tmem[$tm] .=  $s_name . "(" . $s_birthday ."日)、 " ;
            $recordSet->MoveNext();
    }

    echo "<h2 align=\"center\">$class_name[1]-生日月份人數統計 </hr><br>"  ;
    echo ' <table width="96%" border="1" cellspacing="0" BGCOLOR="#FDDDAB" align="center" cellpadding=2  bordercolor=#008080  bordercolorlight=#666666 bordercolordark=#FFFFFF> ' ;
    echo '<tr align="center"><td>月份</td><td>人數</td><td>學生姓名</td> ' ;

    for ($m= 1 ; $m<=12 ; $m++) {                //各月份人數

        if ($birth_array[$m] >0) {
           $all_stud += $birth_array[$m] ;
           echo "<tr align=\"center\">" ;
           echo "<td> $m 月</td><td>". $birth_array[$m]." 人</td><td align=\"left\">" . $tmem[$m]. "</td>" ;
           echo "</tr>" ;
        }
        else {
           echo "<tr align=\"center\">" ;
           echo "<td> $m 月</td><td> 0 人</td><td align=\"left\">&nbsp;</td>" ;
           echo "</tr>" ;
        }
    }

    echo "</table><br>" ;
    echo "共 $all_stud 人" ;


foot() ;

?>