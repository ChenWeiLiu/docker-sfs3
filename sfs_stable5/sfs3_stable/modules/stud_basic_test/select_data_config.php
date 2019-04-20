<?php
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "select_data_function.php";
include_once "module-upgrade.php";

/* 上傳檔案暫存目錄 */
$path_str = "temp/student/";
set_upload_path($path_str);
$temp_path = $UPLOAD_PATH.$path_str;

$menu_p = array("basic_test_stu.php"=>"學生基測名冊","basic_test_data.php"=>"學生基測資料","setup.php"=>"特種身分設定","dis_stud.php"=>"免試學生資料管理","distest5.php"=>"102免試入學","score_input.php"=>"定考補登","distest4.php"=>"100薦送","chart.php"=>"免試報表","setup2.php"=>"標記直升生");

//取得縣市鄉鎮陣列
function get_zip_arr() {
	global $CONN;
	$query = "select zip,country,town from stud_addr_zip order by zip";
	$res= $CONN->Execute($query) or trigger_error("語法錯誤!",E_USER_ERROR);
	while(!$res->EOF){
		$zip_arr[$res->fields[0]] = $res->fields[1].$res->fields[2];
		$res->MoveNext();
	}
	return $zip_arr;
}

//分發區代碼
$area2_arr = array("01"=>"01","03"=>"03","05"=>"05","06"=>"06","07"=>"07","08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17");

//101年加分比例陣列
//依序為無設定, 原住民無族語, 原住民有族語, 科技<1學期, 科技<1學年, 科技<2學年, 科技<3學年, 派外<1學期, 派外<1學年, 派外<2學年, 派外<3學年, 蒙藏, 身障
$plus_arr=array(0=>0,1=>10,2=>35,3=>25,4=>20,5=>15,6=>10,7=>25,8=>20,9=>15,'A'=>10,'B'=>25,'C'=>25);

//考區代碼選單
$area_arr = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18");
$area_sel .= "區碼：<select name='area'>\n";
while(list($v,$t)= each ($area_arr)) {
	$v = $v+1;
	$selected = ($_POST['area']==$v)?"selected":"";
	$area_sel .= "<option value=".$v." $selected>".$t."</option>\n";
}             	 
$area_sel .= "</select>\n";
$parent_arr = array("1"=>"監護人","2"=>"父親","3"=>"母親");
$phone_arr = array("1"=>"戶籍電話","2"=>"連絡電話","3"=>"行動電話");
$address_arr = array("1"=>"戶籍住址","2"=>"連絡住址");

//要處理的學生就學狀況
$cal_str="'0','15'";

//判斷中低收入戶身分別
$type61="";
$have61=0;
$query="select * from sfs_text where t_kind='stud_kind' and t_name='中低收入戶'";
$res=$CONN->Execute($query);
if (intval($res->fields['d_id'])>0) {
	$type61=intval($res->fields['d_id']);
	$have61=1;
} else {
	$type61="-1";
}
?>
