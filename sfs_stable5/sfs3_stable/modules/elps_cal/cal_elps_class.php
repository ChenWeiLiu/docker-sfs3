<?php
//$Id: cal_elps_class.php 8513 2015-09-02 05:18:04Z chiming $
//物件class
class cal_elps{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $seme;//學期
	var $seme_ary;//選中的學期陣列
	var $all_seme;//所有學期資料陣列,下拉式選單用
	var $unit;//標題(單位或處室)
	var $event;//所有行事
	var $WK;//含週別與行事的資料陣列
	var $cal_name;//XX縣XX國民小學XX學年度第X學期 校務行事曆



	//擷取資料
	function get_all_event(){
		$SQL="select * from cal_elps where  syear='{$this->seme}' order by  week asc,unit ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		//$this->all=$arr;//return $arr;
		foreach ($arr as $ary){
		   $week=$ary[week];
		   //$this->event[$week][]=$ary;
			$this->WK[$week][event][]=$ary;
			}
	
	}
	//新增
	function get_use_set(){
		if ($this->seme_ary=='') return;
			$arr=$this->seme_ary;
			//$this->unit=split("@@@",$arr[unit]);//單位陣列
			$this->unit=explode("@@@",$arr[unit]);//單位陣列
			$unit_nu=count($this->unit);//取單位數
			$this->wd=round(80/$unit_nu);//計算欄寬
			$this->colspan=2+$unit_nu;//計算合併欄位數
			
			//整合所有資料到週次陣列
			if ($this->seme_ary[week_mode]=='1'){$this->get_week1();}
			else{$this->get_week();}
			
			$this->sch=get_school_base();
			$this->cal_name=$this->sch[sch_cname].substr($this->seme,0,3)."學年度第".substr($this->seme,3,1)."學期 校務行事曆";
	}


	function get_all_set(){
		$SQL="select * from cal_elps_set order by  sday desc ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		if ($rs->RecordCount()==0) return;
		$arr = $rs->GetArray();
		//print_r($arr);
		//$tmp=array();
		foreach ($arr as $ary){
			$key=$ary[syear];
			if ($this->seme==$ary[syear]) {$this->seme_ary=$ary;}
			$tmp[$key]=substr($key,0,3)."學年 第".substr($key,3,1)."學期";			
		}
		$this->all_seme=$tmp;
		//print_r($tmp);
	}

	function get_week(){
		$loop=$this->seme_ary[weeks];
		//$sday=split("-",$this->seme_ary[sday]);//時間陣列 年月日
		$sday=explode("-",$this->seme_ary[sday]);//時間陣列 年月日
		$TT1=mktime(1,1,0,$sday[1],$sday[2],$sday[0]);
		$one_day=60*60*24;
		$week_ord=date("w",$TT1);//取得星期幾
		if ( $week_ord!=0 ) $TT1=$TT1-($week_ord*$one_day);//非星期日則取到星期日

		for($i=0;$i<$loop;$i++){
			$key=$i+1;
			$wk_1=$TT1+$i*$one_day*7;
			$wk_2=$wk_1+$one_day*6;
			$WK[$key][No]=$key;//週日
			$WK[$key][st_day]=date("m/d",$wk_1);//週日
			$WK[$key][en_day]=date("m/d",$wk_2);//週六
			//$WK[$key][event]=$this->event[$key];
		}
		$this->WK=$WK;
	}

	function get_week1(){
		$year=substr($this->seme,0,3)+0;
		$seme=substr($this->seme,3,1);
		$SQL="SELECT * FROM `week_setup` where year='$year' and semester ='$seme' order by  week_no asc ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		if ($rs->RecordCount()==0) die("<h1 align='center'>[學期初設定/開學日設定]內無".$this->seme."學期的週別設定!</h1>");
		$arr = $rs->GetArray();
		$one_day=60*60*24;
		foreach ($arr as $ary){
			$key=$ary[week_no];
			//$start=split("-",$ary[start_date]);//時間陣列 年月日
			$start=explode("-",$ary[start_date]);//時間陣列 年月日
			$wk_1=mktime(1,1,0,$start[1],$start[2],$start[0]);//週日
			$wk_2=$wk_1+$one_day*6;//週六
			$WK[$key][No]=$key;//週日
			$WK[$key][st_day]=date("m/d",$wk_1);//週日
			$WK[$key][en_day]=date("m/d",$wk_2);//週六
			//$WK[$key][event]=$this->event[$key];
		}
		$this->WK=$WK;
	}
	##################回上頁函式1#####################
	function BK($value= "BACK"){
		echo  "<br><br><br><br><CENTER><form><input type=button value='".$value."' onclick=\"history.back()\" style='font-size:16pt;color:red;'></form><BR><img src='images/stop.png'></CENTER>";
	die();
}
}

