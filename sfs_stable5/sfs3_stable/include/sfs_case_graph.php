<?php

//$Id: sfs_case_graph.php 7771 2013-11-15 06:39:56Z smallduh $
$path="";
while(!is_file($path."jpgraph/jpgraph.php")) {
	$path="../".$path;
}

include_once $path."jpgraph/jpgraph.php";

class sfs_nbar{

	var $datay=array();	//資料
	var $graph="";
	var $ybplot=array();
	var $mfont=FF_CHINESE;	//主標題字型
	var $mfont_size=14;	//主標題字大小
	var $lfont=FF_CHINESE;	//圖例字型
	var $xfont=FF_CHINESE;	//x軸字型
	var $xlfont=FF_CHINESE;	//x軸標籤字型
	var $xfont_size=12;	//x軸字大小
	var $xlfont_size=10;	//x軸標籤字大小
	var $yfont=FF_CHINESE;	//y軸字型
	var $yfont_size=12;	//y軸字大小
	var $ybcolor=array("#9999ef","#993366","yellow","green","#b8860b");	//bar顏色陣列
	var $yfcolor=array("#9999ef","#993366","black","green","#b8860b");	//bar顏色陣列
	var $num_str="%d";	//數字格式

	function sfs_nbar($gx=640,$gy=480) {
		global $path;

		include_once $path."jpgraph/jpgraph_bar.php";
		$this->graph=new Graph($gx,$gy);
		$this->graph->SetBox();
		$this->graph->img->SetMargin(70,90,70,70);
		$this->set_mfont();
		$this->set_lfont();
		$this->set_lshadow();
		$this->set_margincolor();
		$this->set_lpos();
		$this->graph->SetScale("textlin");
		$this->graph->legend->SetLeftMargin(0);

		//設定x軸
		$this->set_xfont();
		$this->graph->xaxis->SetTitleMargin(30);
		$this->graph->xaxis->SetLabelMargin(12);
		$this->graph->xaxis->SetLabelAlign('center','center');

		//設定y1軸
		$this->set_yfont();
		$this->graph->yaxis->SetTitleSide(SIDE_LEFT);
		$this->graph->yaxis->title->Align('center','top');
		$this->graph->yaxis->SetTitleMargin(50);
		$this->graph->yaxis->scale->SetGrace(0);

		//設定y軸格線顏色
		$this->graph->ygrid->SetColor('gray','lightgray');
	}

	//設定為轉90度
	function set90() {
		$this->graph->Set90AndMargin(90,70,70,70);
		$this->graph->xaxis->SetTitleMargin(60);
		$this->graph->xaxis->SetLabelMargin(5);
		$this->graph->xaxis->title->SetAngle(90);
		$this->graph->xaxis->SetLabelAlign('right','center');
		$this->graph->yaxis->SetPos('max');
		$this->graph->yaxis->SetTitleSide(SIDE_RIGHT);
		$this->graph->yaxis->SetLabelSide(SIDE_RIGHT);
		$this->graph->yaxis->SetTickSide(SIDE_LEFT);
		$this->graph->yaxis->SetLabelMargin(20);
		$this->graph->yaxis->SetTitleMargin(40);
		$this->graph->yaxis->title->SetAngle(0);
		$this->graph->yaxis->scale->SetGrace(6);
	}

	//設定資料
	function set_y($y=array()) {
		reset($y);
		while(list($i,$v)=each($y)) {
			if (count($v)>0) {
				$this->ybplot[$i]=new BarPlot($v);
			}
		}
	}

	//設定背景顏色
	function set_margincolor($c="white") {
		$this->graph->SetMarginColor($c);
		$this->graph->legend->SetFillColor($c);	//圖例底色
	}

	//設定主標題
	function set_mtitle($t="") {
		$this->graph->title->Set($t);
	}

	//設定主標題字型
	function set_mfont($f,$s) {
		if ($f) $this->mfont=$f;
		if ($s) $this->mfont_size=$s;
		$this->graph->title->SetFont($this->mfont,FS_NORMAL,$this->mfont_size);
	}

	//設定圖例字型
	function set_lfont($f) {
		if ($f) $this->lfont=$f;
		$this->graph->legend->SetFont($this->lfont);
	}

	//設定圖例相對位置
	function set_lpos($x=0.01,$y=0.5,$align="right",$valign="center") {
		$this->graph->legend->SetPos($x,$y,$align,$valign);
	}

	//設定圖例陰影
	function set_lshadow($e=false) {
		$this->graph->legend->SetShadow($e);
	}

	//設定x軸標題
	function set_xtitle($t="",$a="center") {
		$this->graph->xaxis->SetTitle($t,$a);
	}

	//設定x軸資料
	function set_xlabel($d="") {
		if (is_array($d)) {
			$this->graph->xaxis->SetTickLabels($d);
		}
	}

	//設定x軸資料角度
	function set_xlableangel($d=0) {
		if ($d) {
			$this->graph->xaxis->SetLabelAngle($d);
		}
	}

	//設定x軸標題字型
	function set_xfont($f,$s) {
		if ($f) $this->xfont=$f;
		if ($s) $this->xfont_size=$s;
		$this->graph->xaxis->title->SetFont($this->xfont,FS_NORMAL,$this->xfont_size);
	}

