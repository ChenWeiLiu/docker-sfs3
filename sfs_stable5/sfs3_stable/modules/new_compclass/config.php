<?php
// $Id: config.php 9003 2016-11-08 03:04:16Z chiming $

/* ���o�ǰȨt�γ]�w�� */
require_once "./module-cfg.php";
include_once "../../include/config.php";
require_once "./module-upgrade.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_PLlib.php";

//���o�ҲհѼƳ]�w
$m_arr = &get_sfs_module_set("");
extract($m_arr, EXTR_OVERWRITE);

$PHP_SELF = basename($_SERVER["PHP_SELF"]) ;

//sfs_check();

$teacher_sn=$_SESSION['session_tea_sn'];

$weekN7=array("�P����","�P���@","�P���G","�P���T","�P���|","�P����","�P����");

$today=date("Y-m-d");
//�ؿ����{��
$school_menu_p = array(
"index.php"=>"�Ű�w��",
"reservation.php"=>"�Ű�w��B",
"query.php"=>"�d���°O��",
"query_today.php"=>"����U�`���Ҧ��M��ЫǨϥΦC��"
);
