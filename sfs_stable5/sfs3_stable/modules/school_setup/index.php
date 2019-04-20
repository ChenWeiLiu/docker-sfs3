<?php

// $Id: index.php 6172 2010-09-18 08:49:51Z brucelyc $

// 載入設定檔
include "school_base_config.php";

// 認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

// 檢查 php.ini 是否打開 file_uploads ?
check_phpini_upload();

$sch_attr_p = array("公立","私立");
$sch_mark_p = array("正常","廢校","改名","停止招生");
$sch_class_p = array("一般地區","偏遠地區","特偏地區");
$sch_montain_p = array("否","是");


// 圖檔目錄
$file_dir = $UPLOAD_PATH."/school";

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//執行動作判斷
if($act=="儲存學校資料"){
	save_school_setup($school);
	header("location: {$_SERVER['PHP_SELF']}");
}else{
	$main=main_sett_form($sel_year,$sel_seme);
}


//秀出網頁
head("學校基本資料設定");
echo $main;
foot();


function main_sett_form(){
	global $sch_attr_p,$sch_montain_p,$sch_mark_p,$sch_class_p,$UPLOAD_URL,$school_menu_p;

	//取得學校資料"
	$school=get_school_setup();

	//公私立設定
	$sch_attr_p_option="";
	for($i=0;$i<sizeof($sch_attr_p);$i++){
		$selected=($school["sch_attr_id"] == $sch_attr_p[$i])?"selected":"";
		$sch_attr_p_option.="<option value='$sch_attr_p[$i]' $selected>$sch_attr_p[$i]";
	}
	
	//縣市設定
	$sch_sheng_p = birth_state();
	$sch_sheng_option="";
	while(list($key,$value)=each($sch_sheng_p)){
		$selected=($school["sch_sheng"] == $value)?"selected":"";
		$sch_sheng_option.="<option value='$value' $selected>$value";
	}

	//山地識別設定
	$sch_montain_option="";
	for($i=0;$i<sizeof($sch_montain_p);$i++){
		$selected=($school["sch_montain"] == $sch_montain_p[$i])?"selected":"";
		$sch_montain_option.="<option value='$sch_montain_p[$i]' $selected>$sch_montain_p[$i]";
	}

	//註記設定
	$sch_mark_p_option="";
	for($i=0;$i<sizeof($sch_mark_p);$i++){
		$selected=($school["sch_mark"] == $sch_mark_p[$i])?"selected":"";
		$sch_mark_p_option.="<option value='$sch_mark_p[$i]' $selected>$sch_mark_p[$i]";
	}
	
	//級別設定
	$sch_class_p_option="";
	for($i=0;$i<sizeof($sch_class_p);$i++){
		$selected=($school["sch_class"] == $sch_class_p[$i])?"selected":"";
		$sch_class_p_option.="<option value='$sch_class_p[$i]' $selected>$sch_class_p[$i]";
	}
	
	//相關功能表
	$tool_bar=make_menu($school_menu_p);

	//取得 upload 的主檔名部份
	$fileurl=$UPLOAD_URL;

	$main="
	<script language='JavaScript'>
		<!--
		var writeWin = null;

		function writeLeft(pic) {
		writeWin = window.open('','aWin','scrollbars,resizable,top=0,left=0,height=480,width=640');

		var ePen = \"<html><head><title>圖檔展示</title></head> \";
		ePen +=  \"<body text='#666666' bgcolor='#ffffff'> \";
		ePen +=  '<center><img src=\"".$UPLOAD_URL."school/'+pic+'\"></center></body></html>';

		var wd = writeWin.document;

		wd.open();
		wd.write(ePen);
		wd.close();
		}

		function blowOut() {
		if (writeWin != null && writeWin.open) writeWin.close();
		}
		window.onfocus=blowOut;
		// -->
	</script>

	$tool_bar
	<table cellspacing='1' cellpadding='3' class='main_body'>
	<form method='post' action='{$_SERVER['PHP_SELF']}' encType='multipart/form-data' >
	<tr>
		<td class='title_sbody2'  colspan='4' bgcolor=#cccccc><input type='submit' name='act' value='儲存學校資料'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>學校代碼（教育部）</td>
		<td><input type='text' size='6' maxlength='6' name='school[sch_id]' value='$school[sch_id]'>
		<a href='http://sfs.wpes.tcc.edu.tw/school/qid.php' target=new>查詢</a></td>
		<td class='title_sbody1'>屬性</td>
		<td>
		<select name='school[sch_attr_id]'>
		<option value=''>
		$sch_attr_p_option
		</select>
		</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>中文名稱（全銜）</td>
		<td colspan='3'><input type='text' size='40' maxlength='40' name='school[sch_cname]' value='$school[sch_cname]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>中文名稱（簡稱）</td>
		<td colspan='3'><input type='text' size='40' maxlength='40' name='school[sch_cname_s]' value='$school[sch_cname_s]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>中文名稱（短稱）</td>
		<td colspan='3'><input type='text' size='40' maxlength='40' name='school[sch_cname_ss]' value='$school[sch_cname_ss]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>英文名稱</td>
		<td colspan='3'><input type='text' size='40' maxlength='60' name='school[sch_ename]' value='$school[sch_ename]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>縣市別</td>	
		<td>
		<select name='school[sch_sheng]'>
		<option value=''>
		$sch_sheng_option
		</select>	
		</td>
		<td  class='title_sbody1' >設校日期（西元）</td>
		<td><input type='text' size='10' maxlength='10' name='school[sch_cdate]' value='$school[sch_cdate]'>（例：1918-7-1）</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>註記</td>
		<td>
		<select name='school[sch_mark]'>
		<option value=''>
		$sch_mark_p_option
		</select>
		</td>	
		<td  class='title_sbody1'>級別</td>
		<td>
		<select name='school[sch_class]'>
		<option value=''>
		$sch_class_p_option
		</select>
		</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>山地識別</td>
		<td>
		<select name='school[sch_montain]'>
		<option value=''>
		$sch_montain_option
		</select>	
		</td>
		<td  class='title_sbody1'>校地總面積</td>
		<td><input type='text' size='10' maxlength='10' name='school[sch_area_tol]' value='$school[sch_area_tol]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>校地總延面積</td>
		<td><input type='text' size='10' maxlength='10' name='school[sch_area_ext]' value='$school[sch_area_ext]'></td>
		<td  class='title_sbody1'>建坪面積</td>
		<td><input type='text' size='10' maxlength='10' name='school[sch_area_pin]' value='$school[sch_area_pin]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>資本門支出</td>
		<td><input type='text' size='10' maxlength='10' name='school[sch_money]' value='$school[sch_money]'> 元</td>
		<td  class='title_sbody1'>經常門支出</td>
		<td><input type='text' size='10' maxlength='10' name='school[sch_money_o]' value='$school[sch_money_o]'> 元</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>鄉鎮市區別</td>
		<td><input type='text' size='10' maxlength='10' name='school[sch_local_name]' value='$school[sch_local_name]'></td>
		<td  class='title_sbody1'>郵遞區號</td>
		<td><input type='text' size='5' maxlength='5' name='school[sch_post_num]' value='$school[sch_post_num]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>學校地址</td>
		<td colspan='3'><input type='text' size='60' maxlength='60' name='school[sch_addr]' value='$school[sch_addr]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>學校電話</td>
		<td><input type='text' size='20' maxlength='20' name='school[sch_phone]' value='$school[sch_phone]'></td>
		<td  class='title_sbody1'>學校傳真</td>
		<td><input type='text' size='20' maxlength='20' name='school[sch_fax]' value='$school[sch_fax]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>學校行政區</td>
		<td><input type='text' size='20' maxlength='20' name='school[sch_area]' value='$school[sch_area]'></td>
		<td  class='title_sbody1'>學校類型</td>
		<td><input type='text' size='6' maxlength='6' name='school[sch_kind]' value='$school[sch_kind]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>學校網址</td>
		<td>http:// <input type='text' size='20' maxlength='50' name='school[sch_url]' value='$school[sch_url]'></td>
		<td  class='title_sbody1'>電子郵件</td>
		<td><input type='text' size='30' maxlength='30' name='school[sch_email]' value='$school[sch_email]'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>學校平面圖</td>
		<td colspan='3'><a href=\"javascript:writeLeft('sch_area_img')\">平面圖</a>&nbsp;&nbsp;<input type='file'  name='sch_area_img' ></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>學校交通圖</td>
		<td colspan='3'><a href=\"javascript:writeLeft('sch_traffic_img')\">交通圖</a>&nbsp;&nbsp;<input type='file'   name='sch_traffic_img' ></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1'>首頁標題圖</td>
		<td colspan='3'><a href=\"javascript:writeLeft('sch_title_img')\">首頁標題圖</a>&nbsp;&nbsp;<input type='file'  name='sch_title_img' ></td>
	</tr>

	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody2' width='634' colspan='4' ><input type='submit' name='act' value='儲存學校資料'></td>
	</tr>
	</table>
	</form>";
	return $main;
}

