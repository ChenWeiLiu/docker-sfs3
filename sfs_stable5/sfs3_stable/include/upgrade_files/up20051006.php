<?php

//$Id: up20051006.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

//將出生地加入系統選項清單
$res=$CONN->Execute("select * from sfs_text where t_kind='birth_state'");
if ($res->RecordCount()==0) join_sfs_text(1,"birth_state",array("01"=>"台北市","02"=>"高雄市","03"=>"宜蘭縣","04"=>"基隆市","05"=>"台北縣","06"=>"桃園縣","07"=>"新竹縣","08"=>"新竹市","09"=>"苗栗縣","10"=>"台中縣","11"=>"台中市","12"=>"南投縣","13"=>"彰化縣","14"=>"雲林縣","15"=>"嘉義縣","16"=>"嘉義市","17"=>"台南縣","18"=>"台南市","19"=>"高雄縣","20"=>"屏東縣","21"=>"台東縣","22"=>"花蓮縣","23"=>"澎湖縣","24"=>"金門縣","25"=>"連江縣"));
$res=$CONN->Execute("select * from sfs_text where t_name='birth_state'");
if ($res->RecordCount()!=0) $CONN->Execute("update sfs_text set t_name='出生地' where t_name='birth_state'");
?>