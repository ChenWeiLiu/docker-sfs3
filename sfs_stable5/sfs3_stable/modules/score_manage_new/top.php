<?php
// $Id: top.php 7710 2013-10-23 12:40:27Z smallduh $

/*引入設定檔*/
include "config.php";

//使用者認證
sfs_check();

$use_rate=$_POST['use_rate'];
$target_choice=$_POST['target_choice'];
$tops=$_POST['tops']?$_POST['tops']:$ranks;
$year_seme=$_POST['year_seme'];
$year_name=$_POST['year_name'];
$stage=$_POST['stage'];
$sel=$_POST['sel'];
$save_csv=$_POST['save_csv'];
$bank_data=$_POST['bank_data'];

$html_mode=!($save_csv || $bank_data);
//if ($tops==0) $tops=100;
if (empty($year_seme)) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$score_semester="score_semester_".$sel_year."_".$sel_seme;

//秀出網頁
if ($html_mode)
	head("成績繳交管理");
elseif ($save_csv) {
	$filename="score_top_".$year_seme."_".$year_name."_".$stage.".csv";
        header("Content-type: text/x-csv; Charset=Big5");
	header("Content-disposition: attachment ;filename=$filename");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
}
if (!$html_mode)
	$sel=explode(",",$_POST[all_sn]);

//修正class_id
if ($_POST['fix']) {
	$query="select * from $score_semester where 1=0";
	$res=$CONN->Execute($query);
	if ($res) {
		$query="select student_sn,seme_class from stud_seme where seme_year_seme='$seme_year_seme' order by seme_class,seme_num";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sn_arr[$res->fields['seme_class']][]=$res->fields['student_sn'];
			$res->MoveNext();
		}
		reset($sn_arr);
		while(list($seme_class,$v)=each($sn_arr)) {
			$temp_str="'".implode("','",$v)."'";
			$c=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($seme_class,0,-2),substr($seme_class,-2,2));
			$CONN->Execute("update $score_semester set class_id='$c' where student_sn in ($temp_str)");
		}
	}
	$sel=array();
}

//列出橫向的連結選單模組
if ($html_mode) print_menu($menu_p);

//設定主網頁顯示區的背景顏色
if ($html_mode) echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc><tr><td>";

$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_year_menu =class_year_menu($sel_year,$sel_seme,$year_name);

if($year_name){
	$choice_kind="定期評量";
	$stage_menu =stage_menu($sel_year,$sel_seme,$year_name,$me,$stage,"1");
	$rate_checked=($use_rate)?"checked":"";
	if ($target_choice=="2") 
		$t2_checked="checked";
	else
		$t1_checked="checked";
	$rate_menu="<input type='checkbox' name='use_rate' $rate_checked onclick='this.form.submit()';>加權";
	$target_menu="<input type='radio' name='target_choice' value='1' $t1_checked onclick='this.form.submit()';>全年級<input type='radio' name='target_choice' value='2' $t2_checked onclick='this.form.submit()';>各班";
	$num_menu="列出前<input type='text' name='tops' value='$tops' size='4' maxlength='4'>名<small><font color='#ff0000'>(請先輸入人數再選階段)</font></small>";
}

$menu="<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	<table>
	<tr>
	<td>$year_seme_menu</td><td>$class_year_menu</td><td>$stage_menu</td><td>$rate_menu</td><td>$target_menu</td><td>$num_menu</td>
	</tr>
	</table>";

if ($html_mode) echo $menu."</tr></table><table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#ffffff><tr><td>";