//取得學校設定
function get_school_setup(){
	global $CONN;
	$sql_select = "select * from school_base";
	$recordSet=$CONN->Execute($sql_select);
	$array = $recordSet->FetchRow();
	return $array;
}

//儲存學校資料
function save_school_setup($school){
	global $CONN,$file_dir;

	$CONN->Execute ("delete from school_base");
	$sql_insert = "insert into school_base (sch_id,sch_attr_id,sch_cname,sch_cname_s,sch_cname_ss,sch_ename,sch_sheng,sch_cdate,sch_mark,sch_class,sch_montain,sch_area_tol,sch_area_ext,sch_area_pin,sch_money,sch_money_o,sch_local_name,sch_post_num,sch_addr,sch_phone,sch_fax,sch_area,sch_kind,sch_url,sch_email,update_time,update_id,update_ip)
	values
	('$school[sch_id]','$school[sch_attr_id]','$school[sch_cname]','$school[sch_cname_s]','$school[sch_cname_ss]','$school[sch_ename]','$school[sch_sheng]','$school[sch_cdate]','$school[sch_mark]','$school[sch_class]','$school[sch_montain]','$school[sch_area_tol]','$school[sch_area_ext]','$school[sch_area_pin]','$school[sch_money]','$school[sch_money_o]','$school[sch_local_name]','$school[sch_post_num]','$school[sch_addr]','$school[sch_phone]','$school[sch_fax]','$school[sch_area]','$school[sch_kind]','$school[sch_url]','$school[sch_email]',now(),'$school[update_id]','{$_SERVER['REMOTE_ADDR']}')";
	$CONN->Execute ($sql_insert);
	//圖檔處理
	
	//建立目錄

	if (!is_dir($file_dir))	mkdir($file_dir, 0755); 
	
	//學校圖檔目錄

	// filelist.txt 記錄檔名
	$fp = fopen ("$file_dir/filelist.txt", "w");

	if($_FILES['sch_area_img']['tmp_name'] !="" ){
		copy($_FILES['sch_area_img']['tmp_name'], "$file_dir/sch_area_img");
		fwrite($fp, "sch_area_img:{$_FILES['sch_area_img']['name']}:{$_FILES['sch_area_img']['size']}\n");
	}

	if($_FILES['sch_traffic_img']['tmp_name'] !="") {
		 copy($_FILES['sch_traffic_img']['tmp_name'], "$file_dir/sch_traffic_img");
		fwrite($fp, "sch_traffic_img:{$_FILES['sch_traffic_img']['name']}:{$_FILES['sch_traffic_img']['size']}\n");
	}

	if($_FILES['sch_title_img']['tmp_name'] !="") {
		if (strtoupper(substr(PHP_OS,0,3)=='WIN')) $title_img="sch_title_img.png";
		else $title_img="sch_title_img";
		copy($_FILES['sch_title_img']['tmp_name'], "$file_dir/".$title_img);
		fwrite($fp, "sch_title_img:{$_FILES['sch_title_img']['name']}:{$_FILES['sch_title_img']['size']}\n");
	}
  	fclose($fp);
}
?>
