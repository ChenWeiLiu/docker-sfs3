<?php
//Big5 轉 UTF8
function big52utf8($big5str) {  
	
	$blen = strlen($big5str);  
	$utf8str = "";  
		for($i=0; $i<$blen; $i++) {    
			$sbit = ord(substr($big5str, $i, 1));    
			if ($sbit < 129) {      
				$utf8str.=substr($big5str,$i,1);    
			}elseif ($sbit > 128 && $sbit < 255) {     
				$new_word = iconv("big5", "UTF-8", substr($big5str,$i,2));
				$utf8str.=($new_word=="")?" ":$new_word;      
				$i++;    
			} //end if 
		} // end for
	
	return $utf8str;
}

//由登錄ID取得教師名稱的函數，若查詢不到，則顯示原登錄值
function get_teacher_name_by_id($teach_id){
	$sql_select = "select name from teacher_base where teach_id = '".$teach_id."'";
  $result=mysql_query($sql_select);
	if (mysql_num_rows($result)) {
	list($name) = mysql_fetch_row($result);
	return $name;
  } else {
  return $teach_id;	
  }
}

function get_teacher_email_by_id($teach_id){
	$MYEMAIL="";
	$query="select b.email,b.email2,b.email3 from teacher_base a,teacher_connect b where a.teacher_sn=b.teacher_sn and a.teach_id='$teach_id'";
	$result=mysql_query($query);
	list($email,$email2,$email3)=mysql_fetch_row($result);
	$MYEMAIL=($email=="")?$email2:$email;
	if ($MYEMAIL=="") $MYEMAIL=$email3;
  
  return $MYEMAIL;
  
}

function matchCIDR($addr, $cidr) {
     list($ip, $mask) = explode('/', $cidr);
     return (ip2long($addr) >> (32 - $mask) == ip2long($ip) >> (32 - $mask));
}

// 將字串中的網址加入超連結
function AddLink2Text($strURL = null)
{

$regex = "{ ((https?|telnet|gopher|file|wais|ftp):[\\w/\\#~:.?+=&%@!\\-]+?)(?=[.:?\\-]*(?:[^\\w/\\#~:.?+=&%@!\\-]|$)) }x";
return preg_replace($regex, "<a href=\"$1\" target=\"_blank\" alt=\"$1\" title=\"$1\">$1</a>",$strURL);

}

// 將字串中的網址加入超連結
function get_name_state($teach_id)
{
 mysql_query("SET NAMES 'utf8'");
 $query="select name,state from sc_msn_online where teach_id='".$teach_id."'";
 $result=mysql_query($query);
 list($N[0],$N[1]) = mysql_fetch_row($result);
 return $N;
}

//刪除給某人留言的附檔
function delete_file($idnumber,$to_id) {
	//把 $download_path 宣告進來
	global $download_path;
	  //如果有其他相同 idnumber , 表示為同時發送給多人的訊息, 不處理附檔
    //否則檢查是否有附檔
    $query_other="select idnumber from sc_msn_data where idnumber='".$idnumber."' and to_id<>'".$to_id."' and data_kind<>2";
    $result_other=mysql_query($query_other);
    if (mysql_num_rows($result_other)==0) {
     //檢查是否有附檔, 附檔可能有多個, 全刪
     $query_file="select filename from sc_msn_file where idnumber='".$idnumber."'";
     $result_file=mysql_query($query_file);
     while ($row_file=mysql_fetch_row($result_file)) {
      list($filename)=$row_file;  	  
      unlink($download_path.$filename);
     } // end unlink file
     //刪除附檔記錄
     $query="delete from sc_msn_file where idnumber='".$idnumber."'";
     mysql_query($query);
    }// end if mysql_num_rows
} // end function

//刪除某附檔
function delete_onefile($filename) {
	//把 $download_path 宣告進來
	global $download_path;
    unlink($download_path.$filename);
     $query="delete from sc_msn_file where filename='".$filename."'";
     mysql_query($query);
} // end function



//檢測檔案類型
function check_file_attr($ATTR) {
 global $PHP_FILE_ATTR;
 if (strpos(" ".$PHP_FILE_ATTR,$ATTR)) {
  return true;
 } else {
  return false;
 }
}


//圖檔處理
function ImageResize($from_filename, $save_filename, $in_width=400, $in_height=300, $quality=100)
{
    $allow_format = array('jpeg', 'png', 'gif');
    $sub_name = $t = '';

    // Get new dimensions
    $img_info = getimagesize($from_filename);
    $width    = $img_info['0'];
    $height   = $img_info['1'];
    $imgtype  = $img_info['2'];
    $imgtag   = $img_info['3'];
    $bits     = $img_info['bits'];
    $channels = $img_info['channels'];
    $mime     = $img_info['mime'];

    list($t, $sub_name) = split('/', $mime);
    if ($sub_name == 'jpg') {
        $sub_name = 'jpeg';
    }

    if (!in_array($sub_name, $allow_format)) {
        return false;
    }

    
    // 取得縮在此範圍內的比例
    $percent = getResizePercent($width, $height, $in_width, $in_height);
    $new_width  = $width * $percent;
    $new_height = $height * $percent;

    // Resample
    $image_new = imagecreatetruecolor($new_width, $new_height);

    // $function_name: set function name
    //   => imagecreatefromjpeg, imagecreatefrompng, imagecreatefromgif
    /*
    // $sub_name = jpeg, png, gif
    $function_name = 'imagecreatefrom' . $sub_name;

    if ($sub_name=='png')
        return $function_name($image_new, $save_filename, intval($quality / 10 - 1));

    $image = $function_name($filename); //$image = imagecreatefromjpeg($filename);
    */
    
    
    //$image = imagecreatefromjpeg($from_filename);
    
    $function_name = 'imagecreatefrom'.$sub_name;
    $image = $function_name($from_filename);

    imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    return imagejpeg($image_new, $save_filename, $quality);
    
   
     
    
}

/**
 * 抓取要縮圖的比例
 * $source_w : 來源圖片寬度
 * $source_h : 來源圖片高度
 * $inside_w : 縮圖預定寬度
 * $inside_h : 縮圖預定高度
 *
 * Test:
 *   $v = (getResizePercent(1024, 768, 400, 300));
 *   echo 1024 * $v . "\n";
 *   echo  768 * $v . "\n";
 */
function getResizePercent($source_w, $source_h, $inside_w, $inside_h)
{
    if ($source_w < $inside_w && $source_h < $inside_h) {
        return 1; // Percent = 1, 如果都比預計縮圖的小就不用縮
    }

    $w_percent = $inside_w / $source_w;
    $h_percent = $inside_h / $source_h;

    return ($w_percent > $h_percent) ? $h_percent : $w_percent;
}


?>
