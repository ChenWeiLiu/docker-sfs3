<?php
require_once "./module-cfg.php";

// �ޤJ SFS �]�w�ɡA���|���z���J SFS ���֤ߨ禡�w
include_once "../../include/config.php";
include_once "./module-upgrade.php";

// �ޤJ�L�±Ӯv���禡�w (�o����\���лݭn�Ψ�)
include_once "../../include/sfs_case_PLlib.php" ;




// �C�骺����W��
$WEEK_DATE = array('�@','�G','�T','�|','��','��','��');

// ��J��e
$INPUT_SIZE = 22;

// ��r�s��Ϧ�C���j�p
$TEXTAREA_COLS_SIZE = 20;

$TEXTAREA_ROWS_SIZE = 5;

//���o�ҲհѼƳ]�w
$m_arr = &get_sfs_module_set("lunch");
extract($m_arr, EXTR_OVERWRITE);

$DESIGN = explode(",",$DESIGN_NAME);
$font_size=$font_size?$font_size:'9pt';
$column_bgcolor_w='#FFDDDD';
$column_bgcolor_m='#DDDDFF';

// ���ЦC�X�Ѽ�
$WEEK_DAYS = $WEEK_DAYS?$WEEK_DAYS:5;

?>