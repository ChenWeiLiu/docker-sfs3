<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//系統設定檔
	include_once "./module-cfg.php";
	include_once "../../include/config.php";


	//模組更新程式
	require_once "./module-upgrade.php";
	  

//載入本模組的專用函式庫
include_once ('my_functions.php');

//取得學校代碼
$sql="select sch_id from school_base limit 1";
$res=$CONN->Execute($sql);
$sch_id=$res->fields['sch_id'];


$level_array=array(1=>'國際',2=>'全國、臺灣區',3=>'區域性（跨縣市）',4=>'省、直轄市、縣',5=>'縣市區（鄉鎮）',6=>'校內');

$squad_array=array(1=>'個人賽',2=>'團體賽');

//取得模組變數, 並將陣列的 key 作為變數的名稱
//已設定 $rank_select 獲獎項目 
$m_arr = &get_module_setup("career_race");
extract($m_arr,EXTR_OVERWRITE);

if ($rank_select=='') $rank_select="第一名,冠軍,金獎,特優,白金獎,第二名,亞軍,銀獎,優等,第三名,季軍,銅獎,甲等,第四名,殿軍,佳作,第五名,入選,第六名,第七名,第八名,特別獎,最佳鄉土教材獎,最佳團隊合作獎,最佳創意獎"; 
if ($nature_select=='') $nature_select='體育類,科學類,語文類,音樂類,美術類,舞蹈類,技藝類,綜合類';


//非屏東地區測試時，把以下註解取消
//$sch_id="130001";

//屏東區專用
if (substr($sch_id,0,2)=='13') {
 $level_array=array(1=>'國際性',2=>'全國性',4=>'全縣性');
 $nature_select='體育類,科學類,語文類,音樂類,美術類,舞蹈類,技藝教育類,綜合類,其他類';
 $rank_select="第一名,冠軍,金獎,特優,白金獎,第二名,亞軍,銀獎,優等,第三名,季軍,銅獎,甲等,第四名,殿軍,佳作,第五名,入選,第六名,第七名,第八名,特別獎,最佳鄉土教材獎,最佳團隊合作獎,最佳創意獎"; 
 $school_menu_p['cr_input.php']="登錄/修改個別競賽記錄(屏東版)";
}

	
?>

