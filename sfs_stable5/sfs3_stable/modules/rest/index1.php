<?php	
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//���o�]�w��
include_once "config.php";
//���ҬO�_�n�J
sfs_check(); 
//�s�@��� ( $school_menu_p�}�C�]�w�� module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//Ū���ثe�ާ@���Ѯv���S���޲z�v , �f�t module-cfg.php �̪��]�w
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** �}�l�q�X���� ******************/
//�q�X SFS3 ���D
head();
//�C�X���
echo $tool_bar;


?>

 �o�O���Ҳժ��Ĥ@��{��
 
<?php
//  --�{���ɧ�
foot();
?>