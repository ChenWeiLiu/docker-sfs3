<?php

// $Id: stud_year_1.php 8547 2015-10-01 02:10:53Z infodaes $

// 載入設定檔
include "stud_year_config.php";
include "../../include/sfs_case_dataarray.php";

// 認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//取得模組設
$m_arr = &get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

//查詢當學期編班情形
$curr_year = curr_year();
$curr_seme = curr_seme();
$seme_year_seme = sprintf("%03d%d",$curr_year,$curr_seme);
$not_up="'1','5','6','7','8','11','12'";

if ($_REQUEST[sel]=='change'){
	$edit=$_REQUEST[edit];
	if ($edit=='1') {
		$student_sn=$_GET[student_sn];
		$sql="select stud_id,curr_class_num ,stud_study_cond from stud_base where student_sn='$student_sn'";
		$rs=$CONN->Execute($sql);
		$stud_id=$rs->fields['stud_id'];
		$stud_study_cond = $rs->fields[stud_study_cond];
		$curr_class_num=$rs->fields['curr_class_num'];
		$year_name=(strlen($curr_class_num)==5)?substr($curr_class_num,0,1):substr($curr_class_num,0,2);
		$class=substr($curr_class_num,-4,2);
		$seme_class=$year_name.$class;
		$seme_num=intval(substr($curr_class_num,-2,2));
		$class_id=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),$year_name,$class);
		$sql="select c_name from school_class where class_id='$class_id'";
		$rs=$CONN->Execute($sql);
		$c_name=$rs->fields['c_name'];
		$sql="replace into stud_seme (seme_year_seme,stud_id,seme_class,seme_class_name,seme_num,seme_class_year_s,seme_class_s,seme_num_s,student_sn) values ('$seme_year_seme','$stud_id','$seme_class','$c_name','$seme_num','NULL','NULL','NULL','$student_sn')";
		$rs=$CONN->Execute($sql);
	} elseif ($edit=='2') {
		$sql="update stud_base set curr_class_num=(curr_class_num-10000) where student_sn='$_GET[student_sn]'";
		$rs=$CONN->Execute($sql);
	} elseif ($edit=='3') {
		$student_sn=$_GET['student_sn'];
		$sql="select seme_class,seme_num from stud_seme where student_sn='$student_sn' and seme_year_seme='$seme_year_seme'";
		$rs=$CONN->Execute($sql);
		$seme_class=$rs->fields['seme_class'];
		$seme_num=$rs->fields['seme_num'];
		$curr_class_num=$seme_class.sprintf("%02d",$seme_num);
		$stud_study_year=intval(substr($seme_year_seme,0,-1))-substr($seme_class,0,-2)+1;
		$sql="update stud_base set curr_class_num='$curr_class_num',stud_study_year='$stud_study_year' where student_sn='$student_sn'";
		$CONN->Execute($sql);
	} elseif ($edit=='4'){
		$student_sn=$_POST['student_sn'];
		if ($student_sn=="0")
			$CONN->Execute("update stud_base set stud_study_year='$_POST[stud_study_year]' where stud_study_year='$_POST[wrong_year]' and curr_class_num like '".$_POST[curr_class_num]."%'");
		else
			$CONN->Execute("update stud_base set stud_study_year='$_POST[stud_study_year]' where student_sn='$student_sn'");
		// 判斷有無異動記錄
		$sql="select stud_id,curr_class_num ,stud_study_cond from stud_base where student_sn='$student_sn'";
		$rs=$CONN->Execute($sql);
		$stud_id=$rs->fields['stud_id'];
		$stud_study_cond = $rs->fields[stud_study_cond];
	
		$query = "select student_sn from stud_move where move_kind='$stud_study_cond' and student_sn='$student_sn'";
		$res = $CONN->Execute($query) ;
		if ($res->EOF){
			$query = "insert into stud_move (move_kind,stud_id,student_sn)values('$stud_study_cond','$stud_id','$student_sn')";
			$CONN->Execute($query);
		}
		
	}
}
if ($_GET[sel]=='del'){
	$CONN->Execute("delete from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$_GET[s_year]%'");
}
if ($_GET[sel]=='refine'){
	$seme_year_seme = sprintf("%03d%d",$curr_year,$curr_seme);
	//國小的入學年判斷
	if($IS_JHORES==0){
		// 國小錯誤的座號調整(未正常升級) curr_class_num
		$query = "select stud_id,curr_class_num from stud_base where curr_class_num like '7%'";
		$res = $CONN->Execute($query);
		while(!$res->EOF){
			$tttt = $res->fields[stud_id];
			$tttt2 = '6'.substr($res->fields[curr_class_num],1);
			$CONN->Execute("update stud_base set curr_class_num='$tttt2' where stud_id='$tttt'");
			$res->MoveNext();
		}
	}
	$query = "select seme_class,seme_num,student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$_GET[s_year]%' ";
	//echo $query."<br>";
	$res=$CONN->Execute($query);
	while(!$res->EOF){
		$curr_class_num = sprintf("%d%02d",$res->fields[seme_class],$res->fields[seme_num]);
		$student_sn =  $res->fields[student_sn];
		$query = "update stud_base set curr_class_num='$curr_class_num' where student_sn='$student_sn'";
		//echo $query."<BR>";
		$CONN->Execute($query)or die($query);
		$res->MoveNext();

	}

/*
	//刪除多餘記錄
	$query = "select student_sn from stud_base where curr_class_num like '$_GET[s_year]%' and stud_study_cond<>5 ";
	$res = $CONN->Execute($query);
	$temp_sn ='';
	while(!$res->EOF){
		$temp_sn .= $res->fields[0].",";
		$res->MoveNext();
	}
	if ($temp_sn<>'')
		$temp_sn = substr($temp_sn,0,-1);
	$query = "delete from stud_seme where student_sn not in ($temp_sn) and seme_year_seme='$seme_year_seme' and  seme_class like '$_GET[s_year]%'";
	$CONN->Execute($query);

	$temp_sn="";
	$move_year_seme=$curr_year.$curr_seme;
	$query="select b.student_sn from stud_move a left join stud_seme b on a.move_year_seme=trim(leading '0' from b.seme_year_seme) and a.stud_id=b.stud_id where a.move_year_seme<'$move_year_seme' and a.move_kind in ($not_up)";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		if ($res->fields[student_sn]) $temp_sn.="'".$res->fields[student_sn]."',";
		$res->MoveNext();
	}
	if ($temp_sn!="") {
		$temp_sn=substr($temp_sn,0,-1);
		$query="delete from stud_seme where seme_year_seme='$seme_year_seme' and student_sn in ($temp_sn)";
		$CONN->Execute($query);
	}
*/
}
elseif($_GET[key]== "priorseme"){ //參照上學期編班
	$class_num = ($s_year-1) * 2 +$curr_seme  ;//目前班級座號欄位	
	$cuur_class = "class_num_".$class_num ; 
//		$old_class = "class_num_".($class_num-1) ; 
	if ($curr_seme == 1){ //上學期
		$query = "update stud_base set  curr_class_num = (curr_class_num + 10000)";
		$query .= " where stud_study_year = '".($curr_year-$s_year+1+$IS_JHORES)."'";
		$CONN->Execute($query)or die ($query);
		$seme_year_seme = sprintf("%03d2",$curr_year-1);
		$seme_year = $s_year-1;
		$query = "select a.* from stud_seme a,stud_base b  where a.student_sn=b.student_sn and a.seme_year_seme ='$seme_year_seme' and a.seme_class like '$seme_year%' and b.stud_study_cond not in ($not_up)";  // 原為 b.stud_study_cond=0
		
		$res = $CONN->Execute($query) or die($query);
		while (!$res->EOF) {
			$stud_id = $res->fields[stud_id];
			$seme_year_seme = sprintf("%03d1",$curr_year);
			$seme_class = $s_year.substr($res->fields[seme_class],-2);
			$seme_class_name = $res->fields[seme_class_name];
			$seme_num = $res->fields[seme_num];
			$seme_class_year_s = $res->fields[seme_class_year_s];
			$seme_class_s = $res->fields[seme_class_s];
			$seme_num_s = $res->fields[seme_num_s];
			$student_sn = $res->fields[student_sn];
			$sql_insert = "replace into stud_seme (stud_id,seme_year_seme,seme_class,seme_class_name,seme_num, seme_class_year_s,seme_class_s, seme_num_s,student_sn) values ('$stud_id','$seme_year_seme','$seme_class','$seme_class_name','$seme_num','$seme_class_year_s','$seme_class_s','$seme_num_s','$student_sn')";
			//echo $sql_insert."<br>";
			$CONN->Execute($sql_insert) or die ($sql_insert);
			$res->MoveNext();
		}
	}
	else { //下學期
		$seme_year_seme = sprintf("%03d1",$curr_year);
		$seme_year = $s_year;
		$query = "select a.*,b.stud_study_cond from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme ='$seme_year_seme' and a.seme_class like '$seme_year%' and b.stud_study_cond not in ($not_up)";
		$res = $CONN->Execute($query) or die($query);
		while (!$res->EOF) {
			$stud_id = $res->fields[stud_id];
			$seme_year_seme = sprintf("%03d2",$curr_year);
			$student_sn = $res->fields[student_sn];
			$seme_class = $s_year.substr($res->fields[seme_class],-2);
			$seme_class_name = $res->fields[seme_class_name];
			$seme_num = $res->fields[seme_num];
			$seme_class_year_s = $res->fields[seme_class_year_s];
			$seme_class_s = $res->fields[seme_class_s];
			$seme_num_s = $res->fields[seme_num_s];
			$sql_insert = "replace into stud_seme (stud_id,seme_year_seme,seme_class,seme_class_name,seme_num, seme_class_year_s,seme_class_s, seme_num_s,student_sn) values ('$stud_id','$seme_year_seme','$seme_class','$seme_class_name','$seme_num','$seme_class_year_s','$seme_class_s','$seme_num_s','$student_sn')";
			//echo $sql_insert."<br>";
			$CONN->Execute($sql_insert) or die ($sql_insert);
			$res->MoveNext();
		}
	
	}

}

