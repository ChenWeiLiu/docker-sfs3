<?php
//$Id: module-cfg.php 5519 2009-06-29 01:38:11Z brucelyc $

//---------------------------------------------------
//
// 1.�o�̩w�q�G�Ҳո�ƪ��W�� (�� "�Ҳ��v���]�w" �{���ϥ�)
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

// ��ƪ��W�٩w�q

$MODULE_TABLE_NAME[0] = "score_paper";

//---------------------------------------------------
//
// 2.�o�̩w�q�G�Ҳդ���W�١A�к�²�R�W (�� "�Ҳ��v���]�w" �{���ϥ�)
//
// ���|��ܵ��ϥΪ�
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "�ۭq���Z��";


//---------------------------------------------------
//
// 3. �o�̩w�q�G�Ҳժ���������T (�� "�۰ʧ�s�{��" ����)
//    �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//
//---------------------------------------------------

// �Ҳճ̫��s����
$MODULE_UPDATE_VER="1.0.0";

// �Ҳճ̫��s���
$MODULE_UPDATE="2003-12-02";

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

//�ؿ����{��
$school_menu_p = array(
"input.php"=>"������ƿ�J",
"make.php"=>"���Z��s�@",
"index.php"=>"�ۭq���Z��",
"paper_upload.php"=>"�޲z�W�Ǧ��Z��",
"mark.php"=>"���Z�����",
"stick.php"=>"���Z�K��",
"faq.php"=>"���D��"
);
// �t�οﶵ
$performance=array(1=>"��`�欰���{",2=>"���鬡�ʪ��{",3=>"���@�A��",4=>"�ե~�S�����{");
$performance_option=array(1=>"���{�u��",2=>"���{�}�n",3=>"���{�|�i",4=>"�ݦA�[�o",5=>"���ݧ�i");

//�E�~�@�e�������
$ss9[]="�y��-����y��";
$ss9[]="�y��-�m�g�y��";
$ss9[]="�y��-�^�y";
$ss9[]="�ƾ�";
$ss9[]="���d�P��|";
$ss9[]="�ͬ�";
$ss9[]="�۵M�P�ͬ����";
$ss9[]="���|";
$ss9[]="���N�P�H��";
$ss9[]="��X����";
//$ss9[]="�u�ʽҵ{"; //���T���u�ʾǲ߮ɼ�, �Y�����Z���֤J�U�ǲ߻��, �G�L�����s�b


//�x�n��
$tnc_arr=array(
			array('���{����','7','11.9'),
			array('���{����','7.1','11.9'),
			array('�E_�y��-����y�����','7','11.9'),
			array('�E_�y��-�m�g�y�����','7','11.9'),
			array('�E_�y��-�^�y����','7','11.9'),
			array('�E_�y�奭��','7','11.9'),
			array('�E_�y�嵥��','7.1','11.9'),
			array('�E_���d�P��|����','7','11.9'),
			array('�E_���d�P��|����','7.1','11.9'),
			array('�E_�ƾǤ���','7','11.9'),
			array('�E_�ƾǵ���','7.1','11.9'),
			array('�E_���|����','7','11.9'),
			array('�E_���|����','7.1','11.9'),
			array('�E_���N�P�H�����','7','11.9'),
			array('�E_���N�P�H�嵥��','7.1','11.9'),
			array('�E_�۵M�P�ͬ���ޤ���','7','11.9'),
			array('�E_�۵M�P�ͬ���޵���','7.1','11.9'),
			array('�E_��X���ʤ���','7','11.9'),
			array('�E_��X���ʵ���','7.1','11.9'),
			array('�W�Ҥ��','7.1','11.9'),
			array('�ư�_��','7','11.9'),
			array('�f��_��','7','11.9'),
			array('�m��_��','7','11.9'),
			array('�ʮu�`���','7.1','11.9'),
			array('�ɮv���y�Ϋ�ĳ','3.8','11.9'),
);

