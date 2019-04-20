<?php

// $Id: reward_total.php 7709 2013-10-23 12:24:27Z smallduh $

// 載入設定檔
include "config.php";
include "../../include/sfs_case_dataarray.php";
include "../../include/sfs_oo_zip2.php";

// 認證檢查
sfs_check();

//印出檔頭
if ($_POST[act]!='列印') head("學生獎懲人數統計");

$tool_bar=&make_menu($student_menu_p);
$today=date("Y-m-d");

//取得學年學期
$year_seme=$_REQUEST[year_seme];
if ($year_seme) {
	$sel_year=intval(substr($year_seme,0,3));
	$sel_seme=substr($year_seme,3,1);
}

//學年學期選單
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$year_seme_p=get_class_seme();
$year_seme_select = "<select name='year_seme' onchange='this.form.submit()';>\n<option value=''>請選學年學期</option>\n";
while (list($k,$v)=each($year_seme_p)){
	if ($seme_year_seme==$k)
      		$year_seme_select.="<option value='$k' selected>$v</option>\n";
      	else
      		$year_seme_select.="<option value='$k'>$v</option>\n";
}
$year_seme_select.= "</select>"; 

$main="	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<form action='$_SERVER[PHP_SELF]' method='post'>
	<tr class='title_sbody2'>
	<td>請選學年度<td align='left' bgcolor='white' colspan='2'>$year_seme_select<input type='hidden' name='act' value='cal'>
	</tr>
	</form>
	</table>\n";
if ($_POST[act]) {
	for ($i=1;$i<=3;$i++) {
		$cyear=$i+$IS_JHORES;
		$reward_year_seme=$sel_year.$sel_seme;
		$seme_year_seme=sprintf("%04d",$reward_year_seme);
		for ($j=1;$j<=2;$j++) {
			$all_id="";
			$query="select a.stud_id from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_class like '$cyear%' and a.seme_year_seme='$seme_year_seme' and b.stud_sex='$j'";
			$res=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
			while (!$res->EOF) {
				$all_id.="'".$res->fields[stud_id]."',";
				$res->MoveNext();
			}
			$all_id=substr($all_id,0,-1);
			if (!empty($all_id)) {
				$query="select distinct stud_id from reward where reward_year_seme='$reward_year_seme' and stud_id in ($all_id) and reward_div='1'";
				$res=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
				if ($res) $g[$j]+=$res->RecordCount();
				$query="select distinct stud_id from reward where reward_year_seme='$reward_year_seme' and stud_id in ($all_id) and reward_div='2'";
				$res=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
				if ($res) $b[$j]+=$res->RecordCount();
				$query="select reward_kind,count(reward_year_seme) from reward where reward_year_seme='$reward_year_seme' and stud_id in ($all_id) group by reward_kind order by reward_kind";
				$res=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
				while (!$res->EOF) {
					$num=intval($res->fields[1]);
					$rk=intval($res->fields[reward_kind]);
					$ow=($rk>0)?0:3;
					switch (abs($rk)) {
						case "1":
							$reward_times[$cyear][$j][1+$ow]+=$num;
							break;
						case "2":
							$reward_times[$cyear][$j][1+$ow]+=2*$num;
							break;
						case "3":
							$reward_times[$cyear][$j][2+$ow]+=$num;
							break;
						case "4":
							$reward_times[$cyear][$j][2+$ow]+=2*$num;
							break;
						case "5":
							$reward_times[$cyear][$j][3+$ow]+=$num;
							break;
						case "6":
							$reward_times[$cyear][$j][3+$ow]+=2*$num;
							break;
						case "7":
							$reward_times[$cyear][$j][3+$ow]+=3*$num;
							break;
					}
					$res->MoveNext();
				}
				$query="select reward_kind,stud_id,count(reward_year_seme) from reward where reward_year_seme='$reward_year_seme' and stud_id in ($all_id) group by stud_id,reward_kind order by stud_id,reward_kind";
				$res=$CONN->Execute($query);
				while (!$res->EOF) {
					$rk=intval($res->fields[reward_kind]);
					$ow=($rk>0)?0:3;
					$stud_id=$res->fields[stud_id];
					switch (abs($rk)) {
						case "1":
							$reward_per[$cyear][$j][1+$ow]++;
							break;
						case "2":
							if ($oid!=$stud_id || ($ork!="1" && $ork!="-1")) $reward_per[$cyear][$j][1+$ow]++;
							break;
						case "3":
							$reward_per[$cyear][$j][2+$ow]++;
							break;
						case "4":
							if ($oid!=$stud_id || ($ork!="3" && $ork!="-3")) $reward_per[$cyear][$j][2+$ow]++;
							break;
						case "5":
							$reward_per[$cyear][$j][3+$ow]++;
							break;
						case "6":
							if ($oid!=$stud_id || ($ork!="5" && $ork!="-5")) $reward_per[$cyear][$j][3+$ow]++;
							break;
						case "7":
							if ($oid!=$stud_id || ($ork!="5" && $ork!="-5" && $ork!="6" && $ork!="-6")) $reward_per[$cyear][$j][3+$ow]++;
							break;
						
					}
					$oid=$stud_id;
					$ork=$rk;
					$res->MoveNext();
				}
			}
		}
	}
	for ($i=1;$i<=6;$i++) {
		for ($j=1;$j<=2;$j++) {
			reset ($class_year);
			while (list($k,$v)=each($class_year)) {
				$total_times[$i][$j]+=$reward_times[$k][$j][$i];
				$total_per[$i][$j]+=$reward_per[$k][$j][$i];
			}
		}
	}
}

