<?php
//載入設定檔
include_once "config.php";
include_once "../../include/sfs_core_module.php";
include_once "../../include/sfs_case_PLib.php";
include_once "../../include/sfs_case_PLlib.php";

function ooo_class($title,$body1,$body2,$data_arr){
global $UPLOAD_PATH,$UPLOAD_URL, $SCHOOL_BASE,$ima,$CONN;
$ooo_path="/ooo_a4h";
//新增一個 zipfile 實例
$ttt = new zipfile;

//讀出 xml 檔案
$data = $ttt->read_file(dirname(__FILE__).$ooo_path."/META-INF/manifest.xml");

//加入 xml 檔案到 zip 中，共有五個檔案
//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱
$ttt->add_file($data,"META-INF/manifest.xml");
$data = $ttt->read_file(dirname(__FILE__).$ooo_path."/settings.xml");
$ttt->add_file($data,"settings.xml");
$data = $ttt->read_file(dirname(__FILE__).$ooo_path."/styles.xml");
$ttt->add_file($data,"styles.xml");
$data = $ttt->read_file(dirname(__FILE__).$ooo_path."/meta.xml");
$ttt->add_file($data,"meta.xml");
$data = $ttt->read_file(dirname(__FILE__).$ooo_path."/content.xml");

	// 加入換頁 tag


	$data = str_replace("<office:automatic-styles>",'<office:automatic-styles><style:style style:name="sfs_break_page" style:family="paragraph" style:parent-style-name="Standard"><style:properties fo:break-before="page"/></style:style>',$data);
	
	//拆解 content.xml
	$arr1 = explode("<office:body>",$data);
	//檔頭
	$con_head = $arr1[0]."<office:body>";
	$arr2 = explode("</office:body>",$arr1[1]);
	//資料內容
	$con_body = $arr2[0];
	//檔尾
	$con_foot = "</office:body>".$arr2[1];
	//$i=0;
	$replace_data ='';
	$filename=$title.".sxw";
	
	$today=(date("Y")-1911).".".date("m").".".date("d");
$x=count($data_arr);
for($i=0;$i<$x;$i++){

	$head=$data_arr[$i]["head"];
	$stud_id=$data_arr[$i]["stud_id"];
	$body=$data_arr[$i]["body"];

	//$stud_ima="photo/student/".substr($stud_id,0,2) ."/". $stud_id;
	//校長簽章檔
	if (is_file($UPLOAD_PATH."school/title_img/title_1")){
		$title_img = "http://".$_SERVER["SERVER_ADDR"].$UPLOAD_URL."school/title_img/title_1";		
		$sign_1 ="<draw:image draw:style-name=\"fr1\" draw:name=\"aaaa1\" text:anchor-type=\"paragraph\" svg:x=\"0.73cm\" svg:y=\"0.161cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"0\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
	} else{
		$sign_1=$sign_1_name;	
	}
	if (is_file($UPLOAD_PATH."school/title_img/title_v")){
		$title_img = "http://".$_SERVER["SERVER_ADDR"].$UPLOAD_URL."school/title_img/title_v";		
		$sign_v ="<draw:image draw:style-name=\"fr1\" draw:name=\"aaaa1\" text:anchor-type=\"paragraph\" svg:x=\"0.73cm\" svg:y=\"0.161cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"0\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
	} else{
		$sign_v="";	
	}

	
///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
if ($stud_id <> "") {
    $stmt = $mysqliconn->prepare("select stud_study_year from stud_base where stud_id=? order by stud_study_year desc");
    $stmt->bind_param('s', $stud_id);
} 

$stmt->execute();
$stmt->bind_result($stud_study_year);
$stmt->fetch();
$stmt->close();
///mysqli	
	
	/*
	$sqlstr ="select stud_study_year from stud_base where stud_id='$stud_id' order by stud_study_year desc";
	$result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
	$stud_study_year=$result->fields[0];
	*/
	
	$stud_ima="photo/student/$stud_study_year/". $stud_id;
	
	//學生照片
	if (is_file($UPLOAD_PATH.$stud_ima) and $ima==1){
		$stud_img = "http://".$_SERVER["SERVER_ADDR"].$UPLOAD_URL.$stud_ima;
		$stud_1 ="<draw:image draw:style-name=\"fr1\" draw:name=\"aaaa1\" text:anchor-type=\"paragraph\" svg:x=\"0.73cm\" svg:y=\"0.161cm\"  svg:width=\"3cm\" svg:height=\"5cm\"    draw:z-index=\"0\" xlink:href=\"$stud_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
	} else{
		$stud_1="";	
	}


//(integer)轉整數，避免05變零五
/*
$today_y = Num2CNum(((integer) substr($today,0,2)));
$today_m = Num2CNum(((integer) substr($today,3,2)));
$today_d = Num2CNum(((integer) substr($today,6,2)));
*/
$today_y = (integer) date("Y")-1911;
$today_m = (integer) date("m");
$today_d = (integer) date("d");

$temp_arr[city1] = substr($SCHOOL_BASE[sch_cname],0,6);
$temp_arr[city2] = substr($SCHOOL_BASE[sch_cname],6,6);
$temp_arr[school] = substr($SCHOOL_BASE[sch_cname],12,12);
$temp_arr[head] = $head;
$temp_arr[title] = $title;
$temp_arr[body] = $body;
$temp_arr[body1] = $body1;
$temp_arr[body2] = $body2;
$temp_arr[SIGN_1] = $sign_1;
$temp_arr[SIGN_v] = $sign_v;
$temp_arr[STUD_1] = $stud_1;

$temp_arr[td_y] = $today_y;
$temp_arr[td_m] = $today_m;
$temp_arr[td_d] = $today_d;


	
		// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
		$replace_data .= $ttt->change_temp($temp_arr,$con_body);
		//換頁處理
		if ($i<($x-1))
			$replace_data .='<text:p text:style-name="sfs_break_page"/>';
	
}

	$replace_data = $ttt->change_temp2(array("break_text"=>"<text:line-break/>"),$replace_data);	
	$replace_data = $con_head.$replace_data.$con_foot;
	
	//把一些多餘的標籤以空白取代
	$pattern[]="/\{([^\}]*)\}/";
	$replacement[]="";
	
	$replace_data=preg_replace($pattern, $replacement, $replace_data);
	
	
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");


//產生 zip 檔
$sss = $ttt->file();

///以串流方式送出 sxw

header("Content-disposition: attachment; filename=$filename");
header("Content-type: application/vnd.sun.xml.writer");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

header("Expires: 0");

echo $sss;
exit();
	return;
}
//切割資料陳列

