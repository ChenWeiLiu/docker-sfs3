<?php

// $Id: module-cfg.php 5310 2009-01-10 07:57:56Z smallduh $

//---------------------------------------------------
//
// 1.�o�̩w�q�G�Ҳո�ƪ�W�� (�� "�Ҳ��v���]�w" �{���ϥ�)
//   �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//-----------------------------------------------
//
// �Y���@�ӥH�W�A�б��� $MODULE_TABLE_NAME �}�C�өw�q
//
// �]�i�H�ΥH�U�o�س]�k�G
//
// $MODULE_TABLE_NAME=array(0=>"lunchtb", 1=>"xxxx");
// 
// $MODULE_TABLE_NAME[0] = "lunchtb";
// $MODULE_TABLE_NAME[1]="xxxx";
//
// �Ъ`�N�n�M module.sql ���� table �W�٤@�P!!!
//---------------------------------------------------

// ��ƪ�W�٩w�q

$MODULE_TABLE_NAME[0] = "comp_roomsite";  			 //�q���ЫǮy��

//---------------------------------------------------
//
// 2.�o�̩w�q�G�Ҳդ���W�١A�к�²�R�W (�� "�Ҳ��v���]�w" �{���ϥ�)
//
// ���|��ܵ��ϥΪ�
//-----------------------------------------------

$MODULE_PRO_KIND_NAME = "�q���ЫǤW���޲z";

// �ݭn�ϥκ޲z���v��
$MODULE_MAN=true;


//---------------------------------------------------
//
// 3. �o�̩w�q�G�Ҳժ���������T (�� "�۰ʧ�s�{��" ����)
//    �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//
//---------------------------------------------------

// �Ҳճ̫��s����
$MODULE_UPDATE_VER="1.0";

// �Ҳճ̫��s���
$MODULE_UPDATE="2016-10-21";


//---------------------------------------------------
//
// 4. �o�̽Щw�q�G�z�o��{���ݭn�Ψ쪺�G�ܼƩα`��
//---------------------------------^^^^^^^^^^
//
// (���Q�Q "�ҲհѼƺ޲z" ���ު̡A�иm���)
//
// ��ĳ�G�о��q�έ^��j�g�өw�q�A�̦n�n��Ѧr���ݥX��N���N�q�C
//
// �o�Ϫ� "�ܼƦW��" �i�H�ۥѧ���!!!
//
//---------------------------------------------------
//�ؿ����{��
$school_menu_p = array(
"comp_classroom.php"=>"�q���ЫǤW���޲z",
"comp_classroom_set.php"=>"�]�w�q���ЫǮy��",
"comp_classroom_access.php"=>"�]�w�ҥ~����IP",
"comp_firewall_test.php"=>"������n�J����",
"readme.php"=>"���n����"
);


//---------------------------------------------------
//
// 5. �o�̩w�q�G�w�]�ȭn�� "�ҲհѼƺ޲z" �{���ӱ��ު̡A
//    �Y���Q�A�i�����]�w�C
//
// �榡�G var �N���ܼƦW��
//       msg �N����ܰT��
//       value �N���ܼƳ]�w��
//
// �Y�z�M�w�N�o���ܼƥ�� "�ҲհѼƺ޲z" �ӱ��ޡA����z���Ҳյ{��
// �N�n��o���ܼƦ��P���A�]�N�O���G�Y�o���ܼƭȦb�ҲհѼƺ޲z�����ܡA
// �z���ҲմN�n�w��o���ܼƦ����P���ʧ@�ϬM�C
//
// �Ҧp�G�Y�d���O�ҲաA���ѨC����ܵ��ƪ�����A�p�U�G
// $SFS_MODULE_SETUP[1] =
// array('var'=>"PAGENUM", 'msg'=>"�C����ܵ���", 'value'=>10);
//
// �W�z���N��O���G�z�w�q�F�@���ܼ� PAGENUM�A�o���ܼƪ��w�]�Ȭ� 10
// PAGENUM ������W�٬� "�C����ܵ���"�A�o���ܼƦb�w�˼Ҳծɷ|�g�J
// pro_module �o�� table ��
//
// �ڭ̦����Ѥ@�Ө禡 get_module_setup
// �ѱz���Υثe�o���ܼƪ��̷s���p�ȡA
//
// �Ա��аѦ� include/sfs_core_module.php ���������C
//
// �o�Ϫ� "�ܼƦW�� $SFS_MODULE_SETUP" �Фŧ���!!!
//---------------------------------------------------

// ��2,3,4....�ӡA�̦������G 

 $SFS_MODULE_SETUP[1] =
	array('var'=>"firewall_ip", 'msg'=>"������IP", 'value'=>"");

 $SFS_MODULE_SETUP[2] =
     array('var'=>"firewall_user", 'msg'=>"������n�J�b��", 'value'=>"");

 $SFS_MODULE_SETUP[3] =
    array('var'=>"firewall_pwd", 'msg'=>"������n�J�K�X", 'value'=>"");

 $SFS_MODULE_SETUP[4] =
    array('var'=>"addrgrp_deny", 'msg'=>"������deny�s�զW��(��ĳ�ιw�]��)", 'value'=>"sfs3_comp_out_deny");

 $SFS_MODULE_SETUP[5] =
    array('var'=>"addrgrp_deny_tag", 'msg'=>"���إ�deny�s�զӳ]����ip", 'value'=>"1.1.1.1");

 $SFS_MODULE_SETUP[6] =
    array('var'=>"addrgrp_access", 'msg'=>"������access�s�զW��(��ĳ�ιw�]��)", 'value'=>"sfs3_comp_out_access");

 $SFS_MODULE_SETUP[7] =
    array('var'=>"addrgrp_comp_all", 'msg'=>"�q���Ы�ip�s�զW��(��ĳ�ιw�]��)", 'value'=>"sfs3_comp_all");

 $SFS_MODULE_SETUP[8] =
    array('var'=>"VDOM", 'msg'=>"�O�_�ҥ�VDOM(����������\��)", 'value'=>array(0=>"�_",1=>"�O"));

?>