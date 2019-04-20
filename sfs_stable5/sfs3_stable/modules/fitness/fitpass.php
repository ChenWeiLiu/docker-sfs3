<?php
// $Id: fitpass.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
sfs_check();
$session_tea_sn =  $_SESSION['session_tea_sn'] ;


// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if ( ($_SESSION['session_who']=="教師") or  ($_SESSION['session_log_id']==$stud_id)){
}else{
	echo "限教師或本人查閱護照";
	exit ;
}
//取得學生資料陣列
$sql = "select a.student_sn,a.stud_id,a.stud_birthday,a.stud_name,a.stud_sex,b.seme_class,b.seme_num,c.*  from stud_base a,stud_seme b ,fitness_data c where a.student_sn=b.student_sn  and c.student_sn=a.student_sn and c.c_curr_seme = b.seme_year_seme  and  c.student_sn='$student_sn' order by c.c_curr_seme ";
	$result=$CONN->Execute($sql) or trigger_error("SQL語法錯誤 ", E_USER_ERROR);
$i=0;
while ($row = $result->FetchRow()) {
	if($i==0){
		$name=$row["stud_name"]; 
		$sex=$row["stud_sex"];
		if($sex==1) $sex="男";
		elseif($sex==2) $sex="女";
		$bir=$row["stud_birthday"];
		$birm=(substr($bir,0,4)-1911)."-".substr($bir,5,2)."-".substr($bir,8,2);
		echo   "<font size=4>".$SCHOOL_BASE[sch_cname]." 學生體適能護照　　　　　　　　<a href='javascript:close();'>離開</a>　<a href='javascript:print();'>列印</a><br>";
		echo   "姓名：$name 　性別：$sex 　生日：$birm 　學號：$stud_id</font>";
		echo "<table border='1' cellpadding='1' cellspacing='0' style='border-collapse: collapse' bordercolor='#000000'>
		<tr align=center><td width='5%'>測驗年月</td>
		<td width='3%'>年齡</td>	
		<td width='11%'>年班</td>	
		<td width='3%'> 座號</td>	
		<td width='8%'>$test[0] [％]</td>
		<td width='8%'>$test[1] [％]</td>
		<td width='11%'>BMI指數<br>kg/m2[％]</td>
		<td width='10%'>$test[2] [％]</td>
		<td width='10%'>$test[3] [％]</td>
		<td width='10%'>$test[4] [％]</td>
		<td width='10%'>$test[5] [％]</td>
		<td width='11%'>附註</td></tr>";
	}
	$i++;

	$num=$row["seme_num"];
	$class_num=$row["seme_class"];
	$tall=$row["tall"]; 
	$weigh=$row["weigh"]; 
	$test1=$row["test1"]; 
	$test2=$row["test2"]; 
	$test3=$row["test3"]; 
	$test4=$row["test4"]; 
	$prec_t=$row["prec_t"]; 
	$prec_w=$row["prec_w"]; 
	$prec1=$row["prec1"]; 
	$prec2=$row["prec2"]; 
	$prec3=$row["prec3"]; 
	$prec4=$row["prec4"]; 

	$age=$row["age"];
	$test_y=$row["test_y"];
	$test_m=$row["test_m"];
	$bmt=$row["bmt"];
	$prec_b=$row["prec_b"];
	$textb=text(6,$prec_b,$bmt);
	$text1=text(1,$prec1,$test1);
	$text2=text(1,$prec2,$test2);
	$text3=text(1,$prec3,$test3);
	$text4=text(1,$prec4,$test4);
	$cita=cita_c($prec1,$prec2,$prec3,$prec4);
	$c_curr_seme=$row["c_curr_seme"];
	$sel_year=substr($c_curr_seme,1,2);
	$sel_seme=substr($c_curr_seme,3,1);
	$class_name=class_id2big5($class_num,$sel_year,$sel_seme);


	echo "<tr bgcolor='#ffffff' align=center>
	<td>$test_y-$test_m</td>
	<td>$age</td>
	<td >$class_name</td>
	<td>$num</td>
	<td >$tall [$prec_t]</td>
	<td >$weigh [$prec_w]</td>
	<td > $bmt [$prec_b]<br>$textb</td>
	<td > $test1 [$prec1]<br>$text1 </td>
	<td > $test2 [$prec2]<br>$text2</td>
	<td > $test3 [$prec3]<br>$text3</td>
	<td > $test4 [$prec4]<br>$text4</td>
	<td >$cita</td>
	</tr>";
}
echo "</table>";
echo "[％]內為百分等級，也就是說在同性別、年齡的常模中，你超過百分之多少的人，如[50]，即表示你的表現勝過50％的同齡小朋友。<br>";
echo "請參閱：<a target='_blank' href='http://www.fitness.org.tw/'>教育部體適能網站(www.fitness.org.tw)</a>";
//優良獎章
function cita_c($t1,$t2,$t3,$t4){
$text="";
if($t1>=85 && $t2>=85 && $t3>=85 && $t4>=85 ){
	$text="<font color='#FF0000'><span style='background-color: #FFFF00'>金質獎章</span></font>";
}elseif($t1>=75 && $t2>=75 && $t3>=75 && $t4>=75 ){
	$text="<font color='#FF0000'><span style='background-color: #C0C0C0'>銀質獎章</span></font>";
}elseif($t1>=50 && $t2>=50 && $t3>=50 && $t4>=50 ){
	$text="<font color='#FF0000'><span style='background-color: #FFCC00'>銅質獎章</span></font>";
}
return $text;

}

// 評估
function text($grade,$prec,$s){
$text="";
if($s>0){
	if($grade==1){
		$text="中等";
		if($prec>=75) $text="<font color=purple>優良</font>";
		if($prec<25) $text="<font color=red>請加強</font>";
	}
	if($grade==6){
		$text="適中";
		if($prec>=80) $text="<font color=red>過重</font>";
		if($prec<20) $text="<font color=red>過輕</font>";
	}	
}
return $text;
}

?>
