<?php
//$Id: sfs_case_excel.php 9240 2018-05-04 03:25:04Z igogo $
require_once $SPREADSHEET_PATH."Excel/Writer.php";
require_once $INCLUDE_PATH."sfs_case_encoding.php";

class sfs_xls{

	var $xls;	//資料檔案物件
	var $titleText;	//資料表標題
	var $input_encoding;	//字集
	var $bold;	//粗字體
	var $size="12";	//字體大小
	var $fcolor="black";	//字體顏色
	var $font="Arial";	//字型
	var $border_no="";	//框線類型
	var $bcolor="black";	//框線顏色
	var $bwidth=1;	//框線粗細
	var $btop="";	//框線_頂線
	var $bleft="";	//框線_左線
	var $bright="";	//框線_右線
	var $bbottom="";	//框線_底線
	var $merge;	//合併欄位
	var $filename="book.xls";	//預設檔名
	var $sheet_no=1;	//目前的資料表序次
	var $cart;	//目前的資料表物件
	var $row_no=0;	//目前的資料列
	var $row_height=16.5;	//預設列高
	var $row_weight=8.38;	//預設欄寬
	var $mergeArr=array(); //合併儲存格陣列, 每個大儲存格以array($first_row, $first_col, $last_row, $last_col)紀錄
	var $rowText=array(); //欄名陣列
	var $items=array();	//寫入的資料
	var $f=array();	//format陣列

	function sfs_xls() {
		$this->xls =& new Spreadsheet_Excel_Writer();
	}

	function setTitle($title="") {
		if ($this->input_encoding) {
			$this->titleText=iconv("Big5",$this->input_encoding,$title);
		} else {
			$this->titleText=$title;
		}
	}

	function setRowText($arr=array()) {
		if (is_array($arr)) $this->rowText[]=$arr;
	}

	function setUTF8() {
		$this->input_encoding = "utf-8";
		$this->xls->setVersion(8,"utf-8");
	}

	function init_format() {
		$this->bwidth = "";
		$this->btop = "";
		$this->bleft = "";
		$this->bright = "";
		$this->bbottom = "";
		$this->merge = "";
	}

	function setBorderStyle($no) {
		if (!empty($no)) {
			$this->border_no=$no;
			switch ($no) {
				case 0:
					$this->setBorder();
					break;
				case 1:
					$this->bwidth=1;
					$this->setBorder(1,1,1,1);
					break;
				case 2:
					$this->bwidth=2;
					$this->setBorder(2,2,2,2);
					break;
				case 3:
				case 4:
				case 5:
				case 6:
					switch($no) {
						case 3:
							$bo=1;
							$bi=0;
							break;
						case 4:
							$bo=2;
							$bi=0;
							break;
						case 5:
							$bo=1;
							$bi=1;
							break;
						case 6:
							$bo=2;
							$bi=1;
							break;
					}

					//訂定各種邊框型式
					$this->setBorder($bo,$bi,$bo,$bi);
					$this->f[1]=$this->addFormat();
					$this->setBorder($bo,$bi,$bi,$bi);
					$this->f[2]=$this->addFormat();
					$this->setBorder($bo,$bi,$bi,$bo);
					$this->f[3]=$this->addFormat();
					$this->setBorder($bi,$bi,$bo,$bi);
					$this->f[4]=$this->addFormat();
					$this->setBorder($bi,$bi,$bi,$bi);
					$this->f[5]=$this->addFormat();
					$this->setBorder($bi,$bi,$bi,$bo);
					$this->f[6]=$this->addFormat();
					$this->setBorder($bi,$bo,$bo,$bi);
					$this->f[7]=$this->addFormat();
					$this->setBorder($bi,$bo,$bi,$bi);
					$this->f[8]=$this->addFormat();
					$this->setBorder($bi,$bo,$bi,$bo);
					$this->f[9]=$this->addFormat();
					break;
			}
		}
	}

	function setBorder($top=0,$bottom=0,$left=0,$right=0) {
		$this->btop=$top;
		$this->bleft=$left;
		$this->bright=$right;
		$this->bbottom=$bottom;
	}

