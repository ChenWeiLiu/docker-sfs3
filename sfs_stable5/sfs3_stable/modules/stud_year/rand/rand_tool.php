<?php
// $Id: rand_tool.php 5310 2009-01-10 07:57:56Z hami $
function backe($value= "BACK",$type="1"){
	$ary[1]="history.back()";
	$ary[2]="window.close()";
	$add=$ary[$type];
	echo "<html><head>
<meta http-equiv='Content-Type' content='text/html; Charset=Big5'>
<title>岿粇癟</title>
<META NAME='ROBOTS' CONTENT='NOARCHIVE'>
<META NAME='ROBOTS' CONTENT='NOINDEX, NOFOLLOW'>
<META HTTP-EQUIV='Pargma' CONTENT='no-cache'>
<style type='text/css'>
#Box {background: transparent; margin:1px;}
.xtop, .xbottom {display:block; background:transparent; font-size:1px;}
.xb1, .xb2, .xb3, .xb4 {display:block; overflow:hidden;}
.xb1, .xb2, .xb3 {height:1px;}
.xb2, .xb3, .xb4 {background:#e8eefa; border-left:1px solid #3366cc; border-right:1px solid #3366cc;}
.xb1 {margin:0 5px; background:#3366cc;}
.xb2 {margin:0 3px; border-width:0 2px;}
.xb3 {margin:0 2px;}
.xb4 {height:2px; margin:0 1px;}
.xboxcontent {display:block; background:#e5ecf9; border:0 solid #3366cc; border-width:0 1px;}
.xb9 {width:300; }
</style>
<center style='margin-top: 120px'>
<div id='Box' class='xb9'>
<b class='xtop'><b class='xb1'></b><b class='xb2'></b><b class='xb3'></b><b class='xb4'></b></b>
<div class='xboxcontent'><table width=100%><tr align=center>
<td><b style='color:red'>岿粇癟</b><br>
<b>$value</b><br><br>
<input type=button value='膥尿' onclick='$add' style='font-size:14pt;color:red;'>
</td></tr>
</table>
</div>
<b class='xbottom'><b class='xb4'></b><b class='xb3'></b><b class='xb2'></b><b class='xb1'></b></b>
</div>
</center></body></html>";
exit;

}
//把计肚浪琩
function chkStr($K,$def='') {
	if ($def==''  && $_GET[$K]=='' && $_POST[$K]=='') return;
	if ($def!=''  && $_GET[$K]=='' && $_POST[$K]=='') return $def;
	$$K=($_GET[$K]=='') ? $_POST[$K]:$_GET[$K];
	if (!is_string($$K)) return; 
	return $$K;
}



class Cla_rand{
	var $data;//﹍戈
	var $Num;//絪痁计
	var $No;//ヘ玡痁夹
	var $st_boy;//╧ネ
	var $st_girl;//ネ
	var $beStu;//︽熬畉ネbehavior;2
	var $spStu;//疭ネ3
	var $tolBoy;//羆╧ネ计
	var $tolGirl;//羆ネ计
	var $Tol;//$Tol[痁][boy]
	var $Cla;//穝絪痁
	
	function run() {
		$this->gData();
		//$this->$No='XX';
		//
		$this->run_sp();
		$this->run_behavior();
		$NewClass=$this->run_all();
		return $NewClass;
	}
//--1.疭ネ睹计絪痁-------
	function run_sp() {
 		if (count($this->spStu)==0) return ; 
		shuffle($this->spStu);
		$clano=1; 
		foreach ($this->spStu as $sk=>$stu){ 
			$this->Cla[$clano][]=$stu;
			//疭ネ,╄╧
			$this->Tol[$clano][boy]++;//疭ネ,╧ネ计
			$this->Tol[$clano][girl]++;//疭ネ,ネ计 
			($clano==$this->Num) ? $clano=1:$clano++; 
		unset($stu);
		} 
	///-------干霍厩ネ计(Ω干2,╧)------ 
		if ($clano != 1 ) { 
			for($clano;$clano <= $this->Num;$clano++){ 
				$this->Cla[$clano][]=end($this->st_boy); 
				$this->st_boy=array_slice($this->st_boy,0,-1); 
				$this->Tol[$clano][boy]++; //╧ネ计1
				$this->Cla[$clano][]=end($this->st_girl); 
				$this->st_girl=array_slice($this->st_girl,0,-1);
				$this->Tol[$clano][girl]++;//ネ计1 
				} 
			} 

	}
//--2.︽熬畉ネ睹计絪痁,跑癴-------
	function run_behavior() {
 		if (count($this->beStu)==0) return ; 
		shuffle($this->beStu);
		$clano=$this->Num;// $clano=1;
		//皌钵盽ネ,干ネ 
		foreach ($this->beStu as $sk=>$stu){ 
			$this->Cla[$clano][]=$stu;
			//疭ネ,╄╧
			if ($stu[stud_sex]=='1'){
				$this->Cla[$clano][]=end($this->st_girl); 
				$this->st_girl=array_slice($this->st_girl,0,-1);
					}
			else {
				$this->Cla[$clano][]=end($this->st_boy); 
				$this->st_boy=array_slice($this->st_boy,0,-1);
					}
			$this->Tol[$clano][boy]++;//疭ネ,╧ネ计
			$this->Tol[$clano][girl]++;//疭ネ,ネ计 
			// ($clano==$this->Num) ? $clano=1:$clano++;
			($clano==1) ? $clano=$this->Num:$clano--; 
			unset($stu);
		} 
	///-------干霍厩ネ计(Ω干2,╧)------ 
	//	if ($clano != 1 ) { 
		if ($clano != $this->Num ) {
//			for($clano;$clano <= $this->Num;$clano++){
			for($clano;$clano >= 1;$clano--){ 
				$this->Cla[$clano][]=end($this->st_boy); 
				$this->st_boy=array_slice($this->st_boy,0,-1); 
				$this->Tol[$clano][boy]++; //╧ネ计1
				$this->Cla[$clano][]=end($this->st_girl); 
				$this->st_girl=array_slice($this->st_girl,0,-1);
				$this->Tol[$clano][girl]++;//ネ计1 
				} 
			} 

	}
//3.ネ睹计
	function run_all() {
		$all_stu=array_merge($this->st_boy,$this->st_girl); 
		foreach  ($all_stu as $sk=>$stu ){ 
			$clano=($sk%$this->Num)+1; 
			$this->Cla[$clano][]=$stu;
			unset($stu);
		}
		//echo '<pre>';print_r($this->Cla);
		//////----俱瞶Θ痁皚,ゴ睹畒腹盢╧ネ玡-------/////// 
		for ($i=1;$i <= $this->Num;$i++){ 
			$this_boy='';$this_girl=''; 
			shuffle($this->Cla[$i]); 
			foreach($this->Cla[$i] as $stu){ 
				if ($stu[stud_sex]==1) $this_boy[]=$stu; 
				if ($stu[stud_sex]==2) $this_girl[]=$stu; 
				unset($stu);
				} 
			$OK[$i]=array_merge($this_boy,$this_girl);//盢╧ネ玡 
		}
		//echo '<pre>';print_r($OK);
		//////----恶痁の畒腹-----------------
 		for ($i=1;$i <= $this->Num;$i++){ 
			foreach($OK[$i] as $k=>$stu){
				$OK[$i][$k][ncla]=$i;
				$OK[$i][$k][nnum]=$k+1;	 
			} 
		}	
//		echo '<pre>';print_r($OK);

		return $OK; 
	}

//--盢戈秈︽だ琹-------
	function gData() {
		foreach ($this->data as $cla_id=> $cla){
			foreach ($cla as $stu){
				if ($stu[type]=='2'){$this->beStu[]=$stu;}//︽熬畉
				if ($stu[type]=='3'){$this->spStu[]=$stu;}//疭ネ
				//╧ネ
				if ($stu[type]=='1' && $stu[stud_sex]=='1')	$this->st_boy[]=$stu;
				if ($stu[type]=='1' && $stu[stud_sex]=='2')	$this->st_girl[]=$stu;
				($stu[stud_sex]=='1' && $stu[type]!='0')? $tolBoy++:$tolGirl++;
				unset($stu);
			}
		}	
		shuffle($this->st_boy);shuffle($this->st_girl);
	}
//---------

}//end class