function sel_data($string,$s) {
$i=1;
$tok = strtok ($string,"#"); 
while ($tok) { 
$data_arr[$i]=$tok; 
$tok = strtok ("#"); 
$i++;
} 
return $data_arr[$s];
}



// $Id: make_ooo.php 8650 2015-12-18 03:58:09Z qfon $
// 取代 mzip.php

/*
產生 zip 檔 class
*/
class zipfile 
{ 
  var $datasec = array(); 
  var $ctrl_dir = array(); 
  var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; 
  var $old_offset = 0; 

function add_dir($name) 
    { 
        $name = str_replace("\\", "/", $name); 

        $fr = "\x50\x4b\x03\x04"; 
        $fr .= "\x0a\x00"; 
        $fr .= "\x00\x00"; 
        $fr .= "\x00\x00"; 
        $fr .= "\x00\x00\x00\x00"; 

        $fr .= pack("V",0); 
        $fr .= pack("V",0); 
        $fr .= pack("V",0); 
        $fr .= pack("v", strlen($name) ); 
        $fr .= pack("v", 0 ); 
        $fr .= $name; 
        $fr .= pack("V", 0); 
        $fr .= pack("V", 0); 
        $fr .= pack("V", 0); 

        $this -> datasec[] = $fr ;
        $new_offset = strlen(implode("", $this->datasec)); 

     $cdrec = "\x50\x4b\x01\x02"; 
     $cdrec .="\x00\x00"; 
     $cdrec .="\x0a\x00"; 
     $cdrec .="\x00\x00"; 
     $cdrec .="\x00\x00"; 
     $cdrec .="\x00\x00\x00\x00"; 
     $cdrec .= pack("V",0); 
     $cdrec .= pack("V",0); 
     $cdrec .= pack("V",0); 
     $cdrec .= pack("v", strlen($name) ); 
     $cdrec .= pack("v", 0 ); 
     $cdrec .= pack("v", 0 ); 
     $cdrec .= pack("v", 0 ); 
     $cdrec .= pack("v", 0 ); 
     $ext = "\x00\x00\x10\x00"; 
     $ext = "\xff\xff\xff\xff"; 
     $cdrec .= pack("V", 16 ); 
     $cdrec .= pack("V", $this -> old_offset ); 
     $cdrec .= $name; 

     $this -> ctrl_dir[] = $cdrec; 
     $this -> old_offset = $new_offset; 
     return; 
} 

function add_file($data, $name) { 
   $name = str_replace("\\", "/", $name); 
   $unc_len = strlen($data); 
   $crc = crc32($data); 
   $zdata = gzcompress($data); 
   $zdate = substr ($zdata, 2, -4); 
   $c_len = strlen($zdata);
   
   $fr = "\x50\x4b\x03\x04"; 
        $fr .= "\x14\x00"; 
        $fr .= "\x00\x00"; 
        $fr .= "\x08\x00"; 
        $fr .= "\x00\x00\x00\x00"; 
        $fr .= pack("V",$crc); 
        $fr .= pack("V",$c_len); 
        $fr .= pack("V",$unc_len); 
        $fr .= pack("v", strlen($name) ); 
        $fr .= pack("v", 0 ); 
        $fr .= $name; 
        $fr .= $zdate; 
        $fr .= pack("V",$crc); 
        $fr .= pack("V",$c_len); 
        $fr .= pack("V",$unc_len); 

        $this -> datasec[] = $fr; 
        $fr = "\x50\x4b\x03\x04"; 
        $fr .= "\x14\x00"; 
        $fr .= "\x00\x00"; 
        $fr .= "\x08\x00"; 
        $fr .= "\x00\x00\x00\x00"; 
        $fr .= pack("V",$crc); 
        $fr .= pack("V",$c_len); 
        $fr .= pack("V",$unc_len); 
        $fr .= pack("v", strlen($name) ); 
        $fr .= pack("v", 0 ); 
        $fr .= $name; 
        $fr .= $zdata; 
        $fr .= pack("V",$crc); 
        $fr .= pack("V",$c_len); 
        $fr .= pack("V",$unc_len); 

        $this -> datasec[] = $fr; 
        $new_offset = strlen(implode("", $this->datasec)); 

  $cdrec = "\x50\x4b\x01\x02"; 
  $cdrec .="\x00\x00"; 
  $cdrec .="\x14\x00"; 
  $cdrec .="\x00\x00"; 
  $cdrec .="\x08\x00"; 
  $cdrec .="\x00\x00\x00\x00"; 
  $cdrec .= pack("V",$crc); 
  $cdrec .= pack("V",$c_len); 
  $cdrec .= pack("V",$unc_len); 
  $cdrec .= pack("v", strlen($name) ); 
  $cdrec .= pack("v", 0 ); 
  $cdrec .= pack("v", 0 ); 
  $cdrec .= pack("v", 0 ); 
  $cdrec .= pack("v", 0 ); 
  $cdrec .= pack("V", 32 ); 
  $cdrec .= pack("V", $this -> old_offset ); 

  $this -> old_offset = $new_offset; 

  $cdrec .= $name; 
  $this -> ctrl_dir[] = $cdrec; 
} 

function addFileAndRead ($file) {

    if (is_file($file))
      $this->add_File($this->read_File($file), $file);

  }




function file() { 
        $data = implode("", $this -> datasec); 
        $ctrldir = implode("", $this -> ctrl_dir); 

        return 
            $data. 
            $ctrldir. 
            $this -> eof_ctrl_dir. 
            pack("v", sizeof($this -> ctrl_dir)). 
            pack("v", sizeof($this -> ctrl_dir)). 
            pack("V", strlen($ctrldir)). 
            pack("V", strlen($data)). 
            "\x00\x00"; 
    } 

function read_file($file) {

        if (!($fp = fopen($file, 'r' ))) return false;

        $contents = fread($fp, filesize($file));

        fclose($fp);

        return $contents;
}

function change_temp($arr,$source,$is_reference=0) {
	$temp_str = $source;
	 //XML 實體參照轉換
        $xml_reference_arr = array("<"=>"&lt;","&"=>"&amp;",">"=>"&gt;","\""=>"&quot;","'"=>"&apos;");
 reset($arr);
	while(list($id,$val) = each($arr)){
		reset($xml_reference_arr);
		if ($is_reference){
			while(list($idd,$vall)=each($xml_reference_arr))
				$val = str_replace($idd, $vall,$val);
		}
		$id=$this->spec_uni($id);
		$val=$this->spec_uni($val);

		$id =iconv("Big5","UTF-8",$id);
		$val =iconv("Big5","UTF-8",$val);
		$temp_str = str_replace("{".$id."}", $val,$temp_str);
	}
	return $temp_str;
}

//單存轉換 無關乎 unicode 及陣列
function change_sigle_temp($arr,$source) {
	$temp_str = $source;
	reset($arr);
	while(list($id,$val) = each($arr)){
		$temp_str = str_replace($id, $val,$temp_str);
	}
	return $temp_str;
}

//沒有轉換 UTF-8，模組產生程式會用到。
function change_temp2($arr,$source) {
	$temp_str = $source;
	reset($arr);
	while(list($id,$val) = each($arr)){
		//$val =iconv("Big5","UTF-8",$val);
		$temp_str = str_replace("{".$id."}", $val,$temp_str);
	}
	return $temp_str;
}

//iconv 無法轉的字
function spec_uni($text=""){
	$sw["碁"]="&#30849;";
	$sw["粧"]="&#31911;";
	$sw["裏"]="&#35023;";
	$sw["墻"]="&#22715;";
	$sw["恒"]="&#24658;";
	$sw["銹"]="&#37561;";
	$sw["嫺"]="&#23290;";
	$sw["╔"]="&#9556;";
	$sw["╦"]="&#9574;";
	$sw["╗"]="&#9559;";
	$sw["╠"]="&#9568;";
	$sw["╬"]="&#9580;";
	$sw["╣"]="&#9571;";
	$sw["╚"]="&#9562;";
	$sw["╩"]="&#9577;";
	$sw["╝"]="&#9565;";
	$sw["╒"]="&#9554;";
	$sw["╤"]="&#9572;";
	$sw["╕"]="&#9557;";
	$sw["╞"]="&#9566;";
	$sw["╪"]="&#9578;";
	$sw["╡"]="&#9569;";
	$sw["╘"]="&#9560;";
	$sw["╧"]="&#9575;";
	$sw["╛"]="&#9563;";
	$sw["╓"]="&#9555;";
	$sw["╥"]="&#9573;";
	$sw["╖"]="&#9558;";
	$sw["╟"]="&#9567;";
	$sw["╫"]="&#9579;";
	$sw["╢"]="&#9570;";
	$sw["╙"]="&#9561;";
	$sw["╨"]="&#9576;";
	$sw["╜"]="&#9564;";
	$sw["║"]="&#9553;";
	$sw["═"]="&#9552;";
	$sw["╔"]="&#9556;";
	$sw["╗"]="&#9559;";
	$sw["╚"]="&#9562;";
	$sw["╝"]="&#9565;";
	$sw["█"]="&#9608;";
	$all_word=array_keys($sw);
	foreach($all_word as $spec_uni){
		$text=str_replace($spec_uni,$sw[$spec_uni],$text);
	}
	return $text;
}
}


?>