	//設定x軸標籤字型
	function set_xlfont($f,$s) {
		if ($f) $this->xlfont=$f;
		if ($s) $this->xlfont_size=$s;
		$this->graph->xaxis->SetFont($this->xlfont,FS_NORMAL,$this->xlfont_size);
	}

	//設定y軸標題
	function set_ytitle($t="",$a="center") {
		$this->graph->yaxis->SetTitle($t,$a);
	}

	//設定y軸標題字型
	function set_yfont($f,$s) {
		if ($f) $this->yfont=$f;
		if ($s) $this->yfont_size=$s;
		$this->graph->yaxis->title->SetFont($this->yfont,FS_NORMAL,$this->yfont_size);
	}

	//設定長條顏色
	function set_ybcolor($c) {
		if (is_array($c)) $this->ybcolor=$c;
		while(list($i,$v)=each($this->ybcolor)) {
			if ($v) $this->ybcolor[$i]=$v;
			if (is_object($this->ybplot[$i])) {
				$this->ybplot[$i]->SetColor($v);
				$vv=$v."@0.3";
				$this->ybplot[$i]->SetFillColor($vv);
				$vv=($this->yfcolor[$i])?$this->yfcolor[$i]:$v;
				$this->ybplot[$i]->value->SetColor($vv);
			}
		}
	}

	//設定圖例標題
	function set_ltitle($t) {
		if (is_array($t)) {
			reset($t);
			while(list($i,$v)=each($t)) {
				if ($this->ybplot[$i]) {
					$this->ybplot[$i]->SetLegend($v);
				}
			}
		}
	}

	//設定長條圖顯示數值格式
	function set_shownum($s) {
		if ($s) $this->num_str=$s;
		while(list($i,$v)=each($this->ybplot)) {
			if ($this->ybplot[$i]) {
				$this->ybplot[$i]->value->Show();
				$this->ybplot[$i]->value->SetFormat($this->num_str);
			}
		}
	}

	function draw() {
		$this->ynplot=new GroupBarPlot($this->ybplot);
		$this->ynplot->SetWidth(0.8);
		$this->set_ybcolor();
		$this->set_shownum();
		$this->graph->Add($this->ynplot);
		$this->graph->Stroke();
	}
}

class sfs_pie3d{
	var $data=array();	//圓餅圖資料
	var $graph="";
	var $mfont=FF_CHINESE;	//主標題字型
	var $mfont_size=14;	//主標題字大小
	var $lfont=FF_CHINESE;	//圖例字型
	var $pie;
	var $pfont=FF_CHINESE;	//圓餅圖字型
	var $num_str="%d";	//單位格式
	var $num_unit="人";	//單位文字

	function sfs_pie3d($gx=640,$gy=480) {
		global $path;

		include_once $path."jpgraph/jpgraph_pie.php";
		include_once $path."jpgraph/jpgraph_pie3d.php";
		$this->graph=new PieGraph($gx,$gy,'auto');
		$this->graph->legend->SetFillColor("white");	//圖例底色
		$this->set_mfont();
		$this->set_lfont();
		$this->set_lpos();
		$this->set_lshadow();
	}

	//設定主標題
	function set_mtitle($t="") {
		$this->graph->title->Set($t);
	}

	//設定主標題字型
	function set_mfont($f,$s) {
		if ($f) $this->mfont=$f;
		if ($s) $this->mfont_size=$s;
		$this->graph->title->SetFont($this->mfont,FS_NORMAL,$this->mfont_size);
	}

	//設定圖例字型
	function set_lfont($f) {
		if ($f) $this->lfont=$f;
		$this->graph->legend->SetFont($this->lfont);
	}

	//設定圖例相對位置
	function set_lpos($x=0.02,$y=0.86) {
		$this->graph->legend->SetPos($x,$y);
	}

	//設定圖例陰影
	function set_lshadow($e=false) {
		$this->graph->legend->SetShadow($e);
	}

	//設定資料
	function set_data($d=array()) {
		if (count($d)>0) {
			$this->data=$d;
			$this->pie=new PiePlot3D($this->data);
		}
	}

	//設定圖例標題
	function set_ltitle($t) {
		if ($this->pie) {
			$this->pie->SetLegends($t);
		}
	}

	//設定圓餅圖字型
	function set_pfont($f) {
		if ($f) $this->pfont=$f;
		if ($this->pie) {
			$this->pie->value->SetFont($this->pfont);
		}
	}

	//設定圓餅圖中心位置
	function set_ppos($x=0.5,$y=0.43) {
		if ($this->pie) {
			$this->pie->SetCenter($x,$y);
		}
	}

	//設定圓餅圖顯示資料單位
	function set_shownum($s,$u) {
		if ($s) $this->num_str=$s;
		if ($u) $this->num_unit=$u;
		if ($this->pie) {
			$this->pie->value->SetFormat($this->num_str." ".$this->num_unit);
		}
	}

	function draw() {
		$this->set_pfont();
		$this->pie->SetLabelType(1);
		$this->set_ppos();
		$this->graph->Add($this->pie);
		$this->graph->Stroke();
//		$this->graph->StrokeCSIM();
	}
}
?>
