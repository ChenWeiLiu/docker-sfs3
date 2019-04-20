<?php

/**
* //$Id: sfs_oo_zip2.php 7772 2013-11-15 07:07:28Z smallduh $
*
* EasyZIP class version 1.0 stable
* replacement for class.filesplitter.php
* 14 October 2004
* zip & split on the fly
* Author: huda m elmatsani
* Email : justhuda ## netscape ## net
*
* modified by hami (Email: cik ## boe ## tcc ## edu ## tw )
*
*  example
*  create zip file
*    $z = new EasyZIP;
*    $z -> addFile("map.bmp");
*    $z -> addFile("guide.pdf");
*      $z -> addDir("files/test");
*    $z -> zipFile("xyz.zip");
*
*  created splitted file
*      $z = new EasyZIP;
*      $z -> addFile("guide.pdf");
*    $z -> splitFile("map.zip",1048576);
*
*  pack and split
*      $z = new EasyZIP;
*    $z -> addFile("map.bmp");
*    $z -> addFile("guide.pdf");
*    $z -> splitFile("xyz.zip",1048576);
*
*
*/

//simple error message definition
define(FUNCTION_NOT_FOUND,'Error: gzcompress() function is not found');
define(FILE_NOT_FOUND,'Error: file is not found');
define(DIRECTORY_NOT_FOUND,'Error: directory is not found');

class EasyZIP {
    var $file_path = '';
    var $filelist = array();
    var $filedatalist = array();
    var $data_segments = array();
    var $data_block;
    var $file_headers  = array();
    var $filename;
    var $filedata;
    var $old_offset = 0;
    var $splitted = 0;
    var $split_signature = "";
    var $split_size = 1;
    var $split_offset = 0;
    var $disk_number = 1;
	
    function EasyZIP() {
        if (!@function_exists('gzcompress')) die(FUNCTION_NOT_FOUND);
    }

	function setPath($file_path){
		$this->file_path = $file_path;
	}

    function addFile($filename) {
		if ($this->file_path)
			$rfilename = $this->file_path.'/'.$filename;
		if(file_exists($rfilename)) {
			$this -> filelist[] = str_replace('\\', '/', $filename);
		} else {
			die(FILE_NOT_FOUND);
		}
	}

    function add_file($data,$filename) {
    	$this -> filedatalist[$filename] = & $data;
    }

    function addDir($dirname) {
		if ($this->file_path)
			$rdirname = $this->file_path.'/'.$dirname;
		if ($handle = opendir($rdirname)) {
			while (false !== ($filename = readdir($handle))) {
				if ($filename != "." && $filename != "..")
					$this->addFile($dirname . '/' . $filename);
			}
			closedir($handle);
		} else {
			die(DIRECTORY_NOT_FOUND);
		}
	}

    function zipFile($zipfilename) {
        $zip = $this -> packFiles();
        $fp = fopen($zipfilename, "w");
        fwrite($fp, $zip, strlen($zip));
        fclose($fp);
    }

    function & File() {
        return  $this -> packFiles();
    }

    function splitFile($splitfilename, $chunk_size) {
		if ($this->file_path)
			$splitfilename = $this->file_path.'/'.$splitfilename;
        $this -> chunk_size = $chunk_size;
        $this -> splitted = 1;
        $this -> split_offset = 4;
        $this -> old_offset = $this -> split_offset;
        $this -> split_signature = "\x50\x4b\x07\x08";
        $zip = $this -> packFiles();
        $out = $this -> str_split($this -> split_signature . $zip, $chunk_size);
		for ($i = 0; $i < sizeof($out); $i++){
			if($i < sizeof($out)-1) {
				$sfilename = basename ($splitfilename,".zip");
				$sfilename = $sfilename . ".z" . sprintf("%02d",$i+1);
			}
			else $sfilename = $splitfilename;
			$fp = fopen($sfilename, "w");
			fwrite($fp, $out[$i], strlen($out[$i]));
			fclose($fp);
		}
	}

    function packFiles() {

        foreach($this -> filelist as $k => $filename) {
            $this -> filename =  $filename;
            $this -> setFileData();
            $this -> setLocalFileHeader();
            $this -> setDataDescriptor();
            $this -> setDataSegment();
            $this -> setFileHeader();
        }

        foreach($this -> filedatalist as $filename=>$data) {
            $this -> filename =  $filename;
            $this -> setFileData2($data);
            $this -> setLocalFileHeader();
            $this -> setDataDescriptor();
            $this -> setDataSegment();
            $this -> setFileHeader();
        }

        return  $this -> getDataSegments() .
                $this -> getCentralDirectory();

    }

