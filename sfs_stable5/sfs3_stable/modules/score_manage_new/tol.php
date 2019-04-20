<?php
// $Id: tol.php 8830 2016-02-26 07:34:39Z infodaes $
/*引入設定檔*/
include "config.php";

//使用者認證
sfs_check();

$year_seme=$_REQUEST['year_seme'];
$year_name=$_REQUEST['year_name'];
$me=$_REQUEST['me'];
$go=$_POST['go'];
$friendly_print=$_GET['friendly_print'];
$save_csv=$_GET['save_csv'];

$percision=$_REQUEST['percision'];

$yorn=findyorn();

if ($friendly_print==0) {
	$border="0";
	$bgcolor1="#FDC3F5";
	$bgcolor2="#B8FF91";
	$bgcolor3="#CFFFC4";
	$bgcolor4="#B4BED3";
	$bgcolor5="#CBD6ED";
	$bgcolor6="#D8E4FD";
} else {
	$border="1";
	$bgcolor1="#FFFFFF";
	$bgcolor2="#FFFFFF";
	$bgcolor3="#FFFFFF";
	$bgcolor4="#FFFFFF";
	$bgcolor5="#FFFFFF";
	$bgcolor6="#FFFFFF";
}
//秀出網頁
if ($friendly_print != 1  && ! $save_csv) head("班級成績總表");

//列出橫向的連結選單模組
if ($friendly_print != 1 && ! $save_csv) print_menu($menu_p);

//設定主網頁顯示區的背景顏色
if ($friendly_print != 1 && ! $save_csv) echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#ffffff'>";
if ($year_seme) {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
}
$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_year_menu=class_year_menu($sel_year,$sel_seme,$year_name);

if($year_name)	$class_year_name_menu=class_name_menu($sel_year,$sel_seme,$year_name,$me);

if($me) {
	$percision_radio="<font size=2 color='red'> ◎成績顯示的精度：";
	$percision_array=array('1'=>'整數','2'=>'小數1位','3'=>'小數2位');
	foreach($percision_array as $key=>$value){
		if($percision==$key) $checked='checked'; else $checked='';
		$percision_radio.="<input type='radio' value='$key' name='percision' $checked onclick='this.form.submit();'>$value";	
	}
}

$menu="<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	<table>
	<tr>
	<td>$year_seme_menu</td><td>$class_year_menu</td><td>$class_year_name_menu $percision_radio</td>
	</tr>
	</table></form>";
if ($friendly_print != 1 && ! $save_csv) echo $menu;

//以上為選單bar

