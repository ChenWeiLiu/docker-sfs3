<?php
                                                                                                                             
// $Id: exam_config.php 5310 2009-01-10 07:57:56Z hami $

/*
**
**	學生作業管制系統2.0版
**	作者：
**	HaMi(cik@mail.wpes.tcc.edu.tw)
**	chilang(tea50@mail.wkes.tcc.edu.tw)
**	程式來源：校園自由軟體交流網( http://sfs.wpes.tcc.edu.tw )
**
**	使用平台 Linux
**	使用資料庫 MySQL
**	本系統需結合學務系統，才可運作。
**
*/
//載入學務系統
include_once "../../include/config.php";
//載入函式庫
include_once "../../include/sfs_case_PLlib.php";

/* 上傳檔案目的目錄 */
$path_str = "school/exam/";
set_upload_path($path_str);
$upload_path = $UPLOAD_PATH.$path_str;


//上載目錄URL
$uplaod_url = $UPLOAD_URL.$path_str; 

//取得模組設
$m_arr = &get_sfs_module_set("exam");
extract($m_arr, EXTR_OVERWRITE);


//不允許上傳副檔名
$limit_file_name = array("php","php3","inc");

//以下勿更動------------------------------------------------

mysql_select_db( "$dbname");

//判別是否被呼叫過
$isload =1;


/**
 *	檢查密碼是否標準
 *
 *	限定使用英文或數字
 *
 *	@param $chk - string - 檢查字串
 *	@param $less - integer - 最少幾個字元
 *	@param $max - integer - 最多幾個字元
 *	@return bolean 
 */
function chk_pass($chk,$less=3,$max=10) {
	if (eregi("^[a-zA-Z0-9]{"."$less,$max"."}$",$chk,$arr) )
		return true;
	else
		return false;
}


// 取得班級 $class_id位元說明： 0-3 ->學年 4->學期 5->年級 6- 班級 
function get_class_name($class_id) {
	global $class_year, $class_name ;
	$class_name=class_base();
	$temp_year = substr($class_id,4,1); //取得年級	
	$temp_class = $temp_year.sprintf("%02d",substr($class_id,5)); //取得班級
	return  $class_name[$temp_class];
}
?>
