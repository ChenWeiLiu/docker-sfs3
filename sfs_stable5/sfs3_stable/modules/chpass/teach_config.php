<?php

include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";
//�ؿ����{��
$teach_menu_p = array("../teacher_self/teach_list.php" => "�򥻸��", "../teacher_self/teach_connect.php" => "�������", "teach_cpass.php" => "���K�X");
//���o�ҲհѼƪ����O�]�w
$chp_arr = get_module_setup('chpass');
extract($chp_arr, EXTR_OVERWRITE);

?>