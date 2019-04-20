<?php
// $Id: manage_ele.php 6649 2011-12-27 05:49:42Z infodaes $

/*引入設定檔*/
include "config.php";

//使用者認證
sfs_check();

$year_seme=$_REQUEST['year_seme'];
$year_name=$_REQUEST['year_name'];
$me=$_REQUEST['me'];
$stage=$_REQUEST['stage'];
$act=$_REQUEST['act'];
$yorn=findyorn();

//秀出網頁
head("分組班成績繳交管理");

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
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
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
	<td>$year_seme_menu<td>$class_year_menu</td><td>$stage_menu</td>
	</tr>
	</table></form>";

echo $menu;

$test_kind=array("0"=>"全學期","1"=>"定期評量","2"=>"平時成績");
if ($year_name && $stage) {
	if ($stage=='255')
		$print="and print!='1'";
	else
		$print="and print='1'";
	$sql="select distinct a.ss_id,a.scope_id,a.subject_id from elective_tea b left join score_ss a on a.ss_id=b.ss_id where a.class_year='$year_name' and a.year='$sel_year' and a.semester='$sel_seme' and a.enable='1' and a.need_exam='1' and a.class_id=''  $print order by a.sort,a.sub_sort";
	$rs=$CONN->Execute($sql);
	$all_ele_ss="";
	if($rs->RecordCount()>0) {
        	while (!$rs->EOF) {
			$ss_id=$rs->fields["ss_id"];
			$all_ele_ss.="'".$ss_id."',";
			$subject_id[$ss_id]=$rs->fields["subject_id"];
			if (! $subject_id[$ss_id]) $subject_id[$ss_id]=$rs->fields["scope_id"];
			$rs->MoveNext();
		}
		if ($all_ele_ss) $all_ele_ss=substr($all_ele_ss,0,-1);
		$rowspans=($stage<250&&$yorn=="y")?"rowspan='2' ":"";
		$subject_table="";
		$state_table="";
		while (list($k,$v)=each($subject_id)) {
			$sql="select subject_name from score_subject where subject_id='$v'";
			$rs=$CONN->Execute($sql);
			$subject[$k]=$rs->fields["subject_name"];
			$spans=($stage<250 && $yorn=="y")?"rowspan='2'":"";
			$subject_table.="<td width='80' $spans align='center'>班級</td>";
			if ($stage<250 && $yorn=="y") {
				$subject_table.="<td width='64' colspan='2' align='center'>".$subject[$k]."</td>";
				$state_table.="<td width='32' align='center'>定期</td><td width='32' align='center'>平時</td>";
			} else {
				$subject_table.="<td width='32' align='center'>".$subject[$k]."</td>";
				$state_table.="";
			}
		}
		$all_sns=array();
		$sql="select a.group_id,a.group_name,a.ss_id,a.teacher_sn from elective_tea a left join score_ss b on a.ss_id=b.ss_id where a.ss_id in ($all_ele_ss) order by b.sort,b.sub_sort,a.group_name";
		$res=$CONN->Execute($sql);
		$old_id="";
		while (!$res->EOF) {
			$id=$res->fields[ss_id];
			if ($id!=$old_id) $i=1;
			$group_data[$i][$id][id]=$res->fields[group_id];
			$group_data[$i][$id][name]=$res->fields[group_name];
			$group_data[$i][$id][teacher_sn]=$res->fields[teacher_sn];
			$i++;
			$old_id=$id;
			$res->MoveNext();
		}
		reset($group_data);
		while(list($i,$v)=each($group_data)) {
			while(list($id,$vv)=each($v)) {
				$query="select * from elective_stu where group_id='".$group_data[$i][$id][id]."'";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$group_data[$i][$id][all_sn].="'".$res->fields[student_sn]."',";
					$group_data[$i][$id][num]++;
					$res->MoveNext();
				}
				$group_data[$i][$id][all_sn]=substr($group_data[$i][$id][all_sn],0,-1);
			}
		}
		$fstage=($stage==254)?1:$stage;
		reset($group_data);
		while(list($i,$v)=each($group_data)) {
			while(list($id,$vv)=each($v)) {
				if ($group_data[$i][$id][all_sn]!="") {
					$sql="select count(score),ss_id,test_kind,sendmit from $score_semester where test_sort='$fstage' and student_sn in (".$group_data[$i][$id][all_sn].") and ss_id='$id' group by ss_id,test_kind,sendmit";
					$rs=$CONN->Execute($sql);
					while(!$rs->EOF) {
						$inputs[$i][$id][$rs->fields["test_kind"]][$rs->fields["sendmit"]]=$rs->fields[0];
						$rs->MoveNext();
					}
					$sql="select count(score),ss_id,test_kind from $score_semester where test_sort='$fstage' and student_sn in (".$group_data[$i][$id][all_sn].") and score='-100' and ss_id='$id' group by ss_id,test_kind";
					$rs=$CONN->Execute($sql);
					$chks[$i][$id][$rs->fields["test_kind"]]=$rs->fields[0];
				}
			}
		}
		reset($group_data);
		while(list($i,$w)=each($group_data)) {
			while(list($id,$vv)=each($w)) {
				$class_id=sprintf("%03d_%d_%02d_",$sel_year,$sel_seme,$year_name).$group_data[$i][$id][id]."_g";
				$teacher_name=get_teacher_name($group_data[$i][$id][teacher_sn]);
				$class_state[$i][$id].="<td bgcolor='#D8DEF6'>".$group_data[$i][$id][name]."<br><font size=2 color='#ff0000'>$teacher_name</font>";
				$snum=$group_data[$i][$id][num];
				if ($yorn=="n") {
					if ($stage==254)
						$v="平時成績";
					elseif ($stage==255)
						$v="全學期";
					else
						$v="定期評量";
					$lock_stat="no";
					$Locks=intval($inputs[$i][$id][$v][0]);
					$Opens=intval($inputs[$i][$id][$v][1]);
					$Nulls=intval($chks[$i][$id][$v]);
					$input_all=$Locks+$Opens;
					$null_all=$snum-$input_all+$Nulls;
					if (($Locks==0 && $Opens==0) || $Nulls==$snum) {
						$sstate="<img src='images/no.png'>";
					} elseif ($null_all>0) {
						if ($Locks>0) {
							$sstate="<img src='images/oh.png' border='0'><br><small>".$null_all."</small>";
							$lock_stat="locked";
						} else {
							$sstate="<img src='images/zero.png' border='0'><br><small>".$null_all."</small>";
						}
					} elseif ($Opens==$snum) {
						$sstate="<img src='images/yes.png' border='0'>";
						$lock_stat="opened";
					} else {
						$sstate="<img src='images/key.png' border='0'>";
						$lock_stat="locked";
					}
					if ($lock_stat=="locked") {
						$sstate="<a href='./openlock.php?score_semester=$score_semester&year_name=$year_name&stage=$stage&class_id=$class_id&ss_id=$id&index=manage_ele&kind=$v'>$sstate</a>";
					}
					if ($lock_stat=="opened") {
						$sstate="<a href='./closelock.php?score_semester=$score_semester&year_name=$year_name&stage=$stage&class_id=$class_id&ss_id=$id&index=manage_ele&kind=$v'>$sstate</a>";
					}
					$class_state[$i][$id].="<td align='center'><a href=./score_query.php?year_seme=$year_seme&year_name=$year_name&me=$me&stage=$stage&kind=1 target='new'><img src='../../images/filefind.png' width='16' height='16' hspace='3' border='0'></a><br>$sstate</td>";
				} else {
					reset($test_kind);
					while(list($k,$v)=each($test_kind)) {
						$lock_stat="no";
						$Locks=intval($inputs[$i][$id][$v][0]);
						$Opens=intval($inputs[$i][$id][$v][1]);
						$Nulls=intval($chks[$i][$id][$v]);
						$input_all=$Locks+$Opens;
						$null_all=$snum-$input_all+$Nulls;
						if (($stage<250 && $k>0) || ($stage>250 && $k==0)) {
							if (($Locks==0 && $Opens==0) || $Nulls==$snum) {
								$sstate="<img src='images/no.png'>";
							} elseif ($null_all>0) {
								if ($Locks>0) {
									$sstate="<img src='images/oh.png' border='0'><br><small>".$null_all."</small>";
									$lock_stat="locked";
								} else {
									$sstate="<img src='images/zero.png' border='0'><br><small>".$null_all."</small>";
								}
							} elseif ($Opens==$snum) {
								$sstate="<img src='images/yes.png' border='0'>";
								$lock_stat="opened";
							} else {
								$sstate="<img src='images/key.png' border='0'>";
								$lock_stat="locked";
							}
							if ($lock_stat=="locked") {
								$sstate="<a href='./openlock.php?score_semester=$score_semester&year_name=$year_name&stage=$stage&class_id=$class_id&ss_id=$id&index=manage_ele&kind=$v'>$sstate</a>";
							}
							if ($lock_stat=="opened") {
								$sstate="<a href='./closelock.php?score_semester=$score_semester&year_name=$year_name&stage=$stage&class_id=$class_id&ss_id=$id&index=manage_ele&kind=$v'>$sstate</a>";
							}
							$class_state[$i][$id].="<td align='center'><a href=./score_query.php?year_seme=$year_seme&year_name=$year_name&me=$me&stage=$stage&kind=1 target='new'><img src='../../images/filefind.png' width='16' height='16' hspace='3' border='0'></a><br>$sstate</td>";
						}
					}
				}
			}
		}
	} else {
		echo "<br>分組測驗科目未設定<br>";
	}
	$main="	<table cellpadding='5' cellspacing='1' border='0' bgcolor='#0000ff' align='left'>
		<tr bgcolor='#B8BEF6'>
		$subject_table
		</tr>";
	if ($stage<250 && $yorn=="y")
		$main.="<tr bgcolor='#B8BEF6'>$state_table</tr>";
	while (list($i,$v)=each($class_state)) {
		$main.="<tr bgcolor='#FFFFFF'>";
		while(list($id,$vv)=each($v)) {
			$main.=$class_state[$i][$id];
		}
		$main.="</tr>\n";
	}
}

//程式檔尾
echo $main."</table></td></tr></table>";
if ($year_name && $stage)
	echo "
	<table width='100%' cellpadding='5' cellspacing='0' border='0' align='left'>
	<tr bgcolor='#FBFBC4'><td colspan='2'><img src='../../images/filefind.png' width=16 height=16 hspace=3 border=0>相關說明</td></tr>
	<tr><td align='center'><img src='images/no.png'><td>全班成績都未輸入</td></tr>
	<tr><td align='center'><img src='images/zero.png'><td>部份學生成績未輸入，但未傳送到教務處</td></tr>
	<tr><td align='center'><img src='images/oh.png'><td>部份學生成績未輸入，但已傳送到教務處，按一下可開鎖</td></tr>
	<tr><td align='center'><img src='images/yes.png'><td>成績已經輸入，但未傳送到教務處，按一下可鎖定</td></tr>
	<tr><td align='center'><img src='images/key.png'><td>成績已經傳送到教務處並鎖定，按鑰匙打開鎖定，讓老師能重新上傳成績</td></tr>
	</table>";
foot();

?>
