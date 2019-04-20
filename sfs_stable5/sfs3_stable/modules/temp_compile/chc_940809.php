<?php
//$Id: chc_940809.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔

//建立物件
$obj= new new_stud($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("new_stud模組");之前
$obj->process();

//秀出網頁布景標頭
head("新生編班綜合編修");

//顯示SFS連結選單(欲使用請拿開註解)
print_menu($menu_p);

//顯示內容
$obj->display();
//佈景結尾
foot();
//echo "<pre>";print_r($obj->move_data);

##################回上頁函式1#####################
function backe($st="未填妥!按下後回上頁重填!") {
	echo "<HTML><HEAD><META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=big5\">
<TITLE>$st</TITLE></HEAD><BODY background='images/bg.jpg'>\n";
echo"<BR><BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}



//物件class
class new_stud{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=50;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $all_year;
	var $Year;//選擇學年
	var $SEX=array('1'=>'男','2'=>'女');



	//建構函式
	function new_stud($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		$this->page=($_GET[page]=='') ? 0:$_GET[page];
		($_POST[Year]=='') ? $year=$_GET[Year]:$year=$_POST[Year];
		if ( is_numeric($year)) $this->Year=$year;
		
		}
	//程序
	function process() {

		if($_POST[form_act]=='update' ) $this->update();
		if($_GET[form_act]=='del') $this->del();
		if($_GET[act]=='move') $this->move_data();
		if($_POST[form_act]=='move_it') $this->move_it();
		$this->all_year();//取全部年度列表
		if ($this->Year!='' && $_GET[act]!='move') $this->all();

	}
	//顯示
	function display(){
		$tpl_1 = dirname (__file__)."/chc_940809.htm";
		$tpl_2 = dirname (__file__)."/chc_940809s.htm";
		$this->smarty->assign("this",$this);
		($_GET[act]=='move') ? $tpl= $tpl_2:$tpl= $tpl_1;
		$this->smarty->display($tpl);
	}

	//各年度資料統計
	function all_year(){
		$SQL="select  stud_study_year from new_stud group by stud_study_year order by stud_study_year desc  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL); 
		$this->all_year=$rs->GetArray();
	}
	//擷取該年度所有學生資料
	function all(){
		$SQL="select newstud_sn from new_stud where  stud_study_year='{$this->Year}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->tol=$rs->RecordCount();
		$SQL="select * from new_stud  where  stud_study_year='{$this->Year}'  order by 
		 temp_id, class_year, class_sort,class_site,		
		newstud_sn desc  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
	//產生連結頁面
		$URL=$_SERVER[PHP_SELF]."?Year=".$this->Year;
		$this->links= new Chi_Page($this->tol,$this->size,$this->page,$URL);
	}

	//更新
	function update(){
		//echo "<pre>";print_r($_POST);die();newstud_sn
		if ($_POST['newstud_sn']=='' || !is_numeric($_POST['newstud_sn']) ) backe("未傳入變數！");
		if ($this->Year=='' ) backe("沒有學年度！");
		$SQL="update new_stud set  stud_study_year ='{$_POST['stud_study_year']}', old_school ='{$_POST['old_school']}', stud_person_id ='{$_POST['stud_person_id']}', stud_name ='{$_POST['stud_name']}', stud_sex ='{$_POST['stud_sex']}', stud_tel_1 ='{$_POST['stud_tel_1']}', stud_birthday ='{$_POST['stud_birthday']}', guardian_name ='{$_POST['guardian_name']}', stud_address ='{$_POST['stud_address']}', sure_study ='{$_POST['sure_study']}', stud_id ='{$_POST['stud_id']}', class_year ='{$_POST['class_year']}', class_sort ='{$_POST['class_sort']}', class_site ='{$_POST['class_site']}', temp_score1 ='{$_POST['temp_score1']}', temp_score2 ='{$_POST['temp_score2']}', temp_score3 ='{$_POST['temp_score3']}', meno ='{$_POST['meno']}', old_class ='{$_POST['old_class']}', temp_class ='{$_POST['temp_class']}', temp_site ='{$_POST['temp_site']}', addr_zip ='{$_POST['addr_zip']}', sort_sn ='{$_POST['sort_sn']}', class_memo ='{$_POST['class_memo']}', stud_kind ='{$_POST['stud_kind']}', bao_id ='{$_POST['bao_id']}', oth_class ='{$_POST['oth_class']}', oth_site ='{$_POST['oth_site']}', sure_oth ='{$_POST['sure_oth']}' where newstud_sn='{$_POST['newstud_sn']}'";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?Year=".$this->Year."&page=".$_POST[page];
		header("location:$URL");
	}
	//刪除全學年資料
	function del(){
		if ($this->Year=='' ) backe("沒有學年度！");
		$SQL="Delete from  new_stud  where   stud_study_year='{$this->Year}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF];
		Header("Location:$URL");
	}
	//整理該年度班級資料
	function move_data(){
		if ($this->Year=='' ) backe("沒有學年度！");
		$SQL=" SELECT class_year, class_sort, count( newstud_sn )  AS tol FROM new_stud	WHERE stud_study_year =  '{$this->Year}' GROUP  BY class_year,class_sort ORDER  BY class_year, class_sort ";		
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=&$rs->GetArray();
		foreach ($arr as $ary){
			$grade=$ary[class_year];
			$class=$ary[class_sort];
			$tol=$ary[tol];

			$tmp[$grade][grade]=$grade;
			$tmp[$grade][tol]++;
			$tmp[$grade][cla][$class]=$tol;
			$tmp[$grade][cla_no][$class]=$class;
			$tmp[$grade][cla_no2][$class]=$grade.'_'.$class;
		}
		$this->move_data=$tmp;
		
		//echo "<pre>";print_r($tmp);print_r($tmp1);die($SQL);
		
	}
	//變更該年度學生班級資料
	function move_it(){
		if ($this->Year=='' ) backe("沒有學年度！");
		$stu=$this->move_it_sub();//取學生資料
		//echo "<pre>";print_r($_POST);die($SQL);
		foreach ($_POST[class_id] as $key =>$val){
			$tmp1=explode("_",$key);
			$oyear=$tmp1[0];//舊年級
			$oclass=$tmp1[1];//舊班級
			$tmp2=explode("_",$val);
			$nyear=$tmp2[0];//新年級
			$nclass=$tmp2[1];//新班級
			if (!is_numeric($oyear) || !is_numeric($oclass)) continue;
			if (!is_numeric($nyear) || !is_numeric($nclass)) continue;
			if (count($stu[$key])=='0') continue;
			$stu2=join("','",$stu[$key]);
			//echo$stu2."<br>";
			if ($stu2=='')  continue;
			$stu2="'".$stu2."'";
			$SQL="update new_stud set  class_year='{$nyear}',class_sort='{$nclass}'  
			where  newstud_sn in ({$stu2}) and  stud_study_year='{$this->Year}' ";
			//die($SQL);
			$rs=$this->CONN->Execute($SQL) or die($SQL);
			}	

		$URL=$_SERVER[PHP_SELF].'?Year='.$this->Year;
		Header("Location:$URL");
	}

	function move_it_sub(){
		$SQL="select  newstud_sn,class_year,class_sort  from  new_stud  where   stud_study_year='{$this->Year}' order by  class_year, class_sort, newstud_sn ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=&$rs->GetArray();
		foreach ($arr as $ary){
			$sn=$ary[newstud_sn];		
			$year=$ary[class_year];
			$class=$ary[class_sort];
			$key=$year.'_'.$class;
			$tmp[$key][]=$sn;
		}
		return $tmp;
	}




	
}
?>