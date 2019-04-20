<?php

// $Id: eduh_count.php 7858 2014-01-14 02:25:24Z hsiao $

include "config.php";

sfs_check();

head("輔導--資料查補");
$year = curr_year(); //預設為本年度
$semester = curr_seme(); //預設為本學期
//取得任教班級代號
$class_num = get_teach_class();
if ($class_num == '') {
    head("權限錯誤");
    stud_class_err();
    foot();
    exit;
}
head("查_輔導及訪談紀錄");

//列出統計

$main = list_eduh($year, $semester, $class_num);
echo $main;

foot();

//列出所有班級的資料
function list_eduh($year, $semester, $class_num) {
    global $menu_p, $CONN;
    $toolbar = &make_menu($menu_p);

    //$show.="<table border='1'><tr bgcolor='#00ffff'><td>班級</td><td>人數</td><td>有紀錄</td><td>無紀錄</td><td>待紀錄之學生</td><td>導師</td></tr>";
    $show.="<table border='1' width='100%'><tr bgcolor='#00ffff'><td width='40'>座號</td><td width='60'>學生</td><td width='60'>狀態</td><td>輔導紀錄</td><td width='150'>訪談紀錄</td></tr>";

    //依班級尋找符合之學生
    $sel_year_seme = sprintf("%03d%d", $year, $semester); //格式化學期成4位數，如0911
    $sql_select = "select a.stud_study_cond ,b.stud_id ,b.seme_num ,a.stud_name from stud_base a, stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$sel_year_seme' and b.seme_class='$class_num' order by b.seme_num";
    $record_stud = $CONN->Execute($sql_select) or die($sql_select);

    /*
      $num_yes=0;//班級已輸入之人數起始
      $num_no=0;//班級未輸入之人數起始
      $name_no="-";//待輸入之學生姓名
      $name_teacher=$array_class[teacher_1];//班級導師
     */
    while ($array_stud = $record_stud->FetchRow()) {
        //尋找stud_seme_eduh中，學年度及學號相符的資料
        $show.="<tr><td>" . $array_stud[seme_num] . "</td>";
        $show.="<td>" . $array_stud[stud_name] . "</td>";
        if ($array_stud["stud_study_cond"] > 0) {
            $move_kind_arr = study_cond();
            $show.= "<td><font color=red>" . $move_kind_arr[$array_stud["stud_study_cond"]] . "</font></td>";
        }//增加 } 記號 by misser 93.10.20
        else
            $show.= "<td><font color='blue'>一般</font></td>";

        $sql_select = "select * from stud_seme_eduh where seme_year_seme='$sel_year_seme' and stud_id='$array_stud[stud_id]'";
        $record_num = $CONN->Execute($sql_select) or die($sql_select);
        if ($record_num->RecordCount() > 0) {
            $nothing = "";
            if ($record_num->fields["sse_relation"] == 0)
                $nothing.="*父母關係";
            if ($record_num->fields["sse_family_kind"] == 0)
                $nothing.="*家庭類型";
            if ($record_num->fields["sse_family_air"] == 0)
                $nothing.="*家庭氣氛";
            if ($record_num->fields["sse_farther"] == 0)
                $nothing.="*父管教方式";
            if ($record_num->fields["sse_mother"] == 0)
                $nothing.="*母管教方式";
            if ($record_num->fields["sse_live_state"] == 0)
                $nothing.="*居住情形";
            if ($record_num->fields["sse_rich_state"] == 0)
                $nothing.="*經濟狀況";
            if ($record_num->fields["sse_s1"] == "")
                $nothing.="*最喜愛科目";
            if ($record_num->fields["sse_s2"] == "")
                $nothing.="*最困難科目";
            if ($record_num->fields["sse_s3"] == "")
                $nothing.="*特殊才能";
            if ($record_num->fields["sse_s4"] == "")
                $nothing.="*興趣";
            if ($record_num->fields["sse_s5"] == "")
                $nothing.="*生活習慣";
            if ($record_num->fields["sse_s6"] == "")
                $nothing.="*人際關係";
            if ($record_num->fields["sse_s7"] == "")
                $nothing.="*外向行為";
            if ($record_num->fields["sse_s8"] == "")
                $nothing.="*內向行為";
            if ($record_num->fields["sse_s9"] == "")
                $nothing.="*學習行為";
            if ($record_num->fields["sse_s10"] == "")
                $nothing.="*不良習慣";
            if ($record_num->fields["sse_s11"] == "")
                $nothing.="*焦慮行為";
            if ($nothing == "")
                $show.="<td>有資料</td>"; //找到有資料
            else
                $show.="<td bgcolor='yellow'><font color='red'>未填入：" . $nothing . "*</font></td>";
        }
        //若找不到，紀錄該學生座號及姓名
        else {
            if ($move_kind_arr[$array_stud["stud_study_cond"]] == '調校')
                $show.="<td bgcolor='yellow'><font color='red'>已調校，若要補登記錄，可請註冊組先將學籍暫切。</font></td>";
            else
                $show.="<td bgcolor='yellow'><font color='red'>尚未建立，請補上。</font></td>";
        }
        //尋找stud_seme_talk中，學年度及學號相符的資料
        $sql_select = "select stud_id from stud_seme_talk where seme_year_seme='$sel_year_seme' and stud_id='$array_stud[stud_id]'";
        $record_num = $CONN->Execute($sql_select) or die($sql_select);

        if ($record_num->RecordCount() > 0) {
            $show.="<td>" . $record_num->RecordCount() . "筆</td>"; //找到有資料
        }
//若找不到
        else {
            if ($move_kind_arr[$array_stud["stud_study_cond"]] == '調校')
                $show.="<td bgcolor='yellow'><font color='red'>已調校，請<a href=\"" . $SFS_PATH_HTML . "stud_seme_talk2.php\">按此</a>補登記錄。</font></td>";
            else
                $show.="<td bgcolor='yellow'><font color='red'>尚未建立，請補上。</font></td>";
        }

        $show.="</tr>";
    }
    /*
      //去除待輸入學生之多餘字元(開頭-及結尾,)
      $name_no=(strlen($name_no)>1)?substr($name_no,1,strlen($name_no)-2):$name_no;
      //計算未輸入之人數
      $num_no=$num_all-$num_yes;
      //輸出單列資訊
      $show.=($num_no>0)?"<tr bgcolor='#ffccff'><td>":"<tr><td>";
      $show.=($temp[2]>6)?$temp[2]-6:$temp[2];//國中小判斷
      $show.=$temp[3]."</td>";
      $show.="<td>$num_all 人</td><td>$num_yes 人</td><td>$num_no 人</td><td width='350'>$name_no</td><td>$name_teacher</td></tr>";
     */
    $show.="</table>";
    return $toolbar . $select_yearseme_form . $show;
}

?>
