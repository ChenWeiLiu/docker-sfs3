<?php

// $Id: module-cfg.php 9111 2017-08-03 07:29:55Z infodaes $

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


$MODULE_PRO_KIND_NAME = "��`���Z�޲z";


//---------------------------------------------------
//
// 3. �o�̩w�q�G�Ҳժ���������T (�� "�۰ʧ�s�{��" ����)
//    �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//
//---------------------------------------------------

// �Ҳճ̫��s����
$MODULE_UPDATE_VER="2.0";

// �Ҳճ̫��s���
$MODULE_UPDATE="2009/03/13";

//���n�ҲաA�K�Q�ŧR
if ($IS_JHORES==6)
	$SYS_MODULE=1;
else
	$SYS_MODULE=0;
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

if ($IS_JHORES==6)
$menu_p = array(
"input.php"=>"��`���Z��J",
"nor.php"=>"��`���Z�`��",
"check.php"=>"���ή�W��",
"report.php"=>"��X���{�O����",
"cal.php"=>"�έp�X�ʮu(�T�w1~7�`)�P���g",
"cal_new.php"=>"�έp�X�ʮu(�ۿ�`��)�P���g",
"chk.php"=>"�]�w�ˮ֪�",
"prt.php"=>"�C�L��`���Z�`��",
"five.php"=>"���~���Z��",
"disgrad.php"=>"�׷~��ĳ�W��",
"disgrad2.php"=>"�׷~��ĳ�W��(�s)",
"award.php"=>"���Լ��W��",
"club_serv.php"=>"�C�L�Ǵ��q����");

else
$menu_p=array(
"cal.php"=>"�έp�X�ʮu(�T�w1~7�`)�P���g",
"cal_new.php"=>"�έp�X�ʮu(�ۿ�`��)�P���g",
"chk.php"=>"�]�w�ˮ֪�",
"award.php"=>"���Լ��W��"
);

$item_arr['default_jh']=array(
array("�R���","��O����ۻ��e���","��O���y��Ω�P�����"),
array("��§��","�����|��ݲ����Ưx","��v���A��������§","�ݨ�v���B�P�ǩΨӻ��|�ݦn"),
array("�u����","�W�ү���ɶi�Ы�","��i��Z�W�Υͬ�����","��̳W�w��a�ǥΫ~","����u�Юv�Ұ�W�d���欰","����u�ҸճW�h"),
array("�d����","����Z�Ťu�@��J��¾�d","�W�ү�M��ť��","�����ú��@�~"),
array("���w��","��`���Τ��ιq�A���n�귽�^��","��R���ε��Τ���"),
array("�ͷR���h","��ͷR�P��","���߰ѻP���@�A��"),
array("�ζ��X�@","��P�P�Ǥ��U�X�@","�n���ѻP���鬡��")
);

$item_arr['default_es']=array(
array("�q�R�H","�ͷR�P�ǡA���U�L�H�A���O�H�۷Q","���ǩM�L�����P��ƪ��H","�P�¥ͬ������ڭ̪A�Ȫ��H","�g���L�H�A�þǲߥL�H�����B","�۹ꭱ����~�A�i��勵"),
array("�R���","���e�K��~��B���������f","�ѥ]�B��P�M�ЫǫO�����A���k�w��","�O�����e���A�w���װū���","�U�������a�A���@�ն���","���T�ϥδZ�ҡA�O�����"),
array("�u����","���u�ƶ��Φ�i����","���ټM�B�b�]�B���M�I�ʧ@�ή��F���H","���u���\���ǡA���䨫��Y","���ɨ�աA�W�Ҥ����","���u�����ϥγW�w�A���H�N�}�a"),
array("��§��","�|�V�v���B�ӻ��ΦP�ǰݦn","�`���u�СB���¡B�藍�_�v","�J�Ӳ�ť�O�H�o��","���榳§�A���n�ӻy�B�����ʼɪ���","��߱����v��������"),
array("�����O","���n�U�������B�U����q","���o�귽�^���A�Q��","�Ǯդ��\\�B��~���ʮɡA�۳��\\����\\","�`���෽�A�H�����O�B�����s�Y","�R�������A�����O�귽")
);

$item_sel=array("default_jh"=>"�w�]_�ꤤ","default_es"=>"�w�]_��p");

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

