<?php

//---------------------------------------------------
//
// 1.�o�̩w�q�G�t���ܼ� (�� "�Ҳզw�˺޲z" �{���ϥ�)
//------------------------------------------
//
// "�Ҳզw�˺޲z" �{���|�g�J�Q�ժ� SFS/pro_kind ����
//
// ��ĳ�G�о��q�έ^��j�g�өw�q�A�̦n��Ѧr���ݥX��N�����N�q�C
//---------------------------------------------------
// �z����⦹�@�Ҳթ�b���@�Өt�ΰ϶����O?
//
// �ثe�Ȧ��G�Ϩѱz���
//
// "�հȦ�F" �Ҳհ϶��N�X�G28
// "�u��c"  �Ҳհ϶��N�X�G161
//---------------------------------------------------

// �z�o�ӼҲժ��W�١A�N�O�z�o�ӼҲթ�m�b SFS �����ؿ��W��

$MODULE_NAME = "jshow";

//���Ҳն��Ϥ��޲z�v
$MODULE_MAN = 1 ;

//�޲z�v����
$MODULE_MAN_DESCRIPTION = "�㦳�޲z�v�H��,�i�R�ר�L�H������";

//---------------------------------------------------
//
// 2.�o�̩w�q�G�Ҳո�ƪ��W�� (�� "�Ҳզw�˺޲z" �{���ϥ�)
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

$MODULE_TABLE_NAME[0] = "jshow_setup";  //�����ϳ]�w
$MODULE_TABLE_NAME[1] = "jshow_check"; 	//�����ϱ��v
$MODULE_TABLE_NAME[2] = "jshow_pic";   	//�Ϥ����

//
// 3.�o�̩w�q�G�Ҳդ���W�١A�к�²�R�W (�� "�Ҳզw�˺޲z" �{���ϥ�)
//
// ���|��ܵ��ϥΪ�
//-----------------------------------------------
$MODULE_PRO_KIND_NAME = "Joomla!�Ϥ��i�ܤκ޲z";

//---------------------------------------------------
//
// 4. �o�̩w�q�G�Ҳժ���������T (�� "�����t�ε{��" ����)
//
//---------------------------------------------------

// �Ҳժ���
$MODULE_VER="1.0.0";

// �Ҳյ{���@��
$MODULE_AUTHOR="smallduh";

// �Ҳժ��v����
$MODULE_LICENSE="";

// �Ҳե~��W��(�� "�Ҳճ]�w" �{���ϥ�)
$MODULE_DISPLAY_NAME="Joomla!�Ϥ��i�ܤκ޲z";

// �Ҳն}�l���
$MODULE_CREATE_DATE="2014-03-20";

// �Ҳճ̫��s���
$MODULE_UPDATE="2014-03-20 11:00:00";

// �Ҳէ�s��
$MODULE_UPDATE_MAN="smallduh";


//---------------------------------------------------
//
// 5. �o�̽Щw�q�G�z�o��{���ݭn�Ψ쪺�G�ܼƩα`��
//------------------------------^^^^^^^^^^
//
// (���Q�Q "�Ҳճ]�w" �{�����ު̡A�иm���)
//
// ��ĳ�G�о��q�έ^��j�g�өw�q�A�̦n�n��Ѧr���ݥX��N�����N�q�C
//---------------------------------------------------
$menu_p = array(
"jshow_upload.php"=>"���ɤW��",
"jshow_show.php"=>"�i�ϳ]�w",
"jshow_setup.php"=>"�����Ϻ޲z",
"jshow_check.php"=>"�����ϱ��v"
);


//---------------------------------------------------
//
// 6. �o�̩w�q�G�w�]�ȭn�� "�Ҳճ]�w" �{���ӱ��ު̡A
//    �Y���Q�A�i�����]�w�C
//
// �榡�G var �N���ܼƦW��
//       msg �N����ܰT��
//       value �N���ܼƳ]�w��
//---------------------------------------------------
$SFS_MODULE_SETUP[] =
	array('var'=>"api_key", 'msg'=>"joomla�Ҳճs�uAPI�K�X", 'value'=>"publicKey");

?>