    function setFileData2($data) {
		clearstatcache();
		$this->filedata = & $data;
		$filetime = time();
		$this -> DOSFileTime($filetime);
    }

    function setFileData() {
           if ($this->file_path)
               $filename = $this->file_path.'/'.$this->filename;
            clearstatcache();
            $fd = fopen ($filename, "rb");
            $this->filedata = fread ($fd, filesize ($filename));
            fclose ($fd);
            $filetime = filectime($filename);
            $this -> DOSFileTime($filetime);
    }

    function setLocalFileHeader() {
	
        $local_file_header_signature           = "\x50\x4b\x03\x04";//4 bytes  (0x04034b50)
        $this -> version_needed_to_extract      = "\x14\x00";  //2 bytes
        $this -> general_purpose_bit_flag      = "\x00\x00";  //2 bytes
        $this -> compression_method           = "\x08\x00";  //2 bytes
        $this -> crc_32                          = pack('V', crc32($this -> filedata));//  4 bytes
                //compressing data
                $c_data   = gzcompress($this -> filedata);
                $this->compressed_filedata    = substr(substr($c_data, 0, strlen($c_data) - 4), 2); // fix crc bug
        
        $this -> compressed_size                = pack('V', strlen($this -> compressed_filedata));// 4 bytes
        $this -> uncompressed_size              = pack('V', strlen($this -> filedata));//4 bytes
        $this -> filename_length              = pack('v', strlen($this->filename));// 2 bytes
        $this -> extra_field_length           = pack('v', 0);  //2 bytes
				
        $this -> local_file_header =     $local_file_header_signature .
                $this -> version_needed_to_extract .
                $this -> general_purpose_bit_flag .
                $this -> compression_method .
                $this -> last_mod_file_time .
                $this -> last_mod_file_date .
                $this -> crc_32 .
                $this -> compressed_size .
                $this -> uncompressed_size .
                $this -> filename_length .
                $this -> extra_field_length .
                $this -> filename;
    }

    function setDataDescriptor() {
    
        $this -> data_descriptor =  $this->crc_32 .   //4 bytes
                $this -> compressed_size .           //4 bytes
                $this -> uncompressed_size;          //4 bytes
    }

    function setDataSegment() {
    
            $this -> data_segments[] = $this -> local_file_header .
                                    $this -> compressed_filedata .
                                    $this -> data_descriptor;
            $this -> data_block = implode('', $this -> data_segments);
    }

    function getDataSegments() {
        return $this -> data_block;
    }

    function setFileHeader() {
				//$filename = substr($this->filename,3);
        $new_offset        = strlen( $this -> split_signature . $this -> data_block );
        
        $central_file_header_signature  = "\x50\x4b\x01\x02";//4 bytes  (0x02014b50)
        $version_made_by                = pack('v', 0);  //2 bytes
        
        $file_comment_length            = pack('v', 0);  //2 bytes
        $disk_number_start              = pack('v', $this -> disk_number - 1); //2 bytes
        $internal_file_attributes       = pack('v', 0); //2 bytes
        $external_file_attributes       = pack('V', 32); //4 bytes
        $relative_offset_local_header   = pack('V', $this -> old_offset); //4 bytes
        
        if($this -> splitted) {
            $this -> disk_number = ceil($new_offset/$this->chunk_size);
            $this -> old_offset = $new_offset - ($this->chunk_size * ($this -> disk_number-1));
        } else $this -> old_offset = $new_offset;
        
        $this -> file_headers[] =     $central_file_header_signature .
                $version_made_by .
                $this -> version_needed_to_extract .
                $this -> general_purpose_bit_flag .
                $this -> compression_method .
                $this -> last_mod_file_time .
                $this -> last_mod_file_date .
                $this -> crc_32 .
                $this -> compressed_size .
                $this -> uncompressed_size .
                $this -> filename_length .
                $this -> extra_field_length .
                $file_comment_length .
                $disk_number_start .
                $internal_file_attributes .
                $external_file_attributes .
                $relative_offset_local_header .
                $this -> filename;
    }

