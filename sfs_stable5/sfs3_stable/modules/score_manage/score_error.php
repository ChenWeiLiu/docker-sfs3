<?php

// $Id: score_error.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
include "../../include/config.php";
//引入函式庫
include "../../include/sfs_case_PLlib.php";
//引入函數
include "./my_fun.php";
//使用者認證
sfs_check();

//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$db=($_POST['db'])?"{$_POST['db']}":"{$_GET['db']}";
$id=($_POST['id'])?"{$_POST['id']}":"{$_GET['id']}";
$class_seme=($_POST['class_seme'])?"{$_POST['class_seme']}":"{$_GET['class_seme']}";

//刪除成績
if($act=="del"){
	if($db=="stud_seme_score") $del_sql="delete from $db where sss_id='$id' ";
	elseif(substr($db,0,14)=="score_semester") $del_sql="delete from $db where score_id='$id' ";
	else $del_sql="";
	$CONN->Execute($del_sql) or trigger_error($del_sql,256);
}

//程式檔頭
head("成績檢查");
//列出橫向的連結選單模組
$menu_p = array("index.php"=>"成績管理", "score_error.php"=>"成績檢查");
print_menu($menu_p);
echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor=#FFFFFF>";
//===============================================
//學期選單
$class_seme_array=get_class_seme();
$class_seme_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form1'>\n<select  name='class_seme' onchange='this.form.submit()'>\n";
$i=0;
foreach($class_seme_array as $k => $v){
	if(!$class_seme) $class_seme=sprintf("%03d%d",curr_year(),curr_seme());
	$selected[$i]=($class_seme==$k)?" selected":" ";
	$class_seme_select.="<option value='$k'$selected[$i] >$v</option> \n";
	$i++;
}
$class_seme_select.="</select></form>\n";

//找出所有本學期所有年級的課程設定

$year=substr($class_seme,0,-1);
$semester=substr($class_seme,-1);
$sqla="select * from score_ss where year='$year' and semester='$semester' and enable=1 and need_exam=1 order by class_year , sort , sub_sort";
//echo $sqla;

$rsa=$CONN->Execute($sqla) or trigger_error($sqla,256);
$i=0;
while(!$rsa->EOF){
	$ss_id_arr[$i]=$rsa->fields['ss_id'];
	//echo $ss_id_arr[$i]."<br>";
	$scope_id_arr[$i]=$rsa->fields['scope_id'];
	$subject_id_arr[$i]=$rsa->fields['subject_id'];
	$class_year_arr[$i]=$rsa->fields['class_year'];
	$print_arr[$i]=$rsa->fields['print'];
	$PP[$ss_id_arr[$i]]=$print_arr[$i];
   if($subject_id_arr[$i]!=0){
        $sqlb="select subject_name from score_subject where subject_id=$subject_id_arr[$i]";
        $rsb=$CONN->Execute($sqlb);
        $subject_name[$i] = $rsb->fields["subject_name"];
    }
    else{
        $sqlc="select subject_name from score_subject where subject_id=$scope_id_arr[$i]";
        $rsc=$CONN->Execute($sqlc);
        $subject_name[$i] = $rsc->fields["subject_name"];
    }
	$UU[$ss_id_arr[$i]]=$subject_name[$i];
	$i++;
	$rsa->MoveNext();
}

//找出stud_seme_score的殭屍成績，也就是該成績對應不到課程者
$sqld="select * from stud_seme_score where  seme_year_seme='$class_seme' ";
//echo $sqld;
$rsd=$CONN->Execute($sqld) or trigger_error($sqld,256);
$j=0;
while(!$rsd->EOF){
	$ss_id_stud_seme_score[$j]=$rsd->fields['ss_id'];
	$sss_id[$j]=$rsd->fields['sss_id'];
	//找出班級名稱
	$student_sn[$j]=$rsd->fields['student_sn'];
	//echo $student_sn[$j];
	//echo $ss_id_stud_seme_score[$j]."<br>";
	if(!in_array($ss_id_stud_seme_score[$j],$ss_id_arr)) {
		$student[$j]=classinfo($student_sn[$j],$year,$semester);
		$msg.="<ul>stud_seme_score 的流水號：{$sss_id[$j]}---{$student[$j]}的成績對應不到課程，建議<a href='{$_SERVER['PHP_SELF}']}?act=del&db=stud_seme_score&id={$sss_id[$j]}&class_seme=$class_seme'><font class='button'>刪除</font></a></ul>";
	}
	$j++;
	$rsd->MoveNext();
}


//找出score_semester的殭屍成績，也就是該成績對應不到課程者
$score_semester=sprintf("score_semester_%d_%d",intval($year),$semester);
$sqlf="select * from $score_semester ";
//echo $sqld;
$rsf=$CONN->Execute($sqlf) or trigger_error($sqlf,256);
$j=0;
while(!$rsf->EOF){
	$ss_id_score_semester[$j]=$rsf->fields['ss_id'];
	$test_sort_score_semester[$j]=$rsf->fields['test_sort'];
	$score_id[$j]=$rsf->fields['score_id'];
	//找出班級名稱
	$student_sn[$j]=$rsf->fields['student_sn'];
	//echo $ss_id_stud_seme_score[$j]."<br>";
	if(!in_array($ss_id_score_semester[$j],$ss_id_arr)) {
		$student[$j]=classinfo($student_sn[$j],$year,$semester);
		$msg.="<ul>$score_semester 的流水號：{$score_id[$j]}---{$student[$j]} 的成績對應不到課程，建議<a href='{$_SERVER['PHP_SELF}']}?act=del&db=$score_semester&id={$score_id[$j]}&class_seme=$class_seme'><font class='button'>刪除</font></a></ul>";
	}
	elseif($PP[$ss_id_score_semester[$j]]!=1 && $test_sort_score_semester[$j]!=255) {
		$student[$j]=classinfo($student_sn[$j],$year,$semester);
		$msg.="<ul>{$student[$j]}{$UU[$ss_id_score_semester[$j]]}課程設定為只需繳交學期成績，但教師成績中卻有階段{$test_sort_score_semester[$j]}的成績，建議<a href='{$_SERVER['PHP_SELF}']}?act=del&db=$score_semester&id={$score_id[$j]}&class_seme=$class_seme'><font class='button'>刪除</font></a></ul>";
	}
	elseif($PP[$ss_id_score_semester[$j]]==1 && $test_sort_score_semester[$j]==255){
		$student[$j]=classinfo($student_sn[$j],$year,$semester);
		$msg.="<ul>{$student[$j]}{$UU[$ss_id_score_semester[$j]]}課程設定為要傳送每次的階段成績，但教師成績中卻有全學期的成績，建議<a href='{$_SERVER['PHP_SELF}']}?act=del&db=$score_semester&id={$score_id[$j]}&class_seme=$class_seme'><font class='button'>刪除</font></a></ul>";
	}
	$j++;
	$rsf->MoveNext();
}

if($msg=="") $msg="本學期成績檢查無誤！";
echo $class_seme_select.$msg.$main;
//===============================================
echo "</td></tr></table>";
//程式檔尾
foot();
?>
