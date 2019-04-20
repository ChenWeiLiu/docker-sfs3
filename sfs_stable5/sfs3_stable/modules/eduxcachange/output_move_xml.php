<?php
// $Id: output_xml.php 7713 2013-10-25 02:29:35Z smallduh $

require "config.php";
require "class.php";
require "chinese.lib.php";

sfs_check();

$move_kind_arr=array("0"=>" -- 請選擇 -- ","8"=>"調校","5"=>"畢業");

$all_reward=$_POST['all_reward'];


//若有選擇學年學期，進行分割取得學年及學期
if(!empty($_POST[year_seme])){
	$sel_year=intval(substr($_POST[year_seme],0,-1));
	$sel_seme=substr($_POST[year_seme],-1,1);
	$year_seme=$_POST[year_seme];
} else {
	$sel_year=curr_year(); //目前學年
	$sel_seme=curr_seme(); //目前學期
	$year_seme=sprintf("%03d",$sel_year).$sel_seme;
}
//取得學校代碼
$school_base=get_school_base();
$smarty->assign("sch_id",$school_base['sch_id']);

//異動類別選單
$sel1=new drop_select();
$sel1->s_name="move_kind";
$sel1->id=$_POST[move_kind];
$sel1->arr=$move_kind_arr;
$sel1->has_empty=false;
$sel1->is_submit=true;
$smarty->assign("move_kind_sel",$sel1->get_select());

//學期選單
$sel1=new drop_select();
$sel1->s_name="year_seme";
$sel1->id=$year_seme;
$sel1->arr=get_class_seme();
$sel1->has_empty=false;
$sel1->is_submit=true;
$smarty->assign("year_seme_sel",$sel1->get_select());

if ($_POST[move_kind]=="8") {
		$smarty->assign("form_kind","1");
} else {
		$smarty->assign("form_kind","2");
}
$query="select a.*,b.stud_name from stud_move a ,stud_base b where a.student_sn=b.student_sn and a.move_year_seme='".intval($year_seme)."' and a.move_kind='$_POST[move_kind]' order by a.move_date desc,a.stud_id desc";
//取出所有記錄
$res=$CONN->Execute($query) or die($query);
$smarty->assign("stud_move",$res->GetRows());

if ($_POST[out_arr]) {
	$xml_obj=new sfsxmlfile();
	$xml_obj->student_sn=$_POST[choice];
	$xml_obj->output();
	$smarty->assign("data_arr",$xml_obj->out_arr);
	$smarty->assign("sex_arr",array("1"=>"男","2"=>"女"));
	echo "<pre>";
	print_r($xml_obj->out_arr);
	echo "</pre>";
	exit;
}