$ABOO_ARR= array("0"=>"���Ǵ����ԥ[����","1"=>"�@�Ӥ���ԥ[�@��");
$DBOO_ARR= array("1"=>"�̧C0��","0"=>"�L�U��");
$UBOO_ARR= array("1"=>"�̰�100��","0"=>"�L�W��");
$SCORE_ARR= array("0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9");
$SFS_MODULE_SETUP[0] =
	array('var'=>"f_w", 'msg'=>"�Ĥ@��ĵ�i���X��", 'value'=>array("0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[1] =
	array('var'=>"s_w", 'msg'=>"�ĤG��ĵ�i���X��", 'value'=>array("1"=>"1","0"=>"0","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[2] =
	array('var'=>"t_w", 'msg'=>"�ĤT���H�Wĵ�i���X��", 'value'=>array("1"=>"1","0"=>"0","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[3] =
	array('var'=>"f_sw", 'msg'=>"�Ĥ@���p�L���X��", 'value'=>array("2"=>"2","0"=>"0","1"=>"1","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[4] =
	array('var'=>"s_sw", 'msg'=>"�ĤG���p�L���X��", 'value'=>array("2"=>"2","0"=>"0","1"=>"1","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[5] =
	array('var'=>"t_sw", 'msg'=>"�ĤT���H�W�p�L���X��", 'value'=>array("3"=>"3","0"=>"0","1"=>"1","2"=>"2","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[6] =
	array('var'=>"f_bw", 'msg'=>"�Ĥ@���j�L���X��", 'value'=>array("7"=>"7","0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[7] =
	array('var'=>"s_bw", 'msg'=>"�ĤG���j�L���X��", 'value'=>array("7"=>"7","0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[8] =
	array('var'=>"t_bw", 'msg'=>"�ĤT���H�W�j�L���X��", 'value'=>array("7"=>"7","0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[9] =
	array('var'=>"f_a", 'msg'=>"�Ĥ@���ż��[�X��", 'value'=>array("1"=>"1","0"=>"0","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[10] =
	array('var'=>"s_a", 'msg'=>"�ĤG���ż��[�X��", 'value'=>array("1"=>"1","0"=>"0","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[11] =
	array('var'=>"t_a", 'msg'=>"�ĤT���H�W�ż��[�X��", 'value'=>array("1"=>"1","0"=>"0","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[12] =
	array('var'=>"f_sa", 'msg'=>"�Ĥ@���p�\�[�X��", 'value'=>array("3"=>"3","0"=>"0","1"=>"1","2"=>"2","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[13] =
	array('var'=>"s_sa", 'msg'=>"�ĤG���p�\�[�X��", 'value'=>array("3"=>"3","0"=>"0","1"=>"1","2"=>"2","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[14] =
	array('var'=>"t_sa", 'msg'=>"�ĤT���H�W�p�\�[�X��", 'value'=>array("3"=>"3","0"=>"0","1"=>"1","2"=>"2","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9"));
$SFS_MODULE_SETUP[15] =
	array('var'=>"f_ba", 'msg'=>"�Ĥ@���j�\�[�X��", 'value'=>array("9"=>"9","0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8"));
$SFS_MODULE_SETUP[16] =
	array('var'=>"s_ba", 'msg'=>"�ĤG���j�\�[�X��", 'value'=>array("9"=>"9","0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8"));
$SFS_MODULE_SETUP[17] =
	array('var'=>"t_ba", 'msg'=>"�ĤT���H�W�j�\�[�X��", 'value'=>array("9"=>"9","0"=>"0","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8"));
$SFS_MODULE_SETUP[18] =
	array('var'=>"cl_days", 'msg'=>"�Цh�ָ`�ư����@��", 'value'=>"30");
$SFS_MODULE_SETUP[19] =
	array('var'=>"sl_days", 'msg'=>"�Цh�ָ`�f�����@��", 'value'=>"80");
$SFS_MODULE_SETUP[20] =
	array('var'=>"u_score", 'msg'=>"��`���{���Z���L�W��", 'value'=>$UBOO_ARR);
$SFS_MODULE_SETUP[21] =
	array('var'=>"d_score", 'msg'=>"��`���{���Z���L�U��", 'value'=>$DBOO_ARR);
$SFS_MODULE_SETUP[22] =
	array('var'=>"a_score", 'msg'=>"���ԭp��Ҧ�", 'value'=>$ABOO_ARR);
$SFS_MODULE_SETUP[23] =
	array('var'=>"sday", 'msg'=>"�׷~�p���m�Ҹ`��", 'value'=>"40");
$SFS_MODULE_SETUP[24] =
	array('var'=>"sday2", 'msg'=>"�׷~�p��ʮu�`��", 'value'=>"234");
	
//$SFS_MODULE_SETUP[25] =
//	array('var'=>"ncount", 'msg'=>"�}��έp��`���Z", 'value'=>array("0"=>"�_","1"=>"�O"));

$SFS_MODULE_SETUP[26] =	array('var'=>"print_title", 'msg'=>"�Ǵ��q����L�C���D", 'value'=>"�ǥ;ǲߦ欰���{�Ǵ��q����");
$SFS_MODULE_SETUP[27] =	array('var'=>"stud_chk_data", 'msg'=>"�C�L�ɹw�]�Ŀ��`�ͬ����{", 'value'=>array('0'=>'�_','1'=>'�O'));
$SFS_MODULE_SETUP[28] =	array('var'=>"stud_absent", 'msg'=>"�C�L�ɹw�]�Ŀ�X�ʮu", 'value'=>array('0'=>'�_','1'=>'�O'));
$SFS_MODULE_SETUP[29] =	array('var'=>"stud_leader", 'msg'=>"�C�L�ɹw�]�Ŀ�F�����", 'value'=>array('0'=>'�_','1'=>'�O'));
$SFS_MODULE_SETUP[30] =	array('var'=>"stud_reward", 'msg'=>"�C�L�ɹw�]�Ŀ���g�O��", 'value'=>array('0'=>'�_','1'=>'�O'));
$SFS_MODULE_SETUP[31] =	array('var'=>"stud_club", 'msg'=>"�C�L�ɹw�]�Ŀ���ά���", 'value'=>array('0'=>'�_','1'=>'�O'));
$SFS_MODULE_SETUP[32] =	array('var'=>"stud_service", 'msg'=>"�C�L�ɹw�]�Ŀ�A�Ⱦǲ�", 'value'=>array('0'=>'�_','1'=>'�O'));
$SFS_MODULE_SETUP[33] =	array('var'=>"stud_race", 'msg'=>"�C�L�ɹw�]�Ŀ��v�ɰO��", 'value'=>array('0'=>'�_','1'=>'�O'));
$SFS_MODULE_SETUP[34] =	array('var'=>"section_include", 'msg'=>"�X�ʮu�έp���ĸ`��", 'value'=>'1,2,3,4,5,6,7');

?>