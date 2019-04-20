<?php
// $Id: check_score_error3.php 5310 2009-01-10 07:57:56Z hami $
//載入設定檔
include "stud_check_config.php";

//認證檢查
sfs_check();

$act=$_POST['act'];
$act1=$_POST['act1'];
if ($act1=="del_ok") $del=1;


head("學籍資料檢查");
print_menu($menu_p);

echo "<h3>本項作業將檢查學期成績中的多餘資料, 請務必予以刪除, 以確保畢業成績的正確 !!</h3>";
echo "<form  name ='myform' method='post' action='{$_SERVER['PHP_SELF']}'>";
		
if ($act=="開始檢查" || $del) {
	//取得班級
	$query = "select year,semester,c_year,c_sort from school_class where enable='1' order by class_id";
	$res = $CONN->Execute($query);
	while(!$res->EOF) {
		$class_arr[$res->fields[year]][$res->fields[semester]][$res->fields[c_year]][$res->fields[c_sort]]=1;
		$res->MoveNext();
	}

	//取得班級課程
	$query = "select ss_id,year,semester,class_id from score_ss where enable='1' and class_id <> '' order by class_id,ss_id";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$class_id=$res->fields[class_id];
		$c=explode("_",$class_id);
		$ss_id_arr[$res->fields[year]][$res->fields[semester]][intval($c[2])][intval($c[3])].="'".$res->fields[ss_id]."',";
		$vals[$res->fields[year]][$res->fields[semester]][intval($c[2])][intval($c[3])]=1;
		$res->MoveNext();
	}

	//取得一般課程
	$query = "select ss_id,year,semester,class_year from score_ss where enable='1' and class_id ='' order by year,semester,class_year";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$year=$res->fields[year];
		$semester=$res->fields[semester];
		$c_year=$res->fields[class_year];
		reset($class_arr[$year][$semester][$c_year]);
		while(list($c_sort,$v)=each($class_arr[$year][$semester][$c_year])) {
			if ($vals[$year][$semester][$c_year][$c_sort]!=1) {
				$ss_id_arr[$year][$semester][$c_year][$c_sort].="'".$res->fields[ss_id]."',";
			}
		}
		$res->MoveNext();
	}

	//取得班級學生流水號
	$query = "select * from stud_seme order by seme_year_seme,seme_class";
	$res = $CONN->Execute($query);
	while(!$res->EOF) {
		$seme_year_seme=$res->fields[seme_year_seme];
		$seme_class=$res->fields[seme_class];
		$year=intval(substr($seme_year_seme,0,-1));
		$semester=substr($seme_year_seme,-1,1);
		$year_name=intval(substr($seme_class,0,-2));
		$class_name=intval(substr($seme_class,-2,2));
		if ($year!=$oy || $semester!=$os || $year_name!=$oyn || $class_name!=$ocn) {
			if ($all_sn!="") {
				if ($ss_id_arr[$oy][$os][$oyn][$ocn]!="") {
					$all_sn=substr($all_sn,0,-1);
					$ss_id_str=substr($ss_id_arr[$oy][$os][$oyn][$ocn],0,-1);
					$query_del="select count(seme_year_seme) from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)";
					$res_del=$CONN->Execute($query_del);
					if ($res_del->fields[0]>0) {
						$err=1;
						echo "<table><tr><td bgcolor=yellow>".$oy."學年度第".$os."學期".$class_year[$oyn].$ocn."班有".$res_del->fields[0]."筆多餘資料</td></tr>";
					    $query_del1="select ss_id,student_sn,ss_score,ss_score_memo from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)";
					    $res_del1=$CONN->Execute($query_del1);
				        while(!$res_del1->EOF){
						echo "<tr><td>課程代碼:".$res_del1->fields[0]."學生流水號:".$res_del1->fields[1]." ".$res_del1->fields[3]."</td></tr>";
					    $res_del1->MoveNext();
						}	
						echo "</table>";
						
						
						if ($del) $CONN->Execute("delete from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)");
					}
					
					
				
					$all_sn="";
				}
			}
			$all_sn="";
		}
		$all_sn.="'".$res->fields[student_sn]."',";
		$oy=$year;
		$os=$semester;
		$osys=$seme_year_seme;
		$oyn=$year_name;
		$ocn=$class_name;
		$res->MoveNext();
	}

	//check最後一次資料
	if ($all_sn!="") {
		if ($ss_id_arr[$oy][$os][$oyn][$ocn]!="") {
			$all_sn=substr($all_sn,0,-1);
			$ss_id_str=substr($ss_id_arr[$oy][$os][$oyn][$ocn],0,-1);
			$query_del="select count(seme_year_seme),seme_year_seme,ss_id,student_sn,ss_score from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)";
			$res_del=$CONN->Execute($query_del);
			if ($res_del->fields[0]>0) {
				$err=1;
				echo "<table><tr><td bgcolor=yellow>".$oy."學年度第".$os."學期".$class_year[$oyn].$ocn."班有".$res_del->fields[0]."筆多餘資料</td></tr>";
			    $query_del1="select ss_id,student_sn,ss_score,ss_score_memo from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)";
				$res_del1=$CONN->Execute($query_del1);
				while(!$res_del1->EOF){
				echo "<tr><td>課程代碼:".$res_del1->fields[0]."學生流水號:".$res_del1->fields[1]." ".$res_del1->fields[3]."</td></tr>";
				$res_del1->MoveNext();
				}	
				echo "</table>";		
						
				
				if ($del) $CONN->Execute("delete from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)");
			}
			$all_sn="";
		}
	}

	if ($err!="1")
		echo "學期資料表中無多餘資料！";
	else {
		if ($del)
			echo "<font color='red'>以上資料已刪除</font>";
		else
		{
	     echo "<input type='hidden' name='act1'>";
         echo "<input type='submit' value='刪除多餘資料' onclick='check_del();'>";
	    }
	}

} else {
	echo "<input type='submit' name='act' value='開始檢查'>";

}
echo "</form>";
foot();

?>
<Script Language="JavaScript">
function check_del() {
	
if (confirm('您碓定要刪除多餘資料?')) 
{ 
  document.myform.act1.value='del_ok';
  document.myform.submit();
}

}
</Script>