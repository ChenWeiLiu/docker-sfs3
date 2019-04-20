<?php

// $Id: cal.php 6998 2012-11-13 02:09:59Z infodaes $

// 取得設定檔
include "config.php";
$year_seme=$_POST['year_seme'];
$sel=$_POST['sel'];


sfs_check();

//程式檔頭
head("統計日常成績");
print_menu($menu_p);

$ret_array =get_sfs_module_set("academic_record");
$is_summary_input=$ret_array["is_summary_input"]?$ret_array["is_summary_input"]:"n";


echo "	<script language=\"JavaScript\">
	var OK=false;
	function check() {
		if (OK) {
			return confirm('確定進行統計');
		}
	}
	</script>";

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
}
 // if ($IS_JHORES==6)
 //{
	$sql="select Max(sections) as maxsections from score_setup where year='$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤：$sql", E_USER_ERROR);
	if ($rs) {
		while (!$rs->EOF) {
			$all_sections_max=$rs->fields['maxsections'];
			$rs->MoveNext();
		}
	}
 //}
	
if ($_POST['absent']){
	$ab_c=cal_abs($sel_year,$sel_seme);
	$datenow=date("Y/m/d h:i:s");
	$pm="";
    while (list($key, $value) = each($sel)) 
	{
    if ($value==1)$pm.="+".$key;
    }
	 $pm="升降旗 $pm 節";
	 
	$unit=$IS_JHORES?"節":"日";
			 
	$msg="<br>$datenow 出缺席記錄統計($pm)完成，結果如下:
	<table cellspacing=0 cellpadding=5 border=1 width=100%>
	<tr align=center><td >事假</td><td>病假</td><td>曠課</td><td>集會</td><td>公假</td><td>其他</td></tr>
    <tr align=center><td>$ab_c[1]{$unit}</td><td>$ab_c[2]{$unit}</td><td>$ab_c[3]{$unit}</td><td>$ab_c[4]{$unit}</td><td>$ab_c[5]{$unit}</td><td>$ab_c[6]{$unit}</td></tr>	
	</table>";
}

if ($_POST['reward']){
	$ab_r=cal_rew($sel_year,$sel_seme);
	$datenow=date("Y/m/d h:i:s");
	$msg1="<br>$datenow 獎懲記錄統計完成，結果如下:
	<table cellspacing=0 cellpadding=5 border=1 width=100%>
	<tr align=center><td>大功</td><td>小功</td><td>嘉獎</td><td>大過</td><td>小過</td><td>警告</td></tr>
    <tr align=center><td>$ab_r[1]</td><td>$ab_r[2]</td><td>$ab_r[3]</td><td>$ab_r[4]</td><td>$ab_r[5]</td><td>$ab_r[6]</td></tr>	
	</table>";
	
	
	
}
if ($_POST['nor']){
	cal_nor($sel_year,$sel_seme);
	$msg="<tr><td><br>綜合成績統計完成 ！</td></tr>";
}

//學期選單
$col_name="year_seme";
$id=$year_seme;    
$show_year_seme=select_year_seme($id,$col_name);
$year_seme_menu="<select name='$col_name' OnChange='this.form.submit();'>$show_year_seme</select>";

$query="select count(stud_id) from stud_absent where year='$sel_year' and semester='$sel_seme'";
$res=$CONN->Execute($query);
$abs_nums=$res->fields[0];

//if ($IS_JHORES==6) {
	$query="select count(stud_id) from reward where reward_year_seme='".$sel_year.$sel_seme."'";
	$res=$CONN->Execute($query);
	$rew_nums=$res->fields[0];
	$rew_msg="獎懲記錄共有 <font color='#ff0000'>$rew_nums</font> 筆<br>";
//}

	$ixp="";
	$section_include=explode(",",$section_include);
	for($i=1;$i<=$all_sections_max;$i++)
	{
	  $checked=in_array($i,$section_include)?'checked':'';
      $ixp.="<input type='checkbox' name='sel[$i]' value='1' $checked>第 $i 節";
	}

