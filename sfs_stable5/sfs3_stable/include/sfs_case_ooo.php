<?php
// $Id: sfs_case_ooo.php 7771 2013-11-15 06:39:56Z smallduh $
require_once $INCLUDE_PATH."sfs_oo_zip2.php";

class sfs_ooo{

	var $ooo_path; //檔案目錄
	var $ooo; //OpenOffice.org 檔案
	var $items=array();	//寫入的資料
	var $content; //寫入的資料
	var $rowText=array(); //欄名陣列
	var $filename="book"; //預設檔名
	var $file_extname="ods"; //預設附檔名
	var $sheetname=""; //工作表名
	var $sheetno=1; //工作表數

	function sfs_ooo() {
		global $SFS_PATH;

		$this->ooo_path = $SFS_PATH."/include/ooo/ods";
		$this->ooo = new EasyZip;
		$this->ooo->setPath($this->ooo_path);
		if (is_dir($this->ooo_path)) {
			if ($dh = opendir($this->ooo_path)) {
				while (($file = readdir($dh)) !== false) {
					if($file=="." or $file==".." or $file=="content.xml"){
						continue;
					}elseif(is_dir($this->ooo_path."/".$file)){
						if($file=="Configurations2") $this->file_extname="ods";
						if ($dh2 = opendir($this->ooo_path."/".$file)) {
							while (($file2 = readdir($dh2)) !== false) {
								if($file2=="." or $file2==".."){
									continue;
								}else{
									$data = $this->ooo->read_file($this->ooo_path."/".$file."/".$file2);
									$this->ooo->add_file($data,$file."/".$file2);
								}
							}
							closedir($dh2);
						}
					}else{
						$data = $this->ooo->read_file($this->ooo_path."/".$file);
						$this->ooo->add_file($data,$file);
					}
				}
				closedir($dh);
			}
		}
	}

	function setRowText($arr=array()) {

		if (is_array($arr)) $this->rowText[]=$arr;
	}

	function addSheet($sheetname="") {

		if ($sheetname=="") {
			$this->sheetname="工作表".$this->sheetno;
		} else {
			$this->sheetname=$sheetname;
		}
		$this->sheetno++;
	}

	function writeSheet(){

		if (is_array($this->rowText)) $this->items=array_merge($this->rowText,$this->items);
		$line=1;
		$last_line=count($this->items);
		$content="";
		reset($this->items);
		while(list($k,$v)=each($this->items)) {
			if ($line==1) {
				$colnum=count($v);
				$content .= '<table:table-row table:style-name="ro1">';
				$col=1;
				$last_col=count($v);
				reset($v);
				while(list($kk,$vv)=each($v)) {
					if ($col==1) {
						$sn='ce1';
					} elseif ($col==$last_col) {
						$sn='ce7';
					} else {
						$sn='ce4';
					}
					$content .= '<table:table-cell table:style-name="'.$sn.'" office:value-type="string"><text:p>'.$vv.'</text:p></table:table-cell>';
					$col++;
				}
				$content .= '</table:table-row>';
			} elseif ($line==$last_line) {
				$content .= '<table:table-row table:style-name="ro2">';
				$col=1;
				$last_col=count($v);
				reset($v);
				while(list($kk,$vv)=each($v)) {
					if ($col==1) {
						$sn='ce3';
					} elseif ($col==$last_col) {
						$sn='ce9';
					} else {
						$sn='ce6';
					}
					$content .= '<table:table-cell table:style-name="'.$sn.'" office:value-type="string"><text:p>'.$vv.'</text:p></table:table-cell>';
					$col++;
				}
				$content .= '</table:table-row>';
			} else {
				$content .= '<table:table-row table:style-name="ro2">';
				reset($v);
				while(list($kk,$vv)=each($v)) {
					$content .= '<table:table-cell office:value-type="string"><text:p>'.$vv.'</text:p></table:table-cell>';
				}
				$content .= '</table:table-row>';
			}
			$line++;
		}
		$this->content .= '
<table:table table:name="'.$this->sheetname.'" table:style-name="ta1" table:print="false">
<table:table-column table:style-name="co1" table:default-cell-style-name="ce2"/>
<table:table-column table:style-name="co1" table:number-columns-repeated="'.($colnum-2).'" table:default-cell-style-name="ce5"/>
<table:table-column table:style-name="co1" table:default-cell-style-name="ce8"/>';
		$this->content .= $content.'</table:table>';
	}

	function writeFile() {
		if ($this->items) {
			$i=$this->items;
			reset($i);
			while(list($sheetname,$this->items)=each($i)) {
				$this->addSheet($sheetname);
				$this->writeSheet();
			}
		}
	}

