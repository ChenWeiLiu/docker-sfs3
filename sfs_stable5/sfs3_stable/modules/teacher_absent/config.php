<?php
//$Id: config.php 8104 2014-09-01 05:56:02Z hami $
//�w�]���ޤJ�ɡA���i�����C
require_once "./module-cfg.php";
include_once "../../include/config.php";
include "../../include/sfs_case_PLlib.php";
include "../../include/sfs_case_dataarray.php";
require_once "./module-upgrade.php";

//�z�i�H�ۤv�[�J�ޤJ��
$status_kind=array("0"=>"�ݽT�{","1"=>"");
$check_arr=array("0"=>"�ݽT�{","1"=>"�w�ֳ�");
$course_kind=array("0"=>"�L�Ұ�","1"=>"�ۦ�ս�","2"=>"���O�ƥN","3"=>"�ЫO���N�z","4"=>"�۶O��N");
$c_course_kind=array("1"=>"�ۦ�ս�","2"=>"���O�ƥN","3"=>"�۶O��N");
$d_kind_arr=array("1"=>"�о����I","2"=>"�ɮv�ɶ�","3"=>"����N��");
$times_kind_arr=array("1"=>"�`","2"=>"��","3"=>"��");
$month_arr=array("1"=>"�@��","2"=>"�G��","3"=>"�T��","4"=>"�|��","5"=>"����","6"=>"����","7"=>"�C��","8"=>"�K��","9"=>"�E��","10"=>"�Q��","11"=>"�Q�@��","12"=>"�Q�G��");
//$abs_kind_arr=array("11"=>"�ư�","12"=>"�a�x���U��","21"=>"�f��","22"=>"�Ͳz��","31"=>"�����f��","41"=>"�B��","42"=>"���e��","43"=>"���Y��","44"=>"�y����","45"=>"������","46"=>"�ల","51"=>"���t��","52"=>"���t","61"=>"��","62"=>"�ɥ�","71"=>"��L");
$post_kind=post_kind();  //¾��
$check1="���D��";
$check2="�оǲժ�";
$check3="�ժ�";
$check4="�H�ƥD��";
$check5="�|�p�D��";

require_once "./my_fun.php";

if ($_REQUEST[year_seme]) {
	$ys=explode("_",$_REQUEST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	if(empty($sel_year))	$sel_year = curr_year(); //�ثe�Ǧ~
	
	if(empty($sel_seme))$sel_seme = curr_seme(); //�ثe�Ǵ�
	
}

?>