$print_alert=$abs_nums>0?"":"<font color='#ff0000'>（若無記錄的話，請勿統計綜合成績，以避免之前匯入資料遺失。）</font>";
//if ($IS_JHORES==6) {
	
	$ops="<table><tr><td bgcolor=yellow valign=top>
	
	$ixp<br><input type='submit' name='absent' value='統計出缺席'>
     </td>
	<td valign=top></td></tr></table>";
	//if ($ncount) $ops.=" <input type='submit' name='nor' value='統計日常成績' OnClick=\"OK=true\">";
//} else
	//$ops="<input type='submit' name='absent' value='統計出缺席'>";
echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";
$menu="<table cellspacing=0 cellpadding=0>
	<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\" OnSubmit=\"return check();\">
	<tr><td>請選擇您要執行的工作：$year_seme_menu 
	<table border=1><tr><td valign=top>
	本學期出缺席記錄共有 <font color='#ff0000'>$abs_nums</font> 筆<br>
    $print_alert<br>$ops $msg
	</td><td valign=top>$rew_msg<input type='submit' name='reward' value='統計獎懲'>$msg1</td></tr></table>		
	</form>
	</table>";
if ($is_summary_input=="y")
{	
$menu="
     <table cellspacing=0 cellpadding=0>
	<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\" OnSubmit=\"return check();\">
	<tr><td>請選擇您要執行的工作：$year_seme_menu 
	<table border=1><tr><td width=50% valign=top><font color=red>因系統管理員在【導】製作成績單〈academic_record〉模組中的模組變數允許使用者輸入整學期的缺席統計數據，故不能再進行出缺席統計。</font><br></td><td width=50% valign=top>$rew_msg<input type='submit' name='reward' value='統計獎懲'>$msg1</td></tr></table>		
	</form>
	</table>";
}
echo $menu."</tr></table>";
//if ($IS_JHORES==6) {
	$help_text="建議您依序進行「統計出缺席」→「統計獎懲」→「統計綜合成績」";
	echo help($help_text);
//}




foot();

function cal_abs($sel_year,$sel_seme){
	global $CONN,$IS_JHORES,$sel,$all_sections_max;
	include "../../include/sfs_case_dataarray.php";
	
    $sel_arr="";
   	for($i=1;$i<=$all_sections_max;$i++)
	{
		if($sel[$i]==0)$sel_arr.="'$i',";
	}	
	$sel_arr=substr($sel_arr,0,-1);
	$notsection="";
	if ($sel_arr!="")$notsection="and (section not in ($sel_arr))";
	
	
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$a_array=stud_abs_kind();
	$abs_array=array("事假"=>"1","病假"=>"2","曠課"=>"3","集會"=>"4","公假"=>"5","其他"=>"6","喪假"=>"6");
	/* 2018.02.27 把統計前預先清除記錄的功能 mark 起來, 避免轉學生交換過來的總表資料也被清除 by smallduh */
	//$query="update stud_seme_abs set abs_days='' where seme_year_seme='$seme_year_seme'";
	//$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
	$sql="select class_year,sections from score_setup where year='$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤：$sql", E_USER_ERROR);
	if ($rs) {
		while (!$rs->EOF) {
			$all_sections[$rs->fields['class_year']]=$rs->fields['sections'];
			$rs->MoveNext();
		}
	}
	if ($IS_JHORES==6)
		$sql="select * from stud_absent where year='$sel_year' and semester='$sel_seme' $notsection order by stud_id,absent_kind";
	else
		$sql="select distinct date,stud_id,absent_kind from stud_absent where year='$sel_year' and semester='$sel_seme' $notsection order by stud_id,absent_kind";
	
	
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤：$sql", E_USER_ERROR);
	if ($rs) {
		while (!$rs->EOF) {
			$stud_id=$rs->fields['stud_id'];
			$absent_kind=$rs->fields['absent_kind'];
			
			if ($IS_JHORES==6) {
				$section=$rs->fields['section'];
				$class=explode("_",$rs->fields['class_id']);
			}
			if ($oid!=$stud_id && $oid) {
				for ($i=1;$i<=6;$i++) {
					if ($ab[$i]=="") $ab[$i]="0";
					$query="select * from stud_seme_abs where seme_year_seme='$seme_year_seme' and stud_id='$oid' and abs_kind='$i'";
					$rs_c=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
					if ($rs_c->recordcount()>0) {
						$query="update stud_seme_abs set abs_days='$ab[$i]' where seme_year_seme='$seme_year_seme' and stud_id='$oid' and abs_kind='$i'";
						$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
					} else {
						$query="insert into stud_seme_abs (seme_year_seme,stud_id,abs_kind,abs_days) values ('$seme_year_seme','$oid','$i','$ab[$i]')";
						$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
					}
				}
				$ab="";
			}
			if ($IS_JHORES==6) {
				if ($section=="allday") {
					$ab[$abs_array[$absent_kind]]+=$all_sections[intval($class[2])];
					if ($absent_kind=="曠課") $ab[4]+=2; //allday包含升降旗,故集會$ab[4]+2節
				} elseif ($section=="uf" || $section=="df") {
					if ($absent_kind=="曠課") $ab[4]+=1;
				} elseif (in_array($absent_kind,$a_array)) {
					$ab[$abs_array[$absent_kind]]+=1;
				} else {
					$ab[6]+=1;
				}
			} else {
				$ab[$abs_array[$absent_kind]]++;
			}
			$oid=$stud_id;
			$rs->MoveNext();
		}
		for ($i=1;$i<=6;$i++) {
			$query="select * from stud_seme_abs where seme_year_seme='$seme_year_seme' and stud_id='$oid' and abs_kind='$i'";
			$rs_c=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
			if ($rs_c) {
				$query="update stud_seme_abs set abs_days='$ab[$i]' where seme_year_seme='$seme_year_seme' and stud_id='$oid' and abs_kind='$i'";
				$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
			} else {
				$query="insert into stud_seme_abs (seme_year_seme,stud_id,abs_kind,abs_days) values ('$seme_year_seme','$oid','$i','$ab[$i]')";
				$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
			}
		}
	}
             
			for($k=1;$k<=6;$k++)
			{		
            $countdays[$k]=0;		
		    $query="select sum(abs_days) as countdays from stud_seme_abs where seme_year_seme='$seme_year_seme' and abs_kind='$k'";
			 $rs_c=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
			 if ($rs_c) {
				$countdays[$k]=$rs_c->fields['countdays'];
			 }
	        }
			

			
	return $countdays;
}

