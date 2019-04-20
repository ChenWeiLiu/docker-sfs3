<?php

// $Id: sfs_oo_zip.php 7772 2013-11-15 07:07:28Z smallduh $
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
//轉換字串
function change_str($source,$is_reference=1,$is_iconv=1){
        $temp_str = $source;
        if ($is_reference){
             $ttt='';
             $len = strlen($temp_str);
             for($i=0;$i<$len;$i++){
			          if ($i<($len-1) and $temp_str[$i].$temp_str[$i+1]=='&#')
				           $ttt .= $temp_str[$i];
			          else
                  	$ttt .= $this->xml_reference_change($temp_str[$i]);
		         }
                $temp_str = $ttt;
        }
	if ($is_iconv)
		return iconv("Big5","UTF-8//IGNORE", $this->spec_uni($temp_str));
	else
		return $this->spec_uni($temp_str);

}


function change_temp($arr,$source,$is_reference=1) {
	$temp_str = $source;
	reset($arr);
	while(list($id,$val) = each($arr)){
		$val = strval($val);
		$len = strlen($val) ;
		if ($is_reference && $val<>''){
			$tttt='';
			for($i=0;$i< $len ;$i++){
			  if ($i<($len-1) and $val[$i].$val[$i+1]=='&#')
			     $tttt .= $val[$i];
			  else    
				   $tttt .= $this->xml_reference_change($val[$i]);
			}	   
			$val = $tttt;
		}
		$id=$this->spec_uni($id);
		$val=$this->spec_uni($val);
		$id =iconv("Big5","UTF-8//IGNORE",$id);
		//$val =iconv("Big5","UTF-8//IGNORE",$val);
		$val = $this->big52utf8($val);
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
	while(list($id,$val) = each($arr)){
		//$val =iconv("Big5","UTF-8",$val);
		$temp_str = str_replace("{".$id."}", $val,$temp_str);
	}
	return $temp_str;
}

   // Big5 words can't be changed replaced by 'o'
   function big52utf8($big5str) {
        $blen = strlen($big5str);
        $utf8str = "";
        for($i=0; $i<$blen; $i++) {
             $sbit = ord(substr($big5str, $i, 1));
             if ($sbit < 129) {
                $utf8str.=substr($big5str,$i,1);
             }elseif ($sbit > 128 && $sbit < 255) {
                $new_word = iconv("big5", "UTF-8", substr($big5str,$i,2));
                $utf8str.=($new_word=="")?"o":$new_word;
                $i++;
             }
        } // end for

    return $utf8str;

 } // end function



 //XML 實體參照轉換
function xml_reference_change($text){
	$sw = array("&"=>"&amp;","<"=>"&lt;",">"=>"&gt;","\""=>"&quot;","'"=>"&apos;");
	$all_word=array_keys($sw);
	foreach($all_word as $spec_uni){
		$text=str_replace($spec_uni,$sw[$spec_uni],$text);
	}
	return $text;
}




//iconv 無法轉的字
function spec_uni($text=""){
	$sw["廖"]="&#24278;";
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
	//$sw["╞"]="&#9566;";
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
