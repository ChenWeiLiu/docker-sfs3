<?php

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

$MODULE_TABLE_NAME[0] = "message_info";
$MODULE_TABLE_NAME[1] = "message_record";

//---------------------------------------------------
//
// 2.�o�̩w�q�G�Ҳդ���W�١A�к�²�R�W (�� "�Ҳ��v���]�w" �{���ϥ�)
//
// ���|��ܵ��ϥΪ�
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "�T���ǻ�";


//---------------------------------------------------
//
// 3. �o�̩w�q�G�Ҳժ���������T (�� "�۰ʧ�s�{��" ����)
//    �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//
//---------------------------------------------------

// �Ҳճ̫��s����
$MODULE_UPDATE_VER="1.0";

// �Ҳն}�l���
$MODULE_CREATE_DATE="2010-08-19";

// �Ҳճ̫��s���
$MODULE_UPDATE="2010-08-25";

//���n�ҲաA�K�Q�ŧR
$SYS_MODULE=1;

// �Ҳյ{���@��
$MODULE_AUTHOR="wkb";

// �Ҳժ��v����
$MODULE_LICENSE="";

// �Ҳե~��W��(�� "�Ҳճ]�w" �{���ϥ�)
$MODULE_DISPLAY_NAME="�T���ǻ�";

// �Ҳ����ݸs��
$MODULE_GROUP_NAME="�Юv��";

// �Ҳէ�s��
$MODULE_UPDATE_MAN="wkb";

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


//���n�ʰ}�C
$import_array=array("0"=>"�����n","1"=>"���ӭ��n","2"=>"�@��","3"=>"�`�N","4"=>"���n","5"=>"�ܭ��n","6"=>"�D�`���");
$import_color_array=array("#ffcccc","#ffaaaa","#ff8888","#ff6666","#ff4444","#ff2222","#ff0000");

//�����}�C
$kind_array=array("�հ�","�Ű�","�p��","����","��L");
$kind_color_array=array("#ffff99","#ddffaa","#ffddff","#ccffff","#efefef");

//�ɶ��}�C
for($i=0;$i<=24;$i++){
	$hour_array[$i]="$i ��";
}

//�`���}�C
$restart_array=array("0"=>"���`��", "md"=>"�C�~�Ӥ�", "d"=>"�C��Ӥ�","w"=>"�C�g�Ӥ�");

//�P��
$week_array=array("��", "�@", "�G", "�T", "�|", "��", "��");
$monthNames = array("1"=>"�@��", "�G��", "�T��", "�|��", "����", "����","�C��", "�K��", "�E��", "�Q��", "�Q�@��", "�Q�G��");  

$today=date("Y-m-d");

//�ؿ����{��
$school_menu_p = array(
"index.php"=>"��ƾ�"
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

// =
//	array('var'=>"IS_STANDALONE", 'msg'=>"�O�_���W�ߪ��ɭ�(1�O,0�_)", 'value'=>0);


// ��2,3,4....�ӡA�̦������G 

// $SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// $SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

$SFS_MODULE_SETUP[] =
	array('var'=>"page_unit", 'msg'=>"�w�e�T���C����ܵ���", 'value'=>5);

$SFS_MODULE_SETUP[] =
	array('var'=>"page_count", 'msg'=>"�e�T���ɱ����̤U�������Ӽ�", 'value'=>10);
?>