if ($year_name && $stage && count($sel)==0) {
	if ($html_mode) echo "	
		<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0>
		<tr bgcolor='#ffffff'>
		<td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>選取</td>
		<td align='center'>科目</td>
		</tr>
		";
	$prints=($stage<250)?"print='1'":"print<>'1'";
	$sql="select ss_id,scope_id,subject_id,rate from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$year_name' and class_id='' and enable='1' and need_exam='1' and $prints order by sort,sub_sort";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$ss_id=$rs->fields['ss_id'];
		$sj=$rs->fields['subject_id'];
		$rate[$ss_id]=$rs->fields['rate'];
		if (!$sj) $sj=$rs->fields['scope_id'];
		$sql="select subject_name from score_subject where subject_id='$sj'";
		$rs2=$CONN->Execute($sql);
		$subject[$ss_id]=$rs2->fields['subject_name'];
		if (count($sel)=="0") 
			$checked=($rs->fields['scope_id']!="8")?"checked":"";
		else
			$checked=($sel[$ss_id])?"checked":"";
		if ($html_mode) echo "
			<tr bgcolor='#ffffff'>
			<td align='center'><input type='checkbox' name='sel[".$ss_id."]' value='".$ss_id."' $checked></td>
			<td align='center'>".$subject[$ss_id]."</td>
			<input type='hidden' name='rate[".$ss_id."]' value='".$rate[$ss_id]."'>
			<input type='hidden' name='subject[".$ss_id."]' value='".$subject[$ss_id]."'>
			</tr>
			";
		$rs->MoveNext();
	}
		if ($html_mode) echo "
			</table>
			<input type='hidden' name='year_seme' value='$year_seme'>
			<input type='hidden' name='year_name' value='$year_name'>
			<input type='hidden' name='stage' value='$stage'>
			<input type='hidden' name='use_rate' value='$use_rate'>
			<input type='hidden' name='target_choice' value='$target_choice'>
			<input type='hidden' name='tops' value='$tops'>
			<input type='hidden' name='fix' value=''>
			<input type='submit' value='開始處理'> <input type='button' value='先進行class_id修正' OnClick='this.form.fix.value=1;this.form.submit();'>
			</tr>
			</table>
			";
}


