<?php

// $Id: config.php 8859 2016-03-29 06:09:44Z qfon $
require "../../include/config.php";

require "module-cfg.php";
include_once "../../include/sfs_case_PLlib.php";

//---------------------------------------------------
// �o�̽ФޤJ�z�ۤv���禡�w
//---------------------------------------------------
include_once "my_fun.php";
/* �W���ɮ׼Ȧs�ؿ� */
$path_str = "temp/student/";
set_upload_path($path_str);
$temp_path = $UPLOAD_PATH.$path_str;
//���o�Ҳճ]�w
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
$section_include=$section_include?$section_include:'1,2,3,4,5,6,7';
//�]�w���
$sec=array("�ɡ@�X","�Ĥ@�`","�ĤG�`","�ĤT�`","�ĥ|�`","�Ĥ��`","�Ĥ��`","�ĤC�`","���@�X");
$sec_id=array("0"=>"uf","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"df");
$abs_kind=array("C"=>"�m��","V"=>"�ư�","S"=>"�f��","M"=>"�ల","B"=>"����");
$reward_kind=array("1"=>"ĵ�i","2"=>"�p�L","3"=>"�j�L","4"=>"�ż�","5"=>"�p�\\","6"=>"�j�\\");
$c_times=array("1"=>"�@","2"=>"�G","3"=>"�T","4"=>"�|","5"=>"��","6"=>"��","7"=>"�C","8"=>"�K","9"=>"�E","10"=>"�Q");
$nor_val=array("1"=>"���{�u��","2"=>"���{�}�n","3"=>"���{�|�i","4"=>"�ݦA�[�o","5"=>"���ݧ�i");
$nor_kind=array("10"=>"1","9"=>"1","8"=>"2","7"=>"2","6"=>"3","5"=>"3","4"=>"3","3"=>"4","2"=>"4","1"=>"5","0"=>"5");
?>