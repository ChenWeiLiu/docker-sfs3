<?php
//$Id: chc_stud.php 7979 2014-04-15 14:19:13Z chiming $

/*引入學務系統設定檔*/
include "../../include/config.php";
sfs_check();

//$stu=stu();
//echo "<pre>";print_r($stu);
//echo "hi";die();

//引入函數
require_once "./module-cfg.php";
include_once "../../include/sfs_case_excel.php";
require_once "../../include/sfs_case_ooo.php";
$obj= new chc_stud();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->IS_JHORES=&$IS_JHORES;

$obj->process();
/*
//1.秀出網頁布景標頭
head("全校班級名冊");

//顯示SFS連結選單(欲使用請拿開註解)//echo make_menu($school_menu_p);$link2="syear=$_GET[syear]";
if ($_SESSION[session_tea_sn]!='') print_menu($school_menu_p,$link2);
//myheader();
//2.顯示內容
$obj->display();

//3.佈景結尾
foot();

*/

class chc_stud{ //建立類別
  var $CONN;    //adodb物件
  var $Smarty;  //smarty物件
//  var $seme;    //學期    
  var $rs;      //所有學生
  var $stu;      //本學期所有學生陣列
  var $sch;      //學校
  var $GR=array(1=>'父子',2=>'父女',3=>'母子',4=>'母女',5=>'祖孫',6=>'兄弟',7=>'兄妹',8=>'姐弟',9=>'姊妹',10=>'伯叔姑姪甥',11=>'其他');
  //初始化
  function init() {

  		$this->YY=curr_year();
  		$this->SS=curr_seme();

		$this->seme=sprintf("%04d",$this->YY.$this->SS);		
		$this->head=array('學校編號','學校名稱','年級班級','學生姓名','家長姓名','關係','戶籍地址');
		if ($this->IS_JHORES==6){
			$this->grade=array(7=>" 一年級",8=>" 二年級",9=>" 三年級");
			$this->sgrade=array(7=>" 一年",8=>" 二年",9=>" 三年");
			}
		else{
			$this->grade=array(1=>" 一年級",2=>" 二年級",3=>" 三年級",4=>" 四年級",5=>"五年級",6=>"六年級");
			$this->sgrade=array(1=>" 一年",2=>" 二年",3=>" 三年",4=>" 四年",5=>"五年",6=>"六年");
		}
	}

  //啟用程序
  function process(){
		$this->init();
		$this->sch=$this->get_sch_data();
		$this->cla_nmae=$this->get_class_name();
		$this->stu=$this->stu();
		if ($_GET[type]=='ODS') $this->out_ods();
		if ($_GET[type]=='XLS') $this->out_xls();
		
  }

  function get_sch_data(){
	  $SQL="select * from school_base";
	  $rs=$this->CONN->Execute($SQL) or die("語法錯誤".$SQL);
	  $tmp=$rs->GetArray();
	  return $tmp[0];
  }
  function get_class_name(){
		$SQL="select * from school_class  where year='{$this->YY}' 
		and semester ='{$this->SS}' and enable='1' 
		order by c_year ,c_sort ";
		$rs=$this->CONN->Execute($SQL) or die("語法錯誤".$SQL);
		$tmp=$rs->GetArray();		foreach($tmp as $ary ){
			$class=$ary[c_year].sprintf("%02d",$ary[c_sort]);//601	
			$cla_name[$class]=$this->sgrade[$ary[c_year]].$ary[c_name]."班";
		} 
		return $cla_name;

	  
  }

  function stu(){
	  	$SQL="select a.student_sn,a.seme_class,b.stud_name,c.guardian_name,c. guardian_relation,
	  	a.seme_class,a.seme_num,b.stud_addr_1 from stud_seme a,stud_base b,stud_domicile c where 
	  	a.student_sn=b.student_sn and a.student_sn=c.student_sn 
	  	and b.stud_study_cond ='0'
	  	and a.seme_year_seme='{$this->seme}' order by a.seme_class,a.seme_num ";
  		$rs=$this->CONN->Execute($SQL) or die("語法錯誤".$SQL);
  		$arr=&$rs->GetArray();
		foreach($arr as $ary){
			$year=substr($ary[seme_class],0,1);
			//$cla_name=$ary[seme_class];//原數字班名
			$cla_name=$this->cla_nmae[$ary[seme_class]];//轉為中文班名
			$ar2[$year][]=array($this->sch[sch_id],$this->sch[sch_cname_s],$cla_name,
			$ary[stud_name],$ary[guardian_name],$this->GR[$ary[guardian_relation]],$ary[stud_addr_1]);
		}   		
  		return $ar2;	
  }

	function out_xls(){
		//include_once "../../include/sfs_case_excel.php";
		$x=new sfs_xls();
		$x->setUTF8();//$x->setVersion(8);
		$x->setBorderStyle(1);
		$x->filename=$this->sch[sch_cname].'.xls';
		$x->setRowText($this->head);
		
		foreach ($this->stu as $year=>$stu){
			//$year=iconv("Big5","UTF-8",$grade[$year]);
			$grade=$this->grade[$year];
			//$x->addSheet($grade);
			$x->addSheet("Grade-".$year);
			$x->items=$stu;
			$x->writeSheet();
			}
		$x->process();
	}

	function out_ods(){
		
		$x=new sfs_ooo();
		$x->filename=$this->sch[sch_cname];
		$x->setRowText($this->head);
		foreach ($this->stu as $year=>$stu){
			//if ($year == 1 ) continue ;
			//if ($year == 2 ) continue ;
			//if ($year == 3 ) continue ;
			//if ($year == 4 ) continue ;
			//if ($year == 5 ) continue ;
			//if ($year == 6 ) continue ;
			//echo $year;
			$grade=$this->grade[$year];
			$x->addSheet($grade);
			$x->items=$stu;
			$x->writeSheet();	
		}
		$x->process();

	}



}
//  end class
