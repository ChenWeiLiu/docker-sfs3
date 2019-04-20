<?php
// $Id: dl_pdf.php 5310 2009-01-10 07:57:56Z hami $

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
	function BasicTable($header,$data,$ht,$wd)
	{
		//print_r($ht);
		//Header
		$col_num=count($header);
		if($col_num>10) $this->SetFont('Big5','',10);
		$i=0;
		$this->SetTextColor(0,0,255);
		foreach($header as $col){
 			$this->Cell($wd,7,$col,1,'','C');
			$i++;
		}
		$this->Ln();
		//Data
		$this->SetFont('Big5','',10);
		$this->SetTextColor(0,0,0);
		$j=1;
		foreach($data as $row){
			$i=0;
			foreach($row as $col){
				$col=trim($col);
				//$aaa=$this->Write(6, "sdf\ndfdf\nfdfdf\nfdsfd");
				$this->new_MultiCell($wd,$ht[$j],$col,1,'C','0');
				$i++;
			}
			$this->Ln();
			$j++;
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
function creat_pdf($title,$header,$data,$comment1="",$comment2="",$ht,$wd){
	global $UPLOAD_PATH,$SFS_PATH_HTML,$UPLOAD_URL;
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
	$pdf=new PDF('P','mm','A4');
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
		reset($header);
		reset($data);
		$i=0;
		$cn=intval(180/$wd);
		foreach($header as $Kh => $Vh){
			if(intval($Kh/$cn)==$i) {
				$new_header[$i][]=$Vh;
			}
			if(($Kh%$cn)==($cn-1)) $i++;
		}
		$m=0;
		foreach($data as $Kd => $Vd){
			$n=0;
			foreach($Vd as $a => $b){
				if(intval($a/$cn)==$n) {
					$new_data[$n][$m][]=$b;
				}
				if(($a%$cn)==($cn-1)) $n++;
			}
			$m++;
		}

		for($j=0;$j<=$i;$j++){
			$pdf->AddPage();
			$pdf->SetFont('Big5','',10);
			$pdf->BasicTable($new_header[$j],$new_data[$j],$ht,$wd);
		}
		//$pdf->BasicTable($header,$data);
		//$pdf->Comm2($comment2);
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
    if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")) {
           if(!is_dir($UPLOAD_PATH."stick_PDF")) mkdir ($UPLOAD_PATH."stick_PDF", 0700);
           //先刪除以前做的PDF檔
           $handle=opendir($UPLOAD_PATH."stick_PDF");
           while ($oldpdf = readdir($handle)) {
                   $oldpdf_arr=explode("_",$oldpdf);
                   if(($oldpdf_arr[0])==$_SESSION['session_tea_sn']) unlink ($oldpdf);
           }
            closedir($handle);
            $file = tempnam ($UPLOAD_PATH."stick_PDF", $_SESSION['session_tea_sn']."_").".pdf";
            $pdf->Output($file);

            head("自訂成績單");
                    echo "<a href='http://".$_SERVER["SERVER_ADDR"].$UPLOAD_URL."stick_PDF/".basename($file)."'>下載PDF</a>";
            foot();
        }else $pdf->Output();
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
