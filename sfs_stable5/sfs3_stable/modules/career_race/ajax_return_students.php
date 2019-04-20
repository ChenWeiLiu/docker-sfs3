<?php
header('Content-type: text/html;charset=big5');
// $Id: index.php 7731 2013-10-29 05:45:26Z smallduh $

/* 取得設定檔 */
include_once "config.php";

sfs_check();


if ($_POST['the_class']) {
  
  $seme_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
	$query="select a.student_sn,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='".$_POST['the_class']."' and a.student_sn=b.student_sn order by seme_num";
	$res_stud_list=$CONN->Execute($query) or die("SQL錯誤:".$query);
	$select_students=$res_stud_list->GetRows();
  $data="<table border='0'>";
  $i=0;
  foreach($select_students as $v) {
    $i++;
    if ($i%10==1) $data.="<tr>";
    $chk=(strpos(" ".$_POST['pre_selected'],$v['student_sn'])>=1)?"checked":""; 
    $data.="<td><input type='checkbox' name='chk_student[]' class='chk_student' value='".$v['student_sn']."' $chk >".$v['seme_num'].$v['stud_name']."</td>";
    if ($i%10==0) $data.="</tr>";
  } // end foreach
  $data.="</table>";

  echo $data;
}
?>