<?php

// $Id: stud_sta_rep.php 8695 2015-12-25 04:05:53Z qfon $

//載入設定檔


include "config.php";
include "sfs_oo_date2.php";
include "../../include/sfs_case_PLib.php";
$m_arr = get_sfs_module_set("stud_sta");

//新增一個 zipfile 實例
$ttt = new easyZip;
$ttt->setPath('stud_sta_rep');
$ttt->addDir('META-INF');
$ttt->addfile("settings.xml");
$ttt->addfile("styles.xml");
$ttt->addfile("meta.xml");

$data = $ttt->read_file(dirname(__FILE__)."/stud_sta_rep/content.xml");

$stud_id = $_GET['stud_id'];
$prove_id = $_GET['prove_id'];

$_GET[prove_id]=intval($_GET[prove_id]);
$query = "select a.*, b.stud_name, b.curr_class_num, b.student_sn, b.stud_birthday,year(b.stud_birthday) as years,month(b.stud_birthday) as months,day(b.stud_birthday) as days from stud_sta a, stud_base b where a.student_sn=b.student_sn and a.prove_id='$_GET[prove_id]'";
$result = $CONN->Execute($query) or die ($query);

$curr_seme = curr_year().curr_seme();

$class_list_p = class_base();
/*
$stud_birthday = DtoCh($result->fields["stud_birthday"]);
$birth_y = substr($stud_birthday,0,2);
$birth_m = substr($stud_birthday,3,2);
$birth_d = substr($stud_birthday,6,2);
*/
$stud_birthday = $result->fields["stud_birthday"];
$birth_y = $result->fields["years"]-1911;
$birth_m = $result->fields["months"];
$birth_d = $result->fields["days"];


$stu_g = substr($result->fields["curr_class_num"],0,1);
//$stu_g =substr($class_year[$stu_g],0,2);
$today=(date("Y")-1911).".".date("m").".".date("d");
//(integer)轉整數，避免05變零五
//改橫式不轉大寫
/*
$today_y = (integer) substr($today,0,2);
$today_m = (integer) substr($today,3,2);
$today_d = (integer) substr($today,6,2);
*/
$today_y = (integer) date("Y")-1911;
$today_m = (integer) date("m");
$today_d = (integer) date("d");

$temp_arr[schoolname] = $SCHOOL_BASE[sch_cname];
$temp_arr[stud_name] = $result->fields["stud_name"];
$temp_arr[curr_sum] = curr_year();

$temp_arr[sschoolname] = $m_arr['sta_word'];
$temp_arr[signature] = $m_arr['signature'];
$temp_arr[signature2] = $m_arr['signature2'];

$temp_arr[prove_id] = $prove_id;
$temp_arr[birth_y] = $birth_y;
$temp_arr[birth_m] = $birth_m;
$temp_arr[birth_d] = $birth_d;
$temp_arr[stu_g] = $stu_g;
$temp_arr[class_name] = $class_list_p[substr($result->fields["curr_class_num"],0,3)];
$temp_arr[curr_y] = curr_seme();
$temp_arr[today_y] = $today_y;
$temp_arr[today_m] = $today_m;
$temp_arr[today_d] = $today_d;



//echo      $temp_arr[schoolname]."<br>" ;
//echo  $temp_arr[curr_sum]."<br>" ;
//echo      $temp_arr[sschoolname]."<br>" ;
//echo      $temp_arr[prove_id]."<br>" ;
//echo      $temp_arr[stu_name]."<br>" ;
//echo      $temp_arr[birth_y]."<br>" ;
//echo      $temp_arr[birth_m] ."<br>" ;
//echo      $temp_arr[birth_d] ."<br>" ;
//echo      $temp_arr[stu_g] ."<br>" ;
//echo      $temp_arr[curr_y] ."<br>" ;
//echo      $temp_arr[today_y] ."<br>" ;
//echo      $temp_arr[today_m] ."<br>" ;
//echo      $temp_arr[today_d]."<br>" ;

//exit;



// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
$data = $ttt->change_temp($temp_arr,$data);
$ttt->add_file($data,"content.xml");

//產生 zip 檔
$sss = & $ttt->file();

//以串流方式送出 sxw
header("Content-disposition: attachment; filename=ooo.sxw");
header("Content-type: application/vnd.sun.xml.writer");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
header("Expires: 0");

echo $sss;
?>