//���ƿ�
if($_REQUEST['cols']=="chc_1"){
$chc_arr=array(
			array('�E_�y��-����y�����',6.25,10.3),
			array('�E_�y��-�m�g�y�����',6.25,10.3),
			array('�L',6.25,10.3),
			array('�E_���d�P��|����',6.25,10.3),
			array('�E_�ƾǤ���',6.25,10.3),
			array('�E_�ͬ�����',19,10.3),
			array('�E_��X���ʤ���',6.25,10.3),
			array('���{����',37.8,10.3),
			array('�W�Ҥ��',6.25,10.3),
			array('�ư�_��',6.25,10.3),
			array('�f��_��',6.25,10.3),
			array('�m��_��',6.25,10.3),
			array('�ʮu�`���',6.25,10.3),
			array('�ɮv���y�Ϋ�ĳ',6.2,10.3)
);
}
//���ƿ�
if($_REQUEST['cols']=="chc_2"){
$chc_arr=array(
			array('�E_�y��-����y�����',6.25,10.3),
			array('�E_�y��-�m�g�y�����',6.25,10.3),
			array('�E_�y��-�^�y����',6.25,10.3),
			array('�E_���d�P��|����',6.25,10.3),
			array('�E_�ƾǤ���',6.25,10.3),
			array('�E_���|����',6.25,10.3),
			array('�E_�۵M�P�ͬ���ޤ���',6.25,10.3),
			array('�E_���N�P�H�����',6.25,10.3),
			array('�E_��X���ʤ���',6.25,10.3),
			array('���{����',37.8,10.3),
			array('�W�Ҥ��',6.25,10.3),
			array('�ư�_��',6.25,10.3),
			array('�f��_��',6.25,10.3),
			array('�m��_��',6.25,10.3),
			array('�ʮu�`���',6.25,10.3),
			array('�ɮv���y�Ϋ�ĳ',6.2,10.3)
);
}

//�x����
if($_REQUEST['cols']=="tc_1")
$tc_arr=array(
			array('�E_�y��-����y�����',7.0,11.5),
			array('�E_�y��-�m�g�y�����',7.2,11.5),
			array('�E_�y��-�^�y����',7.2,11.5),
			array('�E_�y�奭��',7.2,11.5),
			array('�E_�ƾǤ���',7.3,11.5),
			array('�E_�ͬ�����',21.6,11.5),
			array('�E_���d�P��|����',7.2,11.5),
			array('�E_��X���ʤ���',7.2,11.5),
			array('�Ǵ��ǲ߻�즨�Z',15,11.5),
			array('���{����',7.5,11.5),
			array('��L-1',7.5,11.5),
			array('��L-2',7.5,11.5),
			array('��L-3',7.5,11.5),
			array('�W�Ҥ��',7.6,11.5),
			array('�ư�_��',7.6,11.5),
			array('�f��_��',7.6,11.5),
			array('�m��_��',7.6,11.5),
			array('��L_��',7.6,11.5),
);
if($_REQUEST['cols']=="tc_2")
$tc_arr=array(
			array('�E_�y��-����y�����',7.0,11.5),
			array('�E_�y��-�m�g�y�����',7.2,11.5),
			array('�E_�y��-�^�y����',7.2,11.5),
			array('�E_�y�奭��',7.2,11.5),
			array('�E_�ƾǤ���',7.3,11.5),
			array('�E_���|����',7.2,11.5),
			array('�E_�۵M�P�ͬ���ޤ���',7.2,11.5),
			array('�E_���N�P�H�����',7.2,11.5),
			array('�E_���d�P��|����',7.2,11.5),
			array('�E_��X���ʤ���',7.2,11.5),
			array('�Ǵ��ǲ߻�즨�Z',15,11.5),
			array('���{����',7.5,11.5),
			array('��L-1',7.5,11.5),
			array('��L-2',7.5,11.5),
			array('��L-3',7.5,11.5),
			array('�W�Ҥ��',7.6,11.5),
			array('�ư�_��',7.6,11.5),
			array('�f��_��',7.6,11.5),
			array('�m��_��',7.6,11.5),
			array('��L_��',7.6,11.5),
);

if($_REQUEST['cols']=="cyc_1")
$cyc_arr=array(
	array('�E_�y��-����y�����',5.6,11),
	array('�E_�y��-�^�y����',5.6,11),
	array('�E_�y��-�m�g�y�����',5.6,11),
	array('�E_�y��-����',5.6,11),
	array('�E_�y��-����',5.6,11),
	array('�E_�ƾǤ���',5.6,11),
	array('�E_�ƾǵ���',5.6,11),
	array('�E_�ͬ�����',16.9,11),
	array('�E_�ͬ�����',16.9,11),
	array('�E_���d�P��|����',5.6,11),
	array('�E_���d�P��|����',5.6,11),
	array('�E_��X���ʤ���',5.6,11),
	array('�E_��X���ʵ���',5.6,11),
	array('�u�ʽҵ{-����',5.6,11),
	array('�u�ʽҵ{-����',5.6,11),
	array('�u�ʽҵ{�G-����',5.6,11),
	array('�u�ʽҵ{�G-����',5.6,11),
	array('�u�ʽҵ{�T-����',5.6,11),
	array('�u�ʽҵ{�T-����',5.6,11),
	array('�u�ʽҵ{�|-����',5.6,11),
	array('�u�ʽҵ{�|-����',5.6,11),
	array('�ǲ߻�즨�Z����',5.6,11),
	array('�ǲ߻�즨�Z����',5.6,11),
);

