<?php

// $Id: module-cfg.php 5310 2009-01-10 07:57:56Z hami $

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

$MODULE_TABLE_NAME[0] = "school_board";

//---------------------------------------------------
//
// 2.�o�̩w�q�G�Ҳդ���W�١A�к�²�R�W (�� "�Ҳ��v���]�w" �{���ϥ�)
//
// ���|��ܵ��ϥΪ�
//-----------------------------------------------
// �z�o�ӼҲժ��W�١A�N�O�z�o�ӼҲթ�m�b SFS �����ؿ��W��


$MODULE_PRO_KIND_NAME = "�հȤ��G��";


//---------------------------------------------------
//
// 3. �o�̩w�q�G�Ҳժ���������T (�� "�۰ʧ�s�{��" ����)
//    �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//
//---------------------------------------------------

// �Ҳճ̫��s����
$MODULE_UPDATE_VER="1.0.0";

// �Ҳճ̫��s���
$MODULE_UPDATE="2003-05-05 ";


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



  $news_title = "�Ǯդ��i";
  
  //�ϥ��ɮ�
  $icon_dir = "images";
  $space_icon = "$icon_dir/space.gif";
  $main_icon = "$icon_dir/main.gif";
  $new_main_icon = "$icon_dir/main_new.gif";
  $reading_icon = "$icon_dir/reading.gif";
  $print_icon = "$icon_dir/print.gif";
  $stop_icon = "$icon_dir/stop.gif" ;
  $top_icon= "$icon_dir/top.gif" ;
  

  $tbname = "school_board" ;	//��ƪ��W

  $usertb = "teacher_base" ;



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
$SFS_MODULE_SETUP[0] =
	array('var'=>"msgs_per_page", 'msg'=>"�C����ܵ���", 'value'=>15);
$SFS_MODULE_SETUP[1] =
	array('var'=>"showday", 'msg'=>"�̪񪺮����Ѽ�", 'value'=>3);
$SFS_MODULE_SETUP[2] =
	array('var'=>"showdays", 'msg'=>"�X�{�X�Ѥ������i", 'value'=>120);
$SFS_MODULE_SETUP[3] =
	array('var'=>"topdays", 'msg'=>"�m����ƭ���", 'value'=>7);

?>