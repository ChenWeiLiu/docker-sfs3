<?php
// $Id: output_xml.php 7270 2013-04-22 02:32:24Z infodaes $

if (!$_POST['sid']){
exit;
}

session_id($_POST['sid']);
session_start();

require "config.php";
require "class.php";

require_once 'Crypt/DiffieHellman.php';
require_once('Crypt/CBC.php');
include 'security.php';
sfs_check();

if ($_POST['getkey']=='true'){

$alice = new Crypt_DiffieHellman($_POST['serverp'], $_POST['serverg']);

$alice_pubKey = $alice->generateKeys()->getPublicKey(Crypt_DiffieHellman::BINARY);

$_SESSION['alicepk']=$alice_pubKey;

$alice_computeKey = $alice->computeSecretKey(base64_decode($_POST['serverpk']),Crypt_DiffieHellman::BINARY)->getSharedSecretKey(Crypt_DiffieHellman::BINARY);

$_SESSION['alicesk']=$alice_computeKey;


echo base64_encode($_SESSION['alicepk']);

}else{

/*
echo base64_encode($_SESSION['alicesk']);
exit;
*/

    $key = base64_encode($_SESSION['alicesk']);

    $aeskey = hash('md5', base64_encode($key));

/*
echo $aeskey;
exit;
*/

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
	//學籍資料
	$smarty->assign("data_arr",$xml_obj->out_arr);
	//性別陣列
	$smarty->assign("sex_arr",array("1"=>"男","2"=>"女"));
	//身份別陣列 (備註暫不產生)
	$smarty->assign("stud_kind_arr",stud_kind());
	//證照類別陣列
	$smarty->assign("id_kind_arr",stud_country_kind());
	//學生班級性質陣列
	$smarty->assign("class_kind_arr",stud_class_kind());
	
	//學生特殊班類別陣列
	$smarty->assign("spe_kind_arr",stud_spe_kind());
	//學生特殊班上課性質陣列
	$smarty->assign("spe_class_id_arr",stud_spe_class_id());
	//學生特殊班班別陣列
	$smarty->assign("spe_class_kind_arr",stud_spe_class_kind());
	//國中小判定 SFS 4.0 必須修正
	$smarty->assign("jhores",$IS_JHORES);
	//入學資格陣列
	$smarty->assign("preschool_status_arr",stud_preschool_status());
	
	//畢修業陣列
	$smarty->assign("grad_kind_arr",grad_kind());

	//存歿陣列
	$smarty->assign("is_live_arr",is_live());
	//與父關係陣列
	$smarty->assign("f_rela_arr",fath_relation());
	//與母關係陣列
	$smarty->assign("m_rela_arr",moth_relation());
	//與監護人關係陣列
	$smarty->assign("g_rela_arr",guardian_relation());
	//學歷陣列
	$smarty->assign("edu_kind_arr",edu_kind());
	//兄弟姐妹陣列
	$smarty->assign("bs_calling_kind_arr",bs_calling_kind());
	
	//生涯輔導考慮因素陣列
	$factor_items=array('self'=>'個人因素','env'=>'環境因素','info'=>'資訊因素');
	foreach($factor_items as $item=>$title){
		$factors[$item]=SFS_TEXT($title);				
	}
	$smarty->assign("factors",$factors);
	
	//抓取各學期應出席日數
	$query="select * from seme_course_date order by seme_year_seme,class_year";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$current_seme_year_seme=$res->fields[seme_year_seme];
		$row_data=$res->FetchRow();
		$seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']]=$row_data['days'];
	}
	$smarty->assign("seme_course_date_arr",$seme_course_date_arr);
	
	//輔導個案資料因內容涉及隱私本系統暫時不交換, 以null值處理
	
//echo "<pre>";	
//print_r($xml_obj->out_arr);
//echo "</pre>";	
//exit;

	$filename=$SCHOOL_BASE['sch_id'].$school_long_name.date('Ymd')."學生XML_3_0交換資料.xml";
	header("Content-disposition: attachment; filename=$filename");
	header("Content-Type:text/xml; charset=utf-8");

	//將smarty輸出的資料先cache住
	ob_start();
	$smarty->display("student_3_0.tpl");
	$xmls=ob_get_contents();
	ob_end_clean();


	//將空值以null取代
	$xmls=str_replace("><",">null<",$xmls);
	$xmls=iconv("Big5","UTF-8",$xmls);
	//轉成Unicode後輸出檔案
	//$xmlsutf8=iconv("Big5","UTF-8",$xmls);
	//$xmls='This is a test';	
	//echo Security::encrypt($xmls, $aeskey);
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    $plaintext_utf8 = base64_encode($xmls);
    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $aeskey, $plaintext_utf8, MCRYPT_MODE_CBC, $iv);
    $ciphertext = $iv . $ciphertext;
    echo base64_encode($ciphertext);
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
}
?>
