<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//�t�γ]�w��
	include_once "./module-cfg.php";
	include_once "../../include/config.php";
	
	require_once "./module-upgrade.php";


//���o�Ҳ��ܼ�
$m_arr = &get_module_setup("computer_manager");
extract($m_arr,EXTR_OVERWRITE);
?>

