<?php

// $Id: module-cfg.php 6997 2012-11-13 02:05:00Z infodaes $

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

//���Ҳն��Ϥ��޲z�v
$MODULE_MAN = 1 ;

//�޲z�v����
$MODULE_MAN_DESCRIPTION = "�㦳�޲z�v�H��,�����y�w������\��,�@��ϥΪ̶ȥi�s�W���y";


//---------------------------------------------------
//
// 2.�o�̩w�q�G�Ҳդ���W�١A�к�²�R�W (�� "�Ҳ��v���]�w" �{���ϥ�)
//
// ���|��ܵ��ϥΪ�
//-----------------------------------------------


$MODULE_PRO_KIND_NAME = "�s�@���Z��";


//---------------------------------------------------
//
// 3. �o�̩w�q�G�Ҳժ���������T (�� "�۰ʧ�s�{��" ����)
//    �o�Ϫ� "�ܼƦW��" �Фŧ���!!!
//
//---------------------------------------------------

// �Ҳճ̫��s����
$MODULE_UPDATE_VER="1.0";

// �Ҳճ̫��s���
$MODULE_UPDATE="2003-01-01";

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

//���o score_chart �Ҳճ]�w
$m_arr = get_sfs_module_set("score_chart");
extract($m_arr, EXTR_OVERWRITE);
//���o�Ҳճ]�w
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

$class_num=get_teach_class();
$chk_menu_arr=($class_num < (curr_year()-93+$IS_JHORES)*100)?array("chk.php"=>"��g��`�ˮ֪�","chk_print_all.php"=>"�C�L�Ԧ��ˮ֪�"):array();

//���X�]�w���������Z�����O
if ($chart_kind=="") $chart_kind=3;

//�̡u�O�_�����ǲ߻���r�y�z�v�ܼƿ�Τ��P���Z�����O
$chart_kind+=$none_text*3;

//�̡u�O�_�����ˮ֪��v�ܼƧP�_�O�_�X�{�ˮ֪�
if ($disable_chk) $chk_menu_arr=array();

//���Z���Z��{��
$class_chart=($chk_menu_arr)?array("chart.php?chart_kind=".$chart_kind=>"�U�����Z���Z��"):array("chart_e.php?act=dlar_all"=>"�U�����Z���Z��");

//�ؿ����{��
if ($IS_JHORES=="0") {
$school_menu_p = array_merge(
$chk_menu_arr,
array("chart_e.php"=>"��g���Z���L���"),
$class_chart,
array(
"absent.php"=>"��g�Դk�O��",
"write_memo.php"=>"�ǲߴy�z��r�s��")
,
array(
"../score_input/"=>"���Z�޲z ^",
"chc_check.php"=>"���Z��J�ˬd",
"chk_account.php"=>"�ˮ֪���g����"
));
} else {
$school_menu_p = array_merge(
$chk_menu_arr,
array(
"chart_j.php"=>"�[�ݦ��Z��",
"absent.php"=>"�[�ݶԴk�O��",
"reward.php"=>"�[�ݼ��g�O��",
//"nor.php"=>"��g��`���Z",
//"score_nor.php"=>"�[�ݤ�`�`���Z"
)
,
array(
"../score_input/"=>"���Z�޲z ^",
"chc_check.php"=>"���Z��J�ˬd",
"chk_account.php"=>"�ˮ֪���g����"
));
}

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

$IS_MODULE_ARR = array("y"=>"�O","n"=>"�_");
$SFS_MODULE_SETUP[0] =array('var'=>"is_modify", 'msg'=>"���\�ϥΪ̽s����y���w", 'value'=>$IS_MODULE_ARR);

$LINE_WIDTH_ARR = array("0.01cm"=>"0.01cm","0.015cm"=>"0.015cm","0.02cm"=>"0.02cm","0.025cm"=>"0.025cm");
$SFS_MODULE_SETUP[1] =array('var'=>"line_width", 'msg'=>"���Z���u�e��", 'value'=>$LINE_WIDTH_ARR);

$LINE_COLOR_ARR = array("#000000"=>"�¦�","#FF0000"=>"����","#00008B"=>"�Ŧ�","#228B22"=>"���");
$SFS_MODULE_SETUP[2] =array('var'=>"line_color", 'msg'=>"���Z���u�C��", 'value'=>$LINE_COLOR_ARR);

$IMG_ARR= array("1.27cm"=>"1.27cm","1.5cm"=>"1.5cm","1.8cm"=>"1.8cm","2cm"=>"2cm","2.5cm"=>"2.5cm","3cm"=>"3cm","3.5cm"=>"3.5cm","4cm"=>"4cm");
$SFS_MODULE_SETUP[3] =array('var'=>"draw_img_width", 'msg'=>"ñ���e��", 'value'=>$IMG_ARR);
$SFS_MODULE_SETUP[4] =array('var'=>"draw_img_height", 'msg'=>"ñ������", 'value'=>$IMG_ARR);
$SFS_MODULE_SETUP[5] =array('var'=>"disable_chk", 'msg'=>"�����ˮ֪��\��", 'value'=>array("0"=>"�_","1"=>"�O"));
$SFS_MODULE_SETUP[6] =array('var'=>"chart_kind", 'msg'=>"���������Z�����O", 'value'=>array("2"=>"��즨�Z��","3"=>"���+²�ˮ�","4"=>"���+�ˮ�"));

$SFS_MODULE_SETUP[7] =array('var'=>"is_summary_input", 'msg'=>"���\�ϥΪ̿�J��Ǵ����ʮu�έp�ƾ�", 'value'=>$IS_MODULE_ARR);

// �t�οﶵ
$SFS_TEXT_SETUP[0] = array(
"g_id"=>3,
"var"=>"��`�欰���{",
"s_arr"=>array(1=>"���{�u��",2=>"���{�}�n",3=>"���{�|�i",4=>"�ݦA�[�o",5=>"���ݧ�i")
);
$SFS_TEXT_SETUP[1] = array(
"g_id"=>3,
"var"=>"���鬡�ʪ��{",
"s_arr"=>array(1=>"���{�u��",2=>"���{�}�n",3=>"���{�|�i",4=>"�ݦA�[�o",5=>"���ݧ�i")
);
$SFS_TEXT_SETUP[2] = array(
"g_id"=>3,
"var"=>"���@�A�Ȫ��{",
"s_arr"=>array(1=>"���{�u��",2=>"���{�}�n",3=>"���{�|�i",4=>"�ݦA�[�o",5=>"���ݧ�i")
);

$SFS_TEXT_SETUP[3] = array(
"g_id"=>3,
"var"=>"�ե~�S�����{",
"s_arr"=>array(1=>"���{�u��",2=>"���{�}�n",3=>"���{�|�i",4=>"�ݦA�[�o",5=>"���ݧ�i")
);
$SFS_TEXT_SETUP[4] = array(
"g_id"=>3,
"var"=>"�V�O�{��",
"s_arr"=>array(1=>"���{�u��",2=>"���{�}�n",3=>"���{�|�i",4=>"�ݦA�[�o",5=>"���ݧ�i")
);


?>