<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//�t�γ]�w��
	include_once "./module-cfg.php";
	include_once "../../include/config.php";
	// ���Ψ禡
	include_once "../../include/sfs_case_PLlib.php";
	include_once "../../include/sfs_case_dataarray.php";
	include_once "../../include/sfs_case_studyclass.php";
	include_once "../../include/sfs_oo_overlib.php";
	
	require_once "./module-upgrade.php";
	  

//�W�Ǧ�m , �O���b sfs3 �� config.php��
// $UPLOAD_PATH �����m
// $UPLOAD_URL  URL��m
//
$UPLOAD_BASE=$UPLOAD_PATH."contest";

//�̷s���� 
$UPLOAD_NEWS_PATH=$UPLOAD_PATH."contest/news/"; 
$UPLOAD_NEWS_URL =$UPLOAD_URL."contest/news/";
//�R�e
$UPLOAD_PAINTING_PATH=$UPLOAD_PATH."contest/painting/";
$UPLOAD_PAINTING_URL =$UPLOAD_URL."contest/painting/";
//�ʵe
$UPLOAD_ANIMATION_PATH=$UPLOAD_PATH."contest/animation/";
$UPLOAD_ANIMATION_URL =$UPLOAD_URL."contest/animation/";
//Scratch�ʵe
$UPLOAD_SCRATCH_ANI_PATH=$UPLOAD_PATH."contest/scratch_ani/";
$UPLOAD_SCRATCH_ANI_URL =$UPLOAD_URL."contest/scratch_ani/";
//²��
$UPLOAD_IMPRESS_PATH=
$UPLOAD_PATH."contest/impress/";
$UPLOAD_IMPRESS_URL =$UPLOAD_URL."contest/impress/";

$UPLOAD_P=array('0'=>$UPLOAD_NEWS_PATH,'2'=>$UPLOAD_PAINTING_PATH,'3'=>$UPLOAD_ANIMATION_PATH,'4'=>$UPLOAD_IMPRESS_PATH,'7'=>$UPLOAD_SCRATCH_ANI_PATH);
$UPLOAD_U=array('0'=>$UPLOAD_NEWS_URL,'2'=>$UPLOAD_PAINTING_URL,'3'=>$UPLOAD_ANIMATION_URL,'4'=>$UPLOAD_IMPRESS_URL,'7'=>$UPLOAD_SCRATCH_ANI_URL);

//�v�����O
$PHP_CONTEST=array('1'=>"�d��Ƥ���",'2'=>"�q��ø��-�R�A",'3'=>"�q��ø��-�ʵe",'4'=>"�M�D²��",'5'=>"���r����-����",'6'=>"���r����-�^��",'7'=>"Scratch�ʵe");

