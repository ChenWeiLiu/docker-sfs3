<?php
// $Id: check_score_error3.php 5310 2009-01-10 07:57:56Z hami $
//���J�]�w��
include "stud_check_config.php";

//�{���ˬd
sfs_check();

$act=$_POST['act'];
$act1=$_POST['act1'];
if ($act1=="del_ok") $del=1;


head("���y����ˬd");
print_menu($menu_p);

echo "<h3>�����@�~�N�ˬd�Ǵ����Z�����h�l���, �аȥ����H�R��, �H�T�O���~���Z�����T !!</h3>";
echo "<form  name ='myform' method='post' action='{$_SERVER['PHP_SELF']}'>";
		
if ($act=="�}�l�ˬd" || $del) {
	//���o�Z��
	$query = "select year,semester,c_year,c_sort from school_class where enable='1' order by class_id";
	$res = $CONN->Execute($query);
	while(!$res->EOF) {
		$class_arr[$res->fields[year]][$res->fields[semester]][$res->fields[c_year]][$res->fields[c_sort]]=1;
		$res->MoveNext();
	}

	//���o�Z�Žҵ{
	$query = "select ss_id,year,semester,class_id from score_ss where enable='1' and class_id <> '' order by class_id,ss_id";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$class_id=$res->fields[class_id];
		$c=explode("_",$class_id);
		$ss_id_arr[$res->fields[year]][$res->fields[semester]][intval($c[2])][intval($c[3])].="'".$res->fields[ss_id]."',";
		$vals[$res->fields[year]][$res->fields[semester]][intval($c[2])][intval($c[3])]=1;
		$res->MoveNext();
	}

	//���o�@��ҵ{
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

	//���o�Z�žǥͬy����
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
						echo "<table><tr><td bgcolor=yellow>".$oy."�Ǧ~�ײ�".$os."�Ǵ�".$class_year[$oyn].$ocn."�Z��".$res_del->fields[0]."���h�l���</td></tr>";
					    $query_del1="select ss_id,student_sn,ss_score,ss_score_memo from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)";
					    $res_del1=$CONN->Execute($query_del1);
				        while(!$res_del1->EOF){
						echo "<tr><td>�ҵ{�N�X:".$res_del1->fields[0]."�ǥͬy����:".$res_del1->fields[1]." ".$res_del1->fields[3]."</td></tr>";
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

	//check�̫�@�����
	if ($all_sn!="") {
		if ($ss_id_arr[$oy][$os][$oyn][$ocn]!="") {
			$all_sn=substr($all_sn,0,-1);
			$ss_id_str=substr($ss_id_arr[$oy][$os][$oyn][$ocn],0,-1);
			$query_del="select count(seme_year_seme),seme_year_seme,ss_id,student_sn,ss_score from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)";
			$res_del=$CONN->Execute($query_del);
			if ($res_del->fields[0]>0) {
				$err=1;
				echo "<table><tr><td bgcolor=yellow>".$oy."�Ǧ~�ײ�".$os."�Ǵ�".$class_year[$oyn].$ocn."�Z��".$res_del->fields[0]."���h�l���</td></tr>";
			    $query_del1="select ss_id,student_sn,ss_score,ss_score_memo from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)";
				$res_del1=$CONN->Execute($query_del1);
				while(!$res_del1->EOF){
				echo "<tr><td>�ҵ{�N�X:".$res_del1->fields[0]."�ǥͬy����:".$res_del1->fields[1]." ".$res_del1->fields[3]."</td></tr>";
				$res_del1->MoveNext();
				}	
				echo "</table>";		
						
				
				if ($del) $CONN->Execute("delete from stud_seme_score where seme_year_seme='$osys' and student_sn in ($all_sn) and ss_id not in ($ss_id_str)");
			}
			$all_sn="";
		}
	}

	if ($err!="1")
		echo "�Ǵ���ƪ��L�h�l��ơI";
	else {
		if ($del)
			echo "<font color='red'>�H�W��Ƥw�R��</font>";
		else
		{
	     echo "<input type='hidden' name='act1'>";
         echo "<input type='submit' value='�R���h�l���' onclick='check_del();'>";
	    }
	}

} else {
	echo "<input type='submit' name='act' value='�}�l�ˬd'>";

}
echo "</form>";
foot();

?>
<Script Language="JavaScript">
function check_del() {
	
if (confirm('�z�P�w�n�R���h�l���?')) 
{ 
  document.myform.act1.value='del_ok';
  document.myform.submit();
}

}
</Script>