if($_REQUEST['cols']=="cyc_2")
$cyc_arr=array(
	array('�E_�y��-����y�����',5.6,11),
	array('�E_�y��-�^�y����',5.6,11),
	array('�E_�y��-�m�g�y�����',5.6,11),
	array('�E_�y��-����',5.6,11),
	array('�E_�y��-����',5.6,11),
	array('�E_�ƾǤ���',5.6,11),
	array('�E_�ƾǵ���',5.6,11),
	array('�E_�۵M�P�ͬ���ޤ���',5.6,11),
	array('�E_�۵M�P�ͬ���޵���',5.6,11),
	array('�E_���N�P�H�����',5.6,11),
	array('�E_���N�P�H�嵥��',5.6,11),
	array('�E_���|����',5.6,11),
	array('�E_���|����',5.6,11),
	array('�E_���d�P��|����',5.6,11),
	array('�E_���d�P��|����',5.6,11),
	array('�E_��X���ʤ���',5.6,11),
	array('�E_��X���ʵ���',5.6,11),
	array('�u�ʽҵ{-����',5.6,11),
	array('�u�ʽҵ{-����',5.6,11),
	array('�u�ʽҵ{�G-����',5.6,11),
	array('�u�ʽҵ{�G-����',5.6,11),
	array('�u�ʽҵ{�T-����',5.6,11),
	array('�u�ʽҵ{�T-����',5.6,11),
	array('�u�ʽҵ{�|-����',5.6,11),
	array('�u�ʽҵ{�|-����',5.6,11),
	array('�ǲ߻�즨�Z����',5.6,11),
	array('�ǲ߻�즨�Z����',5.6,11),
);

if($_REQUEST['cols']=="cyc_3")
$cyc_arr=array(
	array('�E_�y��-����y�����',6.5,10),
	array('�E_�y��-�^�y����',6.5,10),
	array('�E_�y��-�m�g�y�����',6.5,10),
	array('�E_�y��-����',6.5,10),
	array('�E_�y��-����',6.5,10),
	array('�E_�ƾǤ���',6.5,10),
	array('�E_�ƾǵ���',6.5,10),
	array('�E_�۵M�P�ͬ���ޤ���',6.5,10),
	array('�E_�۵M�P�ͬ���޵���',6.5,10),
	array('�E_���N�P�H�����',6.5,10),
	array('�E_���N�P�H�嵥��',6.5,10),
	array('�E_���|����',6.5,10),
	array('�E_���|����',6.5,10),
	array('�E_���d�P��|����',6.5,10),
	array('�E_���d�P��|����',6.5,10),
	array('�E_��X���ʤ���',6.5,10),
	array('�E_��X���ʵ���',6.5,10),
	array('�u�ʽҵ{-����',6.5,10),
	array('�u�ʽҵ{-����',6.5,10),
	array('�u�ʽҵ{�G-����',6.5,10),
	array('�u�ʽҵ{�G-����',6.5,10),
	array('�ǲ߻�즨�Z����',6.5,10),
	array('�ǲ߻�즨�Z����',6.5,10),
	array('�ͬ����{����',6.5,10),
	array('�ͬ����{����',6.5,10),
	array('���X�u���',6.5,10),
	array('�ư�_��',6.5,10),
	array('�f��_��',6.5,10),
	array('�m��_��',6.5,10),
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
// �ϥΪk�G
//
// $ret_array =& get_module_setup("module_makeer")
//
//
// �Ա��аѦ� include/sfs_core_module.php ���������C
//
// �o�Ϫ� "�ܼƦW�� $SFS_MODULE_SETUP" �Фŧ���!!!
//---------------------------------------------------


//$SFS_MODULE_SETUP[0] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>1);

// ��2,3,4....�ӡA�̦������G 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>