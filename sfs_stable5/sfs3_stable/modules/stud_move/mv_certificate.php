<?php

include "stud_move_config.php";

session_start();
sfs_check();

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

$template_dir = $SFS_PATH."/".get_store_path()."/templates";

$template_file=$template_dir."/move_certificate.htm";
// 建立物件
$obj = new certificate($CONN,$smarty,$IS_JHORES);
$obj->pgp_num=$pgp_num?$pgp_num:'__________';
$obj->sign_url=$sign_url;
$obj->sign_width=$sign_width;
$obj->sign_height=$sign_height;

//執行物件程序

$obj->process();

//顯示物件,檔案結束
$obj->display($template_file);

/////------------以下為程式物件------------------///


###------------------------    start class程式物件---------------------------###
class certificate{

	var $CONN;//ado物件
	var $smarty;//smarty物件
	var $mv_id;//異動流水號	
	var $mv_info;
	var $S_id;//學號
	var $SN;//學生流水號
	var $base;//基本資料
	var $ss_ary;//課程資料陣列
	var $seme;//本學期資料
	var $seme_info;//本學期資料
	var $class_info;//本學期資料
	var $class_id;
	var $TB;//成績表
	var $Score;//成績
	var $SS;//成績
	var $tb_width="90%";//表格寬度
	var $IS_JHORES;
	var $pgp_num;

	function certificate($ADO_obj,$smarty,$IS_JHORES){
		$this->CONN=&$ADO_obj;
		$this->smarty=&$smarty;
		$this->IS_JHORES=&$IS_JHORES;
	}


	function process(){
		if($_GET[mv_id]!='') $this->mv_to_base($_GET[mv_id]);

		}
	function init(){
		}

