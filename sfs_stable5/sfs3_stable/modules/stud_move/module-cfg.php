<?php

// $Id: module-cfg.php 9110 2017-08-03 05:24:30Z infodaes $

//---------------------------------------------------
//
// 1.�o�̩w�q�G�Ҳո�ƪ��W�� (�� "�Ҳ��v���]�w" �{���ϥ�)
//   �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//-----------------------------------------------
//
// �Y���@�ӥH�W�A�б���  �}�C�өw�q
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

// ��ƪ��W�٩w�q

$MODULE_TABLE_NAME[0] = "";

//---------------------------------------------------
//
// 2.�o�̩w�q�G�Ҳդ���W�١A�к�²�R�W (�� "�Ҳ��v���]�w" �{���ϥ�)
//
// ���|��ܵ��ϥΪ�
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "�ǥͲ���";


//---------------------------------------------------
//
// 3. �o�̩w�q�G�Ҳժ���������T (�� "�۰ʧ�s�{��" ����)
//    �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//
//---------------------------------------------------

// �Ҳճ̫��s����
$MODULE_UPDATE_VER="";

// �Ҳճ̫��s���
$MODULE_UPDATE="2003-09-23";

//���n�ҲաA�K�Q�ŧR
$SYS_MODULE=1;
//---------------------------------------------------
//
// 4. �o�̽Щw�q�G�z�o��{���ݭn�Ψ쪺�G�ܼƩα`��
//---------------------------------^^^^^^^^^^
//
// (���Q�Q "�ҲհѼƺ޲z" ���ު̡A�иm���)
//
// ��ĳ�G�о��q�έ^��j�g�өw�q�A�̦n�n��Ѧr���ݥX��N�����N�q�C
//
// �o�Ϫ� "�ܼƦW��" �i�H�ۥѧ���!!!
//
//---------------------------------------------------


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

// =
//	array('var'=>"IS_STANDALONE", 'msg'=>"�O�_���W�ߪ��ɭ�(1�O,0�_)", 'value'=>0);


// ��2,3,4....�ӡA�̦������G 

$SFS_MODULE_SETUP[0] =array('var'=>"check_study_year", 'msg'=>"�_�ǧ@�~�ˬd�J�Ǧ~", 'value'=>array(0=>"�O",1=>"�_"));
$SFS_MODULE_SETUP[1] =array('var'=>"default_unit", 'msg'=>"�w�]���ʮ֭�����W��", 'value'=>"�O�����F��");
$SFS_MODULE_SETUP[2] =array('var'=>"default_word", 'msg'=>"�w�]�֭�r", 'value'=>"���о�");
$SFS_MODULE_SETUP[3] =array('var'=>"default_reason", 'msg'=>"�w�]���ʲz��", 'value'=>"�E�~");
$SFS_MODULE_SETUP[4] =array('var'=>"degradu", 'msg'=>"�R���{�Ǧ~���~�O���ɦ^�_�ǥʹN�Ǫ��A", 'value'=>array("0"=>"�_","1"=>"�O"));
$SFS_MODULE_SETUP[5] =array('var'=>"pgp_num", 'msg'=>"PGP���_���X", 'value'=>"");
$SFS_MODULE_SETUP[6] =array('var'=>"sign_url", 'msg'=>"ñ���ɨӷ�", 'value'=>"");
$SFS_MODULE_SETUP[7] =array('var'=>"sign_width", 'msg'=>"ñ������ܼe��", 'value'=>"");
$SFS_MODULE_SETUP[8] =array('var'=>"sign_height", 'msg'=>"ñ������ܰ���", 'value'=>"");
$SFS_MODULE_SETUP[9] =array('var'=>"days_limit", 'msg'=>"XML�����U����ƭ���", 'value'=>"7");
$SFS_MODULE_SETUP[10] =array('var'=>"times_limit", 'msg'=>"XML�����U�����ƭ���", 'value'=>"3");
// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>

