<?php
// $Id: config.php 8977 2016-09-19 07:49:18Z infodaes $

/*�J�ǰȨt�γ]�w��*/
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";

//�ޤJ���
include "./my_fun.php";
include "module-upgrade.php";

//���
$menu_p = array("score_query.php"=>"���Z�d��",
		"top.php"=>"���q���Z(�w��)�u���ƦW",
		"avg.php"=>"�w�����q�U�Z����",
		"manage.php"=>"���Zú�檬�p(�~�Žҵ{)",
		"manage_class.php"=>"���Zú�檬�p(�Z�Žҵ{)",
		"manage_ele.php"=>"���կZú�檬�p",
		"tol.php"=>"�����`��",
		"scope_tol.php"=>"����`��",
		"check.php"=>"�ťզ��Z�ˬd",
		"check100.php"=>"�ʤ����Z�ˬd",
		"out.php"=>"�ư��W��");
//���o�Ҳճ]�w
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
?>