//�w�]�v�ɻ��� , �����x����100�Ǧ~�v�ɳW�h����
$PHP_MEMO[1]="1.���ץ��T�B���f�W�s���ܰ��ɪ̩Ҷ�J�����רӷ����}�A�i�����b�ӭ����W�e�{���T���ת̡A���D���P���u����v�C\\n2.���׿��~�Ψӷ����}���~�A���D���P���u�����v�C \\n3.���רӷ����}�ݩ�U�C��@�̡A���D���P���u�����v�G \\n(1)�ϥν׾°Q�װϡBYahoo����+���ݵ����}�̡C \\n(2)�����(�ҦpDOC��PDF��PPT�����A�]���f�L�k�T�����צb�ӥ���󪺲ĴX��) \\n(3)�j�M�������Ȧs�����C \\n4.�����ɡA�H�����D�Ʀh�֬����Z�A�����D�ƬۦP�̡A�H�@���D�Ƹ��֪̬��u�ӡC";
$PHP_MEMO[2]=$PHP_MEMO[3]="�Ҧ��@�~���ӥѰ��ɪ̨ϥηƹ���ø�ϵ��B�O���]�ơA�Q��ø�ϳn�餧�u��Υ\��ˤ�ø�s�A���o�ϥβ{���Ϥ�(�t�Ϯw���СBø�ϳn��Ҫ����_�c�Ϯw�B�Q�α��y���μƦ�۾����o���Ϥ�)�A�Z�g���f�o�{�θg���|�ϥβ{���Ϥ��̡A�ӧ@�~�������ɸ��(�w�o���̡A�����W���A��L���ɪ̺����줽�i���Z�A���A�ܧ�)�C\\n1.�R�A���@�~�HGimp�BInkscape ���ۥѳn��Χ@�~�t�Τ��ت�ø�ϳn��ø�s�A�åHJPG��Png�榡�W�Ǧ��v�ɥD���C\\n2.�R�A���@�~�����j�p�H1024x768 pixel�s�@�C\\n3.�ʵe���@�~�Hswf��Gif���ʵe�榡�W�Ǧ��v�ɥD���C\\n4.�@�~���P�N�Х�CC���v(�m�W�Хܢw�D�ӷ~�ʢw�ۦP�覡����)";
$PHP_MEMO[4]="�v�ɤ覡�G\\n1.�@�~�Hopenoffice-Impress�s�@�A�åHodp�ɮ׮榡�W�Ǧ��v�ɥD���C\\n2.�ɮפj�p�����30MB�H���A²���`���Ƥ��W�L30���C\\n3.�Щ�@�~�}�Y�ε������[�J�Х�CC���v�ХܡC\\n4.�ϥγn�魭�v�ɳ��a���Ѥ��n��A���o�ۦ�w�ˡC\\n�����зǡG\\n��Ƶ��c35�H�B���e25�H�B��k�зN5�H�B��m���[5�H�B�����w�Ƴ]�p25�H�B�Х�CC�B��5%�C";
$PHP_MEMO[5]=$PHP_MEMO[6]="1.�v�ɮɶ��G�C���Q�����A�i��⦸�C\\n2.�⦸�v�ɤ��A�H���r�t�װ��̧@���Ӧ��v�ɪ��`���Z�C\\n3.���r���T�v���F90�H�H�W�A�_�h�ॢ���C\\n4.���v�ɮɶ������r�t�׳̧֪̬������A�P���ɡA�H���r���T�v���̬��u�ӡC";

$PHP_MEMO[7]="1.�v�ɤu��G�@�ߨϥΥD���줽�i���uScratch�ϧΤƵ{���]�p�n��v���X�v�ɧ@�~�A���ɨϥΪ����� 2.0���C\\n2.�ϥί����G\\n(1)Scratch�{�����د����C\\n(2)�v�ɳ�촣�Ѥ��Х�CC���v����\\n(3)�Ѱ��ɪ̨ϥηƹ���ø�ϵ��B�O���]�ơA�Q��ø�ϳn�餧�u��Υ\��ˤ�ø�s�C\\n�����зǡG\\n�зN:20%�B��ı�ĪG:15%�Bťı�ĪG:15%�B�D�D���F:50%";


//�w�]�����ӥ� , ø��
$SCORE_PAINT=array('1'=>"�D�D��25%",'2'=>"��Щ�30%",'3'=>"���N�ުk15%",'4'=>"�q���ުk30%");

//�w�]�����ӥ� , ²��
$SCORE_IMPRESS=array('1'=>"��Ƶ��c35�H",'2'=>"���e25�H",'3'=>"��k�зN5�H",'4'=>"��m���[5�H",'5'=>"�����w�Ƴ]�p25�H",'6'=>"�Х�CC�B��5%");

//�w�]�����ӥ� , Scratch�ʵe
$SCORE_SCRATCH_ANI=array('1'=>"�зN:20%",'2'=>"��ı�ĪG:15%",'3'=>"ťı�ĪG:15%",'4'=>"�D�D���F:50%");


//�ܼ��ഫ
$OPEN[0]="����";
$OPEN[1]="�}��";


//���o�޲z�v�]�w��
$MANAGER=checkid($_SERVER['SCRIPT_FILENAME'],1);


//Ū�������ܼ� $ITEM[0],$ITEM[1].....
$M_SETUP=get_module_setup('contest_teacher');

$PHP_PAGE=$M_SETUP['page'];
$PHP_FILE_ATTR=$M_SETUP['upload_file_attr'];	  

//���J���Ҳժ��M�Ψ禡�w
include_once ('include/myfunctions.php');
include_once ('include/itembank.inc.php');
include_once ('include/test.inc.php');
include_once ('include/judge.inc.php');

	
?>