if ($year_name && $stage && count($sel)!=0) {
	while(list($k,$v)=each($sel)) {
		$all_sn.=$v.",";
	}
	$all_sn=substr($all_sn,0,-1);
	$sql="select ss_id,scope_id,subject_id,rate from score_ss where class_year='$year_name' and year='$sel_year' and semester='$sel_seme' and enable='1' and print='1' and ss_id in ($all_sn) order by sort,sub_sort";
	$rs=$CONN->Execute($sql);
	$i=0;
	if(is_object($rs)) {
		while (!$rs->EOF) {
			$subject_id[$i]=$rs->fields["subject_id"];
			$ss_id[$i]=$rs->fields["ss_id"];
			$rate[$ss_id[$i]]=($use_rate)?$rs->fields["rate"]:1;
			if (! $subject_id[$i]) $subject_id[$i]=$rs->fields["scope_id"];
			$i++;
			$rs->MoveNext();
		}
		for ($i=0;$i < count($subject_id);$i++) {
			$sql="select subject_name from score_subject where subject_id='$subject_id[$i]'";
			$rs=$CONN->Execute($sql);
			$subject[$i]=$rs->fields["subject_name"];
			$subject_table.="<td width='30' align='center'>".$subject[$i];
			$csv_subject.=$subject[$i].",";
			if ($use_rate) {
				$subject_table.="<br>(x".$rate[$ss_id[$i]].")";
			}
		}
		$Create_db="DROP TABLE IF EXISTS score_top";
		mysql_query($Create_db);  
		$Create_db="		
			CREATE temporary TABLE score_top (
			t_id int(10) unsigned NOT NULL  auto_increment,
			student_sn int(10) unsigned NOT NULL default '0',
			stud_id int(10) unsigned NOT NULL default '0',
			class_num varchar(11) NOT NULL default '',
			score float NOT NULL default '0',
			test_name varchar(80) NOT NULL default '',
			PRIMARY KEY  (t_id))
			";
		mysql_query($Create_db);
		$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$year_name%'";
		$res=$CONN->Execute($query);
		$all_stud_sn="";
		while(!$res->EOF) {
			$all_stud_sn.="'".$res->fields[student_sn]."',";
			$res->MoveNext();
		}
		if ($all_stud_sn) $all_stud_sn=substr($all_stud_sn,0,-1);
		$sql="select a.student_sn,b.stud_id,a.ss_id,a.score,b.curr_class_num from $score_semester a left join stud_base b on a.student_sn=b.student_sn where a.student_sn in ($all_stud_sn) and a.test_kind = '定期評量' and a.test_sort = '$stage' order by b.curr_class_num,a.student_sn,a.ss_id";
		$rs=&$CONN->Execute($sql);
		$sn="";
		$number_ss=count($subject_id);
		$i=0;
		$ci=0;
		while (!$rs->EOF) {
			$student_sn=$rs->fields["student_sn"];
			$stud_id=$rs->fields["stud_id"];
			$sss_id=$rs->fields["ss_id"];
			$sclass_num=substr($rs->fields["curr_class_num"],0,-2);
			$score=$rs->fields["score"];
			if (($student_sn != $sn)&&($i != 0)) {
				$i=0;
				$sum_ss=0;
				$test_ss="";
				$pers=1;
				for ($j=0;$j < $number_ss;$j++) {
					if (($score_ss[$j]=="")||($score_ss[$j]==-100)) {
						$test_ss.="0:";
					} else {
						$sum_ss+=$score_ss[$j]*$rate[$ss_id[$j]];
						if ($rate[$ss_id[$j]]==100) $pers=100;
						$test_ss.=$score_ss[$j].":";
					}
				}
				if ($sclass_num!=$cn) {
					if ($ci==0) $cs_num[$ci]=$cn;
					$ci++;
					$cs_num[$cn]=$sclass_num;
				}
				$sum_ss=$sum_ss/$pers;
				$sql="insert into score_top (student_sn,stud_id,class_num,score,test_name) values ('$sn','$id','$cn','$sum_ss','$test_ss')";
				$rs2=&$CONN->Execute($sql);
				$sn=$student_sn;
				$id=$stud_id;
				for ($j=0;$j < $number_ss;$j++) $score_ss[$j]=0;
			}
			$j=0;
			while (($ss_id[$j] != $sss_id)&&($j < $number_ss)) $j++;
			if ($ss_id[$j]==$sss_id) $score_ss[$j]=$score;	
			$sn=$student_sn;
			$id=$stud_id;
			$cn=$sclass_num;
			$rs->MoveNext();
			$i++;
		}
		$sum_ss=0;
		$test_ss="";
		$pers=1;
		for ($j=0;$j < $number_ss;$j++) {
			if (($score_ss[$j]=="")||($score_ss[$j]==-100)) {
				$test_ss.="0:";
			} else {
				$sum_ss+=$score_ss[$j]*$rate[$ss_id[$j]];
				if ($rate[$ss_id[$j]]==100) $pers=100;
				$test_ss.=$score_ss[$j].":";
			}
		}
		if ($sclass_num!=$cn) {
			if ($ci==0) $cs_num[$ci]=$cn;
			$ci++;
			$cs_num[$cn]=$sclass_num;
		}
		$sum_ss=$sum_ss/$pers;
		$sql="insert into score_top (student_sn,stud_id,class_num,score,test_name) values ('$sn','$id','$cn','$sum_ss','$test_ss')";
		$rs2=&$CONN->Execute($sql);

		$main=" <input type='submit' name='save_csv' value='下載CSV檔'> <input type='submit' name='bank_data' value='下載組距參考表'><input type='hidden' name='all_sn' value='$all_sn'>
			<table cellspacing='1' cellpadding='3' class='main_body'>
    			<tr bgcolor='#FFFFFF'>
    			<td>名次<td>班級<td>座號<td>學號<td width='60' align='center'>姓名".$subject_table."<td width='30' align='center'>總分</td></tr>
    		";
 		if ($target_choice=='2') {
			while(list($k,$v)=each($cs_num)) {
				$main.="<tr></tr>";
				$sql="select * from score_top where class_num='$v' order by score desc";
		    		$rs=&$CONN->Execute($sql);
		    		$i=1;
		    		$no=0;
		    		$lscore=0;
	        		while ((!$rs->EOF)&&($no<=$tops)) {
	        			$student_sn=$rs->fields["student_sn"];
	        			$stud_id=$rs->fields["stud_id"];
	        			$score=$rs->fields["score"];
	        			$test_name=$rs->fields["test_name"];
	        			$show_score="";
	        			$csv_score="";
	        			$sc=explode(":",$test_name);
	        			for ($j=0;$j<$number_ss;$j++) {
	        				$show_score.="<td align='right'>".$sc[$j]."&nbsp;";
	        				$csv_score.=$sc[$j].",";
	        			}
	    				$sql2="select stud_name,curr_class_num from stud_base where student_sn='$student_sn'";
	    				$rs2=&$CONN->Execute($sql2);
	        			$stud_name=$rs2->fields["stud_name"];
	        			$curr_class_num=$rs2->fields["curr_class_num"];
	        			$class=substr($curr_class_num,1,2);
	        			$num=substr($curr_class_num,3,2);
	             			if ($lscore!=$score) {
	             				$no=$i;
	             				$lscore=$score;
	             			}
	       				if ($no<=$tops) {
	       					$main.="<tr bgcolor='#FFFFFF'><td align='right'>".$no."&nbsp;<td align='right'>".$class."&nbsp;<td align='right'>".$num."&nbsp;<td align='right'>".$stud_id."&nbsp;<td>&nbsp;&nbsp;".$stud_name.$show_score."<td align='right'>".$score."&nbsp;</td></tr>";
	       					$print_main.=$no.",".$class.",".$num.",".$stud_id.",".$stud_name.",".$csv_score.$score."\n";
	       				}
	             			$rs->MoveNext();
	             			$i++;
	           		}
			}
		} else {

			//列出組距表
			if ($bank_data) {
				$query="select * from score_top order by score desc";
				$res=$CONN->Execute($query);
				$smarty->assign("rowdata",$res->GetRows());
				$smarty->assign("sel_year",$sel_year);
				$smarty->assign("sel_seme",$sel_seme);
				$smarty->assign("school",get_school_base());
				$smarty->display("score_manage_new_top_bank.tpl");
				exit;
			}

			$sql="select * from score_top order by score desc";
			$rs=&$CONN->Execute($sql);
			$i=1;
			$no=0;
  		while ((!$rs->EOF)&&($no<=$tops)) {
				$student_sn=$rs->fields["student_sn"];
				$stud_id=$rs->fields["stud_id"];
				$score=$rs->fields["score"];
				$test_name=$rs->fields["test_name"];
				$show_score="";
				$csv_score="";
				$sc=explode(":",$test_name);
				for ($j=0;$j<$number_ss;$j++) {
					$show_score.="<td align='right'>".$sc[$j]."&nbsp;";
					$csv_score.=$sc[$j].",";
				}
				$sql2="select stud_name,curr_class_num from stud_base where student_sn='$student_sn'";
				$rs2=&$CONN->Execute($sql2);
				$stud_name=$rs2->fields["stud_name"];
				$curr_class_num=$rs2->fields["curr_class_num"];
				$class=substr($curr_class_num,1,2);
				$num=substr($curr_class_num,3,2);
				if ($lscore!=$score) {
					$no=$i;
					$lscore=$score;
				}
				if ($no<=$tops) {
					$main.="<tr bgcolor='#FFFFFF'><td align='right'>".$no."&nbsp;<td align='right'>".$class."&nbsp;<td align='right'>".$num."&nbsp;<td align='right'>".$stud_id."&nbsp;<td>&nbsp;&nbsp;".$stud_name.$show_score."<td align='right'>".$score."&nbsp;</td></tr>";
					$print_main.=$no.",".$class.",".$num.",".$stud_id.",".$stud_name.",".$csv_score.$score."\n";
				}
				$rs->MoveNext();
				$i++;
			}
		}
	} else {
		if ($html_mode) echo "測試科目未設定";
	}
}

if ($html_mode) 
	echo $main."</table>";
else
	echo "名次,班級,座號,學號,姓名,".$csv_subject."總分\n".$print_main;
//程式檔尾
if ($html_mode) echo "</td></tr></table></form>";
if ($html_mode) foot();
?>