//印出檔頭
head("學期編班");
print_menu($menu_p);


?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr><td valign=top bgcolor="#CCCCCC" align=center >
   <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="660" class=main_body >	
   <tr>
	
	<td class=title_mbody colspan=4 align=center >
	<?php 
		echo sprintf("%d學年第%d學期 編班情形",$curr_year,$curr_seme);
	?>
	</td>	

</tr>
<tr class=title_mbody><td align=right>年級</td><td align=center>編班狀況</td><td>編班動作</td></tr>
<?php
// 判斷 mysql 版本
if(substr($DATA_VAR['version'],0,3)>=5)
	$mysql_is_version5 = true;
$not_count="";
$in_study="'0','15'";
//判斷 stud_move 是否有資料
$query = "select count(*) from stud_move ";
$res = $CONN->Execute($query);
if ($res->fields[0]==0){
	$query = "select student_sn from stud_base where stud_study_cond not in ($in_study)";

}else{
	$query="select a.student_sn from stud_move a left join stud_base b on a.student_sn=b.student_sn where a.move_kind in ($not_up) and b.stud_study_cond not in ($in_study)";
}
$mysql5_temp_query = $query;

$res=$CONN->Execute($query);
while (!$res->EOF) {
	$not_count.="'".$res->fields[student_sn]."',";
	$res->MoveNext();
}
if (!empty($not_count)) $not_count=substr($not_count,0,-1);
$i=0;
while (list($tid,$tname)= each($class_year)) {
	$seme_year_seme = sprintf("%03d%d",$curr_year,$curr_seme);
	if ($not_count<>'')
		$query = "select student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%' and student_sn not in ($not_count) order by student_sn";
	else
		$query = "select student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%'  order by student_sn";
		
	$result = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	$chk_seme="";
	while (!$result->EOF) {
		$chk_seme.="'".$result->fields[student_sn]."',";
		$result->MoveNext();
	}
	if ($chk_seme) $chk_seme=substr($chk_seme,0,-1);
	if ($not_count<>'') {
		if ($mysql_is_version5) // sub query
			$query = "select count(*) as cc from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%' and student_sn not in ($mysql5_temp_query)";
		else
			$query = "select count(*) as cc from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%' and student_sn not in ($not_count)";
	}
	else {
		$query = "select count(*) as cc from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%' ";
	}	
	$result = $CONN->Execute($query) or die($query);
 //if ($tid=='6')	echo$query;
	$seme_tol = $result->fields[0];
	if ($seme_tol==0) {
		echo "<tr><td class=title_sbody1>$tname 級</td><td align=center>尚未編班</td>";
		if ($j == 1)  //新生 
			echo "<td>新生資料輸入(利用批次建立資料匯入)</td>";
		else
			echo "<td><a href=\"{$_SERVER['PHP_SELF']}?key=priorseme&s_year=$tid\">參照上學期資料升級</a></td>";
		echo "</tr>";
	}
	else {
		echo "<tr><td class=title_sbody1>$tname 級</td><td align=center>已編班人數 ".$result->fields[0]."</td>";
		if ($not_count<>''){
			$query = "select student_sn from stud_base where $curr_year-stud_study_year+1+$IS_JHORES='$tid' and curr_class_num like '$tid%' and student_sn not in ($not_count) order by student_sn";
		}
		else
			$query = "select student_sn from stud_base where $curr_year-stud_study_year+1+$IS_JHORES='$tid' and curr_class_num like '$tid%'  order by student_sn";
		$res1 = $CONN->Execute($query) or die($query);
		$chk_base="";
		while (!$res1->EOF) {
			$chk_base.="'".$res1->fields[student_sn]."',";
			$res1->MoveNext();
		}
		if ($chk_base) $chk_base=substr($chk_base,0,-1);
		if ($not_count<>''){
			if ($mysql_is_version5)
				$query = "select count(*) from stud_base where $curr_year-stud_study_year+1+$IS_JHORES='$tid' and curr_class_num like '$tid%' and student_sn not in ($mysql5_temp_query) and stud_study_cond in ($in_study)";
			else
				$query = "select count(*) from stud_base where $curr_year-stud_study_year+1+$IS_JHORES='$tid' and curr_class_num like '$tid%' and student_sn not in ($not_count) and stud_study_cond in ($in_study)";
		}
		else
			$query = "select count(*) from stud_base where $curr_year-stud_study_year+1+$IS_JHORES='$tid' and curr_class_num like '$tid%' and stud_study_cond in ($in_study)";
		$res1 = $CONN->Execute($query) or die($query);
		$check_str ='';
		if ($seme_tol <> $res1->fields[0]) {
			$check_str =" , <B><a href=\"$_SERVER[PHP_SELF]?sel=refine&s_year=$tid\">班級座號不符 , 重新調整</a></b>($seme_tol---".$res1->fields[0].")";
			if ($seme_tol < $res1->fields[0]) $dif[$tid]='1';
			if ($seme_tol > $res1->fields[0]) $dif[$tid]='-1';
			$df='1';
		}
		echo "<td><a href=\"stud_year_2.php?s_year=$tid\">重新編班後班級學生名單調整</a> $check_str";
		if ($del_enable) echo ", <a href=\"{$_SERVER['PHP_SELF']}?sel=del&s_year=$tid\" OnClick=\"return confirm('確定要刪除本學期".$tname."級已編班完成的資料?')\">刪除已編資料</a>";
		echo "</td></tr>";

	}
	$i++;
}
?>

