<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_PLlib.php";
//努力程度
$oth_arr_score = array("表現優異"=>5,"表現良好"=>4,"表現尚可"=>3,"需再加油"=>2,"有待改進"=>1);
$oth_arr_score_2 = array(5=>"表現優異",4=>"表現良好",3=>"表現尚可",2=>"需再加油",1=>"有待改進");
?>
