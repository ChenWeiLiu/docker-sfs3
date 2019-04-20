<?php
// $Id: all_year.php 8337 2015-03-04 02:07:15Z brucelyc $

include "stud_query_config.php";
//include_once "../../include/sfs_case_PLlib.php";
//include_once "../../include/sfs_case_dataarray.php";
//$CONN->debug = true; 

$obj=new all_year($CONN,$smarty);
$obj->stud_coud=cron_split();//學籍資料代碼

$obj->is_jhores=$IS_JHORES;
$obj->process();

//顯示物件,檔案結束dirname(__FILE__);
$template_file =dirname(__FILE__)."/templates/all_year.htm";//目前目錄下
//echo dirname($_SERVER["SCRIPT_FILENAME"]);
//echo sprintf("%03d",curr_year()).curr_seme();
head("歷年學校人數統計");
echo make_menu($menu_p);
$obj->display($template_file);

foot();

class all_year{
	var $CONN;
	var $smarty;
	var $is_jhores;
	var $grade_ary;
	var $Cgrade_ary;
	var $dyna;
	var $now_seme;//目前學期
	var $con_all;//在家自學人數

	function all_year($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->now_seme =  sprintf("%03d",curr_year()).curr_seme();//目前學期
	}

	function process(){
		$this->jhores();//判斷國中小
		$this->class_all();//取出班級
		$this->all();//取出學期資料
//		$this->move_all();
		$this->all_move();//處理異動資料
		$this->con_k();//處理在家自學


		}

	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}

	function view($seme) {
		$class_num=$this->class_num2($seme);
		$this->dyna[$seme][class_tol]=$class_num[class_tol];

		$SQL="SELECT substring( b.seme_class, 1, 1 ) AS YY, a.stud_sex,count( a.student_sn ) AS tol FROM stud_base a,stud_seme b WHERE b.seme_year_seme = '$seme' and a.student_sn=b.student_sn  GROUP BY a.stud_sex,YY ORDER BY YY,a.stud_sex";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$new_arr=array();
		foreach ($this->grade_ary  as $grade){
			$new_arr[$grade][grade]=$grade;
			$new_arr[$grade][boy]='';
			$new_arr[$grade][girl]='';
			$new_arr[$grade][class_num]=$class_num[year][$grade];
			foreach ($arr  as $ary){
				if($ary[YY]==$grade && $ary[stud_sex]=='1') $new_arr[$grade][boy]=$ary[tol];
				if($ary[YY]==$grade && $ary[stud_sex]=='2') $new_arr[$grade][girl]=$ary[tol];
			}
		}
		
		return $new_arr;
	}

	function all() {
		$SQL="SELECT  seme_year_seme,count( student_sn ) AS tols FROM stud_seme group by seme_year_seme ORDER BY seme_year_seme desc";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->all=array();
//		$arr=$rs->GetArray();
		while ($ro=$rs->FetchNextObject(false)) {
			$arr[$ro->seme_year_seme][seme]=$ro->seme_year_seme;
			$arr[$ro->seme_year_seme][year]=substr($ro->seme_year_seme,0,3)+0;
			$seme=substr($ro->seme_year_seme,-1);
			if($seme==1) $arr[$ro->seme_year_seme][C_seme]="上學期";
			if($seme==2) $arr[$ro->seme_year_seme][C_seme]="下學期";
			unset($seme);

			$arr[$ro->seme_year_seme][tol]=$ro->tols;
			$arr[$ro->seme_year_seme][ary]=$this->view($ro->seme_year_seme);
			$arr[$ro->seme_year_seme][class_tol]=$this->dyna[$ro->seme_year_seme][class_tol];

			$SEME[$ro->seme_year_seme][seme]=$ro->seme_year_seme;
			$SEME[$ro->seme_year_seme][year]=$arr[$ro->seme_year_seme][year];
			$SEME[$ro->seme_year_seme][C_seme]=$arr[$ro->seme_year_seme][C_seme];
			$SEME[$ro->seme_year_seme][tol]=$ro->tols;

		}
//		echo "<PRE>";print_r($arr);
		$this->all=$arr;
		$this->SEME=$SEME;
	}

	function jhores() {
		if($this->is_jhores==0) {
			$this->grade_ary=array(1,2,3,4,5,6);
			$this->Cgrade_ary=array("一年級","二年級","三年級","四年級","五年級","六年級");
			}
		if($this->is_jhores==6) {
			$this->grade_ary=array(7,8,9);
			$this->Cgrade_ary=array("一年級","二年級","三年級");
			}
	}

	function class_num2($Seme) {
		$year=substr($Seme,0,3)+0;
		$seme=substr($Seme,-1);
		$tol=0;
		foreach ($this->class_all as $ary){
			if($ary[year]==$year && $ary[semester]==$seme) {
				foreach ($this->grade_ary as $grade){
					if($ary[c_year]== $grade) {
						$tmp_ary[year][$grade]++;
						$tol++;
					}
				}
			}
		}
		$tmp_ary[class_tol]=$tol;
	//	echo "<PRE>";print_r($tmp_ary);
	return $tmp_ary;
	}

	function class_all() {
		$SQL="SELECT  *  FROM school_class  where  enable='1' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->class_all= $arr;
	}

	function move_all($seme) {
		$seme=$seme+0;
		$arr=array();
		$out = array(1,5,6,7,8,11,12);
		$in = array(2,3,4,13,14);
		$this->del=0;
		$this->add=0;
		foreach ($this->all_move as $ary){
			if($ary[move_year_seme]!=$seme) continue;
			if(in_array($ary[move_kind], $out))  $this->del++;
			if(in_array($ary[move_kind], $in))  $this->add++;
			$arr[$ary[move_kind]]++;
		}
		return $arr;
	}

	function all_move() {
		$SQL="SELECT  move_id,stud_id,move_kind,move_year_seme,move_date  FROM stud_move ";//  order by  LPAD(move_kind,2,'0')  
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all_move=$arr;
	}

	function con_k() {
		$SQL="SELECT   student_sn  FROM stud_base  where  stud_study_cond='15' ";//  order by  LPAD(move_kind,2,'0')  
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$i=0;
		foreach ($arr as $ary){
			$st[]=$ary[student_sn];
			$i++;
		}
		$str=join(",",$st);
		if($str=='') {
			$this->con_all=0;
			$this->con_add=0;
			return;
		}

//		$this->con_15=$arr;
		$this->con_all=$i;
		$year_seme = $this->now_seme;
		$SQL="SELECT  seme_year_seme, count(student_sn) as nu  FROM stud_seme  where   seme_year_seme  ='$year_seme' and student_sn in ($str) group by seme_year_seme ";//  order by  LPAD(move_kind,2,'0')  
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->con_add=$i-$arr[0][nu];
//		echo $this->con_15_add;
	}

}

	function cron_split() {
		$study_cond=study_cond();
		foreach ($study_cond as $key =>$value){
			if($key==0) continue;
			if($key==15) continue;
			$arr["$key"]=$value;
			}
		return $arr;
	}

?>
