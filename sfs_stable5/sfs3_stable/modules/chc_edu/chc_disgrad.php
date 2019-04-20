<?php
//$Id: disgrad.php 5310 2009-11-30 07:57:56Z hami $
include_once "chc_disgrad_config.php";
//require_once("./chc_config.php");
include "../../include/sfs_case_score.php";

//認證
sfs_check();
$save_csv=$_POST['save_csv'];

//debug_msg("第".__LINE__."行 _REQUEST ", $_REQUEST);
//debug_msg("第".__LINE__."行 _POST ", $_POST);
//debug_msg("第".__LINE__."行 year_seme ", $year_seme);
//die();

$year_seme=$_POST[year_seme];
if (empty($year_seme)) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
if($IS_JHORES==0){
	$_POST[years]=12;  //國小十二學期
	$StudyYear=6;
}else{
	$_POST[years]=6;  //國中六學期
	$StudyYear=3;
}
$_POST[fail_num]=10;  //列出的領域

if($_POST[grade]>=1){
	$_POST[year_name]=$_POST[grade];
}


$show_ss=array("language"=>"語文","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
if ($_POST[year_name] and count($_POST[class_id])>0) {

	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$class_base=class_base($seme_year_seme);
	$seme_class=$_POST[year_name]."%";
	foreach($_POST[class_id] as $key =>$value){
		if($value=="on"){
			$ClassNum=$_POST[year_name].substr($key, -2);
			$AllClassID[]=$ClassNum;
			$class_id=$key;
		}
	}
	$all_seme_class_str=implode("','", $AllClassID);
	$all_seme_class="('".$all_seme_class_str."')";
	//$query="select a.student_sn from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class LIKE '$seme_class' and b.stud_study_cond in ('0','15')";
	$query="select a.student_sn from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class in $all_seme_class ";
	//debug_msg("第".__LINE__."行 query ", $query);

	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$sn[]=$res->fields[student_sn];
		$res->MoveNext();
	}
	$query="select stud_study_year from stud_base where student_sn='".$sn[0]."'";
	$res=$CONN->Execute($query);
	$stud_study_year=$res->fields[0];
	for ($i=0;$i<$StudyYear;$i++) {
		for ($j=1;$j<=2;$j++) {
			if(!($i==($StudyYear-1) and $j==2)){   //最後一年的下學期不採記
				$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
				$show_year[]=$stud_study_year+$i;
				$show_seme[]=$j;
			}
		}
	}

	//debug_msg("第".__LINE__."行 semes ", $semes);
	//debug_msg("第".__LINE__."行 show_year ", $show_year);
	//debug_msg("第".__LINE__."行 show_seme ", $show_seme);
	//debug_msg("第".__LINE__."行 IS_JHORES ", $IS_JHORES);


	$fin_score=cal_fin_score($sn,$semes,$_POST[fail_num]);
	$all_sn="'".implode("','",array_keys($fin_score))."'";
	$query="select a.*, b.stud_name, b.stud_sex from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.student_sn in ($all_sn) order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$ssn=$res->fields[student_sn];
		$show_sn[$ssn]=$ssn;
		$sclass[$ssn]=$class_base[$res->fields[seme_class]];
		$snum[$ssn]=$res->fields[seme_num];
		$stud_name[$ssn]=$res->fields[stud_name];
		$stud_id[$ssn]=$res->fields[stud_id];
		if($res->fields[stud_sex]=='1'){
			$stud_sex[$ssn]="男";
		}else{
			$stud_sex[$ssn]="女";
		}
		$res->MoveNext();
	}

}

