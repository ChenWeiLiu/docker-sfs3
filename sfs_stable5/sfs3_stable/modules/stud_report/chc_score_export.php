<?php
//$Id: chc_score_export.php 5310 2009-01-10 07:57:56Z hami $
require_once( "./chc_config.php");
//print "<pre>";
//各科成績匯出
//認證
sfs_check();
// 1.smarty物件
//$template_dir = $_SERVER[DOCUMENT_ROOT].dirname($_SERVER[PHP_SELF])."/templates/";
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";

// $smarty->config_dir = $template_dir;
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//	將基本的變數及陣列傳入 smarty
/*
$_POST[class_id]=array('093_2_08_13','093_1_07_01');
$_POST[act]=='OK';
*/
//---改為取得單一班級的學生序號
//----傳入 class_id 
//----傳回 含學生序號的陣列 student_sn
function get_stsn($class_id){
		global $CONN;
	$st_sn=array();
	//--foreach($class_id as $key=>$data){
	$key = $class_id;
	$class_ids=split("_",$key);
	$seme=$class_ids[0].$class_ids[1];
	$the_class=($class_ids[2]+0).$class_ids[3];
	$SQL="select student_sn from stud_seme where  seme_year_seme ='$seme' and seme_class='$the_class' order by seme_num ";
	$rs = $CONN->Execute($SQL);
	$the_sn=$rs->GetArray();
	for ($i=0;$i<$rs->RecordCount();$i++){
		array_push($st_sn,$the_sn[$i][student_sn]);
		}
	//---}
return $st_sn;
}


//--- 初始化需要的變數
$break_page="<P STYLE='page-break-before: always;'>";
$prn_page = 0;
//---載入報表相關的CSS設定值
$smarty->display($template_dir."prn_head.tpl");
$smarty->assign('school_name',$school_long_name);
///以班級陣列取出學生
//print_r($_POST);
if ($_POST[act]=='OK' && is_array($_POST[class_id]) ){
	//print_r($_POST[class_id]);
	//--- 取得所有班級的學生代號
	$class_data=array();
	foreach($_POST[class_id] as $class_id=>$data) {
		$sn_ary=get_stsn($class_id);
		foreach($sn_ary as $student_sn) {
			$class_data[$class_id][$student_sn][seme_ary]=seme_num_detail($student_sn);
		}
	}
	//print_r($class_data);
	$sub_str='';
	$sub_ary=array();
	foreach ($class_data as $class_id=>$stud_ary) {
		foreach($stud_ary as $student_sn=>$stud) {
			foreach($stud[seme_ary] as $seme_key=>$seme_data) {
				if (!empty($seme_data)) {
					if (strpos($sub_str,$seme_data[class_id])===false) {
						$sub_str .= '/'.$seme_data[class_id];
						$class_sub=get_subj($seme_data[class_id],'seme');
						foreach ($class_sub as $ss_id=>$sub) {
							$sub_name=$sub[sb];
							if (empty($sub_ary[$seme_key][$sub_name])) {
								$sub_ary[$seme_key][$sub_name]='--';
							}
						}
					}
				}
			}
		}
	}
	foreach ($sub_ary as $seme_key=>$ary) {
		$sub_ary[$seme_key]['日常生活表現']='--';
	}
	//print_r($sub_ary);
	foreach ($class_data as $class_id=>$stud_ary) {
		//print "<br> class_id= $class_id";
		foreach($stud_ary as $student_sn=>$stud) {
			//print " student_sn= $student_sn ";
			$base_ary = get_base_data($student_sn);
			$class_data[$class_id][$student_sn][base][stud_id]=$base_ary[stud_id];
			$class_data[$class_id][$student_sn][base][stud_name]=$base_ary[stud_name];
			$class_data[$class_id][$student_sn][base][class_id]=$base_ary[class_id];
			$class_data[$class_id][$student_sn][base][cla_no]=$base_ary[cla_no];
			$class_data[$class_id][$student_sn][base][seme_num]=$base_ary[seme_num];
			$stud_id=$base_ary[stud_id];
			//print " stud_id= $stud_id";
			foreach($stud[seme_ary] as $seme_key=>$seme_data) {
				if (empty($seme_data)) {
					unset($class_data[$class_id][$student_sn][seme_ary][$seme_key]);
				}
				else {
					//print "<br> testing seme_key= $seme_key ";
					$seme_score=seme_score_chc($seme_data[class_id], $student_sn, $stud_id);
					$class_data[$class_id][$student_sn][seme_ary][$seme_key][seme_score]=$sub_ary[$seme_key];
				//print_r($seme_data);
				//print_r($seme_score);
					foreach($seme_score as $ss_id=>$score_ary) {
						$sub_name=$score_ary[sb];
						$score=$score_ary[score];
							//print "<br> score= $score sub_name= $sub_name";
						if ($sub_ary[$seme_key][$sub_name]=='--') {
							//print "<br> ***** score= $score sub_name= $sub_name";
							$class_data[$class_id][$student_sn][seme_ary][$seme_key][seme_score][$sub_name]=$score;
						}
					}
				}
			}
		}
	}
	//print_r($sub_ary);
	foreach ($sub_ary as $seme_key=>$subs) {
		$sub_arys[$seme_key][items]=count($subs);
		$sub_arys[$seme_key][name]=$subs;
	}
	//print_r($sub_arys);
	$smarty->assign('sub_arys', $sub_arys);
	$smarty->assign('class_data', $class_data);
	$smarty->display($template_dir."chc_score_export.htm");
	
	//print_r($class_data);


}


?>