function cal_rew($sel_year,$sel_seme) {
	global $CONN;
	
	$reward_year_seme=$sel_year.$sel_seme;
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$CONN->Execute("update stud_seme_rew set sr_num='' where seme_year_seme='$seme_year_seme'");
	$sql="select * from reward where reward_year_seme='$reward_year_seme' and stud_id <> '' and reward_cancel_date='0000-00-00' order by stud_id,reward_kind";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤：$sql", E_USER_ERROR);
	if ($rs) {
		if ($rs->recordcount()>0) {
			while (!$rs->EOF) {
				$stud_id=$rs->fields['stud_id'];
				$reward_kind=intval($rs->fields['reward_kind']);
				$ow=($reward_kind>0)?0:3;
				$ork=abs($reward_kind);
				if ($oid!=$stud_id && $oid!="") {
					for ($i=1;$i<=6;$i++) {
						$val=$stud_rew[$oid][$i];
						$query="select * from stud_seme_rew where seme_year_seme='$seme_year_seme' and stud_id='$oid' and sr_kind_id='$i'";
						$rs_c=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
						if ($rs_c->recordcount() > 0) {
							$query="update stud_seme_rew set sr_num='$val' where seme_year_seme='$seme_year_seme' and stud_id='$oid' and sr_kind_id='$i'";
							$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
						} else {
							$query="insert into stud_seme_rew (seme_year_seme,stud_id,sr_kind_id,sr_num) values ('$seme_year_seme','$oid','$i','$val')";
							$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
						}
					}
				}
				if($rs->fields[reward_sub] ==1){
					switch ($ork) {
						case 1:
							$stud_rew[$stud_id][3+$ow]++;
							break;
						case 2:
							$stud_rew[$stud_id][3+$ow]+=2;
							break;
						case 3:
							$stud_rew[$stud_id][2+$ow]++;
							break;
						case 4:
							$stud_rew[$stud_id][2+$ow]+=2;
							break;
						case 5:
							$stud_rew[$stud_id][1+$ow]++;
							break;
						case 6:
							$stud_rew[$stud_id][1+$ow]+=2;
							break;
						case 7:
							$stud_rew[$stud_id][1+$ow]+=3;
							break;
					}
				}
				$oid=$stud_id;
				$rs->MoveNext();
			}
			//取得student_sn
			$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$oid'";
			$rs_c=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
			$student_sn=$rs_c->fields['student_sn'];

			for ($i=1;$i<=6;$i++) {
				$val=$stud_rew[$oid][$i];
				$query="select * from stud_seme_rew where seme_year_seme='$seme_year_seme' and stud_id='$oid' and sr_kind_id='$i'";
				$rs_c=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
				if ($rs_c->recordcount() > 0) {
					$query="update stud_seme_rew set sr_num='$val' where seme_year_seme='$seme_year_seme' and stud_id='$oid' and sr_kind_id='$i'";
					$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
				} else {
					$query="insert into stud_seme_rew (seme_year_seme,stud_id,student_sn,sr_kind_id,sr_num) values ('$seme_year_seme','$oid','$student_sn','$i','$val')";
					$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);;
				}
			}
		}
	}

	//修正student_sn=0的資料
	$query="select distinct stud_id from stud_seme_rew where student_sn=0 and seme_year_seme='$seme_year_seme'";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$stud_id=$res->fields['stud_id'];
		if (intval($stud_id)>0) $temp_arr[]=$stud_id;
		$res->MoveNext();
	}
	if (count($temp_arr)>0) {
		$temp_str="'".implode("','",$temp_arr)."'";
		$query="select student_sn,stud_id from stud_base where stud_id in ($temp_str) and ($sel_year - stud_study_year between 0 and 9)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$query="update stud_seme_rew set student_sn='".$res->fields['student_sn']."' where stud_id='".$res->fields['stud_id']."' and student_sn=0 and seme_year_seme='$seme_year_seme'";
			$CONN->Execute($query);
			$res->MoveNext();
		}
	}
	
	
			for($k=1;$k<=6;$k++)
			{		
            $countrews[$k]=0;		
		    $query="select sum(sr_num) as countrews from stud_seme_rew where seme_year_seme='$seme_year_seme' and sr_kind_id='$k'";
			 $rs_c=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
			 if ($rs_c) {
				$countrews[$k]=$rs_c->fields['countrews'];
			 }
	        }
			
			return $countrews;
	
}

