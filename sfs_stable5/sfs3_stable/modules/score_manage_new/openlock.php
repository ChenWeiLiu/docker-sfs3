<?php
// $Id: openlock.php 8977 2016-09-19 07:49:18Z infodaes $

/*引入學務系統設定檔*/
include "../../include/config.php";
//使用者認證
sfs_check();

$score_semester=$_GET['score_semester'];
$class_id=$_GET['class_id'];
$ss_id=$_GET['ss_id'];
$year_name=$_GET['year_name'];
$stage=$_GET['stage'];
$index=$_GET['index'];
$kind=$_GET['kind'];
$ys=explode("_",$score_semester);
$year_seme=$ys[2]."_".$ys[3];
$cid=explode("_",$class_id);
$seme_year_seme=sprintf("%03d",$cid[0]).$cid[1];
$test_sort=($stage==254)?"":"and test_sort='$stage'";
if ($cid[4]=="g") {
	//取得分組班學生流水號
	$query="select student_sn from elective_stu where group_id='".$cid[3]."'";
} else {
	$seme_class=intval($cid[2]).sprintf("%02d",$cid[3]);
	//取得一般班學生流水號
	$query="select student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class'";
}
$res=$CONN->Execute($query);
$all_sn="";
while(!$res->EOF){
	$all_sn.="'".$res->fields[student_sn]."',";
	$res->MoveNext();
}
if ($all_sn) $all_sn=substr($all_sn,0,-1);

//鎖上
if($kind=='定期評量' or $kind=='平時成績') $kind="in ('定期評量','平時成績')"; else $kind="='$kind'";
$sql_upd="UPDATE $score_semester SET sendmit='1' where student_sn in ($all_sn) $test_sort and ss_id='$ss_id' and test_kind $kind";
//echo $sql_upd;
$CONN->Execute($sql_upd);

header("Location:$index.php?year_seme=$year_seme&year_name=$year_name&stage=$stage&class_id=$class_id");
?>