	function display($tpl){
		//----Smarty物件----------//
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	function mv_to_base($id){
		$SQL="select  b.* from stud_base a ,stud_move b where b.move_id='$id'  and  (b.move_kind='7' or b.move_kind='8') and a.stud_id=b.stud_id  and a.student_sn=b.student_sn  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$num=$rs->RecordCount();
		if($num!=1) die("查無記錄".$id);
		$arr=$rs->GetArray();
		
		$this->mv_id=$id;
		$this->mv_info=$arr[0];//轉學資料
		unset($arr);

		$aa=split("-",$this->mv_info[move_date]);
		$this->mv_info[C_move_date]=($aa[0]-1911).".".$aa[1].".".$aa[2];//轉學資料
		$this->mv_info[reason2]=$this->mv_info[reason]?$this->mv_info[reason]:"□遷居 □其他:_____________";

		$SQL="select a.*, b.move_year_seme from stud_base a ,stud_move b where b.move_id='$id'  and  (b.move_kind='7' or b.move_kind='8') and a.stud_id=b.stud_id  and a.student_sn=b.student_sn  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$num=$rs->RecordCount();
		if($num!=1) die("查無記錄".$id);
		$arr=$rs->GetArray();
//		$this->mv_info=$arr[0];

		$this->base=$arr[0];//基本資料表
		unset($arr);

		($this->base[stud_sex]=='1')  ? $this->base[C_sex]='男':$this->base[C_sex]='女';
		$aa=split("-",$this->base[stud_birthday]);
		$this->base[C_birthday]=($aa[0]-1911).".".$aa[1].".".$aa[2];
		$this->base[C_birthday2]=($aa[0]-1911)."年".$aa[1]."月".$aa[2]."日";
		$this->Now=array((date("Y")-1911),date("m"),date("d"));
		
		


		$this->S_id=$this->base[stud_id];//學號

		$this->SN=$this->base[student_sn];//學生流水號
		$for_seme=sprintf("%04d",$this->mv_info[move_year_seme]);
		$this->seme=$for_seme;
		
		$this->mv_info['school_move_num']=sprintf('%03d',$this->mv_info['school_move_num']);
		

		$SQL="select  seme_year_seme , seme_class, seme_num from stud_seme  where student_sn='".$this->SN."' and seme_year_seme='{$for_seme}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->seme_info=$arr[0];
		unset($arr);
		$this->class_id=substr($this->seme_info[seme_year_seme],0,3)."_".substr($this->seme_info[seme_year_seme],3,1)."_".sprintf("%02d",substr($this->seme_info[seme_class],0,1))."_".substr($this->seme_info[seme_class],1,2);
		



		$this->TB="score_semester_".intval(substr($this->seme_info[seme_year_seme],0,3))."_".substr($this->seme_info[seme_year_seme],3,1);
//		echo $this->class_id.$this->TB;
		$SQL="select * from  `{$this->TB}` where student_sn='{$this->SN}' and score!='-100' order by ss_id ,test_sort ";
		$rs=$this->CONN->Execute($SQL);// or trigger_error('本學期沒有成績記錄，無法顯示',256);
		if ($rs){		
			$arr = $rs->GetArray();
			$this->Score=$arr;
			unset($arr);
		}
//		echo "<PRE>";print_r($this->seme_ary);print_r($arr_sco);
		$this->get_subj($this->class_id,"");//取科目
		$this->sch_info();//取學校資料
		$this->get_abs_rew();//取缺曠課
		$this->class_info();

		}
function get_abs_rew() {
	$rew=array("1"=>"大功\","2"=>"小功\","3"=>"嘉獎","4"=>"大過","5"=>"小過","6"=>"警告");
	$abs=array("1"=>"事假","2"=>"病假","3"=>"曠課","4"=>"集會","5"=>"公假","6"=>"其他");
	$SQL="select   seme_year_seme ,stud_id , abs_kind, abs_days from stud_seme_abs  where stud_id='".$this->S_id."' and seme_year_seme='{$this->seme}'  order by abs_kind";
	$rs=$this->CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$V_abs='';
	if($arr!='') {
		for ($i=0;$i<count($arr);$i++){
			$V_abs[$i][abs]=$abs[$arr[$i][abs_kind]];
			$V_abs[$i][val]=$arr[$i][abs_days];
		}
	}
	$this->stu_abs=$V_abs;
	$arr='';
	$SQL="select    seme_year_seme, stud_id , sr_kind_id, sr_num  from stud_seme_rew where stud_id='".$this->S_id."' and seme_year_seme='{$this->seme}'  order by sr_kind_id ";
	$rs=$this->CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	$V_abs='';
	if($arr!='') {
		for ($i=0;$i<count($arr);$i++){
			$V_abs[$i][rew]=$rew[$arr[$i][sr_kind_id]];
			$V_abs[$i][val]=$arr[$i][sr_num];
		}
	}
	$this->stu_rew=$V_abs;
//	echo "<PRE>";
//print_r($this->stu_rew);
}



function get_subj($class_id,$type='') {
//global $CONN ;
	switch ($type) {
		case 'all':
		$add_sql=" ";break;
		case 'seme':
		$add_sql=" and need_exam='1' and enable='1' ";break;//有成績的
		case 'stage':
		$add_sql=" and need_exam='1'  and print='1' and enable='1' ";break;//有段考,完整
		case 'no_test':
		$add_sql=" and need_exam='1'  and print!='1' and enable='1' ";break; //不用段考的
		default:
		$add_sql=" and enable='1' ";break;
	} 
	$CID=split("_",$this->class_id);//093_1_01_01
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$CID[2];
	$class=$CID[3];
	$CID_1=$year."_".$seme."_".$grade."_".$class;

	$SQL="select * from score_ss where class_id='$CID_1' $add_sql  and  rate > 0  order by sort,sub_sort ";
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);

	if ($rs->RecordCount()==0){
		$SQL="select * from score_ss where class_id='' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."' $add_sql order by sort,sub_sort ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All_ss=$rs->GetArray();
	}
	else{$All_ss=$rs->GetArray();}
//echo $SQL;
	$subj_name=$this->initArray("subject_id,subject_name","select * from score_subject ");
	$obj_SS=array();
	//echo "<PRE>BB<BR>";

	for($i=0;$i<count($All_ss);$i++){
		$key=$All_ss[$i][ss_id];//索引
		// $obj_SS[$key]=$All_ss[$i];//全部陣列,暫不用
		$obj_SS[$key][rate]=$All_ss[$i][rate];//加權
		$obj_SS[$key][sc]=$subj_name[$All_ss[$i][scope_id]];//領域名稱
		$obj_SS[$key][sb]=$subj_name[$All_ss[$i][subject_id]];//科目名稱
		($obj_SS[$key][sb]=='') ? $obj_SS[$key][sb]=$obj_SS[$key][sc]:"";
	}
	//die("無法查詢，語法:".$SQL);

//-------整理成網頁的分支格式----------------//
	for($i=0;$i<count($All_ss);$i++){
		$TD=$All_ss[$i][scope_id];
		$tmp_scop[$TD][ss_id][]=$All_ss[$i][ss_id];
	}
	foreach ($tmp_scop as $key =>$ary){
		if(count($ary[ss_id])>1) {
			$tmp_scop[$key][H]=count($ary[ss_id]);
		}
	}
	foreach ($tmp_scop as $key =>$ary){
		$i=0;
		foreach ($ary[ss_id] as $ss_id){
			if($i==0 && $tmp_scop[$key][H]!='') {
				$obj_SS[$ss_id][H]=$tmp_scop[$key][H];
				}
			if($i==0 && $tmp_scop[$key][H]=='') {
				$obj_SS[$ss_id][H]='W';
				}
		$i++;
		}

	}
//	echo "<PRE>BB<BR>";print_r($obj_SS);
	$this->SS=$obj_SS;
}



function initArray($F1,$SQL){
//	global $CONN ;
	$col=split(",",$F1);
	$key_field=$col[0];
	$value_field=$col[1];

	$rs = $this->CONN->Execute($SQL) or die($SQL);
	$sch_all = array();
	if (!$rs) {
		Return $this->CONN->ErrorMsg(); 
	} else {
		while (!$rs->EOF) {
		$sch_all[$rs->fields[$key_field]]=$rs->fields[$value_field]; 
		$rs->MoveNext(); // 移至下一筆記錄
		}
	}
	Return $sch_all;
}


function Score($ssid){
	foreach ($this->Score as $ary){
		if($ary[ss_id]==$ssid ) $sco[]=$ary;
	}
	foreach ($sco as $ary2){
		if($ary2[test_kind]=='定期評量' && $ary2[test_sort]==1) $view[1]=$ary2[score];
		if($ary2[test_kind]=='平時成績' && $ary2[test_sort]==1) $view[2]=$ary2[score];
		if($ary2[test_kind]=='定期評量' && $ary2[test_sort]==2) $view[3]=$ary2[score];
		if($ary2[test_kind]=='平時成績' && $ary2[test_sort]==2) $view[4]=$ary2[score];
		if($ary2[test_kind]=='定期評量' && $ary2[test_sort]==3) $view[5]=$ary2[score];
		if($ary2[test_kind]=='平時成績' && $ary2[test_sort]==3) $view[6]=$ary2[score];
		if($ary2[test_kind]=='全學期' && $ary2[test_sort]==255) $view[all]=$ary2[score];
	}
//	echo "<PRE>BB<BR>";print_r($view);
return $view;
}

function num_tw($num, $type=0) {
	$num_str[0] = "十百千";
	$num_str[1] = "拾佰仟";
	$num_type[0]='零一二三四五六七八九';
	$num_type[1]='零壹貳參肆伍陸柒捌玖';
	$num = sprintf("%d",$num);
	while ($num) {
		$num1 = substr($num,0,1);
		$num = substr($num,1);
		$target .= substr($num_type[$type], $num1*2, 2);
		if (strlen($num)>0) $target .= substr($num_str[$type],(strlen($num)-1)*2,2);
	}
	return $target;
}

function sch_info() {
	$SQL="SELECT * FROM `school_base` ";
	$rs = $this->CONN->Execute($SQL) or die($SQL);
	$arr = $rs->GetArray();
	$this->sch_info=$arr[0];
}
function class_info() {
	$SQL="SELECT * FROM `school_class`  where class_id='{$this->class_id}' and  enable='1' ";
	$rs = $this->CONN->Execute($SQL) or die($SQL);
	$arr = $rs->GetArray();
//echo "$SQL";
	$this->class_info=$arr[0];

	//$this->class_info[year]=$this->num_tw($arr[0][year],0);
	//$this->class_info[semester]=$this->num_tw($arr[0][semester],0);
	$this->class_info[year]=$arr[0][year];
	$this->class_info[semester]=$arr[0][semester];
	$this->class_info[c_year]=$this->num_tw(($arr[0][c_year]-$this->IS_JHORES),0);

}


###------------------------    end class---------------------------###
}


/*
////參考用法資料
while ($ro=$rs->FetchNextObject(false)) {
  $arr[$ro->id]=get_object_vars($ro);
  $arr[$ro->id]=$ro->cname;
}
while( $ar = $rs->FetchRow() ) {
	    print $ar['name'] ." " . $ar['year'];
	}

*/

?>