if ($_POST[act]=="列印") {
	$ttt = new EasyZip;
	$ttt->setPath('chart');
	$ttt->addDir('META-INF');
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");
	$data=$ttt->read_file(dirname(__FILE__)."/chart/content.xml");
	$sql="select * from school_base";
	$rs=$CONN->Execute($sql);
	$school_name=$rs->fields['sch_cname'];
	$temp_arr["school_name"]=$school_name;
	$temp_arr["sel_year"]=$sel_year;
	$temp_arr["sel_seme"]=$sel_seme;
	$d=explode("-",$today);
	$temp_arr["ctoday"]=($d[0]-1911).".".$d[1].".".$d[2];
	$temp_arr["g1"]=$g[1]+$g[2];
	$temp_arr["g2"]=$g[1];
	$temp_arr["g3"]=$g[2];
	$temp_arr["b1"]=$b[1]+$b[2];
	$temp_arr["b2"]=$b[1];
	$temp_arr["b3"]=$b[2];
	for ($i=1;$i<=6;$i++) {
		$temp_arr["tp".$i]=$total_per[$i][1]+$total_per[$i][2];
		$temp_arr["tt".$i]=$total_times[$i][1]+$total_times[$i][2];
		for ($j=1;$j<=2;$j++) {
			$temp_arr["p".$j.$i]=$total_per[$i][$j];
			$temp_arr["t".$j.$i]=$total_times[$i][$j];
			for ($k=1;$k<=3;$k++) {
				$temp_arr["p".$k.$j.$i]=intval($reward_per[$k+$IS_JHORES][$j][$i]);
				$temp_arr["t".$k.$j.$i]=intval($reward_times[$k+$IS_JHORES][$j][$i]);
			}
		}
	}

	$replace_data = $ttt->change_temp($temp_arr,$data,0);
	$ttt->add_file($replace_data,"content.xml");
	$sss = & $ttt->file();
	$fl="reward_chart_".$sel_year."_".$sel_seme;
	header("Content-disposition: attachment; filename=$fl.sxw");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $sss;
	exit;
	
} elseif ($_POST[act]=="cal") {
	$reward_kind=stud_rep_kind();
	$reward_k=array("0"=>"獎<br>勵","1"=>"懲<br>戒");
	$main.="<br><small>統計日期：$today</small>
		<table cellspacing=0 cellpadding=0><tr><td>
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
		<tr class='title_sbody2'><td colspan='3'><td>合計<td>男<td>女<td>一男<td>一女<td>二男<td>二女<td>三男<td>三女</td>
		</tr>";
	for ($m=0;$m<=1;$m++) {
		for ($i=1;$i<=3;$i++) {
			$num=$m*3+$i;
			$main.="<tr class='title_sbody2'>";
			if ($i==1) $main.="<td rowspan='3'>".$reward_k[$m];
			$main.="<td>".$reward_kind[$m*3+4-$i]."<td>人數<br>次數<td bgcolor='#ffffff'>".($total_per[$num][1]+$total_per[$num][2])."<br>".($total_times[$num][1]+$total_times[$num][2])."<td bgcolor='#ffffff'>".$total_per[$num][1]."<br>".$total_times[$num][1]."<td bgcolor='#ffffff'>".$total_per[$num][2]."<br>".$total_times[$num][2];
			for ($j=1;$j<=3;$j++) {
				for ($k=1;$k<=2;$k++) {
					$main.="<td bgcolor='#ffffff'>".intval($reward_per[$IS_JHORES+$j][$k][$num])."<br>".intval($reward_times[$IS_JHORES+$j][$k][$num]);
				}
			}
			$main.="</td></tr>";
		}
	}
	$main.="</table></tr></table>";
	$main.="<form action='$_SERVER[PHP_SELF]' name='base_form' method='post'><input type='submit' name='act' value='列印'><input type='hidden' name='year_seme' value='$seme_year_seme'></form>";
}

if ($_POST[act]!="列印") {
	echo $main;
	foot();
}
?>