    function getCentralDirectory() {
        $this -> central_directory = implode('', $this -> file_headers);  
        //echo $this->central_directory; exit;
        return  $this -> central_directory .
                $this -> getEndCentralDirectory();
    }

    function getEndCentralDirectory() {
                    
        $zipfile_comment = "Compressed/Splitted by PHP EasyZIP";

        if($this -> splitted) {
            $data_len = strlen($this -> split_signature . $this -> data_block . $this -> central_directory);
            $last_chunk_len = $data_len - floor($data_len / $this -> chunk_size) * $this -> chunk_size;
            $this -> old_offset = $last_chunk_len - strlen($this -> central_directory);
        }

        $end_central_dir_signature    = "\x50\x4b\x05\x06";//4 bytes  (0x06054b50)
        $number_this_disk             = pack('v', $this->disk_number - 1);//2 bytes
        $number_disk_start              = pack('v', $this->disk_number - 1);//  2 bytes
        $total_number_entries          = pack('v', sizeof($this -> file_headers));//2 bytes
        $total_number_entries_central = pack('v', sizeof($this -> file_headers));//2 bytes
        $size_central_directory         = pack('V', strlen($this -> central_directory));  //4 bytes
        
        $offset_start_central         = pack('V', $this -> old_offset); //4 bytes     
        $zipfile_comment_length       = pack('v', strlen($zipfile_comment));//2 bytes
        
        return $end_central_dir_signature .
            $number_this_disk .
            $number_disk_start .
            $total_number_entries .
            $total_number_entries_central .
            $size_central_directory .
            $offset_start_central .
            $zipfile_comment_length .
            $zipfile_comment;
    }

    function DOSFileTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        }

        $dostime = (($timearray['year'] - 1980) << 25) |
                    ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                    ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) |
                    ($timearray['seconds'] >> 1);
                
        $dtime    = dechex($dostime);
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5];
                  
        $hexddate = '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');
        eval('$hexddate = "' . $hexddate . '";');
        
        $this->last_mod_file_time = $hexdtime;
        $this->last_mod_file_date = $hexddate;
    }

    function str_split($string, $length) {
        for ($i = 0; $i < strlen($string); $i += $length) {
            $array[] = substr($string, $i, $length);
        }
        return $array;
    }

   function & read_file($file) {
        if (!($fp = fopen($file, 'rb' ))) return false;
        $contents = fread($fp, filesize($file));
        fclose($fp);
        return $contents;
	}

	//$mode=0 加入除了 content.xml 以外的所有檔案
	//$mode=1 加入除了 content.xml, settings.xml, styles.xml 以外的所有檔案
	//$file_arr 內容為不加入的所有檔案名
	function addAll($mode=0,$file_arr=array(),$my_path=""){
		$dir_arr=array(".","..");
		if ($file_arr) $file_arr=array_merge($dir_arr,$file_arr);
		elseif ($mode==1) $file_arr=array(".","..","content.xml","settings.xml","styles.xml");
		else $file_arr=array(".","..","content.xml");
		$path=$this->file_path.(($my_path)?"/".$my_path:"");
		$my_path=($my_path)?$my_path."/":"";
		if(is_dir($path)){
			if($dh=opendir($path)){
				while(($file=readdir($dh))!==false) {
					if(in_array($file,$file_arr)){
						continue;
					}elseif(is_dir($path."/".$file)){
						$this->addAll(0,"",$my_path.$file);
					}else{
						$data=$this->read_file($path."/".$file);
						$this->add_file($data,$my_path.$file);
					}
				}
				closedir($dh);
			}
		}
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
/*
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
*/
	$sw["█"]="&#9608;";
	
	$all_word=array_keys($sw);

	foreach($all_word as $spec_uni){
		$text=str_replace($spec_uni,$sw[$spec_uni],$text);
	}
	return $text;
   }
   
/*
	$all_word=array_keys($sw);
	$wt = mb_strlen($text,'BIG-5'  ) ;

	for ($i = 0 ; $i < $wt ; $i++ ) { 
	  $nw = mb_substr($text ,$i ,1,'BIG-5') ;
	  foreach($all_word as $spec_uni){
	  	if ($nw == $spec_uni ) {
	  	   $nw= $sw[$spec_uni];
	  	   break ;
      }     
	  } 
	  $ww.= $nw ;  	   
    }
	return $ww; 
  }
*/
}
?>
