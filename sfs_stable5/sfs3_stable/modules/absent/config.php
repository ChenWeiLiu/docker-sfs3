<?php

// $Id: config.php 9209 2018-03-02 17:43:15Z smallduh $

include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_calendar.php";
include_once "../../include/sfs_case_dataarray.php";
include "../../include/sfs_oo_zip2.php";
include_once "module-cfg.php";

include_once "module-upgrade.php";
//---------------------------------------------------
// �o�̽ФޤJ�z�ۤv���禡�w
//---------------------------------------------------
include_once "function.php";

$abskind=array("�ư�"=>"1","�f��"=>"2","�m��"=>"3","����"=>"5");

//���s�w�q�P��
$weekN=array('�@','�G','�T','�|','��','��');

//���o�Ҳճ]�w
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
$ranks=$ranks?$ranks:50;

$n_arr = get_sfs_module_set("score_nor");
$section_include=$n_arr["section_include"];
?>
