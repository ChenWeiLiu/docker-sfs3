<?php
// $Id: avg.php 6245 2010-10-24 15:37:13Z brucelyc $

/*引入設定檔*/
include "config.php";

//使用者認證
sfs_check();

$year_seme=$_REQUEST['year_seme'];
$year_name=$_REQUEST['year_name'];
$stage=$_REQUEST['stage'];
$score_sort=$_REQUEST['score_sort'];
$sel=$_POST['sel'];
$sel_class=$_POST['sel_class'];
$print_special=$_POST['print_special'];

//秀出網頁
head("成績繳交管理");

//列出橫向的連結選單模組
print_menu($menu_p);

//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc><tr><td>";

if (empty($year_seme)) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
$score_semester="score_semester_".$sel_year."_".$sel_seme;

$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_year_menu =class_year_menu($sel_year,$sel_seme,$year_name);

if($year_name){
	$choice_kind="定期評量";
	$stage_menu =stage_menu($sel_year,$sel_seme,$year_name,$me,$stage,"1");
}

$menu="<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	<table>
	<tr>
	<td>$year_seme_menu</td><td>$class_year_menu</td><td>$stage_menu</td>
	</tr>
	</table></form>";

echo $menu."</tr></table><table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#ffffff><tr><td>";


if ($year_name && $stage && (count($sel)==0 || count($sel_class)==0)) {
	echo "
		<table border=0 cellpadding=0 cellspacing=0><tr>
		<form name='form4' method='post' action='{$_SERVER['PHP_SELF']}'>
		<td valign=top>
		<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0>
		<tr bgcolor='#ffffff'>
		<td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>選取</td>
		<td align='center'>科目</td>
		</tr>
		";
	if ($score_sort) $chk="checked";
	$sql="select * from score_ss where class_id='".sprintf("%03s_%s_%02s_%02s",$sel_year,$sel_seme,$year_name,$me)."' and enable='1'";
	$rs=$CONN->Execute($sql);
	if ($rs->RecordCount() ==0){
		$sql="select ss_id,scope_id,subject_id,rate from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$year_name' and enable='1' and print='1' and class_id='' order by sort,sub_sort";
	}
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
		echo "
			<tr bgcolor='#ffffff'>
			<td align='center'><input type='checkbox' name='sel[".$ss_id."]' value='".$ss_id."' $checked></td>
			<td align='center'>".$subject[$ss_id]."</td>
			<input type='hidden' name='rate[".$ss_id."]' value='".$rate[$ss_id]."'>
			<input type='hidden' name='subject[".$ss_id."]' value='".$subject[$ss_id]."'>
			</tr>
			";
		$rs->MoveNext();
	}
	echo "
		</table>
		<input type='hidden' name='year_seme' value='$year_seme'>
		<input type='hidden' name='year_name' value='$year_name'>
		<input type='hidden' name='stage' value='$stage'><br>
		<input type='checkbox' checked name='score_sort'>顯示排名 
		<input type='checkbox' name='print_special' value='on' checked>套用排除名單<br><br>
		<input type='submit' value='開始處理'>
		</tr>
		</table>
		</td><td>&nbsp;&nbsp;</td><td valign=top>
		<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0>
		<tr bgcolor='#ffffff'>
		<td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>選取</td>
		<td align='center'>班級</td>";
	$query="select * from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$year_name' order by c_sort";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$c_name=$res->fields[c_name];
		$c_sort=$res->fields[c_sort];
		if (count($sel_class)==0)
			$checked="checked";
		else 
			$checked=($sel_class[$c_sort])?"checked":"";
		echo "
			<tr bgcolor='#ffffff'>
			<td align='center'><input type='checkbox' name='sel_class[".$c_sort."]' value='".$c_sort."' $checked></td>
			<td align='center'>".$class_year[$year_name].$c_name."班</td>
			</tr>
			";
		$res->MoveNext();
	}
	echo "
		</tr></table>
		</tr>
		</form>
		</table>";
}
if (count($sel)>0 && count($sel_class)>0) {
if ($year_name && $stage) {
	//----取得排除列表 student_sn
	$eliminate_sn=array();
	$query_eliminate="select * from score_manage_out where year='$sel_year' and semester='$sel_seme'";
	$res_eliminate=$CONN->Execute($query_eliminate);
	while(!$res_eliminate->EOF) {
		$student_sn=$res_eliminate->fields["student_sn"];
		$eliminate_sn[$student_sn]=$res_eliminate->fields["reason"];
		$res_eliminate->MoveNext();
	}
	while(list($k,$v)=each($sel)) {
		$all_ss.=$v.",";
	}
	$Create_db="CREATE TABLE if not exists temp_sort (
		class_id varchar(11) NOT NULL default '',
		ss_id smallint(5) unsigned NOT NULL default '0',
		score decimal(10,2) default NULL,
		PRIMARY KEY (class_id,ss_id)
		)";
	$CONN->Execute($Create_db);
	$all_ss=substr($all_ss,0,-1);
	$sql="select ss_id,scope_id,subject_id,rate from score_ss where class_year='$year_name' and year='$sel_year' and semester='$sel_seme' and enable='1' and print='1' and ss_id in ($all_ss) order by sort,sub_sort";
	$rs=$CONN->Execute($sql);
	$i=0;
	if(is_object($rs)) {
		while (!$rs->EOF) {
			$ss_id=$rs->fields["ss_id"];
			$subject_id[$ss_id]=$rs->fields["subject_id"];
			$rate[$ss_id]=($use_rate)?$rs->fields["rate"]:1;
			if (!$subject_id[$ss_id]) $subject_id[$ss_id]=$rs->fields["scope_id"];
			$i++;
			$rs->MoveNext();
		}
		//製作科目標題 
		while(list($i,$v)=each($subject_id)) {
			$sql="select subject_name from score_subject where subject_id='$v'";
			$rs=$CONN->Execute($sql);
			$subject[$i]=$rs->fields["subject_name"];
			$subject_table.="<td width='30' align='center'>".$subject[$i];
			if ($use_rate) $subject_table.="<br>(x".$rate[$i].")";
		}
		$subject_table.="<td width='30' align='center'>總分";
		$all_class="'".implode("','",$sel_class)."'";
		
		//取得 班級列表
		$sql="select class_id,c_name from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$year_name' and c_sort in ($all_class) order by class_id";
		$rs=$CONN->Execute($sql);
		while (!$rs->EOF) {
			$class_id=$rs->fields["class_id"];
			$c_class[$class_id]=$rs->fields["c_name"];
			$cid=explode("_",$class_id);
			$seme_class=$year_name.sprintf("%02d",$cid[3]);
			$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
			$sql_s="select a.student_sn,a.seme_class,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and b.stud_study_cond='0' and a.student_sn=b.student_sn order by a.student_sn";
			$rs_s=$CONN->Execute($sql_s);
			while (!$rs_s->EOF) {
				$student_sn=$rs_s->fields['student_sn'];
				$stud_name=$rs_s->fields['stud_name'];
				$seme_class=$rs_s->fields['seme_class'];
				$seme_num=$rs_s->fields['seme_num'];
				//套用排除名單判斷
				if($print_special)
				{
					if(array_key_exists($student_sn,$eliminate_sn)) {
						$eliminate_list_array[$student_sn]="( $seme_class-$seme_num )$stud_name:".$eliminate_sn[$student_sn];
					}  else $all_sn[$class_id].="$student_sn,";
				} else 
				$all_sn[$class_id].="$student_sn,";
				
				$rs_s->MoveNext();
			}
			$all_sn[$class_id]=substr($all_sn[$class_id],0,-1);
			$rs->MoveNext();
		}
		//取得 成績統計資料
		while(list($k,$v)=each($c_class)) {
			$sql="select sum(score) as s,count(score)as c,ss_id from $score_semester where class_id='$k' and test_kind='定期評量' and test_sort='$stage' and student_sn in ($all_sn[$k]) and score<>'-100' group by class_id,ss_id";
			$rs=&$CONN->Execute($sql);
			while (!$rs->EOF) {
				$sss_id=$rs->fields["ss_id"];
				$score_sum[$k][$sss_id]=$rs->fields["s"];
				$score_num[$k][$sss_id]=$rs->fields["c"];
				$avg_score=number_format($score_sum[$k][$sss_id]/$score_num[$k][$sss_id],2);
				$CONN->Execute("insert into temp_sort (class_id,ss_id,score) values ('$k','$sss_id','$avg_score')");
				$rs->MoveNext();
			}
		}
		//進行班級成績排比
		$query="select * from temp_sort order by ss_id,score desc";
		$res=$CONN->Execute($query);
		$temp_ss_id="";
		while (!$res->EOF) {
			$tsid=$res->fields['ss_id'];
			$temp_class_id=$res->fields['class_id'];
			if ($temp_ss_id!=$tsid) $temp_num=1;
			$sort[$temp_class_id][$tsid]=$temp_num;
			$temp_ss_id=$tsid;
			$temp_num++;
			$res->MoveNext();
		}
		$rows=($score_sort)?3:2; 
		reset($c_class);
		while(list($k,$v)=each($c_class)) {
			$class_score.="<tr bgcolor='#FFFFFF'><td rowspan='$rows'>".$class_year[$year_name].$v."班</td><td>平均</td>";
			reset($subject_id);
			while(list($i,$j)=each($subject_id)) {
				if ($score_sum[$k][$i]) {
					$class_score.="<td>".number_format($score_sum[$k][$i]/$score_num[$k][$i],2)."</td>";
					$all_sum[$i]+=$score_sum[$k][$i];
					$class_sum[$v]+=number_format($score_sum[$k][$i]/$score_num[$k][$i],2);
				} else
					$class_score.="<td> </td>";
			}
			$class_score.="<td>".$class_sum[$v]."</td>";
			$CONN->Execute("insert into temp_sort (class_id,ss_id,score) values ('$k','0','$class_sum[$v]')");
			$class_score.="</tr>\n<tr bgcolor='#FFFFFF'><td>人數</td>";
			reset($subject_id);
			while(list($i,$j)=each($subject_id)) {
				if ($score_sum[$k][$i]) {
					$class_score.="<td align='center'>".$score_num[$k][$i]."</td>";
					$all_num[$i]+=$score_num[$k][$i];
				} else
					$class_score.="<td> </td>";
			}
			$class_score.="<td align='center'>---</td>";
			if ($score_sort) {
				$class_score.="</tr>\n<tr bgcolor='#FFFFFF'><td>排名</td>";
				reset($subject_id);
				while(list($i,$j)=each($subject_id)) {
					if ($sort[$k][$i]) {
						$class_score.="<td align='center'>".$sort[$k][$i]."</td>";
					} else
						$class_score.="<td> </td>";
				}
				$class_score.="<td align='center'>$k</td>";
			}
			$class_score.="</tr>\n";
		}
		$query="select * from temp_sort where ss_id='0' order by score desc";
		$res=$CONN->Execute($query);
		$i=1;
		while (!$res->EOF) {
			$class_score=str_replace($res->fields[class_id],$i,$class_score);
			$i++;
			$res->MoveNext();
		}
		$class_score.="<tr bgcolor='#FFFFFF'><td rowspan='4'>全年級</td><td>均標</td>";
		reset($all_sum);
		while(list($i,$j)=each($all_sum)) {
			if ($all_sum[$i]) 
				$class_score.="<td align='center'>".number_format($all_sum[$i]/$all_num[$i],2)."</td>";
			else
				$class_score.="<td> </td>";
			$allsum+=number_format($all_sum[$i]/$all_num[$i],2);
		}
		$class_score.="<td>$allsum</td></tr>\n";
		//計算高標
		$class_score.="</tr>\n<tr bgcolor='#FFFFFF'><td>高標</td>";
		reset($subject_id);
		while(list($i,$v)=each($subject_id)) {
			$temp_sum=0;
			$stud_num=round($all_num[$i]/2);
			$query="select score from $score_semester where ss_id='$i' and test_kind='定期評量' and test_sort='$stage' and score<>'-100' order by score desc limit 0,$stud_num";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$temp_sum+=$res->fields['score'];
				$res->MoveNext();
			}
			$class_score.="<td>".number_format($temp_sum/$stud_num,2)."</td>";
		}
		$class_score.="<td align='center'>---</td>";
		//計算低標
		$class_score.="</tr>\n<tr bgcolor='#FFFFFF'><td>低標</td>";
		reset($subject_id);
		while(list($i,$v)=each($subject_id)) {
			$temp_sum=0;
			$stud_num=round($all_num[$i]/2);
			$query="select score from $score_semester where ss_id='$i' and test_kind='定期評量' and test_sort='$stage' and score<>'-100' order by score limit 0,$stud_num";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$temp_sum+=$res->fields['score'];
				$res->MoveNext();
			}
			$class_score.="<td>".number_format($temp_sum/$stud_num,2)."</td>";
		}
		$class_score.="<td align='center'>---</td>";
		$class_score.="</tr>\n<tr bgcolor='#FFFFFF'><td>人數</td>";
		reset($all_num);
		while(list($i,$j)=each($all_num)) {
			if ($all_num[$i]) 
				$class_score.="<td align='center'>".$all_num[$i]."</td>";
			else
				$class_score.="<td> </td>";
		}
		$class_score.="<td align='center'>---</td></tr>\n";
    		$main="
    			<table cellspacing='1' cellpadding='3' class='main_body'>
    			<tr>
    			<td align='center' colspan='2'>班級".$subject_table."</tr>$class_score
    		";
	} else {
		echo "測試科目未設定";
        }
}
}
if(count($eliminate_list_array))
{
	$eliminate_list="<tr><td colspan='".(count($sel)+3)."'>◎排除名單列表：<BR>";
	foreach($eliminate_list_array as $value) $eliminate_list.="<li>$value</li>";
	$eliminate_list.="</td></tr>";	
}
echo $main."$eliminate_list</table>";
//程式檔尾
echo "</td>";
echo "</tr>";
echo "</table>";
$CONN->Execute("drop table temp_sort");
foot();

?>
