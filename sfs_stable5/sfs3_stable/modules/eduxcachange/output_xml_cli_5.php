<?php

// $Id: output_xml.php 7713 2013-10-25 02:29:35Z smallduh $

require "config.php";
require "class.php";
require "chinese.lib.php";

$sel_year = curr_year(); //目前學年
$sel_seme = curr_seme(); //目前學期
$year_seme = sprintf("%03d", $sel_year) . $sel_seme; //現在的學年學期ex.1041,1042

//中心端支援
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}
$temp_dir=$UPLOAD_PATH."eduxcachange/";

//取得學校代碼
$school_base = get_school_base();
$smarty->assign("sch_id", $school_base['sch_id']);

//取出所有記錄
//(只列一個年級測速度)
$stud_query = "select * from stud_base where stud_study_cond in (0,15) and substring(curr_class_num,1,1)=5 order by curr_class_num";
//$stud_query="select * from stud_base where stud_study_cond in (0,15) order by curr_class_num";
$stud_res = $CONN->Execute($stud_query) or die($stud_query);
$stud_arr = array();
while (!$stud_res->EOF) {
      array_push($stud_arr, $stud_res->fields[student_sn]);
      $stud_res->MoveNext(); 
}



//如果確定輸出XML檔案
//個資記錄	
//$sn=implode(",",array_keys($stud_arr));
//$test=pipa_log("XML匯出作業\r\n學生流水號：$sn\r\n");
$xml_obj = new sfsxmlfile();
$xml_obj->student_sn = $stud_arr;
$xml_obj->output();

//igogo 先將資料utf8,物件引自chinese.lib.php
$obj = new Sfs3Data;

$ary = $obj->array_big5_to_utf8($xml_obj->out_arr);
//學籍資料
$xml_obj->out_arr = $ary;
$smarty->assign("data_arr", $xml_obj->out_arr);

//性別陣列
$sex_arr = $obj->array_big5_to_utf8(array("1" => "男", "2" => "女"));
//$smarty->assign("sex_arr",array("1"=>"男","2"=>"女"));
$smarty->assign("sex_arr", $sex_arr);

//身份別陣列 (備註暫不產生)
$stud_kind_arr = $obj->array_big5_to_utf8(stud_kind());
$smarty->assign("stud_kind_arr", $stud_kind_arr);

//證照類別陣列
$stud_country_kind = $obj->array_big5_to_utf8(stud_country_kind());
$smarty->assign("id_kind_arr", $stud_country_kind);

//學生班級性質陣列
$stud_class_kind = $obj->array_big5_to_utf8(stud_class_kind());
$smarty->assign("class_kind_arr", $stud_class_kind);

//學生特殊班類別陣列
$stud_spe_kind = $obj->array_big5_to_utf8(stud_spe_kind());
$smarty->assign("spe_kind_arr", $stud_spe_kind);

//學生特殊班上課性質陣列
$stud_spe_class_id = $obj->array_big5_to_utf8(stud_spe_class_id());
$smarty->assign("spe_class_id_arr", $stud_spe_class_id);


//學生特殊班班別陣列
$stud_spe_class_kind = $obj->array_big5_to_utf8(stud_spe_class_kind());
$smarty->assign("spe_class_kind_arr", $stud_spe_class_kind);


//國中小判定 SFS 4.0 必須修正
$smarty->assign("jhores", $IS_JHORES);

//入學資格陣列
$stud_preschool_status = $obj->array_big5_to_utf8(stud_preschool_status());
$smarty->assign("preschool_status_arr", $stud_preschool_status);

//畢修業陣列
$grad_kind = $obj->array_big5_to_utf8(grad_kind());
$smarty->assign("grad_kind_arr", $grad_kind);

//存歿陣列
$is_live = $obj->array_big5_to_utf8(is_live());
$smarty->assign("is_live_arr", $is_live);

//與父關係陣列
$fath_relation = $obj->array_big5_to_utf8(fath_relation());
$smarty->assign("f_rela_arr", $fath_relation);

//與母關係陣列
$moth_relation = $obj->array_big5_to_utf8(moth_relation());
$smarty->assign("m_rela_arr", $moth_relation);

//與監護人關係陣列
$guardian_relation = $obj->array_big5_to_utf8(guardian_relation());
$smarty->assign("g_rela_arr", $guardian_relation);

//學歷陣列
$edu_kind = $obj->array_big5_to_utf8(edu_kind());
$smarty->assign("edu_kind_arr", $edu_kind);

//兄弟姐妹陣列
$bs_calling_kind = $obj->array_big5_to_utf8(bs_calling_kind());
$smarty->assign("bs_calling_kind_arr", $bs_calling_kind);

//生涯輔導考慮因素陣列
$factor_items = array('self' => '個人因素', 'env' => '環境因素', 'info' => '資訊因素');
foreach ($factor_items as $item => $title) {
    $factors[$item] = SFS_TEXT($title);
}

$factors = $obj->array_big5_to_utf8($factors);
$smarty->assign("factors", $factors);

//抓取各學期應出席日數
$query = "select * from seme_course_date order by seme_year_seme,class_year";
$res = $CONN->Execute($query);
while (!$res->EOF) {
    $current_seme_year_seme = $res->fields[seme_year_seme];
    $row_data = $res->FetchRow();
    $seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']] = $row_data['days'];
}
//print_r($seme_course_date_arr);
$smarty->assign("seme_course_date_arr", $seme_course_date_arr);

//輔導個案資料因內容涉及隱私本系統暫時不交換, 以null值處理
//echo "<pre>";	
//print_r($xml_obj->out_arr);
//echo "</pre>";	
//exit;
//$filename=$SCHOOL_BASE['sch_id'].$school_long_name.date('Ymd')."_XML_3_0交換資料.xml";
$filename_xml = $SCHOOL_BASE['sch_id'] . "_XML_5.xml";
//將smarty輸出的資料先cache住
ob_start();
$smarty->display("eduxcachange.tpl");
$xmls = ob_get_contents();
ob_end_clean();

//將空值以null取代
//$xmls = str_replace("><", ">null<", $xmls);

//轉成Unicode後輸出檔案
//echo iconv("Big5","UTF-8",$xmls);
$xml_file = file_put_contents($temp_dir . $filename_xml, $xmls);
unset($xmls);
exit;
?>
