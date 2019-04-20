<?php
//$Id: chi_page2.php 5351 2009-01-20 00:39:21Z brucelyc $
#######################################################
###  Script By 二林網管  紀明村 95.01.15
#######################################################
/*
#######   -----用法
$size=10;//每頁10筆$tol,$size,$page='',$url=''
if($_GET[page]=='') {$page=0;}else{$page=$_POST[page];}
$Chi_page= new Chi_Page(總筆數,每頁筆數,目前頁數) ;方式1
$Chi_page= new Chi_Page(總筆數,每頁筆數,目前頁數,網址) ;方式2

echo $Chi_page->show_p();//方式1
echo $Chi_page->show_page();//方式2
*/

class Chi_Page {
	var $tol ;//總筆數
	var $page ;//目前頁數
	var $size ;//每頁筆數
	var $url;//連結網址
	var $sy='?';
//	var $txt=array(1=>"第一頁",2=>"上頁",3=>"下頁",4=>"最未頁");
//	var $txt=array(1=>'|&lt;',2=>'&lt;&lt;',3=>'&gt;&gt;',4=>'&gt;|');
	var $txt=array(1=>'&nbsp;|&lt;&nbsp;',2=>'&nbsp;&lt;&lt;&nbsp;',3=>'&nbsp;&gt;&gt;&nbsp;',4=>'&nbsp;&gt;|&nbsp;');

	function Chi_Page($tol,$size,$page,$url='') {
		($page=='') ? $this->page=0:$this->page=$page;
		if 	($url=='') {
			$this->url=$_SERVER[SCRIPT_NAME];
			$this->sy='?';
			}else{
			$this->url=$url;
			 (ereg ("?", $this->url) ) ? $this->sy='&': $this->sy='?';
			}

		$this->tol=$tol;
		$this->size=$size;

//		($size=='') ? die("無法操作Chi_Page class"):$this->size=$size ;
//		if ($this->tol==''|| $this->tol==0) die("沒有資料,無法操作 Chi_Page class");
//		$this->all_p=ceil($this->tol/$this->size);
		}
	
	#####################   跳頁函式  ###########################
	function show_page(){
		if ($this->tol=='' ||$this->tol==0 || $this->size==''|| $this->size==0 ){
			$tt=$this->button('目前無資料!').$this->button('第1頁').$this->button('上頁').$this->button('下頁').$this->button('最末頁');
			return $tt;
		}
		$tol=ceil($this->tol/$this->size);
		$now=$this->page;
		$URL=$this->url;
		($URL==$_SERVER[SCRIPT_NAME]) ? $ln='?':$ln='&';
		if ( $tol==1 ) return $this->button("共".$this->tol."筆資料").$this->button('第1頁').$this->button('上頁').$this->button('下頁').$this->button('最末頁');
		if ( $tol==2 ) {
			if ($now==0) $tt= $this->button('第1頁').$this->button('上頁').$this->button('下頁',$URL.$ln."page=1").$this->button('最末頁',$URL.$ln."page=1");
			if ($now==1) $tt= $this->button('第1頁',$URL.$ln."page=0").$this->button('上頁').$this->button('下頁').$this->button('最末頁');
			}
		if ( $tol>=3) {
			$tol2=$tol-1;
			if ($now==0) $tt=$this->button('第1頁').$this->button('上頁').$this->button('下頁',$URL.$ln."page=1").$this->button('最末頁',$URL.$ln."page=".$tol2);
			if ($now!=$tol2 && $now!=0) 
				$tt=$this->button('第1頁',$URL.$ln."page=0").
				$this->button('上頁',$URL.$ln."page=".($now-1)).$this->button('下頁',$URL.$ln."page=".($now+1)).$this->button('最末頁',$URL.$ln."page=".$tol2);
			if ($now==$tol2) 
				$tt= $this->button('第1頁',$URL.$ln.'page=0').
				$this->button('上頁',$URL.$ln."page=".($now-1)).$this->button('下頁').$this->button('最末頁');
		}
		$ss=$this->jump($URL,$ln,$tol,$now);
		Return $this->button("共 $this->tol 筆").$tt.$ss;
		
		}
	function jump($URL,$ln,$tol,$now){
		$ss="<select name='ch_page' size='1' class='small' onChange=\"location.href='".$URL.$ln."page='+this.options[this.selectedIndex].value;\"   style='border:2px; background-color:#E5E5E5; font-size:10pt;color:#A52A2A' >";
		for ($i=0;$i<$tol;$i++){
			($now==$i) ? $cc=" selected":$cc="";
			$ss.="<option value='$i' $cc>第".($i+1)."頁</option>\n";
		}
		$ss.="</select>";
		return $ss;
		}
		
		
	function button($word,$URL=''){
		$tt="<input type='button' value='$word'  style=' border:1px;font-size:10pt' ";
		($URL=='') ? $tt.=" disabled  >":$tt.=" onclick=\"location.href='$URL'\"  >";
		return $tt;	
	}

	#####################   跳頁函式  ###########################
	function show_p(){
		if ($this->tol=='' ||$this->tol==0 || $this->size==''|| $this->size==0 ){
			$tt=$this->Wd('目前無資料!').$this->Wd(1).$this->Wd(2).$this->Wd(3).$this->Wd(4);
			return $tt;
		}
		$tol=ceil($this->tol/$this->size);
		$now=$this->page;
		$URL=$this->url;
		($URL==$_SERVER[SCRIPT_NAME]) ? $ln='?':$ln='&';
		if ( $tol==1 ) return $this->Wd("共".$this->tol."筆資料").$this->Wd(1).$this->Wd(2).$this->Wd(3).$this->Wd(4);
		if ( $tol==2 ) {
			if ($now==0) $tt= $this->Wd(1).$this->Wd(2).$this->Wd(3,$URL.$ln."page=1").$this->Wd(4,$URL.$ln."page=1");
			if ($now==1) $tt= $this->Wd(1,$URL.$ln."page=0").$this->Wd(2,$URL.$ln."page=0").$this->Wd(3).$this->Wd(4);
			}
		if ( $tol>=3) {
			$tol2=$tol-1;
			if ($now==0) $tt=$this->Wd(1).$this->Wd(2).$this->Wd(3,$URL.$ln."page=1").$this->Wd(4,$URL.$ln."page=".$tol2);
			if ($now!=$tol2 && $now!=0) 
				$tt=$this->Wd(1,$URL.$ln."page=0").
				$this->Wd(2,$URL.$ln."page=".($now-1)).$this->Wd(3,$URL.$ln."page=".($now+1)).$this->Wd(4,$URL.$ln."page=".$tol2);
			if ($now==$tol2) 
				$tt= $this->Wd(1,$URL.$ln.'page=0').$this->Wd(2,$URL.$ln."page=".($now-1)).$this->Wd(3).$this->Wd(4);
		}
		$ss=$this->jump($URL,$ln,$tol,$now);
		Return $this->Wd("共 $this->tol 筆").$tt.$ss;
		
		}
	function Wd($key,$URL=''){
		if(array_key_exists($key,$this->txt)) {
			($URL=='') ? $tt=$this->txt[$key]:$tt="<A HREF='$URL'>".$this->txt[$key]."</A>";
		} else {
			($URL=='') ? $tt=$key:$tt="<A HREF='$URL'>".$key."</A>";
		}
		return $tt;
	}

}

?>