$SemeNum=count($semes);  //學期數
foreach($fin_score as $ShowStudID => $StudSubSemeScore){
	foreach($show_ss as $SubKey => $SubName){
		//debug_msg("第".__LINE__."行 SubKey ", $SubKey);
		$TotalScore=0;
		foreach($semes as $key => $SEME){
			//debug_msg("第".__LINE__."行 SEME ", $SEME);
			//debug_msg("第".__LINE__."行 fin_score[$ShowStudID][$SubKey][$SEME][score] ", $fin_score[$ShowStudID][$SubKey][$SEME][score]);
			if(isset($fin_score[$ShowStudID][$SubKey][$SEME][score]) and $fin_score[$ShowStudID][$SubKey][$SEME][score]>=0){
				$TotalScore+=$fin_score[$ShowStudID][$SubKey][$SEME][score];
			}else{
				$TotalScore="**";
				break 1;
			}
		}
		//debug_msg("第".__LINE__."行 TotalScore ", $TotalScore);
		if($TotalScore!="**"){
			$fin_score[$ShowStudID][AVG][$SubKey]=round($TotalScore/$SemeNum,4);
		}else{
			$fin_score[$ShowStudID][AVG][$SubKey]="**";
		}
	}
}

/*
debug_msg("第".__LINE__."行 semes ", $semes);
debug_msg("第".__LINE__."行 show_year ", $show_year);
debug_msg("第".__LINE__."行 show_seme ", $show_seme);
debug_msg("第".__LINE__."行 show_sn ", $show_sn);
debug_msg("第".__LINE__."行 sclass[264] ", $sclass[264]);
debug_msg("第".__LINE__."行 snum[264] ", $snum[264]);
debug_msg("第".__LINE__."行 stud_id[264] ", $stud_id[264]);
debug_msg("第".__LINE__."行 stud_name[264] ", $stud_name[264]);
debug_msg("第".__LINE__."行 stud_sex[264] ", $stud_sex[264]);
debug_msg("第".__LINE__."行 fin_score[264] ", $fin_score[264]);
debug_msg("第".__LINE__."行 show_ss ", $show_ss);
debug_msg("第".__LINE__."行 si ", $si);
*/

//debug_msg("第".__LINE__."行 main ", $main);


if($save_csv){
	$title_ary0='"'.$school_long_name.'",'."\n".'"注意：",'."\n".'"1.請檢查領域名稱與成績異常者",'."\n".'"2.「**」表示該領域無分數",'."\n".'"3.「_0981」表示98學年度第1學期",';
	$title_ary1=array('','','','','','','','','','','','');
	$title_ary2=array('班級','座號','姓名','學號','性別',"語文(平均)","數學(平均)","自然(平均)","社會(平均)","健體(平均)","藝術與人文(平均)","綜合(平均)");
	foreach($semes as $SEME){
		foreach($show_ss as $SubKey => $SubjectName){
			$title_ary1[]='"_'.$SEME.'"';
			$title_ary2[]='"'.$SubjectName.'"';
		}
	}
	$csv_title1=implode(",",$title_ary1);
	$csv_title2=implode(",",$title_ary2);
	$i=0;
	foreach ($show_sn as $ShowStudID){
		$main[$i]="";
		$main[$i].='"'.$sclass[$ShowStudID].'",';
		$main[$i].='"'.$snum[$ShowStudID].'",';
		$main[$i].='"'.$stud_name[$ShowStudID].'",';
		$main[$i].='"'.$stud_id[$ShowStudID].'",';
		$main[$i].='"'.$stud_sex[$ShowStudID].'",';
		foreach($show_ss as $SubKey => $SubjectName){
			$main[$i].='"'.$fin_score[$ShowStudID][AVG][$SubKey].'",';
		}
		foreach($semes as $SEME){
			foreach($show_ss as $SubKey => $SubjectName){
				if(isset($fin_score[$ShowStudID][$SubKey][$SEME][score])){
					$main[$i].='"'.$fin_score[$ShowStudID][$SubKey][$SEME][score].'",';
				}else{
					$main[$i].='"**",';
				}
			}
		}
		$main[$i].="\n";
		$i++;
	}
	$filename = $class_id."_".time()."_domain_score.csv";

	header("Content-type: text/x-csv ; Charset=Big5");
	header("Content-disposition:attachment ; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $title_ary0."\n".$csv_title1."\n".$csv_title2."\n";
	foreach($main as $num => $my_str){
		echo $my_str;
	}
}



function debug_msg($title, $showarry){
	echo "<pre>";
	echo "<br>$title<br>";
	print_r($showarry);
}


?>