/******************************************************************************************/
if($year_name && $me && $percision){
	$percision--;
	//取得學校資料
	$s=get_school_base();
	
	$ss_val=array("表現優異"=>"5","表現良好"=>"4","表現尚可"=>"3","需再加油"=>"2","有待改進"=>"1");
	$sql="select subject_id,subject_name from score_subject where enable='1'";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$subject_name[$rs->fields['subject_id']]=addslashes($rs->fields['subject_name']);
		$rs->MoveNext();
	}
	//092_2_01_01
	$sql="select * from score_ss where class_id='".sprintf("%03s_%s_%02s_%02s",$sel_year,$sel_seme,$year_name,$me)."' and enable='1' and need_exam='1'";
	$rs=$CONN->Execute($sql);
	if ($rs->RecordCount() ==0){
		$sql="select * from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$year_name' and enable='1' and need_exam='1' and class_id='' order by sort,sub_sort";
		$rs=$CONN->Execute($sql);
	}

	$subject_list="";
	$subject_kind="";
	while (!$rs->EOF) {
		$id=$rs->fields['ss_id'];
		$scope_id=$rs->fields['scope_id'];
		$subject_id=$rs->fields['subject_id'];
		$ss_id[$id]=($subject_id==0)?$scope_id:$subject_id;
		$all_rate += $rs->fields['rate'];
		$s_rate[$id] = $rs->fields['rate'];
		if ($friendly_print==1) {
			$subject_list.="<td width='40' align='center' colspan='2'><p align='left'><span style='font-size:10pt;'>".stripslashes($subject_name[$ss_id[$id]])."</span></p></td>";
			$subject_kind.="<td width='20' alifn='center'><p align='center'><span style='font-size:8pt;'>分數</span></p></td><td width='20' alifn='center'><p align='center'><span style='font-size:8pt;'>努力程度</span></p></td>";
		} 
		else if($save_csv ==1){
			$subject_kind.=stripslashes($subject_name[$ss_id[$id]])."分數,".stripslashes($subject_name[$ss_id[$id]])."努力程度,";			
		}	else if($save_csv ==2){
			$subject_kind.=stripslashes($subject_name[$ss_id[$id]]).",";			
		}	
		else {
			$subject_list.="<td width='40' align='center' colspan='2'><small>".stripslashes($subject_name[$ss_id[$id]])."</small></td>";
			$subject_kind.="<td width='20' align='center' bgcolor='#afcdff'><small>分數</small></td><td width='20' alifn='center'><small>努力程度</small></td>";
		}
		$rs->MoveNext();
	}
	
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$year_name.sprintf("%02d",$me);
	if($save_csv==2) $sql="select student_sn,stud_id,seme_class,seme_class_name,seme_num from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '".substr($seme_class,0,-2)."%' order by seme_class,seme_num,student_sn";
		else $sql="select student_sn,stud_id,seme_class_name,seme_num from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class' order by seme_num,student_sn";
	$rs=$CONN->Execute($sql);
	$all_sn="";
	$all_id="";
	$st_sn_all = array();
	while (!$rs->EOF) {
		$student_sn=$rs->fields['student_sn'];
		$st_sn_all[] = $student_sn;
		$stud_id[$student_sn]=$rs->fields['stud_id'];
		$stud_num[$student_sn]=$rs->fields['seme_num'];
		$stud_name[$student_sn]=$rs->fields['stud_name'];
		$seme_class_name[$student_sn]=$rs->fields['seme_class_name'];
		$all_sn.="'".$student_sn."',";
		$all_id.="'".$stud_id[$student_sn]."',";
		$rs->MoveNext();
	}
	$all_sn=substr($all_sn,0,-1);
	$all_id=substr($all_id,0,-1);
	$sql="select student_sn,stud_name from stud_base where student_sn in ($all_sn)";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$stud_name[$rs->fields['student_sn']]=$rs->fields['stud_name'];
		$rs->MoveNext();
	}
	$sql="select student_sn,ss_id,ss_score from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn in ($all_sn)";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$stud_score[$rs->fields['student_sn']][$rs->fields['ss_id']]=$rs->fields['ss_score'];
		$stud_avg[$rs->fields['student_sn']] += ($rs->fields['ss_score']*$s_rate[$rs->fields['ss_id']]/$all_rate);
		$rs->MoveNext();
	}
	$sql="select stud_id,ss_id,ss_val  from stud_seme_score_oth  where seme_year_seme='$seme_year_seme' and stud_id in ($all_id) and ss_kind='努力程度'";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$stud_val[$rs->fields['stud_id']][$rs->fields['ss_id']]=$rs->fields['ss_val'];
		$rs->MoveNext();
	}
	$sql="select student_sn,ss_score from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and ss_id='0' and student_sn in ($all_sn) ";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$stud_nor[$rs->fields['student_sn']]=$rs->fields['ss_score'];
		$rs->MoveNext();
	}
	$student_and_score_list="";
	for($k=0;$k<count($st_sn_all);$k++){
	
		reset($ss_id);
		if ($friendly_print==1)
			$student_and_score_list.="<tr><td align='right'><p align='right'><span style='font-size:10pt;'>".$stud_num[$st_sn_all[$k]]."</span></p></td><td><p align='right'><span style='font-size:10pt;'>".$stud_name[$st_sn_all[$k]]."</span></p></td>";
		else if($save_csv==1)
			$student_and_score_list.= $stud_id[$st_sn_all[$k]].",".$stud_num[$st_sn_all[$k]].",".$stud_name[$st_sn_all[$k]].",";
		else if($save_csv==2)
			$student_and_score_list.= $seme_class_name[$st_sn_all[$k]].",".$stud_id[$st_sn_all[$k]].",".$stud_num[$st_sn_all[$k]].",".$stud_name[$st_sn_all[$k]].",";
		else
			$student_and_score_list.="<tr bgcolor='#c4d9ff'><td bgcolor='#d5eaff' align='right'>".$stud_num[$st_sn_all[$k]]."</td><td bgcolor='#d5eaff'>".$stud_name[$st_sn_all[$k]]."</td>";
		while (list($id,$subject_id)=each($ss_id)) {
			$sval=$ss_val[$stud_val[$stud_id[$st_sn_all[$k]]][$id]];
			if ($sval=="") $sval="-";
			if ($friendly_print==1)
				$student_and_score_list.="<td><p align='right'><span style='font-size:10pt;'>".number_format($stud_score[$st_sn_all[$k]][$id],$percision)."</span></p></td><td align='center'><p align='right'><span style='font-size:10pt;'>".$sval."</span></p></td>";
			else if($save_csv==1)
				$student_and_score_list .= number_format($stud_score[$st_sn_all[$k]][$id],$percision).",".$sval.",";
			else if($save_csv==2)
				$student_and_score_list .= number_format($stud_score[$st_sn_all[$k]][$id],$percision).",";
			else {
				if($stud_score[$st_sn_all[$k]][$id]<60) $bgcolor='#ffcccc'; else if($stud_score[$st_sn_all[$k]][$id]<70) $bgcolor='#ddffff'; else if($stud_score[$st_sn_all[$k]][$id]<80) $bgcolor='#ffffcc'; else $bgcolor='#ffffff';
				$student_and_score_list.="<td bgcolor='$bgcolor'>".number_format($stud_score[$st_sn_all[$k]][$id],$percision)."</td><td align='center'><font color='#000088'>".$sval."</font></td>";
				}
		}
			if ($friendly_print==1)
				$student_and_score_list.="<td><p align='right'><span style='font-size:10pt;'>".number_format($stud_avg[$st_sn_all[$k]],$percision)."</span></p></td><td><p align='right'><span style='font-size:10pt;'>".number_format($stud_nor[$st_sn_all[$k]],$percision)."</span></p></td>";
			else if($save_csv==1)
				$student_and_score_list.= number_format($stud_avg[$st_sn_all[$k]],$percision).",".number_format($stud_nor[$st_sn_all[$k]],$percision).",";
			else if($save_csv==2)
				$student_and_score_list.= number_format($stud_avg[$st_sn_all[$k]],$percision).",";
			else {
				if($stud_avg[$st_sn_all[$k]]<60) $bgcolor='#ffcccc'; else if($stud_avg[$st_sn_all[$k]]<70) $bgcolor='#ddffff'; else if($stud_avg[$st_sn_all[$k]]<80) $bgcolor='#ffffcc'; else $bgcolor='#ffffff';
				$student_and_score_list.="<td bgcolor='$bgcolor'><p align='right'>".number_format($stud_avg[$st_sn_all[$k]],$percision)."</p></td><td bgcolor='#EEFFDD'>".number_format($stud_nor[$st_sn_all[$k]],$percision)."</td>";
				}
		if($save_csv)
			$student_and_score_list.="\n";
		else
			$student_and_score_list.="</tr>\n";
	}
	if ($friendly_print==1)
		$main=" <p align='center'><b>$s[sch_cname] $sel_year 學年度 第$sel_seme 學期$year_name 年 $me 班學期成績總表</b></p>
			<table border='1' cellspacing='0' height='12' bordercolordark='white' bordercolorlight='black'>
			<tr>
			<td width='30' align='center' rowspan='2'><p align='center'><span style='font-size:10pt;'>座號</span></p></td>
			<td width='50' align='center' rowspan='2'><p align='center'><span style='font-size:10pt;'>姓名</span></p></td>
			$subject_list
			<td width='20' align='center'><span style='font-size:10pt;'>加權平均</span></td>
			<td width='20' align='center'><span style='font-size:10pt;'>綜合表現</span></td>
			</tr>
			<tr>
			$subject_kind
			<td align='center'><span style='font-size:10pt;'>分數</span></td>
			<td align='center'><span style='font-size:10pt;'>分數</span></td>
			</tr>
			$student_and_score_list
			</table>";
	else if($save_csv==1){
   		$filename = $year_seme."_".$year_name."_".$me."_tolscore.csv";

		header("Content-type: text/x-csv ; Charset=Big5");
		header("Content-disposition: attachment ;filename=$filename");
		//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
		header("Expires: 0");
		echo $s[sch_cname].$sel_year."學年度第".$sel_seme."學期".$year_name."年".$me."班學期成績總表\n";
		echo "學號,座號,姓名,".$subject_kind."加權平均,綜合表現\n";	
		echo $student_and_score_list;

		exit;
	}
	else if($save_csv==2){
   		$filename = $sel_year."學年度 第".$sel_seme."學期 ".$year_name."年級學期成績總表.csv";

        header("Content-type: text/x-csv ; Charset=Big5");
		header("Content-disposition: attachment ;filename=$filename");
		//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
		header("Expires: 0");
		echo $s[sch_cname].$sel_year."學年度第".$sel_seme."學期".$year_name."年級學期成績總表\n";
		echo "班級,學號,座號,姓名,".$subject_kind."加權平均\n";	
		echo $student_and_score_list;

		exit;
	}
	else {
		$print_msg="<a href='{$_SERVER['PHP_SELF']}?year_seme=$year_seme&year_name=$year_name&me=$me&friendly_print=1&percision=$_REQUEST[percision]' target='new'><b><small>友善列印</small></b></a>&nbsp;&nbsp;&nbsp;"; 
		$print_msg.="<a href='{$_SERVER['PHP_SELF']}?year_seme=$year_seme&year_name=$year_name&me=$me&save_csv=1&percision=$_REQUEST[percision]'><b><small>匯出本班的csv檔</small></b></a>&nbsp;&nbsp;&nbsp;";
		$print_msg.="<a href='{$_SERVER['PHP_SELF']}?year_seme=$year_seme&year_name=$year_name&me=$me&save_csv=2&percision=$_REQUEST[percision]'><b><small>匯出全年級csv檔(不含努力程度)</small></b></a><br>";
		$main=" $print_msg
			<table bgcolor='#9ebcdd' border=$border cellpadding='6' cellspacing='1'>
			<tr bgcolor='#c4d9ff'>
			<td width='30' align='center' rowspan='2'><small>座號</small></td>
			<td width='80' align='center' rowspan='2'><small>姓名</small></td>
			$subject_list 
			<td width='20' align='center' bgcolor='#FFFF99'><span style='font-size:10pt;'>加權平均</span></td>
			<td width='20' align='center' bgcolor='#CCFF99'><span style='font-size:10pt;'>綜合表現</span></td>

			</tr>
			<tr>
			$subject_kind
			<td align='center' bgcolor='#FFFF99'><span style='font-size:10pt;'>分數</span></td>
			<td align='center' bgcolor='#CCFF99'><span style='font-size:10pt;'>分數</span></td>
			</tr>
			$student_and_score_list
			</table>";
	}

	$help_text="努力程度： 5:表現優異, 4:表現良好, 3:表現尚可, 2:需再加油, 1:有待改進";
	echo $main;
	if ($friendly_print != 1 && ! $save_csv) echo help($help_text);
}

//結束主網頁顯示區
if ($friendly_print != 1 && ! $save_csv) echo "</td></tr></table>";

//程式檔尾
if ($friendly_print != 1 && ! $save_csv) foot();

?>