//如果確定輸出XML檔案
if ($_POST[output_xml]) {
	//個資記錄	
	$sn=implode(",",array_keys($_POST[choice]));
	$test=pipa_log("XML匯出作業\r\n學生流水號：$sn\r\n");
	
	$xml_obj=new sfsxmlfile();
	$xml_obj->student_sn=$_POST[choice];
	$xml_obj->output();

	//igogo 先將資料utf8,物件引自chinese.lib.php
	$obj = new Sfs3Data;

	//學籍資料
	$xml_obj->out_arr = $obj->array_big5_to_utf8($xml_obj->out_arr);
	$smarty->assign("data_arr",$xml_obj->out_arr);

	//性別陣列
	$sex_arr = $obj->array_big5_to_utf8(array("1"=>"男","2"=>"女"));
	//$smarty->assign("sex_arr",array("1"=>"男","2"=>"女"));
	$smarty->assign("sex_arr",$sex_arr);

	//身份別陣列 (備註暫不產生)
	$stud_kind_arr = $obj->array_big5_to_utf8(stud_kind());
	$smarty->assign("stud_kind_arr",$stud_kind_arr);

	//證照類別陣列
	$stud_country_kind =  $obj->array_big5_to_utf8(stud_country_kind());
	$smarty->assign("id_kind_arr",$stud_country_kind);

	//學生班級性質陣列
	$stud_class_kind =  $obj->array_big5_to_utf8(stud_class_kind());
	$smarty->assign("class_kind_arr",$stud_class_kind);
	
	//學生特殊班類別陣列
	$stud_spe_kind = $obj->array_big5_to_utf8(stud_spe_kind());
	$smarty->assign("spe_kind_arr",$stud_spe_kind);

	//學生特殊班上課性質陣列
	$stud_spe_class_id = $obj->array_big5_to_utf8(stud_spe_class_id());
	$smarty->assign("spe_class_id_arr",$stud_spe_class_id);


	//學生特殊班班別陣列
	$stud_spe_class_kind = $obj->array_big5_to_utf8(stud_spe_class_kind());
	$smarty->assign("spe_class_kind_arr",$stud_spe_class_kind);


	//國中小判定 SFS 4.0 必須修正
	$smarty->assign("jhores",$IS_JHORES);

	//入學資格陣列
	$stud_preschool_status = $obj->array_big5_to_utf8(stud_preschool_status());
	$smarty->assign("preschool_status_arr",$stud_preschool_status);
	
	//畢修業陣列
	$grad_kind = $obj->array_big5_to_utf8(grad_kind());
	$smarty->assign("grad_kind_arr",$grad_kind);

	//存歿陣列
	$is_live = $obj->array_big5_to_utf8(is_live());
	$smarty->assign("is_live_arr",$is_live);

	//與父關係陣列
	$fath_relation = $obj->array_big5_to_utf8(fath_relation());
	$smarty->assign("f_rela_arr",$fath_relation);

	//與母關係陣列
	$moth_relation = $obj->array_big5_to_utf8(moth_relation());
	$smarty->assign("m_rela_arr",$moth_relation);

	//與監護人關係陣列
	$guardian_relation = $obj->array_big5_to_utf8(guardian_relation());
	$smarty->assign("g_rela_arr",$guardian_relation);

	//學歷陣列
	$edu_kind = $obj->array_big5_to_utf8(edu_kind());
	$smarty->assign("edu_kind_arr",$edu_kind);

	//兄弟姐妹陣列
	$bs_calling_kind = $obj->array_big5_to_utf8(bs_calling_kind());
	$smarty->assign("bs_calling_kind_arr",$bs_calling_kind);
	
	//生涯輔導考慮因素陣列
	$factor_items=array('self'=>'個人因素','env'=>'環境因素','info'=>'資訊因素');
	foreach($factor_items as $item=>$title){
		$factors[$item]=SFS_TEXT($title);				
	}

	$factors = $obj->array_big5_to_utf8($factors);
	$smarty->assign("factors",$factors);
	
	//抓取各學期應出席日數
	$query="select * from seme_course_date order by seme_year_seme,class_year";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$current_seme_year_seme=$res->fields[seme_year_seme];
		$row_data=$res->FetchRow();
		$seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']]=$row_data['days'];
	}
	//print_r($seme_course_date_arr);
	$smarty->assign("seme_course_date_arr",$seme_course_date_arr);
	
	//輔導個案資料因內容涉及隱私本系統暫時不交換, 以null值處理
	
//echo "<pre>";	
//print_r($xml_obj->out_arr);
//echo "</pre>";	
//exit;

	//$filename=$SCHOOL_BASE['sch_id'].$school_long_name.date('Ymd')."_XML_3_0交換資料.xml";
	$filename_zip=$SCHOOL_BASE['sch_id']."_XML_".date('Ymd').".zip";
	$filename_xml=$SCHOOL_BASE['sch_id']."_XML_".date('Ymd').".xml";
	$temp_dir=$UPLOAD_PATH.$path_str."eduxcachange/";
	if (!is_dir($temp_dir)) mkdir($temp_dir,0700);
	//將smarty輸出的資料先cache住
	ob_start();
	$smarty->display("eduxcachange.tpl");
	$xmls=ob_get_contents();
	ob_end_clean();
	
	//將空值以null取代
	$xmls=str_replace("><",">null<",$xmls);

	//轉成Unicode後輸出檔案
	//echo iconv("Big5","UTF-8",$xmls);
	$xml_file = file_put_contents($temp_dir.$filename_xml, $xmls);
    unset($xmls);
	
	//產出zip檔
	if($xml_file === FALSE){
      die("產生檔案失敗");
    }else{
	  $zip = new ZipArchive;
      $xml_zip = $zip->open($temp_dir.$filename_zip,ZipArchive::CREATE);
      if($xml_zip === TRUE) {
        $zip->addFile($temp_dir.$filename_xml, $filename_xml);
        $zip->close();
        unlink($temp_dir.$filename_xml);
      }else{
        unlink($temp_dir.$filename_xml);
        die("產生壓縮檔失敗");
      }
    }
    echo basename($filename_zip);
	header("Content-disposition: attachment; filename=$filename_zip");
	header('Content-Type: application/zip');
	header("Content-Type:text/xml; charset=utf-8");
  
  //因應 IE 6,7,8 在 SSL 模式下無法下載，取消 no-cache 改為以下
	header("Cache-Control: max-age=0");
	header("Pragma: public");
	header("Expires: 0");
	header('Content-Length: ' . filesize($temp_dir.$filename_zip));
    readfile($temp_dir.$filename_zip);
	exit;
}

//國中加入生涯輔導輸出選項
$checked=$IS_JHOES?'checked':'';
$career_checkbox="<input type='checkbox' name='career' value=1 $checked>輸出國中生涯輔導手冊資料(需有安裝相關模組)";
$smarty->assign("career_checkbox",$career_checkbox);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","XML交換作業");
$smarty->assign("SFS_MENU",$toxml_menu);
$smarty->display("toxml_output_xml.tpl");
?>