	function process(){

		//檔頭
		$con_head = '<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0">
<office:scripts/>
<office:font-face-decls>
<style:font-face style:name="AR PL Mingti2L Big5" svg:font-family="&apos;AR PL Mingti2L Big5&apos;" style:font-pitch="variable"/>
<style:font-face style:name="Nimbus Sans L1" svg:font-family="&apos;Nimbus Sans L&apos;" style:font-pitch="variable"/>
<style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="swiss" style:font-pitch="variable"/>
<style:font-face style:name="Nimbus Sans L" svg:font-family="&apos;Nimbus Sans L&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/>
<style:font-face style:name="Arial Unicode MS" svg:font-family="&apos;Arial Unicode MS&apos;" style:font-family-generic="system" style:font-pitch="variable"/>
<style:font-face style:name="Tahoma" svg:font-family="Tahoma" style:font-family-generic="system" style:font-pitch="variable"/>
</office:font-face-decls>
<office:automatic-styles>
<style:style style:name="co1" style:family="table-column">
<style:table-column-properties fo:break-before="auto" style:column-width="1.5cm"/>
</style:style>
<style:style style:name="ro1" style:family="table-row">
<style:table-row-properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true"/>
</style:style>
<style:style style:name="ro2" style:family="table-row">
<style:table-row-properties style:row-height="0.526cm" fo:break-before="auto" style:use-optimal-row-height="true"/>
</style:style>
<style:style style:name="ta1" style:family="table" style:master-page-name="Default">
<style:table-properties table:display="true" style:writing-mode="lr-tb"/>
</style:style>
<style:style style:name="ce1" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border-bottom="0.018cm solid #000000" fo:border-left="0.088cm solid #000000" fo:border-right="0.018cm solid #000000" fo:border-top="0.088cm solid #000000"/>
</style:style>
<style:style style:name="ce2" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border-bottom="0.018cm solid #000000" fo:border-left="0.088cm solid #000000" fo:border-right="0.018cm solid #000000" fo:border-top="0.018cm solid #000000"/>
</style:style>
<style:style style:name="ce3" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border-bottom="0.088cm solid #000000" fo:border-left="0.088cm solid #000000" fo:border-right="0.018cm solid #000000" fo:border-top="0.018cm solid #000000"/>
</style:style>
<style:style style:name="ce4" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border-bottom="0.018cm solid #000000" fo:border-left="0.018cm solid #000000" fo:border-right="0.018cm solid #000000" fo:border-top="0.088cm solid #000000"/>
</style:style>
<style:style style:name="ce5" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border="0.018cm solid #000000"/>
</style:style>
<style:style style:name="ce6" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border-bottom="0.088cm solid #000000" fo:border-left="0.018cm solid #000000" fo:border-right="0.018cm solid #000000" fo:border-top="0.018cm solid #000000"/>
</style:style>
<style:style style:name="ce7" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border-bottom="0.018cm solid #000000" fo:border-left="0.018cm solid #000000" fo:border-right="0.088cm solid #000000" fo:border-top="0.088cm solid #000000"/>
</style:style>
<style:style style:name="ce8" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border-bottom="0.018cm solid #000000" fo:border-left="0.018cm solid #000000" fo:border-right="0.088cm solid #000000" fo:border-top="0.018cm solid #000000"/>
</style:style>
<style:style style:name="ce9" style:family="table-cell" style:parent-style-name="Default">
<style:table-cell-properties fo:border-bottom="0.088cm solid #000000" fo:border-left="0.018cm solid #000000" fo:border-right="0.088cm solid #000000" fo:border-top="0.018cm solid #000000"/>
</style:style>
</office:automatic-styles>
<office:body>
<office:spreadsheet>';

		//檔尾
		$con_foot = '
</office:spreadsheet>
</office:body>
</office:document-content>';

		$con_head =iconv("Big5","UTF-8//IGNORE",$con_head);
		$con_body =iconv("Big5","UTF-8//IGNORE",$this->content);
		$con_foot =iconv("Big5","UTF-8//IGNORE",$con_foot);

		$replace_data = $con_head.$con_body.$con_foot;

		$this->ooo->add_file($replace_data,"content.xml");

		if($this->file_extname!="ods") $this->file_extname="sxc";
		$filename=$this->filename.".".$this->file_extname;

		//產生 zip 檔
		$sss = $this->ooo->file();

		//以串流方式送出檔案
		if(strpos($_SERVER['HTTP_USER_AGENT'] , 'MSIE') || strpos($_SERVER['HTTP_USER_AGENT'] , 'Opera'))
			$mimeType="application/x-download";
		elseif($this->file_extname=="ods")
			$mimeType="application/vnd.oasis.opendocument.spreadsheet";
		else
			$mimeType="application/vnd.sun.xml.calc";
		header("Content-disposition: attachment; filename=$filename");
		header("Content-type: $mimeType");
		echo $sss;
		exit;
		return;
	}
}
?>