</table>
</td>
</tr>
<?php

	if ($df=='1') {
		echo "	<tr>
			<td class='title_sbody1'><p align='center'><b>班級座號不符或入學年有誤名單</b></p></td>
			</tr>
			<tr>
			<td>
			<table cellspacing=0 cellpadding=0 width='100%'><tr><td>
			<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4 width='100%'>
			<tr class='title_sbody1'>
			<td align='center'>學號<td align='center'>學生姓名<td align='center'>班級座號<br><font color='#ff0000'>(學期資料表&lt;-&gt;基本資料表)</font><td align='center'>入學年<td align='center'>就學狀態<td align='center'>建議處理方式</td>
			</tr>";
  		reset($class_year);
  		$cond_kind=study_cond();
  		while (list($tid,$tname) = each($class_year)) {
  			if ($dif[$tid]!='-1') {
				if ($not_count<>'')
				 	$sql = "select student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%' and student_sn not in ($not_count)";
				else
				 	$sql = "select student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%' ";
				$rs = $CONN->Execute($sql);
				$all_sn="";
				while (!$rs->EOF) {
					$all_sn.=$rs->fields['student_sn'].",";
					$rs->MoveNext();
				}
				$all_sn = substr($all_sn,0,-1);
				$sql = "select * from stud_base where curr_class_num like '$tid%' and '$curr_year'-stud_study_year+1+$IS_JHORES='$tid' and student_sn not in ($all_sn) and stud_study_cond in ($in_study)";
				$rs = $CONN->Execute($sql);
				if ($rs->fields['student_sn']!="") {
					echo "	<tr class='title_sbody2'><td colspan='6' align='center'>".$tname."級</tr>";
					while(!$rs->EOF) {
						$student_sn=$rs->fields['student_sn'];
						$sql_s="select seme_class,seme_num from stud_seme where seme_year_seme='$seme_year_seme' and student_sn='$student_sn'";
						$rs_s=$CONN->Execute($sql_s);
						echo "	<tr class='title_sbody1'>
							<td align='center'>".$rs->fields['stud_id']."
							<td align='center'>".$rs->fields['stud_name']."
							<td align='center'>".$rs->fields['curr_class_num']."&lt;-&gt;".$rs_s->fields['seme_class'].sprintf("%02d",$rs_s->fields['seme_num'])."
							<td align='center'>".$rs->fields['stud_study_year']."
							<td align='center'>".$cond_kind[$rs->fields['stud_study_cond']]."
							<td align='center'><a href=\"$_SERVER[PHP_SELF]?sel=change&student_sn=$student_sn&edit=1\">將班級座號調整成 ".$rs->fields['curr_class_num']."</a>或<a href=\"$_SERVER[PHP_SELF]?sel=change&student_sn=$student_sn&edit=2\">將班級座號調整成 ".($rs->fields['curr_class_num']-10000)."</a></td>
							</tr>";
						$rs->MoveNext();
					}
				}
			}
			if ($dif[$tid]!='1') {
				if ($not_count<>'')
					$sql = "select student_sn from stud_base where $curr_year-stud_study_year+1+$IS_JHORES='$tid' and student_sn not in ($not_count) and stud_study_cond in ($in_study)";
				else
					$sql = "select student_sn from stud_base where $curr_year-stud_study_year+1+$IS_JHORES='$tid'  and stud_study_cond in ($in_study)";
				$rs = $CONN->Execute($sql);
				$all_sn="";
				while (!$rs->EOF) {
					$all_sn.="'".$rs->fields['student_sn']."',";
					$rs->MoveNext();
				}
				$all_sn = substr($all_sn,0,-1);
				$sql_chk="select stud_id from stud_seme where student_sn='0' or student_sn=''";
				$rs_chk=$CONN->Execute($sql_chk);
				if ($rs_chk->RecordCount() > 0) {
					$sql_in="select student_sn,stud_id from stud_base";
					$rs_in=$CONN->Execute($sql_in);
					while ($rs_in->EOF) {
						$stud_id=$rs_in->fields['stud_id'];
						$student_sn=$rs_in->fields['student_sn'];
						$sql_up="update stud_seme set student_sn='$student_sn' where stud_id='$stud_id'";
						$rs_up=$CONN->Execute($sql_up);
						$rs_in->MoveNext();
					}
				}
				if ($not_count<>'')
					$sql = "select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%' and student_sn not in ($all_sn) and student_sn not in ($not_count)";
				else
					$sql = "select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$tid%' and student_sn not in ($all_sn)";
				$rs = $CONN->Execute($sql);
				if ($rs->fields['student_sn']!="") {
					echo "<tr class='title_sbody2'><td colspan='6' align='center'>".$tname."級</tr>";
					while(!$rs->EOF) {
						$student_sn=$rs->fields['student_sn'];
						$sql_s="select stud_name,stud_study_cond,stud_study_year,curr_class_num from stud_base where student_sn='$student_sn'";
						$rs_s=$CONN->Execute($sql_s);
						$curr_site=$rs_s->fields['curr_class_num'];
						$seme_site=$rs->fields['seme_class'].sprintf("%02d",$rs->fields['seme_num']);
						if ($curr_site!=$seme_site) {
							$chg_url="<a href=\"$_SERVER[PHP_SELF]?sel=change&student_sn=$student_sn&edit=3\">將班級座號調整成 $seme_site</a>";
						} else {
							$chg_url="更改入學年為民國<input type='text' size='3' name='stud_study_year' onchange='this.form.submit()'>年<input type='hidden' name='sel' value='change'><input type='hidden' name='student_sn' value='$student_sn'><input type='hidden' name='edit' value='4'>";
						}
						echo "	<tr class='title_sbody1'><form action='$_SERVER[PHP_SELF]' method='post'>
							<td align='center'>".$rs->fields['stud_id']."
							<td align='center'>".$rs_s->fields['stud_name']."
							<td align='center'>".$curr_site."&lt;-&gt;".$seme_site."
							<td align='center'>".$rs_s->fields['stud_study_year']."
							<td align='center'>".$cond_kind[$rs_s->fields['stud_study_cond']]."
							<td align='center'>$chg_url</td>
							</form></tr>";
						$rs->MoveNext();
					}
				}
				$sql = "select count(student_sn) as c,stud_study_year from stud_base where curr_class_num like '$tid%' and (stud_study_year in ($in_study) and stud_study_year+$tid-$IS_JHORES-1<>'".curr_year()."') group by stud_study_year order by stud_study_year";
				$rs = $CONN->Execute($sql);
				if ($rs)
					while (!$rs->EOF) {
						if ($rs->fields[c]>0) {
							if ($chg_url=="") echo "<tr class='title_sbody2'><td colspan='6' align='center'>".$tname."級</tr>";
							echo "	<tr class='title_sbody1'><form action='$_SERVER[PHP_SELF]' method='post'>
								<td align='center' colspan='3'>不正確的入學年共".$rs->fields[c]."筆
								<td align='center'>".$rs->fields['stud_study_year']."
								<td align='center'>---
								<td align='center'>更改入學年為民國<input type='text' size='3' name='stud_study_year' onchange='this.form.submit()'>年<input type='hidden' name='wrong_year' value='".$rs->fields['stud_study_year']."'><input type='hidden' name='sel' value='change'><input type='hidden' name='student_sn' value='0'><input type='hidden' name='curr_class_num' value='$tid'><input type='hidden' name='edit' value='4'></td>
								</form></tr>";
							$chg_url="have_data";
						}
						$rs->MoveNext();
					}
			}
		}
		echo "</table></td></tr></table></td></tr>";
  	}
?>

</table>

<?php
foot();
?>
