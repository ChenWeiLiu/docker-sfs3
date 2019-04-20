<?php

// $Id: sfs_oo_quicktemplate.php 5310 2009-01-10 07:57:56Z hami $
// 取代 QT327class.php

//設定上傳檔案路徑
$img_path = "tmp";
	$QTPLCACHEPATH = set_upload_path("$img_path");
	
//$QTPLCACHEPATH	       = $UPLOAD_PATH."tmp/";	 //folder for cache files
// some settings
$QTPLCACHE             = true;              //want to use the cache-system? (recomanded: true)
$QTPLPRECACHE          = true;              //want to use secondar cache?   (recomanded: true for large templates) 
$QTPLCACHEEXTENSION    = ".qtpl-cache";     //cache file extension
$QTPLPRECACHEEXTENSION = ".qtpl-cache.PRE"; //sec-cache file extension

//(!) NOTE: if you use the cache methods don't forget to 
//          give write rights for your web user (eg: apache)


/* 
LICENSE: GNU LGPL
This class is free software, you can redistribute it and/or modify 
it under the terms of the GNU Lesser General Public License as 
published by the Free Software Foundation.

The author.
*/




class QuickTemplate {

/*************************************************
             *
            * CLASS NAME: QuickTemplate
           * AUTHOR: Stefan Bocskai
                     <stefanbocskai@hotmail.com>
          * Date: 30/09/2002
         * Version: V 3.2.7
        *
       * http://quicktemplate.sourceforge.net/
*************************************************/
        

//===================================================================
var $Error = "";                // Last error
var $BLOCKS;                    // The children of current block
var $TEXT = "";                 // The current text of BLOCK
var $DEFAULTTEXT = "";          // The default text if block is empty
var $BlockName = "";            // The name of the Block
var $BlockTemplate = "";        // Unparsed content of the block
var $VARLIST;                   // List of variables of this block
var $VARS;                      // List of values of variables of this block
var $AUTOINCREMENT = 1;         // Start value of auto-inrement variable
var $INCREMENT = 1;             // step of incrementation

//===================================================================
var $__VERSION__ = "V3.2.7";
var $__CLASS_NAME__ = "QuickTemplate";

var $_start_tag_ = "<!--";
var $_end_tag_  = "-->";
var $_begin_block_ = "BEGIN[[:blank:]]+BLOCK:";
var $_end_block_  = "END[[:blank:]]+BLOCK:";
var $_html_source_= "HTML_SOURCE:";
var $_tpl_source_= "TPL_SOURCE:";
var $_pre_chache_= "HTML_PRE_CACHE:";

var $__BLOCKS_VAR__ = "__BLOCKS_VAR__";
var $__NULL_STRING__ = "BLANK";
var $__VAR_NULL_STRING__ = "__VAR_NULL_STRING__";
var $__DEFAULTS_VALUES__ = "DEFAULTS";
var $__DEFAULTVALUENAME__ = "DEFAULT";
var $__AUTOINCREMENT_NAME__ ="AUTOINCREMENT";

var $LOCAUTOINCREMENT;
var $PARSED;
var $CACHED=false;

var $cachedfilename = "";
var $precachedfname = "";
var $level=0;
var $templatebasedir = "";
//===================================================================
//===================================================================
//====== PUBLIC Functions ===========================================
//===================================================================
//========================
//===========

//========================
//      QuickTemplate
//========================
function QuickTemplate($misc, $part="main", $flag=0, $level=0){
                        // do not use $flag and $level, is just 
                        // for internal use 
global $QTPLCACHEPATH, $QTPLCACHE, $QTPLPRECACHE, $QTPLCACHEEXTENSION, $QTPLPRECACHEEXTENSION;
   $this->level=$level;
   if(($level==0)&&($QTPLCACHE)){   	
   	    $this->templatebasedir = dirname($misc);
        $basefilename = $QTPLCACHEPATH.substr(md5($part.$misc.filemtime($misc).$this->__VERSION__),0,20);
        $this->cachedfilename = $basefilename.$QTPLCACHEEXTENSION;
        $this->precachedfname = $basefilename.$QTPLPRECACHEEXTENSION;

   	if (file_exists($this->cachedfilename))
   	        $chf = fopen($this->cachedfilename,"r");
   	else
   	        $chf = false;

   	if($chf){
   		$obj_ser = fread ($chf, filesize ($this->cachedfilename));
   		fclose($chf);
   		$this=unserialize($obj_ser);
   		$this->CACHED=true;
   	}
   	
   }
   $this->isDebug = false;
   global $PHP_SELF;

   if(!$this->CACHED){
        $this->BlockName = $part;
        $this->BLOCKS = array();
        $this->TEXT = "";
        $this->PARSED=false;
        if (!$flag){
                if ($QTPLCACHE && $QTPLPRECACHE){
                        $this->BlockTemplate = $this->_getBlockPart_($this->_createHtmlSourceCached_($this->_replace_all_tpl_sources_($this->_openFileFromTemplate_($misc))),$part);
                }else{
                        $this->BlockTemplate = $this->_getBlockPart_($this->_openFileFromTemplate_($misc),$part);
                }
        }else{
                $this->BlockTemplate = $misc; 
        }
   }   
   //if ($this->level==0) echo "<hr><pre>".htmlspecialchars($this->BlockTemplate)."</pre>";
   //exit;
   // default variables ...
   $this->VARS[$this->__DEFAULTS_VALUES__] = array();
   $dt = date("l d F Y"); $tm=date("H:i A");
   $this->_assign_defaults_("DATE", $dt);  
   $this->_assign_defaults_("TIME", $tm);	
   $this->_assign_defaults_("VERSION",$this->__CLASS_NAME__." ".$this->__VERSION__);
   $this->_assign_defaults_("SELFURL", $PHP_SELF);
   $this->_assign_defaults_($this->__NULL_STRING__, "");
   $this->_assign_defaults_($this->__VAR_NULL_STRING__, array());
   $this->_assign_defaults_($this->__AUTOINCREMENT_NAME__, $this->AUTOINCREMENT);
   $this->LOCAUTOINCREMENT = $this->AUTOINCREMENT;
   //$this->_assign_defaults_("LANG", $GLOBALS[LANG]);
   $this->_assign_defaults_("REFRESH", md5(uniqid(rand())));
   //$this->_assign_defaults_("USER_TYPE", $GLOBALS[USER_TYPE]);

   if(!$this->CACHED){
        $this->_createChildrens_();
        $this->_getVariables_();
   }
   
   if ((!$flag)&&($this->level==0)&&($QTPLCACHE)){
   	$chf=fopen($this->cachedfilename,"w");
   	if($chf){
   		fwrite($chf, serialize($this));
   		fclose($chf);
   	}
   }
 }

//===================================================
function  _createHtmlSourceCached_(&$text){
global $QTPLCACHEPATH;

        $stopchar=array();
        $stopchar[]=1;
        $p=0;
        $q = false;
        while(true){
           $p=strpos($text,"<!--", $p+1);
           //$q=(strpos(substr($text,$p+1,50),"BLOCK:"));
           if($p===false) break;
           if ($q!==false){$stopchar[]=$p;$stopchar[]=$p+3;}
        }
        $p=0;
        while(true){
           $p=strpos($text,"-->", $p+1);
           //$q=(strpos(substr($text,$p-50,50),"BLOCK:"));
           if($p===false)break;
           if ($q!==false){$stopchar[]=$p;$stopchar[]=$p+2;}
        }
        $p=0;
        while(true){
           $p=strpos($text,"{", $p+1);
           //$q=(strpos(substr($text,$p+1,50),"}"));
           if($p===false) break;
           if ($q!==false){$stopchar[]=$p;}
        }
        $p=0;
        while(true){
           $p=strpos($text,"}", $p+1);
           //$q=(strpos(substr($text,$p-50,50),"{"));
           if($p===false) break;
           if ($q!==false){$stopchar[]=$p;}
        }
        $stopchar[]=0;

        sort($stopchar);
        reset($stopchar);
        $i=1;$s=0;$offset=0;
        $seek=0;
        $chf=fopen($this->precachedfname,"w");
        if (!$chf) return $text;
        while($stopchar[$i]){
                if ($stopchar[$i+1]-$stopchar[$i]>200){  
                           $s=$s+$stopchar[$i+1]-$stopchar[$i];                                
                           $text2=substr($text,-$offset+$stopchar[$i]+1,$stopchar[$i+1]-$stopchar[$i]-1);
                                
   	                        if($chf){
   		                        fwrite($chf, $text2);   		        
   		                        $l1=($stopchar[$i+1]-$stopchar[$i]-1);
   		                        $identificator = sprintf("%06d%06d",$l1,$seek);
   		                        $seek+=$l1;
   		                        $repltext="{".$this->_pre_chache_.$identificator."}";
   		                        $text=substr_replace($text,$repltext,-$offset+$stopchar[$i]+1,$stopchar[$i+1]-$stopchar[$i]-1);
   		                        $offset=$offset+$stopchar[$i+1]-$stopchar[$i]-strlen($repltext)-1;
   	                        }
                }
                $i++;
        }
      fclose($chf); 
      return $text;
}
//===================================================

//========================
//      SetNullString 
//========================
function SetNullString($part, $nullstring = ""){
	$vname = $this->__DEFAULTS_VALUES__.".".$this->__NULL_STRING__;
	if ($part=="")
		$this->_assign_(&$vname,&$nullstring);
	else
		$this->_assignLocal_(&$this->BlockName,&$vname,&$nullstring);
}

function SetVarNullString($part, $var, $nullstring = ""){
	$vname = $this->__DEFAULTS_VALUES__.".".$this->__VAR_NULL_STRING__.".".$var;
	if ($part=="")
		$this->_assign_(&$vname,&$nullstring);
	else
		$this->_assignLocal_(&$this->BlockName,&$vname,&$nullstring);
}
//========================
//      quickText
//========================
function quickText($part = ""){
global $QTPLCACHEPATH, $QTPLCACHE, $QTPLPRECACHE, $QTPLCACHEEXTENSION,$QTPLPRECACHEEXTENSION;
	if ($part=="") $part = $this->BlockName;
	$a = $this->_quicktext_($part);
	if($this->level==0){
		$a = $this->_replace_all_html_sources_(&$a);
		if (($QTPLCACHE)&&($QTPLPRECACHE))
			$a = $this->_replace_all_precached_src_(&$a);
	}
	return $a;
}


//========================
//      quickPrint
//========================
function quickPrint($part = ""){
	if ($part=="") $part = $this->BlockName;
	print($this->quickText($part));
	//flush();
}

//========================
//      parse
//========================
function parse($part = ""){
	if ($part=="") $part = $this->BlockName;
	return $this->_parse_($part);
}
//========================
//  AssignLocal & Assign     
//========================
function AssignLocal($part, $name, $val){
	return $this->_assignLocal_(&$part,&$name,&$val);
}

function Assign($name, $val){
	return $this->_assign_(&$name, &$val);
}

//========================
//      DefaultBlocksValue     
//========================
function DefaultBlocksValue($value=""){
	return $this->_assign_($__DEFAULTS_VALUES__.".".$__DEFAULTVALUENAME__,$value);
}

function DefaultBlockValue($part, $value=""){
	return $this->_assignLocal_($part,$__DEFAULTS_VALUES__.".".$__DEFAULTVALUENAME__,$value);
}

//========================
//      Reset
//========================
function Reset($part=""){
	if ($part=="") $part = $this->BlockName;
	return $this->_reset_($part);
}

//========================
//      SetAutoincrement
//========================
function SetAutoincrement($part, $value=1, $incr=1){
	return $this->_setautoincrement_($part,$value,$incr);
}

//========================
//      GetAutoincrement
//========================
function GetAutoincrement($part = ""){
	if ($part=="") $part = $this->BlockName;
}

//========================
//      Parsed
//========================
function Parsed($part = ""){
	if ($part=="") $part = $this->BlockName;
	return $this->_parsed_($part);
}

//========================
//      getError
//========================
function getError(){
   return ($this->Error == "")?0:$this->Error;
}


//========================
//      setError
//========================
function setError($error){
   $this->Error = $error;
   echo "DebugQT:".$error."<br>";
}



//===================================================================
//====== PRIVAT Functions ===========================================
//===================================================================
//========================
//===========

function _debug_($text){
	if ($this->isDebug){
		list($usec, $sec) = explode(" ",microtime());
		print("<br><b>Debug: [$usec:$sec]</b>&nbsp;".$text."\n");
	}
}
//-------------------------------------------------------------------
function _openFileFromTemplate_($filename){
$res = "";
	// don't need to use: file_exists($filename)
	if ($fh = fopen($filename,"r")){
		$res = fread ($fh, filesize ($filename));
		fclose($fh);
	}else{
		$res="";
		$this->setError("[_openFileFromTemplate_]:Cannot open file: ".$filename);
	}	
return $res;	
}

//---------------------------------------------------------------------
function _getBlockPart_(&$text, $part){
$patern_begin 	= $this->_start_tag_."[[:blank:]]*".$this->_begin_block_."[[:blank:]]*".$part."[[:blank:]]*".$this->_end_tag_;
$patern_end 	= $this->_start_tag_."[[:blank:]]*".$this->_end_block_."[[:blank:]]*".$part."[[:blank:]]*".$this->_end_tag_;
	if (eregi($patern_begin."(.*)".$patern_end, $text, $res)){
		return $res[1];
	}else{
		$this->setError("[_getBlockPart_]:Cannot find part: ".$part);
		return "";
	}
}//end _getBlockPart_

//---------------------------------------------------------------------
function _createChildrens_(){
$part="[a-z,0-9,\_]+";
$patern_begin 	= $this->_start_tag_."[[:blank:]]*".$this->_begin_block_."[[:blank:]]*(".$part.")[[:blank:]]*".$this->_end_tag_;
$allpatern = $patern_begin."(.*)".$patern_end;
	while((eregi("(".$patern_begin.")(.*)$",&$this->BlockTemplate,$res))&&(eregi("^(.*)".$this->_start_tag_."[[:blank:]]*".$this->_end_block_."[[:blank:]]*(".$res[2].")[[:blank:]]*".$this->_end_tag_,&$res[3], $res2))){
	 	// 	now $res2[1] is the content of the new block
		//		and $res[2] is the name ...		
		$this->BLOCKS[$res[2]] = new QuickTemplate(&$res2[1],$res[2], 1, ($this->level+1)); 
		$this->BlockTemplate = str_replace($res[1].$res2[0],"{".$this->__BLOCKS_VAR__.".".$res[2]."}", &$this->BlockTemplate);
	}//while

}

//---------------------------------------------------------------------
function _getVariables_(){
	$v = $this->BlockTemplate;
	while (eregi("[\{]([A-Z,0-9,\.,\_]+)[\}](.*)$",$v, $res)){
		$this->VARLIST[] = $res[1];
		$v = &$res[2];			
	}
}

//---------------------------------------------------------------------
function _get_base_(&$longname){
	$pospoint = strpos($longname,".");
	return substr($longname,0,($pospoint===false)?255:$pospoint);
}
//---------------------------------------------------------------------
function _get_queque_($longname){
	$pospoint = strpos($longname,".");
	if ($pospoint===false) return false;
	return substr($longname,$pospoint+1);
}
//---------------------------------------------------------------------
function _assignLocal_($part,$var,$val,$base=""){
	/*
	if ($base!=$this->BlockName){
		if ($this->_get_base_($part)!=$this->BlockName){
			$this->setError("[_assignLocal_]:Current block is ".$this->BlockName." not ".$this->_get_base_($part).". You cannot assign var in this block!");
			return 0;
		}
	}
	*/
	
	$queque=$this->_get_queque_(&$part);
	
	if (!($queque===false)){
	   $nextpart=$this->_get_base_(&$queque);
		if (gettype($this->BLOCKS[$nextpart])=="object"){
				return $this->BLOCKS[$nextpart]->_assignLocal_(&$queque,&$var,&$val,&$nextpart);
		}else{
			$this->setError("[_assignLocal_]: Assigning var $var to next block: $nextpart failed because block $nextpart is not an object!");
			return false;
		}
	}
	
	$vr = $var;
	$VRS = &$this->VARS;
	while($this->_get_queque_($vr)){
		$vbase = $this->_get_base_($vr);
		if ((!isset($VRS[$vbase]) && (gettype($VRS[$vbase])!="array") ) )
				$VRS[$vbase] = array();
		else if (gettype($VRS[$vbase])!="array"){	
			$this->setError("[_assignLocal_]: Cannot assign $var beacuse the var base name doesn't corespond to an array! $vr");
			return false;
		}	
		
		$VRS = &$VRS[$vbase];
		$vr = $this->_get_queque_($vr);
	}//while
	
		
	$VRS[$vr]=$val;
	return true;
}
//---------------------------------------------------------------------
function _assign_($name, $val){
	if ($this->_exists_var_(&$name))
		$this->_assignLocal_(&$this->BlockName,&$name,&$val);
	reset($this->BLOCKS);
	while(list($k, $v) = each($this->BLOCKS)){
		$this->BLOCKS[$k]->_assign_(&$name,&$val);
	}
}
//---------------------------------------------------------------------
function _exists_var_($varname){
	if (gettype($this->VARLIST)!="array") return false;
	reset($this->VARLIST);
	while(list($k,$v) = each($this->VARLIST))
			if ($v == $varname) return true;
	return false;
}
//---------------------------------------------------------------------
function &_quicktext_($part){
	return ($this->TEXT == "")?$this->DEFAULTTEXT:$this->TEXT;
}
//---------------------------------------------------------------------
function _assign_defaults_($var, $val){
		$this->VARS[$this->__DEFAULTS_VALUES__][$var] = &$val;
}
//---------------------------------------------------------------------
function _parse_($part){
	/* optimized
	if ($this->_get_base_($part)!=$this->BlockName){
		$this->setError("[_parse_]:Current block is ".$this->BlockName." not ".$this->_get_base_($part).". You cannot parse this block!");
		return 0;
	}
	*/
	
	$queque=$this->_get_queque_($part);
	
	if (!($queque===false)){
	   $nextpart=$this->_get_base_($queque);
		if (gettype($this->BLOCKS[$nextpart])=="object"){
				return $this->BLOCKS[$nextpart]->_parse_($queque);
		}else{
			$this->setError("[_parse_]: Parsing next block: $nextpart failed because block $nextpart is not an object!");
			return false;
		}
	}
	
	
	//getting values from subblocks
	if (gettype($this->BLOCKS)=="array"){
		reset($this->BLOCKS);
		while(list($k,$v) = each($this->BLOCKS)){
			$this->AssignLocal(&$this->BlockName,$this->__BLOCKS_VAR__.".".$k,&$this->BLOCKS[$k]->TEXT);
			$this->BLOCKS[$k]->Reset();
		}
	}
	
	//inserting vars ...
	//$tmp = implode('',$this->BlockTemplate);
	$tmp = $this->BlockTemplate;
	if (gettype($this->VARLIST)=="array"){
		reset($this->VARLIST);
		while(list($k, $v) = each($this->VARLIST)){
			$tmp = str_replace("{".$v."}",$this->_get_var_value_($v), $tmp);
		}
	}
	$vname = $this->__DEFAULTS_VALUES__.".".$this->__AUTOINCREMENT_NAME__;
	$this->LOCAUTOINCREMENT += $this->INCREMENT;
	$this->_assignlocal_(&$this->BlockName, &$vname, &$this->LOCAUTOINCREMENT);
	
	$this->TEXT .= $tmp;
	$this->PARSED=true;

}
//---------------------------------------------------------------------
function _get_var_value_($var, $flag=0){
	$vr = $var;
	$VRS = &$this->VARS;
	while($this->_get_queque_($vr)){
		$vbase = $this->_get_base_($vr);
		if (gettype($VRS[$vbase])=="array"){
			$VRS = &$VRS[$vbase];
			$vr = $this->_get_queque_($vr);
		}else{
			if ($flag==0)
				return $this->_get_var_value_($this->__DEFAULTS_VALUES__.".".$this->__VAR_NULL_STRING__.".".$var,1);
			else
			if ($flag==1)
				return $this->_get_var_value_($this->__DEFAULTS_VALUES__.".".$this->__NULL_STRING__,2);
			else
				return "";
		}
	}//while


	if (($VRS[$vr]=="")&&($flag==0))
		return $this->_get_var_value_($this->__DEFAULTS_VALUES__.".".$this->__VAR_NULL_STRING__.".".$var,1);
	else	
	 if (($VRS[$vr]=="")&&($flag==1))
	 	return $this->_get_var_value_($this->__DEFAULTS_VALUES__.".".$this->__NULL_STRING__,2);
	 else
		return $VRS[$vr];
}
//---------------------------------------------------------------------
function _reset_($part){
	if ($this->_get_base_($part)!=$this->BlockName){
		$this->setError("[_reset_]:Current block is ".$this->BlockName." not ".$this->_get_base_($part).". You cannot reset this block!");
		return 0;
	}
	
	$queque=$this->_get_queque_($part);
	
	if (!($queque===false)){
	   $nextpart=$this->_get_base_($queque);
		if (gettype($this->BLOCKS[$nextpart])=="object"){
				return $this->BLOCKS[$nextpart]->_reset_($queque);
		}else{
			$this->setError("[_reset_]: Reseting next block: $nextpart failed because block $nextpart is not an object!");
			return false;
		}
	}
	
	$this->PARSED=false;
	$this->TEXT="";
	return true;
}
//---------------------------------------------------------------------
function _parsed_($part){
	if ($this->_get_base_($part)!=$this->BlockName){
		$this->setError("[_parsed_]:Current block is ".$this->BlockName." not ".$this->_get_base_($part));
		return 0;
	}
	
	$queque=$this->_get_queque_($part);
	
	if (!($queque===false)){
	   $nextpart=$this->_get_base_($queque);
		if (gettype($this->BLOCKS[$nextpart])=="object"){
				return $this->BLOCKS[$nextpart]->_parsed_($queque);
		}else{
			$this->setError("[_parsed_]: Reseting next block: $nextpart failed because block $nextpart is not an object!");
			return false;
		}
	}
	
	return $this->PARSED;
}
//---------------------------------------------------------------------
function _setautoincrement_($part, $value, $incr){
	if ($this->_get_base_($part)!=$this->BlockName){
		$this->setError("[_setautoincrement_]:Current block is ".$this->BlockName." not ".$this->_get_base_($part));
		return 0;
	}
	
	$queque=$this->_get_queque_($part);
	
	if (!($queque===false)){
	   $nextpart=$this->_get_base_($queque);
		if (gettype($this->BLOCKS[$nextpart])=="object"){
				return $this->BLOCKS[$nextpart]->_setautoincrement_($queque, $value, $incr);
		}else{
			$this->setError("[_setautoincrement_]: Reseting next block: $nextpart failed because block $nextpart is not an object!");
			return false;
		}
	}
	$this->AUTOINCREMENT = $value;
	$this->INCREMENT = $incr;
	return true;
}
//---------------------------------------------------------------------
function _replace_all_html_sources_($new_text){	
	$tab = explode("{".$this->_html_source_, &$new_text);
	if(gettype($tab)=="array"){
		reset($tab);
		$new_text = "";
		$first=true;
		foreach($tab as $tpart){
		    if ($first){$first=false;$new_text=$tpart;continue;}
		    if (empty($tpart)) continue;
			$pos = strpos( $tpart, "}");
			if (!$pos) {
				$new_text .= $tpart; 
				continue;
			}
			$file = substr(&$tpart, 0, $pos);
			$file = str_replace("\"","",$file);
			$file = str_replace("'","",$file);
			//$file = str_replace(" ","",$file);
			$file = trim($file);
			/*if ((strpos($file," "))||(strpos($file,"\n"))){
				$new_text .= $tpart; 
				continue;
			}*/

			if ($fh = fopen($file, "r")){
        			$file_text = fread( $fh, 1000000);
			}else $file_text = "";
			
			$new_text .= $file_text.substr(&$tpart, $pos+1);
			
		}
	}	
	
return $new_text;
}
//---------------------------------------------------------------------
function _replace_all_tpl_sources_($new_text){	
	$tab = explode("{".$this->_tpl_source_, &$new_text);
	if(gettype($tab)=="array"){
		reset($tab);
		$new_text = "";
		$first=true;
		foreach($tab as $tpart){
		    if ($first){$first=false;$new_text=$tpart;continue;}
		    if (empty($tpart)) continue;
			$pos = strpos( $tpart, "}");
			if (!$pos) {
				$new_text .= $tpart; 
				continue;
			}
			$file = substr(&$tpart, 0, $pos);
			$file = str_replace("\"","",$file);
			$file = str_replace("'","",$file);
			//$file = str_replace(" ","",$file);
			$file = trim($file);
			/*if ((strpos($file," "))||(strpos($file,"\n"))){
				$new_text .= $tpart; 
				continue;
			}*/

			if ($fh = fopen($this->templatebasedir."/".$file, "r")){
        			$file_text = fread( $fh, 1000000);
			}else $file_text = "";
			
			$new_text .= $file_text.substr(&$tpart, $pos+1);
			
		}
	}	
	
return $new_text;
}

//---------------------------------------------------------------------
function _replace_all_precached_src_($new_text){
	$tab = explode("{".$this->_pre_chache_, &$new_text);
	if(gettype($tab)=="array"){
	        $fh = fopen($this->precachedfname, "r");
                if (!$fh) return $new_text;
		reset($tab);
		$new_text = "";
		$first=true;	
		foreach($tab as $tpart){
		    if ($first){$first=false;$new_text=$tpart;continue;}
		    if (empty($tpart)) continue;
			$pos = strpos( $tpart, "}");
			if ((!$pos)||($pos>15)) {			     
				$new_text .= $tpart; 
				continue;
			}
			
			$identificator = substr(&$tpart, 0, $pos);
			
			if ((strpos($identificator," "))||(strpos($identificator,"\n"))||(!ereg("^[0123456789]+$", $identificator))){
				$new_text .= $tpart;				 
				continue;
			}
					
			$filesize = substr($identificator,0,6)+1-1;
			$seek = substr($identificator,6)+1-1;
			fseek($fh,$seek);
        		$file_text = fread( $fh, $filesize);
			$new_text .= $file_text.substr(&$tpart, $pos+1);
			
		}
		fclose($fh);
	}//if
	
return $new_text;
}

//---------------------------------------------------------------------
//---------------------------------------------------------------------
// miscelaneous functions ...
function tree(){
    echo "<blockquote>\n";
    echo "<li>Block: <b>".$this->BlockName."</b>\n";
	if (gettype($this->BLOCKS)=="array"){
		reset($this->BLOCKS);
		while(list($k,$v) = each($this->BLOCKS)){
			$this->BLOCKS[$k]->tree();
		}
	}
	echo "</blockquote>\n";

}

//just for compatibility with older versions
function testtree(){
	$this->tree();
}
//---------------------------------------------------------------------
//---------------------------------------------------------------------
//---------------------------------------------------------------------
//---------------------------------------------------------------------
} // end of class

//======================================================================
// this is the end my friend !
//===============================
//===========================
//======================
//=================
?>
