<?php
//$Id: config.php 8973 2016-09-12 08:14:48Z infodaes $
//�w�]���ޤJ�ɡA���i�����C
require_once "./module-cfg.php";
include_once "../../include/config.php";
include "module-upgrade.php";

//�z�i�H�ۤv�[�J�ޤJ��
 

//�ؼШ���t_id
$type_id=$_REQUEST[type_id]?$_REQUEST[type_id]:1;
//��V������
$linkstr="type_id=$type_id";
 
?>