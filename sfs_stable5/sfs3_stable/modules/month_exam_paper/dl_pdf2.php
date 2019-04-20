<?php
// $Id: dl_pdf2.php 5310 2009-01-10 07:57:56Z hami $

require('../../include/sfs_case_chinese.php');

class PDF extends PDF_Chinese
{
	//Page header
	function Header($title)
	{
		global $title;
		$this->SetFont('Big5','B',12);
		//Title
		$this->MultiCell(0,10,$title,0);
		//Line break
		//$this->Ln(10);
	}

	//Page footer
	function Footer()
	{
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Big5','I',8);
		//Page number
		$this->Cell(0,10,'頁 '.$this->PageNo().'/{nb}',0,0,'C');
	}

	//Simple table
	function BasicTable($header,$data)
	{
		//Header
		$col_num=count($header);
		if($col_num>10) $this->SetFont('Big5','',10);
		//$col_width=round(170/$col_num,0);
		//每一個字元預設的寬度
		$col_width=1.8;
		//每個欄位預設的字元數
		$default_char=4;
		$i=0;
		$col="";
		foreach($header as $col){
			if(strlen($col)) {
				if(strlen($col)>8) $col_width_a[$i]=$col_width*8 ;
    			else $col_width_a[$i]=$col_width*strlen($col) ;//超過4個中文字就截斷
				//echo $col_width_a[$i]."---";
			}
			else $col_width_a[$i]=$col_width*$default_char ;
			$i++;
		}

		foreach($data as $row){
			$col="";
			$i=0;
			foreach($row as $col){
				if(strlen($col)) {
					if(strlen($col)>8) $new_col_width_a[$i]=$col_width*8;
					else $new_col_width_a[$i]=$col_width*strlen($col) ;//超過4個中文字就截斷
				}
				if($new_col_width_a[$i] > $col_width_a[$i]) $col_width_a[$i]=$new_col_width_a[$i];
				//echo $col_width_a[$i]."---";
				$i++;
			}
			//echo "<br>";
		}


		//print_r($col_width_a);
		$i=0;
		foreach($header as $col){
			//if(strlen($col)) $col_width_a[$i]=$col_width*strlen($col) ;
			//else $col_width_a[$i]=$col_width*$default_char ;
			if(strlen($col)>8) $col=substr($col,0,8);//超過4個中文字就截斷
 			$this->Cell($col_width_a[$i],7,$col,1,'','C');
			$i++;
		}
		$this->Ln();
		//Data
		$this->SetFont('Big5','',10);
		foreach($data as $row){
			$i=0;
			foreach($row as $col){
				if(strlen($col)>8) $col=substr($col,0,8);//超過4個中文字就截斷
				$this->Cell($col_width_a[$i],7,$col,1,'','C');
				$i++;
			}
			$this->Ln();
		}
	}

	//表格前的註解文字，每人不同
	function Comm1($comment1)
	{
		$this->SetFont('Big5','',10);
		if($comment1!=""){
			$this->MultiCell(0,10,$comment1,0);
			//$this->Ln(10);
		}
	}

	//表格後的註解文字，每人相同
	function Comm2($comment2)
	{
		$this->SetFont('Big5','',10);
		//Title
		$this->MultiCell(0,10,$comment2,0);
		//Line break
		$this->Ln(10);
	}

}


//title:文件的抬頭
//header:表格的第一列，一維陣列
//big_data:表格的內容，二維或三維陣列
function creat_pdf($title,$header,$data,$comment1="",$comment2=""){

/*這裡是解說範例
	//文件的抬題
	$title="文件的抬題";
	//表格的第一列，一維陣列
	$header=array("","星期一","星期二","星期三","星期四","星期五","星期六");
	//表格的內容，第二列之後，二維或三維陣列
	$dim=arrs($arr);
	//dim=2，總表，不需分頁
	//dim=3，每人一頁，要分頁
*/
	$dim=arrs($data);

	//產生pdf檔
	$pdf=new PDF();
	$pdf->Open();
	$pdf->AddBig5Font();
	$pdf->Header($title);
	$pdf->AliasNbPages();
	/*	若是每一個人一頁的資料則（也就是一個人結束後要換頁進行下一位資料的輸出）
		先將每一個人的資料備妥
		如data1表1號的個人成績單
		data2表2號的個人成績單
		依此類推
		在產出pdf檔時
		每一個人均應呼叫一次
		$pdf->AddPage();
		$pdf->SetFont('Big5','',12);
		$pdf->BasicTable($header,$data);
	*/
	if($dim==2){//總表
		$pdf->AddPage();
		$pdf->SetFont('Big5','',10);
		$pdf->Comm1($comment1);
		$pdf->BasicTable($header,$data);
		$pdf->Comm2($comment2);
	}elseif($dim==3){//每人一頁
		$k=0;
		foreach($data as $data_val){
			$pdf->AddPage();
			$pdf->SetFont('Big5','',10);
			$pdf->Comm1($comment1[$k]);
			$pdf->BasicTable($header,$data_val);
			$pdf->Comm2($comment2);
			$k++;
		}
	}
	$pdf->Output();

}

//判斷幾維陣列
function arrs($arr,$CC="0"){
	if(!is_array($arr)){
		//echo "<br>".$CC;
		return $CC;
	}else{
		$CC++;
		//echo "<br>".$CC;
		$arr=$arr[0];
		$CC=arrs($arr,$CC);
		return $CC;
	}
}
?>