	function addFormat() {
		$format =& $this->xls->addFormat();

		//Set font
		if (!$this->font) $this->font="Arial";
		$format->setFontFamily($this->font);

		//Set font size
		if (!$this->size) $this->size=12;
		$format->setSize($this->size);

		//Set font bold
		if ($this->bold) $format->setBold();

		//Set font color
		if (!$this->fcolor) $this->fcolor="black";
		if ($this->fcolor) $format->setColor($this->fcolor);

		//Check border color
		if (!$this->bcolor) $this->bcolor="black";

		//Check border width
		if (!$this->bwidth) $this->bwidth=1;

		//Set border top
		if ($this->btop!="") {
			$format->setTop($this->btop);
			$format->setTopColor($this->bcolor);
		}

		//Set border right
		if ($this->bright!="") {
			$format->setRight($this->bright);
			$format->setRightColor($this->bcolor);
		}

		//Set border left
		if ($this->bleft!="") {
			$format->setLeft($this->bleft);
			$format->setLeftColor($this->bcolor);
		}

		//Set border bottom
		if ($this->bbottom!="") {
			$format->setBottom($this->bbottom);
			$format->setBottomColor($this->bcolor);
		}

		//Set column merge
		if ($this->merge) {
			$format->setAlign('merge');
		}

		return $format;
	}

	function addSheet($sheetname="") {
		$this->row_no=0;
		if ($sheetname=="") {
			$sheetname="sheet".$this->sheet_no;
			$this->sheet_no++;
		}
		if ($this->input_encoding) $sheetname=iconv("Big5",$this->input_encoding,$sheetname);
		$this->cart =& $this->xls->addWorksheet($sheetname);
		if ($this->input_encoding) $this->cart->setInputEncoding($this->input_encoding);
	}

	function writeSheet($format="") {
		if ($format=="") $format=$this->addFormat();
		if ($this->cart) {
			// Set the row height
			$this->cart->setRow($this->row_no,$this->row_height);

			// Set the column width
			$this->cart->setColumn($this->row_no,3,$this->row_weight);

			// Set Title
			if ($this->titleText) {
				$this->cart->write($this->row_no,0,$this->titleText,$f);
				$this->row_no++;
			}

			if (is_array($this->rowText)) $this->items=array_merge($this->rowText,$this->items);

			while(list($k,$item)=each($this->items)) {
				// 處理特殊邊框樣式
				if ($this->border_no>2) {
					if ($this->row_no==0) {
						// 左右及上框線用粗線
						$r=0;
					} elseif ($k==count($this->items)) {
						// 左右及下框線用粗線
						$r=6;
					} else {
						//僅左右框線用粗線
						$r=3;
					}
				}

				//處理合併儲存格
				if (count($this->mergeArr)>0) {
					reset($this->mergeArr);
					while(list($k,$v)=each($this->mergeArr)) {
						$this->cart->setMerge($v[0], $v[1], $v[2], $v[3]);
					}
				}

				for ($col=0;$col<count($item);$col++) {
					// 處理特殊邊框樣式
					if ($this->border_no>2) {
						if ($col==0) {
							$f=$this->f[$r+1];
						} elseif ($col==(count($item)-1)) {
							$f=$this->f[$r+3];
						} else {
							$f=$this->f[$r+2];
						}
					} else {
						$f=$format;
					}
					if ($this->input_encoding) {
						$d=spec_uni($item[$col]);
						$d=iconv("Big5","UTF-8//IGNORE",$d);
					} else {
						$d=$item[$col];
					}
					$this->cart->writeString($this->row_no,$col,$d,$f);
				}
				// 處理列數加一
				$this->row_no++;
			}
		}
	}

	function writeFile() {
		if ($this->items) {
			$i=$this->items;
			while(list($sheetname,$this->items)=each($i)) {
				$this->addSheet($sheetname);
				$this->writeSheet();
			}
		}
	}

	function process() {
		$this->xls->send($this->filename);
		$this->xls->close();
	}
}
?>