function cal_nor($sel_year,$sel_seme) {
	global $CONN,$f_w,$cl_days,$sl_days,$u_score,$d_score,$a_score;
    
	if (intval($cl_days)==0) $cl_days=30;
	if (intval($sl_days)==0) $sl_days=80;
	if ($u_score=="") $u_score=1;
	if ($d_score=="") $d_score=1;
	$abs_kind="'事假','病假','曠課','集會'";
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$query="select * from school_day where year='$sel_year' and seme='$sel_seme'";
	$rs_abs=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
	if ($rs_abs) {
		while (!$rs_abs->EOF) {
			$school_day[$rs_abs->fields['day_kind']]=$rs_abs->fields['day'];
			$rs_abs->MoveNext();
		}
	}
	$st=explode("-",$school_day[start]);
	$se=explode("-",$school_day[end]);
	if (intval($se[1])<intval($st[1])) $se[1]+=12;
	$query="select * from stud_seme_abs where seme_year_seme='$seme_year_seme' order by stud_id,abs_kind";
	$rs_abs=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
	if ($rs_abs->recordcount()>0)
		while (!$rs_abs->EOF) {
			$stud_abs[$rs_abs->fields['stud_id']][$rs_abs->fields['abs_kind']]=$rs_abs->fields['abs_days'];
			$rs_abs->MoveNext();
		}
	$query="select * from seme_score_nor where seme_year_seme='$seme_year_seme'";
	$rs_nor=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
	while (!$rs_nor->EOF) {
		$id=$rs_nor->fields['stud_id'];
		$stud_score[$id][0]=$rs_nor->fields['score1'];
		$stud_score[$id][1]=$rs_nor->fields['score2'];
		$stud_score[$id][2]=$rs_nor->fields['score3'];
		$stud_score[$id][3]=$rs_nor->fields['score4'];
		$stud_score[$id][4]=$rs_nor->fields['score5'];
		$stud_score[$id][5]=$rs_nor->fields['score6'];
		$stud_score[$id][6]=$rs_nor->fields['score7'];
		$rs_nor->MoveNext();
	}
	$query="select * from stud_seme_rew where seme_year_seme='$seme_year_seme' order by stud_id,sr_kind_id";
	$rs_rew=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
	if ($rs_rew) {
		while (!$rs_rew->EOF) {
			$id=$rs_rew->fields['stud_id'];
			$stud_rew[$id][$rs_rew->fields['sr_kind_id']]=$rs_rew->fields['sr_num'];
			$rs_rew->MoveNext();
		}
	}
	$query="select b.student_sn,b.stud_name,b.stud_id from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$seme_year_seme' order by a.stud_id";
	$rs=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);;
	while (!$rs->EOF) {
		//取得座號及姓名
		$sn=$rs->fields['student_sn'];
		$id=$rs->fields['stud_id'];
		$stud_name=$rs->fields['stud_name'];

		//取得導師加減分
		$stud_score[$id][7]=round($stud_score[$id][0]+($stud_score[$id][1]+$stud_score[$id][2]+$stud_score[$id][3]+$stud_score[$id][4])/4+$stud_score[$id][5]+$stud_score[$id][6]);
		if ($stud_score[$id][7]=="") $stud_score[$id][7]="0";

		//計算全勤
		$abs_month=array();
		$query="select distinct month from stud_absent where year='$sel_year' and semester='$sel_seme' and stud_id='$id' and absent_kind in ($abs_kind) order by month";
		$rs_abs=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);
		if ($rs_abs)
			if ($rs_abs->recordcount()>0) {
				while (!$rs_abs->EOF) {
					$abs_month[$rs_abs->fields['month']]=1;
					$rs_abs->MoveNext();
				}
			}
		$abs_all=0;
		$k=0;
		for ($i=intval($st[1]);$i<=$se[1];$i++) {
			$j=$i;
			$k++;
			if ($i>12) $j=$i-12;
			if ($abs_month[$j]!=1) $abs_all++;
		}
		if ($a_score!="1") $abs_all=($abs_all==$k)?5:0;

		//取得出缺席值
		for ($i=1;$i<=6;$i++) if ($stud_abs[$id][$i]=="") $stud_abs[$id][$i]=0;
		$stud_abs[$id][0]=$abs_all-floor($stud_abs[$id][3]/2+$stud_abs[$id][2]/$sl_days+$stud_abs[$id][1]/$cl_days+$stud_abs[$id][4]/4);

		//取得獎懲值
		for ($i=1;$i<=6;$i++) {
			if ($stud_rew[$id][$i]=="") $stud_rew[$id][$i]=0;
		}
		$stud_rew[$id][0]=$stud_rew[$id][1]*9+$stud_rew[$id][2]*3+$stud_rew[$id][3]-$stud_rew[$id][4]*7;
		if ($f_w=="") $f_w=0;
		if ($stud_rew[$id][6] > 0) $stud_rew[$id][0]-=$stud_rew[$id][6]-1+$f_w;
		if ($stud_rew[$id][5] > 2)
			$stud_rew[$id][0]-=($stud_rew[$id][5]-2)*3+4;
		else
			$stud_rew[$id][0]-=$stud_rew[$id][5]*2;
		
		$nor_total=80+$stud_abs[$id][0]+$stud_score[$id][7]+$stud_rew[$id][0];
		if ($u_score && $nor_total>100) $nor_total=100;
		if ($d_score && $nor_total<0) $nor_total=0;
		$query="select * from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$sn' and ss_id='0'";
		$rs_nor=$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);;
		if ($rs_nor->recordcount()>0) {
			$query="update stud_seme_score_nor set ss_score='$nor_total' where seme_year_seme='$seme_year_seme' and student_sn='$sn' and ss_id='0'";
			$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);;
		} else {
			$query="insert into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo) values ('$seme_year_seme','$sn','0','$nor_total','')";
			$CONN->Execute($query) or trigger_error("SQL語法錯誤：$query", E_USER_ERROR);;
		}
		$rs->MoveNext();
	}
	
}

?>
