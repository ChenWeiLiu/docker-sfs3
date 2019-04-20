<?php
//$Id$
include "config.php";
//認證
sfs_check();

//建立物件
$obj= new My_TB($CONN,$smarty);
//初始化
$obj->init();
//處理程序
$obj->process();

//秀出網頁布景標頭
head("問題工具箱--獎懲修正");
print_menu($school_menu_p);
//樣本檔

//顯示內容
$obj->display();
//佈景結尾
foot();


//物件class
class My_TB{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數


	//建構函式
	function My_TB($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {}
	//程序
	function process() {
		if($_POST['form_act']=='fix') $this->fixall();
		if($_POST['form_act']=='fixOne') $this->fixOne();
		$this->all();
	}
	//顯示
	function display(){
		$tpl = "fix_stud_seme_rew.htm";
		$this->smarty->template_dir=dirname(__file__)."/templates/";
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if(filter_has_var(INPUT_POST, "stud_id")):
		if ($_POST['stud_id']=='') return ;
		$stud_id=strip_tags($_POST['stud_id']);
		if(!preg_match("/^\d*$/",$stud_id)) die("輸入無法驗證1!!");
		// if (!ctype_digit($stud_id)) die("輸入無法驗證2!!");
		if (strlen($stud_id) < 2 ) return ;
		
		$SQL="select a.stud_name,a.stud_sex,a.stud_study_cond,a.curr_class_num,
		b.stud_id,b.student_sn ,count(b.seme_year_seme) as semeTol 
		from stud_base a,stud_seme b 
		where a.student_sn =b.student_sn and 
		b.stud_id like '$stud_id%' group by b.student_sn order by b.stud_id, b.student_sn ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$arr=&$rs->GetArray();//echo'<pre>';print_r($arr);
		foreach ($arr as $ary){
			$ID=$ary['stud_id'];$SN=$ary['student_sn'];
			$AA[$ID]['SN'][$SN]=$this->rewTol($ID,$SN);
			$AA[$ID]['DA'][$SN]=$ary;
		}
			//echo'<pre>';print_r($AA);die();
		foreach ($AA as $K => $AR){
			if (count($AR['SN'])==1) continue;
			//SN,數量
			list($A0,$B0)=each($AR['SN']);
			list($A1,$B1)=each($AR['SN']);
			//後者的獎懲多於前者-->有問題的
			if ($B0==0 && $B1==0) continue; 
			if ($B1 >= $B0 ) :
			$BB[$K]['A']=$AR['DA'][$A0];
			$BB[$K]['A']['rewTol']=$B0;

			$BB[$K]['B']=$AR['DA'][$A1];
			$BB[$K]['B']['rewTol']=$B1;
			endif;
			
			}
		
		//echo'<pre>';print_r($BB);die();
		$this->all=$BB;//return $arr;
		
		endif;

	}
	//新增StuID[10091]
	function fixall(){
		if (count($_POST['StuID'])==0 ) return ;
		foreach ($_POST['StuID'] as $key => $id){
			if(!preg_match("/^\d*$/",$key)) die("輸入無法驗證!!!");
		// if (!ctype_digit($stud_id)) die("輸入無法驗證2!!");
			$AA[]=$key;unset($id);unset($key);
			}
		if (count($AA)==0 ) return ;
		$Str=join(",",$AA);
		$SQL="select seme_year_seme ,	stud_id, student_sn  from stud_seme where stud_id in ($Str) order by  stud_id ,student_sn, seme_year_seme  ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$arr=&$rs->GetArray();
		if (count($arr)==0) return;
		foreach ($arr as $ary){
			$ID=$ary['stud_id'];$SN=$ary['student_sn'];$seme=$ary['seme_year_seme'];
			//這個學期的這個學號..應是這個學生流水號
			$SQL="update stud_seme_rew set  student_sn ='$SN'  where  stud_id ='$ID' and seme_year_seme='$seme' ";
			$rs=&$this->CONN->Execute($SQL) or die($SQL);			
			}		
		

		$URL=$_SERVER['PHP_SELF'];
		Header("Location:$URL");
	}
	//更新
	function fixOne(){
		if(filter_has_var(INPUT_POST, "stud_id")):
		if ($_POST['stud_id']=='') return ;
		$stud_id=strip_tags($_POST['stud_id']);
		if(!preg_match("/^\d*$/",$stud_id)) die("輸入無法驗證!!");
		// if (!ctype_digit($stud_id)) die("輸入無法驗證2!!");
		if (strlen($stud_id) < 3 ) return ;
		$SQL="select seme_year_seme ,	stud_id, student_sn  from stud_seme where stud_id='$stud_id' order by  stud_id ,student_sn, seme_year_seme  ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$arr=&$rs->GetArray();
		if (count($arr)==0) return;
		foreach ($arr as $ary){
			$ID=$ary['stud_id'];$SN=$ary['student_sn'];$seme=$ary['seme_year_seme'];
			//這個學期的這個學號..應是這個學生流水號
			$SQL="update stud_seme_rew set  student_sn ='$SN'  where  stud_id ='$ID' and seme_year_seme='$seme' ";
			$rs=&$this->CONN->Execute($SQL) or die($SQL);			
			}	
		$URL=$_SERVER['PHP_SELF'];
		Header("Location:$URL");
		endif;
	}
	//更新
	function rewTol($id,$sn){
		if ($this->Rew[$id][$sn]!='' ) return $this->Rew[$id][$sn]+0;
		$SQL="select student_sn , count(*) as Tol 
		from  stud_seme_rew where stud_id='$id' group by student_sn ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		//echo $SQL;
		$arr=&$rs->GetArray();
		foreach ($arr as $ary){
			$SN=$ary['student_sn'];
			$this->Rew[$id][$SN]=$ary['Tol'];
			}
		return $this->Rew[$id][$sn]+0;
	}
	

}

