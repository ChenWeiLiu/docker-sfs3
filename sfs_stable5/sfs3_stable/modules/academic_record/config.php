<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
include_once "../../include/config.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";
include "../../include/sfs_oo_zip2.php";
include "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
require_once "./module-cfg.php";

//---------------------------------------------------
// �o�̽ФޤJ�z�ۤv���禡�w
//---------------------------------------------------
include_once "function.php";
include_once "my_function.php";

//�ˬd��s���O
include_once "module-upgrade.php";

$reward_arr=array("1"=>"�ż��@��","2"=>"�ż��G��","3"=>"�p�\�@��","4"=>"�p�\�G��","5"=>"�j�\�@��","6"=>"�j�\�G��","7"=>"�j�\�T��","-1"=>"ĵ�i�@��","-2"=>"ĵ�i�G��","-3"=>"�p�L�@��","-4"=>"�p�L�G��","-5"=>"�j�L�@��","-6"=>"�j�L�G��","-7"=>"�j�L�T��");
?>